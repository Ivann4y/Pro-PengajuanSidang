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

// Ambil ID sidang dari GET (sekali) lalu simpan ke session
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $_SESSION['id_sidang_aktif'] = (int)$_GET['id'];
    // Hapus nim lama jika id sidang baru dipilih
    unset($_SESSION['nim_aktif']); 
    header("Location: dEvaluasiSidang.php");
    exit;
}

// Ambil NIM dari GET (sekali) lalu simpan ke session, lalu redirect untuk membersihkan URL
if (isset($_GET['nim']) && (!isset($_SESSION['nim_aktif']) || $_SESSION['nim_aktif'] !== $_GET['nim'])) {
    $_SESSION['nim_aktif'] = $_GET['nim'];
    header("Location: dEvaluasiSidang.php");
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil data dari form
    $nim_post = $_POST['nim'] ?? null;
    $catatan_post = $_POST['catatanEvaluasi'] ?? '';

    $n_dokumen    = (isset($_POST['n_dokumen'])    && $_POST['n_dokumen']    !== '') ? (int)$_POST['n_dokumen']    : null;
    $n_presentasi = (isset($_POST['n_presentasi']) && $_POST['n_presentasi'] !== '') ? (int)$_POST['n_presentasi'] : null;
    $n_tanyajawab = (isset($_POST['n_tanyajawab']) && $_POST['n_tanyajawab'] !== '') ? (int)$_POST['n_tanyajawab'] : null;
    $n_proyek     = (isset($_POST['n_proyek'])     && $_POST['n_proyek']     !== '') ? (int)$_POST['n_proyek']     : null;

    if (empty($nim_post)) {
        $_SESSION['error'] = "Terjadi kesalahan: NIM mahasiswa tidak terkirim saat menyimpan data.";
        header("Location: dEvaluasiSidang.php");
        exit;
    }
    
    // ====================================================================================
    // ===== [PERBAIKAN UTAMA] LOGIKA UPSERT UNTUK CATATAN EVALUASI SIDANG =====
    // ====================================================================================
    
    // 1. Cek apakah record untuk dosen ini di sidang ini sudah ada di Detail_Sidang
    $sql_cek_catatan = "SELECT COUNT(*) as 'count' FROM Detail_Sidang WHERE id_sidang = ? AND nomor_dosen = ?";
    $stmt_cek_catatan = sqlsrv_query($conn, $sql_cek_catatan, [$id_sidang, $nomor_dosen_login]);
    $catatan_exists = false;
    if ($stmt_cek_catatan) {
        $catatan_exists = sqlsrv_fetch_array($stmt_cek_catatan, SQLSRV_FETCH_ASSOC)['count'] > 0;
    }

    if ($catatan_exists) {
        // 2a. Jika sudah ada, UPDATE catatan yang ada
        $sql_catatan = "UPDATE Detail_Sidang SET catatan_sidang = ? WHERE id_sidang = ? AND nomor_dosen = ?";
        $params_catatan = [$catatan_post, $id_sidang, $nomor_dosen_login];
    } else {
        // 2b. Jika belum ada, INSERT record baru.
        // Kita butuh id_matkul, jadi kita ambil dulu dari tabel lain (misal: Kelompok)
        $id_matkul_untuk_insert = null;
        $sql_get_matkul = "SELECT k.id_matkul FROM Sidang s JOIN Kelompok k ON s.id_kelompok = k.id_kelompok WHERE s.id_sidang = ?";
        $stmt_get_matkul = sqlsrv_query($conn, $sql_get_matkul, [$id_sidang]);
        if($data_matkul = sqlsrv_fetch_array($stmt_get_matkul, SQLSRV_FETCH_ASSOC)){
            $id_matkul_untuk_insert = $data_matkul['id_matkul'];
        }

        $sql_catatan = "INSERT INTO Detail_Sidang (id_sidang, nomor_dosen, id_matkul, catatan_sidang) VALUES (?, ?, ?, ?)";
        $params_catatan = [$id_sidang, $nomor_dosen_login, $id_matkul_untuk_insert, $catatan_post];
    }

    // 3. Eksekusi query catatan (UPDATE atau INSERT)
    $stmt_proses_catatan = sqlsrv_query($conn, $sql_catatan, $params_catatan);
    if ($stmt_proses_catatan === false) {
        $_SESSION['error'] = "Gagal menyimpan catatan evaluasi: " . print_r(sqlsrv_errors(), true);
        header("Location: dEvaluasiSidang.php");
        exit;
    }
    // ===== AKHIR DARI PERBAIKAN UTAMA =====
    

    // 4. Proses penyimpanan NILAI (logika UPSERT ini sudah benar)
    $sql_cek_nilai = "SELECT COUNT(*) as 'count' FROM Penilaian WHERE id_sidang = ? AND nomor_dosen = ? AND nim = ?";
    $stmt_cek_nilai = sqlsrv_query($conn, $sql_cek_nilai, [$id_sidang, $nomor_dosen_login, $nim_post]);
    $nilai_exists = sqlsrv_fetch_array($stmt_cek_nilai, SQLSRV_FETCH_ASSOC)['count'] > 0;

    if ($nilai_exists) {
        $sql_nilai = "UPDATE Penilaian SET n_dokumen = ?, n_presentasi = ?, n_tanyajawab = ?, n_proyek = ? WHERE id_sidang = ? AND nomor_dosen = ? AND nim = ?";
        $params_nilai = [$n_dokumen, $n_presentasi, $n_tanyajawab, $n_proyek, $id_sidang, $nomor_dosen_login, $nim_post];
    } else {
        $sql_nilai = "INSERT INTO Penilaian (id_sidang, nim, nomor_dosen, n_dokumen, n_presentasi, n_tanyajawab, n_proyek) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params_nilai = [$id_sidang, $nim_post, $nomor_dosen_login, $n_dokumen, $n_presentasi, $n_tanyajawab, $n_proyek];
    }
    
    $stmt_nilai = sqlsrv_query($conn, $sql_nilai, $params_nilai);
    if ($stmt_nilai === false) {
        $_SESSION['error'] = "Gagal menyimpan nilai: " . print_r(sqlsrv_errors(), true);
        header("Location: dEvaluasiSidang.php");
        exit;
    }

    // Redirect dengan status sukses jika semua berhasil
    $_SESSION['sukses'] = "Data evaluasi berhasil disimpan."; // Gunakan session untuk pesan sukses
    require_once __DIR__ . '/../kirimNotifikasi.php';
    $nama_dosen = isset($_SESSION['user_data']['nama_dosen']) ? $_SESSION['user_data']['nama_dosen'] : 'Dosen';
    // Ambil judul sidang dari database
    $sql_judul = "SELECT s.judul FROM Sidang s WHERE s.id_sidang = ?";
    $stmt_judul = sqlsrv_query($conn, $sql_judul, [$id_sidang]);
    $judul = ($stmt_judul && ($row_judul = sqlsrv_fetch_array($stmt_judul, SQLSRV_FETCH_ASSOC))) ? $row_judul['judul'] : '-';
    $nilai_str = "Laporan: $n_dokumen, Presentasi: $n_presentasi, Tanya Jawab: $n_tanyajawab, Proyek: $n_proyek";
    $pesan = "Evaluasi sidang untuk judul '$judul' telah diberikan oleh $nama_dosen. Nilai sementara: $nilai_str. Catatan evaluasi: $catatan_post";
    kirimNotifikasi($nim_post, $pesan, $nomor_dosen_login, $conn);
    header("Location: dEvaluasiSidang.php");
    exit();
}


