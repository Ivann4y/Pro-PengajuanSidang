<?php
require "../../koneksi/koneksiAndrew.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
    exit;
}

header('Content-Type: application/json');

// Ambil semua data dari form
$id_sidang = filter_input(INPUT_POST, 'id_sidang', FILTER_VALIDATE_INT);
$ruangan = $_POST['ruangan'] ?? null;
$tanggal = $_POST['tanggal'] ?? null;
$jam_awal = $_POST['jam_awal'] ?? null;
$jam_akhir = $_POST['jam_akhir'] ?? null;

// Ambil data spesifik
$pembimbing_bobot = filter_input(INPUT_POST, 'pembimbing_bobot', FILTER_VALIDATE_FLOAT);
$penguji_nama_list = $_POST['penguji_nama'] ?? [];
$penguji_bobot_list = $_POST['penguji_bobot'] ?? [];
$pengampu_bobot_list = $_POST['pengampu_bobot'] ?? [];

if (!$id_sidang) {
    echo json_encode(['status' => 'error', 'message' => 'ID Sidang tidak valid.']);
    exit;
}

// ==============================
// VALIDASI TOTAL BOBOT
// ==============================
if ($pembimbing_bobot !== null) { // Cek jika ini adalah form TA
    $bobot_penguji_numerik = array_filter($penguji_bobot_list, 'is_numeric');
    $total_bobot = array_sum($bobot_penguji_numerik) + $pembimbing_bobot;
    
    if (abs($total_bobot - 100) > 0.01) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal: Total bobot (Pembimbing + Penguji) harus tepat 100%. Total saat ini: ' . $total_bobot . '%.']);
        exit;
    }
} elseif (!empty($pengampu_bobot_list)) { // Cek jika ini adalah form Semester
    $bobot_pengampu_numerik = array_filter($pengampu_bobot_list, 'is_numeric');
    $total_bobot = array_sum($bobot_pengampu_numerik);

    if (abs($total_bobot - 100) > 0.01) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal: Total bobot pengampu harus tepat 100%. Total saat ini: ' . $total_bobot . '%.']);
        exit;
    }
}

// ==============================
// MULAI TRANSAKSI
// ==============================
if (sqlsrv_begin_transaction($conn) === false) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memulai transaksi.']);
    exit;
}

$all_queries_ok = true;
$error_message = 'Gagal mengubah jadwal.';

// 1. UPDATE JADWAL
$sql_jadwal = "UPDATE Jadwal SET ruang_sidang = ?, tanggal_sidang = ?, jam_sidang = ?, jam_selesai = ? WHERE id_sidang = ?";
$params_jadwal = [$ruangan, $tanggal, $jam_awal, $jam_akhir, $id_sidang];
if (sqlsrv_query($conn, $sql_jadwal, $params_jadwal) === false) {
    $all_queries_ok = false;
    $error_message = "Gagal memperbarui data jadwal utama.";
}

