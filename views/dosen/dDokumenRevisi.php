<?php
require "../../koneksi/koneksiAndrew.php"; // Pastikan path ini benar

// ===================================================================================
// BAGIAN 1: INISIALISASI
// ===================================================================================
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: ID Sidang tidak valid.");
} 
$id_sidang = (int)$_GET['id'];

// Variabel default
$judul = 'Belum ada judul';
$ruangan = 'Belum Dijadwalkan';
$tanggal_formatted = 'Belum Dijadwalkan';
$jam = 'Belum Dijadwalkan';
$dosenPembimbing = [];
$dosenPenguji = [];

// ===================================================================================
// BAGIAN 2: PENGAMBILAN DATA
// ### PERBAIKAN UTAMA: Logika disederhanakan, tidak lagi bergantung pada 'jenis_sidang' ###
// ===================================================================================

$sql_sidang = "SELECT Judul, id_kelompok FROM Sidang WHERE id_sidang = ?";
$result_sidang = sqlsrv_query($conn, $sql_sidang, [$id_sidang]);

if ($result_sidang && $data_sidang = sqlsrv_fetch_array($result_sidang, SQLSRV_FETCH_ASSOC)) {
    // 1. Selalu ambil Judul dan Dosen
    $judul = !empty($data_sidang['Judul']) ? $data_sidang['Judul'] : 'Belum ada judul';
    $id_kelompok = $data_sidang['id_kelompok'];

    // Ambil Dosen Pembimbing
    if ($id_kelompok) {
        $sql_pembimbing = "SELECT DISTINCT d.nama_dosen FROM [dbo].[Bimbingan] b JOIN [dbo].[Dosen] d ON b.nomor_dosen = d.nomor_dosen WHERE b.id_kelompok = ? AND d.isPembimbing = 0x01";
        $stmt_pembimbing = sqlsrv_query($conn, $sql_pembimbing, [$id_kelompok]);
        if ($stmt_pembimbing) {
            while ($row = sqlsrv_fetch_array($stmt_pembimbing, SQLSRV_FETCH_ASSOC)) {
                $dosenPembimbing[] = $row['nama_dosen'];
            }
        }
    }
    
    // Ambil Dosen Penguji
    $sql_penguji = "SELECT DISTINCT d.nama_dosen FROM [dbo].[Penjadwalan] p JOIN [dbo].[Dosen] d ON p.nomor_dosen = d.nomor_dosen WHERE p.id_sidang = ? AND d.isPenguji = 0x01";
    $stmt_penguji = sqlsrv_query($conn, $sql_penguji, [$id_sidang]);
    if ($stmt_penguji) {
        while ($row = sqlsrv_fetch_array($stmt_penguji, SQLSRV_FETCH_ASSOC)) {
            $dosenPenguji[] = $row['nama_dosen'];
        }
    }

    // 2. Ambil Jadwal
    $sql_jadwal = "SELECT ruang_sidang, tanggal_sidang, jam_sidang FROM Jadwal WHERE id_sidang = ?";
    $result_jadwal = sqlsrv_query($conn, $sql_jadwal, [$id_sidang]);
    if ($result_jadwal && $data_jadwal = sqlsrv_fetch_array($result_jadwal, SQLSRV_FETCH_ASSOC)) {
        $ruangan = $data_jadwal['ruang_sidang'] ?? 'Belum Dijadwalkan';
        $jam = $data_jadwal['jam_sidang'] ? $data_jadwal['jam_sidang']->format('H:i') : 'Belum Dijadwalkan';
        if ($data_jadwal['tanggal_sidang'] instanceof DateTime) {
            setlocale(LC_TIME, 'id_ID.UTF-8', 'Indonesian');
            $tanggal_formatted = strftime('%A, %d %B %Y', $data_jadwal['tanggal_sidang']->getTimestamp());
        }
    }
}

