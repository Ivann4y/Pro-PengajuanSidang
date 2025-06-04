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
    <link rel="stylesheet" href="../../extra/style.css">
    <style>
        

        /* Ikon Header untuk Desktop (Profil) */
        .NavSide__main-content .header-icons-desktop {
            position: absolute;
            top: 30px;
            right: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            z-index: 10;
        }
        .NavSide__main-content .header-icons-desktop .profile-icon-desktop {
            width: 48px; /* Ukuran ikon profil sesuai gambar */
            height: 48px;
            background-color: #1F2937; /* Warna background ikon profil */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem; /* Ukuran ikon di dalam bubble */
        }



        /* Styling dari Gambar */
        .dashboardTitle { /* "Beranda" */
            color: #1F2937; /* Warna teks gelap */
            font-size: 1.25rem; /* Sedikit lebih kecil dari gambar */
            font-weight: 600; /* Lebih tebal */
            margin-bottom: 0.5rem;
        }

        .welcomeText { /* "Selamat Datang, Evan Wahyu!" */
            color: #1F2937;
            font-size: 2.5rem; /* Ukuran sesuai gambar */
            font-weight: 700; /* Bold */
            margin-bottom: 2rem; /* Jarak bawah lebih besar */
        }

        .statusCard {
            padding: 20px;
            border-radius: 12px; /* Radius sesuai gambar */
            height: 100%;
            transition: 0.3s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Konten tengah vertikal */
            min-height: 160px; /* Tinggi minimum card */
        }

        .statusCard:hover {
            transform: translateY(-5px); /* Efek hover sedikit naik */
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .card-pengajuan { /* Kartu "Pengajuan" */
            background-color: #4F46E5; /* Warna ungu sesuai gambar */
            color: white;
        }

        .card-penilaian, .card-perbaikan { /* Kartu "Penilaian" & "Perbaikan" */
            background-color: #F3F4F6; /* Warna abu-abu muda */
            color: #1F2937; /* Teks gelap */
            border: 1px solid #E5E7EB; /* Border tipis */
        }
        .card-penilaian .statusNumber, .card-perbaikan .statusNumber {
             color: #4F46E5; /* Angka berwarna ungu */
        }

        .statusTitle {
            font-weight: 600;
            font-size: 1rem; /* Ukuran judul status */
            margin-bottom: 8px;
        }
        .statusNumberAndText {
            display: flex;
            align-items: center; /* Angka dan teks sejajar */
        }
        .statusNumber {
            font-size: 3.5rem; /* Ukuran angka besar */
            font-weight: 700; /* Bold */
            line-height: 1;
            margin-right: 10px; /* Jarak angka dan teks */
        }
        .statusText {
            font-size: 0.9rem;
            line-height: 1.3;
        }


        .img-slot img{
            max-width: 110%; /* Gambar tidak terlalu lebar */
            max-height: 100%; /* Gambar tidak melebihi kontainer */
            object-fit: contain;
        }

        /* CSS KALENDER (DARI ANDA) */
        .dashboard-card { /* Tambahan untuk styling dasar kartu dashboard */
            background-color: #FFFFFF;
            padding: 1.25rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            height: 100%; /* Agar kartu di kolom yang sama punya tinggi sama jika di dalam row */
        }
        .calendar-card {
            background-color: #4F46E5;
            color: white;
            /* height: 38.5vh; */
            display: flex;
            flex-direction: column;
        }
        .calendar-card .section-title-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 0.5rem; /* Jarak judul dan navigasi ke tabel */
        }
        .calendar-card .section-title { /* Nama bulan dan tahun */
            color: white;
            margin-bottom: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .calendar-card .calendar-nav i {
            font-size: 1.2rem; /* Icon navigasi lebih besar */
            cursor: pointer;
            padding: 0 0.5rem; /* Padding icon navigasi */
            color: #C7D2FE; /* Warna icon navigasi lebih terang */
        }
        .calendar-card .calendar-nav i:hover {
            color: white;
        }
        .calendar-card .calendar {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem; /* Jarak dari navigasi ke tabel */
            flex-grow: 1;
        }
        .calendar-card .calendar th {
            /* padding: -0.2rem 0.25rem; -> padding negatif tidak valid */
            padding: 0.3rem 0.25rem;
            text-align: center;
            font-weight: 500;
            font-size: 0.75rem; /* Ukuran font header hari lebih kecil */
            color: #C7D2FE; /* Warna header hari */
            text-transform: uppercase;
        }
        .calendar-card .calendar td {
            padding: 0.1rem; /* Padding sel tanggal lebih kecil */
            text-align: center;
            vertical-align: middle;
        }
        .calendar-card .calendar-day {
            display: inline-flex; /* Agar bisa align item center */
            align-items: center;
            justify-content: center;
            width: 32px; /* Ukuran bubble tanggal */
            height: 32px;
            line-height: 32px;
            border-radius: 50%;
            font-size: 0.8rem; /* Ukuran font tanggal */
            font-weight: 500;
            margin: 0 auto;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .calendar-card .calendar-day.current-day {
            background-color: white;
            color: #4F46E5;
            font-weight: 700;
        }
        .calendar-card .calendar-day:hover:not(.current-day) {
            background-color: rgba(255,255,255,0.2);
        }
        .calendar-card .calendar-day.other-month { /* Untuk tanggal dari bulan lain */
            color: #A5B4FC; /* Warna lebih redup */
            cursor: default;
        }
        .calendar-card .calendar-day.other-month:hover {
            background-color: transparent;
        }


        /* CSS SIDANG MENDATANG (DARI ANDA) */
       
        .sidang-mendatang-card {
            background-color: #F3F4F6;
            overflow-y: auto;
            max-height: 38.5vh; /* <-- Baris ini diaktifkan */
            padding: 1.25rem;
}
        .sidang-mendatang-card .section-title { /* Judul "Sidang Mendatang" */
            position: sticky;
            top: -1.25rem; /* Sesuaikan dengan padding card agar menempel pas di atas */
            background-color: #F3F4F6;
            z-index: 10;
            padding-top: 1.25rem; /* Agar saat scroll tidak terpotong */
            padding-bottom: 1rem; /* Jarak judul ke item pertama */
            margin: -1.25rem -1.25rem 0 -1.25rem; /* Ambil full width dan hilangkan margin bawah */
            padding-left: 1.25rem; /* Kembalikan padding kiri */
            padding-right: 1.25rem; /* Kembalikan padding kanan */
            font-size: 1.1rem;
            font-weight: 600;
            color: #1F2937; /* Warna teks gelap */
        }
        .sidang-mendatang-card .item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #FFFFFF; /* Item berwarna putih */
            padding: 0.75rem 1rem;
            border-radius: 8px; /* Radius item lebih kecil */
            margin-bottom: 0.75rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        }
        .sidang-mendatang-card .item:last-child {
            margin-bottom: 0;
        }
        .sidang-mendatang-card .date-bubble {
            background-color: #EEF2FF; /* Background bubble tanggal lebih soft */
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
            color: #4338CA; /* Warna ungu lebih gelap untuk tanggal */
            line-height: 1.1;
        }
        .sidang-mendatang-card .date-bubble .month {
            font-size: 0.7rem;
            color: #64748B; /* Warna bulan abu-abu */
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

    </style>
</head>
<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo"> </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="dBeranda.php"><span class="NavSide__sidebar-title">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dPengajuan.php"><span class="NavSide__sidebar-title">Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDaftarSidang.php"><span class="NavSide__sidebar-title">Daftar Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="logout.html" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
                </li>
            </ul>
        </div>

        <div class="NavSide__topbar">
            <div class="NavSide__toggle" id="sidebarToggleMobile">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <div class="header-icons">
                <div class="profile-icon">
                    <i class="bi bi-person-fill fs-5"></i>
                </div>
            </div>
        </div>

        <div class="NavSide__main-content" id="mainContent">
            <div class="header-icons-desktop">
                <div class="profile-icon-desktop">
                    <i class="bi bi-person-fill"></i>
                </div>
            </div>

            <div class="dashboardTitle">Beranda</div>
            <h2 class="welcomeText">Selamat Datang, Evan Wahyu!</h2>

            <div class="row gy-4">
                <div class="col-lg-3">
                    <div class="mb-4">
                        <div class="statusCard card-pengajuan" onclick="location.href='dpengajuan.php'">
                            <div class="statusTitle">Pengajuan</div>
                            <div class="statusNumberAndText">
                                <div class="statusNumber">3</div>
                                <div class="statusText">Menunggu<br>Persetujuan</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="statusCard card-perbaikan" onclick="location.href='aPenjadwalan.php'">
                            <div class="statusTitle">Perbaikan</div>
                            <div class="statusNumberAndText">
                                <div class="statusNumber">2</div>
                                <div class="statusText">Menunggu<br>untuk Dinilai</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="mb-4">
                        <div class="statusCard card-penilaian" onclick="location.href='aPenjadwalan.php'">
                            <div class="statusTitle">Penilaian</div>
                            <div class="statusNumberAndText">
                                <div class="statusNumber">2</div>
                                <div class="statusText">Menunggu<br>untuk Dinilai</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="img-slot">
                            <img src="../../assets/img/img8.png" alt="Dashboard Illustration">
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="row">
                        <div class="col-12 mb-4">
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
                                            <th>Min</th><th>Sen</th><th>Sel</th><th>Rab</th><th>Kam</th><th>Jum</th><th>Sab</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="dashboard-card sidang-mendatang-card">
                                <h3 class="section-title">Sidang Mendatang</h3>
                                <div class="item">
                                    <div class="date-bubble">
                                        <span class="day">22</span><span class="month">Apr</span>
                                    </div>
                                    <span class="info">Nayaka Ivana Putra<br><small style="color: #6B7280; font-size: 0.75rem;">Anniversary TRPL</small></span>
                                    <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                                </div>
                                <div class="item">
                                    <div class="date-bubble">
                                        <span class="day">29</span><span class="month">Mei</span>
                                    </div>
                                    <span class="info">Zahrah Imelda Asari<br><small style="color: #6B7280; font-size: 0.75rem;">Sistem Pengajuan Sidang</small></span>
                                    <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                                </div>
                                <div class="item">
                                    <div class="date-bubble">
                                        <span class="day">17</span><span class="month">Agu</span>
                                    </div>
                                    <span class="info">Mnur<br><small style="color: #6B7280; font-size: 0.75rem;">Sistem Rekayasa Agama</small></span>
                                    <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                                </div>
                                <div class="item">
                                    <div class="date-bubble">
                                        <span class="day">12</span><span class="month">Sep</span>
                                    </div>
                                    <span class="info">Naufal Abdirrahman Faiz<br><small style="color: #6B7280; font-size: 0.75rem;">Pengunaan Kawat</small></span>
                                    <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                                </div>
                                <div class="item">
                                    <div class="date-bubble">
                                        <span class="day">29</span><span class="month">Sep</span>
                                    </div>
                                    <span class="info">Nur Salim<br><small style="color: #6B7280; font-size: 0.75rem;">Dampak Penjualan Narkoba</small></span>
                                    <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                                </div>
                                <div class="item">
                                    <div class="date-bubble">
                                        <span class="day">10</span><span class="month">Okt</span>
                                    </div>
                                    <span class="info">Andrew Hermawan Surgiato<br><small style="color: #6B7280; font-size: 0.75rem;">Rekayasa Logistik</small></span>
                                    <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
    </div>

     <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header mx-auto">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Perhatian!</h1>
        </div>
        <div class="modal-body mx-auto">
             Apakah anda yakin ingin keluar?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
            <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
        </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
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

            // --- Real-time Calendar Functionality ---
            const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            const currentMonthYearEl = document.getElementById('currentMonthYear');
            const calendarTableBody = document.querySelector('#calendarTable tbody');
            const prevMonthBtn = document.getElementById('prevMonth');
            const nextMonthBtn = document.getElementById('nextMonth');

            let today = new Date(); // Get current date and time
            // currentDate is the date the calendar is currently displaying, initialized to the 1st of the current month
            let currentDate = new Date(today.getFullYear(), today.getMonth(), 1);

            function renderCalendar() {
                if (!calendarTableBody || !currentMonthYearEl) return; // Guard clause

                calendarTableBody.innerHTML = ''; // Clear previous calendar
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth(); // 0-indexed (Januari = 0)

                currentMonthYearEl.textContent = `${monthNames[month]} ${year}`;

                // Day of the week for the first day of the month (0=Sunday, 1=Monday, ..., 6=Saturday)
                const firstDayOfMonth = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                // For days from the previous month
                const daysInPrevMonth = new Date(year, month, 0).getDate();
                
                let dateCounter = 1; // Counter for days of the current month
                let nextMonthDateCounter = 1; // Counter for days of the next month

                for (let i = 0; i < 6; i++) { // Maximum 6 rows for a calendar month
                    let row = document.createElement('tr');
                    for (let j = 0; j < 7; j++) { // 7 days a week (Sunday - Saturday)
                        let cell = document.createElement('td');
                        let daySpan = document.createElement('span');
                        daySpan.classList.add('calendar-day');

                        if (i === 0 && j < firstDayOfMonth) {
                            // Days from the previous month
                            let prevMonthDay = daysInPrevMonth - firstDayOfMonth + 1 + j;
                            daySpan.textContent = prevMonthDay;
                            daySpan.classList.add('other-month');
                        } else if (dateCounter > daysInMonth) {
                            // Days from the next month
                            daySpan.textContent = nextMonthDateCounter;
                            daySpan.classList.add('other-month');
                            nextMonthDateCounter++;
                        } else {
                            // Days from the current month
                            daySpan.textContent = dateCounter;
                            // Highlight today's date
                            if (dateCounter === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                                daySpan.classList.add('current-day');
                            }
                            dateCounter++;
                        }
                        cell.appendChild(daySpan);
                        row.appendChild(cell);
                    }
                    calendarTableBody.appendChild(row);
                    // Optimization: if we've filled all days of current month and part of next, and we're past necessary rows, break.
                    if (dateCounter > daysInMonth && i >= Math.floor((firstDayOfMonth + daysInMonth -1) / 7) ) break; 
                }
            }

            if (prevMonthBtn) {
                prevMonthBtn.addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    renderCalendar();
                });
            }

            if (nextMonthBtn) {
                nextMonthBtn.addEventListener('click', () => {
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    renderCalendar();
                });
            }

            // Initial render of the calendar
            renderCalendar(); 
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