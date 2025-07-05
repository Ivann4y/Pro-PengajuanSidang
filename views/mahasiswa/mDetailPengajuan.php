<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$path_to_root = '../../';

// 1. Cek keamanan sesi: login dan role
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php");
    exit();
}

include '../../koneksi/koneksiAndrew.php';

// Get logged in student data
$nim_mahasiswa = $_SESSION['nim'];
$nama_mahasiswa = 'Mahasiswa';
if (isset($_SESSION['user_data']['nama_mhs'])) {
    $nama_mahasiswa = $_SESSION['user_data']['nama_mhs'];
}

// Get sidang ID from URL
$id_sidang = isset($_GET['id_sidang']) ? (int)$_GET['id_sidang'] : 0;

if (!$id_sidang) {
    die("Error: ID Sidang tidak valid.");
}

// Get sidang data
$sql_sidang = "
    SELECT 
        s.id_sidang, s.judul, s.status_ajuan, s.waktu_pengumpulan,
        k.nomor_kelompok, k.jenis_sidang, k.tahun_ajaran,
        mk.nama_matkul,
        d.nama_dosen AS nama_pembimbing
    FROM Sidang s
    JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
    JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul
    LEFT JOIN Bimbingan b ON k.id_kelompok = b.id_kelompok AND b.isPembimbing = 1
    LEFT JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen
    WHERE s.id_sidang = ? AND k.nim = ?
";

$stmt_sidang = sqlsrv_query($conn, $sql_sidang, [$id_sidang, $nim_mahasiswa]);

if (!$stmt_sidang) {
    die("Error saat mengambil data sidang.");
}

$sidang_data = sqlsrv_fetch_array($stmt_sidang, SQLSRV_FETCH_ASSOC);

if (!$sidang_data) {
    die("Data sidang tidak ditemukan atau Anda tidak memiliki akses.");
}

// Get group members
$sql_members = "
    SELECT m.nim, m.nama_mhs
    FROM Kelompok k
    JOIN Mahasiswa m ON k.nim = m.nim
    WHERE k.nomor_kelompok = ? 
      AND k.tahun_ajaran = ? 
      AND k.jenis_sidang = ? 
      AND k.id_matkul = ?
    ORDER BY m.nama_mhs ASC
";

$stmt_members = sqlsrv_query($conn, $sql_members, [
    $sidang_data['nomor_kelompok'], 
    $sidang_data['tahun_ajaran'], 
    $sidang_data['jenis_sidang'], 
    $sidang_data['id_matkul']
]);

$group_members = [];
if ($stmt_members) {
    while ($row = sqlsrv_fetch_array($stmt_members, SQLSRV_FETCH_ASSOC)) {
        $group_members[] = $row;
    }
}

