// ==========================================================================
// BAGIAN 1: FUNGSI GLOBAL & INSTANCE MODAL
// ==========================================================================

let taModalInstance, semModalInstance;
let pengujiCount = 0;

/**
 * Fungsi utama untuk membuka modal penjadwalan.
 */
function openJadwalModal(rowElement) {
  const tipeSidang = rowElement.dataset.tipeSidang;

  if (tipeSidang === "Tugas Akhir") {
    if (!taModalInstance) {
      taModalInstance = new bootstrap.Modal(
        document.getElementById("penjadwalanSidangTAModal")
      );
    }
    resetAndPopulateTAModal(rowElement);
    taModalInstance.show();
  } else if (tipeSidang === "Semester") {
    if (!semModalInstance) {
      semModalInstance = new bootstrap.Modal(
        document.getElementById("penjadwalanSidangSemModal")
      );
    }
    populateSemModal(rowElement);
    semModalInstance.show();
  }
}

/**
 * Mereset dan mengisi modal Sidang TA.
 */
function resetAndPopulateTAModal(el) {
  const formTA = document.getElementById("formDalamModal-ta");
  formTA.reset();
  document.getElementById("modal_id_sidang-ta").value = el.dataset.id || "";

  document.getElementById("modal_nim-ta").value = el.dataset.kelompok || "";
  document.getElementById("modal_judul_sidang-ta").value =
    el.dataset.judul || "";
  document.getElementById("modal_pembimbing-ta").value =
    el.dataset.pembimbing || "";
  document.getElementById("modal_prodi-ta").value = el.dataset.prodi || "";
  document.getElementById("form-error-ta").textContent = "";

  const wrapper = document.getElementById("penguji-wrapper-ta");
  wrapper.innerHTML = "";
  pengujiCount = 0;
  addPenguji();
}

/**
 * Mengisi modal Sidang Semester.
 */
function populateSemModal(el) {
  const formSem = document.getElementById("formDalamModal-sem");
  formSem.reset();
  document.getElementById("modal_id_sidang-sem").value = el.dataset.id || "";

  document.getElementById("modal_nim-sem").value = el.dataset.kelompok || "";
  document.getElementById("modal_matkul-sem").value = el.dataset.judul || ""; // Note: Judul dipakai untuk matkul
  document.getElementById("modal_prodi-sem").value = el.dataset.prodi || "";
  document.getElementById("form-error-sem").textContent = "";

  const pengampuWrapper = document.getElementById("pengampu-wrapper-sem");
  pengampuWrapper.innerHTML = "";
  try {
    const pengampuList = JSON.parse(el.dataset.pengampu || "[]");
    if (pengampuList.length > 0) {
      pengampuList.forEach((nama, index) => {
        if (!nama) return;
        const pengampuIndex = index + 1;
        const pengampuHtml = `
                    <div class="form-group" id="pengampu-form-sem-${pengampuIndex}">
                        <label for="modal_pengampu-sem-${pengampuIndex}">Pengampu ${pengampuIndex}</label>
                        <div class="input-with-buttons">
                            <input type="text" id="modal_pengampu-sem-${pengampuIndex}" name="pengampu_nama[]" value="${nama}" readonly />
                            <div class="bobot-nilai-input-group">
                                <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_pengampu-sem-${pengampuIndex}')">-</button>
                                <div class="input-with-percent">
                                    <input type="number" id="modal_qty_pengampu-sem-${pengampuIndex}" name="pengampu_bobot[]" class="bobot-input-new" value="0" min="0" oninput="cleanNumberInput(this); validateTotalWeightRealtime('Semester');" />
                                    <span class="percent-sign">%</span>
                                </div>
                                <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_qty_pengampu-sem-${pengampuIndex}')">+</button>
                            </div>
                        </div>
                    </div>`;
        pengampuWrapper.insertAdjacentHTML("beforeend", pengampuHtml);
      });
    }
  } catch (e) {
    console.error("Gagal memproses data pengampu:", e);
  }
}

/**
 * Menambah field input penguji baru secara dinamis.
 */
