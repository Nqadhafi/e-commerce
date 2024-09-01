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
$berat_produk = $data[0]['berat_produk'];
$stok_produk = $data[0]['stok_produk'];
$deskripsi_produk = $data[0]['deskripsi_produk'];
}

//Logika transaksi checkout/tambah keranjang
if(isset($_POST['transaksi'])){

    switch($_POST['transaksi']){

    case ('Tambah ke Keranjang'):
        $qty = $_POST['qty'];
        
        // Periksa jumlah total produk di keranjang
        $total_qty_in_cart = isset($_SESSION['keranjang'][$id]) ? $_SESSION['keranjang'][$id] : 0;
        $total_qty_after_addition = $total_qty_in_cart + $qty;

        if ($total_qty_after_addition > $stok_produk) {
            echo "<script>
                alert('Jumlah produk di keranjang melebihi stok yang tersedia.');
                window.location.href='./product.php?id=" . $id . "';
            </script>";
        } else {
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
        }
        break;

    }
}



?>

<div class="container-fluid bg-light">
    <div class=" p-5">
        <?php if(isset($_GET['id']) &&  !empty($data)) :?>
    <div class="card" style="max-width: 100hw;">
        <div class="row">
        <div class="col-md-7 text-center front-display p-5">
        <img src="./assets/uploads/<?php echo $gambar_produk ?>" class="card-img-top " alt="...">
        </div>
        <div class="card-body col-md-5">
            <h2 class="card-title fw-bolder"><?php echo $nama_produk ?></h2>
            <p class="card-text harga fs-4">Rp.<?php echo number_format($harga_produk, 0, ',', '.'); ?></p>
            <span class="d-flex">
                <p class="fw-semibold pe-1">Stok:</p>
                <p><?php echo $stok_produk ?></p>
            </span>
            <hr>
            <span class="d-flex">
            <p class="fw-bold pe-1">Berat Produk: </p>
            <p><?php echo $berat_produk ?> gram</p></span>
            <p class="card-text fw-semibold">Deskripsi produk :</p>
            <p class="card-text"><?php echo $deskripsi_produk ?></p>
            <!-- form -->
            
            <form action="" method="post">
            <!-- hidden input data -->
            <input type="hidden" name="id_session" value="<?php echo $id_sesi ;?>">
            <input type="hidden" name="id_produk" value="<?php echo $id ; ?>">
            <div class="mb-3" style="max-width:7rem;">
                        <div class="input-group">
                            <button class="btn fw-bolder m-0 p-1 " type="button" onclick="decreaseQty()">-</button>
                            <input type="number" class="form-control p-0  text-center" id="qty" name="qty" value="1" min="1" required>
                            <button class="btn fw-bolder m-0 p-1" type="button" onclick="increaseQty()">+</button>
                        </div>
                    </div>
            <div class="d-flex gap-3">
            <input type="submit" value="Tambah ke Keranjang" name="transaksi" class=" btn btn-warning px-3 py-1">
            <!-- <input type="submit" value="Checkout" name="transaksi" class=" btn btn-primary px-3 py-1"> -->
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