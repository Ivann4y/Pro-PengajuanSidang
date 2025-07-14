<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../../koneksi/koneksiAndrew.php"; 

if ($conn === false) {
    die("Koneksi ke database gagal: " . print_r(sqlsrv_errors(), true));
}

// --- Inisialisasi session id_sidang_aktif dan validasi dosen ---
if (isset($_GET['id_sidang']) && is_numeric($_GET['id_sidang'])) {
    $id_sidang = (int)$_GET['id_sidang'];
    $_SESSION['id_sidang_aktif'] = $id_sidang;
} elseif (isset($_SESSION['id_sidang_aktif'])) {
    $id_sidang = (int)$_SESSION['id_sidang_aktif'];
} else {
    die("Error: ID sidang tidak valid atau tidak ditemukan.");
}

// Pastikan data user dan nomor_dosen ada di session
if (!isset($_SESSION['user_data']['nomor_dosen'])) {
    die("Error: Data dosen tidak ditemukan di session. Silakan login kembali.");
}
$loggedInNomorDosen = $_SESSION['user_data']['nomor_dosen'];

$error_message = ''; 

$mahasiswa = [];
$nama_matkul = '';
$dosen_terkait_sidang = ''; 
$id_kelompok = null;
$id_matkul = null; 
$jenis_sidang = null;
$current_nim = '';
$allStudentsGradesComplete = false;
$judul = '';
$nomor_kelompok = '';

$sql_detail = "SELECT DISTINCT k.nomor_kelompok, k.id_kelompok, k.jenis_sidang, k.id_matkul, s.judul
                FROM Sidang s, Kelompok k
                WHERE s.id_sidang = ? AND s.id_kelompok = k.id_kelompok";
$stmt_detail = sqlsrv_query($conn, $sql_detail, array($id_sidang));

if ($stmt_detail === false) {
    error_log("Query detail sidang gagal: " . print_r(sqlsrv_errors(), true));
    $error_message = "Terjadi kesalahan saat mengambil detail sidang. Mohon coba lagi.";
    $id_sidang = 0;
} else {
    $detail = sqlsrv_fetch_array($stmt_detail, SQLSRV_FETCH_ASSOC);
    if ($detail) {
        $nomor_kelompok = $detail['nomor_kelompok'];
        $id_kelompok = $detail['id_kelompok'];
        $id_matkul = $detail['id_matkul'];
        $jenis_sidang = $detail['jenis_sidang'];
        $judul = $detail['judul'];
    } else {
        $error_message = "Data Sidang dengan ID: " . htmlspecialchars($id_sidang ?? '') . " tidak ditemukan."; // Perbaikan: handle null
        $id_sidang = 0;
    }
}

if ($id_sidang) {
    $sql_mhs = "SELECT DISTINCT k.nim, m.nama_mhs
                FROM Kelompok k
                JOIN Mahasiswa m ON k.nim = m.nim
                WHERE k.nomor_kelompok = ?
                ORDER BY k.nim";
    $stmt_mhs = sqlsrv_query($conn, $sql_mhs, array($nomor_kelompok));
    
    if ($stmt_mhs === false) {
        error_log("Query mahasiswa dari Kelompok_Mahasiswa gagal: " . print_r(sqlsrv_errors(), true));
        $error_message = "Terjadi kesalahan saat mengambil data mahasiswa dari kelompok.";
        $mahasiswa = []; 
    } else {
        while ($row = sqlsrv_fetch_array($stmt_mhs, SQLSRV_FETCH_ASSOC)) {
            $mahasiswa[] = $row;
        }
    }

    if (isset($_GET['nim']) && in_array($_GET['nim'], array_column($mahasiswa, 'nim'))) {
        $current_nim = $_GET['nim'];
    } elseif (!empty($mahasiswa)) {
        $current_nim = $mahasiswa[0]['nim'];
    }
} else {
    $mahasiswa = [];
    $error_message = "ID Sidang tidak valid.";
}

