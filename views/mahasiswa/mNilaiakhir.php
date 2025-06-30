<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$path_to_root = '../../';

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: " . $path_to_root . "index.php");
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: " . $path_to_root . "index.php");
    exit();
}

require "../../koneksi/koneksiAndrew.php";

if (!isset($_SESSION['selected_sidang_id'])) {
    die("ID Sidang tidak ditemukan.");
}

$id_sidang = $_SESSION['selected_sidang_id'];

$nilaiAkhir = 'N/A';
$catatanText = 'Tidak ada catatan.';
$dataMahasiswa = [];

// =================== 1. Ambil Nilai Akhir ===================
$sqlNilai = "SELECT nilai_akhir FROM Sidang WHERE id_sidang = ?";
$stmtNilai = sqlsrv_query($conn, $sqlNilai, array($id_sidang));
if ($stmtNilai && $row = sqlsrv_fetch_array($stmtNilai, SQLSRV_FETCH_ASSOC)) {
    $nilaiAkhir = $row['nilai_akhir'] ?? 'N/A';
}

// =================== 2. Ambil Catatan Sidang ===================
$sqlCatatan = "
    SELECT d.nama_dosen, ds.catatan_sidang 
    FROM Detail_Sidang ds
    JOIN Dosen2 d ON ds.nomor_dosen = d.nomor_dosen
    WHERE ds.id_sidang = ?
";
$stmtCat = sqlsrv_query($conn, $sqlCatatan, array($id_sidang));
$catatanList = [];

if ($stmtCat !== false) {
    while ($row = sqlsrv_fetch_array($stmtCat, SQLSRV_FETCH_ASSOC)) {
        if (!empty($row['catatan_sidang'])) {
            $catatanList[] = $row['nama_dosen'] . ": " . $row['catatan_sidang'];
        }
    }
}
$catatanText = !empty($catatanList) ? implode(" | ", $catatanList) : "Tidak ada catatan.";

// =================== 3. Ambil Data Mahasiswa ===================
$sqlMhs = "
    SELECT 
        m.nim, 
        m.nama_mhs,
        mk.nama_matkul,
        d.nama_dosen AS dosen_pembimbing
    FROM Sidang s
    JOIN Mahasiswa2 m ON s.nim = m.nim
    LEFT JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
    LEFT JOIN MataKuliah mk ON ds.id_matkul = mk.id_matkul
    LEFT JOIN Bimbingan b ON m.nim = b.MahasiswaNIM
    LEFT JOIN Dosen2 d ON b.Dosennomor_dosen = d.nomor_dosen
    WHERE s.id_sidang = ?
";

$stmtMhs = sqlsrv_query($conn, $sqlMhs, array($id_sidang));
if ($stmtMhs !== false) {
    while ($row = sqlsrv_fetch_array($stmtMhs, SQLSRV_FETCH_ASSOC)) {
        $dataMahasiswa = [
            'nim' => $row['nim'],
            'nama' => $row['nama_mhs'],
            'matkul' => $row['nama_matkul'],
            'pembimbing' => $row['dosen_pembimbing']
        ];
    }
}

// =================== DEBUG OUTPUT (sementara) ===================
echo "<h3>Data Mahasiswa:</h3>";
echo "NIM: " . $dataMahasiswa['nim'] . "<br>";
echo "Nama: " . $dataMahasiswa['nama'] . "<br>";
echo "Mata Kuliah: " . $dataMahasiswa['matkul'] . "<br>";
echo "Pembimbing: " . $dataMahasiswa['pembimbing'] . "<br><br>";

echo "<h3>Nilai Akhir:</h3> $nilaiAkhir<br><br>";
echo "<h3>Catatan:</h3> $catatanText<br>";

sqlsrv_close($conn);
?>




