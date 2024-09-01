<?php
include('./config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;

// Fungsi untuk mendapatkan nama provinsi berdasarkan ID
function getProvinceName($province_id) {
    $url = buildRajaOngkirUrl("province", []);
    $response = getCurlResponse($url);
    $data = json_decode($response, true);

    if ($data === null) {
        echo "<script>alert('Gagal mengambil data provinsi. Response dari API tidak valid.');</script>";
        return null;
    }

    foreach ($data['rajaongkir']['results'] as $province) {
        if ($province['province_id'] == $province_id) {
            return $province['province'];
        }
    }
    
    echo "<script>alert('Provinsi dengan ID tersebut tidak ditemukan.');</script>";
    return null;
}

function getCityName($city_id) {
    $url = buildRajaOngkirUrl("city", ['province' => '']);
    $response = getCurlResponse($url);
    $data = json_decode($response, true);

    if ($data === null) {
        echo "<script>alert('Gagal mengambil data kabupaten. Response dari API tidak valid.');</script>";
        return null;
    }

    foreach ($data['rajaongkir']['results'] as $city) {
        if ($city['city_id'] == $city_id) {
            return $city['city_name'];
        }
    }

    echo "<script>alert('Kabupaten dengan ID tersebut tidak ditemukan.');</script>";
    return null;
}

// Generate unique order ID
$order_id = uniqid("#");

// Pastikan order ID tidak kosong
if (empty($order_id)) {
    echo "<script>alert('Gagal membuat Order ID.'); window.location.href='./checkout.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nama = filter_input(INPUT_POST, 'nama_lengkap', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $nohp = filter_input(INPUT_POST, 'nomor_handphone', FILTER_SANITIZE_STRING);
    $alamat = filter_input(INPUT_POST, 'alamat', FILTER_SANITIZE_STRING);
    $provinsi_id = filter_input(INPUT_POST, 'provinsi', FILTER_SANITIZE_STRING);
    $kabupaten_id = filter_input(INPUT_POST, 'kabupaten', FILTER_SANITIZE_STRING);
    $subtotal = filter_input(INPUT_POST, 'subtotal', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $total_weight = filter_input(INPUT_POST, 'total_weight', FILTER_SANITIZE_NUMBER_INT);
    $ongkir = filter_input(INPUT_POST, 'ongkir', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $metode_pembayaran = filter_input(INPUT_POST, 'metode_pembayaran', FILTER_SANITIZE_STRING);
    $grand_total = $subtotal + $ongkir;

    $provinsi = getProvinceName($provinsi_id);
    $kabupaten = getCityName($kabupaten_id);

    if (is_null($provinsi) || is_null($kabupaten)) {
        echo "<script>alert('Gagal mengambil data provinsi atau kabupaten. Silakan coba lagi.'); window.location.href='./checkout.php';</script>";
        exit;
    }

    // Insert data ke tabel tb_order
    $order = mysqli_query($config, "INSERT INTO `tb_order` (
        id_order,
        namacust_order,
        email_order,
        nohp_order,
        alamat_order,
        provinsi_order,
        kabupaten_order,
        subtotal_order,
        total_weight_order,
        ongkir_order,
        grandtotal_order,
        metode_pembayaran,
        status_order,
        tanggal_order
    ) VALUES (
        '$order_id',
        '$nama',
        '$email',
        '$nohp',
        '$alamat',
        '$provinsi',
        '$kabupaten',
        '$subtotal',
        '$total_weight',
        '$ongkir',
        '$grand_total',
        '$metode_pembayaran',
        'Belum Bayar',
        NOW()
    )");

    if (!$order) {
        die("Error dalam menyimpan data order: " . mysqli_error($config));
    }

    foreach ($_SESSION['keranjang'] as $id_produk => $qty) {
        $product_query = mysqli_query($config, "SELECT harga_produk, stok_produk FROM tb_produk WHERE id_produk = '$id_produk'");
        $product = mysqli_fetch_assoc($product_query);
        $harga_produk = $product['harga_produk'];
        $stok_produk = $product['stok_produk'];

        $subtotal_keranjang = $qty * $harga_produk;

        if ($stok_produk >= $qty) {
            $new_stok = $stok_produk - $qty;
            mysqli_query($config, "UPDATE tb_produk SET stok_produk = '$new_stok' WHERE id_produk = '$id_produk'");
        } else {
            echo "<script>alert('Stok tidak mencukupi untuk produk $id_produk.'); window.location.href='./cart.php';</script>";
            exit;
        }

        $add_to_cart = mysqli_query($config, "INSERT INTO `tb_keranjang`(
            id_keranjang,
            id_produk,
            qty_keranjang,
            subtotal_keranjang
        ) VALUES (
            '$order_id',
            '$id_produk',
            '$qty',
            '$subtotal_keranjang'
        )");

        if (!$add_to_cart) {
            die("Error dalam menyimpan data keranjang: " . mysqli_error($config));
        }
    }

    // Encode Order ID untuk digunakan dalam URL dan PDF
    $encoded_order_id = urlencode($order_id);

    // Buat dan simpan PDF sementara
    $target_url = 'http://localhost/notepro_shop/cetak_pdf.php?orderid=' . $encoded_order_id;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $target_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $html = curl_exec($ch);
    curl_close($ch);

    if ($html === FALSE) {
        die("Gagal mendapatkan konten dari cetak_pdf.php melalui cURL.");
    }

    // Path ke folder invoices
    $invoice_folder = './invoices/';
    $pdf_file_name = "Invoice_$order_id.pdf";
    $pdf_path = $invoice_folder . $pdf_file_name;

    // Generate PDF dan simpan di folder invoices
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    file_put_contents($pdf_path, $dompdf->output());

    // Kirim Email Konfirmasi dengan PHPMailer
    try {
        $mail = new PHPMailer(true);

        // Konfigurasi server email
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = $smtp_secure;
        $mail->Port = $smtp_port;

        // Pengirim dan penerima
        $mail->setFrom($smtp_username, $nama_toko);
        $mail->addAddress($email, $nama);

        // Konten email
        $mail->isHTML(true);
        $mail->Subject = "Konfirmasi Pesanan $order_id - $nama_toko";
        $mail->Body = "
            <h2>Terima kasih atas pesanan Anda di $nama_toko!</h2>
            <p>Order ID: $order_id</p>
            <p>Total: Rp. " . number_format($grand_total, 0, ',', '.') . "</p>
            <p>Alamat: $alamat, $kabupaten, $provinsi</p>
            <p>Metode Pembayaran: Transfer $metode_pembayaran</p>
            <p>Silakan segera melakukan pembayaran ke rekening berikut dan upload bukti transfer:</p>
            <ul>
                <li>BRI: $rekening_bri</li>
                <li>BCA: $rekening_bca</li>
            </ul>
            <p>Upload bukti transfer melalui menu website untuk memproses pesanan.</p>
        ";

        // Tambahkan lampiran (invoice dalam bentuk PDF)
        $mail->addAttachment($pdf_path, $pdf_file_name);

        // Kirim email
        $mail->send();

        // Hapus file PDF setelah dikirim
        unlink($pdf_path);

    } catch (Exception $e) {
        echo "Email tidak dapat dikirim. Mailer Error: {$mail->ErrorInfo}";
    }

    // Hancurkan sesi
    session_unset(); 
    session_destroy(); 

    // Redirect setelah berhasil dengan Order ID di URL
    echo "<script> alert('Pesanan Anda Berhasil'); window.location.href='./check.php?orderid=$encoded_order_id'; </script>";
} else {
    echo "Tidak ada data yang dikirim.";
}
?>
