<?php
// 1. --- PHP LOGIC UPDATED ---
// Default to 'semua' if not set
$selectedTipe = isset($_GET['tipe']) ? $_GET['tipe'] : 'semua';
$selectedStatus = isset($_GET['status']) ? $_GET['status'] : 'semua';

// Load data
$jsonPath = __DIR__ . '/data_sidang.json'; 
$jsonData = file_exists($jsonPath) ? file_get_contents($jsonPath) : '[]';
$data = json_decode($jsonData, true);
if (!is_array($data)) {
    $data = [];
}

// New flexible filtering logic
$filteredData = array_filter($data, function($entry) use ($selectedTipe, $selectedStatus) {
    // Check Tipe: if 'semua', it's always a match. Otherwise, check the type.
    $tipeMatch = ($selectedTipe == 'semua') ? true : (isset($entry['tipeSidang']) && $entry['tipeSidang'] == $selectedTipe);

    // Check Status: if 'semua', it's always a match. Otherwise, check the boolean status.
    $statusMatch = true;
    if ($selectedStatus == 'disetujui') {
        $statusMatch = (isset($entry['statusPersetujuan']) && $entry['statusPersetujuan'] === true);
    } elseif ($selectedStatus == 'belum') {
        $statusMatch = (isset($entry['statusPersetujuan']) && $entry['statusPersetujuan'] === false);
    }

    return $tipeMatch && $statusMatch;
});

// 2. --- VARIABLES FOR BUTTON TEXT ---
$tipeButtonText = 'Semua Tipe';
if ($selectedTipe == 'TA') {
    $tipeButtonText = 'Sidang TA';
} elseif ($selectedTipe == 'Semester') {
    $tipeButtonText = 'Sidang Semester';
}

$statusButtonText = 'Semua Status';
if ($selectedStatus == 'disetujui') {
    $statusButtonText = 'Disetujui';
} elseif ($selectedStatus == 'belum') {
    $statusButtonText = 'Belum Disetujui';
}

// Determine dynamic header text based on Tipe filter
$dynamicHeaderText = 'Judul/Mata Kuliah';
if ($selectedTipe == 'TA') {
    $dynamicHeaderText = 'Judul Sidang';
} elseif ($selectedTipe == 'Semester') {
    $dynamicHeaderText = 'Mata Kuliah';
}

