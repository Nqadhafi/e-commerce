<?php
include('./header.php');

// Ambil ID order dari URL
$order_id = filter_input(INPUT_GET, 'orderid', FILTER_SANITIZE_STRING);

// Query untuk mendapatkan data pesanan berdasarkan ID
$query = mysqli_query($config, "SELECT * FROM tb_order WHERE id_order = '$order_id'");
// Ambil data order
$order = mysqli_fetch_assoc($query);

// Jika tidak ada data pesanan, tampilkan pesan error


// Query untuk mendapatkan detail produk dalam pesanan
$query_items = mysqli_query($config, "SELECT p.nama_produk, p.harga_produk, k.qty_keranjang 
                                      FROM tb_keranjang k
                                      JOIN tb_produk p ON k.id_produk = p.id_produk
                                      WHERE k.id_keranjang = '$order_id'");


// Ambil data produk dalam pesanan
$items = mysqli_fetch_all($query_items, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan</title>
</head>

<body>
    <div class="container">
    <h2 class="text-center mt-5">Cari Pesanan</h2>
        <form class="d-flex w-75  mx-auto" role="search" method="get">
            <input class="form-control me-2" type="search" placeholder="Masukan Order ID. Contoh #12345" aria-label="Search" name="orderid">
            <button class="btn btn-success btn-outline-seccondary w-25" type="submit">Cari Order</button>
        </form>
        <?php if ($order) : ?>

            <div class="invoice-container">
                <div class="invoice-header">
                    <h2 class="text-center">Detail Pesanan</h2>
                </div>
                <div class="container-fluid d-flex justify-content-center align-items-center">
        <div class="invoice-details row ms-5">
            <p class="col-md-6"><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id_order']); ?></p>
            <p class="col-md-6"><strong>Nama Customer:</strong> <?php echo htmlspecialchars($order['namacust_order']); ?></p>
            <p class="col-md-6"><strong>Email:</strong> <?php echo htmlspecialchars($order['email_order']); ?></p>
            <p class="col-md-6"><strong>No. HP:</strong> <?php echo htmlspecialchars($order['nohp_order']); ?></p>
            <p class="col-md-6"><strong>Alamat:</strong> <?php echo htmlspecialchars($order['alamat_order']); ?></p>
            <p class="col-md-6"><strong>Status:</strong> <?php echo htmlspecialchars($order['status_order']); ?></p>
        </div>
    </div>
                <div class="invoice-items">
                    <h4>Daftar Pesanan</h4>
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
                            <?php
                            $grandtotal = 0; // Inisialisasi grandtotal di luar loop
                            foreach ($items as $item) :
                                $total_produk = $item['harga_produk'] * $item['qty_keranjang'];
                                $grandtotal += $total_produk; // Tambahkan total_produk ke grandtotal
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['nama_produk']); ?></td>
                                    <td>Rp.<?php echo number_format($item['harga_produk'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($item['qty_keranjang']); ?></td>
                                    <td>Rp.<?php echo number_format($total_produk, 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="3" class="fw-bolder">Grandtotal</td>
                                <td class="fw-bolder">Rp.<?php echo number_format($grandtotal, 0, ',', '.'); ?></td>
                            </tr>
                        </tbody>

                    </table>
                </div>

                <div class="invoice-footer">
                    <h4>Terima kasih atas pembelian Anda!</h4>
                </div>
                <?php else :?>    
                    <h4 class="text-center mt-5">Pesanan Tidak Ditemukan</h4>
            <?php endif; ?>
            </div>
    </div>
</body>

</html>