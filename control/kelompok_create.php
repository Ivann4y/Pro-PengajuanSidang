<?php
session_start();
include '../koneksi/koneksiAndrew.php';

if ($conn === false) {
    die("Connection failed: " . print_r(sqlsrv_errors(), true));
}

// Pastikan hanya dosen yang boleh membuat kelompok
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    die("Unauthorized access.");
}

$nomor_dosen = $_SESSION['user_data']['nomor_dosen'] ?? null;
if ($nomor_dosen === null) {
    die("Nomor dosen tidak ditemukan di session.");
}

// Ambil data dari form
$nomor_kelompok = $_POST['nomor_kelompok'] ?? null;
$tahun_ajaran   = $_POST['tahun_ajaran'] ?? null;
$jenis_sidang   = $_POST['jenis_sidang'] ?? null;
$id_matkul      = $_POST['id_matkul'] ?? null;
$anggota        = $_POST['anggota'] ?? $_POST['anggota_nim'] ?? [];

// Debug: Log what we received
error_log("POST data received: " . json_encode($_POST));
error_log("Anggota field: " . json_encode($anggota));

// ========== COMPREHENSIVE VALIDATION ==========

// 1. Basic required field validation
if (!$nomor_kelompok || !$tahun_ajaran || !$jenis_sidang || !$id_matkul || empty($anggota)) {
    error_log("Validation failed - missing required fields: " . json_encode([
        'nomor_kelompok' => $nomor_kelompok,
        'tahun_ajaran' => $tahun_ajaran,
        'jenis_sidang' => $jenis_sidang,
        'id_matkul' => $id_matkul,
        'anggota_count' => is_array($anggota) ? count($anggota) : 'not_array'
    ]));
    die("Data tidak lengkap.");
}

// 2. Data type and format validation
if (!is_numeric($nomor_kelompok) || $nomor_kelompok <= 0) {
    die("Nomor kelompok harus berupa angka positif.");
}

if (!is_numeric($tahun_ajaran) || $tahun_ajaran < 2020 || $tahun_ajaran > 2030) {
    die("Tahun ajaran harus antara 2020-2030.");
}

if (!in_array($jenis_sidang, ['Semester', 'Tugas Akhir'])) {
    die("Jenis sidang harus 'Semester' atau 'Tugas Akhir'.");
}

if (!is_numeric($id_matkul) || $id_matkul <= 0) {
    die("ID Mata Kuliah harus berupa angka positif.");
}

// 3. Validate anggota array
if (!is_array($anggota)) {
    error_log("Anggota is not array: " . gettype($anggota) . " - " . json_encode($anggota));
    die("Data anggota harus berupa array.");
}

if (count($anggota) === 0) {
    die("Minimal harus ada satu anggota kelompok.");
}

if (count($anggota) > 5) {
    die("Maksimal 5 anggota per kelompok.");
}

// 4. Validate each NIM
$validNIMs = [];
foreach ($anggota as $nim) {
    if (empty($nim)) {
        die("NIM tidak boleh kosong.");
    }
    
    // Remove any whitespace and convert to string
    $nim = trim((string)$nim);
    
    // More flexible NIM validation - allow alphanumeric NIM (8-20 characters)
    if (strlen($nim) < 8 || strlen($nim) > 20) {
        die("NIM {$nim} harus berupa 8-20 karakter.");
    }
    
    if (in_array($nim, $validNIMs)) {
        die("NIM {$nim} duplikat dalam kelompok.");
    }
    
    $validNIMs[] = $nim;
}

error_log("Validated NIMs: " . json_encode($validNIMs));

// 5. Check if all NIMs exist in Mahasiswa table
$nimList = implode(',', array_fill(0, count($validNIMs), '?'));
$checkNimSql = "SELECT nim FROM Mahasiswa WHERE nim IN ($nimList)";
$checkNimResult = sqlsrv_query($conn, $checkNimSql, $validNIMs);

if ($checkNimResult === false) {
    die("Gagal memvalidasi NIM: " . json_encode(sqlsrv_errors()));
}

$existingNIMs = [];
while ($row = sqlsrv_fetch_array($checkNimResult, SQLSRV_FETCH_ASSOC)) {
    $existingNIMs[] = $row['nim'];
}

