let kelompokModalInstance;
let anggotaCount = 1;
let currentProdi = "";
let mahasiswaData = [];
let kelompokData = [];
let dosenData = [];
let dosenCount = 1; // Separate counter for Dosen

document.addEventListener("DOMContentLoaded", function () {
  const kelompokModalEl = document.getElementById("kelompokModal");
  if (kelompokModalEl) {
    if (typeof bootstrap !== "undefined") {
      kelompokModalInstance = new bootstrap.Modal(kelompokModalEl);
    } else {
      console.error("Bootstrap is not loaded");
    }
    kelompokModalEl.addEventListener("hidden.bs.modal", resetKelompokForm);
  }

  const kelompokForm = document.getElementById("kelompokForm");
  if (kelompokForm) {
    kelompokForm.addEventListener("submit", handleKelompokFormSubmit);
  }
  fetchMahasiswaData();
  fetchDosenData();
  updateToggleButtonsVisibility();
});

async function fetchMahasiswaData() {
  try {
    const response = await fetch("../../control/get_mahasiswa.php");
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    mahasiswaData = await response.json();
  } catch (error) {
    console.error("Error fetching mahasiswa data:", error);
    alert("Gagal memuat data mahasiswa untuk autocomplete.");
  }
}

async function fetchDosenData() {
  try {
    const response = await fetch("../../control/get_dosen.php");
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    dosenData = await response.json();
    console.log("Loaded dosenData:", dosenData);
  } catch (error) {
    console.error("Error fetching dosen data:", error);
    alert("Gagal memuat data dosen untuk autocomplete.");
  }
}

function openKelompokModal() {
  if (!kelompokModalInstance) {
    console.error("Modal instance not initialized");
    alert("Modal tidak dapat dibuka. Silakan refresh halaman.");
    return;
  }
  resetKelompokForm();
  setNextKelompokId();
  switchTab("tambah");
  loadKelompokList();
  kelompokModalInstance.show();
}

async function setNextKelompokId() {
  try {
    const response = await fetch("../../control/get_next_kelompok_id.php");
    if (!response.ok) throw new Error("Failed to fetch next Kelompok ID");
    const data = await response.json();
    document.getElementById("kelompok_id").value = data.next_id;
  } catch (e) {
    document.getElementById("kelompok_id").value = "";
  }
}

function switchTab(tabName) {
  const tabs = document.querySelectorAll(".modal-tab");
  const tabContents = document.querySelectorAll(".modal-tab-content");
  tabs.forEach((tab) => tab.classList.remove("active"));
  tabContents.forEach((content) => content.classList.remove("active"));
  if (tabName === "tambah") {
    tabs[0].classList.add("active");
    document.getElementById("tambah-tab").classList.add("active");
  } else {
    tabs[1].classList.add("active");
    document.getElementById("daftar-tab").classList.add("active");
    loadKelompokList();
  }
}

function filterMahasiswaByProdi() {
  const prodiSelect = document.getElementById("kelompok_prodi");
  currentProdi = prodiSelect.value;
  resetAnggotaInputs();
}

