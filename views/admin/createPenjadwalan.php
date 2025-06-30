<?php
require "../../koneksi/koneksiAndrew.php";

// Pastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

header('Content-Type: application/json');

// --- Ambil Data dari Form ---
$id_sidang = filter_input(INPUT_POST, 'id_sidang', FILTER_VALIDATE_INT);
$tipe_sidang = $_POST['tipe_sidang'] ?? ''; // 'TA' atau 'Semester'
$ruangan = $_POST['ruangan'] ?? null;
$tanggal = $_POST['tanggal'] ?? null;
$jam_awal = $_POST['jam_awal'] ?? null;
$jam_akhir = $_POST['jam_akhir'] ?? null;

// Ambil data dosen tergantung tipe sidang
if ($tipe_sidang === 'TA') {
    $dosen_nama_list = $_POST['penguji_nama'] ?? [];
    $dosen_bobot_list = $_POST['penguji_bobot'] ?? [];
    $peran_dosen = 0; // 0 untuk Penguji
} elseif ($tipe_sidang === 'Semester') {
    $dosen_nama_list = $_POST['pengampu_nama'] ?? [];
    $dosen_bobot_list = $_POST['pengampu_bobot'] ?? [];
    $peran_dosen = 2; // Asumsi 2 untuk Pengampu
} else {
    echo json_encode(['status' => 'error', 'message' => 'Tipe sidang tidak valid.']);
    exit;
}

// --- Validasi Awal ---
if (!$id_sidang) {
    echo json_encode(['status' => 'error', 'message' => 'ID Sidang tidak valid.']);
    exit;
}

// Validasi total bobot
if (!empty($dosen_bobot_list)) {
    $bobot_numerik = array_filter($dosen_bobot_list, 'is_numeric');
    $total_bobot = array_sum($bobot_numerik);
    
    if ($total_bobot > 100) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal: Total bobot penilaian tidak boleh melebihi 100%. Total saat ini: ' . $total_bobot . '%.']);
        exit;
    }
}

// --- Mulai Transaksi Database ---
if (sqlsrv_begin_transaction($conn) === false) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memulai transaksi database.']);
    exit;
}

$all_queries_ok = true;
$error_message = 'Gagal membuat jadwal. Terjadi kesalahan.';

// 1. Cek apakah jadwal untuk id_sidang ini sudah ada
$sql_check_exist = "SELECT id_sidang FROM Jadwal WHERE id_sidang = ?";
$stmt_check = sqlsrv_query($conn, $sql_check_exist, array($id_sidang));
if ($stmt_check && sqlsrv_has_rows($stmt_check)) {
    $all_queries_ok = false;
    $error_message = "Jadwal untuk sidang ini sudah pernah dibuat sebelumnya.";
}

// 2. Jika belum ada, insert ke tabel Jadwal
if ($all_queries_ok) {
    $sql_insert_jadwal = "INSERT INTO Jadwal (id_sidang, ruang_sidang, tanggal_sidang, jam_sidang, jam_selesai) VALUES (?, ?, ?, ?, ?)";
    $params_jadwal = array($id_sidang, $ruangan, $tanggal, $jam_awal, $jam_akhir);
    if (sqlsrv_query($conn, $sql_insert_jadwal, $params_jadwal) === false) {
        $all_queries_ok = false;
        $error_message = "Gagal menyimpan data jadwal utama.";
    }
}

// 3. Ambil data mahasiswa dari kelompok
$list_nim_mahasiswa = [];
if ($all_queries_ok) {
    $sql_get_kelompok = "SELECT id_kelompok FROM Sidang WHERE id_sidang = ?";
    $stmt_get_kelompok = sqlsrv_query($conn, $sql_get_kelompok, array($id_sidang));
    $kelompok_data = sqlsrv_fetch_array($stmt_get_kelompok, SQLSRV_FETCH_ASSOC);

    if ($kelompok_data) {
        $id_kelompok = $kelompok_data['id_kelompok'];
        $sql_get_nim = "SELECT nim FROM Kelompok_Mahasiswa WHERE id_kelompok = ?";
        $stmt_get_nim = sqlsrv_query($conn, $sql_get_nim, array($id_kelompok));
        while ($row = sqlsrv_fetch_array($stmt_get_nim, SQLSRV_FETCH_ASSOC)) {
            $list_nim_mahasiswa[] = $row['nim'];
        }
    }
    if (empty($list_nim_mahasiswa)) {
        $all_queries_ok = false;
        $error_message = "Gagal menemukan mahasiswa dalam kelompok.";
    }
}

// 4. Insert data dosen (penguji/pengampu) dan bobotnya
if ($all_queries_ok) {
    foreach ($dosen_nama_list as $index => $nama_dosen) {
        if (empty(trim($nama_dosen))) continue;

        // Cari nomor dosen berdasarkan nama
        $sql_get_dosen = "SELECT nomor_dosen FROM Dosen WHERE nama_dosen = ?";
        $stmt_get_dosen = sqlsrv_query($conn, $sql_get_dosen, array($nama_dosen));
        $dosen_data = sqlsrv_fetch_array($stmt_get_dosen, SQLSRV_FETCH_ASSOC);

        if ($dosen_data) {
            $nomor_dosen = $dosen_data['nomor_dosen'];
            $bobot = isset($dosen_bobot_list[$index]) && is_numeric($dosen_bobot_list[$index]) ? (float)$dosen_bobot_list[$index] : 0.0;

            // A. Insert ke tabel Penjadwalan
            $sql_insert_penjadwalan = "INSERT INTO Penjadwalan (id_sidang, nomor_dosen, peran_dosen) VALUES (?, ?, ?)";
            if (sqlsrv_query($conn, $sql_insert_penjadwalan, array($id_sidang, $nomor_dosen, $peran_dosen)) === false) {
                $all_queries_ok = false;
                $error_message = "Gagal menyimpan data dosen ke penjadwalan.";
                break;
            }

            // B. Insert ke tabel Penilaian untuk SETIAP MAHASISWA
            foreach ($list_nim_mahasiswa as $nim) {
                $sql_insert_penilaian = "INSERT INTO Penilaian (id_sidang, nim, nomor_dosen, bobot_penilaian) VALUES (?, ?, ?, ?)";
                $params_penilaian = array($id_sidang, $nim, $nomor_dosen, $bobot);
                if (sqlsrv_query($conn, $sql_insert_penilaian, $params_penilaian) === false) {
                    $all_queries_ok = false;
                    $error_message = "Gagal menyimpan data bobot untuk mahasiswa NIM $nim.";
                    break 2; // Keluar dari kedua loop (dosen dan mahasiswa)
                }
            }
        } else {
             $all_queries_ok = false;
             $error_message = "Dosen dengan nama '$nama_dosen' tidak ditemukan.";
             break;
        }
    }
}

// --- Finalisasi Transaksi ---
if ($all_queries_ok) {
    sqlsrv_commit($conn);
    echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil dibuat!']);
} else {
    sqlsrv_rollback($conn);
    echo json_encode(['status' => 'error', 'message' => $error_message]);
}
exit;
?>