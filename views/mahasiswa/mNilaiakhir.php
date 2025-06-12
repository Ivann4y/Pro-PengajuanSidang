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
        margin-left: 50px;
    }

    #cardNilai {
        border-radius: 50px;
        width: 500px;
        height: 350px;
    }

    #carddetailPenilaian, #cardcatatan {
        width: 1000px;
    }

    .student-image {
        width: 500px;
        height: 350px;
        margin-left: 20px;
        mix-blend-mode: multiply;
    }

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

        /* PERBAIKAN UTAMA: 
           Aturan ini sekarang hanya menargetkan kolom konten utama.
           .col-auto untuk button TIDAK LAGI IKUT menjadi 100% width.
        */
        .row.mt-5 .col-md-6,
        .row.mt-5 .col-12 {
            width: 100% !important;
            margin-bottom: 1.5rem;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        #cardNilai,
        #carddetailPenilaian,
        #cardcatatan {
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
        
        /* Memastikan kolom button punya padding yang benar di mobile */
        .row.mt-5 .col-auto.ps-0 {
            padding-left: 1rem !important;
        }

        /* Menghapus margin kiri pada button agar lurus dengan padding kolom */
        .btn-kembali {
            margin-left: 0 !important;
        }
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
        margin-left: 50px;
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
        <div class="row mb-4 title-container" style="margin-left: 50px;">
            <div class="col-12">
                <h2 class="main-title">
                    Mahasiswa / Detail Evaluasi - Sistem Pengajuan Sidang
                </h2>
                <p class="student-info">
                    <?php echo htmlspecialchars($mahasiswa['nama']); ?> - 
                    <?php echo htmlspecialchars($mahasiswa['nim']); ?> - 
                    <?php echo htmlspecialchars($mahasiswa['prodi']); ?>
                </p>
            </div>
        </div>
        
        <div class="row mt-5 align-items-center justify-content-between">
            <div class="col-md-6">
                <div class="card" id="cardNilai">
                    <div class="card-body d-flex flex-column">
                        <h3 class="card-title" style="padding:10px;">Nilai Mahasiswa:</h3>
                        <div class="d-flex justify-content-center align-items-center flex-grow-1">
                            <input type="text" class="form-control"
                                id="nilaiMahasiswa" value="A" readonly />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <img src="../../assets/img/img5.png" alt="Mahasiswa Illustration" class="img-fluid student-image"/>
            </div>
        </div>
        
        <div class="row mt-5">
          <div class="col-12">
            <div class="card" id="carddetailPenilaian">
                <div class="card-body">
                    <h3 class="card-title">Detail Penilaian :</h3>
                    <div class="d-flex align-items-center gap-4 flex-wrap mt-3">
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
                        <h3 class="card-title">Catatan:</h3>
                        <div id="catatan-content">
                            Tidak ada catatan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
              <!-- STRUKTUR HTML UNTUK BUTTON (Sudah bagus, tidak perlu diubah) -->
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