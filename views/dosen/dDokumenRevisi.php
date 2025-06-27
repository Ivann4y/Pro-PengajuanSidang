<?php
require "../../koneksi/koneksiAndrew.php"; // Pastikan path ini benar

// ===================================================================================
// BAGIAN 1: INISIALISASI
// ===================================================================================
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: ID Sidang tidak valid.");
}
$id_sidang = (int)$_GET['id'];

// Variabel default
$judul = 'Belum ada judul';
$ruangan = 'Belum Dijadwalkan';
$tanggal_formatted = 'Belum Dijadwalkan';
$jam = 'Belum Dijadwalkan';
$dosenPembimbing = [];
$dosenPenguji = [];

// ===================================================================================
// BAGIAN 2: PENGAMBILAN DATA
// ### PERBAIKAN UTAMA: Logika disederhanakan, tidak lagi bergantung pada 'jenis_sidang' ###
// ===================================================================================

$sql_sidang = "SELECT judul, id_kelompok FROM Sidang WHERE id_sidang = ?";
$result_sidang = sqlsrv_query($conn, $sql_sidang, [$id_sidang]);
if ($data_sidang = sqlsrv_fetch_array($result_sidang, SQLSRV_FETCH_ASSOC)) {
    // 1. Selalu ambil Judul dan Dosen
    $judul = $data_sidang['judul'];
    $id_kelompok = $data_sidang['id_kelompok'];

    // Ambil Dosen Pembimbing
    if ($id_kelompok) {
        $sql_pembimbing = "SELECT DISTINCT d.nama_dosen FROM [dbo].[Bimbingan] b JOIN [dbo].[Dosen] d ON b.nomor_dosen = d.nomor_dosen WHERE b.id_kelompok = ? AND d.isPembimbing = 0x01";
        $stmt_pembimbing = sqlsrv_query($conn, $sql_pembimbing, [$id_kelompok]);
        if ($stmt_pembimbing) {
            while ($row = sqlsrv_fetch_array($stmt_pembimbing, SQLSRV_FETCH_ASSOC)) {
                $dosenPembimbing[] = $row['nama_dosen'];
            }
        }
    }

    // Ambil Dosen Penguji
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
}

$sql_revisi = "SELECT dok_revisi FROM Detail_Sidang WHERE id_sidang = ?";
$stmt_revisi = sqlsrv_query($conn, $sql_revisi, [$id_sidang]);
$data_revisi = sqlsrv_fetch_array($stmt_revisi, SQLSRV_FETCH_ASSOC);

if (isset($_POST['approve'])) {
    $id_sidang = $_POST['id_sidang']; // pastikan form mengirimkan id_sidang
    $nomor_dosen = $_SESSION['nomor_dosen']; // ambil dari session login

    // Update status revisi hanya untuk dosen ini
    $sql = "UPDATE Detail_Sidang SET status_revisi = 0x01 WHERE id_sidang = ? AND nomor_dosen = ?";
    $stmt = sqlsrv_query($conn, $sql, [$id_sidang, $nomor_dosen]);

    if ($stmt) {
        // Redirect ke halaman nilai akhir
        header("Location: dNilaiAkhir.php?id=$id_sidang");
        exit;
    } else {
        echo "Gagal menyimpan status revisi.";
    }
}



?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Revisi - Responsive</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../css/button-styles.css">
    <link rel="stylesheet" href="../../assets/css/dDokumenRevisi.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 for pop-up notifications -->

