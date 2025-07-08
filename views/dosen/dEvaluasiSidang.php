<?php
require_once '../../control/dosen/dEvaluasiSidang_queries.php';
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Sidang</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Link CSS Font Awesome (Sudah Benar) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Link Script Font Awesome (INI YANG PERLU DITAMBAHKAN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script> 
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../assets/css/dEvaluasiSidang.css">
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
                    <a href="dEvaluasiSidang.php">
                        <span class="fw-semibold NavSide__sidebar-title">Evaluasi</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDokumenRevisi.php?id=<?= htmlspecialchars($id_sidang) ?>">
                        <span class="fw-semibold NavSide__sidebar-title">Dokumen</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dNilaiAkhir.php?id_sidang=<?= htmlspecialchars($id_sidang) ?>">
                        <span class="fw-semibold NavSide__sidebar-title">Nilai Akhir</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold"> Kembali</span></a>
                </li>
            </ul>
        </div>
        <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
        <div id="page-content-wrapper">
            <div class="NavSide__topbar"></div>
            <main class="NavSide__main-content">
                <h2 class="text-heading text-black" style="font-weight: 700;">Detail Evaluasi - <?= htmlspecialchars($judul) ?></h2>
                <h2 class="fs-5 fw-semibold mb-0" style="margin-left: 15px; margin-top: 20px;   color: #464869;">
              Catatan Perbaikan - Kelompok <?php echo htmlspecialchars($nomor_kelompok ?? ''); ?>
          </h2><br>
                <div class="container-fluid">
                    <!-- [BARU] TAB NAVIGASI MAHASISWA -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <ul class="nav nav-tabs">
                                <?php if (!empty($mahasiswa)): ?>
                                    <?php foreach ($mahasiswa as $mhs): ?>
                                        <li class="nav-item">
                                            <a class="nav-link <?= ($mhs['nim'] == $current_nim) ? 'active active-student-tab' : '' ?>"
                                                href="dEvaluasiSidang.php?nim=<?= htmlspecialchars($mhs['nim']) ?>">
                                                <?= htmlspecialchars($mhs['nama_mhs']) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="nav-item">
                                        <span class="nav-link disabled">Tidak ada mahasiswa dalam kelompok ini.</span>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <br>

                    <form id="evaluasiForm" method="POST" action="dEvaluasiSidang.php">
                        <!-- Hidden input untuk mengirim NIM mahasiswa yang sedang dievaluasi -->
                        <input type="hidden" name="nim" value="<?= htmlspecialchars($current_nim) ?>">

                        <div class="info-card">
                            <div class="section">
                                <div class="info-group">
                                    <div class="label-row"> <i class="fa-solid fa-id-card"></i> <span class="fw-bold"> NIM</span></div>
                                    <div class="value-row"><?php echo htmlspecialchars($current_nim ?: '-'); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="label-row"> <i class="fa-solid fa-file-invoice"></i> <span class="fw-bold"> Judul Sidang</span></div>
                                    <div class="value-row"><?php echo htmlspecialchars($judul); ?></div>
                                </div>
<!-- Bagian Dosen Pembimbing/Pengampu (TETAP) -->
<div class="info-group">
    <div class="label-row">
        <i class="fa-solid fa-user-tie"></i>
        <span class="fw-bold"><?php echo htmlspecialchars($labelPembimbing); ?></span>
    </div>
    <div class="value-row"><?php echo $namaPembimbing_html; ?></div>
</div>

<!-- Bagian Dosen Penguji (HANYA MUNCUL JIKA BUKAN SIDANG SEMESTER) -->
<?php if ($jenis_sidang != 'Semester'): ?>
<div class="info-group">
   <div class="label-row"><i class="fa-solid fa-id-card-clip"></i><span class="fw-bold">Dosen Penguji</span></div>
    <div class="value-row"><?php echo $namaPenguji_html; ?></div>
</div>
<?php endif; ?>



                            </div>
                            <div class="section">
<div class="info-group">
    <div class="label-row"> <i class="fa-solid fa-user"></i>  <span class="fw-bold"> Mata Kuliah </span></div>
     <div><?= htmlspecialchars($nama_matkul_sidang) ?></div>

                                </div>
                                <div class="info-group">
                                    <div class="label-row"> <i class="fa-solid fa-door-open"></i>  <span class="fw-bold"> Ruangan</span></div>
                                    <div class="value-row"><?php echo htmlspecialchars($ruangan); ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="label-row"> <i class="fa-solid fa-calendar-days"></i>  <span class="fw-bold"> Tanggal</span></div>
                                    <div class="value-row"><?php echo $tanggal_formatted; // tidak perlu htmlspecialchars karena sudah diformat aman ?></div>
                                </div>
                                <div class="info-group">
                                    <div class="label-row"> <i class="fa-solid fa-clock"></i>  <span class="fw-bold"> Jam</span></div>
                                    <div class="value-row"><?php echo htmlspecialchars($jam); ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ==== PERUBAHAN DIMULAI DI SINI ==== -->
                        
                      <h3>Nilai Sidang (Sementara)</h3>
<div class="form-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Masukkan Nilai Sidang <span style="color: red;">*</span></h4>
    </div>
     <div class="penilaian-container">
        <div class="penilaian-item">
            <label for="nilaiLaporan">Nilai Laporan :</label>
            <!-- PERBAIKAN DI SINI: ganti name="nilaiLaporan" menjadi name="n_dokumen" -->
            <input type="text" class="form-control-custom text-center input-nilai" name="n_dokumen" maxlength="3" 
                   value="<?= htmlspecialchars($nilai_mahasiswa['n_dokumen'] ?? '') ?>" <?= $nilai_sudah_dikirim_dan_lengkap ? 'readonly' : '' ?>>
        </div>
        <div class="penilaian-item">
            <label for="materiPresentasi">Materi Presentasi :</label>
            <!-- PERBAIKAN DI SINI: ganti name="materiPresentasi" menjadi name="n_presentasi" -->
            <input type="text" class="form-control-custom text-center input-nilai" name="n_presentasi" maxlength="3" 
                   value="<?= htmlspecialchars($nilai_mahasiswa['n_presentasi'] ?? '') ?>" <?= $nilai_sudah_dikirim_dan_lengkap ? 'readonly' : '' ?>>
        </div>
        <div class="penilaian-item">
            <label for="nilaiPenyampaian">Penyampaian :</label>
            <!-- PERBAIKAN DI SINI: ganti name="nilaiPenyampaian" menjadi name="n_tanyajawab" -->
            <input type="text" class="form-control-custom text-center input-nilai" name="n_tanyajawab" maxlength="3" 
                   value="<?= htmlspecialchars($nilai_mahasiswa['n_tanyajawab'] ?? '') ?>" <?= $nilai_sudah_dikirim_dan_lengkap ? 'readonly' : '' ?>>
        </div>
        <div class="penilaian-item">
            <label for="nilaiProyek">Nilai Proyek :</label>
            <!-- PERBAIKAN DI SINI: ganti name="nilaiProyek" menjadi name="n_proyek" -->
            <input type="text" class="form-control-custom text-center input-nilai" name="n_proyek" maxlength="3" 
                   value="<?= htmlspecialchars($nilai_mahasiswa['n_proyek'] ?? '') ?>" <?= $nilai_sudah_dikirim_dan_lengkap ? 'readonly' : '' ?>>
        </div>
    </div>
      <!-- Form vertikal untuk mobile tetap sama -->
                            <p class="error-message" id="nilaiSidangErrorMessage"> *Semua nilai harus diisi!</p>
                        </div>
                        
                        <?php if (!empty($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($_SESSION['error']) ?>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <h3>Catatan Evaluasi Sidang</h3>
                        <div class="form-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4>Masukkan Catatan Evaluasi Sidang <span style="color: red;">*</span></h4>
                            </div>
                            <div class="form-group-custom">
                                <label for="catatanEvaluasi" class="visually-hidden">Catatan Evaluasi</label>
                                <textarea id="catatanEvaluasi" name="catatanEvaluasi" class="form-control-custom" placeholder="Silahkan masukkan Catatan Evaluasi Sidang disini.." <?= $nilai_sudah_dikirim_dan_lengkap ? 'readonly' : '' ?>><?php echo htmlspecialchars($catatan_revisi); ?></textarea>
                            </div>
                            <p class="error-message" id="catatanEvaluasiErrorMessage"> *Harus diisi!</p>
                        </div>

                        <?php if (!$nilai_sudah_dikirim_dan_lengkap): ?>
                        <div class="button-group-bottom">
                            <button style="margin-left:auto;" type="button" class="btn-kirim" id="btnKirim">Kirim</button>
                        </div>
                        <?php endif; ?>

                    </form>
                </div>
            </main>
        </div>
    </div>



    
    
    <!-- Modal konfirmasi -->
    <div class="modal fade" id="confirmationKirimModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmationKirimModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
                <div class="modal-header custom-modal-header border-0 justify-content-center">
                    <h4 class="modal-title fw-bold" id="confirmationKirimModalLabel" style="font-size: 24px;">Perhatian!</h4>
                </div>
                <div class="modal-body custom-modal-body">
                    <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah Anda yakin hendak mengirimkan evaluasi untuk mahasiswa <br><strong><?= htmlspecialchars($current_nama_mhs) ?></strong>?</p>
                    <div class="d-flex justify-content-between px-5"><button type="button" class="btn btn-tolak fw-semibold" data-bs-dismiss="modal">Batalkan</button><button type="button" class="btn btn-setujui fw-semibold" id="btnKonfirmasiKirim">Kirimkan</button></div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // SweetAlert untuk notifikasi sukses
        <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
        Swal.fire({
            title: 'Berhasil!',
            text: 'Data evaluasi berhasil disimpan.',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
        // Membersihkan URL dari parameter status
        setTimeout(() => {
            const url = new URL(window.location);
            url.searchParams.delete('status');
            window.history.replaceState({}, document.title, url);
        }, 2000);
        <?php endif; ?>

        // Script untuk form action di modal
        document.getElementById('btnKonfirmasiKirim').addEventListener('click', function() {
            document.getElementById('evaluasiForm').submit();
        });

        // Script untuk menampilkan modal
        document.getElementById('btnKirim').addEventListener('click', function() {
            // Lakukan validasi dulu jika perlu
            // ...
            var myModal = new bootstrap.Modal(document.getElementById('confirmationKirimModal'));
            myModal.show();
        });
    </script>
    <script src="../../assets/js/dEvaluasiSidang.js"></script> 
</body>
</html>