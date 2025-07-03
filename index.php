<?php
include "koneksi.php";

// Ambil semua artikel dari terbaru ke terlama, TERMASUK tanggal_artikel
$sql = "SELECT id_artikel, nama_artikel, isi_artikel, foto_artikel, tanggal_artikel FROM tbl_artikel ORDER BY tanggal_artikel DESC, id_artikel DESC";
$query = mysqli_query($db, $sql);

if (!$query) {
    die("Query Error: " . mysqli_error($db));
}

$artikel = [];
while ($row = mysqli_fetch_assoc($query)) {
    $artikel[] = $row;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        html,
        body {
            scroll-behavior: smooth;
            margin: 0;
            height: 100%;
            overflow: hidden;
        }

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
            transition: opacity 1.2s ease-out, transform 1.2s ease-out;
        }

        .fade-in-element.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Dropdown Styles */
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

        /* Mobile Dropdown */
        @media (max-width: 768px) {
            .dropdown-content {
                position: static;
                display: none;
                transform: none;
                width: 100%;
                background-color: rgba(0,0,0,0.5);
            }
            
            .dropdown.active .dropdown-content {
                display: block;
            }
        }
    </style>
</head>

<body class="bg-zinc-900 text-white">

    <div class="snap-container">

        <section class="relative w-full overflow-hidden">
            <img src="img/Wuwa.jpg" alt="Fashion Model"
                class="absolute inset-0 w-full h-full object-cover object-center opacity-80" />
            <div class="absolute inset-0 bg-gradient-to-br from-zinc-900/70 to-transparent"></div>

            <header class="absolute top-0 left-0 w-full z-20 py-4 px-8">
                <nav class="flex justify-center">
                    <ul class="flex space-x-8 text-lg font-bold font-serif-custom">
                        <!-- Dropdown Blog -->
                        <li class="dropdown relative">
                            <a href="#" class="dropdown-toggle <?= in_array($current_page, ['index.php', 'foto.php']) ? 'active' : '' ?>">
                                Blog
                            </a>
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

            <div class="relative z-10 max-w-4xl px-6 pt-32 sm:pt-40 lg:pt-52 mx-auto text-center">
                <p class="fade-in-element uppercase tracking-widest text-sm font-bold text-neutral-300">Welcome!</p>
                <h1 class="fade-in-element text-4xl sm:text-5xl md:text-6xl font-bold font-serif-custom leading-tight mt-3">
                    In My Personal<br />
                    Website<br />
                    Dude!
                </h1>
                <a href="#blog-section" class="fade-in-element inline-block mt-6 text-sm tracking-widest uppercase hover:underline">
                    Scroll ↓
                </a>
            </div>
        </section>

        <?php if (empty($artikel)): ?>
            <section id="blog-section" class="flex items-center justify-center h-screen bg-zinc-800 text-neutral-300">
                <p class="text-2xl font-bold">Belum ada artikel yang tersedia.</p>
            </section>
        <?php else: ?>
            <?php foreach ($artikel as $index => $data): ?>
                <?php
                $foto = !empty($data['foto_artikel']) ? 'uploads/' . htmlspecialchars($data['foto_artikel']) : 'img/default.jpg';
                $judul = htmlspecialchars($data['nama_artikel'] ?? 'Judul Tidak Tersedia');
                $isi_full = $data['isi_artikel'] ?? 'Konten belum tersedia.';
                $isi_preview = nl2br(htmlspecialchars(mb_strimwidth($isi_full, 0, 200, "...")));
                
                $tanggal_artikel = 'Tanggal tidak tersedia';
                if (isset($data['tanggal_artikel']) && !empty($data['tanggal_artikel'])) {
                    $timestamp = strtotime($data['tanggal_artikel']);
                    if ($timestamp !== false) {
                        $tanggal_artikel = date('d F Y', $timestamp);
                    }
                }
                ?>
                <section id="<?= $index === 0 ? 'blog-section' : '' ?>" class="bg-cover bg-center bg-no-repeat overflow-hidden"
                    style="background-image: url('<?= $foto ?>');">
                    <div class="absolute inset-0 bg-black/70"></div>
                    <div class="relative z-10 w-full h-full max-w-7xl mx-auto px-6 py-20 flex items-center">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 w-full items-center">

                            <div class="flex justify-center">
                                <img src="<?= $foto ?>" alt="<?= $judul ?>" class="fade-in-element w-full max-w-xl h-auto rounded-2xl shadow-2xl object-cover">
                            </div>

                            <div class="space-y-6 text-white">
                                <p class="fade-in-element uppercase tracking-widest text-sm font-bold text-neutral-300">
                                    <?= $index === 0 ? 'Featured Blog' : 'Latest Post' ?>
                                </p>
                                <h2 class="fade-in-element text-4xl md:text-5xl font-bold font-serif-custom leading-tight"><?= $judul ?></h2>
                                
                                <p class="fade-in-element text-neutral-400 text-sm">
                                    Diterbitkan pada: <span class="font-semibold"><?= $tanggal_artikel ?></span>
                                </p>
                                
                                <p class="fade-in-element text-neutral-300 text-lg leading-relaxed"><?= $isi_preview ?></p>
                                <a href='readmore.php?id_artikel=<?= htmlspecialchars($data['id_artikel']) ?>'
                                    class='fade-in-element inline-block text-blue-400 hover:underline tracking-wider uppercase text-sm font-semibold'>
                                    Read More →
                                </a>
                            </div>

                        </div>
                    </div>
                </section>
            <?php endforeach; ?>
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
        // Fade-in animation
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

        fadeElements.forEach(element => {
            appearOnScroll.observe(element);
        });

        // Scroll snap logic
        document.addEventListener('DOMContentLoaded', () => {
            const sections = document.querySelectorAll('section');
            const container = document.querySelector('.snap-container');
            let current = 0;
            let isAnimating = false;

            function scrollToSection(index) {
                if (isAnimating || index < 0 || index >= sections.length) return;
                isAnimating = true;
                current = index;
                sections[index].scrollIntoView({ behavior: 'smooth' });
                setTimeout(() => { isAnimating = false; }, 700);
            }

            container.addEventListener('wheel', e => {
                e.preventDefault();
                if (isAnimating) return;
                if (e.deltaY > 0) scrollToSection(current + 1);
                else if (e.deltaY < 0) scrollToSection(current - 1);
            }, { passive: false });

            window.addEventListener('keydown', e => {
                if (isAnimating) return;
                if (e.key === 'ArrowDown') scrollToSection(current + 1);
                if (e.key === 'ArrowUp') scrollToSection(current - 1);
            });

            // Mobile dropdown toggle
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768) {
                        e.preventDefault();
                        const dropdown = this.closest('.dropdown');
                        dropdown.classList.toggle('active');
                    }
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 768 && !e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown').forEach(dropdown => {
                        dropdown.classList.remove('active');
                    });
                }
            });
        });
    </script>

</body>

</html>