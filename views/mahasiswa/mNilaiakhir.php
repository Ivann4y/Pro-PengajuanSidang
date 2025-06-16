<!-- Argha arybawa pasha -->
<?php
    // --- BLOK PHP ---
    // Kode PHP dieksekusi di server sebelum halaman dikirim ke browser.

    // Membuat sebuah array asosiatif untuk menyimpan data mahasiswa sebagai placeholder.
    // Dalam aplikasi nyata, data ini biasanya akan diambil dari database.
    $mahasiswa = [
        'nama'  => 'M. Haaris Nur S.', // Kunci 'nama' dengan nilai string nama mahasiswa
        'nim'   => '0920240033',       // Kunci 'nim' dengan nilai string NIM mahasiswa
        'prodi' => 'Teknik Informatika' // Kunci 'prodi' dengan nilai string program studi
    ];
?>

<!DOCTYPE html> <!-- Mendeklarasikan bahwa dokumen ini adalah HTML5 -->
<html lang="en"> <!-- Elemen root dari halaman HTML, dengan atribut bahasa "English" -->
  <head>
    <!-- Bagian <head> berisi metadata dan link ke resource eksternal, tidak terlihat di halaman -->
    <meta charset="UTF-8" /> <!-- Menentukan set karakter yang digunakan adalah UTF-8 (standar universal) -->
    <meta name="viewport" content="width=device-width, initial-scale=1" /> <!-- Membuat halaman menjadi responsif agar tampil baik di berbagai perangkat -->
    
    <!-- === STYLESHEETS & FONTS === -->
    <!-- Link ke stylesheet Bootstrap dari CDN untuk styling dasar dan komponen UI -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <!-- Link ke file JavaScript Bootstrap (termasuk Popper.js) untuk fungsionalitas seperti dropdown, modal, dll. -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Link ke stylesheet Bootstrap Icons untuk menggunakan ikon-ikon dari Bootstrap -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <!-- Link ke Google Fonts untuk memuat font "Poppins" -->
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins&display=swap"
      rel="stylesheet"
    />
    <!-- Link tambahan ke Google Fonts untuk berbagai ketebalan (weight) dari font "Poppins" -->
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
    <!-- Link ke file JavaScript SweetAlert2 untuk membuat notifikasi pop-up yang menarik -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Link ke stylesheet kustom lokal (style.css) -->
    <link rel="stylesheet" href="../../css/style.css" />
    <!-- Link ke stylesheet kustom lokal tambahan (extra/style.css) -->
    <link rel="stylesheet" href="../../extra/style.css" />
    <!-- Link ke stylesheet Font Awesome dari CDN untuk menggunakan library ikon yang lebih beragam -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <title>Mahasiswa - Nilai Akhir</title> <!-- Judul yang akan muncul di tab browser -->