// ===================================================================================
// BAGIAN 3: PENGAMBILAN DATA UNTUK DITAMPILKAN DI HALAMAN
// ===================================================================================

// --- Variabel default ---
$id_kelompok = null;
$judul = 'Data tidak ditemukan';
$ruangan = '-';
$tanggal_formatted = '-';
$jam = '-';
$nama_matkul_sidang = 'Mata Kuliah Tidak Ditemukan';
$mahasiswa = [];
$current_nim = '';
$current_nama_mhs = 'Data mahasiswa tidak ditemukan';
$nomor_kelompok = null;
$jenis_sidang = null;
$id_matkul = null;
$nim_perwakilan = null; // Variabel penting yang ditambahkan

// --- Query utama untuk mengambil data sidang yang lebih lengkap ---
$sql_sidang = "
    SELECT 
        s.judul, 
        k.nomor_kelompok, 
        k.id_kelompok, 
        k.jenis_sidang, 
        k.id_matkul,
        k.nim AS nim_perwakilan, -- INI KRUSIAL UNTUK MENGAMBIL DOSEN PENGAMPU
        mk.nama_matkul
    FROM Sidang s 
    JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
    LEFT JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul
    WHERE s.id_sidang = ?";
$result_sidang = sqlsrv_query($conn, $sql_sidang, [$id_sidang]);

