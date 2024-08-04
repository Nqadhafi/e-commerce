<?php
include('header.php');

if(isset($_GET['hapus'])){
    $id_produk_hapus = intval($_GET['hapus']); // Pastikan ID adalah integer untuk keamanan
    unset($_SESSION['keranjang'][$id_produk_hapus]);
    header('Location: ./cart.php');
}

if (isset($_POST['transaksi'])) {
    $id_produk = intval($_POST['id_produk']);
    $qty = intval($_POST['qty']);
    if ($qty > 0) {
        $_SESSION['keranjang'][$id_produk] = $qty;
        header('Location: ./cart.php');
    }
}
?>
<div class="container-fluid justify-content-center align-self-center align-items-center d-flex">
    
    <div class="bg-light">
        <?php if (isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0) : ?>
            <?php
            $subtotal = 0;
            foreach ($_SESSION['keranjang'] as $id => $qty) :
                $show = mysqli_query($config, "SELECT * FROM tb_produk WHERE id_produk = '$id'");
                $show_produk = mysqli_fetch_array($show, MYSQLI_ASSOC);
                $total_produk = $show_produk['harga_produk'] * $qty;
                $subtotal += $total_produk;
            ?>
                <form method="post">
                    <div class="d-flex justify-content-between">
                        <div class="card m-3 p-2 d-flex flex-row bg-light cart-img" style="width: 18rem;">
                            <img src="./assets/uploads/<?php echo $show_produk['gambar_produk'] ?>" class="card-img-top " alt="...">
                            <div class="card-body">
                                <p class="card-text"><?php echo $show_produk['nama_produk'] ?></p>
                                <p class="card-text">Rp.<?php echo number_format($show_produk['harga_produk'], 0, ',', '.') ?></p>
                            </div>
                        </div>
                        
                        <div class="align-self-center align-items-center mx-auto" style="max-width:6rem;">
                            <p class="text-center">Jumlah</p>
                            <div class="input-group">
                                <input type="hidden" name="id_produk" value="<?php echo $id; ?>">
                                <button class="btn fw-bolder m-0 p-1" type="button" onclick="decreaseQty(this)">-</button>
                                <input type="number" class="form-control p-1 text-center" name="qty" value="<?php echo $qty; ?>" min="1" required>
                                <button class="btn fw-bolder m-0 p-1" type="button" onclick="increaseQty(this)">+</button>
                            </div>
                        </div>
                        <div class="m-3 p-2 align-self-center align-items-center">
                            <p>Total Produk</p>
                            <p class="">Rp. <?php echo number_format($total_produk, 0, ',', '.'); ?></p>
                        </div>
                        <div class="me-3 mx-auto align-self-center">
                            <input type="submit" value="Update" name="transaksi" class="btn btn-success px-2 py-1">
                            <a href="?hapus=<?php echo $id; ?>" class="btn btn-danger px-2 py-1 m-1" onclick="return confirm('Anda yakin menghapus produk ini?');">Hapus</a>
                        </div>
                    </div>
                </form>
            <?php endforeach; ?>
            <hr>
            <div class="d-flex justify-content-between m-4">
                <h5>Subtotal</h5>
                <p>Rp.<?php echo number_format($subtotal, 0, ',', '.'); ?></p>
            </div>
            <div class="align-self-center align-items-center text-center mb-5 gap-3">
                <a href="./checkout.php" class="btn btn-primary">Checkout</a>
                <a href="./index.php" class="btn btn-secondary">Kembali Belanja</a>
            </div>
        <?php else: ?>
            <h4>Keranjang kosong</h4>
        <?php endif; ?>
    </div>
</div>

<script>
// Fungsi untuk mengurangi jumlah produk
function decreaseQty(button) {
    var qtyInput = button.nextElementSibling;
    var currentValue = parseInt(qtyInput.value);
    if (currentValue > 1) {
        qtyInput.value = currentValue - 1;
    }
}

// Fungsi untuk menambah jumlah produk
function increaseQty(button) {
    var qtyInput = button.previousElementSibling;
    var currentValue = parseInt(qtyInput.value);
    qtyInput.value = currentValue + 1;
}
</script>
