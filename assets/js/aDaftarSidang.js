// Jalankan kode setelah seluruh Document Object Model (DOM) dimuat sepenuhnya.
// Ini memastikan semua elemen HTML sudah ada sebelum JavaScript mencoba mengaksesnya.
document.addEventListener('DOMContentLoaded', function () {
    // --- FUNGSI 1: KONTROL SIDEBAR DAN IKON RESPONSIVE ---

    // 1.1. Ambil elemen-elemen yang diperlukan dari DOM.
    // Tombol untuk membuka/menutup sidebar di tampilan mobile.
    const menuToggle = document.querySelector(".NavSide__toggle");
    // Elemen sidebar utama.
    const sidebar = document.getElementById("main-sidebar");
    // Kontainer untuk ikon di header pada tampilan desktop.
    const desktopIconsContainer = document.getElementById('desktop-icons-container');
    // Kontainer untuk ikon di header pada tampilan mobile (di dalam topbar).
    const mobileIconsContainer = document.getElementById('mobile-icons-container');

    // Pastikan kontainer ikon desktop ada untuk mencegah error.
    if (desktopIconsContainer) {
        // Ambil elemen grup ikon header dari dalam kontainer desktop.
        const headerIcons = desktopIconsContainer.querySelector('.header-icons');

        // 1.2. Definisikan fungsi untuk mengatur penempatan ikon.
        // Fungsi ini akan memindahkan ikon antara kontainer desktop dan mobile
        // tergantung pada lebar layar.
        function handleIconPlacement() {
            // Cek jika lebar jendela browser kurang dari atau sama dengan 992px (breakpoint untuk mobile).
            if (window.innerWidth <= 992) {
                // Tampilan Mobile:
                // Cek jika kontainer mobile ada DAN belum berisi ikon-ikon header.
                if (mobileIconsContainer && !mobileIconsContainer.contains(headerIcons)) {
                    // Pindahkan elemen ikon ke dalam kontainer mobile.
                    mobileIconsContainer.appendChild(headerIcons);
                }
            } else {
                // Tampilan Desktop:
                // Cek jika kontainer desktop belum berisi ikon-ikon header.
                if (!desktopIconsContainer.contains(headerIcons)) {
                    // Kembalikan elemen ikon ke dalam kontainer desktop aslinya.
                    desktopIconsContainer.appendChild(headerIcons);
                }
            }
        }

        // 1.3. Atur event listener untuk tombol toggle sidebar.
        // Pastikan elemen toggle dan sidebar ada.
        if (menuToggle && sidebar) {
            // Event 'onclick' akan dijalankan saat tombol di-klik.
            menuToggle.onclick = () => {
                // Toggle (tambah/hapus) kelas '...--active' pada tombol.
                menuToggle.classList.toggle("NavSide__toggle--active");
                // Toggle (tambah/hapus) kelas '...--active-mobile' pada sidebar untuk menampilkan/menyembunyikannya.
                sidebar.classList.toggle("NavSide__sidebar--active-mobile");
            };
        }

        // 1.4. Jalankan fungsi dan atur event listener untuk window resize.
        // Jalankan fungsi penempatan ikon saat halaman pertama kali dimuat.
        handleIconPlacement();
        // Tambahkan event listener yang akan menjalankan 'handleIconPlacement' setiap kali ukuran jendela diubah.
        window.addEventListener('resize', handleIconPlacement);
    }
});

// Jalankan kode search filter tabel setelah DOM siap.
document.addEventListener('DOMContentLoaded', function () {
    // --- FUNGSI 2: FILTER PENCARIAN TABEL ---

    // 2.1. Ambil elemen input pencarian dan semua baris data di tabel.
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('.table-admin-custom tbody tr.isiTabel');

    // 2.2. Tambahkan event listener ke input pencarian.
    // Event 'input' akan dijalankan setiap kali pengguna mengetik atau mengubah isi input.
    searchInput.addEventListener('input', function() {
        // Ambil kata kunci dari input dan ubah menjadi huruf kecil untuk pencarian case-insensitive.
        const keyword = searchInput.value.toLowerCase();

        // 2.3. Loop melalui setiap baris tabel.
        tableRows.forEach(row => {
            // Ambil seluruh teks dari satu baris dan ubah menjadi huruf kecil.
            const rowText = row.innerText.toLowerCase();
            // Cek apakah teks baris mengandung kata kunci.
            // Jika cocok, tampilkan baris (style.display = '').
            // Jika tidak cocok, sembunyikan baris (style.display = 'none').
            row.style.display = rowText.includes(keyword) ? '' : 'none';
        });
    });
});