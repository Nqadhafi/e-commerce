<?php
require_once '../vendor/autoload.php'; // Path to dompdf autoload
use Dompdf\Dompdf;
use Dompdf\Options;

include('../config.php');

// Ambil bulan yang dipilih dari URL
$selected_month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_STRING);

// Buat query untuk mendapatkan semua order yang selesai di bulan yang dipilih atau seluruh bulan jika tidak dipilih
$query_str = "SELECT * FROM tb_order WHERE status_order = 'Selesai'";
if ($selected_month) {
    $query_str .= " AND DATE_FORMAT(tanggal_order, '%Y-%m') = '" . mysqli_real_escape_string($config, $selected_month) . "'";
}

$query = mysqli_query($config, $query_str);
$orders = mysqli_fetch_all($query, MYSQLI_ASSOC);

// Jika tidak ada pesanan, berikan pesan bahwa tidak ada data yang ditemukan
if (empty($orders)) {
    echo "Tidak ada data pesanan yang selesai untuk bulan yang dipilih.";
    exit;
}

$total_grandtotal = 0;

$html = '
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }
    h1 {
        text-align: center;
        margin-bottom: 40px;
    }
    .summary-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .summary-table th, .summary-table td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    .summary-table th {
        background-color: #f2f2f2;
        text-align: left;
    }
</style>
';

$month_title = $selected_month ? date('F Y', strtotime($selected_month . '-01')) : 'Semua Bulan';
$html .= '<h1>Laporan Pesanan Selesai - ' . $month_title . '</h1>';
$html .= '<table class="summary-table">';
$html .= '<thead><tr><th>Order ID</th><th>Nama Customer</th><th>Email</th><th>No. HP</th><th>Alamat</th><th>Kabupaten</th><th>Provinsi</th><th>Tanggal Order</th><th>Grand Total</th></tr></thead>';
$html .= '<tbody>';

foreach ($orders as $order) {
    $tanggal_order = date('d-m-Y', strtotime($order['tanggal_order']));
    $total_grandtotal += $order['grandtotal_order'];
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($order['id_order']) . '</td>';
    $html .= '<td>' . htmlspecialchars($order['namacust_order']) . '</td>';
    $html .= '<td>' . htmlspecialchars($order['email_order']) . '</td>';
    $html .= '<td>' . htmlspecialchars($order['nohp_order']) . '</td>';
    $html .= '<td>' . htmlspecialchars($order['alamat_order']) . '</td>';
    $html .= '<td>' . htmlspecialchars($order['kabupaten_order']) . '</td>';
    $html .= '<td>' . htmlspecialchars($order['provinsi_order']) . '</td>';
    $html .= '<td>' . htmlspecialchars($tanggal_order) . '</td>';
    $html .= '<td>Rp. ' . number_format($order['grandtotal_order'], 0, ',', '.') . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody>';
$html .= '</table>';

$html .= '<h3>Total Keseluruhan: Rp. ' . number_format($total_grandtotal, 0, ',', '.') . '</h3>';

// Setup Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF (force download)
$dompdf->stream('laporan_pesanan_' . ($selected_month ?: 'semua_bulan') . '.pdf', array('Attachment' => 0));
?>
