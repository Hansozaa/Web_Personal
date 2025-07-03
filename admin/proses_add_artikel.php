<?php
include('../koneksi.php');
session_start();
if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit;
}

// Handle file upload
$foto_name = '';
if (isset($_FILES['foto_artikel']) && $_FILES['foto_artikel']['error'] == UPLOAD_ERR_OK) {
    $target_dir = "../uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_ext = pathinfo($_FILES['foto_artikel']['name'], PATHINFO_EXTENSION);
    $foto_name = 'artikel_' . time() . '.' . $file_ext;
    $target_file = $target_dir . $foto_name;

    // Validasi file
    $allowed_types = ['jpg', 'jpeg', 'png'];
    $max_size = 10 * 1024 * 1024; // 10MB

    if (!in_array(strtolower($file_ext), $allowed_types)) {
        echo "<script>alert('Hanya file JPG, JPEG, dan PNG yang diperbolehkan.'); history.back();</script>";
        exit;
    }

    if ($_FILES['foto_artikel']['size'] > $max_size) {
        echo "<script>alert('Ukuran file terlalu besar. Maksimal 10MB.'); history.back();</script>";
        exit;
    }

    if (!move_uploaded_file($_FILES['foto_artikel']['tmp_name'], $target_file)) {
        echo "<script>alert('Gagal mengupload foto.'); history.back();</script>";
        exit;
    }
}

// Gunakan prepared statement
$judul = $_POST['nama_artikel'];
$isi = $_POST['isi_artikel'];

$sql = "INSERT INTO tbl_artikel (nama_artikel, isi_artikel, foto_artikel) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "sss", $judul, $isi, $foto_name);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Artikel berhasil ditambahkan.'); window.location='data_artikel.php';</script>";
} else {
    echo "<script>alert('Gagal menambahkan artikel: " . mysqli_error($db) . "'); history.back();</script>"; 
}

mysqli_stmt_close($stmt);
mysqli_close($db);
?>