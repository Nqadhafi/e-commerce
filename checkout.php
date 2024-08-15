<?php
include('header.php');
include('./config.php'); // Pastikan config.php terinclude untuk koneksi database
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
</head>

<body>
    <?php if (isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0) : ?>
        <div class="container-fluid">
            <h3 class="text-center mt-4"> Checkout</h3>
            <hr>
            <form action="./proses_checkout.php" method="post">
                <div class="row">
                    <div class="col-lg-6">
                        <h4 class="text-center"> Isi Data Diri Anda</h4>
                        <div class="row">
                            <div class="col-6 d-flex flex-column">
                                <label for="nama">Nama Lengkap :</label>
                                <input class="form-control" type="text" name="nama_lengkap" required>
                            </div>
                            <div class="col-6 d-flex flex-column">
                                <label for="email">Email :</label>
                                <input class="form-control" type="email" name="email" required>
                            </div>
                            <div class="col-6 d-flex flex-column">
                                <label for="nomor_handphone">No. HP :</label>
                                <input class="form-control" type="number" name="nomor_handphone" required>
                            </div>
                            <div class="col-6 d-flex flex-column mb-3">
                                <label for="alamat">Alamat Lengkap :</label>
                                <textarea class="form-control" name="alamat" id="" required></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 bg-light">
                        <h4 class="text-center">Produk yang Anda Checkout</h4>
                        <div class="d-flex flex-column">
                            <?php
                            $subtotal = 0;
                            foreach ($_SESSION['keranjang'] as $id => $qty) :
                                $show = mysqli_query($config, "SELECT * FROM tb_produk WHERE id_produk = '$id'");
                                $show_produk = mysqli_fetch_array($show, MYSQLI_ASSOC);
                                $total_produk = $show_produk['harga_produk'] * $qty;
                                $subtotal += $total_produk;
                            ?>
                                <div class="card m-2 p-2 bg-light" style="width: 100%;">
                                    <div class="d-flex checkout">
                                        <img src="./assets/uploads/<?php echo $show_produk['gambar_produk']; ?>" class="card-img-top w-25" alt="...">
                                        <div class="card-body">
                                            <p class="card-text"><?php echo $show_produk['nama_produk']; ?></p>
                                            <p class="card-text">Rp.<?php echo number_format($show_produk['harga_produk'], 0, ',', '.'); ?></p>
                                        </div>
                                        <div class="m-3 p-2 align-items-center">
                                            <p>Jumlah: <?php echo $qty; ?></p>
                                            <p>Total: Rp.<?php echo number_format($total_produk, 0, ',', '.'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between m-4">
                            <h5>Subtotal</h5>
                            <p>Rp.<?php echo number_format($subtotal, 0, ',', '.'); ?></p>
                        </div>
                        <div>
                            <p><i>*Harga di atas belum termasuk ongkos kirim</i></p>
                            <p><i>**Harga ongkir belum pasti, tergantung volume dan jumlah barang, hubungi admin untuk informasi lebih lanjut</i></p>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
                <div class="justify-content-center d-flex mt-5 gap-3">
                    <button class="btn btn-success" type="submit" onclick="return confirm('Apakah anda yakin ingin melakukan transaksi?');">Checkout</button>
                    <a href="./cart.php" class="btn btn-secondary">Kembali ke Keranjang</a>
                </div>
            </form>
        <?php endif; ?>
        </div>
</body>

</html>
