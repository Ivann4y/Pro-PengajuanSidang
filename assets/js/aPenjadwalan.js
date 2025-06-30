    let taModalInstance, semModalInstance;
    let pengujiCount = 1;

    document.addEventListener("DOMContentLoaded", function() {
        // --- PAGINATION SCRIPT (FROM aDaftarSidang.php) ---
        let currentPage = 1;
        const rowsPerPage = 10;
        const tableBody = document.getElementById('adminSidangContent');
        const activeRows = Array.from(tableBody.querySelectorAll('tr.isiTabel'));
        const paginationControls = document.getElementById('pagination-controls');

        function displayPage(page) {
            currentPage = page;
            activeRows.forEach(row => row.style.display = 'none');
            const startIndex = (page - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;
            const paginatedRows = activeRows.slice(startIndex, endIndex);
            paginatedRows.forEach(row => { row.style.display = ''; });
            updatePaginationButtons();
        }

        function setupPagination() {
            paginationControls.innerHTML = '';
            const pageCount = Math.ceil(activeRows.length / rowsPerPage);
            if (pageCount <= 1) return;

            const prevButton = document.createElement('li');
            prevButton.className = 'page-item';
            prevButton.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">«</span></a>`;
            prevButton.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage > 1) displayPage(currentPage - 1);
            });
            paginationControls.appendChild(prevButton);

            for (let i = 1; i <= pageCount; i++) {
                const pageButton = document.createElement('li');
                pageButton.className = 'page-item';
                pageButton.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                pageButton.addEventListener('click', (e) => { e.preventDefault(); displayPage(i); });
                paginationControls.appendChild(pageButton);
            }

            const nextButton = document.createElement('li');
            nextButton.className = 'page-item';
            nextButton.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">»</span></a>`;
            nextButton.addEventListener('click', (e) => {
                e.preventDefault();
                const totalPages = Math.ceil(activeRows.length / rowsPerPage);
                if (currentPage < totalPages) displayPage(currentPage + 1);
            });
            paginationControls.appendChild(nextButton);
            updatePaginationButtons();
        }

        function updatePaginationButtons() {
            const pageCount = Math.ceil(activeRows.length / rowsPerPage);
            const pageItems = paginationControls.querySelectorAll('.page-item');
            if (pageItems.length === 0) return;
            pageItems.forEach((item, index) => {
                item.classList.remove('active', 'disabled');
                if (index === 0) { if (currentPage === 1) item.classList.add('disabled'); }
                else if (index === pageItems.length - 1) { if (currentPage === pageCount) item.classList.add('disabled'); }
                else { if (index === currentPage) item.classList.add('active'); }
            });
        }
        
        // Initialize Pagination
        if(activeRows.length > 0) {
            setupPagination();
            displayPage(1);
        }

        // --- SEARCH SCRIPT ---
        const searchInput = document.querySelector('.search-input-group .form-control');
        const noDataRow = document.querySelector('.no-results-row');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchText = this.value.toLowerCase().trim();
                let visibleRows = [];

                activeRows.forEach(row => {
                    const namaCell = row.cells[2];
                    const namaText = namaCell.textContent.toLowerCase();
                    if (namaText.includes(searchText)) {
                        visibleRows.push(row);
                    }
                });

                // Re-paginate the filtered results
                currentPage = 1;
                activeRows.forEach(row => row.style.display = 'none'); // Hide all original rows first
                
                const startIndex = (currentPage - 1) * rowsPerPage;
                const endIndex = startIndex + rowsPerPage;
                const paginatedVisibleRows = visibleRows.slice(startIndex, endIndex);

                paginatedVisibleRows.forEach(row => {
                    row.style.display = '';
                });

                // Update pagination controls for the new filtered set of rows
                const pageCount = Math.ceil(visibleRows.length / rowsPerPage);
                updatePaginationForSearch(pageCount, visibleRows);

                // Show/hide no results message
                if(noDataRow) noDataRow.style.display = visibleRows.length === 0 ? '' : 'none';
            });
        }

        function updatePaginationForSearch(pageCount, currentVisibleRows) {
            paginationControls.innerHTML = '';
            if (pageCount <= 1) return;

            const prevButton = document.createElement('li');
            prevButton.className = 'page-item';
            prevButton.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">«</span></a>`;
            prevButton.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage > 1) displaySearchedPage(currentPage - 1, currentVisibleRows);
            });
            paginationControls.appendChild(prevButton);

            for (let i = 1; i <= pageCount; i++) {
                const pageButton = document.createElement('li');
                pageButton.className = 'page-item';
                pageButton.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                pageButton.addEventListener('click', (e) => { e.preventDefault(); displaySearchedPage(i, currentVisibleRows); });
                paginationControls.appendChild(pageButton);
            }

            const nextButton = document.createElement('li');
            nextButton.className = 'page-item';
            nextButton.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">»</span></a>`;
            nextButton.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage < pageCount) displaySearchedPage(currentPage + 1, currentVisibleRows);
            });
            paginationControls.appendChild(nextButton);

            displaySearchedPage(1, currentVisibleRows);
        }

        function displaySearchedPage(page, searchedRows) {
            currentPage = page;
            activeRows.forEach(r => r.style.display = 'none');
            const startIndex = (page - 1) * rowsPerPage;
            const endIndex = startIndex + rowsPerPage;
            const paginatedRows = searchedRows.slice(startIndex, endIndex);
            paginatedRows.forEach(row => { row.style.display = ''; });
            updatePaginationButtons(); // This function can be reused
        }
        
        // --- MOBILE MENU AND ICONS SCRIPT (from aDaftarSidang) ---
        const menuToggle = document.querySelector(".NavSide__toggle");
        const sidebar = document.getElementById("main-sidebar");
        const desktopIconsContainer = document.getElementById('desktop-icons-container');
        const mobileIconsContainer = document.getElementById('mobile-icons-container');
        const headerIcons = desktopIconsContainer.querySelector('.header-icons');

        function handleIconPlacement() {
            if (window.innerWidth <= 992) {
                if (!mobileIconsContainer.contains(headerIcons)) mobileIconsContainer.appendChild(headerIcons);
            } else {
                if (!desktopIconsContainer.contains(headerIcons)) desktopIconsContainer.appendChild(headerIcons);
            }
        }
        if (menuToggle && sidebar) {
            menuToggle.onclick = () => {
                menuToggle.classList.toggle("NavSide__toggle--active");
                sidebar.classList.toggle("NavSide__sidebar--active-mobile");
            };
        }
        handleIconPlacement();
        window.addEventListener('resize', handleIconPlacement);

        // --- ORIGINAL MODAL AND FORM SCRIPT ---
        const taModalEl = document.getElementById('penjadwalanSidangTAModal');
        if (taModalEl) taModalInstance = new bootstrap.Modal(taModalEl);
        
        const semModalEl = document.getElementById('penjadwalanSidangSemModal');
        if (semModalEl) semModalInstance = new bootstrap.Modal(semModalEl);

        const formTA = document.getElementById('formDalamModal-ta');
        if(formTA) formTA.addEventListener('submit', handleFormSubmit);

        const formSem = document.getElementById('formDalamModal-sem');
        if(formSem) formSem.addEventListener('submit', handleFormSubmit);
    });
    
    // Function to open modal (unchanged)
    function openJadwalModal(element) {
        const tipeSidang = element.dataset.tipeSidang;
        if (tipeSidang === 'TA') {
            resetAndPopulateTAModal(element);
            taModalInstance.show();
        } else if (tipeSidang === 'Semester') {
            populateSemModal(element);
            semModalInstance.show();
        }
    }
    // All other helper functions (resetAndPopulateTAModal, populateSemModal, etc.) remain the same
    function resetAndPopulateTAModal(el) {
        const wrapper = document.getElementById('penguji-wrapper-ta');
        wrapper.innerHTML = `
            <div class="form-group" id="penguji-form-ta-1">
                <label for="modal_penguji-ta-1">Penguji 1</label>
                <div class="input-with-buttons">
                    <input type="text" id="modal_penguji-ta-1" name="penguji_nama[]" placeholder="Nama Penguji 1" />
                    <div class="bobot-nilai-input-group">
                        <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_penguji-ta-1')">-</button>
                        <input type="number" id="modal_qty_penguji-ta-1" name="penguji_bobot[]" class="bobot-input-new" value="0" min="0" />
                        <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_qty_penguji-ta-1')">+</button>
                    </div>
                    <div class="form-toggle-buttons">
                        <button type="button" onclick="addPenguji()">+</button>
                        <button type="button" onclick="removePenguji()">-</button>
                    </div>
                </div>
            </div>`;
        pengujiCount = 1;
        updateToggleButtonsVisibility();
        document.getElementById('modal_nim-ta').value = el.dataset.nim || '';
        document.getElementById('modal_nim-ta').dataset.id = el.dataset.id || '';
        document.getElementById('modal_judul_sidang-ta').value = el.dataset.judul || '';
        document.getElementById('modal_pembimbing-ta').value = el.dataset.pembimbing || '';
        document.getElementById('modal_prodi-ta').value = el.dataset.prodi || '';
        document.getElementById('modal_ruangan-ta').value = '';
        document.getElementById('modal_tanggal-ta').value = '';
        document.getElementById('modal_jam_awal-ta').value = '';
        document.getElementById('modal_jam_akhir-ta').value = '';
        document.getElementById('form-error-ta').textContent = '';
    }
    function populateSemModal(el) {
        document.getElementById('modal_nim-sem').value = el.dataset.nim || '';
        document.getElementById('modal_nim-sem').dataset.id = el.dataset.id || '';
        document.getElementById('modal_matkul-sem').value = el.dataset.judul || '';
        document.getElementById('modal_prodi-sem').value = el.dataset.prodi || '';
        const pengampu = JSON.parse(el.dataset.pengampu || '[]');
        document.getElementById('modal_pengampu-sem-1').value = pengampu[0] || '';
        document.getElementById('modal_pengampu-sem-2').value = pengampu[1] || '';
        document.getElementById('modal_ruangan-sem').value = '';
        document.getElementById('modal_tanggal-sem').value = '';
        document.getElementById('modal_jam_awal-sem').value = '';
        document.getElementById('modal_jam_akhir-sem').value = '';
        document.getElementById('form-error-sem').textContent = '';
    }
    function addPenguji() {
        pengujiCount++;
        const wrapper = document.getElementById('penguji-wrapper-ta');
        const div = document.createElement('div');
        div.className = 'form-group';
        div.id = `penguji-form-ta-${pengujiCount}`;
        div.innerHTML = `
            <label for="modal_penguji-ta-${pengujiCount}">Penguji ${pengujiCount}</label>
            <div class="input-with-buttons">
                <input type="text" id="modal_penguji-ta-${pengujiCount}" name="penguji_nama[]" placeholder="Nama Penguji ${pengujiCount}" />
                <div class="bobot-nilai-input-group">
                    <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_penguji-ta-${pengujiCount}')">-</button>
                    <input type="number" id="modal_qty_penguji-ta-${pengujiCount}" name="penguji_bobot[]" class="bobot-input-new" value="0" min="0" />
                    <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_qty_penguji-ta-${pengujiCount}')">+</button>
                </div>
                <div class="form-toggle-buttons">
                    <button type="button" onclick="addPenguji()">+</button>
                    <button type="button" onclick="removePenguji()">-</button>
                </div>
            </div>`;
        wrapper.appendChild(div);
        updateToggleButtonsVisibility();
    }
    function removePenguji() {
        if (pengujiCount > 1) {
            const lastForm = document.getElementById(`penguji-form-ta-${pengujiCount}`);
            if (lastForm) { lastForm.remove(); pengujiCount--; }
        }
        updateToggleButtonsVisibility();
    }
    function updateToggleButtonsVisibility() {
        const toggleButtons = document.querySelectorAll('#penguji-wrapper-ta .form-toggle-buttons');
        toggleButtons.forEach((btnGroup, index) => {
            if (index === toggleButtons.length - 1) {
                btnGroup.style.display = 'inline-flex';
                const removeBtn = btnGroup.querySelector('button[onclick="removePenguji()"]');
                if (removeBtn) { removeBtn.style.display = (pengujiCount <= 1) ? 'none' : 'block'; }
            } else { btnGroup.style.display = 'none'; }
        });
    }
    function incrementValue(inputId) { const input = document.getElementById(inputId); if (input) input.value = parseInt(input.value, 10) + 1; }
    function decrementValue(inputId) {
        const input = document.getElementById(inputId);
        if (input) { let val = parseInt(input.value, 10); if (val > (input.min || 0)) { input.value = val - 1; } }
    }
    function handleFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const modalType = form.id.includes('-ta') ? 'TA' : 'Sem';
        if (validateForm(modalType)) {
            const modalInstance = modalType === 'TA' ? taModalInstance : semModalInstance;
            modalInstance.hide();
            Swal.fire({
                title: 'Berhasil', text: 'Jadwal Berhasil Dibuat.', imageUrl: '../../assets/img/centang.svg',
                imageWidth: 120, imageHeight: 120, imageAlt: 'Success checkmark',
                confirmButtonText: 'OK', confirmButtonColor: '#4336F0'
            }).then(() => { location.reload(); });
        }
    }
    function validateForm(modalType) {
        const suffix = modalType === 'TA' ? '-ta' : '-sem';
        const errorBox = document.getElementById(`form-error${suffix}`);
        errorBox.textContent = ''; let errorMessage = '';
        const dosenInputs = document.querySelectorAll(`input[name="${modalType === 'TA' ? 'penguji' : 'pengampu'}_nama[]"]`);
        for (let i = 0; i < dosenInputs.length; i++) { if (dosenInputs[i].value.trim() === '') { errorMessage = `Nama ${modalType === 'TA' ? 'Penguji' : 'Pengampu'} ${i + 1} harus diisi!`; break; } }
        if (errorMessage) { errorBox.textContent = errorMessage; return false; }
        const ruangan = document.getElementById(`modal_ruangan${suffix}`).value.trim();
        if (ruangan === '') { errorBox.textContent = 'Ruangan harus diisi!'; return false; }
        const tanggal = document.getElementById(`modal_tanggal${suffix}`).value;
        if (tanggal === '') { errorBox.textContent = 'Tanggal harus dipilih!'; return false; }
        const today = new Date(); const selectedDate = new Date(tanggal); today.setHours(0, 0, 0, 0); 
        if (selectedDate < today) { errorBox.textContent = 'Tanggal tidak boleh kurang dari tanggal hari ini!'; return false; }
        const jamAwal = document.getElementById(`modal_jam_awal${suffix}`).value;
        const jamAkhir = document.getElementById(`modal_jam_akhir${suffix}`).value;
        if (jamAwal === '' || jamAkhir === '') { errorBox.textContent = 'Jam awal dan jam akhir harus diisi!'; return false; }
        if (jamAkhir <= jamAwal) { errorBox.textContent = 'Jam akhir harus setelah jam awal!'; return false; }
        return true;
    }