if ($id_matkul) {
    $sql_matkul = "SELECT nama_matkul FROM MataKuliah WHERE id_matkul = ?";
    $stmt_matkul = sqlsrv_query($conn, $sql_matkul, array($id_matkul));
    
    if ($stmt_matkul === false) {
        error_log("Query mata kuliah gagal: " . print_r(sqlsrv_errors(), true));
        $error_message = "Terjadi kesalahan saat mengambil nama mata kuliah.";
        $nama_matkul = '';
    } else {
        $row_matkul = sqlsrv_fetch_array($stmt_matkul, SQLSRV_FETCH_ASSOC);
        if ($row_matkul) {
            $nama_matkul = $row_matkul['nama_matkul'];
        }
    }
}

if ($jenis_sidang === 'Tugas Akhir') { 
    error_log("Debug - jenis_sidang: " . $jenis_sidang . ", nomor_kelompok: " . $nomor_kelompok);
    
    $sql_dosen_ta = "SELECT DISTINCT d.nama_dosen 
                     FROM Dosen d 
                     JOIN Bimbingan b ON d.nomor_dosen = b.nomor_dosen 
                     WHERE b.id_kelompok = ? AND b.isPembimbing = 1"; 
    $stmt_dosen_ta = sqlsrv_query($conn, $sql_dosen_ta, array($id_kelompok));
    if ($stmt_dosen_ta === false) {
        error_log("Query dosen TA gagal: " . print_r(sqlsrv_errors(), true));
        $error_message = "Terjadi kesalahan saat mengambil data dosen terkait sidang TA.";
        $dosen_terkait_sidang = '';
    } else {
        $row = sqlsrv_fetch_array($stmt_dosen_ta, SQLSRV_FETCH_ASSOC);
        if ($row) {
            $dosen_terkait_sidang = $row['nama_dosen'];
            error_log("Debug - Dosen TA ditemukan: " . $dosen_terkait_sidang);
        } else {
            $dosen_terkait_sidang = 'Dosen tidak ditemukan';
            error_log("Debug - Dosen TA tidak ditemukan untuk id_kelompok: " . $id_kelompok);
        }
    }
} elseif ($jenis_sidang === 'Semester' && $id_matkul) {
    // Debug: Log the variables
    error_log("Debug - jenis_sidang: " . $jenis_sidang . ", id_sidang: " . $id_sidang . ", id_matkul: " . $id_matkul);
    
    $sql_dosen_semester = "SELECT TOP 1 d.nama_dosen FROM Dosen d, Pengampu_Kelas pk, Detail_Sidang ds WHERE ds.id_sidang = ? AND pk.id_matkul = ds.id_matkul AND pk.nomor_dosen = d.nomor_dosen";
    $stmt_dosen_semester = sqlsrv_query($conn, $sql_dosen_semester, array($id_sidang));
    if ($stmt_dosen_semester === false) {
        error_log("Query dosen semester gagal: " . print_r(sqlsrv_errors(), true));
        $error_message = "Terjadi kesalahan saat mengambil data dosen terkait sidang semester.";
        $dosen_terkait_sidang = '';
    } else {
        $row = sqlsrv_fetch_array($stmt_dosen_semester, SQLSRV_FETCH_ASSOC);
        if ($row) {
            $dosen_terkait_sidang = $row['nama_dosen'];
            error_log("Debug - Dosen Semester ditemukan: " . $dosen_terkait_sidang);
        } else {
            $dosen_terkait_sidang = 'Dosen tidak ditemukan';
            error_log("Debug - Dosen Semester tidak ditemukan untuk id_sidang: " . $id_sidang);
        }
    }
} else {
    // Debug: Log when no condition is met
    error_log("Debug - Tidak ada kondisi yang terpenuhi. jenis_sidang: " . $jenis_sidang . ", id_matkul: " . $id_matkul);
    $dosen_terkait_sidang = 'Jenis sidang tidak valid';
}

