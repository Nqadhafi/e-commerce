<?php
include('../config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dompdf\Dompdf;
use Dompdf\Options;
require '../vendor/autoload.php'; // Pastikan path ini benar dan mengarah ke autoload.php di folder vendor

// Query untuk mendapatkan pesanan yang belum selesai (Belum Bayar, Proses Verifikasi, Sudah Bayar, Pengiriman)
$query = mysqli_query($config, "SELECT * FROM tb_order WHERE status_order != 'Selesai'");
$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

// Fungsi untuk mengirim notifikasi pengiriman via email
function sendShipmentNotification($email, $order_id, $resi) {
    global $smtp_host, $smtp_username, $smtp_password, $smtp_secure, $smtp_port, $nama_toko;

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
        $mail->addAddress($email);

        // Konten email
        $mail->isHTML(true);
        $mail->Subject = "Pesanan Anda telah dikirim";
        $mail->Body = "
            <h2>Pesanan Anda dengan Order ID: $order_id telah dikirim!</h2>
            <p>Nomor Resi: $resi</p>
            <p>Terima kasih telah berbelanja di $nama_toko. Pesanan Anda sedang dalam perjalanan dan akan segera sampai di alamat yang Anda berikan.</p>
            <p>Salam hangat,</p>
            <p>$nama_toko</p>
        ";

        // Buat PDF untuk lampiran
        $encoded_order_id = urlencode($order_id);
        $target_url = 'http://localhost/notepro_shop/cetak_pdf.php?orderid=' . $encoded_order_id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($ch);
        curl_close($ch);

        if ($html === FALSE) {
            throw new Exception("Gagal mendapatkan konten dari cetak_pdf.php melalui cURL.");
        }

        $invoice_folder = '../invoices/';
        $pdf_file_name = "Invoice_$order_id.pdf";
        $pdf_path = $invoice_folder . $pdf_file_name;

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        file_put_contents($pdf_path, $dompdf->output());

        // Tambahkan lampiran (invoice dalam bentuk PDF)
        $mail->addAttachment($pdf_path, $pdf_file_name);

        // Kirim email
        $mail->send();

        // Hapus file PDF setelah dikirim
        unlink($pdf_path);

    } catch (Exception $e) {
        echo "Email tidak dapat dikirim. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Fungsi untuk mengirim email konfirmasi pembayaran
function sendPaymentConfirmationEmail($email, $order_id) {
    global $smtp_host, $smtp_username, $smtp_password, $smtp_secure, $smtp_port, $nama_toko, $no_whatsapp,$rekening_bri, $rekening_bca;
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
        $mail->addAddress($email);

        // Konten email
        $mail->isHTML(true);
        $mail->Subject = "Pembayaran untuk Order ID: $order_id telah Dikonfirmasi";
        $mail->Body = "
            <h2>Pembayaran Anda telah berhasil dikonfirmasi!</h2>
            <p>Order ID: $order_id</p>
            <p>Terima kasih telah melakukan pembayaran. Pesanan Anda sedang diproses dan akan segera dikirimkan.</p>
            <p>Anda dapat menghubungi kami melalui $no_whatsapp untuk informasi lebih lanjut.</p>
            <p>Salam hangat,</p>
            <p>$nama_toko</p>
        ";

        // Kirim email
        $mail->send();

    } catch (Exception $e) {
        echo "Email tidak dapat dikirim. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Cek aksi dari URL
if (isset($_GET['acc']) || isset($_GET['pending']) || isset($_GET['hapus']) || isset($_GET['konfirmasi'])) {
    $order_id = filter_input(INPUT_GET, 'acc', FILTER_SANITIZE_STRING) 
        ?? filter_input(INPUT_GET, 'pending', FILTER_SANITIZE_STRING) 
        ?? filter_input(INPUT_GET, 'hapus', FILTER_SANITIZE_STRING)
        ?? filter_input(INPUT_GET, 'konfirmasi', FILTER_SANITIZE_STRING);

    if (isset($_GET['acc'])) {
        $action = mysqli_query($config, "UPDATE tb_order SET status_order ='Selesai' WHERE id_order = '" . mysqli_real_escape_string($config, $order_id) . "' AND status_order = 'Pengiriman'");
        if (!$action) {
            echo "Error: " . mysqli_error($config);
            exit;
        }
    } elseif (isset($_GET['pending'])) {
        $action = mysqli_query($config, "UPDATE tb_order SET status_order ='Pending' WHERE id_order = '" . mysqli_real_escape_string($config, $order_id) . "'");
        if (!$action) {
            echo "Error: " . mysqli_error($config);
            exit;
        }
    } elseif (isset($_GET['hapus'])) {
        // Ambil data produk dari tb_keranjang sebelum menghapus pesanan
        $query_items = mysqli_query($config, "SELECT id_produk, qty_keranjang FROM tb_keranjang WHERE id_keranjang = '" . mysqli_real_escape_string($config, $order_id) . "'");
        $items = mysqli_fetch_all($query_items, MYSQLI_ASSOC);
        
        // Kembalikan stok produk
        foreach ($items as $item) {
            $id_produk = $item['id_produk'];
            $qty_keranjang = $item['qty_keranjang'];
            
            // Update stok produk
            mysqli_query($config, "UPDATE tb_produk SET stok_produk = stok_produk + $qty_keranjang WHERE id_produk = '$id_produk'");
        }

        // Hapus data pesanan dan keranjang
        $action = mysqli_query($config, "DELETE FROM tb_order WHERE id_order = '" . mysqli_real_escape_string($config, $order_id) . "'");
        $action2 = mysqli_query($config, "DELETE FROM tb_keranjang WHERE id_keranjang = '" . mysqli_real_escape_string($config, $order_id) . "'");
        if (!$action || !$action2) {
            echo "Error: " . mysqli_error($config);
            exit;
        }
    } elseif (isset($_GET['konfirmasi'])) {
        $action = mysqli_query($config, "UPDATE tb_order SET status_order = 'Sudah Bayar' WHERE id_order = '" . mysqli_real_escape_string($config, $order_id) . "' AND status_order = 'Proses Verifikasi'");
        if (!$action) {
            echo "Error: " . mysqli_error($config);
            exit;
        }

        // Ambil informasi pelanggan untuk mengirim email
        $customer_query = mysqli_query($config, "SELECT email_order FROM tb_order WHERE id_order = '$order_id'");
        $customer = mysqli_fetch_assoc($customer_query);
        $email = $customer['email_order'];

        // Kirim notifikasi email pembayaran dikonfirmasi
        sendPaymentConfirmationEmail($email, $order_id);
    }

    // Redirect setelah aksi
    header('Location: ?page=check');
    exit();
}
?>

<div class="container d-flex flex-column justify-content-center">
    <h4 class="text-center mt-3">List Pesanan</h4>

    <div class="table-responsive">
        <table class="table table-striped border">
            <thead class="table-primary">
                <tr>
                    <th>No.</th>
                    <th>Order ID</th>
                    <th>Nama Customer</th>
                    <th>No. Hp</th>
                    <th>Nomor Resi</th>
                    <th>Tanggal Order</th>
                    <th>Grand Total</th>
                    <th>Status</th>
                    <th>Ubah Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($data as $row) : ?>
                    <tr>
                        <?php
                        $tanggal_order = date('d-m-Y', strtotime($row['tanggal_order']));
                        $class = "";
                        if ($row['status_order'] == "Selesai") {
                            $class = "text-success fw-bolder";
                        } else if ($row['status_order'] == "Pending") {
                            $class = "text-warning fw-bolder";
                        } else if ($row['status_order'] == "Pengiriman") {
                            $class = "text-primary fw-bolder";
                        } else if ($row['status_order'] == "Proses Verifikasi") {
                            $class = "text-info fw-bolder";
                        } else if ($row['status_order'] == "Belum Bayar") {
                            $class = "text-danger fw-bolder";
                        }
                        ?>
                        <td><?php echo $no++ . "."; ?></td>
                        <td><?php echo htmlspecialchars($row['id_order']); ?></td>
                        <td><?php echo htmlspecialchars($row['namacust_order']); ?></td>
                        <td><?php echo htmlspecialchars($row['nohp_order']); ?></td>
                        <td><?php echo htmlspecialchars($row['resi_order']); ?></td>
                        <td><?php echo htmlspecialchars($tanggal_order); ?></td>
                        <td>Rp.<?php echo number_format($row['grandtotal_order'], 0, ',', '.'); ?></td>
                        <td class="<?php echo $class ?>"><?php echo htmlspecialchars($row['status_order']); ?></td>
                        <td>
                            <?php if ($row['status_order'] == "Sudah Bayar" || $row['status_order'] == "Pengiriman") : ?>
                                <a href="?page=check&acc=<?php echo urlencode($row['id_order']); ?>" class="btn btn-success mb-1" onclick="return confirm('Anda yakin mengubah status order ini menjadi Selesai?');">Selesai</a>
                                <?php if ($row['status_order'] == "Sudah Bayar") : ?>
                                    <button type="button" class="btn btn-primary mb-1" data-bs-toggle="modal" data-bs-target="#resiModal" data-id="<?php echo $row['id_order']; ?>">
                                        Pengiriman
                                    </button>
                                <?php endif; ?>
                            <?php elseif ($row['status_order'] == "Proses Verifikasi") : ?>
                                <a href="?page=check&konfirmasi=<?php echo urlencode($row['id_order']); ?>" class="btn btn-warning mb-1" onclick="return confirm('Anda yakin ingin mengonfirmasi pembayaran?');">Konfirmasi Pembayaran</a>
                                <button type="button" class="btn btn-secondary mb-1" data-bs-toggle="modal" data-bs-target="#buktiModal" data-id="<?php echo $row['id_order']; ?>" data-bukti="<?php echo urlencode($row['id_order']) . '_' . $row['bukti_bayar']; ?>">
                                    Lihat Bukti
                                </button>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="./generate_pdf.php?orderid=<?php echo urlencode($row['id_order']); ?>" target="_blank" class="btn btn-info mb-1">Cetak PDF</a>
                            <a href="?page=check&hapus=<?php echo urlencode($row['id_order']); ?>" class="btn btn-danger mb-1" onclick="return confirm('Anda yakin menghapus produk ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal untuk input nomor resi -->
<div class="modal fade" id="resiModal" tabindex="-1" aria-labelledby="resiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="resiModalLabel">Masukkan Nomor Resi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="order_id" value="">
                    <div class="mb-3">
                        <label for="resi" class="form-label">Nomor Resi</label>
                        <input type="text" class="form-control" id="resi" name="resi" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" name="submitResi">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal untuk melihat bukti transfer -->
<div class="modal fade" id="buktiModal" tabindex="-1" aria-labelledby="buktiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="buktiModalLabel">Bukti Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img src="" id="bukti-img" class="img-fluid" alt="Bukti Transfer">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?php
// Proses input nomor resi dan update status menjadi Pengiriman
if (isset($_POST['submitResi'])) {
    $order_id = mysqli_real_escape_string($config, filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_STRING));
    $resi = mysqli_real_escape_string($config, filter_input(INPUT_POST, 'resi', FILTER_SANITIZE_STRING));
    $action = mysqli_query($config, "UPDATE tb_order SET status_order ='Pengiriman', resi_order='$resi' WHERE id_order = '$order_id' AND status_order = 'Sudah Bayar'");
    if (!$action) {
        echo "Error: " . mysqli_error($config);
        exit;
    }

    // Ambil informasi pelanggan untuk mengirim email
    $customer_query = mysqli_query($config, "SELECT email_order FROM tb_order WHERE id_order = '$order_id'");
    $customer = mysqli_fetch_assoc($customer_query);
    $email = $customer['email_order'];

    // Kirim notifikasi email
    sendShipmentNotification($email, $order_id, $resi);

    header('Location: ?page=check');
    exit();
}
?>

<script>
    var resiModal = document.getElementById('resiModal');
    resiModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var orderId = button.getAttribute('data-id');
        var modalOrderInput = resiModal.querySelector('#order_id');
        modalOrderInput.value = orderId;
    });

    var buktiModal = document.getElementById('buktiModal');
    buktiModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var buktiImgSrc = "../assets/bukti_bayar/" + button.getAttribute('data-bukti');
        var buktiImg = buktiModal.querySelector('#bukti-img');
        buktiImg.src = buktiImgSrc;
    });
</script>
