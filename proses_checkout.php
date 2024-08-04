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

    // Masukkan data ke tabel tb_order
    $order = mysqli_query($config, "INSERT INTO `tb_order` (
        id_order,
        namacust_order,
        email_order,
        nohp_order,
        alamat_order,
        grandtotal_order,
        status_order
    ) VALUES (
        '$order_id',
        '$nama',
        '$email',
        '$nohp',
        '$alamat',
        '$subtotal',
        'Pending'
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
        echo "Sesi berhasil direset!";
    } else {
        echo "Gagal menyimpan data order.";
    }
} else {
    echo "Tidak ada data yang dikirim.";
}

ob_end_flush(); // Mengakhiri output buffering dan mengirim output ke browser
?>
