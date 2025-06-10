<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DetailSidang-Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../assets/css/style.css">

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
            border-left: 5px solid #4B68FB;
            background: #4B68FB;
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
            color: #ffffff;
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
            color: #4B68FB;
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
            background: #4B68FB;
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
            background: #4B68FB;
            display: block;
        }
        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(1),
        .NavSide__sidebar-item.NavSide__sidebar-item--active b:nth-child(2) {
            display: block;
        }

        /* === PERUBAHAN CSS BAGIAN 1: GAYA TOPBAR === */
        .NavSide__topbar {
            display: none; /* Sembunyikan di desktop, muncul di mobile */
            align-items: center;
            position: fixed;
            top: 0;
            left: 0; /* Dimulai dari kiri */
            width: 100%; /* Lebar penuh */
            height: 60px;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1045; /* Pastikan di atas sidebar saat mobile */
            padding-left: 15px; /* Beri sedikit jarak untuk tombol toggle */
        }
        
        .NavSide__main-content {
            flex-grow: 1;
            padding: 20px;
            margin-left: 280px;
            overflow-y: auto;
            transition: margin-left 0.5s ease-in-out;
            /* Tambahkan padding-top agar konten tidak tertutup topbar di mobile */
            padding-top: calc(60px + 20px);
        }

        /* === PERUBAHAN CSS BAGIAN 2: GAYA H2 === */
        .NavSide__main-content h2 {
            margin-bottom: 1.2cm; /* Menyamakan margin-bottom */
            font-weight: 700;     /* Menyamakan ketebalan font */
            /* Ukuran font (font-size) dihapus agar mengikuti default browser atau Bootstrap yg lebih responsif */
        }
        
        .NavSide__toggle {
            width: 40px;
            height: 40px;
            cursor: pointer;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            position: relative; /* ubah ke relative agar ikon bisa absolute di dalamnya */
        }

        .NavSide__toggle i.bi {
            position: absolute;
            font-size: 28px;
            display: none;
            color: #4B68FB;
            transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
        }

        .NavSide__toggle i.bi.open {
            display: block;
        }
        .NavSide__toggle.NavSide__toggle--active i.bi.open {
            display: none;
        }
        .NavSide__toggle.NavSide__toggle--active i.bi.close {
            display: block;
        }

        @media (max-width: 700px) {
            .NavSide__topbar {
                display: flex;
            }

            .NavSide__sidebar {
                width: 50%;
                transform: translateX(-100%);
                border-left-width: 0;
                z-index: 1040;
                padding-top: 60px; /* Sisakan ruang untuk topbar */
            }

            .NavSide__sidebar.NavSide__sidebar--active-mobile {
                transform: translateX(0);
                box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2);
            }

            .NavSide__main-content {
                margin-left: 0; /* Konten memenuhi layar */
                padding: 20px; /* Reset padding */
                padding-top: calc(60px + 20px); /* Jaga jarak dari topbar */
            }
        }
        
        /* Gaya lain yang sudah ada dipertahankan */
        .status-badge {
            background-color: #4BFBAF;
            color: rgb(48, 48, 110);
            border-radius: 20px;
            padding: 8px 18px; 
            display: inline-block;
            font-size: 0.95rem; 
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            font-weight: 500;
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
            margin-bottom: 25px;
            overflow: hidden;
            transition: background-color 0.4s ease;
            margin-right: 30px;
        }

        .info-card::after { 
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            width: 60px; 
            height: 100%;
            background-color: #4B68FB;
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
            margin-bottom: 15px;
            z-index: 1;
            color: #333;
            transition: color 0.4s ease;
        }

        .info-card:hover .section {
            color: white;
        }

        .info-card .section i {
            margin-right: 10px; 
            color: rgb(70, 70, 70);
            transition: color 0.4s ease;
            width: 20px; 
            text-align: center;
        }

        .info-card:hover .section i{
            color: white;
        }

        .btn-ubah {
            background-color: #ff5f5f;
            color: white;
            border: 2px solid #ff5f5f;
            border-radius: 20px; 
            margin-bottom: 10px;
            padding: 12px 30px; 
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
        }

        .btn-ubah:hover {
            background-color:rgb(255, 255, 255);
            border: 2px solid #ff5f5f; 
            color : #ff5f5f;
            position: relative;
        }

        .btn-kembali {
            background-color: #4B68FB;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 10px 25px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: inline-flex;
            align-items: center;
            margin-top: 4cm;
            width: auto;
            gap: 10px;
        }
        .btn-kembali:hover {
            position: relative;
            background-color: rgb(59, 54, 136);
        }
        
        /* Sisa CSS untuk modal dan lainnya dipertahankan seperti aslinya */
        .modal-content-custom-form {
            border-radius: 25px !important;
        }
        .modal-body .form-container { 
            padding: 15px; 
            background-color:rgb(255, 255, 255); 
            border-radius: 20px; 
        }
        .modal-body .form-container h2 {
            font-size: 1.25rem; 
            margin-bottom: 20px;
            text-align: center;
            color: rgb(51, 47, 47); /* Warna teks disesuaikan agar terlihat */
        }
        .modal-body .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px; 
        }
        .modal-body .form-group label {
            width: 160px; 
            flex-shrink: 0;
            color:rgb(51, 47, 47);
            font-weight: bold;
            font-size: 14px;
            margin-right: 15px;
            text-align: left;
        }
        .modal-body .form-group .input-with-buttons,
        .modal-body .form-group .time-input-range,
        .modal-body .form-group > input[type="text"]{
            flex-grow: 1; 
            height: 35px; 
            display: flex; 
            align-items: center;
        }

        .modal-body .form-group > input[type="date"] {
            flex-grow: 1; 
            height: 35px;
        }

        .modal-body .form-group input[type="text"],
        .modal-body .form-group input[type="date"],
        .modal-body .form-group input[type="time"] {
            width: 100%; 
            height: 35px; 
            padding: 0 15px;
            border: 1px solid #D1D5DB;
            background-color:rgb(255, 255, 255);
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
            pointer-events: none;
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
        .modal-body .input-with-buttons input[type="text"] { 
            flex-grow: 1; 
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
            border-radius: 35px;
            transition: background-color 0.2s ease;
        }
        .modal-body .btn-bobot-new:hover {
            background-color:rgba(0, 0, 0, 0.05); 
        }
        .modal-body .time-input-range { 
            gap: 10px; 
            width: 100%; 
        }
        .modal-body .form-actions {
            display: flex; 
            justify-content: flex-end; 
            margin-top: 25px; 
            padding-left: calc(160px + 15px); 
        }
        .modal-body .form-actions .btn-batal { 
            background-color: #ff5f5f; 
            color:rgb(255, 255, 255); 
            border: none; 
            border-radius: 20px;
            padding: 5px 10px;
            height: 40px;
            width: 120px;
            margin-right: 10px;
        }
        .modal-body .form-actions .btn-submit { 
            background-color: #4B68FB; color: white; 
            border: none;
            border-radius: 20px;
            padding: 5px 10px;
            height: 40px;
            width: 200px;
        }
        .modal-body .form-actions .btn-submit:hover { 
            background-color: rgb(106, 95, 255); 
        }
        .modal-body > h2 {
            font-size: 20px; 
            color: #374151;
            font-weight: 600; /* Dibuat sedikit tebal */
        }
        #penjadwalanSidangModal .modal-dialog {
            max-width: 600px;
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
            border-radius: 50%; /* Dibuat bulat */
            border: 1px solid #ccc;
            cursor: pointer;
            background-color: white;
        }
        .modal-body .form-toggle-buttons button:hover {
            background-color: #ddd;
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
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="aDetailSidangTA.php"><span class="NavSide__sidebar-title fw-semibold">Detail Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aEvaluasi.php"><span class="NavSide__sidebar-title fw-semibold">Evaluasi</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="aNilaiAkhir.php"><span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span></a>
                </li>
            </ul>
        </div>

        <div style="flex-grow: 1; display: flex; flex-direction: column; position: relative;">
            <div class="NavSide__topbar">
                <div class="NavSide__toggle">
                    <i class="bi bi-list open"></i>
                    <i class="bi bi-x-lg close"></i>
                </div>
            </div>

            <main class="NavSide__main-content">
                <h2>Detail Sidang - Sistem Pengajuan Sidang</h2>
                <div class="status-badge">Status Pengajuan : Disetujui</div>

                <div class="info-card">
                    <div class="section">
                        <p><i class="fa-solid fa-book"></i><strong>Judul Sidang</strong><br>Sistem Pengajuan Sidang</p>
                        <p><i class="fa-solid fa-user"></i><strong>Dosen Pembimbing</strong><br>Dr. Rida Indah Fariani, S.Kom, M.Kom</p>
                        <p><i class="fa-solid fa-users"></i><strong>Dosen Penguji</strong><br>Timotius Victory, S.Kom, M.Kom<br>Ning Ratwasturi, S.Kom, M.Kom</p>
                    </div>
                    <div class="section">
                        <p><i class="fa-solid fa-door-open"></i><strong>Ruangan</strong><br>CB101 - RPL 1B</p>
                        <p><i class="fa-solid fa-calendar-days"></i><strong>Tanggal</strong><br>Selasa, 22 April 2025</p>
                        <p><i class="fa-solid fa-clock"></i><strong>Jam</strong><br>09.00 - 10.00</p>
                    </div>
                </div>
                
                <h5 class="mt-4">Aksi</h5>
                <button class="btn-ubah" onclick="openModal()">Ubah Jadwal Sidang</button>
                <br><br>
                <a href="aDaftarSidang.php"><button class="btn-kembali"><i class="fa-solid fa-circle-arrow-left"></i>Kembali</button></a>

                <div class="modal fade" id="penjadwalanSidangModal" aria-labelledby="penjadwalanSidangModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content modal-content-custom-form">
                            <div class="modal-body">
                                <h2>Penjadwalan Sidang</h2>
                                <div class="form-container"> 
                                    <form id="formDalamModal" novalidate>
                                        <div class="form-group">
                                            <label for="modal_nim">NIM</label>
                                            <input type="text" id="modal_nim" value="0920240033" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label for="modal_judul_sidang">Judul Sidang</label>
                                            <input type="text" id="modal_judul_sidang" name="judul_sidang" value="Sistem Pengajuan Sidang" readonly />
                                        </div>
                                        <div class="form-group">
                                            <label for="modal_pembimbing">Pembimbing</label>
                                            <input type="text" id="modal_pembimbing" name="pembimbing_nama" value="Rida Indah Fariani" readonly />
                                        </div>
                                        <div id="penguji-wrapper">
                                            <div class="form-group" id="penguji-form-1">
                                                <label for="modal_penguji1">Penguji 1</label>
                                                <div class="input-with-buttons">
                                                    <input type="text" id="modal_penguji1" name="penguji_nama[]" placeholder="Nama Penguji 1" />
                                                    <div class="bobot-nilai-input-group">
                                                        <button type="button" class="btn-bobot-new btn-decrement-new" onclick="decrementValue('modal_qty_penguji1')">-</button>
                                                        <input type="number" id="modal_qty_penguji1" name="penguji_bobot[]" class="bobot-input-new" value="0" min="0" aria-label="Bobot Penguji 1" />
                                                        <button type="button" class="btn-bobot-new btn-increment-new" onclick="incrementValue('modal_qty_penguji1')">+</button>
                                                    </div>
                                                    <div class="form-toggle-buttons">
                                                        <button type="button" onclick="addPenguji()">+</button>
                                                        <button type="button" onclick="removePenguji()">-</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="modal_prodi">Prodi</label>
                                            <input type="text" id="modal_prodi" name="prodi" value="Teknologi Rekayasa Perangkat Lunak" readonly/>
                                        </div>
                                        <div class="form-group">
                                            <label for="modal_ruangan">Ruangan</label>
                                            <input type="text" id="modal_ruangan" name="ruangan"/>
                                        </div>
                                        <div class="form-group">
                                            <label for="modal_tanggal">Tanggal</label>
                                            <input type="date" id="modal_tanggal" name="tanggal"/>
                                        </div>
                                        <div class="form-group">
                                            <label for="modal_jam_awal">Jam</label>
                                            <div class="time-input-range">
                                                <input type="time" id="modal_jam_awal" name="jam_awal" aria-label="Jam Awal"/>
                                                <span class="time-separator">-</span>
                                                <input type="time" id="modal_jam_akhir" name="jam_akhir" aria-label="Jam Akhir"/>
                                            </div>
                                        </div>
                                        <div id="form-error" style="color: red; margin-bottom: 10px; text-align: right;"></div>
                                        <div class="form-actions"> 
                                            <button type="button" class="btn btn-batal" data-bs-dismiss="modal">Batalkan</button>
                                            <button type="submit" class="btn btn-submit">Buat Penjadwalan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
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


      let pengujiCount = 1;

      function addPenguji() {
        pengujiCount++;
        const wrapper = document.getElementById('penguji-wrapper');

        const div = document.createElement('div');
        div.className = 'form-group';
        div.id = `penguji-form-${pengujiCount}`;
        div.innerHTML = `
          <label for="modal_penguji${pengujiCount}">Penguji ${pengujiCount}</label>
          <div class="input-with-buttons">
            <input type="text" id="modal_penguji${pengujiCount}" name="penguji_nama[]" placeholder="Nama Penguji ${pengujiCount}" />
            <div class="bobot-nilai-input-group">
              <button type="button" class="btn-bobot-new btn-decrement-new" onclick="decrementValue('modal_qty_penguji${pengujiCount}')">-</button>
              <input type="number" id="modal_qty_penguji${pengujiCount}" name="penguji_bobot[]" class="bobot-input-new" value="0" min="0" aria-label="Bobot Penguji ${pengujiCount}" />
              <button type="button" class="btn-bobot-new btn-increment-new" onclick="incrementValue('modal_qty_penguji${pengujiCount}')">+</button>
            </div>
          </div>
        `;

        wrapper.appendChild(div);
      }

      function removePenguji() {
        if (pengujiCount > 1) {
          const wrapper = document.getElementById('penguji-wrapper');
          const lastForm = document.getElementById(`penguji-form-${pengujiCount}`);
          if (lastForm) {
            wrapper.removeChild(lastForm);
            pengujiCount--;
          }
        }
      }

      function incrementValue(inputId) {
        const inputElement = document.getElementById(inputId);
        if (inputElement) {
          let currentValue = parseInt(inputElement.value, 10);
          if (isNaN(currentValue)) currentValue = 0;
          inputElement.value = currentValue + 1;
        }
      }

      function decrementValue(inputId) {
        const inputElement = document.getElementById(inputId);
        if (inputElement) {
          let currentValue = parseInt(inputElement.value, 10);
          if (isNaN(currentValue)) currentValue = 0;
          const minValue = parseInt(inputElement.min, 10);
          inputElement.value = Math.max(minValue || 0, currentValue - 1);
        }
      }


      // --- FUNGSI UNTUK MEMBUKA MODAL ---
      function openModal() {
        var penjadwalanModalElement = document.getElementById('penjadwalanSidangModal');
        if (penjadwalanModalElement) {
          var penjadwalanModal = new bootstrap.Modal(penjadwalanModalElement, {
            keyboard: true // Izinkan tutup modal dengan tombol Esc
          });
          penjadwalanModal.show();
        } else {
          console.error('Modal HTML element with id "penjadwalanSidangModal" not found!');
        }
      }

      // --- Skrip Validasi Form ---
      document.getElementById('formDalamModal').addEventListener('submit', function(event) {
          event.preventDefault(); 

          const errorBox = document.getElementById("form-error");
          errorBox.textContent = ""; 
          
          let isValid = true;
          let errorMessage = "";

          const pengujiInputs = document.querySelectorAll('input[name="penguji_nama[]"]');
          pengujiInputs.forEach((input, index) => {
              if (isValid && input.value.trim() === "") {
                  errorMessage = `Nama penguji ${index + 1} tidak boleh kosong!!`;
                  isValid = false;
              }
          });

          const ruangan = document.getElementById("modal_ruangan").value.trim();
          const tanggal = document.getElementById("modal_tanggal").value;
          const jamAwal = document.getElementById("modal_jam_awal").value;
          const jamAkhir = document.getElementById("modal_jam_akhir").value;

          if (isValid && ruangan === "") {
              errorMessage = "Ruangan harus diisi!!";
              isValid = false;
          } else if (isValid && tanggal === "") {
              errorMessage = "Tanggal harus dipilih!!";
              isValid = false;
          } else if (isValid && (jamAwal === "" || jamAkhir === "")) {
              errorMessage = "Jam awal dan jam akhir harus diisi!!";
              isValid = false;
          } else if (isValid && jamAkhir <= jamAwal) {
              errorMessage = "Jam akhir harus setelah jam awal!!";
              isValid = false;
          }

          if (!isValid) {
              errorBox.textContent = errorMessage;
              return;
          }
          
          console.log("Form valid, data siap dikirim.");
          var myModalEl = document.getElementById('penjadwalanSidangModal');
          var modal = bootstrap.Modal.getInstance(myModalEl);
          modal.hide();

          Swal.fire({
            title: 'Berhasil',
            text: 'Jadwal Berhasil Dibuat.',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#4B68FB'
          });
      });
    </script>
</body>
</html>