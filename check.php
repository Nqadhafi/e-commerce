<?php
include('./header.php');
include('./config.php');

// Ambil ID order dari URL
$order_id = filter_input(INPUT_GET, 'orderid', FILTER_SANITIZE_STRING);

// Query untuk mendapatkan data pesanan berdasarkan ID
$query = mysqli_query($config, "SELECT * FROM tb_order WHERE id_order = '$order_id'");
// Cek jika query berhasil
if (!$query) {
    die('Query Error: ' . mysqli_error($config));
}
// Ambil data order
$order = mysqli_fetch_assoc($query);

if ($order) {
    // Mengambil nilai ongkir langsung dari tabel tb_order
    $ongkir = $order['ongkir_order'];

    // Query untuk mendapatkan detail produk dalam pesanan
    $query_items = mysqli_query($config, "SELECT p.nama_produk, p.harga_produk, k.qty_keranjang 
                                          FROM tb_keranjang k
                                          JOIN tb_produk p ON k.id_produk = p.id_produk
                                          WHERE k.id_keranjang = '$order_id'");
    // Cek jika query items berhasil
    if (!$query_items) {
        die('Query Error: ' . mysqli_error($config));
    }
    // Ambil data produk dalam pesanan
    $items = mysqli_fetch_all($query_items, MYSQLI_ASSOC);

    // Format tanggal menjadi dd-mm-yyyy
    $date = new DateTime($order['tanggal_order']);
    $formatted_date = $date->format('d-m-Y');


    // Mapping status order ke class dan deskripsi yang sesuai
    switch ($order['status_order']) {
        case 'Belum Bayar':
            $class = "text-danger fw-bolder";
            break;
        case 'Proses Verifikasi':
            $class = "text-warning fw-bolder";
            break;
        case 'Sudah Bayar':
            $class = "text-success fw-bolder";
            break;
        case 'Pengiriman':
            $class = "text-primary fw-bolder";
            break;
        case 'Selesai':
            $class = "text-success fw-bolder";
            break;
        default:
            $class = "text-muted fw-bolder";
            break;
    }
}
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
        <form class="d-flex w-75 mx-auto" role="search" method="get">
            <input class="form-control me-2" type="search" placeholder="Masukan Order ID. Contoh #12345" aria-label="Search" name="orderid">
            <input class="btn btn-success btn-outline-secondary text-white w-25" type="submit" value="Cari Order">
        </form>
        <?php if ($order) : ?>
            <div class="invoice-container">
                <div class="invoice-header">
                    <h2 class="text-center">Detail Pesanan</h2>
                </div>
                <div class="container-fluid d-flex justify-content-center align-items-center">
                    <div class="invoice-details row ms-5">
                        <p class="col-md-6"><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id_order']); ?></p>
                        <p class="col-md-6"><strong>Tanggal Transaksi:</strong> <?php echo htmlspecialchars($formatted_date); ?></p>
                        <p class="col-md-6"><strong>Nomor Resi:</strong> <?php echo htmlspecialchars($order['resi_order']); ?></p>
                        <p class="col-md-6"><strong>Nama Customer:</strong> <?php echo htmlspecialchars($order['namacust_order']) ; ?></p>
                        <p class="col-md-6"><strong>Email:</strong> <?php echo htmlspecialchars($order['email_order']); ?></p>
                        <p class="col-md-6"><strong>No. HP:</strong> <?php echo htmlspecialchars($order['nohp_order']); ?></p>
                        <p class="col-md-6"><strong>Alamat:</strong> <?php echo htmlspecialchars($order['alamat_order']); ?></p>
                        <p class="col-md-6"><strong>Kabupaten:</strong> <?php echo htmlspecialchars($order['kabupaten_order']); ?></p>
                        <p class="col-md-6"><strong>Provinsi:</strong> <?php echo htmlspecialchars($order['provinsi_order']); ?></p>
                        <p class="col-md-6"><strong>Status:</strong> <span class="<?php echo $class; ?>"><?php echo htmlspecialchars($order['status_order']); ?></span></p>
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
                            $grandtotal = 0; 
                            foreach ($items as $item) :
                                $total_produk = $item['harga_produk'] * $item['qty_keranjang'];
                                $grandtotal += $total_produk;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['nama_produk']); ?></td>
                                    <td>Rp.<?php echo number_format($item['harga_produk'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($item['qty_keranjang']); ?></td>
                                    <td>Rp.<?php echo number_format($total_produk, 0, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="3" class="fw-bolder">Subtotal</td>
                                <td class="fw-bolder">Rp.<?php echo number_format($grandtotal, 0, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="fw-bolder">Ongkir</td>
                                <td class="fw-bolder">Rp.<?php echo number_format($ongkir, 0, ',', '.'); ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="fw-bolder">Grandtotal + Ongkir</td>
                                <td class="fw-bolder">Rp.<?php echo number_format($grandtotal + $ongkir, 0, ',', '.'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="invoice-footer text-center">
                    <h4 class="mb-3">Terima kasih atas pembelian Anda!</h4>
                    <h4 class="mb-3"><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id_order']); ?></h4>
                    <hr>
                    <?php if ($order['status_order'] == 'Belum Bayar') : ?>
                        <div class="p-2 bg-white border border-black rounded">
                    <p><h5>Silakan transfer pembayaran ke rekening berikut:</h5></p>
                    <ul>
                        <?php if ($order['metode_pembayaran'] == 'BRI') : ?>
                        <h4><strong>BRI:</strong> <?php echo $rekening_bri; ?></h4>
                        <?php else : ?>
                      <h4> <strong>BCA:</strong> <?php echo $rekening_bca; ?></h4>
                        <?php endif; ?>
                    </ul>
                    </div>
                    <?php endif; ?>
                    <?php if ($order['status_order'] === 'Belum Bayar') : ?>
                        <form action="upload_bukti.php" method="post" enctype="multipart/form-data" class="mt-3">
                            <div class="mb-3 ">
                                <label for="bukti_bayar" class="form-label">Upload Bukti Pembayaran:</label>
                                <input class="form-control" type="file" id="bukti_bayar" name="bukti_bayar" required>
                            </div>
                            <input type="hidden" name="order_id" value="<?php echo $order['id_order']; ?>">
                            <button type="submit" class="btn btn-warning mb-2">Upload Bukti</button>
                        </form>
                    <?php elseif ($order['status_order'] === 'Proses Verifikasi') : ?>
                        <p>Bukti pembayaran telah diupload dan sedang dalam proses verifikasi.</p>
                        <p><a href="./assets/bukti_bayar/<?php echo urlencode($order['id_order']) . "_" . $order['bukti_bayar']; ?>" target="_blank">Lihat Bukti Pembayaran</a></p>
                    <?php elseif ($order['status_order'] === 'Sudah Bayar') : ?>
                        <p>Pembayaran telah diverifikasi.</p>
                        <p><a href="./assets/bukti_bayar/<?php echo urlencode($order['id_order']) . "_" . $order['bukti_bayar']; ?>" target="_blank">Lihat Bukti Pembayaran</a></p>
                    <?php endif; ?>
                    <?php
                    $id_order_wa = substr($order['id_order'], 1);
                    $whatsapp = "https://wa.me/" . $no_whatsapp . "?text=Halo%20min!%20Tolong%20cek%20pesanan%20dengan%20order%20ID%20%23" . urlencode($id_order_wa) . "%20ya!";
                    ?>
                    <div class="d-flex justify-content-center gap-3">
                        <div class="text-center">
                            <a href="<?php echo $whatsapp; ?>" class="btn btn-success" target="_blank">Chat Admin via Whatsapp!</a>
                        </div>
                        <div class="text-center">
                            <a href="cetak_pdf.php?orderid=<?php echo urlencode($order_id); ?>" class="btn btn-primary" target="_blank">Cetak Invoice</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else : ?>    
            <h4 class="text-center mt-5">Pesanan Tidak Ditemukan</h4>
        <?php endif; ?>
    </div>
</body>
</html>
