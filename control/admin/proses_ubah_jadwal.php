<?php
// FILE: proses_ubah_jadwal.php (VERSI FINAL YANG BENAR)

include "../../koneksi/koneksiJoin.php";
if ($conn === false) {
    die("Koneksi gagal: " . print_r(sqlsrv_errors(), true));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
    exit;
}
header('Content-Type: application/json');

// ==============================
// AMBIL DATA & VALIDASI AWAL
// ==============================
$id_sidang = filter_input(INPUT_POST, 'id_sidang', FILTER_VALIDATE_INT);
$ruangan = $_POST['ruangan'] ?? null;
$tanggal = $_POST['tanggal'] ?? null;
$jam_awal = $_POST['jam_awal'] ?? null;
$jam_akhir = $_POST['jam_akhir'] ?? null;

if (!$id_sidang) {
    echo json_encode(['status' => 'error', 'message' => 'ID Sidang tidak valid.']);
    exit;
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

// ==============================
// 1. UPDATE JADWAL UTAMA
// ==============================
$sql_update_jadwal = "UPDATE Jadwal SET ruang_sidang = ?, tanggal_sidang = ?, jam_sidang = ?, jam_selesai = ? WHERE id_sidang = ?";
$params_jadwal = [$ruangan, $tanggal, $jam_awal, $jam_akhir, $id_sidang];
if (sqlsrv_query($conn, $sql_update_jadwal, $params_jadwal) === false) {
    $all_queries_ok = false;
    $error_message = "Gagal memperbarui data jadwal utama.";
}

// ==============================
// 2. HAPUS DATA LAMA (PENILAIAN & PENJADWALAN PENGUJI)
// ==============================
if ($all_queries_ok) {
    // Selalu hapus data penilaian lama, akan di-insert ulang
    $sql_delete_penilaian = "DELETE FROM Penilaian WHERE id_sidang = ?";
    if (sqlsrv_query($conn, $sql_delete_penilaian, [$id_sidang]) === false) {
        $all_queries_ok = false;
        $error_message = "Gagal membersihkan data penilaian lama.";
    }

    // Untuk TA, hapus juga data penguji lama dari penjadwalan
    if (isset($_POST['pembimbing_nama'])) {
        $sql_delete_penguji = "DELETE FROM Penjadwalan WHERE id_sidang = ? AND peran_dosen = 0";
        if (sqlsrv_query($conn, $sql_delete_penguji, [$id_sidang]) === false) {
            $all_queries_ok = false;
            $error_message = "Gagal membersihkan data penguji lama.";
        }
    }
}

// ==============================
// 3. AMBIL DAFTAR MAHASISWA
// ==============================
$list_nim_mahasiswa = [];
if ($all_queries_ok) {
    // Ambil nim perwakilan dari kelompok
    $sql_get_info = "SELECT k.nim FROM Sidang s JOIN Kelompok k ON s.id_kelompok = k.id_kelompok WHERE s.id_sidang = ?";
    $stmt_get_info = sqlsrv_query($conn, $sql_get_info, array($id_sidang));
    $info_data = sqlsrv_fetch_array($stmt_get_info, SQLSRV_FETCH_ASSOC);

    if ($info_data) {
        // Cari id_kelas dari nim perwakilan
        $sql_get_kelas = "SELECT TOP 1 id_kelas FROM Kelas_Mahasiswa WHERE nim = ?";
        $stmt_get_kelas = sqlsrv_query($conn, $sql_get_kelas, array($info_data['nim']));
        $kelas_data = sqlsrv_fetch_array($stmt_get_kelas, SQLSRV_FETCH_ASSOC);
        
        if ($kelas_data) {
            // Ambil semua NIM mahasiswa di kelas tersebut
            $sql_get_nim = "SELECT nim FROM Kelas_Mahasiswa WHERE id_kelas = ?";
            $stmt_get_nim = sqlsrv_query($conn, $sql_get_nim, array($kelas_data['id_kelas']));
            while ($row = sqlsrv_fetch_array($stmt_get_nim, SQLSRV_FETCH_ASSOC)) {
                $list_nim_mahasiswa[] = $row['nim'];
            }
        }
    }
    if (empty($list_nim_mahasiswa)) {
        $all_queries_ok = false;
        $error_message = "Gagal menemukan daftar mahasiswa dalam kelompok atau kelas.";
    }
}


// ==============================================================================
// 4. FUNGSI BANTU UNTUK INSERT DOSEN (Sama seperti createPenjadwalan)
// ==============================================================================
function insertDosenDanPenilaian($conn, $id_sidang, $nama_dosen_list, $bobot_list, $peran, &$all_queries_ok, &$error_message, $list_nim_mahasiswa, $is_ta) {
    $role_name = ($peran == 1) ? 'pembimbing' : (($peran == 0) ? 'penguji' : 'pengampu');

    foreach ($nama_dosen_list as $index => $nama_dosen) {
        if (empty(trim($nama_dosen))) continue;

        // Cari nomor dosen
        $sql_get_dosen = "SELECT nomor_dosen FROM Dosen WHERE nama_dosen = ?";
        $stmt_get_dosen = sqlsrv_query($conn, $sql_get_dosen, array($nama_dosen));
        $dosen_data = sqlsrv_fetch_array($stmt_get_dosen, SQLSRV_FETCH_ASSOC);

        if ($dosen_data) {
            $nomor_dosen = $dosen_data['nomor_dosen'];
            $bobot = isset($bobot_list[$index]) && is_numeric($bobot_list[$index]) ? (float)$bobot_list[$index] : 0.0;

            // Insert ke Penjadwalan (HANYA untuk penguji TA baru)
            if ($is_ta && $peran == 0) { // peran 0 = Penguji
                $sql_insert_penjadwalan = "INSERT INTO Penjadwalan (id_sidang, nomor_dosen, peran_dosen) VALUES (?, ?, ?)";
                if (sqlsrv_query($conn, $sql_insert_penjadwalan, array($id_sidang, $nomor_dosen, $peran)) === false) {
                    $all_queries_ok = false;
                    $error_message = "Gagal menyimpan data $role_name '$nama_dosen' ke penjadwalan.";
                    break;
                }
            }

            // Insert ke Penilaian untuk setiap mahasiswa
            foreach ($list_nim_mahasiswa as $nim) {
                $sql_insert_penilaian = "INSERT INTO Penilaian (id_sidang, nim, nomor_dosen, bobot_penilaian) VALUES (?, ?, ?, ?)";
                if (sqlsrv_query($conn, $sql_insert_penilaian, array($id_sidang, $nim, $nomor_dosen, $bobot)) === false) {
                    $all_queries_ok = false;
                    $error_message = "Gagal menyimpan data bobot $role_name untuk mahasiswa NIM $nim.";
                    break 2; // Keluar dari kedua loop
                }
            }
        } else {
            $all_queries_ok = false;
            $error_message = "Dosen $role_name dengan nama '$nama_dosen' tidak ditemukan.";
            break;
        }
    }
}

// ==============================
// 5. INSERT ULANG DATA DOSEN & BOBOT
// ==============================
if ($all_queries_ok) {
    if (isset($_POST['pembimbing_nama'])) { // Jika ini form TA
        $pembimbing_nama_list = $_POST['pembimbing_nama'] ?? [];
        $pembimbing_bobot_list = $_POST['pembimbing_bobot'] ?? [];
        $penguji_nama_list = $_POST['penguji_nama'] ?? [];
        $penguji_bobot_list = $_POST['penguji_bobot'] ?? [];

        // Insert ulang bobot Pembimbing
        insertDosenDanPenilaian($conn, $id_sidang, $pembimbing_nama_list, $pembimbing_bobot_list, 1, $all_queries_ok, $error_message, $list_nim_mahasiswa, true);
        
        // Insert ulang data Penguji
        if ($all_queries_ok) {
            insertDosenDanPenilaian($conn, $id_sidang, $penguji_nama_list, $penguji_bobot_list, 0, $all_queries_ok, $error_message, $list_nim_mahasiswa, true);
        }

    } elseif (isset($_POST['pengampu_nama'])) { // Jika ini form Semester
        $pengampu_nama_list = $_POST['pengampu_nama'] ?? [];
        $pengampu_bobot_list = $_POST['pengampu_bobot'] ?? [];

        // Insert ulang bobot Pengampu
        insertDosenDanPenilaian($conn, $id_sidang, $pengampu_nama_list, $pengampu_bobot_list, 2, $all_queries_ok, $error_message, $list_nim_mahasiswa, false);
    }
}


// ==============================
// 6. FINALISASI TRANSAKSI
// ==============================
if ($all_queries_ok) {
    sqlsrv_commit($conn);
    echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil diubah!']);
} else {
    sqlsrv_rollback($conn);
    echo json_encode(['status' => 'error', 'message' => $error_message]);
}
exit;
?>