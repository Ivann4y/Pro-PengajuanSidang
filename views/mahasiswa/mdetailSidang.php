<?php

session_start();

require "../../koneksi/koneksiAndrew.php";

// TINGKATKAN LAPORAN ERROR UNTUK DEBUGGING - Hapus baris ini setelah selesai debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$id_sidang = null;

// Ambil $id_sidang hanya dari session
if (isset($_SESSION['selected_sidang_id']) && !empty($_SESSION['selected_sidang_id'])) {
    $id_sidang = $_SESSION['selected_sidang_id'];
} else {
    header("Location: mSidang.php");
    exit();
}

// Inisialisasi semua variabel yang akan digunakan
$data_sidang = [];
$data_jadwal = [];
$nama_prodi = 'N/A';
$dosen_pembimbing = 'N/A';
$dosen_penguji = []; // Array karena bisa banyak penguji
$data_matkul = null; // Akan menyimpan array jika ada, null jika tidak
$dosen_pengampu = []; // Array karena bisa banyak pengampu

// 1. Query utama untuk mendapatkan informasi dasar sidang dan jenis sidang
// Hanya join dengan Jadwal, tidak dengan Detail_Sidang, MataKuliah, Dosen, Penjadwalan di sini
$sql_utama = "SELECT
                s.id_sidang,
                s.judul,
                CAST(s.jenis_sidang AS INT) AS jenis_sidang, -- Pastikan jenis_sidang diambil sebagai INT
                s.id_kelompok -- Pastikan id_kelompok juga diambil
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
    header("Location: mSidang.php");
    exit();
}

// 2. Query terpisah untuk Jadwal (lebih aman dan bersih)
$sql_jadwal = "SELECT ruang_sidang, tanggal_sidang, jam_sidang, jam_selesai FROM Jadwal WHERE id_sidang = ?";
$stmt_jadwal = sqlsrv_query($conn, $sql_jadwal, array($id_sidang));
if ($stmt_jadwal === false) {
    error_log("Error fetching jadwal: " . print_r(sqlsrv_errors(), true));
} else {
    $data_jadwal = sqlsrv_fetch_array($stmt_jadwal, SQLSRV_FETCH_ASSOC);
    if (!$data_jadwal) { $data_jadwal = []; } // Pastikan array kosong jika tidak ada jadwal
}

// Konversi format tanggal dan jam untuk tampilan
$tanggal_sidang_formatted = 'Belum Dijadwalkan';
if (isset($data_jadwal['tanggal_sidang']) && $data_jadwal['tanggal_sidang'] instanceof DateTime) {
    setlocale(LC_TIME, 'id_ID.utf8'); // Pastikan locale Indonesia untuk nama hari/bulan
    $tanggal_sidang_formatted = $data_jadwal['tanggal_sidang']->format('l, d F Y');
}

$jam_sidang_formatted = 'Belum Dijadwalkan';
if (isset($data_jadwal['jam_sidang']) && $data_jadwal['jam_sidang'] instanceof DateTime) {
    $jam_sidang_formatted = $data_jadwal['jam_sidang']->format('H.i');
    if (isset($data_jadwal['jam_selesai']) && $data_jadwal['jam_selesai'] instanceof DateTime) {
        $jam_sidang_formatted .= ' - ' . $data_jadwal['jam_selesai']->format('H.i');
    }
}

// --- Logika pengambilan data spesifik berdasarkan jenis_sidang ---
$jenis_sidang = $data_sidang['jenis_sidang'];
$id_kelompok = $data_sidang['id_kelompok'];

// Query untuk nama prodi (umum untuk semua jenis sidang yang terkait kelompok)
if (!empty($id_kelompok)) {
    $sql_prodi = "SELECT m.prodi FROM Mahasiswa m JOIN Kelompok_Mahasiswa km ON m.nim = km.nim WHERE km.id_kelompok = ? AND m.prodi IS NOT NULL";
    $stmt_prodi = sqlsrv_query($conn, $sql_prodi, array($id_kelompok));
    if ($stmt_prodi && $row_prodi = sqlsrv_fetch_array($stmt_prodi, SQLSRV_FETCH_ASSOC)) {
        $nama_prodi = $row_prodi['prodi'];
    } else {
        error_log("Error fetching prodi: " . print_r(sqlsrv_errors(), true));
    }
}


