<?php
include('header.php');
?>
  <div class="container mx-auto justify-content-center align-self-center align-items-center d-flex my-5">
  <div class="col-lg-6 bg-light">
<?php if (isset($_SESSION['keranjang'])) : ?>
    <?php foreach ($_SESSION['keranjang'] as $id => $qty) : ?>
        <?php
        $show = mysqli_query($config, "SELECT * FROM tb_produk where id_produk = '$id'");
        $show_produk = mysqli_fetch_array($show, MYSQLI_ASSOC);
        $total_produk = $show_produk['harga_produk'] * $qty;
        ?>
                <!-- Produk -->
                <div class="d-flex justify-content-between">
                    <div class="card m-3 p-2 d-flex flex-row bg-light cart-img" style="width: 18rem;">
                        <img src="./assets/uploads/<?php echo $show_produk['gambar_produk'] ?>" class="card-img-top " alt="...">
                        <div class="card-body">
                            <p class="card-text"><?php echo $show_produk['nama_produk'] ?></p>
                            <p class="card-text">Rp.<?php echo $show_produk['harga_produk'] ?></p>
                        </div>
                    </div>
                    <div class="m-3 p-2 mt-5 align-items-center">
                        <p>Rp. <?php echo $total_produk; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="d-flex justify-content-between m-4">
                <h5>Subtotal</h5>
                <p>Rp.0000</p>
            </div>
            </div>
        </div>
        <?php else : ?>{
        echo "Keranjang kosong!";
        }
    <?php endif; ?>