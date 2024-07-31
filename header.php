<?php
include('config.php');
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
    <nav class="navbar navbar-expand-md d-flex flex-row bg-warning">
        <div class="container-fluid d-flex flex-column">
            <div class="d-flex flex-row justify-content-between">
            <a class="navbar-brand" href="./"><h1>Toko Onlineku</h1></a>
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
                        <a class="nav-link active" aria-current="page" href="./">Home</a>
                    </li>
                    <li class="nav-item p-1">
                        <a class="nav-link" href="check.php">Check Status</a>
                    </li>
                    <!-- Cart -->
                    <li class="nav-item p-1">
                        <a class="nav-link" href="./cart.php" id="cart">
                            Cart <span class="badge badge-pill bg-danger">0</span>
                        </a>
                    </li>
                    <!-- Cart end -->
                </ul>
                </div>
                <!-- Form -->
                <div>
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-success  btn-outline-seccondary" type="submit">Search</button>
                </form>
                </div>
                <!-- form end -->
            </div>
            </div>
            <!-- Collapse end -->
        </div>
    </nav>
    <script src="./lib/js/bootstrap.bundle.min.js"></script>
</body>
</html>