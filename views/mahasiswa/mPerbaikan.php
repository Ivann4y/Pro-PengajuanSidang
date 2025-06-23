<?php
// Selalu mulai session di baris paling atas
session_start();

// Anda memanggil file koneksi yang membuat variabel $conn
require "../../koneksi/koneksiAndrew.php";

$pesan = '';
if (isset($_SESSION['pesan'])) {
    $pesan = $_SESSION['pesan'];
    unset($_SESSION['pesan']);
}

// === PERUBAHAN 1: Menggunakan Session untuk ID Sidang ===
// Cek apakah ID sidang ada di dalam session.
if (!isset($_SESSION['selected_sidang_id']) || !is_numeric($_SESSION['selected_sidang_id'])) {
    // Jika tidak ada, hentikan eksekusi dan berikan pesan error.
    // Pengguna harus memilih sidang dari halaman sebelumnya.
    die("Error: Tidak ada sidang yang dipilih. Silakan kembali ke halaman daftar sidang dan pilih salah satu.");
}
// Jika session ada, gunakan nilainya.
$id_sidang = (int) $_SESSION['selected_sidang_id'];


// === LOGIKA FETCH DATA (Tidak ada perubahan di sini, karena sudah menggunakan $id_sidang) ===
$nama_mahasiswa = '';
$status_revisi = '';
$catatan_list = [];

$query_info = "
    SELECT TOP 1 ds.status_revisi, m.nama_mhs
    FROM Detail_Sidang ds
    JOIN Sidang s ON ds.id_sidang = s.id_sidang
    JOIN Kelompok_Mahasiswa km ON s.id_kelompok = km.id_kelompok
    JOIN Mahasiswa m ON km.nim = m.nim
    WHERE ds.id_sidang = ?
