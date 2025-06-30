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

error_reporting(E_ALL);
ini_set('display_errors', 1);

$id_sidang = null;

if (isset($_SESSION['selected_sidang_id']) && !empty($_SESSION['selected_sidang_id'])) {
    $id_sidang = $_SESSION['selected_sidang_id'];
} else {
    header("Location: mSidang.php");
    exit();
}

$data_sidang = [];
$data_jadwal = [];
$nama_prodi = 'N/A';
$dosen_pembimbing = 'N/A';
$dosen_penguji = [];
$data_matkul = null;
$dosen_pengampu = [];
$dok_laporan = null;
$status_ajuan = null;

$sql_utama = "SELECT
                s.id_sidang,
                s.judul,
                CAST(s.jenis_sidang AS INT) AS jenis_sidang,
                s.id_kelompok,
                s.dok_laporan,
                s.status_ajuan
              FROM Sidang s
              WHERE s.id_sidang = ?";

$stmt_utama = sqlsrv_prepare($conn, $sql_utama, array(&$id_sidang));

if ($stmt_utama === false) {
    die("Terjadi kesalahan saat mempersiapkan query utama: " . print_r(sqlsrv_errors(), true));
}
if (!sqlsrv_execute($stmt_utama)) {
    die("Terjadi kesalahan saat mengeksekusi query utama: " . print_r(sqlsrv_errors(), true));
}

$data_sidang = sqlsrv_fetch_array($stmt_utama, SQLSRV_FETCH_ASSOC);

if (!$data_sidang) {
    echo "Detail sidang tidak ditemukan untuk ID: " . htmlspecialchars($id_sidang) . ".";
    header("Location: mSidang.php");
    exit();
}

$dok_laporan = $data_sidang['dok_laporan'] ?? null;
$status_ajuan = $data_sidang['status_ajuan'] ?? null;

$status_text = '';
$status_class = '';
if ($status_ajuan === 0) {
    $status_text = 'Status Pengajuan : Belum Disetujui';
    $status_class = 'belum-disetujui';
} elseif ($status_ajuan === 1) {
    $status_text = 'Status Pengajuan : Disetujui';
    $status_class = 'disetujui';
} else {
    $status_text = 'Status Pengajuan : Tidak Diketahui';
    $status_class = '';
}

$sql_jadwal = "SELECT ruang_sidang, tanggal_sidang, jam_sidang, jam_selesai FROM Jadwal WHERE id_sidang = ?";
$stmt_jadwal = sqlsrv_query($conn, $sql_jadwal, array($id_sidang));
if ($stmt_jadwal === false) {
    error_log("Error fetching jadwal: " . print_r(sqlsrv_errors(), true));
} else {
    $data_jadwal = sqlsrv_fetch_array($stmt_jadwal, SQLSRV_FETCH_ASSOC);
    if (!$data_jadwal) { $data_jadwal = []; }
}

$tanggal_sidang_formatted = 'Belum Dijadwalkan';
if (isset($data_jadwal['tanggal_sidang']) && $data_jadwal['tanggal_sidang'] instanceof DateTime) {
    setlocale(LC_TIME, 'id_ID.utf8');
    $tanggal_sidang_formatted = $data_jadwal['tanggal_sidang']->format('l, d F Y');
}

$jam_sidang_formatted = 'Belum Dijadwalkan';
if (isset($data_jadwal['jam_sidang']) && $data_jadwal['jam_sidang'] instanceof DateTime) {
    $jam_sidang_formatted = $data_jadwal['jam_sidang']->format('H.i');
    if (isset($data_jadwal['jam_selesai']) && $data_jadwal['jam_selesai'] instanceof DateTime) {
        $jam_sidang_formatted .= ' - ' . $data_jadwal['jam_selesai']->format('H.i');
    }
}

$jenis_sidang = $data_sidang['jenis_sidang'];
$id_kelompok = $data_sidang['id_kelompok'];

