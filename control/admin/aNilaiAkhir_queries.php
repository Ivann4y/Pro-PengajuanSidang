<?php
session_start();

//koneksi ke database.
require "../../koneksi/koneksiAndrew.php";


if ($conn === false) {
    die("Koneksi ke database gagal: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}

//parameter url session
$id_sidang = isset($_GET['id_sidang']) ? (int)$_GET['id_sidang'] : 0;
$current_nim = isset($_GET['nim']) ? trim($_GET['nim']) : null;
$error_message = '';


$mahasiswa_list = []; 
$nama_matkul = 'Data tidak ditemukan'; 
$dosen_terkait_sidang = 'Data tidak ditemukan'; 
$id_kelompok = null; 
$id_matkul = null; 
$jenis_sidang = null; 

//redirect
if (isset($_GET['id_sidang']) && is_numeric($_GET['id_sidang'])) {
    $_SESSION['id_sidang_aktif'] = (int)$_GET['id_sidang'];
    $redirectUrl = 'aNilaiAkhir.php';
    if (isset($_GET['nim'])) {
        $redirectUrl .= '?nim=' . urlencode($_GET['nim']);
    }    header('Location: ' . $redirectUrl);
    exit();
}

// ambil id sidang dari url session biar query jalan
if (isset($_SESSION['id_sidang_aktif']) && is_numeric($_SESSION['id_sidang_aktif'])) {
    $id_sidang = (int)$_SESSION['id_sidang_aktif'];
} else {
    $_SESSION['error_message'] = "ID Sidang tidak valid atau tidak ditemukan. Silakan pilih sidang dari daftar.";
    header("Location: aDaftarSidang.php");
    exit();
}


// 3. PENGAMBILAN DETAIL SIDANG (ID KELOMPOK, JENIS, ID MATKUL)
if ($id_sidang > 0) {
  $sql_detail = "SELECT 
                    k.nomor_kelompok, 
                    k.id_kelompok, 
                    k.jenis_sidang, 
                    k.id_matkul, 
                    s.judul
                  FROM Sidang s
                  JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
                  WHERE s.id_sidang = ?";
$stmt_detail = sqlsrv_query($conn, $sql_detail, array($id_sidang));


if ($stmt_detail === false) {
  error_log("Query detail sidang gagal: " . print_r(sqlsrv_errors(), true));
  $error_message = "Terjadi kesalahan saat mengambil detail sidang. Mohon coba lagi.";
  $id_sidang = 0;
} else {
  $detail = sqlsrv_fetch_array($stmt_detail, SQLSRV_FETCH_ASSOC);
  if ($detail) {
      $nomor_kelompok = $detail['nomor_kelompok'];
      $id_kelompok = $detail['id_kelompok'];
      $id_matkul = $detail['id_matkul'];
      $jenis_sidang = $detail['jenis_sidang'];
      $judul = $detail['judul'];
  } else {
      $error_message = "Data Sidang dengan ID: " . htmlspecialchars($id_sidang ?? '') . " tidak ditemukan."; // Perbaikan: handle null
      $id_sidang = 0;
  }
}
}


// 4. PENGAMBILAN DAFTAR MAHASISWA DALAM KELOMPOK
if ($id_sidang) {
    $sql_mhs = "SELECT DISTINCT k.nim, m.nama_mhs
                FROM Kelompok k
                JOIN Mahasiswa m ON k.nim = m.nim
                WHERE k.nomor_kelompok = ?
                ORDER BY k.nim";
    $stmt_mhs = sqlsrv_query($conn, $sql_mhs, array($nomor_kelompok));
    
    if ($stmt_mhs) {
        while ($row = sqlsrv_fetch_array($stmt_mhs, SQLSRV_FETCH_ASSOC)) {
            $mahasiswa_list[] = $row;
        }
    }

    if (isset($_GET['nim']) && in_array($_GET['nim'], array_column($mahasiswa_list, 'nim'))) {
        $current_nim = $_GET['nim'];
    } elseif (!empty($mahasiswa_list)) {
        $current_nim = $mahasiswa_list[0]['nim'];
    }
} else {
    $mahasiswa_list = [];
    $error_message = "ID Sidang tidak valid.";
}

if (empty($mahasiswa_list) && empty($error_message) && $id_sidang > 0) {
    $error_message = "Tidak ada mahasiswa yang terdaftar dalam kelompok untuk sidang ini.";
}

// 5. PENGAMBILAN NAMA MATA KULIAH & NAMA DOSEN TERKAIT
if ($id_matkul) {
    $sql_matkul_q = "SELECT nama_matkul FROM MataKuliah WHERE id_matkul = ?";
    $stmt_matkul_q = sqlsrv_query($conn, $sql_matkul_q, array($id_matkul));
    if ($stmt_matkul_q && $row_matkul = sqlsrv_fetch_array($stmt_matkul_q, SQLSRV_FETCH_ASSOC)) {
        $nama_matkul = $row_matkul['nama_matkul'];
    }
}


if ($jenis_sidang === 'Tugas Akhir') { 
    $sql_dosen_ta = "SELECT DISTINCT d.nama_dosen 
                     FROM Dosen d 
                     JOIN Bimbingan b ON d.nomor_dosen = b.nomor_dosen 
                     WHERE b.id_kelompok = ? AND b.isPembimbing = 1"; 
    $stmt_dosen_ta = sqlsrv_query($conn, $sql_dosen_ta, array($id_kelompok));
    if ($stmt_dosen_ta && $row = sqlsrv_fetch_array($stmt_dosen_ta, SQLSRV_FETCH_ASSOC)) {
        $dosen_terkait_sidang = $row['nama_dosen'];
    }
} elseif ($jenis_sidang === 'Semester' && $id_matkul) {
    $sql_dosen_semester = "SELECT TOP 1 d.nama_dosen FROM Dosen d, Pengampu_Kelas pk, Detail_Sidang ds WHERE ds.id_sidang = ? AND pk.id_matkul = ds.id_matkul AND pk.nomor_dosen = d.nomor_dosen";
    $stmt_dosen_semester = sqlsrv_query($conn, $sql_dosen_semester, array($id_sidang));
    if ($stmt_dosen_semester && $row = sqlsrv_fetch_array($stmt_dosen_semester, SQLSRV_FETCH_ASSOC)) {
        $dosen_terkait_sidang = $row['nama_dosen'];
    }
} else {
    $dosen_terkait_sidang = 'Jenis sidang tidak valid';
}

//HITUNG NILAI AKHIR
$dataMahasiswa = [
    'nim' => $current_nim ?? 'N/A',
    'nama_mhs' => 'Data tidak ditemukan',
    'nama_matkul' => $nama_matkul,
    'nama_pembimbing' => $dosen_terkait_sidang
];
$nilaiDetail = [ 'dokumen' => '-', 'presentasi' => '-', 'tanyajawab' => '-', 'proyek' => '-' ];
$nilaiAkhirAngka = '-';
$nilaiAkhirHuruf = '';
// $semuaCatatan = 'Tidak ada catatan.';

// Fungsi konversi nilai
function getGrade($nilai) {
    if ($nilai >= 85) return 'A';
    if ($nilai >= 80) return 'B+';
    if ($nilai >= 75) return 'B';
    if ($nilai >= 70) return 'C+';
    if ($nilai >= 65) return 'C';
    if ($nilai >= 55) return 'D';
    return 'E';
}

if ($current_nim && empty($error_message)) {
    foreach($mahasiswa_list as $mhs) {
        if($mhs['nim'] == $current_nim) {
            $dataMahasiswa['nama_mhs'] = $mhs['nama_mhs'];
            break;
        }
    }

    $sqlNilai = "
        SELECT
            AVG(CAST(n_dokumen AS FLOAT)) AS avg_dokumen,
            AVG(CAST(n_presentasi AS FLOAT)) AS avg_presentasi,
            AVG(CAST(n_tanyajawab AS FLOAT)) AS avg_tanyajawab,
            AVG(CAST(n_proyek AS FLOAT)) AS avg_proyek
        FROM Penilaian
        WHERE id_sidang = ? AND nim = ?;
    ";
    $paramsNilai = array($id_sidang, $current_nim);
    $stmtNilai = sqlsrv_query($conn, $sqlNilai, $paramsNilai);

    if ($stmtNilai && $rowNilai = sqlsrv_fetch_array($stmtNilai, SQLSRV_FETCH_ASSOC)) {
        $nilaiDetail['dokumen'] = !is_null($rowNilai['avg_dokumen']) ? number_format($rowNilai['avg_dokumen'], 2) : '-';
        $nilaiDetail['presentasi'] = !is_null($rowNilai['avg_presentasi']) ? number_format($rowNilai['avg_presentasi'], 2) : '-';
        $nilaiDetail['tanyajawab'] = !is_null($rowNilai['avg_tanyajawab']) ? number_format($rowNilai['avg_tanyajawab'], 2) : '-';
        $nilaiDetail['proyek'] = !is_null($rowNilai['avg_proyek']) ? number_format($rowNilai['avg_proyek'], 2) : '-';

        if (!is_null($rowNilai['avg_dokumen'])) {
            $nilaiAkhirAngka = ($rowNilai['avg_dokumen'] * 0.25) + ($rowNilai['avg_presentasi'] * 0.25) + ($rowNilai['avg_tanyajawab'] * 0.30) + ($rowNilai['avg_proyek'] * 0.20);
            $nilaiAkhirHuruf = getGrade($nilaiAkhirAngka);
            $nilaiAkhirAngka = number_format($nilaiAkhirAngka, 2);
        }
    }

}
?>