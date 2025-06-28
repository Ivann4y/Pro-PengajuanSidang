<?php
session_start();
require_once "../../koneksi/koneksiAndrew.php"; // Pastikan path ini benar dan koneksi berhasil

// ===========================================================================
// PENTING: ID Dosen yang sedang login. Ganti ini dengan mekanisme autentikasi Anda!
// Misalnya: $_SESSION['nomor_dosen']
// Untuk testing, pastikan ID ini sesuai dengan nomor_dosen yang ada di tabel Dosen dan Penilaian.
$loggedInNomorDosen = 1001; // !!! GANTI DENGAN ID DOSEN ASLI DARI SESI LOGIN ANDA !!!
// ===========================================================================

// Aktifkan error reporting untuk debugging selama pengembangan
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ambil id_sidang dari GET parameter
$id_sidang = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Inisialisasi variabel
$mahasiswa = []; // Array untuk menyimpan data mahasiswa dalam kelompok
$nama_matkul = ''; // Menyimpan nama mata kuliah
$dosen_terkait_sidang = []; // Array untuk menyimpan nama dosen terkait sidang (pembimbing/penguji)
$id_kelompok = null; // Menyimpan ID kelompok
$id_matkul = null; // Menyimpan ID mata kuliah
$jenis_sidang = null; // Menyimpan jenis sidang (binary)
$current_nim = ''; // Menyimpan NIM mahasiswa yang sedang aktif/dipilih
$allStudentsGradedByThisDosen = false; // Flag untuk status kelengkapan nilai semua mahasiswa oleh dosen ini

// --- Ambil id_kelompok, id_matkul, dan jenis_sidang dari Sidang & Detail_Sidang ---
// Menggunakan LEFT JOIN agar data sidang tetap bisa diambil meskipun belum ada detailnya
$sql_detail = "SELECT s.id_kelompok, s.jenis_sidang, ds.id_matkul
                FROM Sidang s
                LEFT JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
                WHERE s.id_sidang = ?";
$stmt_detail = sqlsrv_query($conn, $sql_detail, array($id_sidang));

if ($stmt_detail === false) {
    die("Query detail sidang gagal: " . print_r(sqlsrv_errors(), true));
}

$detail = sqlsrv_fetch_array($stmt_detail, SQLSRV_FETCH_ASSOC);
if ($detail) {
    $id_kelompok = $detail['id_kelompok'];
    $id_matkul = $detail['id_matkul'];
    $jenis_sidang = $detail['jenis_sidang']; // Ini adalah binary, 0x00 atau 0x01
} else {
    // Jika sidang tidak ditemukan, bisa jadi error atau id_sidang tidak valid
    die("Data Sidang dengan ID: " . htmlspecialchars($id_sidang) . " tidak ditemukan.");
}

// --- Ambil data mahasiswa dalam kelompok ---
if ($id_kelompok) {
    $sql_mhs = "SELECT m.nim, m.nama_mhs
                FROM Mahasiswa m
                JOIN Kelompok_Mahasiswa km ON m.nim = km.nim
                WHERE km.id_kelompok = ? ORDER BY m.nim ASC";
    $stmt_mhs = sqlsrv_query($conn, $sql_mhs, array($id_kelompok));
    
    if ($stmt_mhs === false) {
        die("Query mahasiswa gagal: " . print_r(sqlsrv_errors(), true));
    }
    
    while ($row = sqlsrv_fetch_array($stmt_mhs, SQLSRV_FETCH_ASSOC)) {
        $mahasiswa[] = $row;
    }

    // Tentukan NIM mahasiswa yang akan ditampilkan secara default
    if (isset($_GET['nim']) && in_array($_GET['nim'], array_column($mahasiswa, 'nim'))) {
        $current_nim = $_GET['nim'];
    } elseif (!empty($mahasiswa)) {
        $current_nim = $mahasiswa[0]['nim']; // Default ke mahasiswa pertama
    }
} else {
    // Jika id_kelompok null, berarti tidak ada mahasiswa terkait langsung
    // Anda bisa menambahkan penanganan khusus atau menampilkan pesan
}

