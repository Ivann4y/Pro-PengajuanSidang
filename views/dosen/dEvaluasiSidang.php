<?php
session_start();
require "../../koneksi/koneksiAndrew.php"; // Pastikan path ini benar

// ===================================================================================
// BAGIAN 1: AMBIL ID SIDANG DARI GET SEKALI SAJA, LALU SIMPAN KE SESSION
// ===================================================================================
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $_SESSION['id_sidang_aktif'] = (int)$_GET['id'];
    header("Location: dEvaluasiSidang.php");
   
}




// Simulasi Dosen yang Login (nantinya ganti dengan session asli)
$nomor_dosen_login = '1001';


if (!isset($_SESSION['id_sidang_aktif'])) {
    die("ID sidang tidak tersedia.");
}
$id_sidang = $_SESSION['id_sidang_aktif'];

// ===================================================================================
// SIMULASI DOSEN LOGIN (GANTI DENGAN SESSION ASLI NANTI)
// ===================================================================================
$nomor_dosen_login = '1001';


// if (!isset($_SESSION['user']['nomor_dosen'])) { die("Akses ditolak."); }
// $nomor_dosen_login = $_SESSION['user']['nomor_dosen'];

// ===================================================================================
// BAGIAN 2: PROSES PENYIMPANAN DATA (SAAT FORM DI-SUBMIT)
// ===================================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil semua data dari form yang dikirim
    $catatan_post = $_POST['catatanEvaluasi'];
    $nilaiLaporan = !empty($_POST['nilaiLaporan']) ? (int)$_POST['nilaiLaporan'] : null;
    $nilaiPresentasi = !empty($_POST['materiPresentasi']) ? (int)$_POST['materiPresentasi'] : null;
    $nilaiPenyampaian = !empty($_POST['nilaiPenyampaian']) ? (int)$_POST['nilaiPenyampaian'] : null;
    $nilaiProyek = !empty($_POST['nilaiProyek']) ? (int)$_POST['nilaiProyek'] : null;

    // Tidak perlu koneksi baru, gunakan $conn yang sudah ada dari include
    // $conn_post = sqlsrv_connect($serverName, $connectionOptions); 

    // 1. UPDATE CATATAN REVISI DI TABEL Detail_Sidang
    $sql_update_catatan = "UPDATE Detail_Sidang SET catatan_sidang = ? WHERE id_sidang = ? AND nomor_dosen = ?";
    $params_update_catatan = [$catatan_post, $id_sidang, $nomor_dosen_login];
    $stmt_update_catatan = sqlsrv_query($conn, $sql_update_catatan, $params_update_catatan);
    if ($stmt_update_catatan === false) {
        die("Gagal memperbarui catatan revisi: " . print_r(sqlsrv_errors(), true));
    }

    // 2. CEK & SIMPAN NILAI (UPSERT) KE TABEL Penilaian
    $sql_cek_nilai = "SELECT COUNT(*) as 'count' FROM Penilaian WHERE id_sidang = ? AND nomor_dosen = ?";
    $stmt_cek_nilai = sqlsrv_query($conn, $sql_cek_nilai, [$id_sidang, $nomor_dosen_login]);
    $nilai_exists = sqlsrv_fetch_array($stmt_cek_nilai, SQLSRV_FETCH_ASSOC)['count'] > 0;

    if ($nilai_exists) {
        $sql_nilai = "UPDATE Penilaian SET n_dokumen = ?, n_presentasi = ?, n_tanyajawab = ?, n_proyek = ? WHERE id_sidang = ? AND nomor_dosen = ?";
        $params_nilai = [$nilaiLaporan, $nilaiPresentasi, $nilaiPenyampaian, $nilaiProyek, $id_sidang, $nomor_dosen_login];
    } else {
        $sql_get_nim = "SELECT TOP 1 km.nim FROM Sidang s JOIN Kelompok_Mahasiswa km ON s.id_kelompok = km.id_kelompok WHERE s.id_sidang = ?";
        $stmt_get_nim = sqlsrv_query($conn, $sql_get_nim, [$id_sidang]);
        $nim_untuk_insert = sqlsrv_fetch_array($stmt_get_nim, SQLSRV_FETCH_ASSOC)['nim'];
        $sql_nilai = "INSERT INTO Penilaian (id_sidang, nim, nomor_dosen, n_dokumen, n_presentasi, n_tanyajawab, n_proyek) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params_nilai = [$id_sidang, $nim_untuk_insert, $nomor_dosen_login, $nilaiLaporan, $nilaiPresentasi, $nilaiPenyampaian, $nilaiProyek];
    }

    $stmt_nilai = sqlsrv_query($conn, $sql_nilai, $params_nilai);
    if ($stmt_nilai === false) {
        die("Gagal menyimpan nilai: " . print_r(sqlsrv_errors(), true));
    }

    // sqlsrv_close($conn_post); // Jangan tutup koneksi di sini

    // ### INI BAGIAN YANG DIPERBAIKI ###
    // Pastikan ada tanda "=" setelah "id"
    header("Location: dEvaluasiSidang.php?id=" .$id_sidang . "&status=sukses");
    exit();
}