// Siapkan variabel untuk ditampilkan di HTML
$namaPembimbing_html = !empty($dosenPembimbing) ? implode('<br>', array_map('htmlspecialchars', $dosenPembimbing)) : 'Belum ditentukan';
$namaPenguji_html = !empty($dosenPenguji) ? implode('<br>', array_map('htmlspecialchars', $dosenPenguji)) : 'Belum ditentukan';
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Revisi - Responsive</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../css/button-styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 for pop-up notifications -->


    <style>
        /* --- General and Body Styles --- */
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

        /* --- Base Sidebar, Main Content, and Info Card Styles --- */
        #NavSide {
            display: flex;
            min-height: 100vh;
            position: relative;
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

        .NavSide__sidebar-brand {
            padding: 10% 5% 50% 5%;
            text-align: center;
        }

        .NavSide__sidebar-brand img {
            width: 90%;
            max-width: 180px;
            height: auto;
            display: inline-block;
            filter: brightness(0) invert(1);
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
            margin-bottom: 15px;
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
            color: white;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active {
            background: #ffffff;
        }

        .NavSide__sidebar-item.NavSide__sidebar-item--active a {
            color: #4B68FB !important;
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

        .NavSide__main-content {
            flex-grow: 1;
            padding: 20px 20px 20px calc(20px + 1cm);
            margin-left: 280px;
            overflow-y: auto;
            transition: margin-left 0.5s ease-in-out;
        }

        .NavSide__main-content h2 {
            margin-bottom: 1.2cm;
            font-weight: 700;
        }

/* ======================================================= */
/* === CSS PERBAIKAN UNTUK KARTU INFORMASI === */
/* ======================================================= */

/* 1. Mengatur Kartu Utama (.info-card) */
.info-card {
    /* Layout Dasar */
    display: flex; /* Tetap gunakan flexbox untuk 2 kolom */
    gap: 30px;     /* Jarak antar kolom */
    
    /* Ukuran dan Jarak */
    padding: 25px;         /* Ruang di dalam kartu */
    margin-bottom: 2.5rem; /* Jarak ke elemen di bawahnya */
    
    /* Tampilan Visual */
    background: #f0f4f7; 
    border-radius: 30px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);

    /* --- PERBAIKAN UTAMA --- */
    position: relative; /* Ini penting agar ::after tidak keluar */
    overflow: hidden;   /* Mencegah ::after keluar dari sudut tumpul */
}

/* 2. Garis Biru di Kanan pada .info-card */
.info-card::after {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    width: 60px; /* Lebar awal garis biru */
    height: 100%;
    background-color: #4B68FB;
    
    /* --- PERBAIKAN UTAMA --- */
    /* Hapus border-radius dari ::after agar tidak aneh saat hover */
    /* border-top-right-radius: 20px; */
    /* border-bottom-right-radius: 20px; */
    
    transition: width 0.4s ease; /* Animasi saat hover */
    z-index: 0; /* Letakkan di belakang konten */
}

/* 3. Efek Hover */
.info-card:hover::after {
    width: 100%; /* Lebar penuh saat di-hover */
}

.info-card:hover {
    color: white; /* Ubah warna teks menjadi putih saat hover */
}

.info-card:hover .info-item i {
    color: white; /* Ubah warna ikon menjadi putih juga */
}

/* 4. Mengatur Kolom (.section) */
.info-card .section {
    flex: 1; /* Setiap kolom mengambil ruang yang sama */
    z-index: 1; /* Pastikan konten berada di atas garis biru */

    /* --- PERBAIKAN UTAMA --- */
    /* Hapus aturan flex yang mungkin ada di sini agar item di dalamnya (paragraf) bisa berjajar ke bawah secara normal */
    /* display: flex; */
    /* flex-direction: column; */
}

/* 4. Mengatur Setiap Baris Informasi (.info-item) - KUNCI UTAMA */
.info-item {
    position: relative; /* Penting untuk posisi ikon absolut */
    padding-left: 35px; /* Dorong SEMUA teks ke kanan */
    
    margin-bottom: 5px; /* Jarak vertikal antar baris informasi */
    line-height: 1.6; /* Keterbacaan teks */
}
.info-item:last-child {
    margin-bottom: 0; /* Hapus margin pada item terakhir */
}

/* 5. Mengatur Ikon di dalam .info-item */
.info-item i {
    /* Posisikan ikon kembali ke kiri */
    position: absolute;
    left: 0;
    top: 5px; /* Sesuaikan agar sejajar dengan teks */
    
    /* Ukuran dan perataan ikon */
    width: 25px; 
    text-align: center;
    font-size: 1.1rem;
    color: #555; /* Warna ikon awal */
    transition: color 0.4s ease; /* Transisi warna ikon saat hover */
}

/* 6. Mengatur Teks Label yang Tebal */
.info-item strong {
    font-weight: 600; 
    display: block; /* <-- INI KUNCINYA, TAMBAHKAN KEMBALI */
    margin-bottom: -18px; /* Sesuaikan nilai ini, 30px mungkin terlalu besar */
}
        /* --- Responsive Design Styles --- */
        .NavSide__toggle {
            display: none; 
            position: fixed;
            top: 15px;
            left: 15px; 
            width: 40px;
            height: 40px;
            z-index: 1100;
            cursor: pointer;
            transition: transform 0.5s ease-in-out; 
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .NavSide__toggle i.bi {
            position: absolute;
            font-size: 28px;
            color: #4B68FB;
            transition: opacity 0.3s ease-in-out;
        }

        .NavSide__toggle .bi.close {
            opacity: 0; 
        }

        .NavSide__toggle.active .bi.open {
            opacity: 0; 
        }

        .NavSide__toggle.active .bi.close {
            opacity: 1; 
        }
        
        .NavSide__toggle.active {
            transform: translateX(50vw); 
        }

        .NavSide__topbar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background-color:rgb(255, 255, 255);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 999;
            align-items: center;
            padding: 0 15px;
            justify-content: space-between;
        }


        /* Responsive styles for screens smaller than 700px */
        @media (max-width: 700px) {
            .NavSide__sidebar {
                width: 50vw; 
                transform: translateX(-100%);
            }

            .NavSide__sidebar.active {
                transform: translateX(0); 
            }

            .NavSide__main-content {
                margin-left: 0;
                padding-top: 75px;
            }

            .NavSide__toggle {
                display: flex; 
            }

            .NavSide__toggle i.bi.open {
                display: block;
            }

            .NavSide__toggle.NavSide__toggle--active {
                color: #4B68FB;
            }

            .NavSide__topbar {
                display: flex;
            }

            .info-card .section {
                flex: 0 0 100%;
                margin-bottom: 1rem;
            }

            .info-card .section:last-child {
                margin-bottom: 0;
            }

            .button-group-bottom {
                flex-direction: row;
                justify-content: flex-end;
                align-items: center;
                margin-top: 1.2cm;
                margin-left: 0;
                margin-right: 0; 
            }
            
            #grup-aksi-dokumen {
                flex-direction: row !important; 
                justify-content: flex-end !important;
                gap: 0 !important; 
                margin-top: 2.5rem;
            }

            #grup-aksi-dokumen .btn {
                width: auto !important; 
                max-width: none !important;
                margin: 0 !important; 
            }

           
            #grup-aksi-dokumen .button-group {
                flex-direction: row !important; 
                gap: 0.5rem !important; 
                margin-top: 0 !important; 
            }
        }  
        
        .button-group-bottom {
            margin-top: 0px; 
            display: flex;
            align-items: center;
            justify-content: flex-end; 
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        /* File Button Styles */
        .file-button {
            display: inline-flex;
            align-items: center;
            background-color: rgb(235, 238, 245);
            border-radius: 20px;
            padding: 12px 20px;
            margin-right: 15px;
            margin-bottom: 15px;
            text-decoration: none;
            color: #4B68FB;
            font-weight: 500;
            font-size: 1rem;
            transition: background-color 0.2s ease, box-shadow 0.2s ease, color 0.2s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .file-button:hover {
            background-color: #4B68FB;
            color: white;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .file-button i {
            font-size: 1.25rem;
            margin-right: 10px;
            color: #4B68FB;
            transition: color 0.2s ease;
        }

        .file-button:hover i {
            color: white;
        }

        /* Penyesuaian responsif untuk tombol berkas */
        @media (max-width: 576px) {
            .file-button {
                flex-basis: 100;
                width: 100%;
                display: flex;
                margin-right: 0;
            }
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 20px;
            border: none;
        }

        .modal-body {
            text-align: center;
            padding: 2rem;
        }

        .modal-title {
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .modal-buttons button {
            min-width: 100px;
        }

    
        .custom-modal-content {
            border-radius: 30px !important;
            background-color: #f8f9fa;
            border: none;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05);
            padding: 20px; 
        }

        .custom-modal-header {
            border-bottom: none;
            justify-content: center;
            padding: 0; 
            margin-bottom: 20px; 
        }

        .custom-modal-body {
            text-align: center;
            padding: 0; 
        }

        #confirmationModal .btn {
            width: auto !important;   
            flex-grow: 0 !important;  
        }






    </style>
</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
            </div>
            <ul class="NavSide__sidebar-nav">
                <!-- MENU "Detail Sidang" DIHAPPU S DARI SINI -->
                <li class="NavSide__sidebar-item "> <!-- Evaluasi aktif -->
                    <b></b><b></b>
                    <a href="dEvaluasiSidang.php?id=<?= $id_sidang ?>">
                        <span class="fw-semibold">Evaluasi</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="dDokumenRevisi.php?id=<?= $id_sidang ?>">
                        <span class="fw-semibold">Dokumen</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dNilaiAkhir.php?id=<?= $id_sidang ?>">
                        <span class="NavSide__sidebar-title fw-semibold">Nilai Akhir</span>
                    </a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDaftarSidang.php">
                        <span class="NavSide__sidebar-title fw-semibold">Kembali</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
        </div>
       
        <main class="NavSide__main-content">
            <h2>Detail Sidang - Sistem Pengajuan Sidang</h2>


<div class="info-card">
    <div class="section">
        <p class="info-item"><i class="fa-solid fa-book"></i><strong>Judul Sidang</strong><br>
            <?php echo htmlspecialchars($judul); ?>
        </p>
        
        <p class="info-item"><i class="fa-solid fa-user"></i><strong>Dosen Pembimbing</strong><br>
            <?php echo $namaPembimbing_html; ?>
        </p>
        
        <p class="info-item"><i class="fa-solid fa-users"></i><strong>Dosen Penguji</strong><br>
            <?php echo $namaPenguji_html; ?>
        </p>
    </div>
    
    <div class="section">
        <p class="info-item"><i class="fa-solid fa-door-open"></i><strong>Ruangan</strong><br>
            <?php echo htmlspecialchars($ruangan); ?>
        </p>

        <p class="info-item"><i class="fa-solid fa-calendar-days"></i><strong>Tanggal</strong><br>
            <?php echo $tanggal_formatted; ?>
        </p>

        <p class="info-item"><i class="fa-solid fa-clock"></i><strong>Jam</strong><br>
            <?php echo htmlspecialchars($jam); ?>
        </p>
    </div>
</div>



            <h5>Dokumen Sidang</h5>
            <div class="file-buttons-container d-flex flex-wrap">
                <a href="#" class="file-button">
                    <i class="fa-solid fa-file-zipper"></i>
                    dokumen_revisi_kel-1.zip
                </a>
            </div>

            <div class="button-group-bottom" id="grup-aksi-dokumen">
                <div class="button-group">
                    <button class="btn btn-tolak" onclick="showConfirmationModal('Ditolak')">Tolak</button>
                    <button class="btn btn-setujui" onclick="showConfirmationModal('Disetujui')">Setujui</button>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="notifModal" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <img src="../../assets/img/centang.svg" width="100" class="mx-auto mb-3" alt="Check Icon">
                    <h5 class="modal-title" id="notifModalLabel"></h5>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal-content">
                <div class="modal-header custom-modal-header">
                    <h4 class="modal-title fw-bold" id="confirmationModalLabel" style="font-size: 24px;">Perhatian!</h4>
                </div>
                <div class="modal-body custom-modal-body">
                    <p class="mb-5 fw-semibold" id="confirmationModalText" style="font-size: 16px;"></p>
                    <div class="d-flex justify-content-between px-4">
                        <button type="button" class="btn btn-tolak fw-semibold" data-bs-dismiss="modal">Batalkan</button>
                        <button type="button" class="btn btn-setujui fw-semibold" id="btnConfirmAction">Lanjutkan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
        // --- Sidebar Toggle Logic for Mobile ---
        const menuToggle = document.querySelector(".NavSide__toggle");
        const sidebar = document.getElementById("main-sidebar");

        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', function() {
                menuToggle.classList.toggle("active");
                sidebar.classList.toggle("active");
            });
        }

        // // --- Sidebar Active Item Logic ---
        // let listItems = document.querySelectorAll(".NavSide__sidebar-item");
        // for (let i = 0; i < listItems.length; i++) {
        //     listItems[i].onclick = function() {
        //         if (!this.classList.contains("NavSide__sidebar-item--active")) {
        //             for (let j = 0; j < listItems.length; j++) {
        //                 listItems[j].classList.remove("NavSide__sidebar-item--active");
        //             }
        //             this.classList.add("NavSide__sidebar-item--active");
        //         }
        //     };
        // }

        

        // --- Modal Logic ---
        function showConfirmationModal(action) {
        const confirmationModalElement = document.getElementById('confirmationModal');
        if (!confirmationModalElement) {
            console.error('Modal HTML dengan id "confirmationModal" tidak ditemukan!');
            return;
        }
        
        const confirmationModal = new bootstrap.Modal(confirmationModalElement);
        const modalText = document.getElementById('confirmationModalText');
        const confirmButton = document.getElementById('btnConfirmAction');

        let actionText = action === 'Disetujui' ? 'menyetujui' : 'menolak';
        
        modalText.innerText = `Apakah Anda yakin ingin ${actionText} dokumen revisi ini?`;

        const newConfirmButton = confirmButton.cloneNode(true);
        confirmButton.parentNode.replaceChild(newConfirmButton, confirmButton);

        newConfirmButton.addEventListener('click', function() {
            confirmationModal.hide(); 
            
            setTimeout(function() {
                if (action === 'Ditolak') {
                    Swal.fire({
                        title: 'Alasan Penolakan',
                        input: 'textarea',
                        inputLabel: 'Catatan:',
                        inputPlaceholder: 'Masukan catatan di sini...',
                        showCancelButton: true,
                        confirmButtonText: 'Kirim',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-setujui',
                            cancelButton: 'btn btn-tolak'
                        },
                        inputValidator: (value) => {
                            if (!value || value.trim() === '') {
                                return 'Alasan penolakan tidak boleh kosong!';
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: `Dokumen revisi telah berhasil ditolak.`,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // --- PERUBAHAN 1: NAVIGASI SETELAH TOLAK ---
                                window.location.href = 'dDaftarSidang.php';
                            });
                            
                            console.log('Catatan Penolakan:', result.value);
                        }
                    });
                } else { // Jika aksi adalah 'Disetujui'
                    Swal.fire({
                        title: 'Berhasil!',
                        text: `Dokumen revisi telah berhasil disetujui.`,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4B68FB'
                    }).then(() => {
                        // --- PERUBAHAN 2: NAVIGASI SETELAH SETUJUI ---
                        window.location.href = 'dNilaiAkhir.php';
                    });
                }
            }, 500); 
        });

        confirmationModal.show();
        }
    </script>

</body>

</html>