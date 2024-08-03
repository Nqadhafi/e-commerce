<?php
include('header.php');

$total_qty = 0; // Inisialisasi variabel untuk menyimpan total quantity

if (isset($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $id => $qty) {
        echo "Product ID: $id - Quantity: $qty<br>";
        $total_qty += $qty; // Tambahkan setiap quantity ke total
    }
    
} else {
    echo "Keranjang kosong!";
}
?>
