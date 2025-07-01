<?php
session_start();

require "../../koneksi/koneksiAndrew.php";

// ======================= STATIC DATA FOR TESTING =======================
$id_sidang = 4001;
$nim = '1000000001'; // Pastikan nim adalah string jika di database tipenya char/varchar

// ======================= INITIALIZE VARIABLES =======================
$dataMahasiswa = [
    'nim' => $nim,
    'nama_mhs' => 'Data tidak ditemukan',
    'nama_matkul' => 'Data tidak ditemukan',
    'nama_pembimbing' => 'Data tidak ditemukan' // Akan diisi dengan salah satu nama penguji
];
$nilaiDetail = [
    'dokumen' => '-',
    'presentasi' => '-',
    'tanyajawab' => '-',
    'proyek' => '-'
];
$nilaiAkhirAngka = '-';
$nilaiAkhirHuruf = '';
$semuaCatatan = 'Tidak ada catatan.';


// ======================= 1. GET MAHASISWA & SIDANG INFO =======================
// untuk menghubungkan Mahasiswa dengan Detail_Sidang, karena Detail_Sidang tidak punya kolom 'nim'.
$sqlInfo = "
    SELECT TOP 1
        m.nama_mhs,
        mk.nama_matkul,
        d.nama_dosen
    FROM Mahasiswa m
    JOIN Penilaian p ON m.nim = p.nim
    JOIN Detail_Sidang ds ON p.id_sidang = ds.id_sidang AND p.nomor_dosen = ds.nomor_dosen
    JOIN MataKuliah mk ON ds.id_matkul = mk.id_matkul
    JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen
    WHERE m.nim = ? AND p.id_sidang = ?;
";

$paramsInfo = array($nim, $id_sidang);
$stmtInfo = sqlsrv_query($conn, $sqlInfo, $paramsInfo);

