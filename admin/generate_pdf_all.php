<?php
require_once '../config.php';
require_once '../vendor/autoload.php'; // Path ke autoload.php dari dompdf

use Dompdf\Dompdf;
use Dompdf\Options;

// Ambil bulan dari query parameter
$selected_month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_STRING);

// Filter data pesanan berdasarkan bulan yang dipilih
$filter_query = "";
if ($selected_month) {
    $filter_query = " WHERE DATE_FORMAT(tanggal_order, '%Y-%m') = '" . mysqli_real_escape_string($config, $selected_month) . "'";
}
$query = mysqli_query($config, "SELECT * FROM tb_order" . $filter_query);

if (!$query) {
    die('Query Error: ' . mysqli_error($config));
}

$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

// Hitung grandtotal
$grandtotal = 0;
foreach ($data as $row) {
    $grandtotal += $row['grandtotal_order'];
}

// Inisialisasi dompdf
$options = new Options();
$options->set('defaultFont', 'Helvetica');
$dompdf = new Dompdf($options);

// Tentukan judul berdasarkan parameter bulan
if ($selected_month) {
    $month_name = date('F Y', strtotime($selected_month . '-01'));
    $title = 'Laporan Pesanan Bulan ' . $month_name;
} else {
    $title = 'Laporan Semua Transaksi';
}

// HTML untuk laporan PDF
$html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . $title . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-weight-bold { font-weight: bold; }
    </style>
</head>
<body>
<h1>Toko Onlineku</h1>
    <h2>' . $title . '</h2>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Order ID</th>
                <th>Nama Customer</th>
                <th>No HP</th>
                <th>Nomor Resi</th>
                <th>Tanggal Order</th>
                <th>Sub Total + Ongkir</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

$no = 1;
foreach ($data as $row) {
    $tanggal_order = date('d-m-Y', strtotime($row['tanggal_order']));
    $html .= '<tr>
                <td>' . $no++ . '</td>
                <td>' . htmlspecialchars($row['id_order']) . '</td>
                <td>' . htmlspecialchars($row['namacust_order']) . '</td>
                <td>' . htmlspecialchars($row['nohp_order']) . '</td>
                <td>' . htmlspecialchars($row['resi_order']) . '</td>
                <td>' . $tanggal_order . '</td>
                <td>Rp.' . number_format($row['after_ongkir_order'], 0, ',', '.') . '</td>
                <td>' . htmlspecialchars($row['status_order']) . '</td>
              </tr>';
}

$html .= '   
<tr>
    <td colspan="6" class="text-right font-weight-bold">Total Transaksi</td>
    <td colspan="2" class="font-weight-bold text-right">Rp.' . number_format($grandtotal, 0, ',', '.') . '</td>
</tr>
</tbody>
    </table>
</body>
</html>';

// Load HTML ke dompdf
$dompdf->loadHtml($html);

// (Opsional) Atur ukuran dan orientasi kertas
$dompdf->setPaper('A4', 'landscape');

// Render PDF (convert HTML to PDF)
$dompdf->render();

// Output the generated PDF (streaming)
$dompdf->stream('Laporan_Pesanan.pdf', array('Attachment' => 0));
?>
