<?php
require "../../koneksi/koneksiAndrew.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    header('Content-Type: application/json');

    // Ambil data dari form
    $id_sidang = isset($_POST['id_sidang']) ? (int)$_POST['id_sidang'] : 0;
    $ruangan = $_POST['ruangan'] ?? null;
    $tanggal = $_POST['tanggal'] ?? null;
    $jam_awal = $_POST['jam_awal'] ?? null;
    $jam_akhir = $_POST['jam_akhir'] ?? null;
    $penguji_nama_list = $_POST['penguji_nama'] ?? [];

    if ($id_sidang == 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID Sidang tidak valid.']);
        exit;
    }

    // Mulai Transaksi
    if (sqlsrv_begin_transaction($conn) === false) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memulai transaksi database.']);
        exit;
    }

    // Variabel penanda keberhasilan semua query
    $all_queries_ok = true;

    // 1. Update atau Insert Jadwal
    $sql_cek_jadwal = "SELECT id_sidang FROM Jadwal WHERE id_sidang = ?";
    $stmt_cek = sqlsrv_query($conn, $sql_cek_jadwal, array($id_sidang));
    if (!$stmt_cek) { $all_queries_ok = false; }
    
    if ($all_queries_ok) {
        $jadwal_exists = sqlsrv_fetch_array($stmt_cek);
        if ($jadwal_exists) {
            $sql_jadwal = "UPDATE Jadwal SET ruang_sidang = ?, tanggal_sidang = ?, jam_sidang = ?, jam_selesai = ? WHERE id_sidang = ?";
            $params_jadwal = array($ruangan, $tanggal, $jam_awal, $jam_akhir, $id_sidang);
        } else {
            $sql_jadwal = "INSERT INTO Jadwal (id_sidang, ruang_sidang, tanggal_sidang, jam_sidang, jam_selesai) VALUES (?, ?, ?, ?, ?)";
            $params_jadwal = array($id_sidang, $ruangan, $tanggal, $jam_awal, $jam_akhir);
        }
        $stmt_jadwal = sqlsrv_query($conn, $sql_jadwal, $params_jadwal);
        if (!$stmt_jadwal) { $all_queries_ok = false; }
    }

    // 2. Logika penguji hanya dijalankan jika sidangnya adalah TA
    if ($all_queries_ok) {
        $sql_jenis = "SELECT CAST(jenis_sidang AS INT) as jenis FROM Sidang WHERE id_sidang = ?";
        $stmt_jenis = sqlsrv_query($conn, $sql_jenis, array($id_sidang));
        if (!$stmt_jenis) { $all_queries_ok = false; }
        
        if ($all_queries_ok) {
            $data_jenis = sqlsrv_fetch_array($stmt_jenis, SQLSRV_FETCH_ASSOC);

            if ($data_jenis && $data_jenis['jenis'] == 0) {
                
                // ========================================================
                // PERBAIKAN UTAMA DI SINI: Hanya hapus penguji, bukan semua peran
                // ========================================================
              $sql_delete_penguji = "DELETE FROM Penjadwalan WHERE id_sidang = ? AND TRIM(peran_dosen) = 'Penguji'";
                $stmt_delete = sqlsrv_query($conn, $sql_delete_penguji, array($id_sidang));
                if (!$stmt_delete) { $all_queries_ok = false; }

                // Insert penguji baru
                if ($all_queries_ok && !empty($penguji_nama_list)) {
                    foreach ($penguji_nama_list as $nama_dosen) {
                        $nama_penguji_var = trim($nama_dosen);
                        if (!empty($nama_penguji_var)) {
                            
                            $sql_get_dosen = "SELECT nomor_dosen FROM Dosen WHERE nama_dosen = ?";
                            $stmt_get_dosen = sqlsrv_query($conn, $sql_get_dosen, array($nama_penguji_var));
                            if (!$stmt_get_dosen) { $all_queries_ok = false; break; }

                            $dosen_data = sqlsrv_fetch_array($stmt_get_dosen, SQLSRV_FETCH_ASSOC);

                            if (!$dosen_data) {
                                // Jika dosen tidak ditemukan, batalkan semua
                                $all_queries_ok = false; 
                                // Simpan nama dosen yang error untuk pesan
                                $failed_dosen_name = $nama_penguji_var; 
                                break;
                            }

                            $nomor_dosen = $dosen_data['nomor_dosen'];
                            // Cek dulu apakah dosen ini sudah ada di penjadwalan (untuk mencegah error duplicate key)
                            $sql_check_exist = "SELECT COUNT(*) as total FROM Penjadwalan WHERE id_sidang = ? AND nomor_dosen = ?";
                            $stmt_check_exist = sqlsrv_query($conn, $sql_check_exist, array($id_sidang, $nomor_dosen));
                            $row_exist = sqlsrv_fetch_array($stmt_check_exist, SQLSRV_FETCH_ASSOC);

                            if($row_exist['total'] == 0) {
                                $sql_insert_penguji = "INSERT INTO Penjadwalan (id_sidang, nomor_dosen, peran_dosen) VALUES (?, ?, 'Penguji')";
                                $stmt_insert = sqlsrv_query($conn, $sql_insert_penguji, array($id_sidang, $nomor_dosen));
                                if (!$stmt_insert) { $all_queries_ok = false; break; }
                            }
                            // Jika sudah ada (misal sebagai pembimbing), kita abaikan saja, tidak perlu insert lagi.
                        }
                    }
                }
            }
        }
    }

    // Selesaikan Transaksi
    if ($all_queries_ok) {
        sqlsrv_commit($conn);
        echo json_encode(['status' => 'success', 'message' => 'Jadwal dan penguji berhasil diubah!']);
    } else {
        sqlsrv_rollback($conn);
        // Buat pesan error yang lebih spesifik jika ada
        $error_message = 'Gagal mengubah jadwal. Terjadi kesalahan pada database.';
        if(isset($failed_dosen_name)) {
            $error_message = "Gagal: Dosen dengan nama '$failed_dosen_name' tidak ditemukan.";
        }
        echo json_encode(['status' => 'error', 'message' => $error_message]);
    }

    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
?>