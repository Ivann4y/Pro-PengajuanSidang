<?php
session_start();
require "../../koneksi/koneksiAndrew.php"; // Pastikan path ini benar
require_once __DIR__ . '/../../control/kirimNotifikasi.php';


// Tetapkan variabel $id_sidang dari session agar bisa digunakan di seluruh skrip
$id_sidang = $_SESSION['id_sidang_aktif'];

// Ambil ID sidang dari GET (sekali) lalu simpan ke session
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $_SESSION['id_sidang_aktif'] = (int)$_GET['id'];
    // Hapus nim lama jika id sidang baru dipilih
    unset($_SESSION['nim_aktif']);
    header("Location: dDokumenRevisi.php");
    exit;
}

// Ambil NIM dari GET (sekali) lalu simpan ke session, lalu redirect untuk membersihkan URL
if (isset($_GET['nim']) && (!isset($_SESSION['nim_aktif']) || $_SESSION['nim_aktif'] !== $_GET['nim'])) {
    $_SESSION['nim_aktif'] = $_GET['nim'];
    header("Location: dDokumenRevisi.php");
    exit;
}
//=================================================================================
// FIX: AMBIL ID SIDANG DARI SESSION SETELAH REDIRECT
// ===================================================================================
// Pastikan ID sidang ada di session sebelum melanjutkan
if (!isset($_SESSION['id_sidang_aktif'])) {
    // Jika tidak ada, hentikan eksekusi atau redirect ke halaman daftar
    die("Sesi sidang tidak ditemukan. Silakan kembali ke daftar sidang dan pilih kembali.");
}
// ===================================================================================
if (!isset($_SESSION['user_data']['nomor_dosen'])) {
    die("Akses ditolak.");
}
$nomor_dosen_login = $_SESSION['user_data']['nomor_dosen'];
// ===================================================================================
// BAGIAN 2: PROSES PENYIMPANAN DATA (SAAT FORM DI-SUBMIT)
// ===================================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json'); // karena dipanggil dari fetch

    $nim_post = $_POST['nim'] ?? '';
    $approve_action = isset($_POST['approve']) ? true : false;
    $reject_action = isset($_POST['reject']) ? true : false;
    $catatan_sidang = $_POST['catatan_sidang'] ?? null;


    // Ambil dokumen revisi
    $sql_revisi = "SELECT dok_revisi, nama_file 
               FROM Detail_Sidang 
               WHERE id_sidang = ? AND nomor_dosen = ?";
    $params_revisi = [$id_sidang, $nomor_dosen_login];

    $stmt_revisi = sqlsrv_query($conn, $sql_revisi, $params_revisi);
    $data_revisi = sqlsrv_fetch_array($stmt_revisi, SQLSRV_FETCH_ASSOC);
    $dokumen_revisi = $data_revisi['dok_revisi'] ?? null;
    $nama_file = $data_revisi['nama_file'] ?? basename($dokumen_revisi);


    if ($reject_action && $catatan_sidang) {
        $sql_update_reject = "UPDATE Detail_Sidang 
    SET status_revisi = 'Ditolak', catatan_sidang = ? 
    WHERE id_sidang = ? AND nomor_dosen = ? AND EXISTS (
        SELECT 1 FROM Kelompok k WHERE k.nim = ? AND k.id_kelompok = (
            SELECT id_kelompok FROM Sidang WHERE id_sidang = ?
        )
    )";
        $params_reject = [$catatan_sidang, $id_sidang, $nomor_dosen_login, $nim_post, $id_sidang];

        $stmt_reject = sqlsrv_query($conn, $sql_update_reject, $params_reject);

        if ($stmt_reject) {
            // Notifikasi
            $nama_dosen = $_SESSION['user_data']['nama_dosen'] ?? 'Dosen';
            $judul_sidang = $judul ?? '';
            $pesan = "Dokumen revisi untuk judul '$judul_sidang' ditolak oleh $nama_dosen. Silakan perbaiki dan upload ulang.";
            kirimNotifikasi($nim_post, $pesan, $conn, $nomor_dosen_login);

            echo json_encode([
                'status' => 'success',
                'message' => 'Dokumen revisi ditolak.',
                'redirectUrl' => 'dDaftarSidang.php'
            ]);
            exit;
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menyimpan alasan penolakan.'
            ]);
        }
        exit;
    }


    if ($approve_action && $dokumen_revisi) {
        // Pastikan entri untuk dosen ini ada
        $check_sql = "SELECT id_sidang FROM Detail_Sidang WHERE id_sidang = ? AND nomor_dosen = ?";
        $params_check = [$id_sidang, $nomor_dosen_login];
        $stmt_check = sqlsrv_query($conn, $check_sql, $params_check);

        if ($stmt_check && sqlsrv_has_rows($stmt_check)) {
            // Update status_revisi dosen ini
            $sql_update = "UPDATE Detail_Sidang SET status_revisi = 'Disetujui' WHERE id_sidang = ? AND nomor_dosen = ?";
            $params_update = [$id_sidang, $nomor_dosen_login];
            $stmt_update = sqlsrv_query($conn, $sql_update, $params_update);

            // Cek apakah SEMUA dosen sudah menyetujui
            $sql_cek_all = "SELECT COUNT(*) AS total, SUM(CASE WHEN status_revisi = 'Disetujui' THEN 1 ELSE 0 END) AS disetujui 
                            FROM Detail_Sidang WHERE id_sidang = ?";
            $stmt_cek_all = sqlsrv_query($conn, $sql_cek_all, [$id_sidang]);
            $result_all = sqlsrv_fetch_array($stmt_cek_all, SQLSRV_FETCH_ASSOC);

            if ($result_all && $result_all['total'] == $result_all['disetujui']) {
                $sql_set_sidang = "UPDATE Sidang SET status_revisi = 1 WHERE id_sidang = ?";
                sqlsrv_query($conn, $sql_set_sidang, [$id_sidang]);
            }

            // Kirim notifikasi ke mahasiswa bahwa dokumen revisi disetujui
            // Ambil nama dosen dari session
            $nama_dosen = isset($_SESSION['user_data']['nama_dosen']) ? $_SESSION['user_data']['nama_dosen'] : 'Dosen';
            // Ambil judul sidang (jika tersedia)
            $judul_sidang = isset($judul) ? $judul : '';
            $pesan = "Dokumen revisi untuk judul '$judul_sidang' telah disetujui oleh $nama_dosen. Silakan cek status revisi Anda.";
            kirimNotifikasi($nim_post, $pesan, $nomor_dosen_login, $conn);

            echo json_encode([
                'status' => 'success',
                'message' => 'Dokumen revisi disetujui.',
                'redirectUrl' => 'dNilaiAkhir.php?id_sidang=' . $id_sidang
            ]);
            exit;
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Data sidang untuk dosen ini tidak ditemukan.'
            ]);
            exit;
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Tidak dapat menyetujui. Dokumen revisi belum ada atau data tidak lengkap.'
        ]);
        exit;
    }
}


