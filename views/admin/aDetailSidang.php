<?php
require_once '../../control/admin/aDetailSidang_queries.php';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Zia Zahran Hadi-AliansiSidang_Kelompok5">
    <title>DetailSidang-Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/aDetailSidang.css">



</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="aDetailSidang.php"><span class="NavSide__sidebar-title fw-semibold">Detail Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aEvaluasi.php"><span class="NavSide__sidebar-title fw-semibold">Evaluasi</span></a>
                </li>

                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aNilaiAkhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold"> Kembali</span></a>
                </li>
            </ul>
        </div>

        <div style="flex-grow: 1; display: flex; flex-direction: column; position: relative;">
            <div class="NavSide__topbar">
                <div class="NavSide__toggle">
                    <i class="bi bi-list open"></i>
                    <i class="bi bi-x-lg close"></i>
                </div>
            </div>

            <main class="NavSide__main-content">
                <h2>Detail Sidang -
                    <?php
                    // Cek jika jenis sidang adalah TA atau Semester
                    if ($data_sidang['jenis_sidang'] == 'Tugas Akhir' || $data_sidang['jenis_sidang'] == 'Semester') {
                        echo htmlspecialchars($data_sidang['judul']);
                    }
                    ?></h2>
                <p class="page-nama">Kelompok <?php echo htmlspecialchars($data_sidang['nomor_kelompok']); ?></p>

                <div class="status-badge">Status Pengajuan : <?php echo htmlspecialchars($data_sidang['status_sidang_text']); ?></div>
                <div class="info-card">
                    <div class="section">
                        <!-- Tampilan akan dirender berdasarkan kondisi IF -->
                        <?php if ($data_sidang['jenis_sidang'] == 'Tugas Akhir'): ?>
                            <p><i class="fa-solid fa-book"></i><strong>Judul Sidang</strong><br><?php echo !empty($data_sidang['judul']) ? htmlspecialchars($data_sidang['judul']) : 'Belum ada judul'; ?></p>
                            <p><i class="fa-solid fa-book"></i><strong>Mata Kuliah</strong><br><?php echo !empty($data_matkul['nama_matkul']) ? htmlspecialchars($data_matkul['nama_matkul']) : 'Tugas Akhir'; ?></p>
                            <p><i class="fa-solid fa-user"></i><strong>Dosen Pembimbing</strong><br><?php echo !empty($dosen_pembimbing['nama_dosen']) ? htmlspecialchars($dosen_pembimbing['nama_dosen']) : 'Belum ditentukan'; ?></p>
                            <p><i class="fa-solid fa-users"></i><strong>Dosen Penguji</strong><br>
                                <?php
                                if (!empty($dosen_penguji)) {
                                    echo implode('<br>', array_map('htmlspecialchars', $dosen_penguji));
                                } else {
                                    echo 'Belum ditentukan';
                                }
                                ?></p>
                        <?php elseif ($data_sidang['jenis_sidang'] == 'Semester'): ?>
                            <p><i class="fa-solid fa-book"></i><strong>Judul Sidang</strong><br><?php echo !empty($data_sidang['judul']) ? htmlspecialchars($data_sidang['judul']) : 'Belum ada judul'; ?></p>
                            <p><i class="fa-solid fa-book"></i><strong>Mata Kuliah</strong><br><?php echo !empty($data_matkul['nama_matkul']) ? htmlspecialchars($data_matkul['nama_matkul']) : 'N/A'; ?></p>
                            <p><i class="fa-solid fa-users"></i><strong>Dosen Pengampu</strong><br>
                                <?php
                                if (!empty($dosen_pengampu)) {
                                    echo implode('<br>', array_map('htmlspecialchars', $dosen_pengampu));
                                } else {
                                    echo 'Belum ditentukan';
                                }
                                ?></p>
                        <?php else: ?>
                            <!-- Ini akan muncul jika jenis_sidang bukan 0 atau 1 -->
                            <p>Jenis sidang tidak dikenali.</p>
                        <?php endif; ?>
                    </div>
                    <div class="section">
                        <p><i class="fa-solid fa-door-open"></i><strong>Ruangan</strong><br><?php echo !empty($data_jadwal['ruang_sidang']) ? htmlspecialchars($data_jadwal['ruang_sidang']) : 'Belum Dijadwalkan'; ?></p>

                        <p><i class="fa-solid fa-calendar-days"></i><strong>Tanggal</strong><br>
                            <?php
                            if (!empty($data_jadwal['tanggal_sidang']) && $data_jadwal['tanggal_sidang'] instanceof DateTime) {
                                setlocale(LC_TIME, 'id_ID.utf8');
                                echo $data_jadwal['tanggal_sidang']->format('l, d F Y');
                            } else {
                                echo 'Belum Dijadwalkan';
                            }
                            ?>
                        </p>

                        <p><i class="fa-solid fa-clock"></i><strong>Jam</strong><br>
                            <?php
                            if (!empty($data_jadwal['jam_sidang']) && $data_jadwal['jam_sidang'] instanceof DateTime) {
                                echo $data_jadwal['jam_sidang']->format('H.i');
                                if (!empty($data_jadwal['jam_selesai']) && $data_jadwal['jam_selesai'] instanceof DateTime) {
                                    echo ' - ' . $data_jadwal['jam_selesai']->format('H.i');
                                }
                            } else {
                                echo 'Belum Dijadwalkan';
                            }
                            ?>
                        </p>
                    </div>
                </div>

                <h5 class="mt-4">Aksi</h5>
                <button class="btn-ubah" onclick="openModal()">Ubah Jadwal Sidang</button>
                <button class="btn-hapus" onclick="confirmDelete(<?php echo htmlspecialchars($id_sidang); ?>)">Hapus Sidang</button>
                <br><br>



                <div class="modal fade" id="penjadwalanSidangModal" aria-labelledby="penjadwalanSidangModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content modal-content-custom-form">
                            <div class="modal-body">
                                <h2>Penjadwalan Sidang</h2>
                                <div class="form-container">
                                    <form id="formDalamModal" novalidate>
                                        <input type="hidden" name="id_sidang" value="<?php echo htmlspecialchars($id_sidang); ?>">

                                        
                                        <div class="form-group">
                                            <label>ID Kelompok</label>
                                            <p><?php echo htmlspecialchars($data_sidang['id_kelompok']); ?></p>
                                        </div>
                                        <div class="form-group">
                                            <label>Prodi</label>
                                            <p><?php echo htmlspecialchars($nama_prodi); ?></p>
                                        </div>
                                        <div class="form-group">
                                           <label><?php echo ($data_sidang['jenis_sidang'] == 'Tugas Akhir') ? 'Judul Sidang' : 'Mata Kuliah'; ?></label>
                                            <p><?php echo htmlspecialchars(($data_sidang['jenis_sidang'] == 'Tugas Akhir') ? $data_sidang['judul'] : ($data_matkul['nama_matkul'] ?? 'N/A')); ?></p>
                                        </div>

                                       
                                        <?php if ($data_sidang['jenis_sidang'] == 'Tugas Akhir'): ?>
                                            <div class="form-group">
                                                <label>Pembimbing</label>
                                                <p><?php echo htmlspecialchars($dosen_pembimbing['nama_dosen'] ?? 'Belum ada'); ?></p>
                                            </div>
                                            <hr>
                                            <div id="penguji-wrapper">
                                                <?php
                                                $penguji_list_dengan_bobot = !empty($dosen_penguji_data) ? $dosen_penguji_data : [['nama' => '', 'bobot' => '']];

                                                foreach ($penguji_list_dengan_bobot as $index => $penguji):
                                                ?>
                                                    <div class="form-group" id="penguji-form-<?php echo $index + 1; ?>">
                                                        <label for="modal_penguji<?php echo $index + 1; ?>">Penguji <?php echo $index + 1; ?></label>
                                                        <div class="input-with-buttons">
                                                            <div class="autocomplete-container">
                                                                <input type="text"
                                                                    id="modal_penguji<?php echo $index + 1; ?>"
                                                                    name="penguji_nama[]"
                                                                    placeholder="Ketik nama dosen penguji"
                                                                    oninput="searchDosen(this, <?php echo $index + 1; ?>)"
                                                                    value="<?php echo htmlspecialchars($penguji['nama']); ?>"
                                                                    autocomplete="off">
                                                                <div class="autocomplete-dropdown" id="autocomplete_penguji_<?php echo $index + 1; ?>" style="display: none;"></div>
                                                            </div>
                                                            <div class="input-with-percent">
                                                                <input type="number" name="penguji_bobot[]" class="form-control-bobot" min="0" placeholder="Bobot" value="<?php echo htmlspecialchars($penguji['bobot']); ?>">
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
                                        <?php endif; ?>

                                    
                                        <?php if ($data_sidang['jenis_sidang'] == 'Semester'): ?>
                                            <hr>
                                            <div id="pengampu-wrapper">
                                                <?php if (!empty($dosen_pengampu)): ?>
                                                    <?php foreach ($dosen_pengampu as $index => $nama_pengampu): ?>
                                                        <div class="form-group">
                                                            <label>Pengampu <?php echo $index + 1; ?></label>
                                                            <p><?php echo htmlspecialchars($nama_pengampu); ?></p>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <p>Tidak ada dosen pengampu terdaftar.</p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                      
                                        <hr>
                                        <div class="form-group">
                                            <label for="modal_ruangan">Ruangan</label>
                                            <input type="text" id="modal_ruangan" name="ruangan" value="<?php echo htmlspecialchars($data_jadwal['ruang_sidang'] ?? ''); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="modal_tanggal">Tanggal</label>
                                            <input type="date" id="modal_tanggal" name="tanggal" value="<?php echo !empty($data_jadwal['tanggal_sidang']) ? $data_jadwal['tanggal_sidang']->format('Y-m-d') : ''; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="modal_jam_awal">Jam</label>
                                            <div class="time-input-range">
                                                <input type="time" id="modal_jam_awal" name="jam_awal" value="<?php echo !empty($data_jadwal['jam_sidang']) ? $data_jadwal['jam_sidang']->format('H:i') : ''; ?>">
                                                <span class="time-separator">-</span>
                                                <input type="time" id="modal_jam_akhir" name="jam_akhir" value="<?php echo !empty($data_jadwal['jam_selesai']) ? $data_jadwal['jam_selesai']->format('H:i') : ''; ?>">
                                            </div>
                                        </div>

                                        <div id="form-error" style="color: red; margin-bottom: 10px;"></div>
                                        <div class="form-actions">
                                            <button type="button" class="btn btn-batal" data-bs-dismiss="modal">Batalkan</button>
                                            <button type="submit" class="btn btn-submit">Ubah Penjadwalan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </main>
            
        </div> 
    </div> 



    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

   <script type="text/javascript">
       
        const dosenData = <?php echo $dosen_list_json; ?>;
        const isSidangTA = <?php echo ($data_sidang['jenis_sidang'] == 'Tugas Akhir') ? 'true' : 'false'; ?>;
    </script>
    
   
    <script src="../../assets/js/aDetailSidang.js"></script>
</body>

</html>