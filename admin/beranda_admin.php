<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit;
}
require_once("../koneksi.php");
$username = $_SESSION['username'];
$sql_user = "SELECT * FROM tbl_user WHERE username = '$username'";
$query_user = mysqli_query($db, $sql_user);
$hasil_user = mysqli_fetch_array($query_user);

// --- PENGAMBILAN DATA STATISTIK ---
$jumlah_artikel = mysqli_num_rows(mysqli_query($db, "SELECT id_artikel FROM tbl_artikel"));
$jumlah_gallery = mysqli_num_rows(mysqli_query($db, "SELECT id_gallery FROM tbl_gallery"));
$jumlah_profil = mysqli_num_rows(mysqli_query($db, "SELECT id_about FROM tbl_about"));
$jumlah_foto = mysqli_num_rows(mysqli_query($db, "SELECT id_foto FROM tbl_foto"));
$query_pengunjung = mysqli_query($db, "SELECT id_pengunjung FROM tbl_pengunjung");
$jumlah_pengunjung = $query_pengunjung ? mysqli_num_rows($query_pengunjung) : 0;

// --- PENGAMBILAN DATA HISTORI AKTIVITAS ---
$histori_artikel = mysqli_query($db, "SELECT nama_artikel, tanggal_artikel FROM tbl_artikel ORDER BY tanggal_artikel DESC LIMIT 3");
$histori_foto = mysqli_query($db, "SELECT judul, foto FROM tbl_foto ORDER BY id_foto DESC LIMIT 3");
?>

<!DOCTYPE html>
<html lang="id" class=""> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <style>
        .perspective-container { perspective: 1200px; }
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
        .card-3d-content, .card-3d-icon {
            transform-style: preserve-3d;
            transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .card-3d-content { transform: translateZ(25px); }
        .card-3d-icon { transform: translateZ(50px); }
        .welcome-card { background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); }
        .mobile-menu-button { display: none; }
        @media (max-width: 768px) {
            .mobile-menu-button { display: block; }
            .sidebar { display: none; position: fixed; top: 0; left: 0; width: 80%; height: 100vh; z-index: 50; overflow-y: auto; }
            .sidebar.active { display: block; }
            .overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 40; }
            .overlay.active { display: block; }
        }
    </style>
