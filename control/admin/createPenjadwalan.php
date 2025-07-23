<?php
// FILE: createPenjadwalan.php (VERSI FINAL YANG BENAR)

// ==============================
// KONEKSI & HEADER
// ==============================
include "../../koneksi/koneksiJoin.php";
if ($conn === false) {
    die("Koneksi gagal: " . print_r(sqlsrv_errors(), true));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}
header('Content-Type: application/json');

// ==============================
// AMBIL DATA DARI FORM
// ==============================
$id_sidang = filter_input(INPUT_POST, 'id_sidang', FILTER_VALIDATE_INT);
$tipe_sidang = $_POST['tipe_sidang'] ?? '';
$ruangan = $_POST['ruangan'] ?? null;
$tanggal = $_POST['tanggal'] ?? null;
$jam_awal = $_POST['jam_awal'] ?? null;
$jam_akhir = $_POST['jam_akhir'] ?? null;

// ==============================
// VALIDASI AWAL
// ==============================
if (!$id_sidang) {
    echo json_encode(['status' => 'error', 'message' => 'ID Sidang tidak valid.']);
    exit;
}

// ==============================
// MULAI TRANSAKSI
// ==============================
if (sqlsrv_begin_transaction($conn) === false) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memulai transaksi database.']);
    exit;
}

$all_queries_ok = true;
$error_message = 'Gagal membuat jadwal. Terjadi kesalahan.';

// ==============================
// 1. CEK JADWAL YANG SUDAH ADA
// ==============================
$sql_check_exist = "SELECT id_sidang FROM Jadwal WHERE id_sidang = ?";
$stmt_check = sqlsrv_query($conn, $sql_check_exist, array($id_sidang));
if ($stmt_check && sqlsrv_has_rows($stmt_check)) {
    $all_queries_ok = false;
    $error_message = "Jadwal untuk sidang ini sudah pernah dibuat sebelumnya.";
}

// ==============================
// 2. INSERT JADWAL UTAMA
// ==============================
if ($all_queries_ok) {
    $sql_insert_jadwal = "INSERT INTO Jadwal (id_sidang, ruang_sidang, tanggal_sidang, jam_sidang, jam_selesai) VALUES (?, ?, ?, ?, ?)";
    $params_jadwal = array($id_sidang, $ruangan, $tanggal, $jam_awal, $jam_akhir);
    if (sqlsrv_query($conn, $sql_insert_jadwal, $params_jadwal) === false) {
        $all_queries_ok = false;
        $error_message = "Gagal menyimpan data jadwal utama.";
    }
}