if ($data_sidang = sqlsrv_fetch_array($result_sidang, SQLSRV_FETCH_ASSOC)) {
    // --- Ambil data dasar dari query ---
    $judul = $data_sidang['judul'];
    $nomor_kelompok = $data_sidang['nomor_kelompok'];
    $id_kelompok = $data_sidang['id_kelompok'];
    $jenis_sidang = $data_sidang['jenis_sidang'];
    $id_matkul = $data_sidang['id_matkul'];
    $nim_perwakilan = $data_sidang['nim_perwakilan']; // Simpan nim perwakilan
    $nama_matkul_sidang = $data_sidang['nama_matkul'] ?? 'Tidak Terkait';

    // --- Ambil daftar mahasiswa dalam kelompok ---
    if (isset($nomor_kelompok)) {
        $sql_mhs = "SELECT DISTINCT k.nim, m.nama_mhs
                    FROM Kelompok k
                    JOIN Mahasiswa m ON k.nim = m.nim
                    WHERE k.nomor_kelompok = ?
                    ORDER BY m.nama_mhs ASC";
        $stmt_mhs = sqlsrv_query($conn, $sql_mhs, [$nomor_kelompok]);
        if ($stmt_mhs) {
            while ($row_mhs = sqlsrv_fetch_array($stmt_mhs, SQLSRV_FETCH_ASSOC)) {
                $mahasiswa[] = $row_mhs;
            }
        }
    }

    // --- Tentukan mahasiswa aktif dari session atau default ---
    if (isset($_SESSION['nim_aktif']) && in_array($_SESSION['nim_aktif'], array_column($mahasiswa, 'nim'))) {
        $current_nim = $_SESSION['nim_aktif'];
    } elseif (!empty($mahasiswa)) {
        $current_nim = $mahasiswa[0]['nim'];
        $_SESSION['nim_aktif'] = $current_nim;
    }
    
    // Ambil nama mahasiswa aktif
    foreach ($mahasiswa as $mhs) {
        if ($mhs['nim'] == $current_nim) {
            $current_nama_mhs = $mhs['nama_mhs'];
            break;
        }
    }

    // =========================================================================
    // ===== [PERBAIKAN UTAMA] LOGIKA PENGAMBILAN DOSEN YANG DISAMAKAN =====
    // =========================================================================
    $dosen_pembimbing_list = [];
    $dosen_penguji_list = [];
    $dosen_pengampu_list = [];
    $labelPembimbing = "Dosen"; // Label default

    if ($jenis_sidang == 'Tugas Akhir') {
        $labelPembimbing = "Dosen Pembimbing";

        // Ambil PEMBIMBING dari tabel Bimbingan
        $sql_pembimbing = "
            SELECT d.nama_dosen FROM Bimbingan b
            JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen
            WHERE b.id_kelompok = ? AND b.isPembimbing = 1
        ";
        $stmt_pembimbing = sqlsrv_query($conn, $sql_pembimbing, [$id_kelompok]);
        if ($stmt_pembimbing) {
            while ($row = sqlsrv_fetch_array($stmt_pembimbing, SQLSRV_FETCH_ASSOC)) {
                $dosen_pembimbing_list[] = $row['nama_dosen'];
            }
        }

        // Ambil PENGUJI dari tabel Penjadwalan
        $sql_penguji = "
            SELECT d.nama_dosen FROM Penjadwalan pj
            JOIN Dosen d ON pj.nomor_dosen = d.nomor_dosen
            WHERE pj.id_sidang = ? AND pj.peran_dosen = 0 -- 0 untuk penguji
        ";
        $stmt_penguji = sqlsrv_query($conn, $sql_penguji, [$id_sidang]);
        if ($stmt_penguji) {
            while ($row = sqlsrv_fetch_array($stmt_penguji, SQLSRV_FETCH_ASSOC)) {
                $dosen_penguji_list[] = $row['nama_dosen'];
            }
        }

    } elseif ($jenis_sidang == 'Semester') {
        $labelPembimbing = "Dosen Pengampu";
        
        // Ambil PENGAMPU dari tabel Pengampu_Kelas menggunakan nim perwakilan
        $sql_pengampu = "
            SELECT DISTINCT d.nama_dosen FROM Pengampu_Kelas pk
            JOIN Dosen d ON pk.nomor_dosen = d.nomor_dosen
            WHERE pk.id_matkul = ? AND pk.id_kelas = (
                SELECT TOP 1 km.id_kelas FROM Kelas_Mahasiswa km WHERE km.nim = ?

               
            )
                 ORDER BY d.nama_dosen DESC
        ";
        // Gunakan $nim_perwakilan yang sudah diambil di query utama
        $stmt_pengampu = sqlsrv_query($conn, $sql_pengampu, [$id_matkul, $nim_perwakilan]);
        if ($stmt_pengampu) {
            while ($row = sqlsrv_fetch_array($stmt_pengampu, SQLSRV_FETCH_ASSOC)) {
                $dosen_pengampu_list[] = $row['nama_dosen'];
            }
        }
        // Di halaman ini, dosen pengampu ditampilkan di posisi 'pembimbing'
        $dosen_pembimbing_list = $dosen_pengampu_list; 
        // Tidak ada penguji terpisah untuk sidang semester
        $dosen_penguji_list = [];
    }
    // =========================================================================
    // ===== AKHIR DARI PERBAIKAN LOGIKA DOSEN =====
    // =========================================================================

    // --- Ambil jadwal sidang ---
    $sql_jadwal = "SELECT ruang_sidang, tanggal_sidang, jam_sidang, jam_selesai FROM Jadwal WHERE id_sidang = ?";
    $result_jadwal = sqlsrv_query($conn, $sql_jadwal, [$id_sidang]);
    if ($result_jadwal && $data_jadwal = sqlsrv_fetch_array($result_jadwal, SQLSRV_FETCH_ASSOC)) {
        $ruangan = $data_jadwal['ruang_sidang'] ?? '-';
        $jam_mulai = $data_jadwal['jam_sidang'] ? $data_jadwal['jam_sidang']->format('H:i') : null;
        $jam_selesai = $data_jadwal['jam_selesai'] ? $data_jadwal['jam_selesai']->format('H:i') : null;

        if ($jam_mulai && $jam_selesai) {
            $jam = $jam_mulai . ' - ' . $jam_selesai;
        } elseif ($jam_mulai) {
            $jam = $jam_mulai;
        }

        if ($data_jadwal['tanggal_sidang'] instanceof DateTime) {
            setlocale(LC_TIME, 'id_ID.UTF-8', 'Indonesian');
            $tanggal_formatted = strftime('%A, %d %B %Y', $data_jadwal['tanggal_sidang']->getTimestamp());
        }
    }

    // --- Ambil catatan revisi dan nilai yang sudah ada ---
    $catatan_revisi = '';
    $sql_catatan = "SELECT catatan_sidang FROM Detail_Sidang WHERE id_sidang = ? AND nomor_dosen = ?";
    $result_catatan = sqlsrv_query($conn, $sql_catatan, [$id_sidang, $nomor_dosen_login]);
    if ($result_catatan && $row_catatan = sqlsrv_fetch_array($result_catatan, SQLSRV_FETCH_ASSOC)) {
        $catatan_revisi = $row_catatan['catatan_sidang'];
    }

    $nilai_mahasiswa = ['n_dokumen' => '', 'n_presentasi' => '', 'n_tanyajawab' => '', 'n_proyek' => ''];
    if (!empty($current_nim)) {
        $sql_get_nilai = "SELECT n_dokumen, n_presentasi, n_tanyajawab, n_proyek FROM Penilaian WHERE id_sidang = ? AND nomor_dosen = ? AND nim = ?";
        $result_get_nilai = sqlsrv_query($conn, $sql_get_nilai, [$id_sidang, $nomor_dosen_login, $current_nim]);
        if ($result_get_nilai && $row_nilai = sqlsrv_fetch_array($result_get_nilai, SQLSRV_FETCH_ASSOC)) {
            $nilai_mahasiswa = $row_nilai;
        }
    }
}

