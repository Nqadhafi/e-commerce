<?php
include('../config.php');
$query = mysqli_query($config, "SELECT * FROM tb_produk");

$data = mysqli_fetch_all($query, MYSQLI_ASSOC);
if(!isset($_SESSION['admin_id']) && !isset($_SESSION['admin_username'])){
    header("Location: ./login.php");
}
?>
<!DOCTYPE html> 
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../lib/css/main.css">
    <link rel="stylesheet" href="../lib/css/bootstrap.min.css">
    <script src="../lib/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-md d-flex flex-row bg-primary">
        <div class="container-fluid d-flex flex-column">
            <div class="d-flex flex-row justify-content-between">
                <a class="navbar-brand text-white" href="./">
                    <h1>Admin Panel</h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <!-- collapse -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="d-flex flex-column">
                    <div class="justify-content-center  align-self-center mb-1">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0 ">
                            <li class="nav-item p-1">
                                <a class="nav-link text-white" aria-current="page" href="?page=tambah">Tambah Produk</a>
                            </li>
                            <li class="nav-item p-1">
                                <a class="nav-link text-white" href="?page=check">Cek Status Pesanan</a>
                            </li>
                            <li class="nav-item p-1">
                                <a class="nav-link text-white" href="./logout.php" onclick="return confirm('Apakah anda yakin logout?');">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Collapse end -->
        </div>
    </nav>

    <?php
    if (isset($_GET['page'])) {
        if ($_GET['page'] == 'tambah') {
            include './upload.php';
        } else if ($_GET['page'] == 'check') {
            include './confirm.php';
            exit();
            
        }
        else if ($_GET['page'] == 'update') {
            include './update.php';
            exit();
        }
        else if ($_GET['page'] == 'ongkir') {
            include './set_ongkir.php';
            exit();
        }
    }
    ?>
    <div class="container d-flex flex-column justify-content-center">
        <h4 class="text-center mt-3">List Produk</h4>
        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <td>No</td>
                <td>Gambar Produk</td>
                <td>Nama Produk</td>
                <td>Harga Produk</td>
                <td>Berat Produk (gram)</td>
                <td>Stok Produk (gram)</td>
                <td>Deskripsi Produk</td>
                <td>Action</td>
            </thead>
            <?php
            $no = 1;
            ?>
            <?php foreach ($data as $data) : ?>
                <tr>
                <td>
                    <?php 
                    echo $no++ .".";
                    ?>
                </td>
                    <td>
                        <div class="product-img">
                            <img src="../assets/uploads/<?php echo $data['gambar_produk'] ?>" alt="">
                        </div>
                    </td>
                    <td><?php echo $data['nama_produk']?></td>
                    <td>Rp. <?php echo number_format($data['harga_produk'], 0, ',', '.')?>,-</td>
                    <td><?php echo $data['berat_produk']?> gram</td>
                    <td><?php echo $data['stok_produk']?></td>
                    <td><?php echo $data['deskripsi_produk']?></td>
                    <td>
                        <a href="./?page=update&id=<?php echo $data['id_produk'] ?>" class="btn btn-warning mb-1">Edit</a>
                        <a href="./delete.php?id=<?php echo $data['id_produk'] ?>" class="btn btn-danger mb-1" onclick="return confirm('Anda yakin menghapus produk ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>

</html>