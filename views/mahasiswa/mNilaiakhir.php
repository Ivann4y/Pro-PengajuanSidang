<!-- Argha arybawa pasha -->

<?php
session_start();
if (!isset($_SESSION['selected_sidang_id']) || empty($_SESSION['selected_sidang_id'])) {
    header("Location: mSidang.php");
    exit();
}
$id_sidang = $_SESSION['selected_sidang_id'];
?>
 


<!DOCTYPE html> 
<html lang="en"> 
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- === STYLESHEETS & FONTS === -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- DIUBAH: Path disesuaikan menjadi path absolut dari root server -->
    <link rel="stylesheet" href="/Projek/Pro-PengajuanSidang/assets/css/style.css" />
    <link rel="stylesheet" href="/Projek/Pro-PengajuanSidang/assets/css/mNilaiakhir.css" />
    <link rel="stylesheet" href="/Projek/Pro-PengajuanSidang/extra/style.css" /> 
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <title>Mahasiswa - Nilai Akhir</title> 
  </head>
  <body>

    <div id="NavSide">

        <div id="main-sidebar" class="NavSide__sidebar">
 
        <div class="NavSide__sidebar-brand img ">
                <!-- DIUBAH: Path gambar disesuaikan menjadi path absolut -->
                <img src="/Projek/Pro-PengajuanSidang/assets/img/WhiteAstra.png" alt="AstraTech Logo">
            </div>

            <ul class="NavSide__sidebar-nav">

            <li class="NavSide__sidebar-item ">
                    <b></b><b></b>
                    <a href="mdetailSidang.php"><span class="NavSide__sidebar-title fw-semibold">Detail Pengajuan</span></a>
                </li>

                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mPerbaikan.php"><span class="NavSide__sidebar-title fw-semibold">Perbaikan</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="mNilaiakhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a>
                </li>

                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mSidang.php"><span class="NavSide__sidebar-title fw-semibold"> Kembali</span></a>
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
                <h2 class="main-title">
                    Detail Evaluasi - Sistem Pengajuan Sidang
                </h2>
            </div>
        </div>
        

        <div class="row mt-4 g-4">

        <div class="col-lg-6 d-flex">
              <div class="card flex-fill" id="carddataMahasiswa">
                <div class="card-body card-soft p-4">
                  <h3 class="card-title text-dark mb-4 text-center">Data Mahasiswa</h3>

                  <div class="row">

                  <div class="col-sm-6 text-black">
                  <div class="info-group mb-5">
                            <div class="label-row d-flex align-items-center gap-2 mb-1">
                              <i class="fa-solid fa-id-card"></i>
                              <span class="fw-bold">NIM</span>
                            </div>
                            <div class="value-row text-secondary fw-bold">0920240033</div>
                          </div>

                          <div class="info-group mb-3">
                            <div class="label-row d-flex align-items-center gap-2 mb-1">
                              <i class="fa-solid fa-user"></i>
                              <span class="fw-bold">Nama</span>
                            </div>
                            <div class="value-row text-secondary fw-bold">M. Harris Nur S.</div>
                          </div>
                      </div>

                      <div class="col-sm-6 text-black">

                      <div class="info-group mb-5">
                            <div class="label-row d-flex align-items-center gap-2 mb-1">
                              <i class="fa-solid fa-book"></i>
                              <span class="fw-bold">Mata Kuliah</span>
                            </div>
                            <div class="value-row text-secondary fw-bold">Tugas Akhir</div>
                          </div>

                          <div class="info-group mb-3">
                            <div class="label-row d-flex align-items-center gap-2 mb-1">
                              <i class="fa-solid fa-user-tie"></i>
                              <span class="fw-bold">Dosen Pembimbing</span>
                            </div>
                            <div class="value-row text-secondary fw-bold">Timotius Victory</div>
                          </div>
                      </div>
                  </div>
                </div>
              </div>
            </div>

            
            <div class="col-lg-6 d-flex">
                <div class="card flex-fill" id="cardNilai">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title text-dark text-center">Nilai Mahasiswa:</h3>
         
                        <div class="d-flex justify-content-center align-items-center flex-grow-1">
         
                        <input type="text" class="form-control text-dark"
                                id="nilaiMahasiswa" value="A" readonly />
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row mt-5 ">
            <div class="col-12">
                <div class="card" id="cardcatatan">
                    <div class="card-body">
                        <h3 class="card-title text-dark" >Catatan :</h3>
                        <div class="text-dark" id="catatan-content">
                            Tidak ada catatan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

    </div>
</main>
<!-- DIUBAH: Path JavaScript disesuaikan menjadi path absolut -->
<script src="/Projek/Pro-PengajuanSidang/assets/js/mNilaiakhir.js"></script>
</body>
</html>