<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Include the database connection
// The path is relative to this file's location in `control/mahasiswa/`
require_once '../../koneksi/koneksiAndrew.php';

// 2. Set the content type to JSON for the response
header('Content-Type: application/json');

// 3. Security Check: Ensure the user is a logged-in mahasiswa
if (!isset($_SESSION['is_logged_in']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa' || !isset($_SESSION['nim'])) {
    // If not, send an error response and stop execution
    http_response_code(403); // Forbidden
    echo json_encode(['error' => 'Akses ditolak. Anda harus login sebagai mahasiswa.']);
    exit();
}

// 4. Get the logged-in student's NIM from the session
$nim = $_SESSION['nim'];

// 5. Get the requested action from the URL (e.g., ?action=sidang_status)
$action = isset($_GET['action']) ? $_GET['action'] : '';

// 6. Use a switch statement to execute the correct query based on the action
switch ($action) {

    // --- CASE 1: Get count of "Sidang Berlangsung" ---
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
            AND s.status_sidang = 0x01
            AND EXISTS (
                SELECT 1
                FROM Jadwal j
                WHERE j.id_sidang = s.id_sidang
                AND j.tanggal_sidang = CAST(GETDATE() AS DATE)
            );
        ";
        $params = array($nim);
        $stmt = sqlsrv_query($conn, $sql, $params);
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        echo json_encode($row ? $row : ['sidang_berlangsung' => 0]);
        break;

    // --- CASE 2: Get count of items "Menunggu Penilaian" ---
    case 'penilaian_status':
        $sql = "
            SELECT COUNT(DISTINCT s.id_sidang) AS menunggu_penilaian
            FROM Sidang s
            JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
            JOIN Kelompok k2 ON k2.nomor_kelompok = k.nomor_kelompok
                AND k2.jenis_sidang = k.jenis_sidang
                AND k2.tahun_ajaran = k.tahun_ajaran
                AND k2.id_matkul = k.id_matkul
            JOIN Detail_Sidang ds ON ds.id_sidang = s.id_sidang
            WHERE k2.nim = ?
            AND s.status_sidang = 0x01
            AND ds.status_revisi != 'Approved';
        ";
        $params = array($nim);
        $stmt = sqlsrv_query($conn, $sql, $params);
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        echo json_encode($row ? $row : ['menunggu_penilaian' => 0]);
        break;

    // --- CASE 3: Get list of "Tanggungan" (revisions needed) ---
    case 'tanggungan':
        $sql = "
            SELECT DISTINCT s.id_sidang, s.Judul
            FROM Sidang s
            JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
            JOIN Kelompok k2 ON k2.nomor_kelompok = k.nomor_kelompok
                AND k2.jenis_sidang = k.jenis_sidang
                AND k2.tahun_ajaran = k.tahun_ajaran
                AND k2.id_matkul = k.id_matkul
            JOIN Detail_Sidang ds ON ds.id_sidang = s.id_sidang
            WHERE k2.nim = ?
            AND s.status_sidang = 0x01
            AND ds.dok_revisi IS NULL;
        ";
        $params = array($nim);
        $stmt = sqlsrv_query($conn, $sql, $params);
        $tanggungan = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $tanggungan[] = $row;
        }
        echo json_encode(['tanggungan' => $tanggungan]);
        break;

    // --- CASE 4: Get list of "Sidang Mendatang" ---
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
            AND j.tanggal_sidang >= DATEADD(DAY, 1, CAST(GETDATE() AS DATE))
            ORDER BY j.tanggal_sidang ASC;
        ";
        $params = array($nim);
        $stmt = sqlsrv_query($conn, $sql, $params);
        $sidang_mendatang = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            // Format the date to an ISO 8601 string, which is easily parsed by JavaScript's `new Date()`
            if ($row['tanggal_sidang'] instanceof DateTime) {
                $row['tanggal_sidang'] = $row['tanggal_sidang']->format('Y-m-d\TH:i:s');
            }
            $sidang_mendatang[] = $row;
        }
        echo json_encode(['sidang_mendatang' => $sidang_mendatang]);
        break;

    // --- DEFAULT: Handle invalid actions ---
    default:
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Aksi tidak valid.']);
        break;
}

// 7. Close the database connection
sqlsrv_close($conn);
?>