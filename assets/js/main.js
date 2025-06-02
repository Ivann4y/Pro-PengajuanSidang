// FUNGSI UNTUK ADMIN
// --- Tulis fungsi di sini ---
document.addEventListener('DOMContentLoaded', function() {

    // =================================================
    // BAGIAN 1: PENGAMBILAN ELEMEN DARI HTML
    // =================================================
    const fileInput = document.getElementById('fileInput');
    const submitBtn = document.getElementById('submitBtn');
    const revisionForm = document.getElementById('revisionForm');
    const btnKembali = document.getElementById('btnKembali');
    
    // Elemen spesifik untuk UI upload
    const initialState = document.getElementById('initial-state');
    const selectedState = document.getElementById('selected-state');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    
    // Cek apakah semua elemen penting ada
    if (!fileInput || !submitBtn || !revisionForm || !initialState || !selectedState || !fileNameDisplay) {
        console.error("Peringatan: Salah satu elemen HTML untuk fungsionalitas upload tidak ditemukan. Pastikan semua ID sudah benar.");
        return; // Hentikan script jika ada yang kurang
    }

    // =================================================
    // BAGIAN 2: LOGIKA SAAT PENGGUNA MEMILIH FILE
    // =================================================
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            // --- JIKA FILE DIPILIH ---
            const selectedFile = this.files[0];

            // Ganti ikon
            initialState.classList.add('d-none');
            selectedState.classList.remove('d-none');

            // Tampilkan nama file
            fileNameDisplay.textContent = selectedFile.name;

            // Aktifkan tombol Kirim (CSS akan otomatis mengubah stylenya)
            submitBtn.disabled = false;

        } else {
            // --- JIKA PEMILIHAN DIBATALKAN ---
            
            // Kembalikan ikon
            initialState.classList.remove('d-none');
            selectedState.classList.add('d-none');

            // Kosongkan nama file
            fileNameDisplay.textContent = '';

            // Non-aktifkan lagi tombol Kirim
            submitBtn.disabled = true;
        }
    });

    // =================================================
    // BAGIAN 3: LOGIKA SAAT TOMBOL "KIRIM" DITEKAN
    // =================================================
    revisionForm.addEventListener('submit', function(event) {
        // Selalu hentikan pengiriman form secara otomatis untuk menampilkan konfirmasi
        event.preventDefault(); 

        // Tampilkan modal konfirmasi SweetAlert2
        Swal.fire({
            title: 'Perhatian',
            text: "Apakah anda sudah yakin ingin mengupload dokumen?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754', // Warna hijau
            cancelButtonColor: '#dc3545',  // Warna merah
            confirmButtonText: 'Lanjutkan',
            cancelButtonText: 'Batalkan',
            customClass: {
                popup: 'rounded-4' // Membuat modal lebih rounded
            }
        }).then((result) => {
            // Cek jika pengguna menekan tombol "Lanjutkan"
            if (result.isConfirmed) {
                // Jika dikonfirmasi, baru kita kirim form-nya secara manual ke PHP
                revisionForm.submit();
            }
        });
    });

    // =================================================
    // BAGIAN 4: LOGIKA TOMBOL "KEMBALI"
    // =================================================
    if (btnKembali) {
        btnKembali.addEventListener('click', function() {
            history.back(); // Perintah untuk kembali ke halaman sebelumnya
        });
    }

});


// FUNGSI UNTUK DOSEN


// FUNGSI UNTUK MAHASISWA

function switchMSidang() {
  const msidang = document.getElementById("mSidangTA");
  const msidang2 = document.getElementById("mSidangSem");
  const msidangButton = document.getElementById("ddMSidang");
  const msidangmenu = document.getElementById("ddMSidangMenu");
  if (msidang && msidang2) {
    if (
      msidang.style.display === "none" ||
      getComputedStyle(msidang).display === "none"
    ) {
      msidang.style.display = "table-row-group";
      msidang2.style.display = "none";
      msidangButton.innerText = "Sidang TA";
      msidangmenu.innerText = "Sidang Semester";
    } else {
      msidang.style.display = "none";
      msidang2.style.display = "table-row-group";
      msidangButton.innerText = "Sidang Semester";
      msidangmenu.innerText = "Sidang TA";
    }
  }
}
