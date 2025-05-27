<?php
// Bagian 1: Logika PHP untuk Menangani Upload File

$pesan = ''; // Variabel untuk menyimpan pesan feedback (sukses/error)

// Cek apakah form telah disubmit menggunakan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Cek apakah ada file yang di-upload melalui input bernama 'fileInput'
    if (isset($_FILES["fileInput"]) && $_FILES["fileInput"]["error"] == 0) {
        
        $folder_target = "uploads/"; // Nama folder tempat menyimpan file
        if (!file_exists($folder_target)) {
            mkdir($folder_target, 0777, true); // Buat folder jika belum ada
        }

        $file_asli = basename($_FILES["fileInput"]["name"]); // Nama file asli dari user
        
        // Buat nama file baru yang unik untuk menghindari nama yang sama dan karakter aneh
        $ekstensi_file = strtolower(pathinfo($file_asli, PATHINFO_EXTENSION));
        $file_unik = uniqid('revisi_', true) . '.' . $ekstensi_file;
        $path_target = $folder_target . $file_unik;
        
        // --- VALIDASI FILE ---

        // 1. Cek ekstensi file yang diizinkan
        $ekstensi_diizinkan = array("pdf", "docx", "pptx", "zip");
        if (!in_array($ekstensi_file, $ekstensi_diizinkan)) {
            $pesan = "Error: Format file tidak diizinkan. Hanya .pdf, .docx, .pptx, dan .zip yang boleh diunggah.";
        }
        
        // 2. Cek ukuran file (misalnya, maks 5MB)
        // 5 * 1024 * 1024 = 5,242,880 bytes
        elseif ($_FILES["fileInput"]["size"] > 5242880) {
            $pesan = "Error: Ukuran file terlalu besar. Maksimal 5 MB.";
        }
        
        // Jika semua validasi lolos
        else {
            // Pindahkan file dari lokasi sementara ke folder 'uploads/'
            if (move_uploaded_file($_FILES["fileInput"]["tmp_name"], $path_target)) {
                $pesan = "Sukses: File ". htmlspecialchars($file_asli) . " berhasil diunggah.";
                // Di dunia nyata, Anda akan menyimpan $path_target ke database di sini
            } else {
                $pesan = "Error: Maaf, terjadi kesalahan saat memindahkan file.";
            }
        }

    } elseif (isset($_FILES["fileInput"])) {
        // Menangani error upload yang lebih spesifik jika perlu
        switch ($_FILES["fileInput"]["error"]) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $pesan = "Error: Ukuran file melebihi batas yang diizinkan.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $pesan = "Error: Tidak ada file yang dipilih.";
                break;
            default:
                $pesan = "Error: Terjadi kesalahan yang tidak diketahui saat mengunggah.";
                break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Detail Sidang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="stylee.css">
  <style>
    body {
  background-color: #f8f9fa;
  font-family: "Segoe UI", sans-serif;
}

.sidebar {
  height: 100vh;
  width: 240px;
  position: fixed;
  top: 0;
  left: 0;
  background-color: #3b52f9;
  color: white;
  padding-top: 2rem;
}

.sidebar h4 {
  text-align: center;
  font-weight: bold;
  margin-bottom: 2rem;
}

.sidebar a {
  display: block;
  color: white;
  text-decoration: none;
  padding: 12px 24px;
  border-top-left-radius: 20px;
  border-bottom-left-radius: 20px;
  margin: 0.5rem 0 0.5rem 1rem;
}

.sidebar a.active,
.sidebar a:hover {
  background-color: white;
  color: #3b52f9;
  font-weight: 600;
}

.main {
  /* INI YANG MEMBUAT KONTEN GESER KE KANAN */
  margin-left: 240px;
  padding: 2rem;
}

/* =================================
   Bagian Upload Dokumen Revisi
   ================================= */
.upload-box {
  background-color: #f8f9fa;
  border: 2px dashed #dee2e6;
  border-radius: 0.5rem;
  padding: 2rem;
  cursor: pointer;
  transition: background-color 0.2s, border-color 0.2s;
  color: #6c757d; /* Warna teks placeholder */
  display: flex; /* Membuat konten di tengah */
  flex-direction: column; /* Menata konten ke bawah */
  justify-content: center; /* Tengah secara vertikal */
  align-items: center; /* Tengah secara horizontal */
  min-height: 180px;
}

.upload-box:hover {
  background-color: #e9ecef;
  border-color: #adb5bd;
}

.upload-box.file-selected {
  background-color: #f1f5f9;
  border: 1px solid #e2e8f0;
}

.upload-box.file-selected svg {
  width: 60px;
  height: 60px;
  fill: #6c757d;
}

#fileNameDisplay {
  margin-top: 1rem;
  margin-bottom: 1rem;
  min-height: 1.2rem;
}

/* =================================
   STYLING TAMBAHAN YANG HILANG
   ================================= */

.badge-custom {
  background-color: #f78d8d;
  color: white;
  font-weight: 600;
  padding: 8px 14px;
  border-radius: 20px;
}

.card-comment {
  background-color: #cbcbcb;
  padding: 20px;
  border-radius: 15px;
  margin-bottom: 20px;
  cursor: pointer;
  transition: background-color 0.3s ease, color 0.3s ease;
  border: none;
}

.card-comment:hover {
  background-color: #007bff;
  color: #fff;
}

.card-comment:hover strong {
  color: #fff; /* Pastikan teks strong juga ikut putih saat hover */
}

