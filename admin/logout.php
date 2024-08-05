<?php
session_destroy();
session_unset();
echo "<script> alert('Berhasil Logout'); window.location.href='./login.php'; </script>";
?>