$missingNIMs = array_diff($validNIMs, $existingNIMs);
if (!empty($missingNIMs)) {
    die("NIM berikut tidak ditemukan: " . implode(', ', $missingNIMs));
}

// 6. Check if MataKuliah exists
$checkMatkulSql = "SELECT id_matkul FROM MataKuliah WHERE id_matkul = ?";
$checkMatkulResult = sqlsrv_query($conn, $checkMatkulSql, [$id_matkul]);

if ($checkMatkulResult === false) {
    die("Gagal memvalidasi Mata Kuliah: " . json_encode(sqlsrv_errors()));
}

if (!sqlsrv_fetch_array($checkMatkulResult, SQLSRV_FETCH_ASSOC)) {
    die("Mata Kuliah dengan ID {$id_matkul} tidak ditemukan.");
}

// 7. Business logic validation - Check dosen permissions
if ($jenis_sidang === 'Semester') {
    // For Semester, check if dosen is assigned to this mata kuliah
    $checkPengampuSql = "SELECT 1 FROM Pengampu_Kelas WHERE nomor_dosen = ? AND id_matkul = ? AND tahun_ajaran = ?";
    $checkPengampuResult = sqlsrv_query($conn, $checkPengampuSql, [$nomor_dosen, $id_matkul, $tahun_ajaran]);
    
    if ($checkPengampuResult === false) {
        die("Gagal memvalidasi pengampu kelas: " . json_encode(sqlsrv_errors()));
    }
    
    if (!sqlsrv_fetch_array($checkPengampuResult, SQLSRV_FETCH_ASSOC)) {
        die("Anda tidak memiliki izin untuk membuat kelompok Semester untuk mata kuliah ini.");
    }
}

// 8. Check if nomor_kelompok already exists for this tahun_ajaran and jenis_sidang
$checkNomorSql = "SELECT 1 FROM Kelompok WHERE nomor_kelompok = ? AND tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ?";
$checkNomorResult = sqlsrv_query($conn, $checkNomorSql, [$nomor_kelompok, $tahun_ajaran, $jenis_sidang, $id_matkul]);

if ($checkNomorResult === false) {
    die("Gagal memvalidasi nomor kelompok: " . json_encode(sqlsrv_errors()));
}

if (sqlsrv_fetch_array($checkNomorResult, SQLSRV_FETCH_ASSOC)) {
    die("Nomor kelompok {$nomor_kelompok} sudah ada untuk tahun ajaran {$tahun_ajaran} dan jenis sidang {$jenis_sidang}.");
}

