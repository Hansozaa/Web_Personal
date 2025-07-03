<?php
// Selalu letakkan koneksi di bagian paling atas
include "koneksi.php";

// Ambil data foto dari database
$sql_foto = "SELECT * FROM tbl_foto ORDER BY id_foto DESC";
$result_foto = mysqli_query($db, $sql_foto);

// Tampung hasil query ke dalam array
$foto = [];
if ($result_foto) {
    while ($row = mysqli_fetch_assoc($result_foto)) {
        $foto[] = $row;
    }
} else {
    // Catat error jika query gagal untuk debugging
    error_log("Error fetching foto data: " . mysqli_error($db));
}

// Dapatkan nama file halaman saat ini untuk menandai navigasi aktif
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <title>Galeri Foto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* GENERAL STYLES */
        html, body {
            scroll-behavior: smooth;
            margin: 0;
            height: 100%;
            overflow-x: hidden;
            background-color: #0a0a0a;
            color: #ffffff;
        }

        /* Font utama disesuaikan seperti index.php */
        body {
            font-family: 'Playfair Display', serif;
        }

        .snap-container {
            height: 100vh;
            overflow-y: scroll;
            scroll-snap-type: y mandatory;
        }

        section {
            height: 100vh;
            scroll-snap-align: start;
            position: relative;
        }

        .fade-in-element {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 1s ease-out, transform 1s ease-out;
        }

        .fade-in-element.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* NAVIGATION STYLES */
        .dropdown:hover .dropdown-content { display: block; }
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
        .dropdown-item:hover { background-color: rgba(255,255,255,0.1); }
        .active::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: #a78bfa;
        }

        /* MODERN GALLERY STYLES */
        .gallery-section {
            background: #111827;
        }
        
        .gallery-title {
            color: #f3f4f6;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            position: relative;
        }
        
        .gallery-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%; 
            transform: translateX(-50%); 
            width: 25%; 
            height: 3px;
            background: linear-gradient(90deg, #a78bfa, #7c3aed);
            border-radius: 2px;
            transition: width 0.4s ease;
        }
        .gallery-title:hover::after { 
            width: 40%; 
        }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 1.5rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        .photo-card {
            background: #1f2937;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
            display: flex;
            flex-direction: column;
        }
        .photo-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(167, 139, 250, 0.2);
        }

        .photo-image-wrapper {
            width: 100%;
            height: 250px;
            overflow: hidden;
        }

        .photo-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }
        .photo-card:hover .photo-image {
            transform: scale(1.05);
        }
        
        .photo-info {
            padding: 1.25rem 1rem;
            background-color: #1f2937;
            border-top: 1px solid #374151;
            text-align: center; 
        }

        .photo-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #f9fafb;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .photo-date {
            font-size: 0.875rem;
            color: #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center; 
        }

        .photo-date i {
            margin-right: 6px;
            color: #a78bfa;
        }

        /* LIGHTBOX MODAL STYLES */
        .lightbox {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .lightbox.show {
            opacity: 1;
            visibility: visible;
        }

        .lightbox-content {
            position: relative;
            max-width: 90vw;
            max-height: 85vh;
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }
        .lightbox.show .lightbox-content {
            transform: scale(1);
        }

        .lightbox-img {
            max-width: 100%;
            max-height: 80vh;
            border-radius: 8px;
            box-shadow: 0 0 50px rgba(0,0,0,0.5);
        }
        
        .lightbox-caption {
            color: #d1d5db;
            text-align: center;
            margin-top: 1rem;
            font-size: 1rem;
        }

        .lightbox-close {
            position: absolute;
            top: -40px;
            right: -20px;
            color: white;
            font-size: 2.5rem;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .lightbox-close:hover {
            transform: scale(1.1);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .photo-grid { grid-template-columns: 1fr; gap: 1.5rem; padding: 1rem; }
            .photo-image-wrapper { height: 240px; }
            .lightbox-close { top: 10px; right: 15px; background: rgba(0,0,0,0.5); border-radius: 50%; width: 40px; height: 40px; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; }
        }
    </style>
</head>
<body class="bg-zinc-900 text-white">

<div class="snap-container">
    <section class="relative w-full overflow-hidden">
        <img src="img/Tide.jpg" alt="Background" class="absolute inset-0 w-full h-full object-cover object-center opacity-80">
        <div class="absolute inset-0 bg-gradient-to-br from-zinc-900/70 to-transparent"></div>
        <header class="absolute top-0 left-0 w-full z-20 py-4 px-8">
            <nav class="flex justify-center">
                <ul class="flex space-x-8 text-lg font-bold">
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
        <div class="relative z-10 flex items-center justify-center h-full">
            <div class="text-center max-w-4xl px-6 mx-auto">
                <p class="fade-in-element uppercase tracking-widest text-sm font-bold text-neutral-300">KOLEKSI VISUAL</p>
                <h1 class="fade-in-element text-4xl sm:text-5xl md:text-6xl font-bold leading-tight mt-3 text-white">
                    Galeri Foto Saya
                </h1>
                <?php if (count($foto) > 0): ?>
                    <a href="#foto-section" class="fade-in-element inline-block mt-6 text-sm tracking-widest uppercase hover:underline text-white-300">
                        Lihat Galeri ↓
                    </a>
                <?php else: ?>
                    <p class="fade-in-element mt-6 text-neutral-300">BELUM ADA FOTO YANG DITAMBAHKAN</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="foto-section" class="gallery-section py-20 overflow-y-auto">
        <div class="container mx-auto px-4">
            <?php if (count($foto) > 0): ?>
                <h2 class="fade-in-element text-3xl md:text-4xl font-bold text-center mb-16 gallery-title">
                    Koleksi Foto Terbaru
                </h2>
                <div class="photo-grid">
                    <?php foreach ($foto as $item): ?>
                        <?php
                            $gambar_path = 'images/' . htmlspecialchars($item['foto']);
                            $judul = htmlspecialchars($item['judul'] ?? 'Foto Tanpa Judul');
                            $upload_date = date('d F Y', strtotime($item['tanggal_upload'] ?? 'now'));
                        ?>
                        <a href="javascript:void(0);" class="photo-card fade-in-element" 
                           data-src="<?= $gambar_path ?>" 
                           data-title="<?= $judul ?>">
                            
                            <div class="photo-image-wrapper">
                                <?php if (file_exists($gambar_path) && !empty($item['foto'])): ?>
                                    <img src="<?= $gambar_path ?>" alt="<?= $judul ?>" class="photo-image">
                                <?php else: ?>
                                    <div class="photo-image bg-gray-800 flex items-center justify-center text-gray-500">
                                        <i class="fas fa-image text-5xl"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="photo-info">
                                <h3 class="photo-title"><?= $judul ?></h3>
                                <div class="photo-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span><?= $upload_date ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="flex flex-col items-center justify-center h-full text-center py-20">
                    <div class="bg-gray-800 p-8 rounded-lg max-w-md shadow-lg">
                        <i class="fas fa-camera-retro text-5xl text-purple-400 mb-4"></i>
                        <h3 class="text-xl font-bold text-white mb-2">Galeri Masih Kosong</h3>
                        <p class="text-gray-300 mb-4">Sepertinya belum ada foto yang diunggah. Cek kembali nanti!</p>
                        <a href="admin/login.php" class="text-purple-400 hover:text-purple-300 font-medium">
                            Login untuk menambahkan foto
                        </a>
                    </div>
                </div>
            <?php endif; ?>
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

<div id="lightbox" class="lightbox">
    <span id="lightbox-close" class="lightbox-close">×</span>
    <div class="lightbox-content">
        <img id="lightbox-img" src="" alt="Tampilan foto" class="lightbox-img">
        <div id="lightbox-caption" class="lightbox-caption"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // FADE-IN ANIMATION LOGIC
    const fadeElements = document.querySelectorAll('.fade-in-element');
    const appearOptions = { threshold: 0.2, rootMargin: "0px 0px -50px 0px" };
    const appearOnScroll = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
                observer.unobserve(entry.target);
            }
        });
    }, appearOptions);
    fadeElements.forEach(element => appearOnScroll.observe(element));

    // LIGHTBOX LOGIC
    const lightbox = document.getElementById('lightbox');
    if (lightbox) {
        const lightboxImg = document.getElementById('lightbox-img');
        const lightboxCaption = document.getElementById('lightbox-caption');
        const lightboxClose = document.getElementById('lightbox-close');
        const photoCards = document.querySelectorAll('.photo-card');

        const openLightbox = (src, title) => {
            lightboxImg.src = src;
            lightboxCaption.textContent = title;
            lightbox.classList.add('show');
            document.body.style.overflow = 'hidden';
        };

        const closeLightbox = () => {
            lightbox.classList.remove('show');
            document.body.style.overflow = '';
        };

        photoCards.forEach(card => {
            card.addEventListener('click', (e) => {
                e.preventDefault();
                const src = card.dataset.src;
                const title = card.dataset.title;
                if(src && title) {
                   openLightbox(src, title);
                }
            });
        });

        lightboxClose.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && lightbox.classList.contains('show')) {
                closeLightbox();
            }
        });
    }
});
</script>

</body>
</html>