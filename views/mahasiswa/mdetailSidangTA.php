<!DOCTYPE html>
<html lang="id"> <!-- Changed lang to id (Indonesian) -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Sidang</title> <!-- Simplified title -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: #ffffff;
        }

        #NavSide {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        .NavSide__sidebar-brand {
            padding: 10% 5% 50% 5%;
            text-align: center;
        }

        .NavSide__sidebar-brand img {
            width: 90%;
            max-width: 180px;
            height: auto;
            display: inline-block;
        }

        .NavSide__sidebar {
            position: fixed;
            top: 0px;
            left: 0px;
            bottom: 0px;
            width: 280px;
            border-radius: 1px;
            box-sizing: border-box;
            border-left: 5px solid rgb(67, 54, 240);
            background: rgb(67, 54, 240);
            overflow-x: hidden;
            overflow-y: auto;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.5s ease-in-out, width 0.5s ease-in-out;
        }

        .NavSide__sidebar-nav {
            width: 100%;
            padding-left: 0;
            padding-top: 0;
            list-style: none;
            flex-grow: 1;
        }

        .NavSide__sidebar-item {
            position: relative;
            display: block;
            width: 100%;
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
        }

        .NavSide__sidebar-item a {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            text-decoration: none;
            color: rgb(252, 252, 252);
            padding: 5% 2%;
            height: 60px;
            box-sizing: border-box;
        }

        .NavSide__sidebar-title {
            white-space: normal;
            text-align: center;
            line-height: 1.5;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active {
            background: #ffffff;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active a {
            color: rgb(67, 54, 240);
        }

        .NavSide__sidebar-item b:nth-child(1) {
            position: absolute;
            top: -20px;
            height: 20px;
            width: 100%;
            background: rgb(255, 255, 255);
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
            background: rgb(67, 54, 240);
            display: block;
        }
        .NavSide__sidebar-item b:nth-child(2) {
            position: absolute;
            bottom: -20px;
            height: 20px;
            width: 100%;
            background: rgb(255, 255, 255);
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
            background: rgb(67, 54, 240);
            display: block;
        }
        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(1),
        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(2) {
            display: block;
        }

        .NavSide__main-content {
            flex-grow: 1;
            padding: 20px 20px 20px calc(20px + 1cm); 
            margin-left: 280px;
            overflow-y: auto;
            transition: margin-left 0.5s ease-in-out;
        }

        /* --- Modifikasi Margin Global --- */
        .NavSide__main-content h2 { 
            margin-bottom: 1.2cm;
            font-weight: 700; 
        }

        .status-badge {
            margin-bottom: 1.2cm; 
            background-color:rgb(226, 42, 42); /* Light green */
            color: black; /* Black text as in original image */
            border-radius: 20px;
            padding: 8px 18px; 
            display: inline-block; 
            font-size: 0.875rem; 
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.08);
            font-weight: bold; 
        }

        .info-card {
            margin-bottom: 1.2cm; 
        }

        .NavSide__main-content h5 { 
            margin-top: 1.2cm;
            margin-bottom: 1.2cm;
            font-weight: 700; 
        }

        .file-buttons-container {
            margin-bottom: 1.2cm; 
        }

        .btn-kembali {
            background-color: rgb(67, 54, 240);
            color: white; /* Teks default putih */
            border: none;
            border-radius: 20px;
            padding: 10px 25px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease; /* Tambah transisi untuk warna teks */
            display: inline-flex; 
            align-items: center; 
            margin-top: 1.2cm; /* Margin atas 1.2cm untuk tombol kembali */
        }
        .btn-kembali:hover {
            position: relative;
            background-color: white; /* UBAH: Latar belakang jadi putih saat hover */
            color: rgb(67, 54, 240); /* UBAH: Teks jadi biru saat hover */
        }
        
        .btn-kembali .icon-circle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px; 
            height: 30px; 
            background-color: white; /* Latar belakang lingkaran default putih */
            border-radius: 50%;
            margin-right: 10px; 
            transition: background-color 0.3s ease; /* Transisi untuk warna latar belakang lingkaran */
        }

        .btn-kembali:hover .icon-circle {
            background-color: rgb(67, 54, 240); /* UBAH: Latar belakang lingkaran jadi biru saat hover */
        }

        .btn-kembali .icon-circle i {
            color: rgb(67, 54, 240); /* Warna ikon default biru */
            font-size: 1rem; 
            transition: color 0.3s ease; /* Transisi untuk warna ikon */
        }

        .btn-kembali:hover .icon-circle i {
            color: white; /* UBAH: Ikon jadi putih saat hover */
        }
        /* --- Akhir Modifikasi Margin Global --- */

        .NavSide__toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            width: 40px;
            height: 40px;
            z-index: 1100;
            transition: left 0.5s ease-in-out;
            cursor: pointer;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .NavSide__toggle i.bi {
            position: absolute;
            font-size: 28px;
            display: none;
        }

        .NavSide__toggle i.bi.open {
            color: rgb(67, 54, 240);
            display: block;
        }
        .NavSide__toggle i.bi.close {
            color: rgb(67, 54, 240);
        }

        .NavSide__toggle.NavSide__toggle--active i.bi.open {
            display: none;
        }
        .NavSide__toggle.NavSide__toggle--active i.bi.close {
            display: block;
        }

        @media (max-width: 700px) {
            .NavSide__sidebar {
                width: 50%;
                transform: translateX(-100%);
                border-left-width: 0;
            }

            .NavSide__sidebar.NavSide__sidebar--active-mobile {
                transform: translateX(0);
                box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2);
            }

            .NavSide__sidebar-brand {
                padding: 20px 10px 30px 10px;
            }
            .NavSide__sidebar-brand img {
                width: 90%;
            }

            .NavSide__sidebar-nav {
                padding-top: 20%;
            }
            .NavSide__sidebar-item a {
                padding: 12% 10%;
                height: 2vh;
            }

            .NavSide__main-content {
                margin-left: 7vh;
            }

            .NavSide__toggle {
                display: flex;
            }

            .NavSide__toggle i.bi.open {
                color: rgb(67, 54, 240);
                display: block;
            }

            .NavSide__toggle.NavSide__toggle--active {
                left: calc(50% + 10px);
                background-color: aliceblue;
            }
        }
       
        .info-card {
            position: relative;
            background: rgb(235, 238, 245); 
            border-radius: 30px; 
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05);
            padding: 25px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            overflow: hidden;
            transition: background-color 0.4s ease;
        }

        .info-card::after { 
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 60px; 
            height: 100%;
            background-color: rgb(67, 54, 240);
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
            transition: width 0.4s ease;
            z-index: 0;
        }

        .info-card:hover::after {
            width: 100%;
            border-radius: 20px;
        }

        .info-card .section {
            flex: 0 0 48%; 
            z-index: 1;
            color: #333;
            transition: color 0.4s ease;
            display: flex; 
            flex-direction: column; 
        }

        .info-card:hover .section {
            color: white;
        }

        /* Styling untuk baris label dan nilai */
        .info-card .section .label-row {
            display: flex;
            align-items: center;
            margin-bottom: 0.25rem; 
            font-size: 1rem; 
        }

        .info-card .section .label-row i {
            margin-right: 10px; 
            color: #495057; 
            font-weight: 900; 
            transition: color 0.4s ease;
            width: 20px; 
            text-align: center;
        }

        .info-card:hover .section .label-row i {
            color: white;
        }

        .info-card .section .label-row .fw-bold {
            font-weight: 600; 
            font-size: 1.05rem; 
        }

        .info-card .section .value-row {
            margin-left: 30px; 
            line-height: 1.5;
            font-size: 0.95rem; 
            margin-bottom: 0; 
        }
       
        /* --- CSS Baru untuk Tombol Berkas --- */
        .file-button {
            display: inline-flex; 
            align-items: center;
            background-color: rgb(235, 238, 245); 
            border-radius: 20px; 
            padding: 12px 20px;
            margin-right: 15px; 
            margin-bottom: 15px; 
            text-decoration: none; 
            color: rgb(67, 54, 240); 
            font-weight: 500;
            font-size: 1rem;
            transition: background-color 0.2s ease, box-shadow 0.2s ease, color 0.2s ease; 
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05); 
        }

        .file-button:hover {
            background-color: rgb(67, 54, 240); 
            color: white; 
            text-decoration: none; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); 
        }

        .file-button i {
            font-size: 1.25rem; 
            margin-right: 10px; 
            color: rgb(67, 54, 240); 
            transition: color 0.2s ease; 
        }

        .file-button:hover i { 
            color: white;
        }

        /* Responsive adjustments for file buttons */
        @media (max-width: 576px) {
            .file-button {
                width: 100%; 
                display: flex; 
                margin-right: 0; 
            }
        }
        /* --- Akhir CSS Baru --- */

        /* --- CSS untuk info-group dan spacer --- */
        .info-card .section .info-group {
            margin-bottom: 1rem; 
        }
        .info-card .section .info-group:last-child {
            margin-bottom: 0; 
        }
        /* The .spacer div and its CSS are removed as it's no longer needed with the correct grouping of info-groups within sections. */
        /* --- Akhir CSS info-group dan spacer --- */
    </style>
