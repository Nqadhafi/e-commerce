<?php
require '../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include('../config.php');

// Ambil ID order dari URL
$order_id = filter_input(INPUT_GET, 'orderid', FILTER_SANITIZE_STRING);

if ($order_id) {
    // Query untuk mendapatkan data pesanan berdasarkan ID
    $query = mysqli_query($config, "SELECT * FROM tb_order WHERE id_order = '$order_id'");
    if (!$query) {
        die('Query Error: ' . mysqli_error($config));
    }
    $order = mysqli_fetch_assoc($query);

    // Query untuk mendapatkan biaya ongkir dari tb_ongkir berdasarkan id_ongkir
    $id_ongkir = $order['id_ongkir'];
    $query_ongkir = mysqli_query($config, "SELECT jumlah_ongkir FROM tb_ongkir WHERE id_ongkir = '$id_ongkir'");
    if (!$query_ongkir) {
        die('Query Error: ' . mysqli_error($config));
    }
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
    $tanggal_order = date('d-m-Y', strtotime($order['tanggal_order']));

    // Mulai membuat HTML untuk PDF
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Detail Pesanan</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .container { width: 100%; }
            .text-center { text-align: center; }
            .mt-5 { margin-top: 3rem; }
            .mb-3 { margin-bottom: 1rem; }
            .fw-bolder { font-weight: bolder; }
            .table { width: 100%; border-collapse: collapse; margin-top: 2rem; }
            .table, .table th, .table td { border: 1px solid black; }
            .table th, .table td { padding: 8px; text-align: left; }
            .thead-light { background-color: #f2f2f2; }
            .invoice-footer { margin-top: 3rem; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="text-center mt-5">
                <h2>Detail Pesanan <?php echo htmlspecialchars($order['id_order']); ?></h2>
            </div>
            <div class="mt-3">
                <p><strong>Order ID</strong>: <?php echo htmlspecialchars($order['id_order']); ?></p>
                <p><strong>Tanggal Order</strong>: <?php echo htmlspecialchars($tanggal_order); ?></p>
                <p><strong>Nomor Resi</strong>: <?php echo htmlspecialchars($order['resi_order']); ?></p>
                <p><strong>Nama Customer</strong>: <?php echo htmlspecialchars($order['namacust_order']); ?></p>
                <p><strong>Email</strong>: <?php echo htmlspecialchars($order['email_order']); ?></p>
                <p><strong>No. HP</strong>: <?php echo htmlspecialchars($order['nohp_order']); ?></p>
                <p><strong>Alamat</strong>: <?php echo htmlspecialchars($order['alamat_order']); ?></p>
                <p><strong>Status</strong>: <?php echo htmlspecialchars($order['status_order']); ?></p>
            </div>
            <div class="mt-5">
                <h4>Daftar Pesanan</h4>
                <table class="table">
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
                        foreach ($items as $item) {
                            $total_produk = $item['harga_produk'] * $item['qty_keranjang'];
                            $grandtotal += $total_produk;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['nama_produk']); ?></td>
                                <td>Rp.<?php echo number_format($item['harga_produk'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($item['qty_keranjang']); ?></td>
                                <td>Rp.<?php echo number_format($total_produk, 0, ',', '.'); ?></td>
                            </tr>
                        <?php } ?>
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
                <h4 class="mb-3">- End of Document -</h4>
            </div>
        </div>
    </body>
    </html>
    <?php
    // Ambil konten HTML
    $html = ob_get_clean();

    // Inisialisasi Dompdf dan cetak PDF
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("detail_pesanan_$order_id.pdf", array("Attachment" => 0));
} else {
    echo 'Order ID tidak valid.';
}
?>