// 2. PROSES DOSEN & BOBOT
if ($all_queries_ok) {
    
    // ================== BLOK IF UNTUK SIDANG TA ==================
    if ($pembimbing_bobot !== null) {
        // Hapus semua entri penilaian lama (pembimbing dan penguji)
        $sql_delete_penilaian = "DELETE FROM Penilaian WHERE id_sidang = ?";
        if (sqlsrv_query($conn, $sql_delete_penilaian, [$id_sidang]) === false) {
            $all_queries_ok = false;
            $error_message = "Gagal membersihkan data penilaian lama.";
        }

        // Hapus semua penguji lama dari Penjadwalan (pembimbing tidak dihapus)
        $sql_delete_penguji = "DELETE FROM Penjadwalan WHERE id_sidang = ? AND peran_dosen = 0";
        if (sqlsrv_query($conn, $sql_delete_penguji, [$id_sidang]) === false) {
            $all_queries_ok = false;
            $error_message = "Gagal membersihkan data penguji lama.";
        }

        // Ambil daftar NIM mahasiswa dalam kelompok ini
        $sql_get_nim = "SELECT nim FROM Kelompok WHERE id_kelompok = (SELECT id_kelompok FROM Sidang WHERE id_sidang = ?)";
        $stmt_get_nim = sqlsrv_query($conn, $sql_get_nim, [$id_sidang]);
        $list_nim_mahasiswa = [];
        while($row = sqlsrv_fetch_array($stmt_get_nim, SQLSRV_FETCH_ASSOC)) {
            $list_nim_mahasiswa[] = $row['nim'];
        }

        if(empty($list_nim_mahasiswa)) {
            $all_queries_ok = false;
            $error_message = "Tidak dapat menemukan mahasiswa dalam kelompok.";
        }

        // INSERT ULANG SEMUA DATA PENILAIAN & PENJADWALAN PENGUJI
        if ($all_queries_ok) {
            // A. Insert/Update bobot pembimbing
            $sql_get_pembimbing = "SELECT nomor_dosen FROM Penjadwalan WHERE id_sidang = ? AND peran_dosen = 1";
            $stmt_get_pembimbing = sqlsrv_query($conn, $sql_get_pembimbing, [$id_sidang]);
            $pembimbing_data = sqlsrv_fetch_array($stmt_get_pembimbing, SQLSRV_FETCH_ASSOC);

            if ($pembimbing_data) {
                $nomor_pembimbing = $pembimbing_data['nomor_dosen'];
                foreach ($list_nim_mahasiswa as $nim) {
                    $sql_insert_penilaian = "INSERT INTO Penilaian (id_sidang, nim, nomor_dosen, bobot_penilaian) VALUES (?, ?, ?, ?)";
                    if(sqlsrv_query($conn, $sql_insert_penilaian, [$id_sidang, $nim, $nomor_pembimbing, $pembimbing_bobot]) === false) {
                        $all_queries_ok = false; $error_message = "Gagal insert bobot pembimbing."; break;
                    }
                }
            }

            // B. Insert penguji dan bobotnya
            if ($all_queries_ok) {
                foreach ($penguji_nama_list as $index => $nama_dosen) {
                    if (empty(trim($nama_dosen))) continue;
                    
                    $sql_get_dosen = "SELECT nomor_dosen FROM Dosen WHERE nama_dosen = ?";
                    $dosen_data = sqlsrv_fetch_array(sqlsrv_query($conn, $sql_get_dosen, [$nama_dosen]), SQLSRV_FETCH_ASSOC);

                    if ($dosen_data) {
                        $nomor_dosen = $dosen_data['nomor_dosen'];
                        $bobot = $penguji_bobot_list[$index] ?? 0.0;
                        
                        sqlsrv_query($conn, "INSERT INTO Penjadwalan (id_sidang, nomor_dosen, peran_dosen) VALUES (?, ?, 0)", [$id_sidang, $nomor_dosen]);
                        
                        foreach ($list_nim_mahasiswa as $nim) {
                            if(sqlsrv_query($conn, "INSERT INTO Penilaian (id_sidang, nim, nomor_dosen, bobot_penilaian) VALUES (?, ?, ?, ?)", [$id_sidang, $nim, $nomor_dosen, $bobot]) === false) {
                                $all_queries_ok = false; $error_message = "Gagal insert bobot penguji."; break 2;
                            }
                        }
                    }
                }
            }
        }
    } 
    // ================== BLOK ELSEIF UNTUK SIDANG SEMESTER ==================
   elseif (!empty($pengampu_bobot_list)) {
    
    // Hapus entri penilaian lama
    $sql_delete_penilaian = "DELETE FROM Penilaian WHERE id_sidang = ?";
    if (sqlsrv_query($conn, $sql_delete_penilaian, [$id_sidang]) === false) {
        $all_queries_ok = false;
        $error_message = "Gagal membersihkan data penilaian pengampu lama.";
    }

    if ($all_queries_ok) {
        // Ambil ID Matkul dan Kelompok
        $sql_info = "SELECT TOP 1 s.id_kelompok, ds.id_matkul FROM Sidang s JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang WHERE s.id_sidang = ?";
        $stmt_info = sqlsrv_query($conn, $sql_info, [$id_sidang]);
        $info_data = sqlsrv_fetch_array($stmt_info, SQLSRV_FETCH_ASSOC);

        if ($info_data) {
            $id_matkul = $info_data['id_matkul'];
            $id_kelompok = $info_data['id_kelompok'];

            // Query untuk mengambil nomor dosen pengampu (logika yang sama dengan di queries.php)
            $sql_get_pengampu = "SELECT d.nomor_dosen 
                FROM Dosen d 
                JOIN Pengampu_Kelas pk ON d.nomor_dosen = pk.nomor_dosen 
                WHERE pk.id_matkul = ? AND pk.id_kelas = (
                    SELECT TOP 1 k_mhs.id_kelas
                    FROM Kelompok klp JOIN Mahasiswa mhs ON klp.nim = mhs.nim
                    JOIN Kelas_Mahasiswa k_mhs ON mhs.nim = k_mhs.nim WHERE klp.id_kelompok = ?
                )";
            $stmt_get_pengampu = sqlsrv_query($conn, $sql_get_pengampu, [$id_matkul, $id_kelompok]);
            
            $nomor_dosen_pengampu = [];
            while($row = sqlsrv_fetch_array($stmt_get_pengampu, SQLSRV_FETCH_ASSOC)) {
                $nomor_dosen_pengampu[] = $row['nomor_dosen'];
            }

            // Ambil daftar NIM mahasiswa dari kelompok
            $sql_get_nim = "SELECT nim FROM Kelompok WHERE id_kelompok = ?";
            $stmt_get_nim = sqlsrv_query($conn, $sql_get_nim, [$id_kelompok]);
            $list_nim_mahasiswa = [];
            while($row = sqlsrv_fetch_array($stmt_get_nim, SQLSRV_FETCH_ASSOC)) {
                $list_nim_mahasiswa[] = $row['nim'];
            }

            // Insert ulang bobot untuk setiap pengampu
            if (!empty($list_nim_mahasiswa) && !empty($nomor_dosen_pengampu)) {
                foreach ($nomor_dosen_pengampu as $index => $nomor_dosen) {
                    $bobot = $pengampu_bobot_list[$index] ?? 0.0;
                    foreach ($list_nim_mahasiswa as $nim) {
                        $sql_insert = "INSERT INTO Penilaian (id_sidang, nim, nomor_dosen, bobot_penilaian) VALUES (?, ?, ?, ?)";
                        if(sqlsrv_query($conn, $sql_insert, [$id_sidang, $nim, $nomor_dosen, $bobot]) === false) {
                            $all_queries_ok = false; 
                            $error_message = "Gagal insert bobot pengampu."; 
                            break 2;
                        }
                    }
                }
            }
        } else {
            $all_queries_ok = false;
            $error_message = "Gagal mengambil info matkul/kelompok untuk update.";
        }
    }
}
            }


// FINALISASI TRANSAKSI
if ($all_queries_ok) {
    sqlsrv_commit($conn);
    echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil diubah!']);
} else {
    sqlsrv_rollback($conn);
    echo json_encode(['status' => 'error', 'message' => $error_message]);
}
exit;