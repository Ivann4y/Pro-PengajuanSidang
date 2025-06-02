<?php
// Read and filter JSON data
$selectedTipe = isset($_GET['tipe']) ? $_GET['tipe'] : 'TA';
$selectedStatus = isset($_GET['status']) ? $_GET['status'] : 'belum';
$statusFilter = ($selectedStatus == 'disetujui') ? true : false;

$jsonData = file_get_contents('data_sidang.json');
$data = json_decode($jsonData, true);

$filteredData = array_filter($data, function($entry) use ($selectedTipe, $statusFilter) {
    return $entry['tipeSidang'] == $selectedTipe && 
           $entry['statusPersetujuan'] === $statusFilter;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin!</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="../../assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      background-color: #f9f9f9;
      min-height: 100vh;
    }

    .bodyContainer {
      margin-left: 280px; /* Matches width: 280px of .NavSide__sidebar in style.css */
      padding: 4vh 3vw;
      position: relative;
    }

    .bodyHeading {
      font-weight: 600;
      margin-bottom: 20px;
    }

    .welcomeText {
      color: #555;
      font-size: 2rem;
    }

    .dashboardTitle {
      color: #4538db;
      font-size: 1.5rem;
      font-weight: 500;
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

    .statusCard {
      padding: 20px;
      border-radius: 15px;
      height: 100%;
      transition: 0.3s ease;
      cursor: pointer;
    }

    .statusCard:hover {
      transform: scale(1.05);
    }

    .img-slot {
      width: 100%;
      height: 100%;
      background-color: #e0e0e0;
      border-radius: 15px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 14px;
      color: gray;
      min-height: 250px;
    }

    .profile-icon {
      position: absolute;
      top: 30px;
      right: 30px;
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

    .filter-btn.secondary {
      background-color: #4538db;
      color: white;
    }

    .filter-btn:hover {
      background-color: #312a9e;
    }
    .filter-btn.secondary:hover {
      background-color: #312a9e;
    }

    .dropdown-menu {
        border-radius: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: none;
        padding: 5px 0;
        background-color: #4538db;
    }

    .dropdown-menu:hover{
        background-color: #4538db;
        color:rgb(255, 255, 255);
        border-radius: 20px;
    }

    .dropdown-item {
        padding: 10px 20px;
        font-size: 0.95rem;
        background-color: #4538db;
        color:rgb(180, 180, 180);
        transition: background-color 0.2s, color 0.2s;
        border-radius: 20px;
    }

    .dropdown-item:hover,
    .dropdown-item:focus {
        background-color: #4538db;
        border-radius: 20px;
        color:rgb(255, 255, 255);
    }

    .dropdown-divider {
        border-top: 1px solid #eee;
    }

    .dropdown-toggle::after {
        vertical-align: 0.15em;
        margin-left: 0.5em;
    }

    .table-container {
      margin-top: 30px;
      width: 100%;
    }

    .data-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 15px;
      margin-top: 1rem;
    }

    .data-table thead th {
      padding: 0 20px 15px 20px;
      border-bottom: 1px solid #ccc;
      font-weight: 600;
      color: #555;
      text-align: left;
    }

    .data-table tbody td {
      background-color: #e2e2e2;
      padding: 15px 20px;
      vertical-align: middle;
      transition: all 0.3s ease;
    }

    .data-table tbody tr:hover td {
      background-color: #4538db;
      color: white;
      cursor: pointer;
    }

    .data-table tbody tr td:first-child {
      border-radius: 15px 0 0 15px;
    }
    .data-table tbody tr td:last-child {
      border-radius: 0 15px 15px 0;
    }

    .data-table th:nth-child(1),
    .data-table td:nth-child(1) { width: 7%; }
    .data-table th:nth-child(2),
    .data-table td:nth-child(2) { width: 10%; }
    .data-table th:nth-child(3),
    .data-table td:nth-child(3) { width: 12%; }
    .data-table th:nth-child(4),
    .data-table td:nth-child(4) { width: 20%; }
    .data-table th:nth-child(5),
    .data-table td:nth-child(5) { width: 15%; }

    @media (max-width: 768px) {
      .table-container {
        overflow-x: auto;
      }
      .data-table {
        min-width: 600px;
      }
    }
  </style>
</head>
<body>
  
  <div id="main-sidebar" class="NavSide__sidebar">
    <div class="NavSide__sidebar-brand">
        <img src="../../assets/img/Logo_Astratech_White-8.png" alt="AstraTech Logo">
    </div>
    <ul class="NavSide__sidebar-nav">
        <li class="NavSide__sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'aBeranda.php' ? 'NavSide__sidebar-item--active' : ''); ?>" id="berandaNav">
            <b></b><b></b>
            <a href="aBeranda.php" onclick="location.href='aBeranda.php'">
                <span class="NavSide__sidebar-title fw-semibold">Beranda</span>
            </a>
        </li>
        <li class="NavSide__sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'aPenjadwalan.php' ? 'NavSide__sidebar-item--active' : ''); ?>" id="penjadwalanNav">
            <b></b><b></b>
            <a href="aPenjadwalan.php" onclick="location.href='aPenjadwalan.php'">
                <span class="NavSide__sidebar-title fw-semibold">Penjadwalan</span>
            </a>
        </li>
        <li class="NavSide__sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'aDaftarSidang.php' ? 'NavSide__sidebar-item--active' : ''); ?>">
            <b></b><b></b>
            <a href="aDaftarSidang.php" onclick="location.href='aDaftarSidang.php'">
                <span class="NavSide__sidebar-title fw-semibold">Daftar Sidang</span>
            </a>
        </li>
        <li class="NavSide__sidebar-item <?php echo (basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'NavSide__sidebar-item--active' : ''); ?>">
            <b></b><b></b>
            <a href="logout.php" onclick="location.href='logout.php'">
                <span class="NavSide__sidebar-title fw-semibold">Keluar</span>
            </a>
        </li>
    </ul>
</div>

  <div class="bodyContainer position-relative">
    <div class="profile-icon">
      <i class="fas fa-user-circle"></i>
    </div>
     <div class="dashboardTitle mb-5">Penjadwalan Sidang</div>
     <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="section-title ">Pengajuan Sidang</h2>
      <div class="search-input-container">
        <i class="fas fa-search"></i>
        <input type="text" class="form-control search-input " placeholder="Nama Mahasiswa">
      </div>
    </div>
   
    <!-- Inside .bodyContainer, after the search bar container -->
    <div class="d-flex" id="dropdownAdminPenjadwalan">
        <div class="dropdown me-2">
            <button class="filter-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?= $selectedTipe == 'TA' ? 'Sidang TA' : 'Sidang Semester' ?>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?tipe=TA&status=<?= $selectedStatus ?>">Sidang TA</a></li>
                <li><a class="dropdown-item" href="?tipe=Semester&status=<?= $selectedStatus ?>">Sidang Semester</a></li>
            </ul>
        </div>
        <div class="dropdown me-2">
            <button class="filter-btn secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?= $selectedStatus == 'belum' ? 'Belum Disetujui' : 'Disetujui' ?>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?tipe=<?= $selectedTipe ?>&status=belum">Belum Disetujui</a></li>
                <li><a class="dropdown-item" href="?tipe=<?= $selectedTipe ?>&status=disetujui">Disetujui</a></li>
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
            <th>Judul Sidang</th>
            <th>Pembimbing</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($filteredData as $entry): ?>
          <tr>
            <td><?= $entry['id'] ?></td>
            <td><?= $entry['nim'] ?></td>
            <td><?= $entry['nama'] ?></td>
            <td><?= $entry['judulSidang'] ?></td>
            <td><?= $entry['pembimbing'] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function markAllRead() {
      document.querySelectorAll('.notifItem').forEach(item => item.remove());
    }

    function markOneRead(el) {
      el.closest('.notifItem').remove();
    }
  </script>
</body>
</html>
