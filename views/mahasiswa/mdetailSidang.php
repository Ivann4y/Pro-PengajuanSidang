<?php
include '../../control/dosen/mdetailSidang_logic.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/mdetailsidang.css">
</head>
<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="mdetailSidang.php">
                        <span class="NavSide__sidebar-title fw-semibold">Detail Pengajuan</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mPerbaikan.php?id_sidang=<?= htmlspecialchars($id_sidang) ?>">
                        <span class="NavSide__sidebar-title fw-semibold">Perbaikan</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mNilaiakhir.php">
                        <span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mSidang.php">
                        <span class="NavSide__sidebar-title fw-semibold"> Kembali</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="NavSide__toggle">
            <i class="bi bi-list open"></i>
            <i class="bi bi-x-lg close"></i>
        </div>

        <div id="page-content-wrapper">
            <div class="NavSide__topbar"></div>

            <main class="NavSide__main-content">
                <h2>Detail Sidang -
                    <?php
                        if ((int)$data_sidang['jenis_sidang'] === 0) {
                            echo !empty($data_sidang['judul']) ? htmlspecialchars($data_sidang['judul']) : 'Tugas Akhir';
                        } elseif ((int)$data_sidang['jenis_sidang'] === 1 && !empty($data_matkul)) {
                            echo htmlspecialchars($data_matkul['nama_matkul']);
                        }
                    ?>
                </h2>

                <h2 class="fs-5 fw-semibold mb-0">
                    Catatan Perbaikan - Kelompok <?php echo htmlspecialchars($id_kelompok); ?>
                </h2><br>

                <div class="status-badge <?= $status_class ?>" id="statusBadge"><?= $status_text ?></div>

                <div class="info-card">
                    <div class="section">
                        <?php if ((int)$data_sidang['jenis_sidang'] === 1): ?>
                            <div class="info-group">
                                <div class="label-row">
                                    <i class="fa-solid fa-book"></i>
                                    <span class="fw-bold">Judul Mata Kuliah</span>
                                </div>
                                <div class="value-row">
                                    <?= htmlspecialchars($data_matkul['nama_matkul'] ?? 'N/A') ?>
                                </div>
                            </div>
                            <div class="spacer"></div>
                            <div class="info-group">
                                <div class="label-row">
                                    <i class="fa-solid fa-user-group"></i>
                                    <span class="fw-bold">Dosen Pengampu</span>
                                </div>
                                <div class="value-row">
                                    <?= !empty($dosen_pengampu) ? implode('<br>', array_map('htmlspecialchars', $dosen_pengampu)) : '-' ?>
                                </div>
                            </div>
                        <?php elseif ((int)$data_sidang['jenis_sidang'] === 0): ?>
                            <div class="info-group">
                                <div class="label-row">
                                    <i class="fa-solid fa-file-invoice"></i>
                                    <span class="fw-bold">Judul Sidang</span>
                                </div>
                                <div class="value-row">
                                    <?= htmlspecialchars($data_sidang['judul']) ?>
                                </div>
                            </div>
                            <div class="info-group">
                                <div class="label-row">
                                    <i class="fa-solid fa-user-tie"></i>
                                    <span class="fw-bold">Dosen Pembimbing</span>
                                </div>
                                <div class="value-row">
                                    <?= htmlspecialchars($dosen_pembimbing) ?>
                                </div>
                            </div>
                            <div class="info-group">
                                <div class="label-row">
                                    <i class="fa-solid fa-user-group"></i>
                                    <span class="fw-bold">Dosen Penguji</span>
                                </div>
                                <div class="value-row">
                                    <?= !empty($dosen_penguji) ? implode('<br>', array_map('htmlspecialchars', $dosen_penguji)) : '-' ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <p>Jenis sidang tidak dikenali.</p>
                        <?php endif; ?>
                    </div>
                    <div class="section">
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-door-open"></i>
                                <span class="fw-bold">Ruangan</span>
                            </div>
                            <div class="value-row">
                                <?= htmlspecialchars($data_jadwal['ruang_sidang'] ?? 'Belum Dijadwalkan') ?>
                            </div>
                        </div>
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span class="fw-bold">Tanggal</span>
                            </div>
                            <div class="value-row">
                                <?= htmlspecialchars($tanggal_sidang_formatted) ?>
                            </div>
                        </div>
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-clock"></i>
                                <span class="fw-bold">Jam</span>
                            </div>
                            <div class="value-row">
                                <?= htmlspecialchars($jam_sidang_formatted) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <h5>Dokumen Sidang</h5>
                <div class="file-buttons-container d-flex flex-wrap">
                    <?php if (!empty($dok_laporan)): ?>
                        <a href="download_document.php?id_sidang=<?= htmlspecialchars($id_sidang) ?>" class="file-button">
                            <i class="fa-solid fa-file-zipper"></i>
                            Dokumen_Laporan_Kelompok_<?= htmlspecialchars($id_kelompok) ?>.zip </a>
                    <?php else: ?>
                        <p>Dokumen tidak tersedia.</p>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script type="text/javascript">
      let menuToggle = document.querySelector(".NavSide__toggle");
      let sidebar = document.getElementById("main-sidebar");

      if (menuToggle && sidebar) {
        menuToggle.onclick = function () {
          menuToggle.classList.toggle("NavSide__toggle--active");
          sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
      }

      let menuItems = document.querySelectorAll(".NavSide__sidebar-item");
      if (menuItems.length > 0) {
        menuItems.forEach(item => {
          item.onclick = function (event) {
            menuItems.forEach(innerItem => {
              innerItem.classList.remove("NavSide__sidebar-item--active");
            });
            this.classList.add("NavSide__sidebar-item--active");
          };
        });
      }
    </script>
</body>
</html>