// --- Ambil nama mata kuliah ---
if ($id_matkul) {
    $sql_matkul = "SELECT nama_matkul FROM MataKuliah WHERE id_matkul = ?";
    $stmt_matkul = sqlsrv_query($conn, $sql_matkul, array($id_matkul));
    
    if ($stmt_matkul === false) {
        die("Query mata kuliah gagal: " . print_r(sqlsrv_errors(), true));
    }
    
    $row_matkul = sqlsrv_fetch_array($stmt_matkul, SQLSRV_FETCH_ASSOC);
    if ($row_matkul) {
        $nama_matkul = $row_matkul['nama_matkul'];
    }
}

// --- Ambil dosen terkait sidang berdasarkan jenis sidang ---
// Jenis sidang TA (0x00) -> Dosen dari Penjadwalan dengan peran_dosen = 0x01
// Jenis sidang Semester (0x01) -> Dosen dari Pengampu_Kelas
if ($jenis_sidang === 0x00) { // Sidang Tugas Akhir
    // Konfirmasi: menampilkan dosen yang tercatat di tabel Penjadwalan yang peran_dosen bernilai 1
    $sql_dosen_ta = "SELECT d.nama_dosen
                     FROM Dosen d
                     JOIN Penjadwalan pj ON d.nomor_dosen = pj.nomor_dosen
                     WHERE pj.id_sidang = ? AND pj.peran_dosen = 0x01"; // Mengambil hanya yang peran_dosen = 0x01
    $stmt_dosen_ta = sqlsrv_query($conn, $sql_dosen_ta, array($id_sidang));
    if ($stmt_dosen_ta === false) {
        die("Query dosen TA gagal: " . print_r(sqlsrv_errors(), true));
    }
    while ($row = sqlsrv_fetch_array($stmt_dosen_ta, SQLSRV_FETCH_ASSOC)) {
        $dosen_terkait_sidang[] = $row['nama_dosen'];
    }
} elseif ($jenis_sidang === 0x01 && $id_matkul) { // Sidang Semester
    $sql_dosen_semester = "SELECT d.nama_dosen
                           FROM Dosen d
                           JOIN Pengampu_Kelas pk ON d.nomor_dosen = pk.nomor_dosen
                           WHERE pk.id_matkul = ?";
    $stmt_dosen_semester = sqlsrv_query($conn, $sql_dosen_semester, array($id_matkul));
    if ($stmt_dosen_semester === false) {
        die("Query dosen semester gagal: " . print_r(sqlsrv_errors(), true));
    }
    while ($row = sqlsrv_fetch_array($stmt_dosen_semester, SQLSRV_FETCH_ASSOC)) {
        $dosen_terkait_sidang[] = $row['nama_dosen'];
    }
}

// ===========================================================================
// BAGIAN AJAX ENDPOINT UNTUK MENGAMBIL NILAI MAHASISWA
// ===========================================================================
if (isset($_GET['action']) && $_GET['action'] == 'get_student_grades' && isset($_GET['nim']) && isset($_GET['id_sidang']) && isset($_GET['nomor_dosen'])) {
    $req_nim = $_GET['nim'];
    $req_id_sidang = (int)$_GET['id_sidang'];
    $req_nomor_dosen = (int)$_GET['nomor_dosen'];

    // Query ke tabel Penilaian, mencocokkan id_sidang, nim, DAN nomor_dosen (nilai per dosen)
    $sql_penilaian_ajax = "SELECT n_dokumen, n_presentasi, n_tanyajawab, n_proyek, catatan
                           FROM Penilaian
                           WHERE id_sidang = ? AND nim = ? AND nomor_dosen = ?";
    $stmt_penilaian_ajax = sqlsrv_query($conn, $sql_penilaian_ajax, array($req_id_sidang, $req_nim, $req_nomor_dosen));

    if ($stmt_penilaian_ajax === false) {
        error_log("AJAX Query Penilaian Gagal: " . print_r(sqlsrv_errors(), true));
        echo json_encode(['error' => 'Gagal mengambil data penilaian dari database: ' . print_r(sqlsrv_errors(), true)]);
        exit;
    }

    $grades = sqlsrv_fetch_array($stmt_penilaian_ajax, SQLSRV_FETCH_ASSOC);
    
    // Jika tidak ada nilai yang ditemukan untuk dosen dan mahasiswa ini, kirim null/kosong
    if (!$grades) {
        $grades = [
            'n_dokumen' => null,
            'n_presentasi' => null,
            'n_tanyajawab' => null,
            'n_proyek' => null,
            'catatan' => null
        ];
    }
    
    echo json_encode($grades);
    exit; // Penting: Hentikan eksekusi script setelah mengirim respon JSON untuk AJAX
}

