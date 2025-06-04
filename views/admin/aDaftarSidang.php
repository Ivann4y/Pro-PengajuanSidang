<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Sidang - ASTRATech</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body {
        /* Global Resets and Font - (Sama seperti sebelumnya) */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Segoe UI", sans-serif;
    background-color: #F8F9FA;
    color: #333333;
    display: flex;
    min-height: 100vh;
    font-size: 14px;
}

.container {
    display: flex;
    width: 100%;
}

/* Sidebar - (Sama seperti sebelumnya) */
.sidebar {
    width: 250px;
    background-color: #3A57D0;
    color: white;
    display: flex;
    flex-direction: column;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
}

.sidebar-header {
    padding: 28px 25px;
    text-align: left;
}

.logo .logo-main {
    font-size: 24px;
    font-weight: 700;
    color: #FFFFFF;
    letter-spacing: 0.5px;
}

.logo .logo-sub {
    font-size: 10px;
    font-weight: 500;
    color: #E0E0E0;
    letter-spacing: 0.5px;
    margin-top: 2px;
}

.sidebar-nav {
    margin-top: 30px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.sidebar-nav ul {
    list-style: none;
    padding-bottom: 20px;
}

.sidebar-nav li a {
    display: block;
    color: #E0E0E0;
    text-decoration: none;
    padding: 16px 25px;
    font-size: 16px;
    font-weight: 500;
    transition: background-color 0.2s ease, color 0.2s ease;
    border-top-left-radius: 20px;
    border-bottom-left-radius: 20px;
}

.sidebar-nav li a.logout-link-nav {
    color: #E0E0E0;
}

.sidebar-nav li:not(.active) a:hover,
.sidebar-nav li a.logout-link-nav:hover {
    background-color: #3A57D0;
    color: #FFFFFF;
}

.sidebar-nav li.active a {
    background-color: #FFFFFF;
    color: #3A57D0;
    font-weight: 600;
}

/* Main Content Area - (Sama seperti sebelumnya) */
.main-content {
    flex-grow: 1;
    margin-left: 250px;
    background-color: #FFFFFF;
    padding: 0 35px;
}

.main-header.new-layout {
    height: 80px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #EAEEF2;
    padding-bottom: 20px;
    margin-bottom: 0;
}

.profile-icon-wrapper svg {
    cursor: pointer;
}

.content-area {
    padding-top: 30px;
    padding-bottom: 30px;
}

.page-main-title {
    font-size: 28px; /* Ukuran Disesuaikan dengan gambar terakhir */
    font-weight: 700;
    color: #3A57D0;
}

.section-controls-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
    margin-bottom: 20px;
}

.section-title {
    font-size: 26px;
    font-weight: 700;
    color: #111827;
}

.search-bar-wrapper input[type="text"] {
    width: 280px;
    padding: 10px 15px 10px 40px;
    border: 1px solid #D1D5DB;
    border-radius: 8px;
    font-size: 14px;
    background-color: #F3F4F6;
    color: #374151;
}
.search-bar-wrapper input[type="text"]::placeholder {
    color: #9CA3AF;
    font-weight: 400;
}
.search-icon-input {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6B7280;
}

.filter-bar {
    margin-bottom: 25px;
    display: flex;
    justify-content: flex-start;
}

.custom-dropdown {
    position: relative;
    width: auto;
}

