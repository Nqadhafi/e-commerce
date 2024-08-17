<?php
require_once '../vendor/autoload.php'; // Path to dompdf autoload
use Dompdf\Dompdf;
use Dompdf\Options;

include('../config.php');

// Ambil ID order dari URL
$order_id = filter_input(INPUT_GET, 'orderid', FILTER_SANITIZE_STRING);

$query = mysqli_query($config, "SELECT * FROM tb_order WHERE id_order = '$order_id'");
$order = mysqli_fetch_assoc($query);

// Ambil detail produk yang dipesan
$query_items = mysqli_query($config, "SELECT p.nama_produk, k.qty_keranjang, k.subtotal_keranjang 
                                      FROM tb_keranjang k 
                                      JOIN tb_produk p ON k.id_produk = p.id_produk 
                                      WHERE k.id_keranjang = '$order_id'");
$items = mysqli_fetch_all($query_items, MYSQLI_ASSOC);

// Format tanggal order
$tanggal_order = date('d-m-Y', strtotime($order['tanggal_order']));

// Start HTML content
$html = '
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }
    h1, h3 {
        text-align: center;
    }
    .details, .summary {
        margin-bottom: 20px;
    }
    .details p {
        margin: 0;
        padding: 2px 0;
    }
    .summary {
        border-top: 2px solid #000;
        border-bottom: 2px solid #000;
        padding: 10px 0;
    }
    .products-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .products-table th, .products-table td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    .products-table th {
        background-color: #f2f2f2;
        text-align: left;
    }
</style>
';

$html .= '<h1>Invoice Pesanan</h1>';
$html .= '<div class="details">';
$html .= '<p><strong>Order ID:</strong> ' . htmlspecialchars($order['id_order']) . '</p>';
$html .= '<p><strong>Tanggal Order:</strong> ' . htmlspecialchars($tanggal_order) . '</p>';
$html .= '<p><strong>Nama Customer:</strong> ' . htmlspecialchars($order['namacust_order']) . '</p>';
$html .= '<p><strong>Email:</strong> ' . htmlspecialchars($order['email_order']) . '</p>';
$html .= '<p><strong>No. HP:</strong> ' . htmlspecialchars($order['nohp_order']) . '</p>';
$html .= '<p><strong>Alamat:</strong> ' . htmlspecialchars($order['alamat_order']) . '</p>';
$html .= '<p><strong>Provinsi:</strong> ' . htmlspecialchars($order['provinsi_order']) . '</p>';
$html .= '<p><strong>Kabupaten:</strong> ' . htmlspecialchars($order['kabupaten_order']) . '</p>';
$html .= '<p><strong>Status:</strong> ' . htmlspecialchars($order['status_order']) . '</p>';
$html .= '</div>';

$html .= '<h3>Detail Produk:</h3>';
$html .= '<table class="products-table">';
$html .= '<thead><tr><th>Nama Produk</th><th>Jumlah</th><th>Subtotal</th></tr></thead>';
$html .= '<tbody>';

foreach ($items as $item) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($item['nama_produk']) . '</td>';
    $html .= '<td>' . htmlspecialchars($item['qty_keranjang']) . '</td>';
    $html .= '<td>Rp. ' . number_format($item['subtotal_keranjang'], 0, ',', '.') . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody>';
$html .= '</table>';

$html .= '<div class="summary">';
$html .= '<p><strong>Ongkir:</strong> Rp. ' . number_format($order['ongkir_order'], 0, ',', '.') . '</p>';
$html .= '<p><strong>Grand Total:</strong> Rp. ' . number_format($order['grandtotal_order'], 0, ',', '.') . '</p>';
$html .= '</div>';

// Setup Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF (force download)
$dompdf->stream('invoice_' . $order_id . '.pdf', array('Attachment' => 0));
?>
