<?php
ob_start(); // Buffer output agar header() dan session_start tidak error
session_start();
include '../../koneksi/koneksiAndrew.php';

// Get parameters
$id_sidang = $_GET['id_sidang'] ?? null;

// Initialize
$sidang = [];
$detail_sidang = [];
$revisions = [];
$all_approved = false;

// Fetch submission details
if ($id_sidang) {
  // Get main submission
  $stmt = sqlsrv_query($conn, "SELECT * FROM Sidang WHERE id_sidang = ?", [$id_sidang]);
  if ($stmt === false) die(print_r(sqlsrv_errors(), true));
  $sidang = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

  // Detail/Revisions
  $stmt = sqlsrv_query($conn, "
        SELECT ds.*, d.nama_dosen 
        FROM Detail_Sidang ds
        JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen
        WHERE ds.id_sidang = ?", [$id_sidang]);
  while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $detail_sidang[] = $row;
    if (!empty($row['dok_revisi'])) {
      $revisions[] = [
        'dokumen' => $row['dok_revisi'],
        'dosen' => $row['nama_dosen'],
        'catatan' => $row['catatan_sidang'],
        'status' => $row['status_revisi'],
        'id' => $row['id'] ?? null
      ];
    }
  }

  // Check approval
  $stmt = sqlsrv_query($conn, "
        SELECT COUNT(*) as total, 
               SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved
        FROM Persetujuan_Sidang WHERE id_sidang = ?", [$id_sidang]);
  $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
  $all_approved = ($row['total'] > 0 && $row['approved'] == $row['total']);

  // Update status if needed
  if ($all_approved) {
    sqlsrv_query($conn, "UPDATE Detail_Sidang SET status_revisi = 'Approved' WHERE id_sidang = ?", [$id_sidang]);
  }

  // Handle approval/rejection
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['nomor_dosen'])) {
    $nomor_dosen = $_SESSION['nomor_dosen'];

    if (isset($_POST['approve'])) {
      // Approval
      $cek = sqlsrv_query($conn, "SELECT id FROM Persetujuan_Sidang WHERE id_sidang = ? AND nomor_dosen = ?", [$id_sidang, $nomor_dosen]);
      if (sqlsrv_has_rows($cek)) {
        sqlsrv_query($conn, "UPDATE Persetujuan_Sidang SET status = 'Approved', catatan = NULL WHERE id_sidang = ? AND nomor_dosen = ?", [$id_sidang, $nomor_dosen]);
      } else {
        sqlsrv_query($conn, "INSERT INTO Persetujuan_Sidang (id_sidang, nomor_dosen, status) VALUES (?, ?, 'Approved')", [$id_sidang, $nomor_dosen]);
      }
      $_SESSION['success'] = "Sidang berhasil disetujui";
      header("Location: " . $_SERVER['PHP_SELF'] . "?id_sidang=$id_sidang");
      exit();
    } elseif (isset($_POST['reject'])) {
      $catatan = trim($_POST['catatan'] ?? '');
      if (empty($catatan)) {
        $_SESSION['error'] = "Silakan isi catatan penolakan";
      } else {
        $cek = sqlsrv_query($conn, "SELECT id FROM Persetujuan_Sidang WHERE id_sidang = ? AND nomor_dosen = ?", [$id_sidang, $nomor_dosen]);
        if (sqlsrv_has_rows($cek)) {
          sqlsrv_query($conn, "UPDATE Persetujuan_Sidang SET status = 'Rejected', catatan = ? WHERE id_sidang = ? AND nomor_dosen = ?", [$catatan, $id_sidang, $nomor_dosen]);
        } else {
          sqlsrv_query($conn, "INSERT INTO Persetujuan_Sidang (id_sidang, nomor_dosen, status, catatan) VALUES (?, ?, 'Rejected', ?)", [$id_sidang, $nomor_dosen, $catatan]);
        }
        $_SESSION['success'] = "Sidang berhasil ditolak";
        header("Location: " . $_SERVER['PHP_SELF'] . "?id_sidang=$id_sidang");
        exit();
      }
    }
  }
}

// Download file
if (isset($_GET['download'])) {
  $doc_type = $_GET['download'];
  $baseDir = '../../uploadtesting/';
  $filepath = '';

  if ($doc_type === 'main' && !empty($sidang['dokumen_path'])) {
    $filepath = $baseDir . ltrim($sidang['dokumen_path'], '/');
  } elseif (is_numeric($doc_type)) {
    foreach ($revisions as $rev) {
      if ($rev['id'] == $doc_type && !empty($rev['dokumen'])) {
        $filepath = $baseDir . ltrim($rev['dokumen'], '/');
        break;
      }
    }
  }

  if (!empty($filepath) && file_exists($filepath)) {
    $filename = basename($filepath);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit;
  } else {
    die("File tidak ditemukan.");
  }
}

// Dummy data jika parameter belum lengkap
$nim = $_GET['nim'] ?? 'N/A';
$tipe = $_GET['tipe'] ?? 'N/A';
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
  <link rel="stylesheet" href="../../assets/css/dDetailPengajuan.css" />
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="../../extra/style.css">
  <link rel="stylesheet" href="../../assets/css/dDetailPengajuan.css">
  <title>Detail Pengajuan</title>
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
            <a class="text-decoration-none base-tombol berkas-laporan"
              href="../../uploadtesting<?php echo htmlspecialchars($sidang['dokumen_path']); ?>" download>
              <i class="fa-solid fa-file-lines"></i>
              <?php echo htmlspecialchars(basename($sidang['dokumen_path'])); ?>
            </a>
          <?php endif; ?>
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
              <button type="submit" class="btn btn-success px-4 py-2 fw-semibold btn-setujui" id="submitBtn">Lanjutkan</button>
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
      document.getElementById('btnSetujui').addEventListener('click', function() {
        modalKonfirmasi.show();
      });

      // Buka modal tolak ketika klik tombol Tolak
      document.getElementById('btnTolak').addEventListener('click', function() {
        modalTolak.show();
      });

      // Jika tekan "Lanjutkan" di modal Setujui
      document.getElementById('submitBtn').addEventListener('click', function() {
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

      document.getElementById('tolakBtn').addEventListener('click', function() {
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