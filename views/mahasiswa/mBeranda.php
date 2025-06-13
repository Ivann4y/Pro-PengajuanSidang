<?php
session_start();
if ($_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../../index.php");
    exit();
}

$mahasiswa_info = [
    'nama' => 'Nayaka'
];

// nnti pake query count kyny cek status_sidang
$card_dashboard = [
    'sidang_berlangsung' => 3,
    'menunggu_penilaian' => 2
];

$tugas_list = [
    'Revisi Sidang PRG',
    'Revisi Sidang Basdat',
    'Revisi Sidang TA',
    'Revisi Sidang Orkom',
    'Revisi Sidang Jaringan Komputer',
    'Revisi Sidang Sistem Informasi',
    'Revisi Sidang Sistem Terdistribusi',
    'Revisi Sidang Sistem Operasi',
    'Revisi Sidang Kecerdasan Buatan',
    'Revisi Sidang Pemrograman Web',
];

$sidang_mendatang = [
    ['tanggal_sidang' => '2025-06-02', 'judul' => 'Sistem Pengajuan Skripsi', 'link_detail' => 'mdetailsidangta.php'],
    ['tanggal_sidang' => '2025-06-05', 'judul' => 'Revisi Proposal KP', 'link_detail' => 'mdetailsidangta.php'],
    ['tanggal_sidang' => '2025-06-10', 'judul' => 'Sidang Akhir TA', 'link_detail' => 'mdetailsidangta.php'],
    ['tanggal_sidang' => '2025-06-15', 'judul' => 'Presentasi Proyek', 'link_detail' => 'mdetailsidangta.php'],
    ['tanggal_sidang' => '2025-07-02', 'judul' => 'Pengumpulan Laporan', 'link_detail' => 'mdetailsidangta.php'],
];


// Ini g perlu ganti kalau udh ke database harusnya
// penanda sidang mendatang
$sidang_dates = array_column($sidang_mendatang, 'tanggal_sidang');

