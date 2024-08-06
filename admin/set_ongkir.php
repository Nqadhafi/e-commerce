<?php
include('../config.php');

// Mengambil data provinsi
$query = mysqli_query($config, "SELECT * FROM tb_ongkir");
$data_provinsi = mysqli_fetch_all($query, MYSQLI_ASSOC);

// Mengambil harga ongkir berdasarkan provinsi yang dipilih
$selected_provinsi = '';
$selected_ongkir = '';

if (isset($_POST['provinsi'])) {
    $selected_provinsi = $_POST['provinsi'];
    $query = mysqli_query($config, "SELECT jumlah_ongkir FROM tb_ongkir WHERE id_ongkir = '$selected_provinsi'");
    $result = mysqli_fetch_assoc($query);
    $selected_ongkir = $result['jumlah_ongkir'];
}

// Mengupdate ongkir jika form disubmit
if (isset($_POST['ongkir'])) {
    $provinsi = $_POST['provinsi'];
    $jumlah_ongkir = $_POST['jumlah_ongkir'];
    $update_ongkir = mysqli_query($config, "UPDATE tb_ongkir SET jumlah_ongkir = '$jumlah_ongkir' WHERE id_ongkir = '$provinsi'");
    if ($update_ongkir) {
        echo "<script> alert('Ongkir berhasil diupdate'); window.location.href='./index.php?page=ongkir'; </script>";
        exit();
    } else {
        echo "<script> alert('Ongkir gagal diupdate'); window.location.href='./index.php?page=ongkir'; </script>";
    }
}
?>

<form method="post" enctype="multipart/form-data">
    <div class="container d-flex flex-column justify-content-center align-self-center align-items-center">
        <div class="mt-3 p-3 border rounded">
            <h4 class="text-center">Set Harga Ongkir</h4>
            <div class="mb-2">
                <label for="provinsi" class="form-label">Provinsi:</label>
                <select class="form-select" name="provinsi" onchange="this.form.submit()">
                    <option value="" disabled selected>Pilih Provinsi</option>
                    <?php foreach ($data_provinsi as $provinsi): ?>
                        <option value="<?php echo $provinsi['id_ongkir']; ?>" <?php if ($provinsi['id_ongkir'] == $selected_provinsi) echo 'selected'; ?>>
                            <?php echo $provinsi['provinsi_ongkir']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-2">
                <label for="jumlah_ongkir" class="form-label">Jumlah Ongkir (Rp):</label>
                <input type="number" value="<?php echo $selected_ongkir; ?>" class="form-control" name="jumlah_ongkir" required>
            </div>
            <div class="text-center mt-5 mb-3">
                <input type="submit" value="Update Ongkir" name="ongkir" class="btn btn-primary px-3 py-1">
            </div>
        </div>
    </div>
</form>

<div class="container d-flex flex-column justify-content-center">
    <h4 class="text-center mt-3">List Ongkir</h4>
    <table class="table table-striped table-bordered">
        <thead class="table-primary">
            <tr>
                <th>No</th>
                <th>Nama Provinsi</th>
                <th>Jumlah Ongkir</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($data_provinsi as $provinsi) : ?>
                <tr>
                    <td><?php echo $no++ . "."; ?></td>
                    <td><?php echo $provinsi['provinsi_ongkir']; ?></td>
                    <td>Rp. <?php echo number_format($provinsi['jumlah_ongkir'], 0, ',', '.'); ?>,-</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
