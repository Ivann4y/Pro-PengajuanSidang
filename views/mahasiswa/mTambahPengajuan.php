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
$success_message = '';
$error_message = '';

// Get logged in student data
$nim_mahasiswa = $_SESSION['nim'];
$nama_mahasiswa = 'Mahasiswa';
if (isset($_SESSION['user_data']['nama_mhs'])) {
    $nama_mahasiswa = $_SESSION['user_data']['nama_mhs'];
}

// Get group information from URL parameters (for rejected pengajuan)
$nomor_kelompok = $_GET['nomor_kelompok'] ?? null;
$jenis_sidang = $_GET['jenis_sidang'] ?? null;
$tahun_ajaran = $_GET['tahun_ajaran'] ?? null;

// Get student's groups
$sql_groups = "
    SELECT DISTINCT
        k.nomor_kelompok,
        k.jenis_sidang,
        k.tahun_ajaran,
        k.id_matkul,
        mk.nama_matkul,
        d.nama_dosen AS nama_pembimbing
    FROM Kelompok k
    JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul
    LEFT JOIN Bimbingan b ON k.id_kelompok = b.id_kelompok AND b.isPembimbing = 1
    LEFT JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen
    WHERE k.nim = ?
    ORDER BY k.tahun_ajaran DESC, k.jenis_sidang ASC, k.nomor_kelompok ASC
";

$stmt_groups = sqlsrv_query($conn, $sql_groups, [$nim_mahasiswa]);
$available_groups = [];

if ($stmt_groups) {
    while ($row = sqlsrv_fetch_array($stmt_groups, SQLSRV_FETCH_ASSOC)) {
        $available_groups[] = $row;
    }
}

