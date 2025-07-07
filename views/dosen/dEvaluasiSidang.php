<?php
session_start();
require "../../koneksi/koneksiAndrew.php"; // Pastikan path ini benar

// Ambil ID sidang dari GET (sekali) lalu simpan ke session
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $_SESSION['id_sidang_aktif'] = (int)$_GET['id'];
    // Hapus nim lama jika id sidang baru dipilih
    unset($_SESSION['nim_aktif']); 
    header("Location: dEvaluasiSidang.php");
    exit;
}

// Ambil NIM dari GET (sekali) lalu simpan ke session
// Ganti blok ini
if (isset($_GET['nim'])) {
    $_SESSION['nim_aktif'] = $_GET['nim'];
    header("Location: dEvaluasiSidang.php");
    exit;
}


if (isset($_GET['nim'])) {
    $_SESSION['nim_aktif'] = $_GET['nim'];
    header("Location: dEvaluasiSidang.php");
    exit;
}

// Menjadi blok ini (Ini sudah benar, pertahankan)
if (isset($_GET['nim'])) {
    $_SESSION['nim_aktif'] = $_GET['nim'];
    // Tidak ada redirect, biarkan script lanjut ke bawah
}
// ===================================================================================
// FIX: AMBIL ID SIDANG DARI SESSION SETELAH REDIRECT
// ===================================================================================
// Pastikan ID sidang ada di session sebelum melanjutkan
if (!isset($_SESSION['id_sidang_aktif'])) {
    // Jika tidak ada, hentikan eksekusi atau redirect ke halaman daftar
    die("Sesi sidang tidak ditemukan. Silakan kembali ke daftar sidang dan pilih kembali.");
}
// Tetapkan variabel $id_sidang dari session agar bisa digunakan di seluruh skrip
$id_sidang = $_SESSION['id_sidang_aktif'];
// ===================================================================================


// ===================================================================================
// SIMULASI DOSEN LOGIN (GANTI DENGAN SESSION ASLI NANTI)
// ===================================================================================

if (!isset($_SESSION['user_data']['nomor_dosen'])) { die("Akses ditolak."); }
$nomor_dosen_login = $_SESSION['user_data']['nomor_dosen'];

// ===================================================================================
// BAGIAN 2: PROSES PENYIMPANAN DATA (SAAT FORM DI-SUBMIT)
// ===================================================================================
// ===================================================================================
// BAGIAN 2: PROSES PENYIMPANAN DATA (SAAT FORM DI-SUBMIT)
// ===================================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil data dari form (NAMA DISAMAKAN DENGAN DATABASE)
    $nim_post = $_POST['nim'] ?? null;
    $catatan_post = $_POST['catatanEvaluasi'] ?? '';
    $n_dokumen = !empty($_POST['n_dokumen']) ? (int)$_POST['n_dokumen'] : null;
    $n_presentasi = !empty($_POST['n_presentasi']) ? (int)$_POST['n_presentasi'] : null;
    $n_tanyajawab = !empty($_POST['n_tanyajawab']) ? (int)$_POST['n_tanyajawab'] : null;
    $n_proyek = !empty($_POST['n_proyek']) ? (int)$_POST['n_proyek'] : null;

    // Validasi penting: pastikan NIM terkirim bersama form
    if (empty($nim_post)) {
        die("Terjadi kesalahan: NIM mahasiswa tidak terkirim saat menyimpan data.");
    }
    
    // 1. UPDATE CATATAN REVISI
    $sql_update_catatan = "UPDATE Detail_Sidang SET catatan_sidang = ? WHERE id_sidang = ? AND nomor_dosen = ?";
    $params_update_catatan = [$catatan_post, $id_sidang, $nomor_dosen_login];
    $stmt_update_catatan = sqlsrv_query($conn, $sql_update_catatan, $params_update_catatan);

    if ($stmt_update_catatan === false) {
        $_SESSION['error'] = "Gagal memperbarui catatan revisi: " . print_r(sqlsrv_errors(), true);
        header("Location: dEvaluasiSidang.php?nim=$nim_post");
        exit;
    }

    // 2. CEK & SIMPAN NILAI (UPSERT)
    $sql_cek_nilai = "SELECT COUNT(*) as 'count' FROM Penilaian WHERE id_sidang = ? AND nomor_dosen = ? AND nim = ?";
    $stmt_cek_nilai = sqlsrv_query($conn, $sql_cek_nilai, [$id_sidang, $nomor_dosen_login, $nim_post]);
    $nilai_exists = sqlsrv_fetch_array($stmt_cek_nilai, SQLSRV_FETCH_ASSOC)['count'] > 0;

    if ($nilai_exists) {
        // UPDATE
        $sql_nilai = "UPDATE Penilaian SET n_dokumen = ?, n_presentasi = ?, n_tanyajawab = ?, n_proyek = ? WHERE id_sidang = ? AND nomor_dosen = ? AND nim = ?";
        $params_nilai = [$n_dokumen, $n_presentasi, $n_tanyajawab, $n_proyek, $id_sidang, $nomor_dosen_login, $nim_post];
    } else {
        // INSERT
        $sql_nilai = "INSERT INTO Penilaian (id_sidang, nim, nomor_dosen, n_dokumen, n_presentasi, n_tanyajawab, n_proyek) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params_nilai = [$id_sidang, $nim_post, $nomor_dosen_login, $n_dokumen, $n_presentasi, $n_tanyajawab, $n_proyek];
    }

    $stmt_nilai = sqlsrv_query($conn, $sql_nilai, $params_nilai);
    if ($stmt_nilai === false) {
        die("Gagal menyimpan nilai: " . print_r(sqlsrv_errors(), true));
    }

    // Redirect
    header("Location: dEvaluasiSidang.php?nim=" . $nim_post . "&status=sukses");
    exit();
}


