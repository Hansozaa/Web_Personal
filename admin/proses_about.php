<?php
include('../koneksi.php');
session_start();
if (!isset($_SESSION['username'])) {
header('location:login.php');
exit;
}
$id = $_POST['id_about'];
$about = mysqli_real_escape_string($db, $_POST['about']);
$sql = "UPDATE tbl_about SET about='$about' WHERE id_about='$id'";
$query = mysqli_query($db, $sql);
if ($query) {
echo "<script>alert('Data berhasil diperbarui.');
window.location='data_about.php';</script>";
} else {
echo "<script>alert('Gagal mengedit data.'); history.back();</script>";
}
?>
