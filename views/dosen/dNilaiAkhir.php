<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$user_session = $_SESSION['user_data'];
$loggedInNomorDosen = $user_session['nomor_dosen'];

require_once "../../koneksi/koneksiAndrew.php"; 

if ($conn === false) {
    die("Koneksi ke database gagal: " . print_r(sqlsrv_errors(), true));
}

$id_sidang = isset($_GET['id_sidang']) ? (int)$_GET['id_sidang'] : 0;
$error_message = ''; 

$mahasiswa = [];
$nama_matkul = '';
$dosen_terkait_sidang = ''; 
$id_kelompok = null;
$id_matkul = null; 
$jenis_sidang = null;
$current_nim = '';
$allStudentsGradesComplete = false;

$sql_detail = "SELECT s.id_kelompok, s.jenis_sidang, ds.id_matkul
                FROM Sidang s
                LEFT JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
                WHERE s.id_sidang = ?";
$stmt_detail = sqlsrv_query($conn, $sql_detail, array($id_sidang));

if ($stmt_detail === false) {
    error_log("Query detail sidang gagal: " . print_r(sqlsrv_errors(), true));
    $error_message = "Terjadi kesalahan saat mengambil detail sidang. Mohon coba lagi.";
    $id_sidang = 0;
} else {
    $detail = sqlsrv_fetch_array($stmt_detail, SQLSRV_FETCH_ASSOC);
    if ($detail) {
        $id_kelompok = $detail['id_kelompok'];
        $id_matkul = $detail['id_matkul'];
        $jenis_sidang = $detail['jenis_sidang'];
    } else {
        $error_message = "Data Sidang dengan ID: " . htmlspecialchars($id_sidang ?? '') . " tidak ditemukan."; // Perbaikan: handle null
        $id_sidang = 0;
    }
}

