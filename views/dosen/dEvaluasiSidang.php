<?php
// <-- BAGIAN AWAL TETAP SAMA -->
// session_start(); // Dinonaktifkan untuk pengujian
require "../../koneksi/koneksiAndrew.php"; // Pastikan path ini benar

// ===================================================================================
// BAGIAN 1: KEAMANAN DAN INISIALISASI
// ===================================================================================
// --- SIMULASI LOGIN (TIDAK PERLU DIUBAH-UBAH, HANYA UNTUK QUERY NILAI/CATATAN) ---
$nomor_dosen_login = '1001'; 

// Nonaktifkan pengecekan session yang asli
/*
if (!isset($_SESSION['user']['nomor_dosen'])) {
    die("Akses ditolak. Silakan login sebagai dosen.");
}
$nomor_dosen_login = $_SESSION['user']['nomor_dosen'];
*/

// if (!isset($_GET['id_sidang']) || !is_numeric($_GET['id_sidang'])) {
//     die("ID Sidang tidak valid atau tidak ditemukan.");
// }

// $id_sidang = (int)$_GET['id_sidang']; // Ambil id_sidang dari URL


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: ID Sidang tidak valid."); // Samakan juga pesan errornya
} 

$id_sidang = (int)$_GET['id']; // Ambil "id" dari URL
// Variabel untuk menampung data yang akan ditampilkan
$id_kelompok = null;
$judul = 'Data tidak ditemukan';
$ruangan = '-';
$tanggal_formatted = '-';
$jam = '-';
$dosenPembimbing = [];
$dosenPenguji = [];
$catatan_revisi = '';
$nilai_mahasiswa = [
    'n_dokumen' => '', 'n_presentasi' => '', 'n_tanyajawab' => '', 'n_proyek' => ''
];

// ===================================================================================
// BAGIAN 2: PROSES PENYIMPANAN DATA (SAAT FORM DI-SUBMIT)
// ===================================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form. id_sidang sudah ada dari URL
    $catatan_post = $_POST['catatanEvaluasi'];
    $nilaiLaporan = !empty($_POST['nilaiLaporan']) ? (int)$_POST['nilaiLaporan'] : null;
    $nilaiPresentasi = !empty($_POST['materiPresentasi']) ? (int)$_POST['materiPresentasi'] : null;
    $nilaiPenyampaian = !empty($_POST['nilaiPenyampaian']) ? (int)$_POST['nilaiPenyampaian'] : null;
    $nilaiProyek = !empty($_POST['nilaiProyek']) ? (int)$_POST['nilaiProyek'] : null;

    $conn_post = sqlsrv_connect($serverName, $connectionOptions);

    $sql_update_catatan = "UPDATE Detail_Sidang SET catatan_sidang = ? WHERE id_sidang = ? AND nomor_dosen = ?";
    $params_update_catatan = [$catatan_post, $id_sidang, $nomor_dosen_login];
    $stmt_update_catatan = sqlsrv_query($conn_post, $sql_update_catatan, $params_update_catatan);
    if ($stmt_update_catatan === false) { die("Gagal memperbarui catatan revisi: " . print_r(sqlsrv_errors(), true)); }
    sqlsrv_free_stmt($stmt_update_catatan);

    $sql_cek_nilai = "SELECT COUNT(*) as 'count' FROM Penilaian WHERE id_sidang = ? AND nomor_dosen = ?";
    $stmt_cek_nilai = sqlsrv_query($conn_post, $sql_cek_nilai, [$id_sidang, $nomor_dosen_login]);
    $nilai_exists = sqlsrv_fetch_array($stmt_cek_nilai, SQLSRV_FETCH_ASSOC)['count'] > 0;
    
    if ($nilai_exists) {
        $sql_nilai = "UPDATE Penilaian SET n_dokumen = ?, n_presentasi = ?, n_tanyajawab = ?, n_proyek = ? WHERE id_sidang = ? AND nomor_dosen = ?";
        $params_nilai = [$nilaiLaporan, $nilaiPresentasi, $nilaiPenyampaian, $nilaiProyek, $id_sidang, $nomor_dosen_login];
    } else {
        $sql_get_nim = "SELECT TOP 1 km.nim FROM Sidang s JOIN Kelompok_Mahasiswa km ON s.id_kelompok = km.id_kelompok WHERE s.id_sidang = ?";
        $stmt_get_nim = sqlsrv_query($conn_post, $sql_get_nim, [$id_sidang]);
        $nim_untuk_insert = sqlsrv_fetch_array($stmt_get_nim, SQLSRV_FETCH_ASSOC)['nim'];
        $sql_nilai = "INSERT INTO Penilaian (id_sidang, nim, nomor_dosen, n_dokumen, n_presentasi, n_tanyajawab, n_proyek) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params_nilai = [$id_sidang, $nim_untuk_insert, $nomor_dosen_login, $nilaiLaporan, $nilaiPresentasi, $nilaiPenyampaian, $nilaiProyek];
    }
    
    $stmt_nilai = sqlsrv_query($conn_post, $sql_nilai, $params_nilai);
    if ($stmt_nilai === false) { die("Gagal menyimpan nilai: " . print_r(sqlsrv_errors(), true)); }
    
    sqlsrv_close($conn_post);

    // $_SESSION['status'] = ['type' => 'success', 'message' => 'Evaluasi berhasil disimpan!'];
    header("Location: dEvaluasiSidang.php?id_sidang=" . $id_sidang);
    exit();
}
// <-- AKHIR BAGIAN AWAL TETAP SAMA -->