if (!empty($id_kelompok)) {
    $sql_prodi = "SELECT m.prodi FROM Mahasiswa m JOIN Kelompok_Mahasiswa km ON m.nim = km.nim WHERE km.id_kelompok = ? AND m.prodi IS NOT NULL";
    $stmt_prodi = sqlsrv_query($conn, $sql_prodi, array($id_kelompok));
    if ($stmt_prodi && $row_prodi = sqlsrv_fetch_array($stmt_prodi, SQLSRV_FETCH_ASSOC)) {
        $nama_prodi = $row_prodi['prodi'];
    } else {
        error_log("Error fetching prodi: " . print_r(sqlsrv_errors(), true));
    }
}

if ($jenis_sidang === 0) {
    $sql_pembimbing = "SELECT d.nama_dosen FROM Dosen d JOIN Bimbingan b ON d.nomor_dosen = b.nomor_dosen WHERE b.id_kelompok = ?";
    $stmt_pembimbing = sqlsrv_query($conn, $sql_pembimbing, array($id_kelompok));
    if ($stmt_pembimbing) {
        $pembimbing_row = sqlsrv_fetch_array($stmt_pembimbing, SQLSRV_FETCH_ASSOC);
        if ($pembimbing_row) {
            $dosen_pembimbing = $pembimbing_row['nama_dosen'];
        }
    } else {
        error_log("Error fetching pembimbing: " . print_r(sqlsrv_errors(), true));
    }

    $sql_penguji = "SELECT d.nama_dosen FROM Dosen d JOIN Penjadwalan p ON d.nomor_dosen = p.nomor_dosen WHERE p.id_sidang = ? AND p.peran_dosen = 0";
    $stmt_penguji = sqlsrv_query($conn, $sql_penguji, array($id_sidang));
    if ($stmt_penguji) {
        while ($row = sqlsrv_fetch_array($stmt_penguji, SQLSRV_FETCH_ASSOC)) {
            $dosen_penguji[] = $row['nama_dosen'];
        }
    } else {
        error_log("Error fetching penguji: " . print_r(sqlsrv_errors(), true));
    }

} elseif ($jenis_sidang === 1) {
    $sql_matkul = "SELECT TOP 1 mk.nama_matkul, mk.id_matkul FROM MataKuliah mk
                    JOIN Detail_Sidang ds ON mk.id_matkul = ds.id_matkul
                    WHERE ds.id_sidang = ?";
    $stmt_matkul = sqlsrv_query($conn, $sql_matkul, array($id_sidang));
    if ($stmt_matkul) {
        $data_matkul = sqlsrv_fetch_array($stmt_matkul, SQLSRV_FETCH_ASSOC);
        if ($data_matkul) {
            $id_matkul = $data_matkul['id_matkul'];

            // Ambil id_kelas mahasiswa login
            $user_session = $_SESSION['user_data'];
            $nim_login = $user_session['nim'];
            $id_kelas = null;
            $sql_kelas = "SELECT TOP 1 id_kelas FROM Kelas_Mahasiswa WHERE nim = ?";
            $stmt_kelas = sqlsrv_query($conn, $sql_kelas, array($nim_login));
            if ($stmt_kelas && $row_kelas = sqlsrv_fetch_array($stmt_kelas, SQLSRV_FETCH_ASSOC)) {
                $id_kelas = $row_kelas['id_kelas'];
            }
            $dosen_pengampu = [];
            if ($id_kelas && $id_matkul) {
                $sql_pengampu = "SELECT DISTINCT d.nama_dosen, k.id_kelas FROM Dosen d, Kelas_Mahasiswa k, Mahasiswa m, Pengampu_Kelas pk WHERE m.nim = k.nim AND d.nomor_dosen = pk.nomor_dosen AND pk.id_kelas = k.id_kelas AND k.id_kelas = ? AND pk.id_matkul = ?";
                $stmt_pengampu = sqlsrv_query($conn, $sql_pengampu, array($id_kelas, $id_matkul));
                if ($stmt_pengampu) {
                    while ($row = sqlsrv_fetch_array($stmt_pengampu, SQLSRV_FETCH_ASSOC)) {
                        $dosen_pengampu[] = $row['nama_dosen'];
                    }
                } else {
                    error_log("Error fetching pengampu: " . print_r(sqlsrv_errors(), true));
                }
            }
        }
    } else {
        error_log("Error fetching matkul: " . print_r(sqlsrv_errors(), true));
    }
}

sqlsrv_close($conn); 