<!-- === CSS KUSTOM UNTUK HALAMAN INI === -->
<style>
    /* Mengatur font default dan warna teks untuk elemen-elemen utama di halaman. */
    /* '!important' digunakan untuk memastikan style ini mengalahkan style lain (misal dari Bootstrap). */
    body,
    .card,
    .form-control,
    h1, h2, h3, h4, h5, h6 {
        font-family: "Poppins", sans-serif !important;
        color: #464869;
    }

    /* Style untuk judul utama halaman */
    .main-title {
        font-weight: 700 !important; /* Ketebalan font tebal */
        color: #343a40; /* Warna teks gelap */
        margin-bottom: 0.5rem; 
    }
    
    /* Style untuk teks informasi mahasiswa (seperti nama, nim, dll) */
    .student-info {
        font-size: 1rem;
        color: #6c757d; /* Warna abu-abu */
        font-weight: 500; /* Ketebalan medium */
    }
    
    /* Style untuk kartu catatan */
    #cardcatatan {
        background-color: rgb(235, 238, 245); /* Warna latar belakang biru keabu-abuan muda */
        border-radius: 20px; /* Sudut yang lebih tumpul */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Efek bayangan halus */
        margin-left: 10px;
    }

    /* Style untuk kartu nilai */
    #cardNilai {
        background-color: rgb(235, 238, 245);
        border-radius: 50px; /* Sudut sangat tumpul (membentuk kapsul jika tinggi) */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        margin-left: 10px;
    }
    
    /* Style untuk kartu data mahasiswa */
    #carddataMahasiswa {
        background-color: rgb(235, 238, 245);
        border-radius: 50px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        margin-left: 10px;
    }

    /* Memastikan kartu catatan memiliki lebar penuh */
    #cardcatatan {
        width: 100%;
    }

    /* Style untuk grup info (Label dan Value) di kartu data mahasiswa */
    .info-group, .value-row {
        font-size: 1rem;
    }
    
    /* Utility class untuk membuat teks lebih tebal */
    .fw-bold {
        font-weight: 600 !important;
    }
    
    /* Blok untuk styling responsif. Aturan di dalam sini hanya berlaku jika lebar layar 992px atau kurang. */
    @media (max-width: 992px) {
        /* (Kosong, bisa diisi style untuk tablet atau mobile) */
    }
    
    /* Style untuk input yang menampilkan nilai huruf (A, B, C, dll) */
    #nilaiMahasiswa {
        font-size: 9.5rem !important; /* Ukuran font sangat besar */
        font-weight: bold; /* Teks tebal */
        text-align: center; /* Teks di tengah */
        border-radius: 30px;
        width: 90%;
        /* Menghilangkan tampilan default dari input field */
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0;
    }
    
    /* Style untuk area konten di dalam kartu catatan */
    #catatan-content {
        padding: 1rem;
        font-size: 1rem;
    }

    /* Style untuk tombol "Kembali" */
    .btn-kembali {
        background-color: #4B68FB; /* Warna latar biru */
        color: white; /* Warna teks putih */
        border: none; /* Tanpa border */
        border-radius: 20px; /* Sudut tumpul */
        padding: 10px 25px; /* Jarak dalam (atas/bawah, kiri/kanan) */
        cursor: pointer; /* Mengubah kursor menjadi tangan saat dihover */
        font-size: 0.95rem;
        font-weight: 500;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease; /* Efek transisi halus */
        display: inline-flex; /* Membuat elemen berperilaku seperti inline tapi bisa diatur layout flexbox */
        align-items: center; /* Menyelaraskan item di dalamnya secara vertikal */
        margin-top: 1.2cm;
        margin-left: 15px;
    }

    /* Style untuk tombol "Kembali" saat di-hover mouse */
    .btn-kembali:hover {
        position: relative;
        background-color: white; /* Latar menjadi putih */
        color: #4B68FB; /* Teks menjadi biru */
    }
    
    /* Style untuk lingkaran ikon di dalam tombol */
    .btn-kembali .icon-circle {
        display: inline-flex;
        align-items: center; /* Tengah vertikal */
        justify-content: center; /* Tengah horizontal */
        width: 30px; 
        height: 30px; 
        background-color: white; /* Latar putih */
        border-radius: 50%; /* Bentuk lingkaran sempurna */
        margin-right: 10px; /* Jarak dengan teks "Kembali" */
        transition: background-color 0.3s ease;
    }

    /* Style untuk lingkaran ikon saat tombol di-hover */
    .btn-kembali:hover .icon-circle {
        background-color: #4B68FB; /* Latar menjadi biru */
    }

    /* Style untuk ikon di dalam lingkaran */
    .btn-kembali .icon-circle i {
        color: #4B68FB; /* Warna ikon biru */
        font-size: 1rem; 
        transition: color 0.3s ease;
    }

    /* Style untuk ikon saat tombol di-hover */
    .btn-kembali:hover .icon-circle i {
        color: white; /* Warna ikon menjadi putih */
    }
