<?php
    // Ambil parameter dari URL
    $nim = isset($_GET['nim']) ? $_GET['nim'] : 'N/A';
    $tipe = isset($_GET['tipe']) ? $_GET['tipe'] : 'N/A';

    $mahasiswa = [];

    if ($tipe === 'TA') {
        $mahasiswa = [
            'nama' => 'M. Haaris Nur S.',
            'nim' => '0920240033',
            'mata_kuliah' => 'Tugas Akhir',
        ];
    } elseif ($tipe === 'Semester') {
        $mahasiswa = [
            'nama' => 'M. Harris Nur S.',
            'nim' => '0920240033',
            'mata_kuliah' => 'Pemrograman 2',
            'judul_sidang' => 'Sistem Pengajuan Sidang'
        ];
    } else {
        // Data default jika tipe tidak dikenali
        $mahasiswa = [
            'nama' => 'Data Tidak Ditemukan',
            'nim' => 'N/A',
            'mata_kuliah' => 'N/A',
            'file_laporan' => '#',
            'file_pendukung' => '#'
        ];
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Sidang</title> <!-- Judul halaman diubah -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: #ffffff;
        }

        #NavSide {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        .NavSide__sidebar-brand {
            padding: 10% 5% 50% 5%;
            text-align: center;
        }

        .NavSide__sidebar-brand img {
            width: 90%;
            max-width: 180px;
            height: auto;
            display: inline-block;
        }

        .NavSide__sidebar {
            position: fixed;
            top: 0px;
            left: 0px;
            bottom: 0px;
            width: 280px;
            border-radius: 1px;
            box-sizing: border-box;
            border-left: 5px solid rgb(67, 54, 240);
            background: rgb(67, 54, 240);
            overflow-x: hidden;
            overflow-y: auto;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.5s ease-in-out, width 0.5s ease-in-out;
        }

        .NavSide__sidebar-nav {
            width: 100%;
            padding-left: 0;
            padding-top: 0;
            list-style: none;
            flex-grow: 1;
        }

        .NavSide__sidebar-item {
            position: relative;
            display: block;
            width: 100%;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
        }

        .NavSide__sidebar-item a {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            text-decoration: none;
            color: rgb(252, 252, 252);
            padding: 5% 2%;
            height: 60px;
            box-sizing: border-box;
        }

        .NavSide__sidebar-title {
            white-space: normal;
            text-align: center;
            line-height: 1.5;
            color:white;
        }

        /* UBAH: Active state di pindah ke Evaluasi */
        .NavSide__sidebar-item.NavSide__sidebar-item--active {
            background: #ffffff;
=        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active a {
            color: rgb(67, 54, 240) !important;
        }

        .NavSide__sidebar-item b:nth-child(1) {
            position: absolute;
            top: -20px;
            height: 20px;
            width: 100%;
            background: rgb(255, 255, 255);
            display: none;
        }
        .NavSide__sidebar-item b:nth-child(1)::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-bottom-right-radius: 20px;
            background: rgb(67, 54, 240);
            display: block;
        }
        .NavSide__sidebar-item b:nth-child(2) {
            position: absolute;
            bottom: -20px;
            height: 20px;
            width: 100%;
            background: rgb(255, 255, 255);
            display: none;
        }
        .NavSide__sidebar-item b:nth-child(2)::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-top-right-radius: 20px;
            background: rgb(67, 54, 240);
            display: block;
        }
        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(1),
        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(2) {
            display: block;
        }

        .NavSide__main-content {
            flex-grow: 1;
            padding: 20px 20px 20px calc(20px + 1cm); 
            margin-left: 280px;
            overflow-y: auto;
            transition: margin-left 0.5s ease-in-out;
        }

        /* --- Modifikasi Margin Global --- */
        .NavSide__main-content h2 { 
            margin-bottom: 1.2cm;
            font-weight: 700; 
        }

        .NavSide__main-content h3 { /* Gaya baru untuk sub-judul seperti "Nilai Sidang (Sementara)" */
            font-weight: 700;
            font-size: 1.4rem; /* Sedikit lebih kecil dari h2 */
            margin-bottom: 0.2cm; 
        }

        .status-badge { 
            background-color:rgb(253, 68, 59); 
            color: white; 
            border-radius: 20px;
            padding: 8px 18px; 
            display: inline-block; 
            font-size: 0.875rem; 
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.08);
            font-weight: bold; 
            margin-bottom: 1.2cm; 
        }

        .info-card {
            position: relative;
            background: rgb(235, 238, 245); 
            border-radius: 30px; 
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05);
            padding: 25px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            overflow: hidden;
            transition: background-color 0.4s ease;
            margin-bottom: 1.2cm; 
        }

        .info-card::after { 
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 60px; 
            height: 100%;
            background-color: rgb(67, 54, 240);
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
            transition: width 0.4s ease;
            z-index: 0;
        }

        .info-card:hover::after {
            width: 100%;
            border-radius: 20px;
        }

        .info-card .section {
            flex: 0 0 48%; 
            z-index: 1;
            color: #333;
            transition: color 0.4s ease;
            display: flex; 
            flex-direction: column; 
            justify-content: space-between; 
        }

        .info-card:hover .section {
            color: white;
        }

        /* Styling untuk baris label dan nilai */
        .info-card .section .label-row {
            display: flex;
            align-items: center;
            margin-bottom: 0.25rem; 
            font-size: 1rem; 
        }

        .info-card .section .label-row i {
            margin-right: 10px; 
            color: #495057; 
            font-weight: 900; 
            transition: color 0.4s ease;
            width: 20px; 
            text-align: center;
        }

        .info-card:hover .section .label-row i {
            color: white;
        }

        .info-card .section .label-row .fw-bold {
            font-weight: 600; 
            font-size: 1.05rem; 
        }

        .info-card .section .value-row {
            margin-left: 30px; 
            line-height: 1.5;
            font-size: 0.95rem; 
            margin-bottom: 0; 
        }
       
        .btn-kembali {
            background-color: rgb(67, 54, 240);
            color: white; 
            border: none;
            border-radius: 20px;
            /* UBAH: Padding vertikal 0, sentralisasi dengan flexbox */
            padding: 0 25px; 
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease; 
            /* UBAH: Gunakan display: flex untuk konten internal */
            display: flex; 
            align-items: center; 
            justify-content: center; /* Pusat konten horizontal di dalam tombol */
            margin-top: 1.2cm; 
            height: 45px; 
        }
        .btn-kembali:hover {
            position: relative;
            background-color: white; 
            color: rgb(67, 54, 240); 
        }
        
        .btn-kembali .icon-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px; 
            height: 30px; 
            background-color: white; 
            border-radius: 50%;
            margin-right: 10px; 
            transition: background-color 0.3s ease; 
        }

        .btn-kembali:hover .icon-circle {
            background-color: rgb(67, 54, 240); 
        }

        /* --- PERBAIKAN WARNA PANAH DI SINI --- */
        .btn-kembali .icon-circle i {
            color: rgb(67, 54, 240); /* Warna ikon saat default */
        }

        .btn-kembali:hover .icon-circle i {
            color: white; /* Warna ikon saat hover */
        }
        /* --- AKHIR PERBAIKAN WARNA PANAH --- */

        /* --- CSS Baru untuk Tombol Berkas (dipertahankan tapi tidak digunakan di Evaluasi) --- */
        .file-button {
            display: inline-flex; 
            align-items: center;
            background-color: rgb(235, 238, 245); 
            border-radius: 20px; 
            padding: 12px 20px;
            margin-right: 15px; 
            margin-bottom: 15px; 
            text-decoration: none; 
            color: rgb(67, 54, 240); 
            font-weight: 500;
            font-size: 1rem;
            transition: background-color 0.2s ease, box-shadow 0.2s ease, color 0.2s ease; 
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05); 
        }

        .file-button:hover {
            background-color: rgb(67, 54, 240); 
            color: white; 
            text-decoration: none; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); 
        }

        .file-button i {
            font-size: 1.25rem; 
            margin-right: 10px; 
            color: rgb(67, 54, 240); 
            transition: color 0.2s ease; 
        }

        .file-button:hover i { 
            color: white;
        }

        /* Responsive adjustments for file buttons */
        @media (max-width: 576px) {
            .file-button {
                width: 100%; 
                display: flex; 
                margin-right: 0; 
            }
        }
        /* --- Akhir CSS Baru --- */

        /* --- CSS untuk info-group dan spacer --- */
        .info-card .section .info-group {
            margin-bottom: 1rem; 
        }
        .info-card .section .info-group:last-child {
            margin-bottom: 0; 
        }
        .info-card .section .spacer {
            flex-grow: 1; 
        }
        /* --- Akhir CSS info-group dan spacer --- */

        /* --- CSS BARU UNTUK HALAMAN EVALUASI --- */
        .form-card {
            background: rgb(235, 238, 245); 
            border-radius: 30px; 
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05);
            padding: 15px 25px; 
            margin-bottom: 1.2cm; 
        }

        .form-card h4 {
            font-weight: 600; 
            font-size: 1.05rem; 
            margin-bottom: 0.8cm; 
        }

        .form-group-custom { 
            margin-bottom: 1rem; 
            display: flex;
            align-items: center; 
            flex-wrap: wrap; 
        }

        .form-group-custom label {
            flex: 0 0 180px; 
            margin-right: 20px; 
            font-size: 1rem;
            font-weight: 500; 
            color: #333;
        }
        
        .form-group-custom .form-control-custom {
            flex: 1; 
            min-width: 200px; 
            background-color: white;
            border: 1px solid #ced4da;
            border-radius: 10px; 
            padding: 10px 15px;
            font-size: 0.95rem;
            height: 45px; 
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .form-group-custom .form-control-custom:focus {
            border-color: rgb(67, 54, 240);
            box-shadow: 0 0 0 0.25rem rgba(67, 54, 240, 0.25);
            outline: none;
        }

        .form-group-custom textarea.form-control-custom {
            min-height: 200px; 
            resize: vertical; 
        }
        
        /* Tombol Kirim */
        .button-group-bottom {
            display: flex;
            justify-content: space-between; 
            align-items: center; /* Ini yang membuat mereka sejajar secara vertikal */
            margin-top: 1.2cm; 
        }

        .btn-kirim {
            background-color: #4cfaab; 
            color: black; 
            border: none;
            border-radius: 20px;
            /* UBAH: Padding vertikal 0, sentralisasi dengan flexbox */
            padding: 0 25px; 
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
            /* UBAH: Gunakan display: flex untuk konten internal */
            display: flex; 
            align-items: center;
            justify-content: center; /* Pusat konten horizontal di dalam tombol */
            height: 45px; 
            margin-top: 55px; /* BARU: Ini yang menggeser tombol Kirim sedikit ke bawah */
        }

        .btn-kirim:hover {
            background-color: #3cb88a; 
            color: white; 
        }
        /* --- AKHIR CSS BARU --- */

        /* Responsive adjustment for form-group-custom */
        @media (max-width: 768px) {
            .form-group-custom {
                flex-direction: column; 
                align-items: flex-start; 
            }
            .form-group-custom label {
                flex: none; 
                width: 100%; 
                margin-bottom: 0.5rem; 
                margin-right: 0; 
            }
            .form-group-custom .form-control-custom {
                width: 100%; 
                min-width: unset; 
            }
        }

        /* --- CSS for Success Modal --- */
        .success-modal-content {
            background-color: rgb(235, 238, 245); /* Matching form-card background */
            border-radius: 30px; /* Same as form-card */
            border: none;
            padding: 20px; /* Adjust padding as needed */
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05); /* Similar shadow */
        }

        .success-modal-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 30px 20px; /* More padding inside for spacing */
        }

        /* --- PERUBAHAN UNTUK IKON SUKSES DI SINI --- */
        .success-icon {
            width: 6rem;   /* Mengatur lebar gambar */
            height: 6rem;  /* Mengatur tinggi gambar */
            margin-bottom: 20px;
        }
        /* --- AKHIR PERUBAHAN UNTUK IKON SUKSES --- */

        .success-message {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }
    </style>
