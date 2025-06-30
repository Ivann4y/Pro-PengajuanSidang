<?php
session_start();

// 1. Get the ID from session
$id_sidang = $_SESSION['id_sidang_aktif'] ?? null;
if (!$id_sidang) {
    die("Session ID sidang tidak ditemukan");
}

// 2. Handle GET parameter if present
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $_SESSION['id_sidang_aktif'] = (int)$_GET['id'];
    header("Location: aDetailSidang.php");
    exit();
}

// 3. Database connection
require "../../koneksi/koneksiAndrew.php";

// ======================= 1. DATA MAHASISWA & SIDANG =======================
$dataSidang = [
    'judul' => '-', 
    'mahasiswa' => [], 
    'pembimbing' => '-'
];

$sqlSidangInfo = "
SELECT DISTINCT
    s.judul,
    m.nim,
    m.nama_mhs,
    (SELECT TOP 1 d.nama_dosen 
     FROM Bimbingan b 
     JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen
     WHERE b.id_kelompok = k.id_kelompok) AS nama_pembimbing
FROM Sidang s
JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
JOIN Kelompok_Mahasiswa km ON k.id_kelompok = km.id_kelompok
JOIN Mahasiswa m ON km.nim = m.nim
WHERE s.id_sidang = ?;
";

$stmtSidangInfo = sqlsrv_query($conn, $sqlSidangInfo, array($id_sidang));
if ($stmtSidangInfo === false) {
    die("Error query data sidang: " . print_r(sqlsrv_errors(), true));
}

while ($row = sqlsrv_fetch_array($stmtSidangInfo, SQLSRV_FETCH_ASSOC)) {
    if (empty($dataSidang['judul']) || $dataSidang['judul'] === '-') {
        $dataSidang['judul'] = $row['judul'];
        $dataSidang['pembimbing'] = $row['nama_pembimbing'];
    }
    $dataSidang['mahasiswa'][] = [
        'nim' => $row['nim'],
        'nama' => $row['nama_mhs']
    ];
}

// Remove duplicate mahasiswa (if any)
$dataSidang['mahasiswa'] = array_unique($dataSidang['mahasiswa'], SORT_REGULAR);

// ======================= 2. NILAI AKHIR MAHASISWA =======================
$nilaiAkhir = '-';

$sqlAkhir = "
SELECT
    p.nim,
    AVG(
        (p.n_dokumen * 0.25) +
        (p.n_presentasi * 0.25) +
        (p.n_tanyajawab * 0.30) +
        (p.n_proyek * 0.20)
    ) AS nilai_akhir_calculated
FROM Penilaian p
WHERE p.id_sidang = ?
GROUP BY p.nim
";

$stmtAkhir = sqlsrv_query($conn, $sqlAkhir, array($id_sidang));
if ($stmtAkhir === false) {
    die("Error query nilai akhir: " . print_r(sqlsrv_errors(), true));
}
if ($rowAkhir = sqlsrv_fetch_array($stmtAkhir, SQLSRV_FETCH_ASSOC)) {
    if (!is_null($rowAkhir['nilai_akhir_calculated'])) {
        $nilaiAkhir = number_format($rowAkhir['nilai_akhir_calculated'], 2);
    }
}



// ======================= 3. NILAI & CATATAN SETIAP PENGUJI =======================
$dataPenguji = [];

$sqlDetail = "
    SELECT 
        s.judul,
        m.nim,
        m.nama_mhs,
        (SELECT TOP 1 d.nama_dosen 
        FROM Bimbingan b 
        JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen
        WHERE b.id_kelompok = k.id_kelompok) AS nama_pembimbing,
        penguji.nama_dosen AS nama_penguji,
        p.n_dokumen,
        p.n_presentasi,
        p.n_tanyajawab,
        p.n_proyek,
        ds.catatan_sidang
    FROM Sidang s
    JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
    JOIN Kelompok_Mahasiswa km ON k.id_kelompok = km.id_kelompok
    JOIN Mahasiswa m ON km.nim = m.nim
    LEFT JOIN Penilaian p ON s.id_sidang = p.id_sidang AND m.nim = p.nim
    LEFT JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang AND p.nomor_dosen = ds.nomor_dosen
    LEFT JOIN Dosen penguji ON p.nomor_dosen = penguji.nomor_dosen
    WHERE s.id_sidang = ?;
    ";

