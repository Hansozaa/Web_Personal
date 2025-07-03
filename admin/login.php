<?php
session_start();
if (isset($_SESSION['username'])) {
    header('location:beranda_admin.php');
}
require_once("../koneksi.php");
?>
<!DOCTYPE html>
<html lang="id" class="transition duration-300 ease-in-out">

<head>
    <meta charset="UTF-8">
    <title>Login Administrator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <style>
        .video-background {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -1;
            object-fit: cover;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.3);
            z-index: 0;
        }
        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 28rem; /* Sama dengan max-w-md (448px) */
            margin: 0 auto;
            padding: 2rem; /* Sama dengan p-8 */
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center transition duration-300 p-4">
    <!-- Video Background -->
    <video autoplay muted loop class="video-background">
        <source src="../videos/Wuwaa.mp4" type="video/mp4">
        Browser tidak mendukung video background.
    </video>
    <div class="overlay"></div>
    
    <!-- Original Login Form (TANPA PERUBAHAN UKURAN) -->
    <div class="login-wrapper">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8 w-full relative">
            <!-- Logo centered at top -->
            <div class="flex justify-center mb-6">
                <img src="../img/Wolha.png" alt="Logo Toko" class="h-20 object-contain">
            </div>
            
            <!-- Toggle Dark Mode -->
            <button id="darkToggle" class="absolute right-4 top-4 text-xl">
                🌙
            </button>

            <h2 class="text-2xl font-bold text-center text-blue-700 dark:text-blue-300 mb-6">Login Administrator</h2>
            <form action="cek_login.php" method="post" class="space-y-5">
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
                <div class="flex justify-between items-center">
                    <input type="submit" name="login" value="Login"
                        class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 cursor-pointer">
                    <input type="reset" name="cancel" value="Cancel"
                        class="bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-white px-4 py-2 rounded hover:bg-gray-400 dark:hover:bg-gray-500 cursor-pointer">
                </div>
            </form>

            <!-- Link ke halaman daftar -->
            <div class="text-center mt-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">Belum punya akun? 
                    <a href="daftar.php" class="text-blue-600 dark:text-blue-300 hover:underline">Daftar disini</a>
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

        // Video background handling
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.querySelector('.video-background');
            
            function resizeVideo() {
                const windowRatio = window.innerWidth / window.innerHeight;
                const videoRatio = 16/9;
                
                if (windowRatio > videoRatio) {
                    video.style.width = '100%';
                    video.style.height = 'auto';
                } else {
                    video.style.width = 'auto';
                    video.style.height = '100%';
                }
            }
            
            video.addEventListener('loadedmetadata', resizeVideo);
            window.addEventListener('resize', resizeVideo);
            
            video.addEventListener('error', function() {
                console.log('Video background failed to load');
                document.querySelector('.overlay').style.backgroundColor = 'rgba(0,0,0,0.6)';
            });
        });
    </script>
</body>
</html>
