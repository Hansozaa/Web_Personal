<?php
include('../koneksi.php');
session_start();
$username = mysqli_real_escape_string($db, $_POST['username']);
$password = mysqli_real_escape_string($db, $_POST['password']);
// Cek di database
$sql = "SELECT * FROM tbl_user WHERE username='$username' AND
password='$password'";
$query = mysqli_query($db, $sql);
if (mysqli_num_rows($query) > 0) {
$_SESSION['username'] = $username;
header('Location: beranda_admin.php');
} else {
echo "<script>alert('Login gagal! Username atau password
salah.');window.location='login.php';</script>";
}
?>