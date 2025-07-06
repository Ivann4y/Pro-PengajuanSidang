// ==========================================================================
// BAGIAN 1: FUNGSI GLOBAL & INSTANCE MODAL
// ==========================================================================

let taModalInstance, semModalInstance;
let pengujiCount = 0;

/**
 * Fungsi utama untuk membuka modal penjadwalan.
 * @param {HTMLElement} rowElement - Elemen <tr> dari baris yang diklik.
 */
function openJadwalModal(rowElement) {
    const tipeSidang = rowElement.dataset.tipeSidang;

    if (tipeSidang === 'Tugas Akhir') { // Disesuaikan dengan nilai dari DB
        if (!taModalInstance) {
            taModalInstance = new bootstrap.Modal(document.getElementById('penjadwalanSidangTAModal'));
        }
        resetAndPopulateTAModal(rowElement);
        taModalInstance.show();
    } else if (tipeSidang === 'Semester') {
        if (!semModalInstance) {
            semModalInstance = new bootstrap.Modal(document.getElementById('penjadwalanSidangSemModal'));
        }
        populateSemModal(rowElement);
        semModalInstance.show();
    }
}

/**
 * Mengisi data ke modal Sidang TA dan mereset field.
 * @param {HTMLElement} el - Elemen <tr> dari baris yang diklik.
 */
function resetAndPopulateTAModal(el) {
    const formTA = document.getElementById('formDalamModal-ta');
    formTA.reset();
    document.getElementById('modal_id_sidang-ta').value = el.dataset.id || '';

    document.getElementById('modal_nim-ta').value = el.dataset.kelompok || '';
    document.getElementById('modal_judul_sidang-ta').value = el.dataset.judul || '';
    document.getElementById('modal_pembimbing-ta').value = el.dataset.pembimbing || '';
    document.getElementById('modal_prodi-ta').value = el.dataset.prodi || '';
    document.getElementById('form-error-ta').textContent = '';

    const wrapper = document.getElementById('penguji-wrapper-ta');
    wrapper.innerHTML = '';
    pengujiCount = 0;

    addPenguji();
}

/**
 * Mengisi data ke modal Sidang Semester.
 * @param {HTMLElement} el - Elemen <tr> dari baris yang diklik.
 */
function populateSemModal(el) {
    const formSem = document.getElementById('formDalamModal-sem');
    formSem.reset();
    document.getElementById('modal_id_sidang-sem').value = el.dataset.id || '';

    document.getElementById('modal_nim-sem').value = el.dataset.kelompok || '';
    document.getElementById('modal_matkul-sem').value = el.dataset.judul || '';
    document.getElementById('modal_prodi-sem').value = el.dataset.prodi || '';
    document.getElementById('form-error-sem').textContent = '';

    const pengampuWrapper = document.getElementById('pengampu-wrapper-sem');
    pengampuWrapper.innerHTML = '';
    try {
        const pengampuList = JSON.parse(el.dataset.pengampu || '[]');
        if (pengampuList.length > 0) {
            pengampuList.forEach((nama, index) => {
                if (!nama) return;
                const pengampuIndex = index + 1;
                const pengampuHtml = `
                    <div class="form-group" id="pengampu-form-sem-${pengampuIndex}">
                        <label for="modal_pengampu-sem-${pengampuIndex}">Pengampu ${pengampuIndex}</label>
                        <div class="input-with-buttons">
                            <input type="text" id="modal_pengampu-sem-${pengampuIndex}" name="pengampu_nama[]" value="${nama}" readonly />
                            <div class="bobot-nilai-input-group">
                                <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_pengampu-sem-${pengampuIndex}')">-</button>
                                <input type="number" id="modal_qty_pengampu-sem-${pengampuIndex}" name="pengampu_bobot[]" class="bobot-input-new" value="0" min="0" />
                                <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_qty_pengampu-sem-${pengampuIndex}')">+</button>
                            </div>
                        </div>
                    </div>`;
                pengampuWrapper.insertAdjacentHTML('beforeend', pengampuHtml);
            });
        }
    } catch (e) {
        console.error("Gagal memproses data pengampu:", e);
    }
}

/**
 * Menambah field input penguji baru.
 */