// --- Cek kelengkapan form untuk menonaktifkan tombol kirim ---
$nilai_sudah_dikirim_dan_lengkap = false;
if (
    isset($nilai_mahasiswa['n_dokumen'])    && $nilai_mahasiswa['n_dokumen']    !== '' && $nilai_mahasiswa['n_dokumen']    !== null &&
    isset($nilai_mahasiswa['n_presentasi']) && $nilai_mahasiswa['n_presentasi'] !== '' && $nilai_mahasiswa['n_presentasi'] !== null &&
    isset($nilai_mahasiswa['n_tanyajawab']) && $nilai_mahasiswa['n_tanyajawab'] !== '' && $nilai_mahasiswa['n_tanyajawab'] !== null &&
    isset($nilai_mahasiswa['n_proyek'])     && $nilai_mahasiswa['n_proyek']     !== '' && $nilai_mahasiswa['n_proyek']     !== null &&
    !empty(trim($catatan_revisi))
) {
    $nilai_sudah_dikirim_dan_lengkap = true;
}

// --- Siapkan variabel untuk ditampilkan di HTML ---
$namaPembimbing_html = !empty($dosen_pembimbing_list) ? implode('<br>', array_map('htmlspecialchars', $dosen_pembimbing_list)) : 'Belum ditentukan';
$namaPenguji_html = !empty($dosen_penguji_list) ? implode('<br>', array_map('htmlspecialchars', $dosen_penguji_list)) : 'Belum ditentukan';

?>
