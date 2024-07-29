<?php
include ('../config.php')
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
 <nav class="navbar navbar-expand-md d-flex flex-row bg-secondary">
        <div class="container-fluid d-flex flex-column">
            <div class="d-flex flex-row justify-content-between">
            <a class="navbar-brand" href="./"><h1>Admin Panel</h1></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            </div>
            <!-- collapse -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="d-flex flex-column">
                    <div class="justify-content-center  align-self-center mb-1">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item p-1">
                        <a class="nav-link" aria-current="page" href="?page=tambah">Tambah Produk</a>
                    </li>
                    <li class="nav-item p-1">
                        <a class="nav-link" href="?page=check">Cek Status Pesanan</a>
                    </li>
                    <li class="nav-item p-1">
                        <a class="nav-link" href="../">Logout</a>
                    </li>
                </ul>
                </div>
            </div>
            </div>
            <!-- Collapse end -->
        </div>
    </nav>

    <?php
    if(isset($_GET['page']))
    {
        if($_GET['page'] == 'tambah'){
        include './upload.php';
        }
        else if($_GET['page'] == 'check'){
            include './confirm.php';
            exit();
        }
    }
    ?>
    <div class="container d-flex flex-column justify-content-center">
        <h4 class="text-center mt-3">List Produk</h4>
        <table  class="table table-striped border" >
            <thead class="table-primary">
                <td>No</td>
                <td>Gambar Produk</td>
                <td>Nama Produk</td>
                <td>Deskripsi Produk</td>
                <td>Harga Produk</td>
                <td class="w-25">Action</td>
            </thead>
            <tr>
                <td>
                    <img src="" alt="" class="product-img">
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    <a href="" class="btn btn-success mb-1">Konfirmasi</a>
                    <a href="" class="btn btn-warning mb-1">Batal</a>
                    <a href="" class="btn btn-danger mb-1">Hapus</a>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>