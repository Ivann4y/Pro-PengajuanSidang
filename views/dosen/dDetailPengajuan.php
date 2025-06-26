<?php
<<<<<<< HEAD
session_start();
if (!isset($_SESSION['nomor_dosen'])) {
  // fallback sementara untuk testing
  $_SESSION['nomor_dosen'] = '1001';
}
include '../../koneksi/koneksiAndrew.php';

// Get parameters
$id_sidang = isset($_GET['id_sidang']) ? (int)$_GET['id_sidang'] : null;
$tipe = isset($_GET['tipe']) ? $_GET['tipe'] : null;

// // Ambil id_sidang dari URL
// $id_sidang = isset($_GET['id_sidang']) ? (int)$_GET['id_sidang'] : null;

// if (!$id_sidang) {
//     echo "ID sidang tidak ditemukan!";
//     exit;
// }

=======
ob_start(); // Buffer output agar header() dan session_start tidak error
session_start();
include '../../koneksi/koneksiAndrew.php';

// Get parameters
$id_sidang = $_GET['id_sidang'] ?? null;
>>>>>>> 0e34b81016c2628f7e7e764536750ce922b6066c

// Initialize
$sidang = [];
$detail_sidang = [];
$revisions = [];
$all_approved = false;
$anggota = []; // Initialize anggota array

// Fetch submission details
if ($id_sidang) {
<<<<<<< HEAD
  // 1. Ambil info judul, jenis sidang, dan nama kelompok
  $sql = "SELECT s.judul, s.jenis_sidang, s.id_sidang, k.nama_kelompok
          FROM Sidang s
          LEFT JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
          WHERE s.id_sidang = ?";
  $params = [$id_sidang];
  $stmt = sqlsrv_query($conn, $sql, $params);
  $info_sidang = [];
  if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
      $info_sidang = $row;
  }

  // 2. Ambil data utama sidang
  $sql = "SELECT * FROM Sidang WHERE id_sidang = ?";
  $stmt = sqlsrv_query($conn, $sql, $params);
  if ($stmt === false) {
    echo "QUERY ERROR: $sql<br>";
    print_r($params);
    die(print_r(sqlsrv_errors(), true));
  }
  $sidang = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

  // 3. Ambil data detail sidang & revisi
  $sql = "SELECT ds.*, d.nama_dosen 
          FROM Detail_Sidang ds
          JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen
          WHERE ds.id_sidang = ?";
  $stmt = sqlsrv_query($conn, $sql, $params);
  if ($stmt !== false) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
      $detail_sidang[] = $row;
      if (!empty($row['dok_revisi'])) {
        $revisions[] = [
          'id' => $row['id'], // tambahkan kalau kamu ingin mendukung download by ID
          'dokumen' => $row['dok_revisi'],
          'dosen' => $row['nama_dosen'],
          'catatan' => $row['catatan_sidang'],
          'status' => $row['status_revisi']
        ];
      }
    }
  }

  // 4. Cek apakah semua dosen sudah menyetujui
  $sql = "SELECT COUNT(*) as total, 
                 SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved
          FROM Persetujuan_Sidang 
          WHERE id_sidang = ?";
  $stmt = sqlsrv_query($conn, $sql, $params);
  if ($stmt !== false) {
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $all_approved = ($row['total'] > 0 && $row['approved'] == $row['total']);

    // 5. Update status jika semua menyetujui
    if ($all_approved) {
      $sql = "UPDATE Detail_Sidang 
              SET status_revisi = 'Approved' 
              WHERE id_sidang = ?";
      sqlsrv_query($conn, $sql, $params);
    }
  }

  // 6. Handle aksi Approve / Reject
