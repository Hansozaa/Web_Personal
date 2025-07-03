<?php
include('../koneksi.php');
session_start();
if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit;
}

if (!isset($_GET['id_foto'])) {
    header('Location: data_foto.php');
    exit;
}

$id = $_GET['id_foto'];

// Ambil data foto untuk menghapus file
$sql = "SELECT foto FROM tbl_foto WHERE id_foto = $id";
$result = mysqli_query($db, $sql);
$data = mysqli_fetch_assoc($result);

if ($data) {
    // Hapus file foto
    $file_path = '../images/' . $data['foto'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    
    // Hapus data dari database
    $delete_sql = "DELETE FROM tbl_foto WHERE id_foto = $id";
    if (mysqli_query($db, $delete_sql)) {
        header('Location: data_foto.php?delete_success=1');
    } else {
        $_SESSION['error'] = "Gagal menghapus foto: " . mysqli_error($db);
        header('Location: data_foto.php');
    }
} else {
    $_SESSION['error'] = "Foto tidak ditemukan";
    header('Location: data_foto.php');
}
exit;
?>