// ===================================================================================
// BAGIAN 3: PENGAMBILAN DATA UNTUK DITAMPILKAN DI HALAMAN
// ===================================================================================

// Variabel default
$id_kelompok = null;
$judul = 'Data tidak ditemukan';
$ruangan = '-';
$tanggal_formatted = '-';
$jam = '-';
$dosenPembimbing = [];
$dosenPenguji = [];
$catatan_revisi = '';
$nilai_mahasiswa = ['n_dokumen' => '', 'n_presentasi' => '', 'n_tanyajawab' => '', 'n_proyek' => ''];

// Ambil data sidang
$sql_sidang = "SELECT Judul, id_kelompok FROM Sidang WHERE id_sidang = ?";
$result_sidang = sqlsrv_query($conn, $sql_sidang, [$id_sidang]);
if ($data_sidang = sqlsrv_fetch_array($result_sidang, SQLSRV_FETCH_ASSOC)) {
    $judul = $data_sidang['Judul'];
    $id_kelompok = $data_sidang['id_kelompok'];

    // Ambil dosen pembimbing
    if ($id_kelompok) {
        $sql_pembimbing = "SELECT DISTINCT d.nama_dosen FROM [dbo].[Bimbingan] b JOIN [dbo].[Dosen] d ON b.nomor_dosen = d.nomor_dosen WHERE b.id_kelompok = ? AND d.isPembimbing = 0x01";
        $stmt_pembimbing = sqlsrv_query($conn, $sql_pembimbing, [$id_kelompok]);
        if ($stmt_pembimbing) {
            while ($row = sqlsrv_fetch_array($stmt_pembimbing, SQLSRV_FETCH_ASSOC)) {
                $dosenPembimbing[] = $row['nama_dosen'];
            }
        }
    }

    // Ambil dosen penguji
    $sql_penguji = "SELECT DISTINCT d.nama_dosen FROM [dbo].[Penjadwalan] p JOIN [dbo].[Dosen] d ON p.nomor_dosen = d.nomor_dosen WHERE p.id_sidang = ? AND d.isPenguji = 0x01";
    $stmt_penguji = sqlsrv_query($conn, $sql_penguji, [$id_sidang]);
    if ($stmt_penguji) {
        while ($row = sqlsrv_fetch_array($stmt_penguji, SQLSRV_FETCH_ASSOC)) {
            $dosenPenguji[] = $row['nama_dosen'];
        }
    }

    // Ambil jadwal
    $sql_jadwal = "SELECT ruang_sidang, tanggal_sidang, jam_sidang FROM Jadwal WHERE id_sidang = ?";
    $result_jadwal = sqlsrv_query($conn, $sql_jadwal, [$id_sidang]);
    if ($result_jadwal && $data_jadwal = sqlsrv_fetch_array($result_jadwal, SQLSRV_FETCH_ASSOC)) {
        $ruangan = $data_jadwal['ruang_sidang'] ?? '-';
        $jam = $data_jadwal['jam_sidang'] ? $data_jadwal['jam_sidang']->format('H:i') : '-';
        if ($data_jadwal['tanggal_sidang'] instanceof DateTime) {
            setlocale(LC_TIME, 'id_ID.UTF-8', 'Indonesian');
            $tanggal_formatted = strftime('%A, %d %B %Y', $data_jadwal['tanggal_sidang']->getTimestamp());
        }
    }

    // Ambil catatan & nilai yang sudah ada untuk ditampilkan di form
    $sql_catatan = "SELECT catatan_sidang FROM Detail_Sidang WHERE id_sidang = ? AND nomor_dosen = ?";
    $result_catatan = sqlsrv_query($conn, $sql_catatan, [$id_sidang, $nomor_dosen_login]);
    if ($result_catatan && $row_catatan = sqlsrv_fetch_array($result_catatan, SQLSRV_FETCH_ASSOC)) {
        $catatan_revisi = $row_catatan['catatan_sidang'];
    }

    $sql_get_nilai = "SELECT n_dokumen, n_presentasi, n_tanyajawab, n_proyek FROM Penilaian WHERE id_sidang = ? AND nomor_dosen = ?";
    $result_get_nilai = sqlsrv_query($conn, $sql_get_nilai, [$id_sidang, $nomor_dosen_login]);
    if ($result_get_nilai && $row_nilai = sqlsrv_fetch_array($result_get_nilai, SQLSRV_FETCH_ASSOC)) {
        $nilai_mahasiswa = $row_nilai;
    }
}

