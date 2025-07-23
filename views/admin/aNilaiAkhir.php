<?php
require_once '../../control/admin/aNilaiAkhir_queries.php';
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
  <link rel="stylesheet" href="../../assets/css/button-styles.css" />
  <link rel="stylesheet" href="../../extra/style.css" />
  <link rel="stylesheet" href="../../assets/css/aNilaiakhir.css">

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
    <?php 
                require_once '../../control/function.php'; 
                echo generateBreadcrumb(getPageTitle('aNilaiAkhir'), 'admin', [
                    ['url' => 'aDaftarSidang.php', 'text' => 'Daftar Sidang']
                ]); 
                ?>
      <div class="dashboard-header p-3">
        <div class="col-12">
          <h2 class="text-heading text-black" style="font-weight: 700;">Detail Evaluasi - <?= htmlspecialchars($judul ?? 'Sistem Evaluasi Sidang') ?></h2>
        </div>
        <div class="header-icons d-none d-md-flex">
          <a href="aNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
          <div class="profile-icon"><a href="aProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a></div>
        </div>
      </div>
      <h2 class="fs-5 fw-semibold mb-0" style="margin-left: 15px; margin-top: 20px;">
        Catatan Perbaikan - Kelompok <?php echo htmlspecialchars($nomor_kelompok ?? ''); ?>
      </h2><br>
      <div class="container-fluid">
        <div class="row mb-3">
          <div class="col-12">
            <ul class="nav nav-tabs">
              <?php foreach ($mahasiswa_list as $index => $mhs): ?>
                <li class="nav-item">
                  <a class="nav-link <?php echo ($mhs['nim'] == $current_nim) ? 'active active-student-tab' : ''; ?>"
                     href="aNilaiAkhir.php?id_sidang=<?php echo htmlspecialchars($id_sidang); ?>&nim=<?php echo htmlspecialchars($mhs['nim'] ?? ''); ?>">
                     <?php echo htmlspecialchars($mhs['nama_mhs'] ?? 'Mahasiswa ' . ($index + 1)); ?>
                  </a>
                </li>
              <?php endforeach; ?>
              <?php if (empty($mahasiswa_list)): ?>
                <li class="nav-item">
                  <span class="nav-link disabled">Tidak ada mahasiswa dalam kelompok ini</span>
                </li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
        <br>
      </div>

            <!-- KONTEN UTAMA -->
      <div class="container-fluid">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error_message) ?></div>
        <?php elseif(empty($current_nim)): ?>
            <div class="alert alert-warning" role="alert">Data mahasiswa tidak ditemukan atau belum dipilih.</div>
        <?php else: ?>
          <div class="row align-items-stretch mb-2">
            <!-- Kartu Data Mahasiswa -->
           <div class="col-lg-6 mb-4 d-flex">
              <div class="card flex-fill" id="carddataMahasiswa">
                  <div class="card-body px-4 py-4">
                      <h3 class="card-title text-black mb-4 text-center py-2">Data Mahasiswa</h3>
                      <div class="row px-3 py-3"> <div class="col-sm-6 text-black">
                              <div class="info-group mb-5"> <div class="label-row d-flex align-items-center gap-2 mb-1">
                                      <i class="fa-solid fa-id-card"></i><span class="fw-bold">NIM</span>
                                  </div>
                                  <div class="value-row text-secondary fw-bold"><?= htmlspecialchars($dataMahasiswa['nim']) ?></div>
                              </div>
                              <div class="info-group mb-5"> <div class="label-row d-flex align-items-center gap-2 mb-1">
                                      <i class="fa-solid fa-user"></i><span class="fw-bold">Nama</span>
                                  </div>
                                  <div class="value-row text-secondary fw-bold"><?= htmlspecialchars($dataMahasiswa['nama_mhs']) ?></div>
                              </div>
                          </div>
                          <div class="col-sm-6 text-black">
                              <div class="info-group mb-5"> <div class="label-row d-flex align-items-center gap-2 mb-1">
                                      <i class="fa-solid fa-book"></i><span class="fw-bold">Mata Kuliah</span>
                                  </div>
                                  <div class="value-row text-secondary fw-bold"><?= htmlspecialchars($dataMahasiswa['nama_matkul']) ?></div>
                              </div>
                              <div class="info-group mb-5"> <div class="label-row d-flex align-items-center gap-2 mb-1">
                                      <i class="fa-solid fa-user-tie"></i><span class="fw-bold">Dosen Pembimbing</span>
                                  </div>
                                  <div class="value-row text-secondary fw-bold"><?= htmlspecialchars($dataMahasiswa['nama_pembimbing']) ?></div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

            <!-- Kartu Nilai Mahasiswa -->
            <div class="col-lg-6 mb-4 d-flex">
              <div class="card flex-fill" id="cardNilai">
                <div class="card-body px-4 py-4 text-center d-flex flex-column justify-content-between">
                  <h3 class="card-title text-black mb-4 text-center py-2">Nilai Mahasiswa</h3>
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
          <!-- <div class="row">
            <div class="col-12">
              <div class="card h-100" id="cardcatatan">
                <div class="card-body px-4 py-4 d-flex flex-column">
                  <h3 class="card-title text-black mb-3">Catatan Evaluasi</h3>
                  <div id="catatan" class="form-control flex-grow-1" rows="8" ><?= nl2br(htmlspecialchars($semuaCatatan)) ?></div>
                </div>
              </div>
            </div>
          </div> -->
        <?php endif; ?>
      </div>
    </main>
  </div>


<script src="../../assets/js/aNilaiakhir.js"></script>
</body>
</html>