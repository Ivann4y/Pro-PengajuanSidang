<?php
$selectedTipe = isset($_GET['tipe']) ? $_GET['tipe'] : 'TA';
$selectedStatus = isset($_GET['status']) ? $_GET['status'] : 'belum';
$statusFilter = ($selectedStatus == 'disetujui') ? true : false;

$jsonPath = __DIR__ . '/data_sidang.json'; 
$jsonData = file_exists($jsonPath) ? file_get_contents($jsonPath) : '[]';
$data = json_decode($jsonData, true);

if (!is_array($data)) {
    $data = [];
}

$filteredData = array_filter($data, function($entry) use ($selectedTipe, $statusFilter) {
    $tipeMatch = isset($entry['tipeSidang']) && $entry['tipeSidang'] == $selectedTipe;
    $statusMatch = isset($entry['statusPersetujuan']) && $entry['statusPersetujuan'] === $statusFilter;
    return $tipeMatch && $statusMatch;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <!-- <meta name="author" content="Dhonnan_aPenjadwaln_AliansiKelompok2"> -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>

    /* Sidebar and Topbar adjustments for smaller screens if not already present/correct */
    /* Ensure your NavSide__sidebar-brand img is horizontally centered */
    .NavSide__sidebar-brand img {
        display: inline-block; /* Ensure logo is centered */
    }

    /* Add cursor pointer for all sidebar items */
    .NavSide__sidebar-item {
        cursor: pointer;
    }

    /* Ensure the NavSide__topbar handles spacing and alignment for mobile */

    .NavSide__topbar i {
        display: flex;
        align-items: center;
        font-size: 2.5rem;
        color: #343a40;
        background-color: white; /* Ensure no background from old styles */
        width: auto;
        height: auto;
        border-radius: 0;
        font-weight: normal;
    }

    /* Header from aDaftarSidang.php */
    .main-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 3rem;
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
        color: #4B68FB;
        font-weight: 700;
        font-size: 2.1rem;
        margin-bottom: 1rem;
        padding-left:10px;
        margin-top: -0.5vh;
    }

    .header-right-panel {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.75rem;
    }

    /* Profile icon (merged from both, prioritizing aDaftarSidang's visual) */
    .profile-icon {
        font-size: 2.5rem;
        color: #343a40;
        background-color: transparent; /* Ensure no background from old styles */
        width: auto;
        height: auto;
        border-radius: 0;
        font-weight: normal;
        margin-top: -0.5vh;
        margin-right: 1vh;
    }

    /* Search input group from aDaftarSidang.php */
    .search-input-group {
        background-color: #F3F4F6;
        border-radius: 0.5rem;
        overflow: hidden;
        width: 250px; /* Standardize width as seen in aDaftarSidang.php */
        margin-top: 0.19vh -1px;
        margin-right : 1vh;
    }

    .search-input-group input.form-control {
        background-color: transparent;
        border: none;
        box-shadow: none;
        padding-left: 1rem; /* Adjust padding as search icon is inside span */
    }

    .search-input-group .input-group-text {
        background-color: transparent;
        border: none;
        padding-right: 0; /* No padding on right as input has left padding */
    }

    /* Filter Buttons (merged from aPenjadwalan's filter-btn and aDaftarSidang's ddAdminSidangTypeButton) */
    #ddAdminSidangTypeButton, #ddAdminSidangStatusButton { /* Apply to both filter buttons */
        background-color: #4B68FB; /* Specific blue from aPenjadwalan.php */
        color: white;
        border-radius: 20px;
        padding: 8px 20px;
        font-weight: 500;
        font-size: 0.95rem;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s ease, color 0.2s;
    }
    #ddAdminSidangTypeButton:hover, #ddAdminSidangStatusButton:hover {
        background-color: #312a9e;
    }

    /* Dropdown Menu styling (merged from both) */
    .dropdown-menu {
        border-radius: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: none;
        padding: 5px 0;
        background-color: #4538db;
    }

    .dropdown-item {
        padding: 10px 20px;
        font-size: 0.95rem;
        background-color: #4538db;
        color: rgb(180, 180, 180);
        transition: color 0.2s;
        border-radius: 20px; /* Apply border-radius to items for consistency */
    }

    .dropdown-item:hover,
    .dropdown-item:focus {
        background-color: #4538db;
        color: rgb(255, 255, 255);
    }

    .dropdown-toggle::after {
        vertical-align: 0.15em;
        margin-left: 0.5em;
    }

    /* Table styling (Prioritizing aDaftarSidang.php's table-admin-custom) */
    .table-admin-custom {
        border-spacing: 0 10px;
        border-collapse: separate;
        width: 100%;
        margin-top: 1rem; /* From aPenjadwalan.php's data-table */
        min-width: 800px; /* From aPenjadwalan.php's data-table */
    }

    .table-admin-custom thead th {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600; /* From aPenjadwalan.php's data-table */
        color: #555; /* From aPenjadwalan.php's data-table */
    }

    .table-admin-custom tbody tr.isiTabel {
        background-color: #F5F5F5;
        transition: background-color 0.3s ease, color 0.3s ease;
        cursor: pointer; /* Ensure clickable rows show cursor */
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
        text-align: center; /* For action column */
    }

    .table-admin-custom tbody tr.isiTabel:hover {
        background-color: #4B68FB;
        color: #FFFFFF;
    }

    /* No results row from aPenjadwalan.php */
    .no-results-row td {
        padding: 20px !important;
        background-color: #f8f9fa !important;
        font-style: italic;
        color: #6c757d;
        border-radius: 10px; /* Added for visual consistency with table rows */
    }

    /* Detail button for actions from aDaftarSidang.php */
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

    /* Modal styling from aDaftarSidang.php */
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

    /* Specific Modal Form styles from aPenjadwalan.php (keep these as they are unique to its modals) */
    .modal-content-custom-form { border-radius: 25px !important; }
    .modal-body .form-container { padding: 15px; background-color:rgb(255, 255, 255); border-radius: 20px; }
    .modal-body .form-group { display: flex; align-items: center; margin-bottom: 15px; }
    .modal-body .form-group label { width: 160px; flex-shrink: 0; color:rgb(51, 47, 47); font-weight: bold; font-size: 14px; margin-right: 15px; text-align: left; }
    .modal-body .form-group .input-with-buttons, .modal-body .form-group .time-input-range, .modal-body .form-group > input { flex-grow: 1; }
    .modal-body .form-group input { width: 100%; height: 35px; padding: 0 15px; border: 1px solid #D1D5DB; box-sizing: border-box; font-size: 14px; color: #374151; border-radius: 26px; }
    .modal-body .form-group input[readonly] { background-color: #f3f4f6; cursor: not-allowed; }
    .modal-body .bobot-input-new { width: 30px; height: auto; text-align: center; border: none; font-size: 16px; color: #2d2d52; background-color: transparent; margin: 0 5px; -moz-appearance: textfield; }
    .modal-body .bobot-input-new::-webkit-outer-spin-button, .modal-body .bobot-input-new::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .modal-body .input-with-buttons { display: flex; align-items: center; gap: 10px; width: 100%; }
    .modal-body .bobot-nilai-input-group { display: inline-flex; align-items: center; background-color: #F9FAFB; border-radius: 35px; padding: 2px 6px; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1); }
    .modal-body .btn-bobot-new { width: auto; height: auto; background-color: transparent; border: none; color: #2d2d52; font-size: 16px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 0 8px; line-height: 1; border-radius: 35px; transition: background-color 0.2s ease; }
    .modal-body .btn-bobot-new:hover { background-color:rgba(0, 0, 0, 0.05); }
    .modal-body .time-input-range { display: flex; gap: 10px; width: 100%; align-items: center; }
    .modal-body .time-input-range .time-separator { color: #374151; font-size: 20px; font-weight: bold; }
    .modal-body .form-actions { display: flex; justify-content: flex-end; margin-top: 25px; }
    .modal-body .form-actions .btn-batal { background-color: #ff5f5f; color:rgb(255, 255, 255); border-radius: 20px; width: 120px; margin-right: 10px; }
    .modal-body .form-actions .btn-submit { background-color:rgb(67, 54, 240); color: white; border-radius: 20px; width: 200px; }
    .modal-body .form-actions .btn-submit:hover { background-color: rgb(106, 95, 255); }
    .modal-body .form-actions .btn { border: none; padding: 5px 10px; height: 40px; }
    .modal-body > h2 { font-size: 1.8rem; color: #374151; font-weight: 600; margin-bottom: 1.5rem; text-align:center; }
    .modal-body .form-toggle-buttons { display: inline-flex; gap: 5px; align-items: center; }
    .modal-body .form-toggle-buttons button { width: 30px; height: 30px; font-size: 18px; border-radius: 35px; border: 1px solid #ccc; cursor: pointer; background-color: white; }
    .modal-body .form-toggle-buttons button:hover { background-color: #ddd; }
    .form-error-message { color: red; margin-bottom: 15px; text-align: left; font-weight: 500; padding-left: 175px;}

    @media (max-width: 940px) {
    /* 1. Allow the main header content to wrap to the next line */
    .main-header {
        position: relative; /* Needed for absolute positioning of the icon */
        flex-wrap: wrap; /* Allows the right panel to drop below */
    }

    /* 2. Move the profile icon to the top right of the header */
    .main-header .header-right-panel .profile-icon {
        position: absolute;
        top: 0;
        right: 0;
        font-size: 2.2rem; /* Optional: adjust size */
    }
    .profile-icon {
        margin-top: -5px;
    }
    /* 3. Make the right panel (now just the search bar) take up the full width */
    .header-right-panel {
        width: 100%;
        margin-top: 1rem; /* Add space between the filters and the search bar */
        /* Override column layout as it's no longer needed */
        flex-direction: row; 
        align-items: flex-start;
    }

    /* 4. Make the search bar fill the available width */
    .search-input-group {
        width: 50% !important; /* Use !important to ensure it overrides inline styles if any */
        }
    }

    /* Responsive adjustments for the new main header structure */
    @media (max-width: 700px) {
        .main-header {
            padding-top: 0;
        }
        #NavSide .main-header {
            flex-direction: column;
            align-items: stretch !important;
            gap: 15px;
        }
        #NavSide .search-input-group { width: 100%; }

        #NavSide .filter-container {
            flex-wrap: wrap;
            gap: 10px;
        }
        #NavSide .filter-container .dropdown { 
            margin-right: 0 !important; 
            color: #4B68FB;
        } /* Overrides Bootstrap default margin */

        /* Adjust main content padding when topbar is active */
        #NavSide .NavSide__main-content {
            padding-top: 75px; /* Increase this if needed to prevent content overlap with topbar */
        }

        #NavSide .modal-body .form-group {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
        #NavSide .modal-body .form-group label { width: 100%; margin-right: 0; }
        #NavSide .modal-body .form-actions { flex-direction: column; gap: 10px; }
        #NavSide .modal-body .form-actions .btn { width: 100%; margin-right: 0; }
        #NavSide .form-error-message { padding-left: 0; }

       #NavSide .main-header .header-right-panel .profile-icon {
        display: none; /* Correct value is 'none', not 'hide' */
    }
    }
  </style>
</head>
<body>
<body>
  <div id="NavSide">

    <!-- Mobile Topbar with Toggle and Profile Icon -->
    <div class="NavSide__topbar">
      <div class="NavSide__toggle" id="nav-toggle">
          <i class="bi bi-list open"></i>  
          <i class="bi bi-x-lg close"></i> 
      </div>
      <div class="header-icons">
          <div class="profile-icon">
              <i class="bi bi-person-circle"></i>
          </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div id="main-sidebar" class="NavSide__sidebar">
        <div class="NavSide__sidebar-brand">
            <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
        </div>
        <ul class="NavSide__sidebar-nav">
            <li class="NavSide__sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'aBeranda.php' ? 'NavSide__sidebar-item--active' : ''); ?>">
                <b></b><b></b><a href="aBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
            </li>
            <li class="NavSide__sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'aPenjadwalan.php' ? 'NavSide__sidebar-item--active' : ''); ?>">
                <b></b><b></b><a href="aPenjadwalan.php"><span class="NavSide__sidebar-title fw-semibold">Penjadwalan</span></a>
            </li>
            <li class="NavSide__sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'aDaftarSidang.php' ? 'NavSide__sidebar-item--active' : ''); ?>">
                <b></b><b></b><a href="aDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span></a>
            </li>
            <li class="NavSide__sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'NavSide__sidebar-item--active' : ''); ?>">
                <b></b><b></b>
                    <a href="logout.html" data-bs-toggle="modal" data-bs-target="#logABeranda"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
            </li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <main class="NavSide__main-content">
        <div class="main-header">
            <div class="header-left-panel">
                <h1 class="main-title">Penjadwalan Sidang</h1>
                <div class="filter-container">
                    <span class="filter-label fw-semibold">Filter:</span>
                    <div class="dropdown me-2">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="ddAdminSidangTypeButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($selectedTipe == 'TA' ? 'Sidang TA' : 'Sidang Semester') ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?tipe=TA&status=<?= htmlspecialchars($selectedStatus) ?>">Sidang TA</a></li>
                            <li><a class="dropdown-item" href="?tipe=Semester&status=<?= htmlspecialchars($selectedStatus) ?>">Sidang Semester</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="ddAdminSidangStatusButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($selectedStatus == 'belum' ? 'Belum Disetujui' : 'Disetujui') ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?tipe=<?= htmlspecialchars($selectedTipe) ?>&status=belum">Belum Disetujui</a></li>
                            <li><a class="dropdown-item" href="?tipe=<?= htmlspecialchars($selectedTipe) ?>&status=disetujui">Disetujui</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="header-right-panel">
                <div class="profile-icon">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="input-group search-input-group" style="width: 250px;">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" placeholder="Cari Nama Mahasiswa..." aria-label="Cari">
                </div>
            </div>
        </div>

        <div class="table-container">
          <table class="table-admin-custom">
            <thead>
              <tr>
                <th>ID</th>
                <th>NIM</th>
                <th>Nama</th>
                <th><?= htmlspecialchars($selectedTipe == 'TA' ? 'Judul Sidang' : 'Mata Kuliah') ?></th>
                <th><?= htmlspecialchars($selectedTipe == 'TA' ? 'Pembimbing' : 'Dosen Pengampu') ?></th>
                <th style="text-align: center;">Aksi</th>
              </tr>
            </thead>
            <tbody id="adminSidangContent">
              <?php if (empty($filteredData)): ?>
                <tr class="no-results-row"><td colspan="6" class="text-center">Tidak ada data untuk ditampilkan.</td></tr>
              <?php else: ?>
                <?php foreach ($filteredData as $entry): ?>
                <?php
                    $row_props_js = "";
                    $action_button = "";
                    if ($selectedStatus == 'disetujui') {
                        $dosen_pengampu_json = isset($entry['dosenPengampu']) ? htmlspecialchars(json_encode($entry['dosenPengampu']), ENT_QUOTES, 'UTF-8') : '[]';
                        $row_props_js = "data-id='".htmlspecialchars($entry['id'] ?? '')."'"
                                    . "data-nim='".htmlspecialchars($entry['nim'] ?? '')."'"
                                    . "data-nama='".htmlspecialchars($entry['nama'] ?? '')."'"
                                    . "data-judul='".htmlspecialchars($entry['judulSidang'] ?? '')."'"
                                    . "data-pembimbing='".htmlspecialchars($entry['pembimbing'] ?? '')."'"
                                    . "data-prodi='".htmlspecialchars($entry['prodi'] ?? 'Teknologi Rekayasa Perangkat Lunak')."'"
                                    . "data-tipe-sidang='".htmlspecialchars($entry['tipeSidang'] ?? '')."'"
                                    . "data-pengampu='".$dosen_pengampu_json."'";
                        $action_button = "<button type=\"button\" class=\"btn detail-btn\" onclick='event.stopPropagation(); openJadwalModal(this.closest(\"tr\"))'>"
                                    . "<i class=\"bi bi-pencil-square fs-5\"></i>"
                                    . "</button>";
                    } else {
                        $action_button = "Tidak Ada";
                    }
                ?>
                <tr class="isiTabel" <?= $row_props_js ?>>
                  <td><?= htmlspecialchars($entry['id'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($entry['nim'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($entry['nama'] ?? 'N/A') ?></td>
                  <td>
                    <?php 
                        if ($selectedTipe == 'TA' && isset($entry['judulSidang'])) {
                            echo htmlspecialchars($entry['judulSidang']);
                        } elseif ($selectedTipe == 'Semester' && isset($entry['mataKuliah'])) {
                            echo htmlspecialchars($entry['mataKuliah']);
                        } else {
                            echo 'N/A';
                        }
                    ?>
                  </td>
                  <td>
                    <?php 
                        if ($selectedTipe == 'TA') {
                            echo htmlspecialchars($entry['pembimbing'] ?? 'N/A');
                        } else {
                            echo isset($entry['dosenPengampu']) && is_array($entry['dosenPengampu']) ? htmlspecialchars(implode(', ', $entry['dosenPengampu'])) : htmlspecialchars($entry['pembimbing'] ?? 'N/A');
                        }
                    ?>
                  </td>
                  <td style="text-align: center;"><?= $action_button ?></td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
    </main>
  </div> <!-- This now correctly closes the #NavSide wrapper -->
  
  <!-- Modals are placed outside the #NavSide wrapper -->
  <div class="modal fade" id="logABeranda" tabindex="-1" aria-labelledby="modalLogoutLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div style="background-color:#4B68FB;">
                <div class="modal-header">
                    <h1 class="modal-title mx-auto fs-5 text-light" id="modalLogoutLabel">Perhatian!</h1>
                </div>
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
        // Updated search input selector to match the new structure
        const searchInput = document.querySelector('.search-input-group .form-control');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchText = this.value.toLowerCase();
                // Updated table class for selection
                const tableRows = document.querySelectorAll('.table-admin-custom tbody tr');
                let anyVisible = false;

                tableRows.forEach(row => {
                    if (row.classList.contains('no-results-row')) {
                        row.style.display = 'none'; // Initially hide the no results row
                        return;
                    }

                    const namaCell = row.cells[2]; // Assuming Name is in the 3rd column (index 2)
                    if (!namaCell) return;

                    const namaText = namaCell.textContent.toLowerCase();
                    if (namaText.includes(searchText)) {
                        row.style.display = '';
                        anyVisible = true;
                    } else {
                        row.style.display = 'none';
                    }
                });

                const tbody = document.querySelector('.table-admin-custom tbody');
                let noDataRow = document.querySelector('.no-results-row');

                if (!anyVisible && searchText !== '') {
                    if (!noDataRow) {
                        // Create and append if not exists
                        noDataRow = document.createElement('tr');
                        noDataRow.className = 'no-results-row';
                        noDataRow.innerHTML = '<td colspan="6" class="text-center">Tidak ditemukan data yang sesuai</td>';
                        tbody.appendChild(noDataRow);
                    } else {
                         noDataRow.style.display = ''; // Show existing no results row
                    }
                } else if (noDataRow) {
                    noDataRow.style.display = 'none'; // Hide if there are results or search is empty
                }
            });
        }
    });
    
    document.addEventListener("DOMContentLoaded", function() {
        const toggleButton = document.getElementById('nav-toggle');
        const sidebar = document.getElementById('main-sidebar');

        if (toggleButton && sidebar) {
            toggleButton.addEventListener('click', () => {
                // Adopted the class toggling from aDaftarSidang.php for consistency
                toggleButton.classList.toggle('NavSide__toggle--active');
                sidebar.classList.toggle('NavSide__sidebar--active-mobile');
            });
        }
        
        const taModalEl = document.getElementById('penjadwalanSidangTAModal');
        if (taModalEl) taModalInstance = new bootstrap.Modal(taModalEl);
        
        const semModalEl = document.getElementById('penjadwalanSidangSemModal');
        if (semModalEl) semModalInstance = new bootstrap.Modal(semModalEl);

        const formTA = document.getElementById('formDalamModal-ta');
        if(formTA) formTA.addEventListener('submit', handleFormSubmit);

        const formSem = document.getElementById('formDalamModal-sem');
        if(formSem) formSem.addEventListener('submit', handleFormSubmit);
    });

    // openJadwalModal now directly takes the table row element
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
        updateToggleButtonsVisibility(); // Ensure buttons are correct after reset

        // Populate modal fields
        document.getElementById('modal_nim-ta').value = el.dataset.nim || '';
        document.getElementById('modal_nim-ta').dataset.id = el.dataset.id || ''; // Store original ID for submission
        document.getElementById('modal_judul_sidang-ta').value = el.dataset.judul || '';
        document.getElementById('modal_pembimbing-ta').value = el.dataset.pembimbing || '';
        document.getElementById('modal_prodi-ta').value = el.dataset.prodi || '';
        // Clear dynamic inputs for new entry
        document.getElementById('modal_ruangan-ta').value = '';
        document.getElementById('modal_tanggal-ta').value = '';
        document.getElementById('modal_jam_awal-ta').value = '';
        document.getElementById('modal_jam_akhir-ta').value = '';
        document.getElementById('form-error-ta').textContent = ''; // Clear error message
    }

    function populateSemModal(el) {
        document.getElementById('modal_nim-sem').value = el.dataset.nim || '';
        document.getElementById('modal_nim-sem').dataset.id = el.dataset.id || ''; // Store original ID for submission
        document.getElementById('modal_matkul-sem').value = el.dataset.judul || ''; // In this context, 'judul' refers to mata kuliah for semester
        document.getElementById('modal_prodi-sem').value = el.dataset.prodi || '';
        
        const pengampu = JSON.parse(el.dataset.pengampu || '[]');
        document.getElementById('modal_pengampu-sem-1').value = pengampu[0] || '';
        document.getElementById('modal_pengampu-sem-2').value = pengampu[1] || '';
        // Clear dynamic inputs for new entry
        document.getElementById('modal_ruangan-sem').value = '';
        document.getElementById('modal_tanggal-sem').value = '';
        document.getElementById('modal_jam_awal-sem').value = '';
        document.getElementById('modal_jam_akhir-sem').value = '';
        document.getElementById('form-error-sem').textContent = ''; // Clear error message
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
            </div>`; // Ensure new rows have +/- buttons
        wrapper.appendChild(div);
        updateToggleButtonsVisibility();
    }

    function removePenguji() {
        if (pengujiCount > 1) {
            const lastForm = document.getElementById(`penguji-form-ta-${pengujiCount}`);
            if (lastForm) {
                lastForm.remove();
                pengujiCount--;
            }
        }
        updateToggleButtonsVisibility();
    }

    function updateToggleButtonsVisibility() {
        const toggleButtons = document.querySelectorAll('#penguji-wrapper-ta .form-toggle-buttons');
        toggleButtons.forEach((btnGroup, index) => {
            if (index === toggleButtons.length - 1) { // Only the last added row has the buttons
                btnGroup.style.display = 'inline-flex';
                // Hide '-' button if only one penguji
                const removeBtn = btnGroup.querySelector('button[onclick="removePenguji()"]');
                if (removeBtn) {
                    removeBtn.style.display = (pengujiCount <= 1) ? 'none' : 'block';
                }
            } else {
                btnGroup.style.display = 'none'; // Hide buttons on previous rows
            }
        });
    }
    
    function incrementValue(inputId) {
        const input = document.getElementById(inputId);
        if (input) input.value = parseInt(input.value, 10) + 1;
    }

    function decrementValue(inputId) {
        const input = document.getElementById(inputId);
        if (input) {
            let val = parseInt(input.value, 10);
            if (val > (input.min || 0)) {
                input.value = val - 1;
            }
        }
    }

    function handleFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const modalType = form.id.includes('-ta') ? 'TA' : 'Sem';
        
        if (validateForm(modalType)) {
            const modalInstance = modalType === 'TA' ? taModalInstance : semModalInstance;
            
            // Get original ID from hidden dataset
            const originalId = modalType === 'TA' ? document.getElementById('modal_nim-ta').dataset.id : document.getElementById('modal_nim-sem').dataset.id;
            
            // Here you would gather form data and send to server
            const formData = new FormData(form);
            // Add originalId to formData if needed by server
            formData.append('originalId', originalId);
            formData.append('tipeSidang', modalType); // Indicate type of sidang being scheduled

            // Example of how to send data (replace with your actual backend endpoint)
            /*
            fetch('your_schedule_api.php', {
                method: 'POST',
                body: formData // Use formData directly for multipart/form-data
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modalInstance.hide();
                    Swal.fire({
                        title: 'Berhasil',
                        text: 'Jadwal Berhasil Dibuat.',
                        imageUrl: '../../assets/img/centang.svg',
                        imageWidth: 120,
                        imageHeight: 120,
                        imageAlt: 'Success checkmark',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4336F0'
                    }).then(() => {
                        location.reload(); 
                    });
                } else {
                    Swal.fire('Gagal', data.message || 'Gagal membuat jadwal.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
            });
            */

            // Simulating success for now
            modalInstance.hide();
            Swal.fire({
                title: 'Berhasil',
                text: 'Jadwal Berhasil Dibuat.',
                imageUrl: '../../assets/img/centang.svg',
                imageWidth: 120,
                imageHeight: 120,
                imageAlt: 'Success checkmark',
                confirmButtonText: 'OK',
                confirmButtonColor: '#4336F0'
            }).then(() => {
                location.reload(); 
            });
        }
    }

    function validateForm(modalType) {
        const suffix = modalType === 'TA' ? '-ta' : '-sem';
        const errorBox = document.getElementById(`form-error${suffix}`);
        errorBox.textContent = '';
        let errorMessage = '';

        const dosenInputs = document.querySelectorAll(`input[name="${modalType === 'TA' ? 'penguji' : 'pengampu'}_nama[]"]`);
        for (let i = 0; i < dosenInputs.length; i++) {
            if (dosenInputs[i].value.trim() === '') {
                errorMessage = `Nama ${modalType === 'TA' ? 'Penguji' : 'Pengampu'} ${i + 1} harus diisi!`;
                break;
            }
        }
        if (errorMessage) { 
            errorBox.textContent = errorMessage; 
            return false; 
        }
        
        const ruangan = document.getElementById(`modal_ruangan${suffix}`).value.trim();
        if (ruangan === '') {
            errorBox.textContent = 'Ruangan harus diisi!';
            return false;
        }

        const tanggal = document.getElementById(`modal_tanggal${suffix}`).value;
        if (tanggal === '') {
            errorBox.textContent = 'Tanggal harus dipilih!';
            return false;
        }
        const today = new Date();
        const selectedDate = new Date(tanggal);
        today.setHours(0, 0, 0, 0); 
        if (selectedDate < today) {
            errorBox.textContent = 'Tanggal tidak boleh kurang dari tanggal hari ini!';
            return false;
        }

        const jamAwal = document.getElementById(`modal_jam_awal${suffix}`).value;
        const jamAkhir = document.getElementById(`modal_jam_akhir${suffix}`).value;
        if (jamAwal === '' || jamAkhir === '') {
            errorBox.textContent = 'Jam awal dan jam akhir harus diisi!';
            return false;
        }
        if (jamAkhir <= jamAwal) {
            errorBox.textContent = 'Jam akhir harus setelah jam awal!';
            return false;
        }

        return true;
    }
</script>

</body>
</html>