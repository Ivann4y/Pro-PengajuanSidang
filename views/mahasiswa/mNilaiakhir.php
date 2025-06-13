<?php
    // Placeholder data untuk mahasiswa (disesuaikan dengan gambar)
    $mahasiswa = [
        'nama'   => 'M. Haaris Nur S.',
        'nim'    => '0920240033',
        'matkul' => 'Tugas Akhir', // Data untuk Mata Kuliah
        'dosen'  => 'Timotius Victory'
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
    
    #cardcatatan {
        background-color: rgb(235, 238, 245);
        border-radius: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
    }

    #cardNilai, #carddataMahasiswa {
        background-color: rgb(235, 238, 245);
        border-radius: 50px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
    }

    #nilaiMahasiswa {
        font-size: 9.5rem !important;
        font-weight: bold;
        text-align: center;
        border-radius: 30px;
        width: 90%;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0;
        margin: 0 auto;
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
        margin-left: 15px;
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
    
    /* CSS UNTUK MEMPERBESAR IKON DAN TEKS DATA MAHASISWA */
    #carddataMahasiswa .label-row {
        gap: 1rem !important;
    }

    #carddataMahasiswa .label-row i {
        font-size: 1.8rem;
        width: 20px;
        color: black; /* Diubah menjadi hitam */
    }

    #carddataMahasiswa .label-row .fw-bold {
        font-size: 1.3rem;
    }

    #carddataMahasiswa .value-row {
        font-size: 1.1rem;
    }

    #carddataMahasiswa .info-group {
        margin-bottom: 2rem !important;
        padding-left: 0 !important;
    }

    #carddataMahasiswa .card-body {
        padding: 2.5rem !important;
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
    <div class="container-fluid">
        <div class="row mb-4 title-container">
            <div class="col-12">
                <h2 class="main-title ps-4">
                    Mahasiswa / Detail Evaluasi - Sistem Pengajuan Sidang
                </h2>
            </div>
        </div>
        
        <!-- ================== AWAL PERUBAHAN STRUKTUR HTML ================== -->
        <div class="row mt-4 g-4 align-items-stretch mb-5 p-3">
            <!-- KARTU DATA MAHASISWA -->
            <div class="col-lg-6 d-flex">
              <div class="card flex-fill" id="carddataMahasiswa">
                <div class="card-body card-soft">
                  <h3 class="card-title text-dark mb-5 text-center">Data Mahasiswa</h3>
                  <div class="info-group">
                    <div class="label-row d-flex align-items-center mb-1">
                      <i class="fa-solid fa-id-card"></i>
                      <span class="fw-bold text-dark">NIM</span>
                    </div>
                    <div class="value-row text-secondary fw-bold"><?php echo htmlspecialchars($mahasiswa['nim']); ?></div>
                  </div>
                  <div class="info-group">
                    <div class="label-row d-flex align-items-center mb-1">
                      <i class="fa-solid fa-user"></i>
                      <span class="fw-bold text-dark">Nama</span>
                    </div>
                    <div class="value-row text-secondary fw-bold"><?php echo htmlspecialchars($mahasiswa['nama']); ?></div>
                  </div>
                  <div class="info-group">
                    <div class="label-row d-flex align-items-center mb-1">
                      <i class="fa-solid fa-book"></i>
                      <span class="fw-bold text-dark">Mata Kuliah</span>
                    </div>
                    <div class="value-row text-secondary fw-bold"><?php echo htmlspecialchars($mahasiswa['matkul']); ?></div>
                  </div>
                  <div class="info-group">
                    <div class="label-row d-flex align-items-center mb-1">
                      <i class="fa-solid fa-user-tie"></i>
                      <span class="fw-bold text-dark">Dosen Pembimbing</span>
                    </div>
                    <div class="value-row text-secondary fw-bold"><?php echo htmlspecialchars($mahasiswa['dosen']); ?></div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- KARTU NILAI MAHASISWA -->
            <div class="col-lg-6 d-flex">
                <div class="card flex-fill" id="cardNilai">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title text-dark text-center">Nilai Mahasiswa :</h3>
                        <div class="d-flex justify-content-center align-items-center flex-grow-1">
                            <input type="text" class="form-control text-dark" id="nilaiMahasiswa" value="A" readonly />
                        </div>
                    </div>
                </div>
            </div>

            <!-- KARTU CATATAN (DIPINDAHKAN KE DALAM ROW YANG SAMA) -->
            <div class="col-12">
                <div class="card" id="cardcatatan">
                    <div class="card-body">
                        <h3 class="card-title text-dark">Catatan:</h3>
                        <div class="text-dark" id="catatan-content">
                            Tidak ada catatan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ================== AKHIR PERUBAHAN STRUKTUR HTML ================== -->
        
        <div class="row">
            <div class="col-auto"> 
                <button class="btn-kembali" onclick="location.href='mSidang.php'">
                    <span class="icon-circle">
                        <i class="fa-solid fa-arrow-left"></i>
                    </span>
                    Kembali
                </button>
            </div>
        </div>
    </div>
</main>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
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