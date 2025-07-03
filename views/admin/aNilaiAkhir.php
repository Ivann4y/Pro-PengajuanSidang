<?php
// Memulai sesi untuk mengakses data pengguna yang login (jika diperlukan di masa depan).
session_start();

// Menyertakan file koneksi ke database.
require "../../koneksi/koneksiAndrew.php";


if ($conn === false) {
    die("Koneksi ke database gagal: <pre>" . print_r(sqlsrv_errors(), true) . "</pre>");
}


$id_sidang = isset($_GET['id_sidang']) ? (int)$_GET['id_sidang'] : 0;
// Mengambil 'nim' mahasiswa yang aktif dari URL. Jika tidak ada, nilainya null.
$current_nim = isset($_GET['nim']) ? trim($_GET['nim']) : null;
// Variabel untuk menampung pesan error jika terjadi kesalahan.
$error_message = '';

$mahasiswa_list = []; // Array untuk menampung daftar mahasiswa dalam kelompok (untuk tab).
$nama_matkul = 'Data tidak ditemukan'; // Untuk menyimpan nama mata kuliah sidang.
$dosen_terkait_sidang = 'Data tidak ditemukan'; // Untuk menyimpan nama dosen pembimbing/pengampu.
$id_kelompok = null; // Untuk menyimpan ID kelompok mahasiswa.
$id_matkul = null; // Untuk menyimpan ID mata kuliah.
$jenis_sidang = null; // Untuk menyimpan jenis sidang (0=TA, 1=Semester).


// =========================================================================
// 3. PENGAMBILAN DETAIL SIDANG (ID KELOMPOK, JENIS, ID MATKUL)
// =========================================================================
if ($id_sidang > 0) {
    // Query untuk mengambil detail dasar sidang berdasarkan id_sidang dari URL.
    $sql_detail = "SELECT TOP 1 s.id_kelompok, s.jenis_sidang, ds.id_matkul
                   FROM Sidang s
                   LEFT JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
                   WHERE s.id_sidang = ?";
    $stmt_detail = sqlsrv_query($conn, $sql_detail, array($id_sidang));

    if ($stmt_detail && $detail = sqlsrv_fetch_array($stmt_detail, SQLSRV_FETCH_ASSOC)) {
        // Jika data ditemukan, isi variabel-variabel yang sudah disiapkan.
        $id_kelompok = $detail['id_kelompok'];
        $id_matkul = $detail['id_matkul'];
        $jenis_sidang = $detail['jenis_sidang'];
    } else {
        $error_message = "Data Sidang dengan ID: " . htmlspecialchars($id_sidang) . " tidak ditemukan.";
        $id_sidang = 0; // Invalidate agar query lain tidak jalan.
    }
} else {
    $error_message = "ID Sidang tidak valid atau tidak disediakan.";
}

// =========================================================================
// 4. PENGAMBILAN DAFTAR MAHASISWA DALAM KELOMPOK
// =========================================================================
if ($id_kelompok) {
    // Query untuk mengambil semua NIM dan nama mahasiswa berdasarkan id_kelompok.
    $sql_mhs_list = "SELECT km.nim, m.nama_mhs
                     FROM Kelompok_Mahasiswa km
                     JOIN Mahasiswa m ON km.nim = m.nim
                     WHERE km.id_kelompok = ?
                     ORDER BY m.nama_mhs";
    $stmt_mhs_list = sqlsrv_query($conn, $sql_mhs_list, array($id_kelompok));

    if ($stmt_mhs_list) {
        while ($row = sqlsrv_fetch_array($stmt_mhs_list, SQLSRV_FETCH_ASSOC)) {
            $mahasiswa_list[] = $row;
        }
    }
    
    // Menentukan mahasiswa mana yang aktif. Jika tidak ada 'nim' di URL, pakai yang pertama.
    if (empty($current_nim) || !in_array($current_nim, array_column($mahasiswa_list, 'nim'))) {
        $current_nim = !empty($mahasiswa_list) ? $mahasiswa_list[0]['nim'] : null;
    }
}

// Jika setelah semua proses tidak ada mahasiswa ditemukan, set pesan error.
if (empty($mahasiswa_list) && empty($error_message) && $id_sidang > 0) {
    $error_message = "Tidak ada mahasiswa yang terdaftar dalam kelompok untuk sidang ini.";
}

