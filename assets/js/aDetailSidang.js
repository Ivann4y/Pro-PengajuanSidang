// FILE: aDetailSidang.js (VERSI FINAL & LENGKAP)

// Variabel global untuk instance modal.
let jadwalModalInstance;
// Variabel untuk melacak jumlah penguji yang ditambahkan secara dinamis.
let pengujiCount = 0;

// ==========================================================================
// INISIALISASI SAAT HALAMAN SIAP (DOMContentLoaded)
// ==========================================================================
document.addEventListener("DOMContentLoaded", function () {
    // --- Logika untuk Toggle Sidebar & Ikon ---
    const menuToggle = document.querySelector(".NavSide__toggle");
    const sidebar = document.getElementById("main-sidebar");
    const desktopIconsContainer = document.getElementById('desktop-icons-container');
    const mobileIconsContainer = document.getElementById('mobile-icons-container');

    // Event listener untuk tombol toggle
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => {
            menuToggle.classList.toggle("NavSide__toggle--active");
            sidebar.classList.toggle("NavSide__sidebar--active-mobile");
        });
    }

    // Fungsi untuk memindahkan ikon di layar mobile/desktop
    function handleIconPlacement() {
        if (!desktopIconsContainer || !mobileIconsContainer) return;
        const headerIcons = desktopIconsContainer.querySelector('.header-icons') || mobileIconsContainer.querySelector('.header-icons');
        if (!headerIcons) return;

        if (window.innerWidth <= 992) {
            if (!mobileIconsContainer.contains(headerIcons)) {
                mobileIconsContainer.appendChild(headerIcons);
            }
        } else {
            if (!desktopIconsContainer.contains(headerIcons)) {
                desktopIconsContainer.appendChild(headerIcons);
            }
        }
    }
    // Jalankan fungsi saat halaman dimuat dan saat ukuran window berubah
    handleIconPlacement();
    window.addEventListener('resize', handleIconPlacement);


  // 1. Inisialisasi instance modal Bootstrap sekali saja. Ini lebih aman.
  const modalElement = document.getElementById("penjadwalanSidangModal");
  if (modalElement) {
    jadwalModalInstance = new bootstrap.Modal(modalElement);
  }

  // 2. Tambahkan event listener untuk submit form modal.
  const form = document.getElementById("formDalamModal");
  if (form) {
    form.addEventListener("submit", handleFormSubmit);
  }

  const hapusBtn = document.getElementById("hapus-sidang-btn");
  if (hapusBtn) {
    hapusBtn.addEventListener("click", function () {
      const idSidang = this.dataset.id;
      confirmDelete(idSidang);
    });
  }

  // 3. Tambahkan event listener untuk klik di luar dropdown autocomplete.
  document.addEventListener("click", function (e) {
    const openDropdown = document.querySelector(
      '.autocomplete-dropdown[style*="display: block"]'
    );
    if (
      openDropdown &&
      !openDropdown.closest(".autocomplete-container").contains(e.target)
    ) {
      openDropdown.style.display = "none";
    }
  });

  // 4. Set jumlah penguji awal berdasarkan elemen yang sudah ada saat halaman dimuat.
  const wrapper = document.getElementById("penguji-wrapper");
  if (wrapper) {
    pengujiCount = wrapper.children.length;
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const navSide = document.getElementById("NavSide");
  const toggleButton = document.querySelector(".NavSide__toggle");

  // Pastikan elemennya ada sebelum menambahkan event listener
  if (navSide && toggleButton) {
    toggleButton.addEventListener("click", () => {
      // Menambahkan/menghapus class pada elemen NavSide utama
      // CSS akan menangani animasi slide-in/out berdasarkan class ini
      navSide.classList.toggle("NavSide--toggled");
    });
  }
});

// ==========================================================================
// FUNGSI-FUNGSI UTAMA (Interaksi Pengguna)
// ==========================================================================

/**
 * Fungsi untuk membuka modal "Ubah Penjadwalan".
 */
