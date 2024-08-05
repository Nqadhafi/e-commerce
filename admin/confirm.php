<?php
include('../config.php');

// Query untuk mendapatkan data pesanan
$query = mysqli_query($config, "SELECT * FROM tb_order");
$data = mysqli_fetch_all($query, MYSQLI_ASSOC);
?>

<div class="container d-flex flex-column justify-content-center overflow-x-auto">
    <h4 class="text-center mt-3">List Pesanan</h4>
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
                <th class="w-25">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($data as $row) : ?>
                <tr>
                    <?php 
                    if($row['status_order'] == "Selesai"){
                        $class = "text-success fw-bolder";
                    }
                    else if($row['status_order'] == "Pending"){
                        $class = "text-warning fw-bolder";
                    }
                    else if($row['status_order'] == "Pengiriman"){
                        $class = "text-primary fw-bolder";
                    }
                    ?>
                    <td><?php echo $no++ . "."; ?></td>
                    <td><?php echo htmlspecialchars($row['id_order']); ?></td>
                    <td><?php echo htmlspecialchars($row['namacust_order']); ?></td>
                    <td><?php echo htmlspecialchars($row['resi_order']); ?></td>
                    <td><?php echo htmlspecialchars($row['tanggal_order']); ?></td>
                    <td>Rp.<?php echo number_format($row['grandtotal_order'], 0, ',', '.'); ?></td>
                    <td class="<?php echo $class?>"><?php echo htmlspecialchars($row['status_order']); ?></td>
                    <td>
                        <a href="?page=check&acc=<?php echo urlencode($row['id_order']); ?>" class="btn btn-success mb-1" onclick="return confirm('Anda yakin mengubah status order ini?');">Selesai</a>
                        <button type="button" class="btn btn-primary mb-1" data-bs-toggle="modal" data-bs-target="#resiModal" data-id="<?php echo $row['id_order']; ?>">
                            Pengiriman
                        </button>
                        <a href="?page=check&pending=<?php echo urlencode($row['id_order']); ?>" class="btn btn-warning mb-1" onclick="return confirm('Anda yakin mengubah status order ini?');">Pending</a>
                        <a href="?page=check&hapus=<?php echo urlencode($row['id_order']); ?>" class="btn btn-danger mb-1" onclick="return confirm('Anda yakin menghapus produk ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_STRING);
    $resi = filter_input(INPUT_POST, 'resi', FILTER_SANITIZE_STRING);
    $action = mysqli_query($config, "UPDATE tb_order SET status_order ='Pengiriman', resi_order='$resi' WHERE id_order = '$order_id'");
    header('Location: ./?page=check');
}

// Cek aksi dari URL
if (isset($_GET['acc'])) {
    $order_id = filter_input(INPUT_GET, 'acc', FILTER_SANITIZE_STRING);
    $action = mysqli_query($config, "UPDATE tb_order SET status_order ='Selesai' WHERE id_order = '$order_id'");
    header('Location: ./?page=check');
} 
elseif (isset($_GET['pending'])) {
    $order_id = filter_input(INPUT_GET, 'pending', FILTER_SANITIZE_STRING);
    $action = mysqli_query($config, "UPDATE tb_order SET status_order ='Pending' WHERE id_order = '$order_id'");
    header('Location: ./?page=check');
} elseif (isset($_GET['hapus'])) {
    $order_id = filter_input(INPUT_GET, 'hapus', FILTER_SANITIZE_STRING);
    $action = mysqli_query($config, "DELETE FROM tb_order WHERE id_order = '$order_id'");
    $action2 = mysqli_query($config, "DELETE FROM tb_keranjang WHERE id_keranjang = '$order_id'");
    header('Location: ./?page=check');
}
?>

<script>
    var resiModal = document.getElementById('resiModal');
    resiModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var orderId = button.getAttribute('data-id');
        var modalOrderInput = resiModal.querySelector('#order_id');
        modalOrderInput.value = orderId;
    });
</script>
