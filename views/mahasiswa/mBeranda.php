<?php
session_start();
if ($_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");


        .sidang-status-card {
            background-color: #4F46E5;
            color: white;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .sidang-status-card .number {
            font-size: 4.8rem;
            font-weight: 700;
            line-height: 1;
            margin-right: 1.2rem;
            min-width: 50px;
            text-align: center;
        }

        .sidang-status-card .text {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .sidang-status-card .text .title {
            font-size: 0.95rem;
            font-weight: 500;
            display: block;
            margin-bottom: 0.1rem;
        }

        .sidang-status-card .text .description {
            font-size: 1.05rem;
            font-weight: 600;
        }

        .penilaian-status-card {
            background-color: rgb(239, 239, 239);
            display: flex;
            align-items: center;
        }

        .penilaian-status-card .number {
            font-size: 4.8rem;
            font-weight: 700;
            line-height: 1;
            margin-right: 1.2rem;
            color: rgb(37, 44, 54);
            min-width: 50px;
            text-align: center;
        }

        .penilaian-status-card .text {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .penilaian-status-card .text .title {
            font-size: 0.95rem;
            font-weight: 500;
            color: #4B5563;
            display: block;
            margin-bottom: 0.1rem;
        }

        .penilaian-status-card .text .description {
            font-size: 1.05rem;
            font-weight: 600;
            color: #1F2937;
        }

        .content-card {
            background-color: #F3F4F6;
        }

        .content-card .section-title {
            /* Base style for section titles in content cards */
            font-size: 1.1rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 1rem;
            /* Default margin, overridden by sticky titles */
        }

        /* --- Notifikasi Card Styles --- */
        .notifikasi-card {
            overflow-y: auto;
            max-height: 35vh;
            /* Adjust as needed */
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
            margin-top: 0;
            margin-bottom: 0;
            border-bottom: 1px solid #DEE2E6;
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
            /* Ensure any previous height: 92% !important; is removed */
            overflow-y: auto;
            max-height: 37.5vh;
            /* Adjust as needed */
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

        /* --- Calendar Card (no changes to scrolling for this one based on request) --- */
        .calendar-card {
            background-color: #4F46E5;
            color: white;
            height: 38.5vh;
            /* Fixed height, not typically a scrollable list of items */
        }

        .calendar-card .section-title-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calendar-card .section-title {
            /* This title is different, not sticky in the same way */
            color: white;
            margin-bottom: 0;
            /* Original had this, if it was part of .content-card .section-title, it would be overridden by more specific sticky titles */
        }

        .calendar-card .calendar-nav i {
            font-size: 1rem;
            cursor: pointer;
            padding: 0 0.3rem;
        }

        .calendar-card .calendar {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.3rem;
        }

        .calendar-card .calendar th {
            padding: -0.2rem 0.25rem;
            /* Note: negative padding is unusual, might be typo or specific intent */
            text-align: center;
            font-weight: 500;
            font-size: 0.85rem;
            color: #C7D2FE;
        }

        .calendar-card .calendar td {
            padding: 0.2rem;
            text-align: center;
        }

        .calendar-card .calendar-day {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            border-radius: 50%;
            font-size: 0.85rem;
            font-weight: 500;
            margin: 0 auto;
            cursor: pointer;
        }

        .calendar-card .calendar-day.current-day {
            background-color: white;
            color: #4F46E5;
            font-weight: 700;
        }

        .calendar-card .calendar-day:hover:not(.current-day) {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* --- Sidang Mendatang Card (User's latest version) --- */
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
            color: #4F46E5;
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
            color: #4F46E5;
        }
    </style>
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
                    <a href="mBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mSidang.php"><span class="NavSide__sidebar-title fw-semibold">Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="logout.html"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
                </li>
            </ul>
        </div>

        <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <div class="header-icons">
                <a href="mNotifikasi.php" title="Notifikasi" style="text-decoration: none; color: inherit;">
                    <i class="bi bi-bell-fill"></i>
                </a>
                <div class="profile-icon">
                    <a href="mProfil.php" title="Profil" style="text-decoration: none; color: inherit;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </a>
                </div>
            </div>
        </div>
        <main class="NavSide__main-content" id="mBeranda">
            <div class="dashboard-header">
                <h2 class="page-title">Beranda</h2>
                <div class="header-icons d-none d-md-flex">
                    <a href="mNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i>
                    </a>
                    <div class="profile-icon">
                        <a href="mProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a>
                    </div>
                </div>
            </div>

            <h1 class="welcome-text">Selamat Datang, Nayaka!</h1>

            <div class="row">
                <div class="col-lg-7">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="dashboard-card sidang-status-card">
                                <div class="number">3</div>
                                <div class="text">
                                    <span class="title">Sidang</span>
                                    <span class="description">Sedang Berlangsung</span>
                                </div>
                            </div>

                            <div class="dashboard-card penilaian-status-card">
                                <div class="number">2</div>
                                <div class="text">
                                    <span class="title">Penilaian</span>
                                    <span class="description">Menunggu untuk Dinilai</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="dashboard-card content-card tanggungan-card">
                                <h3 class="section-title">Tanggungan</h3>
                                <div class="tanggungan-item">Revisi Sidang PRG</div>
                                <div class="tanggungan-item">Revisi Sidang Basdat</div>
                                <div class="tanggungan-item">Revisi Sidang TA</div>
                                <div class="tanggungan-item">Revisi Sidang Orkom</div>
                                <div class="tanggungan-item">Revisi Sidang </div>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-card content-card notifikasi-card">
                        <h3 class="section-title">Notifikasi</h3>
                        <div class="notifikasi-item">Pengajuan Sidang PRG Telah Disetujui</div>
                        <div class="notifikasi-item">Revisi Sidang BasDat Telah Disetujui</div>
                        <div class="notifikasi-item">Pengajuan Sidang SO Telah Ditolak</div>
                        <div class="notifikasi-item">Notifikasi Item 4</div>
                        <div class="notifikasi-item">Notifikasi Item 5</div>
                        <div class="notifikasi-item">Notifikasi Item 6</div>
                    </div>
                </div>

                <div class="col-lg-5">
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
                            </tbody>
                        </table>
                    </div>

                    <div class="dashboard-card content-card sidang-mendatang-card">
                        <h3 class="section-title">Sidang Mendatang</h3>
                        <div class="item">
                            <div class="date-bubble">
                                <span class="day">02</span>
                                <span class="month">Jun</span>
                            </div>
                            <span class="info">Sistem Pengajuan Skripsi</span>
                            <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                        </div>
                        <div class="item">
                            <div class="date-bubble">
                                <span class="day">05</span>
                                <span class="month">Jun</span>
                            </div>
                            <span class="info">Revisi Proposal KP</span>
                            <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                        </div>
                        <div class="item">
                            <div class="date-bubble">
                                <span class="day">10</span>
                                <span class="month">Jun</span>
                            </div>
                            <span class="info">Sidang Akhir TA</span>
                            <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                        </div>
                        <div class="item">
                            <div class="date-bubble">
                                <span class="day">15</span>
                                <span class="month">Jun</span>
                            </div>
                            <span class="info">Presentasi Proyek</span>
                            <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                        </div>
                        <div class="item">
                            <div class="date-bubble">
                                <span class="day">20</span>
                                <span class="month">Jun</span>
                            </div>
                            <span class="info">Ujian Komprehensif</span>
                            <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                        </div>
                        <div class="item">
                            <div class="date-bubble">
                                <span class="day">25</span>
                                <span class="month">Jun</span>
                            </div>
                            <span class="info">Revisi Skripsi Bab 1-3</span>
                            <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                        </div>
                        <div class="item">
                            <div class="date-bubble">
                                <span class="day">28</span>
                                <span class="month">Jun</span>
                            </div>
                            <span class="info">Bimbingan Akhir</span>
                            <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                        </div>
                        <div class="item">
                            <div class="date-bubble">
                                <span class="day">02</span>
                                <span class="month">Jul</span>
                            </div>
                            <span class="info">Pengumpulan Laporan</span>
                            <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        // Sidebar Toggle Logic
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };

        // Sidebar Active Item Logic
        let listItems = document.querySelectorAll(".NavSide__sidebar-item");
        for (let i = 0; i < listItems.length; i++) {
            listItems[i].onclick = function() {
                if (!this.classList.contains("NavSide__sidebar-item--active")) {
                    for (let j = 0; j < listItems.length; j++) {
                        listItems[j].classList.remove("NavSide__sidebar-item--active");
                    }
                    this.classList.add("NavSide__sidebar-item--active");
                }
            };
        }

        // Real-time Calendar Logic
        const calendarTableBody = document.querySelector("#calendarTable tbody");
        const currentMonthYearHeader = document.getElementById("currentMonthYear");
        const prevMonthBtn = document.getElementById("prevMonth");
        const nextMonthBtn = document.getElementById("nextMonth");

        let currentDate = new Date();
        let activeDate = new Date();

        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];

        function renderCalendar() {
            calendarTableBody.innerHTML = "";
            currentMonthYearHeader.textContent = `${monthNames[activeDate.getMonth()]} ${activeDate.getFullYear()}`;

            const year = activeDate.getFullYear();
            const month = activeDate.getMonth();

            const firstDayOfMonth = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            let date = 1;
            for (let i = 0; i < 6; i++) {
                const row = document.createElement("tr");

                for (let j = 0; j < 7; j++) {
                    const cell = document.createElement("td");
                    if (i === 0 && j < firstDayOfMonth) {
                        cell.innerHTML = "";
                    } else if (date > daysInMonth) {
                        cell.innerHTML = "";
                    } else {
                        const daySpan = document.createElement("span");
                        daySpan.classList.add("calendar-day");
                        daySpan.textContent = date;

                        if (date === currentDate.getDate() && month === currentDate.getMonth() && year === currentDate.getFullYear()) {
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

        prevMonthBtn.addEventListener("click", () => {
            activeDate.setMonth(activeDate.getMonth() - 1);
            activeDate.setDate(1);
            renderCalendar();
        });

        nextMonthBtn.addEventListener("click", () => {
            activeDate.setMonth(activeDate.getMonth() + 1);
            activeDate.setDate(1);
            renderCalendar();
        });

        renderCalendar();
    </script>
</body>

</html>