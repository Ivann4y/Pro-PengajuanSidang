<?php
// Read and filter JSON data
$selectedTipe = isset($_GET['tipe']) ? $_GET['tipe'] : 'TA';
$selectedStatus = isset($_GET['status']) ? $_GET['status'] : 'belum';
$statusFilter = ($selectedStatus == 'disetujui') ? true : false;

// Path to your JSON file - using __DIR__ makes it more reliable
$jsonPath = __DIR__ . '/data_sidang.json'; 
$jsonData = file_exists($jsonPath) ? file_get_contents($jsonPath) : '[]';
$data = json_decode($jsonData, true);

// Fallback for cases where json is not an array or file is missing
if (!is_array($data)) {
    $data = [];
}

$filteredData = array_filter($data, function($entry) use ($selectedTipe, $statusFilter) {
    // Ensure keys exist to avoid warnings
    $tipeMatch = isset($entry['tipeSidang']) && $entry['tipeSidang'] == $selectedTipe;
    $statusMatch = isset($entry['statusPersetujuan']) && $entry['statusPersetujuan'] === $statusFilter;
    return $tipeMatch && $statusMatch;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Linking to your existing style.css file -->
  <link rel="stylesheet" href="../../assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    /* CSS for page-specific elements NOT in the main style.css */
    /* NOTE: General layout styles (like margins for the container) are now handled by style.css */

    .no-results-row td {
        padding: 20px !important;
        background-color: #f8f9fa !important;
        font-style: italic;
        color: #6c757d;
    }
    .search-input-container {
      position: relative;
      width: 300px;
      height: 45px;
    }

    .search-input {
      border-radius: 25px;
      padding: 8px 15px 8px 45px;
      border: 1px solid #ccc;
      width: 100%;
      background-color: #e9e9e9;
      height: 100%;
      outline: none;
      font-size: 1rem;
    }

    .search-input::placeholder {
      color: #888;
    }

    .search-input:focus {
      border-color: #4538db;
      box-shadow: 0 0 0 0.25rem rgba(69, 56, 219, 0.25);
      background-color: white;
    }

    .search-input-container .fa-search {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 1.1rem;
      color: #777;
    }

    /* Your profile icon is inside .dashboard-header in style.css, but if you need to override: */
    .profile-icon {
      font-size: 1.8rem;
      color: #444;
      cursor: pointer;
      transition: color 0.3s;
    }

    .profile-icon:hover {
      color: #007bff;
    }

    .filter-btn {
      background-color: #4538db;
      color: white;
      border-radius: 20px;
      padding: 8px 20px;
      font-weight: 500;
      font-size: 0.95rem;
      border: none;
      cursor: pointer;
      margin-right: 15px;
      transition: background-color 0.2s ease, color 0.2s;
    }
    .filter-btn:hover {
      background-color: #312a9e;
    }

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
        color:rgb(180, 180, 180);
        transition: color 0.2s;
        border-radius: 20px;
    }

    .dropdown-item:hover,
    .dropdown-item:focus {
        background-color: #4538db;
        color:rgb(255, 255, 255);
    }
    
    .dropdown-toggle::after {
        vertical-align: 0.15em;
        margin-left: 0.5em;
    }

    .table-container {
      margin-top: 30px;
      width: 100%;
      overflow-x: auto;
    }

    .data-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 15px;
      margin-top: 1rem;
      min-width: 800px;
    }

    .data-table thead th {
      padding: 0 20px 15px 20px;
      border-bottom: 1px solid #ccc;
      font-weight: 600;
      color: #555;
      text-align: left;
    }

    .data-table tbody td {
      background-color: #f5f5f5;
      padding: 15px 20px;
      vertical-align: middle;
      transition: all 0.3s ease;
    }
    
    .data-table tbody tr:hover td {
      background-color: #4538db;
      color: white;
    }

    .data-table tbody tr.clickable:hover{
        cursor: pointer;
    }

    .data-table tbody tr td:first-child { border-radius: 15px 0 0 15px; }
    .data-table tbody tr td:last-child { border-radius: 0 15px 15px 0; }
    
    /* MODAL STYLES */
    .modal-content-custom-form { border-radius: 25px !important; }
    .modal-body .form-container { padding: 15px; background-color:rgb(255, 255, 255); border-radius: 20px; }
    .modal-body .form-group { display: flex; align-items: center; margin-bottom: 15px; }
    .modal-body .form-group label { width: 160px; flex-shrink: 0; color:rgb(51, 47, 47); font-weight: bold; font-size: 14px; margin-right: 15px; text-align: left; }
    .modal-body .form-group .input-with-buttons, .modal-body .form-group .time-input-range, .modal-body .form-group > input { flex-grow: 1; }
    .modal-body .form-group input { width: 100%; height: 35px; padding: 0 15px; border: 1px solid #D1D5DB; box-sizing: border-box; font-size: 14px; color: #374151; border-radius: 26px; }
    .modal-body .form-group input[readonly] { background-color: #f3f4f6; cursor: not-allowed; }
    .modal-body .bobot-input-new { width: 30px; height: auto; text-align: center; border: none; font-size: 16px; color: #2d2d52; background-color: transparent; margin: 0 5px; -moz-appearance: textfield; pointer-events: none; }
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
    
    @media (max-width: 768px) { /* This media query is slightly different than style.css, which is fine for page-specific overrides */
        .main-header-content {
            flex-direction: column;
            align-items: stretch !important;
            gap: 15px;
        }
        .search-input-container { width: 100%; }
        
        #dropdownAdminPenjadwalan {
            flex-wrap: wrap;
            gap: 10px;
        }
        #dropdownAdminPenjadwalan .filter-btn { margin-right: 0; }
        
        /* Responsive modal forms */
        .modal-body .form-group {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
        .modal-body .form-group label { width: 100%; margin-right: 0; }
        .modal-body .form-actions { flex-direction: column; gap: 10px; }
        .modal-body .form-actions .btn { width: 100%; margin-right: 0; }
        .form-error-message { padding-left: 0; }
    }
  </style>
</head>
<body>

  <!-- MOBILE TOP BAR: The logic for this is in style.css -->
  <div class="NavSide__topbar">
      <div class="NavSide__toggle" id="nav-toggle">
          <i class="fas fa-bars"></i>
      </div>
  </div>
  
  <!-- Sidebar: Your style.css refers to this as .NavSide__sidebar -->
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
        <li class="NavSide__sidebar-item">
            <b></b><b></b><a href="#" data-bs-toggle="modal" data-bs-target="#logout"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
        </li>
    </ul>
  </div>

  <!-- CORRECTED: Main Content Container, using the class from your style.css -->
  <main class="NavSide__main-content">
    
    <!-- CORRECTED: Header structure to match style.css for proper layout -->
    <header class="dashboard-header">
        <h1 class="page-title">Penjadwalan Sidang</h1>
        <div class="header-icons">
             <div class="profile-icon">
                <i class="fas fa-user-circle"></i>
            </div>
        </div>
    </header>

     <div class="d-flex justify-content-between align-items-center mb-3 main-header-content">
      <h2 class="section-title ">Pengajuan Sidang</h2>
      <div class="search-input-container">
        <i class="fas fa-search"></i>
        <input type="text" class="form-control search-input" placeholder="Cari Nama Mahasiswa...">
      </div>
    </div>
   
    <div class="d-flex" id="dropdownAdminPenjadwalan">
        <div class="dropdown me-2">
            <button class="filter-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?= htmlspecialchars($selectedTipe == 'TA' ? 'Sidang TA' : 'Sidang Semester') ?>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?tipe=TA&status=<?= htmlspecialchars($selectedStatus) ?>">Sidang TA</a></li>
                <li><a class="dropdown-item" href="?tipe=Semester&status=<?= htmlspecialchars($selectedStatus) ?>">Sidang Semester</a></li>
            </ul>
        </div>
        <div class="dropdown me-2">
            <button class="filter-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?= htmlspecialchars($selectedStatus == 'belum' ? 'Belum Disetujui' : 'Disetujui') ?>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?tipe=<?= htmlspecialchars($selectedTipe) ?>&status=belum">Belum Disetujui</a></li>
                <li><a class="dropdown-item" href="?tipe=<?= htmlspecialchars($selectedTipe) ?>&status=disetujui">Disetujui</a></li>
            </ul>
        </div>
    </div>

    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>NIM</th>
            <th>Nama</th>
            <th><?= htmlspecialchars($selectedTipe == 'TA' ? 'Judul Sidang' : 'Mata Kuliah') ?></th>
            <th><?= htmlspecialchars($selectedTipe == 'TA' ? 'Pembimbing' : 'Dosen Pengampu') ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($filteredData)): ?>
            <tr><td colspan="5" class="text-center">Tidak ada data untuk ditampilkan.</td></tr>
          <?php else: ?>
            <?php foreach ($filteredData as $entry): ?>
              <?php
                $row_props = "";
                if ($selectedStatus == 'disetujui') {
                    $dosen_pengampu_json = isset($entry['dosenPengampu']) ? htmlspecialchars(json_encode($entry['dosenPengampu']), ENT_QUOTES, 'UTF-8') : '[]';
                    $row_props = "class='clickable' onclick='openJadwalModal(this)' "
                               . "data-id='".htmlspecialchars($entry['id'] ?? '')."'"
                               . "data-nim='".htmlspecialchars($entry['nim'] ?? '')."'"
                               . "data-nama='".htmlspecialchars($entry['nama'] ?? '')."'"
                               . "data-judul='".htmlspecialchars($entry['judulSidang'] ?? '')."'"
                               . "data-pembimbing='".htmlspecialchars($entry['pembimbing'] ?? '')."'"
                               . "data-prodi='".htmlspecialchars($entry['prodi'] ?? 'Teknologi Rekayasa Perangkat Lunak')."'"
                               . "data-tipe-sidang='".htmlspecialchars($entry['tipeSidang'] ?? '')."'"
                               . "data-pengampu='".$dosen_pengampu_json."'";
                }
              ?>
              <tr <?= $row_props ?>>
                <td><?= htmlspecialchars($entry['id'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($entry['nim'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($entry['nama'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($entry['judulSidang'] ?? 'N/A') ?></td>
                <td>
                    <?php 
                        if ($selectedTipe == 'TA') {
                            echo htmlspecialchars($entry['pembimbing'] ?? 'N/A');
                        } else {
                            echo isset($entry['dosenPengampu']) && is_array($entry['dosenPengampu']) ? htmlspecialchars(implode(', ', $entry['dosenPengampu'])) : htmlspecialchars($entry['pembimbing'] ?? 'N/A');
                        }
                    ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main> <!-- End of NavSide__main-content -->

  <!-- Logout Modal -->
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
  
  <!-- MODAL FOR SIDANG TA -->
  <div class="modal fade" id="penjadwalanSidangTAModal" aria-labelledby="penjadwalanSidangTAModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
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

  <!-- MODAL FOR SIDANG SEMESTER -->
  <div class="modal fade" id="penjadwalanSidangSemModal" aria-labelledby="penjadwalanSidangSemModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
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
                                          <input type="number" id="modal_qty_pengampu-sem-1" class="bobot-input-new" value="0" min="0" />
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
                                          <input type="number" id="modal_qty_pengampu-sem-2" class="bobot-input-new" value="0" min="0" />
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
    // Global variables for modals and dynamic fields
    let taModalInstance, semModalInstance;
    let pengujiCount = 1;

    document.addEventListener("DOMContentLoaded", function() {
        // ... [existing DOMContentLoaded code] ...
        
        // --- Search Functionality ---
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchText = this.value.toLowerCase();
                const tableRows = document.querySelectorAll('.data-table tbody tr');
                let anyVisible = false;

                tableRows.forEach(row => {
                    if (row.classList.contains('text-center')) {
                        // Hide the "no data" row when searching
                        row.style.display = 'none';
                        return;
                    }

                    const namaCell = row.cells[2]; // Nama column (index 2)
                    if (!namaCell) return;

                    const namaText = namaCell.textContent.toLowerCase();
                    if (namaText.includes(searchText)) {
                        row.style.display = '';
                        anyVisible = true;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Show "no results" message if no matches found
                const noDataRow = document.querySelector('.no-results-row');
                if (!anyVisible && searchText !== '') {
                    if (!noDataRow) {
                        const tbody = document.querySelector('.data-table tbody');
                        const tr = document.createElement('tr');
                        tr.className = 'no-results-row text-center';
                        tr.innerHTML = '<td colspan="5">Tidak ditemukan data yang sesuai</td>';
                        tbody.appendChild(tr);
                    }
                } else if (noDataRow) {
                    noDataRow.remove();
                }
            });
        }
    });
    
    document.addEventListener("DOMContentLoaded", function() {
        // --- JAVASCRIPT FOR MOBILE SIDEBAR TOGGLE ---
        // Your style.css uses .NavSide__sidebar--active-mobile
        const toggleButton = document.getElementById('nav-toggle');
        const sidebar = document.getElementById('main-sidebar');

        if (toggleButton && sidebar) {
            toggleButton.addEventListener('click', () => {
                sidebar.classList.toggle('NavSide__sidebar--active-mobile');
                
                const icon = toggleButton.querySelector('i');
                if (sidebar.classList.contains('NavSide__sidebar--active-mobile')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        }
        
        // --- Modal Initialization ---
        const taModalEl = document.getElementById('penjadwalanSidangTAModal');
        if (taModalEl) taModalInstance = new bootstrap.Modal(taModalEl);
        
        const semModalEl = document.getElementById('penjadwalanSidangSemModal');
        if (semModalEl) semModalInstance = new bootstrap.Modal(semModalEl);

        // --- Form Submission Listeners ---
        const formTA = document.getElementById('formDalamModal-ta');
        if(formTA) formTA.addEventListener('submit', handleFormSubmit);

        const formSem = document.getElementById('formDalamModal-sem');
        if(formSem) formSem.addEventListener('submit', handleFormSubmit);
    });

    // --- ALL JAVASCRIPT FUNCTIONS (UNCHANGED) ---

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

        document.getElementById('modal_nim-ta').value = el.dataset.nim || '';
        document.getElementById('modal_judul_sidang-ta').value = el.dataset.judul || '';
        document.getElementById('modal_pembimbing-ta').value = el.dataset.pembimbing || '';
        document.getElementById('modal_prodi-ta').value = el.dataset.prodi || '';
    }

    function populateSemModal(el) {
        document.getElementById('modal_nim-sem').value = el.dataset.nim || '';
        document.getElementById('modal_matkul-sem').value = el.dataset.judul || '';
        document.getElementById('modal_prodi-sem').value = el.dataset.prodi || '';
        
        const pengampu = JSON.parse(el.dataset.pengampu || '[]');
        document.getElementById('modal_pengampu-sem-1').value = pengampu[0] || '';
        document.getElementById('modal_pengampu-sem-2').value = pengampu[1] || '';
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
            </div>`;
        wrapper.appendChild(div);
    }

    function removePenguji() {
        if (pengujiCount > 1) {
            const lastForm = document.getElementById(`penguji-form-ta-${pengujiCount}`);
            if (lastForm) {
                lastForm.remove();
                pengujiCount--;
            }
        }
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
                // location.reload(); 
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