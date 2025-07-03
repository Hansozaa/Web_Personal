<?php
include('../koneksi.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('location: ../login.php'); // Perhatikan path ke login
    exit;
}

if (isset($_GET['id_about'])) {
    $id_about = $_GET['id_about'];
    
    // Ambil data untuk menghapus file foto jika ada
    $sql_select = "SELECT foto_profil FROM tbl_about WHERE id_about = ?";
    $stmt = mysqli_prepare($db, $sql_select);
    mysqli_stmt_bind_param($stmt, "i", $id_about);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    if ($row) {
        // Hapus file foto jika ada
        if (!empty($row['foto_profil']) && file_exists("../".$row['foto_profil'])) {
            unlink("../".$row['foto_profil']);
        }

        // Hapus data dari database
        $sql_delete = "DELETE FROM tbl_about WHERE id_about = ?";
        $stmt_delete = mysqli_prepare($db, $sql_delete);
        mysqli_stmt_bind_param($stmt_delete, "i", $id_about);
        
        if (mysqli_stmt_execute($stmt_delete)) {
            $_SESSION['success'] = "Data profil berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($db);
        }
    } else {
        $_SESSION['error'] = "Data tidak ditemukan!";
    }
} else {
    $_SESSION['error'] = "ID tidak diberikan!";
}

header('Location: data_about.php'); // Redirect ke halaman data_about
exit;
?>