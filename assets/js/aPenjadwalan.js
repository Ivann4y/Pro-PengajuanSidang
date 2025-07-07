// ==========================================================================
// BAGIAN 1: FUNGSI GLOBAL & INSTANCE MODAL
// Fungsi-fungsi ini berada di lingkup global agar bisa dipanggil
// oleh atribut onclick="..." di HTML.
// Variabel `dosenData` akan diinisialisasi oleh skrip dari file PHP.
// ==========================================================================

let taModalInstance, semModalInstance;
let pengujiCount = 0; // Mulai dari 0, akan di-increment oleh addPenguji()

/**
 * Fungsi utama untuk membuka modal penjadwalan.
 * Dipanggil dari tombol "Aksi" di tabel.
 * @param {HTMLElement} rowElement - Elemen <tr> dari baris yang diklik.
 */
function openJadwalModal(rowElement) {
    const tipeSidang = rowElement.dataset.tipeSidang;

    if (tipeSidang === 'Tugas Akhir') {
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
 * Ini adalah fungsi kunci yang memastikan penguji pertama bekerja dengan benar.
 * @param {HTMLElement} el - Elemen <tr> dari baris yang diklik.
 */
function resetAndPopulateTAModal(el) {
    const formTA = document.getElementById('formDalamModal-ta');
    formTA.reset(); // Membersihkan semua input di form
    document.getElementById('modal_id_sidang-ta').value = el.dataset.id || ''; // Menyimpan ID Sidang

    // Mengisi field yang readonly
    document.getElementById('modal_nim-ta').value = el.dataset.kelompok || '';
    document.getElementById('modal_judul_sidang-ta').value = el.dataset.judul || '';
    document.getElementById('modal_pembimbing-ta').value = el.dataset.pembimbing || '';
    document.getElementById('modal_prodi-ta').value = el.dataset.prodi || '';
    document.getElementById('form-error-ta').textContent = '';

    // Reset field penguji dengan membangun ulang dari awal
    const wrapper = document.getElementById('penguji-wrapper-ta');
    wrapper.innerHTML = ''; // Hapus semua field penguji yang ada
    pengujiCount = 0; // Reset counter

    // Buat field penguji pertama secara dinamis
    addPenguji(); 
}

/**
 * Mengisi data ke modal Sidang Semester.
 * @param {HTMLElement} el - Elemen <tr> dari baris yang diklik.
 */
function populateSemModal(el) {
    const formSem = document.getElementById('formDalamModal-sem');
    formSem.reset();
    document.getElementById('modal_id_sidang-sem').value = el.dataset.id || ''; // Menyimpan ID Sidang

    // Mengisi field readonly
    document.getElementById('modal_nim-sem').value = el.dataset.kelompok || '';
    document.getElementById('modal_matkul-sem').value = el.dataset.judul || '';
    document.getElementById('modal_prodi-sem').value = el.dataset.prodi || '';
    document.getElementById('form-error-sem').textContent = '';

    // Mengisi field pengampu secara dinamis
    const pengampuWrapper = document.getElementById('pengampu-wrapper-sem');
    pengampuWrapper.innerHTML = ''; // Kosongkan dulu
    try {
        const pengampuList = JSON.parse(el.dataset.pengampu || '[]');
        if (pengampuList.length > 0) {
            pengampuList.forEach((nama, index) => {
                if (!nama) return; // Lewati jika nama kosong
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
 * Menambah field input penguji baru dengan struktur autocomplete.
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
    allToggleButtons.forEach(group => group.style.display = 'none'); // Sembunyikan semua

    if (allToggleButtons.length > 0) {
        const lastGroup = allToggleButtons[allToggleButtons.length - 1];
        lastGroup.style.display = 'inline-flex'; // Tampilkan yang terakhir

        const removeButton = lastGroup.querySelector('button[onclick="removePenguji()"]');
        if (removeButton) {
            removeButton.style.display = (pengujiCount > 1) ? 'block' : 'none';
        }
    }
}

/**
 * Mencari dosen untuk dropdown autocomplete.
 * @param {HTMLInputElement} inputElement - Elemen input yang diketik.
 * @param {number} index - Nomor urut penguji.
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
 * @param {string} namaDosen - Nama dosen yang dipilih.
 * @param {number} index - Nomor urut penguji.
 */
function selectPenguji(namaDosen, index) {
    document.getElementById(`modal_penguji-ta-${index}`).value = namaDosen;
    document.getElementById(`autocomplete_penguji_${index}`).style.display = 'none';
}

/**
 * Menambah nilai pada input bobot.
 * @param {string} inputId - ID dari input bobot.
 */
function incrementValue(inputId) {
    const input = document.getElementById(inputId);
    if (input) input.value = parseInt(input.value, 10) + 1;
}

/**
 * Mengurangi nilai pada input bobot.
 * @param {string} inputId - ID dari input bobot.
 */
function decrementValue(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        let val = parseInt(input.value, 10);
        if (val > (input.min || 0)) {
            input.value = val - 1;
        }
    }
}

/**
 * Menangani pengiriman data dari form modal (TA atau Semester).
 * @param {Event} event - Event 'submit' dari form.
 */
// --- GANTI SELURUH FUNGSI INI ---
function handleFormSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const modalType = form.elements['tipe_sidang'].value; // Ambil tipe dari input hidden

    // Validasi form terlebih dahulu
    if (!validateForm(modalType)) {
        return; // Hentikan jika tidak valid
    }
    
    // Dapatkan elemen yang benar setelah validasi
    const modalSuffix = modalType === 'Tugas Akhir' ? 'ta' : 'sem';
    const errorBox = document.getElementById(`form-error-${modalSuffix}`);
    const submitButton = form.querySelector('button[type="submit"]');

    // Lanjutkan dengan proses submit
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...';
    errorBox.textContent = '';

    const formData = new FormData(form);

    fetch('createPenjadwalan.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            const modalToHide = modalType === 'Tugas Akhir' ? taModalInstance : semModalInstance;
            if (modalToHide) modalToHide.hide();
            
            Swal.fire({
                title: 'Berhasil!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => location.reload());

        } else {
            errorBox.textContent = data.message || 'Terjadi kesalahan yang tidak diketahui.';
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
        errorBox.textContent = 'Tidak dapat terhubung ke server. Periksa koneksi Anda.';
        Swal.fire({
            title: 'Koneksi Gagal',
            text: 'Tidak dapat mengirim data ke server.',
            icon: 'error'
        });
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Buat Penjadwalan';
    });
}

/**
 * Validasi form sebelum dikirim.
 * @param {string} modalType - 'Tugas Akhir' atau 'Semester'.
 * @returns {boolean} - True jika valid, false jika tidak.
 */
// --- GANTI SELURUH FUNGSI INI JUGA ---
function validateForm(modalType) {
    // Logika untuk mendapatkan suffix sudah benar sekarang
    const suffix = modalType === 'Tugas Akhir' ? 'ta' : 'sem';
    const errorBox = document.getElementById(`form-error-${suffix}`);

    // Pastikan errorBox ada sebelum melanjutkan
    if (!errorBox) {
        console.error(`Elemen error dengan ID 'form-error-${suffix}' tidak ditemukan!`);
        return false;
    }
    errorBox.textContent = '';
    
    // Validasi field umum
    const fieldsToValidate = [
        { id: `modal_ruangan-${suffix}`, message: 'Ruangan harus diisi.' },
        { id: `modal_tanggal-${suffix}`, message: 'Tanggal harus dipilih.' },
        { id: `modal_jam_awal-${suffix}`, message: 'Jam awal harus diisi.' },
        { id: `modal_jam_akhir-${suffix}`, message: 'Jam akhir harus diisi.' }
    ];

    for (const field of fieldsToValidate) {
        const element = document.getElementById(field.id);
        if (!element || element.value.trim() === '') {
            errorBox.textContent = field.message;
            return false;
        }
    }
    
    // Validasi jam
    const jamAwal = document.getElementById(`modal_jam_awal-${suffix}`).value;
    const jamAkhir = document.getElementById(`modal_jam_akhir-${suffix}`).value;
    if (jamAkhir <= jamAwal) {
        errorBox.textContent = 'Jam akhir harus setelah jam awal.';
        return false;
    }
    
    // Validasi dosen/penguji
    if (modalType === 'Tugas Akhir') {
        const pengujiInputs = document.querySelectorAll('#penjadwalanSidangTAModal input[name="penguji_nama[]"]');
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
    } else { // Validasi untuk Sidang Semester
        const pengampuInputs = document.querySelectorAll('#penjadwalanSidangSemModal input[name="pengampu_nama[]"]');
        for (let i = 0; i < pengampuInputs.length; i++) {
            if (pengampuInputs[i].value.trim() === '') {
                errorBox.textContent = `Nama Pengampu ${i + 1} harus diisi.`;
                return false;
            }
        }
    }

    return true;
}

// ==========================================================================
// BAGIAN 2: SCRIPT YANG BERJALAN SETELAH HALAMAN SIAP (DOMContentLoaded)
// ==========================================================================
document.addEventListener("DOMContentLoaded", function () {
    // --- PASANG EVENT LISTENER KE FORM MODAL ---
    document.getElementById('formDalamModal-ta')?.addEventListener('submit', handleFormSubmit);
    document.getElementById('formDalamModal-sem')?.addEventListener('submit', handleFormSubmit);

    // --- MENUTUP DROPDOWN SAAT KLIK DI LUAR ---
    document.addEventListener('click', function(event) {
        const allDropdowns = document.querySelectorAll('.autocomplete-dropdown');
        const clickedInsideContainer = event.target.closest('.autocomplete-container');
        allDropdowns.forEach(dropdown => {
            if (!clickedInsideContainer || !dropdown.closest('.autocomplete-container').contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    });

    // --- LOGIKA PENCARIAN DAN PAGINASI TABEL UTAMA ---
    const searchInput = document.querySelector('.search-input-group .form-control');
    const allRows = Array.from(document.querySelectorAll('#adminSidangContent tr.isiTabel'));
    const paginationControls = document.getElementById('pagination-controls');
    const noDataRow = document.querySelector('.no-results-row');
    const rowsPerPage = 10;

    function renderTable() {
        const searchText = searchInput.value.toLowerCase().trim();
        const filteredRows = allRows.filter(row => {
            const judulText = (row.cells[2]?.textContent || '').toLowerCase();
            const dosenText = (row.cells[3]?.textContent || '').toLowerCase();
            return judulText.includes(searchText) || dosenText.includes(searchText);
        });
        
        if (noDataRow) noDataRow.style.display = filteredRows.length === 0 ? '' : 'none';
        
        setupPagination(filteredRows);
        displayPage(1, filteredRows);
    }

    function displayPage(page, rows) {
        allRows.forEach(row => row.style.display = 'none');
        
        const startIndex = (page - 1) * rowsPerPage;
        const paginatedRows = rows.slice(startIndex, startIndex + rowsPerPage);
        paginatedRows.forEach(row => row.style.display = '');

        updatePaginationButtons(rows.length, page);
    }

    function setupPagination(rows) {
        paginationControls.innerHTML = '';
        const pageCount = Math.ceil(rows.length / rowsPerPage);
        if (pageCount <= 1) return;

        const createPageItem = (content, pageNum) => {
            const li = document.createElement('li');
            li.className = 'page-item';
            li.innerHTML = `<a class="page-link" href="#">${content}</a>`;
            li.addEventListener('click', e => { e.preventDefault(); displayPage(pageNum, rows); });
            return li;
        };

        paginationControls.appendChild(createPageItem('«', 1));
        for (let i = 1; i <= pageCount; i++) {
            paginationControls.appendChild(createPageItem(i, i));
        }
        paginationControls.appendChild(createPageItem('»', pageCount));
    }
    
    function updatePaginationButtons(totalRows, currentPage) {
        const pageCount = Math.ceil(totalRows / rowsPerPage);
        const pageItems = paginationControls.querySelectorAll('.page-item');
        pageItems.forEach(item => {
            const link = item.querySelector('.page-link');
            const pageNum = parseInt(link.textContent);
            item.classList.remove('active', 'disabled');

            if (link.textContent === '«') {
                if (currentPage === 1) item.classList.add('disabled');
            } else if (link.textContent === '»') {
                if (currentPage === pageCount) item.classList.add('disabled');
            } else if (pageNum === currentPage) {
                item.classList.add('active');
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', renderTable);
    
    // Inisialisasi awal tabel dan paginasi
    if (allRows.length > 0) {
        renderTable();
    }
});