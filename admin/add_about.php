<?php
include('../koneksi.php');
session_start();
if (!isset($_SESSION['username'])) {
    header('location:login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id" class="transition duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
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
        
        .location-btn {
            margin-top: 10px;
            display: flex;
            gap: 10px;
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
        
        textarea.keep-spaces {
            white-space: pre-wrap;
            word-break: break-word;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            display: none;
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
                    <h1 class="text-xl md:text-2xl font-bold">Tambah Data Profil</h1>
                    <p class="text-gray-600 dark:text-slate-400 text-sm">Tambahkan informasi profil perusahaan</p>
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
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar w-full md:w-1/4 bg-white dark:bg-slate-800 rounded-lg shadow-lg p-4 mb-4 md:mb-0 md:mr-6 card-3d">
            <h2 class="text-lg font-bold text-blue-600 dark:text-blue-400 mb-3 border-b border-slate-200 dark:border-slate-700 pb-2">NAVIGATION</h2>
            <ul class="space-y-2">
                <li><a href="beranda_admin.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><i class="fas fa-home text-slate-400"></i><span>Dashboard</span></a></li>
                <li><a href="data_artikel.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><i class="fas fa-newspaper text-slate-400"></i><span>Kelola Blog</span></a></li>
                <li><a href="data_gallery.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><i class="fas fa-images text-slate-400"></i><span>Kelola Portofolio</span></a></li>
                <li><a href="data_foto.php" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700"><i class="fas fa-camera text-slate-400"></i><span>Kelola Foto</span></a></li>
                <li><a href="data_about.php" class="flex items-center gap-3 p-2 rounded-lg bg-blue-500/10 dark:bg-blue-500/20 text-blue-600 dark:text-white font-semibold"><i class="fas fa-building text-blue-500"></i><span>Kelola Profil</span></a></li>
                <li><a href="logout.php" onclick="return confirm('Apakah anda yakin ingin keluar?');" class="flex items-center gap-3 p-2 rounded-lg text-red-500 hover:bg-red-500/10"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
            </ul>
        </aside>

        <main class="w-full md:w-3/4">
            <!-- Header dan Tombol Kembali -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div class="mb-3 md:mb-0">
                    <h2 class="text-xl md:text-2xl font-bold text-slate-800 dark:text-white">Form Tambah Profil Perusahaan</h2>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">Isi detail profil perusahaan</p>
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

            <!-- Form Container -->
            <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg overflow-hidden card-3d p-6">
                <form action="proses_add_about.php" method="post" enctype="multipart/form-data" class="space-y-6" onsubmit="return validateForm()">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Foto Profil
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="file-input-container">
                            <label for="foto-profil-input" class="file-input-label dark:file-input-label dark">
                                <i class="fas fa-cloud-upload-alt text-3xl text-blue-500 mb-2"></i>
                                <span class="font-medium">Klik untuk mengunggah foto</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 mt-1">Format: JPG, PNG, JPEG. Maks: 2MB</span>
                            </label>
                            <input type="file" name="foto_profil" id="foto-profil-input" required class="file-upload-input" accept="image/*">
                        </div>
                        <p id="foto-error" class="error-message">Mohon unggah foto profil.</p>
                    </div>
                    
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Alamat
                            <span class="text-red-500">*</span>
                        </label>
                        <textarea id="alamat" name="alamat" rows="3" required
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                                bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 border-slate-300 dark:border-slate-600"></textarea>
                        <p id="alamat-error" class="error-message">Mohon masukkan alamat.</p>
                        <div id="address-loading" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Mengambil alamat...</span>
                        </div>
                    </div>
                    
                    <input type="hidden" id="koordinat" name="koordinat" required>
                    <p id="koordinat-error" class="error-message">Mohon pilih lokasi di peta.</p>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Pilih Lokasi di Peta
                        </label>
                        <div id="map"></div>
                        <div class="location-btn">
                            <button type="button" id="track-location" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                                <i class="fas fa-location-arrow mr-2"></i> Gunakan Lokasi Saya Sekarang
                            </button>
                            <button type="button" id="reset-location" class="bg-slate-500 hover:bg-slate-600 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                                <i class="fas fa-sync-alt mr-2"></i> Reset Lokasi
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Klik pada peta untuk menentukan lokasi</p>
                        <p id="map-error" class="text-red-500 text-sm hidden">Gagal memuat peta. Silakan cek koneksi internet Anda.</p>
                        <p id="location-error" class="text-red-500 text-sm hidden"></p>
                    </div>
                    
                    <div>
                        <label for="link_maps" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Link Google Maps
                        </label>
                        <input type="url" id="link_maps" name="link_maps" 
                               class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                                   bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 border-slate-300 dark:border-slate-600" 
                               placeholder="Contoh: http://maps.google.com/?q=-6.2088,106.8456">
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            Link ini akan diperbarui otomatis saat Anda memilih lokasi di peta atau memasukkan koordinat.
                        </p>
                    </div>
                    
                    <div>
                        <label for="about" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Deskripsi Tentang Perusahaan
                            <span class="text-red-500">*</span>
                        </label>
                        <textarea id="about" name="about" rows="5" required
                            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                                bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 border-slate-300 dark:border-slate-600 keep-spaces"></textarea>
                        <p id="about-error" class="error-message">Mohon masukkan deskripsi perusahaan.</p>
                    </div>
                    
                    <div class="flex justify-end space-x-4 pt-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fas fa-save mr-2"></i> Simpan Data
                        </button>
                        <a href="data_about.php" class="bg-slate-500 hover:bg-slate-600 text-white px-6 py-3 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fas fa-times mr-2"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <footer class="bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 py-4 mt-6">
        <div class="container mx-auto px-4 text-center text-sm">
            &copy; <?php echo date('Y'); ?> | Created by Raihan Saputra
        </div>
    </footer>

    <script>
    // --- Leaflet Map Variables & Functions ---
    let map, marker;
    const defaultLat = -6.2088; // Default to Jakarta
    const defaultLng = 106.8456; // Default to Jakarta

    async function getAddressFromCoordinates(lat, lng) {
        document.getElementById('address-loading').style.display = 'flex';
        document.getElementById('alamat').value = 'Mencari alamat...';
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
            const data = await response.json();
            
            if (data && data.display_name) {
                document.getElementById('alamat').value = data.display_name;
            } else {
                document.getElementById('alamat').value = 'Alamat tidak ditemukan.';
            }
        } catch (error) {
            console.error('Error fetching address:', error);
            document.getElementById('alamat').value = 'Gagal mendapatkan alamat.';
        } finally {
            document.getElementById('address-loading').style.display = 'none';
        }
    }
    
    function generateGoogleMapsLink(lat, lng) {
        return `http://maps.google.com/?q=${lat},${lng}`;
    }
    
    function setLocationOnMap(lat, lng) {
        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        map.setView([lat, lng], 15);
        
        document.getElementById("koordinat").value = `${lat},${lng}`;
        document.getElementById("link_maps").value = generateGoogleMapsLink(lat, lng);
        
        getAddressFromCoordinates(lat, lng);
        
        marker.on('dragend', function(event) {
            const newPos = marker.getLatLng();
            setLocationOnMap(newPos.lat, newPos.lng);
        });
    }

    function initMap() {
        try {
            map = L.map('map').setView([defaultLat, defaultLng], 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Set initial location on map
            setLocationOnMap(defaultLat, defaultLng);
            
            map.on('click', function(e) {
                setLocationOnMap(e.latlng.lat, e.latlng.lng);
            });
            
            document.getElementById('track-location').addEventListener('click', function() {
                if (navigator.geolocation) {
                    document.getElementById('location-error').classList.add('hidden');
                    document.getElementById('alamat').value = 'Mendapatkan lokasi saat ini...';
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            setLocationOnMap(position.coords.latitude, position.coords.longitude);
                        },
                        function(error) {
                            let errorMessage = "Tidak dapat mendapatkan lokasi: ";
                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    errorMessage += "Izin ditolak oleh pengguna.";
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    errorMessage += "Informasi lokasi tidak tersedia.";
                                    break;
                                case error.TIMEOUT:
                                    errorMessage += "Permintaan lokasi habis waktunya.";
                                    break;
                                default:
                                    errorMessage += "Terjadi kesalahan tidak dikenal.";
                            }
                            document.getElementById('location-error').textContent = errorMessage;
                            document.getElementById('location-error').classList.remove('hidden');
                            document.getElementById('alamat').value = 'Gagal mendapatkan lokasi.';
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                } else {
                    document.getElementById('location-error').textContent = "Geolocation tidak didukung di browser Anda.";
                    document.getElementById('location-error').classList.remove('hidden');
                }
            });

            document.getElementById('reset-location').addEventListener('click', function() {
                setLocationOnMap(defaultLat, defaultLng);
                document.getElementById('location-error').classList.add('hidden');
            });

        } catch (error) {
            console.error("Map initialization error:", error);
            document.getElementById("map-error").classList.remove("hidden");
            // Fallback for map if it fails to load
            document.getElementById("map").innerHTML = `
                <div class="bg-yellow-100 dark:bg-yellow-900 border-l-4 border-yellow-400 dark:border-yellow-600 p-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-500 dark:text-yellow-300 mr-3 mt-1"></i>
                        <div>
                            <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                Peta tidak dapat dimuat. Silakan masukkan koordinat secara manual atau cek koneksi internet Anda.
                            </p>
                            <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Latitude</label>
                                    <input type="text" id="manual_latitude" value="${defaultLat}" required 
                                        class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-gray-100"
                                        onchange="updateManualCoordinates()">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-1">Longitude</label>
                                    <input type="text" id="manual_longitude" value="${defaultLng}" required 
                                        class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-gray-100"
                                        onchange="updateManualCoordinates()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            // Initialize hidden coordinate and link_maps with defaults if map fails
            document.getElementById("koordinat").value = `${defaultLat},${defaultLng}`;
            document.getElementById("link_maps").value = generateGoogleMapsLink(defaultLat, defaultLng);
            getAddressFromCoordinates(defaultLat, defaultLng);
        }
    }

    function updateManualCoordinates() {
        const lat = parseFloat(document.getElementById('manual_latitude').value);
        const lng = parseFloat(document.getElementById('manual_longitude').value);
        if (!isNaN(lat) && !isNaN(lng)) {
            document.getElementById("koordinat").value = `${lat},${lng}`;
            document.getElementById("link_maps").value = generateGoogleMapsLink(lat, lng);
            getAddressFromCoordinates(lat, lng);
        }
    }

    // --- Form Validation (Client-Side) ---
    function validateForm() {
        let isValid = true;

        // Validate Foto Profil
        const fotoProfilInput = document.getElementById('foto-profil-input');
        const fotoError = document.getElementById('foto-error');
        if (!fotoProfilInput.files.length > 0) {
            fotoError.style.display = 'block';
            isValid = false;
        } else {
            fotoError.style.display = 'none';
        }

        // Validate Alamat
        const alamatInput = document.getElementById('alamat');
        const alamatError = document.getElementById('alamat-error');
        if (alamatInput.value.trim() === '') {
            alamatError.style.display = 'block';
            isValid = false;
        } else {
            alamatError.style.display = 'none';
        }

        // Validate Koordinat
        const koordinatInput = document.getElementById('koordinat');
        const koordinatError = document.getElementById('koordinat-error');
        if (koordinatInput.value.trim() === '' || koordinatInput.value.split(',').length !== 2) {
            koordinatError.style.display = 'block';
            isValid = false;
        } else {
            koordinatError.style.display = 'none';
        }

        // Validate Deskripsi
        const aboutInput = document.getElementById('about');
        const aboutError = document.getElementById('about-error');
        if (aboutInput.value.trim() === '') {
            aboutError.style.display = 'block';
            isValid = false;
        } else {
            aboutError.style.display = 'none';
        }
        
        return isValid;
    }

    // --- Dark Mode Toggle ---
    document.addEventListener('DOMContentLoaded', function() {
        initMap(); // Initialize map when DOM is ready

        const darkToggle = document.getElementById('darkToggle');
        const html = document.documentElement;

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
    });
    </script>
</body>
</html>