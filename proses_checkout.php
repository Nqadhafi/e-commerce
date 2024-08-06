<?php
ob_start(); // Memulai output buffering

session_start(); // Memulai sesi

include('./config.php');

// Generate unique order ID
$order_id = "#" . uniqid();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nama = filter_input(INPUT_POST, 'nama_lengkap', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $nohp = filter_input(INPUT_POST, 'nomor_handphone', FILTER_SANITIZE_STRING);
    $alamat = filter_input(INPUT_POST, 'alamat', FILTER_SANITIZE_STRING);
    $subtotal = filter_input(INPUT_POST, 'subtotal', FILTER_SANITIZE_STRING);
    $provinsi = filter_input(INPUT_POST, 'provinsi', FILTER_SANITIZE_STRING);

    $ongkir = mysqli_query($config,"SELECT * FROM tb_ongkir WHERE id_ongkir = '$provinsi'");
    $juml = mysqli_fetch_all($ongkir, MYSQLI_ASSOC);
    $biaya_ongkir = $juml[0]['jumlah_ongkir'];
    $after_ongkir = $biaya_ongkir+$subtotal;
    // Masukkan data ke tabel tb_order dengan waktu sekarang
    $order = mysqli_query($config, "INSERT INTO `tb_order` (
        id_order,
        namacust_order,
        email_order,
        nohp_order,
        alamat_order,
        grandtotal_order,
        after_ongkir_order,
        status_order,
        id_ongkir,
        tanggal_order -- Menambahkan kolom untuk menyimpan tanggal
    ) VALUES (
        '$order_id',
        '$nama',
        '$email',
        '$nohp',
        '$alamat',
        '$subtotal',
        '$after_ongkir',
        'Pending',
        '$provinsi',
        NOW() -- Menyimpan waktu saat ini ke kolom tanggal_order
    )");

    if ($order) {
        foreach ($_SESSION['keranjang'] as $id_produk => $qty) {
            $product_query = mysqli_query($config, "SELECT harga_produk FROM tb_produk WHERE id_produk = '$id_produk'");
            $product = mysqli_fetch_assoc($product_query);
            $harga_produk = $product['harga_produk'];
            $subtotal_keranjang = $qty * $harga_produk;

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

        // Debug statement
        $pagar = "%23";
        $convert_id = $pagar . substr($order_id,1);

        echo "<script> alert('Pesanan Anda Berhasil'); window.location.href='./check.php?orderid=$convert_id'; </script>";
       
    } else {
        echo "Gagal menyimpan data order.";
    }
} else {
    echo "Tidak ada data yang dikirim.";
}

ob_end_flush(); // Mengakhiri output buffering dan mengirim output ke browser
?>