// ===================================================================================
// BAGIAN 3: PENGAMBILAN DATA UNTUK DITAMPILKAN DI HALAMAN
// ===================================================================================

// Variabel default
$id_kelompok = null;
$judul = 'Data tidak ditemukan';
$ruangan = '-';
$tanggal_formatted = '-';
$jam = '-';
$dosenPembimbing = [];
$dosenPenguji = [];
$catatan_revisi = '';
$nilai_mahasiswa = ['n_dokumen' => '', 'n_presentasi' => '', 'n_tanyajawab' => '', 'n_proyek' => ''];
$mahasiswa = [];
$current_nim = '';
$current_nama_mhs = 'Data mahasiswa tidak ditemukan';

// Inisialisasi variabel yang akan diambil dari query
$jenis_sidang = null;
$id_matkul = null;
$nomor_kelompok = null; // Tambahkan variabel ini

// Ambil data sidang utama, tambahkan jenis_sidang dan id_matkul dari tabel Kelompok
$sql_sidang = "
    SELECT 
        s.judul, 
        k.nomor_kelompok, 
        k.id_kelompok, 
        k.jenis_sidang, 
        k.id_matkul 
    FROM Sidang s 
    JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
    WHERE s.id_sidang = ?";

$result_sidang = sqlsrv_query($conn, $sql_sidang, [$id_sidang]);

