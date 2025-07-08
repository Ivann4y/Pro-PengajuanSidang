<?php
session_start();
require_once '../koneksi/koneksiAndrew.php';

// Check if user is logged in
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || 
    !isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$nim = $_SESSION['nim'] ?? '';
if (empty($nim)) {
    http_response_code(400);
    echo json_encode(['error' => 'NIM not found in session']);
    exit;
}

// Get all kelompok data for the mahasiswa
$sql = "SELECT DISTINCT 
            k.nomor_kelompok,
            k.tahun_ajaran,
            k.jenis_sidang,
            k.id_matkul,
            mk.nama_matkul,
            (SELECT COUNT(*) FROM Kelompok k2 
             WHERE k2.nomor_kelompok = k.nomor_kelompok 
               AND k2.tahun_ajaran = k.tahun_ajaran 
               AND k2.jenis_sidang = k.jenis_sidang 
               AND k2.id_matkul = k.id_matkul) as jumlah_anggota
        FROM Kelompok k
        INNER JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul
        WHERE k.nim = ?
        ORDER BY k.nomor_kelompok, k.tahun_ajaran, k.jenis_sidang, k.id_matkul";

$stmt = sqlsrv_query($conn, $sql, [$nim]);

if (!$stmt) {
    http_response_code(500);
    $errors = sqlsrv_errors();
    $error_message = 'Database error';
    if ($errors) {
        $error_message .= ': ' . $errors[0]['message'];
    }
    echo json_encode(['error' => $error_message]);
    exit;
}

$kelompok_list = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    // Get anggota details for this kelompok
    $sql_anggota = "SELECT 
                        k.nim,
                        m.nama_mhs,
                        m.prodi,
                        m.email
                    FROM Kelompok k
                    INNER JOIN Mahasiswa m ON k.nim = m.nim
                    WHERE k.nomor_kelompok = ? 
                      AND k.tahun_ajaran = ? 
                      AND k.jenis_sidang = ? 
                      AND k.id_matkul = ?
                    ORDER BY k.nim";
    
    $stmt_anggota = sqlsrv_query($conn, $sql_anggota, [
        $row['nomor_kelompok'],
        $row['tahun_ajaran'],
        $row['jenis_sidang'],
        $row['id_matkul']
    ]);
    
    $anggota_list = [];
    if ($stmt_anggota) {
        while ($anggota = sqlsrv_fetch_array($stmt_anggota, SQLSRV_FETCH_ASSOC)) {
            $anggota_list[] = [
                'nim' => $anggota['nim'],
                'nama' => $anggota['nama_mhs'],
                'prodi' => $anggota['prodi'],
                'email' => $anggota['email'],
                'is_current_user' => ($anggota['nim'] == $nim)
            ];
        }
    }
    
    // Get dosen pembimbing
    $sql_dosen = "SELECT 
                    d.nomor_dosen,
                    d.nama_dosen,
                    d.email
                  FROM Bimbingan b
                  INNER JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen
                  INNER JOIN Kelompok k ON b.id_kelompok = k.id_kelompok
                  WHERE k.nomor_kelompok = ? 
                    AND k.tahun_ajaran = ? 
                    AND k.jenis_sidang = ? 
                    AND k.id_matkul = ?
                    AND b.isPembimbing = 0x01";
    
    $stmt_dosen = sqlsrv_query($conn, $sql_dosen, [
        $row['nomor_kelompok'],
        $row['tahun_ajaran'],
        $row['jenis_sidang'],
        $row['id_matkul']
    ]);
    
    $dosen_list = [];
    if ($stmt_dosen) {
        while ($dosen = sqlsrv_fetch_array($stmt_dosen, SQLSRV_FETCH_ASSOC)) {
            $dosen_list[] = [
                'nomor_dosen' => $dosen['nomor_dosen'],
                'nama' => $dosen['nama_dosen'],
                'email' => $dosen['email']
            ];
        }
    }
    
    $kelompok_item = [
        'nomor_kelompok' => $row['nomor_kelompok'],
        'tahun_ajaran' => $row['tahun_ajaran'],
        'jenis_sidang' => $row['jenis_sidang'],
        'id_matkul' => $row['id_matkul'],
        'nama_matkul' => $row['nama_matkul'],
        'jumlah_anggota' => $row['jumlah_anggota'],
        'anggota' => $anggota_list,
        'dosen_pembimbing' => $dosen_list
    ];
    
    $kelompok_list[] = $kelompok_item;
}

$response = [
    'success' => true,
    'data' => $kelompok_list,
    'total' => count($kelompok_list),
    'nim' => $nim
];

echo json_encode($response);
?> 