</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
            </div>
            <ul class="NavSide__sidebar-nav">
                <!-- MENU "Detail Sidang" DIHAPPU S DARI SINI -->
                <li class="NavSide__sidebar-item "> <!-- Evaluasi aktif -->
                    <b></b><b></b>
                    <a href="dEvaluasiSidang.php?id=<?= $id_sidang ?>">
                        <span class="fw-semibold">Evaluasi</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="dDokumenRevisi.php?id=<?= $id_sidang ?>">
                        <span class="fw-semibold">Dokumen</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dNilaiAkhir.php?id=<?= $id_sidang ?>">
                        <span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDaftarSidang.php">
                        <span class="NavSide__sidebar-title fw-semibold">Kembali</span>
                    </a>
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
            <h2>Detail Sidang - Sistem Pengajuan Sidang</h2>


            <div class="info-card">
                <div class="section">
                    <?php if (!empty($dosenPembimbing)): ?>
                        <div class="info-group">
                            <div class="label-row"><i class="fa-solid fa-file-invoice"></i><span class="fw-bold">Judul Sidang</span></div>
                            <div class="value-row"><?php echo !empty($judul) ? htmlspecialchars($judul) : 'Belum ada judul'; ?></div>
                        </div>

                        <div class="info-group">
                            <div class="label-row"><i class="fa-solid fa-user-tie"></i><span class="fw-bold">Dosen Pembimbing</span></div>
                            <div class="value-row">
                                <?php
                                if (!empty($dosenPembimbing)) {
                                    echo implode('<br>', array_map('htmlspecialchars', $dosenPembimbing));
                                } else {
                                    echo 'Belum ditentukan';
                                }
                                ?>
                            </div>
                        </div>

                        <div class="info-group">
                            <div class="label-row"><i class="fa-solid fa-user-group"></i><span class="fw-bold">Dosen Penguji</span></div>
                            <div class="value-row">
                                <?php
                                if (!empty($dosenPenguji)) {
                                    echo implode('<br>', array_map('htmlspecialchars', $dosenPenguji));
                                } else {
                                    echo 'Belum ditentukan';
                                }
                                ?>
                            </div>
                        </div>

                    <?php elseif (!empty($dosen_pengampu)): ?>
                        <div class="info-group">
                            <div class="label-row"><i class="fa-solid fa-book"></i><span class="fw-bold">Mata Kuliah</span></div>
                            <div class="value-row"><?php echo !empty($data_matkul['nama_matkul']) ? htmlspecialchars($data_matkul['nama_matkul']) : 'N/A'; ?></div>
                        </div>

                        <div class="info-group">
                            <div class="label-row"><i class="fa-solid fa-user-group"></i><span class="fw-bold">Dosen Pengampu</span></div>
                            <div class="value-row">
                                <?php
                                if (!empty($dosen_pengampu)) {
                                    echo implode('<br>', array_map('htmlspecialchars', $dosen_pengampu));
                                } else {
                                    echo 'Belum ditentukan';
                                }
                                ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="info-item">Data sidang tidak lengkap atau tidak dikenali.</p>
                    <?php endif; ?>
                </div>

                <div class="section">
                    <div class="info-group">
                        <div class="label-row"><i class="fa-solid fa-door-open"></i><span class="fw-bold">Ruangan</span></div>
                        <div class="value-row"><?php echo !empty($ruangan) ? htmlspecialchars($ruangan) : 'Belum Dijadwalkan'; ?></div>
                    </div>

                    <div class="info-group">
                        <div class="label-row"><i class="fa-solid fa-calendar-days"></i><span class="fw-bold">Tanggal</span></div>
                        <div class="value-row">
                            <?php echo !empty($tanggal_formatted) ? htmlspecialchars($tanggal_formatted) : 'Belum Dijadwalkan'; ?>
                        </div>
                    </div>

                    <div class="info-group">
                        <div class="label-row"><i class="fa-solid fa-clock"></i><span class="fw-bold">Jam</span></div>
                        <div class="value-row">
                            <?php echo !empty($jam) ? htmlspecialchars($jam) : 'Belum Dijadwalkan'; ?>
                        </div>
                    </div>
                </div>
            </div>


            <h5>Dokumen Sidang</h5>
            <div class="file-buttons-container d-flex flex-wrap">
                <?php if (!empty($data_revisi['dok_revisi'])): ?>
                    <a href="../../uploadtesting/<?= htmlspecialchars($data_revisi['dok_revisi']) ?>" class="file-button" download>
                        <i class="fa-solid fa-file-zipper"></i>
                        <?= htmlspecialchars(basename($data_revisi['dok_revisi'])) ?>
                    </a>
                <?php else: ?>
                    <p class="text-muted">Belum ada dokumen revisi yang diupload mahasiswa.</p>
                <?php endif; ?>
            </div>


            <div class="button-group-bottom" id="grup-aksi-dokumen">
                <div class="button-group">
                    <button class="btn btn-tolak" onclick="showConfirmationModal('Ditolak')">Tolak</button>
                    <button class="btn btn-setujui" onclick="showConfirmationModal('Disetujui')">Setujui</button>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="notifModal" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <img src="../../assets/img/centang.svg" width="100" class="mx-auto mb-3" alt="Check Icon">
                    <h5 class="modal-title" id="notifModalLabel"></h5>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal-content">
                <div class="modal-header custom-modal-header">
                    <h4 class="modal-title fw-bold" id="confirmationModalLabel" style="font-size: 24px;">Perhatian!</h4>
                </div>
                <div class="modal-body custom-modal-body">
                    <p class="mb-5 fw-semibold" id="confirmationModalText" style="font-size: 16px;"></p>
                    <div class="d-flex justify-content-between px-4">
                        <button type="button" class="btn btn-tolak fw-semibold" data-bs-dismiss="modal">Batalkan</button>
                        <button type="button" class="btn btn-setujui fw-semibold" id="btnConfirmAction">Lanjutkan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
        // --- Sidebar Toggle Logic for Mobile ---
        const menuToggle = document.querySelector(".NavSide__toggle");
        const sidebar = document.getElementById("main-sidebar");

        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', function() {
                menuToggle.classList.toggle("active");
                sidebar.classList.toggle("active");
            });
        }


        // --- Modal Logic ---
        function showConfirmationModal(action) {
            const confirmationModalElement = document.getElementById('confirmationModal');
            if (!confirmationModalElement) {
                console.error('Modal HTML dengan id "confirmationModal" tidak ditemukan!');
                return;
            }

            const confirmationModal = new bootstrap.Modal(confirmationModalElement);
            const modalText = document.getElementById('confirmationModalText');
            const confirmButton = document.getElementById('btnConfirmAction');

            let actionText = action === 'Disetujui' ? 'menyetujui' : 'menolak';

            modalText.innerText = `Apakah Anda yakin ingin ${actionText} dokumen revisi ini?`;

            const newConfirmButton = confirmButton.cloneNode(true);
            confirmButton.parentNode.replaceChild(newConfirmButton, confirmButton);

            newConfirmButton.addEventListener('click', function() {
                confirmationModal.hide();

                setTimeout(function() {
                    if (action === 'Ditolak') {
                        Swal.fire({
                            title: 'Alasan Penolakan',
                            input: 'textarea',
                            inputLabel: 'Catatan:',
                            inputPlaceholder: 'Masukan catatan di sini...',
                            showCancelButton: true,
                            confirmButtonText: 'Kirim',
                            cancelButtonText: 'Batal',
                            reverseButtons: true,
                            customClass: {
                                confirmButton: 'btn btn-setujui',
                                cancelButton: 'btn btn-tolak'
                            },
                            inputValidator: (value) => {
                                if (!value || value.trim() === '') {
                                    return 'Alasan penolakan tidak boleh kosong!';
                                }
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: `Dokumen revisi telah berhasil ditolak.`,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // --- PERUBAHAN 1: NAVIGASI SETELAH TOLAK ---
                                    window.location.href = 'dDaftarSidang.php';
                                });

                                console.log('Catatan Penolakan:', result.value);
                            }
                        });
                    } else { // Jika aksi adalah 'Disetujui'
                        Swal.fire({
                            title: 'Berhasil!',
                            text: `Dokumen revisi telah berhasil disetujui.`,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4B68FB'
                        }).then(() => {
                            // Kirim request ke server
                            fetch(window.location.href, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: new URLSearchParams({
                                        approve: true,
                                        id_sidang: <?= $id_sidang ?>
                                    })
                                })
                                .then(() => {
                                    // Redirect setelah update berhasil
                                    window.location.href = 'dNilaiAkhir.php?id=<?= $id_sidang ?>';
                                });
                        });

                    }
                }, 500);
            });

            confirmationModal.show();
        }
    </script>
</body>

</html>