// PHP Calendar Logic
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$date = new DateTime("$year-$month-01");
//DateTime itu class yang ada di PHP untuk memanipulasi tanggal
//format itu fungsi dari DateTime itu sendiri
// t = jumlah hari dalam bulan tersebut
// w = hari pertama dalam minggu (0 = Minggu, 1 = Senin, ..., 6 = Sabtu)
// n = bulan tanpa leading zero (1-12)
// Y = tahun 4 digit
// m = bulan dengan leading zero (01-12)
// d = hari dalam bulan dengan leading zero (01-31)
$bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
$month_name = $bulan[$month - 1];
// clone itu untuk membuat salinan data
$prev_date = (clone $date)->modify('-1 month');
$next_date = (clone $date)->modify('+1 month');
//? jadi string query pembatas
$prev_link = "?month=" . $prev_date->format('n') . "&year=" . $prev_date->format('Y');
$next_link = "?month=" . $next_date->format('n') . "&year=" . $next_date->format('Y');
$days_in_month = $date->format('t');
$first_day_of_week = $date->format('w');
$today_date = date('Y-m-d');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Mahasiswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <style>
        .sidang-status-card {
            background-color: #4B68FB;
            color: white;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .sidang-status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
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
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .penilaian-status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
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
            font-size: 1.1rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 1rem;
        }

        .tanggungan-card {
            overflow-y: auto;
            max-height: 58.8vh;
            padding-top: 0rem;
            padding-bottom: 1rem;
        }

        .tanggungan-card .section-title {
            position: sticky;
            top: 0;
            background-color: #F3F4F6;
            z-index: 10;
            padding-top: 0.7rem;
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
            border-radius: 5vh;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            min-height: 300px;
            margin-bottom: 2vh;
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

        .calendar-card .calendar-nav a {
            color: #C7D2FE;
            text-decoration: none;
        }

        .calendar-card .calendar-nav i {
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0 0.5rem;
        }

        .calendar-card .calendar-nav a:hover i {
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
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .calendar-card .calendar-day.current-day {
            background-color: white;
            color: #4B68FB;
            font-weight: 700;
        }

        .calendar-card .calendar-day.has-sidang {
            background-color: rgba(255, 255, 255, 0.25);
            font-weight: 600;
        }

        .calendar-card .calendar-day.has-sidang:hover {
            background-color: rgba(255, 255, 255, 0.4);
            transform: scale(1.1);
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
            padding-top: 0.7rem;
            padding-bottom: 0.5rem;
            margin-top: 0;
            margin-bottom: 0;
            border-bottom: 1px solid #DEE2E6;
        }

        .sidang-mendatang-card .item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #FFFFFF;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
        }

        .sidang-mendatang-card .item:last-child {
            margin-bottom: 2vh;
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
            color: #4B68FB;
            line-height: 1.1;
        }

        .sidang-mendatang-card .date-bubble .month {
            font-size: 0.7rem;
            color: #6B7280;
            line-height: 1;
            text-transform: uppercase;
        }

        .sidang-mendatang-card .arrow i {
            font-size: 1.2rem;
            color: #4B68FB;
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
            <div class="NavSide__sidebar-brand"><img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo"></div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active"><b></b><b></b><a href="mBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="mPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="mSidang.php"><span class="NavSide__sidebar-title fw-semibold">Sidang</span></a></li>
                <li class="NavSide__sidebar-item"><b></b><b></b><a href="#" data-bs-toggle="modal" data-bs-target="#logMBeranda"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a></li>
            </ul>
        </div>
        <div class="NavSide__topbar">
            <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
            <div class="header-icons"><a href="mNotifikasi.php" title="Notifikasi" style="text-decoration: none; color: inherit;"><i class="bi bi-bell-fill"></i></a>
                <div class="profile-icon"><a href="mProfil.php" title="Profil" style="text-decoration: none; color: inherit;"><i class="bi bi-person-fill fs-5"></i></a></div>
            </div>
        </div>

        <main class="NavSide__main-content" id="mBeranda">
            <div class="dashboard-header">
                <h2 class="page-title" style="color:#1F2937">Beranda - Mahasiswa</h2>
                <div class="header-icons d-none d-md-flex"><a href="mNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
                    <div class="profile-icon"><a href="mProfil.php" title="Profil"><i class="bi bi-person-fill fs-5" style="color: white"></i></a></div>
                </div>
            </div>

            <!-- DYNAMIC CONTENT: Use data from the Data Layer -->
            <h1 class="welcome-text">Selamat Datang, <?php echo htmlspecialchars($mahasiswa_info['nama']); ?>!</h1>

            <div class="row">
                <div class="col-lg-7">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <a href="mSidang.php" style="text-decoration: none; color: inherit;">
                                <div class="dashboard-card sidang-status-card w-100">
                                    <div class="number"><?php echo $card_dashboard['sidang_berlangsung']; ?></div>
                                    <div class="text"><span class="title">Sidang</span><span class="description">Sedang Berlangsung</span></div>
                                </div>
                            </a>
                        </div>
                        <div class="col-12 col-md-6">
                            <a href="mPengajuan.php" style="text-decoration: none; color: inherit;">
                                <div class="dashboard-card penilaian-status-card">
                                    <div class="number"><?php echo $card_dashboard['menunggu_penilaian']; ?></div>
                                    <div class="text"><span class="title">Penilaian</span><span class="description">Menunggu untuk Dinilai</span></div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="dashboard-card content-card tanggungan-card">
                        <h3 class="section-title">Tugas</h3>
                        <?php if (empty($tugas_list)): ?>
                            <p class="text-center text-muted mt-3">Tidak ada tugas yang perlu dikerjakan.</p>
                        <?php else: ?>
                            <?php foreach ($tugas_list as $tugas): ?>
                                <div class="tanggungan-item"><?php echo htmlspecialchars($tugas); ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="dashboard-card calendar-card">
                        <div class="section-title-container">
                            <div class="calendar-nav">
                                <a href="<?php echo $prev_link; ?>"><i class="bi bi-chevron-left"></i></a>
                                <h3 class="section-title"><?php echo $month_name . ' ' . $year; ?></h3>
                                <a href="<?php echo $next_link; ?>"><i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                        <table class="calendar">
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
                                <?php
                                $day_count = 1;
                                $cell_count = 0;
                                echo '<tr>';
                                for ($i = 0; $i < $first_day_of_week; $i++) {
                                    echo '<td></td>'; //jadi pengisi hari yang kosong sebelum tanggal 1
                                    $cell_count++;
                                }
                                while ($day_count <= $days_in_month) {
                                    // 1. Create a new DateTime object specifically for the current day in the loop.
                                    $current_day_object = new DateTime("$year-$month-$day_count");

                                    // 2. Format it directly to the 'Y-m-d' string. No str_pad needed!
                                    // The 'm' and 'd' characters automatically handle the leading zeros.
                                    $cell_date = $current_day_object->format('Y-m-d');
                                    $class_list = 'calendar-day';
                                    if ($cell_date == $today_date) {
                                        $class_list .= ' current-day';
                                    }
                                    if (in_array($cell_date, $sidang_dates)) {
                                        $class_list .= ' has-sidang';
                                    }
                                    echo "<td><span class=\"$class_list\">$day_count</span></td>";
                                    $day_count++;
                                    $cell_count++;
                                    if ($cell_count % 7 == 0 && $day_count <= $days_in_month) {
                                        echo '</tr><tr>';
                                    }
                                }

                                while ($cell_count % 7 != 0) {
                                    echo '<td></td>'; //jadi pengisi hari yang kosong setelah tanggal terakhir
                                    $cell_count++;
                                }
                                echo '</tr>';
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="dashboard-card content-card sidang-mendatang-card">
                        <h3 class="section-title">Sidang Mendatang</h3>
                        <?php if (empty($sidang_mendatang)): ?>
                            <p class="text-center text-muted mt-3">Tidak ada sidang yang dijadwalkan.</p>
                        <?php else: ?>
                            <?php foreach ($sidang_mendatang as $sidang): ?>
                                <?php $sidang_date_obj = new DateTime($sidang['tanggal_sidang']); ?>
                                <a href="<?php echo htmlspecialchars($sidang['link_detail']); ?>" style="text-decoration: none; color: inherit;">
                                    <div class="item">
                                        <div class="date-bubble">
                                            <span class="day"><?php echo $sidang_date_obj->format('d'); ?></span>
                                            <span class="month"><?php echo $sidang_date_obj->format('M'); ?></span>
                                        </div>
                                        <span class="info"><?php echo htmlspecialchars($sidang['judul']); ?></span>
                                        <span class="arrow"><i class="bi bi-chevron-right"></i></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal and JS remain the same -->
    <div class="modal fade" id="logMBeranda" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div style="background-color:#4B68FB;">
                    <div class="modal-header">
                        <h1 class="modal-title mx-auto fs-5 text-light" id="exampleModalLabel">Perhatian!</h1>
                    </div>
                </div>
                <div class="modal-body mx-auto">Apakah anda yakin ingin keluar?</div>
                <div class="modal-footer justify-content-center border-0"><button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button><button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button></div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");
        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
    </script>
</body>

</html>