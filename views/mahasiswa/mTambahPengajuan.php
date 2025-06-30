<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$path_to_root = '../../';

// 1. Cek jika pengguna BELUM login.
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php"); 
    exit(); 
}

// 2. Cek jika role pengguna BUKAN 'mahasiswa'.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php");
    exit(); 
}

include '../../koneksi/koneksiAndrew.php';
$success_message = '';
$error_message = '';


// Ambil NIM dari sesi yang sudah login
if (!isset($_SESSION['nim'])) {
    // This should ideally never happen because of the checks above,
    // but it's a good safeguard.
    die("KESALAHAN FATAL: NIM pengguna tidak ditemukan di sesi. Silakan login kembali.");
}

// Gunakan variabel nim dari sesi yang sudah ada
$nim_mahasiswa_logged_in = $_SESSION['nim'];

// Menangani pengiriman form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])) {
    // Mulai transaksi
    sqlsrv_begin_transaction($conn);

    try {
        // LANGKAH 1: Buat ID Sidang baru secara manual
        $next_id_sidang = 1;
        $sql_id = "SELECT MAX(id_sidang) AS max_id FROM dbo.Sidang";
        $stmt_id = sqlsrv_query($conn, $sql_id);
        if ($stmt_id === false) {
            throw new Exception("Gagal mendapatkan max ID: " . print_r(sqlsrv_errors(), true));
        }
        if ($row_id = sqlsrv_fetch_array($stmt_id, SQLSRV_FETCH_ASSOC)) {
            $next_id_sidang = $row_id['max_id'] !== null ? $row_id['max_id'] + 1 : 1;
        }
        sqlsrv_free_stmt($stmt_id);

        // LANGKAH 2: Cari ID Kelompok mahasiswa
        $id_kelompok = $_POST['kelompok']; // Get selected group from form
        // Validate student belongs to selected group
        $sql_validate = "SELECT 1 FROM dbo.Kelompok_Mahasiswa 
                         WHERE nim = ? AND id_kelompok = ?";
        $params_validate = [$nim_mahasiswa_logged_in, $id_kelompok];
        $stmt_validate = sqlsrv_query($conn, $sql_validate, $params_validate);

        if (!$stmt_validate || !sqlsrv_has_rows($stmt_validate)) {
            throw new Exception("Anda tidak terdaftar dalam kelompok yang dipilih");
        }
        sqlsrv_free_stmt($stmt_validate);   

        // LANGKAH 3: Cari Dosen Pembimbing untuk relasi ke Detail_Sidang
        $nomor_dosen_pembimbing = null;
        $sql_dosen = "SELECT nomor_dosen FROM dbo.Bimbingan WHERE id_kelompok = ? AND isPembimbing = 0x01";
        $params_dosen = [$id_kelompok];
        $stmt_dosen = sqlsrv_query($conn, $sql_dosen, $params_dosen);
        if ($stmt_dosen) {
            if ($row_dos = sqlsrv_fetch_array($stmt_dosen, SQLSRV_FETCH_ASSOC)) {
                $nomor_dosen_pembimbing = $row_dos['nomor_dosen'];
            }
            sqlsrv_free_stmt($stmt_dosen);
        }

        // LANGKAH 4: Baca isi file (konten biner) ke dalam variabel dengan validasi
        $dok_laporan_content = null;
        if (isset($_FILES['DokumenSidang']) && $_FILES['DokumenSidang']['error'] == UPLOAD_ERR_OK) {
            $file = $_FILES['DokumenSidang'];
            $fileSize = $file['size'];
            $allowedTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'];
            $fileType = mime_content_type($file['tmp_name']);
            if ($fileSize > 10 * 1024 * 1024) { // Batas 10MB
                throw new Exception("Ukuran file melebihi 10MB.");
            }
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Tipe file tidak diizinkan. Gunakan PDF, DOCX, PPTX, atau ZIP.");
            }
            $dok_laporan_content = file_get_contents($file['tmp_name']);
        } else {
            throw new Exception("Gagal mengunggah file: " . ($_FILES['DokumenSidang']['error'] ? "Error " . $_FILES['DokumenSidang']['error'] : "Tidak ada file yang dipilih"));
        }

        // LANGKAH 5: Ambil data form lainnya
        $judul = trim($_POST['judul']);
        $id_matkul_terpilih = $_POST['matkul'];
        $aksi = $_POST['aksi'];
        $status_ajuan = ($aksi == 'Kirim') ? 0x01 : 0x00; // Gunakan format biner 0x01 / 0x00

        if (empty($judul)) {
            throw new Exception("Judul tidak boleh kosong.");
        }
        if (empty($id_matkul_terpilih)) {
            throw new Exception("Mata kuliah harus dipilih.");
        }

        // Tentukan jenis sidang
        $sql_get_matkul_name = "SELECT nama_matkul FROM dbo.MataKuliah WHERE id_matkul = ?";
        $params_get_matkul_name = [$id_matkul_terpilih];
        $stmt_get_matkul_name = sqlsrv_query($conn, $sql_get_matkul_name, $params_get_matkul_name);
        if ($stmt_get_matkul_name === false) {
            throw new Exception("Gagal mengambil nama mata kuliah: " . print_r(sqlsrv_errors(), true));
        }
        $nama_matkul_terpilih = '';
        if ($row_matkul = sqlsrv_fetch_array($stmt_get_matkul_name, SQLSRV_FETCH_ASSOC)) {
            $nama_matkul_terpilih = $row_matkul['nama_matkul'];
        }
        sqlsrv_free_stmt($stmt_get_matkul_name);
        $jenis_sidang_bit = ($nama_matkul_terpilih == 'Tugas Akhir') ? 0x00 : 0x01;

        // LANGKAH 6: INSERT PERTAMA KE TABEL 'Sidang'
        $sql_sidang = "INSERT INTO dbo.Sidang (
                        id_sidang, judul, waktu_pengumpulan, dok_laporan, 
                        status_ajuan, jenis_sidang, id_kelompok
                       ) 
                       VALUES (?, ?, GETDATE(), ?, ?, ?, ?)";
        $params_sidang = [
            $next_id_sidang,
            $judul,
            array($dok_laporan_content, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STREAM(SQLSRV_ENC_BINARY)),
            $status_ajuan,
            $jenis_sidang_bit,
            $id_kelompok
        ];
        $stmt_sidang = sqlsrv_prepare($conn, $sql_sidang, $params_sidang);
        if ($stmt_sidang === false || !sqlsrv_execute($stmt_sidang)) {
            throw new Exception("Gagal menyimpan ke tabel Sidang: " . print_r(sqlsrv_errors(), true));
        }

        // LANGKAH 7: INSERT KEDUA KE TABEL 'Detail_Sidang'
        $sql_detail = "INSERT INTO dbo.Detail_Sidang (id_sidang, nomor_dosen, id_matkul) 
                       VALUES (?, ?, ?)";
        $params_detail = [$next_id_sidang, $nomor_dosen_pembimbing, $id_matkul_terpilih];
        $stmt_detail = sqlsrv_prepare($conn, $sql_detail, $params_detail);
        if ($stmt_detail === false || !sqlsrv_execute($stmt_detail)) {
            throw new Exception("Gagal menyimpan ke tabel Detail_Sidang: " . print_r(sqlsrv_errors(), true));
        }

        // Commit transaksi jika semua berhasil
        sqlsrv_commit($conn);
        $success_message = ($status_ajuan == 0x01) ? 'Pengajuan Berhasil Dikirim!' : 'Pengajuan Berhasil Disimpan!';

    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        sqlsrv_rollback($conn);
        $error_message = "Error: " . $e->getMessage();
    }

    // Bebaskan sumber daya
    if (isset($stmt_sidang)) sqlsrv_free_stmt($stmt_sidang);
    if (isset($stmt_detail)) sqlsrv_free_stmt($stmt_detail);
}
?>