$namaPembimbing_html = !empty($dosenPembimbing) ? implode('<br>', array_map('htmlspecialchars', $dosenPembimbing)) : 'Belum ditentukan';
$namaPenguji_html = !empty($dosenPenguji) ? implode('<br>', array_map('htmlspecialchars', $dosenPenguji)) : 'Belum ditentukan';


?>



<!DOCTYPE html>
<html lang="id">
<!-- KODE HTML LANJUTANNYA TETAP SAMA -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <link rel="stylesheet" href="../../assets/css/dEvaluasiSidang.css">
   

</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
            </div>

            <!-- PERBAIKAN UTAMA ADA DI DALAM 'ul' INI -->
            <ul class="NavSide__sidebar-nav">

                <!-- PERBAIKAN: Nama kelas diperbaiki dan dipisah dengan spasi -->
                <!-- Item ini akan menjadi aktif karena memiliki DUA kelas -->
                <li class="NavSide_sidebar-item NavSide_sidebar-item--active">
                    <b></b><b></b>
                    <a href="dEvaluasiSidang.php?id=<?= $id_sidang ?>">
                        <!-- PERBAIKAN: Nama kelas span juga diperbaiki -->
                        <span class="fw-semibold NavSide__sidebar-title">Evaluasi</span>
                    </a>
                </li>

                <!-- PERBAIKAN: Nama kelas diperbaiki -->
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDokumenRevisi.php?id=<?= $id_sidang ?>">
                        <span class="fw-semibold NavSide__sidebar-title">Dokumen</span>
                    </a>
                </li>

                <!-- PERBAIKAN: Nama kelas diperbaiki -->
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dNilaiAkhir.php?id=<?= $id_sidang ?>">
                        <span class="fw-semibold NavSide__sidebar-title">Nilai Akhir</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold"> Kembali</span></a>
                </li>




            </ul>
        </div>

        <!-- Sisa dari halaman Anda (seperti page-content-wrapper, dll.) -->
        <!-- ... -->

        <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
        <div id="page-content-wrapper">
            <div class="NavSide__topbar"></div>
            <main class="NavSide__main-content">
                <h2>Detail Evaluasi - Sistem Evaluasi Sidang</h2>
                <form id="evaluasiForm" method="POST" action="dEvaluasiSidang.php?id=<?php echo $id_sidang; ?>">
                    <div class="info-card">
                        <div class="section">
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-file-invoice"></i><span class="fw-bold">Judul Sidang</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($judul); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-user-tie"></i><span class="fw-bold">Dosen Pembimbing</span></div>
                                <div class="value-row"><?php echo $namaPembimbing_html; ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-user-group"></i><span class="fw-bold">Dosen Penguji</span></div>
                                <div class="value-row"><?php echo $namaPenguji_html; ?></div>
                            </div>
                        </div>
                        <div class="section">
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-door-open"></i><span class="fw-bold">Ruangan</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($ruangan); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-calendar-days"></i><span class="fw-bold">Tanggal</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($tanggal_formatted); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-clock"></i><span class="fw-bold">Jam</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($jam); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <h3>Nilai Sidang (Sementara)</h3>
<div class="form-card">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Masukkan Nilai Sidang <span style="color: red;">*</span></h4>
    </div>

    <!-- Wadah untuk tampilan desktop (horizontal) -->
   <div class="penilaian-container">
    <div class="penilaian-item">
        <label for="nilaiLaporan">Nilai Laporan :</label>
        <input type="text" class="form-control-custom text-center input-nilai" name="nilaiLaporan" maxlength="3" value="<?= htmlspecialchars($nilai_mahasiswa['n_dokumen'] ?? '') ?>">
    </div>
    <div class="penilaian-item">
        <label for="materiPresentasi">Materi Presentasi :</label>
        <input type="text" class="form-control-custom text-center input-nilai" name="materiPresentasi" maxlength="3" value="<?= htmlspecialchars($nilai_mahasiswa['n_presentasi'] ?? '') ?>">
    </div>
    <div class="penilaian-item">
        <label for="nilaiPenyampaian">Penyampaian :</label>
        <input type="text" class="form-control-custom text-center input-nilai" name="nilaiPenyampaian" maxlength="3" value="<?= htmlspecialchars($nilai_mahasiswa['n_tanyajawab'] ?? '') ?>">
    </div>
    <div class="penilaian-item">
        <label for="nilaiProyek">Nilai Proyek :</label>
        <input type="text" class="form-control-custom text-center input-nilai" name="nilaiProyek" maxlength="3" value="<?= htmlspecialchars($nilai_mahasiswa['n_proyek'] ?? '') ?>">
    </div>
