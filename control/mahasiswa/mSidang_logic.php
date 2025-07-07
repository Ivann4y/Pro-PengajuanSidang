<?php

session_start();

$user_session = $_SESSION['user_data'];
$nim_login = $user_session['nim'];

$path_to_root = '../../';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php"); 
    exit(); 
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    $_SESSION['login_error'] = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php");
    exit(); 
}

include '../../koneksi/koneksiAndrew.php';

// Ambil semua id_kelompok yang diikuti mahasiswa login
$sql_kelompok = "SELECT id_kelompok, jenis_sidang, id_matkul FROM Kelompok WHERE nim = ?";
$stmt_kelompok = sqlsrv_query($conn, $sql_kelompok, [$nim_login]);
$kelompok_ids = [];
$kelompok_map = [];
while ($row = sqlsrv_fetch_array($stmt_kelompok, SQLSRV_FETCH_ASSOC)) {
    $kelompok_ids[] = $row['id_kelompok'];
    $kelompok_map[$row['id_kelompok']] = [
        'jenis_sidang' => $row['jenis_sidang'],
        'id_matkul' => $row['id_matkul']
    ];
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rowsPerPage = 10;

$rows = [];
$totalRecords = 0;
$totalPages = 1;

if (!empty($kelompok_ids)) {
    // Build parameterized IN clause
    $in = implode(',', array_fill(0, count($kelompok_ids), '?'));
    $params = $kelompok_ids;

    // Tambahkan filter jenis sidang jika perlu
    $filter_sql = '';
    if ($filter === 'ta') {
        $filter_sql = " AND k.jenis_sidang = 'Tugas Akhir'";
    } elseif ($filter === 'semester') {
        $filter_sql = " AND k.jenis_sidang = 'Semester'";
    }

    // Hitung total
    $countQuery = "SELECT COUNT(*) AS total FROM Sidang s JOIN Kelompok k ON s.id_kelompok = k.id_kelompok WHERE s.id_kelompok IN ($in) $filter_sql";
    $countResult = sqlsrv_query($conn, $countQuery, $params);
    if ($countResult !== false) {
        $totalRecords = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC)['total'];
        $totalPages = max(1, ceil($totalRecords / $rowsPerPage));
    }

    // Query utama
    $query = "SELECT s.id_sidang, s.judul, mk.nama_matkul, k.jenis_sidang, k.id_kelompok, k.id_matkul,
        CASE 
            WHEN k.jenis_sidang = 'Tugas Akhir' THEN (
                SELECT TOP 1 d.nama_dosen 
                FROM Bimbingan b 
                JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen 
                WHERE b.id_kelompok = k.id_kelompok AND b.isPembimbing = 0x01
            )
            WHEN k.jenis_sidang = 'Semester' THEN (
                SELECT TOP 1 d2.nama_dosen 
                FROM Pengampu_Kelas pk2 
                JOIN Dosen d2 ON d2.nomor_dosen = pk2.nomor_dosen 
                WHERE pk2.id_matkul = k.id_matkul
            )
            ELSE NULL
        END AS nama_dosen
        FROM Sidang s
        JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
        JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul
        WHERE s.id_kelompok IN ($in) $filter_sql
        ORDER BY s.id_sidang
        OFFSET " . (($currentPage - 1) * $rowsPerPage) . " ROWS FETCH NEXT $rowsPerPage ROWS ONLY";
    $result = sqlsrv_query($conn, $query, $params);
    if ($result !== false) {
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $row['dosen'] = $row['nama_dosen'] ?? '-';
            $rows[] = $row;
        }
    }
} 