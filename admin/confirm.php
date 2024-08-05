<?php
include('../config.php');

// Ambil bulan yang dipilih dari dropdown (default: tidak memfilter)
$selected_month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_STRING);

// Query untuk mendapatkan daftar bulan
$month_query = mysqli_query($config, "SELECT DISTINCT DATE_FORMAT(tanggal_order, '%Y-%m') as month FROM tb_order ORDER BY month DESC");
$months = mysqli_fetch_all($month_query, MYSQLI_ASSOC);

// Filter data pesanan berdasarkan bulan yang dipilih
$filter_query = "";
if ($selected_month) {
    $filter_query = " WHERE DATE_FORMAT(tanggal_order, '%Y-%m') = '" . mysqli_real_escape_string($config, $selected_month) . "'";
}
$query = mysqli_query($config, "SELECT * FROM tb_order" . $filter_query);
$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

// Cek aksi dari URL
if (isset($_GET['acc']) || isset($_GET['pending']) || isset($_GET['hapus'])) {
    $order_id = filter_input(INPUT_GET, 'acc', FILTER_SANITIZE_STRING) 
        ?? filter_input(INPUT_GET, 'pending', FILTER_SANITIZE_STRING) 
        ?? filter_input(INPUT_GET, 'hapus', FILTER_SANITIZE_STRING);

    if (isset($_GET['acc'])) {
        $action = mysqli_query($config, "UPDATE tb_order SET status_order ='Selesai' WHERE id_order = '" . mysqli_real_escape_string($config, $order_id) . "'");
    } elseif (isset($_GET['pending'])) {
        $action = mysqli_query($config, "UPDATE tb_order SET status_order ='Pending' WHERE id_order = '" . mysqli_real_escape_string($config, $order_id) . "'");
    } elseif (isset($_GET['hapus'])) {
        $action = mysqli_query($config, "DELETE FROM tb_order WHERE id_order = '" . mysqli_real_escape_string($config, $order_id) . "'");
        $action2 = mysqli_query($config, "DELETE FROM tb_keranjang WHERE id_keranjang = '" . mysqli_real_escape_string($config, $order_id) . "'");
    }

    // Redirect setelah aksi
    header('Location: ?page=check');
    exit();
}
?>

<div class="container d-flex flex-column justify-content-center">
    <h4 class="text-center mt-3">List Pesanan</h4>
    <!-- Dropdown filter bulan -->
    <form method="get" class="mb-3">
        <input type="hidden" name="page" value="check">
        <div class="row">
            <div class="col-md-4 offset-md-4">
                <div class="input-group">
                    <select name="month" class="form-select" onchange="this.form.submit()">
                        <option value="">Pilih Bulan</option>
                        <?php foreach ($months as $month) : ?>
                            <option value="<?php echo htmlspecialchars($month['month']); ?>" <?php echo ($selected_month === $month['month']) ? 'selected' : ''; ?>>
                                <?php echo date('F Y', strtotime($month['month'] . '-01')); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-striped border">
            <thead class="table-primary">
                <tr>
                    <th>No.</th>
                    <th>Order ID</th>
                    <th>Nama Customer</th>
                    <th>Nomor Resi</th>
                    <th>Tanggal Order</th>
                    <th>Grand Total</th>
                    <th>Status</th>
                    <th class="">Ubah Status</th>
                    <th class="">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($data as $row) : ?>
                    <tr>
                        <?php
                        $class = "";
                        if ($row['status_order'] == "Selesai") {
                            $class = "text-success fw-bolder";
                        } else if ($row['status_order'] == "Pending") {
                            $class = "text-warning fw-bolder";
                        } else if ($row['status_order'] == "Pengiriman") {
                            $class = "text-primary fw-bolder";
                        }
                        ?>
                        <td><?php echo $no++ . "."; ?></td>
                        <td><?php echo htmlspecialchars($row['id_order']); ?></td>
                        <td><?php echo htmlspecialchars($row['namacust_order']); ?></td>
                        <td><?php echo htmlspecialchars($row['resi_order']); ?></td>
                        <td><?php echo htmlspecialchars($row['tanggal_order']); ?></td>
                        <td>Rp.<?php echo number_format($row['grandtotal_order'], 0, ',', '.'); ?></td>
                        <td class="<?php echo $class ?>"><?php echo htmlspecialchars($row['status_order']); ?></td>
                        <td>
                            <a href="?page=check&acc=<?php echo urlencode($row['id_order']); ?>" class="btn btn-success mb-1" onclick="return confirm('Anda yakin mengubah status order ini?');">Selesai</a>
                            <button type="button" class="btn btn-primary mb-1" data-bs-toggle="modal" data-bs-target="#resiModal" data-id="<?php echo $row['id_order']; ?>">
                                Pengiriman
                            </button>
                            <a href="?page=check&pending=<?php echo urlencode($row['id_order']); ?>" class="btn btn-warning mb-1" onclick="return confirm('Anda yakin mengubah status order ini?');">Pending</a>
                        </td>
                        <td>
                            <a href="./generate_pdf.php?orderid=<?php echo urlencode($row['id_order']); ?>" target="_blank" class="btn btn-info mb-1">Cetak PDF</a>
                            <a href="?page=check&hapus=<?php echo urlencode($row['id_order']); ?>" class="btn btn-danger mb-1" onclick="return confirm('Anda yakin menghapus produk ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="mb-3">
        <a href="./generate_pdf_all.php?month=<?php echo urlencode($selected_month); ?>" target="_blank"class="btn btn-primary">Cetak Laporan PDF</a>
    </div>
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

<?php
// Proses input nomor resi dan update status menjadi Pengiriman
if (isset($_POST['submitResi'])) {
    $order_id = mysqli_real_escape_string($config, filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_STRING));
    $resi = mysqli_real_escape_string($config, filter_input(INPUT_POST, 'resi', FILTER_SANITIZE_STRING));
    $action = mysqli_query($config, "UPDATE tb_order SET status_order ='Pengiriman', resi_order='$resi' WHERE id_order = '$order_id'");
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
</script>
