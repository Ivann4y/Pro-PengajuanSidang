<?php
include '../../koneksi/koneksiAndrew.php';

// Get next ID
$next_id = 1;
$sql_id = "SELECT MAX(id_sidang) AS max_id FROM Sidang";
$result_id = $conn->query($sql_id);
if ($result_id && $row = $result_id->fetch_assoc()) {
    $next_id = $row['max_id'] + 1;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $matkul = $_POST['matkul'];
    $id_kelompok = 1; // Should come from session
    $aksi = $_POST['aksi'];
    $status_ajuan = ($aksi == 'Kirim') ? 1 : 0;
    $waktu_pengumpulan = date('Y-m-d H:i:s');
    
    // Handle file upload
    $dok_laporan = '';
    $fileName = '';
    
    if (isset($_FILES['DokumenSidang']) && $_FILES['DokumenSidang']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['DokumenSidang'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        
        // Create upload directory if not exists
        $uploadDir = '../../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid('', true) . '.' . $fileExt;
        $uploadPath = $uploadDir . $newFileName;
        
        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            $dok_laporan = $uploadPath;
        }
    }

    // Insert into database
    $sql = "INSERT INTO Sidang (
        id_sidang,
        judul, 
        waktu_pengumpulan, 
        dok_laporan, 
        status_ajuan, 
        jenis_sidang, 
        id_kelompok
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssisi", 
        $next_id,
        $judul, 
        $waktu_pengumpulan, 
        $dok_laporan, 
        $status_ajuan, 
        $matkul, 
        $id_kelompok
    );
    
    if ($stmt->execute()) {
        $message = ($status_ajuan == 1) 
            ? 'Pengajuan Berhasil Dikirim!' 
            : 'Pengajuan Berhasil Disimpan!';
        
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
        $error = "Error: " . $stmt->error;
        echo "<script>
            Swal.fire({
                title: 'Gagal menyimpan data',
                text: '$error',
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
              <input type="text" class="forM form-control" id="judul" name="judul" value="<?php echo $judul ?>" placeholder="Masukkan Judul Sidang" />
            </div>
            <div class="mb-3">
              <label for="matkul" class="form-label">Mata Kuliah
                <span class="text-danger">* </span>
              </label>
              <select class="forM form-select" id="matkul" name="matkul">
                <option selected disabled>Pilih Mata Kuliah</option>
                <option value="Tugas Akhir" <?php if ($matkul == 'Tugas Akhir') echo ' selected'; ?>>Tugas Akhir</option>
                <option value="Pemrograman Web" <?php if ($matkul == 'Pemrograman Web') echo ' selected'; ?>>Pemrograman Web</option>
                <option value="Sistem Operasi" <?php if ($matkul == 'Sistem Operasi') echo ' selected'; ?>>Sistem Operasi</option>
                <option value="Basis Data Lanjut" <?php if ($matkul == 'Basis Data Lanjut') echo ' selected'; ?>>Basis Data Lanjut</option>
                <option value="Struktur Data" <?php if ($matkul == 'Struktur Data') echo ' selected'; ?>>Struktur Data</option>
                <option value="Kecerdasan Buatan" <?php if ($matkul == 'Kecerdasan Buatan') echo ' selected'; ?>>Kecerdasan Buatan</option>
                <option value="Sistem Terdistribusi" <?php if ($matkul == 'Sistem Terdistribusi') echo ' selected'; ?>>Sistem Terdistribusi</option>
                <option value="Jaringan Komputer" <?php if ($matkul == 'Jaringan Komputer') echo ' selected'; ?>>Jaringan Komputer</option>
                <option value="Komputasi Awan" <?php if ($matkul == 'Komputasi Awan') echo ' selected'; ?>>Komputasi Awan</option>
                <option value="Pemrograman Mobile" <?php if ($matkul == 'Pemrograman Mobile') echo ' selected'; ?>>Pemrograman Mobile</option>
                <option value="Analisis Data" <?php if ($matkul == 'Analisis Data') echo ' selected'; ?>>Analisis Data</option>
                <option value="Interaksi Manusia Komputer" <?php if ($matkul == 'Interaksi Manusia Komputer') echo ' selected'; ?>>Interaksi Manusia Komputer</option>
                <option value="Pengujian Perangkat Lunak" <?php if ($matkul == 'Pengujian Perangkat Lunak') echo ' selected'; ?>>Pengujian Perangkat Lunak</option>
                <option value="Pengolahan Citra" <?php if ($matkul == 'Pengolahan Citra') echo ' selected'; ?>>Pengolahan Citra</option>
                <option value="Pemrograman Jaringan" <?php if ($matkul == 'Pemrograman Jaringan') echo ' selected'; ?>>Pemrograman Jaringan</option>
                <option value="Sistem Tertanam" <?php if ($matkul == 'Sistem Tertanam') echo ' selected'; ?>>Sistem Tertanam</option>
                <option value="Analisis Big Data" <?php if ($matkul == 'Analisis Big Data') echo ' selected'; ?>>Analisis Big Data</option>
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
        
        // Hide upload icon and text
        uploadIcon.style.display = 'none';
        labelText.style.display = 'none';
        
        // Add green background
        uploadBox.classList.add('file-selected');
      } else {
        fileNameDisplay.textContent = '';
        fileNameDisplay.style.display = 'none';
        
        // Show upload icon and text
        uploadIcon.style.display = 'block';
        labelText.style.display = 'block';
        
        // Remove green background
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
    btnKirim.addEventListener('click', function() {
      if (validateForm()) {
        formAksi.value = 'Kirim';
        const modalValidasi = new bootstrap.Modal(document.getElementById('modalValidasi'));
        modalValidasi.show();
      }
    });

    // Simpan button handler
    btnSimpan.addEventListener('click', function() {
      if (validateForm()) {
        formAksi.value = 'Simpan';
        form.submit();
      }
    });

    // Lanjutkan button handler
    btnLanjutkan.addEventListener('click', function() {
      form.submit();
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