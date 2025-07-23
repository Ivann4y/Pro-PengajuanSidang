// ==========================================================================
// BAGIAN 1: FUNGSI GLOBAL & INSTANCE MODAL
// ==========================================================================

let taModalInstance, semModalInstance;
let pengujiCount = 0;

/**
 * Fungsi utama untuk membuka modal penjadwalan.
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
 * Mereset dan mengisi modal Sidang TA.
 */
function resetAndPopulateTAModal(el) {
    const formTA = document.getElementById('formDalamModal-ta');
    formTA.reset();
    document.getElementById('modal_id_sidang-ta').value = el.dataset.id || '';

    document.getElementById('modal_nim-ta').value = el.dataset.kelompok || '';
    document.getElementById('modal_judul_sidang-ta').value = el.dataset.judul || '';
    document.getElementById('modal_prodi-ta').value = el.dataset.prodi || '';
    document.getElementById('form-error-ta').textContent = '';

    const wrapper = document.getElementById('penguji-wrapper-ta');
    wrapper.innerHTML = '';
     try {
        const pembimbingList = JSON.parse(el.dataset.pembimbingList || '[]');
        pembimbingList.forEach((nama, index) => {
            const pembimbingIndex = index + 1;
            const pembimbingHtml = `
                <div class="form-group">
                    <label for="modal_pembimbing-ta-${pembimbingIndex}">Pembimbing ${pembimbingIndex}</label>
                    <div class="input-with-buttons">
                        <input type="text" name="pembimbing_nama[]" value="${nama}" readonly />
                        <div class="bobot-nilai-input-group">
                            <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_pembimbing_bobot-ta-${pembimbingIndex}')">-</button>
                            <div class="input-with-percent">
                                <input type="number" id="modal_pembimbing_bobot-ta-${pembimbingIndex}" name="pembimbing_bobot[]" class="bobot-input-new ta-bobot-input" placeholder="Bobot" min="0" oninput="cleanNumberInput(this); validateTotalWeightRealtime('Tugas Akhir');">
                                <span class="percent-sign">%</span>
                            </div>
                            <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_pembimbing_bobot-ta-${pembimbingIndex}')">+</button>
                        </div>
                    </div>
                </div>`;
            wrapper.insertAdjacentHTML('beforeend', pembimbingHtml);
        });
    } catch(e) { console.error("Gagal memproses data pembimbing:", e); }

    pengujiCount = 0;
    addPenguji();
}
function formatTanggalIndonesia(dateString) {
    if (!dateString) {
        return ''; // Kembalikan string kosong jika input null atau kosong
    }

    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    try {
        const tanggal = new Date(dateString);
        // Menambahkan penanganan untuk zona waktu agar tidak mundur satu hari
        const offset = tanggal.getTimezoneOffset();
        const tanggalLokal = new Date(tanggal.valueOf() + offset * 60 * 1000);
        return tanggalLokal.toLocaleDateString('id-ID', options);
    } catch (e) {
        console.error("Format tanggal tidak valid:", dateString);
        return dateString; // Kembalikan tanggal asli jika terjadi error
    }
}

/**
 * Mengisi modal Sidang Semester.
 */
function populateSemModal(el) {
    const formSem = document.getElementById('formDalamModal-sem');
    formSem.reset();
    document.getElementById('modal_id_sidang-sem').value = el.dataset.id || '';

    document.getElementById('modal_nim-sem').value = el.dataset.kelompok || '';
    document.getElementById('modal_matkul-sem').value = el.dataset.judul || ''; // Note: Judul dipakai untuk matkul
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
                                <div class="input-with-percent">
                                    <input type="number" id="modal_qty_pengampu-sem-${pengampuIndex}" name="pengampu_bobot[]" class="bobot-input-new" placeholder="Bobot" min="0" oninput="cleanNumberInput(this); validateTotalWeightRealtime('Semester');" />
                                    <span class="percent-sign">%</span>
                                </div>
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
 * Menambah field input penguji baru secara dinamis.
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
                <input type="text" id="modal_penguji-ta-${pengujiCount}" name="penguji_nama[]" placeholder="Ketik nama dosen penguji" oninput="searchPenguji(this, ${pengujiCount})" autocomplete="off">
                <div class="autocomplete-dropdown" id="autocomplete_penguji_${pengujiCount}"></div>
            </div>
           <div class="bobot-nilai-input-group">
                <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_penguji-ta-${pengujiCount}')">-</button>
                <div class="input-with-percent">
                    <input type="number" id="modal_qty_penguji-ta-${pengujiCount}" name="penguji_bobot[]" class="bobot-input-new ta-bobot-input" placeholder="Bobot" min="0" oninput="cleanNumberInput(this); validateTotalWeightRealtime('Tugas Akhir');">
                    <span class="percent-sign">%</span>
                </div>
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
        validateTotalWeightRealtime('Tugas Akhir');
    }
}

