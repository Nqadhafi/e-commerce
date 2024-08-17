<?php
include('./config.php');

// Pastikan user sudah mengirimkan form upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_STRING);
    $target_dir = "./assets/bukti_bayar/";
    
    // Memastikan direktori tujuan sudah ada
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Nama file dan path target
    $file_name = basename($_FILES["bukti_bayar"]["name"]);
    $target_file = $target_dir . $order_id . "_" . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Cek apakah file gambar adalah gambar asli atau palsu
    $check = getimagesize($_FILES["bukti_bayar"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File bukan gambar.";
        $uploadOk = 0;
    }

    // Cek apakah file sudah ada
    if (file_exists($target_file)) {
        echo "Maaf, file sudah ada.";
        $uploadOk = 0;
    }

    // Cek ukuran file
    if ($_FILES["bukti_bayar"]["size"] > 5000000) { // 5MB maksimal
        echo "Maaf, file terlalu besar.";
        $uploadOk = 0;
    }

    // Hanya format gambar tertentu yang diizinkan
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        echo "Maaf, hanya file JPG, JPEG, PNG yang diperbolehkan.";
        $uploadOk = 0;
    }

    // Cek apakah $uploadOk bernilai 0 karena ada error
    if ($uploadOk == 0) {
        echo "Maaf, file Anda tidak dapat diupload.";
    } else {
        // Jika semua kondisi ok, coba untuk mengupload file
        if (move_uploaded_file($_FILES["bukti_bayar"]["tmp_name"], $target_file)) {
            // Update database setelah berhasil mengupload file
            $update_query = "UPDATE tb_order SET bukti_bayar = ?, status_order = 'Proses Verifikasi' WHERE id_order = ?";
            $stmt = mysqli_prepare($config, $update_query);
            mysqli_stmt_bind_param($stmt, "ss", $file_name, $order_id);
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Bukti pembayaran berhasil diupload.'); window.location.href = 'check.php?orderid=" . urlencode($order_id) . "';</script>";
            } else {
                echo "Gagal memperbarui status di database.";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Maaf, terjadi kesalahan saat mengupload file.";
        }
    }
} else {
    echo "Akses tidak sah.";
}
?>
