<?php
ob_start(); // Memulai output buffering
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$user = "root";
$pwd = "";
$host = "localhost";
$db = "toko_onlineku";
$config = mysqli_connect($host, $user, $pwd, $db);
$no_whatsapp = "6281332975334";
$nama_toko = "Toko Onlineku";
$alamat_toko = "Jl. Veteran, Dusun I, Singopuran, Kec. Kartasura, Kabupaten Sukoharjo, Jawa Tengah 57163";
$deskripsi_toko = "Menyediakan kebutuhan - kebutuhan pokok, harga murah, pelayanan puas!!";


if (!$config) {
    die("Error, tidak dapat terkoneksi dengan database.");
}

mysqli_set_charset($config, 'utf8mb4');
?>
