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
        $sql = "
            SELECT COUNT(*) AS sidang_berlangsung
            FROM Sidang s
            JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
            JOIN Kelompok k2 ON k2.nomor_kelompok = k.nomor_kelompok
                AND k2.jenis_sidang = k.jenis_sidang
                AND k2.tahun_ajaran = k.tahun_ajaran
                AND k2.id_matkul = k.id_matkul
            WHERE k2.nim = ?
            AND s.status_sidang = 0x01;
        ";
        $params = array($nim);
        $stmt = sqlsrv_query($conn, $sql, $params);
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        echo json_encode(['sidang_berlangsung' => $row['sidang_berlangsung']]);
        break;

    case 'penilaian_status':
        // ✅ DIPERBAIKI: Mencari perwakilan id_kelompok dengan JOIN yang konsisten
        $query = "SELECT COUNT(DISTINCT s.id_sidang) AS menunggu_penilaian
        FROM Sidang s
        JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
        JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
        JOIN Kelompok k2 ON k2.nomor_kelompok = k.nomor_kelompok
            AND k2.jenis_sidang = k.jenis_sidang
            AND k2.tahun_ajaran = k.tahun_ajaran
            AND k2.id_matkul = k.id_matkul
        WHERE k2.nim = ?
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
        // ✅ DIPERBAIKI: Mencari perwakilan id_kelompok dengan JOIN yang konsisten
        $query = "SELECT DISTINCT s.id_sidang, s.judul
        FROM Sidang s
        JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
        JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
        JOIN Kelompok k2 ON k2.nomor_kelompok = k.nomor_kelompok
            AND k2.jenis_sidang = k.jenis_sidang
            AND k2.tahun_ajaran = k.tahun_ajaran
            AND k2.id_matkul = k.id_matkul
        WHERE k2.nim = ?
        AND s.status_sidang = 1
        AND ds.dok_revisi IS NULL
        AND ds.catatan_sidang IS NOT NULL
        AND ds.catatan_sidang != '';";
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
        $sql = "
            SELECT s.id_sidang, s.judul, j.tanggal_sidang
            FROM Sidang s
            JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
            JOIN Kelompok k2 ON k2.nomor_kelompok = k.nomor_kelompok
                AND k2.jenis_sidang = k.jenis_sidang
                AND k2.tahun_ajaran = k.tahun_ajaran
                AND k2.id_matkul = k.id_matkul
            JOIN Jadwal j ON j.id_sidang = s.id_sidang
            WHERE k2.nim = ?
            AND s.status_sidang = 0x01
            AND j.tanggal_sidang > CAST(GETDATE() AS DATE)
            ORDER BY j.tanggal_sidang ASC;
        ";
        $params = array($nim);
        $stmt = sqlsrv_query($conn, $sql, $params);
        $sidang_mendatang = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            // Format the date to Y-m-d format for JavaScript compatibility
            if ($row['tanggal_sidang'] instanceof DateTime) {
                $row['tanggal_sidang'] = $row['tanggal_sidang']->format('Y-m-d');
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