<?php
require 'vendor/autoload.php'; // Pastikan path ke autoload.php sesuai dengan setup Anda
use Dompdf\Dompdf;
use Dompdf\Options;

// Inisialisasi Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$dompdf = new Dompdf($options);

// Koneksi ke database
include('./config.php');

// Ambil ID order dari URL
$order_id = filter_input(INPUT_GET, 'orderid', FILTER_SANITIZE_STRING);

// Query untuk mendapatkan data pesanan berdasarkan ID
$query = mysqli_query($config, "SELECT * FROM tb_order WHERE id_order = '$order_id'");
if (!$query) {
    die('Query Error: ' . mysqli_error($config));
}
$order = mysqli_fetch_assoc($query);

if ($order) {
    // Ambil ID Ongkir dari tb_order
    $id_ongkir = $order['id_ongkir'];

    // Query untuk mendapatkan biaya ongkir dari tb_ongkir berdasarkan id_ongkir
    $query_ongkir = mysqli_query($config, "SELECT jumlah_ongkir FROM tb_ongkir WHERE id_ongkir = '$id_ongkir'");
    $ongkir = mysqli_fetch_assoc($query_ongkir)['jumlah_ongkir'];

    // Query untuk mendapatkan detail produk dalam pesanan
    $query_items = mysqli_query($config, "SELECT p.nama_produk, p.harga_produk, k.qty_keranjang 
                                          FROM tb_keranjang k
                                          JOIN tb_produk p ON k.id_produk = p.id_produk
                                          WHERE k.id_keranjang = '$order_id'");
    if (!$query_items) {
        die('Query Error: ' . mysqli_error($config));
    }
    $items = mysqli_fetch_all($query_items, MYSQLI_ASSOC);

    // Format tanggal menjadi dd-mm-yyyy
    $date = new DateTime($order['tanggal_order']);
    $formatted_date = $date->format('d-m-Y');

    // Mulai membuat HTML untuk PDF
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Invoice</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .container { width: 100%; }
            .invoice-header { text-align: center; }
            .invoice-details, .invoice-items { margin: 20px 0; }
            .invoice-items table { width: 100%; border-collapse: collapse; }
            .invoice-items th, .invoice-items td { border: 1px solid #ddd; padding: 8px; }
            .invoice-items th { background-color: #f4f4f4; }
            .fw-bolder { font-weight: bolder; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="invoice-header">
                <h2>Detail Pesanan</h2>
            </div>
            <div class="invoice-details">
                <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id_order']); ?></p>
                <p><strong>Tanggal Transaksi:</strong> <?php echo htmlspecialchars($formatted_date); ?></p>
                <p><strong>Nomor Resi:</strong> <?php echo htmlspecialchars($order['resi_order']); ?></p>
                <p><strong>Nama Customer:</strong> <?php echo htmlspecialchars($order['namacust_order']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email_order']); ?></p>
                <p><strong>No. HP:</strong> <?php echo htmlspecialchars($order['nohp_order']); ?></p>
                <p><strong>Alamat:</strong> <?php echo htmlspecialchars($order['alamat_order']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status_order']); ?></p>
            </div>
            <div class="invoice-items">
                <h4>Daftar Pesanan</h4>
                <table>
                    <thead>
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
            </div>
        </div>
    </body>
    </html>
    <?php
    // Ambil konten HTML
    $html = ob_get_clean();

    // Load HTML ke Dompdf
    $dompdf->loadHtml($html);

    // (Opsional) Atur ukuran kertas dan orientasi
    $dompdf->setPaper('A4', 'portrait');

    // Render PDF
    $dompdf->render();

    // Output PDF ke browser
    $dompdf->stream('invoice.pdf', array('Attachment' => 0));
}
?>