if ($data_sidang = sqlsrv_fetch_array($result_sidang, SQLSRV_FETCH_ASSOC)) {
    $judul = $data_sidang['judul'];
    $nomor_kelompok = $data_sidang['nomor_kelompok']; // Ambil nomor kelompok
    $id_kelompok = $data_sidang['id_kelompok'];
    $jenis_sidang = $data_sidang['jenis_sidang'];
    $id_matkul = $data_sidang['id_matkul'];

    // ===============================================================================
    // ===== BAGIAN YANG DIUBAH (LOGIKA PENGAMBILAN MAHASISWA) =====
    // ===============================================================================
    // Logika ini disamakan dengan file dNilaiAkhir.php, yaitu mengambil mahasiswa
    // dari tabel 'Kelompok' berdasarkan 'nomor_kelompok'.
    if (isset($nomor_kelompok)) {
        $sql_mhs = "SELECT DISTINCT k.nim, m.nama_mhs
                    FROM Kelompok k
                    JOIN Mahasiswa m ON k.nim = m.nim
                    WHERE k.nomor_kelompok = ?
                    ORDER BY k.nim ASC";
        
        $stmt_mhs = sqlsrv_query($conn, $sql_mhs, array($nomor_kelompok));
        
        if ($stmt_mhs) {
            while ($row_mhs = sqlsrv_fetch_array($stmt_mhs, SQLSRV_FETCH_ASSOC)) {
                $mahasiswa[] = $row_mhs;
            }
        } else {
            // Opsional: Tambahkan penanganan error jika query gagal
            error_log("Query mahasiswa gagal: " . print_r(sqlsrv_errors(), true));
        }
    }
    // ===============================================================================
    // ===== AKHIR BAGIAN YANG DIUBAH =====
    // ===============================================================================


    // Menentukan mahasiswa yang sedang aktif (dari SESSION atau default mahasiswa pertama)
    if (isset($_SESSION['nim_aktif']) && in_array($_SESSION['nim_aktif'], array_column($mahasiswa, 'nim'))) {
        $current_nim = $_SESSION['nim_aktif'];
    } elseif (!empty($mahasiswa)) {
        $current_nim = $mahasiswa[0]['nim'];
        $_SESSION['nim_aktif'] = $current_nim;
    }


    // Mendapatkan nama mahasiswa yang sedang aktif untuk ditampilkan
    foreach ($mahasiswa as $mhs) {
        if ($mhs['nim'] == $current_nim) {
            $current_nama_mhs = $mhs['nama_mhs'];
            break;
        }
    }

    // Ambil data dosen (pembimbing dan penguji)
    $dosenPembimbing = [];
    $dosenPenguji = [];
    $labelPembimbing = "Dosen Pembimbing";

    if (isset($jenis_sidang)) {
        if ($jenis_sidang == 'Tugas Akhir') {
            $labelPembimbing = "Dosen Pembimbing";
            $sql_pembimbing = "SELECT d.nama_dosen FROM Dosen d JOIN Bimbingan b ON d.nomor_dosen = b.nomor_dosen WHERE b.id_kelompok = ?";
            $stmt_pembimbing = sqlsrv_query($conn, $sql_pembimbing, array($id_kelompok));
            if ($stmt_pembimbing) {
                while ($row = sqlsrv_fetch_array($stmt_pembimbing, SQLSRV_FETCH_ASSOC)) {
                    $dosenPembimbing[] = $row['nama_dosen'];
                }
            }
        } elseif ($jenis_sidang == 'Semester' && isset($id_matkul)) {
            $labelPembimbing = "Dosen Pengampu";
            $sql_pengampu = "
                SELECT d.nama_dosen 
                FROM Dosen d
                JOIN Pengampu_Kelas pk ON d.nomor_dosen = pk.nomor_dosen
                JOIN Kelas kls ON pk.id_kelas = kls.id_kelas
                WHERE kls.id_matkul = ?";
            
        $stmt_pengampu = sqlsrv_query($conn, $sql_pengampu, array($id_matkul));
        if ($stmt_pengampu) {
            while ($row = sqlsrv_fetch_array($stmt_pengampu, SQLSRV_FETCH_ASSOC)) {
                $dosenPembimbing[] = $row['nama_dosen'];
                $dosenPenguji[] = $row['nama_dosen'];
            }
        }
    }
}
// HILANGKAN DUPLIKAT DI KEDUA ARRAY
if (!empty($dosenPembimbing)) {
    $dosenPembimbing = array_unique($dosenPembimbing);
}
    // Ambil penguji dari penjadwalan
    $sql_penguji_jadwal = "SELECT d.nama_dosen FROM Dosen d JOIN Penjadwalan p ON d.nomor_dosen = p.nomor_dosen WHERE p.id_sidang = ? AND p.peran_dosen = 0";
    $stmt_penguji_jadwal = sqlsrv_query($conn, $sql_penguji_jadwal, array($id_sidang));
    if ($stmt_penguji_jadwal) {
        while ($row = sqlsrv_fetch_array($stmt_penguji_jadwal, SQLSRV_FETCH_ASSOC)) {
            $dosenPenguji[] = $row['nama_dosen'];
        }
    }
    
    // Hilangkan duplikat
    if (!empty($dosenPenguji)) {
        $dosenPenguji = array_unique($dosenPenguji);
    }
    
    // Ambil jadwal
    $sql_jadwal = "SELECT ruang_sidang, tanggal_sidang, jam_sidang FROM Jadwal WHERE id_sidang = ?";
    $result_jadwal = sqlsrv_query($conn, $sql_jadwal, [$id_sidang]);
    if ($result_jadwal && $data_jadwal = sqlsrv_fetch_array($result_jadwal, SQLSRV_FETCH_ASSOC)) {
        $ruangan = $data_jadwal['ruang_sidang'] ?? '-';
        $jam = $data_jadwal['jam_sidang'] ? $data_jadwal['jam_sidang']->format('H:i') : '-';
        if ($data_jadwal['tanggal_sidang'] instanceof DateTime) {
            setlocale(LC_TIME, 'id_ID.UTF-8', 'Indonesian');
            $tanggal_formatted = strftime('%A, %d %B %Y', $data_jadwal['tanggal_sidang']->getTimestamp());
        }
    }

    // Ambil catatan revisi (group-level)
    $sql_catatan = "SELECT catatan_sidang FROM Detail_Sidang WHERE id_sidang = ? AND nomor_dosen = ?";
    $result_catatan = sqlsrv_query($conn, $sql_catatan, [$id_sidang, $nomor_dosen_login]);
    if ($result_catatan && $row_catatan = sqlsrv_fetch_array($result_catatan, SQLSRV_FETCH_ASSOC)) {
        $catatan_revisi = $row_catatan['catatan_sidang'];
    }
    
    // Ambil nilai yang sudah ada untuk mahasiswa yang sedang aktif
    if (!empty($current_nim)) {
        $sql_get_nilai = "SELECT n_dokumen, n_presentasi, n_tanyajawab, n_proyek FROM Penilaian WHERE id_sidang = ? AND nomor_dosen = ? AND nim = ?";
        $result_get_nilai = sqlsrv_query($conn, $sql_get_nilai, [$id_sidang, $nomor_dosen_login, $current_nim]);
        if ($result_get_nilai && $row_nilai = sqlsrv_fetch_array($result_get_nilai, SQLSRV_FETCH_ASSOC)) {
            $nilai_mahasiswa = $row_nilai;
        }
    }
}

