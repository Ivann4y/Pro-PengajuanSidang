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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
      margin-top: 1cm;
    }

    .btn-danger.btn-circle {
      background-color: #FD7D7D;
      color: white;
      border: 2px solid #FD7D7D;
    }

    .btn-danger.btn-circle:hover {
      background-color: transparent;
      color: #FD7D7D;
      border: 2px solid #FD7D7D;
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

    .btn-circle {
      border-radius: 12px;
      padding: 6px 24px;
      font-weight: 500;
    }

    
    .info-p {
      background-color: #efefef; /* default card bg */
      color: #212529;
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    
    .info-p:hover {
      background-color: #4B68FB; /* biru Bootstrap */
      color: #fff;
    }
    
    .info-p:hover p,
    .info-p:hover h5 {
      color: #fff;
    }

    
    .dokumen-sidang {
      background-color: #ebeef5; /* default */
      color: #212529;        
      transition: background-color 0.3s ease, color 0.3s ease;
      
    }

    .dokumen-sidang:hover {
      background-color: #0d6efd; /* biru saat hover */
      color: #fff;
    }

    .dokumen-sidang:hover h5,
    .dokumen-sidang:hover .file-link { 
      border-color: #fff;
      color: #fff
    }

    .file-link {
      display: inline-block;
      align-items: center;
      gap: 8px;
      padding:12px 12px;
      border: 1px solid #212529;  /* border hitam default */
      border-radius: 8px;
      background-color: transparent;
      color: #212529;
      transition: all 0.3s ease;
      text-decoration: none;
      cursor: pointer;
      margin-right: 30px
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
            transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease;
            display: inline-flex; 
            align-items: center; 
            margin-top: 1cm;
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
            width: 25px; 
            height: 25px; 
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

        .info-pengajuan {
            position: relative;
            background: rgb(235, 238, 245); 
            border-radius: 30px; 
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05);
            padding: 25px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 25px;
            overflow: hidden;
            transition: background-color 0.4s ease;
            /* margin-right: 8px;
            margin-left: 8px; */
        }

        .info-pengajuan::after { 
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 60px; 
            height: 100%;
            background-color: #4B68FB;
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
            transition: width 0.4s ease;
            z-index: 0;
        }

        .info-pengajuan:hover::after {
            width: 100%;
            border-radius: 20px;
        }

        .info-pengajuan .section {
            z-index: 1;
            color: #333;
            transition: color 0.4s ease;
        }

        .info-pengajuan:hover .section {
            color: white;
        }

        .info-pengajuan .section i {
            margin-right: 10px; 
            color: rgb(70, 70, 70);
            transition: color 0.4s ease;
            width: 20px; 
            text-align: center;
        }

        .info-pengajuan:hover .section i{
            color: white;
        }

        /* .info-pengajuan .section p,
        .info-pengajuan .section h5 {
           transition: color 0.4s ease;
        } */



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
    <h5 class="fw-semibold section">Informasi Pengajuan</h5>
    <div class="row mt-2">
        <div class="col-md-6 section">
            <p class="mb-1">Nama Mahasiswa</p>
            <p class="fw-bold"><?php echo htmlspecialchars($mahasiswa['nama']); ?></p>

            <p class="mb-1">Nomor Induk Mahasiswa</p>
            <p class="fw-bold"><?php echo htmlspecialchars($mahasiswa['nim']); ?></p>
        </div>
        <div class="col-md-6 section">
            <p class="mb-1">Mata Kuliah</p>
            <p class="fw-bold"><?php echo htmlspecialchars($mahasiswa['mata_kuliah']); ?></p>

            <?php
            if (isset($mahasiswa['judul_sidang'])) {
            ?>
                <p class="mb-1 mt-3">Judul Sidang</p> 
                <p class="fw-bold"><?php echo htmlspecialchars($mahasiswa['judul_sidang']); ?></p>
            <?php
            }
            ?>
        </div>
    </div>
</div>

<div class="card mb-3 dokumen-sidang position-relative">
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

<!-- <div class="card mb-3 dokumen-sidang">
  <h5 class="fw-semibold">Dokumen Sidang</h5>
  <div class="mt-2">
    <a class="file-pill text-decoration-none file-link berkas-laporan" href="#" download>
      <i class="fa-solid fa-file-lines"></i> berkas_laporan_kel-1.pdf
    </a>
    <a class="file-pill text-decoration-none file-link berkas-laporan" href="#" download>
      <i class="fa-solid fa-file-zipper"></i> dokumen_pendukung_kel-1.zip
    </a>
  </div>
</div> -->


  <div class="d-flex justify-content-between">
    <button class="btn-kembali" onclick="location.href='dpengajuan.php'">
                    <span class="icon-circle">
                        <i class="fa-solid fa-arrow-left"></i>
                    </span>
                    Kembali
                </button>
    <div class="d-flex justify-content-between ">
    <button class="btn btn-danger btn-circle me-2" onclick="showModalTolak('Ditolak')">Tolak</button>
    <button class="btn btn-success btn-circle" id="setujui" onclick="val()">Setujui</button>
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

  <div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-labelledby="modalKonfirmasiLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
                  <div class="modal-header border-0 justify-content-center">
                    <h4 class="modal-title fw-bold" id="modalKonfirmasiLabel" style="font-size: 24px;">Perhatian</h4>
                  </div>
                  <div class="modal-body">
                    <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah anda yakin ingin menyetujui?</p>
                    <div class="d-flex justify-content-between px-5">
                      <button type="button" class="btn btn-outline-danger custom-batal px-4 py-2 fw-semibold btn-tolak" data-bs-dismiss="modal">Batalkan</button>
                      <button type="submit" class="btn btn-success px-4 py-2 fw-semibold btn-setujui" onclick="showModalBerhasil()" id="submitBtn" >Lanjutkan</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

  <script>

    function val() {
      modalKonfirmasi.show();
    }
    
    function showModalBerhasil(status) {
      Swal.fire({
            title: 'Berhasil',
            text: 'Nilai akhir telah dikirim.',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#4B68FB'
          }); 
        }

      function showModalTolak(status) {
        Swal.fire({
          title: 'Error',
          text: 'Semua nilai harus diisi sebelum mengirim.',
          icon: 'error',
          confirmButtonText: 'OK',
          confirmButtonColor: '#4B68FB'
        });
      }

    const btnOpenModalKonfirmasi = document.getElementById('btnOpenModalKonfirmasi');
    const modalKonfirmasiEl = document.getElementById('modalKonfirmasi');

    const modalKonfirmasi = new bootstrap.Modal(modalKonfirmasiEl);

    btnOpenModalKonfirmasi.addEventListener('click', function() {
      const tombolSetujui = document.getElementById('setujui');
      {
        modalKonfirmasi.show();
      }
    });

    const submitBtn = document.getElementById('submitBtn');

    submitBtn.addEventListener('click', function() {
      Swal.fire({
        title: 'Pengajuan Berhasil Dikirim!',
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#4B68FB'
      }).then((result) => {
        if (result.isConfirmed) {
          history.back();
        }
      });
    });
    

  </script>
</body>

</html>