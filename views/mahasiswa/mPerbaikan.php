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
    
    if (isset($_FILES["fileInput"]) && $_FILES["fileInput"]["error"] == 0) {
        
        $folder_target = "uploads/";
        if (!file_exists($folder_target)) {
            if (!mkdir($folder_target, 0755, true)) {
                $pesan = "Error: Gagal membuat folder uploads.";
            }
        }

        if (empty($pesan)) {
            $file_asli = basename($_FILES["fileInput"]["name"]);
            $ekstensi_file = strtolower(pathinfo($file_asli, PATHINFO_EXTENSION));
            $file_unik = uniqid('revisi_', true) . '.' . $ekstensi_file;
            $path_target = $folder_target . $file_unik;
            
            $ekstensi_diizinkan = array("pdf", "docx", "pptx", "zip");
            $mime_diizinkan = array(
                "pdf" => "application/pdf",
                "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                "pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
                "zip" => "application/zip" // Tipe MIME utama
            );
            $mime_alternatif_zip = "application/x-zip-compressed"; // Tipe MIME alternatif untuk ZIP

            if (!in_array($ekstensi_file, $ekstensi_diizinkan)) {
                $pesan = "Error: Format file tidak diizinkan (berdasarkan ekstensi). Hanya .pdf, .docx, .pptx, dan .zip yang boleh diunggah.";
            } else {
                $tipe_mime_file_asli = "";
                if (function_exists('finfo_open')) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $tipe_mime_file_asli = finfo_file($finfo, $_FILES["fileInput"]["tmp_name"]);
                    finfo_close($finfo);
                }
                
                $valid_mime = false;
                if (!empty($tipe_mime_file_asli) && isset($mime_diizinkan[$ekstensi_file])) {
                    if ($mime_diizinkan[$ekstensi_file] === $tipe_mime_file_asli) {
                        $valid_mime = true;
                    } elseif ($ekstensi_file === "zip" && $tipe_mime_file_asli === $mime_alternatif_zip) {
                        // Izinkan tipe MIME alternatif untuk ZIP
                        $valid_mime = true;
                    }
                }

                if (!$valid_mime) {
                    $pesan = "Error: Tipe file tidak valid atau tidak cocok dengan ekstensinya. Deteksi MIME: " . htmlspecialchars($tipe_mime_file_asli);
                }
            }
            
            if (empty($pesan) && $_FILES["fileInput"]["size"] > 5242880) { // Maksimal 5 MB
                $pesan = "Error: Ukuran file terlalu besar. Maksimal 5 MB.";
            }
            
            if (empty($pesan)) { 
                if (move_uploaded_file($_FILES["fileInput"]["tmp_name"], $path_target)) {
                    $_SESSION['pesan'] = "Sukses: File ". htmlspecialchars($file_asli) . " berhasil diunggah.";
                    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
                    exit();
                } else {
                    $pesan = "Error: Maaf, terjadi kesalahan saat memindahkan file.";
                }
            }
        }

    } elseif (isset($_FILES["fileInput"])) {
        switch ($_FILES["fileInput"]["error"]) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $pesan = "Error: Ukuran file melebihi batas yang diizinkan server.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $pesan = "Error: Tidak ada file yang dipilih.";
                break;
            default:
                $pesan = "Error: Terjadi kesalahan saat mengunggah file (Kode: " . $_FILES["fileInput"]["error"] . ").";
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    /* Catatan: Idealnya, semua CSS ini ada di file stylee.css */
    body {
        font-family: "Segoe UI", sans-serif; 
    }
    .text-heading {
        color: rgb(67, 54, 240);
        font-weight: 550;
    }

    #NavSide {
        display: flex;
        min-height: 100vh;
        position: relative;
        background-color: #f8f9fa; 
    }
    .NavSide__sidebar-brand {
        padding: 10% 5% 10% 5%;
        text-align: center;
    }
    .NavSide__sidebar-brand img {
        width: 80%;
        max-width: 150px;
        height: auto;
        display: inline-block;
    }
    .NavSide__sidebar {
        position: fixed;
        top: 0px;
        left: 0px;
        bottom: 0px;
        width: 240px;
        background: #3b52f9;
        overflow-x: hidden;
        overflow-y: auto;
        z-index: 1000; /* Default z-index, toggle akan lebih tinggi */
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
        padding-top: 2rem;
    }
    .NavSide__sidebar h4 { 
        text-align: center;
        font-weight: bold;
        margin-bottom: 2rem;
        color: white;
    }
    .NavSide__sidebar-nav {
        width: 100%;
        padding-left: 0;
        margin-bottom: 0;
        list-style: none;
        flex-grow: 1;
    }
    .NavSide__sidebar-item {
        position: relative;
        display: block;
        width: calc(100% - 1rem); 
        margin-left: 1rem; 
        margin-right: 0;
        margin-bottom: 0.5rem;
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
    }
    .NavSide__sidebar-link {
        display: flex;
        align-items: center;
        width: 100%;
        text-decoration: none;
        color: white;
        padding: 12px 24px; 
        box-sizing: border-box;
        font-weight: normal;
    }
    .NavSide__sidebar-title {
        white-space: normal;
        text-align: left;
        line-height: 1.5;
        flex-grow: 1;
    }
    .NavSide__sidebar-item.NavSide__sidebar-item--active {
        background: #ffffff;
    }
    .NavSide__sidebar-item.NavSide__sidebar-item--active .NavSide__sidebar-link {
        color: #3b52f9;
        font-weight: 600;
    }
    .NavSide__sidebar-item b:nth-child(1),
    .NavSide__sidebar-item b:nth-child(2) {
        display: none;
    }

    .NavSide__main-content {
        flex-grow: 1;
        padding: 2rem;
        margin-left: 240px;
        overflow-y: auto;
        transition: margin-left 0.3s ease-in-out;
    }
    
    /* Style untuk Tombol Toggle Desktop (Default Tersembunyi) */
    .NavSide__toggle {
        display: none; /* Tersembunyi di desktop */
        position: fixed;
        top: 15px;
        left: 15px;
        width: 40px;
        height: 40px;
        z-index: 1050; /* Di atas sidebar jika sidebar overlay */
        cursor: pointer;
        border-radius: 5px;
        background-color: white;
        border: 1px solid #ddd;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        align-items: center;
        justify-content: center;
        padding: 0;
        transition: left 0.3s ease-in-out;
    }
    .NavSide__toggle .open-icon,
    .NavSide__toggle .close-icon {
        font-size: 24px; 
        color: #3b52f9; 
        position: absolute; 
        transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
    }
    .NavSide__toggle .close-icon {
        opacity: 0;
        transform: rotate(-90deg) scale(0.5);
    }
    .NavSide__toggle.NavSide__toggle--active .open-icon {
        opacity: 0;
        transform: rotate(90deg) scale(0.5);
    }
    .NavSide__toggle.NavSide__toggle--active .close-icon {
        opacity: 1;
        transform: rotate(0deg) scale(1);
    }

    /* ... (sisa style dari .upload-area-v2, .btn-custom-primary, dll. tetap sama) ... */
    #fileNameDisplay { margin-top: 1rem; margin-bottom: 1rem; font-weight: 600; min-height: 1.5rem; color: #495057; }
    .badge-custom { background-color: #f78d8d; color: white; font-weight: 600; padding: 8px 14px; border-radius: 20px; }
    .card-comment { background-color: #cbcbcb; padding: 20px; border-radius: 15px; margin-bottom: 20px; cursor: pointer; transition: background-color 0.3s ease, color 0.3s ease; border: none; }
    .card-comment:hover { background-color: #007bff; color: #fff; }
    .card-comment:hover strong { color: #fff; }
    .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .revision-card { background-color: white; border-radius: 1.5rem; padding: 2rem; border: 1px solid #e9ecef; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075); }
    .upload-area-v2 { background-color: #f8f9fa; border: 2px dashed #e0e0e0; border-radius: 1rem; padding: 2.5rem; display: flex; flex-direction: column; justify-content: center; align-items: center; cursor: pointer; transition: background-color 0.2s; min-height: 180px; }
    .upload-area-v2:hover { background-color: #f1f3f5; }
    .upload-area-v2 #initial-state svg { width: 80px; height: 80px; fill: #ced4da; }
    .upload-area-v2 #selected-state svg { width: 80px; height: 80px; fill: #8d99ae; }
    .upload-area-v2 #upload-prompt-text { text-align: center; color: #6c757d; font-size: small; margin-top: 1rem; }
    .btn-custom-primary { background-color: #4f46e5; color: white; font-weight: 600; border: none; border-radius: 50px; padding: 0.75rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4); transition: all 0.2s ease-in-out; }
    .btn-custom-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(79, 70, 229, 0.5); }
    .btn-custom-primary:disabled { background-color: #e2e8f0; color: #94a3b8; box-shadow: none; transform: none; cursor: not-allowed; }
    .btn-custom-primary svg { margin-right: 0.5rem; }

    @media (max-width: 700px) {
        .NavSide__sidebar {
            /* Sidebar disembunyikan ke kiri di mobile */
            transform: translateX(-100%); 
            width: 250px; /* Lebar sidebar saat muncul di mobile */
            box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2); 
        }
        /* Kelas ini akan ditambahkan oleh JavaScript untuk menampilkan sidebar */
        .NavSide__sidebar.NavSide__sidebar--active-mobile {
            transform: translateX(0);
        }
        
        .NavSide__main-content {
            margin-left: 0; 
            width: 100%;
            padding: 1rem;
        }
        .text-heading { font-size: large; }
        .upload-area-v2 { padding: 1.5rem; }
        .upload-area-v2 #initial-state svg, 
        .upload-area-v2 #selected-state svg { width: 60px; height: 60px; }
        .btn-custom-primary { padding: 0.6rem 1.2rem; font-size: 0.9rem; }

        /* Tampilkan tombol toggle di mobile */
        .NavSide__toggle {
            display: flex; 
        }
        /* Opsional: Geser tombol toggle saat sidebar terbuka */
        .NavSide__toggle.NavSide__toggle--active {
            left: calc(250px + 10px); /* 250px = lebar sidebar mobile */
        }
    }
    </style>
</head>
<body>

<div id="NavSide">
    <button class="NavSide__toggle" id="sidebarToggleBtn" aria-label="Toggle sidebar">
        <i class="bi bi-list open-icon"></i>   <i class="bi bi-x close-icon"></i>  </button>
    <aside class="NavSide__sidebar" id="mainSidebar"> 
        <h4>ASTRAtech</h4>
        <ul class="NavSide__sidebar-nav">
            <li class="NavSide__sidebar-item">
                <a href="#" class="NavSide__sidebar-link">
                    <span class="NavSide__sidebar-title">Detail Pengajuan</span>
                </a>
            </li>
            <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                <a href="#" class="NavSide__sidebar-link">
                    <span class="NavSide__sidebar-title">Perbaikan</span>
                </a>
            </li>
            <li class="NavSide__sidebar-item">
                <a href="#" class="NavSide__sidebar-link">
                    <span class="NavSide__sidebar-title">Nilai Akhir</span>
                </a>
            </li>
        </ul>
    </aside>

    <div class="NavSide__main-content">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-heading">Detail Sidang - Sistem Pengajuan Sidang</h2>
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

        <div class="revision-card">
            <h5 class="fw-bold text-primary">Dokumen Revisi</h5>
            <form id="revisionForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                <label for="fileInput" class="upload-area-v2" id="uploadArea">
                    <div id="initial-state">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#ced4da" class="bi bi-file-earmark-arrow-up" viewBox="0 0 16 16"><path d="M8.5 11.5a.5.5 0 0 1-1 0V7.707L6.354 8.854a.5.5 0 1 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 7.707V11.5z"/><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 1.5v2A1.5 1.5 0 0 0 11 5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v1.5z"/></svg>
                    </div>
                    <div id="selected-state" class="d-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#8d99ae" class="bi bi-file-earmark-text-fill" viewBox="0 0 16 16"><path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.707 0H9.293zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM4.5 9a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zM4.5 10.5a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zM4.5 12a.5.5 0 0 1 0-1h4a.5.5 0 0 1 0 1h-4z"/></svg>
                    </div>
                    <p id="upload-prompt-text">Unggah berkas revisi dengan format pdf, docx, pptx, dan zip</p>
                </label>
                <input type="file" id="fileInput" name="fileInput" accept=".pdf,.docx,.pptx,.zip" hidden />
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
</div> 

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php
if (!empty($pesan)):
    $isSuccess = strpos(strtolower($pesan), 'sukses') !== false;
    $icon = $isSuccess ? 'success' : 'error';
    $title = $isSuccess ? 'Berhasil!' : 'Oops...';
?>
<script>
    Swal.fire({
        title: '<?php echo $title; ?>',
        text: '<?php echo addslashes(preg_replace("/\r|\n/", "\\n", $pesan)); ?>',
        icon: '<?php echo $icon; ?>',
        confirmButtonColor: '<?php echo $isSuccess ? "#198754" : "#dc3545"; ?>'
    });
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Kode JavaScript untuk Upload File (Sudah Ada) ---
    const fileInput = document.getElementById('fileInput');
    const submitBtn = document.getElementById('submitBtn');
    const revisionForm = document.getElementById('revisionForm');
    const btnKembali = document.getElementById('btnKembali');
    
    const initialState = document.getElementById('initial-state');
    const selectedState = document.getElementById('selected-state');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    const uploadPromptText = document.getElementById('upload-prompt-text'); 
    
    if (fileInput && submitBtn && revisionForm && initialState && selectedState && fileNameDisplay && uploadPromptText) {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                initialState.classList.add('d-none');
                selectedState.classList.remove('d-none');
                fileNameDisplay.textContent = this.files[0].name;
                uploadPromptText.classList.add('d-none'); 
                submitBtn.disabled = false;
            } else {
                initialState.classList.remove('d-none');
                selectedState.classList.add('d-none');
                fileNameDisplay.textContent = '';
                uploadPromptText.classList.remove('d-none'); 
                submitBtn.disabled = true;
            }
        });

        revisionForm.addEventListener('submit', function(event) {
            event.preventDefault(); 
            Swal.fire({
                title: 'Perhatian',
                text: "Apakah anda sudah yakin ingin mengupload dokumen?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Lanjutkan',
                cancelButtonText: 'Batalkan',
                customClass: { popup: 'rounded-4' }
            }).then((result) => {
                if (result.isConfirmed) {
                    revisionForm.submit();
                }
            });
        });
    } else {
        console.error("Peringatan: Salah satu elemen HTML untuk fungsionalitas upload tidak ditemukan.");
    }

    if (btnKembali) {
        btnKembali.addEventListener('click', function() {
            history.back();
        });
    }

    // === JAVASCRIPT BARU UNTUK TOGGLE SIDEBAR ===
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const mainSidebar = document.getElementById('mainSidebar');

    if (sidebarToggleBtn && mainSidebar) {
        sidebarToggleBtn.addEventListener('click', function() {
            mainSidebar.classList.toggle('NavSide__sidebar--active-mobile');
            this.classList.toggle('NavSide__toggle--active'); 
        });
    } else {
        console.error("Peringatan: Elemen sidebar atau tombol toggle tidak ditemukan untuk fungsionalitas mobile.");
    }
    // === AKHIR JAVASCRIPT BARU UNTUK TOGGLE SIDEBAR ===
});
</script>
</body>
</html>