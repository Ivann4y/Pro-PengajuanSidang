<?php
// Mulai sesi dan cek apakah user adalah admin
session_start();
if ($_SESSION['role'] !== 'admin') {
    // Jika bukan admin, redirect ke halaman login
    header("Location: ../../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <!-- Responsive viewport untuk mobile -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Nama penulis halaman -->
    <meta name="author" content="JaisyuNurM-AliansiSidang_Kelompok5" />
    <title>Dashboard Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/style.css" />
    <link rel="stylesheet" href="../../extra/style.css">

    <style>
        /* =======================
           Dashboard Card Styles
        ======================== */
        .dashboardTitle {
            color: #4B68FB;
            font-size: 1.5rem;
            font-weight: 500;
        }


        .penjadwalan-status-card {
            background-color: #4B68FB;
            color: white;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            transition: transform 0.3s ease, box-shadow 0.3s ease;

        }

        .penjadwalan-status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
        }

        .penjadwalan-status-card .number {
            font-size: 4.8rem;
            font-weight: 700;
            line-height: 1;
            margin-right: 1.2rem;
            min-width: 50px;
            text-align: center;
        }

        .penjadwalan-status-card .text {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .penjadwalan-status-card .text .title {
            font-size: 0.95rem;
            font-weight: 500;
            display: block;
            margin-bottom: 0.1rem;
        }

        .penjadwalan-status-card .text .description {
            font-size: 1.05rem;
            font-weight: 600;
        }

        .pengajuan-status-card {
            background-color: rgb(239, 239, 239);
            display: flex;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .pengajuan-status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .pengajuan-status-card .number {
            font-size: 4.8rem;
            font-weight: 700;
            line-height: 1;
            margin-right: 1.2rem;
            color: rgb(37, 44, 54);
            min-width: 50px;
            text-align: center;
        }

        .pengajuan-status-card .text {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .pengajuan-status-card .text .title {
            font-size: 0.95rem;
            font-weight: 500;
            color: #4B5563;
            display: block;
            margin-bottom: 0.1rem;
        }

        .pengajuan-status-card .text .description {
            font-size: 1.05rem;
            font-weight: 600;
            color: #1F2937;
        }

        .content-card {
            background-color: #F3F4F6;
        }

        .content-card .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 1rem;
        }

        /* --- Notifikasi Card Styles --- */
        .notifikasi-card {
            overflow-y: auto;
            max-height: 35vh;
            padding-top: 0rem;
            padding-bottom: 1rem;
        }

        .notifikasi-card .section-title {
            position: sticky;
            top: 0;
            background-color: #F3F4F6;
            z-index: 10;
            padding-top: 0.3rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #DEE2E6;
            margin-top: 0;
            margin-bottom: 0;
        }


        .notifikasi-card .notifikasi-item {
            background-color: white;
            color: #4B5563;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .notifikasi-card .notifikasi-item:last-child {
            margin-bottom: 0;
        }

        /* --- Tanggungan Card Styles --- */
        .tanggungan-card {
            overflow-y: auto;
            max-height: 37.5vh;
            padding-top: 0rem;
            padding-bottom: 1rem;
        }

        .tanggungan-card .section-title {
            position: sticky;
            top: 0;
            background-color: #F3F4F6;
            z-index: 10;
            padding-top: 0.3rem;
            padding-bottom: 0.5rem;
            margin-top: 0;
            margin-bottom: 0;
            border-bottom: 1px solid #DEE2E6;
        }

        .tanggungan-card .tanggungan-item {
            background-color: white;
            color: #374151;
            padding: 0.8rem 1.1rem;
            border-radius: 10px;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .tanggungan-card .tanggungan-item:last-child {
            margin-bottom: 0;
        }

        .calendar-card {
            background-color: #4B68FB;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 1rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            min-height: 300px;
        }

        .calendar-card .section-title-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 0.5rem;
        }

        .calendar-card .section-title {
            color: white;
            margin-bottom: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .calendar-card .calendar-nav i {
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0 0.5rem;
            color: #C7D2FE;
        }

        .calendar-card .calendar-nav i:hover {
            color: white;
        }

        .calendar-card .calendar {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem;
            flex-grow: 1;
        }

        .calendar-card .calendar th {
            padding: 0.3rem 0.25rem;
            text-align: center;
            font-weight: 500;
            font-size: 0.75rem;
            color: #C7D2FE;
            text-transform: uppercase;
        }

        .calendar-card .calendar td {

            padding: 0.1rem;

            text-align: center;
            vertical-align: middle;
        }


        .calendar-card .calendar-day {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            line-height: 32px;
            border-radius: 50%;
            font-size: 0.8rem;
            font-weight: 500;
            margin: 0 auto;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .calendar-card .calendar-day.current-day {
            background-color: white;
            color: #4B68FB;
            font-weight: 700;
        }

        .calendar-card .calendar-day:hover:not(.current-day) {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .calendar-card .calendar-day:hover:not(.current-day) {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .calendar-card .calendar tbody,
        .calendar-card .calendar tr {
            height: 100%;
        }

        .calendar-card .calendar td {
            height: calc(100% / 6);
        }


        .sidang-mendatang-card {
            overflow-y: auto;
            max-height: 36vh;
            padding-top: 0rem;
            padding-bottom: 1rem;
        }

        .sidang-mendatang-card .section-title {
            position: sticky;
            top: 0;
            background-color: #F3F4F6;
            z-index: 10;
            padding-top: 0.3rem;
            padding-bottom: 0.5rem;
            margin-top: 0;
            margin-bottom: 0;
            border-bottom: 1px solid #DEE2E6;
        }

        .sidang-mendatang-card .item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #E0E7FF;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            margin-bottom: 0.75rem;
        }

        .sidang-mendatang-card .item:last-child {
            margin-bottom: 0;
        }

        .sidang-mendatang-card .date-bubble {
            background-color: white;
            border-radius: 8px;
            padding: 0.4rem 0rem;
            text-align: center;
            margin-right: 1rem;
            min-width: 48px;
            height: 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .sidang-mendatang-card .date-bubble .day {
            font-size: 1.1rem;
            font-weight: 700;
            color: #4B68FB;
            line-height: 1.1;
        }

        .sidang-mendatang-card .date-bubble .month {
            font-size: 0.7rem;
            color: #6B7280;
            line-height: 1;
            text-transform: uppercase;
        }

        .sidang-mendatang-card .info {
            flex-grow: 1;
            font-size: 0.9rem;
            font-weight: 500;
            color: #374151;
        }

        .sidang-mendatang-card .arrow i {
            font-size: 1.2rem;
            color: #4B68FB;
        }
    </style>
</head>

<body>
    <div id="NavSide">
        <!-- Sidebar Navigasi -->
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
            </div>
            <ul class="NavSide__sidebar-nav">
                <!-- Menu aktif -->
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">       
                    <a href="aBeranda.php">
                        <span class="NavSide__sidebar-title fw-semibold">Beranda</span>
                    </a>
                </li>
                <!-- Menu lain -->
                <li class="NavSide__sidebar-item">
                    <a href="aPenjadwalan.php">
                        <span class="NavSide__sidebar-title fw-semibold">Penjadwalan</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <a href="aDaftarSidang.php">
                        <span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span>
                    </a>
                </li>
                <!-- Tombol keluar, memunculkan modal konfirmasi -->
                <li class="NavSide__sidebar-item">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#logABeranda">
                        <span class="NavSide__sidebar-title fw-semibold">Keluar</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Topbar untuk mobile -->
        <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <div class="header-icons">
                <!-- Notifikasi -->
                <a href="aNotifikasi.php" title="Notifikasi" style="text-decoration: none; color: inherit;">
                    <i class="bi bi-bell-fill"></i>
                </a>
                <!-- Profil -->
                <div class="profile-icon">
                    <a href="aProfil.php" title="Profil" style="text-decoration: none; color: inherit;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="NavSide__main-content">
            <div class="dashboard-header">
                <h2 class="page-title">Dashboard Admin</h2>
                <div class="header-icons d-none d-md-flex">
                    <a href="aNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
                    <div class="profile-icon">
                        <a href="aProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
                    </div>
                </div>
            </div>

            <h2 class="welcomeText">Selamat Datang, Admin!</h2>

            <div class="row">
                <!-- Statistik Penjadwalan & Pengajuan -->
                <div class="col-lg-7">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Kartu Penjadwalan -->
                            <a href="aPenjadwalan.php" style="text-decoration: none; color: inherit; display: block;">
                                <div class="dashboard-card penjadwalan-status-card">
                                    <div class="number">4</div>
                                    <div class="text">
                                        <span class="title">Penjadwalan</span>
                                        <span class="description">Menunggu Dijadwalkan</span>
                                    </div>
                                </div>
                            </a>
                            <!-- Kartu Pengajuan -->
                            <a href="aPenjadwalan.php" style="text-decoration: none; color: inherit; display: block;">
                                <div class="dashboard-card pengajuan-status-card">
                                    <div class="number">2</div>
                                    <div class="text">
                                        <span class="title">Pengajuan</span>
                                        <span class="description">Menunggu Persetujuan</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <!-- Kartu Tanggungan -->
                            <div class="dashboard-card content-card tanggungan-card">
                                <h3 class="section-title">Tanggungan</h3>
                                <div class="tanggungan-item">Belum terjadwal Sidang PRG</div>
                                <div class="tanggungan-item">Belum terjadwal Sidang Basdat</div>
                                <div class="tanggungan-item">Belum terjadwal Sidang TA</div>
                                <div class="tanggungan-item">Belum terjadwal Sidang Orkom</div>
                                <div class="tanggungan-item">Belum terjadwal Sidang </div>
                            </div>
                        </div>
                    </div>
                    <!-- Kartu Notifikasi -->
                    <a href="aNotifikasi.php" style="text-decoration: none; color: inherit; display: block;">
                        <div class="dashboard-card content-card notifikasi-card">
                            <h3 class="section-title">Notifikasi</h3>
                            <div class="notifikasi-item">Sidang PRG Telah Disetujui oleh Dosen Pembimbing</div>
                            <div class="notifikasi-item">Revisi Sidang BasDat Telah Selesai Dinilai</div>
                            <div class="notifikasi-item">Sidang TA Nayaka Ivana Putra telah selesai dinilai</div>
                        </div>
                    </a>
                </div>

                <!-- Kalender dan Sidang Mendatang -->
                <div class="col-lg-5">
                    <!-- Kalender -->
                    <div class="dashboard-card calendar-card">
                        <div class="section-title-container">
                            <h3 class="section-title" id="currentMonthYear"></h3>
                            <div class="calendar-nav">
                                <i class="bi bi-chevron-left" id="prevMonth"></i>
                                <i class="bi bi-chevron-right" id="nextMonth"></i>
                            </div>
                        </div>
                        <table class="calendar" id="calendarTable">
                            <thead>
                                <tr>
                                    <th>Min</th>
                                    <th>Sen</th>
                                    <th>Sel</th>
                                    <th>Rab</th>
                                    <th>Kam</th>
                                    <th>Jum</th>
                                    <th>Sab</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Kalender akan diisi oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <!-- Sidang Mendatang -->
                    <div class="dashboard-card content-card sidang-mendatang-card">
                        <h3 class="section-title">Sidang Mendatang</h3>
                        <!-- Daftar sidang mendatang, setiap item berisi tanggal, bulan, info, dan ikon -->
                        <div class="item">
                            <div class="date-bubble">
                                <span class="day">02</span>
                                <span class="month">Jun</span>
                            </div>
                            <span class="info">Sistem Pengajuan Skripsi</span>
                            <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                        </div>
                        <!-- ...item lain... -->
                    </div>
                </div>
            </div>
        </main>
    </div>
  
    <!-- Modal keluar (konfirmasi logout) -->
    <div class="modal fade" id="logABeranda" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-heading-color">
                    <div class="modal-header">
                        <h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1>
                    </div>
                </div>
                <div class="modal-body mx-auto">
                    Apakah anda yakin ingin keluar?
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle (termasuk Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // =========================
        // Sidebar Toggle Logic
        // =========================
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        // Toggle sidebar untuk mobile
        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };

        // =========================
        // Kalender Interaktif
        // =========================

        // Ambil elemen kalender
        const calendarTableBody = document.querySelector("#calendarTable tbody");
        const currentMonthYearHeader = document.getElementById("currentMonthYear");
        const prevMonthBtn = document.getElementById("prevMonth");
        const nextMonthBtn = document.getElementById("nextMonth");

        // Tanggal hari ini dan tanggal aktif
        let currentDate = new Date();
        let activeDate = new Date();

        // Nama-nama bulan dalam bahasa Indonesia
        const monthNames = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];

        // Fungsi untuk merender kalender
        function renderCalendar() {
            calendarTableBody.innerHTML = "";
            currentMonthYearHeader.textContent = `${monthNames[activeDate.getMonth()]} ${activeDate.getFullYear()}`;

            const year = activeDate.getFullYear();
            const month = activeDate.getMonth();

            // Hari pertama dalam bulan (0 = Minggu)
            const firstDayOfMonth = new Date(year, month, 1).getDay();
            // Jumlah hari dalam bulan
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            let date = 1;
            // Render 6 baris (minggu)
            for (let i = 0; i < 6; i++) {
                const row = document.createElement("tr");

                // Render 7 kolom (hari)
                for (let j = 0; j < 7; j++) {
                    const cell = document.createElement("td");
                    // Kosongkan sel sebelum tanggal 1
                    if (i === 0 && j < firstDayOfMonth) {
                        cell.innerHTML = "";
                    } else if (date > daysInMonth) {
                        // Kosongkan sel setelah akhir bulan
                        cell.innerHTML = "";
                    } else {
                        // Isi tanggal
                        const daySpan = document.createElement("span");
                        daySpan.classList.add("calendar-day");
                        daySpan.textContent = date;

                        // Tandai hari ini
                        if (
                            date === currentDate.getDate() &&
                            month === currentDate.getMonth() &&
                            year === currentDate.getFullYear()
                        ) {
                            daySpan.classList.add("current-day");
                        }
                        cell.appendChild(daySpan);
                        date++;
                    }
                    row.appendChild(cell);
                }
                calendarTableBody.appendChild(row);
            }
        }

        // Navigasi bulan sebelumnya
        prevMonthBtn.addEventListener("click", () => {
            activeDate.setMonth(activeDate.getMonth() - 1);
            activeDate.setDate(1);
            renderCalendar();
        });

        // Navigasi bulan berikutnya
        nextMonthBtn.addEventListener("click", () => {
            activeDate.setMonth(activeDate.getMonth() + 1);
            activeDate.setDate(1);
            renderCalendar();
        });

        // Render kalender saat halaman dimuat
        renderCalendar();
    </script>
</body>

</html>