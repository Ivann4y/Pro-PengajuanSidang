<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Daftar Pengajuan Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
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
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            /* PERUBAHAN CSS: Menjadikan topbar flex container */
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }

        .NavSide__toggle {
            font-size: 2rem;
            cursor: pointer;
            color: #4B68FB;
            z-index: 1002; /* Pastikan toggle di atas ikon */
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
            color:rgb(0, 0, 0);
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

        .header-icons > a {
            font-size: 1.4rem;
            color: #5a5a5a;
            transition: color 0.2s ease;
        }

        .header-icons > a:hover {
            color: #4B68FB;
        }

        .header-right-panel .profile-icon, .NavSide__topbar .profile-icon {
            background-color: #333;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: transform 0.2s ease;
        }
        
        .header-right-panel .profile-icon:hover, .NavSide__topbar .profile-icon:hover {
            transform: scale(1.1);
        }
        
        .header-right-panel .profile-icon a, .NavSide__topbar .profile-icon a {
            color: white;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #ddAdminSidangTypeButton {
            background-color: #4B68FB;
            border-color: #4B68FB;
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
            display: flex;
            justify-content: center;
            align-items: center;
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
                display: flex; /* Mengganti dari block ke flex */
            }

            .NavSide__toggle {
                position: relative; /* Mengubah dari fixed */
                top: auto;
                left: auto;
            }

            .NavSide__toggle.NavSide__toggle--active {
                transform: translateX(280px); 
            }
            
            /* Sembunyikan search bar di mobile untuk memberi ruang */
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

            .header-right-panel {
                /* Tidak perlu diubah lagi karena ikon sudah pindah */
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
                box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            }
            
            .table-admin-custom td {
                display: flex;
                justify-content: space-between;
                padding: 12px 15px;
                text-align: right;
                border-bottom: 1px solid #e9e9e9;
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
        }
    </style>
</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo Admin">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aBeranda.php"><span class="fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aPenjadwalan.php"><span class="fw-semibold">Penjadwalan</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="#"><span class="fw-semibold">Daftar Sidang</span></a>
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

        <main class="NavSide__main-content" id="adminDaftarSidangContent">
            <div class="main-header">
                <div class="header-left-panel">
                    <h1 class="main-title">Daftar Pengajuan Sidang</h1>
                    <div class="filter-container">
                        <span class="filter-label fw-semibold">Filter:</span>
                        <div class="dropdown" id="switcherDropdownContainer">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="ddAdminSidangTypeButton" data-bs-toggle="dropdown" aria-expanded="false">
                                Semua
                            </button>
                            <ul class="dropdown-menu" id="dynamicDropdownMenu">
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="header-right-panel">
                    <div id="desktop-icons-container">
                         <div class="header-icons">
                            <a href="aNotifikasi.php" title="Notifikasi">
                                <i class="bi bi-bell-fill"></i>
                            </a>
                            <div class="profile-icon">
                                <a href="aProfil.php" title="Profil">
                                    <i class="bi bi-person-fill"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="input-group search-input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cari..." aria-label="Cari">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table-admin-custom">
                    <thead>
                        <tr>
                            <th scope="col">Nomor</th>
                            <th scope="col">NIM</th>
                            <th scope="col">Nama</th>
                            <th scope="col" id="thDynamicHeader">Judul/Mata Kuliah</th>
                            <th scope="col">Pembimbing</th>
                            <th scope="col" style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="adminSidangContent"> 
                        <tr class="isiTabel" data-id="TA001" data-type="ta">
                            <td data-label="Nomor">TA001</td>
                            <td data-label="NIM">0920240053</td>
                            <td data-label="Nama">Nayaka Ivanna</td>
                            <td data-label="Judul/MK">Sistem Pengajuan Sidang</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi">
                                <button type="button" class="btn detail-btn">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="isiTabel" data-id="TA002" data-type="ta">
                            <td data-label="Nomor">TA002</td>
                            <td data-label="NIM">0920240054</td>
                            <td data-label="Nama">Zahrah Imelda</td>
                            <td data-label="Judul/MK">Pengembangan Aplikasi Mobile Edukasi</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F.</td>
                            <td data-label="Aksi">
                                <button type="button" class="btn detail-btn">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM001" data-type="semester">
                            <td data-label="Nomor">SEM001</td>
                            <td data-label="NIM">0920230053</td>
                            <td data-label="Nama">Nayaka Ivanna</td>
                            <td data-label="Judul/MK">Basis Data 1</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F</td>
                            <td data-label="Aksi">
                                <button type="button" class="btn detail-btn">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="isiTabel" data-id="SEM002" data-type="semester">
                            <td data-label="Nomor">SEM002</td>
                            <td data-label="NIM">0920230054</td>
                            <td data-label="Nama">Zahrah Imelda</td>
                            <td data-label="Judul/MK">Pemrograman 2</td>
                            <td data-label="Pembimbing">Dr. Rida Indah F</td>
                            <td data-label="Aksi">
                                <button type="button" class="btn detail-btn">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function switchAdminSidangView(viewType) {
            const allRows = document.querySelectorAll("#adminSidangContent tr.isiTabel");
            const dynamicHeader = document.getElementById("thDynamicHeader");
            const dropdownMenu = document.getElementById("dynamicDropdownMenu");
            const ddButton = document.getElementById("ddAdminSidangTypeButton");
            const dynamicMKHeader = document.querySelectorAll('[data-label="Judul/MK"], [data-label="Judul Sidang"], [data-label="Mata Kuliah"]');

            allRows.forEach(row => {
                const rowType = row.dataset.type;
                if (viewType === 'All' || viewType === rowType) {
                    row.style.display = ""; 
                } else {
                    row.style.display = "none";
                }
            });

            dropdownMenu.innerHTML = '';
            let options = '';
            let mobileLabel = "Judul/Mata Kuliah";

            if (viewType === 'All') {
                ddButton.textContent = "Semua";
                dynamicHeader.textContent = "Judul/Mata Kuliah";
                options = `
                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); switchAdminSidangView('ta');">Sidang TA</a></li>
                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); switchAdminSidangView('semester');">Sidang Semester</a></li>`;
            } else if (viewType === 'ta') {
                ddButton.textContent = "Sidang TA";
                dynamicHeader.textContent = "Judul Sidang";
                mobileLabel = "Judul Sidang";
                options = `
                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); switchAdminSidangView('All');">Semua</a></li>
                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); switchAdminSidangView('semester');">Sidang Semester</a></li>`;
            } else if (viewType === 'semester') {
                ddButton.textContent = "Sidang Semester";
                dynamicHeader.textContent = "Mata Kuliah";
                mobileLabel = "Mata Kuliah";
                options = `
                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); switchAdminSidangView('All');">Semua</a></li>
                    <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); switchAdminSidangView('ta');">Sidang TA</a></li>`;
            }
            
            dynamicMKHeader.forEach(th => {
                th.setAttribute('data-label', mobileLabel);
            });
            
            dropdownMenu.insertAdjacentHTML('beforeend', options);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector(".NavSide__toggle");
            const sidebar = document.getElementById("main-sidebar");

            if (menuToggle && sidebar) {
                menuToggle.onclick = function() {
                    menuToggle.classList.toggle("NavSide__toggle--active");
                    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                };
            }

            const listItems = document.querySelectorAll(".NavSide__sidebar-item");
            
            listItems.forEach(item => {
                item.addEventListener('click', function(event) {
                    const link = this.querySelector('a');
                    if (link && !link.hasAttribute('data-bs-toggle')) {
                        window.location.href = link.href;
                    }
                });
            });

            switchAdminSidangView('All');

            const allTableRows = document.querySelectorAll('.table-admin-custom tbody tr.isiTabel');
            allTableRows.forEach(row => {
                row.addEventListener('click', function(event) {
                    const detailButton = event.target.closest('.detail-btn');

                    if (detailButton) {
                        const sidangId = this.dataset.id;
                        const sidangType = this.dataset.type;

                        if (sidangId && sidangType) {
                            if (sidangType === 'ta') {
                                window.location.href = `aDetailSidangTA.php?type=${sidangType}&id=${sidangId}`;
                            } else if (sidangType === 'semester') {
                                window.location.href = `aDetailSidangSem.php?type=${sidangType}&id=${sidangId}`;
                            }
                        }
                    }
                });
            });
            
            // --- PERUBAHAN JAVASCRIPT: Logika untuk memindahkan ikon ---
            const desktopIconsContainer = document.getElementById('desktop-icons-container');
            const mobileIconsContainer = document.getElementById('mobile-icons-container');
            const headerIcons = desktopIconsContainer.querySelector('.header-icons');

            function handleIconPlacement() {
                if (window.innerWidth <= 992) {
                    // Jika layar mobile, pindahkan ikon ke wadah mobile (di topbar)
                    if (!mobileIconsContainer.contains(headerIcons)) {
                        mobileIconsContainer.appendChild(headerIcons);
                    }
                } else {
                    // Jika layar desktop, kembalikan ikon ke wadah desktop
                    if (!desktopIconsContainer.contains(headerIcons)) {
                        desktopIconsContainer.appendChild(headerIcons);
                    }
                }
            }

            // Jalankan fungsi saat halaman pertama kali dimuat
            handleIconPlacement();

            // Jalankan fungsi setiap kali ukuran jendela diubah
            window.addEventListener('resize', handleIconPlacement);
        });
    </script>
</body>

</html>