function searchMahasiswa(input, anggotaIndex) {
  const query = input.value.trim().toLowerCase();
  const dropdown = document.getElementById(`autocomplete_${anggotaIndex}`);
  const namaDisplay = document.getElementById(`anggota_nama_${anggotaIndex}`);

  if (!currentProdi || currentProdi.trim() === "") {
    dropdown.innerHTML =
      '<div class="autocomplete-item">Pilih Prodi terlebih dahulu</div>';
    dropdown.style.display = "block";
    return;
  }

  const normalizedProdi = currentProdi.trim().toLowerCase();
  const rplAliases = [
    "rekayasa perangkat lunak",
    "trpl",
    "rpl",
    "teknologi rekayasa perangkat lunak",
  ];

  let filteredMahasiswa = mahasiswaData.filter((mhs) => {
    const prodi = mhs.prodi ? mhs.prodi.trim().toLowerCase() : "";
    if (rplAliases.includes(normalizedProdi)) {
      return rplAliases.includes(prodi);
    } else {
      return prodi === normalizedProdi;
    }
  });

  if (query.length > 0) {
    filteredMahasiswa = filteredMahasiswa.filter(
      (mhs) =>
        String(mhs.nim).toLowerCase().includes(query) ||
        mhs.nama_mhs.toLowerCase().includes(query)
    );
  }

  const selectedNIMs = Array.from(
    document.querySelectorAll('input[name="anggota_nim[]"]')
  )
    .map((inp) => inp.value.trim())
    .filter((nim) => nim !== "" && nim !== input.value.trim());

  const finalFilteredMahasiswa = filteredMahasiswa.filter(
    (mhs) => !selectedNIMs.includes(String(mhs.nim))
  );

  if (finalFilteredMahasiswa.length > 0) {
    dropdown.innerHTML = "";
    finalFilteredMahasiswa.forEach((mhs, index) => {
      const item = document.createElement("div");
      item.className = "autocomplete-item";
      item.dataset.nim = mhs.nim;
      item.dataset.nama = mhs.nama_mhs;
      item.dataset.index = index;
      item.innerHTML = `<div class="nim">${mhs.nim}</div><div class="nama">${mhs.nama_mhs}</div>`;
      item.onclick = () => selectMahasiswa(mhs, anggotaIndex);
      dropdown.appendChild(item);
    });
    dropdown.style.display = "block";
  } else {
    dropdown.innerHTML = '<div class="autocomplete-item">Tidak ada hasil</div>';
    dropdown.style.display = "block";
  }
}

function selectMahasiswa(mahasiswa, anggotaIndex) {
  document.getElementById(`anggota_nim_${anggotaIndex}`).value = mahasiswa.nim;
  document.getElementById(`anggota_nama_${anggotaIndex}`).textContent =
    mahasiswa.nama_mhs;
  document.getElementById(`autocomplete_${anggotaIndex}`).style.display =
    "none";
}

function addAnggota() {
  anggotaCount++;
  const wrapper = document.getElementById("anggota-wrapper");
  const div = document.createElement("div");
  div.className = "anggota-form-group";
  div.id = "anggota-form-" + anggotaCount;
  div.innerHTML = `
        <label for="anggota_nim_${anggotaCount}">Anggota ${anggotaCount}:</label>
        <div class="anggota-input-group">
            <div class="input-container">
                <input type="text" id="anggota_nim_${anggotaCount}" name="anggota_nim[]" placeholder="Masukkan NIM atau nama" oninput="searchMahasiswa(this, ${anggotaCount})" />
                <div class="autocomplete-dropdown" id="autocomplete_${anggotaCount}" style="display: none;"></div>
            </div>
            <div class="anggota-nama-display" id="anggota_nama_${anggotaCount}">Nama akan muncul otomatis</div>
            <div class="form-toggle-buttons">
                <button type="button" onclick="addAnggota()">+</button>
                <button type="button" onclick="removeAnggota()">-</button>
            </div>
        </div>`;
  wrapper.appendChild(div);
  updateToggleButtonsVisibility();
}

function removeAnggota() {
  if (anggotaCount > 1) {
    document.getElementById("anggota-form-" + anggotaCount).remove();
    anggotaCount--;
  }
  updateToggleButtonsVisibility();
}

function resetAnggotaInputs() {
  document.getElementById("anggota-wrapper").innerHTML = `
        <div class="anggota-form-group" id="anggota-form-1">
            <label for="anggota_nim_1">Anggota 1:</label>
            <div class="anggota-input-group">
                <div class="input-container">
                    <input type="text" id="anggota_nim_1" name="anggota_nim[]" placeholder="Masukkan NIM atau nama" oninput="searchMahasiswa(this, 1)" />
                    <div class="autocomplete-dropdown" id="autocomplete_1" style="display: none;"></div>
                </div>
                <div class="anggota-nama-display" id="anggota_nama_1">Nama akan muncul otomatis</div>
                <div class="form-toggle-buttons">
                    <button type="button" onclick="addAnggota()">+</button>
                    <button type="button" onclick="removeAnggota()" style="display: none;">-</button>
                </div>
            </div>
        </div>`;
  anggotaCount = 1;
  updateToggleButtonsVisibility();
}

