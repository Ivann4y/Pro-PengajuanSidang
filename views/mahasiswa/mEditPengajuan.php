<?php
session_start();
include '../../koneksi/koneksiAndrew.php';

$success_message = '';
$error_message = '';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['aksi'])) {
    // Sanitize and retrieve form data
    $judul = $_POST['judul'] ?? '';
    $matkul_name = $_POST['matkul'] ?? ''; // Renamed to avoid confusion
    $aksi = $_POST['aksi'];

    // --- Server-side Validation ---
    if (empty($judul) || empty($matkul_name) || $matkul_name == "Pilih Mata Kuliah" || !isset($_FILES['DokumenSidang']) || $_FILES['DokumenSidang']['error'] != 0) {
        $error_message = 'Semua field wajib diisi dan dokumen harus diupload.';
    } else {
        // --- File Upload Handling ---
        $dok_laporan_name = '';
        $uploadDir = '../../uploads/laporan/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '-' . preg_replace("/[^a-zA-Z0-9.\-_]+/", "", basename($_FILES['DokumenSidang']['name']));
        $targetFilePath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmpName, $targetFilePath)) {
            $dok_laporan_name = $fileName;

            // --- Database Insertion Logic ---
            $status_ajuan = ($aksi == 'Kirim') ? 1 : 0;
            // Map the selected matkul name to the 'jenis_sidang' bit value
            $jenis_sidang = ($matkul_name == 'Tugas Akhir') ? 0 : 1;
            $status_sidang = 0; // Default status
            
            // Allow id_kelompok to be NULL if the student is not in a group
            // For now, we set it to NULL as per your screenshot showing it's allowed.
            $id_kelompok = NULL;

            // *** FIX: REMOVED MANUAL ID GENERATION ***
            // *** Let the database handle the auto-incrementing id_sidang ***
            
            // *** FIX: Corrected SQL INSERT statement. Removed id_sidang from the column list. ***
            $sql = "INSERT INTO Sidang (judul, waktu_pengumpulan, dok_laporan, status_ajuan, status_sidang, jenis_sidang, id_kelompok, dok_final) 
                    VALUES (?, GETDATE(), ?, ?, ?, ?, ?, NULL)";
            
            // *** FIX: Corrected parameters array. Removed id_sidang. ***
            $params = [$judul, $dok_laporan_name, $status_ajuan, $status_sidang, $jenis_sidang, $id_kelompok];
            
            $stmt = sqlsrv_prepare($conn, $sql, $params);

            if ($stmt && sqlsrv_execute($stmt)) {
                if ($aksi == 'Kirim') {
                    $success_message = 'Pengajuan Berhasil Dikirim!';
                } else {
                    $success_message = 'Pengajuan Berhasil Disimpan!';
                }
            } else {
                // Keep detailed error reporting active
                $error_message = "Gagal menyimpan data ke database. Debug: " . print_r(sqlsrv_errors(), true);
            }
        } else {
            $error_message = 'Gagal mengupload file dokumen.';
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
  <title>Edit Pengajuan Sidang</title>
</head>

<body>
  <div id="NavSide">
    <div id="main-sidebar" class="NavSide__sidebar">
      <div class="NavSide__sidebar-brand"><img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo"></div>
      <ul class="NavSide__sidebar-nav">
        <li class="NavSide__sidebar-item "><a href="mBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a></li>
        <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><a href="mPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a></li>
        <li class="NavSide__sidebar-item"><a href="mSidang.php"><span class="NavSide__sidebar-title fw-semibold">Sidang</span></a></li>
        <li class="NavSide__sidebar-item"><a href="#" data-bs-toggle="modal" data-bs-target="#logMBeranda"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a></li>
      </ul>
    </div>
    <div class="NavSide__topbar">
      <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
    </div>
    <main class="NavSide__main-content" id="mPengajuan">
      <div class="container-fluid">
        <div class="dashboard-header"><h2 class="text-heading">Nayaka Ivana Putra (Mahasiswa)</h2></div>
        <div class="row">
          <div class="col-12"><h5 class="fw-bold mt-4 mb-3">Tambah Sidang</h5><hr></div>
          <?php
          $judul_get = $_GET['judul'] ?? '';
          $matkul_get = $_GET['matkul'] ?? '';
          ?>
          <!-- *** FIX: Form action should point to the file itself to process the POST request *** -->
          <form action="mEditPengajuan.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="judul" class="form-label">Judul Sidang<span class="text-danger">* </span></label>
              <input type="text" class="forM form-control" id="judul" name="judul" value="<?php echo htmlspecialchars($judul_get); ?>" placeholder="Masukkan Judul Sidang" required />
            </div>
            <div class="mb-3">
              <label for="matkul" class="form-label">Mata Kuliah<span class="text-danger">* </span></label>
              <select class="forM form-select" id="matkul" name="matkul" required>
                <option value="" selected disabled>Pilih Mata Kuliah</option>
                <option value="Tugas Akhir" <?php if ($matkul_get == 'Tugas Akhir') echo ' selected'; ?>>Tugas Akhir</option>
                <option value="Pemrograman Web" <?php if ($matkul_get == 'Pemrograman Web') echo ' selected'; ?>>Pemrograman Web</option>
                <!-- Other options... -->
              </select>
            </div>
            <div class="row">
              <div class="mb-4">
                <div class="p-4 rounded bg-light border text-start">
                  <h6 class="fw-bold text-dark">Dokumen Sidang<span class="text-danger">* </span></h6>
                  <div id="DokumenSidangForm">
                    <label class="upload-box w-100 mt-3 text-center">
                      <input type="file" id="DokumenSidang" name="DokumenSidang" accept=".pdf,.docx,.pptx,.zip" hidden required />
                      <div class="upload-content">
                        <svg id="uploadIcon" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#6c757d" class="bi bi-upload" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v3.6a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5V10.4a.5.5 0 0 1 1 0v3.6a1.5 1.5 0 0 1-1.5 1.5H1.5A1.5 1.5 0 0 1 0 14V10.4a.5.5 0 0 1 .5-.5z" /><path d="M7.646 1.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 3.207V10a.5.5 0 0 1-1 0V3.207L5.354 5.354a.5.5 0 1 1-.708-.708l3-3z" /></svg>
                        <p class="mt-2 text-muted small DokumenLabelText">Upload file revisi dengan format pdf, docx, pptx, dan zip</p>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
              <div class="bintangMerah"><p>* : wajib diisi</p></div>
            </div>
            <div class="modal fade" id="modalValidasi" tabindex="-1" aria-labelledby="modalValidasiLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
                  <div class="modal-header border-0 justify-content-center"><h4 class="modal-title fw-bold" id="modalValidasiLabel">Perhatian</h4></div>
                  <div class="modal-body">
                    <p class="mb-5 fw-semibold">Apakah anda yakin ingin mengajukan sidang?</p>
                    <div class="d-flex justify-content-between px-5">
                      <button type="button" class="btn btn-outline-danger custom-batal px-4 py-2 fw-semibold btn-tolak" data-bs-dismiss="modal">Batalkan</button>
                      <!-- *** FIX: Changed to submit button to trigger form submission *** -->
                      <button type="submit" name="aksi" value="Kirim" class="btn btn-success px-4 py-2 fw-semibold btn-setujui" id="btnLanjutkan">Lanjutkan</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <div><button type="button" class="btn btn-kembali" onclick="history.back()"><span class="icon-circle"><i class="fa-solid fa-arrow-left"></i></span>Kembali</button></div>
              <div class="d-flex gap-2">
                <!-- *** FIX: Changed to submit button to trigger form submission *** -->
                <button type="submit" name="aksi" value="Simpan" class="btn btn-secondary" id="btnSimpan">Simpan</button>
                <!-- This button only opens the modal now -->
                <button type="button" class="btn-setuju" id="btnKirim">Kirim</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>

  <script>
    // This JavaScript logic is now simplified and more reliable.
    document.addEventListener("DOMContentLoaded", function() {
        <?php if (!empty($success_message)): ?>
          Swal.fire({ title: 'Berhasil!', html: '<?php echo addslashes($success_message); ?>', icon: 'success', confirmButtonColor: '#4B68FB' })
              .then(() => window.location.href = 'mPengajuan.php');
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
          Swal.fire({ title: 'Gagal!', html: '<?php echo addslashes(nl2br(htmlspecialchars($error_message))); ?>', icon: 'error', confirmButtonColor: '#4B68FB' });
        <?php endif; ?>

        const pengajuanForm = document.querySelector('form');
        const btnKirim = document.getElementById('btnKirim');
        
        function validateForm() {
            const judul = document.getElementById('judul').value;
            const matkul = document.getElementById('matkul').value;
            const laporan = document.getElementById('DokumenSidang').files.length;
            if (!judul.trim() || !matkul || matkul === "Pilih Mata Kuliah" || laporan === 0) {
                Swal.fire('Data Tidak Lengkap', 'Mohon isi semua field yang wajib diisi dan upload dokumen.', 'warning', { confirmButtonColor: '#4B68FB' });
                return false;
            }
            return true;
        }

        btnKirim.addEventListener('click', function() {
            if (validateForm()) {
                const modalValidasi = new bootstrap.Modal(document.getElementById('modalValidasi'));
                modalValidasi.show();
            }
        });

        // The form now handles its own submission correctly for the 'Simpan' button
        // and the 'Lanjutkan' button in the modal.
    });
  </script>
</body>
</html>