// ===================================================================================
// BAGIAN 3: PENGAMBILAN DATA UNTUK DITAMPILKAN DI HALAMAN
// ===================================================================================

// Variabel default
$id_kelompok = null;
$nim = 'Data tidak ditemukan';
$ruangan = '-';
$tanggal_formatted = '-';
$jam = '-';
$dosenPembimbing = [];
$dosenPenguji = [];
$mahasiswa = [];
$current_nim = '';

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

    // ==========================================================
    // ===== [FIX] AMBIL NAMA MATA KULIAH DENGAN BENAR =====
    // ==========================================================
    $nama_matkul_sidang = 'Tidak ada mata kuliah'; // Nilai default
    $sql_matkul = "SELECT TOP 1 mk.nama_matkul 
                   FROM Detail_Sidang ds
                   JOIN MataKuliah mk ON ds.id_matkul = mk.id_matkul
                   WHERE ds.id_sidang = ?";
    $stmt_matkul = sqlsrv_query($conn, $sql_matkul, [$id_sidang]);
    if ($stmt_matkul && $data_matkul = sqlsrv_fetch_array($stmt_matkul, SQLSRV_FETCH_ASSOC)) {
        $nama_matkul_sidang = $data_matkul['nama_matkul'];
    }
    // ==========================================================
    // ===== AKHIR DARI [FIX] =====
    // ==========================================================
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

    // Ambil dokumen revisi milik current_nim
    $sql_revisi = "SELECT dok_revisi, nama_file FROM Detail_Sidang WHERE id_sidang = ? AND nomor_dosen = ?";
    $stmt_revisi = sqlsrv_query($conn, $sql_revisi, [$id_sidang, $nomor_dosen_login]);
    $data_revisi = sqlsrv_fetch_array($stmt_revisi, SQLSRV_FETCH_ASSOC);
    $dokumen_revisi = $data_revisi['dok_revisi'] ?? '';
    $nama_file_revisi = $data_revisi['nama_file'] ?? '';




    // Mendapatkan nama mahasiswa yang sedang aktif untuk ditampilkan
    foreach ($mahasiswa as $mhs) {
        if ($mhs['nim'] == $current_nim) {
            $current_nama_mhs = $mhs['nama_mhs'];
            break;
        }
    }

    // Inisialisasi variabel
    $dosenPembimbing = []; // Akan berisi nama Pembimbing atau Pengampu
    $dosenPenguji = [];
    $labelPembimbing = "Dosen"; // Label default

    if (isset($jenis_sidang)) {
        // --- Logika untuk Pembimbing TA atau Pengampu Semester ---
        if ($jenis_sidang == 'Tugas Akhir') {
            $labelPembimbing = "Dosen Pembimbing";
            // LOGIKA IDENTIK: Ambil nama dosen dari tabel Bimbingan berdasarkan id_kelompok.
            $sql_dosen = "SELECT d.nama_dosen FROM Dosen d JOIN Bimbingan b ON d.nomor_dosen = b.nomor_dosen WHERE b.id_kelompok = ?";
            $params_dosen = [$id_kelompok];

            $stmt_dosen = sqlsrv_query($conn, $sql_dosen, $params_dosen);
            if ($stmt_dosen) {
                while ($row = sqlsrv_fetch_array($stmt_dosen, SQLSRV_FETCH_ASSOC)) {
                    $dosenPembimbing[] = $row['nama_dosen'];
                }
            }
        } elseif ($jenis_sidang == 'Semester' && isset($id_matkul)) {
            $labelPembimbing = "Dosen Pengampu";
            // LOGIKA IDENTIK: Ambil nama dosen dari tabel Pengampu_Kelas berdasarkan id_matkul.
            $sql_dosen = "SELECT d.nama_dosen FROM Dosen d JOIN Pengampu_Kelas pk ON d.nomor_dosen = pk.nomor_dosen WHERE pk.id_matkul = ?";
            $params_dosen = [$id_matkul];

            $stmt_dosen = sqlsrv_query($conn, $sql_dosen, $params_dosen);
            if ($stmt_dosen) {
                while ($row = sqlsrv_fetch_array($stmt_dosen, SQLSRV_FETCH_ASSOC)) {
                    // Dosen pengampu dianggap sebagai 'pembimbing' dan juga 'penguji' di halaman ini
                    $dosenPembimbing[] = $row['nama_dosen'];
                }
            }
        }
    }

    // --- Ambil Dosen Penguji tambahan dari tabel Penjadwalan (logika ini tetap) ---
    $sql_penguji_jadwal = "SELECT d.nama_dosen FROM Dosen d JOIN Penjadwalan p ON d.nomor_dosen = p.nomor_dosen WHERE p.id_sidang = ? AND p.peran_dosen = 0"; // peran 0 = penguji
    $stmt_penguji_jadwal = sqlsrv_query($conn, $sql_penguji_jadwal, [$id_sidang]);
    if ($stmt_penguji_jadwal) {
        while ($row = sqlsrv_fetch_array($stmt_penguji_jadwal, SQLSRV_FETCH_ASSOC)) {
            $dosenPenguji[] = $row['nama_dosen'];
        }
    }



    // --- Hilangkan duplikat jika ada nama yang sama ---
    $dosenPembimbing = array_unique($dosenPembimbing);
    $dosenPenguji = array_unique($dosenPenguji);
    // 

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
}