function addPenguji() {
  pengujiCount++;
  const wrapper = document.getElementById("penguji-wrapper-ta");
  const div = document.createElement("div");
  div.className = "form-group";
  div.id = `penguji-form-ta-${pengujiCount}`;

  div.innerHTML = `
        <label for="modal_penguji-ta-${pengujiCount}">Penguji ${pengujiCount}</label>
        <div class="input-with-buttons">
            <div class="autocomplete-container">
                <input type="text" id="modal_penguji-ta-${pengujiCount}" name="penguji_nama[]" placeholder="Ketik nama dosen penguji" oninput="searchPenguji(this, ${pengujiCount})" autocomplete="off">
                <div class="autocomplete-dropdown" id="autocomplete_penguji_${pengujiCount}"></div>
            </div>
           <div class="bobot-nilai-input-group">
                <button type="button" class="btn-bobot-new" onclick="decrementValue('modal_qty_penguji-ta-${pengujiCount}')">-</button>
                <div class="input-with-percent">
                    <input type="number" id="modal_qty_penguji-ta-${pengujiCount}" name="penguji_bobot[]" class="bobot-input-new" value="0" min="0" oninput="cleanNumberInput(this); validateTotalWeightRealtime('Tugas Akhir');">
                    <span class="percent-sign">%</span>
                </div>
                <button type="button" class="btn-bobot-new" onclick="incrementValue('modal_qty_penguji-ta-${pengujiCount}')">+</button>
            </div>
            <div class="form-toggle-buttons">
                <button type="button" onclick="addPenguji()">+</button>
                <button type="button" onclick="removePenguji()">-</button>
            </div>
        </div>`;
  wrapper.appendChild(div);
  updateToggleButtonsVisibility();
}

/**
 * Menghapus field input penguji terakhir.
 */
function removePenguji() {
  if (pengujiCount > 1) {
    document.getElementById(`penguji-form-ta-${pengujiCount}`).remove();
    pengujiCount--;
    updateToggleButtonsVisibility();
  }
}

function validateTotalWeightRealtime(modalType) {
  let totalBobot = 0;
  const suffix = modalType === "Tugas Akhir" ? "ta" : "sem";
  const messageElement = document.getElementById(
    `realtime-validation-${suffix}`
  );

  if (modalType === "Tugas Akhir") {
    const modal = document.getElementById("penjadwalanSidangTAModal");
    const pembimbingInput = modal.querySelector("#modal_pembimbing_bobot-ta");
    const pengujiInputs = modal.querySelectorAll(
      'input[name="penguji_bobot[]"]'
    );

    totalBobot += parseInt(pembimbingInput.value, 10) || 0;
    pengujiInputs.forEach((input) => {
      totalBobot += parseInt(input.value, 10) || 0;
    });
  } else if (modalType === "Semester") {
    const modal = document.getElementById("penjadwalanSidangSemModal");
    const pengampuInputs = modal.querySelectorAll(
      'input[name="pengampu_bobot[]"]'
    );

    pengampuInputs.forEach((input) => {
      totalBobot += parseInt(input.value, 10) || 0;
    });
  }

  // Tampilkan atau sembunyikan pesan peringatan
  if (totalBobot > 100) {
    messageElement.textContent = `Total bobot melebihi 100% (Saat ini: ${totalBobot}%)`;
  } else {
    messageElement.textContent = ""; // Kosongkan jika sudah benar
  }
}

/**
 * Mengatur visibilitas tombol tambah/kurang.
 */
function updateToggleButtonsVisibility() {
  const allToggleButtons = document.querySelectorAll(
    "#penguji-wrapper-ta .form-toggle-buttons"
  );
  allToggleButtons.forEach((group) => (group.style.display = "none"));

  if (allToggleButtons.length > 0) {
    const lastGroup = allToggleButtons[allToggleButtons.length - 1];
    lastGroup.style.display = "inline-flex";

    const removeButton = lastGroup.querySelector(
      'button[onclick="removePenguji()"]'
    );
    if (removeButton) {
      removeButton.style.display = pengujiCount > 1 ? "block" : "none";
    }
  }
}

/**
 * Mencari dosen untuk autocomplete.
 */
