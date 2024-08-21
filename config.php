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
$rekening_bri = "12345789 a.n Agus";
$rekening_bca = "4567898 a.n Agus";
$api_rajaongkir = "";

if (!$config) {
    die("Error, tidak dapat terkoneksi dengan database.");
}

mysqli_set_charset($config, 'utf8mb4');

// Cek apakah fungsi buildRajaOngkirUrl sudah ada, jika tidak ada, deklarasikan
if (!function_exists('buildRajaOngkirUrl')) {
    function buildRajaOngkirUrl($endpoint, $params = []) {
        $base_url = "http://localhost/notepro_shop/rajaongkir_proxy.php?endpoint=" . urlencode($endpoint); //ganti dengan path url proxy
        
        // Jika $params bukan array, maka ubah menjadi array
        if (!is_array($params)) {
            $params = ['id' => $params];
        }

        // Tambahkan parameter ke URL jika ada
        if (!empty($params)) {
            $base_url .= '&' . http_build_query($params);
        }

        return $base_url;
    }
}

// Cek apakah fungsi getCurlResponse sudah ada, jika tidak ada, deklarasikan
if (!function_exists('getCurlResponse')) {
    // Fungsi untuk mengambil data menggunakan cURL
    function getCurlResponse($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}

?>
