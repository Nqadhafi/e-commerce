<?php
include('../config.php');
if(isset($_SESSION['admin_id']) && isset($_SESSION['admin_username'])){
  header("Location: ./index.php");
  exit;
}

// Proses jika form login disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['userID'];
  $password = $_POST['passWord'];

  // Cek apakah input kosong
  if (!empty($username) && !empty($password)) {
    // Ambil data admin dari database berdasarkan username dan password
    $sql = "SELECT * FROM tb_admin WHERE user_admin = ? AND password_admin = ?";
    $stmt = $config->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $admin = $result->fetch_assoc();

      // Set session admin
      $_SESSION['admin_id'] = $admin['id'];
      $_SESSION['admin_username'] = $admin['user_admin'];

      // Redirect ke halaman dashboard
      header("Location: ./index.php");
      exit;
    } else {
      $error = "Username atau password salah!";
    }
  } else {
    $error = "Username dan password harus diisi!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../lib/css/bootstrap.min.css">
  <link rel="stylesheet" href="../lib/css/main.css">
</head>

<body class="bg-dark">

  <div class="container bg-light my-5 py-4 rounded mx-auto">

    <form method="post">
      <div class="mx-5 px-5">

        <h1 class="text-center">Login Admin</h1>
        <div class="text-center d-flex justify-content-center">
          <?php
          if (isset($error)) {
            echo "<p class='bg-danger border rounded text-white p-2'>$error</p>";
          }
          ?>
        </div>
        <div class="mb-3 ">
          <label for="userID" class="form-label">User</label>
          <input type="text" class="form-control" name="userID">
        </div>
        <!-- Password -->
        <div class="mb-3 ">
          <label for="passWord" class="form-label">Password</label>
          <input type="Password" class="form-control" name="passWord">
        </div>
      </div>
      <div class="text-center mt-5 mb-3">
        <button type="submit" class="btn btn-primary px-5 py-3">Login</button>
        <a href="../" class="btn btn-secondary px-5 py-3">Kembali ke Beranda</a>
      </div>
  </div>
  </form>
  </div>
</body>

</html>