</div>

<!-- Wadah BARU untuk tampilan tablet/mobile (vertikal) -->
<div class="penilaian-grid-vertical">
    <label for="nilaiLaporan_v">Nilai Laporan</label> <span>:</span>
    <input type="text" class="form-control-custom text-center input-nilai" name="nilaiLaporan_v" maxlength="3" value="<?= htmlspecialchars($nilai_mahasiswa['n_dokumen'] ?? '') ?>">
    
    <label for="materiPresentasi_v">Materi Presentasi</label> <span>:</span>
    <input type="text" class="form-control-custom text-center input-nilai" name="materiPresentasi_v" maxlength="3" value="<?= htmlspecialchars($nilai_mahasiswa['n_presentasi'] ?? '') ?>">
    
    <label for="nilaiPenyampaian_v">Penyampaian</label> <span>:</span>
    <input type="text" class="form-control-custom text-center input-nilai" name="nilaiPenyampaian_v" maxlength="3" value="<?= htmlspecialchars($nilai_mahasiswa['n_tanyajawab'] ?? '') ?>">
    
    <label for="nilaiProyek_v">Nilai Proyek</label> <span>:</span>
    <input type="text" class="form-control-custom text-center input-nilai" name="nilaiProyek_v" maxlength="3" value="<?= htmlspecialchars($nilai_mahasiswa['n_proyek'] ?? '') ?>">
</div>
    
    <p class="error-message" id="nilaiSidangErrorMessage"> *Semua nilai harus diisi!</p>
