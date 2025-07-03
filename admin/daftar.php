<?php
session_start();
if (isset($_SESSION['username'])) {
    header('location:beranda_admin.php');
}
require_once("../koneksi.php");

// Proses pendaftaran jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($db, $_POST['confirm_password']);

    // Validasi
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Semua field harus diisi!";
    } elseif ($password != $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } else {
        // Cek apakah username sudah ada
        $check_sql = "SELECT * FROM tbl_user WHERE username='$username'";
        $check_query = mysqli_query($db, $check_sql);
        
        if (mysqli_num_rows($check_query) > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Simpan ke database
            $insert_sql = "INSERT INTO tbl_user (username, password) VALUES ('$username', '$password')";
            if (mysqli_query($db, $insert_sql)) {
                $success = "Pendaftaran berhasil! Silakan login.";
            } else {
                $error = "Terjadi kesalahan: " . mysqli_error($db);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="transition duration-300 ease-in-out">

<head>
    <meta charset="UTF-8">
    <title>Daftar Akun Administrator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
</head>

<body class="bg-gradient-to-br from-blue-100 to-blue-300 dark:from-gray-800 dark:to-gray-900 min-h-screen flex items-center justify-center transition duration-300">

    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8 w-full max-w-md relative">
        <!-- Logo centered at top -->
        <div class="flex justify-center mb-6">
            <img src="../img/Wolha.png" alt="Logo Toko" class="h-20 object-contain">
        </div>
        
        <!-- Toggle Dark Mode -->
        <button id="darkToggle" class="absolute right-4 top-4 text-xl">
            🌙
        </button>

        <h2 class="text-2xl font-bold text-center text-blue-700 dark:text-blue-300 mb-6">Daftar Akun Baru</h2>
        
        <!-- Notifikasi Error/Success -->
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $success; ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="space-y-5">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                <input type="text" name="username" id="username" required
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required
                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-between items-center">
                <input type="submit" name="daftar" value="Daftar"
                    class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 cursor-pointer">
                <input type="reset" name="cancel" value="Cancel"
                    class="bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-white px-4 py-2 rounded hover:bg-gray-400 dark:hover:bg-gray-500 cursor-pointer">
            </div>
        </form>

        <!-- Link ke halaman login -->
        <div class="text-center mt-6">
            <p class="text-sm text-gray-600 dark:text-gray-400">Sudah punya akun? 
                <a href="login.php" class="text-blue-600 dark:text-blue-300 hover:underline">Login disini</a>
            </p>
        </div>

        <!-- Tombol Kembali -->
        <div class="text-center mt-6">
            <a href="../index.php" class="inline-block text-blue-600 dark:text-blue-300 hover:underline text-sm">
                ← Kembali ke Beranda Utama
            </a>
        </div>

        <!-- Footer -->
        <div class="text-center text-sm text-gray-600 dark:text-gray-400 mt-6">
            &copy; <?php echo date('Y'); ?> - Raihan Saputra
        </div>
    </div>

    <!-- Script Toggle Dark Mode -->
    <script>
        const toggleBtn = document.getElementById('darkToggle');
        const htmlEl = document.documentElement;

        if (localStorage.getItem('theme') === 'dark') {
            htmlEl.classList.add('dark');
            toggleBtn.textContent = '☀️';
        }

        toggleBtn.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            const isDark = htmlEl.classList.contains('dark');
            toggleBtn.textContent = isDark ? '☀️' : '🌙';
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    </script>
</body>

</html>