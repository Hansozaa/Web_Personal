<?php
include('../koneksi.php');
session_start();
if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit;
}

// Pesan sukses
if (isset($_GET['success'])) {
    $success_message = "Data artikel berhasil ditambahkan!";
}

if (isset($_GET['edit_success'])) {
    $success_message = "Data artikel berhasil diperbarui!";
}

if (isset($_GET['delete_success'])) {
    $success_message = "Data artikel berhasil dihapus!";
}
?>
<!DOCTYPE html>
<html lang="id" class="transition duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Artikel</title>
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
        }
        
        /* Efek 3D untuk card kecuali yang berisi tabel */
        .card-3d:not(.table-card) {
            transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            transform-style: preserve-3d;
            will-change: transform;
            position: relative;
            overflow: hidden;
        }
        
        .card-3d:not(.table-card):hover {
            transform: translateY(-10px) rotateX(10deg) rotateY(-5deg) translateZ(30px);
            box-shadow: 0px 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        /* Style dasar lainnya */
        .thumbnail {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            transition: transform 0.3s ease;
        }
        
        .thumbnail:hover {
            transform: scale(1.5);
            z-index: 10;
        }
        
        .truncate-text {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .animate-slide-in {
            animation: slideIn 0.5s ease-out forwards;
        }
        
        .glow-on-hover:hover {
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.7);
        }
        
        /* Mobile menu styles */
        @media (max-width: 768px) {
            .mobile-menu-button { display: block; }
            .sidebar { 
                display: none; 
                position: fixed; 
                top: 0; 
                left: 0; 
                width: 80%; 
                height: 100vh; 
                z-index: 50; 
            }
            .sidebar.active { display: block; }
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
            .overlay.active { display: block; }
        }
    </style>
</head>

<body class="flex flex-col min-h-screen bg-slate-100 dark:bg-slate-900 text-gray-800 dark:text-slate-200">
    <!-- Header -->
    <header class="bg-white dark:bg-slate-800 shadow-lg sticky top-0 z-40 border-b border-slate-200 dark:border-slate-700">
        <div class="container mx-auto px-4 flex justify-between items-center py-4">
            <div class="flex items-center">
                <button id="mobileMenuButton" class="mobile-menu-button mr-4 text-xl md:hidden">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold">Kelola Artikel</h1>
                    <p class="text-gray-600 dark:text-slate-400 text-sm">Kelola informasi artikel anda</p>
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
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar w-full md:w-1/4 bg-white dark:bg-slate-800 rounded-lg shadow-lg p-4 mb-4 md:mb-0 md:mr-6 card-3d">
            <div class="card-3d-content">
                <h2 class="text-lg font-bold text-blue-600 dark:text-blue-400 mb-3 border-b border-slate-200 dark:border-slate-700 pb-2">NAVIGATION</h2>
                <ul class="space-y-2">
                    <li><a href="beranda_admin.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><i class="fas fa-home text-slate-400"></i><span>Dashboard</span></a></li>
                    <li><a href="data_artikel.php" class="flex items-center gap-3 p-2 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-white font-semibold"><i class="fas fa-newspaper text-blue-500"></i><span>Kelola Blog</span></a></li>
                    <li><a href="data_gallery.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><i class="fas fa-images text-slate-400"></i><span>Kelola Portofolio</span></a></li>
                    <li><a href="data_foto.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><i class="fas fa-camera text-slate-400"></i><span>Kelola Foto</span></a></li>
                    <li><a href="data_about.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><i class="fas fa-building text-slate-400"></i><span>Kelola Profil</span></a></li>
                    <li><a href="logout.php" onclick="return confirm('Apakah anda yakin ingin keluar?');" class="flex items-center gap-3 p-2 rounded-lg text-red-500 hover:bg-red-500/10"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
                </ul>
            </div>
        </aside>

        <main class="w-full md:w-3/4">
            <!-- Notifikasi -->
            <div class="mb-4 animate-slide-in">
                <?php if (isset($success_message)): ?>
                    <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-100 px-4 py-3 rounded-lg flex items-center">
                        <i class="fas fa-check-circle mr-3 text-green-500 dark:text-green-300"></i>
                        <p class="font-medium"><?= $success_message ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-100 px-4 py-3 rounded-lg flex items-center">
                        <i class="fas fa-exclamation-circle mr-3 text-red-500 dark:text-red-300"></i>
                        <p class="font-medium"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Header dan Tombol Tambah -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div class="mb-3 md:mb-0">
                    <h2 class="text-xl md:text-2xl font-bold text-slate-800 dark:text-white">Daftar Artikel</h2>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">Kelola Informasi Artikel Anda</p>
                </div>
                <a href="add_artikel.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-plus mr-2"></i> 
                    <span>Tambah Artikel</span>
                </a>
            </div>
            
            <!-- Tabel Artikel -->
            <?php
            $sql = "SELECT * FROM tbl_artikel ORDER BY id_artikel DESC";
            $query = mysqli_query($db, $sql);
            
            if (mysqli_num_rows($query) === 0): ?>
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-6 text-center">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-newspaper text-blue-500 dark:text-blue-300 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-blue-800 dark:text-blue-200 mb-2">Belum Ada Artikel</h3>
                    <p class="text-blue-700 dark:text-blue-300 mb-4">Mulai dengan menambahkan artikel pertama Anda</p>
                    <a href="add_artikel.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                        <i class="fas fa-plus mr-2"></i> Tambah Artikel
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg overflow-hidden table-card">
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-slate-200 dark:divide-slate-700">
                            <thead class="bg-slate-50 dark:bg-slate-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wider">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wider">Foto</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wider">Judul</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wider">Isi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-700 dark:text-slate-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                                <?php $no = 1; while ($data = mysqli_fetch_array($query)): ?>
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                    <td class="px-4 py-3 whitespace-nowrap text-center"><?= $no++ ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <?php if (!empty($data['foto_artikel'])): ?>
                                            <?php
                                            $found = false;
                                            $paths = [
                                                '../uploads/'.$data['foto_artikel'],
                                                'uploads/'.$data['foto_artikel'],
                                                $data['foto_artikel']
                                            ];
                                            
                                            foreach ($paths as $path) {
                                                if (file_exists($path)) {
                                                    echo '<img src="'.$path.'" alt="'.htmlspecialchars($data['nama_artikel']).'" class="thumbnail mx-auto">';
                                                    $found = true;
                                                    break;
                                                }
                                            }
                                            
                                            if (!$found): ?>
                                                <span class="text-red-500 text-sm">Gambar tidak ditemukan</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-gray-400 text-sm">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="max-w-[150px] truncate">
                                            <?= htmlspecialchars($data['nama_artikel']) ?>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="max-w-xs max-h-[100px] overflow-y-auto text-sm">
                                            <?= nl2br(htmlspecialchars(substr($data['isi_artikel'], 0, 200))) ?>...
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <?= !empty($data['tanggal_artikel']) ? date('d M Y, H:i', strtotime($data['tanggal_artikel'])) : '-' ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap action-buttons">
                                        <div class="flex gap-2">
                                            <a href="edit_artikel.php?id_artikel=<?= $data['id_artikel'] ?>" 
                                               class="action-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded flex items-center"
                                               onclick="return confirm('Yakin ingin mengedit artikel ini?')">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <a href="delete_artikel.php?id_artikel=<?= $data['id_artikel'] ?>" 
                                               class="action-btn bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded flex items-center"
                                               onclick="return confirm('Yakin ingin menghapus artikel ini?')">
                                                <i class="fas fa-trash mr-1"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <footer class="bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 py-4 mt-6">
        <div class="container mx-auto px-4 text-center text-sm">
            &copy; <?= date('Y') ?> Sistem Admin
        </div>
    </footer>

    <script>
        // Dark Mode Toggle
        const toggleBtn = document.getElementById('darkToggle');
        const htmlEl = document.documentElement;
        
        if (toggleBtn) {
            if (localStorage.getItem('theme') === 'light') {
                htmlEl.classList.remove('dark');
                toggleBtn.textContent = '🌙';
            } else {
                htmlEl.classList.add('dark');
                toggleBtn.textContent = '☀️';
            }
            
            toggleBtn.addEventListener('click', () => {
                htmlEl.classList.toggle('dark');
                const isDark = htmlEl.classList.contains('dark');
                toggleBtn.textContent = isDark ? '☀️' : '🌙';
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
            });
        }
        
        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        if (mobileMenuButton && sidebar && overlay) {
            mobileMenuButton.addEventListener('click', () => {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            });
            
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }
        
        // Nonaktifkan efek 3D untuk tabel dan tombol aksi
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.table-card, .action-buttons, .action-btn').forEach(el => {
                el.style.transform = 'none !important';
                el.style.pointerEvents = 'auto !important';
            });
        });
    </script>
</body>
</html>