// LOGIKA VALIDASI UNTUK TOMBOL "KIRIM" (Interpretasi B: semua mahasiswa telah dinilai oleh dosen ini)
$allStudentsGradesComplete = true;
if (!empty($mahasiswa)) {
    foreach ($mahasiswa as $mhs_item) {
        $nim_check = $mhs_item['nim'];
        $sql_check_all_grades = "SELECT n_dokumen, n_presentasi, n_tanyajawab, n_proyek
                                 FROM Penilaian
                                 WHERE id_sidang = ? AND nim = ? AND nomor_dosen = ?";
        $stmt_check_all_grades = sqlsrv_query($conn, $sql_check_all_grades, array($id_sidang, $nim_check, $loggedInNomorDosen));
        
        if ($stmt_check_all_grades === false) {
            error_log("Query cek kelengkapan nilai gagal (all students): " . print_r(sqlsrv_errors(), true));
            $allStudentsGradesComplete = false; 
            $error_message = "Terjadi kesalahan saat memeriksa kelengkapan nilai.";
            break; 
        }
        
        $grade_row = sqlsrv_fetch_array($stmt_check_all_grades, SQLSRV_FETCH_ASSOC);
        
        if (
            !$grade_row ||
            !isset($grade_row['n_dokumen']) || $grade_row['n_dokumen'] === null || $grade_row['n_dokumen'] === '' ||
            !isset($grade_row['n_presentasi']) || $grade_row['n_presentasi'] === null || $grade_row['n_presentasi'] === '' ||
            !isset($grade_row['n_tanyajawab']) || $grade_row['n_tanyajawab'] === null || $grade_row['n_tanyajawab'] === '' ||
            !isset($grade_row['n_proyek']) || $grade_row['n_proyek'] === null || $grade_row['n_proyek'] === ''
        ) {
            $allStudentsGradesComplete = false; 
            break; 
        }
    }
} else {
    $allStudentsGradesComplete = false;
}

