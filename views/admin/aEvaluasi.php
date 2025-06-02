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
  <link rel="stylesheet" href="stylee.css?v=<?= time() ?>">
<script src="js.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

  <div class="sidebar">
    <h4>ASTRAtech</h4>
    <a href="#">Detail Sidang</a>
    <a href="#" class="active">Evaluasi</a>
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

    <!-- <div class="card-comment mt-4" data-bs-toggle="modal" data-bs-target="#modalDetail">
      <strong>Timotius Victory, S.Kom, M.Kom - Penguji</strong>
      <p class="mt-2 mb-0 text-truncate-2">
        Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik...
      </p>
    </div> -->
 
    <div class="card-comment mt-4" data-bs-toggle="modal" data-bs-target="#modalDetail">
      <h6>Dr. Rida Indah Fariani, S.Kom, M.Kom – Pembimbing</h6>
      <p class="mt-2 mb-0 text-truncate-2">
        Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik...
      </p>
      <div class="approved-badge">Telah Menyetujui</div>
    </div>


    <div class="card-comment" data-bs-toggle="modal" data-bs-target="#modalDetail">
      <strong>Yosep Setiawan, S.Kom, M.Kom - Penguji</strong>
      <p class="mt-2 mb-0 text-truncate-2">
        Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik...
      </p>
      <div class="approved-badge">Telah Menyetujui</div>
    </div>

        <div class="card-comment" data-bs-toggle="modal" data-bs-target="#modalDetail">
      <strong>Yosep Setiawan, S.Kom, M.Kom - Penguji</strong>
      <p class="mt-2 mb-0 text-truncate-2">
        Pastikan seluruh bagian dokumen mengikuti format penulisan yang telah ditentukan oleh panduan akademik...
      </p>
      <div class="approved-badge">Telah Menyetujui</div>
    </div>
<div class="revision-card shadow-sm">
  <h5 class="fw-bold text-primary">Dokumen Revisi</h5>
  <div class="revision-cardUp">
  <p class="text-center text-muted small mt-2" ><b>Dokumen_Revisi_SidangSemester_XX</b></p>

  <div class="text-center mt-3">
    
    <a href="#" class="btn btn primary" target="_blank">
       <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#8d99ae" class="bi bi-file-earmark-text-fill" viewBox="0 0 16 16"><path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.707 0H9.293zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM4.5 9a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zM4.5 10.5a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zM4.5 12a.5.5 0 0 1 0-1h4a.5.5 0 0 1 0 1h-4z"/></svg>    

    </a>


</div>

  </div>
    <p class="text-center text-muted small mt-2" id="upload-prompt-text">Unggah berkas revisi dengan format pdf, docx, pptx, dan zip</p> <br/>
      <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn-kirim" id="submitBtn" disabled>unggah</button>
            </div>

</div>
            <input type="file" id="fileInput" name="fileInput" accept=".pdf,.docx,.pptx,.zip" hidden />
          
            

            <div class="text-center mt-3"><p id="fileNameDisplay" class="fw-bold mb-0"></p></div>
          

             <div class="mt-4">
        <button type="button" id="btnKembali" class="btn btn-custom-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/></svg>
            Kembali
        </button>
    </div>

    
        </form>
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