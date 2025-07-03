<?php 
include "koneksi.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!$db) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Tambahkan baris ini untuk mendeteksi halaman aktif
$current_page = basename($_SERVER['PHP_SELF']);

/**
 * Memformat teks deskripsi untuk tampilan HTML.
 * - Mengatasi masalah backslash ganda yang salah.
 * - Menambahkan tag <p> untuk setiap baris, memberikan spasi ke bawah.
 * - Melakukan sanitasi HTML dasar.
 * @param string $text Teks deskripsi dari database.
 * @return string Teks deskripsi yang sudah diformat HTML.
 */
function formatDescription($text) {
    // 1. Membersihkan backslash yang tidak perlu atau ganda yang meng-escape newline
    // Mengubah '\\r\\n' menjadi '\r\n', '\\n' menjadi '\n'
    $cleaned_text = str_replace(array('\\r\\n', '\\n', '\\r'), array("\r\n", "\n", "\r"), $text);
    
    // Hapus backslash tunggal yang mungkin tersisa atau tidak diinginkan secara global
    // PERHATIAN: Ini akan menghapus SEMUA backslash.
    // Jika Anda memiliki kasus di mana backslash harus ada (misal: C:\path),
    // maka logic ini perlu disesuaikan atau masalah di input/penyimpanan harus diatasi.
    $cleaned_text = str_replace('\\', '', $cleaned_text); 

    // 2. Mengubah karakter khusus HTML menjadi entitas HTML untuk keamanan
    $safe_text = htmlspecialchars($cleaned_text, ENT_QUOTES, 'UTF-8');
    
    // 3. Memisahkan berdasarkan baris baru dan membungkus setiap paragraf dengan tag <p>
    $paragraphs = explode("\n", $safe_text); // Gunakan \n karena \r\n sudah distandarisasi
    $formatted = '';
    
    foreach ($paragraphs as $para) {
        if (trim($para) != '') { // Pastikan paragraf tidak kosong
            $formatted .= '<p class="deskripsi-paragraf">' . trim($para) . '</p>';
        }
    }
    
    // Jika tidak ada paragraf yang valid (misal: hanya spasi kosong), kembalikan teks asli (atau kosong)
    if (empty($formatted) && !empty(trim($safe_text))) {
        $formatted = '<p class="deskripsi-paragraf">' . trim($safe_text) . '</p>';
    } elseif (empty($formatted)) {
        return ''; // Mengembalikan string kosong jika input benar-benar kosong atau hanya spasi
    }

    return $formatted;
}

$sqlAll = "SELECT * FROM tbl_about ORDER BY id_about ASC";
$queryAll = mysqli_query($db, $sqlAll);

$allAboutData = [];
while ($row = mysqli_fetch_assoc($queryAll)) {
    $allAboutData[] = $row;
}

$profile = [];
if (!empty($allAboutData)) {
    $profile = end($allAboutData); // Mengambil data profil terakhir yang ditambahkan
}

// Data default jika tidak ada profil di database
if (empty($profile)) {
    $profile = [
        'foto_profil' => 'images/profil.jpg',
        'alamat' => 'Jl. Permata Indah No. 123, Bandung, Jawa Barat',
        'link_maps' => 'http://maps.google.com/maps?q=-6.2088,106.8456', // Contoh link yang lebih realistis
        'koordinat' => '-6.2088,106.8456',
        'about' => 'Selamat datang di halaman tentang saya. Ini adalah teks deskripsi default. Silakan tambahkan informasi tentang diri Anda melalui panel admin untuk melihat pembaruan di sini. Setiap paragraf akan memiliki spasi yang jelas di bawahnya.'
    ];
}