</head>
<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                                <a onclick="location.href='mdetailSidangTA.php'">
                        <span class="NavSide__sidebar-title fw-semibold">Detail Pengajuan</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                          <a onclick="location.href='mPerbaikan.php'">
                        <span class="NavSide__sidebar-title fw-semibold">Perbaikan</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                         <a onclick="location.href='mNilaiakhir.php'">
                        <span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="NavSide__toggle">
            <i class="bi bi-list open"></i>
            <i class="bi bi-x-lg close"></i>
        </div>

        <main class="NavSide__main-content">
            <h2>Detail Sidang - Sistem Pengajuan Sidang</h2>
            <div class="status-badge">Status Pengajuan : Belum Disetujui</div>
            
            <div class="info-card">
                <div class="section">
                    <!-- Judul Sidang -->
                    <div class="info-group">
                        <div class="label-row">
                           <i class="fa-solid fa-book"></i>
                            <span class="fw-bold">Judul Sidang</span> 
                        </div>
                        <div class="value-row">Sistem Pengajuan Sidang</div> 
                    </div>

                    <!-- Dosen Pembimbing -->
                    <div class="info-group">
                        <div class="label-row">
                            <i class="fa-solid fa-user"></i> 
                            <span class="fw-bold">Dosen Pembimbing</span>
                        </div>
                        <div class="value-row">
                            Dr. Rida Indah Fariani, S.Kom, M.Kom 
                        </div>
                    </div>

                    <!-- Dosen Penguji -->
                    <div class="info-group">
                        <div class="label-row">
                            <i class="fa-solid fa-user-group"></i> 
                            <span class="fw-bold">Dosen Penguji</span>
                        </div>
                        <div class="value-row">
                            Timotius Victory, S.Kom, M.Kom<br>
                            Ning Ratwasturi, S.Kom, M.Kom 
                        </div>
                    </div>
                </div>

                <div class="section">
                    <!-- Ruangan -->
                    <div class="info-group">
                        <div class="label-row">
                            <i class="fa-solid fa-door-open"></i>
                            <span class="fw-bold">Ruangan</span>
                        </div>
                        <div class="value-row">CB101 - RPL 1B</div>
                    </div>

                    <!-- Tanggal -->
                    <div class="info-group">
                        <div class="label-row">
                            <i class="fa-solid fa-calendar-days"></i>
                            <span class="fw-bold">Tanggal</span>
                        </div>
                        <div class="value-row">Selasa, 22 April 2025</div>
                    </div>

                    <!-- Jam -->
                    <div class="info-group">
                        <div class="label-row">
                            <i class="fa-solid fa-clock"></i>
                            <span class="fw-bold">Jam</span>
                        </div>
                        <div class="value-row">09.00 - 10.00</div>
                    </div>
                </div>
            </div>
            
            <h5>Dokumen Sidang</h5>
            <div class="file-buttons-container d-flex flex-wrap"> 
                <a href="#" class="file-button">
                            <i class="fa-solid fa-file-pdf"></i>
                    file_laporan_kel-1.pdf
                </a>
                <a href="#" class="file-button">
                 <i class="fa-solid fa-file-zipper"></i>
                    dokumen_pendukung_kel-1.zip
                </a>
            </div>
            
            <button class="btn-kembali">
                <span class="icon-circle">
                    <i class="fa-solid fa-arrow-left"></i>
                </span>
                Kembali
            </button>
            
            <!-- Modal Penjadwalan Sidang telah dihapus, jadi bagian ini tetap dikosongkan -->

        </main>
    </div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script type="text/javascript">
      // Skrip untuk toggle sidebar dan active menu item
      let menuToggle = document.querySelector(".NavSide__toggle");
      let sidebar = document.getElementById("main-sidebar");

      if (menuToggle && sidebar) {
        menuToggle.onclick = function () {
          menuToggle.classList.toggle("NavSide__toggle--active");
          sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
      }

      let listItems = document.querySelectorAll(".NavSide__sidebar-item");
      if (listItems.length > 0) {
        for (let i = 0; i < listItems.length; i++) {
          listItems[i].onclick = function (event) {
            for (let j = 0; j < listItems.length; j++) {
              listItems[j].classList.remove("NavSide__sidebar-item--active");
            }
            this.classList.add("NavSide__sidebar-item--active");
          };
        }
      }

      // Fungsi-fungsi JS terkait modal penjadwalan sidang (openModal, incrementValue, decrementValue)
      // telah dihapus karena modalnya sudah tidak digunakan.
    </script>
</body>
</html>