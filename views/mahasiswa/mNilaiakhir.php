<?php
    // Placeholder data untuk mahasiswa
    // Anda bisa menggantinya dengan data dinamis jika diperlukan
    $mahasiswa = [
        'nama'  => 'M. Haaris Nur S.',
        'nim'   => '0920240033',
        'prodi' => 'Teknik Informatika' 
    ];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../css/style.css" />
    <link rel="stylesheet" href="../../extra/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <title>Mahasiswa - Nilai Akhir</title>
<style>
    body,
    .card,
    .form-control,
    h1, h2, h3, h4, h5, h6 {
        font-family: "Poppins", sans-serif !important;
        color: #464869;
    }

    .main-title {
        font-weight: 700 !important;
        color: #343a40; 
        margin-bottom: 0.5rem; 
    }
    
    .student-info {
        font-size: 1rem;
        color: #6c757d; 
        font-weight: 500;
    }
    
    /* Aturan untuk card agar konsisten */
    #cardNilai, #carddetailPenilaian, #cardcatatan {
        background-color: #f2f2f2;
        border-radius: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-left: 15px;
        
    }

    #cardNilai {
        border-radius: 50px;
        width: 500px;
        height: 480px;
    }

    #carddetailPenilaian, #cardcatatan {
        width: 1000px;
    }


    /* Custom column class untuk layout 2 kolom berdampingan */
    .col-lg-49 {
        flex: 0 0 49%;
        max-width: 49%;
    }

    /* Style khusus untuk card Data Mahasiswa */
    #carddataMahasiswa {
        background-color: rgb(235, 238, 245);
        border-radius: 50px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 500px;
        height: 477px;
    }

    /* Style untuk grup info (NIM, Nama, dll) */
    .info-group, .value-row {
        font-size: 1rem;
    }

    /* Class untuk mengatur layout internal di dalam card data mahasiswa */
    .section, .section2 {
        flex: 1 1 200px; /* Membuat layout flexbox yang responsif */
        color: #333;
    }
    .section {
        margin-left: 30px;
        margin-top: 25px;
    }
    .section2 {
        margin-top: 25px;
    }
    /* === AKHIR DARI STYLES BARU === */


    @media (max-width: 1200px) {
        #carddetailPenilaian,
        #cardcatatan {
            width: calc(100% - 40px);
            margin-left: 20px;
            margin-right: 20px;
        }
        .student-image {
            width: 100%;
            height: auto;
            margin-left: 0;
        }
    }
    
    @media (max-width: 992px) {
        .row.mt-5.align-items-center.justify-content-between {
            flex-direction: column !important;
            align-items: center !important;
        }

        .row.mt-5 .col-md-6,
        .row.mt-5 .col-12 {
            width: 100% !important;
            margin-bottom: 1.5rem;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        #cardNilai,
        #carddetailPenilaian,
        #cardcatatan,
        #carddataMahasiswa { /* Menambahkan #carddataMahasiswa agar konsisten */
            width: 100% !important;
            margin-left: auto !important;
            margin-right: auto !important;
            height: auto !important;
        }

        .student-image {
            display: none !important;
        }

        #nilaiMahasiswa {
            font-size: 7rem !important;
        }
        
        .title-container {
            margin-left: 0 !important;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        #carddetailPenilaian .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 0.5rem !important;
        }
        
        .row.mt-5 .col-auto.ps-0 {
            padding-left: 1rem !important;
        }

        .btn-kembali {
            margin-left: 0 !important;
        }

        /* --- Penyesuaian untuk Card Data Mahasiswa di Mobile --- */
        .col-lg-49 {
            flex: 0 0 100%; /* Membuat card mengisi penuh di mobile */
            max-width: 100%;
        }
        .section, .section2 {
            margin: 0 !important; /* Menghapus margin agar rata kiri */
        }
        .section2 {
            margin-top: 1rem !important; /* Memberi sedikit jarak antar section di mobile */
        }
        /* --- Akhir Penyesuaian --- */
    }
    
    #nilaiMahasiswa {
        font-size: 9.5rem !important;
        font-weight: bold;
        text-align: center;
        border-radius: 30px;
        width: 90%;
        background: #f2f2f2 !important;
        border-color: #f2f2f2 !important;
        border: none;
        padding: 0;
    }
    
    #catatan-content {
        padding: 1rem;
        font-size: 1rem;
    }

    .btn-kembali {
        background-color: #4B68FB;
        color: white;
        border: none;
        border-radius: 20px;
        padding: 10px 25px;
        cursor: pointer;
        font-size: 0.95rem;
        font-weight: 500;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
        display: inline-flex; 
        align-items: center; 
        margin-top: 1.2cm;
        margin-left: 60px;
    }
    .btn-kembali:hover {
        position: relative;
        background-color: white;
        color: #4B68FB;
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
        background-color: #4B68FB;
    }

    .btn-kembali .icon-circle i {
        color: #4B68FB;
        font-size: 1rem; 
        transition: color 0.3s ease;
    }

    .btn-kembali:hover .icon-circle i {
        color: white;
    }