// PROSES INSERT/UPDATE PENILAIAN JIKA FORM DISUBMIT (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nim'])) {
    $nim = $_POST['nim'];
    $n_dokumen = $_POST['nilaiLaporan'] ?? null;
    $n_presentasi = $_POST['MateriPresentasi'] ?? null;
    $n_tanyajawab = $_POST['TanyaJawab'] ?? null;
    $n_proyek = $_POST['NilaiProyek'] ?? null;

    // Validasi input
    if (empty($nim) || $n_dokumen === null || $n_presentasi === null || 
        $n_tanyajawab === null || $n_proyek === null) {
        $error_message = "Semua field nilai harus diisi!";
    } else {
        // Validasi range nilai (0-100)
        if ($n_dokumen < 0 || $n_dokumen > 100 || $n_presentasi < 0 || $n_presentasi > 100 || 
            $n_tanyajawab < 0 || $n_tanyajawab > 100 || $n_proyek < 0 || $n_proyek > 100) {
            $error_message = "Nilai harus antara 0-100!";
        } else {
            $sql_check_penilaian_exists = "SELECT COUNT(*) AS count FROM Penilaian WHERE id_sidang = ? AND nim = ? AND nomor_dosen = ?";
            $stmt_check_penilaian_exists = sqlsrv_query($conn, $sql_check_penilaian_exists, array($id_sidang, $nim, $loggedInNomorDosen));
            
            if ($stmt_check_penilaian_exists === false) {
                $error_message = "Error saat memeriksa data penilaian: " . print_r(sqlsrv_errors(), true);
            } else {
                $check_result = sqlsrv_fetch_array($stmt_check_penilaian_exists, SQLSRV_FETCH_ASSOC);

                if ($check_result['count'] > 0) {
                    // Jika ada, lakukan UPDATE
                    $sql_upsert_penilaian = "UPDATE Penilaian SET n_dokumen = ?, n_presentasi = ?, n_tanyajawab = ?, n_proyek = ?
                                           WHERE id_sidang = ? AND nim = ? AND nomor_dosen = ?";
                    $params_upsert_penilaian = array($n_dokumen, $n_presentasi, $n_tanyajawab, $n_proyek, $id_sidang, $nim, $loggedInNomorDosen);
                    $stmt_upsert_penilaian = sqlsrv_query($conn, $sql_upsert_penilaian, $params_upsert_penilaian);

                    if ($stmt_upsert_penilaian === false) {
                        $error_message = "Gagal memperbarui penilaian: " . print_r(sqlsrv_errors(), true);
                    } else {
                        $success = "Penilaian berhasil diperbarui untuk NIM " . htmlspecialchars($nim);
                    }
                } else {
                    // Jika belum ada, lakukan INSERT
                    $sql_upsert_penilaian = "INSERT INTO Penilaian (id_sidang, nim, nomor_dosen, n_dokumen, n_presentasi, n_tanyajawab, n_proyek, bobot_penilaian)
                                           VALUES (?, ?, ?, ?, ?, ?, ?, NULL)";
                    $params_upsert_penilaian = array($id_sidang, $nim, $loggedInNomorDosen, $n_dokumen, $n_presentasi, $n_tanyajawab, $n_proyek);
                    $stmt_upsert_penilaian = sqlsrv_query($conn, $sql_upsert_penilaian, $params_upsert_penilaian);

                    if ($stmt_upsert_penilaian === false) {
                        $error_message = "Gagal menyimpan penilaian: " . print_r(sqlsrv_errors(), true);
                    } else {
                        $success = "Penilaian berhasil disimpan untuk NIM " . htmlspecialchars($nim);
                    }
                }
            }
        }
    }
    
    // Redirect setelah POST untuk mencegah re-submission form saat refresh
    header("Location: dNilaiAkhir.php?id_sidang=" . $id_sidang . "&nim=" . $nim . "&status=" . (isset($success) ? 'success' : 'error') . "&msg=" . urlencode($error_message ?? ($success ?? ''))); 
    exit();
}

// Menangani pesan status dari redirect
$display_success_message = '';
$display_error_message = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $display_success_message = $_GET['msg'] ?? "Penilaian berhasil disimpan/diperbarui.";
    } elseif ($_GET['status'] == 'error') {
        $display_error_message = $_GET['msg'] ?? "Gagal menyimpan/memperbarui penilaian.";
    }
}

// --- Ambil nilai yang sudah ada untuk mahasiswa yang sedang aktif ---
$existing_grades = [
    'n_dokumen' => '',
    'n_presentasi' => '',
    'n_tanyajawab' => '',
    'n_proyek' => ''
];

if (!empty($current_nim) && $id_sidang) {
    $sql_existing_grades = "SELECT n_dokumen, n_presentasi, n_tanyajawab, n_proyek
                           FROM Penilaian
                           WHERE id_sidang = ? AND nim = ? AND nomor_dosen = ?";
    $stmt_existing_grades = sqlsrv_query($conn, $sql_existing_grades, array($id_sidang, $current_nim, $loggedInNomorDosen));
    
    if ($stmt_existing_grades === false) {
        error_log("Query nilai yang sudah ada gagal: " . print_r(sqlsrv_errors(), true));
    } else {
        $grades_row = sqlsrv_fetch_array($stmt_existing_grades, SQLSRV_FETCH_ASSOC);
        if ($grades_row) {
            $existing_grades = [
                'n_dokumen' => $grades_row['n_dokumen'] ?? '',
                'n_presentasi' => $grades_row['n_presentasi'] ?? '',
                'n_tanyajawab' => $grades_row['n_tanyajawab'] ?? '',
                'n_proyek' => $grades_row['n_proyek'] ?? ''
            ];
        }
    }
}
?> 