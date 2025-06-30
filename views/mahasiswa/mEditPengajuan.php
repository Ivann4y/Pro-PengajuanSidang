<?php
// =================================================================
// BLOK PHP YANG DIPERBAIKI (Backend Logic)
// =================================================================
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

// Get logged in student name
$nama_mahasiswa = 'Mahasiswa';
if (isset($_SESSION['user_data']['nama_mhs'])) {
    $nama_mahasiswa = $_SESSION['user_data']['nama_mhs'];
} elseif (isset($_SESSION['nim'])) {
    // Fetch name from database if not in session
    $sql_nama = "SELECT nama_mhs FROM Mahasiswa WHERE nim = ?";
    $stmt_nama = sqlsrv_query($conn, $sql_nama, [$_SESSION['nim']]);
    if ($stmt_nama && $row_nama = sqlsrv_fetch_array($stmt_nama, SQLSRV_FETCH_ASSOC)) {
        $nama_mahasiswa = $row_nama['nama_mhs'];
    }
}

// STEP 1: Determine the id_sidang. Prioritize the form submission value.
$id_sidang = 0;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // If it's a POST request, the ID must be in the form data.
    $id_sidang = isset($_POST['id_sidang']) ? (int)$_POST['id_sidang'] : 0;
}

// Security check: If no ID, redirect away.
if ($id_sidang <= 0) {
     die("
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <body onload=\"Swal.fire({title: 'Error!', text: 'ID Sidang tidak valid atau tidak ditemukan.', icon: 'error'}).then(() => window.location.href = 'mPengajuan.php');\"></body>
    ");
}

// STEP 2: Fetch existing data for the form *unconditionally*.
// This ensures the form is always populated, even if a submission fails.
$query_existing = "
    SELECT s.judul, s.id_kelompok, ds.id_matkul, s.dok_laporan
    FROM Sidang s
    LEFT JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
    WHERE s.id_sidang = ?
";
$stmt_existing = sqlsrv_query($conn, $query_existing, [$id_sidang]);
if ($stmt_existing === false) die("Error saat mengambil data awal.");
$existing_data = sqlsrv_fetch_array($stmt_existing, SQLSRV_FETCH_ASSOC);

if (!$existing_data) {
    die("Data sidang dengan ID $id_sidang tidak ditemukan.");
}

// Now populate variables for the form
$existing_judul = $existing_data['judul'];
$existing_id_matkul = $existing_data['id_matkul'];
$existing_id_kelompok = $existing_data['id_kelompok'];    
$file_exists = !empty($existing_data['dok_laporan']);

// --- REPLACE WITH THIS (AFTER) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])) {
    // Start a transaction to ensure all database changes succeed or fail together.
    sqlsrv_begin_transaction($conn);
    try {
        // 1. Initialize variables for file handling
        $dok_laporan_content = null;
        $updateFile = false;

        // 2. Process file upload if a new file is provided
        if (isset($_FILES['DokumenSidang']) && $_FILES['DokumenSidang']['error'] == UPLOAD_ERR_OK) {
            $file = $_FILES['DokumenSidang'];
            // Validate file size (max 10MB)
            if ($file['size'] > 10 * 1024 * 1024) {
                throw new Exception("Ukuran file tidak boleh melebihi 10MB.");
            }
            // Validate file type
            $allowedTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'];
            if (!in_array(mime_content_type($file['tmp_name']), $allowedTypes)) {
                throw new Exception("Tipe file tidak diizinkan. Hanya PDF, DOCX, PPTX, dan ZIP yang diperbolehkan.");
            }
            
            // Read file content as binary data for the database
            $dok_laporan_content = file_get_contents($file['tmp_name']);
            $updateFile = true; // Mark that the file needs to be updated in the DB
        }

        // 3. Get data from the form
        $judul = trim($_POST['judul']);
        $id_matkul_terpilih = $_POST['matkul'];
        if ($_POST['aksi'] == 'Kirim') {
            $status_ajuan = 0; // Sudah dikirim, menunggu dosen
        } else {
            $status_ajuan = NULL; // Simpan sebagai draft
        }

        if (empty($judul) || empty($id_matkul_terpilih)) throw new Exception("Judul dan Mata Kuliah wajib diisi.");

        // Basic validation for required fields
        if (empty($judul) || empty($id_matkul_terpilih)) {
            throw new Exception("Judul dan Mata Kuliah wajib diisi.");
        }

        // 4. Determine 'jenis_sidang' based on the selected Mata Kuliah
        $sql_matkul_name = "SELECT nama_matkul FROM dbo.MataKuliah WHERE id_matkul = ?";
        $stmt_matkul_name = sqlsrv_query($conn, $sql_matkul_name, [$id_matkul_terpilih]);
        if ($stmt_matkul_name === false) {
            throw new Exception("Gagal mengambil nama mata kuliah.");
        }
        $nama_matkul_row = sqlsrv_fetch_array($stmt_matkul_name, SQLSRV_FETCH_ASSOC);
        $nama_matkul_terpilih = $nama_matkul_row['nama_matkul'];
        
        // Set jenis_sidang to 0x00 for 'Tugas Akhir' and 0x01 for others (case-insensitive)
        $jenis_sidang_bit = (strcasecmp($nama_matkul_terpilih, 'Tugas Akhir') == 0) ? 0x00 : 0x01;
        
        // 5. Build and execute the query to update the 'Sidang' table
        $sql_update_sidang = "UPDATE dbo.Sidang SET judul = ?, status_ajuan = ?, jenis_sidang = ?, waktu_pengumpulan = GETDATE()";
        $params_update_sidang = [$judul, $status_ajuan, $jenis_sidang_bit];
        
        // If a new file was uploaded, add it to the SQL query and parameters
        if ($updateFile) {
            $sql_update_sidang .= ", dok_laporan = ?";
            // SQL Server requires special handling for binary stream data
            $params_update_sidang[] = ['Value' => $dok_laporan_content, 'Type' => SQLSRV_PARAM_IN, 'PhpType' => SQLSRV_PHPTYPE_STREAM(SQLSRV_ENC_BINARY)];
        }
        
        $sql_update_sidang .= " WHERE id_sidang = ?";
        $params_update_sidang[] = $id_sidang;
        
        $stmt_update_sidang = sqlsrv_prepare($conn, $sql_update_sidang, $params_update_sidang);
        if (!sqlsrv_execute($stmt_update_sidang)) {
            throw new Exception("Gagal mengupdate data Sidang. Error: " . print_r(sqlsrv_errors(), true));
        }

        // 6. Build and execute the query to update the 'Detail_Sidang' table (for Mata Kuliah)
        $sql_update_detail = "UPDATE dbo.Detail_Sidang SET id_matkul = ? WHERE id_sidang = ?";
        $params_update_detail = [$id_matkul_terpilih, $id_sidang];
        
        $stmt_update_detail = sqlsrv_prepare($conn, $sql_update_detail, $params_update_detail);
        if (!sqlsrv_execute($stmt_update_detail)) {
            throw new Exception("Gagal mengupdate Detail Sidang. Error: " . print_r(sqlsrv_errors(), true));
        }

        // 7. If all queries were successful, commit the changes to the database
        sqlsrv_commit($conn);
        if (is_null($status_ajuan)) {
            $success_message = 'Pengajuan Berhasil Disimpan!';
        } elseif ($status_ajuan === 0) {
            $success_message = 'Pengajuan Berhasil Dikirim!';
        }

    } catch (Exception $e) {
        // If any error occurred, roll back all changes
        sqlsrv_rollback($conn);
        $error_message = $e->getMessage();
    }
}?>
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
  <title>Edit Pengajuan Sidang</title>
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
            <h5 class="fw-bold mt-4 mb-3">Edit Sidang</h5>
            <hr>
          </div>

          <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_sidang" value="<?php echo $id_sidang; ?>">
            <input type="hidden" name="aksi" id="formAksi" value="">
            
            <div class="mb-3">
              <label for="judul" class="form-label">Judul Sidang
                <span class="text-danger">* </span>
              </label>
              <input type="text" class="forM form-control" id="judul" name="judul" value="<?php echo htmlspecialchars($existing_judul); ?>" placeholder="Masukkan Judul Sidang" required />
            </div>
            <!-- ADD THIS MISSING BLOCK -->
            <div class="mb-3">
                <label for="matkul" class="form-label">Mata Kuliah<span class="text-danger">* </span></label>
                <select class="form-select" id="matkul" name="matkul" required>
                    <option value="" disabled>Pilih Mata Kuliah</option>
                    <?php
                    // Logika untuk menampilkan daftar mata kuliah dan memilih yang sudah ada
                    $query_matkul = "SELECT id_matkul, nama_matkul FROM Matakuliah ORDER BY nama_matkul ASC";
                    $result_matkul = sqlsrv_query($conn, $query_matkul);
                    while ($row = sqlsrv_fetch_array($result_matkul, SQLSRV_FETCH_ASSOC)) {
                        $matkul_id = $row['id_matkul'];
                        $nama_matkul = $row['nama_matkul'];
                        $selected = ($matkul_id == $existing_id_matkul) ? 'selected' : '';
                        echo "<option value=\"$matkul_id\" $selected>$nama_matkul</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
              <label for="kelompok" class="form-label">Kelompok</label>
              <div class="form-control" style="background-color: #e9ecef; cursor: not-allowed;">
                  Kelompok <?php echo htmlspecialchars($existing_id_kelompok); ?>
              </div>
              <input type="hidden" name="kelompok" value="<?php echo $existing_id_kelompok; ?>">
          </div>
            <div class="row">
              <div class="mb-4">
                <div class="p-4 rounded bg-light border text-start">
                  <h6 class="fw-bold text-dark">Dokumen Sidang</h6>
                   <?php if($file_exists): ?>
                      <p class="small text-success mb-2"><i class="fa-solid fa-check-circle"></i> File sudah ada. Unggah file baru hanya jika ingin menggantinya.</p>
                   <?php else: ?>
                      <p class="small text-danger mb-2"><i class="fa-solid fa-triangle-exclamation"></i> Dokumen belum ada. Silakan unggah file.</p>
                   <?php endif; ?>
                  <div id="DokumenSidangForm">
                    <label class="upload-box w-100 mt-3 text-center">
                      <input type="file" id="DokumenSidang" name="DokumenSidang" accept=".pdf,.docx,.pptx,.zip" hidden />
                      <div class="upload-content">
                        <svg id="uploadIcon" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#6c757d" class="bi bi-upload" viewBox="0 0 16 16">
                          <path d="M.5 9.9a.5.5 0 0 1 .5.5v3.6a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5V10.4a.5.5 0 0 1 1 0v3.6a1.5 1.5 0 0 1-1.5 1.5H1.5A1.5 1.5 0 0 1 0 14V10.4a.5.5 0 0 1 .5-.5z" />
                          <path d="M7.646 1.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 3.207V10a.5.5 0 0 1-1 0V3.207L5.354 5.354a.5.5 0 1 1-.708-.708l3-3z" />
                        </svg>
                        <p class="mt-2 text-muted small DokumenLabelText">Upload file revisi dengan format pdf, docx, pptx, dan zip</p>
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

           <div class="modal fade" id="modalValidasi" tabindex="-1" aria-labelledby="modalValidasiLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
                                    <div class="modal-header border-0 justify-content-center">
                                        <h4 class="modal-title fw-bold" id="modalValidasiLabel" style="font-size: 24px;">Perhatian</h4>
                                    </div>
                                    <div class="modal-body">
                                        <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah Anda yakin ingin mengajukan sidang?</p>
                                        <div class="d-flex justify-content-between px-5">
                                            <button type="button" class="btn btn-outline-danger custom-batal px-4 py-2 fw-semibold btn-tolak" data-bs-dismiss="modal">Batalkan</button>
                                            <button type="button" class="btn btn-success px-4 py-2 fw-semibold btn-setujui" id="btnLanjutkan">Lanjutkan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <button type="button" class="btn btn-kembali" onclick="window.location.href='mPengajuan.php'">
                  <span class="icon-circle"><i class="fa-solid fa-arrow-left"></i></span> Kembali
                </button>
              </div>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary" id="btnSimpan">Simpan </button>
                <button type="button" class="btn btn-setuju" id="btnKirim">Kirim </button>
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

    // Form validation and submission
    const btnKirim = document.getElementById('btnKirim');
    const btnSimpan = document.getElementById('btnSimpan');
    const btnLanjutkan = document.getElementById('btnLanjutkan');
    const form = document.querySelector('form');
    const formAksi = document.getElementById('formAksi');

    // Kirim button handler
    btnKirim.addEventListener('click', function(event) {
      event.preventDefault();
      if (validateForm()) {
        formAksi.value = 'Kirim';
        const modalValidasi = new bootstrap.Modal(document.getElementById('modalValidasi'));
        modalValidasi.show();
      }
    });

    // Simpan button handler
    btnSimpan.addEventListener('click', function(event) {
      event.preventDefault();
      if (validateForm()) {
        formAksi.value = 'Simpan';
        form.submit();
      }
    });

    // Lanjutkan button handler (inside modal)
    btnLanjutkan.addEventListener('click', function() {
      form.submit();
    });

    // Form validation function
    function validateForm() {
        const judul = document.getElementById('judul').value;
        const matkul = document.getElementById('matkul').value;
        const laporan = document.getElementById('DokumenSidang').files.length;
        const fileExists = <?php echo $file_exists ? 'true' : 'false'; ?>;

        if (judul.trim() === "") {
            Swal.fire({ title: 'Judul tidak boleh kosong!', icon: 'error', confirmButtonText: 'OK', confirmButtonColor: '#4B68FB' });
            return false;
        }

        if (matkul === "" || !matkul) {
            Swal.fire({ title: 'Pilih mata kuliah!', icon: 'error', confirmButtonText: 'OK', confirmButtonColor: '#4B68FB' });
            return false;
        }
        
        if (laporan === 0 && !fileExists) {
            Swal.fire({ title: 'Dokumen wajib diunggah karena belum ada!', icon: 'error', confirmButtonText: 'OK', confirmButtonColor: '#4B68FB' });
            return false;
        }

        return true;
    }
  </script>
</body>
</html>