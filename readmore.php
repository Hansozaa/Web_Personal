<?php
include 'koneksi.php';

if (!isset($_GET['id_artikel']) || empty($_GET['id_artikel'])) {
    echo "<p class='text-center text-red-500'>Artikel tidak ditemukan. ID artikel tidak valid.</p>";
    exit;
}

$id = intval($_GET['id_artikel']);

$query = mysqli_query($db, "SELECT id_artikel, nama_artikel, isi_artikel, foto_artikel, tanggal_artikel FROM tbl_artikel WHERE id_artikel = $id");

if (!$query) {
    die("<p class='text-center text-red-500'>Query Error: " . mysqli_error($db) . "</p>");
}

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<p class='text-center text-red-500'>Artikel tidak ditemukan.</p>";
    exit;
}

$tanggal_penerbitan = 'Tanggal tidak tersedia';
if (isset($data['tanggal_artikel']) && !empty($data['tanggal_artikel'])) {
    $timestamp = strtotime($data['tanggal_artikel']);
    if ($timestamp !== false) {
        $tanggal_penerbitan = date('d F Y', $timestamp);
    }
}

$foto_artikel = !empty($data['foto_artikel']) ? 'uploads/' . htmlspecialchars($data['foto_artikel']) : 'img/default.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['nama_artikel']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .font-serif-custom {
            font-family: 'Playfair Display', serif;
        }
        .article-content p {
            margin-bottom: 1em;
            line-height: 1.6;
        }
    </style>
