<?php
session_start();
include('../koneksi.php');

if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit;
}

// Tangkap data dari form
$alamat = mysqli_real_escape_string($db, $_POST['alamat']);
$koordinat = mysqli_real_escape_string($db, $_POST['koordinat']);
$about = mysqli_real_escape_string($db, $_POST['about']);

// >>> TAMBAHKAN BARIS INI UNTUK MENANGKAP LINK_MAPS <<<
$link_maps = isset($_POST['link_maps']) ? mysqli_real_escape_string($db, $_POST['link_maps']) : '';
// Menggunakan isset() untuk memastikan variabel ada, dan berikan nilai default '' jika tidak ada.
// Anda juga bisa menggunakan NULL jika kolom di database diizinkan NULL.
// $link_maps = !empty($_POST['link_maps']) ? mysqli_real_escape_string($db, $_POST['link_maps']) : NULL;


// Proses upload foto
$foto_profil = '';
if(isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
    $target_dir = "../uploads/about/"; 
    
    // Pastikan folder ada
    if(!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION);
    $new_filename = 'profile_' . time() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Debugging: Tampilkan info file
    error_log("Mencoba mengupload file: " . $_FILES['foto_profil']['tmp_name'] . " ke " . $target_file);
    
    if(move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_file)) {
        // Simpan path RELATIF (tanpa ../)
        $foto_profil = "uploads/about/" . $new_filename;
        error_log("File berhasil diupload: " . $foto_profil);
    } else {
        $error_message = "Gagal mengupload file. Error: " . $_FILES['foto_profil']['error'];
        error_log($error_message);
        $_SESSION['error'] = $error_message;
        header('location:add_about.php');
        exit;
    }
} else {
    // Jika tidak ada file diupload atau ada error upload, anggap ini sebagai error
    // Anda mungkin ingin mengubah logika ini jika foto_profil tidak wajib
    $error_message = "Error upload file atau tidak ada file yang diupload. Error code: " . ($_FILES['foto_profil']['error'] ?? 'N/A');
    error_log($error_message);
    $_SESSION['error'] = $error_message;
    header('location:add_about.php');
    exit;
}

// Debugging: Tampilkan data sebelum insert
error_log("Data yang akan disimpan:");
error_log("Foto: " . $foto_profil);
error_log("Alamat: " . $alamat);
error_log("Koordinat: " . $koordinat);
error_log("Link Maps: " . $link_maps); // <<< TAMBAHKAN INI
error_log("About: " . substr($about, 0, 50) . "...");

// Simpan ke database
// >>> PERBAIKI QUERY INSERT UNTUK MENYERTAKAN link_maps <<<
$query = "INSERT INTO tbl_about (foto_profil, alamat, koordinat, link_maps, about) 
          VALUES ('$foto_profil', '$alamat', '$koordinat', '$link_maps', '$about')";

error_log("Query: " . $query);

if(mysqli_query($db, $query)) {
    error_log("Data berhasil disimpan");
    header('location:data_about.php?success=1');
    exit; // Penting: tambahkan exit setelah header redirect
} else {
    $error_message = "Database error: " . mysqli_error($db);
    error_log($error_message);
    $_SESSION['error'] = $error_message;
    header('location:add_about.php');
    exit; // Penting: tambahkan exit setelah header redirect
}

mysqli_close($db);
?>