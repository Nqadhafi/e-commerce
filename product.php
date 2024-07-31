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
?>

<div class="container-fluid">
    <div class=" p-5">
        <?php if(isset($_GET['id']) &&  !empty($data)) :?>
    <div class="card" style="max-width: 100hw;">
        <div class="row">
        <div class="col-md-7 text-center front-display">
        <img src="./assets/uploads/<?php echo $data[0]['gambar_produk'] ?>" class="card-img-top " alt="...">
        </div>
        <div class="card-body col-md-5">
            <h2 class="card-title fw-bolder"><?php echo $data[0]['nama_produk'] ?></h2>
            <p class="card-text harga fs-4">Rp.<?php echo $data[0]['harga_produk'] ?></p>
            <hr>
            <p class="card-text fw-semibold">Deskripsi produk :</p>
            <p class="card-text"><?php echo $data[0]['deskripsi_produk'] ?></p>
        </div>
        </div>
    </div>
    <?php else: ?>
        <div> <h4 class="text-center">No Product Found</h4></div>
    <?php endif ;?>
    </div>
</div>