// Pengecekan HANYA berdasarkan nilai mahasiswa yang bersangkutan, bukan catatan kelompok.
$nilai_sudah_dikirim_dan_lengkap = false;
if (
    // Pastikan semua field nilai ada, tidak null, dan tidak kosong.
    // Nilai default untuk mahasiswa yang belum dinilai adalah string kosong (''), 
    // jadi pengecekan !== '' sangat penting.
    isset($nilai_mahasiswa['n_dokumen']) && $nilai_mahasiswa['n_dokumen'] !== null && $nilai_mahasiswa['n_dokumen'] !== '' &&
    isset($nilai_mahasiswa['n_presentasi']) && $nilai_mahasiswa['n_presentasi'] !== null && $nilai_mahasiswa['n_presentasi'] !== '' &&
    isset($nilai_mahasiswa['n_tanyajawab']) && $nilai_mahasiswa['n_tanyajawab'] !== null && $nilai_mahasiswa['n_tanyajawab'] !== '' &&
    isset($nilai_mahasiswa['n_proyek']) && $nilai_mahasiswa['n_proyek'] !== null && $nilai_mahasiswa['n_proyek'] !== ''
) {
    $nilai_sudah_dikirim_dan_lengkap = true;
}

$namaPembimbing_html = !empty($dosenPembimbing) ? implode('<br>', array_map('htmlspecialchars', $dosenPembimbing)) : 'Belum ditentukan';
$namaPenguji_html = !empty($dosenPenguji) ? implode('<br>', array_map('htmlspecialchars', $dosenPenguji)) : 'Belum ditentukan';

