<?php

session_start();

require "../../koneksi.php";

$id_sidang = null;

if (isset($_SESSION['selected_sidang_id']) && !empty($_SESSION['selected_sidang_id'])) {
    $id_sidang = $_SESSION['selected_sidang_id'];
} else {
    header("Location: mSidang.php");
    exit();
}

$query = "SELECT s.id_sidang, s.judul, m.nama_matkul, 
          STRING_AGG(d.nama_dosen, ', ') WITHIN GROUP (ORDER BY d.nama_dosen) AS dosen_pengampu, 
          j.ruang_sidang, j.jam_sidang, j.tanggal_sidang 
          FROM Sidang s
          JOIN Detail_Sidang ds ON ds.id_sidang = s.id_sidang 
          JOIN MataKuliah m ON ds.id_matkul = m.id_matkul 
          JOIN Dosen d ON ds.nomor_dosen = d.nomor_dosen 
          JOIN Jadwal j ON j.id_sidang = s.id_sidang 
          WHERE s.id_sidang = ?
          GROUP BY s.id_sidang, s.judul, m.nama_matkul, j.ruang_sidang, j.jam_sidang, j.tanggal_sidang";

$stmt = sqlsrv_prepare($conn, $query, array(&$id_sidang));

if ($stmt === false) {
    echo "Terjadi kesalahan saat mempersiapkan query:<br>";
    if (($errors = sqlsrv_errors()) != null) {
        foreach ($errors as $error) {
            echo "SQLSTATE: " . $error['SQLSTATE'] . "<br>";
            echo "Code: " . $error['code'] . "<br>";
            echo "Message: " . $error['message'] . "<br>";
        }
    }
    exit();
}

if (!sqlsrv_execute($stmt)) {
    echo "Terjadi kesalahan saat mengeksekusi query:<br>";
    if (($errors = sqlsrv_errors()) != null) {
        foreach ($errors as $error) {
            echo "SQLSTATE: " . $error['SQLSTATE'] . "<br>";
            echo "Code: " . $error['code'] . "<br>";
            echo "Message: " . $error['message'] . "<br>";
        }
    }
    exit();
}

$sidang_data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($sidang_data === null) {
    echo "Detail sidang tidak ditemukan.";
    // Mungkin redirect kembali ke daftar jika tidak ditemukan
    header("Location: mSidang.php");
    exit();
}

// Konversi format tanggal jika perlu
$tanggal_sidang_formatted = null;
if ($sidang_data['tanggal_sidang'] instanceof DateTime) {
    $tanggal_sidang_formatted = $sidang_data['tanggal_sidang']->format('d F Y');
}
// Konversi format jam jika perlu
$jam_sidang_formatted = null;
if ($sidang_data['jam_sidang'] instanceof DateTime) {
    $jam_sidang_formatted = $sidang_data['jam_sidang']->format('H.i');
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
        <!-- Sidebar -->
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a onclick="location.href='mdetailSidang.php'">
                        <span class="NavSide__sidebar-title fw-semibold">Detail Pengajuan</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                   <a onclick="location.href='mPerbaikan.php'">
                        <span class="NavSide__sidebar-title fw-semibold">Perbaikan</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a onclick="location.href='mNilaiakhir.php'">
                        <span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Sidebar toggle button, visible and functional on mobile -->
        <div class="NavSide__toggle">
            <i class="bi bi-list open"></i>
            <i class="bi bi-x-lg close"></i>
        </div>

        <!-- Wrapper for main page content -->
        <div id="page-content-wrapper">
            <!-- Full-width topbar visible on mobile -->
            <div class="NavSide__topbar"></div>

            <main class="NavSide__main-content">
                <h2>Detail Sidang - Sistem Pengajuan Sidang</h2>
                <!-- Application status badge -->
                <div class="status-badge" id="statusBadge">Status Pengajuan : Belum Disetujui</div>
                
                <div class="info-card">
                    <div class="section">
                        <!-- Course Title -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-book"></i>
                                <span class="fw-bold">Judul Mata Kuliah</span>
                            </div>
                            <div class="value-row">
                                <?= htmlspecialchars($sidang_data['judul']) ?>
                            </div>
                        </div>

                        <!-- Spacer to push Lecturer info down -->
                        <div class="spacer"></div>

                        <!-- Lecturers -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-user-group"></i> 
                                <span class="fw-bold">Dosen Pengampu</span>
                            </div>
                            <div class="value-row">
                                <?= htmlspecialchars($sidang_data['dosen_pengampu']) ?>
                            </div>
                        </div>
                    </div>
                    <div class="section">
                        <!-- Room -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-door-open"></i>
                                <span class="fw-bold">Ruangan</span>
                            </div>
                            <div class="value-row">
                                <?= htmlspecialchars($sidang_data['ruang_sidang']) ?>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="info-group">
                            <div class="label-row">
                                <i class="fa-solid fa-calendar-days"></i>
                                <span class="fw-bold">Tanggal</span>
                            </div>
                            <div class="value-row">
                                <?= htmlspecialchars($tanggal_sidang_formatted) ?>
                            </div>
                        </div>

                        <!-- Time -->
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