function addPenguji() {
    pengujiCount++;
    const wrapper = document.getElementById('penguji-wrapper-ta');
    const div = document.createElement('div');
    div.className = 'form-group';
    div.id = `penguji-form-ta-${pengujiCount}`;
    
    div.innerHTML = `
        <label for="modal_penguji-ta-${pengujiCount}">Penguji ${pengujiCount}</label>
        <div class="input-with-buttons">
            <div class="autocomplete-container">
                <input type="text"
                       id="modal_penguji-ta-${pengujiCount}"
                       name="penguji_nama[]"
                       placeholder="Ketik nama dosen penguji"
                       oninput="searchPenguji(this, ${pengujiCount})"
                       autocomplete="off">
                <div class="autocomplete-dropdown" id="autocomplete_penguji_${pengujiCount}"></div>
            </div>
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

/**
 * Menghapus field input penguji terakhir.
 */
function removePenguji() {
    if (pengujiCount > 1) {
        document.getElementById(`penguji-form-ta-${pengujiCount}`).remove();
        pengujiCount--;
        updateToggleButtonsVisibility();
    }
}

/**
 * Mengatur visibilitas tombol tambah/kurang penguji.
 */
function updateToggleButtonsVisibility() {
    const allToggleButtons = document.querySelectorAll('#penguji-wrapper-ta .form-toggle-buttons');
    allToggleButtons.forEach(group => group.style.display = 'none');

    if (allToggleButtons.length > 0) {
        const lastGroup = allToggleButtons[allToggleButtons.length - 1];
        lastGroup.style.display = 'inline-flex';

        const removeButton = lastGroup.querySelector('button[onclick="removePenguji()"]');
        if (removeButton) {
            removeButton.style.display = (pengujiCount > 1) ? 'block' : 'none';
        }
    }
}

/**
 * Mencari dosen untuk dropdown autocomplete.
 */
function searchPenguji(inputElement, index) {
    const query = inputElement.value.toLowerCase().trim();
    const dropdown = document.getElementById(`autocomplete_penguji_${index}`);

    if (query.length < 1) {
        dropdown.style.display = 'none';
        return;
    }

    const filteredDosen = dosenData.filter(dosen =>
        dosen.nama.toLowerCase().includes(query)
    );

    dropdown.innerHTML = '';
    if (filteredDosen.length > 0) {
        filteredDosen.forEach(dosen => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            item.textContent = dosen.nama;
            item.onclick = () => selectPenguji(dosen.nama, index);
            dropdown.appendChild(item);
        });
    } else {
        dropdown.innerHTML = '<div class="autocomplete-item no-results">Dosen tidak ditemukan</div>';
    }
    dropdown.style.display = 'block';
}

/**
 * Memilih dosen dari dropdown dan mengisi input.
 */
function selectPenguji(namaDosen, index) {
    document.getElementById(`modal_penguji-ta-${index}`).value = namaDosen;
    document.getElementById(`autocomplete_penguji_${index}`).style.display = 'none';
}

/**
 * Menambah nilai pada input bobot.
 */
function incrementValue(inputId) {
    const input = document.getElementById(inputId);
    if (input) input.value = parseInt(input.value, 10) + 1;
}

/**
 * Mengurangi nilai pada input bobot.
 */
function decrementValue(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        let val = parseInt(input.value, 10);
        if (val > (parseInt(input.min, 10) || 0)) {
            input.value = val - 1;
        }
    }
}

/**
 * Menangani pengiriman data dari form modal.
 */
function handleFormSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const modalType = form.id.includes('-ta') ? 'TA' : 'Semester';
    const errorBox = document.getElementById(`form-error-${modalType.toLowerCase()}`);

    if (!validateForm(modalType)) return;

    const submitButton = form.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...';
    errorBox.textContent = '';

    const formData = new FormData(form);

    fetch('createPenjadwalan.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const modalToHide = modalType === 'TA' ? taModalInstance : semModalInstance;
            if (modalToHide) modalToHide.hide();
            
            Swal.fire({
                title: 'Berhasil!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => location.reload());

        } else {
            errorBox.textContent = data.message || 'Terjadi kesalahan.';
            Swal.fire({
                title: 'Gagal!',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'Coba Lagi'
            });
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        errorBox.textContent = 'Tidak dapat terhubung ke server.';
        Swal.fire({
            title: 'Koneksi Gagal',
            text: 'Tidak dapat mengirim data ke server.',
            icon: 'error',
            confirmButtonText: 'Tutup'
        });
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Buat Penjadwalan';
    });
}

/**
 * Validasi form sebelum dikirim.
 */
function validateForm(modalType) {
    const suffix = modalType === 'TA' ? '-ta' : '-sem';
    const errorBox = document.getElementById(`form-error${suffix}`);
    errorBox.textContent = '';
    
    const fieldsToValidate = [
        { id: `modal_ruangan${suffix}`, message: 'Ruangan harus diisi.' },
        { id: `modal_tanggal${suffix}`, message: 'Tanggal harus dipilih.' },
        { id: `modal_jam_awal${suffix}`, message: 'Jam awal harus diisi.' },
        { id: `modal_jam_akhir${suffix}`, message: 'Jam akhir harus diisi.' }
    ];

    for (const field of fieldsToValidate) {
        if (document.getElementById(field.id).value.trim() === '') {
            errorBox.textContent = field.message;
            return false;
        }
    }
    
    const jamAwal = document.getElementById(`modal_jam_awal${suffix}`).value;
    const jamAkhir = document.getElementById(`modal_jam_akhir${suffix}`).value;
    if (jamAkhir <= jamAwal) {
        errorBox.textContent = 'Jam akhir harus setelah jam awal.';
        return false;
    }
    
    if (modalType === 'TA') {
        const pengujiInputs = document.querySelectorAll('input[name="penguji_nama[]"]');
        const namaPengujiList = [];

        for (let i = 0; i < pengujiInputs.length; i++) {
            const input = pengujiInputs[i];
            const nama = input.value.trim();

            if (nama === '') {
                errorBox.textContent = `Nama Penguji ${i + 1} harus diisi.`;
                return false;
            }
            
            const isDosenValid = dosenData.some(dosen => dosen.nama === nama);
            if (!isDosenValid) {
                errorBox.textContent = `Nama dosen '${nama}' tidak valid. Harap pilih dari daftar.`;
                return false;
            }
            namaPengujiList.push(nama);
        }
        
        const uniqueNama = new Set(namaPengujiList);
        if (uniqueNama.size < namaPengujiList.length) {
            errorBox.textContent = 'Tidak boleh ada nama dosen penguji yang sama.';
            return false;
        }
    }
    return true;
}

// ==========================================================================
// BAGIAN 2: SCRIPT YANG BERJALAN SETELAH HALAMAN SIAP (DOMContentLoaded)
// ==========================================================================
document.addEventListener("DOMContentLoaded", function () {
    
    // ==========================================================
    // == BAGIAN BARU: LOGIKA UNTUK RESPONSIVE LAYOUT ==
    // ==========================================================
    const sidebar = document.getElementById('main-sidebar');
    const toggleButton = document.querySelector('.NavSide__toggle');
    const desktopIconsContainer = document.getElementById('desktop-icons-container');
    const topbar = document.querySelector('.NavSide__topbar');
    const headerIcons = desktopIconsContainer ? desktopIconsContainer.querySelector('.header-icons') : null;

    // 1. Logika untuk Toggle Sidebar
    if (toggleButton && sidebar) {
        toggleButton.addEventListener('click', function() {
            sidebar.classList.toggle('NavSide__sidebar--active-mobile');
            this.classList.toggle('NavSide__toggle--active');
        });
    }
    
    // 2. Logika untuk Memindahkan Ikon
    if (topbar && headerIcons) {
        function handleResponsiveIcons() {
            if (window.innerWidth <= 992) {
                if (!topbar.contains(headerIcons)) {
                    topbar.appendChild(headerIcons);
                }
            } else {
                if (desktopIconsContainer && !desktopIconsContainer.contains(headerIcons)) {
                    desktopIconsContainer.appendChild(headerIcons);
                }
            }
        }
        handleResponsiveIcons();
        window.addEventListener('resize', handleResponsiveIcons);
    }
    
    // --- (KODE ASLI ANDA MULAI DARI SINI) ---

    // --- PASANG EVENT LISTENER KE FORM MODAL ---
    document.getElementById('formDalamModal-ta')?.addEventListener('submit', handleFormSubmit);
    document.getElementById('formDalamModal-sem')?.addEventListener('submit', handleFormSubmit);

    // --- MENUTUP DROPDOWN SAAT KLIK DI LUAR ---
    document.addEventListener('click', function(event) {
        const allDropdowns = document.querySelectorAll('.autocomplete-dropdown');
        const clickedInsideContainer = event.target.closest('.autocomplete-container');
        
        // Sembunyikan semua dropdown KECUALI yang sedang aktif
        allDropdowns.forEach(dropdown => {
            const container = dropdown.closest('.autocomplete-container');
            if (container !== clickedInsideContainer) {
                dropdown.style.display = 'none';
            }
        });
    });

    // --- LOGIKA PENCARIAN DAN PAGINASI TABEL UTAMA ---
    const searchInput = document.querySelector('.search-input-group .form-control');
    const tableBody = document.getElementById('adminSidangContent');
    const paginationControls = document.getElementById('pagination-controls');
    const noDataRow = document.querySelector('.no-results-row');
    const rowsPerPage = 10;
    let allRows = []; // Akan diisi dengan baris tabel yang ada

    function initializeTableLogic() {
        allRows = Array.from(tableBody.querySelectorAll('tr.isiTabel'));
        if (allRows.length > 0) {
            renderTable();
        } else if(noDataRow) {
            noDataRow.style.display = ''; // Tampilkan jika tidak ada data sama sekali
            if(paginationControls) paginationControls.innerHTML = ''; // Kosongkan paginasi
        }
    }

    function renderTable() {
        const searchText = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const filteredRows = allRows.filter(row => {
            // Mengambil dari data-attributes untuk pencarian yang lebih akurat
            const namaList = (row.dataset.namaList || '').toLowerCase();
            const judul = (row.dataset.judul || '').toLowerCase();
            const pembimbing = (row.dataset.pembimbing || '').toLowerCase();

            return namaList.includes(searchText) || 
                   judul.includes(searchText) || 
                   pembimbing.includes(searchText);
        });
        
        if (noDataRow) {
            // Tampilkan "no data" hanya jika tidak ada baris setelah filter
            noDataRow.style.display = filteredRows.length === 0 ? '' : 'none';
        }
        
        setupPagination(filteredRows);
        displayPage(1, filteredRows);
    }

    function displayPage(page, rows) {
        // Sembunyikan SEMUA baris yang ada di DOM terlebih dahulu
        tableBody.querySelectorAll('tr.isiTabel').forEach(row => row.style.display = 'none');
        
        const startIndex = (page - 1) * rowsPerPage;
        const paginatedRows = rows.slice(startIndex, startIndex + rowsPerPage);
        
        // Tampilkan hanya baris yang terpaginasi
        paginatedRows.forEach(row => row.style.display = '');

        updatePaginationButtons(rows.length, page);
    }

    function setupPagination(rows) {
        if (!paginationControls) return;
        paginationControls.innerHTML = '';
        const pageCount = Math.ceil(rows.length / rowsPerPage);
        if (pageCount <= 1) return;

        const createPageItem = (content, pageNum, isDisabled = false) => {
            const li = document.createElement('li');
            li.className = `page-item ${isDisabled ? 'disabled' : ''}`;
            li.innerHTML = `<a class="page-link" href="#">${content}</a>`;
            if (!isDisabled) {
                li.addEventListener('click', e => { e.preventDefault(); displayPage(pageNum, rows); });
            }
            return li;
        };
        
        const currentPage = 1; // Selalu mulai dari halaman 1 saat setup
        paginationControls.appendChild(createPageItem('«', currentPage - 1, currentPage === 1));
        
        for (let i = 1; i <= pageCount; i++) {
            const item = createPageItem(i, i);
            if (i === currentPage) item.classList.add('active');
            paginationControls.appendChild(item);
        }
        
        paginationControls.appendChild(createPageItem('»', currentPage + 1, currentPage === pageCount));
    }
    
    function updatePaginationButtons(totalRows, currentPage) {
        if (!paginationControls) return;
        const pageCount = Math.ceil(totalRows / rowsPerPage);
        const pageItems = paginationControls.querySelectorAll('.page-item');
        
        pageItems.forEach(item => {
            const link = item.querySelector('.page-link');
            const content = link.innerHTML;
            item.classList.remove('active', 'disabled');

            if (content === '«') {
                if (currentPage === 1) item.classList.add('disabled');
                link.onclick = (e) => { e.preventDefault(); if(currentPage > 1) displayPage(currentPage - 1, allRows.filter(r => r.style.display !== 'none')); };
            } else if (content === '»') {
                if (currentPage === pageCount) item.classList.add('disabled');
                link.onclick = (e) => { e.preventDefault(); if(currentPage < pageCount) displayPage(currentPage + 1, allRows.filter(r => r.style.display !== 'none')); };
            } else {
                const pageNum = parseInt(content);
                if (pageNum === currentPage) {
                    item.classList.add('active');
                }
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', renderTable);
    }
    
    // Inisialisasi awal tabel
    initializeTableLogic();
});