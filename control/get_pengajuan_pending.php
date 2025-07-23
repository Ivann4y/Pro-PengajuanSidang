<?php
// get_pengajuan_pending.php
session_start();
include_once '../koneksi/koneksiJoin.php';

// Set proper headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Role detection
$isDosen = isset($_SESSION['role']) && $_SESSION['role'] === 'dosen';
$nomorDosen = $isDosen ? $_SESSION['user_data']['nomor_dosen'] : null;

if (!$isDosen) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
    exit();
}

$pengajuanList = [];

try {
    // --- Query untuk pengajuan SEMESTER di mana dosen sebagai pengampu ---
    $sql_semester = "
        SELECT 
            s.id_sidang,
            s.judul,
            s.status_ajuan,
            s.tanggal_pengajuan,
            k.nomor_kelompok,
            k.tahun_ajaran,
            k.jenis_sidang,
            k.id_matkul,
            mk.nama_matkul,
            m.prodi,
            k.id_kelompok,
            k.nim,
            m.nama_mhs,
            NULL AS nomor_dosen,
            NULL AS nama_dosen
        FROM Sidang s
        JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
        JOIN Pengampu_Kelas pk ON pk.id_matkul = k.id_matkul 
            AND pk.tahun_ajaran = k.tahun_ajaran 
            AND pk.nomor_dosen = ?
        JOIN Mahasiswa m ON m.nim = k.nim
        JOIN MataKuliah mk ON mk.id_matkul = k.id_matkul
        WHERE k.jenis_sidang = 'Semester' 
            AND s.status_ajuan = 'Pending'
        ORDER BY k.nomor_kelompok, k.nim
    ";
    
    $params_semester = [$nomorDosen];
    $stmt1 = sqlsrv_query($conn, $sql_semester, $params_semester);
    
    if ($stmt1) {
        while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
            $key = $row['nomor_kelompok'].'_'.$row['tahun_ajaran'].'_'.$row['jenis_sidang'].'_'.$row['id_matkul'];
            
            if (!isset($pengajuanList[$key])) {
                $pengajuanList[$key] = [
                    'id_sidang' => $row['id_sidang'],
                    'judul' => $row['judul'],
                    'status_ajuan' => $row['status_ajuan'],
                    'tanggal_pengajuan' => $row['tanggal_pengajuan'] ? $row['tanggal_pengajuan']->format('Y-m-d H:i:s') : null,
                    'nomor_kelompok' => $row['nomor_kelompok'],
                    'tahun_ajaran' => $row['tahun_ajaran'],
                    'jenis_sidang' => $row['jenis_sidang'],
                    'id_matkul' => $row['id_matkul'],
                    'nama_matkul' => $row['nama_matkul'],
                    'prodi' => $row['prodi'],
                    'anggota' => [],
                    'pembimbing' => []
                ];
            }
            
            // Prevent duplicate anggota
            $already = false;
            foreach ($pengajuanList[$key]['anggota'] as $a) {
                if ($a['nim'] == $row['nim']) {
                    $already = true;
                    break;
                }
            }
            if (!$already) {
                $pengajuanList[$key]['anggota'][] = [
                    'nim' => $row['nim'],
                    'nama_mhs' => $row['nama_mhs']
                ];
            }
        }
    }

    // --- Query untuk pengajuan TUGAS AKHIR di mana dosen sebagai pembimbing ---
    $sql_ta = "
        SELECT 
            s.id_sidang,
            s.judul,
            s.status_ajuan,
            s.tanggal_pengajuan,
            k.nomor_kelompok,
            k.tahun_ajaran,
            k.jenis_sidang,
            k.id_matkul,
            mk.nama_matkul,
            m.prodi,
            k.id_kelompok,
            k.nim,
            m.nama_mhs,
            b.nomor_dosen,
            d.nama_dosen
        FROM Sidang s
        JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
        JOIN Bimbingan b ON b.id_kelompok = k.id_kelompok AND b.nomor_dosen = ?
        JOIN Dosen d ON d.nomor_dosen = b.nomor_dosen
        JOIN Mahasiswa m ON m.nim = k.nim
        JOIN MataKuliah mk ON mk.id_matkul = k.id_matkul
        WHERE k.jenis_sidang = 'Tugas Akhir' 
            AND s.status_ajuan = 'Pending'
        ORDER BY k.nomor_kelompok, k.nim
    ";
    
    $params_ta = [$nomorDosen];
    $stmt2 = sqlsrv_query($conn, $sql_ta, $params_ta);
    
    if ($stmt2) {
        while ($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
            $key = $row['nomor_kelompok'].'_'.$row['tahun_ajaran'].'_'.$row['jenis_sidang'].'_'.$row['id_matkul'];
            
            if (!isset($pengajuanList[$key])) {
                $pengajuanList[$key] = [
                    'id_sidang' => $row['id_sidang'],
                    'judul' => $row['judul'],
                    'status_ajuan' => $row['status_ajuan'],
                    'tanggal_pengajuan' => $row['tanggal_pengajuan'] ? $row['tanggal_pengajuan']->format('Y-m-d H:i:s') : null,
                    'nomor_kelompok' => $row['nomor_kelompok'],
                    'tahun_ajaran' => $row['tahun_ajaran'],
                    'jenis_sidang' => $row['jenis_sidang'],
                    'id_matkul' => $row['id_matkul'],
                    'nama_matkul' => $row['nama_matkul'],
                    'prodi' => $row['prodi'],
                    'anggota' => [],
                    'pembimbing' => []
                ];
            }
            
            // Prevent duplicate anggota
            $already = false;
            foreach ($pengajuanList[$key]['anggota'] as $a) {
                if ($a['nim'] == $row['nim']) {
                    $already = true;
                    break;
                }
            }
            if (!$already) {
                $pengajuanList[$key]['anggota'][] = [
                    'nim' => $row['nim'],
                    'nama_mhs' => $row['nama_mhs']
                ];
            }
            
            // Add pembimbing if exists
            if ($row['nomor_dosen']) {
                $pengajuanList[$key]['pembimbing'][$row['nomor_dosen']] = [
                    'nomor_dosen' => $row['nomor_dosen'],
                    'nama_dosen' => $row['nama_dosen']
                ];
            }
        }
    }

    // Normalisasi pembimbing array
    foreach ($pengajuanList as &$group) {
        $group['pembimbing'] = array_values($group['pembimbing']);
    }
    unset($group);

    // Convert to indexed array
    $result = array_values($pengajuanList);
    
    echo json_encode([
        'status' => 'success',
        'data' => $result,
        'count' => count($result)
    ]);

} catch (Exception $e) {
    error_log("Error in get_pengajuan_pending.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan saat mengambil data pengajuan'
    ]);
}
?> 