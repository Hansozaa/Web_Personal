<?php
include('../koneksi.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit;
}

// Pesan sukses atau error
$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    $success_message = "Data profil berhasil ditambahkan!";
} elseif (isset($_GET['edit_success'])) {
    $success_message = "Data profil berhasil diperbarui!";
} elseif (isset($_GET['delete_success'])) {
    $success_message = "Data profil berhasil dihapus!";
}

if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']); // Hapus pesan error setelah ditampilkan
}

// Query ke tabel tbl_about
$sql = "SELECT * FROM tbl_about ORDER BY id_about DESC";
$result = mysqli_query($db, $sql);

if (!$result) {
    die("Query Error: " . mysqli_error($db));
}

// Cek kolom-kolom yang ada
$hasKoordinatColumn = false;
$hasLinkMapsColumn = false;

$result_columns = mysqli_query($db, "SHOW COLUMNS FROM tbl_about");
if ($result_columns) {
    while ($column = mysqli_fetch_assoc($result_columns)) {
        if ($column['Field'] == 'koordinat') {
            $hasKoordinatColumn = true;
        }
        if ($column['Field'] == 'link_maps') {
            $hasLinkMapsColumn = true;
        }
    }
} else {
    error_log("Error checking columns: " . mysqli_error($db));
}