/**
 * Memvalidasi total bobot secara real-time.
 */
function validateTotalWeightRealtime(modalType) {
    let totalBobot = 0;
    const suffix = modalType === 'Tugas Akhir' ? 'ta' : 'sem';
    const messageElement = document.getElementById(`realtime-validation-${suffix}`);

    if (modalType === 'Tugas Akhir') {
        const modal = document.getElementById('penjadwalanSidangTAModal');
        // BENAR: Ambil SEMUA input bobot di modal TA (pembimbing + penguji)
        const allBobotInputs = modal.querySelectorAll('.ta-bobot-input'); 
        allBobotInputs.forEach(input => {
            totalBobot += parseInt(input.value, 10) || 0;
        });

    } else if (modalType === 'Semester') {
        const modal = document.getElementById('penjadwalanSidangSemModal');
        const pengampuInputs = modal.querySelectorAll('input[name="pengampu_bobot[]"]');
        pengampuInputs.forEach(input => {
            totalBobot += parseInt(input.value, 10) || 0;
        });
    }

    if (totalBobot > 100) {
        messageElement.textContent = `Total bobot melebihi 100% (Saat ini: ${totalBobot}%)`;
    } else {
        messageElement.textContent = '';
    }
}

/**
 * Mengatur visibilitas tombol tambah/kurang.
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
 * Mencari dosen untuk autocomplete.
 */
function searchPenguji(inputElement, index) {
    const query = inputElement.value.toLowerCase().trim();
    const dropdown = document.getElementById(`autocomplete_penguji_${index}`);

    if (query.length < 1) {
        dropdown.style.display = 'none';
        return;
    }

    const filteredDosen = dosenData.filter(dosen => dosen.nama.toLowerCase().includes(query));

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
 * Memilih dosen dari dropdown.
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
    if (input) {
        let currentValue = parseInt(input.value, 10) || 0;
        input.value = currentValue + 1;
        cleanNumberInput(input);
        validateTotalWeightRealtime(input.form.elements['tipe_sidang'].value);
    }
}

/**
 * Mengurangi nilai pada input bobot.
 */
function decrementValue(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        let val = parseInt(input.value, 10) || 0;
        if (val > (parseInt(input.min, 10) || 0)) {
            input.value = val - 1;
        }
        cleanNumberInput(input);
        validateTotalWeightRealtime(input.form.elements['tipe_sidang'].value);
    }
}

/**
 * Membersihkan nilai input dari angka nol di depan.
 */
function cleanNumberInput(inputElement) {
    if (inputElement.value) {
        const numericValue = parseInt(inputElement.value, 10);
        if (isNaN(numericValue) || numericValue < 0) {
            inputElement.value = 0;
        } else {
            inputElement.value = numericValue;
        }
    }
}

/**
 * Menangani pengiriman data form.
 */
function handleFormSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const modalType = form.elements['tipe_sidang'].value;

    if (!validateForm(modalType)) {
        return;
    }
    
    const modalSuffix = modalType === 'Tugas Akhir' ? 'ta' : 'sem';
    const errorBox = document.getElementById(`form-error-${modalSuffix}`);
    const submitButton = form.querySelector('button[type="submit"]');

    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...';
    errorBox.textContent = '';

    const formData = new FormData(form);

    fetch('../../control/admin/createPenjadwalan.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
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
            errorBox.textContent = data.message || 'Terjadi kesalahan.';
            Swal.fire('Gagal!', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        errorBox.textContent = 'Tidak dapat terhubung ke server.';
        Swal.fire('Koneksi Gagal', 'Tidak dapat mengirim data ke server.', 'error');
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Buat Penjadwalan';
    });
}

/**
 * Memvalidasi form sebelum dikirim.
 */
// ==========================================================================
// GANTI FUNGSI INI DENGAN VERSI BARU
// ==========================================================================
/**
 * Memvalidasi form sebelum dikirim (VERSI DENGAN URUTAN YANG BENAR).
 */