function searchPenguji(inputElement, index) {
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
      item.onclick = () => selectPenguji(dosen.nama, index);
      dropdown.appendChild(item);
    });
  } else {
    dropdown.innerHTML =
      '<div class="autocomplete-item no-results">Dosen tidak ditemukan</div>';
  }
  dropdown.style.display = "block";
}

/**
 * Memilih dosen dari dropdown.
 */
function selectPenguji(namaDosen, index) {
  document.getElementById(`modal_penguji-ta-${index}`).value = namaDosen;
  document.getElementById(`autocomplete_penguji_${index}`).style.display =
    "none";
}

/**
 * Menambah nilai pada input bobot.
 */
function incrementValue(inputId) {
  const input = document.getElementById(inputId);
  if (input) {
    let currentValue = parseInt(input.value, 10) || 0;
    input.value = currentValue + 1;
  }
}

/**
 * Mengurangi nilai pada input bobot.
 */
function decrementValue(inputId) {
  const input = document.getElementById(inputId);
  if (input) {
    let val = parseInt(input.value, 10) || 0;
    if (val > (parseInt(input.min, 10) || 0)) {
      input.value = val - 1;
    }
  }
}

/**
 * FUNGSI BARU: Membersihkan nilai input dari angka nol di depan.
 */
function cleanNumberInput(inputElement) {
  if (inputElement.value) {
    const numericValue = parseInt(inputElement.value, 10);
    if (isNaN(numericValue) || numericValue < 0) {
      inputElement.value = 0;
    } else {
      inputElement.value = numericValue;
    }
  }
}

/**
 * Menangani pengiriman data form.
 */
function handleFormSubmit(event) {
  event.preventDefault();
  const form = event.target;
  const modalType = form.elements["tipe_sidang"].value;

  if (!validateForm(modalType)) {
    return;
  }

  const modalSuffix = modalType === "Tugas Akhir" ? "ta" : "sem";
  const errorBox = document.getElementById(`form-error-${modalSuffix}`);
  const submitButton = form.querySelector('button[type="submit"]');

  submitButton.disabled = true;
  submitButton.innerHTML =
    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...';
  errorBox.textContent = "";

  const formData = new FormData(form);

  fetch("../../control/admin/createPenjadwalan.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        const modalToHide =
          modalType === "Tugas Akhir" ? taModalInstance : semModalInstance;
        if (modalToHide) modalToHide.hide();

        Swal.fire({
          title: "Berhasil!",
          text: data.message,
          icon: "success",
          confirmButtonText: "OK",
        }).then(() => location.reload());
      } else {
        errorBox.textContent = data.message || "Terjadi kesalahan.";
        Swal.fire("Gagal!", data.message, "error");
      }
    })
    .catch((error) => {
      console.error("Fetch Error:", error);
      errorBox.textContent = "Tidak dapat terhubung ke server.";
      Swal.fire(
        "Koneksi Gagal",
        "Tidak dapat mengirim data ke server.",
        "error"
      );
    })
    .finally(() => {
      submitButton.disabled = false;
      submitButton.innerHTML = "Buat Penjadwalan";
    });
}

/**
 * FUNGSI KRUSIAL: Memvalidasi form sebelum dikirim.
 */
