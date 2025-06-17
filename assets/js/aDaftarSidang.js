// Menjalankan script setelah seluruh konten DOM selesai dimuat
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi variabel untuk paginasi, filter, dan data
            let currentPage = 1; // Halaman saat ini
            const rowsPerPage = 10; // Jumlah baris per halaman
            let activeRows = []; // Array untuk menyimpan baris yang aktif (sesuai filter/pencarian)
            let currentTypeFilter = 'All'; // Filter tipe sidang saat ini

            // Mengambil elemen-elemen DOM yang diperlukan
            const paginationControls = document.getElementById('pagination-controls');
            const allTableRows = Array.from(document.querySelectorAll('#adminSidangContent tr.isiTabel'));
            const searchInput = document.getElementById('searchInput');

            // Fungsi untuk menampilkan data pada halaman tertentu
            function displayPage(page) {
                currentPage = page;
                // Sembunyikan semua baris yang aktif terlebih dahulu
                activeRows.forEach(row => row.style.display = 'none');
                
                // Hitung indeks awal dan akhir untuk baris yang akan ditampilkan
                const startIndex = (page - 1) * rowsPerPage;
                const endIndex = startIndex + rowsPerPage;
                const paginatedRows = activeRows.slice(startIndex, endIndex);

                // Tampilkan baris untuk halaman saat ini
                paginatedRows.forEach(row => {
                    row.style.display = ''; // Menghapus 'display: none'
                });
                
                // Perbarui status tombol paginasi (misal: tombol mana yang aktif/nonaktif)
                updatePaginationButtons();
            }
            
            // Fungsi untuk membuat tombol-tombol paginasi
            function setupPagination() {
                paginationControls.innerHTML = ''; // Kosongkan kontrol paginasi sebelumnya
                const pageCount = Math.ceil(activeRows.length / rowsPerPage);
                
                // Jika hanya ada 1 halaman atau kurang, jangan tampilkan paginasi
                if (pageCount <= 1) return;

                // Buat tombol "Previous"
                const prevButton = document.createElement('li');
                prevButton.className = 'page-item';
                prevButton.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
                prevButton.addEventListener('click', (e) => {
                    e.preventDefault(); // Mencegah link default
                    if (currentPage > 1) displayPage(currentPage - 1);
                });
                paginationControls.appendChild(prevButton);

                // Buat tombol untuk setiap nomor halaman
                for (let i = 1; i <= pageCount; i++) {
                    const pageButton = document.createElement('li');
                    pageButton.className = 'page-item';
                    pageButton.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    pageButton.addEventListener('click', (e) => {
                        e.preventDefault();
                        displayPage(i);
                    });
                    paginationControls.appendChild(pageButton);
                }

                // Buat tombol "Next"
                const nextButton = document.createElement('li');
                nextButton.className = 'page-item';
                nextButton.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
                nextButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    const totalPages = Math.ceil(activeRows.length / rowsPerPage);
                    if (currentPage < totalPages) displayPage(currentPage + 1);
                });
                paginationControls.appendChild(nextButton);
                
                // Perbarui status tombol setelah dibuat
                updatePaginationButtons();
            }

            // Fungsi untuk memperbarui tampilan tombol paginasi (aktif, nonaktif)
            function updatePaginationButtons() {
                const pageCount = Math.ceil(activeRows.length / rowsPerPage);
                const pageItems = paginationControls.querySelectorAll('.page-item');
                if (pageItems.length === 0) return;

                pageItems.forEach((item, index) => {
                    item.classList.remove('active', 'disabled');
                    if (index === 0) { // Tombol "Previous"
                        if (currentPage === 1) item.classList.add('disabled');
                    } else if (index === pageItems.length - 1) { // Tombol "Next"
                        if (currentPage === pageCount) item.classList.add('disabled');
                    } else { // Tombol nomor halaman
                        if (index === currentPage) {
                            item.classList.add('active');
                        }
                    }
                });
            }
            
            // Fungsi utama untuk memfilter dan memperbarui tampilan tabel
            function updateTableDisplay() {
                const searchTerm = searchInput.value.toLowerCase();
                let filteredRows = allTableRows;

                // 1. Filter berdasarkan tipe sidang (jika bukan 'All')
                if (currentTypeFilter !== 'All') {
                    filteredRows = filteredRows.filter(row => row.dataset.type === currentTypeFilter);
                }

                // 2. Filter berdasarkan input pencarian
                if (searchTerm) {
                    filteredRows = filteredRows.filter(row => {
                        const rowText = row.textContent.toLowerCase();
                        return rowText.includes(searchTerm);
                    });
                }

                // Setelah difilter, simpan hasilnya ke activeRows
                activeRows = filteredRows;
                // Sembunyikan semua baris terlebih dahulu
                allTableRows.forEach(row => row.style.display = 'none');
                // Buat ulang paginasi berdasarkan data yang sudah difilter
                setupPagination();
                // Tampilkan halaman pertama dari data hasil filter
                displayPage(1);
            }

            // Fungsi untuk mengganti view (filter) tabel, dibuat global agar bisa dipanggil dari HTML
            window.switchAdminSidangView = function (viewType) {
                // Ambil elemen yang perlu diubah
                const dynamicHeader = document.getElementById("thDynamicHeader");
                const dropdownMenu = document.getElementById("dynamicDropdownMenu");
                const ddButton = document.getElementById("ddAdminSidangTypeButton");
                const dynamicMKHeader = document.querySelectorAll('[data-label="Judul/MK"], [data-label="Judul Sidang"], [data-label="Mata Kuliah"]');

                currentTypeFilter = viewType;

                // Kosongkan dan isi ulang menu dropdown sesuai pilihan saat ini
                dropdownMenu.innerHTML = '';
                let options = '';
                let mobileLabel = "Judul/Mata Kuliah";

                if (viewType === 'All') {
                    ddButton.textContent = "Semua";
                    dynamicHeader.textContent = "Judul/Mata Kuliah";
                    options = `<li><a class="dropdown-item" href="#" onclick="switchAdminSidangView('ta')">Sidang TA</a></li>
                               <li><a class="dropdown-item" href="#" onclick="switchAdminSidangView('semester')">Sidang Semester</a></li>`;
                } else if (viewType === 'ta') {
                    ddButton.textContent = "Sidang TA";
                    dynamicHeader.textContent = "Judul Sidang";
                    mobileLabel = "Judul Sidang";
                    options = `<li><a class="dropdown-item" href="#" onclick="switchAdminSidangView('All')">Semua</a></li>
                               <li><a class="dropdown-item" href="#" onclick="switchAdminSidangView('semester')">Sidang Semester</a></li>`;
                } else if (viewType === 'semester') {
                    ddButton.textContent = "Sidang Semester";
                    dynamicHeader.textContent = "Mata Kuliah";
                    mobileLabel = "Mata Kuliah";
                    options = `<li><a class="dropdown-item" href="#" onclick="switchAdminSidangView('All')">Semua</a></li>
                               <li><a class="dropdown-item" href="#" onclick="switchAdminSidangView('ta')">Sidang TA</a></li>`;
                }
                
                // Perbarui atribut data-label untuk tampilan mobile
                dynamicMKHeader.forEach(th => th.setAttribute('data-label', mobileLabel));
                dropdownMenu.insertAdjacentHTML('beforeend', options);
                
                // Panggil fungsi untuk memperbarui tampilan tabel sesuai filter baru
                updateTableDisplay();
            }
            
            // Event listener untuk item di sidebar agar bisa diklik
            const listItems = document.querySelectorAll(".NavSide__sidebar-item");
            listItems.forEach(item => {
                item.addEventListener('click', function (event) {
                    const link = this.querySelector('a');
                    // Jika link ada dan bukan link untuk modal, pindah halaman
                    if (link && !link.hasAttribute('data-bs-toggle')) {
                        window.location.href = link.href;
                    }
                });
            });
            
            // Event listener untuk klik pada baris tabel (mengarahkan ke halaman detail)
            allTableRows.forEach(row => {
                row.addEventListener('click', function (event) {
                    // Pastikan yang diklik adalah tombol detail
                    const detailButton = event.target.closest('.detail-btn');
                    if (detailButton) {
                        const sidangId = this.dataset.id;
                        const sidangType = this.dataset.type;
                        if (sidangId && sidangType) {
                            // Arahkan ke halaman detail yang sesuai (TA atau Semester)
                            if (sidangType === 'ta') {
                                window.location.href = `aDetailSidangTA.php?type=${sidangType}&id=${sidangId}`;
                            } else if (sidangType === 'semester') {
                                window.location.href = `aDetailSidangSem.php?type=${sidangType}&id=${sidangId}`;
                            }
                        }
                    }
                });
            });

            // Logika untuk menangani tampilan mobile (sidebar toggle & pemindahan ikon)
            const menuToggle = document.querySelector(".NavSide__toggle");
            const sidebar = document.getElementById("main-sidebar");
            const desktopIconsContainer = document.getElementById('desktop-icons-container');
            const mobileIconsContainer = document.getElementById('mobile-icons-container');

            if (desktopIconsContainer) {
                const headerIcons = desktopIconsContainer.querySelector('.header-icons');
                // Fungsi untuk memindahkan ikon antara topbar (mobile) dan header (desktop)
                function handleIconPlacement() {
                    if (window.innerWidth <= 992) {
                        // Jika layar kecil dan ikon belum ada di kontainer mobile, pindahkan
                        if (!mobileIconsContainer.contains(headerIcons)) mobileIconsContainer.appendChild(headerIcons);
                    } else {
                        // Jika layar besar dan ikon belum ada di kontainer desktop, pindahkan kembali
                        if (!desktopIconsContainer.contains(headerIcons)) desktopIconsContainer.appendChild(headerIcons);
                    }
                }
                if (menuToggle && sidebar) {
                    // Event listener untuk tombol hamburger menu
                    menuToggle.onclick = () => {
                        menuToggle.classList.toggle("NavSide__toggle--active");
                        sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                    };
                }
                // Panggil fungsi penempatan ikon saat halaman dimuat
                handleIconPlacement();
                // Panggil kembali saat ukuran window berubah
                window.addEventListener('resize', handleIconPlacement);
            }
            
            // Event listener untuk input pencarian, memanggil update setiap kali user mengetik
            searchInput.addEventListener('keyup', updateTableDisplay);
            
            // Panggil fungsi filter 'All' saat halaman pertama kali dimuat
            switchAdminSidangView('All');
        });