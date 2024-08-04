<?php
include('./config.php');

// Generate unique order ID
$order_id = "#" . uniqid();

// Proses data hanya jika ada form yang dikirim (menggunakan request method)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_keranjang = filter_input(INPUT_POST, 'id_keranjang', FILTER_SANITIZE_STRING);
    $nama = filter_input(INPUT_POST, 'nama_lengkap', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $nohp = filter_input(INPUT_POST, 'nomor_handphone', FILTER_SANITIZE_STRING);
    $alamat = filter_input(INPUT_POST, 'alamat', FILTER_SANITIZE_STRING);
    $subtotal = filter_input(INPUT_POST, 'subtotal', FILTER_SANITIZE_STRING);
    
    // Masukkan data ke tabel tb_order
    $order = mysqli_query($config, "INSERT INTO `tb_order` (
        id_order,
        id_keranjang,
        namacust_order,
        email_order,
        nohp_order,
        alamat_order,
        grandtotal_order,
        status_order
    ) VALUES (
        '$order_id',
        '$id_keranjang',
        '$nama',
        '$email',
        '$nohp',
        '$alamat',
        '$subtotal',
        'Pending'
    )");

    // Jika data berhasil disimpan di tb_order, lanjutkan menyimpan data ke tb_keranjang
    if ($order) {
        foreach ($_SESSION['keranjang'] as $id_produk => $qty) {
            // Ambil harga produk dari database
            $product_query = mysqli_query($config, "SELECT harga_produk FROM tb_produk WHERE id_produk = '$id_produk'");
            $product = mysqli_fetch_assoc($product_query);
            $harga_produk = $product['harga_produk'];

            // Hitung subtotal untuk produk ini
            $subtotal_keranjang = $qty * $harga_produk;

            // Masukkan data ke tabel tb_keranjang
            $add_to_cart = mysqli_query($config, "INSERT INTO `tb_keranjang`(
                id_keranjang,
                id_produk,
                qty_keranjang,
                subtotal_keranjang
            ) VALUES (
                '$id_keranjang',
                '$id_produk',
                '$qty',
                '$subtotal_keranjang'
            )");
        }

        // Bersihkan keranjang belanja setelah checkout berhasil
        unset($_SESSION['keranjang']);
        
        echo "Order berhasil diproses!";
    } else {
        echo "Gagal menyimpan data order.";
    }
} else {
    echo "Tidak ada data yang dikirim.";
}
?>
