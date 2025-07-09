<?php
session_start();
include '../../../koneksi/koneksiAndrew.php'; // Sesuaikan path jika perlu
header('Content-Type: application/json');

// Get parameters with defaults
$tahun_ajaran = $_GET['tahun_ajaran'] ?? null;
$jenis_sidang = $_GET['jenis_sidang'] ?? null;
$id_matkul = $_GET['id_matkul'] ?? null;
$prodi = $_GET['prodi'] ?? null;

// Edit mode parameters - for validating existing members
$edit_mode = $_GET['edit_mode'] ?? false;
$current_nomor_kelompok = $_GET['current_nomor_kelompok'] ?? null;
$current_tahun_ajaran = $_GET['current_tahun_ajaran'] ?? null;
$current_jenis_sidang = $_GET['current_jenis_sidang'] ?? null;
$current_id_matkul = $_GET['current_id_matkul'] ?? null;

// Debug logging
error_log("get_mahasiswa.php called with: tahun_ajaran=$tahun_ajaran, jenis_sidang=$jenis_sidang, id_matkul=$id_matkul, prodi=$prodi, edit_mode=$edit_mode");

// Build the query to exclude students already in groups
$sql = "SELECT m.nim, m.nama_mhs, m.prodi
        FROM Mahasiswa m
        WHERE 1=1";

$params = [];

// Add filtering conditions based on provided parameters
if ($tahun_ajaran && $jenis_sidang && $id_matkul) {
    // Filter by specific context (tahun_ajaran, jenis_sidang, id_matkul)
    if ($edit_mode && $current_nomor_kelompok && $current_tahun_ajaran && $current_jenis_sidang && $current_id_matkul) {
        // In edit mode: exclude students from other kelompok but include current kelompok members
        $sql .= " AND NOT EXISTS (
            SELECT 1 FROM Kelompok k
            WHERE k.nim = m.nim
              AND k.tahun_ajaran = ?
              AND k.jenis_sidang = ?
              AND k.id_matkul = ?
              AND k.nomor_kelompok != ?
        )";
        $params[] = $tahun_ajaran;
        $params[] = $jenis_sidang;
        $params[] = $id_matkul;
        $params[] = $current_nomor_kelompok;
    } else {
        // Normal mode: exclude all students in this context
    $sql .= " AND NOT EXISTS (
        SELECT 1 FROM Kelompok k
        WHERE k.nim = m.nim
          AND k.tahun_ajaran = ?
          AND k.jenis_sidang = ?
          AND k.id_matkul = ?
    )";
    $params[] = $tahun_ajaran;
    $params[] = $jenis_sidang;
    $params[] = $id_matkul;
    }
} elseif ($tahun_ajaran && $jenis_sidang) {
    // Filter by tahun_ajaran and jenis_sidang only
    if ($edit_mode && $current_nomor_kelompok && $current_tahun_ajaran && $current_jenis_sidang) {
        // In edit mode: exclude students from other kelompok but include current kelompok members
        $sql .= " AND NOT EXISTS (
            SELECT 1 FROM Kelompok k
            WHERE k.nim = m.nim
              AND k.tahun_ajaran = ?
              AND k.jenis_sidang = ?
              AND k.nomor_kelompok != ?
        )";
        $params[] = $tahun_ajaran;
        $params[] = $jenis_sidang;
        $params[] = $current_nomor_kelompok;
    } else {
        // Normal mode: exclude all students in this context
    $sql .= " AND NOT EXISTS (
        SELECT 1 FROM Kelompok k
        WHERE k.nim = m.nim
          AND k.tahun_ajaran = ?
          AND k.jenis_sidang = ?
    )";
    $params[] = $tahun_ajaran;
    $params[] = $jenis_sidang;
    }
} elseif ($tahun_ajaran) {
    // Filter by tahun_ajaran only
    if ($edit_mode && $current_nomor_kelompok && $current_tahun_ajaran) {
        // In edit mode: exclude students from other kelompok but include current kelompok members
        $sql .= " AND NOT EXISTS (
            SELECT 1 FROM Kelompok k
            WHERE k.nim = m.nim
              AND k.tahun_ajaran = ?
              AND k.nomor_kelompok != ?
        )";
        $params[] = $tahun_ajaran;
        $params[] = $current_nomor_kelompok;
    } else {
        // Normal mode: exclude all students in this context
    $sql .= " AND NOT EXISTS (
        SELECT 1 FROM Kelompok k
        WHERE k.nim = m.nim
          AND k.tahun_ajaran = ?
    )";
    $params[] = $tahun_ajaran;
    }
} else {
    // No specific filtering - exclude students who are in ANY group
    if ($edit_mode && $current_nomor_kelompok && $current_tahun_ajaran && $current_jenis_sidang && $current_id_matkul) {
        // In edit mode: exclude students from other kelompok but include current kelompok members
        $sql .= " AND NOT EXISTS (
            SELECT 1 FROM Kelompok k
            WHERE k.nim = m.nim
              AND k.nomor_kelompok != ?
        )";
        $params[] = $current_nomor_kelompok;
    } else {
        // Normal mode: exclude all students in any group
    $sql .= " AND NOT EXISTS (
        SELECT 1 FROM Kelompok k
        WHERE k.nim = m.nim
    )";
    }
}

// Add prodi filtering with TRPL alias support
if ($prodi) {
    $normalizedProdi = strtolower(trim($prodi));
    $rplAliases = [
        "rekayasa perangkat lunak",
        "trpl",
        "rpl",
        "teknologi rekayasa perangkat lunak",
    ];
    
    if (in_array($normalizedProdi, $rplAliases)) {
        // For RPL-related prodi, include all TRPL aliases
        $sql .= " AND (LOWER(m.prodi) IN (?, ?, ?, ?))";
        $params[] = "rekayasa perangkat lunak";
        $params[] = "trpl";
        $params[] = "rpl";
        $params[] = "teknologi rekayasa perangkat lunak";
        error_log("Using RPL alias filter for prodi: $prodi");
    } else {
        // For other prodi, use exact match
        $sql .= " AND LOWER(m.prodi) = ?";
        $params[] = $normalizedProdi;
        error_log("Using exact match filter for prodi: $prodi");
    }
}

$sql .= " ORDER BY m.nama_mhs ASC";

error_log("Final SQL: $sql");
error_log("Parameters: " . json_encode($params));

$stmt = sqlsrv_query($conn, $sql, $params);

if (!$stmt) {
    error_log("Error in get_mahasiswa.php: " . json_encode(sqlsrv_errors()));
    echo json_encode([]);
    exit;
}

$result = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $result[] = $row;
}

error_log("Returning " . count($result) . " students for prodi: $prodi (edit_mode: $edit_mode)");
echo json_encode($result);
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>