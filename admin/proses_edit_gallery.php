<?php
include('../koneksi.php');
session_start();
if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit;
}
$id = $_POST['id_gallery'];
$judul = mysqli_real_escape_string($db, $_POST['judul']);
$foto = $_FILES['foto']['name'];
$tmp = $_FILES['foto']['tmp_name'];
if ($foto != "") {
    // Jika user upload gambar baru
    $folder = '../images/';
    $target = $folder . basename($foto);
    if (move_uploaded_file($tmp, $target)) {
        // Hapus gambar lama (opsional, jika diperlukan)
        $query_lama = mysqli_query($db, "SELECT foto FROM tbl_gallery WHERE
id_gallery = '$id'");
        $data_lama = mysqli_fetch_array($query_lama);
        $foto_lama = $data_lama['foto'];
        if (file_exists("../images/$foto_lama")) {
            unlink("../images/$foto_lama");
        }
        $sql = "UPDATE tbl_gallery SET judul='$judul', foto='$foto' WHERE
id_gallery='$id'";
    } else {
        echo "<script>alert('Gagal upload gambar baru.');
history.back();</script>";
        exit;
    }
} else {
    // Jika hanya mengubah judul tanpa ganti gambar
    $sql = "UPDATE tbl_gallery SET judul='$judul' WHERE id_gallery='$id'";
}
$query = mysqli_query($db, $sql);
if ($query) {
    echo "<script>alert('Data gallery berhasil diperbarui.');
window.location='data_gallery.php';</script>";
} else {
    echo "<script>alert('Gagal mengupdate data.');
history.back();</script>";
}
?>