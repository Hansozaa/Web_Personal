<?php
include('../koneksi.php');
session_start();
if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit;
}

$id = mysqli_real_escape_string($db, $_GET['id_gallery']);

// Ambil nama file gambar
$sql = "SELECT foto FROM tbl_gallery WHERE id_gallery = '$id'";
$query = mysqli_query($db, $sql);
$data = mysqli_fetch_array($query);
$foto = $data['foto'] ?? '';

// Hapus file gambar dari folder jika ada
if ($foto && file_exists("../images/$foto")) {
    unlink("../images/$foto");
}

// Hapus data dari database
$sql_delete = "DELETE FROM tbl_gallery WHERE id_gallery = '$id'";
$query_delete = mysqli_query($db, $sql_delete);

if ($query_delete) {
    echo "<script>alert('Data gallery berhasil dihapus.'); window.location='data_gallery.php';</script>";
    exit;
} else {
    echo "<script>alert('Gagal menghapus data.'); history.back();</script>";
}
?>