</style>
  </head>
  <body>
    <!-- Container utama untuk layout sidebar dan konten -->
    <div id="NavSide">
        <!-- === SIDEBAR NAVIGASI KIRI === -->
        <div id="main-sidebar" class="NavSide__sidebar">
            <!-- Logo di bagian atas sidebar -->
             <div class="NavSide__sidebar-brand img ">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
            </div>
            <!-- Daftar menu navigasi -->
            <ul class="NavSide__sidebar-nav">
                <!-- Item menu "Detail Pengajuan" -->
                <li class="NavSide__sidebar-item ">
                    <b></b><b></b>
                    <a href="MdetailSidang.php"><span class="NavSide__sidebar-title fw-semibold">Detail Pengajuan</span></a>
                </li>
                <!-- Item menu "Perbaikan" -->
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mPerbaikan.php"><span class="NavSide__sidebar-title fw-semibold">Perbaikan</span></a>
                </li>
                <!-- Item menu "Nilai Akhir" (aktif) -->
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"> <!-- Kelas '...--active' menandakan halaman ini yang sedang dibuka -->
                    <b></b><b></b>
                    <a href="mNilaiakhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a>
                </li>
            </ul>
        </div>

        <!-- === TOPBAR / HEADER === -->
        <div class="NavSide__topbar">
            <!-- Ikon "hamburger" untuk membuka/menutup sidebar di tampilan mobile -->
             <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <!-- Ikon-ikon di sisi kanan topbar -->
            <div class="header-icons">
                <i class="bi bi-bell-fill"></i> <!-- Ikon notifikasi -->
                <div class="profile-icon">
                    <i class="bi bi-person-fill fs-5"></i> <!-- Ikon profil pengguna -->
                </div>
            </div>
        </div>

   <!-- === KONTEN UTAMA HALAMAN === -->
   <main class="NavSide__main-content">
    <!-- Menggunakan container-fluid dari Bootstrap agar lebar konten penuh -->
    <div class="container-fluid">
        <!-- Baris untuk judul halaman -->
        <div class="row mb-4 title-container">
            <div class="col-12">
                <h2 class="main-title">
                    Mahasiswa / Detail Evaluasi - Sistem Pengajuan Sidang
                </h2>
            </div>
        </div>
        
        <!-- Baris untuk kartu data dan nilai -->
        <div class="row mt-4 g-4">
            <!-- Kolom untuk Kartu Data Mahasiswa (setengah lebar di layar besar) -->
            <div class="col-lg-6 d-flex">
              <div class="card flex-fill" id="carddataMahasiswa">
                <div class="card-body card-soft p-4">
                  <h3 class="card-title text-dark mb-4 text-center">Data Mahasiswa</h3>
                  <!-- Baris di dalam kartu untuk membagi info jadi dua kolom -->
                  <div class="row">
                      <!-- Kolom kiri untuk NIM dan Nama -->
                      <div class="col-sm-6">
                           <!-- Info NIM -->
                          <div class="info-group mb-5">
                            <div class="label-row d-flex align-items-center gap-2 mb-1">
                              <i class="fa-solid fa-id-card"></i> <!-- Ikon kartu identitas -->
                              <span class="fw-bold">NIM</span>
                            </div>
                            <div class="value-row text-secondary fw-bold">0920240033</div>
                          </div>
                          <!-- Info Nama -->
                          <div class="info-group mb-3">
                            <div class="label-row d-flex align-items-center gap-2 mb-1">
                              <i class="fa-solid fa-user"></i> <!-- Ikon pengguna -->
                              <span class="fw-bold">Nama</span>
                            </div>
                            <div class="value-row text-secondary fw-bold">M. Harris Nur S.</div>
                          </div>
                      </div>
                      <!-- Kolom kanan untuk Mata Kuliah dan Dosen -->
                      <div class="col-sm-6">
                           <!-- Info Mata Kuliah -->
                          <div class="info-group mb-5">
                            <div class="label-row d-flex align-items-center gap-2 mb-1">
                              <i class="fa-solid fa-book"></i> <!-- Ikon buku -->
                              <span class="fw-bold">Mata Kuliah</span>
                            </div>
                            <div class="value-row text-secondary fw-bold">Tugas Akhir</div>
                          </div>
                          <!-- Info Dosen Pembimbing -->
                          <div class="info-group mb-3">
                            <div class="label-row d-flex align-items-center gap-2 mb-1">
                              <i class="fa-solid fa-user-tie"></i> <!-- Ikon dosen -->
                              <span class="fw-bold">Dosen Pembimbing</span>
                            </div>
                            <div class="value-row text-secondary fw-bold">Timotius Victory</div>
                          </div>
                      </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Kolom untuk Kartu Nilai Mahasiswa -->
            <div class="col-lg-6 d-flex">
                <div class="card flex-fill" id="cardNilai">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title text-dark text-center">Nilai Mahasiswa:</h3>
                        <!-- Container untuk menengahkan nilai -->
                        <div class="d-flex justify-content-center align-items-center flex-grow-1">
                            <!-- Input field untuk menampilkan nilai. 'readonly' agar tidak bisa diubah oleh pengguna. -->
                            <input type="text" class="form-control text-dark"
                                id="nilaiMahasiswa" value="A" readonly />
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Baris untuk kartu catatan -->
        <div class="row mt-5 ">
            <div class="col-12">
                <div class="card" id="cardcatatan">
                    <div class="card-body">
                        <h3 class="card-title text-dark" >Catatan:</h3>
                        <div class="text-dark" id="catatan-content">
                            Tidak ada catatan. <!-- Teks catatan dari dosen -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Baris untuk tombol "Kembali" -->
        <div class="row mt-5">
            <div class="col-auto"> 
                <!-- Tombol yang saat diklik akan mengarahkan ke halaman 'mSidang.php' -->
                <button class="btn-kembali" onclick="location.href='mSidang.php'">
                    <span class="icon-circle">
                        <i class="fa-solid fa-arrow-left"></i> <!-- Ikon panah kiri -->
                    </span>
                    Kembali
                </button>
            </div>
        </div>
    </div>
