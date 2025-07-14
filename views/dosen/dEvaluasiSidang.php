    <?php
    require_once '../../control/dosen/dEvaluasiSidang_queries.php';
    // HAPUS: require_once __DIR__ . '/../../control/kirimNotifikasi.php';
    // HAPUS: if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nim'])) { ... kirimNotifikasi ... }
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
        <h2 class="fs-5 fw-semibold mb-0" style="margin-left: 15px; margin-top: 20px; color: #464869;">
            Catatan Perbaikan - Kelompok <?php echo htmlspecialchars($nomor_kelompok ?? ''); ?>
        </h2><br>

        <!-- HAPUS CLASS .nilai-box DARI SINI, BIARKAN HANYA CONTAINER-FLUID -->
        <div class="container-fluid px-0"> <!-- Hapus padding horizontal bawaan container-fluid jika perlu -->
            <!-- TAB NAVIGASI MAHASISWA -->
            <div class="row">
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
                <!-- Hidden input untuk NIM -->
                <input type="hidden" name="nim" value="<?= htmlspecialchars($current_nim) ?>">

                <!-- CARD PERTAMA: INFORMASI SIDANG DAN NILAI -->
                <div class="nilai-box mb-4"> <!-- Tambahkan class mb-4 (margin-bottom) untuk memberi jarak -->
                    <div class="info-card">
                        <div class="section">
                            <!-- ... (Konten info-group NIM, Judul, Pembimbing, Penguji) ... -->
                            <div class="info-group">
                                <div class="label-row"> <i class="fa-solid fa-id-card"></i> <span class="fw-bold"> NIM</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($current_nim ?: '-'); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row"> <i class="fa-solid fa-file-invoice"></i> <span class="fw-bold"> Judul Sidang</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($judul); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row">
                                    <i class="fa-solid fa-user-tie"></i>
                                    <span class="fw-bold"><?php echo htmlspecialchars($labelPembimbing); ?></span>
                                </div>
                                <div class="value-row"><?php echo $namaPembimbing_html; ?></div>
                            </div>
                            <?php if ($jenis_sidang != 'Semester'): ?>
                                <div class="info-group">
                                    <div class="label-row"><i class="fa-solid fa-id-card-clip"></i><span class="fw-bold">Dosen Penguji</span></div>
                                    <div class="value-row"><?php echo $namaPenguji_html; ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="section">
                            <!-- ... (Konten info-group Mata Kuliah, Ruangan, Tanggal, Jam) ... -->
                            <div class="info-group">
                                <div class="label-row"> <i class="fa-solid fa-user"></i> <span class="fw-bold"> Mata Kuliah </span></div>
                                <div><?= htmlspecialchars($nama_matkul_sidang) ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row"> <i class="fa-solid fa-door-open"></i> <span class="fw-bold"> Ruangan</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($ruangan); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row"> <i class="fa-solid fa-calendar-days"></i> <span class="fw-bold"> Tanggal</span></div>
                                <div class="value-row"><?php echo $tanggal_formatted; ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row"> <i class="fa-solid fa-clock"></i> <span class="fw-bold"> Jam</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($jam); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian Nilai Sidang -->
                    <h3 class="mt-4">Nilai Sidang (Sementara)</h3>
                    <div class="form-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4>Masukkan Nilai Sidang <span style="color: red;">*</span></h4>
                        </div>
                        <div class="penilaian-container">
                            <!-- ... (Konten input nilai) ... -->
                            <div class="penilaian-item">
                                <label for="nilaiLaporan">Nilai Laporan :</label>
                                <input type="text" class="form-control-custom text-center input-nilai" name="n_dokumen" maxlength="3"
                                    value="<?= htmlspecialchars($nilai_mahasiswa['n_dokumen'] ?? '') ?>" <?= $nilai_sudah_dikirim_dan_lengkap ? 'readonly' : '' ?>>
                            </div>
                            <div class="penilaian-item">
                                <label for="materiPresentasi">Materi Presentasi :</label>
                                <input type="text" class="form-control-custom text-center input-nilai" name="n_presentasi" maxlength="3"
                                    value="<?= htmlspecialchars($nilai_mahasiswa['n_presentasi'] ?? '') ?>" <?= $nilai_sudah_dikirim_dan_lengkap ? 'readonly' : '' ?>>
                            </div>
                            <div class="penilaian-item">
                                <label for="nilaiPenyampaian">Penyampaian :</label>
                                <input type="text" class="form-control-custom text-center input-nilai" name="n_tanyajawab" maxlength="3"
                                    value="<?= htmlspecialchars($nilai_mahasiswa['n_tanyajawab'] ?? '') ?>" <?= $nilai_sudah_dikirim_dan_lengkap ? 'readonly' : '' ?>>
                            </div>
                            <div class="penilaian-item">
                                <label for="nilaiProyek">Nilai Proyek :</label>
                                <input type="text" class="form-control-custom text-center input-nilai" name="n_proyek" maxlength="3"
                                    value="<?= htmlspecialchars($nilai_mahasiswa['n_proyek'] ?? '') ?>" <?= $nilai_sudah_dikirim_dan_lengkap ? 'readonly' : '' ?>>
                            </div>
                        </div>
                        <p class="error-message" id="nilaiSidangErrorMessage"> *Semua nilai harus diisi!</p>
                    </div>
                </div> <!-- AKHIR DARI CARD PERTAMA -->


                <!-- CARD KEDUA: CATATAN EVALUASI DAN TOMBOL KIRIM -->
                <div class="nilai-box">
                    <h3>Catatan Evaluasi Sidang</h3>
                    <div class="form-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4>Masukkan Catatan Evaluasi Sidang <span style="color: red;">*</span></h4>
                        </div>
                        <div class="form-group-custom">
                            <label for="catatanEvaluasi" class="visually-hidden">Catatan Evaluasi</label>
                            <textarea id="catatanEvaluasi" name="catatanEvaluasi" class="form-control-custom" placeholder="Silahkan masukkan Catatan Evaluasi Sidang disini.."><?php echo htmlspecialchars($catatan_revisi); ?></textarea>
                        </div>
                        <p class="error-message" id="catatanEvaluasiErrorMessage"> *Harus diisi!</p>
                    </div>

                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger mt-3">
                            <?= htmlspecialchars($_SESSION['error']) ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    </div> <!-- AKHIR DARI CARD KEDUA -->

                    <!-- Tombol Kirim dipindahkan ke dalam card ini -->
                    <div class="col-12 d-flex justify-content-end mt-4">
                        <button type="button" id="btnKirim"
                            class="btn btn-setujui <?= $nilai_sudah_dikirim_dan_lengkap ? '' : 'btn-passive' ?>"
                            <?= $nilai_sudah_dikirim_dan_lengkap ? 'disabled' : '' ?>>
                            <?= $nilai_sudah_dikirim_dan_lengkap ? 'kirim' : 'Kirim' ?>
                        </button>
                    </div>
                

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
            <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
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

        

            

    <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
                // ... kode sweetalert ...
            <?php endif; ?>

            // Script untuk form action di modal
            document.getElementById('btnKonfirmasiKirim').addEventListener('click', function() {
                document.getElementById('evaluasiForm').submit();
            });

            
            document.getElementById('btnKirim').addEventListener('click', function() {
            
                var myModal = new bootstrap.Modal(document.getElementById('confirmationKirimModal'));
                myModal.show();
            });
        </script>

        <script>
            const isFormLocked = <?= json_encode($nilai_sudah_dikirim_dan_lengkap); ?>;
        </script>

        <script src="../../assets/js/dEvaluasiSidang.js"></script>
    </body>

    </html>