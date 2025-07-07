<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include '../koneksi/koneksiAndrew.php';
error_log('EDIT POST: ' . json_encode($_POST));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']); exit();
}

// Pastikan hanya dosen yang boleh edit kelompok
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']); exit();
}

$current_dosen = $_SESSION['user_data']['nomor_dosen'] ?? null;
if ($current_dosen === null) {
    echo json_encode(['status' => 'error', 'message' => 'Nomor dosen tidak ditemukan di session.']); exit();
}

// Get data from form
$nomor_kelompok = $_POST['nomor_kelompok'] ?? null;
$tahun_ajaran = $_POST['tahun_ajaran'] ?? null;
$jenis_sidang = $_POST['jenis_sidang'] ?? null;
$id_matkul = $_POST['id_matkul'] ?? null;
$anggota = $_POST['anggota'] ?? $_POST['anggota_nim'] ?? [];
$pembimbing_input = $_POST['nomor_dosen'] ?? [];

// Debug: Log all POST data and anggota specifically
error_log("All POST data: " . json_encode($_POST));
error_log("Anggota from POST: " . json_encode($anggota));
error_log("Anggota type: " . gettype($anggota));

// Debug: Check what NIMs exist in database
$sql_debug = "SELECT TOP 10 nim FROM Mahasiswa ORDER BY nim";
$stmt_debug = sqlsrv_query($conn, $sql_debug);
$existing_nims = [];
while ($stmt_debug && $row = sqlsrv_fetch_array($stmt_debug, SQLSRV_FETCH_ASSOC)) {
    $existing_nims[] = $row['nim'];
}
error_log("Sample NIMs in database: " . json_encode($existing_nims));

// Ensure anggota is unique and clean
$anggota = array_unique(array_filter($anggota));
error_log("Cleaned anggota array: " . json_encode($anggota));

// Debug logging
error_log("Edit kelompok POST data: " . json_encode($_POST));

// Validation
if (!$nomor_kelompok || !$tahun_ajaran || !$jenis_sidang || !$id_matkul || empty($anggota)) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']); exit();
}

// Set id_matkul based on jenis_sidang FIRST
if ($jenis_sidang === 'Tugas Akhir') {
    $id_matkul = 2006;
}

if ($jenis_sidang === 'Semester') {
    if (!$id_matkul) {
        echo json_encode(['status'=>'error','message'=>'Mata kuliah harus dipilih untuk Sidang Semester']); exit();
    }
    $sql = "SELECT 1 FROM Pengampu_Kelas WHERE nomor_dosen = ? AND id_matkul = ? AND tahun_ajaran = ?";
    $stmt = sqlsrv_query($conn, $sql, [$current_dosen, $id_matkul, $tahun_ajaran]);
    if (!$stmt || !sqlsrv_fetch($stmt)) {
        echo json_encode(['status'=>'error','message'=>'Anda bukan pengampu mata kuliah ini di tahun ajaran ini']); exit();
    }
}

// Lock check: cannot edit if group has Approved Pengajuan (AFTER id_matkul is set)
$sql = "SELECT 1 FROM Kelompok k
         JOIN Sidang s ON k.id_kelompok = s.id_kelompok
        WHERE k.nomor_kelompok = ? AND k.tahun_ajaran = ? AND k.jenis_sidang = ? AND k.id_matkul = ?
          AND s.status_ajuan = 'Approved'";
$stmt = sqlsrv_query($conn, $sql, [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul]);
if ($stmt && sqlsrv_fetch($stmt)) {
    echo json_encode(['status' => 'error', 'message' => 'Kelompok terkunci: Pengajuan sudah Approved.']); exit();
}

// Get current anggota
$current_anggota = [];
$id_kelompok_map = [];
error_log("Getting current anggota with params: nomor_kelompok=$nomor_kelompok, tahun_ajaran=$tahun_ajaran, jenis_sidang=$jenis_sidang, id_matkul=$id_matkul");

// Debug: Check if kelompok exists
$sql_debug_kelompok = "SELECT COUNT(*) as total FROM Kelompok WHERE nomor_kelompok = ? AND tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ?";
$stmt_debug = sqlsrv_query($conn, $sql_debug_kelompok, [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul]);
if ($stmt_debug && $row = sqlsrv_fetch_array($stmt_debug, SQLSRV_FETCH_ASSOC)) {
    error_log("Total anggota in kelompok: " . $row['total']);
}