function resetKelompokForm() {
  document.getElementById("kelompokForm").reset();
  document.getElementById("kelompok_prodi").value = "";
  resetAnggotaInputs();
  updateToggleButtonsVisibility();
}

async function loadKelompokList() {
  const container = document.getElementById("kelompok-list-container");
  container.innerHTML =
    '<p class="text-center text-muted">Memuat daftar kelompok...</p>';
  try {
    const response = await fetch("../../control/get_kelompok_list.php");
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    kelompokData = await response.json();
    if (kelompokData.length === 0) {
      container.innerHTML =
        '<p class="text-center text-muted">Belum ada kelompok yang dibuat.</p>';
      return;
    }
    container.innerHTML = "";
    kelompokData.forEach((kelompok) => {
      const anggotaList = kelompok.anggota
        .map((angg) => `${angg.nim} - ${angg.nama_mhs}`)
        .join("<br>");
      const kelompokItem = document.createElement("div");
      kelompokItem.className = "kelompok-list-item";
      kelompokItem.innerHTML = `
        <div class="kelompok-list-header d-flex justify-content-between align-items-center">
          <div>
            <div class="kelompok-list-title">${kelompok.id_kelompok}</div>
            <div class="kelompok-list-prodi">${
              kelompok.prodi || "Tidak ada prodi"
            }</div>
          </div>
          <button class="btn btn-link text-danger p-0 ms-2" title="Hapus Kelompok" onclick="deleteKelompok(${
            kelompok.id_kelompok
          }, this)">
            <i class="bi bi-trash-fill"></i>
          </button>
        </div>
        <div class="kelompok-list-anggota">
          <strong>Anggota:</strong><br>${anggotaList}
        </div>`;
      container.appendChild(kelompokItem);
    });
  } catch (error) {
    console.error("Error fetching kelompok data:", error);
    container.innerHTML =
      '<p class="text-center text-danger">Gagal memuat daftar kelompok.</p>';
  }
}

async function handleKelompokFormSubmit(event) {
  event.preventDefault();
  if (!validateKelompokForm()) return;
  const formData = new FormData(document.getElementById("kelompokForm"));
  try {
    const response = await fetch("../../control/kelompok_create.php", {
      method: "POST",
      body: formData,
    });
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const result = await response.json();
    if (result.success) {
      alert(result.message);
      resetKelompokForm();
      kelompokModalInstance.hide();
      window.location.reload();
    } else {
      alert("Error: " + result.message);
    }
  } catch (error) {
    console.error("Error creating kelompok:", error);
    alert("Terjadi kesalahan saat membuat kelompok.");
  }
}

function validateKelompokForm() {
  let isValid = true;
  document
    .querySelectorAll(".error-message")
    .forEach((el) => (el.style.display = "none"));
  document
    .querySelectorAll(".is-invalid")
    .forEach((el) => el.classList.remove("is-invalid"));

  const prodi = document.getElementById("kelompok_prodi").value;
  if (!prodi) {
    showError("kelompok_prodi", "Pilih Prodi terlebih dahulu!");
    isValid = false;
  }
  let hasAnggota = false;
  const selectedNIMs = new Set();
  const nimInputs = document.querySelectorAll('input[name="anggota_nim[]"]');
  for (const nimInput of nimInputs) {
    const nimValue = nimInput.value.trim();
    if (nimValue !== "") {
      const foundMahasiswa = mahasiswaData.find(
        (mhs) => String(mhs.nim) === nimValue
      );
      if (!foundMahasiswa) {
        showError(nimInput.id, `NIM ${nimValue} tidak ditemukan.`);
        isValid = false;
      }
      if (selectedNIMs.has(nimValue)) {
        showError(nimInput.id, `NIM ${nimValue} sudah ditambahkan.`);
        isValid = false;
      }
      selectedNIMs.add(nimValue);
      hasAnggota = true;
    }
  }
  if (!hasAnggota) {
    showError("anggota-wrapper", "Minimal harus ada satu anggota!");
    isValid = false;
  }
  return isValid;
}

