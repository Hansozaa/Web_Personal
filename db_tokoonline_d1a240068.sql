-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 03 Jul 2025 pada 15.50
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_tokoonline_d1a240068`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_about`
--

CREATE TABLE `tbl_about` (
  `id_about` int(2) NOT NULL,
  `about` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_profil` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `koordinat` varchar(50) NOT NULL,
  `link_maps` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_about`
--

INSERT INTO `tbl_about` (`id_about`, `about`, `foto_profil`, `alamat`, `koordinat`, `link_maps`) VALUES
(16, 'Hallo?!, Bagaimana Kabarnya? Semoga kalian dalam keadaan baik-baik saja ya.\\\\\\\\n\\\\\\\\nPerkenalkan, Saya Raihan Saputra, Mahasiswa Sistem Informasi Fakultas Ilmu Komputer, Universitas Subang. Alasan saya memilih Sistem Informasi Fakultas Ilmu Komputer adalah agar saya bisa belajar tentang apa itu Komputer, bagaimana cara membuat website dari nol, dan belajar sesuatu yang baru tentang Komputer. \\\\\\\\n\\\\\\\\nAlasan saya juga berhubungan dengan visi saya sebagai seorang mahasiswa FASILKOM, yakni membuat dan memfasilitasi masyarakat dengan teknologi yang dapat berguna untuk mereka di kemudian hari, maka dari itu saya memulai memfasilitasi lingkunagn sekitar saya dulu, contohnya Membuat Website Toko Raihan, Walaupun masih basic dan belum di upgrade fitur dan mobilitasnya. \\\\\\\\n\\\\\\\\nMoto saya adalah \\\\\\\\\\\\\\\"Long Life Education\\\\\\\\\\\\\\\" dan \\\\\\\\\\\\\\\"Freedom\\\\\\\\\\\\\\\"', 'uploads/about/profile_1751202446.png', 'Jalan Letnan Ukin, Kelurahan Soklat, Subang, West Java, Java, 41215, Indonesia', '-6.5683218094408975,107.77618337879667', 'https://www.google.com/maps/search/?api=1&query=-6.5683218094408975,107.77618337879667');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_artikel`
--

CREATE TABLE `tbl_artikel` (
  `id_artikel` int(5) NOT NULL,
  `nama_artikel` text NOT NULL,
  `isi_artikel` text NOT NULL,
  `foto_artikel` text NOT NULL,
  `tanggal_artikel` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_artikel`
--

INSERT INTO `tbl_artikel` (`id_artikel`, `nama_artikel`, `isi_artikel`, `foto_artikel`, `tanggal_artikel`) VALUES
(14, 'Wuthering Waves 2.4 Membuat Dunia Pergamingan Gempar', 'Update Wuthering Waves versi 2.4, \"Lightly We Toss the Crown,\" menghadirkan ekspansi besar dengan memperkenalkan wilayah baru bernama Septimont, lengkap dengan kelanjutan cerita utama dalam dua babak baru. Pemain dapat menantikan kehadiran dua karakter Bintang 5 baru, yaitu Cartethyia (Aero/Sword) dan Lupa (Fusion/Broadblade). Selain itu, update ini juga membawa serangkaian konten segar seperti tantangan permanen \"Banners Never Fall,\" Echoes dan Sonata Effects baru, skin bertema musim panas untuk Changli dan Carlotta, serta berbagai event berhadiah menarik dan optimalisasi sistem penting seperti Guide Depot untuk mempermudah jalannya permainan.\n\nUntuk memulai proses pra-unduh di PC, kamu bisa mengikuti langkah - langkah yang telah kami sediakan. Ukuran file unduhan untuk prapunduh versi 2.4 ini adalah sekitar 19 GB. Meskipun demikian, sangat disarankan untuk menyediakan ruang penyimpanan kosong setidaknya 38 GB. Ruang ekstra ini diperlukan untuk kelancaran proses dekompresi file setelah unduhan selesai, memastikan semua data game dapat terpasang dengan sempurna di PC kamu.\n\nCara Manual Update Data Wuthering Waves 2.4.0 adalah salah satu solusi termudah untuk mengurangi interaksi dengan launcher dalam game. Pemain dapat menggunakan unduhan pihak ketiga ini untuk memastikan agar file tidak corrupt dan terunduh dengan kecepatan penuh. Kamu bisa menggunakan aplikasi semacam Internet Download Manager (IDM), Free Download Manager (FDM) atau semacamnya.', 'artikel_1750963968.jpg', '2025-06-29 20:45:22'),
(15, 'Lingyang Adalah Karakter Paling Dibenci Di Wutering Waves', 'Lingyang (凌阳) adalah karakter Glacio Natural Resonator bintang lima (5★) yang menggunakan gauntlet sebagai senjata utama. Lahir pada 8 Agustus di Huanglong, ia merupakan anggota Liondance Troupe di Jinzhou dan dikenal sebagai Suan’ni terakhir, makhluk mirip singa dalam mitologi Tionghoa.\n\nDalam pertempuran, Lingyang memiliki dua mode utama: Striding Lion (mode Forte) dan Lion’s Vigor (ultimate). Ketika Forte-nya aktif, ia berubah menjadi wujud singa yang melancarkan serangan udara selama beberapa detik. Ultimate-nya menambahkan +50 % Glacio DMG selama hingga 14 detik \n\nSistem Resonance Chain Lingyang sangat kaya dan memberikan efek seperti anti-interupsi pada mode Forte, pemulihan energi, peningkatan damage dan buff tim. Node final menambahkan bonus damage signifikan lagi saat dalam mode Striding Lion \n\nDari segi kekuatan, Lingyang unggul dalam single-target burst, terutama jika rotasi skill-nya tepat. Namun, ia memiliki kelemahan seperti HP dan DEF rendah, serta kemampuan AoE terbatas. Selain itu, jangkauan serangan kadang terasa sempit dan ia cukup bergantung pada tim untuk mengeluarkan potensi penuhnya .\n\nDalam komunitas, ia menuai reaksi campuran. Banyak pemain merasa mekanisme tanky-nya sulit dipakai dan tidak praktis dibanding hero lain, namun fans setianya memuji playstilenya yang unik sebagai tantangan yang menyenangkan .\n\nUntuk meningkatkan Lingyang secara optimal, kamu bisa mengutamakan rank Resonance node 3, 6 (damage bonus), dan node 4 (buff tim). Sepaket artefak dan gauntlet seperti Abyss Surges, Stonard, atau Marcato juga sangat direkomendasikan untuk meningkatkan regen energi, damage glacio, dan buff selama Striding Lion', 'artikel_1749821239.jpeg', '2025-06-29 20:45:22'),
(16, 'Cartethya Menjadi Karakter Paling Laku Saat Ini Di Wuthering Waves', 'Cartethyia adalah salah satu karakter bintang lima (5★) dalam game Wuthering Waves, yang menggunakan elemen Aero dan senjata pedang. Ia merupakan seorang Resonator bawaan (Congenital Resonator) dan dikenal dengan julukan \"Fleurdelys\" atau \"Gadis Suci yang Diberkati\" dari organisasi Order of the Deep di kerajaan Rinascita. Karakter ini resmi dirilis pada tanggal 12 Juni 2025 dalam pembaruan versi 2.4 melalui banner bertajuk Dance in the Storm\'s Wake. Suara Cartethyia diisi oleh Amanda Elizabeth Rischel untuk versi Inggris, Asakawa Yū untuk versi Jepang, Yun He Zhui untuk versi Mandarin, dan Bae Ha-gyoung untuk versi Korea.\n\nCartethyia memiliki gaya bermain yang unik karena menggunakan mekanik dua bentuk (dual-form). Dalam bentuk awal, ia tampil sebagai seorang gadis muda yang menyerang dengan cepat, menumpuk efek Aero Erosion, dan memanggil bayangan pedang untuk membantu menyerang musuh. Namun saat masuk ke mode transformasi Fleurdelys, Cartethyia melepaskan kekuatan penuhnya sebagai DPS utama dengan serangan burst tinggi, durasi sekitar 12 detik, dan peningkatan Aero DMG berdasarkan persentase HP yang dimilikinya.\n\nDalam pertempuran, Cartethyia menggunakan berbagai jenis serangan seperti serangan normal, berat, udara, dan serangan balasan setelah menghindar. Skill utamanya bernama Sword to Bear Their Names, yang mampu menarik musuh ke satu titik dan mempercepat penumpukan efek Erosion. Skill tertingginya adalah Resonance Liberation, yaitu saat ia berubah ke mode Fleurdelys dengan mengorbankan sebagian HP untuk melepaskan serangan kombinasi mematikan. Mekanik penumpukan Erosion dan skala berdasarkan HP menjadikannya karakter yang sangat kuat jika dibangun dengan benar.\n\nUntuk optimalisasi, Cartethyia sangat cocok dipasangkan dengan karakter pendukung seperti Ciaccona dan Aero Rover, yang mampu mempercepat Erosion dan memberikan buff Aero. Artefak yang disarankan adalah Windward Pilgrimage, karena memberikan bonus HP, Critical, dan dukungan terhadap efek Erosion. Echo dan senjata yang digunakan juga sebaiknya difokuskan pada peningkatan HP, Crit Damage, serta buff Aero secara keseluruhan.\n\nDari sisi perkembangan karakter, material yang dibutuhkan untuk Ascension termasuk Bamboo Iris, Tidal Residuum, dan berbagai jenis Metallic Drip, serta item langka seperti Unfading Glory. Material tersebut bisa didapatkan di wilayah Septimont, domain Rinascita Forgery Challenge, atau melalui event-event khusus dalam game. Senjata andalannya yang disebut Defier’s Thorn tersedia melalui banner eksklusif dan dirancang khusus untuk mendukung kemampuan HP-scaling miliknya.\n\nDari segi lore, Cartethyia adalah sosok yang memiliki kedekatan mendalam dengan dunia spiritual dan kehancuran besar yang disebut Threnodian Leviathan dan Dark Tide. Ia menyukai makanan khas seperti Laurus Salad, dan dalam dialognya menyebut kutipan puisi klasik: \"A thousand stars light up the air. Freedom blooms like flowers wide.\"—yang menggambarkan pandangannya tentang kebebasan dan harapan.\n\nDengan mekanik permainan yang kompleks, potensi burst damage yang besar, dan latar belakang cerita yang kuat, Cartethyia menjadi salah satu karakter yang paling menarik dan dicintai oleh pemain Wuthering Waves. Bagi pemain yang ingin mengembangkan karakter ini secara optimal, pemahaman mendalam terhadap sistem Erosion dan pemilihan tim yang tepat sangatlah penting.\n\n', 'artikel_1749822320.jpg', '2025-06-29 20:45:22'),
(25, 'Phainon: Cahaya Terasing dari Dunia Honkai: Star Rail', 'Dalam jagat semesta Honkai: Star Rail yang luas dan penuh teka-teki, muncul nama Phainon, sosok misterius yang mulai menarik perhatian komunitas meskipun belum diperkenalkan secara resmi dalam permainan. Keberadaannya diketahui sebagai salah satu anggota dari kelompok rahasia bernama Stellaron Hunters, organisasi yang memiliki peran penting dalam dinamika cerita utama. Meskipun informasi mengenai dirinya masih terbatas, nama Phainon telah memicu banyak spekulasi di kalangan penggemar, terutama karena keterkaitannya dengan karakter-karakter besar lain yang lebih dahulu dikenal.\n\nNama “Phainon” sendiri bukanlah nama biasa. Dalam tradisi kosmologi kuno, Phainon adalah sebutan untuk planet Saturnus. HoYoverse, pengembang Honkai: Star Rail, dikenal kerap menyematkan unsur mitologi dan astronomi dalam penamaan karakter-karakternya. Dengan nama yang berakar pada konsep langit dan bintang, Phainon diprediksi akan memiliki peran penting dalam konteks narasi semesta—sebuah cahaya dingin yang berkilau jauh dari pusat konflik namun tetap memiliki pengaruh besar.\n\nDari informasi yang beredar, Phainon digambarkan sebagai pribadi tenang, penuh perhitungan, dan sangat strategis. Ia diperkirakan menjadi otak dari sebagian besar pergerakan Stellaron Hunters, berbeda dengan anggota lain seperti Kafka yang tampil lebih karismatik atau Blade yang cenderung brutal. Karakteristik ini menjadikan Phainon sebagai figur yang tidak hanya kuat dari segi kekuatan bertarung, tetapi juga unggul dalam kecerdasan dan manipulasi taktik. Gaya ini membuat kehadirannya terasa mengancam sekaligus elegan—sebuah campuran yang menarik dalam dunia yang penuh ambiguitas moral.\n\nSebagai bagian dari Stellaron Hunters, Phainon memiliki tujuan yang masih diselimuti kabut misteri. Organisasi ini dikenal sering bertindak di luar hukum, bahkan menantang lembaga-lembaga besar seperti Interastral Peace Corporation dan Xianzhou Alliance. Namun, tidak semua tindakan mereka murni bersifat jahat. Dalam beberapa kesempatan, mereka justru terlihat berusaha mengungkap kebenaran yang disembunyikan oleh kekuasaan besar. Di sinilah kemungkinan besar Phainon bermain: bukan sebagai penjahat, tetapi sebagai karakter dengan motivasi yang kompleks dan tujuan pribadi yang dalam.\n\nMeski belum dapat dimainkan atau muncul dalam cerita utama secara langsung, para pemain berspekulasi bahwa Phainon akan membawa kekuatan elemen unik, kemungkinan Quantum atau Imaginary, serta mengikuti Path seperti Nihility atau Harmony. Hal ini sejalan dengan citranya yang strategis dan misterius, cocok untuk peran support atau debuffer yang mematikan secara diam-diam. Komunitas Honkai: Star Rail pun terus menantikan pengumuman resmi mengenai detail karakternya, termasuk gaya bertarung dan latar belakang masa lalunya.\n\nKehadiran Phainon di dunia Honkai bukan sekadar tambahan karakter, melainkan simbol dari perluasan semesta dan kedalaman cerita yang disajikan oleh HoYoverse. Banyak yang meyakini bahwa ia memiliki hubungan erat dengan karakter-karakter utama lainnya, termasuk Elio sang peramal masa depan, atau bahkan dengan latar cerita dari Trailblazer sendiri. Dengan potensi sebesar itu, Phainon bukan hanya menjadi karakter yang dinanti karena kekuatannya, tetapi juga karena dampaknya terhadap arah cerita permainan.\n\nSecara keseluruhan, Phainon adalah gambaran dari kekuatan yang tersembunyi namun nyata. Meskipun masih berada dalam bayang-bayang cerita utama, kehadirannya telah cukup untuk memicu rasa penasaran yang tinggi. Dalam dunia yang penuh dengan konflik antarbintang dan pertarungan eksistensial, Phainon tampaknya akan muncul sebagai salah satu tokoh penting yang mampu mengubah jalannya sejarah. Kini, yang tersisa hanyalah waktu sebelum cahaya dari planet Phainon benar-benar bersinar dalam perjalanan para Trailblazer.\n', 'artikel_1751483738.jpg', '2025-07-03 02:15:38'),
(26, 'Byon Combat 5: Malam Penuh Aksi dan Kejutan di Senayan', 'Gelaran Byon Combat Showbiz Vol. 5 kembali mengguncang Tennis Indoor Senayan, Jakarta, pada Sabtu malam, 28 Juni 2025. Acara ini menjadi salah satu ajang pertarungan paling dinanti dalam skena bela diri Indonesia karena format unik yang mereka usung—kick striking. Format ini merupakan modifikasi dari tinju dan kickboxing, dengan penekanan pada teknik berdiri. Beberapa aturan seperti clinching (kuncian), teknik sikut (elbow), dan swapping (tukar pukulan) ditiadakan, menjadikan pertandingan lebih cepat, bersih, dan atraktif. Sebanyak 11 pertarungan sengit tersaji sepanjang malam, menyuguhkan ketegangan dan kejutan sejak awal hingga akhir.\r\n\r\nPertarungan pembuka langsung menyuguhkan ketegangan tinggi saat Prastio mengalahkan Samuel Tehuayo melalui TKO di ronde pertama. Suasana semakin panas ketika duel antara Jordan Boy dan Ammarul dimenangkan Ammarul melalui keputusan bulat. Namun, salah satu titik klimaks malam itu terjadi dalam perebutan gelar WBC Youth Championship, ketika Surya \"The Pretty Boy\" Dharma tampil mengagumkan dengan kemenangan TKO ronde pertama atas Puriwat Taousuwat. Aksi dominan itu tak hanya memantapkan posisi Surya sebagai rising star, tetapi juga membuktikan bahwa petarung muda Indonesia mampu bersaing di level Asia.\r\n\r\nTidak kalah menarik, duel antara petarung nasional Felmy Sumaehe melawan veteran Jepang Tenkai Tsunami berakhir dengan split decision yang memicu banyak perdebatan. Tenkai akhirnya dinyatakan sebagai pemenang dan membawa pulang sabuk WBA Asia Title. Pertarungan ini menjadi contoh nyata betapa tipisnya perbedaan antara kemenangan dan kekalahan dalam pertarungan teknik tinggi. Tak hanya itu, Andi Cobra juga menorehkan kemenangan besar dengan merebut gelar ICB National Title setelah menumbangkan Jaden Bahtera via TKO.\r\n\r\nMalam itu penuh kejutan. Beberapa petarung unggulan dari akademi yang diasuh oleh influencer dan pelatih ternama Celloszxz, justru tumbang secara mengejutkan. Brian Lawitan dikalahkan oleh Kabilan Jelevan, sedangkan Jemz Mokoginta terkena KO dari petarung Thailand, Pawitchaya Ruangchutiphophan. Kejutan ini menyulut reaksi dari para penonton yang tak menyangka anak-anak emas Cellos bisa jatuh begitu cepat. Sementara itu, atmosfer semakin memuncak menjelang pertarungan utama yang mempertemukan dua nama besar, Aziz \"The Crauser\" Calim dan Jekson \"KKAjhe\" Karmela di kelas 61 kg.\r\n\r\nPertarungan puncak antara Aziz Calim dan KKAjhe menjadi klimaks sempurna dari malam tersebut. Di ronde pertama dan awal ronde kedua, KKAjhe tampil agresif dan sempat menggoyahkan Aziz dengan kombinasi pukulan eksplosif. Namun, momen krusial terjadi di ronde dua ketika Aziz berhasil menjatuhkan KKAjhe lewat pukulan ke arah rusuk, sebuah knockdown yang sempat menuai kontroversi. Meski sebagian penonton mengira itu hanya slip, tayangan ulang memperlihatkan kontak yang bersih. Aziz kemudian menjaga kontrol penuh hingga ronde kelima dengan pertahanan disiplin dan serangan terukur, memaksa KKAjhe lebih banyak bertahan. Di akhir pertarungan, para juri memberikan keputusan bulat kepada Aziz, menjadikannya juara nasional resmi kick striking kelas 61 kg.\r\n\r\nDari statistik tidak resmi yang beredar, Aziz tercatat melepaskan 63 serangan dengan akurasi 65%, sedangkan KKAjhe melepaskan 78 serangan dengan akurasi 41%. Efisiensi dan kontrol tempo dari Aziz dianggap sebagai faktor kunci kemenangan. Duel ini pun dipuji luas oleh penggemar dan pengamat, bahkan banyak yang menyebutnya sebagai pertandingan terbaik sepanjang sejarah Byon Combat. Keberhasilan Aziz Calim membuka peluang baru baginya ke pentas internasional, bahkan manajernya mengungkap bahwa telah ada tawaran bertarung dari promotor Thailand dan Filipina.\r\n\r\nByon Combat 5 menjadi malam yang mengukir sejarah baru bagi dunia bela diri nasional. Dengan kualitas pertandingan yang meningkat tajam, kombinasi pertarungan lokal dan internasional, serta atmosfer penonton yang luar biasa, ajang ini menunjukkan bahwa Indonesia siap jadi tuan rumah untuk event bertaraf Asia bahkan dunia. Tidak hanya kemenangan Aziz, tetapi juga keseluruhan acara menjadi bukti bahwa combat sports kini telah menjadi panggung utama baru dalam hiburan olahraga Tanah Air.\r\n\r\n', 'artikel_1751484267.jpg', '2025-07-03 02:24:27'),
(27, 'Skirk, Mentor Childe yang Trending: Trailer \"Gelembung Sahabat\" dan Reaksi Pedas Komunitas', 'Dalam versi 4.2 Genshin Impact, HoYoverse memperkenalkan karakter Skirk secara resmi melalui sebuah trailer yang awalnya disambut antusias oleh komunitas. Namun, ekspektasi itu segera berubah menjadi perdebatan hangat ketika trailer menampilkan adegan di mana Skirk tersenyum lembut di tengah momen yang disebut para fans sebagai “gelembung sahabat”. Adegan ini dianggap terlalu ringan dan bertolak belakang dengan ekspektasi terhadap Skirk yang selama ini dikenal sebagai mentor tangguh Childe dari Abyss — sosok yang seharusnya keras, misterius, dan dipenuhi aura kelam.\r\n\r\nBanyak penggemar menyayangkan bagaimana Skirk yang selama ini dipersepsikan sebagai sosok petarung legendaris justru ditampilkan dengan gaya yang terlalu manis dan umum. Beberapa bahkan membandingkannya dengan karakter-karakter dari Honkai Impact yang penuh estetika \"waifu\", dan menganggap desain akhir Skirk terlalu generik, tidak mencerminkan kedalaman atau trauma masa lalunya di Abyss. Sebagian menyebut desainnya seperti \"gadis anime cantik biasa\", padahal sebelumnya komunitas membayangkan Skirk akan memiliki aura seperti “King Hassan” dari seri Fate, yang kelam dan kuat secara visual serta emosional.\r\n\r\nTidak hanya desain, pemilihan narasi dan tone dalam trailer juga menjadi sasaran kritik. Momen-momen seperti tersenyum sambil dikelilingi gelembung dianggap tidak mendukung latar belakang suram dan keras dari Abyss. Komunitas merasa HoYoverse terlalu menekankan sisi manis karakter perempuan tanpa memberikan kedalaman emosional yang cukup. Ada pula yang menilai perubahan ini sebagai langkah komersial untuk menarik penggemar karakter feminin, namun mengorbankan konsistensi cerita dan dunia Genshin.\r\n\r\nMeski begitu, tidak semua reaksi bersifat negatif. Sebagian komunitas membela Skirk, menyatakan bahwa banyak kritik terlalu berlebihan dan mengabaikan kemungkinan bahwa karakter tersebut memiliki sisi lembut di balik masa lalunya yang keras. Mereka berpendapat bahwa karakter perempuan tidak harus selalu tampil maskulin atau suram untuk dianggap kuat, dan bahwa momen ringan tidak berarti Skirk kehilangan kedalaman atau ketegasan. Beberapa pemain justru menilai bahwa trailer tersebut menunjukkan sisi kemanusiaan dari Skirk, memberi warna baru dalam dinamika Genshin yang selama ini sering menampilkan karakter petarung dengan satu sisi saja.\r\n\r\nNamun demikian, kritik terhadap trailer tetap berlanjut terutama dari para pemain veteran yang telah lama menantikan kemunculan Skirk. Mereka mengharapkan adegan trailer yang lebih kelam, berisi konflik batin atau gambaran pertarungan brutal, bukan sekadar estetika ringan. Untuk menjawab keresahan ini, HoYoverse kemudian merilis video pendek tambahan berjudul “Star Odyssey”, yang menampilkan latar belakang Skirk secara lebih mendalam. Dalam video tersebut, ia diperlihatkan sebagai penyusup dari dunia lain yang hidup dalam kehampaan, memiliki kenangan masa kecil yang penuh luka, dan beban emosional besar yang membentuknya.\r\n\r\nSetelah versi 5.7 resmi dirilis dan Skirk menjadi karakter playable, banyak pemain mulai mengubah pandangan mereka. Skirk hadir sebagai pengguna elemen Cryo dengan pedang, memiliki mekanisme bertarung unik bernama “Serpent’s Subtlety” yang memungkinkan gaya bertarung ganda dengan efisiensi tinggi. Gameplay-nya menunjukkan sisi tajam dari karakter ini, dan banyak yang mulai menyadari bahwa trailer sebelumnya hanya menunjukkan sepotong kecil dari kepribadiannya. Seiring waktu, kritik terhadap “gelembung sahabat” mulai mereda, tergantikan oleh apresiasi terhadap kompleksitas Skirk di dalam permainan.\r\n\r\n', 'artikel_1751485303.png', '2025-07-03 02:41:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_foto`
--

CREATE TABLE `tbl_foto` (
  `id_foto` int(11) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_foto`
--

INSERT INTO `tbl_foto` (`id_foto`, `judul`, `foto`) VALUES
(8, 'With My Friend', 'WhatsApp Image 2025-07-02 at 21.11.42_4f018ee0.jpg'),
(9, 'With My Friend', 'WhatsApp Image 2025-07-02 at 21.11.42_8877c2b7.jpg'),
(10, 'With My Friend', 'WhatsApp Image 2025-07-02 at 21.11.49_c50cb34a.jpg'),
(11, 'Mt. Bromo', 'WhatsApp Image 2025-07-02 at 21.11.42_6cfbed75.jpg'),
(12, 'My Hobby', 'WhatsApp Image 2025-07-02 at 21.11.51_7b2d7bab.jpg'),
(13, 'Gedegedi Gedageda O', 'WhatsApp Image 2025-07-02 at 21.11.48_05d4db3b.jpg'),
(14, 'Alone ', 'WhatsApp Image 2025-07-02 at 21.11.43_8ed00ad0.jpg'),
(15, 'With Family', 'WhatsApp Image 2025-07-02 at 21.11.44_6a5e8fac.jpg'),
(16, 'Cukurukuk', 'WhatsApp Image 2025-07-02 at 21.11.49_4d894586.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_gallery`
--

CREATE TABLE `tbl_gallery` (
  `id_gallery` int(5) NOT NULL,
  `judul` text NOT NULL,
  `foto` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_gallery`
--

INSERT INTO `tbl_gallery` (`id_gallery`, `judul`, `foto`) VALUES
(1, 'Membuat Form Pendaftaran Seminar Digital Marketing ', 'FormPendaftaran.jpg'),
(2, 'Membuat Website Toko Online Menggunakan PHP Database MySQL ', 'Toko Online.jpg'),
(3, 'Membuat Web Personal Mengguanakan Html, Css, Dan Javascript', 'Portofolio.jpg'),
(4, 'Membuat Website Sederhana Dengan Tema Game', 'Game.jpg'),
(5, 'Membuat Web Personal Sederhana Menggunakan PHP MySQL', 'WhatsApp Image 2025-06-15 at 02.01.33_3c0705c7.jpg'),
(10, 'Membuat Dasboard Admin Yang Simpel Namun Objektif', 'Screenshot (23).png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_pengunjung`
--

CREATE TABLE `tbl_pengunjung` (
  `id_pengunjung` int(11) NOT NULL,
  `waktu_kunjungan` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_user`
--

CREATE TABLE `tbl_user` (
  `username` varchar(10) NOT NULL,
  `password` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_user`
--

INSERT INTO `tbl_user` (`username`, `password`) VALUES
('Admin1', 'admin1'),
('Rehan', 'rehan1');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tbl_about`
--
ALTER TABLE `tbl_about`
  ADD PRIMARY KEY (`id_about`);

--
-- Indeks untuk tabel `tbl_artikel`
--
ALTER TABLE `tbl_artikel`
  ADD PRIMARY KEY (`id_artikel`);

--
-- Indeks untuk tabel `tbl_foto`
--
ALTER TABLE `tbl_foto`
  ADD PRIMARY KEY (`id_foto`);

--
-- Indeks untuk tabel `tbl_gallery`
--
ALTER TABLE `tbl_gallery`
  ADD PRIMARY KEY (`id_gallery`);

--
-- Indeks untuk tabel `tbl_pengunjung`
--
ALTER TABLE `tbl_pengunjung`
  ADD PRIMARY KEY (`id_pengunjung`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tbl_about`
--
ALTER TABLE `tbl_about`
  MODIFY `id_about` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `tbl_artikel`
--
ALTER TABLE `tbl_artikel`
  MODIFY `id_artikel` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `tbl_foto`
--
ALTER TABLE `tbl_foto`
  MODIFY `id_foto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `tbl_gallery`
--
ALTER TABLE `tbl_gallery`
  MODIFY `id_gallery` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `tbl_pengunjung`
--
ALTER TABLE `tbl_pengunjung`
  MODIFY `id_pengunjung` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
