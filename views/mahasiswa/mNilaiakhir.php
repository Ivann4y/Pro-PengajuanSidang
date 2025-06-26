    <?php
    session_start();
// $nim = $_SESSION['user_data']['nim'];
include '../../koneksi/koneksiAndrew.php';


// Cek apakah sidang dipilih
if (!isset($_SESSION['selected_sidang_id']) || empty($_SESSION['selected_sidang_id'])) {
    header("Location: mSidang.php");
    exit();
}
$id_sidang = $_SESSION['selected_sidang_id'];

$nilaiAkhir = null;
$catatan = 'Tidak ada catatan.';
$dataMahasiswa = [
    'nim' => '-',
    'nama' => '-',
    'matkul' => '-',
    'dosen' => '-'
];

//Cek kolom nilai_akhir ada di tabel Sidang
$sqlCheck = "SELECT TOP 1 * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='Sidang' AND COLUMN_NAME='nilai_akhir'";
$stmtCol = sqlsrv_query($conn, $sqlCheck);
$kolomAda = false;
if ($stmtCol !== false && ($colRow = sqlsrv_fetch_array($stmtCol, SQLSRV_FETCH_ASSOC))) {
    $kolomAda = true;
}

//Ambil nilai akhir
if ($kolomAda) {
    $sqlVal = "SELECT nilai_akhir FROM Sidang WHERE id_sidang = ?";
    $stmtVal = sqlsrv_query($conn, $sqlVal, array($id_sidang));
    if ($stmtVal && ($rowVal = sqlsrv_fetch_array($stmtVal, SQLSRV_FETCH_ASSOC))) {
        if (isset($rowVal['nilai_akhir']) && $rowVal['nilai_akhir'] !== null) {
            $nilaiAkhir = $rowVal['nilai_akhir'];
        }
    }
}

//Hitung manual jika nilai_akhir belum ada
if ($nilaiAkhir === null) {
    $sqlCalc = "
        SELECT 
            SUM(
                (
                    ISNULL(n_dokumen,0) 
                    + ISNULL(n_presentasi,0) 
                    + ISNULL(n_tanyajawab,0) 
                    + ISNULL(n_proyek,0)
                ) / 4.0 
                * ISNULL(bobot_penilaian,0)
            ) AS tot_score,
            SUM(ISNULL(bobot_penilaian,0)) AS tot_bobot
        FROM Penilaian
        WHERE id_sidang = ?
    ";
    $stmtCalc = sqlsrv_query($conn, $sqlCalc, array($id_sidang));
    if ($stmtCalc && ($rowCalc = sqlsrv_fetch_array($stmtCalc, SQLSRV_FETCH_ASSOC))) {
        if (isset($rowCalc['tot_bobot']) && $rowCalc['tot_bobot'] > 0) {
            $nilaiHitung = floatval($rowCalc['tot_score']) / floatval($rowCalc['tot_bobot']);
            $nilaiAkhir = number_format($nilaiHitung, 2);
        } else {
            $nilaiAkhir = '';
        }
    }
}

//Ambil catatan dari Detail_Sidang
$sqlCatatan = "SELECT catatan FROM Detail_Sidang WHERE id_sidang = ?";
$stmtCat = sqlsrv_query($conn, $sqlCatatan, array($id_sidang));
if ($stmtCat && ($rowCat = sqlsrv_fetch_array($stmtCat, SQLSRV_FETCH_ASSOC))) {
    if (!empty($rowCat['catatan'])) {
        $catatan = $rowCat['catatan'];
    }
}

// Ambil data mahasiswa dari relasi Sidang, Mahasiswa, MataKuliah, Dosen
$sqlMhs = "
    SELECT 
        m.nim, m.nama_mhs, mk.nama_matkul, d.nama_dosen
    FROM Sidang s
    JOIN Mahasiswa m ON s.nim = m.nim
    JOIN PenanggungJawab pj ON pj.id_sidang = s.id_sidang
    JOIN MataKuliah mk ON mk.id_matkul = pj.id_matkul
    JOIN Dosen d ON d.nomor_dosen = pj.nomor_dosen
    WHERE s.id_sidang = ?
";
$stmtMhs = sqlsrv_query($conn, $sqlMhs, array($id_sidang));
if ($stmtMhs && ($rowMhs = sqlsrv_fetch_array($stmtMhs, SQLSRV_FETCH_ASSOC))) {
    $dataMahasiswa = [
        'nim' => $rowMhs['nim'],
        'nama' => $rowMhs['nama_mhs'],
        'matkul' => $rowMhs['nama_matkul'],
        'dosen' => $rowMhs['nama_dosen']
    ];
}
?>



<!DOCTYPE html> <!-- Mendeklarasikan bahwa dokumen ini adalah HTML5 -->
<html lang="en"> <!-- Elemen root dari halaman HTML, dengan atribut bahasa "English" -->
  <head>
            <?php
    echo "Nilai akhir: " . $nilaiAkhir . "<br>";
    echo "Catatan: " . $catatan . "<br>";
    echo "Nama: " . $dataMahasiswa['nama'] . "<br>";
    ?>


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
                            <!-- Input field untuk menampilkan nilai. 'readonly' agar tidak bisa diubah oleh pengguna. -->
                            <input type="text" class="form-control text-dark"
                                id="nilaiMahasiswa" value="A" readonly />
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
                            Tidak ada catatan. <!-- Teks catatan dari Database yang dibuat oleh dosen   -->
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