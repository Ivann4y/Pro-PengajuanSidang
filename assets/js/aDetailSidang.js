// Membuka modal penjadwalan dan mereset form di dalamnya
function openModal() {
    document.getElementById('formDalamModal').reset();
    document.getElementById('form-error').textContent = '';
    var myModal = new bootstrap.Modal(document.getElementById('penjadwalanSidangModal'), {
        keyboard: false
    });
    myModal.show();
}
// Fungsi autocomplete pencarian dosen penguji
function searchDosen(inputElement, index) {
    const query = inputElement.value.toLowerCase().trim();
    const dropdown = document.getElementById(`autocomplete_penguji_${index}`);

    if (query.length < 1) {
        dropdown.style.display = 'none';
        return;
    }

    // Filter dosen berdasarkan input
    const filteredDosen = dosenData.filter(dosen =>
        dosen.nama.toLowerCase().includes(query)
    );

    dropdown.innerHTML = ''; 

    if (filteredDosen.length > 0) {
        filteredDosen.forEach(dosen => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            item.textContent = dosen.nama;
            // Pilih dosen saat diklik
            item.addEventListener('click', () => {
                selectDosen(dosen.nama, index);
            });
            
            dropdown.appendChild(item);
        });
        dropdown.style.display = 'block';
    } else {
        dropdown.innerHTML = '<div class="autocomplete-item">Dosen tidak ditemukan</div>';
    }
     dropdown.style.display = 'block';
}
// Mengisi input nama dosen penguji dari hasil autocomplete
function selectDosen(namaDosen, index) {
    const inputElement = document.getElementById(`modal_penguji${index}`);
    const dropdown = document.getElementById(`autocomplete_penguji_${index}`);
    
    inputElement.value = namaDosen;
    dropdown.style.display = 'none';
}
// Menambah field penguji baru pada form
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
// Menghapus field penguji terakhir pada form
function removePenguji() {
    const wrapper = document.getElementById('penguji-wrapper');
    if (wrapper.children.length > 1) {
        wrapper.lastElementChild.remove();
    } else {
        // Jika hanya satu, kosongkan saja inputnya
        const lastForm = wrapper.firstElementChild;
        if (lastForm) {
            const inputNama = lastForm.querySelector('input[name="penguji_nama[]"]');
            const inputBobot = lastForm.querySelector('input[name="penguji_bobot[]"]');
            if (inputNama) inputNama.value = '';
            if (inputBobot) inputBobot.value = '';
        }
    }
}


document.addEventListener('DOMContentLoaded', function() {
    // Event listener untuk toggle sidebar di mobile
    let menuToggle = document.querySelector(".NavSide__toggle");
    let sidebar = document.getElementById("main-sidebar");

    if (menuToggle && sidebar) {
        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
    }
    
    // Event listener untuk menutup dropdown autocomplete saat klik di luar
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

    // Event listener untuk submit form penjadwalan (dengan validasi)
    const form = document.getElementById('formDalamModal');
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Cegah submit default

            const errorBox = document.getElementById("form-error");
            errorBox.textContent = ""; 
            
            let isValid = true;
            let errorMessage = "";

            const pengujiInputs = document.querySelectorAll('input[name="penguji_nama[]"]');
            
             // Validasi khusus untuk sidang TA
            if (isSidangTA) {
                const namaDosenList = [];
                pengujiInputs.forEach(input => {
                    const nama = input.value.trim();
                    if (nama !== "") {
                        namaDosenList.push(nama);
                    }
                });
                // Cek duplikasi nama dosen penguji
                const uniqueNamaDosen = new Set(namaDosenList);
                if (uniqueNamaDosen.size < namaDosenList.length) {
                    errorMessage = "Tidak boleh ada nama dosen penguji yang sama.";
                    isValid = false;
                }
                // Validasi nama dosen harus ada di daftar dosenData
                pengujiInputs.forEach((input, index) => {
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
            // Validasi ruangan, tanggal, jam
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
            // Jika validasi gagal, tampilkan pesan error
            if (!isValid) {
                errorBox.textContent = errorMessage;
                return;
            }
            // Submit form dengan AJAX (fetch)
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
                // Tutup modal jika berhasil
                var myModalEl = document.getElementById('penjadwalanSidangModal');
                var modal = bootstrap.Modal.getInstance(myModalEl);
                if (modal) { modal.hide(); }

                if (data.status === 'success') {
                    Swal.fire({
                        // Tampilkan notifikasi sukses dan reload halaman
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
                    // Tampilkan notifikasi gagal
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
                // Tampilkan error jika fetch gagal
                console.error('Error:', error);
                Swal.fire({
                    title: 'Oops!',
                    text: 'Terjadi kesalahan saat menghubungi server.',
                    icon: 'error'
                });
            })
                .finally(() => {
                // Aktifkan kembali tombol submit
                submitButton.disabled = false;
                submitButton.textContent = 'Ubah Penjadwalan';
            });
        });
    }
});
// Menampilkan konfirmasi hapus sidang dengan SweetAlert
function confirmDelete(idSidang) {
    Swal.fire({
        title: 'Anda Yakin?',
        text: "Data sidang ini dan semua jadwal terkait akan dihapus permanen. Aksi ini tidak dapat dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika pengguna menekan "Ya, Hapus!", panggil fungsi untuk proses penghapusan
            processDelete(idSidang);
        }
    });
}


 //Mengirim permintaan penghapusan ke server.
 //@param {number} idSidang - ID sidang yang akan dihapus.
 
function processDelete(idSidang) {
    // Buat objek FormData untuk mengirim ID
    const formData = new FormData();
    formData.append('id_sidang', idSidang);

    // Kirim permintaan menggunakan Fetch API
    fetch('proses_hapus_sidang.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Jika sukses, tampilkan notifikasi dan redirect ke daftar sidang
            Swal.fire({
                title: 'Dihapus!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                // Arahkan pengguna kembali ke halaman daftar sidang
                window.location.href = 'aDaftarSidang.php';
            });
        } else {
            // Jika gagal, tampilkan pesan error
            Swal.fire({
                title: 'Gagal!',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'Coba Lagi'
            });
        }
    })
        .catch(error => {
        // Tampilkan error jika fetch gagal
        console.error('Error:', error);
        Swal.fire('Error Jaringan', 'Tidak dapat terhubung ke server.', 'error');
    });
}