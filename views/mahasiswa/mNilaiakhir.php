<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$path_to_root = '../../';

// 1. Cek login dan role
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || $_SESSION['role'] !== 'mahasiswa') {
    $_SESSION['login_error'] = 'Anda harus login sebagai mahasiswa untuk mengakses halaman ini.';
    header("Location: " . $path_to_root . "index.php");
    exit();
}

if (!isset($_SESSION['user_data']['nim'])) {
    die("NIM mahasiswa tidak ditemukan di session. Silakan login kembali.");
}

$nim = $_SESSION['user_data']['nim'];

require "../../koneksi/koneksiAndrew.php";

// === 1. Ambil ID Sidang yang diikuti mahasiswa ===
// PERBAIKAN: Menggunakan Penilaian untuk memastikan mahasiswa punya nilai.
$sqlGetSidang = "SELECT TOP 1 id_sidang FROM Penilaian WHERE nim = ?";
$stmtGetSidang = sqlsrv_query($conn, $sqlGetSidang, [$nim]);

if (!$stmtGetSidang || !($row = sqlsrv_fetch_array($stmtGetSidang, SQLSRV_FETCH_ASSOC))) {
    // Jika tidak ditemukan di Penilaian, artinya belum dinilai.
    // Kita bisa hentikan atau tampilkan pesan 'belum dinilai'.
    // Untuk sekarang, kita akan hentikan agar tidak ada error di query selanjutnya.
    die("Data penilaian untuk Anda belum ditemukan. Mohon cek kembali nanti.");
}
$id_sidang = $row['id_sidang'];


// === 2. Hitung Nilai Akhir MAHASISWA (Weighted Average) ===
// PERBAIKAN: Logika ini sekarang benar-benar menghitung nilai akhir mahasiswa yang sedang login
// dengan memperhitungkan bobot dari setiap dosen penilai.
$sqlNilai = "
    WITH NilaiPerDosen AS (
        SELECT
            (n_dokumen * 0.25 + n_presentasi * 0.25 + n_tanyajawab * 0.30 + n_proyek * 0.20) AS nilai_dosen,
            bobot_penilaian
        FROM Penilaian
        WHERE id_sidang = ? AND nim = ?
    )
    SELECT
        SUM(nilai_dosen * bobot_penilaian) / SUM(bobot_penilaian) AS nilai_akhir_weighted
    FROM NilaiPerDosen;
";

$stmtNilai = sqlsrv_query($conn, $sqlNilai, [$id_sidang, $nim]);
$nilaiAngka = null;
$nilaiHuruf = 'N/A'; // Default value

if ($stmtNilai && ($rowNilai = sqlsrv_fetch_array($stmtNilai, SQLSRV_FETCH_ASSOC))) {
    if (isset($rowNilai['nilai_akhir_weighted'])) {
        $nilaiAngka = $rowNilai['nilai_akhir_weighted'];
        
        // Konversi ke nilai huruf
        if ($nilaiAngka >= 85) $nilaiHuruf = 'A';
        elseif ($nilaiAngka >= 75) $nilaiHuruf = 'B';
        elseif ($nilaiAngka >= 65) $nilaiHuruf = 'C';
        elseif ($nilaiAngka >= 50) $nilaiHuruf = 'D';
        else $nilaiHuruf = 'E';
    }
}

// === 3. Ambil Data Mahasiswa + Judul Sidang + Pembimbing ===
// PERBAIKAN: Query yang lebih sederhana dan fokus untuk mendapatkan data yang relevan.
$sqlDataSidang = "
    SELECT 
        m.nama_mhs, 
        s.judul, 
        d.nama_dosen AS dosen_pembimbing
    FROM Mahasiswa m
    LEFT JOIN Penilaian p ON m.nim = p.nim
    LEFT JOIN Sidang s ON p.id_sidang = s.id_sidang
    LEFT JOIN Bimbingan b ON s.id_kelompok = b.id_kelompok AND b.isPembimbing = 1
    LEFT JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen
    WHERE m.nim = ? AND s.id_sidang = ?
";
$stmtDataSidang = sqlsrv_query($conn, $sqlDataSidang, [$nim, $id_sidang]);
$dataSidang = [
    'nama' => $_SESSION['user_data']['nama_mhs'] ?? 'Nama Tidak Ditemukan',
    'judul' => 'Judul Tidak Ditemukan',
    'pembimbing' => 'Pembimbing Tidak Ditemukan'
];

if ($stmtDataSidang && ($rowData = sqlsrv_fetch_array($stmtDataSidang, SQLSRV_FETCH_ASSOC))) {
    $dataSidang['nama'] = $rowData['nama_mhs'];
    $dataSidang['judul'] = $rowData['judul'] ?? 'Belum ada judul';
    $dataSidang['pembimbing'] = $rowData['dosen_pembimbing'] ?? 'Belum ditentukan';
}


// === 4. Ambil Catatan Sidang yang Relevan untuk Mahasiswa ===
// PERBAIKAN: Menambahkan 'AND p.nim = ?' untuk memastikan hanya catatan untuk mahasiswa ini yang diambil.
$sqlCatatan = "
    SELECT d.nama_dosen, p.catatan_sidang
    FROM Penilaian p
    JOIN Dosen d ON p.nomor_dosen = d.nomor_dosen
    WHERE p.id_sidang = ? AND p.nim = ?
";
$stmtCat = sqlsrv_query($conn, $sqlCatatan, [$id_sidang, $nim]);
$catatanList = [];