$namaPembimbing_html = !empty($dosenPembimbing) ? implode('<br>', array_map('htmlspecialchars', $dosenPembimbing)) : 'Belum ditentukan';
$namaPenguji_html = !empty($dosenPenguji) ? implode('<br>', array_map('htmlspecialchars', $dosenPenguji)) : 'Belum ditentukan';

// Tangani permintaan download file revisi
if (isset($_GET['download']) && $_GET['download'] === 'revisi') {
    $id_sidang = (int)($_GET['id'] ?? 0);
    $nim_download = $_GET['nim'] ?? '';

    if ($id_sidang && $nim_download) {
        $sql_revisi = "
            SELECT ds.dok_revisi, ds.nama_file
            FROM Detail_Sidang ds
            JOIN Sidang s ON ds.id_sidang = s.id_sidang
            WHERE ds.id_sidang = ? AND EXISTS (
                SELECT 1 FROM Kelompok k2 WHERE k2.id_kelompok = s.id_kelompok AND k2.nim = ?
            )
        ";

        $params = [$id_sidang, $nim_download];
        $stmt_revisi = sqlsrv_query($conn, $sql_revisi, $params);

        if ($stmt_revisi && ($data_revisi = sqlsrv_fetch_array($stmt_revisi, SQLSRV_FETCH_ASSOC))) {
            $path_revisi = $data_revisi['dok_revisi'];
            $nama_file = $data_revisi['nama_file'] ?? basename($path_revisi);
            $full_path = __DIR__ . '/../../' . $path_revisi;

            if (file_exists($full_path)) {
                // Jangan ada output sebelum header!
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $nama_file . '"');
                header('Content-Length: ' . filesize($full_path));
                flush();
                readfile($full_path);
                exit;
            } else {
                die("File revisi tidak ditemukan di server.");
            }
        } else {
            die("Dokumen revisi tidak ditemukan di database.");
        }
    } else {
        die("Parameter download tidak lengkap.");
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Revisi</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../css/button-styles.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <link rel="stylesheet" href="../../assets/css/dDokumenRevisi.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 for pop-up notifications -->
        <link rel="stylesheet" href="../../assets/css/breadcrumb.css">

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
                    <a href="dEvaluasiSidang.php?id=<?= htmlspecialchars($id_sidang) ?>">
                        <span class="fw-semibold NavSide__sidebar-title">Evaluasi</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="dDokumenRevisi.php">
                        <span class="fw-semibold">Dokumen</span>
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
                    <a href="dDaftarSidang.php">
                        <span class="NavSide__sidebar-title fw-semibold">Kembali</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="NavSide__toggle">
            <i class="bi bi-list open"></i>
            <i class="bi bi-x-lg close"></i>
        </div>

        <div id="page-content-wrapper">
            <div class="NavSide__topbar"></div>
            <main class="NavSide__main-content" id="dDokumenRevisi">
            <?php 
            require_once '../../control/function.php'; 
            echo generateBreadcrumb(getPageTitle('dDokumenRevisi'), 'dosen', [
                ['url' => 'dDaftarSidang.php', 'text' => 'Daftar Sidang']
            ]); 
            ?>
                <h2 class="text-heading text-black" style="font-weight: 700;">Dokumen Revisi - <?= htmlspecialchars($judul) ?></h2>
                <form id="dokumenRevisiForm" method="POST" action="dDokumenRevisi.php">
                    <input type="hidden" name="nim" value="<?= htmlspecialchars($current_nim) ?>">
                    <div class="info-card">
                        <div class="section">
                            <div class="info-group">
                                <div class="label-row"> <i class="fa-solid fa-id-card"></i> <span class="fw-bold"> Kelompok</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($nomor_kelompok ?: '-'); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-users"></i><span class="fw-bold">Anggota Kelompok</span></div>
                                <div class="value-row">
                                    <?php if (!empty($mahasiswa)): ?>
                                        <?php foreach ($mahasiswa as $mhs): ?>
                                            <?= htmlspecialchars($mhs['nama_mhs']) ?><br>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <em>Belum ada anggota</em>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-file-invoice"></i><span class="fw-bold">Judul Sidang</span></div>
                                <div class="value-row"><?php echo !empty($judul) ? htmlspecialchars($judul) : 'Belum ada judul'; ?></div>
                            </div>

                            <div class="info-group">
                                <div class="label-row">
                                    <i class="fa-solid fa-user-tie"></i>
                                    <span class="fw-bold"><?php echo htmlspecialchars($labelPembimbing); ?></span>
                                </div>
                                <div class="value-row"><?php echo $namaPembimbing_html; ?></div>
                            </div>

                            <!-- Bagian Dosen Penguji (HANYA MUNCUL JIKA BUKAN SIDANG SEMESTER) -->
                            <?php if ($jenis_sidang != 'Semester'): ?>
                                <div class="info-group">
                                    <div class="label-row"><i class="fa-solid fa-id-card-clip"></i><span class="fw-bold">Dosen Penguji</span></div>
                                    <div class="value-row"><?php echo $namaPenguji_html; ?></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="section">
                            <div class="info-group">
                                <div class="label-row"> <i class="fa-solid fa-user"></i><span class="fw-bold"> Mata Kuliah </span></div>
                                <div class="value-row"><?php echo htmlspecialchars($nama_matkul_sidang) ?></div>

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

                    <h3>Dokumen Revisi</h3>
                    <div class="file-buttons-container d-flex flex-wrap">
                        <?php if (!empty($dokumen_revisi)): ?>
                            <a href="/SIDANG/Pro-PengajuanSidang/<?= htmlspecialchars($dokumen_revisi) ?>"
                                class="file-button"
                                id="linkDokumenRevisi"
                                download="<?= htmlspecialchars($nama_file_revisi) ?>">
                                <i class="fa-solid fa-file-zipper"></i>
                                <?= htmlspecialchars($nama_file_revisi) ?>
                            </a>



                        <?php else: ?>
                            <p class="text-muted">Belum ada dokumen revisi yang diunggah oleh mahasiswa.</p>
                        <?php endif; ?>
                    </div>



                    <div class="button-group-bottom" id="grup-aksi-dokumen">
                        <div class="button-group">
                            <button type="button" class="btn btn-tolak" onclick="handleAction('Ditolak')">Tolak</button>
                            <button type="button" class="btn btn-setujui" onclick="handleAction('Disetujui')">Setujui</button>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>
    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="confirmationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal-content">
                <div class="modal-header custom-modal-header">
                    <h4 class="modal-title fw-bold" id="confirmationModalLabel" style="font-size: 24px;">Perhatian!</h4>
                </div>
                <div class="modal-body custom-modal-body">
                    <p class="mb-5 fw-semibold" id="confirmationModalText" style="font-size: 16px;"></p>
                    <div class="d-flex justify-content-between px-4">
                        <button type="button" class="btn btn-tolak fw-semibold" data-bs-dismiss="modal">Batalkan</button>
                        <button type="button" class="btn btn-setujui fw-semibold" id="btnConfirmAction">Lanjutkan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
        // --- Sidebar Toggle Logic ---
        const menuToggle = document.querySelector(".NavSide__toggle");
        const sidebar = document.getElementById("main-sidebar");

        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', function() {
                menuToggle.classList.toggle("active");
                sidebar.classList.toggle("active");
            });
        }

        // --- Modal Logic ---
        function handleAction(action) {
            const dokumenAda = document.getElementById('linkDokumenRevisi') !== null;

            // Jika tidak ada dokumen, tampilkan error
            if (!dokumenAda) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Dokumen revisi belum diunggah oleh mahasiswa.',
                });
                return;
            }

            // Untuk "Ditolak", langsung munculkan input alasan
            if (action === 'Ditolak') {
                Swal.fire({
                    title: 'Alasan Penolakan',
                    input: 'textarea',
                    inputLabel: 'Catatan:',
                    inputPlaceholder: 'Masukkan catatan penolakan di sini...',
                    showCancelButton: true,
                    confirmButtonText: 'Kirim',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'btn btn-setujui',
                        cancelButton: 'btn btn-tolak'
                    },
                    inputValidator: (value) => {
                        if (!value || value.trim() === '') {
                            return 'Alasan penolakan tidak boleh kosong!';
                        }
                    }
                }).then(async (result) => {
                    if (result.isConfirmed && result.value) {
                        const postData = new URLSearchParams({
                            reject: true,
                            catatan_sidang: result.value,
                            nim: '<?= $current_nim ?>'
                        });

                        const response = await fetch(window.location.href, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: postData
                        });

                        const resultData = await response.json();
                        if (resultData.status === 'success') {
                            Swal.fire('Ditolak', resultData.message, 'success').then(() => {
                                window.location.href = resultData.redirectUrl;
                            });
                        } else {
                            Swal.fire('Gagal', resultData.message, 'error');
                        }
                    }
                });

            } else if (action === 'Disetujui') {
                showConfirmationModal('Disetujui');
            }
        }

        function showConfirmationModal(action) {
            const confirmationModalElement = document.getElementById('confirmationModal');
            const modalText = document.getElementById('confirmationModalText');
            const modalInstance = new bootstrap.Modal(confirmationModalElement);

            modalText.innerText = "Apakah Anda yakin ingin menyetujui dokumen revisi ini?";
            modalInstance.show();

            // Reset tombol biar tidak double listener
            const oldBtn = document.getElementById('btnConfirmAction');
            const newBtn = oldBtn.cloneNode(true);
            oldBtn.parentNode.replaceChild(newBtn, oldBtn);

            // Tambahkan listener baru
            newBtn.addEventListener('click', async function() {
                modalInstance.hide();

                const postData = new URLSearchParams({
                    approve: true,
                    nim: '<?= $current_nim ?>'
                });

                try {
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: postData
                    });

                    const text = await response.text();
                    let result;
                    try {
                        result = JSON.parse(text);
                    } catch (e) {
                        console.error('Gagal parsing JSON:', text);
                        Swal.fire('Kesalahan', 'Respon dari server tidak valid.', 'error');
                        return;
                    }

                    if (result.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: result.message
                        }).then(() => {
                            window.location.href = result.redirectUrl;
                        });
                    } else {
                        Swal.fire('Gagal', result.message, 'error');
                    }

                } catch (error) {
                    console.error('Fetch error:', error);
                    Swal.fire('Kesalahan', 'Gagal mengirim data ke server.', 'error');
                }
            });

            modalText.innerText = "Apakah Anda yakin ingin menyetujui dokumen revisi ini?";
            modalInstance.show();
        }
    </script>
</body>

</html>