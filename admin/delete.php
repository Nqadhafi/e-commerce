<?php
include('../config.php');
$id = $_GET['id'];

// Ambil path gambar dari database
$query = mysqli_query($config, "SELECT gambar_produk FROM tb_produk WHERE id_produk = '$id'");
$row = mysqli_fetch_assoc($query);
$image_path = "../assets/uploads/" . $row['gambar_produk'];

// Hapus file gambar dari server
if (file_exists($image_path)) {
    unlink($image_path);
}

// Hapus record dari database
$query = mysqli_query($config, "DELETE FROM tb_produk WHERE id_produk = '$id'");

if($query){
    echo "<script> alert('Produk dan gambar berhasil dihapus');window.location.href='./index.php';</script>";
} else {
    echo "<script> alert('Produk gagal dihapus');window.location.href='./index.php';</script>";
}
?>
