<?php
include('../config.php');

// Ambil bulan yang dipilih dari dropdown (default: tidak memfilter)
$selected_month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_STRING);

// Query untuk mendapatkan daftar bulan dari pesanan yang selesai
$month_query = mysqli_query($config, "SELECT DISTINCT DATE_FORMAT(tanggal_order, '%Y-%m') as month FROM tb_order WHERE status_order = 'Selesai' ORDER BY month DESC");
$months = mysqli_fetch_all($month_query, MYSQLI_ASSOC);

// Filter data pesanan berdasarkan bulan yang dipilih
$filter_query = " WHERE status_order = 'Selesai'";
if ($selected_month) {
    $filter_query .= " AND DATE_FORMAT(tanggal_order, '%Y-%m') = '" . mysqli_real_escape_string($config, $selected_month) . "'";
}
$query = mysqli_query($config, "SELECT * FROM tb_order" . $filter_query);
$data = mysqli_fetch_all($query, MYSQLI_ASSOC);
?>

<div class="container d-flex flex-column justify-content-center">
    <h4 class="text-center mt-3">List Pesanan Selesai</h4>
    <!-- Dropdown filter bulan -->
    <form method="get" class="mb-3">
        <input type="hidden" name="page" value="finished">
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
                    <th>No. Hp</th>
                    <th>Nomor Resi</th>
                    <th>Tanggal Order</th>
                    <th>Grand Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($data as $row) : ?>
                    <tr>
                        <?php
                        $tanggal_order = date('d-m-Y', strtotime($row['tanggal_order']));
                        ?>
                        <td><?php echo $no++ . "."; ?></td>
                        <td><?php echo htmlspecialchars($row['id_order']); ?></td>
                        <td><?php echo htmlspecialchars($row['namacust_order']); ?></td>
                        <td><?php echo htmlspecialchars($row['nohp_order']); ?></td>
                        <td><?php echo htmlspecialchars($row['resi_order']); ?></td>
                        <td><?php echo htmlspecialchars($tanggal_order); ?></td>
                        <td>Rp.<?php echo number_format($row['grandtotal_order'], 0, ',', '.'); ?></td>
                        <td>
                            <a href="./generate_pdf.php?orderid=<?php echo urlencode($row['id_order']); ?>" target="_blank" class="btn btn-info mb-1">Cetak PDF</a>
                            <button type="button" class="btn btn-secondary mb-1" data-bs-toggle="modal" data-bs-target="#buktiModal" data-id="<?php echo $row['id_order']; ?>" data-bukti="<?php echo urlencode($row['id_order']) . '_' . $row['bukti_bayar']; ?>">
                                Lihat Bukti
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="mb-3 text-center">
            <a href="./generate_pdf_all.php?month=<?php echo urlencode($selected_month); ?>" target="_blank" class="btn btn-primary">Cetak Laporan PDF</a>
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

<script>
    var buktiModal = document.getElementById('buktiModal');
    buktiModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var buktiImgSrc = "../assets/bukti_bayar/" + button.getAttribute('data-bukti');
        var buktiImg = buktiModal.querySelector('#bukti-img');
        buktiImg.src = buktiImgSrc;
    });
</script>