</style>
  </head>
  <body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand img ">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item ">
                    <b></b><b></b>
                    <a href="MdetailSidang.php"><span class="NavSide__sidebar-title fw-semibold">Detail Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mPerbaikan.php"><span class="NavSide__sidebar-title fw-semibold">Perbaikan</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="mNilaiakhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a>
                </li>
            </ul>
        </div>

        <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <div class="header-icons">
                <i class="bi bi-bell-fill"></i>
                <div class="profile-icon">
                    <i class="bi bi-person-fill fs-5"></i>
                </div>
            </div>
        </div>
   <main class="NavSide__main-content">
    <div class="container-fluid p-0">
        <div class="row mb-4 title-container" style="margin-left: 40px;">
            <div class="col-12">
                <h2 class="main-title">
                    Mahasiswa / Detail Evaluasi - Sistem Pengajuan Sidang
                </h2>
            </div>
        </div>
        
        <div class="row mt-5 align-items-center justify-content-between">
            <div class="col-md-6">
                <div class="card" id="cardNilai">
                    <div class="card-body d-flex flex-column">
                        <h3 class="card-title text-dark" style="padding:10px;">Nilai Mahasiswa:</h3>
                        <div class="d-flex justify-content-center align-items-center flex-grow-1">
                            <input type="text" class="form-control text-dark"
                                id="nilaiMahasiswa" value="A" readonly />
                        </div>
                    </div>
                </div>
            </div>
            
<div class="col-lg-49 mb-3 d-flex">
  <div class="card flex-fill" id="carddataMahasiswa">
    <div class="card-body card-soft px-4 py-3">
      <h3 class="card-title text-black mb-4 text text-center" style="padding:10px;">Data Mahasiswa</h3>
      <div class="d-flex flex-wrap gap-1 px-4 py-3">
        <!-- Section 1 -->
        <div class="section" style="flex: 1 1 200px; margin-left:30px; margin-top:25px; color: #333;">
          <!-- NIM -->
          <div class="info-group mb-3">
            <div class="label-row d-flex align-items-center gap-2 mb-1">
              <i class="fa-solid fa-id-card"></i>
              <span class="fw-bold">NIM</span>
            </div>
            <div class="value-row text-secondary">0920240033</div>
          </div>
          <!-- Nama -->
          <div class="info-group mb-3">
            <div class="label-row d-flex align-items-center gap-2 mb-1">
              <i class="fa-solid fa-user"></i>
              <span class="fw-bold">Nama</span>
            </div>
            <div class="value-row text-secondary">M. Harris Nur S.</div>
          </div>
        </div>
        <!-- Section 2 -->
        <div class="section2" style="flex: 1 1 200px;; margin-top:25px; color: #333;">
          <!-- Mata Kuliah -->
          <div class="info-group mb-3">
            <div class="label-row d-flex align-items-center gap-2 mb-1">
              <i class="fa-solid fa-book"></i>
              <span class="fw-bold">Mata Kuliah</span>
            </div>
            <div class="value-row text-secondary">Tugas Akhir</div>
          </div>
          <!-- Dosen Pembimbing -->
          <div class="info-group mb-3">
            <div class="label-row d-flex align-items-center gap-2 mb-1">
              <i class="fa-solid fa-user-tie"></i>
              <span class="fw-bold">Dosen Pembimbing</span>
            </div>
            <div class="value-row text-secondary">Timotius Victory</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
        
        <div class="row mt-5">
          <div class="col-12">
            <div class="card" id="carddetailPenilaian">
                <div class="card-body">
                    <h3 class="card-title text-dark" >Detail Penilaian :</h3>
                    <div class="d-flex align-items-center gap-4 flex-wrap mt-3 text-dark">
                        <span><strong>Nilai Laporan :</strong> 95</span>
                        <span><strong>Materi Presentasi :</strong> 90</span>
                        <span><strong>Penyampaian :</strong> 94</span>
                        <span><strong>Nilai Proyek :</strong> 93</span>
                    </div>
                </div>
            </div>
          </div>
        </div>

        <!-- STRUKTUR BARU (YANG BENAR) -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card" id="cardcatatan">
                    <div class="card-body">
                        <h3 class="card-title text-dark" >Catatan:</h3>
                        <div class="text-dark" id="catatan-content">
                            Tidak ada catatan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <!-- Kolom ini akan kita biarkan tidak 100% width di mobile -->
            <div class="col-auto ps-0"> 
                <button class="btn-kembali" onclick="location.href='mSidang.php'">
                    <span class="icon-circle">
                        <i class="fa-solid fa-arrow-left"></i>
                    </span>
                    Kembali
                </button>
            </div>
        </div>
        <!-- === END OF FINAL STRUCTURE === -->
    </div>
</main>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    // ... (Your existing JavaScript remains unchanged) ...
    let menuToggle = document.querySelector(".NavSide__toggle");
    let sidebar = document.getElementById("main-sidebar");

    menuToggle.onclick = function () {
        menuToggle.classList.toggle("NavSide__toggle--active");
        sidebar.classList.toggle("NavSide__sidebar--active-mobile");
    };

    let listItems = document.querySelectorAll(".NavSide__sidebar-item");
    for (let i = 0; i < listItems.length; i++) {
        listItems[i].onclick = function () {
            if(!this.classList.contains("NavSide__sidebar-item--active")) {
                for (let j = 0; j < listItems.length; j++) {
                    listItems[j].classList.remove("NavSide__sidebar-item--active");
                }
                this.classList.add("NavSide__sidebar-item--active");
            }
        };
    }
    </script>
  </body>
</html>