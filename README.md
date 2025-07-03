# Web Personal 
Deskripsi :

Studi kasus ini bertujuan untuk mengembangkan sebuah aplikasi web personal yang bersifat dinamis, memungkinkan pemilik situs untuk mengelola konten secara mandiri melalui halaman admin yang telah disediakan. Aplikasi ini dibangun menggunakan bahasa pemrograman PHP dan memanfaatkan database MySQL untuk penyimpanan data. Dari sisi tampilan, antarmuka dirancang dengan Tailwind CSS guna menciptakan desain yang modern, responsif, dan mudah untuk dikustomisasi sesuai kebutuhan.
Website ini memiliki dua bagian utama:

1. Halaman Publik, yang dapat diakses oleh semua pengunjung.
2. Halaman Admin, yang hanya dapat diakses setelah login, digunakan untuk mengelola konten.

Fitur-fitur

1. Login & Logout (Halaman login admin dengan validasi, Sistem sesi untuk melindungi halaman admin, Logout untuk mengakhiri sesi dengan aman)
2. Manajemen Artikel (Tambah artikel (judul + isi + tanggal + foto), Edit artikel yang sudah ada, Hapus artikel, Tampilkan daftar artikel di halaman utama, Sidebar "Daftar Artikel" yang terupdate otomatis)
3. Manajemen Gallery Foto (Upload gambar beserta judul, Ganti gambar dan judul yang sudah ada, Hapus gambar, Tampilkan gambar di halaman galeri publik dalam grid responsif)
4. Manajemen About (Tambah deskripsi tentang diri, Edit dan hapus bagian “About”, Tampilkan deskripsi di halaman publik about.php)
5. Dashboard Statistik Admin (Menampilkan ringkasan jumlah: Artikel & Gambar di gallery)
6. Manajement Portofolio Project (Tambah Project (Foto Project + Deskripsi Project) Hapus Project, Edit Project, Tampilan di halaman Portofolio Project Terupdate secara otomatis)
7. Halaman Publik yang Rapi & Dinamis (Artikel terbaru ditampilkan otomatis, ada fitur readmore sebagai fitur tambahan di artikel, Galeri responsif dan ringan, Tentang Saya tampil dengan struktur informatif)
8. Mode Darkmode di Halaman Baackend 

Teknologi yang Digunakan

1. Bahasa Pemrograman : PHP
2. Database : MySQL
3. Frontend : Tailwind CSS, HTML, JavaScript
4. Server Side : Apache / XAMPP