function showError(fieldId, message) {
  let field = document.getElementById(fieldId);
  let error = document.createElement("div");
  error.className = "error-message";
  error.style.color = "red";
  error.style.fontSize = "0.9em";
  error.style.marginTop = "4px";
  error.textContent = message;
  if (field) {
    field.classList.add("is-invalid");
    let next = field.nextElementSibling;
    while (next && next.classList && next.classList.contains("error-message")) {
      let toRemove = next;
      next = next.nextElementSibling;
      toRemove.remove();
    }
    field.parentNode.insertBefore(error, field.nextSibling);
  } else {
    document.body.insertBefore(error, document.body.firstChild);
  }
}

document.addEventListener("click", function (event) {
  const dropdowns = document.querySelectorAll(".autocomplete-dropdown");
  dropdowns.forEach((dropdown) => {
    if (
      !dropdown.contains(event.target) &&
      !event.target.matches('input[name="anggota_nim[]"]') &&
      !event.target.matches('input[name="dosen_pembimbing[]"]')
    ) {
      dropdown.style.display = "none";
    }
  });
});

function searchDosen(input, index) {
  const query = input.value.trim().toLowerCase();
  const dropdown = document.getElementById(`autocomplete_dosen_${index}`);
  const namaDisplay = document.getElementById(`dosen_nama_display_${index}`);

  if (query.length === 0) {
    dropdown.style.display = "none";
    namaDisplay.textContent = "Nama dosen akan muncul otomatis";
    document.getElementById(`dosen_nomor_hidden_${index}`).value = "";
    return;
  }

  let filteredDosen = dosenData;
  if (currentProdi && currentProdi.trim() !== "") {
    const normalizedProdi = currentProdi.trim().toLowerCase();
    const rplAliases = [
      "rekayasa perangkat lunak",
      "trpl",
      "rpl",
      "teknologi rekayasa perangkat lunak",
    ];
    filteredDosen = dosenData.filter((dosen) => {
      const prodi = dosen.prodi ? dosen.prodi.trim().toLowerCase() : "";
      if (rplAliases.includes(normalizedProdi)) {
        return rplAliases.includes(prodi);
      } else {
        return prodi === normalizedProdi;
      }
    });
  }

  filteredDosen = filteredDosen.filter(
    (dosen) =>
      String(dosen.nomor_dosen).toLowerCase().includes(query) ||
      dosen.nama_dosen.toLowerCase().includes(query)
  );

  const selectedDosenNIPs = Array.from(
    document.querySelectorAll('input[name="dosen_nomor_hidden[]"]')
  )
    .map((inp) => inp.value.trim())
    .filter(
      (nip) =>
        nip !== "" &&
        nip !==
          document.getElementById(`dosen_nomor_hidden_${index}`).value.trim()
    );

  const finalFilteredDosen = filteredDosen.filter(
    (dosen) => !selectedDosenNIPs.includes(String(dosen.nomor_dosen))
  );

  if (finalFilteredDosen.length > 0) {
    dropdown.innerHTML = "";
    finalFilteredDosen.forEach((dosen, dropdownIndex) => {
      const item = document.createElement("div");
      item.className = "autocomplete-item";
      item.dataset.nomor = dosen.nomor_dosen;
      item.dataset.nama = dosen.nama_dosen;
      item.dataset.index = dropdownIndex;
      item.innerHTML = `<div class="nim">${dosen.nomor_dosen}</div><div class="nama">${dosen.nama_dosen}</div>`;
      item.onclick = () => selectDosen(dosen, index);
      dropdown.appendChild(item);
    });
    dropdown.style.display = "block";
  } else {
    dropdown.innerHTML = '<div class="autocomplete-item">Tidak ada hasil</div>';
    dropdown.style.display = "block";
  }
}

