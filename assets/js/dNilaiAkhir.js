 document.addEventListener('DOMContentLoaded', function () {
  // Tampilkan modal konfirmasi saat halaman dibuka
  const modal = new bootstrap.Modal(document.getElementById('konfirmasiModal'));
  modal.show();
});

  document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
      new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });
 function showTooltipPensil() {
  var tooltipTrigger = document.querySelector('[data-bs-toggle="tooltip"]');
  var tooltipInstance = bootstrap.Tooltip.getInstance(tooltipTrigger) || new bootstrap.Tooltip(tooltipTrigger);
  tooltipInstance.show();
  setTimeout(function () {
    tooltipInstance.hide();
  }, 5000);
}


    let menuToggle = document.querySelector(".NavSide__toggle");
        let sidebar = document.getElementById("main-sidebar");

        menuToggle.onclick = function() {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        };

        // Sidebar Active Item Logic
        let listItems = document.querySelectorAll(".NavSide__sidebar-item");
        for (let i = 0; i < listItems.length; i++) {
            listItems[i].onclick = function() {
                if (!this.classList.contains("NavSide__sidebar-item--active")) {
                    for (let j = 0; j < listItems.length; j++) {
                        listItems[j].classList.remove("NavSide__sidebar-item--active");
                    }
                    this.classList.add("NavSide__sidebar-item--active");
                }
            };
        }

   document.querySelectorAll('.input-nilai').forEach(function(input){
  input.addEventListener('input', function() {
  this.value = this.value.replace(/[^0-9]/g, '');
  if (this.value.length > 1 && this.value.startsWith('0')) {
      this.value = this.value.replace(/^0+/, '');
    }
  if (this.value > 100 ) {
    this.value = '';
  }
    });
   });
  document.getElementById('nilaiMahasiswa').addEventListener('input', function() {
  this.value = this.value.replace(/[^A-Ea-e]/g, '').toUpperCase();
  if (!this.value || this.value.length > 1) {
    this.value = '';
  }
});
  function pindahKeHalamanDaftarSidang() {
    window.location.href = "dDaftarSidang.php";
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
        modal.hide();
      });
    } else{
    modal.hide();
    Swal.fire({
      title: 'Nilai akhir telah dikirim.',
      icon: 'success',
      confirmButtonText: 'OK',
      confirmButtonColor: '#4B68FB'
    });
  
}
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
    input.focus();
    showTooltipPensil();
  }, 300);
}
  
  function isiNilaiAkhir() {
  document.getElementById("nilaiMahasiswa").value = "A";
  document.getElementsByName("nilaiLaporan")[0].value = "90";
  document.getElementsByName("MateriPresentasi")[0].value = "85";
  document.getElementsByName("Penyampaian")[0].value = "88";
  document.getElementsByName("NilaiProyek")[0].value = "92";
  const modal = bootstrap.Modal.getInstance(document.getElementById('konfirmasiModal'));
  modal.hide();
  showTooltipPensil();
}

  function hitungRataRataDanSetNilai() {
  // Ambil semua nilai input detail penilaian
  const nilaiLaporan = parseFloat(document.getElementsByName("nilaiLaporan")[0].value) || 0;
  const materiPresentasi = parseFloat(document.getElementsByName("MateriPresentasi")[0].value) || 0;
  const penyampaian = parseFloat(document.getElementsByName("Penyampaian")[0].value) || 0;
  const nilaiProyek = parseFloat(document.getElementsByName("NilaiProyek")[0].value) || 0;

  // Hitung rata-rata
  const arr = [nilaiLaporan, materiPresentasi, penyampaian, nilaiProyek];
  // Jika ada yang kosong, jangan proses
  if (arr.some(v => v === 0)) return;

  const rataRata = (nilaiLaporan + materiPresentasi + penyampaian + nilaiProyek) / 4;

  // Set nilai mahasiswa otomatis jika rata-rata 85-100
  if (rataRata >= 85 && rataRata <= 100) {
    document.getElementById("nilaiMahasiswa").value = "A";
  } else if (rataRata >= 70 && rataRata < 85) {
    document.getElementById("nilaiMahasiswa").value = "B";
  } else if (rataRata >= 60 && rataRata < 70) {
    document.getElementById("nilaiMahasiswa").value = "C";
  } else if (rataRata >= 40 && rataRata < 60) { 
    document.getElementById("nilaiMahasiswa").value = "D";
  } else if (rataRata < 40) {
    document.getElementById("nilaiMahasiswa").value = "E";
  }
  }
  document.getElementsByName("nilaiLaporan")[0].addEventListener('input', hitungRataRataDanSetNilai);
document.getElementsByName("MateriPresentasi")[0].addEventListener('input', hitungRataRataDanSetNilai);
document.getElementsByName("Penyampaian")[0].addEventListener('input', hitungRataRataDanSetNilai);
document.getElementsByName("NilaiProyek")[0].addEventListener('input', hitungRataRataDanSetNilai);