// Handle file download
if (isset($_GET['download']) && $_GET['download'] === 'main') {
    $sql_download = "SELECT dok_laporan FROM Sidang WHERE id_sidang = ?";
    $stmt_download = sqlsrv_query($conn, $sql_download, [$id_sidang]);
    
    if ($stmt_download && $row = sqlsrv_fetch_array($stmt_download, SQLSRV_FETCH_ASSOC)) {
        if (!empty($row['dok_laporan'])) {
            $file_content = $row['dok_laporan'];
            
            // Determine file extension
            $magic_pdf = "\x25\x50\x44\x46"; // %PDF
            $magic_docx = "\x50\x4b\x03\x04"; // PK..
            
            $file_extension = ".dat";
            if (substr($file_content, 0, 4) === $magic_pdf) {
                $file_extension = ".pdf";
            } elseif (substr($file_content, 0, 4) === $magic_docx) {
                $file_extension = ".zip";
            }
            
            $filename = "Laporan_Sidang_" . $id_sidang . $file_extension;
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            echo $file_content;
            exit;
        }
    }
    die("Dokumen tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../../assets/css/mEditPengajuan.css" />
  <link rel="stylesheet" href="../../assets/css/style.css" />
  <link rel="stylesheet" href="../../extra/style.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <title>Detail Pengajuan Sidang</title>
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
        <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
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
    </div>

    <main class="NavSide__main-content" id="mPengajuan">
      <div class="container-fluid">
      <div class="dashboard-header">
        <h2 class="text-heading"><?php echo htmlspecialchars($nama_mahasiswa); ?> (Mahasiswa)</h2>
      </div>
        <div class="row">
          <div class="col-12">
            <h5 class="fw-bold mt-4 mb-3">Detail Pengajuan Sidang</h5>
            <hr>
          </div>

          <div class="card mb-3">
            <div class="card-body">
              <h6 class="fw-bold">Informasi Pengajuan</h6>
              <div class="row">
                <div class="col-md-6">
                  <p class="mb-1"><strong>ID Sidang:</strong> <?php echo htmlspecialchars($sidang_data['id_sidang']); ?></p>
                  <p class="mb-1"><strong>Judul:</strong> <?php echo htmlspecialchars($sidang_data['judul']); ?></p>
                  <p class="mb-1"><strong>Status:</strong> 
                    <?php 
                    $statusClass = '';
                    $statusText = '';
                    switch($sidang_data['status_ajuan']) {
                        case 'Draft':
                            $statusClass = 'badge bg-secondary';
                            $statusText = 'Draft';
                            break;
                        case 'Pending':
                            $statusClass = 'badge bg-warning';
                            $statusText = 'Pending';
                            break;
                        case 'Approved':
                            $statusClass = 'badge bg-success';
                            $statusText = 'Diterima';
                            break;
                        case 'Rejected':
                            $statusClass = 'badge bg-danger';
                            $statusText = 'Ditolak';
                            break;
                        default:
                            $statusClass = 'badge bg-secondary';
                            $statusText = 'Unknown';
                    }
                    ?>
                    <span class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                  </p>
                </div>
                <div class="col-md-6">
                  <p class="mb-1"><strong>Kelompok:</strong> <?php echo htmlspecialchars($sidang_data['nomor_kelompok']); ?></p>
                  <p class="mb-1"><strong>Mata Kuliah:</strong> <?php echo htmlspecialchars($sidang_data['nama_matkul']); ?></p>
                  <p class="mb-1"><strong>Jenis Sidang:</strong> <?php echo htmlspecialchars($sidang_data['jenis_sidang']); ?></p>
                  <p class="mb-1"><strong>Tahun Ajaran:</strong> <?php echo htmlspecialchars($sidang_data['tahun_ajaran']); ?></p>
                  <p class="mb-1"><strong>Dosen Pembimbing:</strong> <?php echo htmlspecialchars($sidang_data['nama_pembimbing'] ?? 'N/A'); ?></p>
                  <p class="mb-1"><strong>Waktu Pengumpulan:</strong> <?php echo $sidang_data['waktu_pengumpulan'] ? date('d/m/Y H:i', strtotime($sidang_data['waktu_pengumpulan']->format('Y-m-d H:i:s'))) : 'N/A'; ?></p>
                </div>
              </div>
            </div>
          </div>

          <div class="card mb-3">
            <div class="card-body">
              <h6 class="fw-bold">Anggota Kelompok</h6>
              <ul class="list-group list-group-flush">
                <?php foreach ($group_members as $member): ?>
                  <li class="list-group-item">
                    <?php echo htmlspecialchars($member['nama_mhs']); ?> (<?php echo htmlspecialchars($member['nim']); ?>)
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>

          <div class="card mb-3">
            <div class="card-body">
              <h6 class="fw-bold">Dokumen Laporan</h6>
              <?php
              // Check if document exists
              $sql_check_doc = "SELECT dok_laporan FROM Sidang WHERE id_sidang = ?";
              $stmt_check_doc = sqlsrv_query($conn, $sql_check_doc, [$id_sidang]);
              $doc_data = sqlsrv_fetch_array($stmt_check_doc, SQLSRV_FETCH_ASSOC);
              ?>
              <?php if (!empty($doc_data['dok_laporan'])): ?>
                <a class="btn btn-primary" href="?id_sidang=<?php echo $id_sidang; ?>&download=main">
                  <i class="fa-solid fa-download me-2"></i>Download Dokumen
                </a>
              <?php else: ?>
                <p class="text-muted">Tidak ada dokumen yang diunggah</p>
              <?php endif; ?>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <div>
              <button type="button" class="btn btn-kembali" onclick="window.location.href='mPengajuan.php'">
                <span class="icon-circle"><i class="fa-solid fa-arrow-left"></i></span> Kembali
              </button>
            </div>
            <?php if ($sidang_data['status_ajuan'] === 'Draft'): ?>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-warning" onclick="window.location.href='mEditPengajuan.php'">
                  <i class="fa-solid fa-edit me-2"></i>Edit Pengajuan
                </button>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <div class="modal fade" id="logMBeranda" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header"><h1 class="modal-title fs-5">Perhatian!</h1></div>
                    <div class="modal-body"><p>Apakah Anda yakin ingin keluar?</p></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                        <button type="button" class="btn btn-danger" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </main>
  </div>

  <script>
    // Sidebar Toggle Logic 
    let menuToggle = document.querySelector(".NavSide__toggle");
    let sidebar = document.getElementById("main-sidebar");
    if (menuToggle) {
      menuToggle.onclick = function() {
        menuToggle.classList.toggle("NavSide__toggle--active");
        sidebar.classList.toggle("NavSide__sidebar--active-mobile");
      };
    }
  </script>
</body>
</html> 