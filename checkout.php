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
    <!-- Tambahkan jQuery untuk AJAX -->
    <script src="./lib/js/jquery.js"></script>
</head>

<body>
    <?php if (isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0) : ?>
        <div class="container-fluid">
            <h3 class="text-center mt-4">Checkout</h3>
            <hr>
            <form action="./proses_checkout.php" method="post">
                <div class="row">
                    <div class="col-lg-6">
                        <h4 class="text-center">Isi Data Diri Anda</h4>
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
                            <div class="col-6 d-flex flex-column">
                                <label for="provinsi">Provinsi :</label>
                                <select class="form-control" id="provinsi" name="provinsi" required>
                                    <option value="">Pilih Provinsi</option>
                                </select>
                            </div>
                            <div class="col-6 d-flex flex-column mb-3">
                                <label for="kabupaten">Kabupaten/Kota :</label>
                                <select class="form-control" id="kabupaten" name="kabupaten" required>
                                    <option value="">Pilih Kabupaten/Kota</option>
                                </select>
                            </div>
                            <div class="col-12 d-flex flex-column mb-3">
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
                            $total_weight = 0; // Variabel untuk menyimpan total berat
                            foreach ($_SESSION['keranjang'] as $id => $qty) :
                                $show = mysqli_query($config, "SELECT * FROM tb_produk WHERE id_produk = '$id'");
                                $show_produk = mysqli_fetch_array($show, MYSQLI_ASSOC);
                                $total_produk = $show_produk['harga_produk'] * $qty;
                                $subtotal += $total_produk;
                                $weight_produk = $show_produk['berat_produk'] * $qty; // Menghitung berat total produk
                                $total_weight += $weight_produk; // Menambahkan ke total berat
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
                                            <p>Berat: <?php echo $weight_produk; ?> gram</p> <!-- Menampilkan berat produk -->
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
                        <div class="d-flex justify-content-between m-4">
                            <h5>Total Berat</h5>
                            <p><?php echo $total_weight; ?> gram</p>
                        </div>
                        <div class="d-flex justify-content-between m-4">
                            <h5>Total Ongkir</h5>
                            <p id="total-ongkir">Rp.0</p> <!-- Menampilkan total ongkir -->
                        </div>
                        <div class="d-flex justify-content-between m-4">
                            <h5>Grand Total</h5>
                            <p id="grand-total">Rp.<?php echo number_format($subtotal, 0, ',', '.'); ?></p> <!-- Menampilkan grand total -->
                        </div>
                        <div class="text-center mt-4">
                            <img src="./assets/img/jtr_logo.png" alt="JNE Logo" style="width: 100px;"> <!-- Tambahkan logo JNE -->
                        </div>
                    </div>
                </div>
                <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
                <input type="hidden" name="total_weight" value="<?php echo $total_weight; ?>"> <!-- Mengirim total berat -->
                <input type="hidden" name="ongkir" id="input-ongkir" value="0"> <!-- Mengirim total ongkir -->
                <div class="justify-content-center d-flex mt-5 gap-3">
                    <button class="btn btn-success" type="submit" onclick="return confirm('Apakah anda yakin ingin melakukan transaksi?');">Checkout</button>
                    <a href="./cart.php" class="btn btn-secondary">Kembali ke Keranjang</a>
                </div>
            </form>
        <?php endif; ?>
        </div>

        <script>
            $(document).ready(function() {
                let totalWeight = <?php echo $total_weight; ?>;
                let subtotal = <?php echo $subtotal; ?>;

                // Mengambil data provinsi dari API RajaOngkir melalui proxy
                $.ajax({
                    type: "GET",
                    url: "http://localhost/wleowleo/rajaongkir_proxy.php?endpoint=province",
                    success: function(data) {
                        try {
                            data = JSON.parse(data);
                            if (data.rajaongkir && data.rajaongkir.results) {
                                $.each(data.rajaongkir.results, function(index, value) {
                                    $('#provinsi').append('<option value="'+value.province_id+'">'+value.province+'</option>');
                                });
                            } else {
                                console.error("Data tidak memiliki struktur yang diharapkan:", data);
                                alert("Data provinsi tidak tersedia. Silakan coba lagi.");
                            }
                        } catch (e) {
                            console.error("Error parsing JSON:", e);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("Error fetching province data: ", textStatus, errorThrown);
                    }
                });

                // Mengambil data kabupaten berdasarkan provinsi yang dipilih
                $('#provinsi').on('change', function() {
                    var province_id = $(this).val();
                    if(province_id) {
                        $.ajax({
                            type: "GET",
                            url: "http://localhost/wleowleo/rajaongkir_proxy.php?endpoint=city&province=" + province_id,
                            success: function(data) {
                                try {
                                    data = JSON.parse(data);
                                    if (data.rajaongkir && data.rajaongkir.results) {
                                        $('#kabupaten').empty().append('<option value="">Pilih Kabupaten/Kota</option>');
                                        $.each(data.rajaongkir.results, function(index, value) {
                                            $('#kabupaten').append('<option value="'+value.city_id+'">'+value.city_name+'</option>');
                                        });
                                    } else {
                                        console.error("Data tidak memiliki struktur yang diharapkan:", data);
                                        alert("Data kabupaten/kota tidak tersedia. Silakan coba lagi.");
                                    }
                                } catch (e) {
                                    console.error("Error parsing JSON:", e);
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error("Error fetching city data: ", textStatus, errorThrown);
                            }
                        });
                    } else {
                        $('#kabupaten').empty().append('<option value="">Pilih Kabupaten/Kota</option>');
                    }
                });

                // Menghitung ongkir berdasarkan total berat dan kabupaten/kota yang dipilih
                $('#kabupaten').on('change', function() {
                    var city_id = $(this).val();
                    if(city_id) {
                        $.ajax({
                            type: "POST",
                            url: "http://localhost/wleowleo/rajaongkir_proxy.php?endpoint=cost",
                            data: {
                                origin: 445, // ID Kota Solo (Surakarta)
                                destination: city_id,
                                weight: totalWeight,
                                courier: "jne"
                            },
                            success: function(data) {
                                try {
                                    data = JSON.parse(data);
                                    if (data.rajaongkir && data.rajaongkir.results) {
                                        // Filter untuk mencari JNE Cargo (JTR)
                                        var cargoService = data.rajaongkir.results[0].costs.find(service => service.service === "JTR");
                                        
                                        if (cargoService) {
                                            var ongkir = cargoService.cost[0].value;
                                            $('#total-ongkir').text('Rp.' + ongkir.toLocaleString());
                                            $('#grand-total').text('Rp.' + (subtotal + ongkir).toLocaleString());
                                            $('#input-ongkir').val(ongkir);
                                        } else {
                                            alert("Layanan JNE Cargo tidak tersedia untuk rute ini.");
                                        }
                                    } else {
                                        console.error("Data tidak memiliki struktur yang diharapkan:", data);
                                        alert("Data ongkir tidak tersedia. Silakan coba lagi.");
                                    }
                                } catch (e) {
                                    console.error("Error parsing JSON:", e);
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error("Error fetching cost data: ", textStatus, errorThrown);
                            }
                        });
                    }
                });
            });
        </script>
</body>

</html>