</head>
<body class="flex flex-col min-h-screen bg-slate-100 dark:bg-gradient-to-br dark:from-slate-900 dark:to-gray-900 text-gray-800 dark:text-slate-200">
    <header class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm text-gray-800 dark:text-white py-4 shadow-lg sticky top-0 z-40 border-b border-slate-200 dark:border-slate-700">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center">
                <button id="mobileMenuButton" class="mobile-menu-button mr-4 text-xl md:hidden"><i class="fas fa-bars"></i></button>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold">Dashboard</h1>
                    <p class="text-gray-600 dark:text-slate-400 text-sm">System Interface</p>
                </div>
            </div>
            <div class="flex items-center">
                <button id="darkToggle" class="mr-4 text-xl" title="Toggle Dark Mode">🌙</button>
                <a href="logout.php" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-3 py-1 md:px-4 md:py-2 rounded-lg transition-all">
                    <i class="fas fa-sign-out-alt"></i><span class="hidden md:inline">Logout</span>
                </a>
            </div>
        </div>
    </header>

    <div id="overlay" class="overlay"></div>

    <div class="flex flex-grow container mx-auto mt-4 px-4 w-full perspective-container">
        <aside id="sidebar" class="sidebar w-full md:w-1/4 bg-white dark:bg-slate-800 text-gray-600 dark:text-slate-300 rounded-lg shadow-2xl p-4 mb-4 md:mb-0 md:mr-6 card-3d">
            <div class="card-3d-content">
                <h2 class="text-lg font-bold text-blue-600 dark:text-blue-400 mb-3 border-b border-slate-200 dark:border-slate-700 pb-2">NAVIGATION</h2>
                <ul class="space-y-2">
                    <li><a href="beranda_admin.php" class="flex items-center gap-3 p-2 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-white font-semibold"><i class="fas fa-home text-blue-500"></i><span>Dashboard</span></a></li>
                    <li><a href="data_artikel.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><i class="fas fa-newspaper text-slate-400"></i><span>Kelola Blog</span></a></li>
                    <li><a href="data_gallery.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><i class="fas fa-images text-slate-400"></i><span>Kelola Portofolio</span></a></li>
                    <li><a href="data_foto.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><i class="fas fa-camera text-slate-400"></i><span>Kelola Foto</span></a></li>
                    <li><a href="data_about.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><i class="fas fa-building text-slate-400"></i><span>Kelola Profil</span></a></li>
                    <li><a href="logout.php" onclick="return confirm('Apakah anda yakin ingin keluar?');" class="flex items-center gap-3 p-2 rounded-lg text-red-500 hover:bg-red-500/10"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
                </ul>
            </div>
        </aside>

        <main class="w-full md:w-3/4">
            <div class="welcome-card p-6 mb-6 rounded-lg shadow-2xl text-white card-3d">
                <div class="flex items-center card-3d-content">
                    <div class="bg-white bg-opacity-20 rounded-full p-3 mr-4 card-3d-icon">
                        <i class="fas fa-user-shield text-3xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold">Halo, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
                        <p class="text-blue-100">Sistem dalam kendali Anda.</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-6">
                <div class="stat-card bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 card-3d"><a href="data_artikel.php" class="flex justify-between items-center card-3d-content"><div><h3 class="text-slate-500 dark:text-slate-400">Artikel</h3><p class="text-2xl font-bold text-slate-800 dark:text-white"><?= $jumlah_artikel ?></p></div><i class="fas fa-newspaper text-blue-500 text-4xl opacity-70 card-3d-icon"></i></a></div>
                <div class="stat-card bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 card-3d"><a href="data_gallery.php" class="flex justify-between items-center card-3d-content"><div><h3 class="text-slate-500 dark:text-slate-400">Portofolio</h3><p class="text-2xl font-bold text-slate-800 dark:text-white"><?= $jumlah_gallery ?></p></div><i class="fas fa-images text-green-500 text-4xl opacity-70 card-3d-icon"></i></a></div>
                <div class="stat-card bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 card-3d"><a href="data_foto.php" class="flex justify-between items-center card-3d-content"><div><h3 class="text-slate-500 dark:text-slate-400">Foto</h3><p class="text-2xl font-bold text-slate-800 dark:text-white"><?= $jumlah_foto ?></p></div><i class="fas fa-camera text-purple-500 text-4xl opacity-70 card-3d-icon"></i></a></div>
                <div class="stat-card bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 card-3d"><a href="data_about.php" class="flex justify-between items-center card-3d-content"><div><h3 class="text-slate-500 dark:text-slate-400">Profil</h3><p class="text-2xl font-bold text-slate-800 dark:text-white"><?= $jumlah_profil ?></p></div><i class="fas fa-building text-indigo-500 text-4xl opacity-70 card-3d-icon"></i></a></div>
                <div class="stat-card bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 card-3d"><div class="flex justify-between items-center card-3d-content"><div><h3 class="text-slate-500 dark:text-slate-400">Pengunjung</h3><p class="text-2xl font-bold text-slate-800 dark:text-white"><?= $jumlah_pengunjung ?></p></div><i class="fas fa-users text-yellow-500 text-4xl opacity-70 card-3d-icon"></i></div></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:items-start">
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 backdrop-blur-sm p-6 rounded-lg shadow-2xl card-3d">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4 card-3d-content">Visualisasi Data</h3>
                    <div class="relative h-80 md:h-96 card-3d-content">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 backdrop-blur-sm p-6 rounded-lg shadow-2xl card-3d">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4 card-3d-content">Log Aktivitas</h3>
                    <ul class="space-y-4 max-h-96 overflow-y-auto pr-2 card-3d-content">
                        <?php while($artikel = mysqli_fetch_array($histori_artikel)): ?>
                        <li class="flex items-start">
                            <div class="bg-blue-100 dark:bg-slate-700 rounded-full h-10 w-10 flex-shrink-0 flex items-center justify-center mr-4 border border-blue-200 dark:border-slate-600">
                                <i class="fas fa-newspaper text-blue-500"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-700 dark:text-slate-200">Log: Artikel</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400 truncate" title="<?= htmlspecialchars($artikel['nama_artikel']) ?>"><?= htmlspecialchars($artikel['nama_artikel']) ?></p>
                            </div>
                        </li>
                        <?php endwhile; ?>
                        <?php while($foto = mysqli_fetch_array($histori_foto)): ?>
                        <li class="flex items-start">
                            <div class="bg-purple-100 dark:bg-slate-700 rounded-full h-10 w-10 flex-shrink-0 flex items-center justify-center mr-4 border border-purple-200 dark:border-slate-600">
                                <i class="fas fa-camera text-purple-500"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-700 dark:text-slate-200">Log: Foto</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400 truncate" title="<?= htmlspecialchars($foto['judul']) ?>"><?= htmlspecialchars($foto['judul']) ?></p>
                            </div>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <footer class="bg-white dark:bg-slate-800 text-slate-500 py-6 mt-6 border-t border-slate-200 dark:border-slate-700">
        <div class="container mx-auto px-4 text-center"><p class="text-sm">&copy; <?php echo date('Y'); ?> | System Interface by Raihan Saputra</p></div>
    </footer>

    <script>
        // --- SCRIPT UTAMA (FUNGSI DARK MODE DIPERBAIKI) ---
        
        const toggleBtn = document.getElementById('darkToggle');
        const htmlEl = document.documentElement;

        if (toggleBtn) {
            // Periksa localStorage. Jika 'light', set mode terang.
            // Jika tidak ada (null) atau isinya 'dark', maka set mode gelap sebagai default.
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
        
        // ... (Mobile menu script) ...
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
        
        // ... (Chart.js script) ...
        const ctx = document.getElementById('myChart');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Artikel', 'Portofolio', 'Foto', 'Profil'],
                datasets: [{
                    label: 'Total Data',
                    data: [<?= $jumlah_artikel ?>, <?= $jumlah_gallery ?>, <?= $jumlah_foto ?>, <?= $jumlah_profil ?>],
                    backgroundColor: ['rgba(59, 130, 246, 0.7)','rgba(34, 197, 94, 0.7)','rgba(168, 85, 247, 0.7)','rgba(99, 102, 241, 0.7)'],
                    borderColor: 'transparent',
                    borderWidth: 4,
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1500,
                    easing: 'easeOutQuart',
                },
                plugins: { 
                    legend: { 
                        position: 'bottom',
                        labels: {
                            color: document.documentElement.classList.contains('dark') ? '#cbd5e1' : '#475569',
                            font: { size: 14 }
                        }
                     },
                     tooltip: {
                         backgroundColor: '#1e293b', titleColor: '#f1f5f9',
                         bodyColor: '#e2e8f0', borderColor: '#334155', borderWidth: 1
                     }
                },
                cutout: '60%'
            }
        });

        // ... (3D Interactive script) ...
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.card-3d');
            cards.forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    const rotateX = ((y - centerY) / centerY) * -8;
                    const rotateY = ((x - centerX) / centerX) * 8;
                    card.style.transform = `translateY(-5px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(20px)`;
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'rotateX(0deg) rotateY(0deg) translateZ(0px)';
                });
            });
        });
    </script>
</body>
</html>