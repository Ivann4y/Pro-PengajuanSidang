<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$path_to_root = '../../';

// 1. Cek jika pengguna BELUM login.
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php"); 
    exit(); 
}

// 2. Cek jika role pengguna BUKAN 'mahasiswa'.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php");
    exit(); 
}

include '../../koneksi/koneksiAndrew.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
   <link rel="stylesheet" href="../../assets/css/style.css" />
  <link rel="stylesheet" href="../../extra/style.css">
  <link rel="stylesheet" href="../../css/button-styles.css">
  <link rel="stylesheet" href="../../assets/css/mNotifikasi.css">
  
  <title>Mahasiswa - Notifikasi</title>
  <style>
  
  </style>
</head>

<body>
   <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mSidang.php"><span class="NavSide__sidebar-title fw-semibold">Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#logMBeranda"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
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
          <div class="row">
            <h2 class="text-heading text-black">
              Nayaka Ivana Putra (Mahasiswa)
            </h2>
          </div><br>
          <div class="row align-items-center mb-3">
            <div class="col">
              <b>Notifikasi</b>
            </div>
            
            <div class="col-auto ms-auto">
              <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle " type="button" data-bs-toggle="dropdown" aria-expanded="false" id="ddMBelumDibaca">
                  Belum Dibaca
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#" id="ddMSudahDibaca" onclick="switchMNotifikasi();">Sudah Dibaca</a></li>
                </ul>
              </div>
            </div>

          </div><br>
          <div class="row">
            <div class="col-12">
              <div class="table-responsive">
            <table>
              <thead>
                <tr>
                  <th scope="col">Dari</th>
                  <th scope="col">Pesan</th>
                  <th scope="col">Waktu</th>
                  <th scope="col">Status</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="BelumDibaca">
                <tr class="isiTabel jadiBiru ">
                  <td>Rida Indah Fariani</td>
                  <td>Pengajuan telah disetujui oleh dosen pembimbing</td>
                  <td>20 April 2025, 10:00</td>
                  <td>Belum Dibaca</td>
                  <td><span onclick="bacaModal(this)">✔️</span></td>
                </tr>
              </tbody>
              <tbody id="SudahDibaca" style="display: none;">
                <tr class="isiTabel ">
                  <td>Timotius Victory</td>
                  <td>Pengajuan telah ditolak oleh dosen pembimbing</td>
                  <td>20 Januari 2025, 10:00</td>
                  <td>Sudah Dibaca</td>
                  <td></td>
                </tr>
                <tr class="isiTabel ">
                  <td>Timotius Victory</td>
                  <td>Pengajuan telah disetujui oleh dosen pembimbing</td>
                  <td>21 Januari 2025, 10:00</td>
                  <td>Sudah Dibaca</td>
                  <td></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
    </div>
    </main>
    <!-- Modal Konfirmasi -->
     <div class="modal fade" id="konfirmasiModalnotifikasi" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
      <div class="modal-header border-0 justify-content-center">
                    <h4 class="modal-title fw-bold" id="modalKonfirmasiLabel" style="font-size: 24px;">Perhatian</h4>
                  </div>
      <div class="modal-body">
        <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah anda sudah yakin ingin mengubah status Terbaca?</p>
        <div class="d-flex justify-content-between px-5">
          <button type="button" class="btnKonfirmasi btn-tolak" id="tidakmodal" data-bs-dismiss="modal">Tidak</button>
          <button type="button" class="btnKonfirmasi btn-setujui" id="iyamodal" onclick="lanjutkanAksi()">Iya</button>
        </div>
      </div>
    </div>
  </div>
</div>
  <div class="modal fade" id="logABeranda" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-heading-color">
                    <div class="modal-header">
                        <h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1>
                    </div>
                </div>
                <div class="modal-body mx-auto">
                    Apakah anda yakin ingin keluar?
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

  </div>

  <scrip src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  
    <script src="../../assets/js/mNotifikasi.js"></script>
  
</body>

</html>