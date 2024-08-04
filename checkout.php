<?php
include('header.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
</head>

<body>
    <div class="container-fluid">
        <h3 class="text-center mt-4"> Checkout</h3>
        <hr>
        <form action="">
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
                            <textarea class="form-control" name="alamat" id=""></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 bg-light">
                    <!-- Produk -->
                    <div class="d-flex justify-content-between">
                    <div class="card m-3 p-2 d-flex flex-row bg-light" style="width: 18rem;">
                        <img src="..." class="card-img-top w-25 checkout-img" alt="...">
                        <div class="card-body">
                            <p class="card-text">Sepatu Mahal Coy</p>
                            <p class="card-text">Rp.99999</p>
                        </div>
                    </div>

                    <div class="m-3 p-2 mt-5 align-items-center">
                        <p>Rp.99999</p>
                    </div>
                    </div>
                    <!-- Produk -->
                     

                    <div class="d-flex justify-content-between m-4">
                    <h5>Subtotal</h5>
                    <p>Rp.0000</p>
                    </div>
                    
                </div>

            </div>
            <div class="justify-content-center d-flex mt-5">
                <button class="btn btn-success" type="submit">Checkout</button>
            </div>
        </form>

    </div>
</body>

</html>