// Debug: Check all kelompok with same nomor_kelompok, tahun_ajaran, jenis_sidang
$sql_debug_all = "SELECT id_matkul, COUNT(*) as total FROM Kelompok WHERE nomor_kelompok = ? AND tahun_ajaran = ? AND jenis_sidang = ? GROUP BY id_matkul";
$stmt_debug_all = sqlsrv_query($conn, $sql_debug_all, [$nomor_kelompok, $tahun_ajaran, $jenis_sidang]);
while ($stmt_debug_all && $row = sqlsrv_fetch_array($stmt_debug_all, SQLSRV_FETCH_ASSOC)) {
    error_log("Kelompok with id_matkul " . $row['id_matkul'] . ": " . $row['total'] . " anggota");
}

$sql = "SELECT nim, id_kelompok FROM Kelompok WHERE nomor_kelompok = ? AND tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ?";
$stmt = sqlsrv_query($conn, $sql, [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul]);

if (!$stmt) {
    error_log("Error querying current anggota: " . print_r(sqlsrv_errors(), true));
    echo json_encode(['status' => 'error', 'message' => 'Error saat mengambil data anggota saat ini']); 
    exit();
}

while ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $current_anggota[] = $row['nim'];
    $id_kelompok_map[strval($row['nim'])] = $row['id_kelompok'];
    error_log("Added to map: nim=" . strval($row['nim']) . ", id_kelompok={$row['id_kelompok']}");
}
error_log("Current anggota from database: " . json_encode($current_anggota));
error_log("ID Kelompok map: " . json_encode($id_kelompok_map));
// Convert anggota to strings for comparison (ensure consistent type)
    $anggota_int = array_map('strval', $anggota);
    $current_anggota_int = array_map('strval', $current_anggota);
    
    $to_add = array_diff($anggota_int, $current_anggota_int);
    $to_remove = array_diff($current_anggota_int, $anggota_int);

// Debug logging after variables are defined
error_log("Anggota to add: " . json_encode($to_add));
error_log("Anggota to remove: " . json_encode($to_remove));
error_log("Current anggota: " . json_encode($current_anggota));
error_log("Anggota: " . json_encode($anggota_int));
error_log("Current anggota: " . json_encode($current_anggota_int));

// Debug: Check if NIMs in to_remove exist in id_kelompok_map
foreach ($to_remove as $nim_remove) {
    if (!isset($id_kelompok_map[$nim_remove])) {
        error_log("WARNING: NIM {$nim_remove} not found in id_kelompok_map!");
    } else {
        error_log("OK: NIM {$nim_remove} found in id_kelompok_map with id_kelompok {$id_kelompok_map[$nim_remove]}");
    }
}
error_log("Processing edit for kelompok: {$nomor_kelompok}, tahun: {$tahun_ajaran}, jenis: {$jenis_sidang}, matkul: {$id_matkul}");

// Log the edit operation summary
if (empty($to_add) && empty($to_remove)) {
    error_log("No changes needed - all anggota remain the same");
} else {
    if (!empty($to_add)) {
        error_log("Will add " . count($to_add) . " new anggota: " . json_encode($to_add));
    }
    if (!empty($to_remove)) {
        error_log("Will remove " . count($to_remove) . " anggota: " . json_encode($to_remove));
    }
}

// Pembimbing list
$pembimbing_list = [];
if ($jenis_sidang === 'Tugas Akhir') {
    if (!$current_dosen) {
        echo json_encode(['status'=>'error','message'=>'Session dosen tidak valid']); exit();
    }
    $pembimbing_list = is_array($pembimbing_input) ? $pembimbing_input : (empty($pembimbing_input) ? [] : [$pembimbing_input]);
    if (!in_array($current_dosen, $pembimbing_list)) {
        $pembimbing_list[] = $current_dosen;
    }
    
    // Validate all pembimbing exist
    foreach ($pembimbing_list as $nd) {
        $sql = "SELECT 1 FROM Dosen WHERE nomor_dosen = ?";
        $stmt = sqlsrv_query($conn, $sql, [$nd]);
        if (!$stmt || !sqlsrv_fetch($stmt)) {
            echo json_encode(['status'=>'error','message'=>"Dosen $nd tidak ditemukan"]); exit();
        }
    }
}

