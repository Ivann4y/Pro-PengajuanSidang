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

    // --- LOGIKA UNTUK PROSES PENGIRIMAN FORM ---
    const btnKirim = document.getElementById('btnKirim');
    const confirmationKirimModalElement = document.getElementById('confirmationKirimModal');

    // Hanya tambahkan event listener jika tombol "Kirim" ada di halaman
    if (btnKirim && confirmationKirimModalElement) {
        btnKirim.addEventListener('click', function() {
            
            // Ambil nilai input saat tombol diklik
            const nilaiLaporan = document.getElementsByName('nilaiLaporan')[0].value;
            const materiPresentasi = document.getElementsByName('materiPresentasi')[0].value;
            const nilaiPenyampaian = document.getElementsByName('nilaiPenyampaian')[0].value;
            const nilaiProyek = document.getElementsByName('nilaiProyek')[0].value;
            const catatanEvaluasi = document.getElementById('catatanEvaluasi').value;

            // Sembunyikan pesan error sebelumnya
            document.getElementById('nilaiSidangErrorMessage').style.display = 'none';
            document.getElementById('catatanEvaluasiErrorMessage').style.display = 'none';

            let isValid = true;

            // Validasi apakah semua input nilai dan catatan sudah diisi
            if ([nilaiLaporan, materiPresentasi, nilaiPenyampaian, nilaiProyek].some(val => val.trim() === '')) {
                document.getElementById('nilaiSidangErrorMessage').style.display = 'block';
                isValid = false;
            }
            if (catatanEvaluasi.trim() === '') {
                document.getElementById('catatanEvaluasiErrorMessage').style.display = 'block';
                isValid = false;
            }

            // Tampilkan peringatan jika tidak valid, atau tampilkan modal konfirmasi jika valid
            if (!isValid) {
                Swal.fire({
                    title: 'Harap mengisi semua kolom nilai dan catatan!',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#4B68FB'
                });
            } else {
                const confirmationKirimModal = new bootstrap.Modal(confirmationKirimModalElement);
                confirmationKirimModal.show();
            }
        });

        // Event listener untuk tombol konfirmasi di dalam modal
        const btnKonfirmasiKirim = document.getElementById('btnKonfirmasiKirim');
        if (btnKonfirmasiKirim) {
            btnKonfirmasiKirim.addEventListener('click', function() {
                const modalInstance = bootstrap.Modal.getInstance(confirmationKirimModalElement);
                if (modalInstance) {
                    modalInstance.hide();
                }
                // Tampilkan notifikasi sukses lalu submit form
                Swal.fire({
                    title: 'Evaluasi Sidang Berhasil Dikirim!',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    document.getElementById('evaluasiForm').submit();
                });
            });
        }
    }



    
});