// ===========================================================================
// LOGIKA VALIDASI UNTUK TOMBOL "KIRIM" (Interpretasi B: semua mahasiswa telah dinilai oleh dosen ini)
// ===========================================================================
// Ambil status nilai untuk SEMUA mahasiswa dalam kelompok ini, khusus untuk dosen yang login
$gradesStatusPerStudent = [];
$allStudentsGradesComplete = true; // Asumsi awal semua sudah lengkap
if (!empty($mahasiswa)) {
    foreach ($mahasiswa as $mhs_item) {
        $nim_check = $mhs_item['nim'];
        $sql_check_all_grades = "SELECT n_dokumen, n_presentasi, n_tanyajawab, n_proyek
                                 FROM Penilaian
                                 WHERE id_sidang = ? AND nim = ? AND nomor_dosen = ?";
        $stmt_check_all_grades = sqlsrv_query($conn, $sql_check_all_grades, array($id_sidang, $nim_check, $loggedInNomorDosen));
        
        if ($stmt_check_all_grades === false) {
            error_log("Query cek kelengkapan nilai gagal: " . print_r(sqlsrv_errors(), true));
            $allStudentsGradesComplete = false; // Jika query error, anggap tidak lengkap
            break; 
        }
        
        $grade_row = sqlsrv_fetch_array($stmt_check_all_grades, SQLSRV_FETCH_ASSOC);
        
        // Cek apakah semua nilai (dokumen, presentasi, tanya jawab, proyek) terisi (tidak NULL atau kosong)
        if (
            !$grade_row || // Jika tidak ada baris penilaian sama sekali
            empty($grade_row['n_dokumen']) || 
            empty($grade_row['n_presentasi']) || 
            empty($grade_row['n_tanyajawab']) || 
            empty($grade_row['n_proyek'])
        ) {
            $allStudentsGradesComplete = false; // Ada mahasiswa yang belum lengkap
            break; // Hentikan loop karena sudah ada yang tidak lengkap
        }
    }
} else {
    // Jika tidak ada mahasiswa dalam kelompok, tombol kirim harus disabled
    $allStudentsGradesComplete = false;
}


// ===========================================================================
// PROSES INSERT/UPDATE PENILAIAN JIKA FORM DISUBMIT (POST request)
// ===========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nim'])) {
    $nim = $_POST['nim'];
    // Gunakan null coalescing operator (??) untuk menangani jika input tidak ada
    $n_dokumen = $_POST['nilaiLaporan'] ?? null;
    $n_presentasi = $_POST['MateriPresentasi'] ?? null;
    $n_tanyajawab = $_POST['TanyaJawab'] ?? null;
    $n_proyek = $_POST['NilaiProyek'] ?? null;
    $catatan = $_POST['catatan'] ?? null;

    // Periksa apakah sudah ada catatan penilaian dari DOSEN INI untuk SIDANG dan MAHASISWA ini
    $sql_check_penilaian_exists = "SELECT COUNT(*) AS count FROM Penilaian WHERE id_sidang = ? AND nim = ? AND nomor_dosen = ?";
    $stmt_check_penilaian_exists = sqlsrv_query($conn, $sql_check_penilaian_exists, array($id_sidang, $nim, $loggedInNomorDosen));
    
    if ($stmt_check_penilaian_exists === false) {
        $error = "Error saat memeriksa data penilaian: " . print_r(sqlsrv_errors(), true);
    } else {
        $check_result = sqlsrv_fetch_array($stmt_check_penilaian_exists, SQLSRV_FETCH_ASSOC);

        if ($check_result['count'] > 0) {
            // Jika ada, lakukan UPDATE
            $sql_upsert_penilaian = "UPDATE Penilaian SET n_dokumen = ?, n_presentasi = ?, n_tanyajawab = ?, n_proyek = ?, catatan = ?
                                   WHERE id_sidang = ? AND nim = ? AND nomor_dosen = ?";
            $params_upsert_penilaian = array($n_dokumen, $n_presentasi, $n_tanyajawab, $n_proyek, $catatan, $id_sidang, $nim, $loggedInNomorDosen);
            $stmt_upsert_penilaian = sqlsrv_query($conn, $sql_upsert_penilaian, $params_upsert_penilaian);

            if ($stmt_upsert_penilaian === false) {
                $error = "Gagal memperbarui penilaian: " . print_r(sqlsrv_errors(), true);
            } else {
                $success = "Penilaian berhasil diperbarui untuk NIM " . htmlspecialchars($nim);
            }
        } else {
            // Jika belum ada, lakukan INSERT
            // bobot_penilaian di-set NULL karena diabaikan untuk role dosen pada tahap ini
            $sql_upsert_penilaian = "INSERT INTO Penilaian (id_sidang, nim, nomor_dosen, n_dokumen, n_presentasi, n_tanyajawab, n_proyek, catatan, bobot_penilaian)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, NULL)";
            $params_upsert_penilaian = array($id_sidang, $nim, $loggedInNomorDosen, $n_dokumen, $n_presentasi, $n_tanyajawab, $n_proyek, $catatan);
            $stmt_upsert_penilaian = sqlsrv_query($conn, $sql_upsert_penilaian, $params_upsert_penilaian);

            if ($stmt_upsert_penilaian === false) {
                $error = "Gagal menyimpan penilaian: " . print_r(sqlsrv_errors(), true);
            } else {
                $success = "Penilaian berhasil disimpan untuk NIM " . htmlspecialchars($nim);
            }
        }
    }
    
    // Redirect setelah POST untuk mencegah re-submission form saat refresh
    // Tambahkan parameter status kelengkapan nilai agar JS bisa langsung update tombol "Kirim"
    header("Location: dNilaiAkhir.php?id_sidang=" . $id_sidang . "&nim=" . $nim . "&status=" . (isset($success) ? 'success' : 'error'));
    exit();
}