// =========================================================================
// 5. PENGAMBILAN NAMA MATA KULIAH & NAMA DOSEN TERKAIT
// =========================================================================
// Mengambil nama mata kuliah jika id_matkul ditemukan.
if ($id_matkul) {
    $sql_matkul_q = "SELECT nama_matkul FROM MataKuliah WHERE id_matkul = ?";
    $stmt_matkul_q = sqlsrv_query($conn, $sql_matkul_q, array($id_matkul));
    if ($stmt_matkul_q && $row_matkul = sqlsrv_fetch_array($stmt_matkul_q, SQLSRV_FETCH_ASSOC)) {
        $nama_matkul = $row_matkul['nama_matkul'];
    }
}

// Menentukan dan mengambil nama dosen berdasarkan jenis sidang.
if (isset($jenis_sidang)) {
    if ((int)$jenis_sidang === 0) { // Jika jenis sidang adalah 0 (Tugas Akhir).
        $sql_dosen = "SELECT d.nama_dosen FROM Dosen d JOIN Bimbingan b ON d.nomor_dosen = b.nomor_dosen WHERE b.id_kelompok = ? AND b.isPembimbing = 1";
        $params_dosen = array($id_kelompok);
    } elseif ((int)$jenis_sidang === 1 && $id_matkul) { // Jika jenis sidang adalah 1 (Semester).
        $sql_dosen = "SELECT TOP 1 d.nama_dosen FROM Dosen d JOIN Pengampu_Kelas pk ON d.nomor_dosen = pk.nomor_dosen WHERE pk.id_matkul = ?";
        $params_dosen = array($id_matkul);
    }

    if (isset($sql_dosen)) {
        $stmt_dosen = sqlsrv_query($conn, $sql_dosen, $params_dosen);
        if ($stmt_dosen && $row_dosen = sqlsrv_fetch_array($stmt_dosen, SQLSRV_FETCH_ASSOC)) {
            $dosen_terkait_sidang = $row_dosen['nama_dosen'];
        }
    }
}

// =========================================================================
// BAGIAN INI SPESIFIK UNTUK aNilaiAkhir.php (MENGHITUNG NILAI AKHIR)
// =========================================================================
$dataMahasiswa = [
    'nim' => $current_nim ?? 'N/A',
    'nama_mhs' => 'Data tidak ditemukan',
    'nama_matkul' => $nama_matkul,
    'nama_pembimbing' => $dosen_terkait_sidang
];
$nilaiDetail = [ 'dokumen' => '-', 'presentasi' => '-', 'tanyajawab' => '-', 'proyek' => '-' ];
$nilaiAkhirAngka = '-';
$nilaiAkhirHuruf = '';
$semuaCatatan = 'Tidak ada catatan.';

// Fungsi konversi nilai
function getGrade($nilai) {
    if ($nilai >= 85) return 'A';
    if ($nilai >= 80) return 'B+';
    if ($nilai >= 75) return 'B';
    if ($nilai >= 70) return 'C+';
    if ($nilai >= 65) return 'C';
    if ($nilai >= 55) return 'D';
    return 'E';
}

