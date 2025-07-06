<?php
require "../../koneksi/koneksiAndrew.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    header('Content-Type: application/json');

    // Ambil semua data dari form, TERMASUK BOBOT
    $id_sidang = isset($_POST['id_sidang']) ? (int)$_POST['id_sidang'] : 0;
    $ruangan = $_POST['ruangan'] ?? null;
    $tanggal = $_POST['tanggal'] ?? null;
    $jam_awal = $_POST['jam_awal'] ?? null;
    $jam_akhir = $_POST['jam_akhir'] ?? null;
    $penguji_nama_list = $_POST['penguji_nama'] ?? [];
    $penguji_bobot_list = $_POST['penguji_bobot'] ?? []; // <-- DATA BOBOT DIAMBIL

    if ($id_sidang == 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID Sidang tidak valid.']);
        exit;
    }

    if (sqlsrv_begin_transaction($conn) === false) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memulai transaksi database.']);
        exit;
    }
     if (!empty($penguji_bobot_list)) {
        // Membersihkan array dari nilai non-numerik sebelum menjumlahkan
        $bobot_numerik = array_filter($penguji_bobot_list, 'is_numeric');
        $total_bobot = array_sum($bobot_numerik);
    }
    if ($total_bobot < 0){
        // Jika total bobot kurang dari 0, langsung hentikan proses dan kirim error
        echo json_encode(['status' => 'error', 'message' => 'Gagal: Total bobot penilaian tidak boleh kurang dari 0%.']);
        exit; // Hentikan eksekusi skrip
    }

    if ($total_bobot > 100) {
        // Jika total bobot lebih dari 100, langsung hentikan proses dan kirim error
        echo json_encode(['status' => 'error', 'message' => 'Gagal: Total bobot penilaian tidak boleh melebihi 100%. Total saat ini adalah ' . $total_bobot . '%.']);
        exit; // Hentikan eksekusi skrip
    }

    $all_queries_ok = true;
    $error_message = 'Gagal mengubah jadwal. Terjadi kesalahan yang tidak diketahui.';

    // 1. Update Jadwal (Tidak berubah)
    // ... (kode ini sudah benar) ...
    $sql_cek_jadwal = "SELECT id_sidang FROM Jadwal WHERE id_sidang = ?";
    $stmt_cek = sqlsrv_query($conn, $sql_cek_jadwal, array($id_sidang));
    if($stmt_cek !== false){
        $jadwal_exists = sqlsrv_fetch_array($stmt_cek);
        if ($jadwal_exists) {
            $sql_jadwal = "UPDATE Jadwal SET ruang_sidang = ?, tanggal_sidang = ?, jam_sidang = ?, jam_selesai = ? WHERE id_sidang = ?";
            $params_jadwal = array($ruangan, $tanggal, $jam_awal, $jam_akhir, $id_sidang);
        } else {
            $sql_jadwal = "INSERT INTO Jadwal (id_sidang, ruang_sidang, tanggal_sidang, jam_sidang, jam_selesai) VALUES (?, ?, ?, ?, ?)";
            $params_jadwal = array($id_sidang, $ruangan, $tanggal, $jam_awal, $jam_akhir);
        }
        $stmt_jadwal = sqlsrv_query($conn, $sql_jadwal, $params_jadwal);
        if ($stmt_jadwal === false) { $all_queries_ok = false; }
    } else {
        $all_queries_ok = false;
    }


    // 2. Logika Penguji & BOBOT
    if ($all_queries_ok) {
       $sql_sidang = "SELECT k.id_kelompok, k.jenis_sidang 
               FROM Sidang s
               JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
               WHERE s.id_sidang = ?";
$stmt_sidang = sqlsrv_query($conn, $sql_sidang, array($id_sidang));
        
        if ($stmt_sidang !== false) {
    $data_sidang = sqlsrv_fetch_array($stmt_sidang, SQLSRV_FETCH_ASSOC);
    // PERBAIKAN: Cek dengan string 'TA'
    if ($data_sidang && $data_sidang['jenis_sidang'] == 'Tugas Akhir') { 
                
                // Ambil daftar nomor dosen penguji lama untuk dihapus dari Penilaian
                $penguji_lama_nos = [];
                $sql_get_lama = "SELECT nomor_dosen FROM Penjadwalan WHERE id_sidang = ? AND peran_dosen = 0";
                $stmt_get_lama = sqlsrv_query($conn, $sql_get_lama, array($id_sidang));
                if($stmt_get_lama !== false){
                    while($row = sqlsrv_fetch_array($stmt_get_lama, SQLSRV_FETCH_ASSOC)){
                        $penguji_lama_nos[] = $row['nomor_dosen'];
                    }
                }

                // Hapus entri penilaian lama untuk penguji lama
                if (!empty($penguji_lama_nos)) {
                    $placeholders = implode(',', array_fill(0, count($penguji_lama_nos), '?'));
                    $sql_delete_penilaian = "DELETE FROM Penilaian WHERE id_sidang = ? AND nomor_dosen IN ($placeholders)";
                    $params_delete = array_merge([$id_sidang], $penguji_lama_nos);
                    if (sqlsrv_query($conn, $sql_delete_penilaian, $params_delete) === false) { $all_queries_ok = false; }
                }

                // Hapus penguji lama dari Penjadwalan
                $sql_delete_penguji = "DELETE FROM Penjadwalan WHERE id_sidang = ? AND peran_dosen = 0";
                if (sqlsrv_query($conn, $sql_delete_penguji, array($id_sidang)) === false) { $all_queries_ok = false; }

                // Ambil daftar NIM mahasiswa dalam kelompok ini
                $id_kelompok = $data_sidang['id_kelompok'];
                $list_nim_mahasiswa = [];
                $sql_get_nim = "SELECT nim FROM Kelompok WHERE id_kelompok = ?";
                $stmt_get_nim = sqlsrv_query($conn, $sql_get_nim, array($id_kelompok));
                while($row = sqlsrv_fetch_array($stmt_get_nim, SQLSRV_FETCH_ASSOC)) {
                    $list_nim_mahasiswa[] = $row['nim'];
                }

                // Loop melalui setiap penguji yang dikirim dari form
                if ($all_queries_ok && !empty($list_nim_mahasiswa)) {
                    foreach ($penguji_nama_list as $index => $nama_dosen) {
                        if (empty(trim($nama_dosen))) continue;

                        $sql_get_dosen = "SELECT nomor_dosen FROM Dosen WHERE nama_dosen = ?";
                        $stmt_get_dosen = sqlsrv_query($conn, $sql_get_dosen, array($nama_dosen));
                        $dosen_data = sqlsrv_fetch_array($stmt_get_dosen, SQLSRV_FETCH_ASSOC);

                        if ($dosen_data) {
                            $nomor_dosen = $dosen_data['nomor_dosen'];
                            $bobot = isset($penguji_bobot_list[$index]) && is_numeric($penguji_bobot_list[$index]) ? (float)$penguji_bobot_list[$index] : 0.0;
                            
                            // A. Insert ke tabel Penjadwalan
                            $sql_insert_jadwal = "INSERT INTO Penjadwalan (id_sidang, nomor_dosen, peran_dosen) VALUES (?, ?, 0)";
                            if (sqlsrv_query($conn, $sql_insert_jadwal, array($id_sidang, $nomor_dosen)) === false) { $all_queries_ok = false; break; }

                            // B. Insert ke tabel Penilaian untuk SETIAP MAHASISWA
                            foreach ($list_nim_mahasiswa as $nim) {
                                $sql_insert_penilaian = "INSERT INTO Penilaian (id_sidang, nim, nomor_dosen, bobot_penilaian) VALUES (?, ?, ?, ?)";
                                $params_penilaian = array($id_sidang, $nim, $nomor_dosen, $bobot);
                                if(sqlsrv_query($conn, $sql_insert_penilaian, $params_penilaian) === false) {
                                    $all_queries_ok = false;
                                    $error_message = "Gagal insert bobot untuk mhs $nim.";
                                    break 2; // Keluar dari kedua loop
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    if ($all_queries_ok) {
        sqlsrv_commit($conn);
        echo json_encode(['status' => 'success', 'message' => 'Jadwal, penguji, dan bobot berhasil diubah!']);
    } else {
        sqlsrv_rollback($conn);
        echo json_encode(['status' => 'error', 'message' => $error_message ?: 'Terjadi kesalahan pada database.']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
?>