<?php
session_start();
include '../../koneksi/koneksiAndrew.php';
header('Content-Type: application/json');

// Get the action parameter
$action = $_GET['action'] ?? '';

// Check if user is logged in and is dosen
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    echo json_encode(['error' => 'Not authorized']);
    exit();
}

// Get nomor_dosen from session
if (!isset($_SESSION['user_data']['nomor_dosen'])) {
    echo json_encode(['error' => 'Dosen data not found']);
    exit();
}
$nomor_dosen = $_SESSION['user_data']['nomor_dosen'];

switch($action) {
    case 'pengajuan':
        // Filter pengajuan by dosen's nomor_dosen
        $sqlPengajuan = "SELECT COUNT(*) AS total FROM Sidang WHERE status_ajuan = 0x00 AND nomor_dosen = ?";
        $stmtPengajuan = sqlsrv_query($conn, $sqlPengajuan, [$nomor_dosen]);
        if ($stmtPengajuan === false) {
            echo json_encode(['error' => sqlsrv_errors()]);
            exit();
        }
        $jumlahPengajuan = ($stmtPengajuan && $row = sqlsrv_fetch_array($stmtPengajuan)) ? $row['total'] : 0;
        echo json_encode(['total' => $jumlahPengajuan]);
        break;

    case 'perbaikan':
        // Filter perbaikan by dosen's nomor_dosen
        $sqlPerbaikan = "SELECT COUNT(*) AS total FROM Detail_Sidang WHERE (status_revisi IS NULL /*OR status_revisi = 'pending'*/) AND nomor_dosen = ?";
        $stmtPerbaikan = sqlsrv_query($conn, $sqlPerbaikan, [$nomor_dosen]);
        if ($stmtPerbaikan === false) {
            echo json_encode(['error' => sqlsrv_errors()]);
            exit();
        }
        $jumlahPerbaikan = ($stmtPerbaikan && $row = sqlsrv_fetch_array($stmtPerbaikan)) ? $row['total'] : 0;
        echo json_encode(['total' => $jumlahPerbaikan]);
        break;

    case 'penilaian':
        // Filter penilaian by dosen's nomor_dosen
        $sqlPenilaian = "SELECT COUNT(*) AS total FROM Penilaian WHERE (n_dokumen IS NULL OR n_presentasi IS NULL OR n_tanyajawab IS NULL OR n_proyek IS NULL) AND nomor_dosen = ?";
        $stmtPenilaian = sqlsrv_query($conn, $sqlPenilaian, [$nomor_dosen]);
        if ($stmtPenilaian === false) {
            echo json_encode(['error' => sqlsrv_errors()]);
            exit();
        }
        $jumlahPenilaian = ($stmtPenilaian && $row = sqlsrv_fetch_array($stmtPenilaian)) ? $row['total'] : 0;
        echo json_encode(['total' => $jumlahPenilaian]);
        break;

    case 'sidang_mendatang':
        // Filter sidang mendatang by dosen's nomor_dosen
        $query = "SELECT s.id_sidang, s.judul, j.tanggal_sidang
        FROM Sidang s
        JOIN Jadwal j ON s.id_sidang = j.id_sidang
        JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
        WHERE ds.nomor_dosen = ?
        AND s.status_sidang = 1
        AND j.tanggal_sidang > GETDATE()
        ORDER BY j.tanggal_sidang ASC;";
        $stmt = sqlsrv_query($conn, $query, [$nomor_dosen]);
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