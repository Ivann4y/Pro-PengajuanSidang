



<?php
session_start();
require "../../koneksi/koneksiAndrew.php"; // Pastikan path ini benar

// ===================================================================================
// BAGIAN 1: PENGAMBILAN ID SIDANG (VERSI BARU YANG LEBIH BAIK)
// ===================================================================================
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $_SESSION['id_sidang_aktif'] = (int)$_GET['id'];
    header("Location: dEvaluasiSidang.php");
}





// Simulasi Dosen yang Login (nantinya ganti dengan session asli)


if (!isset($_SESSION['id_sidang_aktif'])) {
    die("ID sidang tidak tersedia.");
// Ambil ID Sidang langsung dari GET. Jauh lebih sederhana dan tidak merusak parameter lain.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Jika tidak ada ID, kita tidak bisa melanjutkan.
    // Anda bisa redirect ke halaman daftar sidang atau menampilkan pesan error.
    die("ID sidang tidak valid atau tidak tersedia.");
}
$id_sidang = (int)$_GET['id'];

// ===================================================================================
// SIMULASI DOSEN LOGIN (GANTI DENGAN SESSION ASLI NANTI)
// ===================================================================================

if (!isset($_SESSION['user_data']['nomor_dosen'])) { die("Akses ditolak."); }
$nomor_dosen_login = $_SESSION['user_data']['nomor_dosen'];

// ===================================================================================
// BAGIAN 2: PROSES PENYIMPANAN DATA (SAAT FORM DI-SUBMIT)
// ===================================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Ambil data dari form
    $nim_mahasiswa = $_POST['nim_mahasiswa'] ?? null;
    $catatan_post = $_POST['catatanEvaluasi'] ?? '';
    $nilaiLaporan = !empty($_POST['nilaiLaporan']) ? (int)$_POST['nilaiLaporan'] : null;
    $nilaiPresentasi = !empty($_POST['materiPresentasi']) ? (int)$_POST['materiPresentasi'] : null;
    $nilaiPenyampaian = !empty($_POST['nilaiPenyampaian']) ? (int)$_POST['nilaiPenyampaian'] : null;
    $nilaiProyek = !empty($_POST['nilaiProyek']) ? (int)$_POST['nilaiProyek'] : null;

    // Validasi dasar
    if (!$nim_mahasiswa) {
        $_SESSION['error'] = "Terjadi kesalahan: NIM Mahasiswa tidak ditemukan.";
        // Redirect kembali ke halaman dengan ID sidang
        header("Location: dEvaluasiSidang.php?id=$id_sidang");
        exit; // PENTING: Selalu exit setelah header redirect
    }

    // 1. UPDATE CATATAN REVISI DI TABEL Detail_Sidang
    $sql_update_catatan = "UPDATE Detail_Sidang SET catatan_sidang = ? WHERE id_sidang = ? AND nomor_dosen = ?";
    $params_update_catatan = [$catatan_post, $id_sidang, $nomor_dosen_login];
    $stmt_update_catatan = sqlsrv_query($conn, $sql_update_catatan, $params_update_catatan);
    $error_message = '';
    if ($stmt_update_catatan === false) {
        $_SESSION['error'] = "Gagal memperbarui catatan revisi: " . print_r(sqlsrv_errors(), true);
        header("Location: dDaftarSidang.php?id=$id_sidang");
        exit;
    }



    // 2. CEK & SIMPAN NILAI (UPSERT) PER MAHASISWA
    $sql_cek_nilai = "SELECT COUNT(*) as 'count' FROM Penilaian WHERE id_sidang = ? AND nim = ? AND nomor_dosen = ?";
    $stmt_cek_nilai = sqlsrv_query($conn, $sql_cek_nilai, [$id_sidang, $nim_mahasiswa, $nomor_dosen_login]);
    $nilai_exists = sqlsrv_fetch_array($stmt_cek_nilai, SQLSRV_FETCH_ASSOC)['count'] > 0;

    if ($nilai_exists) {
        $sql_nilai = "UPDATE Penilaian SET n_dokumen = ?, n_presentasi = ?, n_tanyajawab = ?, n_proyek = ? WHERE id_sidang = ? AND nim = ? AND nomor_dosen = ?";
    } else {
        $sql_nilai = "INSERT INTO Penilaian (id_sidang, nim, nomor_dosen, n_dokumen, n_presentasi, n_tanyajawab, n_proyek) VALUES (?, ?, ?, ?, ?, ?, ?)";
    }
    $params_nilai = [$nilaiLaporan, $nilaiPresentasi, $nilaiPenyampaian, $nilaiProyek, $id_sidang, $nim_mahasiswa, $nomor_dosen_login];
    
    $stmt_nilai = sqlsrv_query($conn, $sql_nilai, $params_nilai);
    if ($stmt_nilai === false) {
        $_SESSION['error'] = "Gagal menyimpan nilai mahasiswa: " . print_r(sqlsrv_errors(), true);
    } else {
        $_SESSION['success'] = "Evaluasi untuk NIM $nim_mahasiswa berhasil disimpan.";
    }

    // FIX UTAMA: Redirect kembali ke halaman dengan ID dan NIM yang sama agar tab yang benar tetap aktif
    header("Location: dEvaluasiSidang.php?id=$id_sidang&nim=$nim_mahasiswa");
    exit(); // PENTING
}