$stmtDetail = sqlsrv_query($conn, $sqlDetail, array($id_sidang));
if ($stmtDetail === false) {
    die("Error query data penguji: " . print_r(sqlsrv_errors(), true));
}

while ($rowDetail = sqlsrv_fetch_array($stmtDetail, SQLSRV_FETCH_ASSOC)) {
    $dataPenguji[] = [
        'nim_dinilai' => $rowDetail['nim'],
        'mahasiswa_dinilai' => $rowDetail['nama_mhs'],
        'n_dokumen' => $rowDetail['n_dokumen'] ?? '-',
        'n_presentasi' => $rowDetail['n_presentasi'] ?? '-',
        'n_tanyajawab' => $rowDetail['n_tanyajawab'] ?? '-',
        'n_proyek' => $rowDetail['n_proyek'] ?? '-',
        'catatan' => $rowDetail['catatan_sidang'] ?? 'Tidak ada catatan.'
    ];
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

  
  <!-- FONT POPPINS GANDA DIHAPUS, CUKUP SATU INI YANG LENGKAP -->
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet"
  />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="../../css/button-styles.css" />
  <link rel="stylesheet" href="../../extra/style.css" />

  
  <title>Admin - Nilai Akhir</title>
  
  <style>
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
        <li class="NavSide_sidebar-item NavSide_sidebar-item--active">
          <b></b><b></b>
          <a href="aEvaluasi.php"><span class="NavSide__sidebar-title fw-semibold">Evaluasi</span></a>
        </li>
        <li class="NavSide__sidebar-item">
          <b></b><b></b>
          <a href="aNilaiAkhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a>
        </li>
      </ul>
    </div>

    
  <div id="NavSide">
    <div id="main-sidebar" class="NavSide__sidebar">
      <div class="NavSide__sidebar-brand">
        <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
      </div>
            <ul class="NavSide__sidebar-nav">
        <li class="NavSide__sidebar-item">
          <a href="aDetailSidang.php">
            <span class="NavSide__sidebar-title fw-semibold">Detail Sidang</span>
          </a>
        </li>
        <li class="NavSide__sidebar-item">
          <a href="aEvaluasi.php">
            <span class="NavSide__sidebar-title fw-semibold">Evaluasi</span>
          </a>
        </li>
        <li class="NavSide_sidebar-item NavSide_sidebar-item--active">
          <a href="aNilaiAkhir.php">
            <span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span>
          </a>
        </li>
          <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold"> Kembali</span></a>
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

    <!-- Top bar desktop -->
      
            <div class="dashboard-header p-3">
        <div>
          <h2 class="text-heading text-black mb-3" style="font-weight: 700;">Detail Evaluasi - Sistem Evaluasi Sidang</h2>

            <ul class="nav nav-tabs" id="myTab" role="tablist">
              <li class="nav-item" role="presentation">
                <a class="nav-link active" id="mahasiswa1-tab" data-bs-toggle="tab" data-bs-target="#mahasiswa1-tab-pane" role="tab" aria-controls="mahasiswa1-tab-pane" aria-selected="true" href="#">mahasiswa1</a>
              </li>
              <li class="nav-item" role="presentation">
                <a class="nav-link" id="mahasiswa2-tab" data-bs-toggle="tab" data-bs-target="#mahasiswa2-tab-pane" role="tab" aria-controls="mahasiswa2-tab-pane" aria-selected="false" href="#">mahasiswa2</a>
              </li>
              <li class="nav-item" role="presentation">
                <a class="nav-link" id="mahasiswa3-tab" data-bs-toggle="tab" data-bs-target="#mahasiswa3-tab-pane" role="tab" aria-controls="mahasiswa3-tab-pane" aria-selected="false" href="#">mahasiswa3</a>
              </li>
            </ul>
        </div>

        
        <div class="header-icons d-none d-md-flex">
            <a href="aNotifikasi.php" title="tugas"><i class="bi bi-bell-fill"></i></a>
            <div class="profile-icon">
              <a href="aProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
            </div>
          </div>
        </div>

  <script>
      document.addEventListener("DOMContentLoaded", function () {
                const navLinks = document.querySelectorAll(".nav-link");

                navLinks.forEach(function (link) {
                  link.addEventListener("click", function (e) {
                    e.preventDefault(); // biar gak reload
                    navLinks.forEach(l => l.classList.remove("active")); // hapus semua active
                    this.classList.add("active"); // tambahkan ke yang diklik
                  });
                });
              });
</script>


        <!-- Baris Nilai & Data Mahasiswa -->
        <div class="row align-items-stretch mb-4 p-2">
          <div class="col-lg-6 mb-3 d-flex">
            <div class="card flex-fill" id="carddataMahasiswa">
              <div class="card-body card-soft px-4 py-3">
                <h3 class="card-title text-black mb-4 text-center" style="padding:10px;">Data Mahasiswa</h3>
                <div class="d-flex flex-wrap gap-1 px-4 py-3">
                  <!-- Section 1 -->
                  <div class="section" style="flex: 1 1 200px; margin-left:30px; margin-top:25px; color: #333;">
                    <!-- NIM -->
                    <div class="info-group mb-3">
                      <div class="label-row d-flex align-items-center gap-2 mb-1">
                        <i class="fa-solid fa-id-card"></i>
                        <span class="fw-bold">NIM</span>  
                      </div>
                    <div class="value-row text-secondary fw-bold">
                      <?= $dataSidang['mahasiswa'][0]['nim'] ?? '-' ?>
                    </div>
                    </div>
                

                    <!-- Nama -->
                    <div class="info-group mb-3  section-bawah" style="margin-top:45px;">
                      <div class="label-row d-flex align-items-center gap-2 mb-1">
                        <i class="fa-solid fa-user"></i>
                        <span class="fw-bold">Nama</span>
                      </div>
                    <div class="value-row text-secondary fw-bold">
                      <?= $dataSidang['mahasiswa'][0]['nama'] ?? '-' ?>
                    </div>
                    </div>
                  </div>


                  <!-- Section 2 -->
                  <div class="section2" style="flex: 1 1 200px; margin-top:25px; color: #333;">
                    <!-- Mata Kuliah -->
                    <div class="info-group mb-3">
                      <div class="label-row d-flex align-items-center gap-2 mb-1">
                        <i class="fa-solid fa-book"></i>
                        <span class="fw-bold">Mata Kuliah</span>
                      </div>
                      <div class="value-row text-secondary fw-bold"> instalasi otomatis</div>
                    </div>
                    <!-- Dosen Pembimbing -->
                    <div class="info-group mb-3 section-bawah" style="margin-top:45px;" >
                      <div class="label-row d-flex align-items-center gap-2 mb-1">
                        <i class="fa-solid fa-user-tie"></i>
                        <span class="fw-bold">Dosen Pembimbing</span>
                      </div>
                      <div class="value-row text-secondary fw-bold">Dr. Ridah Indah F.</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 mb-3 d-flex">
            <div class="card flex-fill" id="cardNilai">
              <div class="card-body card-soft px-3 py-3 text-center">
                <h3 class="card-title mb-3 text-black" style="padding:10px;">Nilai Mahasiswa</h3>
                <div>
                <input
                  type="text"
                  class="form-control form-control-lg text-center mx-auto"
                  id="nilaiMahasiswa"
                  value="<?php echo $nilaiAkhir; ?>"
                  maxlength="5"
                  readonly
                />
              </div>
            </div>
          </div>
        </div>



        <!-- Baris Detail Penilaian tanpa modal -->
        <div class="row mt-3 p-3">
            <div class="card flex-fill h-100" id="carddetailPenilaian">
              <div class="card-body px-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h3 class="card-title text-black mb-0">Detail Penilaian :</h3>
                </div>
                <div class="row justify-content-center align-items-center">
                  <div class="col d-flex align-items-center">
                    <label for="nilaiLaporan" class="text-black me-2 mb-2">Nilai laporan</label>
                    <label class="colon1 me-2 mb-2">:</label>
                    <input
                      type="text"
                      class="form-control form-control-lg text-center input-nilai mb-2"
                      name="nilaiLaporan"
                      placeholder="<?= $dataPenguji[0]['n_dokumen'] ?? '-' ?>"
                      readonly/>
                  </div>
                  <div class="col d-flex align-items-center">
                    <label for="MateriPresentasi" class="text-black me-2 mb-2">Materi Presentasi</label>
                    <label class="colon2 me-2 mb-2">:</label>
                    <!-- Typo diperbaiki: dari type="type" jadi type="text" -->
                    <input
                      type="text"
                      class="form-control form-control-lg text-center input-nilai mb-2"
                      name="MateriPresentasi"
                      placeholder="<?= $dataPenguji[0]['n_presentasi'] ?? '-' ?>"
                      readonly/>
                  </div>
                  <div class="col d-flex align-items-center">
                    <label for="Penyampaian" class="text-black me-2 mb-2">Penyampaian</label>
                    <label class="colon3 me-2 mb-2">:</label>
                    <!-- Typo diperbaiki: dari type="tyep" jadi type="text" -->
                    <input
                      type="text"
                      class="form-control form-control-lg text-center input-nilai mb-2"
                      name="Penyampaian"
                      placeholder="<?= $dataPenguji[0]['n_tanyajawab'] ?? '-' ?>"
                      readonly/>
                  </div>
                  <div class="col d-flex align-items-center">
                    <label for="NilaiProyek" class="text-black me-2 mb-2">Nilai Proyek</label>
                    <label class="colon4 me-2 mb-2">:</label>
                    <!-- Typo diperbaiki: dari type="type" jadi type="text" -->
                  <input
                    type="text"
                    class="form-control form-control-lg text-center input-nilai mb-2"
                    name="NilaiProyek"
                    placeholder="<?= $dataPenguji[0]['n_proyek'] ?? '-' ?>"
                    readonly/>
                  </div>
                </div>
              </div>
            </div>
        </div>



        <!-- Baris Catatan -->
        <div class="row mt-3 p-3">
            <div class="card flex-fill h-100" id="cardcatatan">
              <div class="card-body px-4 py-3 d-flex flex-column">
                <h3 class="card-title text-black mb-4">Catatan:</h3>
                <textarea
                  class="form-control flex-grow-1"
                  id="catatan"
                  placeholder="<?= $dataPenguji[0]['catatan'] ?? 'Tidak ada catatan.' ?>"
                  rows="4"
                  readonly></textarea>

              </div>
            </div>
        </div>

    </main>
  </div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
      // Inisialisasi tooltip Bootstrap (jika ada)
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
      });


      // Fungsi untuk membuat elemen menjadi statis (tidak bisa diklik/fokus)
      const makeElementStatic = (element) => {
        if (element) {
          // 1. Pindahkan teks dari atribut 'placeholder' ke 'value' agar terlihat
          const placeholderText = element.getAttribute('placeholder');
          if (placeholderText) {
            element.value = placeholderText.trim(); // .trim() untuk hapus spasi berlebih
            element.removeAttribute('placeholder');
          }

          // 2. Buat elemen tidak bisa diinteraksi
          element.style.pointerEvents = 'none'; // Menonaktifkan semua event mouse (klik, hover)
          element.style.userSelect = 'none';   // Mencegah teks di dalamnya bisa di-select
          element.tabIndex = -1;               // Menghapus dari urutan navigasi keyboard (Tab)
        }
      };



      // Terapkan fungsi ke semua input nilai di dalam 'carddetailPenilaian'
      const scoreInputs = document.querySelectorAll('#carddetailPenilaian input.form-control');
      scoreInputs.forEach(makeElementStatic);

      // Terapkan fungsi ke textarea 'catatan'
      const notesTextarea = document.getElementById('catatan');
      makeElementStatic(notesTextarea);

      // --- AKHIR SOLUSI ---
    });



    // Kode untuk toggle sidebar (tetap dipertahankan)
    let menuToggle = document.querySelector(".NavSide__toggle");
    let sidebar = document.getElementById("main-sidebar");
    menuToggle.onclick = function() {
      menuToggle.classList.toggle("NavSide__toggle--active");
      sidebar.classList.toggle("NavSide__sidebar--active-mobile");
    };



    // Kode untuk active item di sidebar (tetap dipertahankan)
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


    // Fungsi untuk tombol kembali (tetap dipertahankan)
    function pindahKeHalamanDaftarSidang() {
      window.location.href = "aDaftarSidang.php"; // Ganti dengan halaman tujuan yang benar
    }
</script>
</body>
</html>