</head>
<body class="bg-zinc-900 text-white font-serif-custom min-h-screen flex items-start justify-center py-12 px-4">

    <div class="max-w-3xl w-full">
        <p class="text-blue-400 hover:underline text-sm mb-6">
            <a href="index.php">← Kembali ke Beranda</a>
        </p>

        <h1 class="text-4xl md:text-5xl font-bold mb-3 leading-tight"><?= htmlspecialchars($data['nama_artikel']) ?></h1>
        
        <p class="text-neutral-400 text-base mb-6">
            Diterbitkan pada: <span class="font-semibold"><?= $tanggal_penerbitan ?></span>
        </p>

        <?php if (!empty($data['foto_artikel'])): ?>
            <div class="relative mb-8">
                <img src="<?= $foto_artikel ?>" alt="<?= htmlspecialchars($data['nama_artikel']) ?>" class="w-full max-h-[500px] object-cover rounded-2xl shadow-lg">

                <div class="absolute bottom-3 right-4 flex gap-3 bg-black/50 px-3 py-2 rounded-xl backdrop-blur-md">
                    <a href="https://www.facebook.com/share/18zi9EpJ6f/" target="_blank" class="text-white hover:text-blue-400" title="Bagikan ke Facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-5 h-5" viewBox="0 0 24 24"><path d="M22 12.073C22 6.504 17.523 2 12 2S2 6.504 2 12.073C2 17.065 5.656 21.128 10.438 21.878v-6.41H7.898v-2.573h2.54V10.41c0-2.507 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.462h-1.26c-1.243 0-1.63.775-1.63 1.568v1.88h2.773l-.443 2.573h-2.33v6.41C18.344 21.128 22 17.065 22 12.073z"/></svg>
                    </a>
                    <a href="https://www.instagram.com/hannsoza?igsh=bWdxazZxNzB5Zjdz" target="_blank" class="text-white hover:text-pink-400" title="Bagikan ke Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-5 h-5" viewBox="0 0 24 24"><path d="M12 2.2c3.2 0 3.584.012 4.85.07 1.17.055 1.97.24 2.43.403a4.92 4.92 0 011.767 1.008 4.918 4.918 0 011.008 1.768c.163.459.348 1.258.403 2.428.058 1.267.07 1.65.07 4.85s-.012 3.584-.07 4.85c-.055 1.17-.24 1.97-.403 2.43a4.918 4.918 0 01-1.008 1.767 4.918 4.918 0 01-1.768 1.008c-.459.163-1.258.348-2.428.403-1.267.058-1.65.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.055-1.97-.24-2.43-.403a4.918 4.918 0 01-1.767-1.008 4.918 4.918 0 01-1.008-1.768c-.163-.459-.348-1.258-.403-2.428C2.212 15.584 2.2 15.2 2.2 12s.012-3.584.07-4.85c.055-1.17.24-1.97.403-2.43a4.92 4.92 0 011.008-1.767 4.918 4.918 0 011.768-1.008c.459-.163 1.258-.348 2.428-.403C8.416 2.212 8.8 2.2 12 2.2zm0 1.8c-3.15 0-3.522.012-4.762.07-1.03.049-1.59.219-1.958.363a3.12 3.12 0 00-1.164.757 3.12 3.12 0 00-.757 1.165c-.144.367-.314.928-.363 1.957-.058 1.24-.07 1.612-.07 4.763s.012 3.522.07 4.762c.049 1.03.219 1.59.363 1.958.144.437.37.827.757 1.164.338.338.728.613 1.165.757.367.144.928.314 1.957.363 1.24.058 1.612.07 4.763.07s3.522-.012 4.762-.07c1.03-.049 1.59-.219 1.958-.363.437-.144.827-.37 1.164-.757.338-.338.613-.728.757-1.165.144-.367.314-.928.363-1.957.058-1.24.07-1.612.07-4.763s-.012-3.522-.07-4.762c-.049-1.03-.219-1.59-.363-1.958a3.12 3.12 0 00-.757-1.164 3.12 3.12 0 00-1.165-.757c-.367-.144-.928-.314-1.957-.363-1.24-.058-1.612-.07-4.763-.07zM12 5.838a6.162 6.162 0 110 12.324 6.162 6.162 0 010-12.324zm0 1.8a4.362 4.362 0 100 8.724 4.362 4.362 0 000-8.724zm5.838-.9a1.44 1.44 0 110 2.88 1.44 1.44 0 010-2.88z"/></svg>
                    </a>
                    <a href="https://youtube.com/@raihansaputra6283?si=PQiT9YWRmcoayTKg" target="_blank" class="text-white hover:text-red-500" title="Bagikan ke YouTube">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M21.8 8.001s-.2-1.4-.8-2c-.7-.8-1.6-.8-2-1C16.1 4.5 12 4.5 12 4.5s-4.1 0-6.9.5c-.5.2-1.3.2-2 1-.6.6-.8 2-.8 2S2 9.6 2 11.2v1.6c0 1.6.2 3.2.2 3.2s.2 1.4.8 2c.7.8 1.6.8 2 1 2.8.5 6.9.5 6.9.5s4.1 0 6.9-.5c.5-.2 1.3-.2 2-1 .6-.6.8-2 .8-2s.2-1.6.2-3.2v-1.6c0-1.6-.2-3.2-.2-3.2zM9.8 14.8V9.2l5.5 2.8-5.5 2.8z" />
                        </svg>
                    </a>
                    <a href="https://www.tiktok.com/@vgrace1_?_t=ZS-8xB5x3suWbU&_r=1" target="_blank" class="text-white hover:text-white" title="Bagikan ke TikTok">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M9.5 3c0-.6.4-1 1-1h2c.6 0 1 .4 1 1 0 1.7 1.3 3 3 3 .6 0 1 .4 1 1v1c0 .6-.4 1-1 1-1.1 0-2.1-.3-3-1v6.1c0 2.8-2.2 5-5 5s-5-2.2-5-5 2.2-5 5-5c.3 0 .7 0 1 .1v2.1c-.3-.1-.7-.1-1-.1-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3V3z" />
                        </svg>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <div class="leading-relaxed text-lg text-neutral-200 mb-10 space-y-4 article-content">
            <?php
            $isi_bersih = str_replace(["\\r", "\\n"], ["", "\n"], $data['isi_artikel']);
            $paragraf = explode("\n", trim($isi_bersih));
            
            foreach ($paragraf as $p) {
                $trimmed_p = trim($p);
                if ($trimmed_p !== '') {
                    echo '<p>' . nl2br(htmlspecialchars($trimmed_p)) . '</p>';
                }
            }
            ?>
        </div>
    </div>

</body>
</html>