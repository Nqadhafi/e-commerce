<?php
include('header.php');
$query = mysqli_query($config, "SELECT * FROM tb_produk");
$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

?>
<body class="bg-light">
    <div class="container-fluid">
        <!-- Header  -->
        <div class="text-center mt-3">
            <h3>Katalog kami</h3>
            <hr>
        </div>
        <!-- katalog -->
        <div class="container">
            <div class="row justify-content-center">
            <?php foreach ($data as $data) : ?>
                <!-- Produk -->
                <div class="col-md-4 d-flex justify-content-center mb-4">
                    <div class="card border-secondary border" style="width: 15rem;">
                        <img src="./assets/uploads/<?php echo $data['gambar_produk'] ?>" class="card-img-top p-2" alt="...">
                        <div class="card-body border-top border-secondary">
                            <h5 class="card-title"><?php echo $data['nama_produk']?></h5>
                            <p class="card-text harga">Rp.<?php echo number_format($data['harga_produk'], 0, ',', '.')?></p>
                            <a href="./product.php?id=<?php echo $data['id_produk'] ?>" class="btn btn-primary text-decoration-none mt-1">Detail Produk</a>
                        </div>
                    </div>
                </div>
                <!-- Produk End -->
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
