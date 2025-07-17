<?php
session_start();
include '../../koneksi/koneksiAndrew.php';
header('Content-Type: application/json');

// Ambil parameter action 
$action = $_GET['action'] ?? '';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Not authorized']);
    exit();
}

// Validasi action parameter
switch($action) {
    // Card Penjadwalan
    case 'penjadwalan':
        
        // Query untuk mengambil data sidang yang belum terjadwal
        $query = "SELECT judul FROM View_aPerluPenjadwalan ORDER BY id_sidang ASC";
        $stmt = sqlsrv_query($conn, $query);
        
        if ($stmt === false) {
            echo json_encode(['error' => sqlsrv_errors()]);
            exit();
        }
        
        $sidangBelumTerjadwal = [];
        $jumlah = 0;
        
        // Ambil semua data dan menghitung jumlahnya
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $sidangBelumTerjadwal[] = $row;
            $jumlah++;
        }
        

        $response = [
            "jumlah" => $jumlah,
            "data" => $sidangBelumTerjadwal
        ];
        
        echo json_encode($response);
        break;

    // Card Pengajuan
    case 'pengajuan':
       
        // Query untuk menghitung jumlah pengajuan yang perlu aksi
        $query = "SELECT COUNT(*) AS jumlah_pengajuan_perlu_aksi FROM Sidang WHERE status_ajuan = 'Pending'";
        $stmt = sqlsrv_query($conn, $query);
        
        if ($stmt === false) {
            echo json_encode(['error' => sqlsrv_errors()]);
            exit();
        }
        
        $jumlah = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        echo json_encode($jumlah);
        break;
 // Card Sidang Mendatang
    case 'sidang_mendatang':
       
        $query = "SELECT tanggal_sidang, judul FROM View_SidangMendatang ORDER BY tanggal_sidang ASC";
        $stmt = sqlsrv_query($conn, $query);
        
        if ($stmt === false) {
            echo json_encode(['error' => sqlsrv_errors()]);
            exit();
        }
        
        $sidang = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
             // Jika sudah DateTime, gunakan format langsung
            if ($row['tanggal_sidang'] instanceof DateTime) {
                $row['tanggal_sidang'] = $row['tanggal_sidang']->format('Y-m-d');
            } else {
                // Jika masih string, konversi ke DateTime
                $row['tanggal_sidang'] = date('Y-m-d', strtotime($row['tanggal_sidang']));
            }
            $sidang[] = $row;
        }
        echo json_encode($sidang);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?> 