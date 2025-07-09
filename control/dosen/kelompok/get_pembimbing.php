<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include '../../../koneksi/koneksiAndrew.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']); exit();
}

// Pastikan hanya dosen yang boleh akses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']); exit();
}

$current_dosen = $_SESSION['user_data']['nomor_dosen'] ?? null;
if ($current_dosen === null) {
    echo json_encode(['status' => 'error', 'message' => 'Nomor dosen tidak ditemukan di session.']); exit();
}

// Get data from JSON or POST
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    // Fallback to POST data
    $data = $_POST;
}

$nomor_kelompok = $data['nomor_kelompok'] ?? null;
$tahun_ajaran = $data['tahun_ajaran'] ?? null;
$jenis_sidang = $data['jenis_sidang'] ?? null;
$id_matkul = $data['id_matkul'] ?? null;

// Debug logging
error_log("Get pembimbing data: " . json_encode($data));

// Validation
if (!$nomor_kelompok || !$tahun_ajaran || !$jenis_sidang || !$id_matkul) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']); exit();
}

// Set id_matkul for Tugas Akhir
if ($jenis_sidang === 'Tugas Akhir') {
    $id_matkul = 2006;
}

// Validate dosen permissions for Semester
if ($jenis_sidang === 'Semester') {
    $sql = "SELECT 1 FROM Pengampu_Kelas WHERE nomor_dosen = ? AND id_matkul = ? AND tahun_ajaran = ?";
    $stmt = sqlsrv_query($conn, $sql, [$current_dosen, $id_matkul, $tahun_ajaran]);
    if (!$stmt || !sqlsrv_fetch($stmt)) {
        echo json_encode(['status'=>'error','message'=>'Anda bukan pengampu mata kuliah ini di tahun ajaran ini']); exit();
    }
}

try {
    // Untuk Semester: tidak ada pembimbing, return empty array
    if ($jenis_sidang === 'Semester') {
        error_log("Semester type - no pembimbing needed, returning empty array");
        echo json_encode(['status' => 'ok', 'data' => []]);
        exit();
    }
    
    // Untuk Tugas Akhir: ambil pembimbing dari tabel Bimbingan
    if ($jenis_sidang === 'Tugas Akhir') {
$sql = "SELECT DISTINCT d.nomor_dosen, d.nama_dosen
        FROM Dosen d
        JOIN Bimbingan b ON d.nomor_dosen = b.nomor_dosen
        JOIN Kelompok k ON b.id_kelompok = k.id_kelompok
        WHERE k.nomor_kelompok = ? 
          AND k.tahun_ajaran = ? 
          AND k.jenis_sidang = ? 
          AND k.id_matkul = ?
                  AND b.isPembimbing = 1";

$stmt = sqlsrv_query($conn, $sql, [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul]);

        if ($stmt === false) {
            $errors = sqlsrv_errors();
            error_log("Failed to get pembimbing: " . json_encode($errors));
            throw new Exception("Gagal mengambil data pembimbing: " . json_encode($errors, JSON_PRETTY_PRINT));
}

        $pembimbing_list = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $pembimbing_list[] = [
                'nomor_dosen' => $row['nomor_dosen'],
                'nama_dosen' => $row['nama_dosen']
            ];
}

        error_log("Found pembimbing for Tugas Akhir: " . json_encode($pembimbing_list));
        echo json_encode(['status' => 'ok', 'data' => $pembimbing_list]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Jenis sidang tidak valid']);
    }
    
} catch (Exception $e) {
    error_log("Get Pembimbing Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
?> 