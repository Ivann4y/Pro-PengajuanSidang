<?php
include '../../koneksi.php';

// Get parameters
$id_sidang = isset($_GET['id_sidang']) ? $_GET['id_sidang'] : null;

// Initialize variables
$sidang = [];
$detail_sidang = [];
$dosen_penguji = [];
$revisions = [];
$all_approved = false;

// Fetch submission details
if ($id_sidang) {
    // Get main submission data
    $sql = "SELECT * FROM Sidang WHERE id_sidang = ?";
    $params = array($id_sidang);
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    $sidang = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    
    // Get detail/revision data
    $sql = "SELECT ds.*, d.nama_dosen 
            FROM Detail_Sidang ds
            JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen
            WHERE ds.id_sidang = ?";
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt !== false) {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $detail_sidang[] = $row;
            // Collect revisions
            if (!empty($row['dok_revisi'])) {
                $revisions[] = [
                    'dokumen' => $row['dok_revisi'],
                    'dosen' => $row['nama_dosen'],
                    'catatan' => $row['catatan_sidang'],
                    'status' => $row['status_revisi']
                ];
            }
        }
    }
    
    // Check if all panelists have approved
    $sql = "SELECT COUNT(*) as total, 
                   SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved
            FROM Persetujuan_Sidang 
            WHERE id_sidang = ?";
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt !== false) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $all_approved = ($row['total'] > 0 && $row['approved'] == $row['total']);
        
        // Update revision status if all approved
        if ($all_approved) {
            $sql = "UPDATE Detail_Sidang 
                    SET status_revisi = 'Approved' 
                    WHERE id_sidang = ?";
            sqlsrv_query($conn, $sql, $params);
        }
    }

    // Handle Approval/Rejection (add to top of file)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['nomor_dosen'])) {
    $nomor_dosen = $_SESSION['nomor_dosen'];
    
    // APPROVE action
    if (isset($_POST['approve'])) {
        $sql = "UPDATE Detail_Sidang 
                SET status_revisi = 'Approved' 
                WHERE id_sidang = ? AND nomor_dosen = ?";
        $params = [$id_sidang, $nomor_dosen];
        sqlsrv_query($conn, $sql, $params);
        
        $_SESSION['alert'] = "Sidang disetujui!";
    }
    
    // REJECT action
    elseif (isset($_POST['reject']) && !empty($_POST['catatan'])) {
        $sql = "UPDATE Detail_Sidang 
                SET status_revisi = 'Rejected', 
                    catatan_sidang = ? 
                WHERE id_sidang = ? AND nomor_dosen = ?";
        $params = [$_POST['catatan'], $id_sidang, $nomor_dosen];
        sqlsrv_query($conn, $sql, $params);
        
        $_SESSION['alert'] = "Revisi diminta. Catatan telah disimpan.";
    }
    
    header("Location: " . $_SERVER['PHP_SELF'] . "?id_sidang=" . $id_sidang);
    exit();
}
}

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['nomor_dosen'])) {
    $nomor_dosen = $_SESSION['nomor_dosen'];
    
    if (isset($_POST['approve'])) {
        // Check if already approved/rejected by this panelist
        $sql = "SELECT id FROM Persetujuan_Sidang 
                WHERE id_sidang = ? AND nomor_dosen = ?";
        $params = array($id_sidang, $nomor_dosen);
        $stmt = sqlsrv_query($conn, $sql, $params);
        
        if (sqlsrv_has_rows($stmt)) {
            // Update existing approval
            $sql = "UPDATE Persetujuan_Sidang 
                    SET status = 'Approved', catatan = NULL 
                    WHERE id_sidang = ? AND nomor_dosen = ?";
        } else {
            // Create new approval
            $sql = "INSERT INTO Persetujuan_Sidang 
                    (id_sidang, nomor_dosen, status) 
                    VALUES (?, ?, 'Approved')";
        }
        
        $stmt = sqlsrv_query($conn, $sql, $params);
        
        $_SESSION['success'] = "Sidang berhasil disetujui";
        header("Location: ".$_SERVER['PHP_SELF']."?id_sidang=".$id_sidang);
        exit();
        
    } elseif (isset($_POST['reject'])) {
        $catatan = $_POST['catatan'] ?? '';
        
        if (empty($catatan)) {
            $_SESSION['error'] = "Silakan isi catatan penolakan";
        } else {
            // Check if already approved/rejected by this panelist
            $sql = "SELECT id FROM Persetujuan_Sidang 
                    WHERE id_sidang = ? AND nomor_dosen = ?";
            $params = array($id_sidang, $nomor_dosen);
            $stmt = sqlsrv_query($conn, $sql, $params);
            
            if (sqlsrv_has_rows($stmt)) {
                // Update existing approval
                $sql = "UPDATE Persetujuan_Sidang 
                        SET status = 'Rejected', catatan = ? 
                        WHERE id_sidang = ? AND nomor_dosen = ?";
            } else {
                // Create new approval
                $sql = "INSERT INTO Persetujuan_Sidang 
                        (id_sidang, nomor_dosen, status, catatan) 
                        VALUES (?, ?, 'Rejected', ?)";
            }
            
            $params = array($catatan, $id_sidang, $nomor_dosen);
            $stmt = sqlsrv_query($conn, $sql, $params);
            
            $_SESSION['success'] = "Sidang berhasil ditolak";
            header("Location: ".$_SERVER['PHP_SELF']."?id_sidang=".$id_sidang);
            exit();
        }
    }
}