?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Sidang</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Link CSS Font Awesome (Sudah Benar) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Link Script Font Awesome (INI YANG PERLU DITAMBAHKAN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script> 
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../assets/css/dEvaluasiSidang.css">
</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="dEvaluasiSidang.php">
                        <span class="fw-semibold NavSide__sidebar-title">Evaluasi</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDokumenRevisi.php?id=<?= htmlspecialchars($id_sidang) ?>">
                        <span class="fw-semibold NavSide__sidebar-title">Dokumen</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dNilaiAkhir.php?id_sidang=<?= htmlspecialchars($id_sidang) ?>">
                        <span class="fw-semibold NavSide__sidebar-title">Nilai Akhir</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold"> Kembali</span></a>
                </li>
            </ul>
        </div>
        <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
        <div id="page-content-wrapper">
            <div class="NavSide__topbar"></div>
            <main class="NavSide__main-content">
                <h2 class="text-heading text-black" style="font-weight: 700;">Detail Evaluasi - <?= htmlspecialchars($judul) ?></h2>
                <h2 class="fs-5 fw-semibold mb-0" style="margin-left: 15px; margin-top: 20px;   color: #464869;">
              Catatan Perbaikan - Kelompok <?php echo htmlspecialchars($nomor_kelompok ?? ''); ?>
          </h2><br>
                <div class="container-fluid">
                    <!-- [BARU] TAB NAVIGASI MAHASISWA -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <ul class="nav nav-tabs">
                                <?php if (!empty($mahasiswa)): ?>
                                    <?php foreach ($mahasiswa as $mhs): ?>
                                        <li class="nav-item">
                                            <a class="nav-link <?= ($mhs['nim'] == $current_nim) ? 'active active-student-tab' : '' ?>"
                                                href="dEvaluasiSidang.php?nim=<?= htmlspecialchars($mhs['nim']) ?>">
                                                <?= htmlspecialchars($mhs['nama_mhs']) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="nav-item">
                                        <span class="nav-link disabled">Tidak ada mahasiswa dalam kelompok ini.</span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <br>

                    <form id="evaluasiForm" method="POST" action="dEvaluasiSidang.php">
                        <!-- Hidden input untuk mengirim NIM mahasiswa yang sedang dievaluasi -->
                        <input type="hidden" name="nim" value="<?= htmlspecialchars($current_nim) ?>">

                        <div class="info-card">
                            <div class="section">
                                <div class="info-group">
                                    <div class="label-row"> <i class="fa-solid fa-id-card"></i> <span class="fw-bold"> NIM</span></div>
                                    <div class="value-row"><?php echo htmlspecialchars($current_nim ?: '-'); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="label-row"> <i class="fa-solid fa-file-invoice"></i> <span class="fw-bold"> Judul Sidang</span></div>
                                    <div class="value-row"><?php echo htmlspecialchars($judul); ?></div>
                                </div>
                              <div class="info-group">
    <div class="label-row">
        <i class="fa-solid fa-user-tie"></i>
        <span class="fw-bold"><?php echo htmlspecialchars($labelPembimbing); ?></span>
    </div>
    <div class="value-row"><?php echo $namaPembimbing_html; ?></div>
</div>
                                <div class="info-group">
                                   <div class="label-row"><i class="fa-solid fa-id-card-clip"></i><span class="fw-bold">Dosen Penguji</span></div>
                                    <div class="value-row"><?php echo $namaPenguji_html; ?></div>
                                </div>
                            </div>
                            <div class="section">
                                 <div class="info-group">
                                    <div class="label-row"> <i class="fa-solid fa-user"></i>  <span class="fw-bold"> Nama Mahasiswa</span></div>
                                    <div class="value-row"><?php echo htmlspecialchars($current_nama_mhs); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="label-row"> <i class="fa-solid fa-door-open"></i>  <span class="fw-bold"> Ruangan</span></div>
                                    <div class="value-row"><?php echo htmlspecialchars($ruangan); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="label-row"> <i class="fa-solid fa-calendar-days"></i>  <span class="fw-bold"> Tanggal</span></div>
                                    <div class="value-row"><?php echo $tanggal_formatted; // tidak perlu htmlspecialchars karena sudah diformat aman ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="label-row"> <i class="fa-solid fa-clock"></i>  <span class="fw-bold"> Jam</span></div>
                                    <div class="value-row"><?php echo htmlspecialchars($jam); ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ==== PERUBAHAN DIMULAI DI SINI ==== -->
                        
                      <h3>Nilai Sidang (Sementara)</h3>
<div class="form-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Masukkan Nilai Sidang <span style="color: red;">*</span></h4>
    </div>
     <div class="penilaian-container">
        <div class="penilaian-item">
            <label for="nilaiLaporan">Nilai Laporan :</label>
            <!-- PERBAIKAN DI SINI: ganti name="nilaiLaporan" menjadi name="n_dokumen" -->
            <input type="text" class="form-control-custom text-center input-nilai" name="n_dokumen" maxlength="3" 
                   value="<?= htmlspecialchars($nilai_mahasiswa['n_dokumen'] ?? '') ?>" <?= $nilai_sudah_dikirim_dan_lengkap ? 'readonly' : '' ?>>
        </div>
        <div class="penilaian-item">
            <label for="materiPresentasi">Materi Presentasi :</label>
            <!-- PERBAIKAN DI SINI: ganti name="materiPresentasi" menjadi name="n_presentasi" -->
            <input type="text" class="form-control-custom text-center input-nilai" name="n_presentasi" maxlength="3" 
                   value="<?= htmlspecialchars($nilai_mahasiswa['n_presentasi'] ?? '') ?>" <?= $nilai_sudah_dikirim_dan_lengkap ? 'readonly' : '' ?>>
        </div>
        <div class="penilaian-item">
            <label for="nilaiPenyampaian">Penyampaian :</label>
            <!-- PERBAIKAN DI SINI: ganti name="nilaiPenyampaian" menjadi name="n_tanyajawab" -->
            <input type="text" class="form-control-custom text-center input-nilai" name="n_tanyajawab" maxlength="3" 
                   value="<?= htmlspecialchars($nilai_mahasiswa['n_tanyajawab'] ?? '') ?>" <?= $nilai_sudah_dikirim_dan_lengkap ? 'readonly' : '' ?>>
        </div>
        <div class="penilaian-item">
            <label for="nilaiProyek">Nilai Proyek :</label>
            <!-- PERBAIKAN DI SINI: ganti name="nilaiProyek" menjadi name="n_proyek" -->
            <input type="text" class="form-control-custom text-center input-nilai" name="n_proyek" maxlength="3" 
                   value="<?= htmlspecialchars($nilai_mahasiswa['n_proyek'] ?? '') ?>" <?= $nilai_sudah_dikirim_dan_lengkap ? 'readonly' : '' ?>>
        </div>
    </div>
      <!-- Form vertikal untuk mobile tetap sama -->
                            <p class="error-message" id="nilaiSidangErrorMessage"> *Semua nilai harus diisi!</p>
                        </div>
                        
                        <?php if (!empty($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($_SESSION['error']) ?>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <h3>Catatan Evaluasi Sidang</h3>
                        <div class="form-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4>Masukkan Catatan Evaluasi Sidang <span style="color: red;">*</span></h4>
                            </div>
                            <div class="form-group-custom">
                                <label for="catatanEvaluasi" class="visually-hidden">Catatan Evaluasi</label>
                                <textarea id="catatanEvaluasi" name="catatanEvaluasi" class="form-control-custom" placeholder="Silahkan masukkan Catatan Evaluasi Sidang disini.." <?= $nilai_sudah_dikirim_dan_lengkap ? 'readonly' : '' ?>><?php echo htmlspecialchars($catatan_revisi); ?></textarea>
                            </div>
                            <p class="error-message" id="catatanEvaluasiErrorMessage"> *Harus diisi!</p>
                        </div>

                        <?php if (!$nilai_sudah_dikirim_dan_lengkap): ?>
                        <div class="button-group-bottom">
                            <button style="margin-left:auto;" type="button" class="btn-kirim" id="btnKirim">Kirim</button>
                        </div>
                        <?php endif; ?>

                    </form>
                </div>
            </main>
        </div>
    </div>



    
    
    <!-- Modal konfirmasi -->
    <div class="modal fade" id="confirmationKirimModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmationKirimModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
                <div class="modal-header custom-modal-header border-0 justify-content-center">
                    <h4 class="modal-title fw-bold" id="confirmationKirimModalLabel" style="font-size: 24px;">Perhatian!</h4>
                </div>
                <div class="modal-body custom-modal-body">
                    <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah Anda yakin hendak mengirimkan evaluasi untuk mahasiswa <br><strong><?= htmlspecialchars($current_nama_mhs) ?></strong>?</p>
                    <div class="d-flex justify-content-between px-5"><button type="button" class="btn btn-tolak fw-semibold" data-bs-dismiss="modal">Batalkan</button><button type="button" class="btn btn-setujui fw-semibold" id="btnKonfirmasiKirim">Kirimkan</button></div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // SweetAlert untuk notifikasi sukses
        <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
        Swal.fire({
            title: 'Berhasil!',
            text: 'Data evaluasi berhasil disimpan.',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
        // Membersihkan URL dari parameter status
        setTimeout(() => {
            const url = new URL(window.location);
            url.searchParams.delete('status');
            window.history.replaceState({}, document.title, url);
        }, 2000);
        <?php endif; ?>

        // Script untuk form action di modal
        document.getElementById('btnKonfirmasiKirim').addEventListener('click', function() {
            document.getElementById('evaluasiForm').submit();
        });

        // Script untuk menampilkan modal
        document.getElementById('btnKirim').addEventListener('click', function() {
            // Lakukan validasi dulu jika perlu
            // ...
            var myModal = new bootstrap.Modal(document.getElementById('confirmationKirimModal'));
            myModal.show();
        });
    </script>
    <script src="../../assets/js/dEvaluasiSidang.js"></script> 
</body>
</html>