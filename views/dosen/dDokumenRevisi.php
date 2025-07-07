<?php
error_log("=== TEST LOG === PHP logging aktif.");
?>

<?php
session_start();
require "../../koneksi/koneksiAndrew.php"; // Pastikan path ini benar

// ===================================================================================
// BAGIAN 0: PENANGANAN REQUEST POST (APPROVE) SECARA EKSKLUSIF
// ===================================================================================
if (isset($_POST['approve'])) {
    // Set header untuk memberitahu browser bahwa ini adalah respon JSON
    header('Content-Type: application/json');

    $id_sidang = $_POST['id_sidang'] ?? 0;
    $nomor_dosen = isset($_SESSION['user_data']['nomor_dosen'])
        ? (string)$_SESSION['user_data']['nomor_dosen']
        : null;

    // Validasi input
    if (!$nomor_dosen || !$id_sidang) {
        echo json_encode(['status' => 'error', 'message' => 'Sesi tidak valid atau ID Sidang tidak ditemukan. Silakan login ulang.']);
        exit;
    }

    error_log("=== DEBUG SIDANG ===");
    error_log("ID SIDANG: " . var_export($id_sidang, true));
    error_log("NOMOR DOSEN dari SESSION: " . var_export($_SESSION['user_data']['nomor_dosen'] ?? 'TIDAK ADA', true));


    // Cek dulu apakah baris yang akan diupdate memang ada untuk dosen ini
    $check_sql = "SELECT id_sidang FROM Detail_Sidang WHERE id_sidang = ? AND nomor_dosen = ?";
    $params_check = [(int)$id_sidang, $nomor_dosen];
    $stmt_check = sqlsrv_query($conn, $check_sql, $params_check);

    // Jika query check gagal atau tidak mengembalikan baris
    if ($stmt_check === false) {
        error_log("❌ QUERY CHECK GAGAL:");
        error_log(print_r(sqlsrv_errors(), true));
        echo json_encode([
            'status' => 'error',
            'message' => "Query gagal dijalankan. Silakan cek log."
        ]);
        exit;
    }

    $fetched = sqlsrv_fetch($stmt_check);
    if (!$fetched) {
        error_log("⚠️ QUERY BERHASIL, tapi tidak ditemukan baris cocok.");
        error_log("Diperiksa: id_sidang = " . var_export($id_sidang, true));
        error_log("Diperiksa: nomor_dosen = " . var_export($nomor_dosen, true));

        echo json_encode([
            'status' => 'error',
            'message' => "Gagal menyetujui. Tidak ditemukan data revisi yang terhubung dengan akun Anda untuk sidang ini."
        ]);
        exit;
    }


    error_log("DEBUG: id_sidang = $id_sidang");
    error_log("DEBUG: nomor_dosen = " . var_export($nomor_dosen, true));

    // Jika baris ada, lakukan update
    $sql_update = "UPDATE Detail_Sidang SET status_revisi = 'Disetujui' WHERE id_sidang = ? AND nomor_dosen = ?";
    $params_update = [$id_sidang, $nomor_dosen];
    $stmt_update = sqlsrv_query($conn, $sql_update, $params_update);

    $stmt_update = sqlsrv_query($conn, $sql_update, $params_update);

    if ($stmt_update === false) {
        error_log("❌ UPDATE GAGAL:");
        error_log(print_r(sqlsrv_errors(), true));
        echo json_encode([
            'status' => 'error',
            'message' => 'Terjadi kesalahan saat memperbarui status revisi.'
        ]);
        exit;
    } else {
        error_log("✅ UPDATE SUKSES: status_revisi harusnya jadi 0x01");
        echo json_encode([
            'status' => 'success',
            'message' => 'Dokumen revisi berhasil disetujui!',
            'redirectUrl' => "dNilaiAkhir.php?id=" . $id_sidang
        ]);
        exit;
    }
}



// ===================================================================================
// BAGIAN 1: INISIALISASI HALAMAN (GET REQUEST)
// ===================================================================================
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_sidang = (int)$_GET['id'];
    $_SESSION['id_sidang_aktif'] = $id_sidang;
} elseif (isset($_SESSION['id_sidang_aktif'])) {
    $id_sidang = (int)$_SESSION['id_sidang_aktif'];
} else {
    die("Error: ID sidang tidak valid atau tidak ditemukan.");
}