if ($stmtCat) {
    while ($row = sqlsrv_fetch_array($stmtCat, SQLSRV_FETCH_ASSOC)) {
        if (!empty(trim($row['catatan_sidang']))) {
            $catatanList[] = "<strong>" . htmlspecialchars($row['nama_dosen']) . ":</strong> " . htmlspecialchars($row['catatan_sidang']);
        }
    }
}
$catatanText = !empty($catatanList) ? implode("<br><br>", $catatanList) : "Tidak ada catatan.";

sqlsrv_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- === STYLESHEETS & FONTS === -->
    <!-- Bootstrap & Icons (These paths from CDN are fi ne) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <!-- Google Fonts (These are fine) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet" />

    <!-- === KOREKSI PATH CSS LOKAL ANDA DI SINI === -->
    <!-- Path ke style.css utama (untuk layout sidebar) -->
    <link rel="stylesheet" href="../../assets/css/style.css" /> 
    
    <!-- Path ke mNilaiakhir.css (untuk style spesifik halaman ini) -->
    <link rel="stylesheet" href="../../assets/css/mNilaiakhir.css">

    <!-- Path ke button-styles.css -->
    <link rel="stylesheet" href="../../css/button-styles.css" />

    <!-- Path ke extra/style.css (jika masih digunakan) -->
    <link rel="stylesheet" href="../../extra/style.css" />
    
    <!-- Scripts (These are fine) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <title>Mahasiswa - Nilai Akhir</title>
</head>
<body>
    <div id="NavSide">
        <!-- ... (Bagian Sidebar dan Topbar Anda tetap sama) ... -->
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand img "><img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo"></div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item "><a href="mdetailSidang.php"><span class="NavSide__sidebar-title fw-semibold">Detail Pengajuan</span></a></li>
                <li class="NavSide__sidebar-item"><a href="mPerbaikan.php"><span class="NavSide__sidebar-title fw-semibold">Perbaikan</span></a></li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><a href="mNilaiakhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a></li>
                <li class="NavSide__sidebar-item"><a href="mSidang.php"><span class="NavSide__sidebar-title fw-semibold"> Kembali</span></a></li>
            </ul>
        </div>
        <div class="NavSide__topbar">
             <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
            <div class="header-icons"><i class="bi bi-bell-fill"></i><div class="profile-icon"><i class="bi bi-person-fill fs-5"></i></div></div>
        </div>
           <main class="NavSide__main-content">
            <div class="container-fluid">
                <div class="row mb-4 title-container"><div class="col-12"><h2 class="main-title">Detail Evaluasi - Sistem Pengajuan Sidang</h2></div></div>
                
                <div class="row mt-4 g-4">
                    <div class="col-lg-6 d-flex">
                    <div class="card flex-fill" id="carddataMahasiswa">
                        <div class="card-body card-soft p-4">
                        <h3 class="card-title text-dark mb-4 text-center">Data Mahasiswa</h3>
                        <div class="row">
                            <div class="col-sm-6 text-black">
                                <div class="info-group mb-5">
                                    <div class="label-row d-flex align-items-center gap-2 mb-1"><i class="fa-solid fa-id-card"></i><span class="fw-bold">NIM</span></div>
                                    <div class="value-row text-secondary fw-bold"><?= htmlspecialchars($nim) ?></div>
                                </div>
                                <div class="info-group mb-3">
                                    <div class="label-row d-flex align-items-center gap-2 mb-1"><i class="fa-solid fa-user"></i><span class="fw-bold">Nama</span></div>
                                    <div class="value-row text-secondary fw-bold"><?= htmlspecialchars($dataSidang['nama']) ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6 text-black">
                                <div class="info-group mb-5">
                                    <div class="label-row d-flex align-items-center gap-2 mb-1"><i class="fa-solid fa-book"></i><span class="fw-bold">Judul Proyek</span></div>
                                    <div class="value-row text-secondary fw-bold"><?= htmlspecialchars($dataSidang['judul']) ?></div>
                                </div>
                                <div class="info-group mb-3">
                                    <div class="label-row d-flex align-items-center gap-2 mb-1"><i class="fa-solid fa-user-tie"></i><span class="fw-bold">Dosen Pembimbing</span></div>
                                    <div class="value-row text-secondary fw-bold"><?= htmlspecialchars($dataSidang['pembimbing']) ?></div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                    
                    <div class="col-lg-6 d-flex">
                        <div class="card flex-fill" id="cardNilai">
                            <div class="card-body d-flex flex-column justify-content-center">
                                <h3 class="card-title text-dark text-center">Nilai Mahasiswa:</h3>
                                <div class="d-flex justify-content-center align-items-center flex-grow-1">
                                    <!-- PERBAIKAN: Menggunakan variabel $nilaiHuruf yang sudah dihitung -->
                                    <input type="text" class="form-control text-dark" id="nilaiMahasiswa" value="<?= htmlspecialchars($nilaiHuruf) ?>" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-5 ">
                    <div class="col-12">
                        <div class="card" id="cardcatatan">
                            <div class="card-body">
                                <h3 class="card-title text-dark" >Catatan dari Dosen Penguji:</h3>
                                <!-- PERBAIKAN: Menggunakan $catatanText dan membiarkan HTML (seperti <br>) dirender -->
                                <div class="text-dark" id="catatan-content">
                                    <?= $catatanText ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    

<script src="../../assets/js/mNilaiakhir.js"></script>
</body>
</html>