function selectDosen(dosen, index) {
  document.getElementById(`dosen_pembimbing_${index}`).value =
    dosen.nomor_dosen;
  document.getElementById(`dosen_nama_display_${index}`).textContent =
    dosen.nama_dosen;
  document.getElementById(`dosen_nomor_hidden_${index}`).value =
    dosen.nomor_dosen;
  document.getElementById(`autocomplete_dosen_${index}`).style.display = "none";
}

function addDosen() {
  dosenCount++;
  const wrapper = document.getElementById("dosen-wrapper");
  const div = document.createElement("div");
  div.className = "anggota-form-group";
  div.id = "dosen-form-" + dosenCount;
  div.innerHTML = `
        <label for="dosen_pembimbing_${dosenCount}">Dosen Pembimbing ${dosenCount}:</label>
        <div class="anggota-input-group">
            <div class="input-container">
                <input type="text" id="dosen_pembimbing_${dosenCount}" name="dosen_pembimbing[]" placeholder="Masukkan NIP atau nama dosen" autocomplete="off" oninput="searchDosen(this, ${dosenCount})" />
                <div class="autocomplete-dropdown" id="autocomplete_dosen_${dosenCount}" style="display: none;"></div>
            </div>
            <div class="anggota-nama-display" id="dosen_nama_display_${dosenCount}">Nama akan muncul otomatis</div>
            <div class="form-toggle-buttons">
                <button type="button" onclick="addDosen()">+</button>
                <button type="button" onclick="removeDosen()">-</button>
            </div>
        </div>
        <input type="hidden" id="dosen_nomor_hidden_${dosenCount}" name="dosen_nomor_hidden[]" />
    `;
  wrapper.appendChild(div);
  updateToggleButtonsVisibility();
}

function removeDosen() {
  if (dosenCount > 1) {
    document.getElementById("dosen-form-" + dosenCount).remove();
    dosenCount--;
  }
  updateToggleButtonsVisibility();
}

function updateToggleButtonsVisibility() {
  // Mahasiswa
  const mhsToggleButtons = document.querySelectorAll(
    "#anggota-wrapper .form-toggle-buttons"
  );
  mhsToggleButtons.forEach((btnGroup, index) => {
    if (index === mhsToggleButtons.length - 1) {
      btnGroup.style.display = "inline-flex";
      const removeBtn = btnGroup.querySelector(
        'button[onclick="removeAnggota()"]'
      );
      if (removeBtn) {
        removeBtn.style.display = anggotaCount <= 1 ? "none" : "block";
      }
    } else {
      btnGroup.style.display = "none";
    }
  });

  // Dosen
  const dosenToggleButtons = document.querySelectorAll(
    "#dosen-wrapper .form-toggle-buttons"
  );
  dosenToggleButtons.forEach((btnGroup, index) => {
    if (index === dosenToggleButtons.length - 1) {
      btnGroup.style.display = "inline-flex";
      const removeBtn = btnGroup.querySelector(
        'button[onclick="removeDosen()"]'
      );
      if (removeBtn) {
        removeBtn.style.display = dosenCount <= 1 ? "none" : "block";
      }
    } else {
      btnGroup.style.display = "none";
    }
  });
}

// Add deleteKelompok function globally
window.deleteKelompok = async function (id_kelompok, btn) {
  if (!confirm("Yakin ingin menghapus kelompok ini?")) return;
  try {
    const formData = new FormData();
    formData.append("id_kelompok", id_kelompok);
    const response = await fetch("../../control/delete_kelompok.php", {
      method: "POST",
      body: formData,
    });
    const result = await response.json();
    if (result.success) {
      // Remove kelompok from DOM
      btn.closest(".kelompok-list-item").remove();
    } else {
      alert(result.message || "Gagal menghapus kelompok.");
    }
  } catch (e) {
    alert("Terjadi kesalahan saat menghapus kelompok.");
  }
};
