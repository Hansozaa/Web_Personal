<?php
include('../koneksi.php');
session_start();
if (!isset($_SESSION['username'])) {
header('location:login.php');
exit;
}
$id = $_GET['id_artikel'];
$sql = "DELETE FROM tbl_artikel WHERE id_artikel = '$id'";
$query = mysqli_query($db, $sql);
if ($query) {
echo "<script>alert('Artikel berhasil dihapus.');
window.location='data_artikel.php';</script>";
} else {
echo "<script>alert('Gagal menghapus artikel.');
history.back();</script>";
}
?>