.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* =================================
   Tombol Kirim
   ================================= */
.btn-kirim {
  border-radius: 10px;
  padding: 10px 30px;
  font-weight: bold;
}
</style>
</head>
<body>

  <div class="sidebar">
    <h4>ASTRAtech</h4>
    <a href="#">Detail Pengajuan</a>
    <a href="#" class="active">Perbaikan</a>
    <a href="#">Nilai Akhir</a>
  </div>

  <div class="main">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h2>Detail Sidang - Sistem Pengajuan Sidang</h2>
        <h5 class="mt-3">Catatan Perbaikan</h5>
      </div>
      <span class="badge-custom">Status Revisi : Belum Disetujui</span>
    </div>

    <div class="card-comment mt-4" data-bs-toggle="modal" data-bs-target="#modalDetail">
      <strong>Timotius Victory, S.Kom, M.Kom - Penguji</strong>
      <p class="mt-2 mb-0 text-truncate-2">
        Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik...
      </p>
    </div>

    <div class="card-comment" data-bs-toggle="modal" data-bs-target="#modalDetail">
      <strong>Yosep Setiawan, S.Kom, M.Kom - Penguji</strong>
      <p class="mt-2 mb-0 text-truncate-2">
        Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik...
      </p>
    </div>

    <h5 class="mt-5 mb-3 fw-bold text-primary">Dokumen Revisi</h5>

    <?php
    if (!empty($pesan)):
      $alert_type = (strpos(strtolower($pesan), 'sukses') !== false) ? 'alert-success' : 'alert-danger';
    ?>
      <div class="alert <?php echo $alert_type; ?>" role="alert">
        <?php echo $pesan; ?>
      </div>
    <?php endif; ?>

    <form id="revisionForm" action="index.php" method="POST" enctype="multipart/form-data">
      <div class="upload-section mb-3">
        <label for="fileInput" class="upload-box text-center">
          <img src="https://cdn-icons-png.flaticon.com/512/1828/1828911.png" width="40" alt="Upload Icon" class="mb-2" />
          <p class="m-0">Unggah berkas revisi dengan format pdf, docx, pptx, dan zip</p>
        </label>
        <input type="file" id="fileInput" name="fileInput" accept=".pdf,.docx,.pptx,.zip" hidden />
      </div>

      <p id="fileNameDisplay" class="text-center text-muted small"></p>

      <div class="d-flex justify-content-between align-items-center mt-4">
          
          <button type="button" id="btnKembali" class="btn btn-outline-secondary d-inline-flex align-items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-2" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
              </svg>
              Kembali
          </button>
      
          <button type="submit" class="btn btn-secondary btn-kirim" id="submitBtn" disabled>
              Kirim
          </button>
      
      </div>
      </form>

    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 p-4">
          <div class="d-flex justify-content-between align-items-start">
            <h4 id="modalDetailLabel" class="fw-bold text-primary">Detail Catatan Perbaikan</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body pt-3 pb-2">
            <p>
              Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik,
              termasuk margin, jenis huruf, ukuran font, dan penomoran halaman. Periksa kembali penggunaan bahasa.
              Hindari kesalahan ejaan, tanda baca, dan kalimat yang kurang efektif. Gunakan bahasa ilmiah yang baku dan konsisten.
              Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik,
              termasuk margin, jenis huruf, ukuran font, dan penomoran halaman. Periksa kembali penggunaan bahasa.
              Hindari kesalahan ejaan, tanda baca, dan kalimat yang kurang efektif. Gunakan bahasa ilmiah yang baku dan konsisten.
            </p>
          </div>
          <div class="modal-footer justify-content-end">
            <button type="button" class="btn btn-dark px-4" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {

  const fileInput = document.getElementById('fileInput');
  const submitBtn = document.getElementById('submitBtn');
  const uploadBox = document.querySelector('.upload-box');
  const fileNameDisplay = document.getElementById('fileNameDisplay');
  const revisionForm = document.getElementById('revisionForm');
  
  if (!fileInput || !submitBtn || !uploadBox || !fileNameDisplay || !revisionForm) {
    console.error("Peringatan: Salah satu elemen HTML yang dibutuhkan (fileInput, submitBtn, uploadBox, fileNameDisplay, atau revisionForm) tidak ditemukan. Pastikan ID dan Class sudah benar di file HTML.");
    return;
  }
  
  const originalUploadBoxContent = uploadBox.innerHTML;
  const fileSelectedContent = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/><path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/></svg>`;

  fileInput.addEventListener('change', function() {
    if (this.files.length > 0) {
      const selectedFile = this.files[0];
      uploadBox.innerHTML = fileSelectedContent;
      uploadBox.classList.add('file-selected');
      fileNameDisplay.textContent = selectedFile.name;
      submitBtn.disabled = false;
      submitBtn.classList.remove('btn-secondary');
      submitBtn.classList.add('btn-primary');
    } else {
      uploadBox.innerHTML = originalUploadBoxContent;
      uploadBox.classList.remove('file-selected');
      fileNameDisplay.textContent = '';
      submitBtn.disabled = true;
      submitBtn.classList.remove('btn-primary');
      submitBtn.classList.add('btn-secondary');
    }
  });
  
  revisionForm.addEventListener('submit', function(event) {
    if (fileInput.files.length === 0) {
      event.preventDefault(); 
      alert("Silakan pilih file terlebih dahulu!");
    }
  });
});
</script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>