<?php
// Start the session to get the logged-in user's ID
session_start(); 

include '../../koneksi/koneksiAndrew.php'; // Your SQL Server connection

// --- IMPORTANT ---
// Assume the logged-in student's ID is stored in the session.
// You MUST set this variable after a successful login.
// For this example, I'll hardcode it, but in your real app, it must come from the session.
// $_SESSION['mahasiswa_id'] = 123; // Example ID
if (!isset($_SESSION['mahasiswa_id'])) {
    die("Error: User is not logged in. Session 'mahasiswa_id' not set.");
}
$id_mahasiswa_logged_in = $_SESSION['mahasiswa_id'];


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // --- CHANGE 1: LOOK UP id_kelompok DYNAMICALLY ---
    $id_kelompok = null;
    $sql_kelompok = "SELECT id_kelompok FROM dbo.Kelompok_Mahasiswa WHERE id_mahasiswa = ?";
    $params_kelompok = [$id_mahasiswa_logged_in];
    $stmt_kelompok = sqlsrv_query($conn, $sql_kelompok, $params_kelompok);

    if ($stmt_kelompok === false) {
        die("Error fetching group ID: " . print_r(sqlsrv_errors(), true));
    }
    if ($row = sqlsrv_fetch_array($stmt_kelompok, SQLSRV_FETCH_ASSOC)) {
        $id_kelompok = $row['id_kelompok'];
    }
    sqlsrv_free_stmt($stmt_kelompok);

    // If no group was found for the student, stop execution.
    if ($id_kelompok === null) {
        die("Fatal Error: Could not find a 'kelompok' (group) for the logged-in student.");
    }
    
    // Get form data
    $judul = $_POST['judul'];
    $jenis_sidang_id = $_POST['matkul']; // This is now an ID from the dropdown
    $aksi = $_POST['aksi'];
    $status_ajuan = ($aksi == 'Kirim') ? 1 : 0;
    $waktu_pengumpulan = date('Y-m-d H:i:s');
    
    // Handle file upload
    $dok_laporan_filename = null; // Store only the filename
    if (isset($_FILES['DokumenSidang']) && $_FILES['DokumenSidang']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['DokumenSidang'];
        $uploadDir = '../../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
        // --- CHANGE 2: STORE ONLY THE FILENAME, NOT THE FULL PATH ---
        $dok_laporan_filename = 'laporan_' . uniqid('', true) . '.' . $fileExt;
        $uploadPath = $uploadDir . $dok_laporan_filename;
        
        move_uploaded_file($file['tmp_name'], $uploadPath);
    }

    // --- CHANGE 3: MODIFIED INSERT STATEMENT ---
    // We removed `id_sidang` because the database will generate it (IDENTITY column).
    $sql = "INSERT INTO Sidang (
        judul, 
        waktu_pengumpulan, 
        dok_laporan, 
        status_ajuan, 
        jenis_sidang, 
        id_kelompok
    ) VALUES (?, ?, ?, ?, ?, ?)";
    
    // Create an array of parameters to bind
    $params = [
        $judul, 
        $waktu_pengumpulan, 
        $dok_laporan_filename, // Use the filename
        $status_ajuan, 
        $jenis_sidang_id,    // Use the ID from the dropdown
        $id_kelompok         // Use the looked-up group ID
    ];
    
    $stmt = sqlsrv_prepare($conn, $sql, $params);

    if ($stmt) {
        if (sqlsrv_execute($stmt)) {
            $message = ($status_ajuan == 1) ? 'Pengajuan Berhasil Dikirim!' : 'Pengajuan Berhasil Disimpan!';
            echo "<script>
                Swal.fire({
                    title: '$message',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4B68FB'
                }).then(() => {
                    window.location.href = 'mPengajuan.php';
                });
            </script>";
            exit;
        } else {
            // More detailed error for debugging
            $errors = sqlsrv_errors();
            $errorMessage = "Gagal menyimpan data. Error: " . $errors[0]['message'];
            error_log($errorMessage); // Log error to server logs

            echo "<script>
                Swal.fire({
                    title: 'Gagal menyimpan data',
                    text: 'Terjadi kesalahan pada server. Silakan coba lagi. Cek log untuk detail.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4B68FB'
                });
            </script>";
        }
    } else {
         $errors = sqlsrv_errors();
         $errorMessage = "Gagal menyiapkan statement. Error: " . $errors[0]['message'];
         error_log($errorMessage); // Log error to server logs

         echo "<script>
            Swal.fire({
                title: 'Gagal menyiapkan data',
                text: 'Terjadi kesalahan pada server. Silakan coba lagi.',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4B68FB'
            });
        </script>";
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
  <title>Edit Pengajuan Sidang</title>
  <style>
    .file-selected {
      background-color: #d1fae5 !important;
      border: 1px solid #10b981 !important;
    }
    .file-name {
      color: #047857;
      font-weight: 500;
      font-size: 0.875rem;
    }
  </style>
</head>

<body>
  <!-- The rest of your HTML and JavaScript is correct and does not need to be changed. -->
  <!-- I am including it here for completeness. -->

  <div id="NavSide">
    <div id="main-sidebar" class="NavSide__sidebar">
      <div class="NavSide__sidebar-brand">
        <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
      </div>
      <ul class="NavSide__sidebar-nav">
        <li class="NavSide__sidebar-item ">
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
          <h2 class="text-heading">Nayaka Ivana Putra (Mahasiswa)</h2>
        </div>
        <div class="row">
          <div class="col-12">
            <h5 class="fw-bold mt-4 mb-3">Tambah Sidang</h5>
            <hr>
          </div>

          <?php
          $judul = $_GET['judul'] ?? '';
          $matkul = $_GET['matkul'] ?? '';
          ?>
          <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_sidang" value="<?php echo $next_id; ?>">
            <input type="hidden" name="aksi" id="formAksi" value="">
            
            <div class="mb-3">
              <label for="judul" class="form-label">Judul Sidang
                <span class="text-danger">* </span>
              </label>
              <input type="text" class="forM form-control" id="judul" name="judul" value="<?php echo htmlspecialchars($judul); ?>" placeholder="Masukkan Judul Sidang" />
            </div>
                        <div class="mb-3">
              <label for="matkul" class="form-label">Mata Kuliah
                <span class="text-danger">* </span>
              </label>
              <select class="forM form-select" id="matkul" name="matkul">
                <option selected disabled>Pilih Mata Kuliah</option>
                <?php
                  // Fetch all courses from the database to populate the dropdown
                  $sql_matkul = "SELECT id_matkul, nama_matkul FROM dbo.MataKuliah ORDER BY nama_matkul ASC";
                  $stmt_matkul = sqlsrv_query($conn, $sql_matkul);
                  if ($stmt_matkul === false) {
                      // Basic error handling
                      echo '<option disabled>Error memuat mata kuliah</option>';
                  } else {
                      // Loop through the results and create an <option> for each one
                      while ($matkul = sqlsrv_fetch_array($stmt_matkul, SQLSRV_FETCH_ASSOC)) {
                          echo '<option value="' . htmlspecialchars($matkul['id_matkul']) . '">' . htmlspecialchars($matkul['nama_matkul']) . '</option>';
                      }
                  }
                  // Free the statement resource
                  sqlsrv_free_stmt($stmt_matkul);
                ?>
              </select>
            </div>

            <!-- Upload Dokumen Laporan Sidang -->
            <div class="row">
              <div class="mb-4">
                <div class="p-4 rounded bg-light border text-start">
                  <h6 class="fw-bold text-dark">Dokumen Sidang
                    <span class="text-danger">* </span>
                  </h6>
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

            <!-- Modal -->
            <div class="modal fade" id="modalValidasi" tabindex="-1" aria-labelledby="modalValidasiLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
                  <div class="modal-header border-0 justify-content-center">
                    <h4 class="modal-title fw-bold" id="modalValidasiLabel" style="font-size: 24px;">Perhatian</h4>
                  </div>
                  <div class="modal-body">
                    <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah anda yakin ingin mengajukan sidang?</p>
                    <div class="d-flex justify-content-between px-5">
                      <button type="button" class="btn btn-outline-danger custom-batal px-4 py-2 fw-semibold btn-tolak" data-bs-dismiss="modal">Batalkan</button>
                      <button type="button" class="btn btn-success px-4 py-2 fw-semibold btn-setujui" id="btnLanjutkan">Lanjutkan</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Tombol Kembali -->
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <button type="button" class="btn btn-kembali" onclick="history.back()">
                  <span class="icon-circle">
                    <i class="fa-solid fa-arrow-left"></i>
                  </span>
                  Kembali
                </button>
              </div>

              <!-- Tombol Simpan & Kirim -->
              <div class="d-flex gap-2">
                <button type="button" name="aksi" value="Simpan" class="btn btn-secondary" id="btnSimpan">Simpan</button>
                <button type="button" name="aksi" value="Kirim" class="btn-setuju" id="btnKirim">Kirim</button>
              </div>
            </div>
          </form>
        </div>

        <!-- Modal keluar-->
        <div class="modal fade" id="logMBeranda" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div style="background-color: rgb(67, 54, 240);">
                <div class="modal-header">
                  <h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1>
                </div>
              </div>
              <div class="modal-body mx-auto">
                Apakah anda yakin ingin keluar?
              </div>
              <div class="modal-footer justify-content-center border-0">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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

    // Sidebar Active Item Logic
    let listItems = document.querySelectorAll(".NavSide__sidebar-item");
    for (let i = 0; i < listItems.length; i++) {
      listItems[i].onclick = function(event) {
        if (!this.classList.contains("NavSide__sidebar-item--active")) {
          for (let j = 0; j < listItems.length; j++) {
            listItems[j].classList.remove("NavSide__sidebar-item--active");
          }
          this.classList.add("NavSide__sidebar-item--active");
        }
      };
    }

    // File upload handling
    const DokumenSidang = document.getElementById('DokumenSidang');
    const uploadBox = document.querySelector('.upload-box');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const uploadIcon = document.getElementById('uploadIcon');
    const labelText = document.querySelector('.DokumenLabelText');

    DokumenSidang.addEventListener('change', function() {
      if (this.files.length > 0) {
        const fileName = this.files[0].name;
        fileNameDisplay.textContent = fileName;
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
      event.preventDefault(); // Prevent immediate form submission
      if (validateForm()) {
        formAksi.value = 'Kirim';
        const modalValidasi = new bootstrap.Modal(document.getElementById('modalValidasi'));
        modalValidasi.show();
      }
    });

    // Simpan button handler
    btnSimpan.addEventListener('click', function(event) {
      event.preventDefault(); // Prevent immediate form submission
      if (validateForm()) {
        formAksi.value = 'Simpan';
        form.submit();
      }
    });

    // Lanjutkan button handler (inside modal)
    btnLanjutkan.addEventListener('click', function() {
      form.submit(); // This is where the form submission happens for "Kirim"
    });

    // Form validation function
    function validateForm() {
      const judul = document.getElementById('judul').value;
      const matkul = document.getElementById('matkul').value;
      const laporan = document.getElementById('DokumenSidang').files.length;

      if (judul.trim() === "") {
        Swal.fire({
          title: 'Judul tidak boleh kosong!',
          icon: 'error',
          confirmButtonText: 'OK',
          confirmButtonColor: '#4B68FB'
        });
        return false;
      }

      if (matkul === "Pilih Mata Kuliah" || !matkul) {
        Swal.fire({
          title: 'Pilih mata kuliah!',
          icon: 'error',
          confirmButtonText: 'OK',
          confirmButtonColor: '#4B68FB'
        });
        return false;
      }

      if (laporan === 0) {
        Swal.fire({
          title: 'Dokumen tidak boleh kosong!',
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