<?php
include('./config.php');
$total_keranjang = 0; // Inisialisasi variabel untuk menyimpan total quantity

if (isset($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $id => $qty) {
        $total_keranjang += $qty; // Tambahkan setiap quantity ke total
    }
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./lib/css/main.css">
    <link rel="stylesheet" href="./lib/css/bootstrap.min.css">


    
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-md d-flex flex-row bg-dark text-white">
        <div class="container-fluid d-flex flex-column">
            <div class="d-flex flex-row justify-content-between">
            <a class="navbar-brand" href="./">
                <div class="mb-0 text-center">
        <img src="./assets/img/logo-utama.png" alt="Note Pro" class="w-50">
        </div>
            </a>
            <button class="navbar-toggler bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
           
            </div>
            <marquee behavior="" direction=""><p><?php echo $deskripsi_toko?></p></marquee>
            <p class="fs-6 text-center"><i><b>Alamat : </b><?php echo $alamat_toko ?> |<b> Whatsapp Admin :</b> <?php echo $no_whatsapp?></i></p>
            <!-- collapse -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="d-flex flex-column">
                    <div class="justify-content-center  align-self-center mb-1">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ">
                    <li class="nav-item p-1">
                        <a class="nav-link active text-white" aria-current="page" href="./">Home</a>
                    </li>
                    <li class="nav-item p-1">
                        <a class="nav-link active text-white" aria-current="page" href="./cara_order.php">Cara Order</a>
                    </li>
                    <li class="nav-item p-1">
                        <a class="nav-link text-white" href="check.php">Check Status</a>
                    </li>
                    
                    <!-- Cart -->
                    <li class="nav-item p-1 ">
                        <a class="nav-link d-flex text-white" href="./cart.php" id="cart">
                        <div class="icon-cart me-1">
                        <img src="./assets/img/cart.png" alt="">
                        </div>
                            Cart <span class="badge badge-pill bg-danger align-self-center ms-1"><?php echo $total_keranjang?>
                        </span>
                        </a>
                    </li>
                    <!-- Cart end -->
                  
                </ul>
                </div>

            </div>
            </div>
            <!-- Collapse end -->
        </div>
    </nav>
    <script src="./lib/js/bootstrap.bundle.min.js"></script>
</body>
</html>