try {
    sqlsrv_begin_transaction($conn);

    $lastInsertedIds = [];

    foreach ($anggota as $nim) {
        // Check if mahasiswa already exists in kelompok for this tahun_ajaran and jenis_sidang
        $checkSql = "SELECT id_kelompok FROM Kelompok WHERE nim = ? AND tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ?";
        $checkParams = [$nim, (int)$tahun_ajaran, $jenis_sidang, (int)$id_matkul];
        $checkResult = sqlsrv_query($conn, $checkSql, $checkParams);
        
        if ($checkResult !== false) {
            $existingRow = sqlsrv_fetch_array($checkResult, SQLSRV_FETCH_ASSOC);
            if ($existingRow) {
                error_log("Mahasiswa already in kelompok - Data: " . json_encode([
                    'nim' => $nim,
                    'tahun_ajaran' => $tahun_ajaran,
                    'jenis_sidang' => $jenis_sidang,
                    'id_matkul' => $id_matkul
                ]));
                throw new Exception("Mahasiswa dengan NIM {$nim} sudah terdaftar dalam kelompok untuk tahun ajaran {$tahun_ajaran} dan jenis sidang {$jenis_sidang}");
            }
        }

        // Use OUTPUT clause to get the inserted id_kelompok directly
        $insertSql = "INSERT INTO Kelompok (nomor_kelompok, nim, tahun_ajaran, jenis_sidang, id_matkul) 
                      OUTPUT INSERTED.id_kelompok 
                      VALUES (?, ?, ?, ?, ?)";
        
                    $insertParams = [
                (int)$nomor_kelompok,
                $nim,
            (int)$tahun_ajaran,
            $jenis_sidang,
            (int)$id_matkul
        ];
        
        error_log("Inserting kelompok - Data: " . json_encode([
            'sql' => $insertSql,
            'params' => $insertParams
        ]));
        
        $insertResult = sqlsrv_query($conn, $insertSql, $insertParams);

        if ($insertResult === false) {
            $errors = sqlsrv_errors();
            error_log("INSERT failed with errors: " . json_encode($errors));
            throw new Exception("INSERT gagal: " . json_encode($errors, JSON_PRETTY_PRINT));
        }

        // Get the inserted id_kelompok from the OUTPUT clause
        $insertedRow = sqlsrv_fetch_array($insertResult, SQLSRV_FETCH_ASSOC);
        $id_kelompok = $insertedRow['id_kelompok'] ?? null;
        
        error_log("INSERT result: " . json_encode($insertedRow));
        error_log("Extracted id_kelompok: " . ($id_kelompok ?? 'null'));

        if ($id_kelompok === null) {
            // Fallback: try to get the ID using a separate query
            $getLastIdSql = "SELECT TOP 1 id_kelompok FROM Kelompok WHERE nomor_kelompok = ? AND nim = ? AND tahun_ajaran = ? AND jenis_sidang = ? AND id_matkul = ? ORDER BY id_kelompok DESC";
            $getLastIdParams = [(int)$nomor_kelompok, $nim, (int)$tahun_ajaran, $jenis_sidang, (int)$id_matkul];
            $getLastIdResult = sqlsrv_query($conn, $getLastIdSql, $getLastIdParams);
            
            if ($getLastIdResult !== false) {
                $lastIdRow = sqlsrv_fetch_array($getLastIdResult, SQLSRV_FETCH_ASSOC);
                $id_kelompok = $lastIdRow['id_kelompok'] ?? null;
                error_log("Fallback ID retrieval result: " . json_encode($lastIdRow));
            }
            
            if ($id_kelompok === null) {
                throw new Exception("Gagal mendapatkan id_kelompok untuk NIM {$nim}");
            }
        }

        $lastInsertedIds[] = $id_kelompok;

        // Insert ke tabel Bimbingan hanya untuk Tugas Akhir
        if ($jenis_sidang === "Tugas Akhir") {
            // Untuk Tugas Akhir: gunakan dosen yang sedang login
            $insertBimbinganSql = "INSERT INTO Bimbingan (nomor_dosen, isPembimbing, id_kelompok) VALUES (?, ?, ?)";
            $isPembimbing = 1; // isPembimbing = 1 untuk Tugas Akhir
            $paramsBimbingan = [$nomor_dosen, $isPembimbing, $id_kelompok];
            
            error_log("Inserting Bimbingan for Tugas Akhir - Data: " . json_encode([
                'sql' => $insertBimbinganSql,
                'params' => $paramsBimbingan,
                'jenis_sidang' => $jenis_sidang,
                'isPembimbing' => $isPembimbing
            ]));
            
            $stmtBimbingan = sqlsrv_query($conn, $insertBimbinganSql, $paramsBimbingan);

            if ($stmtBimbingan === false) {
                $errors = sqlsrv_errors();
                error_log("INSERT Bimbingan failed with errors: " . json_encode($errors));
                throw new Exception("INSERT Bimbingan gagal: " . json_encode($errors, JSON_PRETTY_PRINT));
            }
            
            error_log("Successfully inserted Bimbingan for Tugas Akhir, id_kelompok: {$id_kelompok}");
        } else {
            error_log("Skipping Bimbingan insert for Semester, id_kelompok: {$id_kelompok}");
        }
    }

    sqlsrv_commit($conn);
    echo json_encode([
        "success" => true,
        "message" => "Data kelompok berhasil disimpan.",
        "inserted_ids" => $lastInsertedIds
    ]);
} catch (Exception $e) {
    sqlsrv_rollback($conn);
    error_log("Kelompok Create Error: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Gagal menyimpan data kelompok. Error: " . $e->getMessage()
    ]);
}
