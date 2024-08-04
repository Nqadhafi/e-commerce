<?php
include('./config.php');

// Ambil ID order dari URL
$order_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);

// Query untuk mendapatkan data pesanan berdasarkan ID
$query = mysqli_query($config, "SELECT * FROM tb_order WHERE id_order = '$order_id'");

// Periksa jika query berhasil
if (!$query) {
    die('Query Error: ' . mysqli_error($config));
}

// Ambil data order
$order = mysqli_fetch_assoc($query);

// Jika tidak ada data pesanan, tampilkan pesan error
if (!$order) {
    die('Pesanan tidak ditemukan.');
}

// Query untuk mendapatkan detail produk dalam pesanan
$query_items = mysqli_query($config, "SELECT p.nama_produk, p.harga_produk, k.qty_keranjang 
                                      FROM tb_keranjang k
                                      JOIN tb_produk p ON k.id_produk = p.id_produk
                                      WHERE k.id_keranjang = '$order_id'");

// Periksa jika query berhasil
if (!$query_items) {
    die('Query Error: ' . mysqli_error($config));
}

// Ambil data produk dalam pesanan
$items = mysqli_fetch_all($query_items, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .invoice-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .invoice-header, .invoice-footer {
            margin-bottom: 20px;
        }
        .invoice-header h2, .invoice-footer h4 {
            margin: 0;
        }
        .invoice-items table {
            width: 100%;
        }
        .invoice-items th, .invoice-items td {
            padding: 10px;
            text-align: left;
        }
        .invoice-items th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="invoice-container">
            <div class="invoice-header">
                <h2 class="text-center">Detail Pesanan</h2>
            </div>

            <div class="invoice-details">
                <h4>Informasi Pesanan</h4>
                <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id_order']); ?></p>
                <p><strong>Nama Customer:</strong> <?php echo htmlspecialchars($order['namacust_order']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email_order']); ?></p>
                <p><strong>No. HP:</strong> <?php echo htmlspecialchars($order['nohp_order']); ?></p>
                <p><strong>Alamat:</strong> <?php echo htmlspecialchars($order['alamat_order']); ?></p>
                <p><strong>Grand Total:</strong> Rp.<?php echo number_format($order['grandtotal_order'], 0, ',', '.'); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status_order']); ?></p>
            </div>

            <div class="invoice-items">
                <h4>Daftar Produk</h4>
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['nama_produk']); ?></td>
                                <td>Rp.<?php echo number_format($item['harga_produk'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($item['qty_keranjang']); ?></td>
                                <td>Rp.<?php echo number_format($item['harga_produk'] * $item['qty_keranjang'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="invoice-footer">
                <h4>Terima kasih atas pembelian Anda!</h4>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
