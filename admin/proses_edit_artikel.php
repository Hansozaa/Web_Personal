<?php
include('../koneksi.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit;
}

$id = $_POST['id_artikel'];
$judul = mysqli_real_escape_string($db, $_POST['nama_artikel']);
$isi_raw = $_POST['isi_artikel'];
$isi_cleaned = str_replace(["\r\n", "\r", "\n"], "\n", $isi_raw);
$isi = $isi_cleaned; // tanpa mysqli_real_escape_string

// Ambil data foto lama untuk fallback jika tidak upload baru
$foto_baru = '';
$query_foto = mysqli_query($db, "SELECT foto_artikel FROM tbl_artikel WHERE id_artikel = '$id'");
$row = mysqli_fetch_assoc($query_foto);
$foto_lama = $row['foto_artikel'];

// Jika user upload file baru
if (isset($_FILES['foto_artikel']) && $_FILES['foto_artikel']['error'] == UPLOAD_ERR_OK) {
    $target_dir = "../uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_ext = pathinfo($_FILES['foto_artikel']['name'], PATHINFO_EXTENSION);
    $foto_baru = 'artikel_' . time() . '.' . $file_ext;
    $target_file = $target_dir . $foto_baru;

    // Validasi
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

    // Hapus foto lama jika ada
    if (!empty($foto_lama) && file_exists("../uploads/$foto_lama")) {
        unlink("../uploads/$foto_lama");
    }
} else {
    $foto_baru = $foto_lama; // Tidak upload baru, pakai lama
}

// Gunakan prepared statement untuk update
$sql = "UPDATE tbl_artikel SET nama_artikel = ?, isi_artikel = ?, foto_artikel = ? WHERE id_artikel = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $judul, $isi, $foto_baru, $id);

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Artikel berhasil diperbarui.'); window.location='data_artikel.php';</script>";
} else {
    echo "<script>alert('Gagal mengedit artikel: " . mysqli_error($db) . "'); history.back();</script>";
}

mysqli_stmt_close($stmt);
mysqli_close($db);
?>
