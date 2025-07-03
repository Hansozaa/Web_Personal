<?php
include('../koneksi.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit;
}

$id_about = $_GET['id_about'] ?? 0;
$id_about = (int)$id_about;

$sql = "SELECT * FROM tbl_about WHERE id_about = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_about);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['error'] = "Data tidak ditemukan";
    header('Location: data_about.php');
    exit;
}

$koordinat = $data['koordinat'] ?? '-6.2088,106.8456';
[$default_lat, $default_lng] = explode(',', $koordinat);
$link_maps_db = $data['link_maps'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $alamat = mysqli_real_escape_string($db, $_POST['alamat']);
    
    $about_raw = $_POST['about'];
    $about_standardized = str_replace("\r\n", "\n", $about_raw);
    $about_standardized = str_replace("\r", "\n", $about_standardized);
    $about = mysqli_real_escape_string($db, $about_standardized);

    $koordinat = mysqli_real_escape_string($db, $_POST['koordinat']);
    $link_maps = mysqli_real_escape_string($db, $_POST['link_maps'] ?? '');

    $foto_profil = $data['foto_profil'];
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/about/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION);
        $new_filename = 'profil_' . time() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $max_size = 2 * 1024 * 1024;
        
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            if ($_FILES['foto_profil']['size'] <= $max_size) {
                if (!empty($foto_profil) && file_exists("../" . $foto_profil)) {
                    unlink("../" . $foto_profil);
                }
                
                if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_file)) {
                    $foto_profil = "uploads/about/" . $new_filename;
                } else {
                    $_SESSION['error'] = "Gagal mengupload file. Error code: " . $_FILES['foto_profil']['error'];
                    header("Location: edit_about.php?id_about=$id_about");
                    exit;
                }
            } else {
                $_SESSION['error'] = "Ukuran file terlalu besar. Maksimal 2MB.";
                header("Location: edit_about.php?id_about=$id_about");
                exit;
            }
        } else {
            $_SESSION['error'] = "Format file tidak didukung. Gunakan JPG, JPEG, atau PNG.";
            header("Location: edit_about.php?id_about=$id_about");
            exit;
        }
    }

    $sql = "UPDATE tbl_about SET 
            foto_profil = ?,
            alamat = ?,
            koordinat = ?,
            link_maps = ?,
            about = ?
            WHERE id_about = ?";
    
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $foto_profil, $alamat, $koordinat, $link_maps, $about, $id_about);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Data profil berhasil diperbarui!";
        header('Location: data_about.php');
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($db);
        header("Location: edit_about.php?id_about=$id_about");
    }
    
    mysqli_stmt_close($stmt);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id" class="transition duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Data Diri Anda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <style>
        .image-preview {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #e5e7eb;
        }
        #map {
            height: 400px;
            width: 100%;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            background-color: #f3f4f6;
            z-index: 1;
        }
        .leaflet-container {
            background: #f8fafc !important;
        }
        .dark .leaflet-container {
            background: #1e293b !important;
        }
        #address-loading {
            display: none;
            color: #3b82f6;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        #location-error {
            display: none;
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        textarea.keep-spaces {
            white-space: pre-wrap;
            word-break: break-word;
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
        
        .action-btn {
            transition: all 0.2s ease;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            white-space: nowrap;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
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
                    <h1 class="text-xl md:text-2xl font-bold">Edit Profil Anda</h1>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">Perbarui informasi profil Anda</p>
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
            <h2 class="text-lg font-bold text-blue-600 dark:text-blue-400 mb-3 border-b border-slate-200 dark:border-slate-700 pb-2">MENU ADMIN</h2>
            <ul class="space-y-2">
                <li>
                    <a href="beranda_admin.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">
                        <i class="fas fa-home text-slate-400"></i>
                        <span>Beranda</span>
                    </a>
                </li>
                <li>
                    <a href="data_artikel.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">
                        <i class="fas fa-newspaper text-slate-400"></i>
                        <span>Kelola Blog</span>
                    </a>
                </li>
                <li>
                    <a href="data_gallery.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">
                        <i class="fas fa-images text-slate-400"></i>
                        <span>Kelola Portofolio</span>
                    </a>
                </li>
                <li>
                    <a href="data_foto.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">
                        <i class="fas fa-camera text-slate-400"></i>
                        <span>Kelola Foto</span>
                    </a>
                </li>
                <li>
                    <a href="data_about.php" class="flex items-center gap-3 p-2 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-white font-semibold">
                        <i class="fas fa-building text-blue-500"></i>
                        <span>Kelola Profil</span>
                    </a>
                </li>
                <li>
                    <a href="logout.php" onclick="return confirm('Apakah anda yakin ingin keluar?');"
                        class="flex items-center gap-3 p-2 rounded-lg text-red-500 hover:bg-red-500/10">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </aside>

        <main class="w-full md:w-3/4 bg-white dark:bg-slate-800 rounded-lg shadow-lg p-4 md:p-6 card-3d">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div class="mb-3 md:mb-0">
                    <h2 class="text-xl md:text-2xl font-bold text-slate-800 dark:text-white">Form Edit Profil</h2>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">Perbarui informasi profil Anda</p>
                </div>
                <a href="data_about.php" class="bg-slate-600 hover:bg-slate-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 dark:bg-red-800 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-100 px-4 py-3 rounded-lg flex items-center mb-6">
                    <i class="fas fa-exclamation-circle mr-3 text-red-500 dark:text-red-300"></i>
                    <div>
                        <p class="font-medium"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Foto Profil</label>
                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <?php if (!empty($data['foto_profil'])): ?>
                            <img id="imagePreview" class="image-preview" src="../<?= htmlspecialchars($data['foto_profil']) ?>" alt="Preview">
                        <?php else: ?>
                            <div id="imagePreview" class="bg-slate-200 dark:bg-slate-700 border-2 border-dashed rounded-full w-40 h-40 flex items-center justify-center text-slate-500 dark:text-slate-400">
                                <i class="fas fa-image text-3xl"></i>
                            </div>
                        <?php endif; ?>
                        <div class="flex-grow">
                            <div class="file-input-container">
                                <label for="fotoInput" class="file-input-label dark:file-input-label dark">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-blue-500 mb-2"></i>
                                    <span class="font-medium">Klik untuk memilih gambar baru</span>
                                    <span class="text-sm text-slate-500 dark:text-slate-400 mt-1">Format: JPG, JPEG, PNG (Maks. 2MB)</span>
                                </label>
                                <input type="file" name="foto_profil" id="fotoInput">
                            </div>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Biarkan kosong jika tidak ingin mengubah foto</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label for="alamat" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Alamat
                        <span class="text-red-500">*</span>
                    </label>
                    <textarea name="alamat" id="alamat" rows="3" required
                               class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                               bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-slate-600"><?= htmlspecialchars($data['alamat']) ?></textarea>
                    <div id="address-loading" class="flex items-center mt-2">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mengambil alamat...</span>
                    </div>
                    <div id="location-error" class="flex items-center mt-2 text-red-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span class="error-message"></span>
                    </div>
                </div>
                
                <input type="hidden" id="koordinat" name="koordinat" value="<?= htmlspecialchars($koordinat) ?>">
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Pilih Lokasi di Peta</label>
                    
                    <div class="flex justify-between mb-3">
                        <button type="button" id="use-current-location" class="bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-4 rounded flex items-center">
                            <i class="fas fa-location-arrow mr-2"></i>
                            Gunakan Lokasi Saat Ini
                        </button>
                    </div>
                    
                    <div id="map"></div>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Klik pada peta atau geser marker untuk mengubah lokasi</p>
                    <p id="map-error" class="mt-2 text-sm text-red-500 hidden">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Gagal memuat peta. Silakan cek koneksi internet Anda.
                    </p>
                </div>

                <div>
                    <label for="link_maps" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Link Google Maps (URL Tautan)
                    </label>
                    <input type="url" name="link_maps" id="link_maps"
                               value="<?= htmlspecialchars($link_maps_db) ?>"
                               class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                               bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-slate-600"
                               placeholder="Contoh: https://maps.google.com/?q=...">
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                        Link ini akan diperbarui otomatis saat Anda memilih lokasi di peta atau memasukkan koordinat. Ini adalah URL untuk membuka lokasi di Google Maps.
                    </p>
                </div>
                
                <div>
                    <label for="about" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Deskripsi Perusahaan
                        <span class="text-red-500">*</span>
                    </label>
                    <textarea name="about" id="about" rows="8" required
                               class="keep-spaces w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                               bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-slate-600"><?= htmlspecialchars($data['about']) ?></textarea>
                </div>
                
                <div class="flex justify-end mt-8">
                    <button type="submit" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center justify-center">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </main>
    </div>

    <footer class="bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 py-4 mt-6">
        <div class="container mx-auto px-4 text-center text-sm">
            &copy; <?php echo date('Y'); ?> | Created by Raihan Saputra
        </div>
    </footer>

    < <script>
        // Deklarasikan variabel map dan marker di luar fungsi agar bisa diakses global
        let map;
        let marker;
        const defaultLat = parseFloat('<?= $default_lat; ?>');
        const defaultLng = parseFloat('<?= $default_lng; ?>');

        // Fungsi untuk mendapatkan alamat dari koordinat dan memperbarui semua input terkait lokasi
        async function updateLocationInputs(lat, lng) {
            // Update koordinat tersembunyi
            document.getElementById('koordinat').value = `${lat},${lng}`;

            // PERBAIKAN: Memperbarui kolom Link Google Maps dengan format yang lebih standar
            // Menggunakan format URL Google Maps yang universal untuk penanda lokasi
            // URL ini akan membuka Google Maps di koordinat yang diberikan, dengan marker.
            const newMapLink = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
            document.getElementById('link_maps').value = newMapLink;

            // Tampilkan loading indicator untuk alamat
            document.getElementById('address-loading').style.display = 'flex';
            document.getElementById('location-error').style.display = 'none';
            document.getElementById('alamat').value = 'Mencari alamat...'; // Beri feedback ke user

            try {
                // Gunakan Nominatim untuk reverse geocoding
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                const data = await response.json();
                
                if (data && data.display_name) {
                    // Isi alamat ke textarea
                    document.getElementById('alamat').value = data.display_name;
                } else {
                    document.getElementById('alamat').value = 'Alamat tidak ditemukan.';
                }
            } catch (error) {
                console.error('Error fetching address:', error);
                document.getElementById('alamat').value = 'Gagal mendapatkan alamat.';
                showLocationError('Gagal mendapatkan alamat dari koordinat.');
            } finally {
                // Sembunyikan loading indicator
                document.getElementById('address-loading').style.display = 'none';
            }
        }

        // Fungsi untuk mendapatkan lokasi pengguna saat ini
        function getCurrentLocation() {
            if (!navigator.geolocation) {
                showLocationError('Geolocation tidak didukung di browser Anda.');
                return;
            }
            
            document.getElementById('location-error').style.display = 'none';
            document.getElementById('alamat').value = 'Mendapatkan lokasi saat ini...'; // Feedback

            navigator.geolocation.getCurrentPosition(
                position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    // Pindahkan marker ke lokasi baru
                    if (marker) {
                        map.removeLayer(marker);
                    }
                    marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                    map.setView([lat, lng], 15);
                    
                    updateLocationInputs(lat, lng); // Panggil fungsi perbaikan

                    // Tambahkan event marker baru setelah dibuat
                    marker.on('dragend', function(e) {
                        const newPos = marker.getLatLng();
                        updateLocationInputs(newPos.lat, newPos.lng); // Panggil fungsi perbaikan
                    });
                },
                error => {
                    let errorMessage;
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = "Pengguna menolak permintaan geolocation.";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = "Informasi lokasi tidak tersedia.";
                            break;
                        case error.TIMEOUT:
                            errorMessage = "Permintaan lokasi habis waktu.";
                            break;
                        default:
                            errorMessage = "Terjadi kesalahan yang tidak diketahui.";
                    }
                    showLocationError(errorMessage);
                    document.getElementById('alamat').value = 'Gagal mendapatkan lokasi.'; // Reset alamat
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 } // Opsi untuk akurasi lebih baik dan timeout
            );
        }
        
        // Fungsi untuk menampilkan pesan error lokasi
        function showLocationError(message) {
            const errorElement = document.getElementById('location-error');
            errorElement.querySelector('.error-message').textContent = message;
            errorElement.style.display = 'flex';
        }

        // Inisialisasi peta dengan Leaflet
        function initMap() {
            try {
                map = L.map('map').setView([defaultLat, defaultLng], 15);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                
                marker = L.marker([defaultLat, defaultLng], {
                    draggable: true
                }).addTo(map);
                
                // Event untuk marker yang digerakkan
                marker.on('dragend', function(event) {
                    const position = marker.getLatLng();
                    updateLocationInputs(position.lat, position.lng); // Panggil fungsi perbaikan
                });
                
                // Event untuk klik peta
                map.on('click', function(e) {
                    const position = e.latlng;
                    
                    if (marker) {
                        map.removeLayer(marker);
                    }
                    
                    marker = L.marker(position, {
                        draggable: true
                    }).addTo(map);
                    
                    updateLocationInputs(position.lat, position.lng); // Panggil fungsi perbaikan
                    
                    // Event untuk marker baru setelah dibuat
                    marker.on('dragend', function(e) {
                        const newPosition = marker.getLatLng();
                        updateLocationInputs(newPosition.lat, newPosition.lng); // Panggil fungsi perbaikan
                    });
                });
                
            } catch (error) {
                console.error("Map initialization error:", error);
                document.getElementById("map-error").classList.remove("hidden");
                
                // Tampilkan input manual jika peta gagal dimuat
                const mapContainer = document.getElementById("map");
                mapContainer.innerHTML = `
                    <div class="bg-yellow-100 dark:bg-yellow-900 border-l-4 border-yellow-400 dark:border-yellow-600 p-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-500 dark:text-yellow-300 mr-3 mt-1"></i>
                            <div>
                                <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                    Peta tidak dapat dimuat. Silakan masukkan koordinat secara manual:
                                </p>
                                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Latitude</label>
                                        <input type="text" name="latitude_manual" value="<?= htmlspecialchars($default_lat) ?>" required 
                                            class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-gray-100"
                                            onchange="document.getElementById('koordinat').value = this.value + ',' + document.getElementById('longitude_manual').value; updateLocationInputs(parseFloat(this.value), parseFloat(document.getElementById('longitude_manual').value));">
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Longitude</label>
                                        <input type="text" name="longitude_manual" id="longitude_manual" value="<?= htmlspecialchars($default_lng) ?>" required 
                                            class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-gray-100"
                                            onchange="document.getElementById('koordinat').value = document.getElementById('latitude_manual').value + ',' + this.value; updateLocationInputs(parseFloat(document.getElementById('latitude_manual').value), parseFloat(this.value));">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                // Jika peta gagal, atur nilai awal link_maps berdasarkan default_lat, default_lng
                updateLocationInputs(defaultLat, defaultLng);
            }
        }

        // Panggil initMap saat DOM siap
        document.addEventListener('DOMContentLoaded', function() {
            initMap();

            // Event listener untuk tombol "Gunakan Lokasi Saat Ini"
            document.getElementById('use-current-location').addEventListener('click', getCurrentLocation);

            // Preview image sebelum upload
            const fotoInput = document.getElementById('fotoInput');
            const imagePreview = document.getElementById('imagePreview');
            const originalImageUrl = "<?= !empty($data['foto_profil']) ? htmlspecialchars('../' . $data['foto_profil']) : '' ?>"; // Simpan URL asli

            if (fotoInput) {
                fotoInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            if (imagePreview.tagName === 'IMG') {
                                imagePreview.src = e.target.result;
                            } else {
                                const newImg = document.createElement('img');
                                newImg.id = 'imagePreview';
                                newImg.className = 'image-preview';
                                newImg.src = e.target.result;
                                imagePreview.replaceWith(newImg);
                            }
                        }
                        reader.readAsDataURL(file);
                    } else {
                        // Jika file dihapus dari input, kembalikan ke gambar lama atau placeholder
                        if (originalImageUrl) {
                            if (imagePreview.tagName === 'IMG') {
                                imagePreview.src = originalImageUrl;
                            } else {
                                // Jika awalnya div placeholder dan ada original image, ganti jadi img
                                const newImg = document.createElement('img');
                                newImg.id = 'imagePreview';
                                newImg.className = 'image-preview';
                                newImg.src = originalImageUrl;
                                imagePreview.replaceWith(newImg);
                            }
                        } else {
                            // Kembali ke placeholder div jika tidak ada gambar lama
                            if (imagePreview.tagName === 'IMG') {
                                const newDiv = document.createElement('div');
                                newDiv.id = 'imagePreview';
                                newDiv.className = 'bg-gray-200 dark:bg-gray-700 border-2 border-dashed rounded-full w-40 h-40 flex items-center justify-center text-gray-500 dark:text-gray-400';
                                newDiv.innerHTML = '<i class="fas fa-image text-3xl"></i>';
                                imagePreview.replaceWith(newDiv);
                            }
                        }
                    }
                });
            }

            // Dark Mode Toggle
            const darkToggle = document.getElementById('darkToggle');
            const html = document.documentElement;

            // Load saved preference or default to system preference
            const prefersDarkMode = localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (prefersDarkMode) {
                html.classList.add('dark');
                darkToggle.textContent = '☀️';
            } else {
                html.classList.remove('dark');
                darkToggle.textContent = '🌙';
            }

            darkToggle.addEventListener('click', () => {
                if (html.classList.contains('dark')) {
                    html.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                    darkToggle.textContent = '🌙';
                } else {
                    html.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                    darkToggle.textContent = '☀️';
                }
            });
        });
    </script>
</body>
</html>