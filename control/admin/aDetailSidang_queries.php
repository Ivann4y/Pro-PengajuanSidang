<?php
// ==============================
// FUNGSI 1: KONTROL SESI DAN KEAMANAN
// ==============================

// Cek dan mulai session jika belum aktif (agar bisa pakai $_SESSION)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Path ke root aplikasi (untuk redirect)
$path_to_root = '../../';

// 1.1. Cek apakah user sudah login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    // Jika belum login, simpan pesan error dan redirect ke halaman login
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php"); 
    exit();
}

// 1.2. Cek apakah user adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Jika bukan admin, simpan pesan error dan redirect ke halaman login
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php");
    exit();
}

// ==============================
// FUNGSI 2: KONEKSI DATABASE
// ==============================

// Sertakan file koneksi ke database SQL Server
require "../../koneksi/koneksiAndrew.php";

// ==============================
// FUNGSI 3: AMBIL ID SIDANG DARI URL/SESSION
// ==============================

if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['judul'])) {
    
    $_SESSION['id_sidang_aktif'] = (int)$_GET['id'];
    // INI JANGAN DI HAPUS
    $_SESSION['judul'] = $_GET['judul'];

    // Redirect ke halaman yang sama TAPI TANPA parameter GET
    header("Location: aDetailSidang.php");
    exit();
}

// Ambil id sidang dari session, jika tidak ada redirect ke daftar sidang
if (isset($_SESSION['id_sidang_aktif']) && is_numeric($_SESSION['id_sidang_aktif'])) {
    $id_sidang = (int)$_SESSION['id_sidang_aktif'];
} else {
    // Jika tidak ada id sidang valid, redirect ke daftar sidang
    $_SESSION['error_message'] = "ID Sidang tidak valid atau tidak ditemukan. Silakan pilih sidang dari daftar.";
    header("Location: aDaftarSidang.php");
    exit();
}

// ==============================
// FUNGSI 4: INISIALISASI VARIABEL DATA
// ==============================

// Siapkan variabel-variabel kosong untuk menampung data hasil query
$data_nim = [];              // Untuk daftar NIM mahasiswa (jika diperlukan)
$nama_prodi = 'N/A';         // Nama prodi kelompok
$data_sidang = [];           // Data utama sidang
$data_mahasiswa = [];        // Data mahasiswa dalam kelompok
$dosen_pembimbing = null;    // Data dosen pembimbing (TA)
$dosen_penguji = [];         // Daftar nama dosen penguji (TA)
$dosen_pengampu = [];        // Daftar nama dosen pengampu (Semester)
$data_matkul = null;         // Data mata kuliah (Semester)
$data_bobotPenilaian = [];   // Data bobot penilaian (jika ada)

// ==============================
// FUNGSI 5: AMBIL DATA SIDANG UTAMA
// ==============================

// Query untuk mengambil data utama sidang berdasarkan id_sidang
$sql_utama = "SELECT 
    s.id_sidang, 
    s.judul, 
    CASE 
        WHEN s.status_sidang = 1 THEN 'Disetujui'
        WHEN s.status_sidang = 0 THEN 'Ditolak'
        ELSE 'Menunggu'
    END AS status_sidang_text, 
    k.jenis_sidang,
    s.id_kelompok,
    k.nomor_kelompok
  FROM Sidang s
  JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
  WHERE s.id_sidang = ?";

// Siapkan parameter untuk query (id sidang)
$params_utama = array($id_sidang);

// Jalankan query utama
$stmt_utama = sqlsrv_query($conn, $sql_utama, $params_utama);
if ($stmt_utama === false) {
    // Jika query gagal, tampilkan error dan hentikan script
    die("Error pada query utama: " . print_r(sqlsrv_errors(), true));
}

// Ambil hasil query sebagai array asosiatif
$data_sidang = sqlsrv_fetch_array($stmt_utama, SQLSRV_FETCH_ASSOC);
if (!$data_sidang) {
    // Jika data tidak ditemukan, tampilkan error dan hentikan script
    die("Error: Data Sidang dengan ID $id_sidang tidak ditemukan.");
}

// ==============================
// FUNGSI 6: AMBIL DATA JADWAL SIDANG
// ==============================

// Query untuk mengambil jadwal sidang (ruangan, tanggal, jam)
$sql_jadwal = "SELECT ruang_sidang, tanggal_sidang, jam_sidang, jam_selesai FROM Jadwal WHERE id_sidang = ?";
$stmt_jadwal = sqlsrv_query($conn, $sql_jadwal, array($id_sidang));
// Ambil hasil query jadwal, jika tidak ada jadwal, isi array kosong
$data_jadwal = sqlsrv_fetch_array($stmt_jadwal, SQLSRV_FETCH_ASSOC) ?: [];

// ==============================
// FUNGSI 7: AMBIL PRODI KELOMPOK SIDANG
// ==============================

// Ambil id_kelompok dari data sidang
$id_kelompok = $data_sidang['id_kelompok'];
// Query untuk mengambil nama prodi kelompok
$sql_prodi = "SELECT m.prodi 
              FROM Mahasiswa m 
              JOIN Kelompok k ON m.nim = k.nim 
              WHERE k.id_kelompok = ?";
