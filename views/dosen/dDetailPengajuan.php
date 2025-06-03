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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  <title>Detail Pengajuan</title>
  <style>
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
            margin-bottom: 10px; /* Kept from user's last version */
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
        }

        .NavSide_sidebar-item.NavSide_sidebar-item--active {
            background: #ffffff;
        }

        .NavSide_sidebar-item.NavSide_sidebar-item--active a {
            color: rgb(67, 54, 240);
        }

        .NavSide__sidebar-item b:nth-child(1) {
            position: absolute;
            top: -20px;
            height: 20px;
            width: 100%;
            background: rgb(255, 255, 255);
            display: none;
            right: 0;
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
            right: 0;
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
        .NavSide_sidebar-item.NavSide_sidebar-item--active b:nth-child(1),
        .NavSide_sidebar-item.NavSide_sidebar-item--active b:nth-child(2) {
            display: block;
        }

        .NavSide__main-content {
            flex-grow: 1;
            padding: 20px;
            margin-left: 280px;
            overflow-y: auto;
            transition: margin-left 0.5s ease-in-out;
            background-color: #F9FAFB;
            padding-top: 3vh; /* Default for larger screens */
        }

        .NavSide__toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            width: 40px;
            height: 40px;
            z-index: 1100;
            transition: left 0.5s ease-in-out;
            cursor: pointer;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: none; /* Hidden by default for larger screens */
        }

        .NavSide__toggle i.bi {
            position: absolute;
            font-size: 28px;
            display: none;
        }

        .NavSide__toggle i.bi.open {
            color: rgb(67, 54, 240);
        }
        .NavSide__toggle i.bi.close {
            color: rgb(67,54,240);
        }
        .NavSide_toggle.NavSide_toggle--active i.bi.open { display: none; }
        .NavSide_toggle.NavSide_toggle--active i.bi.close { display: block; }

        /* NEW: Top Bar for smaller screens */
        .NavSide__topbar {
            display: none; /* Hidden by default for larger screens */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px; /* Adjust height as needed */
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 999; /* Below sidebar, above main content */
            align-items: center; /* Vertically align items */
            padding: 0 15px; /* Add horizontal padding */
            justify-content: space-between; /* Space between toggle and icons */
        }

        .NavSide__topbar .header-icons {
            display: flex;
            align-items: center;
            /* margin-left: auto; This is handled by justify-content: space-between */
        }

        /* --- STYLES FOR ICONS IN TOPBAR TO MATCH DESKTOP --- */
        .NavSide__topbar .header-icons .bi-bell-fill {
            font-size: 1.5rem; /* Matches desktop size */
            color: #555; /* Matches desktop color */
            margin-right: 1.5rem; /* Space between bell and profile */
            cursor: pointer;
        }
        .NavSide__topbar .profile-icon {
            width: 40px; /* Matches desktop size */
            height: 40px; /* Matches desktop size */
            background-color: #333; /* Matches desktop color */
            border-radius: 50%; /* Matches desktop shape */
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        /* --- END ICON STYLES --- */


        @media (max-width: 700px) {
            .NavSide__sidebar {
                width: 50%;
                transform: translateX(-100%);
                border-left-width: 0;
            }
            .NavSide_sidebar.NavSide_sidebar--active-mobile {
                transform: translateX(0);
                box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2);
            }
            .NavSide__sidebar-brand {
                padding: 20px 10px 30px 10px;
            }
            .NavSide__sidebar-brand img {
                width: 90%;
            }
            .NavSide__sidebar-nav {
                padding-top: 20%;
            }
            .NavSide__sidebar-item a {
                padding: 12% 10%;
                height: auto;
            }
            .NavSide__main-content {
                margin-left: 0;
                padding: 15px;
                padding-top: 75px; /* Adjust this to create space for the fixed top bar */
            }
            .NavSide__toggle {
                display: flex; /* Show toggle button on small screens */
                /* When inside NavSide__topbar, remove fixed positioning */
                position: relative;
                top: auto; /* Reset */
                background-color: transparent; /* Maintain transparency */
                box-shadow: none; /* Remove shadow if topbar has one */
                left: 0; /* Adjusted for better alignment */
            }
            .NavSide__toggle i.bi.open {
                display: block;
            }
            .NavSide_toggle.NavSide_toggle--active {
                /* This rule targets the toggle's position when the sidebar is open on mobile */
                /* We need to re-evaluate if this specific 'left' adjustment is still needed/correct */
                /* given the toggle is now part of the topbar's flex layout. */
                /* For now, leaving it as is, but it might not have the intended effect or might conflict */
                /* if the toggle is a flex item. */
                left: calc(50% + 10px);
                background-color: aliceblue; /* This might still be needed if you want a background change when active */
            }

            /* Show the top bar on small screens */
            .NavSide__topbar {
                display: flex;
            }
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
    body {
      font-family: 'Poppins;
      background-color: #f4f4f4;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 24px;
    }
    .btn-circle {
      border-radius: 12px;
      padding: 6px 24px;
      font-weight: 500;
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
      padding: 6px 12px;
      margin-right: 10px;
      border-radius: 20px;
      background-color: transparent;
      transition: background-color 0.3s ease, color 0.3s ease;
      color: #212529;
      border: 1px solid #212529;
    }

    .dokumen-sidang:hover .file-link:hover {
      background-color: #fff;
      color: #0d6efd;
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
                <div class="profile-icon">
                    <i class="bi bi-person-fill fs-5"></i>
                </div>
            </div>
        </div>
        <main class="NavSide__main-content" id="dPengajuan">
            <div class="dashboard-header">
                <div class="header-icons d-none d-md-flex"> <i class="bi bi-bell-fill"></i>
                    <div class="profile-icon">
                        <i class="bi bi-person-fill fs-5"></i>
                    </div>
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
      <a class="file-pill text-decoration-none file-link" href="#" download>
        <i class="bi bi-file-earmark-pdf"></i> berkas_laporan_kel-1.pdf
      </a>
      <a class="file-pill text-decoration-none file-link" href="#" download>
        <i class="bi bi-file-earmark-zip"></i> dokumen_pendukung_kel-1.zip
      </a>
    </div>
  </div>

  <div class="d-flex justify-content-between">
    <a href="dpengajuan.php" class="btn btn-primary btn-circle">Kembali</a>
    <div>
     <button class="btn btn-danger btn-circle me-2" onclick="showModal('Ditolak')">Tolak</button>
    <button class="btn btn-success btn-circle" onclick="showModal('Disetujui')">Setujui</button>
    </div>
  </div>

    <div class="modal fade" id="notifModal" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <img src="http://localhost/WEBSITE PRG/Pro-PengajuanSidang/assets/img/centang.svg" width="200" class="mx-auto mb-3" alt="Check Icon">
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