// Pastikan data user dan nomor_dosen ada di session
if (!isset($_SESSION['user_data']['nomor_dosen'])) {
    die("Error: Data dosen tidak ditemukan di session. Silakan login kembali.");
}
$nomorDosen = $_SESSION['user_data']['nomor_dosen'];

// Variabel default
$judul = 'Belum ada judul';
$ruangan = 'Belum Dijadwalkan';
$tanggal_formatted = 'Belum Dijadwalkan';
$jam = 'Belum Dijadwalkan';
$dosenPembimbing = [];
$dosenPenguji = [];
$dosen_pengampu = []; // Variabel untuk dosen pengampu

// ===================================================================================
// BAGIAN 2: PENGAMBILAN DATA (TIDAK DIUBAH)
// ===================================================================================
$sql_sidang = "SELECT judul, id_kelompok FROM Sidang WHERE id_sidang = ?";
$result_sidang = sqlsrv_query($conn, $sql_sidang, [$id_sidang]);
if ($data_sidang = sqlsrv_fetch_array($result_sidang, SQLSRV_FETCH_ASSOC)) {
    // 1. Selalu ambil Judul
    $judul = $data_sidang['judul'];
    $id_kelompok = $data_sidang['id_kelompok'];

    // Ambil Dosen Pembimbing (jika ada kelompok)
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
    $sql_penguji = "SELECT DISTINCT d.nama_dosen FROM [dbo].[Penjadwalan] p JOIN [dbo].[Dosen] d ON p.nomor_dosen = d.nomor_dosen WHERE p.id_sidang = ? AND d.isPenguji = 0x01";
    $stmt_penguji = sqlsrv_query($conn, $sql_penguji, [$id_sidang]);
    if ($stmt_penguji) {
        while ($row = sqlsrv_fetch_array($stmt_penguji, SQLSRV_FETCH_ASSOC)) {
            $dosenPenguji[] = $row['nama_dosen'];
        }
    }

    // Ambil Dosen Pengampu jika ada (ditambahkan sebagai fallback)
    $sql_pengampu = "SELECT DISTINCT d.nama_dosen FROM [dbo].[Penjadwalan] p JOIN [dbo].[Dosen] d ON p.nomor_dosen = d.nomor_dosen WHERE p.id_sidang = ? AND d.isPengampu = 0x01";
    $stmt_pengampu = sqlsrv_query($conn, $sql_pengampu, [$id_sidang]);
    if ($stmt_pengampu) {
        while ($row = sqlsrv_fetch_array($stmt_pengampu, SQLSRV_FETCH_ASSOC)) {
            $dosen_pengampu[] = $row['nama_dosen'];
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

// Ambil dokumen revisi
$sql_revisi = "SELECT dok_revisi FROM Detail_Sidang WHERE id_sidang = ?";
$stmt_revisi = sqlsrv_query($conn, $sql_revisi, [$id_sidang]);
$data_revisi = sqlsrv_fetch_array($stmt_revisi, SQLSRV_FETCH_ASSOC);
$namaFileRevisi = "dokumen_dummy_revisi.zip"; // Nama file default jika tidak ada revisi

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
    <link rel="stylesheet" href="../../extra/style.css">
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
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dEvaluasiSidang.php?id=<?= $id_sidang ?>">
                        <span class="fw-semibold NavSide__sidebar-title">Evaluasi</span>
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
                    <a href="dNilaiAkhir.php?id_sidang=<?= $id_sidang ?>">
                        <span class="fw-semibold NavSide__sidebar-title">Nilai Akhir</span>
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

        <div class="NavSide__toggle">
            <i class="bi bi-list open"></i>
            <i class="bi bi-x-lg close"></i>
        </div>
        
        <div id="page-content-wrapper">
            <div class="NavSide__topbar"></div>
            <main class="NavSide__main-content">
                <h2 class="text-heading text-black" style="font-weight: 700;">Detail Sidang - <?= htmlspecialchars($judul) ?></h2>
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
                                    <?php echo implode('<br>', array_map('htmlspecialchars', $dosenPembimbing)); ?>
                                </div>
                            </div>

                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-user-group"></i><span class="fw-bold">Dosen Penguji</span></div>
                                <div class="value-row">
                                    <?php echo !empty($dosenPenguji) ? implode('<br>', array_map('htmlspecialchars', $dosenPenguji)) : 'Belum ditentukan'; ?>
                                </div>
                            </div>

                        <?php elseif (!empty($dosen_pengampu)): ?>
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-book"></i><span class="fw-bold">Mata Kuliah</span></div>
                                <div class="value-row"><?php echo !empty($judul) ? htmlspecialchars($judul) : 'N/A'; ?></div>
                            </div>

                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-user-group"></i><span class="fw-bold">Dosen Pengampu</span></div>
                                <div class="value-row">
                                    <?php echo implode('<br>', array_map('htmlspecialchars', $dosen_pengampu)); ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="info-item">Data sidang tidak lengkap atau tidak dikenali.</p>
                        <?php endif; ?>
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

                <h3>Dokumen Revisi</h3>
                <div class="file-buttons-container d-flex flex-wrap">
                    <?php if (!empty($data_revisi['dok_revisi'])): ?>
                        <a href="../../uploadtesting/<?= $namaFileRevisi ?>" class="file-button" download>
                            <i class="fa-solid fa-file-zipper"></i>
                            <?= htmlspecialchars(basename($namaFileRevisi)) ?>
                        </a>
                    <?php else: ?>
                        <p class="text-muted">Belum ada dokumen revisi yang diunggah oleh mahasiswa.</p>
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
    </div>
    <!-- Modal Konfirmasi -->
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
        // --- Sidebar Toggle Logic ---
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

            // Penting: Hapus event listener lama untuk menghindari eksekusi ganda
            const newConfirmButton = confirmButton.cloneNode(true);
            confirmButton.parentNode.replaceChild(newConfirmButton, confirmButton);

            newConfirmButton.addEventListener('click', async function() { // Gunakan async
                confirmationModal.hide();

                // Beri jeda sedikit agar modal sempat tertutup
                await new Promise(resolve => setTimeout(resolve, 300));

                if (action === 'Ditolak') {
                    const {
                        value: catatan,
                        isConfirmed
                    } = await Swal.fire({
                        title: 'Alasan Penolakan',
                        input: 'textarea',
                        inputLabel: 'Catatan:',
                        inputPlaceholder: 'Masukan catatan penolakan di sini...',
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
                    });

                    if (isConfirmed && catatan) {
                        // Di sini Anda bisa menambahkan logika fetch untuk mengirim data penolakan ke server
                        console.log('Catatan Penolakan:', catatan); // Untuk saat ini kita log saja

                        await Swal.fire({
                            title: 'Berhasil!',
                            text: 'Dokumen revisi telah ditolak dan catatan telah disimpan.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                        // Redirect ke daftar sidang setelah menolak
                        window.location.href = 'dDaftarSidang.php';
                    }

                } else { // Jika aksi adalah 'Disetujui'
                    try {
                        const postData = new URLSearchParams({
                            approve: true,
                            id_sidang: <?= $id_sidang ?>
                        });

                        const response = await fetch(window.location.href, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: postData
                        });

                        // Periksa apakah respons adalah JSON yang valid
                        const contentType = response.headers.get("content-type");
                        if (!response.ok || !contentType || !contentType.includes("application/json")) {
                            const errorText = await response.text();
                            throw new Error(`Server memberikan respon yang tidak valid. Isi respon: \n${errorText}`);
                        }

                        const result = await response.json();

                        if (result.status === 'success') {
                            await Swal.fire({
                                title: 'Berhasil!',
                                text: result.message,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#4B68FB'
                            });
                            // Redirect ke URL yang diberikan oleh server
                            window.location.href = result.redirectUrl;
                        } else {
                            // Tampilkan pesan error spesifik dari server
                            Swal.fire({
                                title: 'Gagal!',
                                text: result.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    } catch (error) {
                        console.error("Terjadi error saat proses persetujuan:", error);
                        Swal.fire({
                            title: 'Error Teknis!',
                            text: 'Tidak dapat terhubung ke server atau terjadi kesalahan. Silakan cek konsol untuk detail.',
                            icon: 'error'
                        });
                    }
                }
            });
            confirmationModal.show();
        }
    </script>
</body>

</html>