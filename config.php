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
$no_whatsapp = "6289643967492";
$nama_toko = "Notepro";
$alamat_toko = "Depan SMK Batik 1, JL. Papagan Kleco, Jl. Makam H., Pajang, Kec. Laweyan, Kota Surakarta, Jawa Tengah 57161";
$deskripsi_toko = "Menyediakan segala kebutuhan sparepart CCTV & Projektor";


if (!$config) {
    die("Error, tidak dapat terkoneksi dengan database.");
}

mysqli_set_charset($config, 'utf8mb4');
?>