<!DOCTYPE html> <!-- Mendeklarasikan bahwa dokumen ini adalah HTML5 -->
<html lang="en"> <!-- Elemen root dari halaman HTML, dengan atribut bahasa "English" -->
  <head>

    <!-- Bagian <head> berisi metadata dan link ke resource eksternal, tidak terlihat di halaman -->
    <meta charset="UTF-8" /> <!-- Menentukan set karakter yang digunakan adalah UTF-8 (standar universal) -->
    <meta name="viewport" content="width=device-width, initial-scale=1" /> <!-- Membuat halaman menjadi responsif agar tampil baik di berbagai perangkat -->
    
    <!-- === STYLESHEETS & FONTS === -->
    <!-- Link ke stylesheet Bootstrap dari CDN untuk styling dasar dan komponen UI -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <!-- Link ke file JavaScript Bootstrap (termasuk Popper.js) untuk fungsionalitas seperti dropdown, modal, dll. -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Link ke stylesheet Bootstrap Icons untuk menggunakan ikon-ikon dari Bootstrap -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <!-- Link ke Google Fonts untuk memuat font "Poppins" -->
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins&display=swap"
      rel="stylesheet"
    />
    <!-- Link tambahan ke Google Fonts untuk berbagai ketebalan (weight) dari font "Poppins" -->
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
    <!-- Link ke file JavaScript SweetAlert2 untuk membuat notifikasi pop-up yang menarik -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Link ke stylesheet kustom lokal (style.css) -->
    <link rel="stylesheet" href="../../css/style.css" />
    <link rel="stylesheet" href="../../assets/css/mNilaiakhir.css">
    <!-- Link ke stylesheet kustom lokal tambahan (extra/style.css) -->
    <link rel="stylesheet" href="../../extra/style.css" />
    <!-- Link ke stylesheet Font Awesome dari CDN untuk menggunakan library ikon yang lebih beragam -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <title>Mahasiswa - Nilai Akhir</title> <!-- Judul yang akan muncul di tab browser -->
  </head>
  <body>
    <!-- Container utama untuk layout sidebar tdan konten -->
    <div id="NavSide">
        <!-- === SIDEBAR NAVIGASI KIRI === -->
        <div id="main-sidebar" class="NavSide__sidebar">
            <!-- Logo di bagian atas sidebar -->
             <div class="NavSide__sidebar-brand img ">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
            </div>
            <!-- Daftar menu navigasi -->
            <ul class="NavSide__sidebar-nav">
                <!-- Item menu "Detail Pengajuan" -->
                <li class="NavSide__sidebar-item ">
                    <b></b><b></b>
                    <a href="mdetailSidang.php"><span class="NavSide__sidebar-title fw-semibold">Detail Pengajuan</span></a>
                </li>
                <!-- Item menu "Perbaikan" -->
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mPerbaikan.php"><span class="NavSide__sidebar-title fw-semibold">Perbaikan</span></a>
                </li>
                <!-- Item menu "Nilai Akhir" (aktif) -->
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"> <!-- Kelas '...--active' menandakan halaman ini yang sedang dibuka -->
                    <b></b><b></b>
                    <a href="mNilaiakhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a>
                </li>

                <li class="NavSide__sidebar-item"> <!-- Kelas '...--active' menandakan halaman ini yang sedang dibuka -->
                    <b></b><b></b>
                    <a href="mSidang.php"><span class="NavSide__sidebar-title fw-semibold"> Kembali</span></a>
                </li>
            </ul>
        </div>

        <!-- === TOPBAR / HEADER === -->
        <div class="NavSide__topbar">
            <!-- Ikon "hamburger" untuk membuka/menutup sidebar di tampilan mobile -->
             <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <!-- Ikon-ikon di sisi kanan topbar -->
            <div class="header-icons">
                <i class="bi bi-bell-fill"></i> <!-- Ikon notifikasi -->
                <div class="profile-icon">
                    <i class="bi bi-person-fill fs-5"></i> <!-- Ikon profil pengguna -->
                </div>
            </div>
        </div>

        
   <!-- === KONTEN UTAMA HALAMAN === -->
   <main class="NavSide__main-content">
    <!-- Menggunakan container-fluid dari Bootstrap agar lebar konten penuh -->
    <div class="container-fluid">
        <!-- Baris untuk judul halaman -->
        <div class="row mb-4 title-container">
            <div class="col-12">
                <h2 class="main-title">
                    Detail Evaluasi - Sistem Pengajuan Sidang
                </h2>
            </div>
        </div>
        
        <!-- Baris untuk kartu data dan nilai -->
        <div class="row mt-4 g-4">
            <!-- Kolom untuk Kartu Data Mahasiswa (setengah lebar di layar besar) -->
            <div class="col-lg-6 d-flex">
              <div class="card flex-fill" id="carddataMahasiswa">
                <div class="card-body card-soft p-4">
                  <h3 class="card-title text-dark mb-4 text-center">Data Mahasiswa</h3>
                  <!-- Baris di dalam kartu untuk membagi info jadi dua kolom -->
                  <div class="row">
                      <!-- Kolom kiri untuk NIM dan Nama -->
                      <div class="col-sm-6 text-black">
                           <!-- Info NIM -->
                          <div class="info-group mb-5">
                            <div class="label-row d-flex align-items-center gap-2 mb-1">
                              <i class="fa-solid fa-id-card"></i> <!-- Ikon kartu identitas -->
                              <span class="fw-bold">NIM</span>
                            </div>
                            <div class="value-row text-secondary fw-bold">0920240033</div>
                          </div>
                          <!-- Info Nama -->
                          <div class="info-group mb-3">
                            <div class="label-row d-flex align-items-center gap-2 mb-1">
                              <i class="fa-solid fa-user"></i> <!-- Ikon pengguna -->
                              <span class="fw-bold">Nama</span>
                            </div>
                            <div class="value-row text-secondary fw-bold">M. Harris Nur S.</div>
                          </div>
                      </div>
                      <!-- Kolom kanan untuk Mata Kuliah dan Dosen -->
                      <div class="col-sm-6 text-black">
                           <!-- Info Mata Kuliah -->
                          <div class="info-group mb-5">
                            <div class="label-row d-flex align-items-center gap-2 mb-1">
                              <i class="fa-solid fa-book"></i> <!-- Ikon buku -->
                              <span class="fw-bold">Mata Kuliah</span>
                            </div>
                            <div class="value-row text-secondary fw-bold">Tugas Akhir</div>
                          </div>
                          <!-- Info Dosen Pembimbing -->
                          <div class="info-group mb-3">
                            <div class="label-row d-flex align-items-center gap-2 mb-1">
                              <i class="fa-solid fa-user-tie"></i> <!-- Ikon dosen -->
                              <span class="fw-bold">Dosen Pembimbing</span>
                            </div>
                            <div class="value-row text-secondary fw-bold">Timotius Victory</div>
                          </div>
                      </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Kolom untuk Kartu Nilai Mahasiswa -->
            <div class="col-lg-6 d-flex">
                <div class="card flex-fill" id="cardNilai">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <h3 class="card-title text-dark text-center">Nilai Mahasiswa:</h3>
                        <!-- Container untuk menengahkan nilai -->
                        <div class="d-flex justify-content-center align-items-center flex-grow-1">
                            <input type="text" class="form-control text-dark"
                                id="nilaiMahasiswa" value="<?= htmlspecialchars($nilaiAkhir) ?>" readonly />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Baris untuk kartu catatan yang read catatan -->
        <div class="row mt-5 ">
            <div class="col-12">
                <div class="card" id="cardcatatan">
                    <div class="card-body">
                        <h3 class="card-title text-dark" >Catatan :</h3>
                        <div class="text-dark" id="catatan-content">
                            <?= htmlspecialchars($catatanText) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

    </div>
</main>
<script src="../../assets/js/mNilaiakhir.js"></script>
</body>
</html>