function openModal() {
  if (jadwalModalInstance) {
    const errorBox = document.getElementById("form-error");
    if (errorBox) errorBox.textContent = "";

    validateTotalWeightRealtime(); // Panggil validasi untuk menampilkan status awal.

    jadwalModalInstance.show();
  } else {
    console.error("Instance modal Bootstrap tidak ditemukan.");
    alert("Gagal membuka modal. Periksa konsol untuk error.");
  }
}

/**
 * Fungsi untuk menghapus sidang dengan konfirmasi.
 * @param {number} idSidang - ID sidang yang akan dihapus.
 */
function confirmDelete(idSidang) {
  Swal.fire({
    title: "Anda Yakin?",
    text: "Data sidang ini dan semua yang terkait akan dihapus permanen!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      const formData = new FormData();
      formData.append("id_sidang", idSidang);

      fetch("../../control/admin/proses_hapus_sidang.php", {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          Swal.fire({
            title: data.status === "success" ? "Dihapus!" : "Gagal!",
            text: data.message,
            icon: data.status,
          }).then(() => {
            if (data.status === "success") {
              window.location.href = "aDaftarSidang.php";
            }
          });
        })
        .catch((err) => {
          console.error("Error:", err);
          Swal.fire("Error", "Gagal menghubungi server.", "error");
        });
    }
  });
}

/**
 * Fungsi untuk menangani submit form modal.
 * @param {Event} event - Event object dari form submission.
 */
function handleFormSubmit(event) {
  event.preventDefault();
  const form = event.target;
  const errorBox = document.getElementById("form-error");
  errorBox.textContent = "";

   const fieldsToValidate = [
    { id: 'modal_ruangan', message: 'Ruangan harus diisi.' },
    { id: 'modal_tanggal', message: 'Tanggal harus dipilih.' },
    { id: 'modal_jam_awal', message: 'Jam awal harus diisi.' },
    { id: 'modal_jam_akhir', message: 'Jam akhir harus diisi.' },
  ];

  for (const field of fieldsToValidate) {
      const inputElement = document.getElementById(field.id);
      // Periksa apakah elemen ada dan nilainya kosong
      if (inputElement && inputElement.value.trim() === '') {
          errorBox.textContent = field.message;
          return; // Menghentikan eksekusi fungsi jika ada field yang kosong
      }
  }

  // Validasi total bobot sebelum mengirim
  let totalBobot = 0;
  if (isSidangTA) {
    document
      .querySelectorAll('input[name="pembimbing_bobot[]"]')
      .forEach((input) => {
        totalBobot += parseInt(input.value, 10) || 0;
      });
    document
      .querySelectorAll('input[name="penguji_bobot[]"]')
      .forEach((input) => {
        totalBobot += parseInt(input.value, 10) || 0;
      });
  } else {
    document
      .querySelectorAll('input[name="pengampu_bobot[]"]')
      .forEach((input) => {
        totalBobot += parseInt(input.value, 10) || 0;
      });
  }

  if (totalBobot !== 100) {
    errorBox.textContent = `Gagal: Total bobot harus tepat 100%. Total saat ini: ${totalBobot}%.`;
    return;
  }

  // Proses pengiriman data
  const formData = new FormData(form);
  const submitButton = form.querySelector('button[type="submit"]');
  submitButton.disabled = true;
  submitButton.textContent = "Menyimpan...";

  fetch("../../control/admin/proses_ubah_jadwal.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (jadwalModalInstance) jadwalModalInstance.hide();
      Swal.fire({
        title: data.status === "success" ? "Berhasil!" : "Gagal!",
        text: data.message,
        icon: data.status,
      }).then(() => {
        if (data.status === "success") location.reload();
      });
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire("Error", "Gagal menghubungi server.", "error");
    })
    .finally(() => {
      submitButton.disabled = false;
      submitButton.textContent = "Ubah Penjadwalan";
    });
}

