<?php
session_start();
include '../../koneksi/koneksiJoin.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// Validasi sesi: pastikan pengguna adalah 'mahasiswa'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    echo json_encode(['error' => 'Tidak diizinkan']);
    exit();
}

// ASUMSI UTAMA: NIM mahasiswa yang login tersimpan di $_SESSION['user_data']['nim']
if (!isset($_SESSION['user_data']['nim'])) {
    echo json_encode(['error' => 'Data NIM mahasiswa tidak ditemukan di sesi']);
    exit();
}
$nim_mahasiswa = $_SESSION['user_data']['nim'];

/**
 * Subquery ini adalah inti dari validasi.
 * Ia mencari semua `id_kelompok` (semua anggota) yang berada dalam satu
 * kelompok logis yang sama dengan mahasiswa yang sedang login.
 * Sebuah kelompok dianggap sama jika memiliki `nomor_kelompok`, `tahun_ajaran`, dan `jenis_sidang` yang identik.
 */
$subQueryKelompok = "
    SELECT k_inner.id_kelompok
    FROM dbo.Kelompok k_inner
    WHERE EXISTS (
        SELECT 1
        FROM dbo.Kelompok k_outer
        WHERE k_outer.nim = ? -- Parameter NIM mahasiswa login
        AND k_outer.nomor_kelompok = k_inner.nomor_kelompok
        AND k_outer.tahun_ajaran = k_inner.tahun_ajaran
        AND k_outer.jenis_sidang = k_inner.jenis_sidang
    )
";

switch($action) {

    /**
     * Card 1: Sidang Sedang Berlangsung
     * Logika: Menghitung sidang kelompok mahasiswa yang status_sidangnya adalah 0x00.
     * Validasi: s.id_kelompok HARUS berada dalam daftar id_kelompok yang ditemukan oleh subquery.
     */
    case 'sidang_berlangsung':
        $sql = "SELECT COUNT(s.id_sidang) AS total
                FROM dbo.Sidang s
                WHERE s.status_sidang = 0x00
                  AND s.id_kelompok IN ($subQueryKelompok)";
        
        $stmt = sqlsrv_query($conn, $sql, [$nim_mahasiswa]);
        if ($stmt === false) {
            echo json_encode(['error' => sqlsrv_errors()]);
            exit();
        }
        $row = sqlsrv_fetch_array($stmt);
        echo json_encode(['total' => $row ? $row['total'] : 0]);
        break;

    /**
     * Card 2: Penilaian Menunggu
     * Logika: Menghitung sidang yang semua revisinya 'Approved' tapi masih ada nilai yang NULL.
     * Validasi: Sama seperti sebelumnya, s.id_kelompok divalidasi melalui subquery.
     */
    case 'penilaian_menunggu':
        $sql = "SELECT COUNT(DISTINCT s.id_sidang) AS total
                FROM dbo.Sidang s
                WHERE s.id_kelompok IN ($subQueryKelompok)
                -- Kondisi 1: Pastikan TIDAK ADA revisi yang statusnya BUKAN 'Approved'.
                AND NOT EXISTS (
                    SELECT 1 FROM dbo.Detail_Sidang ds
                    WHERE ds.id_sidang = s.id_sidang AND (ds.status_revisi IS NULL OR ds.status_revisi <> 'Approved')
                )
                -- Kondisi 2: Pastikan ADA SETIDAKNYA satu nilai yang masih NULL.
                AND EXISTS (
                    SELECT 1 FROM dbo.Penilaian p
                    WHERE p.id_sidang = s.id_sidang AND (p.n_dokumen IS NULL OR p.n_presentasi IS NULL OR p.n_tanyajawab IS NULL OR p.n_proyek IS NULL)
                )";

        $stmt = sqlsrv_query($conn, $sql, [$nim_mahasiswa]);
        if ($stmt === false) {
            echo json_encode(['error' => sqlsrv_errors()]);
            exit();
        }
        $row = sqlsrv_fetch_array($stmt);
        echo json_encode(['total' => $row ? $row['total'] : 0]);
        break;

    /**
     * Card 3: Sidang Mendatang
     * Logika: Mengambil daftar sidang mendatang dari kelompok mahasiswa.
     * Validasi: View_SidangMendatang di-JOIN ke tabel Sidang untuk memfilter berdasarkan id_kelompok
     * yang ditemukan oleh subquery.
     */
    case 'sidang_mendatang':
        $sql = "SELECT vsm.id_sidang, vsm.judul, vsm.tanggal_sidang
                FROM dbo.View_SidangMendatang vsm
                JOIN dbo.Sidang s ON vsm.id_sidang = s.id_sidang
                WHERE s.id_kelompok IN ($subQueryKelompok)
                ORDER BY vsm.tanggal_sidang ASC";
        
        $stmt = sqlsrv_query($conn, $sql, [$nim_mahasiswa]);
        if ($stmt === false) {
            echo json_encode(['error' => sqlsrv_errors()]);
            exit();
        }
        
        $sidang_mendatang = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            if ($row['tanggal_sidang'] instanceof DateTime) {
                $row['tanggal_sidang'] = $row['tanggal_sidang']->format('Y-m-d');
            }
            $sidang_mendatang[] = $row;
        }
        echo json_encode(['sidang_mendatang' => $sidang_mendatang]);
        break;

    default:
        echo json_encode(['error' => 'Aksi tidak valid']);
        break;
}

sqlsrv_close($conn);
?>