// Handle document download
if (isset($_GET['download'])) {
    $doc_type = $_GET['download'];
    
    if ($doc_type === 'main' && !empty($sidang['dokumen_path'])) {
        $filepath = '../../' . $sidang['dokumen_path'];
        $filename = basename($filepath);
    } elseif (is_numeric($doc_type)) {
        // Find the specific revision
        foreach ($revisions as $rev) {
            if ($rev['id'] == $doc_type) {
                $filepath = '../../' . $rev['dokumen'];
                $filename = basename($filepath);
                break;
            }
        }
    }
    
    if (isset($filepath) && file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        die("File not found");
    }
}
?>


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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
      color: #e56a6a;
      border: 2px solid #e56a6a;
    }

    .btn-success.btn-circle {
      background-color: #4FD382;
      color: white;
      border: 2px solid #4FD382;
    }

    .btn-success.btn-circle:hover {
      background-color: transparent;
      color: #3ab070;
      border: 2px solid #3ab070;
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
      border-radius: 20px;
      background-color: transparent;
      color: #212529;
      transition: all 0.3s ease;
      text-decoration: none;
      cursor: pointer;
      margin-right: 30px;
      margin-bottom: 10px;
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

         .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 15px;
            margin-bottom: 30px;
        }

        .dashboard-header .bodyHeading {
            font-weight: bold;
            font-size: 40px;
            margin: 0;
            color: #1a1a1a;
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
          <a href="#" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
        </li>
      </ul>
    </div>

    <div class="NavSide__topbar">
      <div class="NavSide__toggle">
        <i class="bi bi-list open"></i>
        <i class="bi bi-x-lg close"></i>
      </div>
      <div class="header-icons d-flex d-md-none">
        <!-- <i class="bi bi-bell-fill"></i> -->
      </div>
    </div>
    <main class="NavSide__main-content" id="dPengajuan">
      <div class="dashboard-header">
        <div class="header-icons d-none d-md-flex"> 
          <!-- <i class="bi bi-bell-fill"></i> -->
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
    <?php if (!empty($sidang['dokumen_path'])): ?>
      <a class="file-pill text-decoration-none file-link dokumen-sidang" href="../../<?php echo htmlspecialchars($sidang['dokumen_path']); ?>" download>
        <i class="fa-solid fa-file-lines"></i> <?php echo htmlspecialchars(basename($sidang['dokumen_path'])); ?>
      </a>
    <?php endif; ?>

    <?php foreach ($revisions as $rev): ?>
    <a class="file-pill text-decoration-none file-link berkas-laporan" href="?id_sidang=<?= $id_siang ?>&download=<?= $rev['id'] ?>" download>
      <i class="fa-solid fa-file-lines"></i> Revisi: <?= basename ($rev['dokumen']) ?>
    </a>
    <?php endforeach; ?>
  </div>
</div> 

<div class="d-flex justify-content-between">
  <button class="btn-kembali" onclick="location.href='dpengajuan.php'">
    <span class="icon-circle"> <i class="fa-solid fa-arrow-left"></i></span>Kembali</button>
  <?php if (isset($_SESSION['nomor_dosen'])): ?>
  <div class="d-flex justify-content-between ">

    <button class="btn btn-danger btn-circle me-2" id="btnTolak" data-bs-toggle="#modalTolak">Tolak</button>
    <form method="POST" style="display: inline;">
    <button type="submit" name="approve" class="btn btn-success btn-circle" id="btnSetujui">Setujui</button>
    </form>
  </div>
</div>

    <div class="modal fade" id="notifModal" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <img src="../../assets/img/centang.svg" width="200" class="mx-auto mb-3" alt="Check Icon">
        <h5 class="modal-title fw-bold" id="notifModalLabel">Alasan Penolakan</h5>
        </div>
    </div>
  </div>
  <?php endif; ?>

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
                      <button type="submit" class="btn btn-success px-4 py-2 fw-semibold btn-setujui" id="submitBtn" >Lanjutkan</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

<div class="modal fade" id="modalTolak" tabindex="-1" aria-labelledby="modalTolakLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
      <div class="modal-header border-0 justify-content-center">
        <h4 class="modal-title fw-bold" id="modalTolakLabel" style="font-size: 24px;">Perhatian</h4>
          </div>
          <div class="modal-body">
          <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah anda yakin ingin menolak?</p>
          <div class="mb-4 px-3">
            <textarea id="alasanTolak" class="form-control mb-4" placeholder="Masukkan alasan penolakan" rows="3"></textarea>
            <small id="errorAlasan" class="text-danger d-none">Silakan isi alasan terlebih dahulu.</small>
          </div>
          <div class="d-flex justify-content-between px-5">
          <button type="button" class="btn btn-outline-danger custom-batal px-4 py-2 fw-semibold btn-tolak" data-bs-dismiss="modal">Batalkan</button>
          <button type="button" class="btn btn-success px-4 py-2 fw-semibold btn-setujui" id="tolakBtn">Lanjutkan</button>
        </div>
      </div>
    </div>
  </div>
</div>

    <!-- Modal keluar-->
    <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div style="background-color: rgb(67, 54, 240);">
            <div class="modal-header">
              <h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1>
            </div>
          </div>
          <div class="modal-body mx-auto">Apakah anda yakin ingin keluar?</div>
          <div class="modal-footer justify-content-center border-0">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
          <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
        </div>
      </div>
    </div>
  </div>

<script> 
    const modalKonfirmasi = new bootstrap.Modal(document.getElementById('modalKonfirmasi'));
    const modalTolak = new bootstrap.Modal(document.getElementById('modalTolak'));

    // Buka modal konfirmasi ketika klik tombol Setujui
    document.getElementById('btnSetujui').addEventListener('click', function () {
      modalKonfirmasi.show();
    });

    // Buka modal tolak ketika klik tombol Tolak
    document.getElementById('btnTolak').addEventListener('click', function () {
      modalTolak.show();
    });

    // Jika tekan "Lanjutkan" di modal Setujui
    document.getElementById('submitBtn').addEventListener('click', function () {
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

    document.getElementById('tolakBtn').addEventListener('click', function () {
    const alasan = document.getElementById('alasanTolak').value.trim();

    if (alasan === '') {
      Swal.fire({
        title: 'Gagal',
        text: 'Silakan isi alasan penolakan terlebih dahulu.',
        icon: 'warning',
        confirmButtonText: 'OK',
        confirmButtonColor: '#4B68FB'
      });
      return;
    }

    Swal.fire({
      title: 'Pengajuan Ditolak',
      text: 'Pengajuan sidang berhasil ditolak.',
      icon: 'success',
      confirmButtonText: 'OK',
      confirmButtonColor: '#4B68FB'
    }).then((result) => {
      if (result.isConfirmed) {
        console.log("Alasan penolakan:", alasan);
        history.back();
      }
    });
  });

  // Sidebar Toggle Logic
    let menuToggle = document.querySelector(".NavSide__toggle");
    let sidebar = document.getElementById("main-sidebar");

    menuToggle.onclick = function() {
      menuToggle.classList.toggle("NavSide__toggle--active");
      sidebar.classList.toggle("NavSide__sidebar--active-mobile");
    };
</script>
<script src="../../assets/js/main.js"></script>
</body>

</html>