// ==============================
// 3. AMBIL DAFTAR MAHASISWA
// ==============================
$list_nim_mahasiswa = [];
if ($all_queries_ok) {
    // Ambil nim perwakilan dari kelompok
  $list_nim_mahasiswa = [];
if ($all_queries_ok) {
    // Langsung ambil SEMUA NIM mahasiswa dari kelompok yang bersidang, bukan berdasarkan kelas.
    $sql_get_all_nim_in_group = "SELECT k.nim 
                                 FROM Sidang s 
                                 JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
                                 WHERE s.id_sidang = ?";
    
    $params_get_nim = array($id_sidang);
    $stmt_get_nim = sqlsrv_query($conn, $sql_get_all_nim_in_group, $params_get_nim);

    if ($stmt_get_nim) {
        while ($row = sqlsrv_fetch_array($stmt_get_nim, SQLSRV_FETCH_ASSOC)) {
            // Masukkan setiap NIM yang ditemukan ke dalam array
            $list_nim_mahasiswa[] = $row['nim'];
        }
    }

    // Cek jika setelah query, daftar mahasiswa tetap kosong (misal: ada sidang tapi tidak ada anggota di kelompoknya)
    if (empty($list_nim_mahasiswa)) {
        $all_queries_ok = false;
        // Anda bisa membuat pesan error lebih spesifik
        $error_message = "Gagal menemukan anggota mahasiswa dalam kelompok yang terkait dengan sidang ini."; 
    }
}
}
// ==============================================================================
// 4. FUNGSI BANTU UNTUK INSERT DOSEN (PENTING!)
//    Fungsi ini untuk menghindari duplikasi kode.
// ==============================================================================
function insertDosenDanPenilaian($conn, $id_sidang, $nama_dosen_list, $bobot_list, $peran, &$all_queries_ok, &$error_message, $list_nim_mahasiswa, $tipe_sidang) {
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

            // Insert ke Penjadwalan (hanya untuk TA)
            if ($tipe_sidang === 'Tugas Akhir') {
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
// 5. EKSEKUSI INSERT DOSEN
// ==============================
if ($all_queries_ok) {
    if ($tipe_sidang === 'Tugas Akhir') {
        // --- Ambil data Pembimbing dan Penguji dari form ---
        $pembimbing_nama_list = $_POST['pembimbing_nama'] ?? [];
        $pembimbing_bobot_list = $_POST['pembimbing_bobot'] ?? [];
        $penguji_nama_list = $_POST['penguji_nama'] ?? [];
        $penguji_bobot_list = $_POST['penguji_bobot'] ?? [];

        // --- Panggil fungsi untuk insert Pembimbing ---
        insertDosenDanPenilaian($conn, $id_sidang, $pembimbing_nama_list, $pembimbing_bobot_list, 1, $all_queries_ok, $error_message, $list_nim_mahasiswa, $tipe_sidang);
        
        // --- Panggil fungsi untuk insert Penguji (hanya jika pembimbing sukses) ---
        if ($all_queries_ok) {
            insertDosenDanPenilaian($conn, $id_sidang, $penguji_nama_list, $penguji_bobot_list, 0, $all_queries_ok, $error_message, $list_nim_mahasiswa, $tipe_sidang);
        }

    } elseif ($tipe_sidang === 'Semester') {
        // --- Ambil data Pengampu dari form ---
        $pengampu_nama_list = $_POST['pengampu_nama'] ?? [];
        $pengampu_bobot_list = $_POST['pengampu_bobot'] ?? [];

        // --- Panggil fungsi untuk insert Pengampu ---
        // Peran disini tidak terlalu penting jika tidak dimasukkan ke Penjadwalan, kita bisa set ke 2 (pengampu)
        insertDosenDanPenilaian($conn, $id_sidang, $pengampu_nama_list, $pengampu_bobot_list, 2, $all_queries_ok, $error_message, $list_nim_mahasiswa, $tipe_sidang);
    }
}

// ==============================
// 6. FINALISASI TRANSAKSI
// ==============================
if ($all_queries_ok) {
    // Kirim notifikasi ke mahasiswa dan dosen terkait
    require_once __DIR__ . '/../kirimNotifikasi.php';
    // Ambil info kelompok, judul, tanggal, jam, ruangan
    $sql_info = "SELECT s.id_kelompok, s.judul, j.tanggal_sidang, j.jam_sidang, j.jam_selesai, j.ruang_sidang FROM Sidang s JOIN Jadwal j ON s.id_sidang = j.id_sidang WHERE s.id_sidang = ?";
    $stmt_info = sqlsrv_query($conn, $sql_info, [$id_sidang]);
    $info = ($stmt_info && ($row = sqlsrv_fetch_array($stmt_info, SQLSRV_FETCH_ASSOC))) ? $row : [];
    $id_kelompok = $info['id_kelompok'] ?? '-';
    $judul = $info['judul'] ?? '-';
    $tanggal = $info['tanggal_sidang'] instanceof DateTime ? $info['tanggal_sidang']->format('Y-m-d') : ($info['tanggal_sidang'] ?? '-');
    $jam = ($info['jam_sidang'] && $info['jam_selesai']) ? $info['jam_sidang']->format('H:i') . ' - ' . $info['jam_selesai']->format('H:i') : '-';
    $ruangan = $info['ruang_sidang'] ?? '-';
    $pengirim = 'ad01';
    // Notif mahasiswa
    foreach ($list_nim_mahasiswa as $nim) {
        $pesan_mhs = "Jadwal sidang Anda telah dibuat. Silakan cek detail jadwal di sistem.";
        kirimNotifikasi($nim, $pesan_mhs, $pengirim, $conn);
    }
    // Notif dosen
    // Pembimbing
    if (!empty($pembimbing_nama_list)) {
        foreach ($pembimbing_nama_list as $nama) {
            if (empty(trim($nama))) continue;
            $sql_dosen = "SELECT nomor_dosen FROM Dosen WHERE nama_dosen = ?";
            $stmt_dosen = sqlsrv_query($conn, $sql_dosen, [$nama]);
            if ($stmt_dosen && ($row = sqlsrv_fetch_array($stmt_dosen, SQLSRV_FETCH_ASSOC))) {
                $pesan = "Anda dijadwalkan sebagai Pembimbing pada sidang kelompok $id_kelompok (judul: $judul) pada tanggal $tanggal jam $jam di ruangan $ruangan.";
                kirimNotifikasi($row['nomor_dosen'], $pesan, $pengirim, $conn);
            }
        }
    }
    // Penguji
    if (!empty($penguji_nama_list)) {
        foreach ($penguji_nama_list as $nama) {
            if (empty(trim($nama))) continue;
            $sql_dosen = "SELECT nomor_dosen FROM Dosen WHERE nama_dosen = ?";
            $stmt_dosen = sqlsrv_query($conn, $sql_dosen, [$nama]);
            if ($stmt_dosen && ($row = sqlsrv_fetch_array($stmt_dosen, SQLSRV_FETCH_ASSOC))) {
                $pesan = "Anda dijadwalkan sebagai Penguji pada sidang kelompok $id_kelompok (judul: $judul) pada tanggal $tanggal jam $jam di ruangan $ruangan.";
                kirimNotifikasi($row['nomor_dosen'], $pesan, $pengirim, $conn);
            }
        }
    }
    // Pengampu (untuk sidang Semester)
    if (!empty($pengampu_nama_list)) {
        foreach ($pengampu_nama_list as $nama) {
            if (empty(trim($nama))) continue;
            $sql_dosen = "SELECT nomor_dosen FROM Dosen WHERE nama_dosen = ?";
            $stmt_dosen = sqlsrv_query($conn, $sql_dosen, [$nama]);
            if ($stmt_dosen && ($row = sqlsrv_fetch_array($stmt_dosen, SQLSRV_FETCH_ASSOC))) {
                $pesan = "Anda dijadwalkan sebagai Pengampu pada sidang kelompok $id_kelompok (judul: $judul) pada tanggal $tanggal jam $jam di ruangan $ruangan.";
                kirimNotifikasi($row['nomor_dosen'], $pesan, $pengirim, $conn);
            }
        }
    }
    sqlsrv_commit($conn);
    echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil dibuat!']);
} else {
    sqlsrv_rollback($conn);
    echo json_encode(['status' => 'error', 'message' => $error_message]);
}
exit;
?>