</head>
<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
            </div>
            <ul class="NavSide__sidebar-nav">
                <!-- MENU "Detail Sidang" DIHAPUS DARI SINI -->
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"> <!-- Evaluasi aktif -->
                    <b></b><b></b>
                    <a href="#">
                        <span class="fw-semibold">Evaluasi</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="#">
                        <span class="NavSide__sidebar-title fw-semibold">Dokumen</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a onclick="location.href='dNilaiAkhir.php'">
                        <span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="NavSide__toggle">
            <i class="bi bi-list open"></i>
            <i class="bi bi-x-lg close"></i>
        </div>

        <main class="NavSide__main-content">
            <h2>Detail Sidang - Sistem Pengajuan Sidang</h2>
            <!-- Badge Status Pengajuan Dihapus sesuai gambar -->
            
            <div class="info-card">
                <div class="section">
                    <!-- Judul Sidang -->
                    <div class="info-group">
                        <div class="label-row">
                            <i class="fa-solid fa-file-invoice"></i> <!-- Icon berubah -->
                            <span class="fw-bold">Judul Sidang</span>
                        </div>
                        <div class="value-row">Struktur Data</div> <!-- Nilai berubah -->
                    </div>

                    <!-- Dosen Pembimbing -->
                    <div class="info-group">
                        <div class="label-row">
                            <i class="fa-solid fa-user-tie"></i> <!-- Icon baru -->
                            <span class="fw-bold">Dosen Pembimbing</span>
                        </div>
                        <div class="value-row">Dr. Rida Indah Fariani, S.Kom, M.Kom</div> <!-- Nilai baru -->
                    </div>
                    
                    <!-- Dosen Penguji -->
                    <div class="info-group">
                        <div class="label-row">
                            <i class="fa-solid fa-user-group"></i> 
                            <span class="fw-bold">Dosen Penguji</span>
                        </div>
                        <div class="value-row">
                            Timotius Victory, S.Kom, M.Kom<br>
                            Ning Ratwasturi, S.Kom, M.Kom
                        </div>
                    </div>
                </div>
                <div class="section">
                    <!-- Ruangan -->
                    <div class="info-group">
                        <div class="label-row">
                            <i class="fa-solid fa-door-open"></i>
                            <span class="fw-bold">Ruangan</span>
                        </div>
                        <div class="value-row">CB101 - RPL 1B</div>
                    </div>

                    <!-- Tanggal -->
                    <div class="info-group">
                        <div class="label-row">
                            <i class="fa-solid fa-calendar-days"></i>
                            <span class="fw-bold">Tanggal</span>
                        </div>
                        <div class="value-row">Selasa, 22 April 2025</div>
                    </div>

                    <!-- Jam -->
                    <div class="info-group">
                        <div class="label-row">
                            <i class="fa-solid fa-clock"></i>
                            <span class="fw-bold">Jam</span>
                        </div>
                        <div class="value-row">09.00 - 10.00</div>
                    </div>
                </div>
            </div>
            
            <!-- BAGIAN BARU: Nilai Sidang (Sementara) -->
            <h3>Nilai Sidang (Sementara)</h3>
            <div class="form-card">
                <h4>Masukkan Nilai Sidang</h4>
                <div class="form-group-custom">
                    <label for="nilaiLaporan">Nilai Laporan</label>
                    <input type="text" id="nilaiLaporan" class="form-control-custom">
                </div>
                <div class="form-group-custom">
                    <label for="materiPresentasi">Materi Presentasi</label>
                    <input type="text" id="materiPresentasi" class="form-control-custom">
                </div>
                <div class="form-group-custom">
                    <label for="nilaiPenyampaian">Nilai Penyampaian</label>
                    <input type="text" id="nilaiPenyampaian" class="form-control-custom">
                </div>
                <div class="form-group-custom">
                    <label for="nilaiProyek">Nilai Proyek</label>
                    <input type="text" id="nilaiProyek" class="form-control-custom">
                </div>
            </div>

            <!-- BAGIAN BARU: Catatan Evaluasi Sidang -->
            <h3>Catatan Evaluasi Sidang</h3>
            <div class="form-card">
                <h4>Masukkan Catatan Evaluasi Sidang</h4>
                <div class="form-group-custom">
                    <label for="catatanEvaluasi" class="visually-hidden">Catatan Evaluasi</label> 
                    <textarea id="catatanEvaluasi" class="form-control-custom"></textarea>
                </div>
            </div>
            
            <div class="button-group-bottom">
                <button class="btn-kembali">
                    <span class="icon-circle">
                        <i class="fa-solid fa-arrow-left"></i>
                    </span>
                     <a onclick="location.href='dDaftarSidang.php'">
                    Kembali
                </button>
                <!-- Tombol Kirim yang akan memicu modal -->
                <button class="btn-kirim" data-bs-toggle="modal" data-bs-target="#successModal">
                    Kirim
                </button>
            </div>
        </main>
    </div>
    
    <!-- Success Modal HTML -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content success-modal-content">
                <div class="modal-body success-modal-body">
                    <!-- --- PERUBAHAN IKON SUKSES DI SINI --- -->
                    <img src="../../assets/img/centang.svg" alt="Success Checkmark" class="success-icon">
                    <!-- --- AKHIR PERUBAHAN IKON SUKSES --- -->
                    <p class="success-message">Evaluasi Sidang Berhasil Dikirim!</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script type="text/javascript">
      // Skrip untuk toggle sidebar dan active menu item
      let menuToggle = document.querySelector(".NavSide__toggle");
      let sidebar = document.getElementById("main-sidebar");

      if (menuToggle && sidebar) {
        menuToggle.onclick = function () {
          menuToggle.classList.toggle("NavSide__toggle--active");
          sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
      }

      let listItems = document.querySelectorAll(".NavSide__sidebar-item");
      if (listItems.length > 0) {
        for (let i = 0; i < listItems.length; i++) {
          listItems[i].onclick = function (event) {
            for (let j = 0; j < listItems.length; j++) {
              listItems[j].classList.remove("NavSide__sidebar-item--active");
            }
            this.classList.add("NavSide__sidebar-item--active");
          };
        }
      }

      // Tidak ada fungsi JS kustom tambahan yang diperlukan untuk modal Bootstrap,
      // karena pemicunya sudah diatur melalui data-bs-toggle dan data-bs-target.
    </script>
</body>
</html>