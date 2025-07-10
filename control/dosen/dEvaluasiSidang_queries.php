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

    // PERBAIKAN: Gunakan isset() dan periksa apakah string kosong untuk menangani nilai '0' dengan benar
    $n_dokumen    = (isset($_POST['n_dokumen'])    && $_POST['n_dokumen']    !== '') ? (int)$_POST['n_dokumen']    : null;
    $n_presentasi = (isset($_POST['n_presentasi']) && $_POST['n_presentasi'] !== '') ? (int)$_POST['n_presentasi'] : null;
    $n_tanyajawab = (isset($_POST['n_tanyajawab']) && $_POST['n_tanyajawab'] !== '') ? (int)$_POST['n_tanyajawab'] : null;
    $n_proyek     = (isset($_POST['n_proyek'])     && $_POST['n_proyek']     !== '') ? (int)$_POST['n_proyek']     : null;

    // Validasi penting: pastikan NIM terkirim bersama form
    if (empty($nim_post)) {
        // Sebaiknya gunakan session untuk pesan error agar tidak hilang saat redirect
        $_SESSION['error'] = "Terjadi kesalahan: NIM mahasiswa tidak terkirim saat menyimpan data.";
        header("Location: dEvaluasiSidang.php");
        exit;
    }
    
    // 1. UPDATE CATATAN REVISI (Logika sudah benar)
    $sql_update_catatan = "UPDATE Detail_Sidang SET catatan_sidang = ? WHERE id_sidang = ? AND nomor_dosen = ?";
    $params_update_catatan = [$catatan_post, $id_sidang, $nomor_dosen_login];
    $stmt_update_catatan = sqlsrv_query($conn, $sql_update_catatan, $params_update_catatan);

    if ($stmt_update_catatan === false) {
        $_SESSION['error'] = "Gagal memperbarui catatan revisi: " . print_r(sqlsrv_errors(), true);
        header("Location: dEvaluasiSidang.php"); // Redirect kembali ke halaman dengan nim terakhir
        exit;
    }

    // 2. CEK & SIMPAN NILAI (UPSERT) (Logika sudah benar)
    $sql_cek_nilai = "SELECT COUNT(*) as 'count' FROM Penilaian WHERE id_sidang = ? AND nomor_dosen = ? AND nim = ?";
    $stmt_cek_nilai = sqlsrv_query($conn, $sql_cek_nilai, [$id_sidang, $nomor_dosen_login, $nim_post]);
    $nilai_exists = sqlsrv_fetch_array($stmt_cek_nilai, SQLSRV_FETCH_ASSOC)['count'] > 0;

    if ($nilai_exists) {
        // UPDATE
        $sql_nilai = "UPDATE Penilaian SET n_dokumen = ?, n_presentasi = ?, n_tanyajawab = ?, n_proyek = ? WHERE id_sidang = ? AND nomor_dosen = ? AND nim = ?";
    } else {
        // INSERT
        $sql_nilai = "INSERT INTO Penilaian (id_sidang, nim, nomor_dosen, n_dokumen, n_presentasi, n_tanyajawab, n_proyek) VALUES (?, ?, ?, ?, ?, ?, ?)";
    }
    
    // Sesuaikan parameter berdasarkan query
    if ($nilai_exists) {
        $params_nilai = [$n_dokumen, $n_presentasi, $n_tanyajawab, $n_proyek, $id_sidang, $nomor_dosen_login, $nim_post];
    } else {
        $params_nilai = [$id_sidang, $nim_post, $nomor_dosen_login, $n_dokumen, $n_presentasi, $n_tanyajawab, $n_proyek];
    }

    $stmt_nilai = sqlsrv_query($conn, $sql_nilai, $params_nilai);
    if ($stmt_nilai === false) {
        // Tampilkan error yang lebih spesifik
        $_SESSION['error'] = "Gagal menyimpan nilai: " . print_r(sqlsrv_errors(), true);
        header("Location: dEvaluasiSidang.php");
        exit;
    }

    // Redirect dengan status sukses
    header("Location: dEvaluasiSidang.php?status=sukses");
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

// --- Query utama untuk mengambil data sidang ---


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