// ===================================================================================
// BAGIAN 3: PENGAMBILAN DATA UNTUK DITAMPILKAN
// ===================================================================================

$sql_sidang = "SELECT Judul, id_kelompok FROM Sidang WHERE id_sidang = ?";
$result_sidang = sqlsrv_query($conn, $sql_sidang, [$id_sidang]);
if ($result_sidang === false) { die("Error Query Sidang: " . print_r(sqlsrv_errors(), true)); }
$data_sidang = sqlsrv_fetch_array($result_sidang, SQLSRV_FETCH_ASSOC);

if ($data_sidang) {
    $judul = $data_sidang['Judul'];
    $id_kelompok = $data_sidang['id_kelompok'];

    // --- LOGIKA PENGAMBILAN DOSEN ---
    if ($id_kelompok) {
        // ### PERUBAHAN DI SINI: Ditambahkan DISTINCT untuk mencegah duplikasi ###
        $sql_pembimbing = "SELECT DISTINCT d.nama_dosen FROM [dbo].[Bimbingan] b JOIN [dbo].[Dosen] d ON b.nomor_dosen = d.nomor_dosen WHERE b.id_kelompok = ? AND d.isPembimbing = 0x01";
        $stmt_pembimbing = sqlsrv_query($conn, $sql_pembimbing, [$id_kelompok]);
        if ($stmt_pembimbing) {
            while ($row = sqlsrv_fetch_array($stmt_pembimbing, SQLSRV_FETCH_ASSOC)) {
                $dosenPembimbing[] = $row['nama_dosen'];
            }
        }
    }
    
    // ### PERUBAHAN DI SINI: Ditambahkan DISTINCT untuk membuat query lebih aman ###
    $sql_penguji = "SELECT DISTINCT d.nama_dosen FROM [dbo].[Penjadwalan] p JOIN [dbo].[Dosen] d ON p.nomor_dosen = d.nomor_dosen WHERE p.id_sidang = ? AND d.isPenguji = 0x01";
    $stmt_penguji = sqlsrv_query($conn, $sql_penguji, [$id_sidang]);
    if ($stmt_penguji) {
        while ($row = sqlsrv_fetch_array($stmt_penguji, SQLSRV_FETCH_ASSOC)) {
            $dosenPenguji[] = $row['nama_dosen'];
        }
    }
    // --- AKHIR PERBAIKAN ---

    // <-- LANJUTAN KODE TETAP SAMA -->
    $sql_jadwal = "SELECT ruang_sidang, tanggal_sidang, jam_sidang FROM Jadwal WHERE id_sidang = ?";
    $result_jadwal = sqlsrv_query($conn, $sql_jadwal, [$id_sidang]);
    if ($result_jadwal && $data_jadwal = sqlsrv_fetch_array($result_jadwal, SQLSRV_FETCH_ASSOC)) {
        $ruangan = $data_jadwal['ruang_sidang'] ?? '-';
        $jam = $data_jadwal['jam_sidang'] ? $data_jadwal['jam_sidang']->format('H:i') : '-';
        if ($data_jadwal['tanggal_sidang'] instanceof DateTime) {
            setlocale(LC_TIME, 'id_ID.UTF-8', 'Indonesian');
            $tanggal_formatted = strftime('%A, %d %B %Y', $data_jadwal['tanggal_sidang']->getTimestamp());
        }
    }

    $sql_catatan = "SELECT catatan_sidang FROM Detail_Sidang WHERE id_sidang = ? AND nomor_dosen = ?";
    $result_catatan = sqlsrv_query($conn, $sql_catatan, [$id_sidang, $nomor_dosen_login]);
    if ($result_catatan && $row_catatan = sqlsrv_fetch_array($result_catatan, SQLSRV_FETCH_ASSOC)) { $catatan_revisi = $row_catatan['catatan_sidang']; }

    $sql_get_nilai = "SELECT n_dokumen, n_presentasi, n_tanyajawab, n_proyek FROM Penilaian WHERE id_sidang = ? AND nomor_dosen = ?";
    $result_get_nilai = sqlsrv_query($conn, $sql_get_nilai, [$id_sidang, $nomor_dosen_login]);
    if ($result_get_nilai && $row_nilai = sqlsrv_fetch_array($result_get_nilai, SQLSRV_FETCH_ASSOC)) { $nilai_mahasiswa = $row_nilai; }
}