try {
    sqlsrv_begin_transaction($conn);
    $success = true;
    
    // Remove anggota
    foreach ($to_remove as $nim_remove) {
        $id_kelompok = $id_kelompok_map[$nim_remove] ?? null;
        
        if (!$id_kelompok) {
            error_log("ERROR: No id_kelompok found for removed anggota {$nim_remove}");
            error_log("Available id_kelompok_map: " . json_encode($id_kelompok_map));
            $success = false;
            break;
        }
        
        error_log("Removing anggota {$nim_remove} with id_kelompok {$id_kelompok}");
        
        // Delete Bimbingan hanya untuk Tugas Akhir
        if ($jenis_sidang === 'Tugas Akhir') {
            $sql = "DELETE FROM Bimbingan WHERE id_kelompok = ?";
            if (!sqlsrv_query($conn, $sql, [$id_kelompok])) { 
                error_log("ERROR: Failed to delete Bimbingan for id_kelompok {$id_kelompok}");
                $success = false; 
                break; 
            }
            error_log("Deleted Bimbingan for removed Tugas Akhir anggota {$nim_remove} with id_kelompok {$id_kelompok}");
        } else {
            error_log("Skipping Bimbingan delete for removed Semester anggota {$nim_remove} with id_kelompok {$id_kelompok}");
        }
        
        $sql = "DELETE FROM Kelompok WHERE id_kelompok = ?";
        if (!sqlsrv_query($conn, $sql, [$id_kelompok])) { 
            error_log("ERROR: Failed to delete Kelompok for id_kelompok {$id_kelompok}");
            $success = false; 
            break; 
        }
        
        error_log("Successfully deleted Kelompok for removed anggota {$nim_remove} with id_kelompok {$id_kelompok}");
    }
    
    // Validate all anggota (existing + new) in one query
    if (!empty($anggota_int)) {
        // Simple validation: check if all NIMs exist in Mahasiswa table
        $placeholders = str_repeat('?,', count($anggota_int) - 1) . '?';
        $sql = "SELECT m.nim, m.nama_mhs 
                FROM Mahasiswa m 
                WHERE m.nim IN ($placeholders)";
        
        $stmt = sqlsrv_query($conn, $sql, $anggota_int);
        
        if (!$stmt) {
            error_log("Error validating anggota: " . print_r(sqlsrv_errors(), true));
            echo json_encode(['status' => 'error', 'message' => 'Error saat validasi anggota']); 
            sqlsrv_rollback($conn);
            exit();
        }
        
        $valid_nims = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $valid_nims[] = $row['nim'];
        }
        
        // Check for invalid NIMs
        $invalid_nims = array_diff($anggota_int, $valid_nims);
        if (!empty($invalid_nims)) {
            $invalid_list = implode(', ', $invalid_nims);
            error_log("Invalid NIMs found: $invalid_list");
            echo json_encode(['status' => 'error', 'message' => "NIM tidak ditemukan: " . $invalid_list]); 
            sqlsrv_rollback($conn);
            exit();
        }
        
        // Check if NIMs are already in other kelompok (excluding current kelompok)
        foreach ($anggota_int as $nim_check) {
            $sql_check = "SELECT 1 FROM Kelompok 
                         WHERE nim = ? 
                           AND tahun_ajaran = ? 
                           AND jenis_sidang = ? 
                           AND id_matkul = ? 
                           AND nomor_kelompok != ?";
            $stmt_check = sqlsrv_query($conn, $sql_check, [$nim_check, $tahun_ajaran, $jenis_sidang, $id_matkul, $nomor_kelompok]);
            
            if ($stmt_check && sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC)) {
                echo json_encode(['status' => 'error', 'message' => "NIM {$nim_check} sudah terdaftar di kelompok lain"]); 
                sqlsrv_rollback($conn);
                exit();
            }
        }
        
        error_log("All anggota validated successfully: " . json_encode($valid_nims));
    }
    
    // Add new anggota only
    if (!empty($to_add)) {
        error_log("Adding " . count($to_add) . " new anggota: " . json_encode($to_add));
        
        foreach ($to_add as $nim_add) {
            // Insert new anggota with OUTPUT clause to get the inserted id_kelompok
            $sql = "INSERT INTO Kelompok (nomor_kelompok, nim, tahun_ajaran, jenis_sidang, id_matkul) 
                    OUTPUT INSERTED.id_kelompok 
                    VALUES (?, ?, ?, ?, ?)";
            $params = [$nomor_kelompok, $nim_add, $tahun_ajaran, $jenis_sidang, $id_matkul];
            $stmt = sqlsrv_query($conn, $sql, $params);
            if (!$stmt) { 
                $success = false; 
                break; 
            }
            
            // Get the inserted id_kelompok from OUTPUT
            $id_kelompok = null;
            if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $id_kelompok = $row['id_kelompok'];
            }
            $id_kelompok_map[$nim_add] = $id_kelompok;
            
            error_log("Added new anggota {$nim_add} with id_kelompok {$id_kelompok}");
            
            // Add pembimbing hanya untuk Tugas Akhir
            if ($id_kelompok && $jenis_sidang === 'Tugas Akhir') {
                // Untuk Tugas Akhir: gunakan dosen yang sedang login
                $isPembimbing = 1;
                $sql2 = "INSERT INTO Bimbingan (id_kelompok, nomor_dosen, isPembimbing) VALUES (?, ?, ?)";
                if (!sqlsrv_query($conn, $sql2, [$id_kelompok, $current_dosen, $isPembimbing])) { 
                    $success = false; 
                    break; 
                }
                error_log("Added pembimbing for new Tugas Akhir anggota {$nim_add} with id_kelompok {$id_kelompok}");
            } else if ($id_kelompok && $jenis_sidang === 'Semester') {
                error_log("Skipping pembimbing insert for new Semester anggota {$nim_add} with id_kelompok {$id_kelompok}");
            }
        }
    }

    // Update pembimbing for retained anggota (hanya untuk Tugas Akhir)
    $retained = array_intersect($anggota_int, $current_anggota_int);
    if (!empty($retained) && $jenis_sidang === 'Tugas Akhir') {
        error_log("Updating pembimbing for " . count($retained) . " retained Tugas Akhir anggota: " . json_encode($retained));
        
        foreach ($retained as $nim) {
            $id_kelompok = $id_kelompok_map[$nim];
            if (!$id_kelompok) {
                error_log("Warning: No id_kelompok found for retained anggota {$nim}");
                continue;
            }
            
            error_log("Updating pembimbing for retained Tugas Akhir anggota {$nim} with id_kelompok {$id_kelompok}");
            
            // For Tugas Akhir: handle multiple pembimbing
            // Remove non-present pembimbing
            if (count($pembimbing_list)) {
                $sql = "DELETE FROM Bimbingan WHERE id_kelompok = ? AND nomor_dosen NOT IN (" . implode(",", array_fill(0, count($pembimbing_list), "?")) . ")";
                $params = array_merge([$id_kelompok], $pembimbing_list);
                if (!sqlsrv_query($conn, $sql, $params)) { 
                    $success = false; 
                    break; 
                }
            }
            
            // Add any new pembimbing
            $sql_exist = "SELECT nomor_dosen FROM Bimbingan WHERE id_kelompok = ?";
            $stmt_exist = sqlsrv_query($conn, $sql_exist, [$id_kelompok]);
            $current_pembimbing = [];
            while ($stmt_exist && $row = sqlsrv_fetch_array($stmt_exist, SQLSRV_FETCH_ASSOC)) {
                $current_pembimbing[] = $row['nomor_dosen'];
            }
            foreach ($pembimbing_list as $nd) {
                if (!in_array($nd, $current_pembimbing)) {
                    $sql2 = "INSERT INTO Bimbingan (id_kelompok, nomor_dosen, isPembimbing) VALUES (?, ?, 1)";
                    if (!sqlsrv_query($conn, $sql2, [$id_kelompok, $nd])) { 
                        $success = false; 
                        break 2; 
                    }
                }
            }
        }
    } else if (!empty($retained) && $jenis_sidang === 'Semester') {
        error_log("Skipping pembimbing update for " . count($retained) . " retained Semester anggota: " . json_encode($retained));
    } else {
        error_log("No retained anggota to update pembimbing for");
    }

    // Delete pembimbing for removed anggota (hanya untuk Tugas Akhir)
    $removed = array_diff($current_anggota_int, $anggota_int);
    if (!empty($removed) && $jenis_sidang === 'Tugas Akhir') {
        error_log("Deleting pembimbing for " . count($removed) . " removed Tugas Akhir anggota: " . json_encode($removed));
        
        foreach ($removed as $nim) {
            $id_kelompok = $id_kelompok_map[$nim];
            if (!$id_kelompok) {
                error_log("Warning: No id_kelompok found for removed anggota {$nim}");
                continue;
            }
            
            error_log("Deleting pembimbing for removed Tugas Akhir anggota {$nim} with id_kelompok {$id_kelompok}");
            
            $sql = "DELETE FROM Bimbingan WHERE id_kelompok = ?";
            if (!sqlsrv_query($conn, $sql, [$id_kelompok])) { 
                $success = false; 
                break; 
            }
        }
    } else if (!empty($removed) && $jenis_sidang === 'Semester') {
        error_log("Skipping pembimbing delete for " . count($removed) . " removed Semester anggota: " . json_encode($removed));
    } else {
        error_log("No removed anggota to delete pembimbing for");
    }

    if ($success) {
        sqlsrv_commit($conn);
        error_log("Kelompok edit successful for kelompok $nomor_kelompok, tahun $tahun_ajaran, jenis $jenis_sidang");
        echo json_encode(['status' => 'ok', 'message' => 'Kelompok berhasil diperbarui']);
    } else {
        sqlsrv_rollback($conn);
        error_log("Kelompok edit failed - rolling back transaction");
        echo json_encode(['status' => 'error', 'message' => 'Error saat update, data di-rollback']);
    }
} catch (Exception $e) {
    sqlsrv_rollback($conn);
    error_log("Kelompok Edit Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
?>
