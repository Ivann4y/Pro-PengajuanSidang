// ==========================================================================
// FUNGSI GLOBAL & HELPER
// ==========================================================================

function openModal() {
    document.getElementById('formDalamModal').reset();
    document.getElementById('form-error').textContent = '';
    
    // Panggil validasi real-time saat modal dibuka untuk pertama kali
    if (isSidangTA) {
        validateTotalWeightRealtime();
    }

    var myModal = new bootstrap.Modal(document.getElementById('penjadwalanSidangModal'));
    myModal.show();
}

function searchDosen(inputElement, index) {
    const query = inputElement.value.toLowerCase().trim();
    const dropdown = document.getElementById(`autocomplete_penguji_${index}`);
    if (query.length < 1) { dropdown.style.display = 'none'; return; }

    const filteredDosen = dosenData.filter(dosen => dosen.nama.toLowerCase().includes(query));
    dropdown.innerHTML = ''; 

    if (filteredDosen.length > 0) {
        filteredDosen.forEach(dosen => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            item.textContent = dosen.nama;
            item.addEventListener('click', () => selectDosen(dosen.nama, index));
            dropdown.appendChild(item);
        });
    } else {
        dropdown.innerHTML = '<div class="autocomplete-item">Dosen tidak ditemukan</div>';
    }
    dropdown.style.display = 'block';
}

function selectDosen(namaDosen, index) {
    document.getElementById(`modal_penguji${index}`).value = namaDosen;
    document.getElementById(`autocomplete_penguji_${index}`).style.display = 'none';
}

function addPenguji() {
    const wrapper = document.getElementById('penguji-wrapper');
    const newIndex = wrapper.children.length + 1;
    const newPengujiDiv = document.createElement('div');
    newPengujiDiv.className = 'form-group';
    newPengujiDiv.id = `penguji-form-${newIndex}`;

    newPengujiDiv.innerHTML = `
        <label for="modal_penguji${newIndex}">Penguji ${newIndex}</label>
        <div class="input-with-buttons">
            <div class="autocomplete-container">
                <input type="text" id="modal_penguji${newIndex}" name="penguji_nama[]" placeholder="Ketik nama dosen penguji" oninput="searchDosen(this, ${newIndex})" autocomplete="off">
                <div class="autocomplete-dropdown" id="autocomplete_penguji_${newIndex}"></div>
            </div>
            <div class="input-with-percent">
                <input type="number" name="penguji_bobot[]" class="form-control-bobot" min="0" placeholder="Bobot" oninput="cleanNumberInput(this); validateTotalWeightRealtime();">
                <span class="percent-sign">%</span>
            </div>
        </div>
    `;
    wrapper.appendChild(newPengujiDiv);
}

function removePenguji() {
    const wrapper = document.getElementById('penguji-wrapper');
    if (wrapper.children.length > 1) {
        wrapper.lastElementChild.remove();
        validateTotalWeightRealtime(); // Update total setelah menghapus
    }
}

function cleanNumberInput(inputElement) {
    if (inputElement.value) {
        const numericValue = parseInt(inputElement.value, 10);
        inputElement.value = isNaN(numericValue) || numericValue < 0 ? 0 : numericValue;
    }
}

function validateTotalWeightRealtime() {
    let totalBobot = 0;
    const messageElement = document.getElementById('realtime-validation-detail'); 
     if (!messageElement) return;

    if (isSidangTA) {
        const pembimbingInput = document.querySelector('input[name="pembimbing_bobot"]');
        const pengujiInputs = document.querySelectorAll('input[name="penguji_bobot[]"]');
        if (pembimbingInput) totalBobot += parseInt(pembimbingInput.value, 10) || 0;
        pengujiInputs.forEach(input => totalBobot += parseInt(input.value, 10) || 0);
    } else { // Untuk Sidang Semester
        const pengampuInputs = document.querySelectorAll('input[name="pengampu_bobot[]"]');
        pengampuInputs.forEach(input => totalBobot += parseInt(input.value, 10) || 0);
    }

    if (totalBobot > 100) {
        messageElement.textContent = `Peringatan: Total bobot melebihi 100% (Saat ini: ${totalBobot}%)`;
    } else {
        messageElement.textContent = '';
    }
}


// ==========================================================================
// EVENT LISTENERS & INSIALISASI
// ==========================================================================
document.addEventListener('DOMContentLoaded', function() {
    // Event listener untuk submit form
   document.getElementById('formDalamModal')?.addEventListener('submit', function(event) {
    event.preventDefault();
    const errorBox = document.getElementById("form-error");
    errorBox.textContent = ""; 
    
    let totalBobot = 0;
    
    if (isSidangTA) {
        const pembimbingInput = document.querySelector('input[name="pembimbing_bobot"]');
        totalBobot += parseInt(pembimbingInput.value, 10) || 0;
        document.querySelectorAll('input[name="penguji_bobot[]"]').forEach(input => {
            totalBobot += parseInt(input.value, 10) || 0;
        });
    } else { // Untuk Sidang Semester
        document.querySelectorAll('input[name="pengampu_bobot[]"]').forEach(input => {
            totalBobot += parseInt(input.value, 10) || 0;
        });
    }

    if (totalBobot !== 100) {
        errorBox.textContent = `Gagal: Total bobot harus tepat 100%. Total saat ini: ${totalBobot}%.`;
        return;
    }
        
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Menyimpan...';

        fetch('../../control/admin/proses_ubah_jadwal.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('penjadwalanSidangModal'));
            if (modal) modal.hide();

            Swal.fire({
                title: data.status === 'success' ? 'Berhasil!' : 'Gagal!',
                text: data.message,
                icon: data.status
            }).then(() => {
                if (data.status === 'success') location.reload();
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi kesalahan saat menghubungi server.', 'error');
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.textContent = 'Ubah Penjadwalan';
        });
    });

    // Event listener untuk klik di luar dropdown
    document.addEventListener('click', e => {
        const openDropdown = document.querySelector('.autocomplete-dropdown[style*="display: block"]');
        if (openDropdown && !openDropdown.closest('.autocomplete-container').contains(e.target)) {
            openDropdown.style.display = 'none';
        }
    });
});

function confirmDelete(idSidang) {
    Swal.fire({
        title: 'Anda Yakin?',
        text: "Data sidang ini akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('id_sidang', idSidang);

            fetch('../../control/admin/proses_hapus_sidang.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    title: data.status === 'success' ? 'Dihapus!' : 'Gagal!',
                    text: data.message,
                    icon: data.status
                }).then(() => {
                    if (data.status === 'success') window.location.href = 'aDaftarSidang.php';
                });
            })
            .catch(err => {
                console.error('Error:', err);
                Swal.fire('Error', 'Gagal menghubungi server.', 'error');
            });
        }
    });
}