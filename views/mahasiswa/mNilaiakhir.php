<?php
require_once '../../control/mahasiswa/mNilaiAkhir_queries.php';
?>
<script>
    // Debug: kirim data hasil query ke dev console browser
    const debugDataNilaiAkhir = <?php
        echo json_encode([
            'id_sidang' => $id_sidang ?? null,
            'nim' => $nim ?? null,
            'dataSidang' => $dataSidang ?? null,
            'nilaiAngka' => $nilaiAngka ?? null,
            'nilaiHuruf' => $nilaiHuruf ?? null,
        ]);
    ?>;
    console.log('DEBUG DATA NILAI AKHIR:', debugDataNilaiAkhir);
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet" />


    <link rel="stylesheet" href="../../assets/css/style.css" /> 
    
    <link rel="stylesheet" href="../../assets/css/mNilaiakhir.css">

    <link rel="stylesheet" href="../../css/button-styles.css" />

    <link rel="stylesheet" href="../../extra/style.css" />
    <link rel="stylesheet" href="../../assets/css/breadcrumb.css" />
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <title>Mahasiswa - Nilai Akhir</title>

    
</head>
<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand img "><img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo"></div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="mdetailSidang.php"><span class="fw-semibold">Detail Pengajuan</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="mPerbaikan.php"><span class="fw-semibold">Perbaikan</span></a></li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="mNilaiakhir.php"><span class="fw-semibold">Nilai Akhir</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="mSidang.php"><span class="fw-semibold">Kembali</span></a></li>
            </ul>
        </div>
        <div class="NavSide__topbar">
             <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
            <div class="header-icons"><i class="bi bi-bell-fill"></i><div class="profile-icon"><i class="bi bi-person-fill fs-5"></i></div></div>
        </div>
           <main class="NavSide__main-content">
            <?php 
            // Include the function file
            require_once '../../control/function.php'; 
            // Generate breadcrumb
            echo generateBreadcrumb(getPageTitle('mNilaiakhir'), 'mahasiswa', [
                ['url' => 'mSidang.php', 'text' => 'Sidang']
            ]); 
            ?>
            <div class="container-fluid">
                <div class="row mb-4 title-container"><div class="col-12"><h2 class="main-title">Detail Evaluasi - <?= htmlspecialchars($judul ?? 'Sistem Evaluasi Sidang') ?></h2></div></div>
                
                <div class="row mt-3 g-3">
                    <div class="col-lg-6 d-flex">
                        
                    <div class="card flex-fill" id="carddataMahasiswa">
                        <div class="card-body card-soft p-4">
                        <h3 class="card-title text-dark mb-4 text-center py-3">Data Mahasiswa</h3>
                        <div class="row">
                            <div class="col-sm-6 text-black">
                                <div class="info-group mb-5">
                                    <div class="label-row d-flex align-items-center gap-2 mb-1"><i class="fa-solid fa-id-card"></i><span class="fw-bold">NIM</span></div>
                                    <div class="value-row text-secondary fw-bold"><?= htmlspecialchars($nim) ?></div>
                                </div>
                                <div class="info-group mb-5">
                                    <div class="label-row d-flex align-items-center gap-2 mb-1"><i class="fa-solid fa-user"></i><span class="fw-bold">Nama</span></div>
                                    <div class="value-row text-secondary fw-bold"><?= htmlspecialchars($dataSidang['nama']) ?></div>
                                </div>
                            </div>
                            <div class="col-sm-6 text-black">
                                <div class="info-group mb-5">
                                    <div class="label-row d-flex align-items-center gap-2 mb-1"><i class="fa-solid fa-book"></i><span class="fw-bold">Judul Proyek</span></div>
                                    <div class="value-row text-secondary fw-bold"><?= htmlspecialchars($dataSidang['judul']) ?></div>
                                </div>
                                <div class="info-group mb-5">
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
                                <h3 class="card-title text-dark mb-4 text-center py-3">Nilai Mahasiswa</h3>
                                <div class="d-flex flex-column align-items-center justify-content-center flex-grow-2">
                                <input type="text" class="form-control text-dark nilai-mahasiswa-display-mnilai" id="nilaiMahasiswa" value="<?= htmlspecialchars($nilaiHuruf) ?>" readonly />

                                <p class="mt-3 fs-6 text-secondary fw-bold text-center">
                                    <?php if ($nilaiAngka !== null) { // Pastikan nilai angka ada ?>
                                        (Skor: <?= number_format($nilaiAngka, 2) ?>)
                                    <?php } else { ?>
                                        Belum dinilai
                                    <?php } ?>
                                </p>
                            </div>
                    </div>
                </div>

<!--                 
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card h-100 mb-4" id="cardcatatan"> 
                            <div class="card-body px-4 py-4 d-flex flex-column">
                                <h3 class="card-title text-black mb-3">Catatan Evaluasi</h3>
                                <div id="catatan" class="form-control flex-grow-1" rows="8"><?= nl2br(htmlspecialchars($semuaCatatan)) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
     -->

<script src="../../assets/js/mNilaiakhir.js"></script>
</body>
</html>