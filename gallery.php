<?php
include "koneksi.php";

$sql = "SELECT * FROM tbl_gallery ORDER BY id_gallery DESC";
$result = mysqli_query($db, $sql);

$gallery = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $gallery[] = $row;
    }
} else {
    error_log("Error fetching gallery data: " . mysqli_error($db));
}

// Tambahkan baris ini untuk mendeteksi halaman aktif
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <title>Project Portfolio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Playfair Display', serif;
            margin: 0;
            height: 100vh;
            overflow: hidden;
            background-color: #0a0a0a;
            color: #ffffff;
        }

        .snap-container {
            height: 100vh;
            overflow-y: scroll;
            scroll-snap-type: y mandatory;
            scroll-behavior: smooth;
            background-color: #0a0a0a;
        }

        section {
            height: 100vh;
            scroll-snap-align: start;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 2rem 1rem;
            text-align: center;
        }

        .fade-in-element {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 1.2s ease-out, transform 1.2s ease-out;
            transition-delay: 0s;
        }

        .fade-in-element.show {
            opacity: 1;
            transform: translateY(0);
        }

        header {
            background: linear-gradient(to bottom, rgba(0,0,0,0.8), rgba(0,0,0,0));
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
            bottom: -2px; /* Disesuaikan agar pas */
            left: 0;
            background-color: #a78bfa;
        }

        .hero-section .background-image-hero {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            opacity: 0.8;
            transition: transform 0.5s ease-out;
        }
        .hero-section.show .background-image-hero {
            transform: scale(1.05);
        }
        .hero-section .overlay-hero {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.5));
        }

        /* --- Project Section Styles (Tidak diubah) --- */
        .project-section {
            background-color: #1a1a1a;
        }
        .project-section .background-image-project {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            opacity: 0.2;
            filter: grayscale(100%);
            transition: opacity 0.3s ease-in-out;
        }
        .project-section:hover .background-image-project {
            opacity: 0.3;
        }
        .project-section .overlay-project {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0), rgba(0,0,0,0.1));
        }
        .project-item-layout {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            text-align: left;
            border-radius: 1.5rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            background-color: rgba(26, 26, 26, 0.8);
            transition: box-shadow 0.3s ease-in-out, transform 0.3s ease-in-out;
            transform: scale(0.98);
        }
        .project-section:hover .project-item-layout {
            transform: scale(1);
            box-shadow: 0 12px 30px rgba(0,0,0,0.4);
        }
        .project-item-layout.reversed {
            grid-template-areas: "image content";
        }
        .project-item-layout.reversed .project-details { grid-area: content; }
        .project-item-layout.reversed .project-image-wrapper { grid-area: image; }
        .project-item-layout .project-image-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .project-item-layout img {
            width: 100%;
            max-width: 550px;
            height: auto;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.6);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }
        .project-item-layout img:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.8);
        }
        .project-title {
            font-size: 3rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }
        .project-description {
            font-size: 1.15rem;
            line-height: 1.7;
            color: #d1d5db;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .placeholder-image {
            background-color: #3f3f46;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #a1a1aa;
            height: 300px;
            width: 100%;
            border-radius: 1rem;
        }
        .footer-section {
            background-color: #1a1a1a;
        }
        .footer-section .background-image-footer {
            position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center; opacity: 0.2; filter: grayscale(100%);
        }
        .footer-section .overlay-footer { background: rgba(0,0,0,0.5); }
        .social-icon-link { transition: transform 0.2s ease-in-out; }
        .social-icon-link:hover { transform: translateY(-5px); }
        .social-icon-link.instagram:hover { color: #e1306c; }
        .social-icon-link.youtube:hover { color: #ff0000; }
        .social-icon-link.tiktok:hover { color: #69c9d0; }
        .social-icon-link.facebook:hover { color: #1877f2; }

        @media (max-width: 900px) {
            .project-item-layout { grid-template-columns: 1fr; gap: 2.5rem; text-align: center; padding: 2rem 1.5rem; border-radius: 1rem; }
            .project-item-layout.reversed { grid-template-areas: unset; }
            .project-item-layout .project-image-wrapper { order: -1; grid-column: unset; }
            .project-item-layout .project-details, .project-item-layout.reversed .project-details { grid-column: unset; }
            .project-title { font-size: 2.5rem; }
            .project-description { font-size: 1rem; }
        }
     
    </style>
</head>
<body class="bg-zinc-900 text-white">

<div class="snap-container">
    <section class="hero-section relative">
        <img src="img/Shore.jpg" alt="Background" class="background-image-hero">
        <div class="overlay-hero"></div>
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
        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-6">
            <p class="fade-in-element uppercase tracking-widest text-sm font-bold text-neutral-300">Selamat Datang</p>
            <h1 class="fade-in-element text-5xl md:text-6xl font-bold leading-tight mt-3">Portofolio<br>Project Terbaru</h1>
            <?php if (count($gallery) > 0): ?>
                <a href="#project-<?= htmlspecialchars($gallery[0]['id_gallery']) ?>" class="fade-in-element inline-block mt-6 text-sm tracking-widest uppercase hover:underline">Lihat Project ↓</a>
            <?php else: ?>
                <p class="fade-in-element inline-block mt-6 text-sm tracking-widest uppercase text-neutral-400">Belum ada project yang ditambahkan.</p>
            <?php endif; ?>
        </div>
    </section>

    <?php if (count($gallery) > 0): ?>
        <?php foreach ($gallery as $index => $item): ?>
            <?php
            $gambar_path = 'images/' . htmlspecialchars($item['foto']);
            $judul_proyek = htmlspecialchars($item['judul'] ?? 'Judul Proyek Tanpa Nama');
            $deskripsi_proyek = htmlspecialchars($item['deskripsi'] ?? '');
            $reversed_layout = ($index % 2 !== 0) ? 'reversed' : '';
            ?>
            <section id="project-<?= htmlspecialchars($item['id_gallery']) ?>" class="project-section">
                <?php if (file_exists($gambar_path) && !empty($item['foto'])): ?>
                    <img src="<?= $gambar_path ?>" alt="<?= $judul_proyek ?>" class="background-image-project">
                <?php endif; ?>
                <div class="overlay-project"></div>

                <div class="project-item-layout <?= $reversed_layout ?>">
                    <div class="project-details fade-in-element">
                        <p class="uppercase tracking-widest text-sm font-bold text-purple-400 mb-2">Project #<?= $index + 1 ?></p>
                        <h2 class="project-title"><?= $judul_proyek ?></h2>
                        <?php if (!empty($item['deskripsi'])): ?>
                            <p class="project-description"><?= $deskripsi_proyek ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="project-image-wrapper fade-in-element">
                        <?php if (file_exists($gambar_path) && !empty($item['foto'])): ?>
                            <img src="<?= $gambar_path ?>" alt="<?= $judul_proyek ?>">
                        <?php else: ?>
                            <div class="placeholder-image">
                                Gambar tidak tersedia
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endforeach; ?>
    <?php else: ?>
        <section class="flex items-center justify-center h-screen bg-zinc-800 text-neutral-300">
            <p class="text-2xl font-bold fade-in-element">Belum ada project yang tersedia.</p>
        </section>
    <?php endif; ?>

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
        const fadeElements = document.querySelectorAll('.fade-in-element');
        const appearOptions = { threshold: 0.2, rootMargin: '0px 0px -100px 0px' };

        const appearOnScroll = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("show");
                    observer.unobserve(entry.target);
                }
            });
        }, appearOptions);

        fadeElements.forEach(element => appearOnScroll.observe(element));

        const heroElements = document.querySelector('.hero-section').querySelectorAll('.fade-in-element');
        heroElements.forEach((el, index) => {
            el.style.transitionDelay = `${index * 0.15}s`;
            el.classList.add('show');
        });

        const snapContainer = document.querySelector('.snap-container');
        const sections = document.querySelectorAll('section');
        let currentSectionIndex = 0;
        let isThrottled = false;
        const throttleDelay = 700;

        function navigateSections(direction) {
            if (isThrottled) return;
            let nextIndex = currentSectionIndex;
            if (direction === 'down' && currentSectionIndex < sections.length - 1) {
                nextIndex++;
            } else if (direction === 'up' && currentSectionIndex > 0) {
                nextIndex--;
            } else {
                return;
            }
            currentSectionIndex = nextIndex;
            sections[currentSectionIndex].scrollIntoView({ behavior: 'smooth' });
            isThrottled = true;
            setTimeout(() => { isThrottled = false; }, throttleDelay);
        }

        snapContainer.addEventListener('wheel', (e) => {
            e.preventDefault();
            if (e.deltaY > 0) { navigateSections('down'); } 
            else { navigateSections('up'); }
        }, { passive: false });

        window.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown') { e.preventDefault(); navigateSections('down'); } 
            else if (e.key === 'ArrowUp') { e.preventDefault(); navigateSections('up'); }
        });

        snapContainer.addEventListener('scroll', () => {
            if (isThrottled) return;
            let closestSectionIndex = 0;
            let minDistance = Infinity;
            sections.forEach((section, index) => {
                const rect = section.getBoundingClientRect();
                const distance = Math.abs(rect.top);
                if (distance < minDistance) {
                    minDistance = distance;
                    closestSectionIndex = index;
                }
            });
            currentSectionIndex = closestSectionIndex;
        });

        const lihatProjectBtn = document.querySelector('a[href^="#project-"]');
        if (lihatProjectBtn) {
            lihatProjectBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetSection = document.querySelector(targetId);
                if (targetSection) {
                    const index = Array.from(sections).indexOf(targetSection);
                    if (index !== -1) {
                        currentSectionIndex = index;
                        sections[currentSectionIndex].scrollIntoView({ behavior: 'smooth' });
                    }
                }
            });
        }
    });
</script>

</body>
</html>