if ($id_sidang) {
    $sql_mhs = "SELECT km.nim, m.nama_mhs
                FROM Kelompok_Mahasiswa km
                JOIN Mahasiswa m ON km.nim = m.nim
                WHERE km.id_kelompok = ?
                ORDER BY km.nim";
    $stmt_mhs = sqlsrv_query($conn, $sql_mhs, array($id_kelompok));
    
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

if ((int)$jenis_sidang === 0) { 
    error_log("Debug - jenis_sidang: " . $jenis_sidang . ", id_kelompok: " . $id_kelompok);
    
    $sql_dosen_ta = "SELECT d.nama_dosen 
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
} elseif ((int)$jenis_sidang === 1 && $id_matkul) {
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

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"  
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../assets/css/style.css" />
    <link rel="stylesheet" href="../../css/button-styles.css" />
    <link rel="stylesheet" href="../../extra/style.css" />
    <link rel="stylesheet" href="../../assets/css/dNilaiAkhir.css" />
    <title>Dosen - Nilai Akhir</title>
    <style>
      /* Style untuk tab aktif */
      .nav-link.active-student-tab {
        font-weight: bold;
        color: var(--primary-color) !important;
        border-bottom: 2px solid var(--primary-color) !important;
      }
    </style>
  </head>
  <body>

   <div id="NavSide">
      <div id="main-sidebar" class="NavSide__sidebar">
        <div class="NavSide__sidebar-brand">
          <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
        </div>
        <ul class="NavSide__sidebar-nav">
          <li class="NavSide__sidebar-item ">
            <b></b>
            <b></b>
             <a href="dEvaluasiSidang.php">
              <span class="NavSide__sidebar-title fw-semibold">Evaluasi</span>
            </a>
          </li>
          <li class="NavSide__sidebar-item">
            <b></b>
            <b></b>
            <a href="dDokumenRevisi.php">
              <span class="NavSide__sidebar-title fw-semibold">Dokumen</span>
            </a>
          </li>
          <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
            <b></b>
            <b></b>
            <a href="dNilaiAkhir.php">
              <span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span>
            </a>
          </li>
          <li class="NavSide__sidebar-item">
                     <b></b><b></b>
                     <a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold"> Kembali</span></a>
                 </li>
         </ul>
      </div>

      <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
      </div>

      <main class="NavSide__main-content">
        <div class="col-12">
          <h2 class="text-heading text-black" style="font-weight: 700;">Detail Evaluasi - Sistem Evaluasi Sidang</h2>
        </div>
          <h2 class="fs-5 fw-semibold mb-0" style="margin-left: 15px; margin-top: 20px;">
              Catatan Perbaikan - Kelompok <?php echo htmlspecialchars($id_kelompok ?? ''); ?>
          </h2><br>
          <div class="container-fluid">
           <div class="row mb-3">
       <div class="col-12">
         <ul class="nav nav-tabs">
            <?php foreach ($mahasiswa as $index => $mhs): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($mhs['nim'] == $current_nim) ? 'active active-student-tab' : ''; ?>"
                       href="dNilaiAkhir.php?id_sidang=<?php echo htmlspecialchars($id_sidang); ?>&nim=<?php echo htmlspecialchars($mhs['nim'] ?? ''); ?>">
                       Mahasiswa <?php echo $index + 1; ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <?php if (empty($mahasiswa)): ?>
                <li class="nav-item">
                    <span class="nav-link disabled">Tidak ada mahasiswa dalam kelompok ini</span>
                </li>
            <?php endif; ?>
         </ul>
       </div>
     </div>
     <br>
        <form method="POST" id="penilaianForm">
         <input type="hidden" name="nim" id="currentNimInput" value="<?php echo htmlspecialchars($current_nim ?? ''); ?>">
         
         <div class="row align-items-stretch">
           <div class="col-lg-49 mb-4 d-flex">
   <div class="card flex-fill" id="carddataMahasiswa">
     <div class="card-body card-soft px-4 py-3">
       <h3 class="card-title text-black mb-4 text text-center" style="padding:10px;">Data Mahasiswa</h3>
       <div class="d-flex flex-wrap gap-1 px-4 py-3">
         
         <div class="section" style="flex: 1 1 200px; margin-left:30px;  color: #333;">
           
           <div class="info-group mb-3">
             <div class="label-row d-flex align-items-center gap-2 mb-1">
               <i class="fa-solid fa-id-card"></i>
               <span class="fw-bold">NIM</span>
             </div>
             <div class="value-row text-secondary fw-bold" id="displayNim">
                <?php echo htmlspecialchars($current_nim ?? ''); ?>
             </div>
           </div>
           
           <div class="info-group mb-3 section-bawah" style="margin-top:45px;">
             <div class="label-row d-flex align-items-center gap-2 mb-1">
               <i class="fa-solid fa-user"></i>
               <span class="fw-bold">Nama</span>
             </div>
             <div class="value-row text-secondary fw-bold" id="displayNama">
                <?php
                    $initial_mhs_name = '';
                    foreach ($mahasiswa as $mhs) {
                        if ($mhs['nim'] == $current_nim) {
                            $initial_mhs_name = $mhs['nama_mhs'];
                            break;
                        }
                    }
                    echo htmlspecialchars($initial_mhs_name ?? '');
                ?>
             </div>
           </div>
         </div>
         
         <div class="section2" style="flex: 1 1 200px; color: #333;">
           
           <div class="info-group mb-3">
             <div class="label-row d-flex align-items-center gap-2 mb-1">
               <i class="fa-solid fa-book"></i>
               <span class="fw-bold">Mata Kuliah</span>
             </div>
             <div class="value-row text-secondary fw-bold">
                <?php echo htmlspecialchars($nama_matkul ?? ''); ?>
             </div>
           </div>
           
           <div class="info-group mb-3 section-bawah" style="margin-top:45px;">
             <div class="label-row d-flex align-items-center gap-2 mb-1">
               <i class="fa-solid fa-user-tie"></i>
               <span class="fw-bold">Dosen Terkait Sidang</span>
             </div>
             <div class="value-row text-secondary fw-bold">
                 <?php echo htmlspecialchars($dosen_terkait_sidang ?? ''); ?>
             </div>
             <!-- Debug Info -->
             <!-- <div style="font-size: 12px; color: #666; margin-top: 5px;">
                 Debug: jenis_sidang=<?php echo $jenis_sidang; ?>, 
                 id_kelompok=<?php echo $id_kelompok; ?>, 
                 id_matkul=<?php echo $id_matkul; ?>, 
                 dosen_terkait_sidang=<?php echo $dosen_terkait_sidang; ?>
             </div> -->
           </div>
         </div>
       </div>
     </div>
   </div>
</div>
   <div class="col-lg-49 mb-4 d-flex">
     <div class="card flex-fill" id="cardNilai">
       <div class="card-body card-soft px-4 py-3 text-center">
         <h3 class="card-title mb-3 text-black" style="padding:10px ;">Nilai Mahasiswa:</h3>
         <div>
           <input
             type="text"
             class="form-control form-control-lg text-center mx-auto"
             id="nilaiMahasiswa"
             placeholder="--"
             maxlength="1"
             style="cursor:pointer;"
             readonly
           />
         </div>
       </div>
     </div>
   </div>
   
             <div class="col-12 mb-4 d-flex">
               <div class="card flex-fill" id="carddetailPenilaian">

<div class="card-body" id="card-penilaian-body">
     <div class="d-flex justify-content-between align-items-center mb-4">
         <h3 class="card-title text-black mb-0">Detail Penilaian :</h3>
         <a onclick="bukaKonfirmasiModal()" style="cursor:pointer" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tekan ini jika ingin menggunakan nilai sementara">
             <i class="bi bi-pencil-fill fs-5" style="color: var(--text-dark);"></i>
         </a>
     </div>
    
     <div class="penilaian-container">
         <div class="penilaian-item">
             <label for="nilaiLaporanInput">Nilai laporan :</label> 
             <input type="text" class="form-control text-center input-nilai" name="nilaiLaporan" id="nilaiLaporanInput" maxlength="3" value="<?php echo htmlspecialchars($existing_grades['n_dokumen']); ?>"> 
         </div>
         <div class="penilaian-item">
             <label for="materiPresentasiInput">Materi Presentasi :</label> 
             <input type="text" class="form-control text-center input-nilai" name="MateriPresentasi" id="materiPresentasiInput" maxlength="3" value="<?php echo htmlspecialchars($existing_grades['n_presentasi']); ?>"> 
         </div>
         <div class="penilaian-item">
             <label for="tanyaJawabInput">Tanya Jawab :</label> 
             <input type="text" class="form-control text-center input-nilai" name="TanyaJawab" id="tanyaJawabInput" maxlength="3" value="<?php echo htmlspecialchars($existing_grades['n_tanyajawab']); ?>"> 
         </div>
         <div class="penilaian-item">
             <label for="nilaiProyekInput">Nilai Proyek :</label> 
             <input type="text" class="form-control text-center input-nilai" name="NilaiProyek" id="nilaiProyekInput" maxlength="3" value="<?php echo htmlspecialchars($existing_grades['n_proyek']); ?>"> 
         </div>
     </div>

     <div class="penilaian-grid-vertical">
         <label for="nilaiLaporanInput_v">Nilai laporan</label> <span>:</span> 
         <input type="text" class="form-control text-center input-nilai" name="nilaiLaporan_v" id="nilaiLaporanInput_v" maxlength="3" value="<?php echo htmlspecialchars($existing_grades['n_dokumen']); ?>"> 
         
         <label for="materiPresentasiInput_v">Materi Presentasi</label> <span>:</span> 
         <input type="text" class="form-control text-center input-nilai" name="MateriPresentasi_v" id="materiPresentasiInput_v" maxlength="3" value="<?php echo htmlspecialchars($existing_grades['n_presentasi']); ?>"> 
         
         <label for="tanyaJawabInput_v">Tanya Jawab</label> <span>:</span> 
         <input type="text" class="form-control text-center input-nilai" name="TanyaJawab_v" id="tanyaJawabInput_v" maxlength="3" value="<?php echo htmlspecialchars($existing_grades['n_tanyajawab']); ?>"> 
         
         <label for="nilaiProyekInput_v">Nilai Proyek</label> <span>:</span> 
         <input type="text" class="form-control text-center input-nilai" name="NilaiProyek_v" id="nilaiProyekInput_v" maxlength="3" value="<?php echo htmlspecialchars($existing_grades['n_proyek']); ?>"> 
     </div>
</div>
             </div>
           </div>
           
           
             </div>
          <div class="row mt-5 justify-content-between">
           </div>
           <div class="col-12 d-flex justify-content-end">
             <button type="button" class="btn btn-setujui" id="btnKirim"
                onclick="bukaKonfirmasiModalKirim()" 
                <?php echo ($allStudentsGradesComplete && !empty($mahasiswa)) ? '' : 'disabled'; ?>>
               Kirim
             </button>
           </div>
       </form>
         </div>
     
     </main>
     
   </div>
   
   <div class="modal fade" id="konfirmasiModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
     <div class="modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
       <div class="modal-header border-0 justify-content-center">
                     <h4 class="modal-title fw-bold" id="modalKonfirmasiLabel" style="font-size: 24px;">Perhatian</h4>
                   </div>
       <div class="modal-body">
         <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah nilai akhir sama dengan nilai sementara?</p>
         <div class="d-flex justify-content-between px-5">
           <button type="button" class="btnKonfirmasi btn-tolak" id="tidakmodal"onclick="TutupKonfirmasiModal()">Tidak</button>
           <button type="button" class="btnKonfirmasi btn-setujui" id="iyamodal" onclick="checkAndFillGrades()">Iya</button>
         </div>
       </div>
     </div>
   </div>
</div>
   <div class="modal fade" id="konfirmasiModalKirim" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
     <div class="modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
       <div class="modal-header border-0 justify-content-center">
                     <h4 class="modal-title fw-bold" id="modalKonfirmasiLabel" style="font-size: 24px;">Perhatian</h4>
                   </div>
       <div class="modal-body">
         <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah yakin ingin mengirim nilai akhir?</p>
         <div class="d-flex justify-content-between px-5">
           <button type="button" class="btnKonfirmasi  btn-tolak" id="tidakmodal" data-bs-dismiss="modal">Tidak</button>
           <button type="button" class="btnKonfirmasi  btn-setujui" id="iyamodal" onclick="kirimNilaiAkhir()">Iya</button>
         </div>
       </div>
     </div>
   </div>
</div>
<script>
    // Data Mahasiswa dari PHP (dilewatkan sebagai JSON)
    const allMahasiswa = <?php echo json_encode($mahasiswa); ?>;
    const currentSidangId = <?php echo json_encode($id_sidang); ?>;
    const loggedInNomorDosen = <?php echo json_encode($loggedInNomorDosen); ?>; // ID Dosen yang login

    // Status kelengkapan nilai semua mahasiswa oleh dosen ini
    let allStudentsGradedByThisDosen = <?php echo json_encode($allStudentsGradesComplete); ?>; // Initial status from PHP

    document.addEventListener('DOMContentLoaded', function() {
        // Logika saat halaman dimuat pertama kali
        const initialNim = document.getElementById('currentNimInput').value;
        if (initialNim) {
            // Nilai sudah dimuat dari PHP, hitung rata-rata
            calculateAndDisplayAverage();
        }

        // Perbarui status tombol "Kirim" saat halaman dimuat
        updateKirimButtonStatus();

        // Menampilkan pesan SweetAlert jika ada status dari redirect
        <?php if (!empty($display_success_message)): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?php echo htmlspecialchars($display_success_message); ?>', // Perbaikan
                confirmButtonText: 'OK'
            });
        <?php elseif (!empty($display_error_message)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                html: '<?php echo htmlspecialchars($display_error_message); ?>', // Perbaikan
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    });

    /**
     * Mengosongkan semua input nilai.
     */
    function clearGradeInputs() {
        // Mengosongkan input nilai desktop
        document.getElementById('nilaiLaporanInput').value = '';
        document.getElementById('materiPresentasiInput').value = '';
        document.getElementById('tanyaJawabInput').value = '';
        document.getElementById('nilaiProyekInput').value = '';

        // Mengosongkan input nilai mobile/tablet
        document.getElementById('nilaiLaporanInput_v').value = '';
        document.getElementById('materiPresentasiInput_v').value = '';
        document.getElementById('tanyaJawabInput_v').value = '';
        document.getElementById('nilaiProyekInput_v').value = '';

        // Mengosongkan nilai akhir
        document.getElementById('nilaiMahasiswa').value = '--';
    }

    /**
     * Membuka modal konfirmasi untuk nilai sementara.
     */
    function bukaKonfirmasiModal() {
        var konfirmasiModal = new bootstrap.Modal(document.getElementById('konfirmasiModal'));
        konfirmasiModal.show();
    }

    /**
     * Menutup modal konfirmasi nilai sementara.
     */
    function TutupKonfirmasiModal() {
        var konfirmasiModal = bootstrap.Modal.getInstance(document.getElementById('konfirmasiModal'));
        konfirmasiModal.hide();
    }

    /**
     * Menghitung rata-rata nilai dari input detail penilaian dan mengonversinya ke nilai huruf.
     * Kemudian menampilkannya di kolom "Nilai Mahasiswa".
     */
    function calculateAndDisplayAverage() {
        // Ambil nilai dari input detail penilaian (prioritaskan input desktop)
        const nilaiLaporan = parseFloat(document.getElementById('nilaiLaporanInput').value);
        const materiPresentasi = parseFloat(document.getElementById('materiPresentasiInput').value);
        const tanyaJawab = parseFloat(document.getElementById('tanyaJawabInput').value);
        const nilaiProyek = parseFloat(document.getElementById('nilaiProyekInput').value);

        let totalScore = 0;
        let count = 0;

        // Hanya sertakan nilai yang valid (angka dan non-negatif) dalam perhitungan
        if (!isNaN(nilaiLaporan) && nilaiLaporan >= 0) {
            totalScore += nilaiLaporan;
            count++;
        }
        if (!isNaN(materiPresentasi) && materiPresentasi >= 0) {
            totalScore += materiPresentasi;
            count++;
        }
        if (!isNaN(tanyaJawab) && tanyaJawab >= 0) {
            totalScore += tanyaJawab;
            count++;
        }
        if (!isNaN(nilaiProyek) && nilaiProyek >= 0) {
            totalScore += nilaiProyek;
            count++;
        }

        // Hitung rata-rata, jika tidak ada nilai valid, set null
        let averageScore = (count > 0) ? (totalScore / count) : null;

        // Konversi rata-rata ke nilai huruf
        let nilaiHuruf = '--';
        if (averageScore !== null) {
            averageScore = Math.round(averageScore); // Bulatkan ke bilangan bulat terdekat
            if (averageScore >= 85) nilaiHuruf = 'A';
            else if (averageScore >= 75) nilaiHuruf = 'B';
            else if (averageScore >= 65) nilaiHuruf = 'C';
            else if (averageScore >= 50) nilaiHuruf = 'D';
            else nilaiHuruf = 'E';
        }
        
        document.getElementById('nilaiMahasiswa').value = nilaiHuruf;
    }

    /**
     * Dipicu saat tombol "Iya" diklik di modal konfirmasi nilai sementara.
     * Memuat ulang nilai mahasiswa dan memperbarui tampilan.
     */
    function checkAndFillGrades() {
        // Nilai sudah dimuat dari PHP, tidak perlu load ulang
        // Hanya tutup modal
        TutupKonfirmasiModal();
    }

    /**
     * Membuka modal konfirmasi sebelum mengirim nilai akhir.
     * Melakukan validasi sederhana pada input nilai untuk mahasiswa aktif.
     */
    function bukaKonfirmasiModalKirim() {
        // Validasi ini hanya memastikan nilai mahasiswa AKTIF sudah terisi.
        // Validasi bahwa SEMUA mahasiswa sudah dinilai dilakukan di PHP (allStudentsGradedByThisDosen).
        const nilaiLaporan = document.getElementById('nilaiLaporanInput').value;
        const materiPresentasi = document.getElementById('materiPresentasiInput').value;
        const tanyaJawab = document.getElementById('tanyaJawabInput').value;
        const nilaiProyek = document.getElementById('nilaiProyekInput').value;

        // Periksa apakah semua kolom nilai utama untuk mahasiswa aktif sudah terisi
        if (!nilaiLaporan || !materiPresentasi || !tanyaJawab || !nilaiProyek) {
            Swal.fire({
                icon: 'warning',
                title: 'Input Tidak Lengkap!',
                text: 'Harap isi semua kolom penilaian (nilai laporan, materi presentasi, tanya jawab, dan nilai proyek) untuk mahasiswa ini sebelum mengirim.',
                confirmButtonText: 'OK'
            });
            return; // Hentikan fungsi jika validasi gagal
        }

        // Jika validasi sukses, tampilkan modal konfirmasi kirim
        var konfirmasiModalKirim = new bootstrap.Modal(document.getElementById('konfirmasiModalKirim'));
        konfirmasiModalKirim.show();
    }

    /**
     * Mengirim form penilaian setelah konfirmasi dari pengguna.
     */
    function kirimNilaiAkhir() {
        document.getElementById('penilaianForm').submit(); // Submit form
    }

    /**
     * Memperbarui status tombol "Kirim" berdasarkan variabel allStudentsGradedByThisDosen dari PHP.
     */
    function updateKirimButtonStatus() {
        const btnKirim = document.getElementById('btnKirim');
        if (btnKirim) {
            // Tombol aktif jika allStudentsGradedByThisDosen adalah TRUE DAN ada mahasiswa di kelompok
            btnKirim.disabled = !(allStudentsGradedByThisDosen && allMahasiswa.length > 0);
            
            // Opsional: Tambahkan tooltip informatif
            if (btnKirim.disabled) {
                if (allMahasiswa.length === 0) {
                    btnKirim.title = "Tidak ada mahasiswa dalam kelompok ini untuk dinilai.";
                } else {
                    btnKirim.title = "Harap selesaikan penilaian untuk semua mahasiswa dalam kelompok ini.";
                }
            } else {
                btnKirim.title = ""; // Hapus tooltip jika tombol aktif
            }
        }
    }
</script>
  </body>
</html>