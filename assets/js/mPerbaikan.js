/**
 * Menjalankan semua skrip setelah seluruh konten halaman (DOM) selesai dimuat.
 */
document.addEventListener('DOMContentLoaded', function() {
    
    /**
     * Menginisialisasi fungsi untuk tombol buka/tutup sidebar pada tampilan mobile.
     * Fungsi ini akan berjalan di semua halaman yang memiliki sidebar.
     */
    function initSidebarToggle() {
        const menuToggle = document.querySelector(".NavSide__toggle");
        const sidebar = document.getElementById("main-sidebar");

        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', () => {
                menuToggle.classList.toggle("NavSide__toggle--active");
                sidebar.classList.toggle("NavSide__sidebar--active-mobile");
            });
        }
    }

    /**
     * Menginisialisasi semua fungsionalitas yang berhubungan dengan form upload revisi.
     * Fungsi ini hanya akan berjalan di halaman yang memiliki form dengan id 'revisionForm'.
     */
    function initRevisionFormHandler() {
        const revisionForm = document.getElementById('revisionForm');
        if (!revisionForm) return; // KELUAR JIKA TIDAK ADA FORM DI HALAMAN INI

        const fileInput = document.getElementById('fileInput');
        const submitBtn = document.getElementById('submitBtn') || document.getElementById('openConfirmModalBtn');
        const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');

        // ... (sisa logika form upload tetap sama) ...
        const initialState = document.getElementById('initial-state');
        const selectedState = document.getElementById('selected-state');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const uploadPromptText = document.getElementById('upload-prompt-text');

        const updateUploadUI = (file) => {
            const hasFile = file && file.name;
            if (initialState) initialState.classList.toggle('d-none', hasFile);
            if (selectedState) selectedState.classList.toggle('d-none', !hasFile);
            if (uploadPromptText) uploadPromptText.classList.toggle('d-none', hasFile);
            if (fileNameDisplay) fileNameDisplay.textContent = hasFile ? file.name : '';
            if (submitBtn) submitBtn.disabled = !hasFile;
        };

        if (fileInput) {
            updateUploadUI(null);
            fileInput.addEventListener('change', function() {
                updateUploadUI(this.files[0]);
            });
        }
        
        if (confirmSubmitBtn) {
             confirmSubmitBtn.addEventListener('click', function() {
                 if (submitBtn && !submitBtn.disabled) {
                    revisionForm.submit();
                 }
             });
        }
    }

    /**
     * Menginisialisasi fungsionalitas untuk komponen paginasi (halaman).
     * Fungsi ini hanya akan berjalan di halaman yang memiliki elemen dengan class 'pagination-container'.
     */
    function initPaginationHandler() {
        const paginationContainer = document.querySelector('.pagination-container');
        if (!paginationContainer) return; // KELUAR JIKA TIDAK ADA PAGINASI DI HALAMAN INI

        const currentPage = parseInt(paginationContainer.dataset.currentPage, 10);
        const totalPages = parseInt(paginationContainer.dataset.totalPages, 10);

        if (totalPages <= 1) {
            paginationContainer.style.display = 'none';
            return;
        }

        const pageItems = paginationContainer.querySelectorAll('.page-item');
        const pageLinks = paginationContainer.querySelectorAll('.page-link');
        
        const prevItem = pageItems[0];
        const nextItem = pageItems[pageItems.length - 1];

        // Terapkan class 'disabled'
        if (currentPage === 1) {
            prevItem.classList.add('disabled');
        }
        if (currentPage === totalPages) {
            nextItem.classList.add('disabled');
        }

        // Terapkan class 'active'
        pageLinks.forEach(link => {
            if (parseInt(link.textContent, 10) === currentPage) {
                link.parentElement.classList.add('active');
            }
        });
    }

    // Panggil semua fungsi inisialisasi
    initSidebarToggle();
    initRevisionFormHandler();
    initPaginationHandler(); // <-- Sekarang fungsi paginasi sudah ditambahkan

});