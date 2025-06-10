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
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="../../extra/style.css">
  <title>Detail Pengajuan</title>
  <style>
    body {
      font-family: 'Poppins';
    }

    .btn-circle {
      border-radius: 12px;
      padding: 6px 24px;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .btn-danger.btn-circle {
      background-color: #dc3545;
      color: white;
      border: 2px solid #dc3545;
    }

    .btn-danger.btn-circle:hover {
      background-color: transparent;
      color: #dc3545;
      border: 2px solid #dc3545;
    }

    .btn-success.btn-circle {
      background-color: #198754;
      color: white;
      border: 2px solid #198754;
    }

    .btn-success.btn-circle:hover {
      background-color: transparent;
      color: #198754;
      border: 2px solid #198754;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 24px;
    }

    .file-pill {
      background: #f4f6fa;
      border: 2px solid #2f3a8f;
      border-radius: 30px;
      padding: 6px 12px;
      margin-right: 10px;
      display: inline-flex;
      align-items: center;
    }

    .file-pill i {
      margin-right: 6px;
    }

    .info-pengajuan {
      background-color: #f8f9fa; /* default card bg */
      color: #212529;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .info-pengajuan:hover {
      background-color: #0d6efd; /* biru Bootstrap */
      color: #fff;
    }

    .info-pengajuan:hover p,
    .info-pengajuan:hover h5 {
      color: #fff;
    }

    .dokumen-sidang {
      background-color: #f8f9fa; /* default */
      color: #212529;        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .dokumen-sidang:hover {
      background-color: #0d6efd; /* biru saat hover */
      color: #fff;
    }

    .dokumen-sidang:hover h5,
    .dokumen-sidang:hover .file-link { 
      color: #fff;
    }

    .file-link {
      display: inline-block;
      align-items: center;
      gap: 8px;
      padding: 6px 12px;
      border: 1px solid #212529;  /* border hitam default */
      border-radius: 8px;
      background-color: transparent;
      color: #212529;
      transition: all 0.3s ease;
      text-decoration: none;
      cursor: pointer;
    }
    .file-link i {
      transition: color 0.3s ease;
      color: inherit;
    }

    .file-link.berkas-laporan:hover {
      background-color: #fff;
      border-color: #fff;  
      color: #0d6efd;          
    }

    .file-link.berkas-laporan:hover i {
      color: #0d6efd; 
    }

    .dokumen-sidang:hover .file-link:hover {
      background-color: #fff;
      color: #0d6efd;
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
      transition: background-color 0.3s ease, transform 0.2s ease;
      display: inline-flex;
      align-items: center;
      /* margin-top: 4cm; */
      width: auto;
      gap: 10px;
    }
    .btn-kembali:hover {
      position: relative;
      background-color: rgb(59, 54, 136);
    }
  </style>
</head>

<body class="p-4">
  <div id="NavSide">
    <div id="main-sidebar" class="NavSide__sidebar">
      <div class="NavSide__sidebar-brand">
        <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
      </div>
      <ul class="NavSide__sidebar-nav">
        <li class="NavSide__sidebar-item">
          <b></b><b></b>
          <a href="dBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
        </li>
        <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
          <b></b><b></b>
          <a href="dPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
        </li>
        <li class="NavSide__sidebar-item">
          <b></b><b></b>
          <a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a>
        </li>
        <li class="NavSide__sidebar-item">
          <b></b><b></b>
          <a href="logout.html" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
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
        <!-- <div class="profile-icon">
          <i class="bi bi-person-fill fs-5"></i>
        </div> -->
      </div>
    </div>
    <main class="NavSide__main-content" id="dPengajuan">
      <div class="dashboard-header">
        <div class="header-icons d-none d-md-flex"> <i class="bi bi-bell-fill"></i>
          <!-- <div class="profile-icon">
            <i class="bi bi-person-fill fs-5"></i>
          </div> -->
        </div>
     </div>

  <h3 class="mb-4">Detail Pengajuan</h3>

<div class="card mb-3 info-pengajuan">
    <h5 class="fw-semibold">Informasi Pengajuan</h5>
    <div class="row mt-2">
        <div class="col-md-6">
            <p class="mb-1">Nama Mahasiswa</p>
            <p class="fw-bold"><?php echo htmlspecialchars($mahasiswa['nama']); ?></p>

            <p class="mb-1">Nomor Induk Mahasiswa</p>
            <p class="fw-bold"><?php echo htmlspecialchars($mahasiswa['nim']); ?></p>
        </div>
        <div class="col-md-6">
            <p class="mb-1">Mata Kuliah</p>
            <p class="fw-bold"><?php echo htmlspecialchars($mahasiswa['mata_kuliah']); ?></p>

            <?php
            if (isset($mahasiswa['judul_sidang'])) {
            ?>
                <p class="mb-1 mt-3">Judul Sidang</p> <p class="fw-bold"><?php echo htmlspecialchars($mahasiswa['judul_sidang']); ?></p>
            <?php
            }
            ?>
        </div>
    </div>
</div>

<div class="card mb-3 dokumen-sidang">
  <h5 class="fw-semibold">Dokumen Sidang</h5>
  <div class="mt-2">
    <a class="file-pill text-decoration-none file-link berkas-laporan" href="#" download>
      <i class="fa-solid fa-file-lines"></i> berkas_laporan_kel-1.pdf
    </a>
    <a class="file-pill text-decoration-none file-link berkas-laporan" href="#" download>
      <i class="fa-solid fa-file-zipper"></i> dokumen_pendukung_kel-1.zip
    </a>
  </div>
</div>


  <div class="d-flex justify-content-between">
    <a href="dpengajuan.php"><button class="btn-kembali"><i class="fa-solid fa-circle-arrow-left"></i>Kembali</button></a>
    <div>
     <button class="btn btn-danger btn-circle me-2" onclick="showModal('Ditolak')">Tolak</button>
    <button class="btn btn-success btn-circle" onclick="showModal('Disetujui')">Setujui</button>
    </div>
  </div>

    <div class="modal fade" id="notifModal" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <img src="../../assets/img/centang.svg" width="200" class="mx-auto mb-3" alt="Check Icon">
        <h5 class="modal-title fw-bold" id="notifModalLabel"></h5>
        </div>
    </div>
  </div>

  <script>
    function showModal(status) {
      const modalLabel = document.getElementById('notifModalLabel');
      modalLabel.innerText = `Sidang telah berhasil ${status.toLowerCase()}`;
      const modal = new bootstrap.Modal(document.getElementById('notifModal'));
      modal.show();
    }
  </script>
</body>

</html>