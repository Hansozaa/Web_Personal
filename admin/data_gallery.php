<?php
include('../koneksi.php');
session_start();
if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit;
}

// Pesan sukses
if (isset($_GET['success'])) {
    $success_message = "Produk berhasil ditambahkan!";
}

// Pesan untuk operasi edit dan delete
if (isset($_GET['edit_success'])) {
    $success_message = "Produk berhasil diperbarui!";
}

if (isset($_GET['delete_success'])) {
    $success_message = "Produk berhasil dihapus!";
}
?>
<!DOCTYPE html>
<html lang="id" class="transition duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <style>
        /* PERBAIKAN UTAMA - Pastikan tombol aksi bisa diklik */
        .action-buttons {
            position: relative;
            z-index: 100;
            pointer-events: auto !important;
        }
        
        .action-btn {
            position: relative;
            z-index: 101;
            pointer-events: auto !important;
            transform: none !important;
            transition: all 0.2s ease;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            white-space: nowrap;
        }
        
        .action-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        /* Efek 3D untuk sidebar seperti contoh */
        .card-3d {
            transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            transform-style: preserve-3d;
            will-change: transform;
            position: relative;
            overflow: hidden;
        }
        
        .card-3d:hover {
            transform: translateY(-10px) rotateX(10deg) rotateY(-5deg) translateZ(30px);
            box-shadow: 0px 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        /* Style untuk gallery item */
        .gallery-item {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .gallery-img-container {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .gallery-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .gallery-item:hover .gallery-img {
            transform: scale(1.05);
        }
        
        .no-image {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6;
            color: #9ca3af;
            font-size: 14px;
            text-align: center;
            padding: 20px;
        }
        
        /* Animasi slide in untuk notifikasi */
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .animate-slide-in {
            animation: slideIn 0.5s ease-out forwards;
        }
        
        /* Efek glow pada hover */
        .glow-on-hover:hover {
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.7);
        }
        
        /* Mobile menu styles */
        .mobile-menu-button {
            display: none;
        }

        @media (max-width: 768px) {
            .mobile-menu-button {
                display: block;
            }
            
            .sidebar { 
                display: none; 
                position: fixed; 
                top: 0; 
                left: 0; 
                width: 80%; 
                height: 100vh; 
                z-index: 50; 
                overflow-y: auto;
                transform: none !important;
            }
            
            .sidebar.active {
                display: block;
                animation: slideInLeft 0.3s ease-out;
            }
            
            @keyframes slideInLeft {
                from { transform: translateX(-100%); }
                to { transform: translateX(0); }
            }
            
            .overlay { 
                display: none; 
                position: fixed; 
                top: 0; 
                left: 0; 
                width: 100%; 
                height: 100%; 
                background-color: rgba(0,0,0,0.5); 
                z-index: 40; 
            }
            
            .overlay.active {
                display: block;
            }
            
            .gallery-img-container {
                height: 150px;
            }
            
            /* Nonaktifkan efek 3D di mobile */
            .card-3d {
                transform: none !important;
            }
        }
    </style>
</head>
<body class="flex flex-col min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    <!-- Header - Updated -->
    <header class="bg-white dark:bg-slate-800 shadow-lg sticky top-0 z-40 border-b border-slate-200 dark:border-slate-700">
        <div class="container mx-auto px-4 flex justify-between items-center py-4">
            <div class="flex items-center">
                <button id="mobileMenuButton" class="mobile-menu-button mr-4 text-xl md:hidden">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold dark:text-white">Kelola Portofolio</h1>
                    <p class="text-gray-600 dark:text-slate-400 text-sm">Kelola Daftar Portofolio</p>
                </div>
            </div>
            <div class="flex items-center">
                <button id="darkToggle" class="mr-4 text-xl" title="Toggle Dark Mode">🌙</button>
                <a href="logout.php" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="hidden md:inline">Logout</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div id="overlay" class="overlay"></div>

    <!-- Konten Utama -->
    <div class="flex flex-grow container mx-auto mt-4 px-4 w-full">
        <!-- Sidebar with 3D effect -->
        <aside id="sidebar" class="sidebar w-1/4 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 mb-4 card-3d">
            <div class="card-3d-content">
                <h2 class="text-lg font-bold text-blue-700 dark:text-blue-400 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">MENU ADMIN</h2>
                <ul class="space-y-2">
                    <li>
                        <a href="beranda_admin.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700">
                            <i class="fas fa-home text-blue-600 dark:text-blue-400"></i>
                            <span class="dark:text-white">Beranda</span>
                        </a>
                    </li>
                    <li>
                        <a href="data_artikel.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700">
                            <i class="fas fa-newspaper text-blue-600 dark:text-blue-400"></i>
                            <span class="dark:text-white">Kelola Blog</span>
                        </a>
                    </li>
                    <li>
                        <a href="data_gallery.php" class="flex items-center gap-3 p-2 rounded-lg bg-blue-100 dark:bg-gray-700 text-blue-700 dark:text-white">
                            <i class="fas fa-images text-blue-700 dark:text-blue-400"></i>
                            <span>Kelola Portofolio</span>
                        </a>
                    </li>
                    <li>
                        <a href="data_foto.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700">
                            <i class="fas fa-camera text-blue-600 dark:text-blue-400"></i>
                            <span class="dark:text-white">Kelola Foto</span>
                        </a>
                    </li>
                    <li>
                        <a href="data_about.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700">
                            <i class="fas fa-building text-blue-600 dark:text-blue-400"></i>
                            <span class="dark:text-white">Kelola Profil</span>
                        </a>
                    </li>
                    <li>
                        <a href="logout.php" onclick="return confirm('Apakah anda yakin ingin keluar?');"
                            class="flex items-center gap-3 p-2 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-gray-700">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="dark:text-white">Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="w-full md:w-3/4 bg-white dark:bg-gray-800 rounded-lg shadow p-4 md:p-6 md:ml-6 mb-16">
            <!-- Alert Section -->
            <div class="mb-4">
                <?php if (isset($success_message)): ?>
                    <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-100 px-4 py-3 rounded-lg flex items-center animate-slide-in">
                        <i class="fas fa-check-circle mr-3 text-green-500 dark:text-green-300"></i>
                        <div>
                            <p class="font-medium"><?= $success_message ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-100 px-4 py-3 rounded-lg flex items-center animate-slide-in">
                        <i class="fas fa-exclamation-circle mr-3 text-red-500 dark:text-red-300"></i>
                        <div>
                            <p class="font-medium"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Header Content -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div class="mb-3 md:mb-0">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white">Daftar Portofolio</h2>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">Kelola Portofolio Anda</p>
                </div>
                <a href="add_gallery.php" class="bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white px-4 py-2 rounded-lg flex items-center justify-center glow-on-hover">
                    <i class="fas fa-plus mr-2"></i> Tambah Portofolio
                </a>
            </div>
            
            <?php
            $sql = "SELECT * FROM tbl_gallery";
            $query = mysqli_query($db, $sql);
            
            if (mysqli_num_rows($query) === 0): ?>
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/50 dark:to-indigo-900/50 border border-blue-200 dark:border-blue-700 rounded-xl p-6 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-info-circle text-blue-500 dark:text-blue-300 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-blue-800 dark:text-blue-200 mb-2">Data Produk Belum Tersedia</h3>
                        <p class="text-blue-700 dark:text-blue-300 mb-4">Anda belum memiliki data produk. Mulai dengan menambahkan produk baru.</p>
                        <a href="add_gallery.php" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2 rounded-lg inline-flex items-center glow-on-hover">
                            <i class="fas fa-plus mr-2"></i> Tambah Produk
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while ($data = mysqli_fetch_array($query)): ?>
                        <div class="gallery-item bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                            <div class="gallery-img-container">
                                <?php 
                                $imagePath = '../images/' . $data['foto'];
                                if (file_exists($imagePath)): ?>
                                    <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($data['judul']) ?>" class="gallery-img">
                                <?php else: ?>
                                    <div class="no-image dark:bg-gray-600 dark:text-gray-300 h-full">
                                        <div>
                                            <i class="fas fa-image text-3xl mb-2"></i>
                                            <p>Gambar tidak ditemukan</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-4 flex-grow flex flex-col">
                                <h3 class="font-semibold text-gray-800 dark:text-white mb-3 text-lg"><?= htmlspecialchars($data['judul']) ?></h3>
                                <div class="mt-auto flex justify-between action-buttons">
                                    <a href="edit_gallery.php?id_gallery=<?= $data['id_gallery'] ?>" class="action-btn bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <a href="delete_gallery.php?id_gallery=<?= $data['id_gallery'] ?>" onclick="return confirm('Yakin ingin menghapus produk ini?');" class="action-btn bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded flex items-center">
                                        <i class="fas fa-trash mr-1"></i> Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Footer - Updated -->
    <footer class="bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 py-4 mt-6">
        <div class="container mx-auto px-4 text-center text-sm">
            <p>&copy; <?php echo date('Y'); ?> | Created by Raihan Saputra</p>
        </div>
    </footer>

    <!-- Script Toggle Dark Mode - Updated -->
    <script>
        const toggleBtn = document.getElementById('darkToggle');
        const htmlEl = document.documentElement;

        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            htmlEl.classList.add('dark');
            toggleBtn.textContent = '☀️';
        } else {
            toggleBtn.textContent = '🌙';
        }

        toggleBtn.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            const isDark = htmlEl.classList.contains('dark');
            toggleBtn.textContent = isDark ? '☀️' : '🌙';
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });

        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        mobileMenuButton.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });

        // Nonaktifkan efek 3D untuk tombol aksi
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.action-buttons, .action-btn').forEach(el => {
                el.style.transform = 'none !important';
                el.style.pointerEvents = 'auto !important';
            });
        });
    </script>
</body>
</html>