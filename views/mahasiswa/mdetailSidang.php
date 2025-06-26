<?php

session_start();

require "../../koneksi/koneksiAndrew.php"; // Adjust this path if your koneksi file is elsewhere

// TINGKATKAN LAPORAN ERROR UNTUK DEBUGGING - Hapus baris ini setelah selesai debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$id_sidang = null;

// Get $id_sidang from session only
if (isset($_SESSION['selected_sidang_id']) && !empty($_SESSION['selected_sidang_id'])) {
    $id_sidang = $_SESSION['selected_sidang_id'];
} else {
    // Redirect if no sidang ID is selected
    header("Location: mSidang.php");
    exit();
}

// Initialize all variables to be used
$data_sidang = [];
$data_jadwal = [];
$nama_prodi = 'N/A';
$dosen_pembimbing = 'N/A';
$dosen_penguji = [];
$data_matkul = null;
$dosen_pengampu = [];
$dok_laporan = null; // Initialize for document content
$status_ajuan = null; // Initialize for status

// Main query to get Sidang details, including document and status
$sql_utama = "SELECT
                s.id_sidang,
                s.judul,
                CAST(s.jenis_sidang AS INT) AS jenis_sidang,
                s.id_kelompok,
                s.dok_laporan,    -- Fetch the document content (VARBINARY(MAX))
                s.status_ajuan    -- Fetch the submission status
              FROM Sidang s
              WHERE s.id_sidang = ?";

$stmt_utama = sqlsrv_prepare($conn, $sql_utama, array(&$id_sidang));

if ($stmt_utama === false) {
    die("Terjadi kesalahan saat mempersiapkan query utama: " . print_r(sqlsrv_errors(), true));
}
if (!sqlsrv_execute($stmt_utama)) {
    die("Terjadi kesalahan saat mengeksekusi query utama: " . print_r(sqlsrv_errors(), true));
}

$data_sidang = sqlsrv_fetch_array($stmt_utama, SQLSRV_FETCH_ASSOC);

if (!$data_sidang) {
    echo "Detail sidang tidak ditemukan untuk ID: " . htmlspecialchars($id_sidang) . ".";
    header("Location: mSidang.php"); // Redirect if sidang not found
    exit();
}

// Assign fetched values for document and status
$dok_laporan = $data_sidang['dok_laporan'] ?? null;
$status_ajuan = $data_sidang['status_ajuan'] ?? null;

// --- PHP Logic for Status Badge ---
$status_text = '';
$status_class = '';
if ($status_ajuan === 0) {
    $status_text = 'Status Pengajuan : Belum Disetujui';
    $status_class = 'belum-disetujui';
} elseif ($status_ajuan === 1) {
    $status_text = 'Status Pengajuan : Disetujui';
    $status_class = 'disetujui';
} else {
    $status_text = 'Status Pengajuan : Tidak Diketahui'; // Fallback for unexpected values
    $status_class = '';
}
// --- End PHP Logic for Status Badge ---


// Fetch Jadwal (Schedule) details
$sql_jadwal = "SELECT ruang_sidang, tanggal_sidang, jam_sidang, jam_selesai FROM Jadwal WHERE id_sidang = ?";
$stmt_jadwal = sqlsrv_query($conn, $sql_jadwal, array($id_sidang));
if ($stmt_jadwal === false) {
    error_log("Error fetching jadwal: " . print_r(sqlsrv_errors(), true));
} else {
    $data_jadwal = sqlsrv_fetch_array($stmt_jadwal, SQLSRV_FETCH_ASSOC);
    if (!$data_jadwal) { $data_jadwal = []; }
}

// Format date and time for display
$tanggal_sidang_formatted = 'Belum Dijadwalkan';
if (isset($data_jadwal['tanggal_sidang']) && $data_jadwal['tanggal_sidang'] instanceof DateTime) {
    setlocale(LC_TIME, 'id_ID.utf8'); // Set locale for Indonesian date formatting
    $tanggal_sidang_formatted = $data_jadwal['tanggal_sidang']->format('l, d F Y');
}

$jam_sidang_formatted = 'Belum Dijadwalkan';
if (isset($data_jadwal['jam_sidang']) && $data_jadwal['jam_sidang'] instanceof DateTime) {
    $jam_sidang_formatted = $data_jadwal['jam_sidang']->format('H.i');
    if (isset($data_jadwal['jam_selesai']) && $data_jadwal['jam_selesai'] instanceof DateTime) {
        $jam_sidang_formatted .= ' - ' . $data_jadwal['jam_selesai']->format('H.i');
    }
}

$jenis_sidang = $data_sidang['jenis_sidang'];
$id_kelompok = $data_sidang['id_kelompok'];