if ($jenis_sidang === 0) { // Asumsi 0 = Sidang Tugas Akhir (TA)
    // Ambil Dosen Pembimbing (asumsi satu pembimbing per kelompok)
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

    // Ambil Dosen Penguji (bisa lebih dari satu)
    $sql_penguji = "SELECT d.nama_dosen FROM Dosen d JOIN Penjadwalan p ON d.nomor_dosen = p.nomor_dosen WHERE p.id_sidang = ? AND p.peran_dosen = 0"; // Peran 0 untuk penguji
    $stmt_penguji = sqlsrv_query($conn, $sql_penguji, array($id_sidang));
    if ($stmt_penguji) {
        while ($row = sqlsrv_fetch_array($stmt_penguji, SQLSRV_FETCH_ASSOC)) {
            $dosen_penguji[] = $row['nama_dosen'];
        }
    } else {
        error_log("Error fetching penguji: " . print_r(sqlsrv_errors(), true));
    }

} elseif ($jenis_sidang === 1) { // Asumsi 1 = Sidang Semester
    // Ambil Mata Kuliah
    $sql_matkul = "SELECT TOP 1 mk.nama_matkul, mk.id_matkul FROM MataKuliah mk
                   JOIN Detail_Sidang ds ON mk.id_matkul = ds.id_matkul
                   WHERE ds.id_sidang = ?";
    $stmt_matkul = sqlsrv_query($conn, $sql_matkul, array($id_sidang));
    if ($stmt_matkul) {
        $data_matkul = sqlsrv_fetch_array($stmt_matkul, SQLSRV_FETCH_ASSOC);
        if ($data_matkul) {
            $id_matkul = $data_matkul['id_matkul'];

            // Ambil Dosen Pengampu (bisa lebih dari satu)
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

sqlsrv_close($conn);
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
                    <a href="mPerbaikan.php?id_sidang=<?= $id_sidang ?>">
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
                <!-- <p class="page-nama">Kelompok <?php echo htmlspecialchars($data_sidang['id_kelompok'] ?? 'N/A'); ?></p> -->

                <div class="status-badge" id="statusBadge">Status Pengajuan : Belum Disetujui</div>
                
                <div class="info-card">
                    <div class="section">
                        <?php if ((int)$data_sidang['jenis_sidang'] === 1): // Info-card ATAS: tampil jika jenis_sidang == 1 (Semester) ?>
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
                        <?php elseif ((int)$data_sidang['jenis_sidang'] === 0): // Info-card BAWAH: tampil jika jenis_sidang == 0 (TA) ?>
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
                    <a href="#" class="file-button">
                        <i class="fa-solid fa-file-pdf"></i>
                        file_laporan_kel-1.pdf
                    </a>
                    <a href="#" class="file-button">
                        <i class="fa-solid fa-file-zipper"></i>
                        dokumen_pendukung_kel-1.zip
                    </a>
                </div>
                
               <button type="button" class="btn-kembali" onclick="location.href='mSidang.php'">
                    <span class="icon-circle">
                        <i class="fa-solid fa-arrow-left"></i>
                    </span>
                    Kembali
                </button>
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
            menuItems.forEach(innerItem => {
              innerItem.classList.remove("NavSide__sidebar-item--active");
            });
            this.classList.add("NavSide__sidebar-item--active");
          };
        });
      }

      // Functionality: Change status badge on click
      const statusBadge = document.getElementById('statusBadge');

      if (statusBadge) {
          statusBadge.addEventListener('click', function() {
              // Check if the badge currently says "Belum Disetujui"
              if (this.textContent.includes('Belum Disetujui')) {
                  this.textContent = 'Status Pengajuan : Disetujui';
                  this.classList.add('approved'); // Add 'approved' class
              } else {
                  // If it says "Disetujui", revert to "Belum Disetujui"
                  this.textContent = 'Status Pengajuan : Belum Disetujui';
                  this.classList.remove('approved'); // Remove 'approved' class
              }
          });
      }
    </script>
</body>
</html>