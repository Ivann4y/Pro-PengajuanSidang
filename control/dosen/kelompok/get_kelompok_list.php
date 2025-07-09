<?php
// get_kelompok_list.php
session_start();
include_once '../../../koneksi/koneksiAndrew.php'; // Sesuaikan path koneksi

// Set proper headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Role detection
$isDosen = isset($_SESSION['role']) && $_SESSION['role'] === 'dosen';
$nomorDosen = $isDosen ? $_SESSION['user_data']['nomor_dosen'] : null;

$kelompokList = [];

try {
    // --- Query utama untuk Dosen ---
    if ($isDosen) {
        // 1. Kelompok SEMESTER di mana dosen sebagai pengampu
        $sql_semester = "
            SELECT k.nomor_kelompok, k.tahun_ajaran, k.jenis_sidang, k.id_matkul, mk.nama_matkul, 
                   m.prodi,
                   k.id_kelompok, k.nim, m.nama_mhs,
                   NULL AS nomor_dosen, NULL AS nama_dosen
            FROM kelompok k
            JOIN pengampu_kelas pk ON pk.id_matkul = k.id_matkul AND pk.tahun_ajaran = k.tahun_ajaran AND pk.nomor_dosen = ?
            JOIN mahasiswa m ON m.nim = k.nim
            JOIN matakuliah mk ON mk.id_matkul = k.id_matkul
            WHERE k.jenis_sidang = 'Semester'
            ORDER BY k.nomor_kelompok, k.nim
        ";
        $params_semester = [$nomorDosen];
        $stmt1 = sqlsrv_query($conn, $sql_semester, $params_semester);
        if ($stmt1) {
            while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
                $key = $row['nomor_kelompok'].'_'.$row['tahun_ajaran'].'_'.$row['jenis_sidang'].'_'.$row['id_matkul'];
                if (!isset($kelompokList[$key])) {
                    $kelompokList[$key] = [
                        'nomor_kelompok' => $row['nomor_kelompok'],
                        'tahun_ajaran' => $row['tahun_ajaran'],
                        'jenis_sidang' => $row['jenis_sidang'],
                        'id_matkul' => $row['id_matkul'],
                        'nama_matkul' => $row['nama_matkul'],
                        'prodi' => $row['prodi'],
                        'anggota' => [],
                        'pembimbing' => [],
                        'pengajuan_status' => null
                    ];
                }
                // Prevent duplicate anggota
                $already = false;
                foreach ($kelompokList[$key]['anggota'] as $a) {
                    if ($a['nim'] == $row['nim']) {
                        $already = true;
                        break;
                    }
                }
                if (!$already) {
                $kelompokList[$key]['anggota'][] = [
                    'nim' => $row['nim'],
                    'nama_mhs' => $row['nama_mhs']
                ];
                }
            }
        }

        // 2. Kelompok TUGAS AKHIR di mana dosen sebagai pembimbing
        $sql_ta = "
            SELECT k.nomor_kelompok, k.tahun_ajaran, k.jenis_sidang, k.id_matkul, mk.nama_matkul, 
                   m.prodi,
                   k.id_kelompok, k.nim, m.nama_mhs,
                   b.nomor_dosen, d.nama_dosen
            FROM kelompok k
            JOIN bimbingan b ON b.id_kelompok = k.id_kelompok AND b.nomor_dosen = ?
            JOIN dosen d ON d.nomor_dosen = b.nomor_dosen
            JOIN mahasiswa m ON m.nim = k.nim
            JOIN matakuliah mk ON mk.id_matkul = k.id_matkul
            WHERE k.jenis_sidang = 'Tugas Akhir'
            ORDER BY k.nomor_kelompok, k.nim
        ";
        $params_ta = [$nomorDosen];
        $stmt2 = sqlsrv_query($conn, $sql_ta, $params_ta);
        if ($stmt2) {
            while ($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
                $key = $row['nomor_kelompok'].'_'.$row['tahun_ajaran'].'_'.$row['jenis_sidang'].'_'.$row['id_matkul'];
                if (!isset($kelompokList[$key])) {
                    $kelompokList[$key] = [
                        'nomor_kelompok' => $row['nomor_kelompok'],
                        'tahun_ajaran' => $row['tahun_ajaran'],
                        'jenis_sidang' => $row['jenis_sidang'],
                        'id_matkul' => $row['id_matkul'],
                        'nama_matkul' => $row['nama_matkul'],
                        'prodi' => $row['prodi'],
                        'anggota' => [],
                        'pembimbing' => [],
                        'pengajuan_status' => null
                    ];
                }
                // Prevent duplicate anggota
                $already = false;
                foreach ($kelompokList[$key]['anggota'] as $a) {
                    if ($a['nim'] == $row['nim']) {
                        $already = true;
                        break;
                    }
                }
                if (!$already) {
                $kelompokList[$key]['anggota'][] = [
                    'nim' => $row['nim'],
                    'nama_mhs' => $row['nama_mhs']
                ];
                }
                if ($row['nomor_dosen']) {
                    $kelompokList[$key]['pembimbing'][$row['nomor_dosen']] = [
                        'nomor_dosen' => $row['nomor_dosen'],
                        'nama_dosen' => $row['nama_dosen']
                    ];
                }
            }
        }

        // Normalisasi pembimbing
        foreach ($kelompokList as &$group) {
            $group['pembimbing'] = array_values($group['pembimbing']);
        }
        unset($group);
    }

    // --- Query status pengajuan (sidang) untuk tiap kelompok ---
    foreach ($kelompokList as &$group) {
        $sql_pengajuan = "
            SELECT TOP 1 s.id_sidang, s.status_ajuan, k.nim, m.nama_mhs
            FROM sidang s
            JOIN kelompok k ON k.id_kelompok = s.id_kelompok
            JOIN mahasiswa m ON m.nim = k.nim
            WHERE k.nomor_kelompok = ?
              AND k.tahun_ajaran = ?
              AND k.jenis_sidang = ?
              AND k.id_matkul = ?
            ORDER BY s.id_sidang DESC
        ";
        $params_pengajuan = [
            $group['nomor_kelompok'],
            $group['tahun_ajaran'],
            $group['jenis_sidang'],
            $group['id_matkul']
        ];
        $stmt3 = sqlsrv_query($conn, $sql_pengajuan, $params_pengajuan);
        if ($stmt3) {
            $result = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC);
            if ($result) {
                $group['pengajuan_status'] = [
                    'id_sidang' => $result['id_sidang'],
                    'status_ajuan' => $result['status_ajuan'],
                    'nim_pengaju' => $result['nim'],
                    'nama_pengaju' => $result['nama_mhs']
                ];
            } else {
                $group['pengajuan_status'] = null;
            }
        } else {
            $group['pengajuan_status'] = null;
        }
    }
    unset($group);

    $result = array_values($kelompokList);
    error_log("get_kelompok_list.php returning " . count($result) . " kelompok");
    echo json_encode($result);
    
} catch (Exception $e) {
    echo json_encode([]);
}
?>
