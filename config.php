<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$user = "root";
$pwd = "";
$host = "localhost";
$db = "toko_onlineku";
$config = mysqli_connect($host, $user, $pwd, $db);
if (!$config){
    echo "Error, tidak dapat terkoneksi dengan database.";
}
?>