define('UPLOAD_BASE_PATH', '../uploads/about/'); 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Profil</title>
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
        
        /* Efek 3D untuk sidebar */
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
        
        /* Style untuk menu item */
        .menu-item {
            transition: all 0.3s ease;
            transform-style: preserve-3d;
        }
        
        .menu-item:hover {
            transform: translateX(5px);
            background-color: rgba(59, 130, 246, 0.1);
        }
        
        .menu-item.active {
            background-color: rgba(59, 130, 246, 0.2);
            border-left: 3px solid #3b82f6;
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
            
            /* Nonaktifkan efek 3D di mobile */
            .card-3d {
                transform: none !important;
            }
        }
        
        /* Table styles */
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .sticky-actions {
            position: sticky;
            right: 0;
            background: white;
            z-index: 2;
            box-shadow: -5px 0 15px rgba(0,0,0,0.05);
        }
        
        .dark .sticky-actions {
            background: #1f2937;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    <!-- Header - updated to match reference -->
    <header class="bg-white dark:bg-slate-800 shadow-lg sticky top-0 z-40 border-b border-slate-200 dark:border-slate-700">
        <div class="container mx-auto px-4 flex justify-between items-center py-4">
            <div class="flex items-center">
                <button id="mobileMenuButton" class="mobile-menu-button mr-4 text-xl md:hidden">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold">Data Profil</h1>
                    <p class="text-gray-600 dark:text-slate-400 text-sm">Kelola informasi profil pribadi Anda</p>
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

    <div class="flex flex-grow container mx-auto mt-4 px-4 w-full">
        <!-- Sidebar with 3D effect -->
        <aside id="sidebar" class="sidebar w-1/4 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 mb-4 card-3d">
            <div class="card-3d-content">
                <h2 class="text-lg font-bold text-blue-700 dark:text-blue-400 mb-3 border-b border-gray-200 dark:border-gray-700 pb-2">MENU ADMIN</h2>
                <ul class="space-y-2">
                    <li class="menu-item">
                        <a href="beranda_admin.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700">
                            <i class="fas fa-home text-blue-600 dark:text-blue-400"></i>
                            <span class="dark:text-white">Beranda</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="data_artikel.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700">
                            <i class="fas fa-newspaper text-blue-600 dark:text-blue-400"></i>
                            <span class="dark:text-white">Kelola Blog</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="data_gallery.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700">
                            <i class="fas fa-images text-blue-600 dark:text-blue-400"></i>
                            <span class="dark:text-white">Kelola Portofolio</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="data_foto.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700">
                            <i class="fas fa-camera text-blue-600 dark:text-blue-400"></i>
                            <span class="dark:text-white">Kelola Foto</span>
                        </a>
                    </li>
                    <li class="menu-item active">
                        <a href="data_about.php" class="flex items-center gap-3 p-2 rounded-lg bg-blue-100 dark:bg-gray-700 text-blue-700 dark:text-white">
                            <i class="fas fa-building text-blue-700 dark:text-blue-400"></i>
                            <span>Kelola Profil</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="logout.php" onclick="return confirm('Apakah anda yakin ingin keluar?');"
                            class="flex items-center gap-3 p-2 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-gray-700">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="dark:text-white">Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <main class="w-full md:w-3/4 bg-white dark:bg-gray-800 rounded-lg shadow p-4 md:p-6 md:ml-6 mb-16">
            <div class="mb-4">
                <?php if (!empty($success_message)): ?>
                    <div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-100 px-4 py-3 rounded-lg flex items-center animate-slide-in">
                        <i class="fas fa-check-circle mr-3 text-green-500 dark:text-green-300"></i>
                        <div>
                            <p class="font-medium"><?= $success_message ?></p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                    <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-100 px-4 py-3 rounded-lg flex items-center animate-slide-in">
                        <i class="fas fa-exclamation-circle mr-3 text-red-500 dark:text-red-300"></i>
                        <div>
                            <p class="font-medium"><?= $error_message ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
                <div class="mb-3 md:mb-0">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white">Daftar Profil Pribadi</h2>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">Kelola informasi profil pribadi Anda</p>
                </div>
                <a href="add_about.php" class="bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white px-4 py-2 rounded-lg flex items-center justify-center glow-on-hover">
                    <i class="fas fa-plus mr-2"></i> Tambah Data
                </a>
            </div>
            
            <?php if (mysqli_num_rows($result) === 0): ?>
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/50 dark:to-indigo-900/50 border border-blue-200 dark:border-blue-700 rounded-xl p-6 text-center mt-4">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-info-circle text-blue-500 dark:text-blue-300 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-blue-800 dark:text-blue-200 mb-2">Data Profil Belum Tersedia</h3>
                        <p class="text-blue-700 dark:text-blue-300 mb-4">Anda belum memiliki data profil. Mulai dengan menambahkan data baru.</p>
                        <a href="add_about.php" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2 rounded-lg inline-flex items-center glow-on-hover">
                            <i class="fas fa-plus mr-2"></i> Tambah Profil
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-container mt-4">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Foto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Alamat</th>
                                <?php if ($hasKoordinatColumn): ?>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Koordinat</th>
                                <?php endif; ?>
                                <?php if ($hasLinkMapsColumn): ?>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Link Maps</th>
                                <?php endif; ?>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider sticky-actions">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <?php 
                                    $image_path = '';
                                    if (!empty($row['foto_profil'])) {
                                        $potential_path = UPLOAD_BASE_PATH . basename($row['foto_profil']);
                                        if (file_exists($potential_path)) {
                                            $image_path = $potential_path;
                                        } else {
                                            if (file_exists($row['foto_profil'])) {
                                                $image_path = $row['foto_profil'];
                                            } elseif (file_exists('../' . $row['foto_profil'])) {
                                                $image_path = '../' . $row['foto_profil'];
                                            } elseif (file_exists('../../' . $row['foto_profil'])) {
                                                $image_path = '../../' . $row['foto_profil'];
                                            }
                                        }
                                    }

                                    if (!empty($image_path)):
                                    ?>
                                        <img src="<?= htmlspecialchars($image_path) ?>" alt="Foto Profil" class="w-16 h-16 object-cover rounded-lg">
                                    <?php else: ?>
                                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg w-16 h-16 flex items-center justify-center border border-dashed border-gray-300 dark:border-gray-600">
                                            <i class="fas fa-image text-gray-400 dark:text-gray-300"></i>
                                        </div>
                                        <?php if (!empty($row['foto_profil'])): ?>
                                            <div class="text-red-500 text-xs mt-1">Gambar tidak ditemukan</div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="max-w-[150px] dark:text-gray-200">
                                        <?= htmlspecialchars($row['alamat']) ?>
                                    </div>
                                </td>
                                <?php if ($hasKoordinatColumn): ?>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300 px-2 py-1 rounded text-xs font-mono">
                                            <?= htmlspecialchars($row['koordinat'] ?? '') ?>
                                        </div>
                                    </td>
                                <?php endif; ?>
                                <?php if ($hasLinkMapsColumn): ?>
                                    <td class="px-4 py-3">
                                        <?php if (!empty($row['link_maps'])): ?>
                                            <a href="<?= htmlspecialchars($row['link_maps']) ?>" 
                                                target="_blank" 
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 inline-flex items-center"
                                                title="<?= htmlspecialchars($row['link_maps']) ?>">
                                                <i class="fas fa-map-marker-alt mr-1 text-red-500"></i>
                                                <span class="truncate max-w-[150px]"><?= htmlspecialchars($row['link_maps']) ?></span>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-500 dark:text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td class="px-4 py-3">
                                    <div class="max-w-xs max-h-[100px] overflow-y-auto text-sm dark:text-gray-300">
                                        <?php
                                        $description = $row['about'];
                                        $description = str_replace(array('\\r\\n', '\\n', '\\r'), array("\r\n", "\n", "\r"), $description);
                                        $description = str_replace('\\', '', $description); 
                                        echo nl2br(htmlspecialchars($description));
                                        ?>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap sticky-actions action-buttons">
                                    <div class="flex gap-2">
                                        <a href="edit_about.php?id_about=<?= $row['id_about'] ?>" class="action-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded flex items-center">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        <a href="delete_about.php?id_about=<?= $row['id_about'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="action-btn bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded flex items-center">
                                            <i class="fas fa-trash mr-1"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Footer - updated to match reference -->
    <footer class="bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 py-4 mt-6">
        <div class="container mx-auto px-4 text-center text-sm">
            &copy; <?php echo date('Y'); ?> | Dibuat oleh Raihan Saputra
        </div>
    </footer>

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