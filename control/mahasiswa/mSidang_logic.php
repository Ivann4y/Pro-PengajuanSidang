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

$countQuery = "SELECT DISTINCT
    COUNT(S.judul) AS total
FROM
    Mahasiswa AS M
JOIN
    Kelompok AS K_mhs ON M.nim = K_mhs.nim
JOIN
    Kelompok AS K_sidang ON K_mhs.nomor_kelompok = K_sidang.nomor_kelompok AND K_sidang.nim = M.nim
JOIN
    Sidang AS S ON K_sidang.id_kelompok = S.id_kelompok 
WHERE
    M.nim = ? AND S.status_ajuan = 'Approved'";

if ($filter === 'ta') {
    $countQuery .= " AND K_mhs.jenis_sidang = 'Tugas Akhir'";
} elseif ($filter === 'semester') {
    $countQuery .= " AND K_mhs.jenis_sidang = 'Semester'";
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

$query = "SELECT DISTINCT S.id_sidang, S.judul, mk.nama_matkul, K_mhs.jenis_sidang, 
	CASE 
        WHEN K_mhs.jenis_sidang = 'Tugas Akhir' THEN (
            SELECT d1.nama_dosen 
            FROM Penjadwalan p1 
            JOIN Dosen d1 ON d1.nomor_dosen = p1.nomor_dosen 
            WHERE p1.id_sidang = S.id_sidang AND p1.peran_dosen = 1
        )
        WHEN K_mhs.jenis_sidang = 'Semester' THEN (
            SELECT TOP 1 d2.nama_dosen
			FROM Pengampu_Kelas pk2
			JOIN Dosen d2 ON d2.nomor_dosen = pk2.nomor_dosen
			WHERE pk2.id_matkul = K_mhs.id_matkul
        )
        ELSE NULL
    END AS nama_dosen
FROM
    Mahasiswa AS M
JOIN
    Kelompok AS K_mhs ON M.nim = K_mhs.nim
JOIN
    Kelompok AS K_sidang ON K_mhs.nomor_kelompok = K_sidang.nomor_kelompok AND K_sidang.nim = M.nim
JOIN
    Sidang AS S ON K_sidang.id_kelompok = S.id_kelompok 
JOIN 
	MataKuliah AS mk ON mk.id_matkul = K_mhs.id_matkul
WHERE
    M.nim = ? AND S.status_ajuan = 'Approved'";

if ($filter === 'ta') {
    $query .= " AND K_mhs.jenis_sidang = 'Tugas Akhir'";
} elseif ($filter === 'semester') {
    $query .= " AND K_mhs.jenis_sidang = 'Semester'";
}

$query .= " GROUP BY S.id_sidang, S.judul, K_mhs.jenis_sidang, K_mhs.id_matkul, mk.nama_matkul ORDER BY S.id_sidang";

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