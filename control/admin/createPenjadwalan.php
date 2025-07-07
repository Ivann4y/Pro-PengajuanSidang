<?php
// ==============================
// KONEKSI DATABASE
// ==============================
require "../../koneksi/koneksiAndrew.php"; // Menghubungkan ke database SQL Server

// ==============================
// CEK REQUEST METHOD
// ==============================

// Pastikan request adalah POST (bukan GET, dsb)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Set HTTP status code 405 (Method Not Allowed)
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']); // Kirim pesan error dalam format JSON
    exit; // Hentikan eksekusi script
}

// Set header response agar hasilnya berupa JSON
header('Content-Type: application/json');

// ==============================
// AMBIL DATA DARI FORM (POST)
// ==============================

// Ambil dan validasi id_sidang (harus integer)
$id_sidang = filter_input(INPUT_POST, 'id_sidang', FILTER_VALIDATE_INT);
// Ambil tipe sidang (Tugas Akhir/Semester)
$tipe_sidang = $_POST['tipe_sidang'] ?? '';
// Ambil data ruangan, tanggal, jam mulai, jam selesai dari form
$ruangan = $_POST['ruangan'] ?? null;
$tanggal = $_POST['tanggal'] ?? null;
$jam_awal = $_POST['jam_awal'] ?? null;
$jam_akhir = $_POST['jam_akhir'] ?? null;

// ==============================
// AMBIL DATA DOSEN SESUAI TIPE SIDANG
// ==============================

if ($tipe_sidang === 'Tugas Akhir') {
    // Jika sidang TA, ambil nama pembimbing dan daftar penguji beserta bobotnya
    $pembimbing_nama = $_POST['pembimbing_nama'] ?? null;
    $dosen_nama_list = $_POST['penguji_nama'] ?? [];
    $dosen_bobot_list = $_POST['penguji_bobot'] ?? [];
    $peran_dosen = 0; // 0 untuk Penguji
} elseif ($tipe_sidang === 'Semester') {
    // Jika sidang Semester, ambil daftar dosen pengampu beserta bobotnya
    $dosen_nama_list = $_POST['pengampu_nama'] ?? [];
    $dosen_bobot_list = $_POST['pengampu_bobot'] ?? [];
    // Tidak perlu peran_dosen = 2 jika tidak ingin memasukkan pengampu ke Penjadwalan
} else {
    // Jika tipe sidang tidak valid, kirim error dan hentikan script
    echo json_encode(['status' => 'error', 'message' => 'Tipe sidang tidak valid.']);
    exit;
}

// ==============================
// VALIDASI AWAL DATA
// ==============================

// Validasi id_sidang harus ada dan valid
if (!$id_sidang) {
    echo json_encode(['status' => 'error', 'message' => 'ID Sidang tidak valid.']);
    exit;
}

// Validasi total bobot dosen (tidak boleh lebih dari 100%)
if (!empty($dosen_bobot_list)) {
    $bobot_numerik = array_filter($dosen_bobot_list, 'is_numeric'); // Ambil hanya nilai numerik dari bobot
    $total_bobot = array_sum($bobot_numerik); // Hitung total bobot
    if ($total_bobot > 100) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal: Total bobot penilaian tidak boleh melebihi 100%. Total saat ini: ' . $total_bobot . '%.']);
        exit;
    }
}

// ==============================
// MULAI TRANSAKSI DATABASE
// ==============================

if (sqlsrv_begin_transaction($conn) === false) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal memulai transaksi database.']);
    exit;
}

$all_queries_ok = true; // Flag untuk cek apakah semua query sukses
$error_message = 'Gagal membuat jadwal. Terjadi kesalahan.'; // Pesan error default

// ==============================
// 1. CEK APAKAH JADWAL SUDAH ADA
// ==============================

$sql_check_exist = "SELECT id_sidang FROM Jadwal WHERE id_sidang = ?";
$stmt_check = sqlsrv_query($conn, $sql_check_exist, array($id_sidang)); // Cek apakah jadwal sudah ada
if ($stmt_check && sqlsrv_has_rows($stmt_check)) {
    $all_queries_ok = false;
    $error_message = "Jadwal untuk sidang ini sudah pernah dibuat sebelumnya.";
}

// ==============================
// 2. INSERT DATA JADWAL JIKA BELUM ADA
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
// 3. AMBIL DAFTAR MAHASISWA DALAM KELOMPOK
// ==============================