// ==========================================================================
// FUNGSI-FUNGSI PEMBANTU (HELPER)
// ==========================================================================

function addPenguji() {
  const wrapper = document.getElementById("penguji-wrapper");
  if (!wrapper) return;

  pengujiCount++; // Increment counter global
  const newIndex = pengujiCount;

  const newPengujiDiv = document.createElement("div");
  newPengujiDiv.className = "form-group";
  newPengujiDiv.id = `penguji-form-${newIndex}`;

  newPengujiDiv.innerHTML = `
        <label for="modal_penguji${newIndex}">Penguji ${newIndex}</label>
        <div class="input-with-buttons">
            <div class="autocomplete-container">
                <input type="text" id="modal_penguji${newIndex}" name="penguji_nama[]" placeholder="Ketik nama dosen" oninput="searchDosen(this, ${newIndex})" autocomplete="off">
                <div class="autocomplete-dropdown" id="autocomplete_penguji_${newIndex}"></div>
            </div>
            <div class="input-with-percent">
                <input type="number" name="penguji_bobot[]" class="form-control-bobot" placeholder="Bobot" min="0" oninput="cleanNumberInput(this); validateTotalWeightRealtime();">
                <span class="percent-sign">%</span>
            </div>
        </div>
    `;
  wrapper.appendChild(newPengujiDiv);
}

function removePenguji() {
  const wrapper = document.getElementById("penguji-wrapper");
  if (wrapper && wrapper.children.length > 1) {
    // Minimal harus ada 1 penguji
    wrapper.lastElementChild.remove();
    pengujiCount--;
    validateTotalWeightRealtime();
  }
}

function searchDosen(inputElement, index) {
  const query = inputElement.value.toLowerCase().trim();
  const dropdown = document.getElementById(`autocomplete_penguji_${index}`);
  if (query.length < 1) {
    dropdown.style.display = "none";
    return;
  }

  const filteredDosen = dosenData.filter((dosen) =>
    dosen.nama.toLowerCase().includes(query)
  );
  dropdown.innerHTML = "";

  if (filteredDosen.length > 0) {
    filteredDosen.forEach((dosen) => {
      const item = document.createElement("div");
      item.className = "autocomplete-item";
      item.textContent = dosen.nama;
      item.addEventListener("click", () => selectDosen(dosen.nama, index));
      dropdown.appendChild(item);
    });
  } else {
    dropdown.innerHTML =
      '<div class="autocomplete-item">Dosen tidak ditemukan</div>';
  }
  dropdown.style.display = "block";
}

function selectDosen(namaDosen, index) {
  document.getElementById(`modal_penguji${index}`).value = namaDosen;
  document.getElementById(`autocomplete_penguji_${index}`).style.display =
    "none";
}

function cleanNumberInput(inputElement) {
  if (inputElement.value) {
    const numericValue = parseInt(inputElement.value, 10);
    inputElement.value =
      isNaN(numericValue) || numericValue < 0 ? 0 : numericValue;
  }
}

function validateTotalWeightRealtime() {
  let totalBobot = 0;
  const messageElement = document.getElementById("realtime-validation-detail");
  if (!messageElement) return;

  if (isSidangTA) {
    document
      .querySelectorAll('input[name="pembimbing_bobot[]"]')
      .forEach((input) => {
        totalBobot += parseInt(input.value, 10) || 0;
      });
    document
      .querySelectorAll('input[name="penguji_bobot[]"]')
      .forEach((input) => {
        totalBobot += parseInt(input.value, 10) || 0;
      });
  } else {
    document
      .querySelectorAll('input[name="pengampu_bobot[]"]')
      .forEach((input) => {
        totalBobot += parseInt(input.value, 10) || 0;
      });
  }

  if (totalBobot > 100) {
    messageElement.textContent = `Peringatan: Total bobot melebihi 100% (Saat ini: ${totalBobot}%)`;
  } else {
    messageElement.textContent = "";
  }
}
