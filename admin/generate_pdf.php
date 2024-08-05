<?php
require '../vendor/autoload.php';
use Dompdf\Dompdf;

include('../config.php');

// Ambil ID order dari URL
$order_id = filter_input(INPUT_GET, 'orderid', FILTER_SANITIZE_STRING);

if ($order_id) {
    // Query untuk mendapatkan data pesanan berdasarkan ID
    $query = mysqli_query($config, "SELECT * FROM tb_order WHERE id_order = '$order_id'");
    $order = mysqli_fetch_assoc($query);

    // Query untuk mendapatkan detail produk dalam pesanan
    $query_items = mysqli_query($config, "SELECT p.nama_produk, p.harga_produk, k.qty_keranjang 
                                          FROM tb_keranjang k
                                          JOIN tb_produk p ON k.id_produk = p.id_produk
                                          WHERE k.id_keranjang = '$order_id'");

    // Ambil data produk dalam pesanan
    $items = mysqli_fetch_all($query_items, MYSQLI_ASSOC);

    $html = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Detail Pesanan</title>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { width: 100%; margin: 0 auto; }
            .text-center { text-align: center; }
            .mt-5 { margin-top: 3rem; }
            .mb-3 { margin-bottom: 1rem; }
            .fw-bolder { font-weight: bolder; }
            .table { width: 100%; border-collapse: collapse; margin-top: 2rem; }
            .table, .table th, .table td { border: 1px solid black; }
            .table th, .table td { padding: 8px; text-align: left; }
            .thead-light { background-color: #f2f2f2; }
            .invoice-footer { margin-top: 3rem; }
            .row { display: flex; margin-right: -15px; margin-left: -15px; }
            .col { flex: 1; padding-right: 15px; padding-left: 15px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="text-center mt-5">
                <h2>Detail Pesanan</h2>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <p><strong>Order ID:</strong> ' . htmlspecialchars($order['id_order']) . '</p>
                    <p><strong>Nomor Resi:</strong> ' . htmlspecialchars($order['resi_order']) . '</p>
                    <p><strong>Nama Customer:</strong> ' . htmlspecialchars($order['namacust_order']) . '</p>
                    <p><strong>Email:</strong> ' . htmlspecialchars($order['email_order']) . '</p>
                    <p><strong>No. HP:</strong> ' . htmlspecialchars($order['nohp_order']) . '</p>
                    <p><strong>Alamat:</strong> ' . htmlspecialchars($order['alamat_order']) . '</p>
                    <p><strong>Status:</strong> ' . htmlspecialchars($order['status_order']) . '</p>
                </div>
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
                    <tbody>';

    foreach ($items as $item) {
        $total_produk = $item['harga_produk'] * $item['qty_keranjang'];
        $html .= '
                        <tr>
                            <td>' . htmlspecialchars($item['nama_produk']) . '</td>
                            <td>Rp.' . number_format($item['harga_produk'], 0, ',', '.') . '</td>
                            <td>' . htmlspecialchars($item['qty_keranjang']) . '</td>
                            <td>Rp.' . number_format($total_produk, 0, ',', '.') . '</td>
                        </tr>';
    }

    $grandtotal = array_reduce($items, function($carry, $item) {
        return $carry + ($item['harga_produk'] * $item['qty_keranjang']);
    }, 0);

    $html .= '
                        <tr>
                            <td colspan="3" class="fw-bolder">Grandtotal</td>
                            <td class="fw-bolder">Rp.' . number_format($grandtotal, 0, ',', '.') . '</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="invoice-footer text-center">
                <h4 class="mb-3">Terima kasih atas pembelian Anda!</h4>
            </div>
        </div>
    </body>
    </html>';

    // Inisialisasi Dompdf dan cetak PDF
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("detail_pesanan_$order_id.pdf", array("Attachment" => 0));
} else {
    echo 'Order ID tidak valid.';
}
?>