$koordinat = $profile['koordinat'] ?? '-6.2088,106.8456';
$coords = explode(',', $koordinat);
$lat = $coords[0] ?? -6.2088;
$lng = $coords[1] ?? 106.8456;
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <title>About Us</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        :root {
            --primary: #ef4444;
            --secondary: #60a5fa;
            --dark: #1a1a1a;
            --light: #f0f0f0;
            --gray: #333;
        }
        
        body {
            font-family: 'Playfair Display', serif;
            margin: 0;
            height: 100vh;
            overflow: hidden;
            background-color: var(--dark);
            color: var(--light);
        }
        
        .snap-container {
            height: 100vh;
            overflow-y: scroll;
            scroll-snap-type: y mandatory;
            scroll-behavior: smooth;
        }
        
        section {
            height: 100vh;
            scroll-snap-align: start;
            position: relative;
            overflow: hidden;
        }
        
        /* Animasi Fade In */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.8s cubic-bezier(0.215, 0.61, 0.355, 1);
        }
        
        .fade-in.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Animasi Fade Out */
        .fade-out {
            opacity: 1;
            transition: all 0.6s cubic-bezier(0.55, 0.085, 0.68, 0.53);
        }
        
        .fade-out.hide {
            opacity: 0;
            transform: translateY(-20px);
        }
        
        /* Animasi Scale In */
        .scale-in {
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .scale-in.show {
            transform: scale(1);
            opacity: 1;
        }
        
        /* Animasi Slide In */
        .slide-in-left {
            transform: translateX(-50px);
            opacity: 0;
            transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .slide-in-left.show {
            transform: translateX(0);
            opacity: 1;
        }
        
        .slide-in-right {
            transform: translateX(50px);
            opacity: 0;
            transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .slide-in-right.show {
            transform: translateX(0);
            opacity: 1;
        }
        
        /* Animasi untuk konten utama */
        .content-section {
            background: transparent;
            padding: 2.5rem;
            width: 90%;
            max-width: 1000px;
            margin: 2rem auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .profile-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2rem;
            width: 100%;
        }
        
        .profile-image {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid rgba(239, 68, 68, 0.3);
            background-color: var(--gray);
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .profile-image:hover {
            transform: scale(1.05);
            border-color: rgba(239, 68, 68, 0.6);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            width: 100%;
            margin-top: 2rem;
        }
        
        @media (min-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        .address-container {
            padding: 1.5rem;
            height: 100%;
            transition: all 0.5s ease;
        }
        
        .map-container {
            height: 100%;
            border-radius: 0.75rem;
            overflow: hidden;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.8s ease 0.3s;
        }
        
        .map-container.show {
            transform: translateY(0);
            opacity: 1;
        }
        
        .map-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-right: 1.5rem;
            flex-shrink: 0;
        }
        
        .address-details h3 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #f3f4f6;
        }
        
        .address-details p {
            color: #d1d5db;
            line-height: 1.6;
        }
        
        .map-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(96, 165, 250, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            color: #93c5fd;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        .map-link:hover {
            background: rgba(96, 165, 250, 0.3);
            color: #bfdbfe;
            transform: translateY(-2px);
        }
        
        .about-content {
            width: 100%;
            margin-top: 3rem;
        }

        .deskripsi-container {
            padding: 1.5rem;
            width: 100%;
        }
        
        .deskripsi-item {
            padding: 1.5rem 0;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }
        
        .deskripsi-item.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .deskripsi-paragraf {
            text-align: justify;
            line-height: 1.6;
            color: #d1d5db;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }
        
        .deskripsi-paragraf:last-child {
            margin-bottom: 0;
        }

        .deskripsi-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #f3f4f6;
            display: flex;
            align-items: center;
        }
        
        .deskripsi-title i {
            margin-right: 10px;
            color: var(--primary);
            background: rgba(239, 68, 68, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #map {
            height: 100%;
            width: 100%;
            border-radius: 0.75rem;
            background-color: var(--gray);
        }

        .content-overlay {
            padding: 2rem;
            width: 100%;
        }

        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 2.5rem;
            font-size: 2.2rem;
            text-align: center;
            width: 100%;
            font-weight: 700;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }
        
        .section-title.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--primary);
            border-radius: 2px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 0.8rem 1.8rem;
            border-radius: 50px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: 'Playfair Display', serif;
        }
        
        .no-data-message {
            padding: 2rem;
            text-align: center;
            font-family: 'Playfair Display', serif;
            opacity: 0;
            transform: scale(0.9);
            transition: all 0.6s ease;
        }
        
        .no-data-message.show {
            opacity: 1;
            transform: scale(1);
        }
        
        .no-data-message i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        /* --- Tambahan CSS untuk Dropdown & Active State --- */
        .dropdown:hover .dropdown-content {
            display: block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: rgba(0,0,0,0.8);
            min-width: 160px;
            z-index: 1;
            border-radius: 0.5rem;
            overflow: hidden;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
        }
        .dropdown-item {
            color: white;
            padding: 0.75rem 1rem;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        .dropdown-item:hover {
            background-color: rgba(255,255,255,0.1);
        }
        .active {
            position: relative;
        }
        .active::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: #a78bfa;
        }

        @media (max-width: 768px) {
            .content-section {
                padding: 1.5rem;
                margin: 1rem auto;
            }
            
            .profile-image {
                width: 160px;
                height: 160px;
            }
            
            .address-container {
                padding: 1.2rem;
            }
            
            .deskripsi-item {
                padding: 1rem 0;
            }
            
            .deskripsi-title {
                font-size: 1.4rem;
            }
            
            .deskripsi-paragraf {
                font-size: 1rem;
            }
            
            .content-overlay {
                padding: 1rem;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body class="bg-zinc-900 text-white">

<div class="snap-container">
    <section class="relative">
        <img src="img/Rinaschita.jpg" alt="Background" class="absolute inset-0 w-full h-full object-cover object-center opacity-80" />
        <div class="absolute inset-0 bg-gradient-to-br from-black/70 to-transparent"></div>
        
        <header class="absolute top-0 left-0 w-full z-20 py-4 px-4 md:px-8">
            <nav class="flex justify-center">
                <ul class="flex flex-wrap justify-center gap-4 md:gap-8 text-lg font-bold">
                    <li class="dropdown relative">
                        <a href="#" class="dropdown-toggle <?= in_array($current_page, ['index.php', 'foto.php']) ? 'active' : '' ?>">Blog</a>
                        <ul class="dropdown-content">
                            <li><a href="index.php" class="dropdown-item <?= $current_page == 'index.php' ? 'font-bold text-purple-400' : '' ?>">Artikel</a></li>
                            <li><a href="foto.php" class="dropdown-item <?= $current_page == 'foto.php' ? 'font-bold text-purple-400' : '' ?>">Galerry</a></li>
                        </ul>
                    </li>
                    <li><a href="gallery.php" class="<?= $current_page == 'gallery.php' ? 'active' : '' ?>">Project</a></li>
                    <li><a href="about.php" class="<?= $current_page == 'about.php' ? 'active' : '' ?>">About</a></li>
                    <li><a href="admin/login.php" class="<?= $current_page == 'login.php' ? 'active' : '' ?>">Login</a></li>
                </ul>
            </nav>
        </header>
        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-6">
            <p class="fade-in uppercase tracking-widest text-sm font-bold text-neutral-300">Welcome!</p>
            <h1 class="fade-in text-4xl md:text-6xl font-bold leading-tight mt-3" style="transition-delay: 0.2s">About Us<br>Raihan Saputra</h1>
            <a href="#konten" class="fade-in inline-block mt-6 text-sm tracking-widest uppercase hover:underline" style="transition-delay: 0.4s">Lihat Tentang Saya ↓</a>
        </div>
    </section>

    <section id="konten" class="relative" style="background-image: url('img/Silver.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="absolute inset-0 bg-black/60 z-0"></div>
        <div class="relative z-10 w-full h-full flex flex-col items-center py-10 px-4 overflow-y-auto">
            <div class="content-section scale-in content-overlay">
                <div class="profile-container slide-in-left">
                    <?php 
                    $foto_path = 'images/profil.jpg';
                    
                    if (!empty($profile['foto_profil'])) {
                        $possible_paths = [
                            $profile['foto_profil'],
                            'admin/' . $profile['foto_profil'],
                            '../' . $profile['foto_profil'],
                            'uploads/about/' . basename($profile['foto_profil']) // Path standar baru
                        ];
                        
                        $found = false;
                        foreach ($possible_paths as $path) {
                            // Gunakan realpath untuk memeriksa keberadaan file secara lebih andal
                            if (file_exists($path) && !is_dir($path)) { // Pastikan itu file, bukan direktori
                                $foto_path = $path;
                                $found = true;
                                break;
                            }
                        }
                        
                        if (!$found) {
                            // Opsional: Log error atau tampilkan pesan debug
                            // echo '<div class="text-red-500">Gambar tidak ditemukan: ' . htmlspecialchars($profile['foto_profil']) . '</div>';
                        }
                    }
                    ?>
                    
                    <img src="<?= htmlspecialchars($foto_path) ?>" 
                         alt="Foto Profil" 
                         class="profile-image">
                    
                    <div class="mt-6 text-center">
                        <h2 class="text-3xl font-bold">Raihan Saputra</h2>
                        <p class="text-gray-300 mt-2">Mahasiswa & Web Developer</p>
                    </div>
                </div>
                
                <div class="about-content mt-8">
                    <h2 class="section-title">Tentang Saya</h2>
                    
                    <div class="deskripsi-container">
                        <?php if (!empty($allAboutData)): ?>
                            <?php foreach ($allAboutData as $index => $data): ?>
                                <div class='deskripsi-item' style="transition-delay: <?= $index * 0.1 + 0.3 ?>s">
                                    <div class='deskripsi-title'>
                                        <i class='fas fa-user'></i>
                                        <span>Deskripsi #<?= $index + 1 ?></span>
                                    </div>
                                    
                                    <?php if (!empty($data['about'])): ?>
                                        <div class="deskripsi-text">
                                            <?= formatDescription($data['about']) ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-gray-400 italic mt-2">Deskripsi belum ditambahkan</p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class='no-data-message'>
                                <i class='fas fa-info-circle'></i>
                                <h3 class="text-xl font-bold mb-2">Belum Ada Data</h3>
                                <p>Belum ada data tentang toko yang ditambahkan. Menggunakan data default.</p>
                                <div class="mt-4">
                                    <h4 class="deskripsi-title"><i class='fas fa-info'></i> Deskripsi Default</h4>
                                    <div class="deskripsi-text">
                                        <?= formatDescription($profile['about']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="info-grid">
                    <div class="address-container slide-in-left">
                        <div class="flex items-start">
                            <div class="map-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="address-details">
                                <h3>Alamat Kami</h3>
                                <p>
                                    <?= !empty($profile['alamat']) 
                                        ? htmlspecialchars($profile['alamat']) 
                                        : 'Alamat belum ditambahkan' ?>
                                </p>
                                
                                <?php if (!empty($profile['link_maps'])): ?>
                                    <a href="<?= htmlspecialchars($profile['link_maps']) ?>" 
                                       target="_blank" 
                                       class="map-link">
                                        <i class="fas fa-map-marked-alt"></i> Lihat di Google Maps
                                    </a>
                                <?php else: ?>
                                    <p class="text-gray-400 italic mt-3">Link Google Maps belum ditambahkan</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="map-container">
                        <div id="map"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
     <section class="footer-section bg-cover bg-center bg-no-repeat relative text-white flex flex-col items-center justify-center text-center px-4"
        style="background-image: url('img/Gen2.jpg');">
        <div class="absolute inset-0 overlay-footer"></div>
        <div class="relative z-10">
            <h2 class="fade-in-element text-3xl md:text-4xl font-bold mb-4">Terima kasih telah berkunjung!</h2>
            <p class="fade-in-element text-neutral-300 max-w-xl mb-8 text-lg">
                Jangan lupa cek artikel dan galeri lainnya. Semoga harimu menyenangkan!
            </p>

            <div class="fade-in-element flex space-x-6 justify-center mb-10">
                <a href="https://www.instagram.com/hannsoza?igsh=bWdxazZxNzB5Zjdz" target="_blank"
                    class="social-icon-link instagram text-neutral-300 transition" title="Kunjungi Instagram kami">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 2 .3 2.5.5.6.2 1 .5 1.5 1s.8.9 1 1.5c.2.5.4 1.3.5 2.5.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.3 2-.5 2.5-.2.6-.5 1-.9 1.5-.5.5-.9.8-1.5 1s-1.3.4-2.5.5c-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-2-.3-2.5-.5-.6-.2-1-.5-1.5-1s-.8-.9-1-1.5c-.2-.5-.4-1.3-.5-2.5C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c.1-1.2.3-2 .5-2.5.2-.6.5-1 .9-1.5.5-.5.9-.8 1.5-1 .5-.2 1.3-.4 2.5-.5C8.4 2.2 8.8 2.2 12 2.2zm0 1.8c-3.1 0-3.5 0-4.7.1-1 .1-1.6.2-2 .4-.5.2-.8.4-1.2.8s-.6.7-.8 1.2c-.1.4-.3 1-.4 2-.1 1.2-.1 1.6-.1 4.7s0 3.5.1 4.7c.1 1 .2 1.6.4 2 .2.5.4.8.8 1.2.4.4.7.6 1.2.8.4.1 1 .3 2 .4 1.2.1 1.6.1 4.7.1s3.5 0 4.7-.1c1-.1 1.6-.2 2-.4.5-.2.8-.4 1.2-.8.4-.4.6-.7.8-1.2.1-.4.3-1 .4-2 .1-1.2.1-1.6.1-4.7s0-3.5-.1-4.7c-.1-1-.2-1.6-.4-2-.2-.5-.4-.8-.8-1.2-.4-.4-.7-.6-1.2-.8-.4-.1-1-.3-2-.4-1.2-.1-1.6-.1-4.7-.1zM12 5.8a6.2 6.2 0 1 0 0 12.4 6.2 6.2 0 0 0 0-12.4zm0 10.2a4 4 0 1 1 0-8.1 4 4 0 0 1 0 8.1zm5.5-10.9a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z"/></svg>
                </a>
                <a href="https://youtube.com/@raihansaputra6283?si=PQiT9YWRmcoayTKg" target="_blank"
                    class="social-icon-link youtube text-neutral-300 transition" title="Kunjungi Channel YouTube kami">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M21.8 8.001s-.2-1.4-.8-2c-.7-.8-1.6-.8-2-1C16.1 4.5 12 4.5 12 4.5s-4.1 0-6.9.5c-.5.2-1.3.2-2 1-.6.6-.8 2-.8 2S2 9.6 2 11.2v1.6c0 1.6.2 3.2.2 3.2s.2 1.4.8 2c.7.8 1.6.8 2 1 2.8.5 6.9.5 6.9.5s4.1 0 6.9-.5c.5-.2 1.3-.2 2-1 .6-.6.8-2 .8-2s.2-1.6.2-3.2v-1.6c0-1.6-.2-3.2-.2-3.2zM9.8 14.8V9.2l5.5 2.8-5.5 2.8z"/></svg>
                </a>
                <a href="https://www.tiktok.com/@vgrace1_?_t=ZS-8xB5x3suWbU&_r=1" target="_blank"
                    class="social-icon-link tiktok text-neutral-300 transition" title="Kunjungi TikTok kami">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M9.5 3c0-.6.4-1 1-1h2c.6 0 1 .4 1 1 0 1.7 1.3 3 3 3 .6 0 1 .4 1 1v1c0 .6-.4 1-1 1-1.1 0-2.1-.3-3-1v6.1c0 2.8-2.2 5-5 5s-5-2.2-5-5 2.2-5 5-5c.3 0 .7 0 1 .1v2.1c-.3-.1-.7-.1-1-.1-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3V3z"/></svg>
                </a>
                <a href="https://www.facebook.com/share/18zi9EpJ6f/" target="_blank"
                    class="social-icon-link facebook text-neutral-300 transition" title="Kunjungi Facebook kami">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-7 h-7" viewBox="0 0 24 24"><path d="M22 12.073C22 6.504 17.523 2 12 2S2 6.504 2 12.073C2 17.065 5.656 21.128 10.438 21.878v-6.41H7.898v-2.573h2.54V10.41c0-2.507 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.462h-1.26c-1.243 0-1.63.775-1.63 1.568v1.88h2.773l-.443 2.573h-2.33v6.41C18.344 21.128 22 17.065 22 12.073z"/></svg>
                </a>
            </div>
            <p class="fade-in-element text-sm text-neutral-400">© <?= date("Y") ?> Created by Raihan Saputra. All rights reserved.</p>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi observer untuk animasi
        const initAnimations = () => {
            const fadeElements = document.querySelectorAll('.fade-in, .scale-in, .slide-in-left, .slide-in-right, .section-title, .deskripsi-item, .no-data-message');
            
            const appearOptions = { 
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const appearOnScroll = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("show");
                        
                        // Tambahkan efek fade out ketika elemen keluar dari viewport
                        if (entry.target.classList.contains('fade-in') || 
                            entry.target.classList.contains('scale-in')) {
                            entry.target.classList.remove("hide");
                        }
                    } else {
                        // Efek fade out hanya untuk elemen tertentu
                        if (entry.target.classList.contains('fade-in') || 
                            entry.target.classList.contains('scale-in')) {
                            entry.target.classList.add("hide");
                            entry.target.classList.remove("show");
                        }
                    }
                });
            }, appearOptions);

            fadeElements.forEach(element => {
                appearOnScroll.observe(element);
            });

            // Animasi khusus untuk map container
            const mapContainer = document.querySelector('.map-container');
            if (mapContainer) {
                appearOnScroll.observe(mapContainer);
            }
        };

        // Trigger animasi untuk elemen di hero section saat halaman dimuat
        const animateHeroSection = () => {
            const heroElements = document.querySelector('section:first-child').querySelectorAll('.fade-in');
            heroElements.forEach((el, index) => {
                setTimeout(() => {
                    el.classList.add('show');
                }, index * 200);
            });
        };

        // Inisialisasi peta
        const initMap = () => {
            try {
                const map = L.map('map').setView([<?= $lat ?>, <?= $lng ?>], 15);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                
                const customIcon = L.icon({
                    iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                    iconSize: [40, 40],
                    iconAnchor: [20, 40],
                    popupAnchor: [0, -40]
                });
                
                L.marker([<?= $lat ?>, <?= $lng ?>], {icon: customIcon}).addTo(map)
                    .bindPopup('<b>Lokasi Kami</b><br>' + "<?= addslashes(htmlspecialchars($profile['alamat'] ?? 'Lokasi')) ?>")
                    .openPopup();
                    
                L.control.zoom({
                    position: 'topright'
                }).addTo(map);
            } catch (error) {
                console.error("Error initializing map:", error);
                document.getElementById('map').innerHTML = `
                    <div class="w-full h-full flex items-center justify-center bg-gray-800">
                        <div class="text-center p-4">
                            <i class="fas fa-map-marked-alt text-4xl text-red-500 mb-3"></i>
                            <p class="text-red-400 font-bold text-lg">Peta tidak dapat dimuat</p>
                            <p class="text-gray-500 mt-2 text-sm">Koordinat: <?= htmlspecialchars($koordinat) ?></p>
                        </div>
                    </div>
                `;
            }
        };
        
        // Smooth scroll untuk anchor link
        const initSmoothScroll = () => {
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });
        };

        // Navigasi keyboard
        const initKeyboardNavigation = () => {
            window.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    const container = document.querySelector('.snap-container');
                    const sections = document.querySelectorAll('section');
                    const currentScroll = container.scrollTop;
                    let targetIndex = 0;
                    
                    sections.forEach((section, index) => {
                        const sectionTop = section.offsetTop;
                        const sectionBottom = sectionTop + section.offsetHeight;
                        
                        if (currentScroll >= sectionTop && currentScroll < sectionBottom) {
                            if (e.key === 'ArrowDown' && index < sections.length - 1) {
                                targetIndex = index + 1;
                            } else if (e.key === 'ArrowUp' && index > 0) {
                                targetIndex = index - 1;
                            } else {
                                targetIndex = index;
                            }
                        }
                    });
                    
                    sections[targetIndex].scrollIntoView({ behavior: 'smooth' });
                }
            });
        };

        // Jalankan semua fungsi inisialisasi
        initAnimations();
        animateHeroSection();
        initMap();
        initSmoothScroll();
        initKeyboardNavigation();
    });
</script>

</body>
</html>