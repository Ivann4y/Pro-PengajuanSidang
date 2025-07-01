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

if (isset($_GET['action']) && $_GET['action'] === 'set_sidang_session' && isset($_GET['id_sidang']) && is_numeric($_GET['id_sidang'])) {
    $id_sidang_from_get = (int)$_GET['id_sidang'];

    $checkQuery = "SELECT id_sidang FROM Sidang WHERE id_sidang = ?";
    $checkStmt = sqlsrv_prepare($conn, $checkQuery, array($id_sidang_from_get));

    if ($checkStmt === false) {
        header("Location: mSidang.php?error=query_check");
        exit();
    }

    if (!sqlsrv_execute($checkStmt)) {
        header("Location: mSidang.php?error=execute_check");
        exit();
    }

    if (sqlsrv_has_rows($checkStmt)) {
        $_SESSION['selected_sidang_id'] = $id_sidang_from_get;
        header("Location: mdetailSidang.php");
        exit();
    } else {
        header("Location: mSidang.php?error=sidang_not_found");
        exit();
    }
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rowsPerPage = 10;
$countQuery = "SELECT COUNT(DISTINCT s.id_sidang) as total 
                FROM Sidang s
                JOIN Kelompok_Mahasiswa km ON s.id_kelompok = km.id_kelompok
                JOIN Detail_Sidang ds ON ds.id_sidang = s.id_sidang 
                JOIN MataKuliah m ON ds.id_matkul = m.id_matkul 
                JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen
                WHERE km.nim = ?";

if ($filter === 'ta') {
    $countQuery .= " AND s.jenis_sidang = 0";
} elseif ($filter === 'semester') {
    $countQuery .= " AND s.jenis_sidang = 1";
}

$countResult = sqlsrv_query($conn, $countQuery, array($nim_login));

if ($countResult === false) {
    $errorMsg = "Terjadi kesalahan saat mengeksekusi countQuery:";
    if (($errors = sqlsrv_errors()) != null) {
        foreach ($errors as $error) {
            $errorMsg .= " SQLSTATE: " . $error['SQLSTATE'];
            $errorMsg .= " Code: " . $error['code'];
            $errorMsg .= " Message: " . $error['message'];
        }
    }
    die($errorMsg);
}

$totalRecords = sqlsrv_fetch_array($countResult, SQLSRV_FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $rowsPerPage);

$query = "SELECT s.id_sidang, s.judul, s.jenis_sidang, m.nama_matkul, ds.id_matkul,
    CASE 
        WHEN s.jenis_sidang = 0 THEN (
            SELECT d1.nama_dosen 
            FROM Penjadwalan p1 
            JOIN Dosen d1 ON d1.nomor_dosen = p1.nomor_dosen 
            WHERE p1.id_sidang = s.id_sidang AND p1.peran_dosen = 1
        )
        WHEN s.jenis_sidang = 1 THEN (
            SELECT TOP 1 d2.nama_dosen 
            FROM Pengampu_Kelas pk2 
            JOIN Dosen d2 ON d2.nomor_dosen = pk2.nomor_dosen 
            WHERE pk2.id_matkul = ds.id_matkul
        )
        ELSE NULL
    END AS nama_dosen
FROM Sidang s
JOIN Kelompok_Mahasiswa km ON s.id_kelompok = km.id_kelompok
JOIN Detail_Sidang ds ON ds.id_sidang = s.id_sidang 
JOIN MataKuliah m ON ds.id_matkul = m.id_matkul 
WHERE km.nim = ?";

if ($filter === 'ta') {
    $query .= " AND s.jenis_sidang = 0";
} elseif ($filter === 'semester') {
    $query .= " AND s.jenis_sidang = 1";
}

$query .= " GROUP BY s.id_sidang, s.judul, s.jenis_sidang, ds.id_matkul, m.nama_matkul ORDER BY s.id_sidang";

$query .= " OFFSET " . (($currentPage - 1) * $rowsPerPage) . " ROWS FETCH NEXT " . $rowsPerPage . " ROWS ONLY";

$result = sqlsrv_query($conn, $query, array($nim_login));

if ($result === false) {
    $errorMsg = "Terjadi kesalahan saat mengeksekusi main query:";
    if (($errors = sqlsrv_errors()) != null) {
        foreach ($errors as $error) {
            $errorMsg .= " SQLSTATE: " . $error['SQLSTATE'];
            $errorMsg .= " Code: " . $error['code'];
            $errorMsg .= " Message: " . $error['message'];
        }
    }
    die($errorMsg);
}

$rows = [];
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $row['dosen'] = $row['nama_dosen'] ?? '-';
    $rows[] = $row;
} 