.custom-dropdown.blue-dropdown .dropdown-selected {
    background-color: #3A57D0;
    color: #FFFFFF;
    border: none;
    border-radius: 6px;
    padding: 10px 16px;
    font-size: 14px;
    font-weight: 500;
    min-width: 130px;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.custom-dropdown.blue-dropdown .dropdown-selected:hover {
    background-color: #2F4AB2;
}

.custom-dropdown.blue-dropdown .dropdown-selected span {
    margin-right: 8px;
}

.custom-dropdown.blue-dropdown .chevron-down path {
    stroke: #FFFFFF;
}
.custom-dropdown.open .chevron-down {
    transform: rotate(180deg);
}

.dropdown-options {
    list-style: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    min-width: 100%;
    background-color: #FFFFFF;
    border: 1px solid #D1D5DB;
    border-radius: 6px;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.2s ease-out, opacity 0.2s ease-out, visibility 0.2s ease-out;
    z-index: 100;
    opacity: 0;
    visibility: hidden;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
}
.custom-dropdown.open .dropdown-options {
    max-height: 150px;
    opacity: 1;
    visibility: visible;
}

.dropdown-options li {
    padding: 10px 16px;
    cursor: pointer;
    font-size: 14px;
    color: #374151;
    transition: background-color 0.2s ease, color 0.2s ease;
}

.dropdown-options li:hover {
    background-color: #3A57D0;
    color: #FFFFFF;
}

/* Table Styling - Penyesuaian untuk Baris Lebih Bundar */
.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 10px; /* Jarak vertikal antar baris "pill", bisa disesuaikan */
}

thead th {
    background-color: #FFFFFF;
    color: #4B5563;
    font-weight: 600;
    font-size: 13px;
    text-align: left;
    padding: 12px 16px;
    border-bottom: 2px solid #D1D5DB;
}

tbody td {
    background-color: #F3F4F6; /* Warna latar untuk sel */
    color: #374151;
    font-weight: 500;
    padding: 18px 16px; /* Padding vertikal bisa sedikit ditambah agar lebih tinggi */
    transition: background-color 0.2s ease, color 0.2s ease;
}

/* Styling untuk membuat bentuk pill pada baris LEBIH BUNDAR */
tbody td:first-child {
    border-top-left-radius: 50px;  /* Nilai besar untuk efek bundar penuh */
    border-bottom-left-radius: 50px;
}

tbody td:last-child {
    border-top-right-radius: 50px; /* Nilai besar untuk efek bundar penuh */
    border-bottom-right-radius: 50px;
}

