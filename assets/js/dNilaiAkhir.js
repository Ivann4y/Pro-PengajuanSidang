document.addEventListener('DOMContentLoaded', function () {
  
  // --- 1. INISIALISASI DASAR ---
  // Tampilkan modal konfirmasi awal
  const modal = new bootstrap.Modal(document.getElementById('konfirmasiModal'));
  modal.show();

  // Aktifkan semua tooltip di halaman
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // --- 2. LOGIKA SIDEBAR ---
  const menuToggle = document.querySelector(".NavSide__toggle");
  const sidebar = document.getElementById("main-sidebar");
  if (menuToggle && sidebar) {
    menuToggle.onclick = function() {
        // Toggle untuk mobile view (jika Anda punya CSS untuk ini)
        sidebar.classList.toggle("NavSide__sidebar--active-mobile"); 
        
        // Toggle untuk ikon list/x
        menuToggle.classList.toggle("NavSide__toggle--active"); 
    };
  }

  const listItems = document.querySelectorAll(".NavSide__sidebar-item");
  listItems.forEach(item => {
    item.onclick = function() {
      listItems.forEach(li => li.classList.remove("NavSide__sidebar-item--active"));
      this.classList.add("NavSide__sidebar-item--active");
    };
  });

  // --- 3. LOGIKA FORM NILAI ---
  const detailInputs = document.querySelectorAll('.input-nilai');
  
  // Pasang listener untuk validasi dan kalkulasi
  detailInputs.forEach(function(input) {
    // Listener untuk KALKULASI OTOMATIS
    input.addEventListener('input', hitungRataRataDanSetNilai);
    
    // Listener untuk VALIDASI ANGKA
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
  
  // Sinkronisasi input antara tampilan desktop dan mobile
  syncInputs('nilaiLaporan', 'nilaiLaporan_v');
  syncInputs('MateriPresentasi', 'MateriPresentasi_v');
  syncInputs('Penyampaian', 'Penyampaian_v');
  syncInputs('NilaiProyek', 'NilaiProyek_v');

}); // AKHIR DARI DOMContentLoaded

// --- FUNGSI-FUNGSI GLOBAL (DI LUAR DOMContentLoaded) ---

function showTooltipPensil() {
  const tooltipTrigger = document.querySelector('[data-bs-toggle="tooltip"]');
  if (tooltipTrigger) {
    const tooltipInstance = bootstrap.Tooltip.getInstance(tooltipTrigger) || new bootstrap.Tooltip(tooltipTrigger);
    tooltipInstance.show();
    setTimeout(() => tooltipInstance.hide(), 5000);
  }
}

function syncInputs(name1, name2) {
  const input1 = document.getElementsByName(name1)[0];
  const input2 = document.getElementsByName(name2)[0];
  if (input1 && input2) {
    input1.addEventListener('input', () => { input2.value = input1.value; });
    input2.addEventListener('input', () => { input1.value = input2.value; });
  }
}

function pindahKeHalamanDaftarSidang() {
  window.location.href = "dDaftarSidang.php";
}

function bukaKonfirmasiModal() {
  const modal = new bootstrap.Modal(document.getElementById('konfirmasiModal'));
  modal.show();
}

function TutupKonfirmasiModal() {
  const modal = bootstrap.Modal.getInstance(document.getElementById('konfirmasiModal'));
  modal.hide();
  setTimeout(() => {
    const input = document.getElementById("nilaiMahasiswa");
    input.focus(); // Ini akan error karena inputnya readonly, kita hapus saja atau biarkan
    showTooltipPensil();
  }, 300);
}

function isiNilaiAkhir() {
  // Mengisi nilai ke kedua set input agar sinkron
  document.getElementsByName("nilaiLaporan")[0].value = "90";
  document.getElementsByName("nilaiLaporan_v")[0].value = "90";
  document.getElementsByName("MateriPresentasi")[0].value = "85";
  document.getElementsByName("MateriPresentasi_v")[0].value = "85";
  document.getElementsByName("Penyampaian")[0].value = "88";
  document.getElementsByName("Penyampaian_v")[0].value = "88";
  document.getElementsByName("NilaiProyek")[0].value = "92";
  document.getElementsByName("NilaiProyek_v")[0].value = "92";

  // PENTING: Panggil fungsi kalkulasi secara manual setelah nilai diisi
  hitungRataRataDanSetNilai(); 

  const modal = bootstrap.Modal.getInstance(document.getElementById('konfirmasiModal'));
  modal.hide();
  showTooltipPensil();
}

// FUNGSI PERHITUNGAN YANG DIPERBAIKI DAN DISEDERHANAKAN
function hitungRataRataDanSetNilai() {
  // Cukup ambil nilai dari input desktop, karena sudah sinkron
  const nilaiLaporanEl = document.getElementsByName("nilaiLaporan")[0];
  const materiPresentasiEl = document.getElementsByName("MateriPresentasi")[0];
  const penyampaianEl = document.getElementsByName("Penyampaian")[0];
  const nilaiProyekEl = document.getElementsByName("NilaiProyek")[0];

  const inputs = [
    nilaiLaporanEl.value,
    materiPresentasiEl.value,
    penyampaianEl.value,
    nilaiProyekEl.value
  ];

  // Cek apakah ada input yang masih kosong. Jika ya, kosongkan nilai akhir dan berhenti.
  if (inputs.some(val => val === "")) {
    document.getElementById("nilaiMahasiswa").value = ""; 
    return;
  }
  
  // Konversi ke angka setelah yakin semua terisi
  const nilaiLaporan = parseFloat(nilaiLaporanEl.value);
  const materiPresentasi = parseFloat(materiPresentasiEl.value);
  const penyampaian = parseFloat(penyampaianEl.value);
  const nilaiProyek = parseFloat(nilaiProyekEl.value);
  
  const rataRata = (nilaiLaporan + materiPresentasi + penyampaian + nilaiProyek) / 4;

  let nilaiHuruf = "";
  if (rataRata >= 85) { nilaiHuruf = "A"; } 
  else if (rataRata >= 70) { nilaiHuruf = "B"; } 
  else if (rataRata >= 60) { nilaiHuruf = "C"; } 
  else if (rataRata >= 40) { nilaiHuruf = "D"; } 
  else { nilaiHuruf = "E"; }
  
  document.getElementById("nilaiMahasiswa").value = nilaiHuruf;
}

function bukaKonfirmasiModalKirim() {
  const modal = new bootstrap.Modal(document.getElementById('konfirmasiModalKirim'));
  modal.show();
}

function kirimNilaiAkhir() {
  const modal = bootstrap.Modal.getInstance(document.getElementById('konfirmasiModalKirim'));
  const nilaiMahasiswa = document.getElementById("nilaiMahasiswa").value;
  const nilaiLaporan = document.getElementsByName("nilaiLaporan")[0].value;
  const materiPresentasi = document.getElementsByName("MateriPresentasi")[0].value;
  const penyampaian = document.getElementsByName("Penyampaian")[0].value;
  const nilaiProyek = document.getElementsByName("NilaiProyek")[0].value;

  if (nilaiMahasiswa === "" || nilaiLaporan === "" || materiPresentasi === "" || penyampaian === "" || nilaiProyek === "") {
    Swal.fire({
      title: 'Semua nilai harus diisi sebelum mengirim!',
      icon: 'error',
      confirmButtonText: 'OK',
      confirmButtonColor: '#4B68FB'
    }).then(() => {
      if(modal) modal.hide();
    });
  } else {
    if(modal) modal.hide();
    Swal.fire({
      title: 'Nilai akhir telah dikirim.',
      icon: 'success',
      confirmButtonText: 'OK',
      confirmButtonColor: '#4B68FB'
    });
  }
}