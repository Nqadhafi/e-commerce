<?php
include('header.php');
if(isset($_GET['id'])){
    $id = $_GET['id'];
}
else {
    $id= '';
}
$query = mysqli_query($config, "SELECT * FROM tb_produk WHERE id_produk = '$id'");
$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

if (!empty($data)){
$gambar_produk =$data[0]['gambar_produk'];
$nama_produk = $data[0]['nama_produk'];
$harga_produk = $data[0]['harga_produk'];
$deskripsi_produk = $data[0]['deskripsi_produk'];
}

//Logika transaksi checkout/tambah keranjang
if(isset($_POST['transaksi'])){

    switch($_POST['transaksi']){

    case ('Tambah ke Keranjang'):
        $qty = $_POST['qty'];
        
        if (!isset($_SESSION['keranjang'])) {
            $_SESSION['keranjang'] = [];
        }
    
        if (isset($_SESSION['keranjang'][$id])) {
            $_SESSION['keranjang'][$id] += $qty;
            
        } else {
            $_SESSION['keranjang'][$id] = $qty;
        }
        echo "<script>
        alert('Produk berhasil ditambahkan');
        window.location.href='./product.php?id=" . $id . "';
    </script>";
    
        break;

        case('Checkout'):
            echo "Uwawww";
            break;

}
}

?>

<div class="container-fluid bg-light">
    <div class=" p-5">
        <?php if(isset($_GET['id']) &&  !empty($data)) :?>
    <div class="card" style="max-width: 100hw;">
        <div class="row">
        <div class="col-md-7 text-center front-display">
        <img src="./assets/uploads/<?php echo $gambar_produk ?>" class="card-img-top " alt="...">
        </div>
        <div class="card-body col-md-5">
            <h2 class="card-title fw-bolder"><?php echo $nama_produk ?></h2>
            <p class="card-text harga fs-4">Rp.<?php echo $harga_produk ?></p>
            <hr>
            <p class="card-text fw-semibold">Deskripsi produk :</p>
            <p class="card-text"><?php echo $deskripsi_produk ?></p>
            <!-- form -->
            
            <form action="" method="post">
            <!-- hidden input data -->
            <input type="hidden" name="id_session" value="<?php echo $id_sesi ;?>">
            <input type="hidden" name="id_produk" value="<?php echo $id ; ?>">
            <div class="mb-3" style="max-width:7rem;">
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" onclick="decreaseQty()">-</button>
                            <input type="number" class="form-control" id="qty" name="qty" value="1" min="1" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="increaseQty()">+</button>
                        </div>
                    </div>
            <div class="d-flex gap-3">
            <input type="submit" value="Tambah ke Keranjang" name="transaksi" class=" btn btn-warning px-3 py-1">
            <input type="submit" value="Checkout" name="transaksi" class=" btn btn-primary px-3 py-1">
        </div>
            </form>
        </div>
        </div>
    </div>
    <?php else: ?>
        <div> <h4 class="text-center">No Product Found</h4></div>
    <?php endif ;?>
    </div>
</div>
<script>
        function increaseQty() {
            let qtyInput = document.getElementById('qty');
            qtyInput.value = parseInt(qtyInput.value) + 1;
        }

        function decreaseQty() {
            let qtyInput = document.getElementById('qty');
            if (qtyInput.value > 1) {
                qtyInput.value = parseInt(qtyInput.value) - 1;
            }
        }
    </script>