";
$params_info = array($id_sidang);
$stmt_info = sqlsrv_query($conn, $query_info, $params_info);
if ($stmt_info === false) {
    die("Error saat menjalankan query info: <br><pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}
$data_info = sqlsrv_fetch_array($stmt_info, SQLSRV_FETCH_ASSOC);
if (!$data_info) {
    die("Error: Data sidang dengan ID " . htmlspecialchars($id_sidang) . " tidak ditemukan.");
}
$nama_mahasiswa = $data_info['nama_mhs'];
$status_revisi = $data_info['status_revisi'];

$query_catatan = "
    SELECT ds.catatan_sidang, d.nama_dosen
    FROM Detail_Sidang ds
    JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen
    WHERE ds.id_sidang = ?
    ORDER BY d.nama_dosen ASC
";
$params_catatan = array($id_sidang);
$stmt_catatan = sqlsrv_query($conn, $query_catatan, $params_catatan);
if ($stmt_catatan === false) {
    die("Error saat menjalankan query catatan: <br><pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}
while ($row = sqlsrv_fetch_array($stmt_catatan, SQLSRV_FETCH_ASSOC)) {
    $catatan_list[] = $row;
}

// === LOGIKA FILE UPLOAD (Tidak ada perubahan di sini) ===
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["fileInput"]) && $_FILES["fileInput"]["error"] == 0) {
        $folder_target = "uploads/";
        if (!file_exists($folder_target)) {
            mkdir($folder_target, 0755, true);
        }

        $file_asli = basename($_FILES["fileInput"]["name"]);
        $ekstensi_file = strtolower(pathinfo($file_asli, PATHINFO_EXTENSION));
        $file_unik = 'revisi_' . $id_sidang . '_' . time() . '.' . $ekstensi_file;
        $path_target = $folder_target . $file_unik;
        $ekstensi_diizinkan = array("pdf", "docx", "pptx", "zip");

        if (!in_array($ekstensi_file, $ekstensi_diizinkan) || $_FILES["fileInput"]["size"] > 5242880) { // Max 5MB
            $pesan = "Error: Format atau ukuran file tidak sesuai.";
        } else {
            if (move_uploaded_file($_FILES["fileInput"]["tmp_name"], $path_target)) {
                $query_update_dokumen = "
                    UPDATE Detail_Sidang 
                    SET dok_revisi = ?, status_revisi = 'Menunggu Persetujuan' 
                    WHERE id_sidang = ?
                ";
                $params_update = array($path_target, $id_sidang);
                $stmt_update = sqlsrv_query($conn, $query_update_dokumen, $params_update);

                if ($stmt_update) {
                    $_SESSION['pesan'] = "Sukses: File revisi '" . htmlspecialchars($file_asli) . "' berhasil diunggah.";
                } else {
                    unlink($path_target);
                    $_SESSION['pesan'] = "Error: Gagal menyimpan informasi file ke database.";
                }
                // Redirect ke halaman yang sama (tanpa parameter URL) untuk mencegah resubmit form
                header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
                exit();
            } else {
                $pesan = "Error: Maaf, terjadi kesalahan saat memindahkan file.";
            }
        }
    } elseif (isset($_FILES["fileInput"])) {
        // ... (logika error handling upload)
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detail Sidang & Perbaikan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../css/style.css" />

    <style>
        /* === CSS LENGKAP DAN BERSIH UNTUK SIDEBAR DAN KONTEN (SUDAH DISESUAIKAN) === */
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background-color: #ffffff;
        }

        #NavSide {
            display: flex;
            min-height: 100vh;
        }

        #page-content-wrapper {
            flex-grow: 1;
            margin-left: 280px;
            transition: margin-left 0.4s ease-in-out;
            display: flex;
            flex-direction: column;
        }

        .NavSide__main-content {
            padding: 2.5rem;
            flex-grow: 1;
            overflow-y: auto;
            background-color: #ffffff;
            border-top-left-radius: 40px;
        }

        /* --- STYLING SIDEBAR YANG DIINGINKAN --- */
        .NavSide__sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 280px;
            background: #4B68FB;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.4s ease-in-out;
        }

        .NavSide__sidebar-brand {
            padding: 2.5rem 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        .NavSide__sidebar-brand img {
            width: 80%;
            max-width: 150px;
            height: auto;
        }

        .NavSide__sidebar-nav {
            width: 100%;
            padding: 0;
            list-style: none;
            flex-grow: 1;
            margin-top: 10vh;
        }

        .NavSide__sidebar-item {
            position: relative;
            width: 100%;
            padding-right: 20px;
            margin-bottom: 8px;
        }

        /* === PERUBAHAN DI SINI === */
        .NavSide__sidebar-item a {
            position: relative;
            display: flex;
            align-items: center;
            /* Membuat teks di tengah secara vertikal */
            justify-content: center;
            /* Membuat teks di tengah secara horizontal */
            width: 100%;
            text-decoration: none;
            color: #fff;
            padding: 1rem 1.5rem;
            /* Padding disesuaikan agar tidak terlalu lebar */
            height: 60px;
            box-sizing: border-box;
            z-index: 1;
        }

        .NavSide__sidebar-item--active {
            background-color: #ffffff;
            border-top-left-radius: 30px;
            border-bottom-left-radius: 30px;
        }

        .NavSide__sidebar-item--active a {
            color: #4B68FB;
        }

        .NavSide__sidebar-item--active b:nth-child(1),
        .NavSide__sidebar-item--active b:nth-child(2) {
            position: absolute;
            right: 0;
            width: 20px;
            height: 30px;
            background-color: transparent;
            z-index: 0;
        }

        .NavSide__sidebar-item--active b:nth-child(1) {
            top: -30px;
        }

        .NavSide__sidebar-item--active b:nth-child(2) {
            bottom: -30px;
        }

        .NavSide__sidebar-item--active b:nth-child(1)::before,
        .NavSide__sidebar-item--active b:nth-child(2)::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #4B68FB;
        }

        .NavSide__sidebar-item--active b:nth-child(1)::before {
            border-bottom-right-radius: 30px;
        }

        .NavSide__sidebar-item--active b:nth-child(2)::before {
            border-top-right-radius: 30px;
        }

        /* --- SISA CSS KONTEN (Tidak diubah) --- */
        .page-content-header-wrapper h1 {
            font-size: 2rem;
        }

        .badge-custom {
            padding: 8px 14px;
            border-radius: 20px;
            font-weight: 600;
            color: white;
        }

        .status-belum-disetujui {
            background-color: #FFA3A3;
        }

        .status-menunggu-persetujuan {
            background-color: #FFD56F;
            color: #5d4a1a;
        }

        .status-disetujui {
            background-color: #A3E4D7;
            color: #0E6655;
        }

        .card-comment {
            background-color: #f1f3f5;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            border: none;
        }

        .card-comment:hover {
            background-color: #4B68FB;
            color: #fff;
        }

        .card-comment:hover strong,
        .card-comment:hover .text-selengkapnya {
            color: #fff;
        }

        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .text-selengkapnya {
            font-weight: 600;
            color: #4B68FB;
            font-size: 0.85rem;
            text-decoration: none;
            transition: color 0.3s ease;
            margin-left: 4px;
        }

        .revision-card {
            background-color: white;
            border-radius: 1.5rem;
            padding: 2rem;
            border: 1px solid #e9ecef;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
        }

        .upload-area-v2 {
            background-color: #f8f9fa;
            border: 2px dashed #e0e0e0;
            border-radius: 1rem;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.2s;
            min-height: 180px;
        }

        .upload-area-v2:hover {
            background-color: #f1f3f5;
        }

        .btn-custom-primary {
            background-color: #4FD382;
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 4px 15px rgba(75, 254, 159, 0.87);
            transition: all 0.2s ease-in-out;
        }

        .btn-custom-primary:hover {
            background-color: #3FA970;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(75, 254, 159, 0.87);
            color: white;
        }

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
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mdetailSidang.php?id_sidang=<?php echo $id_sidang; ?>">
                        <span class="fw-semibold">Detail Pengajuan</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="mPerbaikan.php?id_sidang=<?php echo $id_sidang; ?>">
                        <span class="fw-semibold">Perbaikan</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mNilaiakhir.php?id_sidang=<?php echo $id_sidang; ?>">
                        <span class="fw-semibold">Nilai Akhir</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mSidang.php">
                        <span class="fw-semibold">Kembali</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>

        <div id="page-content-wrapper">
            <main class="NavSide__main-content">
                <div class="page-content-header-wrapper">
                    <h1 class="fs-2 fw-bold">Detail Sidang - Sistem Pengajuan Sidang</h1>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <h2 class="fs-5 fw-semibold mb-0">Catatan Perbaikan -
                            <?php echo htmlspecialchars($nama_mahasiswa); ?>
                        </h2>
                        <span
                            class="badge-custom status-<?php echo strtolower(str_replace(' ', '-', $status_revisi)); ?>">Status
                            Revisi : <?php echo htmlspecialchars($status_revisi); ?></span>
                    </div>
                </div>

                <?php foreach ($catatan_list as $index => $catatan): ?>
                    <div class="card-comment mt-4" data-bs-toggle="modal"
                        data-bs-target="#modalDetail<?php echo $index; ?>">
                        <strong><?php echo htmlspecialchars($catatan['nama_dosen']); ?> - Penguji</strong>
                        <p class="mt-2 mb-0 text-truncate-2">
                            <?php echo htmlspecialchars($catatan['catatan_sidang']); ?>
                            <span class="text-selengkapnya">Selengkapnya...</span>
                        </p>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($catatan_list)): ?>
                    <div class="alert alert-info mt-4">Belum ada catatan perbaikan untuk sidang ini.</div>
                <?php endif; ?>

                <div class="revision-card mt-4">
                    <h5 class="fw-bold" style="color:#4B68FB;">Dokumen Revisi</h5>
                    <form id="revisionForm"
                        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                        method="POST" enctype="multipart/form-data">
                        <label for="fileInput" class="upload-area-v2 mt-3" id="uploadArea">
                            <div id="initial-state"><i class="bi bi-file-earmark-arrow-up fs-1 text-secondary"></i>
                            </div>
                            <div id="selected-state" class="d-none"><i
                                    class="bi bi-file-earmark-text-fill fs-1 text-primary"></i></div>
                            <p id="upload-prompt-text" class="text-muted mt-2">Unggah berkas revisi (.pdf, .docx, .pptx,
                                .zip)</p>
                        </label>
                        <input type="file" id="fileInput" name="fileInput" accept=".pdf,.docx,.pptx,.zip" hidden />
                        <div class="text-center mt-2">
                            <p id="fileNameDisplay" class="fw-bold mb-0"></p>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-custom-primary" id="openConfirmModalBtn"
                                data-bs-toggle="modal" data-bs-target="#modalKonfirmasi" disabled>Kirim</button>
                        </div>
                    </form>
                </div>

                <?php foreach ($catatan_list as $index => $catatan): ?>
                    <div class="modal fade" id="modalDetail<?php echo $index; ?>" tabindex="-1"
                        aria-labelledby="modalDetailLabel<?php echo $index; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title fs-5" id="modalDetailLabel<?php echo $index; ?>">Detail Catatan
                                        dari <?php echo htmlspecialchars($catatan['nama_dosen']); ?></h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Tutup"></button>
                                </div>
                                <div class="modal-body">
                                    <p style="white-space: pre-wrap;">
                                        <?php echo htmlspecialchars($catatan['catatan_sidang']); ?>
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </main>
        </div>
    </div>

    <div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-labelledby="modalKonfirmasiLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalKonfirmasiLabel">Perhatian</h4>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda sudah yakin ingin mengupload dokumen revisi ini?</p>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-tolak me-3" data-bs-dismiss="modal">Batalkan</button>
                        <button type="button" class="btn" id="confirmSubmitBtn"
                            style="background-color: #4FD382; color:white;">Lanjutkan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php
    if (!empty($pesan)):
        $isSuccess = stripos($pesan, 'sukses') !== false;
        $cleanPesan = preg_replace('/^(Sukses|Error): /i', '', $pesan);
        ?>
        <script>
            Swal.fire({
                title: '<?php echo $isSuccess ? "Berhasil" : "Error"; ?>',
                text: '<?php echo addslashes($cleanPesan); ?>',
                icon: '<?php echo $isSuccess ? "success" : "error"; ?>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4B68FB'
            });
        </script>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.querySelector(".NavSide__toggle");
            const sidebar = document.getElementById("main-sidebar");
            if (menuToggle && sidebar) {
                menuToggle.onclick = function () {
                    menuToggle.classList.toggle("NavSide__toggle--active");
                    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                };
            }
            const fileInput = document.getElementById('fileInput');
            const openConfirmModalBtn = document.getElementById('openConfirmModalBtn');
            const initialState = document.getElementById('initial-state');
            const selectedState = document.getElementById('selected-state');
            const fileNameDisplay = document.getElementById('fileNameDisplay');
            const uploadPromptText = document.getElementById('upload-prompt-text');
            if (fileInput && openConfirmModalBtn) {
                fileInput.addEventListener('change', function () {
                    if (this.files.length > 0) {
                        if (initialState) initialState.classList.add('d-none');
                        if (selectedState) selectedState.classList.remove('d-none');
                        if (fileNameDisplay) fileNameDisplay.textContent = this.files[0].name;
                        if (uploadPromptText) uploadPromptText.classList.add('d-none');
                        openConfirmModalBtn.disabled = false;
                    } else {
                        if (initialState) initialState.classList.remove('d-none');
                        if (selectedState) selectedState.classList.add('d-none');
                        if (fileNameDisplay) fileNameDisplay.textContent = '';
                        if (uploadPromptText) uploadPromptText.classList.remove('d-none');
                        openConfirmModalBtn.disabled = true;
                    }
                });
            }
            const revisionForm = document.getElementById('revisionForm');
            const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
            if (revisionForm && confirmSubmitBtn) {
                confirmSubmitBtn.addEventListener('click', function () {
                    revisionForm.submit();
                });
            }
        });
    </script>
</body>

</html>