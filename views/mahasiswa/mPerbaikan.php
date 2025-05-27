<?php
// Langkah 1: Mulai session di baris paling pertama
session_start();

// Langkah 3: Cek apakah ada pesan dari proses sebelumnya, lalu hapus.
$pesan = '';
if (isset($_SESSION['pesan'])) {
    $pesan = $_SESSION['pesan'];
    unset($_SESSION['pesan']); // Hapus pesan dari session agar tidak muncul lagi saat di-refresh
}

// Cek apakah form telah disubmit menggunakan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Cek apakah ada file yang di-upload melalui input bernama 'fileInput'
    if (isset($_FILES["fileInput"]) && $_FILES["fileInput"]["error"] == 0) {
        
        $folder_target = "uploads/";
        if (!file_exists($folder_target)) {
            mkdir($folder_target, 0777, true);
        }

        $file_asli = basename($_FILES["fileInput"]["name"]);
        $ekstensi_file = strtolower(pathinfo($file_asli, PATHINFO_EXTENSION));
        $file_unik = uniqid('revisi_', true) . '.' . $ekstensi_file;
        $path_target = $folder_target . $file_unik;
        
        $ekstensi_diizinkan = array("pdf", "docx", "pptx", "zip");
        if (!in_array($ekstensi_file, $ekstensi_diizinkan)) {
            // Untuk pesan error, kita langsung tampilkan saja tanpa redirect
            $pesan = "Error: Format file tidak diizinkan. Hanya .pdf, .docx, .pptx, dan .zip yang boleh diunggah.";
        }
        
        elseif ($_FILES["fileInput"]["size"] > 5242880) {
            $pesan = "Error: Ukuran file terlalu besar. Maksimal 5 MB.";
        }
        
        else {
            if (move_uploaded_file($_FILES["fileInput"]["tmp_name"], $path_target)) {
                
                // Langkah 2: Jika SUKSES, simpan pesan ke session dan lakukan redirect
                $_SESSION['pesan'] = "Sukses: File ". htmlspecialchars($file_asli) . " berhasil diunggah.";
                header("Location: index.php"); // Perintahkan browser untuk redirect
                exit(); // Hentikan eksekusi script setelah redirect

            } else {
                $pesan = "Error: Maaf, terjadi kesalahan saat memindahkan file.";
            }
        }

    } elseif (isset($_FILES["fileInput"])) {
        switch ($_FILES["fileInput"]["error"]) {
            case UPLOAD_ERR_NO_FILE:
                $pesan = "Error: Tidak ada file yang dipilih.";
                break;
            default:
                $pesan = "Error: Terjadi kesalahan saat mengunggah.";
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* =================================
   Struktur Utama & Sidebar
   ================================= */
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
  font-weight: 600; /* Membuat nama file tebal */
  min-height: 1.5rem; /* Cadangkan ruang agar layout tidak bergeser */
  color: #495057; /* Warna teks lebih gelap */
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

/* =================================
   STYLE BARU SESUAI DESAIN MODERN
   ================================= */

/* Card utama untuk bagian revisi */
.revision-card {
  background-color: white;
  border-radius: 1.5rem; /* 24px */
  padding: 2rem; /* 32px */
  border: 1px solid #e9ecef;
}

/* Area upload yang baru */
.upload-area-v2 {
  background-color: #f8f9fa;
  border: 2px dashed #e0e0e0;
  border-radius: 1rem; /* 16px */
  padding: 2.5rem;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  transition: background-color 0.2s;
}

.upload-area-v2:hover {
  background-color: #f1f3f5;
}

/* Tombol Kembali yang baru (di luar card) */
/* Style Tombol Aksi Utama (dipakai untuk Kembali & Kirim) */
.btn-custom-primary {
  background-color: #4f46e5;
  color: white;
  font-weight: 600;
  border: none;
  border-radius: 50px; /* Membuatnya berbentuk pil */
  padding: 0.75rem 1.5rem;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem; /* Jarak antara ikon dan teks */
  box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
  transition: all 0.2s ease-in-out;
}

.btn-custom-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(79, 70, 229, 0.5);
}

/* Style khusus untuk saat tombol dinonaktifkan */
.btn-custom-primary:disabled {
  background-color: #e2e8f0;
  color: #94a3b8;
  box-shadow: none;
  transform: none;
  cursor: not-allowed;
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

    <div class="revision-card shadow-sm">
        <h5 class="fw-bold text-primary">Dokumen Revisi</h5>
        <form id="revisionForm" action="index.php" method="POST" enctype="multipart/form-data">
            <label for="fileInput" class="upload-area-v2" id="uploadArea">
                <div id="initial-state">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#ced4da" class="bi bi-file-earmark-arrow-up" viewBox="0 0 16 16"><path d="M8.5 11.5a.5.5 0 0 1-1 0V7.707L6.354 8.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 7.707V11.5z"/><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 1.5v2A1.5 1.5 0 0 0 11 5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v1.5z"/></svg>
                </div>
                <div id="selected-state" class="d-none">
                     <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#8d99ae" class="bi bi-file-earmark-text-fill" viewBox="0 0 16 16"><path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.707 0H9.293zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM4.5 9a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zM4.5 10.5a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zM4.5 12a.5.5 0 0 1 0-1h4a.5.5 0 0 1 0 1h-4z"/></svg>
                </div>
            </label>
            <input type="file" id="fileInput" name="fileInput" accept=".pdf,.docx,.pptx,.zip" hidden />
            <p class="text-center text-muted small mt-2" id="upload-prompt-text">Unggah berkas revisi dengan format pdf, docx, pptx, dan zip</p>
            <div class="text-center mt-3"><p id="fileNameDisplay" class="fw-bold mb-0"></p></div>
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-custom-primary" id="submitBtn" disabled>Kirim</button>
            </div>
        </form>
    </div>

    <div class="mt-4">
        <button type="button" id="btnKembali" class="btn btn-custom-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/></svg>
            Kembali
        </button>
    </div>

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
          <div class="modal-footer border-0 justify-content-end">
            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
     </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js.js"></script>

  <?php
  if (!empty($pesan) && strpos(strtolower($pesan), 'sukses') !== false):
  ?>
  <script>
      Swal.fire({
          title: 'Berhasil!',
          text: 'Dokumen Anda telah berhasil diunggah.',
          icon: 'success',
          confirmButtonColor: '#007bff'
      });
  </script>
  <?php endif; ?>
  
</body>
</html>

  <script>
  document.addEventListener('DOMContentLoaded', function() {

    // =================================================
    // BAGIAN 1: PENGAMBILAN ELEMEN DARI HTML
    // =================================================
    const fileInput = document.getElementById('fileInput');
    const submitBtn = document.getElementById('submitBtn');
    const revisionForm = document.getElementById('revisionForm');
    const btnKembali = document.getElementById('btnKembali');
    
    // Elemen spesifik untuk UI upload
    const initialState = document.getElementById('initial-state');
    const selectedState = document.getElementById('selected-state');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    
    // Cek apakah semua elemen penting ada
    if (!fileInput || !submitBtn || !revisionForm || !initialState || !selectedState || !fileNameDisplay) {
        console.error("Peringatan: Salah satu elemen HTML untuk fungsionalitas upload tidak ditemukan. Pastikan semua ID sudah benar.");
        return; // Hentikan script jika ada yang kurang
    }

    // =================================================
    // BAGIAN 2: LOGIKA SAAT PENGGUNA MEMILIH FILE
    // =================================================
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            // --- JIKA FILE DIPILIH ---
            const selectedFile = this.files[0];

            // Ganti ikon
            initialState.classList.add('d-none');
            selectedState.classList.remove('d-none');

            // Tampilkan nama file
            fileNameDisplay.textContent = selectedFile.name;

            // Aktifkan tombol Kirim (CSS akan otomatis mengubah stylenya)
            submitBtn.disabled = false;

        } else {
            // --- JIKA PEMILIHAN DIBATALKAN ---
            
            // Kembalikan ikon
            initialState.classList.remove('d-none');
            selectedState.classList.add('d-none');

            // Kosongkan nama file
            fileNameDisplay.textContent = '';

            // Non-aktifkan lagi tombol Kirim
            submitBtn.disabled = true;
        }
    });

    // =================================================
    // BAGIAN 3: LOGIKA SAAT TOMBOL "KIRIM" DITEKAN
    // =================================================
    revisionForm.addEventListener('submit', function(event) {
        // Selalu hentikan pengiriman form secara otomatis untuk menampilkan konfirmasi
        event.preventDefault(); 

        // Tampilkan modal konfirmasi SweetAlert2
        Swal.fire({
            title: 'Perhatian',
            text: "Apakah anda sudah yakin ingin mengupload dokumen?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754', // Warna hijau
            cancelButtonColor: '#dc3545',  // Warna merah
            confirmButtonText: 'Lanjutkan',
            cancelButtonText: 'Batalkan',
            customClass: {
                popup: 'rounded-4' // Membuat modal lebih rounded
            }
        }).then((result) => {
            // Cek jika pengguna menekan tombol "Lanjutkan"
            if (result.isConfirmed) {
                // Jika dikonfirmasi, baru kita kirim form-nya secara manual ke PHP
                revisionForm.submit();
            }
        });
    });

    // =================================================
    // BAGIAN 4: LOGIKA TOMBOL "KEMBALI"
    // =================================================
    if (btnKembali) {
        btnKembali.addEventListener('click', function() {
            history.back(); // Perintah untuk kembali ke halaman sebelumnya
        });
    }

});