$namaPembimbing_html = !empty($dosenPembimbing) ? implode('<br>', array_map('htmlspecialchars', $dosenPembimbing)) : 'Belum ditentukan';
$namaPenguji_html = !empty($dosenPenguji) ? implode('<br>', array_map('htmlspecialchars', $dosenPenguji)) : 'Belum ditentukan';

// sqlsrv_close($conn);
?>
<!DOCTYPE html>
<html lang="id">
<!-- KODE HTML LANJUTANNYA TETAP SAMA -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Sidang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <style>
    /* --- STYLE DASAR & FONT --- */
    /* Reset margin, padding, dan box-sizing untuk semua elemen agar konsisten di semua browser */

    /* --- STYLE DASAR & FONT --- */
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Poppins", sans-serif; }
    body { min-height: 100vh; background-color: #ffffff; }

    /* --- LAYOUT UTAMA --- */
    #NavSide { display: flex; min-height: 100vh; position: relative; }
    #page-content-wrapper { flex-grow: 1; display: flex; flex-direction: column; position: relative; margin-left: 280px; transition: margin-left 0.5s ease-in-out; }

    /* --- SIDEBAR (NAVIGASI KIRI) --- */
    .NavSide__sidebar { position: fixed; top: 0; left: 0; bottom: 0; width: 280px; border-left: 5px solid #4B68FB; background: #4B68FB; overflow-x: hidden; overflow-y: auto; z-index: 1000; display: flex; flex-direction: column; transition: transform 0.5s ease-in-out, width 0.5s ease-in-out; }
    .NavSide__sidebar-brand { padding: 10% 5% 50% 5%; text-align: center; }
    .NavSide__sidebar-brand img { width: 90%; max-width: 180px; height: auto; display: inline-block; filter: brightness(0) invert(1); }
    .NavSide__sidebar-nav { width: 100%; padding-left: 0; padding-top: 0; list-style: none; flex-grow: 1; }
    
    /* ======================================================= */
    /* === PERBAIKAN UTAMA DIMULAI DARI SINI === */
    /* ======================================================= */

    /* Style dasar untuk setiap item menu di sidebar */
    .NavSide__sidebar-item { 
        position: relative; 
        display: block; 
        width: 100%; 
        border-top-left-radius: 20px; 
        border-bottom-left-radius: 20px; 
        margin-bottom: 15px; 
    }
    
    /* Style untuk link (tag <a>) di dalam item menu */
    .NavSide__sidebar-item a { 
        position: relative; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        width: 100%; 
        text-decoration: none; 
        color: rgb(252, 252, 252); 
        height: 60px; 
        box-sizing: border-box; 
    }

    /* Style untuk judul/teks di dalam link menu */
    .NavSide__sidebar-title { 
        white-space: normal; 
        text-align: center; 
        line-height: 1.5; 
    }
    
    /* Style untuk item menu yang sedang AKTIF (latar putih, teks biru) */
    .NavSide__sidebar-item.NavSide__sidebar-item--active { 
        background: #ffffff; 
    }
    .NavSide__sidebar-item.NavSide__sidebar-item--active a { 
        color: #4B68FB !important; 
    }
    
    /* Efek Sudut Melengkung pada Item Aktif (menggunakan elemen <b>) */
    .NavSide__sidebar-item b {
        position: absolute;
        height: 20px;
        width: 100%;
        background: #ffffff; /* Warna ini HARUS SAMA dengan background item aktif */
        display: none;       /* Sembunyikan secara default */
    }
    .NavSide__sidebar-item b:nth-child(1) { top: -20px; }  /* Posisi lengkungan atas */
    .NavSide__sidebar-item b:nth-child(2) { bottom: -20px; } /* Posisi lengkungan bawah */

    /* Pseudo-elemen '::before' untuk menciptakan bentuk lengkungannya */
    .NavSide__sidebar-item b::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #4B68FB; /* Warna ini HARUS SAMA dengan background sidebar */
    }
    .NavSide__sidebar-item b:nth-child(1)::before { border-bottom-right-radius: 20px; }
    .NavSide__sidebar-item b:nth-child(2)::before { border-top-right-radius: 20px; }

    /* KUNCI UTAMA: Tampilkan elemen lengkungan HANYA pada item yang aktif */
    .NavSide__sidebar-item.NavSide__sidebar-item--active b {
        display: block;
    }
    
    /* ======================================================= */
    /* === AKHIR DARI BAGIAN YANG DIPERBAIKI === */
    /* ======================================================= */

    /* --- KONTEN UTAMA & KOMPONENNYA --- */
    .NavSide__main-content { flex-grow: 1; padding: 20px 0.7cm 20px calc(20px + 0.7cm); margin-right: 0; overflow-y: auto; padding-top: 20px; }
    .NavSide__main-content h2 { margin-bottom: 0.9cm; font-weight: 700; }
    .NavSide__main-content h3 { font-weight: 700; font-size: 1.4rem; margin-bottom: 0.2cm; }

    /* Badge Status */
    .status-badge { background-color: #FFA3A3; color: black; border-radius: 20px; padding: 8px 18px; display: inline-block; font-size: 0.875rem; box-shadow: 0 3px 5px rgba(0, 0, 0, 0.08); font-weight: bold; margin-bottom: 1.2cm; }
    .status-badge.approved { background-color: #4BFBAF; } /* Warna badge jika statusnya 'approved' */

    /* Kartu Informasi (Info Card) */
    .info-card { position: relative; background: rgb(235, 238, 245); border-radius: 30px; box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05); padding: 25px; display: flex; justify-content: space-between; flex-wrap: wrap; overflow: hidden; transition: background-color 0.4s ease; margin-bottom: 1.2cm; margin-right: 0.9cm; }
    /* Efek hover biru yang menutupi kartu (trik dengan pseudo-elemen '::after') */
    .info-card::after { content: ""; position: absolute; top: 0; right: 0; width: 60px; height: 100%; background-color: #4B68FB; border-top-right-radius: 20px; border-bottom-right-radius: 20px; transition: width 0.4s ease; z-index: 0; }
    .info-card:hover::after { width: 100%; border-radius: 20px; }
    /* Section di dalam info-card (perlu z-index agar teks tetap di atas efek hover) */
    .info-card .section { flex: 0 0 48%; z-index: 1; color: #333; transition: color 0.4s ease; display: flex; flex-direction: column; justify-content: space-between; }
    .info-card:hover .section { color: white; } /* Teks menjadi putih saat di-hover */
    .info-card .section .info-group { margin-bottom: 1rem; }
    .info-card .section .info-group:last-child { margin-bottom: 0; }
    .info-card .section .label-row { display: flex; align-items: center; margin-bottom: 0.25rem; font-size: 1rem; }
    .info-card .section .label-row i { margin-right: 10px; color: #495057; font-weight: 900; transition: color 0.4s ease; width: 20px; text-align: center; }
    .info-card:hover .section .label-row i { color: white; } /* Ikon menjadi putih saat di-hover */
    .info-card .section .label-row .fw-bold { font-weight: 600; font-size: 1.05rem; }
    .info-card .section .value-row { margin-left: 30px; line-height: 1.5; font-size: 0.95rem; margin-bottom: 0; }

    /* Kartu Form (Form Card) & Elemen Form */
    .form-card { background:rgb(235, 238, 245); border-radius: 30px; box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05); padding: 15px 25px; margin-bottom: 1.2cm; margin-right: 0.9cm; }
    .form-card h4 { font-weight: 600; font-size: 1.05rem; margin-bottom: 0.8cm; }
    /* Layout untuk baris input penilaian */
    .penilaian-row { display: flex; flex-wrap: wrap; justify-content: flex-start; align-items: center; margin-top: 0.5cm; margin-bottom: 1rem; margin-left: 0 !important; margin-right: 0 !important; gap: 20px; }
    .penilaian-row .col-item { padding: 0 !important; flex: 0 0 auto; width: calc(25% - (20px * 3 / 4)); box-sizing: border-box; display: flex; align-items: center; margin-bottom: 0; }
    .penilaian-row label { flex-shrink: 0; white-space: nowrap; margin-right: 10px; font-weight: 550; margin-top: 0; color: #333; min-width: unset; text-align: left; }
    .penilaian-row .colon { flex-shrink: 0; margin-right: 10px; color: #333; }
    /* Style untuk input nilai yang kecil */
    input.form-control-custom.input-nilai { width: 75px; font-size: 1rem; height: 40px; padding: 5px 10px; text-align: center; flex-grow: 0; min-width: unset; background-color: #ffffff !important; border: 1px solid #F2F2F2; border-radius: 10px; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
    .form-card .form-control-custom.input-nilai:focus { border-color: #4B68FB; box-shadow: 0 0 0 0.25rem rgba(75, 104, 251, 0.25); outline: none; }
    /* Grup form umum (label + input) */
    .form-group-custom { margin-bottom: 1rem; display: flex; align-items: center; flex-wrap: wrap; }
    .form-group-custom label { flex: 0 0 180px; margin-right: 20px; font-size: 1rem; font-weight: 500; color: #333; }
    .form-group-custom .form-control-custom { flex: 1; min-width: 200px; background-color: white !important; border: 1px solid #F2F2F2; border-radius: 10px; padding: 10px 15px; font-size: 0.95rem; height: 45px; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
    .form-group-custom .form-control-custom:focus { border-color: #4B68FB; box-shadow: 0 0 0 0.25rem rgba(75, 104, 251, 0.25); outline: none; }
    .form-group-custom textarea.form-control-custom { min-height: 200px; resize: vertical; }

    /* --- TOMBOL-TOMBOL --- */
    /* Grup tombol di bagian bawah halaman */
    .button-group-bottom { display: flex; justify-content: space-between; align-items: center; margin-top: 1.2cm; margin-right: 0.9cm; }
    /* Tombol Kembali */
    .btn-kembali { background-color: #4B68FB; color: white; border: none; border-radius: 20px; padding: 0 25px; cursor: pointer; font-size: 0.95rem; font-weight: 500; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease; display: flex; align-items: center; justify-content: center; height: 45px; text-decoration: none;}
    .btn-kembali:hover { position: relative; background-color: white; color: #4B68FB; border: 1px solid #4B68FB; }
    .btn-kembali .icon-circle { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; background-color: white; border-radius: 50%; margin-right: 10px; transition: background-color 0.3s ease; }
    .btn-kembali:hover .icon-circle { background-color: #4B68FB; }
    .btn-kembali .icon-circle i { color: #4B68FB; }
    .btn-kembali:hover .icon-circle i { color: white; }
    /* Tombol Kirim */
    .btn-kirim { background-color: #4FD382; color: #FFFFFF; border: none; border-radius: 20px; padding: 0 25px; cursor: pointer; font-size: 0.95rem; font-weight: 500; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease; display: flex; align-items: center; justify-content: center; height: 45px; }
    .btn-kirim:hover { background-color: #3AB070; color: white; }

    /* --- MODAL (POP-UP) --- */
    /* Style untuk konten modal (sebelumnya) */
    .modal-content.custom-modal-content { border-radius: 30px !important; background-color: #f8f9fa; border: none; box-shadow: 0 10px 10px rgba(0, 0, 0, 0.05); padding: 20px; }
    /* Header modal (sebelumnya) */
    .modal-header.custom-modal-header { border-bottom: none; justify-content: center; padding: 0; margin-bottom: 20px; }
    /* Body modal (sebelumnya) */
    .modal-body.custom-modal-body { text-align: center; padding: 0; }
    /* Tombol di dalam modal (sebelumnya) */
    .btn-tolak, .btn-setujui { border-radius: 20px; cursor: pointer; font-size: 0.95rem; font-weight: 500; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); transition: background-color 0.3s ease, transform 0.2s ease, color 0.3s ease; height: 45px; border: none; display: flex; align-items: center; justify-content: center; padding: 0 25px; }
    .btn-tolak { background-color: #FD7D7D; color: white; }
    .btn-tolak:hover { background-color: #F85C5C; color: white; }
    .btn-setujui { background-color: #4FD382; color: white; }
    .btn-setujui:hover { background-color: #3AB070; color: white; }
    
    /* Pesan Error Validasi */
    .error-message { color: red; font-size: 0.9rem; font-weight: 500; display: none; margin-top: 10px; margin-left: 0; text-align: left; }

    /* --- RESPONSIVE DESIGN --- */

    /* Style untuk Tablet (lebar layar maks 768px) */
    @media (max-width: 768px) {
        .penilaian-row { margin-left: 0 !important; margin-right: 0 !important; gap: 15px 0; }
        .penilaian-row .col-3 { width: 100%; justify-content: flex-start; padding: 0 !important; margin-bottom: 0; }
        .penilaian-row label { min-width: unset; text-align: left; margin-right: 10px; }
        .form-card .form-control-custom.input-nilai { flex-grow: 1; width: auto; }
        .form-group-custom { flex-direction: column; align-items: flex-start; }
        .form-group-custom label { flex: none; width: 100%; margin-bottom: 0.5rem; margin-right: 0; }
        .form-group-custom .form-control-custom { width: 100%; min-width: unset; }
        .modal-dialog { margin: 1rem auto; max-width: 95% !important; }
        .modal-content.custom-modal-content { padding: 15px !important; }
        .modal-body.custom-modal-body { padding: 0 !important; text-align: center; }
        .modal-body.custom-modal-body > .d-flex.justify-content-between.px-5 { padding-left: 20px !important; padding-right: 20px !important; gap: 15px; width: 100%; flex-wrap: nowrap; }
        .modal-body.custom-modal-body .btn-tolak, .modal-body.custom-modal-body .btn-setujui { flex-grow: 1; flex-shrink: 0; flex-basis: auto; width: auto; height: 40px; font-size: 0.9rem; padding: 0 10px !important; border-radius: 18px; box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1); }
    }
    
    /* Style untuk Mobile (lebar layar maks 700px) */
    @media (max-width: 700px) {
        /* Sidebar menjadi slide-in menu (tersembunyi secara default) */
        .NavSide__sidebar { width: 280px; transform: translateX(-280px); border-left-width: 0; z-index: 1040; padding-top: 35px; }
        .NavSide_sidebar.NavSide_sidebar--active-mobile { transform: translateX(0); box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2); }
        .NavSide__sidebar-brand { padding: 10% 5% 50% 5%; }
        .NavSide__sidebar-brand img { width: 90%; }
        .NavSide__sidebar-nav { padding-top: 3%; }
        .NavSide__sidebar-item a { height: 50px; }
        /* Tombol Toggle (hamburger menu) ditampilkan */
        .NavSide__toggle { display: block; }
        .NavSide_toggle.NavSide_toggle--active { transform: translateX(280px); }
        /* Konten utama memenuhi seluruh layar */
        #page-content-wrapper { margin-left: 0; }
        /* Topbar ditampilkan di mobile */
        .NavSide__topbar { display: block; }
        .NavSide__main-content { margin-left: 0; padding: 20px; padding-top: calc(60px + 20px); margin-right: 0; }
        .NavSide__main-content h2 { margin-bottom: 0.5cm; }
        .status-badge { margin-bottom: 0.5cm; }
        .info-card { margin-bottom: 0.5cm; margin-right: 0; flex-direction: column; }
        .info-card .section { flex: 0 0 100%; margin-bottom: 1rem; }
        .info-card .section:last-child { margin-bottom: 0; }
        .form-card { margin-right: 0; padding: 15px; }
        .button-group-bottom { flex-direction: row; justify-content: space-between; align-items: center; margin-top: 1.2cm; margin-left: 0; margin-right: 0; }
        .btn-kembali, .btn-kirim { width: auto; flex: none; margin: 0; height: 40px; padding: 0 10px; font-size: 0.85rem; border-radius: 18px; box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1); }
        .btn-kembali .icon-circle { width: 25px; height: 25px; margin-right: 8px; }
        .btn-kembali .icon-circle i { font-size: 1.1rem; }
        .penilaian-row label { width: 150px; flex-shrink: 0; white-space: nowrap; margin-right: 10px; text-align: left; }
        input.form-control-custom.input-nilai { flex-grow: 1; width: auto; min-width: unset; }
    }
</style>
</head>
<body>
  <div id="NavSide">
    <div id="main-sidebar" class="NavSide__sidebar">
        <div class="NavSide__sidebar-brand">
            <img src="../../assets/img/WhiteAstra.png" alt="Astra Logo" />
        </div>
        
        <!-- PERBAIKAN UTAMA ADA DI DALAM 'ul' INI -->
        <ul class="NavSide__sidebar-nav">
            
            <!-- PERBAIKAN: Nama kelas diperbaiki dan dipisah dengan spasi -->
            <!-- Item ini akan menjadi aktif karena memiliki DUA kelas -->
            <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                <b></b><b></b>
                <a href="dEvaluasiSidang.php?id=<?= $id_sidang ?>">
                    <!-- PERBAIKAN: Nama kelas span juga diperbaiki -->
                    <span class="fw-semibold NavSide__sidebar-title">Evaluasi</span>
                </a>
            </li>
            
            <!-- PERBAIKAN: Nama kelas diperbaiki -->
            <li class="NavSide__sidebar-item">
                <b></b><b></b>
                <a href="dDokumenRevisi.php?id=<?= $id_sidang ?>">
                    <span class="fw-semibold NavSide__sidebar-title">Dokumen</span>
                </a>
            </li>
            
            <!-- PERBAIKAN: Nama kelas diperbaiki -->
            <li class="NavSide__sidebar-item">
                <b></b><b></b>
                <a href="dNilaiAkhir.php?id=<?= $id_sidang ?>">
                    <span class="fw-semibold NavSide__sidebar-title">Nilai Akhir</span>
                </a>
            </li>
            <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="dDaftarSidang.php"><span class="NavSide__sidebar-title fw-semibold"> Kembali</span></a>
                </li>


            
            
        </ul>
    </div>
    
    <!-- Sisa dari halaman Anda (seperti page-content-wrapper, dll.) -->
    <!-- ... -->

        <div class="NavSide__toggle"><i class="bi bi-list open"></i><i class="bi bi-x-lg close"></i></div>
        <div id="page-content-wrapper">
            <div class="NavSide__topbar"></div>
            <main class="NavSide__main-content">
                <h2>Detail Evaluasi - Sistem Evaluasi Sidang</h2>
                <form id="evaluasiForm" method="POST" action="dEvaluasiSidang.php?id_sidang=<?php echo $id_sidang; ?>">
                    <div class="info-card">
                        <div class="section">
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-file-invoice"></i><span class="fw-bold">Judul Sidang</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($judul); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-user-tie"></i><span class="fw-bold">Dosen Pembimbing</span></div>
                                <div class="value-row"><?php echo $namaPembimbing_html; ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-user-group"></i><span class="fw-bold">Dosen Penguji</span></div>
                                <div class="value-row"><?php echo $namaPenguji_html; ?></div>
                            </div>
                        </div>
                        <div class="section">
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-door-open"></i><span class="fw-bold">Ruangan</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($ruangan); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-calendar-days"></i><span class="fw-bold">Tanggal</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($tanggal_formatted); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="label-row"><i class="fa-solid fa-clock"></i><span class="fw-bold">Jam</span></div>
                                <div class="value-row"><?php echo htmlspecialchars($jam); ?></div>
                            </div>
                        </div>
                    </div>
                    <h3>Nilai Sidang (Sementara)</h3>
                    <div class="form-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4>Masukkan Nilai Sidang <span style="color: red;">*</span></h4>
                        </div>
                        <div class="penilaian-row">
                            <div class="col-item"><label for="nilaiLaporan">Nilai laporan :</label><input type="number" id="nilaiLaporan" name="nilaiLaporan" class="form-control-custom input-nilai" min="0" max="100" ></div>
                            <div class="col-item"><label for="materiPresentasi">Materi Presentasi :</label><input type="number" id="materiPresentasi" name="materiPresentasi" class="form-control-custom input-nilai" min="0" max="100" ></div>
                            <div class="col-item"><label for="nilaiPenyampaian">Penyampaian :</label><input type="number" id="nilaiPenyampaian" name="nilaiPenyampaian" class="form-control-custom input-nilai" min="0" max="100" ></div>
                            <div class="col-item"><label for="nilaiProyek">Nilai Proyek :</label><input type="number" id="nilaiProyek" name="nilaiProyek" class="form-control-custom input-nilai" min="0" max="100" ></div>
                        </div>
                        <p class="error-message" id="nilaiSidangErrorMessage"> *Semua nilai harus diisi!</p>
                    </div>
                    <h3>Catatan Evaluasi Sidang</h3>
                    <div class="form-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4>Masukkan Catatan Evaluasi Sidang <span style="color: red;">*</span></h4>
                        </div>
                        <div class="form-group-custom">
                            <label for="catatanEvaluasi" class="visually-hidden">Catatan Evaluasi</label>
                            <textarea id="catatanEvaluasi" name="catatanEvaluasi" class="form-control-custom" placeholder="Silahkan masukkan Catatan Evaluasi Sidang disini.."><?php echo htmlspecialchars($catatan_revisi); ?></textarea>
                        </div>
                        <p class="error-message" id="catatanEvaluasiErrorMessage"> *Harus diisi!</p>
                    </div>
                    <div class="button-group-bottom">
                        
                        <button style="margin-left:90%" type="button" class="btn-kirim" id="btnKirim">Kirim</button>
                    </div>
                </form>
            </main>
        </div>
    </div>
    <div class="modal fade" id="confirmationKirimModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="confirmationKirimModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal-content border-0 rounded-4 text-center py-4 px-3" style="background-color: #f8f9fa;">
                <div class="modal-header custom-modal-header border-0 justify-content-center"><h4 class="modal-title fw-bold" id="confirmationKirimModalLabel" style="font-size: 24px;">Perhatian!</h4></div>
                <div class="modal-body custom-modal-body">
                    <p class="mb-5 fw-semibold" style="font-size: 16px;">Apakah anda yakin hendak mengirimkan evaluasi sidang?</p>
                    <div class="d-flex justify-content-between px-5"><button type="button" class="btn btn-tolak fw-semibold" data-bs-dismiss="modal">Batalkan</button><button type="button" class="btn btn-setujui fw-semibold" id="btnKonfirmasiKirim">Kirimkan</button></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            let menuToggle = document.querySelector(".NavSide__toggle");
            let sidebar = document.getElementById("main-sidebar");
            if (menuToggle && sidebar) {
                menuToggle.onclick = function() {
                    menuToggle.classList.toggle("NavSide__toggle--active");
                    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                };
            }
            let listItems = document.querySelectorAll(".NavSide__sidebar-item");
            if (listItems.length > 0) {
                listItems.forEach(item => {
                    item.addEventListener('click', function (event) { 
                        listItems.forEach(innerItem => {
                            innerItem.classList.remove("NavSide__sidebar-item--active");
                        });
                        this.classList.add("NavSide__sidebar-item--active");
                    });
                });
                const currentPath = window.location.pathname.split('/').pop();
                listItems.forEach(item => {
                    const link = item.querySelector('a');
                    if (link) {
                        const linkHref = link.getAttribute('href'); 
                        if (linkHref && linkHref.toLowerCase().includes(currentPath.toLowerCase())) {
                            item.classList.add("NavSide__sidebar-item--active");
                        }
                    }
                });
            }
            const btnKirim = document.getElementById('btnKirim');
            const nilaiLaporan = document.getElementById('nilaiLaporan');
            const materiPresentasi = document.getElementById('materiPresentasi');
            const nilaiPenyampaian = document.getElementById('nilaiPenyampaian');
            const nilaiProyek = document.getElementById('nilaiProyek');
            const catatanEvaluasi = document.getElementById('catatanEvaluasi');
            const nilaiSidangError = document.getElementById('nilaiSidangErrorMessage');
            const catatanEvaluasiError = document.getElementById('catatanEvaluasiErrorMessage');
            const confirmationKirimModalElement = document.getElementById('confirmationKirimModal');
            if (confirmationKirimModalElement) {
                const btnKonfirmasiKirim = document.getElementById('btnKonfirmasiKirim');
                btnKirim.addEventListener('click', function(event) {
                    let isValid = true;
                    nilaiSidangError.style.display = 'none';
                    catatanEvaluasiError.style.display = 'none';
                    if (nilaiLaporan.value.trim() === '' || materiPresentasi.value.trim() === '' || nilaiPenyampaian.value.trim() === '' || nilaiProyek.value.trim() === '') {
                        nilaiSidangError.style.display = 'block';
                        isValid = false;
                    }
                    if (catatanEvaluasi.value.trim() === '') {
                        catatanEvaluasiError.style.display = 'block';
                        isValid = false;
                    }
                    if (!isValid) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'Harap mengisi kolom nilai dan catatan terlebih dahulu sebelum mengirim!',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#4B68FB'
                        });
                    } else {
                        const confirmationKirimModal = new bootstrap.Modal(confirmationKirimModalElement);
                        confirmationKirimModal.show();
                    }
                });
                btnKonfirmasiKirim.addEventListener('click', function() {
                    const confirmationKirimModalInstance = bootstrap.Modal.getInstance(confirmationKirimModalElement);
                    if (confirmationKirimModalInstance) {
                        confirmationKirimModalInstance.hide();
                    }
                    Swal.fire({
                        title: 'Evaluasi Sidang Berhasil Dikirim!',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4B68FB'
                    }).then(() => {
                        document.getElementById('evaluasiForm').submit();
                    });
                });
            }
            document.querySelectorAll('.input-nilai').forEach(function(input){
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                    if (this.value.length > 3) this.value = this.value.slice(0, 3);
                    if (this.value.length > 1 && this.value.startsWith('0')) this.value = this.value.replace(/^0+/, '');
                    if (parseInt(this.value) > 100) this.value = '100';
                });
            });
        });
    </script>
</body>
</html>