// Menangani pesan status dari redirect
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $success = "Penilaian berhasil disimpan/diperbarui.";
    } elseif ($_GET['status'] == 'error') {
        $error = "Gagal menyimpan/memperbarui penilaian.";
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
      <div class="container-fluid">
           <div class="row mb-3">
       <div class="col-12">
         <h2 class="text-heading text-black" style="font-weight: 700;">Detail Evaluasi - Sistem Evaluasi Sidang</h2>
       </div>
       <div class="col-12">
         <ul class="nav nav-tabs">
            <?php foreach ($mahasiswa as $index => $mhs): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($mhs['nim'] == $current_nim) ? 'active active-student-tab' : ''; ?>"
                       href="dNilaiAkhir.php?id_sidang=<?php echo htmlspecialchars($id_sidang); ?>&nim=<?php echo htmlspecialchars($mhs['nim']); ?>"
                       data-nim="<?php echo htmlspecialchars($mhs['nim']); ?>"
                       onclick="loadStudentData('<?php echo htmlspecialchars($mhs['nim']); ?>', '<?php echo htmlspecialchars($id_sidang); ?>'); return false;">
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
         <input type="hidden" name="nim" id="currentNimInput" value="<?php echo htmlspecialchars($current_nim); ?>">
         
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
                <?php echo htmlspecialchars($current_nim); ?>
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
                    echo htmlspecialchars($initial_mhs_name);
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
                <?php echo htmlspecialchars($nama_matkul); ?>
             </div>
           </div>
           
           <div class="info-group mb-3 section-bawah" style="margin-top:45px;">
             <div class="label-row d-flex align-items-center gap-2 mb-1">
               <i class="fa-solid fa-user-tie"></i>
               <span class="fw-bold">Dosen Terkait Sidang</span>
             </div>
             <div class="value-row text-secondary fw-bold">
                 <?php 
                    if (!empty($dosen_terkait_sidang)) { 
                        echo implode(', ', array_map('htmlspecialchars', $dosen_terkait_sidang));
                    } else {
                        echo 'N/A';
                    }
                 ?>
             </div>
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
             <input type="text" class="form-control text-center input-nilai" name="nilaiLaporan" id="nilaiLaporanInput" maxlength="3"> 
         </div>
         <div class="penilaian-item">
             <label for="materiPresentasiInput">Materi Presentasi :</label> 
             <input type="text" class="form-control text-center input-nilai" name="MateriPresentasi" id="materiPresentasiInput" maxlength="3"> 
         </div>
         <div class="penilaian-item">
             <label for="tanyaJawabInput">Tanya Jawab :</label> 
             <input type="text" class="form-control text-center input-nilai" name="TanyaJawab" id="tanyaJawabInput" maxlength="3"> 
         </div>
         <div class="penilaian-item">
             <label for="nilaiProyekInput">Nilai Proyek :</label> 
             <input type="text" class="form-control text-center input-nilai" name="NilaiProyek" id="nilaiProyekInput" maxlength="3"> 
         </div>
     </div>

     <div class="penilaian-grid-vertical">
         <label for="nilaiLaporanInput_v">Nilai laporan</label> <span>:</span> 
         <input type="text" class="form-control text-center input-nilai" name="nilaiLaporan_v" id="nilaiLaporanInput_v" maxlength="3"> 
         
         <label for="materiPresentasiInput_v">Materi Presentasi</label> <span>:</span> 
         <input type="text" class="form-control text-center input-nilai" name="MateriPresentasi_v" id="materiPresentasiInput_v" maxlength="3"> 
         
         <label for="tanyaJawabInput_v">Tanya Jawab</label> <span>:</span> 
         <input type="text" class="form-control text-center input-nilai" name="TanyaJawab_v" id="tanyaJawabInput_v" maxlength="3"> 
         
         <label for="nilaiProyekInput_v">Nilai Proyek</label> <span>:</span> 
         <input type="text" class="form-control text-center input-nilai" name="NilaiProyek_v" id="nilaiProyekInput_v" maxlength="3"> 
     </div>
</div>
             </div>
           </div>
           
           
             <div class="col-12 mb-4 d-flex">
               <div class="card flex-fill" id="cardcatatan">
                 <div class="card-body">
                   <h3 class="card-title text-black">Catatan:</h3>
                   <textarea
                     class="form-control form-control-lg"
                     id="catatan"
                     name="catatan"
                     placeholder="Masukan catatan di sini...(Opsional)"
                     rows="4"
                   ></textarea>
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
    let allStudentsGradedByThisDosen = <?php echo json_encode($allStudentsGradedByThisDosen); ?>;

    document.addEventListener('DOMContentLoaded', function() {
        // Logika saat halaman dimuat pertama kali
        const initialNim = document.getElementById('currentNimInput').value;
        if (initialNim) {
            updateStudentInfo(initialNim); // Update info mahasiswa
            loadStudentGrades(initialNim); // Muat nilai yang sudah ada untuk mahasiswa ini
        }

        // Perbarui status tombol "Kirim" saat halaman dimuat
        updateKirimButtonStatus();

        // Menampilkan pesan SweetAlert jika ada status dari redirect
        <?php if (isset($success)): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?php echo $success; ?>',
                confirmButtonText: 'OK'
            });
        <?php elseif (isset($error)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                html: '<?php echo $error; ?>', // Menggunakan html untuk menampilkan pesan error lengkap dari PHP
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    });

    /**
     * Memperbarui tampilan informasi mahasiswa (NIM dan Nama)
     * @param {string} nim - NIM mahasiswa yang akan ditampilkan
     */
    function updateStudentInfo(nim) {
        const student = allMahasiswa.find(m => m.nim === nim);
        if (student) {
            document.getElementById('displayNim').textContent = student.nim;
            document.getElementById('displayNama').textContent = student.nama_mhs;
            document.getElementById('currentNimInput').value = student.nim; // Update hidden input
        } else {
            // Jika mahasiswa tidak ditemukan, set ke 'N/A'
            document.getElementById('displayNim').textContent = 'N/A';
            document.getElementById('displayNama').textContent = 'N/A';
            document.getElementById('currentNimInput').value = '';
        }
    }

    /**
     * Mengosongkan semua input nilai dan catatan.
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

        // Mengosongkan catatan dan nilai akhir
        document.getElementById('catatan').value = '';
        document.getElementById('nilaiMahasiswa').value = '--';
    }

    /**
     * Memuat data mahasiswa baru saat tab diklik.
     * Memperbarui URL, tampilan, dan memuat nilai dari database via AJAX.
     * @param {string} nim - NIM mahasiswa yang dipilih
     * @param {number} idSidang - ID sidang yang sedang aktif
     */
    function loadStudentData(nim, idSidang) {
        // Perbarui URL browser dan history untuk state yang benar
        const url = new URL(window.location.href);
        url.searchParams.set('nim', nim);
        url.searchParams.set('id_sidang', idSidang);
        window.history.pushState({ nim: nim, id_sidang: idSidang }, '', url.toString());

        // Atur kelas 'active' pada tab navigasi yang dipilih
        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.classList.remove('active', 'active-student-tab');
        });
        document.querySelector(`.nav-link[data-nim="${nim}"]`).classList.add('active', 'active-student-tab');

        updateStudentInfo(nim); // Perbarui informasi mahasiswa di card
        clearGradeInputs(); // Kosongkan input nilai sebelum memuat yang baru
        loadStudentGrades(nim); // Panggil fungsi untuk memuat nilai dari database
    }

    /**
     * Memuat nilai penilaian dari database untuk mahasiswa tertentu via AJAX.
     * Mengisi input form dengan nilai yang ditemukan.
     * @param {string} nim - NIM mahasiswa yang nilainya akan dimuat
     */
    function loadStudentGrades(nim) {
        // Lakukan fetch API ke endpoint PHP untuk mengambil nilai
        fetch(`dNilaiAkhir.php?action=get_student_grades&nim=${nim}&id_sidang=${currentSidangId}&nomor_dosen=${loggedInNomorDosen}`)
            .then(response => {
                // Tangani respon jika tidak OK (misal 404, 500)
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json(); // Parse respon sebagai JSON
            })
            .then(data => {
                if (data && !data.error) {
                    // Isi input nilai desktop dengan data yang diterima (jika ada, jika tidak kosong)
                    document.getElementById('nilaiLaporanInput').value = data.n_dokumen || '';
                    document.getElementById('materiPresentasiInput').value = data.n_presentasi || '';
                    document.getElementById('tanyaJawabInput').value = data.n_tanyajawab || '';
                    document.getElementById('nilaiProyekInput').value = data.n_proyek || '';

                    // Isi input nilai mobile/tablet (jaga konsistensi)
                    document.getElementById('nilaiLaporanInput_v').value = data.n_dokumen || '';
                    document.getElementById('materiPresentasiInput_v').value = data.n_presentasi || '';
                    document.getElementById('tanyaJawabInput_v').value = data.n_tanyajawab || '';
                    document.getElementById('nilaiProyekInput_v').value = data.n_proyek || '';
                    
                    document.getElementById('catatan').value = data.catatan || ''; // Isi catatan

                    calculateAndDisplayAverage(); // Hitung dan tampilkan rata-rata setelah nilai dimuat
                } else if (data && data.error) {
                    // Tampilkan error dari PHP via SweetAlert
                    console.error("Error fetching grades:", data.error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Memuat Nilai!',
                        html: `Terjadi kesalahan saat memuat nilai: <br>${data.error}`,
                        confirmButtonText: 'OK'
                    });
                    clearGradeInputs(); // Kosongkan input jika ada error spesifik
                } else {
                    // Jika tidak ada data atau data null, kosongkan input
                    clearGradeInputs(); 
                    document.getElementById('nilaiMahasiswa').value = '--';
                }
            })
            .catch(error => {
                // Tangani error jaringan atau parsing JSON
                console.error('Fetch Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan!',
                    text: 'Tidak dapat terhubung ke server atau memproses data. Mohon coba lagi.',
                    confirmButtonText: 'OK'
                });
                clearGradeInputs(); // Kosongkan input jika ada error jaringan
                document.getElementById('nilaiMahasiswa').value = '--';
            });
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
        const currentNim = document.getElementById('currentNimInput').value;
        if (currentNim) {
            // Memuat ulang nilai dari database untuk memastikan konsistensi
            loadStudentGrades(currentNim); 
        }
        TutupKonfirmasiModal();
    }

    /**
     * Membuka modal konfirmasi sebelum mengirim nilai akhir.
     * Melakukan validasi sederhana pada input nilai.
     */
    function bukaKonfirmasiModalKirim() {
        // PENTING: Validasi ini hanya memastikan nilai mahasiswa AKTIF sudah terisi.
        // Validasi bahwa SEMUA mahasiswa sudah dinilai dilakukan di PHP.
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
            // Tombol aktif jika semua mahasiswa telah dinilai oleh dosen ini DAN ada mahasiswa di kelompok
            btnKirim.disabled = !(allStudentsGradedByThisDosen && allMahasiswa.length > 0);
            if (btnKirim.disabled && allMahasiswa.length > 0) {
                 // Anda bisa tambahkan tooltip atau indikator visual di UI jika disabled karena belum lengkap
                 // Misalnya: btnKirim.title = "Belum semua mahasiswa dalam kelompok ini dinilai.";
            } else if (allMahasiswa.length === 0) {
                 btnKirim.title = "Tidak ada mahasiswa dalam kelompok ini.";
            }
        }
    }
</script>
  </body>
</html>