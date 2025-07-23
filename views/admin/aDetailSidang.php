<?php
require_once '../../control/admin/aDetailSidang_queries.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Sidang - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../assets/css/aDetailSidang.css">
    <link rel="stylesheet" href="../../assets/css/breadcrumb.css">
</head>
<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <!-- Sidebar Anda -->
            <div class="NavSide__sidebar-brand"><img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo"></div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="#"><span class="NavSide__sidebar-title fw-semibold">Detail Sidang</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="aEvaluasi.php"><span class="NavSide__sidebar-title fw-semibold">Evaluasi</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="aNilaiAkhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="aDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold"> Kembali</span></a></li>
            </ul>
        </div>

       
            <div class="NavSide__topbar">
               <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <div id="mobile-icons-container"></div>
            </div>

            <main class="NavSide__main-content">
               <?php 
                require_once '../../control/function.php'; 
                echo generateBreadcrumb(getPageTitle('aDetailSidang'), 'admin', [
                    ['url' => 'aDaftarSidang.php', 'text' => 'Daftar Sidang']
                ]); 
                ?>
               <div class="main-header">
                <div class="header-left-panel">
                    <h2>Detail Sidang - <?php echo htmlspecialchars($data_sidang['judul']); ?></h2>
                    <p class="page-nama">Kelompok <?php echo htmlspecialchars($data_sidang['id_kelompok']); ?></p>
                </div>
                <div class="header-right-panel">
                    <div id="desktop-icons-container">
                        <!--<div class="header-icons">
                            <a href="aNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
                            <div class="profile-icon">
                                <a href="aProfil.php" title="Profil"><i class="bi bi-person-fill"></i></a>
                            </div>
                        </div>-->
                    </div>
                </div>
            </div>

            <div class="status-badge">Status Sidang : <?php echo htmlspecialchars($data_sidang['status_sidang_text']); ?></div>
                <div class="info-card">
                    <div class="section">
                        <?php if ($data_sidang['jenis_sidang'] == 'Tugas Akhir'): ?>
                            <p><i class="fa-solid fa-book"></i><strong>Judul Sidang</strong><br><?php echo htmlspecialchars($data_sidang['judul']); ?></p>
                            <p><i class="fa-solid fa-book"></i><strong>Mata Kuliah</strong><br><?php echo htmlspecialchars($data_matkul['nama_matkul'] ?? 'Tugas Akhir'); ?></p>
                            <p><i class="fa-solid fa-user"></i><strong>Dosen Pembimbing</strong><br>
                                <?php echo !empty($dosen_pembimbing) ? implode('<br>', array_map(fn($p) => htmlspecialchars($p['nama_dosen']), $dosen_pembimbing)) : 'Belum ditentukan'; ?>
                            </p>
                            <p><i class="fa-solid fa-users"></i><strong>Dosen Penguji</strong><br>
                                <?php echo !empty($dosen_penguji_data) ? implode('<br>', array_map(fn($p) => htmlspecialchars($p['nama_dosen']), $dosen_penguji_data)) : 'Belum ditentukan'; ?>
                            </p>
                        <?php elseif ($data_sidang['jenis_sidang'] == 'Semester'): ?>
                            <p><i class="fa-solid fa-book"></i><strong>Judul Sidang</strong><br><?php echo htmlspecialchars($data_sidang['judul']); ?></p>
                            <p><i class="fa-solid fa-book"></i><strong>Mata Kuliah</strong><br><?php echo htmlspecialchars($data_matkul['nama_matkul'] ?? 'N/A'); ?></p>
                            <p><i class="fa-solid fa-users"></i><strong>Dosen Pengampu</strong><br>
                                <?php echo !empty($dosen_pengampu_data) ? implode('<br>', array_map(fn($p) => htmlspecialchars($p['nama_dosen']), $dosen_pengampu_data)) : 'Belum ditentukan'; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="section">
                        <p><i class="fa-solid fa-door-open"></i><strong>Ruangan</strong><br><?php echo htmlspecialchars($data_jadwal['ruang_sidang'] ?? 'Belum Dijadwalkan'); ?></p>
                        <p><i class="fa-solid fa-calendar-days"></i><strong>Tanggal</strong><br>
                             <?php 
                            echo formatTanggalIndonesiaManual($data_jadwal['tanggal_sidang'] ?? null); 
                            ?>
                        </p>
                        <p><i class="fa-solid fa-clock"></i><strong>Jam</strong><br>
                            <?php echo !empty($data_jadwal['jam_sidang']) ? $data_jadwal['jam_sidang']->format('H.i') . ' - ' . $data_jadwal['jam_selesai']->format('H.i') : 'Belum Dijadwalkan'; ?>
                        </p>
                    </div>
                </div>

                 <?php
                if ($data_sidang['status_sidang'] != '0x00') :
                ?>
                <h5 class="mt-4">Aksi</h5>
                <button class="btn-ubah" onclick="openModal()">Ubah Jadwal Sidang</button>
                <button class="btn-hapus" onclick="confirmDelete(<?php echo $id_sidang; ?>)">Batalkan Sidang</button>
            <?php 
            endif; 
            ?>
                <!-- MODAL UBAH JADWAL -->
      <div class="modal fade" id="penjadwalanSidangModal" tabindex="-1" aria-labelledby="penjadwalanSidangModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-content-custom-form">
            <div class="modal-body">
                <h2>Ubah Penjadwalan</h2>
                <form id="formDalamModal" novalidate>
                    <input type="hidden" name="id_sidang" value="<?php echo $id_sidang; ?>">
                    <div class="form-container">
                        <!-- Info statis -->
                        <div class="form-group"><label>Kelompok</label><p><?php echo htmlspecialchars($data_sidang['nomor_kelompok']); ?></p></div>
                        <div class="form-group"><label>Prodi</label><p><?php echo htmlspecialchars($nama_prodi); ?></p></div>
                        <div class="form-group">
                            <label><?php echo ($data_sidang['jenis_sidang'] == 'Tugas Akhir') ? 'Judul Sidang' : 'Mata Kuliah'; ?></label>
                            <p><?php echo htmlspecialchars(($data_sidang['jenis_sidang'] == 'Tugas Akhir') ? $data_sidang['judul'] : ($data_matkul['nama_matkul'] ?? 'N/A')); ?></p>
                        </div>
                        <hr>

                        <?php if ($data_sidang['jenis_sidang'] == 'Tugas Akhir'): ?>
                            <!-- Loop untuk Pembimbing -->
                            <?php foreach ($dosen_pembimbing as $index => $pembimbing): ?>
                                <div class="form-group">
                                    <label>Pembimbing <?php echo $index + 1; ?></label>
                                    <div class="input-with-buttons">
                                        <input type="text" value="<?php echo htmlspecialchars($pembimbing['nama_dosen']); ?>" readonly>
                                        <div class="input-with-percent">
                                            <input type="number" name="pembimbing_bobot[]" class="form-control-bobot" value="<?php echo htmlspecialchars($pembimbing['bobot'] ?? '0'); ?>" oninput="cleanNumberInput(this); validateTotalWeightRealtime();">
                                            <span class="percent-sign">%</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="pembimbing_nama[]" value="<?php echo htmlspecialchars($pembimbing['nama_dosen']); ?>">
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            
                            <!-- Wrapper untuk Penguji -->
                            <div id="penguji-wrapper">
                                <?php $penguji_list = !empty($dosen_penguji_data) ? $dosen_penguji_data : [['nama_dosen' => '', 'bobot' => '']]; ?>
                                <?php foreach ($penguji_list as $index => $penguji): ?>
                                    <div class="form-group" id="penguji-form-<?php echo $index + 1; ?>">
                                        <label for="modal_penguji<?php echo $index + 1; ?>">Penguji <?php echo $index + 1; ?></label>
                                        <div class="input-with-buttons">
                                            <div class="autocomplete-container">
                                                <input type="text" id="modal_penguji<?php echo $index + 1; ?>" name="penguji_nama[]" placeholder="Ketik nama dosen" value="<?php echo htmlspecialchars($penguji['nama_dosen'] ?? ''); ?>" oninput="searchDosen(this, <?php echo $index + 1; ?>)" autocomplete="off">
                                                <div class="autocomplete-dropdown" id="autocomplete_penguji_<?php echo $index + 1; ?>"></div>
                                            </div>
                                            <div class="input-with-percent">
                                                <input type="number" name="penguji_bobot[]" class="form-control-bobot" value="<?php echo htmlspecialchars($penguji['bobot'] ?? '0'); ?>"  oninput="cleanNumberInput(this); validateTotalWeightRealtime();">
                                                <span class="percent-sign">%</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-toggle-buttons">
                                <button type="button" class="btn-tambah-penguji" onclick="addPenguji()"><i class="fa-solid fa-plus"></i> Tambah Penguji</button>
                                <button type="button" class="btn-hapus-penguji" onclick="removePenguji()"><i class="fa-solid fa-minus"></i> Hapus Penguji</button>
                            </div>
                        
                        <?php elseif ($data_sidang['jenis_sidang'] == 'Semester'): ?>
                            <!-- Loop untuk Pengampu -->
                            <?php foreach ($dosen_pengampu_data as $index => $pengampu): ?>
                                <div class="form-group">
                                    <label>Pengampu <?php echo $index + 1; ?></label>
                                    <div class="input-with-buttons">
                                        <input type="text" value="<?php echo htmlspecialchars($pengampu['nama_dosen']); ?>" readonly>
                                        <div class="input-with-percent">
                                            <input type="number" name="pengampu_bobot[]" class="form-control-bobot" value="<?php echo htmlspecialchars($pengampu['bobot'] ?? '0'); ?>"  oninput="cleanNumberInput(this); validateTotalWeightRealtime();">
                                            <span class="percent-sign">%</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="pengampu_nama[]" value="<?php echo htmlspecialchars($pengampu['nama_dosen']); ?>">
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <hr>
                        <!-- Field Ruangan, Tanggal, Jam -->
                        <div class="form-group"><label for="modal_ruangan">Ruangan</label><input type="text" id="modal_ruangan" name="ruangan" value="<?php echo htmlspecialchars($data_jadwal['ruang_sidang'] ?? ''); ?>"></div>
                        <div class="form-group"><label for="modal_tanggal">Tanggal</label><input type="date" id="modal_tanggal" name="tanggal" value="<?php echo !empty($data_jadwal['tanggal_sidang']) ? $data_jadwal['tanggal_sidang']->format('Y-m-d') : ''; ?>"></div>
                        <div class="form-group">
                            <label for="modal_jam_awal">Jam</label>
                            <div class="time-input-range">
                                <input type="time" id="modal_jam_awal" name="jam_awal" value="<?php echo !empty($data_jadwal['jam_sidang']) ? $data_jadwal['jam_sidang']->format('H:i') : ''; ?>">
                                <span>-</span>
                                <input type="time" id="modal_jam_akhir" name="jam_akhir" value="<?php echo !empty($data_jadwal['jam_selesai']) ? $data_jadwal['jam_selesai']->format('H:i') : ''; ?>">
                            </div>
                        </div>
                        <div class="realtime-validation-message" id="realtime-validation-detail"></div>
                        <div id="form-error" class="form-error-message"></div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-batal" data-bs-dismiss="modal">Batalkan</button>
                            <button type="submit" class="btn btn-submit">Ubah Penjadwalan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const dosenData = <?php echo $dosen_list_json; ?>;
        const isSidangTA = <?php echo ($data_sidang['jenis_sidang'] == 'Tugas Akhir') ? 'true' : 'false'; ?>;
    </script>
    <script src="../../assets/js/aDetailSidang.js"></script>
</body>
</html>