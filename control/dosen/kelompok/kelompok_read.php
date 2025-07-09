<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include '../../../koneksi/koneksiAndrew.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']); exit();
}

$current_dosen = $_SESSION['user_data']['nomor_dosen'] ?? null;

// Check if this is a request for a specific kelompok (edit mode)
$nomor_kelompok = $_GET['nomor_kelompok'] ?? null;
$tahun_ajaran = $_GET['tahun_ajaran'] ?? null;
$jenis_sidang = $_GET['jenis_sidang'] ?? null;
$id_matkul = $_GET['id_matkul'] ?? null;

if ($nomor_kelompok && $tahun_ajaran && $jenis_sidang && $id_matkul) {
    // Get specific kelompok for editing
    $sql = "SELECT k.id_kelompok, k.nomor_kelompok, k.nim, m.nama_mhs, m.prodi,
                   k.tahun_ajaran, k.jenis_sidang, k.id_matkul, mk.nama_matkul
            FROM Kelompok k
            JOIN Mahasiswa m ON k.nim = m.nim
            JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul
            WHERE k.nomor_kelompok = ? 
              AND k.tahun_ajaran = ? 
              AND k.jenis_sidang = ? 
              AND k.id_matkul = ?
            ORDER BY k.nim";
    
    $stmt = sqlsrv_query($conn, $sql, [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul]);
    
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']); 
        exit();
    }
    
    $result = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $result[] = $row;
    }
    
    echo json_encode(['status' => 'ok', 'data' => $result]);
    exit();
}

// Original logic for listing all kelompok
$tahun_ajaran = ($_GET['tahun_ajaran'] ?? '') === 'Semua' ? null : ($_GET['tahun_ajaran'] ?? null);
$jenis_sidang = ($_GET['jenis_sidang'] ?? '') === 'Semua' ? null : ($_GET['jenis_sidang'] ?? null);
$id_matkul    = ($_GET['id_matkul']    ?? '') === 'Semua' ? null : ($_GET['id_matkul']    ?? null);

$where = [];
$params = [];
$roleFilter = [];

if ($jenis_sidang === 'Semester' || !$jenis_sidang) {
    $roleFilter[] = "(k.jenis_sidang = 'Semester' AND EXISTS (
        SELECT 1 FROM Pengampu_Kelas pk
        WHERE pk.nomor_dosen = ? AND pk.id_matkul = k.id_matkul AND pk.tahun_ajaran = k.tahun_ajaran
    ))";
    $params[] = $current_dosen;
}
if ($jenis_sidang === 'Tugas Akhir' || !$jenis_sidang) {
    $roleFilter[] = "(k.jenis_sidang = 'Tugas Akhir' AND EXISTS (
        SELECT 1 FROM Bimbingan b
        WHERE b.nomor_dosen = ? AND b.id_kelompok = k.id_kelompok
    ))";
    $params[] = $current_dosen;
}
if ($roleFilter) {
    $where[] = '('.implode(' OR ', $roleFilter).')';
}
if ($tahun_ajaran) {
    $where[] = "k.tahun_ajaran = ?";
    $params[] = $tahun_ajaran;
}
if ($id_matkul) {
    $where[] = "k.id_matkul = ?";
    $params[] = $id_matkul;
}

$whereClause = count($where) ? 'WHERE '.implode(' AND ', $where) : '';
$sql = "SELECT k.id_kelompok, k.nomor_kelompok, k.nim, m.nama_mhs, 
               k.tahun_ajaran, k.jenis_sidang, k.id_matkul, mk.nama_matkul
        FROM Kelompok k
        JOIN Mahasiswa m ON k.nim = m.nim
        JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul
        $whereClause
        ORDER BY k.nomor_kelompok, k.nim";
$stmt = sqlsrv_query($conn, $sql, $params);
$result = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $result[] = $row;
}
echo json_encode(['status' => 'ok', 'data' => $result]);
?>