if ($stmtInfo === false) {
    die("Error query data mahasiswa & sidang: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}

if ($row = sqlsrv_fetch_array($stmtInfo, SQLSRV_FETCH_ASSOC)) {
    $dataMahasiswa['nama_mhs'] = $row['nama_mhs'];
    $dataMahasiswa['nama_matkul'] = $row['nama_matkul'];
    $dataMahasiswa['nama_pembimbing'] = $row['nama_dosen']; // Diasumsikan dosen pertama yang ditemukan adalah pembimbing
}


// Fungsi untuk konversi nilai angka ke huruf. Didefinisikan di luar agar rapi.
function getGrade($nilai) {
    if ($nilai >= 85) return 'A';
    if ($nilai >= 80) return 'B+';
    if ($nilai >= 75) return 'B';
    if ($nilai >= 70) return 'C+';
    if ($nilai >= 65) return 'C';
    if ($nilai >= 55) return 'D';
    return 'E';
}

// ======================= 2. CALCULATE SCORES =======================
// Query untuk mengambil rata-rata setiap komponen nilai dari semua dosen penguji
$sqlNilai = "
    SELECT
        AVG(CAST(n_dokumen AS FLOAT)) AS avg_dokumen,
        AVG(CAST(n_presentasi AS FLOAT)) AS avg_presentasi,
        AVG(CAST(n_tanyajawab AS FLOAT)) AS avg_tanyajawab,
        AVG(CAST(n_proyek AS FLOAT)) AS avg_proyek
    FROM Penilaian
    WHERE id_sidang = ? AND nim = ?;
";

$paramsNilai = array($id_sidang, $nim);
$stmtNilai = sqlsrv_query($conn, $sqlNilai, $paramsNilai);

if ($stmtNilai === false) {
    die("Error query penilaian: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}

if ($rowNilai = sqlsrv_fetch_array($stmtNilai, SQLSRV_FETCH_ASSOC)) {
    // Tampilkan nilai rata-rata per komponen
    $nilaiDetail['dokumen'] = !is_null($rowNilai['avg_dokumen']) ? number_format($rowNilai['avg_dokumen'], 2) : '-';
    $nilaiDetail['presentasi'] = !is_null($rowNilai['avg_presentasi']) ? number_format($rowNilai['avg_presentasi'], 2) : '-';
    $nilaiDetail['tanyajawab'] = !is_null($rowNilai['avg_tanyajawab']) ? number_format($rowNilai['avg_tanyajawab'], 2) : '-';
    $nilaiDetail['proyek'] = !is_null($rowNilai['avg_proyek']) ? number_format($rowNilai['avg_proyek'], 2) : '-';

    // Hitung nilai akhir berdasarkan bobot hanya jika ada nilai
    if (!is_null($rowNilai['avg_dokumen'])) {
        $nilaiAkhirAngka =
            ($rowNilai['avg_dokumen'] * 0.25) +
            ($rowNilai['avg_presentasi'] * 0.25) +
            ($rowNilai['avg_tanyajawab'] * 0.30) +
            ($rowNilai['avg_proyek'] * 0.20);
        
        $nilaiAkhirHuruf = getGrade($nilaiAkhirAngka);
        $nilaiAkhirAngka = number_format($nilaiAkhirAngka, 2);
    }
}

// ======================= 3. GET ALL NOTES =======================
// Query untuk mengambil semua catatan dari setiap dosen penguji
$sqlCatatan = "
    SELECT
        d.nama_dosen,
        ds.catatan_sidang
    FROM Detail_Sidang ds
    JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen
    WHERE ds.id_sidang = ?
    ORDER BY d.nama_dosen;
";

$paramsCatatan = array($id_sidang);
$stmtCatatan = sqlsrv_query($conn, $sqlCatatan, $paramsCatatan);

if ($stmtCatatan === false) {
    die("Error query catatan: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}


$catatanArray = [];
while ($rowCatatan = sqlsrv_fetch_array($stmtCatatan, SQLSRV_FETCH_ASSOC)) {
    $catatan = trim($rowCatatan['catatan_sidang']);
    if (!empty($catatan) && $catatan !== '-') {
        // Format catatan agar lebih rapi saat ditampilkan di textarea
        $catatanArray[] = "• " . $rowCatatan['nama_dosen'] . ":\n  " . $catatan;
    }
}

if (!empty($catatanArray)) {
    // Gabungkan catatan dengan 2x baris baru untuk spasi antar catatan
    $semuaCatatan = implode("\n\n", $catatanArray);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="../../css/style.css" />
  <link rel="stylesheet" href="../../css/button-styles.css" />
  <link rel="stylesheet" href="../../extra/style.css" />
  <title>Admin - Nilai Akhir</title>

  <style>
    #NavSide { display: flex; min-height: 100vh; position: relative; }
    .label-row i { font-size: 1.5rem; }
    body, .card, .form-control, h1, h2, h3, h4, h5, h6 { font-family: "Poppins", sans-serif !important; color: #464869; }
    #cardNilai, #carddataMahasiswa, #carddetailPenilaian, #cardcatatan {
      background-color: rgb(235, 238, 245);
      border-radius: 50px;
      border: none !important;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      width: 100%;
    }
    .nilai-mahasiswa-display {
      font-size: 5rem !important; 
      font-weight: bold;
      text-align: center;
      background-color: transparent !important;
      border: none !important;
      box-shadow: none !important;
      padding: 0;
      cursor: default;
    }
    #carddetailPenilaian label { font-weight: 550; }
    .detail-penilaian-input {
      font-size: 1.2rem; 
      font-weight: 600;
      text-align: center;
      border: none;
      background-color: transparent;
      padding: 5px;
      cursor: default;
    }
    #catatan {
      background-color: rgb(235, 238, 245);
      border: none;
      
      padding: 15px;
      font-size: 1rem;
      resize: vertical;
      cursor: default;
      white-space: pre-wrap; 
    }
    textarea[readonly], input[readonly] { background-color: #e9ecef; }
  </style>
</head>
<body>
  <div id="NavSide">
    <div id="main-sidebar" class="NavSide__sidebar">
      <div class="NavSide__sidebar-brand">
        <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
      </div>
      <ul class="NavSide__sidebar-nav">
        <li class="NavSide__sidebar-item">
          <b></b><b></b>
          <a href="aDetailSidang.php"><span class="NavSide__sidebar-title fw-semibold">Detail Sidang</span></a>
        </li>
        <li class="NavSide__sidebar-item ">
          <b></b><b></b>
          <a href="aEvaluasi.php"><span class="NavSide__sidebar-title fw-semibold">Evaluasi</span></a>
        </li>
        <li class="NavSide__sidebar-item NavSide__sidebar-item--active ">
          <b></b><b></b>
          <a href="aNilaiAkhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a>
        </li>
        <li class="NavSide__sidebar-item">
          <b></b><b></b>
          <a href="aDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Kembali</span></a>
        </li>
      </ul>
    </div>
    <div class="NavSide__topbar">
      <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
    </div>

    <main class="NavSide__main-content">
      <div class="dashboard-header p-3">
        <div>
          <h2 class="text-heading text-black mb-5" style="font-weight: 700;">Detail Evaluasi - Sistem Evaluasi Sidang</h2>
          <!-- Navigasi Tab disederhanakan untuk menampilkan satu mahasiswa yang sedang dilihat -->
          <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <a class="nav-link active" id="mahasiswa-tab" href="#"><?= htmlspecialchars($dataMahasiswa['nama_mhs']) ?></a>
            </li>
          </ul>
        </div>
        <div class="header-icons d-none d-md-flex">
            <a href="aNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
            <div class="profile-icon"><a href="aProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a></div>
        </div>
      </div>

      <!-- KONTEN UTAMA -->
      <div class="p-3">
          <div class="row align-items-stretch mb-2">
            <!-- Kartu Data Mahasiswa -->
            <div class="col-lg-6 mb-4 d-flex">
              <div class="card flex-fill" id="carddataMahasiswa">
                <div class="card-body px-3 py-2">
                  <h3 class="card-title text-black mb-4 text-center py-2">Data Mahasiswa</h3>
                  <div class="d-flex flex-column gap-4 px-4 py-2">
                    <div class="info-group"><div class="label-row d-flex align-items-center gap-3 mb-1"><i class="fa-solid fa-id-card"></i><span class="fw-bold">NIM</span></div><div class="value-row text-secondary fw-bold ps-5"><?= htmlspecialchars($dataMahasiswa['nim']) ?></div></div>
                    <div class="info-group"><div class="label-row d-flex align-items-center gap-3 mb-1"><i class="fa-solid fa-user"></i><span class="fw-bold">Nama</span></div><div class="value-row text-secondary fw-bold ps-5"><?= htmlspecialchars($dataMahasiswa['nama_mhs']) ?></div></div>
                    <div class="info-group"><div class="label-row d-flex align-items-center gap-3 mb-1"><i class="fa-solid fa-book"></i><span class="fw-bold">Mata Kuliah</span></div><div class="value-row text-secondary fw-bold ps-5"><?= htmlspecialchars($dataMahasiswa['nama_matkul']) ?></div></div>
                    <div class="info-group"><div class="label-row d-flex align-items-center gap-3 mb-1"><i class="fa-solid fa-user-tie"></i><span class="fw-bold">Dosen</span></div><div class="value-row text-secondary fw-bold ps-5"><?= htmlspecialchars($dataMahasiswa['nama_pembimbing']) ?></div></div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Kartu Nilai Mahasiswa -->
            <div class="col-lg-6 mb-4 d-flex">
              <div class="card flex-fill" id="cardNilai">
                <div class="card-body px-3 py-3 text-center d-flex flex-column justify-content-center">
                  <h3 class="card-title mb-4 text-black py-2">Nilai Akhir Mahasiswa</h3>
                  <input type="text" class="form-control nilai-mahasiswa-display" value="<?= $nilaiAkhirAngka !== '-' ? htmlspecialchars($nilaiAkhirHuruf) : '-' ?>" readonly/>
                  <p class="mt-3 fs-5 text-secondary fw-bold"><?= $nilaiAkhirAngka !== '-' ? '(Skor: ' . htmlspecialchars($nilaiAkhirAngka) . ')' : 'Belum dinilai' ?></p>
                </div>
              </div>
            </div>
          </div>
          <!-- Kartu Detail Penilaian -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="card h-100" id="carddetailPenilaian">
                <div class="card-body px-4 py-4">
                  <h3 class="card-title text-black mb-3">Detail Penilaian</h3>
                  <div class="row text-center">
                    <div class="col-md-3 col-6"><label class="d-block mb-1">Nilai Laporan:</label><input type="text" class="form-control detail-penilaian-input" value="<?= htmlspecialchars($nilaiDetail['dokumen']) ?>" readonly/></div>
                    <div class="col-md-3 col-6"><label class="d-block mb-1">Presentasi:</label><input type="text" class="form-control detail-penilaian-input" value="<?= htmlspecialchars($nilaiDetail['presentasi']) ?>" readonly/></div>
                    <div class="col-md-3 col-6"><label class="d-block mb-1">Tanya Jawab:</label><input type="text" class="form-control detail-penilaian-input" value="<?= htmlspecialchars($nilaiDetail['tanyajawab']) ?>" readonly/></div>
                    <div class="col-md-3 col-6"><label class="d-block mb-1">Nilai Proyek:</label><input type="text" class="form-control detail-penilaian-input" value="<?= htmlspecialchars($nilaiDetail['proyek']) ?>" readonly/></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Kartu Catatan -->
          <div class="row">
            <div class="col-12">
              <div class="card h-100" id="cardcatatan">
                <div class="card-body px-4 py-4 d-flex flex-column">
                  <h3 class="card-title text-black mb-3">Catatan</h3>
                  <div id="catatan" class="form-control flex-grow-1" rows="8" ><?= htmlspecialchars($semuaCatatan) ?></div>
                </div>
              </div>
            </div>
          </div>

<script>
    // Kode untuk toggle sidebar (tidak diubah)
    let menuToggle = document.querySelector(".NavSide__toggle");
    let sidebar = document.getElementById("main-sidebar");
    menuToggle.onclick = function() {
      menuToggle.classList.toggle("NavSide__toggle--active");
      sidebar.classList.toggle("NavSide__sidebar--active-mobile");
    };

    // Kode untuk active item di sidebar (tidak diubah)
    let listItems = document.querySelectorAll(".NavSide__sidebar-item");
    for (let i = 0; i < listItems.length; i++) {
      listItems[i].onclick = function() {
        if (!this.classList.contains("NavSide__sidebar-item--active")) {
          for (let j = 0; j < listItems.length; j++) {
            listItems[j].classList.remove("NavSide__sidebar-item--active");
          }
          this.classList.add("NavSide__sidebar-item--active");
        }
      };
    }
</script>
</body>
</html>