<!DOCTYPE html>
<html lang="id">

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
                <img src="../../assets/img/WhiteAstra.png" alt="Logo AstraTech">
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
                    <h2 class="text-heading"><?php echo isset($_SESSION['user_data']['nama_mhs']) ? htmlspecialchars($_SESSION['user_data']['nama_mhs']) : 'Mahasiswa'; ?> (Mahasiswa)</h2>
                    <div class="header-icons d-none d-md-flex">
                        <a href="mNotifikasi.php" title="tugas"><i class="bi bi-bell-fill"></i></a>
                        <div class="profile-icon">
                            <a href="mProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h5 class="fw-bold mt-4 mb-3">Tambah Sidang</h5>
                        <hr>
                    </div>

                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="aksi" id="formAksi" value="">

                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Sidang
                                <span class="text-danger">* </span>
                            </label>
                            <input type="text" class="form-control" id="judul" name="judul" placeholder="Masukkan Judul Sidang" required />
                        </div>
                        <div class="mb-3">
                            <label for="matkul" class="form-label">Mata Kuliah
                                <span class="text-danger">* </span>
                            </label>
                            <select class="form-select" id="matkul" name="matkul" required>
                                <option value="" selected disabled>Pilih Mata Kuliah</option>
                                <?php
                                $sql_matkul = "SELECT id_matkul, nama_matkul FROM dbo.MataKuliah ORDER BY nama_matkul ASC";
                                $stmt_matkul = sqlsrv_query($conn, $sql_matkul);
                                if ($stmt_matkul) {
                                    while ($matkul_row = sqlsrv_fetch_array($stmt_matkul, SQLSRV_FETCH_ASSOC)) {
                                        echo '<option value="' . htmlspecialchars($matkul_row['id_matkul']) . '">' . htmlspecialchars($matkul_row['nama_matkul']) . '</option>';
                                    }
                                } else {
                                    echo '<option disabled>Error memuat mata kuliah</option>';
                                }
                                sqlsrv_free_stmt($stmt_matkul);
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kelompok" class="form-label">Kelompok 
                                <span class="text-danger">* </span>
                            </label>
                            <select class="form-select" id="kelompok" name="kelompok" required>
                                <option value="" selected disabled>Pilih Kelompok</option>
                                <?php
                                $sql_kelompok = "SELECT DISTINCT k.id_kelompok 
                                                 FROM dbo.Kelompok_Mahasiswa km
                                                 JOIN dbo.Kelompok k ON km.id_kelompok = k.id_kelompok
                                                 WHERE km.nim = ?";
                                $params_kelompok = [$nim_mahasiswa_logged_in];
                                $stmt_kelompok = sqlsrv_query($conn, $sql_kelompok, $params_kelompok);
                                
                                if ($stmt_kelompok) {
                                    while ($kelompok_row = sqlsrv_fetch_array($stmt_kelompok, SQLSRV_FETCH_ASSOC)) {
                                        echo '<option value="' . htmlspecialchars($kelompok_row['id_kelompok']) . '">';
                                        echo 'Kelompok ' . htmlspecialchars($kelompok_row['id_kelompok']);
                                        echo '</option>';
                                    }
                                    sqlsrv_free_stmt($stmt_kelompok);
                                } else {
                                    echo '<option disabled>Error memuat kelompok</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="mb-4">
                                <div class="p-4 rounded bg-light border text-start">
                                    <h6 class="fw-bold text-dark">Dokumen Sidang
                                        <span class="text-danger">* </span>
                                    </h6>
                                    <div id="DokumenSidangForm">
                                        <label class="upload-box w-100 mt-3 text-center">
                                            <input type="file" id="DokumenSidang" name="DokumenSidang" accept=".pdf,.docx,.pptx,.zip" hidden required />
                                            <div class="upload-content">
                                                <svg id="uploadIcon" xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#6c757d" class="bi bi-upload" viewBox="0 0 16 16">
                                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v3.6a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5V10.4a.5.5 0 0 1 1 0v3.6a1.5 1.5 0 0 1-1.5 1.5H1.5A1.5 1.5 0 0 1 0 14V10.4a.5.5 0 0 1 .5-.5z"/>
                                                    <path d="M7.646 1.646a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 3.207V10a.5.5 0 0 1-1 0V3.207L5.354 5.354a.5.5 0 1 1-.708-.708l3-3z"/>
                                                </svg>
                                                <p class="mt-2 text-muted small DokumenLabelText">Unggah file dengan format pdf, docx, pptx, dan zip</p>
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
                                    <span class="icon-circle">
                                        <i class="fa-solid fa-arrow-left"></i>
                                    </span>
                                    Kembali
                                </button>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" name="aksi" value="Simpan" class="btn btn-secondary" id="btnSimpan">Simpan</button>
                                <button type="button" name="aksi" value="Kirim" class="btn-setuju" id="btnKirim">Kirim</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal fade" id="logMBeranda" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div style="background-color: rgb(67, 54, 240);">
                                <div class="modal-header">
                                    <h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1>
                                </div>
                            </div>
                            <div class="modal-body mx-auto">Apakah anda yakin ingin keluar?</div>
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

    <script>
        // Menampilkan notifikasi dari PHP
        <?php if (!empty($success_message)): ?>
        Swal.fire({
            title: 'Berhasil!',
            text: '<?php echo addslashes($success_message); ?>',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#4B68FB'
        }).then(() => {
            window.location.href = 'mPengajuan.php'; // Redirect ke halaman mahasiswa
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

        // Logika Sidebar
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");
        if (menuToggle) {
            menuToggle.onclick = function() {
                menuToggle.classList.toggle("NavSide__toggle--active");
                sidebar.classList.toggle("NavSide__sidebar--active-mobile");
            };
        }

        // Penanganan unggahan file
        const DokumenSidang = document.getElementById('DokumenSidang');
        const uploadBox = document.querySelector('.upload-box');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const uploadIcon = document.getElementById('uploadIcon');
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

        // Validasi dan pengiriman form
        const btnKirim = document.getElementById('btnKirim');
        const btnSimpan = document.getElementById('btnSimpan');
        const btnLanjutkan = document.getElementById('btnLanjutkan');
        const form = document.querySelector('form');
        const formAksi = document.getElementById('formAksi');

        function validateForm() {
            const judul = document.getElementById('judul').value;
            const matkul = document.getElementById('matkul').value;
            const laporan = document.getElementById('DokumenSidang').files.length;

            if (judul.trim() === "") {
                Swal.fire({ title: 'Judul tidak boleh kosong!', icon: 'error', confirmButtonText: 'OK', confirmButtonColor: '#4B68FB' });
                return false;
            }
            if (matkul === "" || !matkul) {
                Swal.fire({ title: 'Pilih mata kuliah!', icon: 'error', confirmButtonText: 'OK', confirmButtonColor: '#4B68FB' });
                return false;
            }
            if (laporan === 0) {
                Swal.fire({ title: 'Dokumen tidak boleh kosong!', icon: 'error', confirmButtonText: 'OK', confirmButtonColor: '#4B68FB' });
                return false;
            }
            if (kelompok === "" || !kelompok) {
            Swal.fire({ title: 'Pilih kelompok!', icon: 'error', confirmButtonText: 'OK', confirmButtonColor: '#4B68FB' });
            return false;
            }
            return true;
        
            return true;
        }

        btnKirim.addEventListener('click', function(e) {
            e.preventDefault();
            if (validateForm()) {
                formAksi.value = 'Kirim';
                const modalValidasi = new bootstrap.Modal(document.getElementById('modalValidasi'));
                modalValidasi.show();
            }
        });

        btnSimpan.addEventListener('click', function(e) {
            e.preventDefault();
            if (validateForm()) {
                formAksi.value = 'Simpan';
                form.submit();
            }
        });

        btnLanjutkan.addEventListener('click', function() {
            form.submit();
        });
    </script>
</body>
</html>