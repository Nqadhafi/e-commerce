<?php

if (isset($_POST['tambah'])){
    $p_nama = $_POST['nama_produk'];
    $p_harga = $_POST['harga_produk'];
    $p_deskripsi = $_POST['deskripsi_produk'];
    $p_gambar = $_FILES['gambar_produk']['name'];
    $p_gambar_temp = $_FILES['gambar_produk']['tmp_name'];
    $p_folder_upload = '../assets/uploads/'.$p_gambar;

    $query = mysqli_query($config, "INSERT INTO `tb_produk`(
    nama_produk,
    harga_produk,
    deskripsi_produk,
    gambar_produk
    ) 
    VALUES (
    '$p_nama',
    '$p_harga',
    '$p_deskripsi',
    '$p_gambar'
    )");

    if($query){
        move_uploaded_file($p_gambar_temp, $p_folder_upload);
        echo "Sukses tambah";
    }
    else{
        echo "Tambah gagal";
    }
    
}
?>

<form method="post" enctype="multipart/form-data">
    <div class="container d-flex flex-column justify-content-center align-self-center align-items-center">
        <div class="mt-3 p-3 border rounded">
            <h4 class="text-center">Tambah Produk</h4>
            <div class="mb-2 ">
                <label for="nama_produk" class="form-label">Nama Produk :</label>
                <input type="text" class="form-control" name="nama_produk" required>
            </div>
            <div class="mb-2 ">
                <label for="harga_produk" class="form-label">Harga Produk (Rp)</label>
                <input type="number" class="form-control" name="harga_produk" required>
            </div>
            <div class="mb-2 ">
                <label for="deskripsi_produk" class="form-label">Deskripsi produk :</label>
                <textarea name="deskripsi_produk" class="form-control" id=""></textarea>
            </div>
            <div class="mb-2">
                <label for="formFile" class="form-label" >Gambar Produk (png/jpg)</label>
                <input class="form-control" type="file" name="gambar_produk" id="formFile" accept="image/png, image/jpg, image/jpeg" required>
            </div>
            <div class="text-center mt-5 mb-3">
            <input type="submit" value="Tambah Produk" name="tambah" class=" btn btn-primary px-3 py-1">
      
        </div>
        </div>
        
    </div>
    </div>
</form>