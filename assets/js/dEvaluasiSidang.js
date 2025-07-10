document.addEventListener('DOMContentLoaded', function() {

    // --- FUNGSI UTILITAS UNTUK SINKRONISASI INPUT NILAI ---
    // Menyinkronkan nilai antara input untuk tampilan desktop dan mobile
    function syncInputs(name1, name2) {
        const input1 = document.getElementsByName(name1)[0];
        const input2 = document.getElementsByName(name2)[0];
        
        if (input1 && input2) {
            input1.addEventListener('input', () => {
                if (document.activeElement !== input2) input2.value = input1.value;
            });
            input2.addEventListener('input', () => {
                if (document.activeElement !== input1) input1.value = input2.value;
            });
        }
    }

    // --- VALIDASI INPUT: HANYA ANGKA 0-100 ---
    document.querySelectorAll('.input-nilai').forEach(function(input) {
        input.addEventListener('input', function() {
            // Hanya izinkan angka
            this.value = this.value.replace(/[^0-9]/g, '');
            // Batasi maksimal 3 digit
            if (this.value.length > 3) this.value = this.value.slice(0, 3);
            // Hapus leading zero jika angka lebih dari 1 digit (misal: 09 menjadi 9)
            if (this.value.length > 1 && this.value.startsWith('0')) {
                this.value = this.value.replace(/^0+/, '');
            }
            // Batasi nilai maksimal 100
            if (parseInt(this.value, 10) > 100) {
                this.value = '100';
            }
        });
    });

    // --- PANGGIL FUNGSI SINKRONISASI UNTUK SETIAP PASANGAN INPUT ---
    syncInputs('nilaiLaporan', 'nilaiLaporan_v');
    syncInputs('materiPresentasi', 'materiPresentasi_v');
    syncInputs('nilaiPenyampaian', 'nilaiPenyampaian_v');
    syncInputs('nilaiProyek', 'nilaiProyek_v');

 
    // --- LOGIKA UNTUK TOGGLE SIDEBAR ---
    const menuToggle = document.querySelector(".NavSide__toggle");
    const sidebar = document.getElementById("main-sidebar");
    if (menuToggle && sidebar) {
        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };
    }

    // =================================================================
    // === LOGIKA UTAMA UNTUK TOMBOL KIRIM YANG PROAKTIF ===
    // =================================================================

      const btnKirim = document.getElementById('btnKirim');
    const form = document.getElementById('evaluasiForm');
    
    // Pastikan elemen penting ada sebelum melanjutkan
    if (!btnKirim || !form) {
        console.error('Tombol Kirim atau Form tidak ditemukan. Script dihentikan.');
        return;
    }

    // Kumpulkan semua input yang wajib diisi.
    const requiredInputs = [
        document.querySelector('input[name="n_dokumen"]'),
        document.querySelector('input[name="n_presentasi"]'),
        document.querySelector('input[name="n_tanyajawab"]'),
        document.querySelector('input[name="n_proyek"]'),
        document.getElementById('catatanEvaluasi')
    ].filter(Boolean); // .filter(Boolean) untuk menghapus elemen null jika ada yg tidak ditemukan

    /**
     * Fungsi utama untuk memeriksa semua input dan mengaktifkan/menonaktifkan tombol "Kirim".
     */
    function updateKirimButtonState() {
        // PERUBAHAN UTAMA: Cek dulu apakah form dikunci dari server.
        // Variabel 'isFormLocked' ini datang dari tag <script> di file PHP.
        if (typeof isFormLocked !== 'undefined' && isFormLocked) {
            btnKirim.classList.remove('btn-passive'); // Hapus class pasif agar jadi hijau
            btnKirim.disabled = true;                // Tapi tetap non-aktifkan
            btnKirim.textContent = 'Evaluasi Terkirim'; // Pastikan teks sesuai
            // Non-aktifkan semua input juga
            requiredInputs.forEach(input => input.readOnly = true);
            return; // Hentikan fungsi di sini, tidak perlu validasi lagi.
        }

        // Jika form tidak dikunci, lanjutkan dengan logika validasi seperti biasa.
        const isFormValid = requiredInputs.every(input => input.value.trim() !== '');

        if (isFormValid) {
            // Jika valid, buat tombol menjadi AKTIF
            btnKirim.classList.remove('btn-passive');
            btnKirim.disabled = false;
            btnKirim.title = 'Klik untuk mengirim evaluasi';
        } else {
            // Jika tidak valid, buat tombol menjadi PASIF
            btnKirim.classList.add('btn-passive');
            btnKirim.disabled = true;
            btnKirim.title = 'Harap lengkapi semua kolom nilai dan catatan evaluasi';
        }
    }

    // Tambahkan event listener ke setiap input yang wajib diisi
    requiredInputs.forEach(input => {
        if(input) { // Pastikan input tidak null
             input.addEventListener('input', updateKirimButtonState);
        }
    });

    // Jalankan pengecekan saat halaman pertama kali dimuat
    updateKirimButtonState();
    
    // --- VALIDASI INPUT: HANYA ANGKA 0-100 ---
    document.querySelectorAll('.input-nilai').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 1 && this.value.startsWith('0')) {
                this.value = this.value.replace(/^0+/, '');
            }
            if (parseInt(this.value, 10) > 100) {
                this.value = '100';
            }
        });
    });

    // --- MODAL CONFIRMATION ---
    // Logika ini sudah benar, tidak perlu diubah.
    const confirmationKirimModalElement = document.getElementById('confirmationKirimModal');
    if (confirmationKirimModalElement) {
        btnKirim.addEventListener('click', function(event) {
            event.preventDefault(); // Mencegah submit form langsung
            if (!this.disabled) { 
                const confirmationKirimModal = new bootstrap.Modal(confirmationKirimModalElement);
                confirmationKirimModal.show();
            }
        });

        const btnKonfirmasiKirim = document.getElementById('btnKonfirmasiKirim');
        if (btnKonfirmasiKirim) {
            btnKonfirmasiKirim.addEventListener('click', function() {
                form.submit();
            });
        }
    }
});