=======
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
>>>>>>> 0e34b81016c2628f7e7e764536750ce922b6066c
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['nomor_dosen'])) {
    $nomor_dosen = $_SESSION['nomor_dosen'];

    if (isset($_POST['approve'])) {
<<<<<<< HEAD
      // Cek apakah sudah pernah disetujui/ditolak
      $sql = "SELECT id FROM Persetujuan_Sidang WHERE id_sidang = ? AND nomor_dosen = ?";
      $params = [$id_sidang, $nomor_dosen];
      $stmt = sqlsrv_query($conn, $sql, $params);

      if ($stmt === false) {
        echo "QUERY ERROR: $sql<br>";
        print_r($params);
        die(print_r(sqlsrv_errors(), true));
      }
      if (sqlsrv_has_rows($stmt)) {
        $sql = "UPDATE Persetujuan_Sidang 
                SET status = 'Approved', catatan = NULL 
                WHERE id_sidang = ? AND nomor_dosen = ?";
      } else {
        $sql = "INSERT INTO Persetujuan_Sidang 
                (id_sidang, nomor_dosen, status) 
                VALUES (?, ?, 'Approved')";
      }
      sqlsrv_query($conn, $sql, $params);
      $_SESSION['success'] = "Sidang berhasil disetujui";
      header("Location: " . $_SERVER['PHP_SELF'] . "?id_sidang=" . $id_sidang);
      exit();
    }

    elseif (isset($_POST['reject'])) {
      $catatan = $_POST['catatan'] ?? '';
      if (empty($catatan)) {
        $_SESSION['error'] = "Silakan isi catatan penolakan";
      } else {
        $sql = "SELECT id FROM Persetujuan_Sidang WHERE id_sidang = ? AND nomor_dosen = ?";
        $params = [$id_sidang, $nomor_dosen];
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
          echo "QUERY ERROR: $sql<br>";
          print_r($params);
          die(print_r(sqlsrv_errors(), true));
        }
        if (sqlsrv_has_rows($stmt)) {
          $sql = "UPDATE Persetujuan_Sidang 
                  SET status = 'Rejected', catatan = ? 
                  WHERE id_sidang = ? AND nomor_dosen = ?";
        } else {
          $sql = "INSERT INTO Persetujuan_Sidang 
                  (id_sidang, nomor_dosen, status, catatan) 
                  VALUES (?, ?, 'Rejected', ?)";
        }

        $params = [$catatan, $id_sidang, $nomor_dosen];
        sqlsrv_query($conn, $sql, $params);

        $_SESSION['success'] = "Sidang berhasil ditolak";
        header("Location: " . $_SERVER['PHP_SELF'] . "?id_sidang=" . $id_sidang);
=======
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
>>>>>>> 0e34b81016c2628f7e7e764536750ce922b6066c
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

// Query data sidang
// $sql = "SELECT s.*, k.nama_kelompok FROM Sidang s
//         LEFT JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
//         WHERE s.id_sidang = ?";
// $params = [$id_sidang];
// $stmt = sqlsrv_query($conn, $sql, $params);
// $sidang = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

<<<<<<< HEAD
// if (!$sidang) {
//     echo "Data sidang tidak ditemukan!";
//     exit;
// }
=======
// Dummy lengkap sesuai id dan tipe
$dummyData = [
  '001' => [
    'no_kelompok' => 'KEL001',
    'anggota' => ['M. Haaris', 'Rudi Nur Salim', 'Siti Rahayu'],
    'mata_kuliah' => 'Tugas Akhir',
    'judul_sidang' => 'Sistem Informasi Penggajian',
    'dosen_pembimbing' => 'Timotius Victory'
  ],
  '002' => [
    'no_kelompok' => 'KEL002',
    'anggota' => ['Maya Sari', 'Fikri Ramadhan', 'Gilang Pratama'],
    'mata_kuliah' => 'Pemrograman 2',
    'judul_sidang' => 'Aplikasi Kasir Modern',
    'dosen_pembimbing' => 'Timotius Victory'
  ]
];

if (isset($dummyData[$id_sidang])) {
  $mahasiswa = $dummyData[$id_sidang];
}

>>>>>>> 0e34b81016c2628f7e7e764536750ce922b6066c
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
  <link rel="stylesheet" href="../../assets/css/dDetailPengajuan.css">
  <link rel="stylesheet" href="../../extra/style.css">
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
        <li class="NavSide_sidebar-item NavSide_sidebar-item--active">
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

<<<<<<< HEAD
      <h2 class="mb-4">Detail Pengajuan</h2>
=======
  <h3 class="mb-4">Detail Pengajuan</h3>
>>>>>>> 0e34b81016c2628f7e7e764536750ce922b6066c

      <div class="card mb-3 info-pengajuan">
        <h5 class="fw-semibold section">Informasi Pengajuan</h5>
        <div class="row mt-2">
          <div class="col-md-6 section">
            <p class="mb-1">Kelompok</p>
            <p class="fw-bold"><?= htmlspecialchars($info_sidang['nama_kelompok'] ?? $info_sidang['id_sidang'] ?? '-') ?></p>

            <p class="mb-1">ID Sidang</p>
            <p class="fw-bold"><?= htmlspecialchars($info_sidang['id_sidang'] ?? '-') ?></p>
          </div>

          <div class="col-md-6 section">
            <p class="mb-1">Judul Sidang</p>
            <p class="fw-bold"><?= htmlspecialchars($info_sidang['judul'] ?? '-') ?></p>

            <p class="mb-1">Jenis Sidang</p>
            <p class="fw-bold">
              <?php 
              $jenis = $info_sidang['jenis_sidang'] ?? '';
              if ($jenis === '0') {
                echo 'Sidang TA';
              } elseif ($jenis === '1') {
                echo 'Sidang Semester';
              } else {
                echo htmlspecialchars($jenis);
              }
              ?>
            </p>

            <p class="mb-1 mt-3">Dosen Pembimbing</p>
            <p class="fw-bold">
              <?php
              $dosen_pembimbing = '-';
              foreach ($detail_sidang as $dosen) {
                if (strtolower($dosen['peran'] ?? '') === 'pembimbing') {
                  $dosen_pembimbing = htmlspecialchars($dosen['nama_dosen']);
                  break;
                }
              }
              echo $dosen_pembimbing;
              ?>
            </p>
          </div>
        </div>
      </div>


<<<<<<< HEAD
      <div class="card mb-3 dokumen-sidang position-relative">
        <h5 class="fw-semibold">Dokumen Sidang</h5>
        <div class="mt-2">
          <?php if (!empty($sidang['dokumen_path'])): ?>
            <a class="text-decoration-none base-tombol berkas-laporan"
              href="../../uploadtesting<?php echo htmlspecialchars($sidang['dokumen_path']); ?>" download>
              <i class="fa-solid fa-file-lines"></i>
              <?php echo htmlspecialchars(basename($sidang['dokumen_path'])); ?>
            </a>
          <?php else: ?>
            <p class="text-muted">Tidak ada dokumen yang diunggah</p>
          <?php endif; ?>
=======
<div class="card mb-3 dokumen-sidang position-relative">
  <h5 class="fw-semibold">Dokumen Sidang</h5>
  <div class="mt-2">
    <a class="file-pill text-decoration-none file-link berkas-laporan" href="#" download>
      <i class="fa-solid fa-file-lines"></i> berkas_laporan_kel-1.pdf
    </a>
  </div>
</div> 

<div class="d-flex justify-content-between">
  <button class="btn-kembali" onclick="location.href='dpengajuan.php'">
    <span class="icon-circle"> <i class="fa-solid fa-arrow-left"></i></span>Kembali</button>
  <div class="d-flex justify-content-between ">
    <button class="btn btn-danger btn-circle me-2" id="btnTolak">Tolak</button>
    <button class="btn btn-success btn-circle" id="btnSetujui">Setujui</button>
  </div>
</div>

    <div class="modal fade" id="notifModal" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <img src="../../assets/img/centang.svg" width="200" class="mx-auto mb-3" alt="Check Icon">
        <h5 class="modal-title fw-bold" id="notifModalLabel"></h5>
>>>>>>> 0e34b81016c2628f7e7e764536750ce922b6066c
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
                      <button type="submit" class="btn btn-success px-4 py-2 fw-semibold btn-setujui" id="submitBtn" >Lanjutkan</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

<<<<<<< HEAD
            <button class="btn btn-danger btn-circle me-2" id="btnTolak" data-bs-toggle="modal" data-bs-target="#modalTolak">Tolak</button>
            <form method="POST" style="display: inline;">
              <button type="submit" name="approve" class="btn btn-success btn-circle" id="btnSetujui">Setujui</button>
            </form>
          </div>
        <?php endif; ?>
      </div>

      <!-- Form untuk rejection -->
      <form id="rejectForm" method="POST" style="display: none;">
        <input type="hidden" name="catatan" id="catatanInput">
        <input type="hidden" name="reject" value="1">
      </form>

      <div class="modal fade" id="notifModal" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content text-center p-4">
            <img src="../../assets/img/centang.svg" width="200" class="mx-auto mb-3" alt="Check Icon">
            <h5 class="modal-title fw-bold" id="notifModalLabel">Alasan Penolakan</h5>
          </div>
        </div>
      </div>

    <div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-labelledby="modalKonfirmasiLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
          <div class="modal-header border-0 justify-content-center">
            <h4 class="modal-title fw-bold" id="modalKonfirmasiLabel" style="font-size: 24px;">Perhatian</h4>
=======
<div class="modal fade" id="modalTolak" tabindex="-1" aria-labelledby="modalTolakLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
      <div class="modal-header border-0 justify-content-center">
        <h4 class="modal-title fw-bold" id="modalTolakLabel" style="font-size: 24px;">Perhatian</h4>
>>>>>>> 0e34b81016c2628f7e7e764536750ce922b6066c
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

<<<<<<< HEAD
      // Buka modal konfirmasi ketika klik tombol Setujui
      document.getElementById('btnSetujui').addEventListener('click', function(e) {
        e.preventDefault();
        modalKonfirmasi.show();
      });
=======
    // Buka modal konfirmasi ketika klik tombol Setujui
    document.getElementById('btnSetujui').addEventListener('click', function () {
      modalKonfirmasi.show();
    });
>>>>>>> 0e34b81016c2628f7e7e764536750ce922b6066c

    // Buka modal tolak ketika klik tombol Tolak
    document.getElementById('btnTolak').addEventListener('click', function () {
      modalTolak.show();
    });

<<<<<<< HEAD
      // Jika tekan "Lanjutkan" di modal Setujui
      document.getElementById('submitBtn').addEventListener('click', function() {
        // Submit the approve form
        document.querySelector('form[method="POST"]').submit();
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

        // Set the catatan value and submit the reject form
        document.getElementById('catatanInput').value = alasan;
        document.getElementById('rejectForm').submit();
=======
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
>>>>>>> 0e34b81016c2628f7e7e764536750ce922b6066c
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