// ===================================================================================
// BAGIAN 3: PENGAMBILAN DATA UNTUK DITAMPILKAN DI HALAMAN
// ===================================================================================

// Variabel default
$mahasiswa = [];
$current_nim = '';
$current_nama_mhs = 'Mahasiswa tidak ditemukan';
$id_kelompok = null;
$judul = 'Data tidak ditemukan';
$ruangan = '-';
$tanggal_formatted = '-';
$jam = '-';
$dosenPembimbing = [];
$dosenPenguji = [];
$catatan_revisi = '';
$nilai_mahasiswa = ['n_dokumen' => '', 'n_presentasi' => '', 'n_tanyajawab' => '', 'n_proyek' => ''];

// 1. Ambil detail dasar sidang (id_kelompok & judul)
$sql_sidang = "SELECT id_kelompok, judul FROM Sidang WHERE id_sidang = ?";
$stmt_sidang = sqlsrv_query($conn, $sql_sidang, [$id_sidang]);
if ($stmt_sidang && $data_sidang = sqlsrv_fetch_array($stmt_sidang, SQLSRV_FETCH_ASSOC)) {
    $id_kelompok = $data_sidang['id_kelompok'];
    $judul = $data_sidang['judul'];
} else {
    die("Detail sidang dengan ID $id_sidang tidak ditemukan.");
}

// 2. Ambil daftar mahasiswa dalam kelompok
if ($id_kelompok) {
    $sql_mhs = "SELECT km.nim, m.nama_mhs FROM Kelompok_Mahasiswa km JOIN Mahasiswa m ON km.nim = m.nim WHERE km.id_kelompok = ? ORDER BY km.nim";
    $stmt_mhs = sqlsrv_query($conn, $sql_mhs, [$id_kelompok]);
    if ($stmt_mhs) {
        while ($row = sqlsrv_fetch_array($stmt_mhs, SQLSRV_FETCH_ASSOC)) {
            $mahasiswa[] = $row;
        }
    }
}

// 3. Tentukan mahasiswa yang sedang aktif
if (!empty($mahasiswa)) {
    if (isset($_GET['nim']) && in_array($_GET['nim'], array_column($mahasiswa, 'nim'))) {
        $current_nim = $_GET['nim'];
    } else {
        $current_nim = $mahasiswa[0]['nim']; // Default ke mahasiswa pertama
    }
    // Cari nama mahasiswa yang aktif
    foreach ($mahasiswa as $mhs) {
        if ($mhs['nim'] == $current_nim) {
            $current_nama_mhs = $mhs['nama_mhs'];
            break;
        }
    }
}

// 4. Ambil data dosen, jadwal, catatan, dan nilai
$sql_dosen_terjadwal = "SELECT d.nama_dosen, p.peran_dosen FROM Penjadwalan p JOIN Dosen d ON p.nomor_dosen = d.nomor_dosen WHERE p.id_sidang = ?";
$stmt_dosen_terjadwal = sqlsrv_query($conn, $sql_dosen_terjadwal, [$id_sidang]);
if ($stmt_dosen_terjadwal) {
    while ($row = sqlsrv_fetch_array($stmt_dosen_terjadwal, SQLSRV_FETCH_ASSOC)) {
        // Asumsi peran_dosen adalah bit/boolean (1 untuk Pembimbing, 0 untuk Penguji)
        if ($row['peran_dosen']) { 
            $dosenPembimbing[] = $row['nama_dosen'];
        } else {
            $dosenPenguji[] = $row['nama_dosen'];
        }
    }
}
$namaPembimbing_html = !empty($dosenPembimbing) ? implode('<br>', array_map('htmlspecialchars', $dosenPembimbing)) : 'Belum ditentukan';
$namaPenguji_html = !empty($dosenPenguji) ? implode('<br>', array_map('htmlspecialchars', $dosenPenguji)) : 'Belum ditentukan';

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

