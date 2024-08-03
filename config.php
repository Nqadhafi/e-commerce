<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_customer'])){
    $_SESSION['id_customer'] = session_id();

}

$id_sesi = $_SESSION['id_customer'] ?? NULL;

$user = "root";
$pwd = "";
$host = "localhost";
$db = "toko_onlineku";
$config = mysqli_connect($host, $user, $pwd, $db);
if (!$config){
    echo "Error, tidak dapat terkoneksi dengan database.";
}
?>