// Check if group already has a pengajuan
function checkGroupPengajuan($conn, $nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul) {
    $sql = "
        SELECT COUNT(*) as count
        FROM Sidang s
        JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
        WHERE k.nomor_kelompok = ? 
          AND k.tahun_ajaran = ? 
          AND k.jenis_sidang = ? 
          AND k.id_matkul = ?
          AND s.status_ajuan IN ('Draft', 'Pending', 'Approved')
    ";
    $result = sqlsrv_query($conn, $sql, [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul]);
    return sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)['count'] > 0;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_group = $_POST['selected_group'] ?? '';
    $judul = trim($_POST['judul'] ?? '');
    $aksi = $_POST['aksi'] ?? 'Simpan'; // Default to Simpan if not specified
    
    if (empty($selected_group) || empty($judul)) {
        $error_message = "Pilih kelompok dan isi judul sidang.";
    } else {
        // Parse group information
        $group_info = explode('|', $selected_group);
        $nomor_kelompok = $group_info[0];
        $tahun_ajaran = $group_info[1];
        $jenis_sidang = $group_info[2];
        $id_matkul = $group_info[3];
        
        // Check if group already has a pengajuan
        if (checkGroupPengajuan($conn, $nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul)) {
            $error_message = "Kelompok ini sudah memiliki pengajuan yang aktif.";
        } else {
            // Get student's id_kelompok for this group
            $sql_get_id = "
                SELECT id_kelompok 
                FROM Kelompok 
                WHERE nim = ? AND nomor_kelompok = ? AND tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ?
            ";
            $stmt_get_id = sqlsrv_query($conn, $sql_get_id, [$nim_mahasiswa, $nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul]);
            $id_kelompok = sqlsrv_fetch_array($stmt_get_id, SQLSRV_FETCH_ASSOC)['id_kelompok'];
            
            // Handle file upload
            $dok_laporan_content = null;
            if (isset($_FILES['DokumenSidang']) && $_FILES['DokumenSidang']['error'] == UPLOAD_ERR_OK) {
                $file = $_FILES['DokumenSidang'];
                if ($file['size'] > 10 * 1024 * 1024) {
                    $error_message = "Ukuran file melebihi 10MB.";
                } else {
                    $allowedTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'];
                    if (!in_array(mime_content_type($file['tmp_name']), $allowedTypes)) {
                        $error_message = "Tipe file tidak diizinkan.";
                    } else {
                        $dok_laporan_content = file_get_contents($file['tmp_name']);
                    }
                }
            }
            
            if (empty($error_message)) {
                sqlsrv_begin_transaction($conn);
                try {
                    // Determine status based on aksi
                    $status_ajuan = ($aksi === 'Kirim') ? 'Pending' : 'Draft';
                    
                    // Insert into Sidang table
                    $sql_sidang = "
                        INSERT INTO Sidang (id_kelompok, judul, status_ajuan, dok_laporan, waktu_pengumpulan) 
                        VALUES (?, ?, ?, ?, GETDATE())
                    ";
                    $stmt_sidang = sqlsrv_query($conn, $sql_sidang, [$id_kelompok, $judul, $status_ajuan, $dok_laporan_content]);
                    
                    if (!$stmt_sidang) {
                        throw new Exception("Gagal membuat pengajuan: " . print_r(sqlsrv_errors(), true));
                    }
                    
                    sqlsrv_commit($conn);
                    $status_text = ($status_ajuan === 'Pending') ? 'berhasil dikirim!' : 'draft berhasil dibuat!';
                    $_SESSION['success_message'] = "Pengajuan $status_text";
                    header("Location: mPengajuan.php");
                    exit();
                    
                } catch (Exception $e) {
                    sqlsrv_rollback($conn);
                    $error_message = $e->getMessage();
                }
            }
        }
    }
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
  <title>Tambah Pengajuan Sidang</title>
  <style>
    .file-selected { background-color: #d1fae5 !important; border: 1px solid #10b981 !important; }
    .file-name { color: #047857; font-weight: 500; font-size: 0.875rem; }
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
            <h5 class="fw-bold mt-4 mb-3">Tambah Pengajuan Sidang</h5>
            <hr>
          </div>

          <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="selected_group" class="form-label">Pilih Kelompok
                <span class="text-danger">* </span>
              </label>
              <select class="form-select" id="selected_group" name="selected_group" required>
                <option value="">Pilih Kelompok</option>
                <?php foreach ($available_groups as $group): ?>
                  <?php 
                  $group_value = $group['nomor_kelompok'] . '|' . $group['tahun_ajaran'] . '|' . $group['jenis_sidang'] . '|' . $group['id_matkul'];
                  $group_label = "Kelompok " . $group['nomor_kelompok'] . " - " . $group['nama_matkul'] . " (" . $group['jenis_sidang'] . ") - " . $group['tahun_ajaran'];
                  
                  // Check if this group already has a pengajuan
                  $has_pengajuan = checkGroupPengajuan($conn, $group['nomor_kelompok'], $group['tahun_ajaran'], $group['jenis_sidang'], $group['id_matkul']);
                  
                  // Pre-select if coming from rejected pengajuan
                  $selected = '';
                  if ($nomor_kelompok && $jenis_sidang && $tahun_ajaran) {
                      if ($group['nomor_kelompok'] == $nomor_kelompok && 
                          $group['jenis_sidang'] == $jenis_sidang && 
                          $group['tahun_ajaran'] == $tahun_ajaran) {
                          $selected = 'selected';
                      }
                  }
                  ?>
                  <option value="<?php echo $group_value; ?>" <?php echo $selected; ?> <?php echo $has_pengajuan ? 'disabled' : ''; ?>>
                    <?php echo $group_label; ?>
                    <?php if ($has_pengajuan): ?> (Sudah ada pengajuan)<?php endif; ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">Pilih kelompok yang akan mengajukan sidang. Kelompok yang sudah memiliki pengajuan aktif tidak dapat dipilih.</div>
            </div>
            
            <div class="mb-3">
              <label for="judul" class="form-label">Judul Sidang
                <span class="text-danger">* </span>
              </label>
              <input type="text" class="forM form-control" id="judul" name="judul" placeholder="Masukkan Judul Sidang" required />
            </div>
            
            <div class="row">
              <div class="mb-4">
                <div class="p-4 rounded bg-light border text-start">
                  <h6 class="fw-bold text-dark">Dokumen Sidang</h6>
                  <p class="small text-info mb-2"><i class="fa-solid fa-info-circle"></i> Upload file dengan format pdf, docx, pptx, dan zip (maksimal 10MB)</p>
                  <div id="DokumenSidangForm">
                    <label class="upload-box w-100 mt-3 text-center">
                      <input type="file" id="DokumenSidang" name="DokumenSidang" accept=".pdf,.docx,.pptx,.zip" hidden />
                      <div class="upload-content">
                        <svg id="uploadIcon" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#6c757d" class="bi bi-upload" viewBox="0 0 16 16">
                          <path d="M.5 9.9a.5.5 0 0 1 .5.5v3.6a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5V10.4a.5.5 0 0 1 1 0v3.6a1.5 1.5 0 0 1-1.5 1.5H1.5A1.5 1.5 0 0 1 0 14V10.4a.5.5 0 0 1 .5-.5z" />
                          <path d="M7.646 1.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 3.207V10a.5.5 0 0 1-1 0V3.207L5.354 5.354a.5.5 0 1 1-.708-.708l3-3z" />
                        </svg>
                        <p class="mt-2 text-muted small DokumenLabelText">Upload file dengan format pdf, docx, pptx, dan zip</p>
                        <div id="fileNameDisplay" class="file-name mt-2"></div>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
              <div class="bintangMerah">
                <p>* : wajib diisi</p>
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <div>
                <button type="button" class="btn btn-kembali" onclick="window.location.href='mPengajuan.php'">
                  <span class="icon-circle"><i class="fa-solid fa-arrow-left"></i></span> Kembali
                </button>
              </div>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary" id="btnSimpan">Simpan</button>
                <button type="button" class="btn btn-setuju" id="btnKirim">Kirim</button>
              </div>
            </div>
          </form>
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
    // Menampilkan notifikasi sukses/gagal dari PHP
    <?php if (!empty($success_message)): ?>
    Swal.fire({
        title: 'Berhasil!',
        text: '<?php echo addslashes($success_message); ?>',
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#4B68FB'
    }).then(() => {
        window.location.href = 'mPengajuan.php';
    });
    <?php elseif (!empty($error_message)): ?>
    Swal.fire({
        title: 'Gagal!',
        html: '<?php echo addslashes($error_message); ?>',
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#4B68FB'
    });
    <?php endif; ?>
    
    // Sidebar Toggle Logic 
    let menuToggle = document.querySelector(".NavSide__toggle");
    let sidebar = document.getElementById("main-sidebar");
    if (menuToggle) {
      menuToggle.onclick = function() {
        menuToggle.classList.toggle("NavSide__toggle--active");
        sidebar.classList.toggle("NavSide__sidebar--active-mobile");
      };
    }

    // File upload UI handling
    const DokumenSidang = document.getElementById('DokumenSidang');
    const uploadBox = document.querySelector('.upload-box');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const uploadIcon = document.querySelector('#uploadIcon');
    const labelText = document.querySelector('.DokumenLabelText');

    DokumenSidang.addEventListener('change', function() {
      if (this.files.length > 0) {
        fileNameDisplay.textContent = this.files[0].name;
        fileNameDisplay.style.display = 'block';
        uploadIcon.style.display = 'none';
        labelText.style.display = 'none';
        uploadBox.classList.add('file-selected');
      } else {
        fileNameDisplay.textContent = '';
        fileNameDisplay.style.display = 'none';
        uploadIcon.style.display = 'block';
        labelText.style.display = 'block';
        uploadBox.classList.remove('file-selected');
      }
    });

    // Form validation and button handlers
    const btnSimpan = document.getElementById('btnSimpan');
    const btnKirim = document.getElementById('btnKirim');
    const form = document.querySelector('form');

    // Add hidden input for aksi
    const aksiInput = document.createElement('input');
    aksiInput.type = 'hidden';
    aksiInput.name = 'aksi';
    aksiInput.id = 'aksi';
    aksiInput.value = 'Simpan'; // Default value
    form.appendChild(aksiInput);

    // Simpan button handler
    btnSimpan.addEventListener('click', function(e) {
      e.preventDefault();
      if (validateForm()) {
        aksiInput.value = 'Simpan';
        form.submit();
      }
    });

    // Kirim button handler
    btnKirim.addEventListener('click', function(e) {
      e.preventDefault();
      if (validateForm()) {
        aksiInput.value = 'Kirim';
        form.submit();
      }
    });

    // Form validation function
    function validateForm() {
      const selectedGroup = document.getElementById('selected_group').value;
      const judul = document.getElementById('judul').value.trim();
      const file = document.getElementById('DokumenSidang').files[0];

      if (!selectedGroup) {
        Swal.fire({
          title: 'Pilih Kelompok!',
          text: 'Silakan pilih kelompok terlebih dahulu.',
          icon: 'error',
          confirmButtonText: 'OK',
          confirmButtonColor: '#4B68FB'
        });
        return false;
      }

      if (!judul) {
        Swal.fire({
          title: 'Judul Kosong!',
          text: 'Silakan isi judul sidang.',
          icon: 'error',
          confirmButtonText: 'OK',
          confirmButtonColor: '#4B68FB'
        });
        return false;
      }

      if (!file) {
        Swal.fire({
          title: 'File Kosong!',
          text: 'Silakan upload dokumen sidang.',
          icon: 'error',
          confirmButtonText: 'OK',
          confirmButtonColor: '#4B68FB'
        });
        return false;
      }

      return true;
    }
  </script>
</body>
</html>