</div>


   


                   

                    <h3>Catatan Evaluasi Sidang</h3>
                    <div class="form-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4>Masukkan Catatan Evaluasi Sidang <span style="color: red;">*</span></h4>
                        </div>
                        <div class="form-group-custom">
                            <label for="catatanEvaluasi" class="visually-hidden">Catatan Evaluasi</label>
                            <textarea id="catatanEvaluasi" name="catatanEvaluasi" class="form-control-custom" placeholder="Silahkan masukkan Catatan Evaluasi Sidang disini.."></textarea>
                        </div>
                        <p class="error-message" id="catatanEvaluasiErrorMessage"> *Harus diisi!</p>
                    </div>
                    <div class="button-group-bottom">

                        <button style="margin-left:90%" type="button" class="btn-kirim" id="btnKirim">Kirim</button>
                    </div>
                </form>
            </main>
        </div>
    </div>
    <div class="modal fade" id="confirmationKirimModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmationKirimModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
                <div class="modal-header custom-modal-header border-0 justify-content-center">
                    <h4 class="modal-title fw-bold" id="confirmationKirimModalLabel" style="font-size: 24px;">Perhatian!</h4>
                </div>
                <div class="modal-body custom-modal-body">
                    <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah anda yakin hendak mengirimkan evaluasi sidang?</p>
                    <div class="d-flex justify-content-between px-5"><button type="button" class="btn btn-tolak fw-semibold" data-bs-dismiss="modal">Batalkan</button><button type="button" class="btn btn-setujui fw-semibold" id="btnKonfirmasiKirim">Kirimkan</button></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    
    // --- FUNGSI UTILITAS UNTUK SINKRONISASI INPUT ---
    function syncInputs(name1, name2) {
        const input1 = document.getElementsByName(name1)[0];
        const input2 = document.getElementsByName(name2)[0];
        if (input1 && input2) {
            input1.addEventListener('input', () => {
                if (document.activeElement !== input2) input2.value = input1.value;
            });
            input2.addEventListener('input', () => {
                if (document.activeElement !== input1) input1.value = input2.value;
            });
        }
    }

    // --- TERAPKAN VALIDASI 'HANYA ANGKA' ---
    document.querySelectorAll('.input-nilai').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 3) this.value = this.value.slice(0, 3);
            if (this.value.length > 1 && this.value.startsWith('0')) {
                this.value = this.value.replace(/^0+/, '');
            }
            if (parseInt(this.value, 10) > 100) {
                this.value = '100';
            }
        });
    });

    // --- PANGGIL FUNGSI SINKRONISASI ---
    syncInputs('nilaiLaporan', 'nilaiLaporan_v');
    syncInputs('materiPresentasi', 'materiPresentasi_v');
    syncInputs('nilaiPenyampaian', 'nilaiPenyampaian_v');
    syncInputs('nilaiProyek', 'nilaiProyek_v');

    // --- LOGIKA UNTUK SIDEBAR ---
    const menuToggle = document.querySelector(".NavSide__toggle");
    const sidebar = document.getElementById("main-sidebar");
    if (menuToggle && sidebar) {
        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
    }

    // --- LOGIKA UNTUK TOMBOL KIRIM FORM ---
    const btnKirim = document.getElementById('btnKirim');
    const confirmationKirimModalElement = document.getElementById('confirmationKirimModal');

    if (btnKirim && confirmationKirimModalElement) {
        btnKirim.addEventListener('click', function() {
            
            // --- VARIABEL DIAMBIL DI SINI (SAAT DIKLIK), BUKAN DI LUAR ---
            const nilaiLaporan = document.getElementsByName('nilaiLaporan')[0].value;
            const materiPresentasi = document.getElementsByName('materiPresentasi')[0].value;
            const nilaiPenyampaian = document.getElementsByName('nilaiPenyampaian')[0].value;
            const nilaiProyek = document.getElementsByName('nilaiProyek')[0].value;
            const catatanEvaluasi = document.getElementById('catatanEvaluasi').value;

            // Sembunyikan pesan error sebelumnya
            document.getElementById('nilaiSidangErrorMessage').style.display = 'none';
            document.getElementById('catatanEvaluasiErrorMessage').style.display = 'none';

            let isValid = true;

            // Lakukan validasi
            if ([nilaiLaporan, materiPresentasi, nilaiPenyampaian, nilaiProyek].some(val => val.trim() === '')) {
                document.getElementById('nilaiSidangErrorMessage').style.display = 'block';
                isValid = false;
            }
            if (catatanEvaluasi.trim() === '') {
                document.getElementById('catatanEvaluasiErrorMessage').style.display = 'block';
                isValid = false;
            }

            // Tampilkan peringatan atau modal
            if (!isValid) {
                Swal.fire({
                    title: 'Harap mengisi semua kolom nilai dan catatan!',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4B68FB'
                });
            } else {
                const confirmationKirimModal = new bootstrap.Modal(confirmationKirimModalElement);
                confirmationKirimModal.show();
            }
        });

        // Event listener untuk tombol konfirmasi di dalam modal
        const btnKonfirmasiKirim = document.getElementById('btnKonfirmasiKirim');
        if (btnKonfirmasiKirim) {
            btnKonfirmasiKirim.addEventListener('click', function() {
                const modalInstance = bootstrap.Modal.getInstance(confirmationKirimModalElement);
                if (modalInstance) {
                    modalInstance.hide();
                }
                Swal.fire({
                    title: 'Evaluasi Sidang Berhasil Dikirim!',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    document.getElementById('evaluasiForm').submit();
                });
            });
        }
    }
});
    </script>
<script src="/Projek/Pro-PengajuanSidang/assets/js/dDaftarSidang.js"></script>
</body>

</html>