</main>
    
    <!-- Memuat jQuery dari CDN. Diperlukan untuk beberapa fungsionalitas Bootstrap dan script custom. -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- === JAVASCRIPT KUSTOM UNTUK HALAMAN INI === -->
    <script>
    // Script ini menangani interaksi pada sidebar navigasi.

    // 1. Fungsionalitas Toggle Sidebar untuk Mobile
    // Memilih elemen tombol toggle (hamburger menu)
    let menuToggle = document.querySelector(".NavSide__toggle");
    // Memilih elemen sidebar utama
    let sidebar = document.getElementById("main-sidebar");

    // Menambahkan event listener 'click' pada tombol toggle
    menuToggle.onclick = function () {
        // Menambah/menghapus kelas '...--active' pada tombol, biasanya untuk mengubah ikon (dari hamburger ke 'x')
        menuToggle.classList.toggle("NavSide__toggle--active");
        // Menambah/menghapus kelas '...--active-mobile' pada sidebar untuk menampilkan atau menyembunyikannya
        sidebar.classList.toggle("NavSide__sidebar--active-mobile");
    };

    // 2. Fungsionalitas untuk Menandai Item Menu yang Aktif
    // Memilih semua item menu di sidebar
    let listItems = document.querySelectorAll(".NavSide__sidebar-item");
    // Melakukan loop (iterasi) untuk setiap item menu
    for (let i = 0; i < listItems.length; i++) {
        // Menambahkan event listener 'click' pada setiap item menu
        listItems[i].onclick = function () {
            // Cek jika item yang diklik belum memiliki kelas '...--active'
            if(!this.classList.contains("NavSide__sidebar-item--active")) {
                // Hapus kelas '...--active' dari SEMUA item menu
                for (let j = 0; j < listItems.length; j++) {
                    listItems[j].classList.remove("NavSide__sidebar-item--active");
                }
                // Tambahkan kelas '...--active' HANYA ke item yang baru saja diklik
                this.classList.add("NavSide__sidebar-item--active");
            }
        };
    }
    </script>
  </body>
</html>