<?php
include('../config.php');

$produk = null; // Inisialisasi variabel $produk
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Ambil data produk berdasarkan ID
    $query = mysqli_query($config, "SELECT * FROM tb_produk WHERE id_produk = '$id'");
    if ($query && mysqli_num_rows($query) > 0) {
        $produk = mysqli_fetch_assoc($query);
    } else {
        echo "<script> alert('Produk tidak ditemukan'); window.location.href='./index.php'; </script>";
        exit();
    }
}

if (isset($_POST['update'])) {
    $p_nama = $_POST['nama_produk'];
    $p_harga = $_POST['harga_produk'];
    $p_berat = $_POST['berat_produk'];
    $p_stok = $_POST['stok_produk'];
    $p_deskripsi = $_POST['deskripsi_produk'];
    $p_gambar = $_FILES['gambar_produk']['name'];
    $p_gambar_temp = $_FILES['gambar_produk']['tmp_name'];

    // Jika gambar diupload
    if ($p_gambar != "") {
        // Generate unique name for the file
        $file_extension = pathinfo($p_gambar, PATHINFO_EXTENSION);
        $unique_name = uniqid() . '.' . $file_extension;
        $p_folder_upload = '../assets/uploads/' . $unique_name;

        // Hapus gambar lama dari server
        if ($produk && !empty($produk['gambar_produk'])) {
            $old_image_path = '../assets/uploads/' . $produk['gambar_produk'];
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
        }

        // Pindahkan gambar baru ke folder uploads
        move_uploaded_file($p_gambar_temp, $p_folder_upload);

        // Update query termasuk gambar baru
        $query = mysqli_query($config, "UPDATE tb_produk SET 
            nama_produk = '$p_nama', 
            harga_produk = '$p_harga', 
            berat_produk = '$p_berat', 
            stok_produk = '$p_stok', 
            deskripsi_produk = '$p_deskripsi', 
            gambar_produk = '$unique_name' 
            WHERE id_produk = '$id'");
    } else {
        // Update query tanpa gambar baru
        $query = mysqli_query($config, "UPDATE tb_produk SET 
            nama_produk = '$p_nama', 
            harga_produk = '$p_harga', 
            berat_produk = '$p_berat', 
            stok_produk = '$p_stok',
            deskripsi_produk = '$p_deskripsi' 
            WHERE id_produk = '$id'");
    }

    if ($query) {
        echo "<script> alert('Produk berhasil diupdate'); window.location.href='./index.php'; </script>";
        exit();
    } else {
        echo "<script> alert('Produk gagal diupdate'); window.location.href='./index.php'; </script>";
    }
}
?>
<form method="post" enctype="multipart/form-data">
    <div class="container d-flex flex-column justify-content-center align-self-center align-items-center">
        <div class="mt-3 p-3 border rounded">
            <h4 class="text-center">Update Produk</h4>
            <div class="product-img text-center">
                            <img src="../assets/uploads/<?php echo $produk['gambar_produk'] ?>" alt="">
                        </div>
            <div class="mb-2">
                <label for="nama_produk" class="form-label">Nama Produk:</label>
                <input type="text" value="<?php echo isset($produk['nama_produk']) ? $produk['nama_produk'] : ''; ?>" class="form-control" name="nama_produk" required>
            </div>
            <div class="mb-2">
                <label for="harga_produk" class="form-label">Harga Produk (Rp):</label>
                <input type="number" value="<?php echo isset($produk['harga_produk']) ? $produk['harga_produk'] : ''; ?>" min="1" class="form-control" name="harga_produk" required>
            </div>
            <div class="mb-2">
                <label for="berat_produk" class="form-label">Berat Produk (gram):</label>
                <input type="number" value="<?php echo isset($produk['berat_produk']) ? $produk['berat_produk'] : ''; ?>" min="1" class="form-control" name="berat_produk" required>
            </div>
            <div class="mb-2">
                <label for="stok_produk" class="form-label">Stok Produk:</label>
                <input type="number" value="<?php echo isset($produk['stok_produk']) ? $produk['stok_produk'] : ''; ?>" min="1" class="form-control" name="stok_produk" required>
            </div>
            <div class="mb-2">
                <label for="deskripsi_produk" class="form-label">Deskripsi Produk:</label>
                <textarea name="deskripsi_produk" class="form-control"><?php echo isset($produk['deskripsi_produk']) ? $produk['deskripsi_produk'] : ''; ?></textarea>
            </div>
            <div class="mb-2">
                <label for="formFile" class="form-label">Gambar Produk (png/jpg):</label>
                <input class="form-control" type="file" name="gambar_produk" id="formFile" accept="image/png, image/jpg, image/jpeg">
            </div>
            <div class="text-center mt-5 mb-3">
                <input type="submit" value="Update Produk" name="update" class="btn btn-primary px-3 py-1">
            </div>
        </div>
    </div>
</form>
