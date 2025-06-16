<?php
session_start();
if ($_SESSION['role'] !== 'dosen') {
    header("Location: ../../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dosen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css"/>
    <link rel="stylesheet" href="../../extra/style.css">

    <style>

        /* Ikon Header untuk Desktop (Notifikasi & Profil) */
        .NavSide__main-content .header-icons-desktop {
            position: absolute;
            top: 30px;
            right: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            z-index: 10;
        }

        .NavSide__main-content .header-icons-desktop .notification-icon {
            font-size: 1.5rem;
            color: #1F2937;
            cursor: pointer;
        }

        .NavSide__main-content .header-icons-desktop .profile-icon-desktop {
            width: 40px; 
            height: 40px;
            background-color: #1F2937; 
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white; 
            font-size: 1.5rem; 
        }

        .NavSide__topbar .header-icons {
            display: flex;
            align-items: center;
            gap: 0px; 
        }
        .NavSide__topbar .header-icons .notification-icon i {
             color: #1F2937; 
        }

        /* "Beranda" */
        .dashboardTitle { 
            color: #1F2937; 
            font-size: 1.25rem; 
            font-weight: 600; 
            margin-bottom: 0.5rem;
        }
        /* "Selamat Datang, Evan Wahyu!" */
        .welcomeText { 
            color: #1F2937;
            font-size: 2.5rem; 
            font-weight: 700; 
            margin-bottom: 2rem; /   
        }

        .hover-effect-card { /* Kelas baru untuk card yang bisa dihover */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .hover-effect-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        /* Gaya umum untuk card status (pengajuan, perbaikan, penilaian) */
        .status-card-common {
            display: flex;
            align-items: center;
            padding: 1.25rem; 
        }

        .status-card-common .number {
            font-size: 4.8rem;
            font-weight: 700;
            line-height: 1;
            margin-right: 1.2rem;
            min-width: 50px; 
            text-align: center;
        }

        .status-card-common .text-content { 
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
            overflow-wrap: break-word;
            min-width: 0;
        }

        .status-card-common .text-content .title {
            font-size: 0.95rem;
            font-weight: 500;
            display: block;
            margin-bottom: 0.1rem;
        }

        .status-card-common .text-content .description {
            font-size: 1.05rem;
            font-weight: 600;
        }

        /* Card Pengajuan (biru) */
        .card-pengajuan.status-card-common {
            background-color: #4B68FB;
            color: white;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .card-pengajuan.status-card-common .number {
            color: white; 
        }

        /* Card Perbaikan & Penilaian (abu-abu) */
        .card-perbaikan.status-card-common,
        .card-penilaian.status-card-common {
            background-color: #F3F4F6;
            color: #1F2937;
            border: 1px solid #E5E7EB;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1); 
        }

        .card-perbaikan.status-card-common .number,
        .card-penilaian.status-card-common .number {
            color: rgb(37, 44, 54); 
        }

        .card-perbaikan.status-card-common .text-content .title,
        .card-penilaian.status-card-common .text-content .title {
            color: #4B5563; 
        }

        .card-perbaikan.status-card-common .text-content .description,
        .card-penilaian.status-card-common .text-content .description {
            color: #1F2937; 
        }

        .dashboard-card { /* Tambahan untuk styling dasar kartu dashboard */
            background-color: #FFFFFF;
            padding: 1.25rem;
            border-radius: 22px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .calendar-card {
            background-color: #4B68FB;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 1rem;
            border-radius: 5vh;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            min-height: 300px;
            height: fit-content;
        }
        .calendar-card .section-title-container {
            display: flex;
            align-items: center;
            padding-bottom: 0.5rem;
            flex-shrink: 0;
        }
        .calendar-card .calendar-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
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
            display: flex;
            flex-direction: column;
        }
        .calendar-card .calendar thead,
        .calendar-card .calendar tbody {
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        .calendar-card .calendar tbody {
            flex-grow: 1;
        }
        .calendar-card .calendar tr {
            display: flex;
            flex-grow: 1;
            width: 100%;
        }
        .calendar-card .calendar th,
        .calendar-card .calendar td {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.1rem;
            text-align: center;
        }
        .calendar-card .calendar th {
            font-weight: 500;
            font-size: 0.75rem;
            color: #C7D2FE;
            text-transform: uppercase;
            padding: 0.3rem 0.25rem;
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
        .calendar-card .calendar-day.other-month { 
            color: #A5B4FC;
            cursor: default;
        }
        .calendar-card .calendar-day.other-month:hover {
            background-color: transparent;
        }
        
        .sidang-mendatang-card {
            background-color: #F3F4F6;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            max-height: 57vh; 
            overflow-y: auto; 
            padding-bottom: 1.25rem; 
        }
        
        .sidang-mendatang-card .section-title { 
            position: sticky;
            top: -1.25rem; 
            background-color: #F3F4F6;
            z-index: 10;
            padding-top: 1.25rem; 
            padding-bottom: 1rem; 
            margin: -1.25rem -1.25rem 0 -1.25rem; 
            padding-left: 1.25rem; 
            padding-right: 1.25rem; 
            font-size: 1.1rem;
            font-weight: 600;
            color: #1F2937; 
        }
        .sidang-mendatang-card .item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #FFFFFF; 
            padding: 0.75rem 1rem;
            border-radius: 8px; 
            margin-bottom: 0.75rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        }
        .sidang-mendatang-card .item:last-child {
            margin-bottom: 0;
        }
        .sidang-mendatang-card .date-bubble {
            background-color: #EEF2FF;
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
            color: #4338CA; 
            line-height: 1.1;
        }
        .sidang-mendatang-card .date-bubble .month {
            font-size: 0.7rem;
            color: #64748B;
            line-height: 1;
            text-transform: uppercase;
            font-weight: 500;
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

        .modal-footer .btn-danger {
            background-color: #FD7D7D;
            border-color: #FD7D7D;
        }

        .modal-footer .btn-success {
            background-color: #4FD382;
            border-color: #4FD382;
        }

    </style>
</head>
<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo"> </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="dBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="dPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="#" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a></li>
            </ul>
        </div>

        <div class="NavSide__topbar">
            <div class="NavSide__toggle" id="sidebarToggleMobile">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <div class="header-icons">
                 <a href="#" class="notification-icon" title="Notifikasi">
                    <i class="bi bi-bell-fill fs-5"></i>
                </a>
                <div class="profile-icon">
                    <i class="bi bi-person-fill fs-5"></i>
                </div>
            </div>
            </div>

        <div class="NavSide__main-content" id="mainContent">
            <div class="header-icons-desktop d-none d-lg-flex">
                <a href="#" class="notification-icon" title="Notifikasi">
                    <i class="bi bi-bell-fill"></i>
                </a>
                <div class="profile-icon-desktop">
                    <i class="bi bi-person-fill"></i>
                </div>
            </div>
            <div class="dashboardTitle">Beranda Dosen</div>
            <h2 class="welcomeText">Selamat Datang, Evan Wahyu!</h2>

            <div class="row gy-4">
            <div class="col-lg-3">
                <div class="mb-4">
                    <a href="dpengajuan.php" style="text-decoration: none; color: inherit;">
                        <div class="dashboard-card card-pengajuan status-card-common hover-effect-card"> <div class="number">3</div>
                            <div class="text-content">
                                <span class="title">Pengajuan</span>
                                <span class="description">Menunggu Persetujuan</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="mb-4">
                    <a href="dDaftarSidang.php" style="text-decoration: none; color: inherit;">
                        <div class="dashboard-card card-perbaikan status-card-common hover-effect-card"> <div class="number">2</div>
                            <div class="text-content">
                                <span class="title">Perbaikan</span>
                                <span class="description">Menunggu untuk Dinilai</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div>
                    <a href="dDaftarSidang.php" style="text-decoration: none; color: inherit;">
                        <div class="dashboard-card card-penilaian status-card-common hover-effect-card"> <div class="number">2</div>
                            <div class="text-content">
                                <span class="title">Penilaian</span>
                                <span class="description">Menunggu untuk Dinilai</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

                <div class="col-lg-4">
                    <div class="dashboard-card sidang-mendatang-card">
                        <h3 class="section-title">Sidang Mendatang</h3>
                        <div class="item">
                            <div class="date-bubble"><span class="day">22</span><span class="month">Apr</span></div>
                            <span class="info">Nayaka Ivana Putra<br><small style="color: #6B7280; font-size: 0.75rem;">Anniversary TRPL</small></span>
                            <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                        </div>
                        <div class="item">
                            <div class="date-bubble"><span class="day">29</span><span class="month">Mei</span></div>
                            <span class="info">Zahrah Imelda Asari<br><small style="color: #6B7280; font-size: 0.75rem;">Sistem Pengajuan Sidang</small></span>
                            <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                        </div>
                         <div class="item">
                            <div class="date-bubble"><span class="day">17</span><span class="month">Agu</span></div>
                            <span class="info">Mnur<br><small style="color: #6B7280; font-size: 0.75rem;">Sistem Rekayasa Agama</small></span>
                            <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                        </div>
                        <div class="item">
                            <div class="date-bubble"><span class="day">12</span><span class="month">Sep</span></div>
                            <span class="info">Naufal Abdirrahman Faiz<br><small style="color: #6B7280; font-size: 0.75rem;">Pengunaan Kawat</small></span>
                            <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                        </div>
                        <div class="item">
                            <div class="date-bubble"><span class="day">27</span><span class="month">Sep</span></div>
                            <span class="info">Ezra<br><small style="color: #6B7280; font-size: 0.75rem;">STEI ITB</small></span>
                            <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-5">
                    <div class="dashboard-card calendar-card">
                        <div class="section-title-container">
                            <div class="calendar-nav">
                                <i class="bi bi-chevron-left" id="prevMonth"></i>
                                <h3 class="section-title" id="currentMonthYear"></h3>
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
                                <!-- Calendar will be rendered by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
     <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div style="background-color:#4B68FB;">
                    <div class="modal-header">
                        <h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1>
                    </div>
                </div>
                <div class="modal-body mx-auto">
                    Apakah anda yakin ingin keluar?
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                    <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>
        
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/dashboard.js"></script>
    <script>
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Toggle Sidebar ---
            const sidebarToggle = document.getElementById('sidebarToggleMobile');
            const sidebar = document.getElementById('main-sidebar');
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('NavSide__sidebar--active-mobile');
                    sidebarToggle.classList.toggle('NavSide__toggle--active');
                });
            }

            // // --- Real-time Calendar Functionality ---
            // const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            // const currentMonthYearEl = document.getElementById('currentMonthYear');
            // const calendarTableBody = document.querySelector('#calendarTable tbody');
            // const prevMonthBtn = document.getElementById('prevMonth');
            // const nextMonthBtn = document.getElementById('nextMonth');

            // let today = new Date(); 
            // let currentDate = new Date(today.getFullYear(), today.getMonth(), 1);

            // function renderCalendar() {
            //     calendarTableBody.innerHTML = "";
            //     currentMonthYearEl.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;

            //     const year = currentDate.getFullYear();
            //     const month = currentDate.getMonth();

            //     const firstDayOfMonth = new Date(year, month, 1).getDay();
            //     const daysInMonth = new Date(year, month + 1, 0).getDate();

            //     let date = 1;
            //     for (let i = 0; i < 6; i++) {
            //         const row = document.createElement("tr");

            //         for (let j = 0; j < 7; j++) {
            //             const cell = document.createElement("td");
            //             if (i === 0 && j < firstDayOfMonth) {
            //                 cell.innerHTML = "";
            //             } else if (date > daysInMonth) {
            //                 cell.innerHTML = "";
            //             } else {
            //                 const daySpan = document.createElement("span");
            //                 daySpan.classList.add("calendar-day");
            //                 daySpan.textContent = date;

            //                 if (date === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
            //                     daySpan.classList.add("current-day");
            //                 }
            //                 cell.appendChild(daySpan);
            //                 date++;
            //             }
            //             row.appendChild(cell);
            //         }
            //         calendarTableBody.appendChild(row);
            //     }
            // }

            // if (prevMonthBtn) {
            //     prevMonthBtn.addEventListener('click', () => {
            //         currentDate.setMonth(currentDate.getMonth() - 1);
            //         renderCalendar();
            //     });
            // }

            // if (nextMonthBtn) {
            //     nextMonthBtn.addEventListener('click', () => {
            //         currentDate.setMonth(currentDate.getMonth() + 1);
            //         renderCalendar();
            //     });
            // }

            // // Initial render of the calendar
            // renderCalendar(); 
        });

        // Fungsi notifikasi (jika masih digunakan)
        function markAllRead() {
            // Logika Anda
        }
        function markOneRead(el) {
            // Logika Anda
        }
    </script>
</body>
</html>