<?php
$query = mysqli_query($config, "SELECT * FROM tb_order");

$data = mysqli_fetch_all($query, MYSQLI_ASSOC);
?>

<div class="container d-flex flex-column justify-content-center">
        <h4 class="text-center mt-3">List Pesanan</h4>
        <table  class="table table-striped border" >
            <thead class="table-primary">
                <td>No.</td>
                <td>Nama Customer</td>
                <td>Order ID</td>
                <td>Subtotal</td>
                <td>Status</td>
                <td class="w-25">Action</td>

            </thead>
            <?php foreach ($data as $data) : ?>
            <tr>
                <td>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    <a href="" class="btn btn-success mb-1">Konfirmasi</a>
                    <a href="" class="btn btn-warning mb-1">Batal</a>
                    <a href="" class="btn btn-danger mb-1">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>