<?php
include('./config.php');

// Fungsi untuk mendapatkan nama provinsi berdasarkan ID
function getProvinceName($province_id) {
    $url = buildRajaOngkirUrl("province", []);
    $response = getCurlResponse($url);
    $data = json_decode($response, true);

    if ($data === null) {
        echo "<script>alert('Gagal mengambil data provinsi. Response dari API tidak valid.');</script>";
        return null;
    }

    // Mencari nama provinsi berdasarkan ID
    foreach ($data['rajaongkir']['results'] as $province) {
        if ($province['province_id'] == $province_id) {
            return $province['province'];
        }
    }
    
    echo "<script>alert('Provinsi dengan ID tersebut tidak ditemukan.');</script>";
    return null;
}

// Fungsi untuk mendapatkan nama kota berdasarkan ID
function getCityName($city_id) {
    $url = buildRajaOngkirUrl("city", ['province' => '']);
    $response = getCurlResponse($url);
    $data = json_decode($response, true);

    if ($data === null) {
        echo "<script>alert('Gagal mengambil data kabupaten. Response dari API tidak valid.');</script>";
        return null;
    }

    // Mencari nama kota berdasarkan ID
    foreach ($data['rajaongkir']['results'] as $city) {
        if ($city['city_id'] == $city_id) {
            return $city['city_name'];
        }
    }

    echo "<script>alert('Kabupaten dengan ID tersebut tidak ditemukan.');</script>";
    return null;
}

// Generate unique order ID
$order_id = "#" . uniqid();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nama = filter_input(INPUT_POST, 'nama_lengkap', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $nohp = filter_input(INPUT_POST, 'nomor_handphone', FILTER_SANITIZE_STRING);
    $alamat = filter_input(INPUT_POST, 'alamat', FILTER_SANITIZE_STRING);
    $provinsi_id = filter_input(INPUT_POST, 'provinsi', FILTER_SANITIZE_STRING);
    $kabupaten_id = filter_input(INPUT_POST, 'kabupaten', FILTER_SANITIZE_STRING);
    $subtotal = filter_input(INPUT_POST, 'subtotal', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $total_weight = filter_input(INPUT_POST, 'total_weight', FILTER_SANITIZE_NUMBER_INT);
    $ongkir = filter_input(INPUT_POST, 'ongkir', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $metode_pembayaran = filter_input(INPUT_POST, 'metode_pembayaran', FILTER_SANITIZE_STRING);
    $grand_total = $subtotal + $ongkir;

    // Fetch province and city names
    $provinsi = getProvinceName($provinsi_id);
    $kabupaten = getCityName($kabupaten_id);

    // Debug: Cek apakah provinsi dan kabupaten berhasil diambil
    if (is_null($provinsi) || is_null($kabupaten)) {
        echo "<script>alert('Gagal mengambil data provinsi atau kabupaten. Silakan coba lagi.'); window.location.href='./checkout.php';</script>";
        exit;
    }

    // Masukkan data ke tabel tb_order
    $order = mysqli_query($config, "INSERT INTO `tb_order` (
        id_order,
        namacust_order,
        email_order,
        nohp_order,
        alamat_order,
        provinsi_order,
        kabupaten_order,
        subtotal_order,
        total_weight_order,
        ongkir_order,
        grandtotal_order,
        metode_pembayaran,
        status_order,
        tanggal_order
    ) VALUES (
        '$order_id',
        '$nama',
        '$email',
        '$nohp',
        '$alamat',
        '$provinsi', -- Simpan nama provinsi
        '$kabupaten', -- Simpan nama kabupaten
        '$subtotal',
        '$total_weight',
        '$ongkir',
        '$grand_total',
        '$metode_pembayaran',
        'Belum Bayar',
        NOW()
    )");

    if ($order) {
        foreach ($_SESSION['keranjang'] as $id_produk => $qty) {
            $product_query = mysqli_query($config, "SELECT harga_produk, stok_produk FROM tb_produk WHERE id_produk = '$id_produk'");
            $product = mysqli_fetch_assoc($product_query);
            $harga_produk = $product['harga_produk'];
            $stok_produk = $product['stok_produk'];

            // Menghitung subtotal untuk produk
            $subtotal_keranjang = $qty * $harga_produk;

            // Kurangi stok produk
            if ($stok_produk >= $qty) {
                $new_stok = $stok_produk - $qty;
                mysqli_query($config, "UPDATE tb_produk SET stok_produk = '$new_stok' WHERE id_produk = '$id_produk'");
            } else {
                echo "<script>alert('Stok tidak mencukupi untuk produk $id_produk.'); window.location.href='./cart.php';</script>";
                exit;
            }

            // Masukkan data ke tb_keranjang
            $add_to_cart = mysqli_query($config, "INSERT INTO `tb_keranjang`(
                id_keranjang,
                id_produk,
                qty_keranjang,
                subtotal_keranjang
            ) VALUES (
                '$order_id',
                '$id_produk',
                '$qty',
                '$subtotal_keranjang'
            )");
        }

        // Hancurkan sesi
        session_unset(); // Menghapus semua variabel sesi
        session_destroy(); // Menghancurkan sesi

        // Redirect ke halaman sukses
        $order_id_encoded = urlencode($order_id);
        echo "<script> alert('Pesanan Anda Berhasil'); window.location.href='./check.php?orderid=$order_id_encoded'; </script>";
        
    } else {
        echo "Gagal menyimpan data order.";
    }
} else {
    echo "Tidak ada data yang dikirim.";
}

?>
