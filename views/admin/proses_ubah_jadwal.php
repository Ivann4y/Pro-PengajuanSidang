<?php
require "../../koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // PENYEMPURNAAN 1: Tambahkan header JSON
    header('Content-Type: application/json');

    // Ambil data dari form yang dikirim lewat AJAX
    $id_sidang = isset($_POST['id_sidang']) ? (int)$_POST['id_sidang'] : 0;
    $ruangan = $_POST['ruangan'] ?? null;
    $tanggal = $_POST['tanggal'] ?? null;
    $jam_awal = $_POST['jam_awal'] ?? null;
    $jam_akhir = $_POST['jam_akhir'] ?? null;
    $penguji_nama_list = $_POST['penguji_nama'] ?? [];

    if ($id_sidang == 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID Sidang tidak valid.']);
        exit; // PENYEMPURNAAN 2: Gunakan exit di sini
    }

    // Mulai Transaksi
    if (sqlsrv_begin_transaction($conn) === false) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memulai transaksi database.']);
        exit;
    }

    // 1. Update atau Insert Jadwal
    $sql_cek_jadwal = "SELECT id_sidang FROM Jadwal WHERE id_sidang = ?";
    $stmt_cek = sqlsrv_query($conn, $sql_cek_jadwal, array($id_sidang));
    $jadwal_exists = sqlsrv_fetch_array($stmt_cek);

    if ($jadwal_exists) {
        $sql_jadwal = "UPDATE Jadwal SET ruang_sidang = ?, tanggal_sidang = ?, jam_sidang = ?, jam_selesai = ? WHERE id_sidang = ?";
        $params_jadwal = array($ruangan, $tanggal, $jam_awal, $jam_akhir, $id_sidang);
    } else {
        $sql_jadwal = "INSERT INTO Jadwal (id_sidang, ruang_sidang, tanggal_sidang, jam_sidang, jam_selesai) VALUES (?, ?, ?, ?, ?)";
        $params_jadwal = array($id_sidang, $ruangan, $tanggal, $jam_awal, $jam_akhir);
    }
    $stmt_jadwal = sqlsrv_query($conn, $sql_jadwal, $params_jadwal);
    
    $all_queries_ok = ($stmt_jadwal) ? true : false;

    // PENYEMPURNAAN 3: Logika penguji hanya dijalankan jika sidangnya adalah TA
    $sql_jenis = "SELECT CAST(jenis_sidang AS INT) as jenis FROM Sidang WHERE id_sidang = ?";
    $stmt_jenis = sqlsrv_query($conn, $sql_jenis, array($id_sidang));
    $data_jenis = sqlsrv_fetch_array($stmt_jenis, SQLSRV_FETCH_ASSOC);

    if ($all_queries_ok && $data_jenis && $data_jenis['jenis'] == 0) {
        
        // Hapus penguji lama
        $sql_delete_penguji = "DELETE FROM Penjadwalan WHERE id_sidang = ?";
        $stmt_delete = sqlsrv_query($conn, $sql_delete_penguji, array($id_sidang));
        $all_queries_ok = $stmt_delete;

        // Insert penguji baru
        if ($all_queries_ok && !empty($penguji_nama_list)) {
            $sql_insert_penguji = "INSERT INTO Penjadwalan (id_sidang, nomor_dosen, peran_dosen) 
                                   SELECT ?, d.nomor_dosen, 'Penguji' 
                                   FROM Dosen d WHERE d.nama_dosen = ?";
            $stmt_insert_penguji = sqlsrv_prepare($conn, $sql_insert_penguji, array(&$id_sidang, &$nama_penguji_var));

            if ($stmt_insert_penguji) {
                foreach ($penguji_nama_list as $nama_dosen) {
                    $nama_penguji_var = trim($nama_dosen);
                    if (!empty($nama_penguji_var)) {
                        if (!sqlsrv_execute($stmt_insert_penguji)) {
                            $all_queries_ok = false;
                            break;
                        }
                    }
                }
            } else {
                 $all_queries_ok = false;
            }
        }
    }

    // Selesaikan Transaksi
    if ($all_queries_ok) {
        sqlsrv_commit($conn);
        echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil diubah!']);
    } else {
        sqlsrv_rollback($conn);
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah jadwal. Terjadi kesalahan pada database.']);
    }

    exit; // PENYEMPURNAAN 2: Pastikan skrip berhenti di sini
}

// Jika request bukan POST, kirim error
http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
?>