// Fetch Prodi (Study Program) name
if (!empty($id_kelompok)) {
    $sql_prodi = "SELECT m.prodi FROM Mahasiswa m JOIN Kelompok_Mahasiswa km ON m.nim = km.nim WHERE km.id_kelompok = ? AND m.prodi IS NOT NULL";
    $stmt_prodi = sqlsrv_query($conn, $sql_prodi, array($id_kelompok));
    if ($stmt_prodi && $row_prodi = sqlsrv_fetch_array($stmt_prodi, SQLSRV_FETCH_ASSOC)) {
        $nama_prodi = $row_prodi['prodi'];
    } else {
        error_log("Error fetching prodi: " . print_r(sqlsrv_errors(), true));
    }
}

// Fetch Dosen details based on jenis_sidang
if ($jenis_sidang === 0) { // If it's a "Tugas Akhir" (TA) sidang
    $sql_pembimbing = "SELECT d.nama_dosen FROM Dosen d JOIN Bimbingan b ON d.nomor_dosen = b.nomor_dosen WHERE b.id_kelompok = ?";
    $stmt_pembimbing = sqlsrv_query($conn, $sql_pembimbing, array($id_kelompok));
    if ($stmt_pembimbing) {
        $pembimbing_row = sqlsrv_fetch_array($stmt_pembimbing, SQLSRV_FETCH_ASSOC);
        if ($pembimbing_row) {
            $dosen_pembimbing = $pembimbing_row['nama_dosen'];
        }
    } else {
        error_log("Error fetching pembimbing: " . print_r(sqlsrv_errors(), true));
    }

    $sql_penguji = "SELECT d.nama_dosen FROM Dosen d JOIN Penjadwalan p ON d.nomor_dosen = p.nomor_dosen WHERE p.id_sidang = ? AND p.peran_dosen = 0"; // Peran 0 for examiner
    $stmt_penguji = sqlsrv_query($conn, $sql_penguji, array($id_sidang));
    if ($stmt_penguji) {
        while ($row = sqlsrv_fetch_array($stmt_penguji, SQLSRV_FETCH_ASSOC)) {
            $dosen_penguji[] = $row['nama_dosen'];
        }
    } else {
        error_log("Error fetching penguji: " . print_r(sqlsrv_errors(), true));
    }

} elseif ($jenis_sidang === 1) { // If it's a "Semester" (Mata Kuliah) sidang
    $sql_matkul = "SELECT TOP 1 mk.nama_matkul, mk.id_matkul FROM MataKuliah mk
                    JOIN Detail_Sidang ds ON mk.id_matkul = ds.id_matkul
                    WHERE ds.id_sidang = ?";
    $stmt_matkul = sqlsrv_query($conn, $sql_matkul, array($id_sidang));
    if ($stmt_matkul) {
        $data_matkul = sqlsrv_fetch_array($stmt_matkul, SQLSRV_FETCH_ASSOC);
        if ($data_matkul) {
            $id_matkul = $data_matkul['id_matkul'];

            $sql_pengampu = "SELECT d.nama_dosen FROM Dosen d JOIN Pengampu_Kelas pk ON d.nomor_dosen = pk.nomor_dosen WHERE pk.id_matkul = ?";
            $stmt_pengampu = sqlsrv_query($conn, $sql_pengampu, array($id_matkul));
            if ($stmt_pengampu) {
                while ($row = sqlsrv_fetch_array($stmt_pengampu, SQLSRV_FETCH_ASSOC)) {
                    $dosen_pengampu[] = $row['nama_dosen'];
                }
            } else {
                error_log("Error fetching pengampu: " . print_r(sqlsrv_errors(), true));
            }
        }
    } else {
        error_log("Error fetching matkul: " . print_r(sqlsrv_errors(), true));
    }
}

sqlsrv_close($conn); // Close the database connection
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
                        <?php if ((int)$data_sidang['jenis_sidang'] === 1): // Info-card TOP: displayed if jenis_sidang == 1 (Semester) ?>
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
                        <?php elseif ((int)$data_sidang['jenis_sidang'] === 0): // Info-card BOTTOM: displayed if jenis_sidang == 0 (TA) ?>
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
      // Script for sidebar toggle functionality
      let menuToggle = document.querySelector(".NavSide__toggle");
      let sidebar = document.getElementById("main-sidebar");

      if (menuToggle && sidebar) {
        menuToggle.onclick = function () {
          menuToggle.classList.toggle("NavSide__toggle--active");
          sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
      }

      // Script for marking active menu item
      let menuItems = document.querySelectorAll(".NavSide__sidebar-item");
      if (menuItems.length > 0) {
        menuItems.forEach(item => {
          item.onclick = function (event) {
            // Remove 'active' class from all items
            menuItems.forEach(innerItem => {
              innerItem.classList.remove("NavSide__sidebar-item--active");
            });
            // Add 'active' class to the clicked item
            this.classList.add("NavSide__sidebar-item--active");
          };
        });
      }

      // The status badge state is now controlled by PHP, so no client-side click toggle needed here
    </script>
</body>
</html>