function validateForm(modalType) {
  const suffix = modalType === "Tugas Akhir" ? "ta" : "sem";
  const errorBox = document.getElementById(`form-error-${suffix}`);
  errorBox.textContent = "";

  const fieldsToValidate = [
    { id: `modal_ruangan-${suffix}`, message: "Ruangan harus diisi." },
    { id: `modal_tanggal-${suffix}`, message: "Tanggal harus dipilih." },
    { id: `modal_jam_awal-${suffix}`, message: "Jam awal harus diisi." },
    { id: `modal_jam_akhir-${suffix}`, message: "Jam akhir harus diisi." },
  ];

  for (const field of fieldsToValidate) {
    const element = document.getElementById(field.id);
    if (!element || element.value.trim() === "") {
      errorBox.textContent = field.message;
      element.focus();
      return false;
    }
  }

  const jamAwal = document.getElementById(`modal_jam_awal-${suffix}`).value;
  const jamAkhir = document.getElementById(`modal_jam_akhir-${suffix}`).value;
  if (jamAkhir <= jamAwal) {
    errorBox.textContent = "Jam akhir harus setelah jam awal.";
    return false;
  }

  if (modalType === "Tugas Akhir") {
    let totalBobot = 0;
    const pembimbingBobotInput = document.getElementById(
      "modal_pembimbing_bobot-ta"
    );
    const bobotPembimbing = parseInt(pembimbingBobotInput.value, 10);

    if (isNaN(bobotPembimbing) || bobotPembimbing <= 0) {
      errorBox.textContent = "Bobot pembimbing harus diisi (lebih dari 0).";
      pembimbingBobotInput.focus();
      return false;
    }
    totalBobot += bobotPembimbing;

    const pengujiNamaInputs = document.querySelectorAll(
      '#penjadwalanSidangTAModal input[name="penguji_nama[]"]'
    );
    const pengujiBobotInputs = document.querySelectorAll(
      '#penjadwalanSidangTAModal input[name="penguji_bobot[]"]'
    );

    for (let i = 0; i < pengujiNamaInputs.length; i++) {
      if (pengujiNamaInputs[i].value.trim() === "") {
        errorBox.textContent = `Nama Penguji ${i + 1} harus diisi.`;
        pengujiNamaInputs[i].focus();
        return false;
      }
      const bobotPenguji = parseInt(pengujiBobotInputs[i].value, 10);
      if (isNaN(bobotPenguji) || bobotPenguji <= 0) {
        errorBox.textContent = `Bobot Penguji ${
          i + 1
        } harus diisi (lebih dari 0).`;
        pengujiBobotInputs[i].focus();
        return false;
      }
      totalBobot += bobotPenguji;
    }

    if (totalBobot !== 100) {
      errorBox.textContent = `Total bobot (Pembimbing + Penguji) harus tepat 100%. Saat ini totalnya adalah ${totalBobot}%.`;
      return false;
    }
  } else if (modalType === "Semester") {
    let totalBobotSem = 0;
    const pengampuBobotInputs = document.querySelectorAll(
      '#penjadwalanSidangSemModal input[name="pengampu_bobot[]"]'
    );

    for (let i = 0; i < pengampuBobotInputs.length; i++) {
      const bobotPengampu = parseInt(pengampuBobotInputs[i].value, 10);
      if (isNaN(bobotPengampu) || bobotPengampu <= 0) {
        errorBox.textContent = `Bobot Pengampu ${
          i + 1
        } harus diisi (lebih dari 0).`;
        pengampuBobotInputs[i].focus();
        return false;
      }
      totalBobotSem += bobotPengampu;
    }

    if (totalBobotSem !== 100) {
      errorBox.textContent = `Total bobot Pengampu harus tepat 100%. Saat ini totalnya adalah ${totalBobotSem}%.`;
      return false;
    }
  }

  return true;
}

// ==========================================================================
// BAGIAN 2: SCRIPT YANG BERJALAN SETELAH HALAMAN SIAP
// ==========================================================================
document.addEventListener("DOMContentLoaded", function () {
  document
    .getElementById("formDalamModal-ta")
    ?.addEventListener("submit", handleFormSubmit);
  document
    .getElementById("formDalamModal-sem")
    ?.addEventListener("submit", handleFormSubmit);

  document.addEventListener("click", function (event) {
    const allDropdowns = document.querySelectorAll(".autocomplete-dropdown");
    const clickedInsideContainer = event.target.closest(
      ".autocomplete-container"
    );
    allDropdowns.forEach((dropdown) => {
      if (
        !clickedInsideContainer ||
        !dropdown.closest(".autocomplete-container").contains(event.target)
      ) {
        dropdown.style.display = "none";
      }
    });
  });

  const searchInput = document.getElementById("searchInput");
  const tableRows = document.querySelectorAll(
    "#adminSidangContent tr.isiTabel"
  );

  if (searchInput) {
    searchInput.addEventListener("input", function () {
      const keyword = searchInput.value.toLowerCase();
      tableRows.forEach((row) => {
        const rowText = row.innerText.toLowerCase();
        row.style.display = rowText.includes(keyword) ? "" : "none";
      });
    });
  }
});
