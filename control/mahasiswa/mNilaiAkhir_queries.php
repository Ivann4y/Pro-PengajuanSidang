<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
    
$path_to_root = '../../';

// 1. Pengecekan Akses dan Session
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || $_SESSION['role'] !== 'mahasiswa') {
    $_SESSION['login_error'] = 'Anda harus login sebagai mahasiswa untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php");
    exit();
}
if (!isset($_SESSION['user_data']['nim'])) {
    die("NIM mahasiswa tidak ditemukan di session. Silakan login kembali.");
}

$nim = $_SESSION['user_data']['nim'];
$nama_mhs = $_SESSION['user_data']['nama_mhs'] ?? 'Nama Tidak Ditemukan';

require "../../koneksi/koneksiJoin.php";

// 2. Tentukan ID Sidang yang akan ditampilkan
$id_sidang = isset($_GET['id_sidang']) ? (int)$_GET['id_sidang'] : null;

// Jika tidak ada ID di URL, cari sidang yang terakhir diikuti mahasiswa
if ($id_sidang === null) {
    $sqlGetLastSidang = "
        SELECT TOP 1 s.id_sidang
        FROM Sidang s
        JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
        JOIN Kelompok k_member ON k_member.nomor_kelompok = k.nomor_kelompok
            AND k_member.tahun_ajaran = k.tahun_ajaran
            AND k_member.jenis_sidang = k.jenis_sidang
            AND k_member.id_matkul = k.id_matkul
        WHERE k_member.nim = ?
        ORDER BY s.id_sidang DESC
    ";
    $stmtGetLastSidang = sqlsrv_query($conn, $sqlGetLastSidang, [$nim]);
    
    if ($stmtGetLastSidang && ($row = sqlsrv_fetch_array($stmtGetLastSidang, SQLSRV_FETCH_ASSOC))) {
        $id_sidang = $row['id_sidang'];
    }
}

// 3. Inisialisasi variabel dengan nilai default untuk mencegah error jika sidang tidak ditemukan
$dataSidang = [
    'nama' => $nama_mhs,
    'judul' => '-',
    'pembimbing' => '-'
];
$nilaiAngka = null;
$nilaiHuruf = 'N/A';
$judul = '';
$nilai = '-';

$nomor_kelompok = null;
$id_kelompok = null;
$id_matkul = null;
$jenis_sidang = null;

// 4. Jika ID Sidang ditemukan, ambil data detail dan nilai
if ($id_sidang !== null) {
    // Ambil detail sidang dan kelompok, pastikan mahasiswa login adalah anggota kelompok
    $sql_detail = "
        SELECT k.nomor_kelompok, k.id_kelompok, k.jenis_sidang, k.id_matkul, s.judul
        FROM Sidang s
        JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
        JOIN Kelompok k_member ON k_member.nomor_kelompok = k.nomor_kelompok
            AND k_member.tahun_ajaran = k.tahun_ajaran
            AND k_member.jenis_sidang = k.jenis_sidang
            AND k_member.id_matkul = k.id_matkul
        WHERE s.id_sidang = ? AND k_member.nim = ?
    ";
    $stmt_detail = sqlsrv_query($conn, $sql_detail, [$id_sidang, $nim]);
    if ($stmt_detail && ($detail = sqlsrv_fetch_array($stmt_detail, SQLSRV_FETCH_ASSOC))) {
        $nomor_kelompok = $detail['nomor_kelompok'];
        $id_kelompok = $detail['id_kelompok'];
        $id_matkul = $detail['id_matkul'];
        $jenis_sidang = $detail['jenis_sidang'];
        $judul = $detail['judul'];
        $dataSidang['judul'] = $judul ?? '-';
    }
    // Ambil nama pembimbing sesuai jenis sidang
    $pembimbing = '-';
    if ($jenis_sidang === 'Tugas Akhir') {
        $sql_dosen_ta = "SELECT DISTINCT d.nama_dosen FROM Dosen d JOIN Bimbingan b ON d.nomor_dosen = b.nomor_dosen WHERE b.id_kelompok = ? AND b.isPembimbing = 1";
        $stmt_dosen_ta = sqlsrv_query($conn, $sql_dosen_ta, [$id_kelompok]);
        if ($stmt_dosen_ta && ($row = sqlsrv_fetch_array($stmt_dosen_ta, SQLSRV_FETCH_ASSOC))) {
            $pembimbing = $row['nama_dosen'];
        }
    } elseif ($jenis_sidang === 'Semester' && $id_matkul) {
        $sql_dosen_semester = "SELECT TOP 1 d.nama_dosen FROM Dosen d JOIN Pengampu_Kelas pk ON d.nomor_dosen = pk.nomor_dosen WHERE pk.id_matkul = ?";
        $stmt_dosen_semester = sqlsrv_query($conn, $sql_dosen_semester, [$id_matkul]);
        if ($stmt_dosen_semester && ($row = sqlsrv_fetch_array($stmt_dosen_semester, SQLSRV_FETCH_ASSOC))) {
            $pembimbing = $row['nama_dosen'];
        }
    }
    $dataSidang['pembimbing'] = $pembimbing;
    // Hitung nilai akhir hanya untuk mahasiswa login dan sidang terpilih
    $sqlNilai = "WITH NilaiPerDosen AS (SELECT (n_dokumen * 0.25 + n_presentasi * 0.25 + n_tanyajawab * 0.30 + n_proyek * 0.20) AS nilai_dosen, bobot_penilaian FROM Penilaian WHERE id_sidang = ? AND nim = ?) SELECT SUM(nilai_dosen * bobot_penilaian) / SUM(bobot_penilaian) AS nilai_akhir_weighted FROM NilaiPerDosen;";
    $stmtNilai = sqlsrv_query($conn, $sqlNilai, [$id_sidang, $nim]);
    if ($stmtNilai && ($rowNilai = sqlsrv_fetch_array($stmtNilai, SQLSRV_FETCH_ASSOC))) {
        if (isset($rowNilai['nilai_akhir_weighted'])) {
            $nilaiAngka = $rowNilai['nilai_akhir_weighted'];
            if ($nilaiAngka >= 85) $nilaiHuruf = 'A';
            elseif ($nilaiAngka >= 75) $nilaiHuruf = 'B';
            elseif ($nilaiAngka >= 65) $nilaiHuruf = 'C';
            elseif ($nilaiAngka >= 50) $nilaiHuruf = 'D';
            else $nilaiHuruf = 'E';
        }
    }
}
sqlsrv_close($conn);
// Variabel $id_sidang, $dataSidang, $nilaiAngka, $nilaiHuruf sekarang siap untuk digunakan di HTML.
?>