// Ambil catatan (berlaku per kelompok)
$sql_catatan = "SELECT catatan_sidang FROM Detail_Sidang WHERE id_sidang = ? AND nomor_dosen = ?";
$result_catatan = sqlsrv_query($conn, $sql_catatan, [$id_sidang, $nomor_dosen_login]);
if ($result_catatan && $row_catatan = sqlsrv_fetch_array($result_catatan, SQLSRV_FETCH_ASSOC)) {
    $catatan_revisi = $row_catatan['catatan_sidang'];
}

// Ambil nilai untuk mahasiswa yang aktif
if (!empty($current_nim)) {
    $sql_get_nilai = "SELECT n_dokumen, n_presentasi, n_tanyajawab, n_proyek FROM Penilaian WHERE id_sidang = ? AND nim = ? AND nomor_dosen = ?";
    $result_get_nilai = sqlsrv_query($conn, $sql_get_nilai, [$id_sidang, $current_nim, $nomor_dosen_login]);
    if ($result_get_nilai && $row_nilai = sqlsrv_fetch_array($result_get_nilai, SQLSRV_FETCH_ASSOC)) {
        $nilai_mahasiswa = $row_nilai;
    }
}

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../assets/css/dEvaluasiSidang.css">


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
                    <a href="dEvaluasiSidang.php?">
                        <span class="fw-semibold NavSide__sidebar-title">Evaluasi</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDokumenRevisi.php">
                        <span class="fw-semibold NavSide__sidebar-title">Dokumen</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dNilaiAkhir.php">
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
                <h2>Detail Evaluasi - Sistem Evaluasi Sidang</h2>
                </br>

                <div class="container-fluid">
                   <div class="row mb-3">
                       <div class="col-12">
                         <ul class="nav nav-tabs">
                            <?php if (empty($mahasiswa)): ?>
                                <li class="nav-item">
                                    <span class="nav-link disabled">Tidak ada mahasiswa dalam kelompok ini</span>
                                </li>
                            <?php else: ?>
                                <?php foreach ($mahasiswa as $index => $mhs): ?>
                                    <li class="nav-item">
                                        <!-- Link tab sekarang menyertakan parameter nim -->
                                        <a class="nav-link <?php echo ($mhs['nim'] == $current_nim) ? 'active active-student-tab' : ''; ?>"
                                           href="dEvaluasiSidang.php?id=<?php echo htmlspecialchars($id_sidang); ?>&nim=<?php echo htmlspecialchars($mhs['nim']); ?>">
                                           Mahasiswa <?php echo $index + 1; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                         </ul>
                       </div>
                   </div>
                   <br>

                <!-- Pastikan form memiliki id untuk di-submit via JavaScript jika perlu -->
                <form id="evaluasiForm" method="POST" action="dEvaluasiSidang.php?id=<?php echo htmlspecialchars($id_sidang); ?>">
                    <!-- INPUT HIDDEN UNTUK MENGIRIM NIM MAHASISWA YANG AKTIF -->
                    <input type="hidden" name="nim_mahasiswa" value="<?= htmlspecialchars($current_nim) ?>">

                    <div class="info-card">
                        <div class="section">
                                  <div class="info-group">
                                    <div class="label-row"><i class="fa-solid fa-id-card"></i><span class="fw-bold">NIM</span></div>
                                    <div class="value-row"> <?php echo htmlspecialchars($current_nim ?? 'N/A'); ?></div>
                                  </div>
                                  <div class="info-group">
                                    <div class="label-row"><i class="fa-solid fa-file-invoice"></i><span class="fw-bold">Judul Sidang</span></div>
                                    <div class="value-row"><?php echo htmlspecialchars($judul); ?></div>
                                  </div>
                                  <div class="info-group">
                                    <div class="label-row"><i class="fa-solid fa-user-tie"></i><span class="fw-bold">Dosen Pembimbing</span></div>
                                    <div class="value-row"><?php echo $namaPembimbing_html; ?></div>
                                  </div>
                                  <div class="info-group">
                                    <div class="label-row"><i class="fa-solid fa-user-group"></i><span class="fw-bold">Dosen Penguji</span></div>
                                    <div class="value-row"><?php echo $namaPenguji_html; ?></div>
                                  </div>
                        </div>
                        <div class="section">
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-user"></i><span class="fw-bold">Nama</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($current_nama_mhs); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-door-open"></i><span class="fw-bold">Ruangan</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($ruangan); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-calendar-days"></i><span class="fw-bold">Tanggal</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($tanggal_formatted); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-clock"></i><span class="fw-bold">Jam</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($jam); ?></div>
                            </div>
                        </div>
                    </div>

                    <h3>Nilai Sidang (Sementara)</h3>
                    <div class="form-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4>Masukkan Nilai Sidang <span style="color: red;">*</span></h4>
                        </div>

                        <!-- Wadah untuk tampilan desktop (horizontal) -->
                        <div class="penilaian-container">
                            <div class="penilaian-item">
                                <label for="nilaiLaporan">Nilai Laporan :</label>
                                <input type="text" class="form-control-custom text-center input-nilai" name="nilaiLaporan" maxlength="3" value="<?= htmlspecialchars($nilai_mahasiswa['n_dokumen'] ?? '') ?>">
                            </div>
                            <div class="penilaian-item">
                                <label for="materiPresentasi">Materi Presentasi :</label>
                                <input type="text" class="form-control-custom text-center input-nilai" name="materiPresentasi" maxlength="3" value="<?= htmlspecialchars($nilai_mahasiswa['n_presentasi'] ?? '') ?>">
                            </div>
                            <div class="penilaian-item">
                                <label for="nilaiPenyampaian">Penyampaian :</label>
                                <input type="text" class="form-control-custom text-center input-nilai" name="nilaiPenyampaian" maxlength="3" value="<?= htmlspecialchars($nilai_mahasiswa['n_tanyajawab'] ?? '') ?>">
                            </div>
                            <div class="penilaian-item">
                                <label for="nilaiProyek">Nilai Proyek :</label>
                                <input type="text" class="form-control-custom text-center input-nilai" name="nilaiProyek" maxlength="3" value="<?= htmlspecialchars($nilai_mahasiswa['n_proyek'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Wadah BARU untuk tampilan tablet/mobile (vertikal) -->
                        <div class="penilaian-grid-vertical">
                            <label for="nilaiLaporan_v">Nilai Laporan</label> <span>:</span>
                            <input type="text" class="form-control-custom text-center input-nilai" name="nilaiLaporan_v" maxlength="3" value="<?= htmlspecialchars($nilai_mahasiswa['n_dokumen'] ?? '') ?>">

                            <label for="materiPresentasi_v">Materi Presentasi</label> <span>:</span>
                            <input type="text" class="form-control-custom text-center input-nilai" name="materiPresentasi_v" maxlength="3" value="<?= htmlspecialchars($nilai_mahasiswa['n_presentasi'] ?? '') ?>">

                            <label for="nilaiPenyampaian_v">Penyampaian</label> <span>:</span>
                            <input type="text" class="form-control-custom text-center input-nilai" name="nilaiPenyampaian_v" maxlength="3" value="<?= htmlspecialchars($nilai_mahasiswa['n_tanyajawab'] ?? '') ?>">

                            <label for="nilaiProyek_v">Nilai Proyek</label> <span>:</span>
                            <input type="text" class="form-control-custom text-center input-nilai" name="nilaiProyek_v" maxlength="3" value="<?= htmlspecialchars($nilai_mahasiswa['n_proyek'] ?? '') ?>">
                        </div>

                        <p class="error-message" id="nilaiSidangErrorMessage"> *Semua nilai harus diisi!</p>
                    </div>






                    <?php if (!empty($_SESSION['error'])): ?>
                        <div style="color: red; font-weight: bold;">
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

                            <!-- TAMBAHKAN LOGIKA 'readonly' DI SINI -->
                            <textarea id="catatanEvaluasi" name="catatanEvaluasi" class="form-control-custom" placeholder="Silahkan masukkan Catatan Evaluasi Sidang disini.." <?= $nilai_sudah_dikirim_dan_lengkap ? 'readonly' : '' ?>><?php echo htmlspecialchars($catatan_revisi); ?></textarea>
                        </div>
                        <p class="error-message" id="catatanEvaluasiErrorMessage"> *Harus diisi!</p>
                    </div>

                    <?php
                    // GUNAKAN BLOK 'if' UNTUK MENAMPILKAN TOMBOL SECARA KONDISIONAL
                    if (!$nilai_sudah_dikirim_dan_lengkap): ?>
                        <div class="button-group-bottom">
                            <button style="margin-left:auto;" type="button" class="btn-kirim" id="btnKirim">Kirim</button>
                        </div>
                    <?php endif; ?>

                </form>
            </main>
        </div>
    </div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/dEvaluasiSidang.js"></script>


</body>
</html>