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

    // 1. Definisikan semua elemen yang diperlukan untuk validasi
    const btnKirim = document.getElementById('btnKirim');
    const confirmationKirimModalElement = document.getElementById('confirmationKirimModal');
    
    // Pastikan kita punya tombol sebelum melanjutkan
    if (!btnKirim) {
        console.warn('Tombol dengan ID "btnKirim" tidak ditemukan. Fungsi tombol pasif tidak akan berjalan.');
        return; // Hentikan eksekusi script jika tombol tidak ada
    }

    // Kumpulkan semua input yang wajib diisi.
    // PENTING: Nama input (n_dokumen, n_presentasi, dll.) harus sesuai dengan atribut 'name' di HTML Anda.
    const requiredInputs = [
        document.querySelector('input[name="n_dokumen"]'),
        document.querySelector('input[name="n_presentasi"]'),
        document.querySelector('input[name="n_tanyajawab"]'),
        document.querySelector('input[name="n_proyek"]'),
        document.getElementById('catatanEvaluasi')
    ]

    /**
     * Fungsi utama untuk memeriksa semua input dan mengaktifkan/menonaktifkan tombol "Kirim".
     */
    function updateKirimButtonState() {
        // Cek apakah SEMUA input yang wajib diisi memiliki nilai (tidak kosong)
        const isFormValid = requiredInputs.every(input => input.value.trim() !== '');

        if (isFormValid) {
            // Jika valid, buat tombol menjadi AKTIF
            btnKirim.classList.remove('btn-passive');
            btnKirim.title = 'Klik untuk mengirim evaluasi';
        } else {
            // Jika tidak valid, buat tombol menjadi PASIF
            btnKirim.classList.add('btn-passive');
            btnKirim.title = 'Harap lengkapi semua kolom nilai dan catatan evaluasi';
        }
    }

    // 2. Tambahkan event listener ke setiap input yang wajib diisi agar tombol diperbarui secara real-time
    requiredInputs.forEach(input => {
        input.addEventListener('input', updateKirimButtonState);
    });

    // 3. Atur status awal tombol saat halaman pertama kali dimuat
    // Ini penting jika form sudah terisi data dari database
    updateKirimButtonState();

    // 4. Modifikasi event klik pada tombol "Kirim"
    // Event ini sekarang hanya akan berjalan jika tombol dalam keadaan aktif
// ... (di dalam DOMContentLoaded)
    if (confirmationKirimModalElement && btnKirim) {
        btnKirim.addEventListener('click', function() {
            // Cek sekali lagi jika tombol tidak disabled sebelum membuka modal
            if (!this.disabled) { 
                const confirmationKirimModal = new bootstrap.Modal(confirmationKirimModalElement);
                confirmationKirimModal.show();
            }
        });

        // Event listener untuk tombol konfirmasi di dalam modal (sudah ada di HTML Anda)
        const btnKonfirmasiKirim = document.getElementById('btnKonfirmasiKirim');
        if (btnKonfirmasiKirim) {
            btnKonfirmasiKirim.addEventListener('click', function() {
                document.getElementById('evaluasiForm').submit();
            });
        }
    }


    // --- VALIDASI INPUT: HANYA ANGKA 0-100 (opsional, bisa digabung) ---
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

});
        