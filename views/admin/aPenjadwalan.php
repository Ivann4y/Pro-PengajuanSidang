<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      background-color: #f9f9f9;
    }

    .sideNav {
      position: fixed;
      top: 0;
      left: 0;
      width: 15vw;
      height: 100vh;
      background-color: #4538db;
      padding: 2% 1%;
      color: white;
      display: flex;
      flex-direction: column;
      align-items: center;
      z-index: 1000;
    }

    .sideNav h4 img {
      width: 100%;
      max-width: 120px;
      margin-bottom: 30px;
    }

    .nav-item {
      margin: 15px 0;
      font-weight: 500;
      cursor: pointer;
      width: 100%;
      padding: 10px 15px;
      text-align: center;
      border-radius: 20px;
      transition: all 0.3s ease;
    }

    .nav-item.active {
      background-color: white;
      color: #4538db;
      font-weight: 600;
    }

    .nav-item:hover {
      background-color: #ffffffcc;
      color: #4538db;
    }

    .bodyContainer {
      margin-left: 15vw;
      padding: 4vh 3vw;
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
        /* --- Styles for Search Bar and Filters --- */
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

    @media (max-width: 768px) {
      .sideNav {
        position: relative;
        width: 100vw;
        height: auto;
        flex-direction: row;
        justify-content: space-around;
      }
      .bodyContainer {
        margin-left: 0;
      }
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
      margin-right: 15px; /* Space between buttons */
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
        border-radius: 20px; /* Slightly rounded corners */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Soft shadow */
        border: none; /* Remove default Bootstrap border if desired */
        padding: 5px 0; /* Adjust padding inside the menu */
        background-color: #d9d9d9; 
    }

    .dropdown-menu:hover{
        background-color: #4538db; /* Light grey background on hover/focus */
        color:rgb(255, 255, 255); /* Your primary blue text color on hover */
    }

    /* Styling for individual dropdown items */
    .dropdown-item {
        padding: 10px 20px; /* More padding for better click area */
        font-size: 0.95rem;
        color: #7b7b7b; /* Darker text color */
        transition: background-color 0.2s, color 0.2s; /* Smooth transition for hover effects */
    }

    /* Hover/Focus effect for dropdown items */
    .dropdown-item:hover,
    .dropdown-item:focus {
        background-color: #4538db; /* Light grey background on hover/focus */
        border-radius: 20px; /* Slightly rounded corners */
        color:rgb(255, 255, 255); /* Your primary blue text color on hover */
    }

    /* Styles for the optional divider */
    .dropdown-divider {
        border-top: 1px solid #eee; /* Light grey line */
    }

    /* Adjust the caret position for the dropdown buttons */
    .dropdown-toggle::after {
        vertical-align: 0.15em; /* Keep your existing adjustment */
        margin-left: 0.5em; /* Add some space between text and caret */
    }

    .data-table {
      margin-top: 30px;
      width: 100%;
    }

    .data-table-header {
      border-bottom: 1px solid #ccc;
      padding-bottom: 10px;
      margin-bottom: 15px;
      margin-left: 20px ;
      display: flex; /* Makes columns align horizontally */
      font-weight: 600;
      color: #555;
    }

    .data-table-header > div,
    .data-table-row > div {
      flex: 1; /* Default flex grow for all columns */
      text-align: left;
      padding: 5px 0;
    }

    .data-table-row {
      background-color: #e2e2e2; /* Light grey for rows */
      border-radius: 15px;
      padding: 15px 20px;
      margin-bottom: 15px;
      display: flex; /* Makes columns align horizontally within a row */
      align-items: center; /* Vertically center content */
      font-weight: 500;
      color: #333;
      transition: background-color 0.2s ease;
      cursor: pointer; /* To indicate rows are clickable */
    }

    .data-table-row:hover {
        background-color: #4538db;
        color: white;
    }

    /* Specific flex values to control column widths, matching the image */
    .data-table-row > div:nth-child(1) { flex: 0.5; } /* ID */
    .data-table-row > div:nth-child(2) { flex: 1; } /* NIM */
    .data-table-row > div:nth-child(3) { flex: 1.2; } /* Nama */
    .data-table-row > div:nth-child(4) { flex: 2; } /* Judul Sidang */
    .data-table-row > div:nth-child(5) { flex: 1.5; } /* Pembimbing */

    /* Apply same flex values to headers to match column alignment */
    .data-table-header > div:nth-child(1) { flex: 0.7; }
    .data-table-header > div:nth-child(2) { flex: 1; }
    .data-table-header > div:nth-child(3) { flex: 1.2; }
    .data-table-header > div:nth-child(4) { flex: 2; }
    .data-table-header > div:nth-child(5) { flex: 1.5; }

    /* Media query for responsiveness (adjust column wrapping on small screens) */
    @media (max-width: 768px) {
      .data-table-header, .data-table-row {
        flex-wrap: wrap; /* Allow wrapping on small screens */
      }
      .data-table-header > div,
      .data-table-row > div {
        flex: 1 1 50%; /* Adjust column widths on small screens (e.g., 2 columns per row) */
      }
    }

  </style>
</head>
<body>
  <div class="sideNav">
    <h4><img src="logo-astratech.png" alt="ASTRAtech Logo"></h4>
    <div href="aBeranda.php" class="nav-item " id="berandaNav" onclick="location.href='aBeranda.php'">Beranda</div>
    <div href="aPenjadwalan.php" class="nav-item active" id="penjadwalanNav" onclick="location.href='aPenjadwalan.php'">Penjadwalan</div>
    <div class="nav-item">Daftar Sidang</div>
    <div class="nav-item">Keluar</div>
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
            Sidang TA
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Sidang Semester</a></li>
            </ul>
        </div>
        <div class="dropdown me-2">
            <button class="filter-btn secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Belum Disetujui
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Disetujui</a></li>
            </ul>
        </div>
    </div>
    <div class="data-table">
        <div class="data-table-header">
            <div>ID</div>
            <div>NIM</div>
            <div>Nama</div>
            <div>Judul Sidang</div>
            <div>Pembimbing</div>
        </div>
        <div class="data-table-row">
            <div>005</div>
            <div>0920240053</div>
            <div>M. Haaris Nur S.</div>
            <div>Sistem Pengajuan Sidang</div>
            <div>Dr. Rida Indah F.</div>
        </div>
        <div class="data-table-row">
            <div>006</div>
            <div>0920240053</div>
            <div>Naufal A F</div>
            <div>Sistem Pengajuan Sidang</div>
            <div>Dr. Rida Indah F.</div>
        </div>
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