$list_nim_mahasiswa = []; // Array untuk menampung NIM mahasiswa dalam kelompok
if ($all_queries_ok) {
    // Ambil NIM perwakilan dari kelompok sidang
    $sql_get_info = "SELECT k.nim FROM Sidang s JOIN Kelompok k ON s.id_kelompok = k.id_kelompok WHERE s.id_sidang = ?";
    $stmt_get_info = sqlsrv_query($conn, $sql_get_info, array($id_sidang));
    $info_data = sqlsrv_fetch_array($stmt_get_info, SQLSRV_FETCH_ASSOC);

    if ($info_data) {
        $nim_perwakilan = $info_data['nim'];
        // Cari id_kelas dari nim perwakilan
        $sql_get_kelas = "SELECT TOP 1 id_kelas FROM Kelas_Mahasiswa WHERE nim = ?";
        $stmt_get_kelas = sqlsrv_query($conn, $sql_get_kelas, array($nim_perwakilan));
        $kelas_data = sqlsrv_fetch_array($stmt_get_kelas, SQLSRV_FETCH_ASSOC);
        
        if ($kelas_data) {
            $id_kelas = $kelas_data['id_kelas'];
            // Ambil semua NIM mahasiswa yang ada di kelas tersebut
            $sql_get_nim = "SELECT nim FROM Kelas_Mahasiswa WHERE id_kelas = ?";
            $stmt_get_nim = sqlsrv_query($conn, $sql_get_nim, array($id_kelas));
            while ($row = sqlsrv_fetch_array($stmt_get_nim, SQLSRV_FETCH_ASSOC)) {
                $list_nim_mahasiswa[] = $row['nim'];
            }
        }
    }

    // Validasi: pastikan daftar mahasiswa tidak kosong
    if (empty($list_nim_mahasiswa)) {
        $all_queries_ok = false;
        $error_message = "Gagal menemukan daftar mahasiswa dalam kelompok atau kelas.";
    }
}

// ==============================
// 4. INSERT DATA DOSEN DAN PENILAIAN
// ==============================

if ($all_queries_ok) {
    // =====================================================================================
    // AWAL PERBAIKAN: Kode duplikat dihapus, hanya blok ini yang tersisa.
    // =====================================================================================
    if ($tipe_sidang === 'Tugas Akhir') {
        // --- A. Insert data pembimbing (khusus TA) ---
        if (isset($pembimbing_nama) && !empty($pembimbing_nama)) {
            $sql_get_pembimbing = "SELECT nomor_dosen FROM Dosen WHERE nama_dosen = ?";
            $stmt_get_pembimbing = sqlsrv_query($conn, $sql_get_pembimbing, array($pembimbing_nama));
            $pembimbing_data = sqlsrv_fetch_array($stmt_get_pembimbing, SQLSRV_FETCH_ASSOC);

            if ($pembimbing_data) {
                $nomor_pembimbing = $pembimbing_data['nomor_dosen'];
                // Insert ke Penjadwalan dengan peran_dosen = 1 (pembimbing)
                $sql_insert_pembimbing = "INSERT INTO Penjadwalan (id_sidang, nomor_dosen, peran_dosen) VALUES (?, ?, 1)";
                if (sqlsrv_query($conn, $sql_insert_pembimbing, array($id_sidang, $nomor_pembimbing)) === false) {
                    $all_queries_ok = false;
                    $error_message = "Gagal menyimpan data pembimbing.";
                }
            } else {
                $all_queries_ok = false;
                $error_message = "Dosen pembimbing dengan nama '$pembimbing_nama' tidak ditemukan.";
            }
        } else {
             $all_queries_ok = false;
             $error_message = "Nama dosen pembimbing tidak terkirim dari form.";
        }
    }
    
    // --- B. Insert data dosen penguji/pengampu dan penilaian ---
    if ($all_queries_ok) { // Cek lagi sebelum masuk loop
        foreach ($dosen_nama_list as $index => $nama_dosen) {
            if (empty(trim($nama_dosen))) continue; // Lewati jika nama dosen kosong

            // Cari nomor dosen berdasarkan nama
            $sql_get_dosen = "SELECT nomor_dosen FROM Dosen WHERE nama_dosen = ?";
            $stmt_get_dosen = sqlsrv_query($conn, $sql_get_dosen, array($nama_dosen));
            $dosen_data = sqlsrv_fetch_array($stmt_get_dosen, SQLSRV_FETCH_ASSOC);

            if ($dosen_data) {
                $nomor_dosen = $dosen_data['nomor_dosen'];
                // Ambil bobot penilaian dari input, default 0 jika tidak valid
                $bobot = isset($dosen_bobot_list[$index]) && is_numeric($dosen_bobot_list[$index]) ? (float)$dosen_bobot_list[$index] : 0.0;

                // Insert ke tabel Penjadwalan hanya untuk penguji (TA)
                if ($tipe_sidang === 'Tugas Akhir') {
                    $sql_insert_penjadwalan = "INSERT INTO Penjadwalan (id_sidang, nomor_dosen, peran_dosen) VALUES (?, ?, 0)"; // peran_dosen = 0 (Penguji)
                    if (sqlsrv_query($conn, $sql_insert_penjadwalan, array($id_sidang, $nomor_dosen)) === false) {
                        $all_queries_ok = false;
                        $error_message = "Gagal menyimpan data penguji '$nama_dosen' ke penjadwalan.";
                        break;
                    }
                }

                // Insert ke tabel Penilaian untuk setiap mahasiswa
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
}

// ==============================
// FINALISASI TRANSAKSI DATABASE
// ==============================

if ($all_queries_ok) {
    sqlsrv_commit($conn); // Simpan semua perubahan ke database
    echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil dibuat!']);
} else {
    sqlsrv_rollback($conn); // Batalkan semua perubahan jika ada error
    echo json_encode(['status' => 'error', 'message' => $error_message]);
}
exit; // Hentikan eksekusi script
?>