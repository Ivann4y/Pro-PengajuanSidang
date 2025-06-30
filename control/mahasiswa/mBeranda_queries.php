<?php
session_start();
include '../../koneksi/koneksiAndrew.php';
header('Content-Type: application/json');

// Get the action parameter
$action = $_GET['action'] ?? '';

// Check if user is logged in and is mahasiswa
if (!isset($_SESSION['nim'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}
$nim = $_SESSION['nim'];

switch($action) {
    case 'sidang_status':
        // Original code from mBeranda_sidang_status.php
        $query = "SELECT COUNT(*) AS sidang_berlangsung
        FROM Sidang s
        JOIN Jadwal j ON s.id_sidang = j.id_sidang
        WHERE s.id_kelompok IN (
            SELECT id_kelompok FROM Kelompok_Mahasiswa WHERE nim = ?
        )
        AND s.status_sidang = 1
        AND j.tanggal_sidang > GETDATE();";
        $params = [$nim];
        $stmt = sqlsrv_query($conn, $query, $params);
        if ($stmt === false) {
            echo json_encode(['error' => sqlsrv_errors()]);
            exit();
        }
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        echo json_encode(['sidang_berlangsung' => $row['sidang_berlangsung']]);
        break;

    case 'penilaian_status':
        // Original code from mBeranda_penilaian_status.php
        $query = "SELECT COUNT(DISTINCT s.id_sidang) AS menunggu_penilaian
        FROM Sidang s
        JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
        WHERE s.id_kelompok IN (
            SELECT id_kelompok FROM Kelompok_Mahasiswa WHERE nim = ?
        )
        AND s.status_sidang = 1
        AND ds.status_revisi != 1;";
        $params = [$nim];
        $stmt = sqlsrv_query($conn, $query, $params);
        if ($stmt === false) {
            echo json_encode(['error' => sqlsrv_errors()]);
            exit();
        }
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        echo json_encode(['menunggu_penilaian' => $row['menunggu_penilaian']]);
        break;

    case 'tanggungan':
        // Original code from mBeranda_tanggungan.php
        $query = "SELECT DISTINCT s.id_sidang, s.judul
        FROM Sidang s
        JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
        WHERE s.id_kelompok IN (
            SELECT id_kelompok FROM Kelompok_Mahasiswa WHERE nim = ?
        )
        AND s.status_sidang = 1
        AND ds.dok_revisi IS NULL;";
        $params = [$nim];
        $stmt = sqlsrv_query($conn, $query, $params);
        if ($stmt === false) {
            echo json_encode(['error' => sqlsrv_errors()]);
            exit();
        }
        $tanggungan = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $tanggungan[] = [
                'id_sidang' => $row['id_sidang'],
                'judul' => $row['judul']
            ];
        }
        echo json_encode(['tanggungan' => $tanggungan]);
        break;

    case 'sidang_mendatang':
        // Original code from mBeranda_sidang_mendatang.php
        $query = "SELECT s.id_sidang, s.judul, j.tanggal_sidang
        FROM Sidang s
        JOIN Jadwal j ON s.id_sidang = j.id_sidang
        WHERE s.id_kelompok IN (
            SELECT id_kelompok FROM Kelompok_Mahasiswa WHERE nim = ?
        )
        AND s.status_sidang = 1
        AND j.tanggal_sidang > GETDATE()
        ORDER BY j.tanggal_sidang ASC;";
        $params = [$nim];
        $stmt = sqlsrv_query($conn, $query, $params);
        if ($stmt === false) {
            echo json_encode(['error' => sqlsrv_errors()]);
            exit();
        }
        $sidang_mendatang = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            // Format tanggal_sidang as Y-m-d
            if ($row['tanggal_sidang'] instanceof DateTime) {
                $row['tanggal_sidang'] = $row['tanggal_sidang']->format('Y-m-d');
            } else {
                $row['tanggal_sidang'] = date('Y-m-d', strtotime($row['tanggal_sidang']));
            }
            $sidang_mendatang[] = [
                'id_sidang' => $row['id_sidang'],
                'judul' => $row['judul'],
                'tanggal_sidang' => $row['tanggal_sidang']
            ];
        }
        echo json_encode(['sidang_mendatang' => $sidang_mendatang]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?> 