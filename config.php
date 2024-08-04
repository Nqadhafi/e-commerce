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


if (!$config) {
    die("Error, tidak dapat terkoneksi dengan database.");
}

mysqli_set_charset($config, 'utf8mb4');
?>
