<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$path_to_root = '../../';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
 
    header("Location: " . $path_to_root . "index.php"); 
    exit(); 
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
 
    header("Location: " . $path_to_root . "index.php");
    exit();
}

require "../../koneksi/koneksiAndrew.php";


if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['judul'])) {
    $_SESSION['id_sidang_aktif'] = (int)$_GET['id'];
    $_SESSION['judul'] = $_GET['judul'];

    // Redirect ke halaman dengan hanya parameter judul
    header("Location: aDetailSidang.php?judul=" . urlencode($_GET['judul']));
    exit();
}


if (isset($_SESSION['id_sidang_aktif']) && is_numeric($_SESSION['id_sidang_aktif'])) {
    $id_sidang = (int)$_SESSION['id_sidang_aktif'];
} else {

    $_SESSION['error_message'] = "ID Sidang tidak valid atau tidak ditemukan. Silakan pilih sidang dari daftar.";
    header("Location: aDaftarSidang.php");
    exit();
}


$data_nim = [];
$nama_prodi = 'N/A';
$data_sidang = [];
$data_mahasiswa = [];
$dosen_pembimbing = null;
$dosen_penguji = [];
$dosen_pengampu = [];
$data_matkul = null;
$data_bobotPenilaian = [];


$sql_utama = "SELECT 
                s.id_sidang, s.judul, 
                CASE 
                    WHEN s.status_sidang = 1 THEN 'Disetujui'
                    WHEN s.status_sidang = 0 THEN 'Ditolak'
                    ELSE 'Menunggu'
                END AS status_sidang_text, 
                k.jenis_sidang,
                s.id_kelompok
              FROM Sidang s
              JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
              WHERE s.id_sidang = ?";
$params_utama = array($id_sidang);
$stmt_utama = sqlsrv_query($conn, $sql_utama, $params_utama);
if ($stmt_utama === false) {
    die("Error pada query utama: " . print_r(sqlsrv_errors(), true));
}
$data_sidang = sqlsrv_fetch_array($stmt_utama, SQLSRV_FETCH_ASSOC);
if (!$data_sidang) {
    die("Error: Data Sidang dengan ID $id_sidang tidak ditemukan.");
}


$sql_jadwal = "SELECT ruang_sidang, tanggal_sidang, jam_sidang, jam_selesai FROM Jadwal WHERE id_sidang = ?";
$stmt_jadwal = sqlsrv_query($conn, $sql_jadwal, array($id_sidang));
$data_jadwal = sqlsrv_fetch_array($stmt_jadwal, SQLSRV_FETCH_ASSOC) ?: [];


$id_kelompok = $data_sidang['id_kelompok'];
$sql_prodi = "SELECT m.prodi 
              FROM Mahasiswa m 
              JOIN Kelompok k ON m.nim = k.nim 
              WHERE k.id_kelompok = ?";
$stmt_prodi = sqlsrv_query($conn, $sql_prodi, array($id_kelompok));
if ($row = sqlsrv_fetch_array($stmt_prodi, SQLSRV_FETCH_ASSOC)) {
    $nama_prodi = $row['prodi'];
}


if ($data_sidang['jenis_sidang'] == 'Tugas Akhir') { 

    
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
        while ($row = sqlsrv_fetch_array($stmt_dosen_terlibat, SQLSRV_FETCH_ASSOC)) {
            if ($row['peran_dosen'] == 1) { 
                $dosen_pembimbing = $row; 
            } elseif ($row['peran_dosen'] == 0) { 
                
                $dosen_penguji_data[] = [
                    'nama' => $row['nama_dosen'],
                    'bobot' => $row['bobot']
                ];
              
                $dosen_penguji[] = $row['nama_dosen'];
            }
        }
    }
} elseif ($data_sidang['jenis_sidang'] == 'Semester') { 
   
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
            die("Error pada query pengampu: " . print_r(sqlsrv_errors(), true));
        }
        
        while ($row = sqlsrv_fetch_array($stmt_pengampu, SQLSRV_FETCH_ASSOC)) {
            $dosen_pengampu[] = $row['nama_dosen'];
        }
    }
}



$dosen_list_penguji = [];
$sql_all_dosen = "SELECT nama_dosen FROM Dosen WHERE isPenguji = 1 ORDER BY nama_dosen ASC";
$stmt_all_dosen = sqlsrv_query($conn, $sql_all_dosen);
if ($stmt_all_dosen) {
    while ($row = sqlsrv_fetch_array($stmt_all_dosen, SQLSRV_FETCH_ASSOC)) {
        $dosen_list_penguji[] = ['nama' => $row['nama_dosen']];
    }
}
$dosen_list_json = json_encode($dosen_list_penguji);
?>