<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../koneksi/koneksiAndrew.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $jenis_sidang = $_POST['jenis_sidang'] ?? '';
    $id_matkul = $_POST['id_matkul'] ?? '';
    $tahun_ajaran = $_POST['tahun_ajaran'] ?? date('Y'); // Auto-filled current year
    $dosen_nomor_array = isset($_POST['dosen_nomor_hidden']) ? $_POST['dosen_nomor_hidden'] : [];
    $nomor_dosen = isset($_SESSION['user_data']['nomor_dosen']) ? $_SESSION['user_data']['nomor_dosen'] : null;
    $anggota_nims = $_POST['anggota_nim'] ?? [];

    // Validation
    if (empty($jenis_sidang) || empty($id_matkul) || empty($tahun_ajaran)) {
        echo json_encode(['success' => false, 'message' => 'Jenis sidang, mata kuliah, dan tahun ajaran harus diisi.']);
        exit();
    }

    if (empty($anggota_nims)) {
        echo json_encode(['success' => false, 'message' => 'Minimal harus ada satu anggota kelompok.']);
        exit();
    }

    // Ensure the creator dosen is always included for Tugas Akhir
    if ($jenis_sidang === 'Tugas Akhir' && $nomor_dosen) {
        $dosen_nomor_array = array_filter($dosen_nomor_array, function($value) {
            return trim($value) !== '';
        });
        
        if (!in_array($nomor_dosen, $dosen_nomor_array)) {
            $dosen_nomor_array[] = $nomor_dosen;
        }
    }

    // Validate unique group conditions
    foreach ($anggota_nims as $nim) {
        if (trim($nim) !== '') {
            // Check if student already belongs to a group with same tahun_ajaran + jenis_sidang + id_matkul
            $sql_check = "SELECT COUNT(*) as count FROM Kelompok 
                         WHERE nim = ? AND tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ?";
            $check_result = sqlsrv_query($conn, $sql_check, [$nim, $tahun_ajaran, $jenis_sidang, $id_matkul]);
            
            if ($check_result && sqlsrv_fetch_array($check_result, SQLSRV_FETCH_ASSOC)['count'] > 0) {
                echo json_encode(['success' => false, 'message' => "Mahasiswa dengan NIM $nim sudah terdaftar dalam kelompok untuk tahun ajaran $tahun_ajaran, jenis sidang $jenis_sidang, dan mata kuliah yang dipilih."]);
                exit();
            }
        }
    }

    // Get next nomor_kelompok for this combination
    $sql_next_nomor = "SELECT ISNULL(MAX(nomor_kelompok), 0) + 1 AS next_nomor 
                       FROM Kelompok 
                       WHERE tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ?";
    $next_result = sqlsrv_query($conn, $sql_next_nomor, [$tahun_ajaran, $jenis_sidang, $id_matkul]);
    
    if (!$next_result) {
        echo json_encode(['success' => false, 'message' => 'Gagal mendapatkan nomor kelompok berikutnya.']);
        exit();
    }
    
    $nomor_kelompok = sqlsrv_fetch_array($next_result, SQLSRV_FETCH_ASSOC)['next_nomor'];

    // Begin transaction
    sqlsrv_begin_transaction($conn);

    try {
        // 1. Insert anggota into Kelompok (one record per student)
        foreach ($anggota_nims as $nim) {
            if (trim($nim) !== '') {
                $sql_anggota = "INSERT INTO Kelompok (nomor_kelompok, nim, tahun_ajaran, jenis_sidang, id_matkul) 
                               VALUES (?, ?, ?, ?, ?)";
                $stmt_anggota = sqlsrv_query($conn, $sql_anggota, [$nomor_kelompok, $nim, $tahun_ajaran, $jenis_sidang, $id_matkul]);
                
                if (!$stmt_anggota) {
                    throw new Exception("Gagal menambahkan anggota kelompok: " . print_r(sqlsrv_errors(), true));
                }
            }
        }

        // 2. Insert into Bimbingan for Tugas Akhir only
        if ($jenis_sidang === 'Tugas Akhir' && !empty($dosen_nomor_array)) {
            foreach ($dosen_nomor_array as $dosen_nomor) {
                if (trim($dosen_nomor) !== '') {
                    // Get all id_kelompok for this group to create Bimbingan records
                    $sql_get_ids = "SELECT id_kelompok FROM Kelompok 
                                   WHERE nomor_kelompok = ? AND tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ?";
                    $ids_result = sqlsrv_query($conn, $sql_get_ids, [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul]);
                    
                    while ($row = sqlsrv_fetch_array($ids_result, SQLSRV_FETCH_ASSOC)) {
                        $sql_bimbingan = "INSERT INTO Bimbingan (id_kelompok, nomor_dosen, isPembimbing) 
                                         VALUES (?, ?, 1)";
                        $stmt_bimbingan = sqlsrv_query($conn, $sql_bimbingan, [$row['id_kelompok'], $dosen_nomor]);
                        
                        if (!$stmt_bimbingan) {
                            throw new Exception("Gagal menambahkan dosen pembimbing: " . print_r(sqlsrv_errors(), true));
                        }
                    }
                }
            }
        }

        sqlsrv_commit($conn);
        echo json_encode(['success' => true, 'message' => 'Kelompok berhasil dibuat!', 'nomor_kelompok' => $nomor_kelompok]);
        
    } catch (Exception $e) {
        sqlsrv_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    
    exit();
}
?> 