$stmt_prodi = sqlsrv_query($conn, $sql_prodi, array($id_kelompok));
if ($row = sqlsrv_fetch_array($stmt_prodi, SQLSRV_FETCH_ASSOC)) {
    $nama_prodi = $row['prodi'];
}

// ==============================
// FUNGSI 8: AMBIL DATA DOSEN TERLIBAT & MATA KULIAH
// ==============================

// Cek jenis sidang: jika TA, ambil dosen pembimbing & penguji
if ($data_sidang['jenis_sidang'] == 'Tugas Akhir') { 
    // Query untuk mengambil dosen pembimbing dan penguji sidang TA
    $sql_dosen_terlibat = "SELECT 
            d.nama_dosen, 
            CAST(p.peran_dosen AS INT) AS peran_dosen,
            (SELECT TOP 1 pl.bobot_penilaian 
             FROM Penilaian pl 
             WHERE pl.id_sidang = p.id_sidang AND pl.nomor_dosen = p.nomor_dosen) AS bobot
        FROM Dosen d 
        JOIN Penjadwalan p ON d.nomor_dosen = p.nomor_dosen
        WHERE p.id_sidang = ?
    ";
    $stmt_dosen_terlibat = sqlsrv_query($conn, $sql_dosen_terlibat, array($id_sidang));
    if ($stmt_dosen_terlibat) {
        $dosen_penguji_data = []; 
        // Loop setiap dosen yang terlibat
        while ($row = sqlsrv_fetch_array($stmt_dosen_terlibat, SQLSRV_FETCH_ASSOC)) {
            if ($row['peran_dosen'] == 1) { 
                // Jika peran 1 = pembimbing
                $dosen_pembimbing = $row; 
            } elseif ($row['peran_dosen'] == 0) { 
                // Jika peran 0 = penguji
                $dosen_penguji_data[] = [
                    'nama' => $row['nama_dosen'],
                    'bobot' => $row['bobot']
                ];
                $dosen_penguji[] = $row['nama_dosen'];
            }
        }
    }
} elseif ($data_sidang['jenis_sidang'] == 'Semester') { 
    // Jika sidang Semester, ambil data mata kuliah dan dosen pengampu
    $sql_matkul = "SELECT TOP 1 mk.nama_matkul, mk.id_matkul 
                   FROM MataKuliah mk 
                   JOIN Detail_Sidang ds ON mk.id_matkul = ds.id_matkul 
                   WHERE ds.id_sidang = ?";
    $stmt_matkul = sqlsrv_query($conn, $sql_matkul, array($id_sidang));
    if ($stmt_matkul) {
        $data_matkul = sqlsrv_fetch_array($stmt_matkul, SQLSRV_FETCH_ASSOC);
    }
    if ($data_matkul) {
        $id_matkul = $data_matkul['id_matkul'];
        $id_kelompok = $data_sidang['id_kelompok']; 
        // Query untuk mengambil dosen pengampu berdasarkan matkul dan kelas
        $sql_pengampu = "SELECT d.nama_dosen 
            FROM Dosen d 
            JOIN Pengampu_Kelas pk ON d.nomor_dosen = pk.nomor_dosen 
            WHERE 
                pk.id_matkul = ?
               AND pk.id_kelas = (
                SELECT TOP 1 k_mhs.id_kelas
                FROM Kelompok klp
                JOIN Mahasiswa mhs ON klp.nim = mhs.nim
                JOIN Kelas_Mahasiswa k_mhs ON mhs.nim = k_mhs.nim
                WHERE klp.id_kelompok = ?
                )
        ";
        $params_pengampu = array($id_matkul, $id_kelompok);
        $stmt_pengampu = sqlsrv_query($conn, $sql_pengampu, $params_pengampu);
        if ($stmt_pengampu === false) {
            // Jika query gagal, tampilkan error
            die("Error pada query pengampu: " . print_r(sqlsrv_errors(), true));
        }
        // Loop setiap dosen pengampu
        while ($row = sqlsrv_fetch_array($stmt_pengampu, SQLSRV_FETCH_ASSOC)) {
            $dosen_pengampu[] = $row['nama_dosen'];
        }
    }
}

// ==============================
// FUNGSI 9: AMBIL DAFTAR DOSEN PENGUJI (untuk autocomplete JS)
// ==============================

// Query untuk mengambil semua dosen yang bisa jadi penguji (untuk fitur autocomplete di JS)
$dosen_list_penguji = [];
$sql_all_dosen = "SELECT nama_dosen FROM Dosen WHERE isPenguji = 1 ORDER BY nama_dosen ASC";
$stmt_all_dosen = sqlsrv_query($conn, $sql_all_dosen);
if ($stmt_all_dosen) {
    while ($row = sqlsrv_fetch_array($stmt_all_dosen, SQLSRV_FETCH_ASSOC)) {
        $dosen_list_penguji[] = ['nama' => $row['nama_dosen']];
    }
}
// Encode ke JSON agar bisa dipakai di JavaScript
$dosen_list_json = json_encode($dosen_list_penguji);

?>