<?php
include('../config.php');

// Query untuk mendapatkan data pesanan
$query = mysqli_query($config, "SELECT * FROM tb_order");

$data = mysqli_fetch_all($query, MYSQLI_ASSOC);
?>

<div class="container d-flex flex-column justify-content-center">
    <h4 class="text-center mt-3">List Pesanan</h4>
    <table class="table table-striped border">
        <thead class="table-primary">
            <tr>
                <th>No.</th>
                <th>Nama Customer</th>
                <th>Order ID</th>
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
                    ?>
                    <td><?php echo $no++ . "."; ?></td>
                    <td><?php echo htmlspecialchars($row['namacust_order']); ?></td>
                    <td><?php echo htmlspecialchars($row['id_order']); ?></td>
                    <td>Rp.<?php echo number_format($row['grandtotal_order'], 0, ',', '.'); ?></td>
                    <td class="<?php echo $class?>"><?php echo htmlspecialchars($row['status_order']); ?></td>
                    <td>
                        <a href="?page=check&acc=<?php echo urlencode($row['id_order']); ?>" class="btn btn-success mb-1" onclick="return confirm('Anda yakin menyelesaikan produk ini?');">Konfirmasi</a>
                        <a href="?page=check&batal=<?php echo urlencode($row['id_order']); ?>" class="btn btn-warning mb-1" onclick="return confirm('Anda yakin membatalkan produk ini?');">Batal</a>
                        <a href="?page=check&hapus=<?php echo urlencode($row['id_order']); ?>" class="btn btn-danger mb-1" onclick="return confirm('Anda yakin menghapus produk ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
// Cek aksi dari URL
if (isset($_GET['acc'])) {
    $order_id = filter_input(INPUT_GET, 'acc', FILTER_SANITIZE_STRING);
    echo $order_id;
    $action = mysqli_query($config, "UPDATE tb_order SET status_order ='Selesai' WHERE id_order = '$order_id'");
    header('Location: ./?page=check');
} elseif (isset($_GET['batal'])) {
    $order_id = filter_input(INPUT_GET, 'batal', FILTER_SANITIZE_STRING);
    // Proses pembatalan pesanan
    $action = mysqli_query($config, "UPDATE tb_order SET status_order ='Pending' WHERE id_order = '$order_id'");
    header('Location: ./?page=check');
} elseif (isset($_GET['hapus'])) {
    $order_id = filter_input(INPUT_GET, 'hapus', FILTER_SANITIZE_STRING);
    // Proses penghapusan pesanan
    $action = mysqli_query($config, "DELETE FROM tb_order WHERE id_order = '$order_id'");
    $action2 = mysqli_query($config, "DELETE FROM tb_keranjang WHERE id_keranjang = '$order_id'");
    header('Location: ./?page=check');
}
?>