function validateForm(modalType) {
    const suffix = modalType === 'Tugas Akhir' ? 'ta' : 'sem';
    const errorBox = document.getElementById(`form-error-${suffix}`);
    errorBox.textContent = '';
     
    // LANGKAH 1: Validasi bahwa semua field wajib diisi. INI HARUS PERTAMA.
    const fieldsToValidate = [
        { id: `modal_ruangan-${suffix}`, message: 'Ruangan harus diisi.' },
        { id: `modal_tanggal-${suffix}`, message: 'Tanggal harus dipilih.' },
        { id: `modal_jam_awal-${suffix}`, message: 'Jam awal harus diisi.' },
        { id: `modal_jam_akhir-${suffix}`, message: 'Jam akhir harus diisi.' },
    ];
    for (const field of fieldsToValidate) {
        const element = document.getElementById(field.id);
        if (!element || element.value.trim() === '') {
            errorBox.textContent = field.message;
            element.focus();
            return false;
        }
    }

    // LANGKAH 2: Setelah yakin semua field terisi, baru lakukan validasi logika waktu.
    const dateTimeValidation = validateDateTime(suffix);
    if (!dateTimeValidation.isValid) {
        errorBox.textContent = dateTimeValidation.message;
        return false;
    }

    // LANGKAH 3: Lanjutkan validasi sisa (bobot, nama penguji, dll)
    if (modalType === 'Tugas Akhir') {
        let totalBobot = 0;
        const bobotPembimbingInputs = document.querySelectorAll('#penjadwalanSidangTAModal input[name="pembimbing_bobot[]"]');
        const namaPengujiInputs = document.querySelectorAll('#penjadwalanSidangTAModal input[name="penguji_nama[]"]');
        const bobotPengujiInputs = document.querySelectorAll('#penjadwalanSidangTAModal input[name="penguji_bobot[]"]');

        for (let i = 0; i < bobotPembimbingInputs.length; i++) {
            const bobot = parseInt(bobotPembimbingInputs[i].value, 10);
            if (isNaN(bobot) || bobot <= 0) {
                errorBox.textContent = `Bobot Pembimbing ${i + 1} harus diisi (lebih dari 0).`;
                bobotPembimbingInputs[i].focus();
                return false;
            }
            totalBobot += bobot;
        }

        for (let i = 0; i < namaPengujiInputs.length; i++) {
            if (namaPengujiInputs[i].value.trim() === '') {
                errorBox.textContent = `Nama Penguji ${i + 1} harus diisi.`;
                namaPengujiInputs[i].focus();
                return false;
            }
            const bobot = parseInt(bobotPengujiInputs[i].value, 10);
            if (isNaN(bobot) || bobot <= 0) {
                errorBox.textContent = `Bobot Penguji ${i + 1} harus diisi (lebih dari 0).`;
                bobotPengujiInputs[i].focus();
                return false;
            }
            totalBobot += bobot;
        }

        if (totalBobot !== 100) {
            errorBox.textContent = `Total bobot harus tepat 100%. Saat ini totalnya adalah ${totalBobot}%.`;
            return false;
        }

    } else if (modalType === 'Semester') {
        let totalBobotSem = 0;
        const pengampuBobotInputs = document.querySelectorAll('#penjadwalanSidangSemModal input[name="pengampu_bobot[]"]');
        if (pengampuBobotInputs.length === 0) {
            errorBox.textContent = 'Tidak ada dosen pengampu yang ditemukan.';
            return false;
        }
        for (let i = 0; i < pengampuBobotInputs.length; i++) {
            const bobot = parseInt(pengampuBobotInputs[i].value, 10);
            if (isNaN(bobot) || bobot <= 0) {
                errorBox.textContent = `Bobot Pengampu ${i + 1} harus diisi (lebih dari 0).`;
                pengampuBobotInputs[i].focus();
                return false;
            }
            totalBobotSem += bobot;
        }
        if (totalBobotSem !== 100) {
            errorBox.textContent = `Total bobot Pengampu harus tepat 100%. Saat ini totalnya adalah ${totalBobotSem}%.`;
            return false;
        }
    }

    return true; // Semua validasi berhasil
}
// ==========================================================================
// BAGIAN 2: SCRIPT YANG BERJALAN SETELAH HALAMAN SIAP
// ==========================================================================
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById('formDalamModal-ta')?.addEventListener('submit', handleFormSubmit);
    document.getElementById('formDalamModal-sem')?.addEventListener('submit', handleFormSubmit);
    ['ta', 'sem'].forEach(suffix => {
    const tanggalInput = document.getElementById(`modal_tanggal-${suffix}`);
    const jamAwalInput = document.getElementById(`modal_jam_awal-${suffix}`);
    const jamAkhirInput = document.getElementById(`modal_jam_akhir-${suffix}`);

    if (tanggalInput) tanggalInput.addEventListener('change', () => validateDateTimeRealtime(suffix));
    if (jamAwalInput) jamAwalInput.addEventListener('change', () => validateDateTimeRealtime(suffix));
    if (jamAkhirInput) jamAkhirInput.addEventListener('change', () => validateDateTimeRealtime(suffix));
});
    // Fungsi untuk mendapatkan tanggal hari ini dalam format YYYY-MM-DD
    const getTodayDateString = () => {
        const today = new Date();
        const year = today.getFullYear();
        // `slice(-2)` memastikan format dua digit (misal: 05, 09, 12)
        const month = ('0' + (today.getMonth() + 1)).slice(-2); 
        const day = ('0' + today.getDate()).slice(-2);
        return `${year}-${month}-${day}`;
    };

    // Ambil tanggal hari ini sekali saja
    const todayString = getTodayDateString();

    // Temukan semua input tanggal di halaman (untuk modal TA dan Semester)
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    // Loop setiap input tanggal dan atur atribut 'min' nya
    dateInputs.forEach(input => {
        input.setAttribute('min', todayString);
    });


    document.addEventListener('click', function(event) {
        const allDropdowns = document.querySelectorAll('.autocomplete-dropdown');
        const clickedInsideContainer = event.target.closest('.autocomplete-container');
        allDropdowns.forEach(dropdown => {
            if (!clickedInsideContainer || !dropdown.closest('.autocomplete-container').contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    });

    // ==========================================================================
    // BAGIAN BARU: LOGIKA UNTUK TOGGLE SIDEBAR MOBILE
    // ==========================================================================
    const sidebar = document.getElementById('main-sidebar');
    const toggleButton = document.querySelector('.NavSide__toggle');

    if (toggleButton && sidebar) {
        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('NavSide__sidebar--active-mobile');
            toggleButton.classList.toggle('NavSide__toggle--active');
        });
    }

    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('#adminSidangContent tr.isiTabel');

    if(searchInput) {
        searchInput.addEventListener('input', function() {
            const keyword = searchInput.value.toLowerCase();
            let visibleRows = 0;
            tableRows.forEach(row => {
                const rowText = row.innerText.toLowerCase();
                const isVisible = rowText.includes(keyword);
                row.style.display = isVisible ? '' : 'none';
                if (isVisible) {
                    visibleRows++;
                }
            });

            const noResultsRow = document.querySelector('.no-results-row');
            if (noResultsRow) {
                noResultsRow.style.display = visibleRows === 0 ? '' : 'none';
            }
        });
    }
});
// FUNGSI BARU YANG DITAMBAHKAN
function validateDateTime(suffix) {
    const tanggalInput = document.getElementById(`modal_tanggal-${suffix}`);
    const jamAwalInput = document.getElementById(`modal_jam_awal-${suffix}`);
    const jamAkhirInput = document.getElementById(`modal_jam_akhir-${suffix}`);

    // Jangan validasi jika field belum terisi
    if (!tanggalInput.value || !jamAwalInput.value) {
        return { isValid: true, message: '' };
    }

    // 1. Gabungkan tanggal dan jam mulai menjadi satu objek Date yang utuh
    const selectedDateTime = new Date(`${tanggalInput.value}T${jamAwalInput.value}`);
    const now = new Date(); // Objek Date untuk waktu saat ini

    // 2. Aturan Utama: Waktu yang dipilih tidak boleh di masa lalu
    if (selectedDateTime < now) {
        return { isValid: false, message: 'Waktu sidang tidak boleh di masa lalu.' };
    }
    
    // 3. Aturan Tambahan: Jam akhir harus setelah jam mulai
    if (jamAkhirInput.value && (jamAkhirInput.value <= jamAwalInput.value)) {
        return { isValid: false, message: 'Jam akhir harus setelah jam awal.' };
    }

    // Jika semua aturan lolos
    return { isValid: true, message: '' };
}