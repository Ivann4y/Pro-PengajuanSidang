// File: assets/js/aDetailSidang.js

// =================================================================
// BAGIAN 1: FUNGSI-FUNGSI UTAMA
// =================================================================
// Catatan: Variabel `dosenData` dan `isSidangTA` akan didefinisikan di file .php

function openModal() {
    document.getElementById('formDalamModal').reset();
    document.getElementById('form-error').textContent = '';
    var myModal = new bootstrap.Modal(document.getElementById('penjadwalanSidangModal'), {
        keyboard: false
    });
    myModal.show();
}

function searchDosen(inputElement, index) {
    const query = inputElement.value.toLowerCase().trim();
    const dropdown = document.getElementById(`autocomplete_penguji_${index}`);

    if (query.length < 1) {
        dropdown.style.display = 'none';
        return;
    }

    // Menggunakan variabel global `dosenData`
    const filteredDosen = dosenData.filter(dosen =>
        dosen.nama.toLowerCase().includes(query)
    );

    dropdown.innerHTML = ''; 

    if (filteredDosen.length > 0) {
        filteredDosen.forEach(dosen => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            item.textContent = dosen.nama;
            
            item.addEventListener('click', () => {
                selectDosen(dosen.nama, index);
            });
            
            dropdown.appendChild(item);
        });
        dropdown.style.display = 'block';
    } else {
        dropdown.innerHTML = '<div class="autocomplete-item">Dosen tidak ditemukan</div>';
    }
}

function selectDosen(namaDosen, index) {
    const inputElement = document.getElementById(`modal_penguji${index}`);
    const dropdown = document.getElementById(`autocomplete_penguji_${index}`);
    
    inputElement.value = namaDosen;
    dropdown.style.display = 'none';
}

function addPenguji() {
    const wrapper = document.getElementById('penguji-wrapper');
    const newPengujiDiv = document.createElement('div');
    newPengujiDiv.className = 'form-group';
    
    const newIndex = wrapper.children.length + 1;
    newPengujiDiv.id = `penguji-form-${newIndex}`;

    newPengujiDiv.innerHTML = `
        <label for="modal_penguji${newIndex}">Penguji ${newIndex}</label>
        <div class="input-with-buttons">
            <div class="autocomplete-container">
                <input type="text"
                       id="modal_penguji${newIndex}"
                       name="penguji_nama[]"
                       placeholder="Ketik nama dosen penguji"
                       oninput="searchDosen(this, ${newIndex})"
                       autocomplete="off">
                <div class="autocomplete-dropdown" id="autocomplete_penguji_${newIndex}" style="display: none;"></div>
            </div>
            <div class="input-with-percent">
                <input type="number" name="penguji_bobot[]" class="form-control-bobot" min="0" placeholder="Bobot">
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
    } else {
        const lastForm = wrapper.firstElementChild;
        if (lastForm) {
            const inputNama = lastForm.querySelector('input[name="penguji_nama[]"]');
            const inputBobot = lastForm.querySelector('input[name="penguji_bobot[]"]');
            if (inputNama) inputNama.value = '';
            if (inputBobot) inputBobot.value = '';
        }
    }
}

// =================================================================
// BAGIAN 2: EVENT LISTENERS (Dijalankan setelah halaman dimuat)
// =================================================================

document.addEventListener('DOMContentLoaded', function() {
    // Event listener untuk toggle sidebar
    let menuToggle = document.querySelector(".NavSide__toggle");
    let sidebar = document.getElementById("main-sidebar");

    if (menuToggle && sidebar) {
        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
    }
    
    // Event listener untuk menutup dropdown saat klik di luar
    document.addEventListener('click', function(event) {
        setTimeout(() => {
            const allDropdowns = document.querySelectorAll('.autocomplete-dropdown');
            allDropdowns.forEach(dropdown => {
                const container = dropdown.closest('.autocomplete-container');
                if (container && !container.contains(document.activeElement)) {
                    dropdown.style.display = 'none';
                }
            });
        }, 150);
    });

    // Event listener untuk submit form (dengan validasi)
    const form = document.getElementById('formDalamModal');
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); 

            const errorBox = document.getElementById("form-error");
            errorBox.textContent = ""; 
            
            let isValid = true;
            let errorMessage = "";

            const pengujiInputs = document.querySelectorAll('input[name="penguji_nama[]"]');
            
            // Menggunakan variabel global `isSidangTA`
            if (isSidangTA) {
                const namaDosenList = [];
                pengujiInputs.forEach(input => {
                    const nama = input.value.trim();
                    if (nama !== "") {
                        namaDosenList.push(nama);
                    }
                });

                const uniqueNamaDosen = new Set(namaDosenList);
                if (uniqueNamaDosen.size < namaDosenList.length) {
                    errorMessage = "Tidak boleh ada nama dosen penguji yang sama.";
                    isValid = false;
                }
                pengujiInputs.forEach((input, index) => {
                    // Menggunakan variabel global `dosenData`
                    const namaDosenValid = dosenData.some(dosen => dosen.nama === input.value.trim());
                    if (isValid && input.value.trim() === "") {
                        errorMessage = `Nama penguji ${index + 1} tidak boleh kosong!`;
                        isValid = false;
                    } else if (isValid && !namaDosenValid && input.value.trim() !== '') {
                        errorMessage = `Nama dosen '${input.value}' tidak valid. Harap pilih dari daftar.`;
                        isValid = false;
                    }
                });
            }
            
            const ruangan = document.getElementById("modal_ruangan").value.trim();
            const tanggal = document.getElementById("modal_tanggal").value;
            const jamAwal = document.getElementById("modal_jam_awal").value;
            const jamAkhir = document.getElementById("modal_jam_akhir").value;

            if (isValid && ruangan === "") {
                errorMessage = "Ruangan harus diisi!";
                isValid = false;
            } else if (isValid && tanggal === "") {
                errorMessage = "Tanggal harus dipilih!";
                isValid = false;
            } else if (isValid && (jamAwal === "" || jamAkhir === "")) {
                errorMessage = "Jam awal dan jam akhir harus diisi!";
                isValid = false;
            } else if (isValid && jamAkhir <= jamAwal) {
                errorMessage = "Jam akhir harus setelah jam awal!";
                isValid = false;
            }

            if (!isValid) {
                errorBox.textContent = errorMessage;
                return;
            }
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            
            submitButton.disabled = true;
            submitButton.textContent = 'Menyimpan...';

            fetch('proses_ubah_jadwal.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                var myModalEl = document.getElementById('penjadwalanSidangModal');
                var modal = bootstrap.Modal.getInstance(myModalEl);
                if (modal) { modal.hide(); }

                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4B68FB'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(); 
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ff5f5f'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Oops!',
                    text: 'Terjadi kesalahan saat menghubungi server.',
                    icon: 'error'
                });
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = 'Ubah Penjadwalan';
            });
        });
    }
});