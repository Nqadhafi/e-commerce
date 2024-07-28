<form method="post">
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
                <input class="form-control" type="file" id="formFile" accept="image/png, image/jpg, image/jpeg" required>
            </div>
            <div class="text-center mt-5 mb-3">
            <input type="submit" value="Tambah Produk" name="tambah" class=" btn btn-primary px-3 py-1">
      
        </div>
        </div>
        
    </div>
    </div>
</form>