Struktur Folder :
![WhatsApp Image 2025-07-03 at 04 12 21_7afa953b](https://github.com/user-attachments/assets/63ec8a30-236f-4ba1-99c0-6e64be56565c)



 # User Interface : Front End

A. Halaman Blog - Artikel
 1. Header Menampilkan Ucapan Selamat Datang Kepada Pengunjung
    ![image](https://github.com/user-attachments/assets/bc815280-5eaf-4770-862a-5a7c0cd89815)
 2. Menampilkan Artikel Ketika Scroll Kebawah 
    ![image](https://github.com/user-attachments/assets/f24a075b-f93f-4c6e-b4e4-f3d13207d217)
 3. Ketika Pengunjung menekan/mengklik "Readmore" pada bagian artikel, Maka Akan di arahkan ke bagian isi artikel
    ![image](https://github.com/user-attachments/assets/596fc2be-05bc-45ba-a280-8eb30e2d072f)
 Note : Artikel Terbaru di tandai dengan "Feature blog" Sedangkan yang sebelumnya di tandai dengan "Latest Post
 4. Footter

    
B. Halaman Blog - Gallery
  1. Header Menampilkan Keterangan Bahwa Ini adalah halaman Galerry
     ![image](https://github.com/user-attachments/assets/7a3f428b-5381-40f1-b1d4-026f337d870c)
  2. Menampilkan foto-foto dari Galerry
     ![image](https://github.com/user-attachments/assets/99cc971e-2ba7-438a-b3b4-880a96bc47f4)
  3. Footter


C. Halaman Project - Portofolio
  1. Header Menampilkan Keterangan Bahwa Ini adalah halaman Portofolio
     ![image](https://github.com/user-attachments/assets/a311d1b5-46af-4d73-b391-5be45fe9b689)
  2. Menampilkan Project-Project Ketika di scroll ke bawah 
     ![image](https://github.com/user-attachments/assets/be2a9f94-bf87-4b0b-9290-37f0879d67a3)
  3. Footter


D. Halaman About - About Us
  1. Header Menampilkan Keterangan Bahwa Ini adalah halaman About
     ![image](https://github.com/user-attachments/assets/bd7f83e4-249e-428a-a2b5-4a915aadef44)
  2. Halaman About yang menampilkan deskripsi tentang saya atau profile dari masing-masing mahasiswa.
     ![image](https://github.com/user-attachments/assets/9fa6ee6b-6747-4f27-a642-64f15a812cd8)
  3. Footter


E. Halaman Login 
  1. Halaman Login adalah halaman yang digunakan untuk mengakses halaman admin, diperlukan username dan password.
  2. Penambahan Fitur Daftar / Register
     ![image](https://github.com/user-attachments/assets/9bc3e292-c49b-457d-9ebe-bab094d0b123)



# User Interface : Back End


A. Halaman Beranda Admin
  1. Menampilakan Data Dari Semua Proses Yang Terjadi Seperti Berapa Jumlah Data, Jenisnya Apa, History Penambahan Data, Dan Juga Diagram Statistik Perbandingan Jumlah Data.
     ![image](https://github.com/user-attachments/assets/d3e2b7fe-ef47-486f-ba88-8faad192aa16)
     Ditambahkan juga beberapa animasi 3D animation agar tidak terlalu boring.


B. Halaman Kelola Blog - Artikel 
  1. Menampilkan Data Artikel Yang Telah Ditambahkan dan akan di tampilkan di Frontend Blog - Artikel
     ![image](https://github.com/user-attachments/assets/ae044d78-680a-447a-8a3f-5feaa624c75d)
  2. Ketika di tekan "Tambah Artikel" Akan Muncul Tampilan Seperti ini
     ![image](https://github.com/user-attachments/assets/a5e15388-c657-4098-8979-5c0bf9f5cab4)
  3. Begitu Pula dengan "Edit" Untuk Mengedit Artikel yang kita telah tambahkan
     ![image](https://github.com/user-attachments/assets/293442a9-bb37-4f49-bd67-dc993be1a80e)

Note : Tanggal Perilisan tetap sesuai dengan tanggal pembuatan artikel di hari itu juga, jadi misalkan kita menambahkan artikel di tanggal 2 Jully, Maka, si Tanggal Akan tetap berada pada Tanggal 2 Jully        pada saat kita edit artikel juga sama, tidak bisa di ubah, dan si tanggal akan otomatis menyesuaikan tanggal perilisan pada hari dimana kita menambahkan artikelnya secara otomatis, walaupun tidak ada di dalam halaman menu tambah artikel si atur tanggalnya.


C. Halaman Kelola Blog - Gallery
  1. Menampilkan Data Foto-foto yang di tambahkan ke gallery
     ![image](https://github.com/user-attachments/assets/b6c8c330-8161-4b67-8932-28389fd34915)
  2. Ini Halaman Add Foto-fotonya
     ![image](https://github.com/user-attachments/assets/1c90a5c3-5f81-4517-83bc-0f0c350ae1d4)
  3. Dan Ini Halaman Edit Foto-fotonya
     ![image](https://github.com/user-attachments/assets/fd194aa0-fb06-43b7-9c18-9b2a2e98e241)



D Halaman Kelola Project - Portofolio
  1. Ini Adalah halaman untuk menyimpa data di Halaman Project
     ![image](https://github.com/user-attachments/assets/7c86c02d-219b-4465-ab81-501329356b21)
  2. Ini Halaman Add Projectnya
     ![image](https://github.com/user-attachments/assets/895557b8-caca-4f9b-be94-c9dea9ce862f)
  3. Dan ini adalah halaman Edit Projectnya
     ![image](https://github.com/user-attachments/assets/e21bf18f-bc26-4437-82c8-0bf2cb2697c4)



D. Halaman Kelola About - Profil
  1. Menampilkan data diri yang akan ditampilkan ke ke Frontend About
     ![image](https://github.com/user-attachments/assets/2a7154af-56e0-442b-8a86-6f8565568284)
  2. Ini adalah halaman Add Profil-nya
     ![image](https://github.com/user-attachments/assets/10fae2e0-6b68-4828-9c81-b09668970aaf)
  3. dan ini adalah halaman Edit Profil-nya
     ![image](https://github.com/user-attachments/assets/a83d9029-78b8-4d8f-bf6c-d7ddba0afa8d)


# Penambahan Fitur :

1. Fitur Dark Mode di Halaman Backend
2. Fitur Auto Create Tanggal Di Kelola Artikel (Tanggal Pembuatan Di Saat Artikel Dibuat) Otomatis. Fitur ini ada di Backend Kelola Atikel
3. Statistik Tambahan Di Dasboard Admin Seperti, Statistik Jumlah : Foto dan Pengunjung
4. Diagram Yang Menampilkan Perbandingan Antar Data-data.
5. Tambahkan Alamat Secara Otomatis, saat di klik "Gunakan Lokasi Saat Ini" Otomatis akan langsung membuat link, kordinat, dan juga alamatmya. Fitur Ini ada di Backend Kelola Profil
6. History / Log Aktivitas
7. Fitur Readmore, Dibuat untuk pengunjung bisa dengan nyaman membaca artikel tanpa menganggu artikel lain.
8. Register Akun
9. dll




     








     








    