// Hanya jalankan query nilai jika ada mahasiswa yang aktif dan tidak ada error
if ($current_nim && empty($error_message)) {
    // Isi nama mahasiswa aktif
    foreach($mahasiswa_list as $mhs) {
        if($mhs['nim'] == $current_nim) {
            $dataMahasiswa['nama_mhs'] = $mhs['nama_mhs'];
            break;
        }
    }

    // Query untuk menghitung rata-rata nilai dari semua dosen penguji
    $sqlNilai = "
        SELECT
            AVG(CAST(n_dokumen AS FLOAT)) AS avg_dokumen,
            AVG(CAST(n_presentasi AS FLOAT)) AS avg_presentasi,
            AVG(CAST(n_tanyajawab AS FLOAT)) AS avg_tanyajawab,
            AVG(CAST(n_proyek AS FLOAT)) AS avg_proyek
        FROM Penilaian
        WHERE id_sidang = ? AND nim = ?;
    ";
    $paramsNilai = array($id_sidang, $current_nim);
    $stmtNilai = sqlsrv_query($conn, $sqlNilai, $paramsNilai);

    if ($stmtNilai && $rowNilai = sqlsrv_fetch_array($stmtNilai, SQLSRV_FETCH_ASSOC)) {
        $nilaiDetail['dokumen'] = !is_null($rowNilai['avg_dokumen']) ? number_format($rowNilai['avg_dokumen'], 2) : '-';
        $nilaiDetail['presentasi'] = !is_null($rowNilai['avg_presentasi']) ? number_format($rowNilai['avg_presentasi'], 2) : '-';
        $nilaiDetail['tanyajawab'] = !is_null($rowNilai['avg_tanyajawab']) ? number_format($rowNilai['avg_tanyajawab'], 2) : '-';
        $nilaiDetail['proyek'] = !is_null($rowNilai['avg_proyek']) ? number_format($rowNilai['avg_proyek'], 2) : '-';

        if (!is_null($rowNilai['avg_dokumen'])) {
            $nilaiAkhirAngka = ($rowNilai['avg_dokumen'] * 0.25) + ($rowNilai['avg_presentasi'] * 0.25) + ($rowNilai['avg_tanyajawab'] * 0.30) + ($rowNilai['avg_proyek'] * 0.20);
            $nilaiAkhirHuruf = getGrade($nilaiAkhirAngka);
            $nilaiAkhirAngka = number_format($nilaiAkhirAngka, 2);
        }
    }

    // Query untuk mengambil semua catatan dari setiap dosen penguji
    $sqlCatatan = "
        SELECT d.nama_dosen, ds.catatan_sidang
        FROM Detail_Sidang ds
        JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen
        WHERE ds.id_sidang = ?
        ORDER BY d.nama_dosen;
    ";
    $paramsCatatan = array($id_sidang);
    $stmtCatatan = sqlsrv_query($conn, $sqlCatatan, $paramsCatatan);

    $catatanArray = [];
    if ($stmtCatatan) {
        while ($rowCatatan = sqlsrv_fetch_array($stmtCatatan, SQLSRV_FETCH_ASSOC)) {
            $catatan = trim($rowCatatan['catatan_sidang']);
            if (!empty($catatan) && $catatan !== '-') {
                $catatanArray[] = "• " . $rowCatatan['nama_dosen'] . ":\n  " . $catatan;
            }
        }
        if (!empty($catatanArray)) {
            $semuaCatatan = implode("\n\n", $catatanArray);
        }
    }
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
          <a href="aDetailSidang.php">
            <span class="NavSide__sidebar-title fw-semibold">Detail Sidang</span>
        </a>
        </li>
        <li class="NavSide__sidebar-item ">
          <b></b>
          <b></b>
          <a href="aEvaluasi.php">
            <span class="NavSide__sidebar-title fw-semibold">Evaluasi</span>
        </a>
        </li>
        <li class="NavSide__sidebar-item NavSide__sidebar-item--active ">
          <b></b>
          <b></b>
          <a href="aNilaiAkhir.php">
            <span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span>
          </a>
        </li>
        <li class="NavSide__sidebar-item">
          <b></b><b></b>
          <a href="aDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Kembali</span></a>
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
      <div class="dashboard-header p-3">
        <div>
          <h2 class="text-heading text-black " style="font-weight: 700;">Detail Evaluasi - Sistem Evaluasi Sidang</h2>
           <?php if ($id_kelompok): ?>
                    <h3 class="fs-5 fw-semibold mb-2 mt-4" style="color: #6c757d;">
                        Kelompok: <?php echo htmlspecialchars($id_kelompok); ?>
                    </h3>
                <?php endif; ?>
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
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error_message) ?></div>
        <?php elseif(empty($current_nim)): ?>
            <div class="alert alert-warning" role="alert">Data mahasiswa tidak ditemukan atau belum dipilih.</div>
        <?php else: ?>
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
                <div class="card-body px-4 py-3 text-center d-flex flex-column justify-content-center">
                  <h3 class="card-title mb-3 text-black" style="padding:10px ;">Nilai Mahasiswa</h3>
                  <input 
                  type="text" 
                  class="form-control nilai-mahasiswa-display" 
                  value="<?= $nilaiAkhirAngka !== '-' ? htmlspecialchars($nilaiAkhirHuruf) : '-' ?>" 
                  readonly/>
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
                  <h3 class="card-title text-black mb-3">Catatan Evaluasi</h3>
                  <div id="catatan" class="form-control flex-grow-1" rows="8" ><?= nl2br(htmlspecialchars($semuaCatatan)) ?></div>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </main>
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