tbody tr:hover td {
    background-color: #3A57D0;
    color: #FFFFFF;
    cursor: pointer;
}
</style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="logo-main">ASTRAtech</div>
                    <div class="logo-sub">POLITEKNIK MANUFAKTUR</div>
                </div>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="beranda_admin.php">Beranda</a></li>
                    <li><a href="aPenjadwalan">Penjadwalan</a></li>
                    <li class="active"><a href="#">Daftar Sidang</a></li>
                    <li><a href="aLogin.php" class="logout-link-nav">Keluar</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="main-header new-layout">
                <h1 class="page-main-title">Daftar Sidang</h1>
                <div class="profile-icon-wrapper">
                    <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="18" cy="18" r="18" fill="black"/>
                        <path d="M26 27.5V25.5C26 23.2909 24.2091 21.5 22 21.5H14C11.7909 21.5 10 23.2909 10 25.5V27.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18 17.5C20.2091 17.5 22 15.7091 22 13.5C22 11.2909 20.2091 9.5 18 9.5C15.7909 9.5 14 11.2909 14 13.5C14 15.7091 15.7909 17.5 18 17.5Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </header>

            <section class="content-area">
                <div class="section-controls-header">
                    <h2 class="section-title">Pengajuan Sidang</h2>
                    <div class="search-bar-wrapper">
                        <svg class="search-icon-input" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.33333 12.6667C10.2789 12.6667 12.6667 10.2789 12.6667 7.33333C12.6667 4.38781 10.2789 2 7.33333 2C4.38781 2 2 4.38781 2 7.33333C2 10.2789 4.38781 12.6667 7.33333 12.6667Z" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14.0006 14L11.1006 11.1" stroke="#6B7280" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input type="text" placeholder="Nayaka Ivanna">
                    </div>
                </div>

                <div class="filter-bar">
                    <div class="custom-dropdown blue-dropdown" id="customDropdown">
                        <div class="dropdown-selected" id="dropdownSelected">
                            <span>Sidang TA</span> <svg class="chevron-down white-chevron" width="12" height="12" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L5 5L9 1" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <ul class="dropdown-options" id="dropdownOptions">
                            <li data-value="sidang_ta">Sidang TA</li>
                            <li data-value="sidang_semester">Sidang Semester</li>
                        </ul>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th id="dynamicColumnHeader">Judul Sidang</th> <th>Pembimbing</th>
                            </tr>
                        </thead>
                        <tbody id="dataTableBody"> </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('customDropdown');
    const selectedDisplay = document.getElementById('dropdownSelected');
    const selectedDisplayText = selectedDisplay ? selectedDisplay.querySelector('span') : null;
    const optionsList = document.getElementById('dropdownOptions');

    // Data untuk tabel
    const dataSidang = {
        sidang_ta: {
            header: "Judul Sidang",
            rows: [
                { id: "001", nim: "0920240053", nama: "Nayaka Ivanna", kolom4: "Sistem Pengajuan Sidang", pembimbing: "Dr. Rida Indah F." },
                { id: "002", nim: "0920240053", nama: "Zahrah Imelda", kolom4: "Sistem Pengajuan Sidang", pembimbing: "Dr. Rida Indah F." },
                { id: "003", nim: "0920240053", nama: "Nur Widya Astuti", kolom4: "Sistem Pengajuan Sidang", pembimbing: "Dr. Rida Indah F." }
            ]
        },
        sidang_semester: {
            header: "Mata Kuliah",
            rows: [
                { id: "001", nim: "0920240053", nama: "Nayaka Ivanna", kolom4: "Basis Data 1", pembimbing: "Dr. Rida Indah F." },
                { id: "002", nim: "0920240053", nama: "Zahrah Imelda", kolom4: "Pemrograman 2", pembimbing: "Timotius Victory" },
                { id: "003", nim: "0920240053", nama: "Nur Widya Astuti", kolom4: "Sistem Operasi", pembimbing: "Suhendra" }
            ]
        }
    };

    const dynamicColumnHeader = document.getElementById('dynamicColumnHeader');
    const dataTableBody = document.getElementById('dataTableBody');

    function updateTableContent(dataType) {
        const tableConfig = dataSidang[dataType];
        if (!tableConfig || !dynamicColumnHeader || !dataTableBody) {
            console.error("Error: Elemen tabel atau konfigurasi data tidak ditemukan.");
            return;
        }

        // Update table header
        dynamicColumnHeader.textContent = tableConfig.header;

        // Clear existing table body content
        dataTableBody.innerHTML = '';

        // Populate table with new rows
        tableConfig.rows.forEach(rowData => {
            const row = dataTableBody.insertRow();
            row.insertCell().textContent = rowData.id;
            row.insertCell().textContent = rowData.nim;
            row.insertCell().textContent = rowData.nama;
            row.insertCell().textContent = rowData.kolom4; // Ini akan menjadi Judul Sidang atau Mata Kuliah
            row.insertCell().textContent = rowData.pembimbing;
        });
    }

    if (dropdown && selectedDisplay && optionsList && selectedDisplayText && dynamicColumnHeader && dataTableBody) {
        const options = optionsList.getElementsByTagName('li');

        // Toggle dropdown
        selectedDisplay.addEventListener('click', function(event) {
            event.stopPropagation();
            dropdown.classList.toggle('open');
        });

        // Handle option selection
        for (let option of options) {
            option.addEventListener('click', function() {
                const selectedValue = this.getAttribute('data-value');
                selectedDisplayText.textContent = this.textContent; // Update teks di span
                
                updateTableContent(selectedValue); // Panggil fungsi untuk update tabel

                dropdown.classList.remove('open');
            });
        }

        // Klik di luar dropdown untuk menutup
        document.addEventListener('click', function(event) {
            if (dropdown.classList.contains('open')) {
                dropdown.classList.remove('open');
            }
        });

        optionsList.addEventListener('click', function(event){
            event.stopPropagation();
        });

        // Muat data default saat halaman pertama kali dibuka (misalnya Sidang TA)
        updateTableContent('sidang_ta');
        selectedDisplayText.textContent = "Sidang TA"; // Pastikan teks dropdown juga sesuai

    } else {
        console.error("Error: Salah satu elemen dropdown atau tabel tidak ditemukan.");
    }
});
    </script>
</body>
</html>