$dynamicDosenHeaderText = 'Pembimbing/Dosen';
if ($selectedTipe == 'TA') {
    $dynamicDosenHeaderText = 'Pembimbing';
} elseif ($selectedTipe == 'Semester') {
    $dynamicDosenHeaderText = 'Dosen Pengampu';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Penjadwalan Sidang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script><style>
    /* Copied and merged styles from aDaftarSidang.php for consistency */
    @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
    }

    body {
        min-height: 100vh;
        background-color: #FFFFFF;
    }

    #NavSide {
        display: flex;
        min-height: 100vh;
        position: relative;
    }

    .NavSide__sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: 280px;
        background: #4B68FB;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        transition: transform 0.4s ease-in-out;
    }

    .NavSide__sidebar-brand {
        padding: 10% 5% 50% 5%;
        text-align: center;
    }

    .NavSide__sidebar-brand img {
        width: 90%;
        max-width: 180px;
        height: auto;
    }

    .NavSide__sidebar-nav {
        width: 100%;
        padding: 0;
        list-style: none;
        flex-grow: 1;
    }

    .NavSide__sidebar-item {
        position: relative;
        display: block;
        width: 100%;
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
        margin-bottom: 10px;
        cursor: pointer;
    }

    .NavSide__sidebar-item a {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        text-decoration: none;
        color: #fff;
        padding: 5% 2%;
        height: 60px;
        box-sizing: border-box;
    }

    .NavSide__sidebar-item.NavSide__sidebar-item--active {
        background: #ffffff;
    }

    .NavSide__sidebar-item.NavSide__sidebar-item--active a {
        color: #4B68FB;
    }

    .NavSide__sidebar-item b:nth-child(1) {
        position: absolute;
        top: -20px;
        height: 20px;
        width: 100%;
        background: #fff;
        display: none;
    }

    .NavSide__sidebar-item b:nth-child(1)::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-bottom-right-radius: 20px;
        background: #4B68FB;
    }

    .NavSide__sidebar-item b:nth-child(2) {
        position: absolute;
        bottom: -20px;
        height: 20px;
        width: 100%;
        background: #fff;
        display: none;
    }

    .NavSide__sidebar-item b:nth-child(2)::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-top-right-radius: 20px;
        background: #4B68FB;
    }

    .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(1),
    .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(2) {
        display: block;
    }
    
    .detail-btn.action-disabled {
        cursor: not-allowed;
        opacity: 0.4;
    }

    .table-admin-custom tbody tr.isiTabel:hover .detail-btn.action-disabled {
        color: #4B68FB;
        opacity: 0.4;
    }

    .NavSide__main-content {
        flex-grow: 1;
        padding: 25px 30px;
        margin-left: 280px;
        transition: margin-left 0.4s ease-in-out;
    }

    .NavSide__topbar {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 60px;
        z-index: 900;
        background: #FFFFFF;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
    }

    .NavSide__toggle {
        font-size: 2rem;
        cursor: pointer;
        color: #4B68FB;
        z-index: 1002;
    }

    .NavSide__toggle .close {
        display: none;
    }

    .NavSide__toggle.NavSide__toggle--active .open {
        display: none;
    }

    .NavSide__toggle.NavSide__toggle--active .close {
        display: block;
    }

    .main-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
    }

    .header-left-panel {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .filter-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-container .filter-label {
        font-weight: 600;
        font-size: 1rem;
        color: #333;
    }

    .main-title {
        color: rgb(0, 0, 0);
        font-weight: 700;
        font-size: 2.1rem;
        margin-bottom: 1rem;
    }

    .header-right-panel {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.75rem;
    }

    .search-input-group {
        background-color: #F3F4F6;
        border-radius: 0.5rem;
        overflow: hidden;
        max-width: 250px;
        width: 100%;
    }

    .search-input-group input.form-control {
        background-color: transparent;
        border: none;
        box-shadow: none;
    }

    .search-input-group .input-group-text {
        background-color: transparent;
        border: none;
    }

    .header-icons {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .header-icons>a {
        font-size: 1.4rem;
        color: #5a5a5a;
        transition: color 0.2s ease;
    }

    .header-icons>a:hover {
        color: #4B68FB;
    }

    .header-right-panel .profile-icon,
    .NavSide__topbar .profile-icon {
        background-color: #333;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: transform 0.2s ease;
    }

    .header-right-panel .profile-icon:hover,
    .NavSide__topbar .profile-icon:hover {
        transform: scale(1.1);
    }

    .header-right-panel .profile-icon a,
    .NavSide__topbar .profile-icon a {
        color: white;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #ddAdminSidangTypeButton,
    #ddAdminSidangStatusButton {
        background-color: #4B68FB;
        border-color: #4B68FB;
        border-radius: 20px;
        padding: 10px 30px;
        font-weight: 500;
        box-shadow: none !important;
    }

    .table-admin-custom {
        border-spacing: 0 10px;
        border-collapse: separate;
        width: 100%;
    }

    .table-admin-custom thead th {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
    }

    .table-admin-custom tbody tr.isiTabel {
        background-color: #F5F5F5;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .table-admin-custom .isiTabel td {
        padding: 15px 18px;
        vertical-align: middle;
    }

    .table-admin-custom .isiTabel td:first-child {
        border-radius: 10px 0 0 10px;
    }

    .table-admin-custom .isiTabel td:last-child {
        border-radius: 0 10px 10px 0;
        text-align: center;
    }

    .table-admin-custom tbody tr.isiTabel:hover {
        background-color: #4B68FB;
        color: #FFFFFF;
    }

    .detail-btn {
        border: none !important;
        background-color: transparent !important;
        color: #4B68FB;
        padding: 0.25rem 0.5rem;
    }

    .detail-btn:hover {
        opacity: 0.7;
    }

    .table-admin-custom tbody tr.isiTabel:hover .detail-btn {
        color: #FFFFFF;
        opacity: 1;
    }

    .no-results-row td {
        text-align: center;
        padding: 20px !important;
        background-color: #f8f9fa !important;
        font-style: italic;
        color: #6c757d;
        border-radius: 10px;
    }

    .pagination-container {
        margin-top: 2rem;
    }

    .pagination .page-item.active .page-link {
        background-color: #4B68FB;
        border-color: #4B68FB;
        color: white;
        z-index: 2;
    }

    .pagination .page-link {
        color: #4B68FB;
    }

    .pagination .page-link:hover {
        color: #2c45c9;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
    }

    .modal-header-custom {
        background-color: #4B68FB;
        color: white;
    }

    .modal-footer .btn-danger {
        background-color: #FD7D7D;
        border-color: #FD7D7D;
    }

    .modal-footer .btn-success {
        background-color: #4FD382;
        border-color: #4FD382;
    }

    /* Unique Modal Styles from original aPenjadwalan.php */
    .modal-content-custom-form {
        border-radius: 25px !important;
    }

    .modal-body .form-container {
        padding: 15px;
        background-color: rgb(255, 255, 255);
        border-radius: 20px;
    }

    .modal-body .form-group {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .modal-body .form-group label {
        width: 160px;
        flex-shrink: 0;
        color: rgb(51, 47, 47);
        font-weight: bold;
        font-size: 14px;
        margin-right: 15px;
        text-align: left;
    }

    .modal-body .form-group .input-with-buttons,
    .modal-body .form-group .time-input-range,
    .modal-body .form-group>input {
        flex-grow: 1;
    }

    .modal-body .form-group input {
        width: 100%;
        height: 35px;
        padding: 0 15px;
        border: 1px solid #D1D5DB;
        box-sizing: border-box;
        font-size: 14px;
        color: #374151;
        border-radius: 26px;
    }

    .modal-body .form-group input[readonly] {
        background-color: #f3f4f6;
        cursor: not-allowed;
    }

    .modal-body .bobot-input-new {
        width: 30px;
        height: auto;
        text-align: center;
        border: none;
        font-size: 16px;
        color: #2d2d52;
        background-color: transparent;
        margin: 0 5px;
        -moz-appearance: textfield;
    }

    .modal-body .bobot-input-new::-webkit-outer-spin-button,
    .modal-body .bobot-input-new::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .modal-body .input-with-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
    }

    .modal-body .bobot-nilai-input-group {
        display: inline-flex;
        align-items: center;
        background-color: #F9FAFB;
        border-radius: 35px;
        padding: 2px 6px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    }

    .modal-body .btn-bobot-new {
        width: auto;
        height: auto;
        background-color: transparent;
        border: none;
        color: #2d2d52;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 8px;
        line-height: 1;
        border-radius: 35px;
        transition: background-color 0.2s ease;
    }

    .modal-body .btn-bobot-new:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .modal-body .time-input-range {
        display: flex;
        gap: 10px;
        width: 100%;
        align-items: center;
    }

    .modal-body .time-input-range .time-separator {
        color: #374151;
        font-size: 20px;
        font-weight: bold;
    }

    .modal-body .form-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 25px;
    }

    .modal-body .form-actions .btn-batal {
        background-color: #ff5f5f;
        color: rgb(255, 255, 255);
        border-radius: 20px;
        width: 120px;
        margin-right: 10px;
    }

    .modal-body .form-actions .btn-submit {
        background-color: rgb(67, 54, 240);
        color: white;
        border-radius: 20px;
        width: 200px;
    }

    .modal-body .form-actions .btn-submit:hover {
        background-color: rgb(106, 95, 255);
    }

    .modal-body .form-actions .btn {
        border: none;
        padding: 5px 10px;
        height: 40px;
    }

    .modal-body>h2 {
        font-size: 1.8rem;
        color: #374151;
        font-weight: 600;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .modal-body .form-toggle-buttons {
        display: inline-flex;
        gap: 5px;
        align-items: center;
    }

    .modal-body .form-toggle-buttons button {
        width: 30px;
        height: 30px;
        font-size: 18px;
        border-radius: 35px;
        border: 1px solid #ccc;
        cursor: pointer;
        background-color: white;
    }

    .modal-body .form-toggle-buttons button:hover {
        background-color: #ddd;
    }

    .form-error-message {
        color: red;
        margin-bottom: 15px;
        text-align: left;
        font-weight: 500;
        padding-left: 175px;
    }

    @media (max-width: 992px) {
        .NavSide__sidebar {
            transform: translateX(-280px);
        }

        .NavSide__sidebar.NavSide__sidebar--active-mobile {
            transform: translateX(0);
        }

        .NavSide__main-content {
            margin-left: 0;
            padding-top: 80px;
        }

        .NavSide__topbar {
            display: flex;
        }

        .NavSide__toggle {
            position: relative;
            top: auto;
            left: auto;
        }

        .NavSide__toggle.NavSide__toggle--active {
            transform: translateX(280px);
        }

        .search-input-group {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .main-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1.5rem;
        }

        .main-title {
            font-size: 1.8rem;
        }

        .table-responsive {
            overflow-x: hidden;
        }

        .table-admin-custom thead {
            display: none;
        }

        .table-admin-custom tbody,
        .table-admin-custom tr,
        .table-admin-custom td {
            display: block;
            width: 100%;
        }

        .table-admin-custom tr.isiTabel {
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .table-admin-custom td {
            display: flex;
            justify-content: space-between;
            padding: 12px 15px;
            text-align: right;
            border-bottom: 1px solid #e9e9e9;
            align-items: center;
        }

        .table-admin-custom tr.isiTabel:hover,
        .table-admin-custom tr.isiTabel:hover .detail-btn {
            background-color: #F5F5F5;
            color: #000;
        }

        .table-admin-custom tr.isiTabel:hover .detail-btn {
            color: #4B68FB;
        }

        .table-admin-custom td:first-child,
        .table-admin-custom td:last-child {
            border-radius: 0;
        }

        .table-admin-custom td:last-child {
            border-bottom: none;
            justify-content: center;
        }

        .table-admin-custom td::before {
            content: attr(data-label);
            font-weight: 600;
            text-align: left;
            margin-right: 10px;
            color: #333;
        }

        .modal-body .form-group {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }

        .modal-body .form-group label {
            width: 100%;
            margin-right: 0;
        }

        .modal-body .form-actions {
            flex-direction: column;
            gap: 10px;
        }

        .modal-body .form-actions .btn {
            width: 100%;
            margin-right: 0;
        }

        .form-error-message {
            padding-left: 0;
        }
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
            <li class="NavSide__sidebar-item">
                <b></b><b></b><a href="aBeranda.php"><span class="fw-semibold">Beranda</span></a>
            </li>
            <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                <b></b><b></b><a href="#"><span class="fw-semibold">Penjadwalan</span></a>
            </li>
            <li class="NavSide__sidebar-item">
                <b></b><b></b><a href="aDaftarSidang.php"><span class="fw-semibold">Daftar Sidang</span></a>
            </li>
            <li class="NavSide__sidebar-item">
                <b></b><b></b>
                <a href="#" data-bs-toggle="modal" data-bs-target="#logABeranda"><span class="fw-semibold">Keluar</span></a>
            </li>
        </ul>
    </div>

    <div class="NavSide__topbar">
        <div class="NavSide__toggle">
            <i class="bi bi-list open"></i>
            <i class="bi bi-x-lg close"></i>
        </div>
        <div id="mobile-icons-container"></div>
    </div>

    <main class="NavSide__main-content">
        <div class="main-header">
            <div class="header-left-panel">
                <h1 class="main-title">Penjadwalan Sidang</h1>
                <div class="filter-container">
                    <span class="filter-label fw-semibold">Filter:</span>
                    <div class="dropdown me-2">
                       <button class="btn btn-primary dropdown-toggle" type="button" id="ddAdminSidangTypeButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($tipeButtonText) ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?tipe=semua&status=<?= htmlspecialchars($selectedStatus) ?>">Semua Tipe</a></li>
                            <li><a class="dropdown-item" href="?tipe=TA&status=<?= htmlspecialchars($selectedStatus) ?>">Sidang TA</a></li>
                            <li><a class="dropdown-item" href="?tipe=Semester&status=<?= htmlspecialchars($selectedStatus) ?>">Sidang Semester</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="ddAdminSidangStatusButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($statusButtonText) ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?tipe=<?= htmlspecialchars($selectedTipe) ?>&status=semua">Semua Status</a></li>
                            <li><a class="dropdown-item" href="?tipe=<?= htmlspecialchars($selectedTipe) ?>&status=belum">Belum Disetujui</a></li>
                            <li><a class="dropdown-item" href="?tipe=<?= htmlspecialchars($selectedTipe) ?>&status=disetujui">Disetujui</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="header-right-panel">
                <div id="desktop-icons-container">
                    <div class="header-icons">
                        <a href="aNotifikasi.php" title="Notifikasi"><i class="bi bi-bell-fill"></i></a>
                        <div class="profile-icon"><a href="aProfil.php" title="Profil"><i class="bi bi-person-fill"></i></a></div>
                    </div>
                </div>
                <div class="input-group search-input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" placeholder="Cari Nama Mahasiswa..." aria-label="Cari">
                </div>
            </div>
        </div>

        <div class="table-responsive">
          <table class="table-admin-custom">
            <thead>
              <tr>
                <th scope="col">ID</th>
                <th scope="col">NIM</th>
                <th scope="col">Nama</th>
                <th scope="col"><?= htmlspecialchars($dynamicHeaderText) ?></th>
                <th scope="col"><?= htmlspecialchars($dynamicDosenHeaderText) ?></th>
                <th scope="col" style="text-align: center;">Aksi</th>
              </tr>
            </thead>
            <tbody id="adminSidangContent">
              <?php if (empty($filteredData)): ?>
                <tr class="no-results-row"><td colspan="6">Tidak ada data untuk ditampilkan.</td></tr>
              <?php else: ?>
                <?php foreach ($filteredData as $entry): ?>
                <?php
                    $row_props_js = "";
                    $action_button = "";
                    $dosen_pengampu_json = '[]';
                    $judul_or_matkul = 'N/A';
                    $pembimbing_or_pengampu = 'N/A';

                    // Determine content based on tipe sidang
                    if ($entry['tipeSidang'] == 'TA') {
                        $judul_or_matkul = htmlspecialchars($entry['judulSidang'] ?? 'N/A');
                        $pembimbing_or_pengampu = htmlspecialchars($entry['pembimbing'] ?? 'N/A');
                    } elseif ($entry['tipeSidang'] == 'Semester') {
                        $judul_or_matkul = htmlspecialchars($entry['mataKuliah'] ?? $entry['judulSidang'] ?? 'N/A');
                        if (isset($entry['dosenPengampu']) && is_array($entry['dosenPengampu'])) {
                           $dosen_pengampu_json = htmlspecialchars(json_encode($entry['dosenPengampu']), ENT_QUOTES, 'UTF-8');
                           $pembimbing_or_pengampu = htmlspecialchars(implode(', ', $entry['dosenPengampu']));
                        } else {
                           $pembimbing_or_pengampu = htmlspecialchars($entry['pembimbing'] ?? 'N/A');
                        }
                    }

                    if ($entry['statusPersetujuan'] === true) {
                        $row_props_js = "data-id='".htmlspecialchars($entry['id'] ?? '')."'"
                                    . " data-nim='".htmlspecialchars($entry['nim'] ?? '')."'"
                                    . " data-nama='".htmlspecialchars($entry['nama'] ?? '')."'"
                                    . " data-judul='". $judul_or_matkul ."'"
                                    . " data-pembimbing='". $pembimbing_or_pengampu ."'"
                                    . " data-prodi='".htmlspecialchars($entry['prodi'] ?? 'Teknologi Rekayasa Perangkat Lunak')."'"
                                    . " data-tipe-sidang='".htmlspecialchars($entry['tipeSidang'] ?? '')."'"
                                    . " data-pengampu='".$dosen_pengampu_json."'";
                        $action_button = "<button type=\"button\" class=\"btn detail-btn\" onclick='event.stopPropagation(); openJadwalModal(this.closest(\"tr\"))'>"
                                    . "<i class=\"fa-solid fa-file-signature fs-5\"></i>"
                                    . "</button>";
                    } else {
                        $action_button = "<button type=\"button\" class=\"btn detail-btn action-disabled\">"
                                       . "<i class=\"fa-solid fa-file-signature fs-5\"></i>"
                                       . "</button>";
                    }
                ?>
                <tr class="isiTabel" <?= $row_props_js ?>>
                  <td data-label="ID"><?= htmlspecialchars($entry['id'] ?? 'N/A') ?></td>
                  <td data-label="NIM"><?= htmlspecialchars($entry['nim'] ?? 'N/A') ?></td>
                  <td data-label="Nama"><?= htmlspecialchars($entry['nama'] ?? 'N/A') ?></td>
                  <td data-label="<?= $dynamicHeaderText ?>"><?= $judul_or_matkul ?></td>
                  <td data-label="<?= $dynamicDosenHeaderText ?>"><?= $pembimbing_or_pengampu ?></td>
                  <td data-label="Aksi" style="text-align: center;"><?= $action_button ?></td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        
        <div class="pagination-container">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center" id="pagination-controls"></ul>
            </nav>
        </div>
    </main>
  </div>
  
  <div class="modal fade" id="logABeranda" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h1 class="modal-title mx-auto fs-5" id="modalLogoutLabel">Perhatian!</h1>
            </div>
            <div class="modal-body text-center py-3">
                Apakah anda yakin ingin keluar?
            </div>
            <div class="modal-footer justify-content-center border-0">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batalkan</button>
                <button type="button" class="btn btn-success" onclick="window.location.href='../../logout.php'">Lanjutkan</button>
            </div>
        </div>
    </div>
  </div> 

  <!-- Modals for scheduling -->
  <div class="modal fade" id="penjadwalanSidangTAModal" aria-labelledby="penjadwalanSidangTAModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content modal-content-custom-form">
              <div class="modal-body">
                  <h2>Penjadwalan Sidang TA</h2>
                  <form id="formDalamModal-ta" novalidate>
                      <div class="form-container">
                          <div class="form-group"><label for="modal_nim-ta">NIM</label><input type="text" id="modal_nim-ta" readonly /></div>
                          <div class="form-group"><label for="modal_judul_sidang-ta">Judul Sidang</label><input type="text" id="modal_judul_sidang-ta" readonly /></div>
                          <div class="form-group"><label for="modal_pembimbing-ta">Pembimbing</label><input type="text" id="modal_pembimbing-ta" readonly /></div>
                          <div id="penguji-wrapper-ta">
                              <div class="form-group" id="penguji-form-ta-1">
                                  <label for="modal_penguji-ta-1">Penguji 1</label>
                                  <div class="input-with-buttons">
                                      <input type="text" id="modal_penguji-ta-1" name="penguji_nama[]" placeholder="Nama Penguji 1" />
                                      <div class="bobot-nilai-input-group">
                                          <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_penguji-ta-1')">-</button>
                                          <input type="number" id="modal_qty_penguji-ta-1" name="penguji_bobot[]" class="bobot-input-new" value="0" min="0" />
                                          <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_qty_penguji-ta-1')">+</button>
                                      </div>
                                      <div class="form-toggle-buttons">
                                          <button type="button" onclick="addPenguji()">+</button>
                                          <button type="button" onclick="removePenguji()">-</button>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="form-group"><label for="modal_prodi-ta">Prodi</label><input type="text" id="modal_prodi-ta" readonly /></div>
                          <div class="form-group"><label for="modal_ruangan-ta">Ruangan</label><input type="text" id="modal_ruangan-ta" name="ruangan" /></div>
                          <div class="form-group"><label for="modal_tanggal-ta">Tanggal</label><input type="date" id="modal_tanggal-ta" name="tanggal" /></div>
                          <div class="form-group">
                              <label for="modal_jam_awal-ta">Jam</label>
                              <div class="time-input-range">
                                  <input type="time" id="modal_jam_awal-ta" name="jam_awal" /><span class="time-separator">-</span><input type="time" id="modal_jam_akhir-ta" name="jam_akhir" />
                              </div>
                          </div>
                          <div class="form-error-message" id="form-error-ta"></div>
                          <div class="form-actions">
                              <button type="button" class="btn btn-batal" data-bs-dismiss="modal">Batalkan</button>
                              <button type="submit" class="btn btn-submit">Buat Penjadwalan</button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
  <div class="modal fade" id="penjadwalanSidangSemModal" aria-labelledby="penjadwalanSidangSemModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content modal-content-custom-form">
              <div class="modal-body">
                  <h2>Penjadwalan Sidang Semester</h2>
                  <form id="formDalamModal-sem" novalidate>
                      <div class="form-container">
                          <div class="form-group"><label for="modal_nim-sem">NIM</label><input type="text" id="modal_nim-sem" readonly /></div>
                          <div class="form-group"><label for="modal_matkul-sem">Mata Kuliah</label><input type="text" id="modal_matkul-sem" readonly /></div>
                          <div id="pengampu-wrapper-sem">
                              <div class="form-group" id="pengampu-form-sem-1">
                                  <label for="modal_pengampu-sem-1">Pengampu 1</label>
                                  <div class="input-with-buttons">
                                      <input type="text" id="modal_pengampu-sem-1" name="pengampu_nama[]" placeholder="Nama Pengampu 1" />
                                      <div class="bobot-nilai-input-group">
                                          <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_pengampu-sem-1')">-</button>
                                          <input type="number" id="modal_qty_pengampu-sem-1" name="pengampu_bobot[]" class="bobot-input-new" value="0" min="0" />
                                          <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_qty_pengampu-sem-1')">+</button>
                                      </div>
                                  </div>
                              </div>
                              <div class="form-group" id="pengampu-form-sem-2">
                                  <label for="modal_pengampu-sem-2">Pengampu 2</label>
                                  <div class="input-with-buttons">
                                      <input type="text" id="modal_pengampu-sem-2" name="pengampu_nama[]" placeholder="Nama Pengampu 2" />
                                      <div class="bobot-nilai-input-group">
                                          <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_pengampu-sem-2')">-</button>
                                          <input type="number" id="modal_qty_pengampu-sem-2" name="pengampu_bobot[]" class="bobot-input-new" value="0" min="0" />
                                          <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_qty_pengampu-sem-2')">+</button>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="form-group"><label for="modal_prodi-sem">Prodi</label><input type="text" id="modal_prodi-sem" readonly /></div>
                          <div class="form-group"><label for="modal_ruangan-sem">Ruangan</label><input type="text" id="modal_ruangan-sem" name="ruangan" /></div>
                          <div class="form-group"><label for="modal_tanggal-sem">Tanggal</label><input type="date" id="modal_tanggal-sem" name="tanggal" /></div>
                          <div class="form-group">
                              <label for="modal_jam_awal-sem">Jam</label>
                              <div class="time-input-range">
                                  <input type="time" id="modal_jam_awal-sem" name="jam_awal" /><span class="time-separator">-</span><input type="time" id="modal_jam_akhir-sem" name="jam_akhir" />
                              </div>
                          </div>
                          <div class="form-error-message" id="form-error-sem"></div>
                          <div class="form-actions">
                              <button type="button" class="btn btn-batal" data-bs-dismiss="modal">Batalkan</button>
                              <button type="submit" class="btn btn-submit">Buat Penjadwalan</button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    let taModalInstance, semModalInstance;
    let pengujiCount = 1;

    document.addEventListener("DOMContentLoaded", function() {
        // --- PAGINATION SCRIPT (FROM aDaftarSidang.php) ---
        let currentPage = 1;
        const rowsPerPage = 10;
        const tableBody = document.getElementById('adminSidangContent');
        const activeRows = Array.from(tableBody.querySelectorAll('tr.isiTabel'));
        const paginationControls = document.getElementById('pagination-controls');

        function displayPage(page) {
            currentPage = page;
            activeRows.forEach(row => row.style.display = 'none');
            const startIndex = (page - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;
            const paginatedRows = activeRows.slice(startIndex, endIndex);
            paginatedRows.forEach(row => { row.style.display = ''; });
            updatePaginationButtons();
        }

        function setupPagination() {
            paginationControls.innerHTML = '';
            const pageCount = Math.ceil(activeRows.length / rowsPerPage);
            if (pageCount <= 1) return;

            const prevButton = document.createElement('li');
            prevButton.className = 'page-item';
            prevButton.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">«</span></a>`;
            prevButton.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage > 1) displayPage(currentPage - 1);
            });
            paginationControls.appendChild(prevButton);

            for (let i = 1; i <= pageCount; i++) {
                const pageButton = document.createElement('li');
                pageButton.className = 'page-item';
                pageButton.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                pageButton.addEventListener('click', (e) => { e.preventDefault(); displayPage(i); });
                paginationControls.appendChild(pageButton);
            }

            const nextButton = document.createElement('li');
            nextButton.className = 'page-item';
            nextButton.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">»</span></a>`;
            nextButton.addEventListener('click', (e) => {
                e.preventDefault();
                const totalPages = Math.ceil(activeRows.length / rowsPerPage);
                if (currentPage < totalPages) displayPage(currentPage + 1);
            });
            paginationControls.appendChild(nextButton);
            updatePaginationButtons();
        }

        function updatePaginationButtons() {
            const pageCount = Math.ceil(activeRows.length / rowsPerPage);
            const pageItems = paginationControls.querySelectorAll('.page-item');
            if (pageItems.length === 0) return;
            pageItems.forEach((item, index) => {
                item.classList.remove('active', 'disabled');
                if (index === 0) { if (currentPage === 1) item.classList.add('disabled'); }
                else if (index === pageItems.length - 1) { if (currentPage === pageCount) item.classList.add('disabled'); }
                else { if (index === currentPage) item.classList.add('active'); }
            });
        }
        
        // Initialize Pagination
        if(activeRows.length > 0) {
            setupPagination();
            displayPage(1);
        }

        // --- SEARCH SCRIPT ---
        const searchInput = document.querySelector('.search-input-group .form-control');
        const noDataRow = document.querySelector('.no-results-row');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchText = this.value.toLowerCase().trim();
                let visibleRows = [];

                activeRows.forEach(row => {
                    const namaCell = row.cells[2];
                    const namaText = namaCell.textContent.toLowerCase();
                    if (namaText.includes(searchText)) {
                        visibleRows.push(row);
                    }
                });

                // Re-paginate the filtered results
                currentPage = 1;
                activeRows.forEach(row => row.style.display = 'none'); // Hide all original rows first
                
                const startIndex = (currentPage - 1) * rowsPerPage;
                const endIndex = startIndex + rowsPerPage;
                const paginatedVisibleRows = visibleRows.slice(startIndex, endIndex);

                paginatedVisibleRows.forEach(row => {
                    row.style.display = '';
                });

                // Update pagination controls for the new filtered set of rows
                const pageCount = Math.ceil(visibleRows.length / rowsPerPage);
                updatePaginationForSearch(pageCount, visibleRows);

                // Show/hide no results message
                if(noDataRow) noDataRow.style.display = visibleRows.length === 0 ? '' : 'none';
            });
        }

        function updatePaginationForSearch(pageCount, currentVisibleRows) {
            paginationControls.innerHTML = '';
            if (pageCount <= 1) return;

            const prevButton = document.createElement('li');
            prevButton.className = 'page-item';
            prevButton.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">«</span></a>`;
            prevButton.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage > 1) displaySearchedPage(currentPage - 1, currentVisibleRows);
            });
            paginationControls.appendChild(prevButton);

            for (let i = 1; i <= pageCount; i++) {
                const pageButton = document.createElement('li');
                pageButton.className = 'page-item';
                pageButton.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                pageButton.addEventListener('click', (e) => { e.preventDefault(); displaySearchedPage(i, currentVisibleRows); });
                paginationControls.appendChild(pageButton);
            }

            const nextButton = document.createElement('li');
            nextButton.className = 'page-item';
            nextButton.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">»</span></a>`;
            nextButton.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage < pageCount) displaySearchedPage(currentPage + 1, currentVisibleRows);
            });
            paginationControls.appendChild(nextButton);

            displaySearchedPage(1, currentVisibleRows);
        }

        function displaySearchedPage(page, searchedRows) {
            currentPage = page;
            activeRows.forEach(r => r.style.display = 'none');
            const startIndex = (page - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;
            const paginatedRows = searchedRows.slice(startIndex, endIndex);
            paginatedRows.forEach(row => { row.style.display = ''; });
            updatePaginationButtons(); // This function can be reused
        }
        
        // --- MOBILE MENU AND ICONS SCRIPT (from aDaftarSidang) ---
        const menuToggle = document.querySelector(".NavSide__toggle");
        const sidebar = document.getElementById("main-sidebar");
        const desktopIconsContainer = document.getElementById('desktop-icons-container');
        const mobileIconsContainer = document.getElementById('mobile-icons-container');
        const headerIcons = desktopIconsContainer.querySelector('.header-icons');

        function handleIconPlacement() {
            if (window.innerWidth <= 992) {
                if (!mobileIconsContainer.contains(headerIcons)) mobileIconsContainer.appendChild(headerIcons);
            } else {
                if (!desktopIconsContainer.contains(headerIcons)) desktopIconsContainer.appendChild(headerIcons);
            }
        }
        if (menuToggle && sidebar) {
            menuToggle.onclick = () => {
                menuToggle.classList.toggle("NavSide__toggle--active");
                sidebar.classList.toggle("NavSide__sidebar--active-mobile");
            };
        }
        handleIconPlacement();
        window.addEventListener('resize', handleIconPlacement);

        // --- ORIGINAL MODAL AND FORM SCRIPT ---
        const taModalEl = document.getElementById('penjadwalanSidangTAModal');
        if (taModalEl) taModalInstance = new bootstrap.Modal(taModalEl);
        
        const semModalEl = document.getElementById('penjadwalanSidangSemModal');
        if (semModalEl) semModalInstance = new bootstrap.Modal(semModalEl);

        const formTA = document.getElementById('formDalamModal-ta');
        if(formTA) formTA.addEventListener('submit', handleFormSubmit);

        const formSem = document.getElementById('formDalamModal-sem');
        if(formSem) formSem.addEventListener('submit', handleFormSubmit);
    });
    
    // Function to open modal (unchanged)
    function openJadwalModal(element) {
        const tipeSidang = element.dataset.tipeSidang;
        if (tipeSidang === 'TA') {
            resetAndPopulateTAModal(element);
            taModalInstance.show();
        } else if (tipeSidang === 'Semester') {
            populateSemModal(element);
            semModalInstance.show();
        }
    }
    // All other helper functions (resetAndPopulateTAModal, populateSemModal, etc.) remain the same
    function resetAndPopulateTAModal(el) {
        const wrapper = document.getElementById('penguji-wrapper-ta');
        wrapper.innerHTML = `
            <div class="form-group" id="penguji-form-ta-1">
                <label for="modal_penguji-ta-1">Penguji 1</label>
                <div class="input-with-buttons">
                    <input type="text" id="modal_penguji-ta-1" name="penguji_nama[]" placeholder="Nama Penguji 1" />
                    <div class="bobot-nilai-input-group">
                        <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_penguji-ta-1')">-</button>
                        <input type="number" id="modal_qty_penguji-ta-1" name="penguji_bobot[]" class="bobot-input-new" value="0" min="0" />
                        <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_qty_penguji-ta-1')">+</button>
                    </div>
                    <div class="form-toggle-buttons">
                        <button type="button" onclick="addPenguji()">+</button>
                        <button type="button" onclick="removePenguji()">-</button>
                    </div>
                </div>
            </div>`;
        pengujiCount = 1;
        updateToggleButtonsVisibility();
        document.getElementById('modal_nim-ta').value = el.dataset.nim || '';
        document.getElementById('modal_nim-ta').dataset.id = el.dataset.id || '';
        document.getElementById('modal_judul_sidang-ta').value = el.dataset.judul || '';
        document.getElementById('modal_pembimbing-ta').value = el.dataset.pembimbing || '';
        document.getElementById('modal_prodi-ta').value = el.dataset.prodi || '';
        document.getElementById('modal_ruangan-ta').value = '';
        document.getElementById('modal_tanggal-ta').value = '';
        document.getElementById('modal_jam_awal-ta').value = '';
        document.getElementById('modal_jam_akhir-ta').value = '';
        document.getElementById('form-error-ta').textContent = '';
    }
    function populateSemModal(el) {
        document.getElementById('modal_nim-sem').value = el.dataset.nim || '';
        document.getElementById('modal_nim-sem').dataset.id = el.dataset.id || '';
        document.getElementById('modal_matkul-sem').value = el.dataset.judul || '';
        document.getElementById('modal_prodi-sem').value = el.dataset.prodi || '';
        const pengampu = JSON.parse(el.dataset.pengampu || '[]');
        document.getElementById('modal_pengampu-sem-1').value = pengampu[0] || '';
        document.getElementById('modal_pengampu-sem-2').value = pengampu[1] || '';
        document.getElementById('modal_ruangan-sem').value = '';
        document.getElementById('modal_tanggal-sem').value = '';
        document.getElementById('modal_jam_awal-sem').value = '';
        document.getElementById('modal_jam_akhir-sem').value = '';
        document.getElementById('form-error-sem').textContent = '';
    }
    function addPenguji() {
        pengujiCount++;
        const wrapper = document.getElementById('penguji-wrapper-ta');
        const div = document.createElement('div');
        div.className = 'form-group';
        div.id = `penguji-form-ta-${pengujiCount}`;
        div.innerHTML = `
            <label for="modal_penguji-ta-${pengujiCount}">Penguji ${pengujiCount}</label>
            <div class="input-with-buttons">
                <input type="text" id="modal_penguji-ta-${pengujiCount}" name="penguji_nama[]" placeholder="Nama Penguji ${pengujiCount}" />
                <div class="bobot-nilai-input-group">
                    <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_penguji-ta-${pengujiCount}')">-</button>
                    <input type="number" id="modal_qty_penguji-ta-${pengujiCount}" name="penguji_bobot[]" class="bobot-input-new" value="0" min="0" />
                    <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_qty_penguji-ta-${pengujiCount}')">+</button>
                </div>
                <div class="form-toggle-buttons">
                    <button type="button" onclick="addPenguji()">+</button>
                    <button type="button" onclick="removePenguji()">-</button>
                </div>
            </div>`;
        wrapper.appendChild(div);
        updateToggleButtonsVisibility();
    }
    function removePenguji() {
        if (pengujiCount > 1) {
            const lastForm = document.getElementById(`penguji-form-ta-${pengujiCount}`);
            if (lastForm) { lastForm.remove(); pengujiCount--; }
        }
        updateToggleButtonsVisibility();
    }
    function updateToggleButtonsVisibility() {
        const toggleButtons = document.querySelectorAll('#penguji-wrapper-ta .form-toggle-buttons');
        toggleButtons.forEach((btnGroup, index) => {
            if (index === toggleButtons.length - 1) {
                btnGroup.style.display = 'inline-flex';
                const removeBtn = btnGroup.querySelector('button[onclick="removePenguji()"]');
                if (removeBtn) { removeBtn.style.display = (pengujiCount <= 1) ? 'none' : 'block'; }
            } else { btnGroup.style.display = 'none'; }
        });
    }
    function incrementValue(inputId) { const input = document.getElementById(inputId); if (input) input.value = parseInt(input.value, 10) + 1; }
    function decrementValue(inputId) {
        const input = document.getElementById(inputId);
        if (input) { let val = parseInt(input.value, 10); if (val > (input.min || 0)) { input.value = val - 1; } }
    }
    function handleFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const modalType = form.id.includes('-ta') ? 'TA' : 'Sem';
        if (validateForm(modalType)) {
            const modalInstance = modalType === 'TA' ? taModalInstance : semModalInstance;
            modalInstance.hide();
            Swal.fire({
                title: 'Berhasil', text: 'Jadwal Berhasil Dibuat.', imageUrl: '../../assets/img/centang.svg',
                imageWidth: 120, imageHeight: 120, imageAlt: 'Success checkmark',
                confirmButtonText: 'OK', confirmButtonColor: '#4336F0'
            }).then(() => { location.reload(); });
        }
    }
    function validateForm(modalType) {
        const suffix = modalType === 'TA' ? '-ta' : '-sem';
        const errorBox = document.getElementById(`form-error${suffix}`);
        errorBox.textContent = ''; let errorMessage = '';
        const dosenInputs = document.querySelectorAll(`input[name="${modalType === 'TA' ? 'penguji' : 'pengampu'}_nama[]"]`);
        for (let i = 0; i < dosenInputs.length; i++) { if (dosenInputs[i].value.trim() === '') { errorMessage = `Nama ${modalType === 'TA' ? 'Penguji' : 'Pengampu'} ${i + 1} harus diisi!`; break; } }
        if (errorMessage) { errorBox.textContent = errorMessage; return false; }
        const ruangan = document.getElementById(`modal_ruangan${suffix}`).value.trim();
        if (ruangan === '') { errorBox.textContent = 'Ruangan harus diisi!'; return false; }
        const tanggal = document.getElementById(`modal_tanggal${suffix}`).value;
        if (tanggal === '') { errorBox.textContent = 'Tanggal harus dipilih!'; return false; }
        const today = new Date(); const selectedDate = new Date(tanggal); today.setHours(0, 0, 0, 0); 
        if (selectedDate < today) { errorBox.textContent = 'Tanggal tidak boleh kurang dari tanggal hari ini!'; return false; }
        const jamAwal = document.getElementById(`modal_jam_awal${suffix}`).value;
        const jamAkhir = document.getElementById(`modal_jam_akhir${suffix}`).value;
        if (jamAwal === '' || jamAkhir === '') { errorBox.textContent = 'Jam awal dan jam akhir harus diisi!'; return false; }
        if (jamAkhir <= jamAwal) { errorBox.textContent = 'Jam akhir harus setelah jam awal!'; return false; }
        return true;
    }
</script>
</body>
</html>