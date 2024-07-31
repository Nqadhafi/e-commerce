<?php
include('header.php');
include('config.php');
$query = mysqli_query($config, "SELECT * FROM tb_produk");
$data = mysqli_fetch_all($query, MYSQLI_ASSOC);

?>
<body>
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
                        <img src="./assets/uploads/<?php echo $data['gambar_produk'] ?>" class="card-img-top front-display p-2" alt="...">
                        <div class="card-body border-top border-secondary">
                            <h5 class="card-title"><?php echo $data['nama_produk']?></h5>
                            <p class="card-text harga">Rp.<?php echo $data['harga_produk']?></p>
                        </div>
                    </div>
                </div>
                <!-- Produk End -->
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
