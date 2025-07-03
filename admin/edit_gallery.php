<?php
include('../koneksi.php');
session_start();
if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit;
}
$id = $_GET['id_gallery'];
$sql = "SELECT * FROM tbl_gallery WHERE id_gallery = '$id'";
$query = mysqli_query($db, $sql);
$data = mysqli_fetch_array($query);
?>
<!DOCTYPE html>
<html lang="id" class="transition duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <style>
        .file-input-container {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }
        
        .file-input-container input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-input-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            border: 2px dashed #d1d5db;
            border-radius: 0.5rem;
            background-color: #f9fafb;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-input-label:hover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        
        .file-input-label.dark {
            border-color: #4b5563;
            background-color: #1f2937;
            color: #d1d5db;
        }
        
        .file-input-label.dark:hover {
            border-color: #60a5fa;
            background-color: #1e3a8a;
        }
        
        .preview-container {
            margin-top: 1rem;
        }
        
        .preview-image {
            max-width: 100%;
            max-height: 300px;
            border-radius: 0.5rem;
            object-fit: cover;
        }
        
        /* Efek 3D untuk card */
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
                    <h1 class="text-xl md:text-2xl font-bold">Edit Project</h1>
                    <p class="text-gray-600 dark:text-slate-400 text-sm">Edit detail project perusahaan</p>
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
            <h2 class="text-lg font-bold text-blue-600 dark:text-blue-400 mb-3 border-b border-slate-200 dark:border-slate-700 pb-2">NAVIGATION</h2>
            <ul class="space-y-2">
                <li><a href="beranda_admin.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><i class="fas fa-home text-slate-400"></i><span>Dashboard</span></a></li>
                <li><a href="data_artikel.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><i class="fas fa-newspaper text-slate-400"></i><span>Kelola Blog</span></a></li>
                <li><a href="data_gallery.php" class="flex items-center gap-3 p-2 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-white font-semibold"><i class="fas fa-images text-blue-500"></i><span>Kelola Portofolio</span></a></li>
                <li><a href="data_foto.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><i class="fas fa-camera text-slate-400"></i><span>Kelola Foto</span></a></li>
                <li><a href="data_about.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><i class="fas fa-building text-slate-400"></i><span>Kelola Profil</span></a></li>
                <li><a href="logout.php" onclick="return confirm('Apakah anda yakin ingin keluar?');" class="flex items-center gap-3 p-2 rounded-lg text-red-500 hover:bg-red-500/10"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="w-full md:w-3/4">
            <!-- Header dan Tombol Kembali -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div class="mb-3 md:mb-0">
                    <h2 class="text-xl md:text-2xl font-bold text-slate-800 dark:text-white">Form Edit Project</h2>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">Perbarui detail project Anda</p>
                </div>
                <a href="data_gallery.php" class="bg-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>

            <!-- Form -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg overflow-hidden card-3d p-6">
                <form action="proses_edit_gallery.php" method="post" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="id_gallery" value="<?php echo $data['id_gallery']; ?>">
                    
                    <div>
                        <label for="judul" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Nama Project
                            <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="judul" name="judul" required
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                                bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 border-slate-300 dark:border-slate-600"
                            value="<?php echo htmlspecialchars($data['judul']); ?>"
                            placeholder="Masukkan nama project">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Gambar Saat Ini
                        </label>
                        <div class="flex items-center gap-4">
                            <?php 
                            $imagePath = '../images/' . $data['foto'];
                            if (file_exists($imagePath)): ?>
                                <img src="<?= $imagePath ?>" alt="Current Image" class="w-40 h-40 object-cover rounded-lg border border-slate-300 dark:border-slate-600">
                            <?php else: ?>
                                <div class="w-40 h-40 bg-slate-200 dark:bg-slate-700 rounded-lg flex flex-col items-center justify-center text-slate-500 dark:text-slate-400">
                                    <i class="fas fa-image text-3xl mb-2"></i>
                                    <span class="text-sm">Gambar tidak ditemukan</span>
                                </div>
                            <?php endif; ?>
                            <div>
                                <p class="text-sm text-slate-600 dark:text-slate-300">Nama file: <?= $data['foto'] ?></p>
                                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">Ganti gambar jika diperlukan</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Ganti Gambar (Opsional)
                        </label>
                        <div class="file-input-container">
                            <label for="foto" class="file-input-label dark:file-input-label dark">
                                <i class="fas fa-sync-alt text-3xl text-blue-500 mb-2"></i>
                                <span class="font-medium">Klik untuk memilih gambar baru</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 mt-1">Format: JPG, PNG, JPEG (Maks. 5MB)</span>
                            </label>
                            <input type="file" id="foto" name="foto" accept="image/*">
                        </div>
                        <div id="preview-container" class="preview-container mt-4 hidden">
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Pratinjau Gambar Baru:</p>
                            <img id="preview-image" class="preview-image">
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4 mt-8">
                        <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                        <a href="data_gallery.php" 
                            class="bg-slate-500 hover:bg-slate-600 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fas fa-times mr-2"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 py-4 mt-6">
        <div class="container mx-auto px-4 text-center text-sm">
            &copy; <?php echo date('Y'); ?> | Created by Raihan Saputra
        </div>
    </footer>

    <!-- Script Toggle Dark Mode -->
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

        // Image preview functionality
        const fileInput = document.getElementById('foto');
        const previewContainer = document.getElementById('preview-container');
        const previewImage = document.getElementById('preview-image');

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.addEventListener('load', function() {
                    previewImage.src = this.result;
                    previewContainer.style.display = 'block';
                });
                
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
                previewImage.src = '';
            }
        });
    </script>
</body>
</html>