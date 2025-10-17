// Add SweetAlert2 import at the top if not present
if (typeof Swal === "undefined") {
  var script = document.createElement("script");
  script.src = "https://cdn.jsdelivr.net/npm/sweetalert2@11";
  document.head.appendChild(script);
}

let kelompokModalInstance;
let anggotaCount = 1;
let currentProdi = "";
let mahasiswaData = [];
let kelompokData = [];
let dosenData = [];
let matkulData = [];
let dosenCount = 1;
let selectedMahasiswa = {};

document.addEventListener("DOMContentLoaded", function () {
  const kelompokModalEl = document.getElementById("kelompokModal");
  const jenisSidang = document.getElementById("jenis_sidang");

  if (kelompokModalEl) {
    try {
      if (typeof bootstrap !== "undefined" && bootstrap.Modal) {
        kelompokModalInstance = new bootstrap.Modal(kelompokModalEl, {
          backdrop: true,
          keyboard: true,
          focus: true,
        });
      } else {
        console.error("Bootstrap Modal is not available");
        // Fallback for environments without Bootstrap JS
        kelompokModalInstance = {
          show: () => (kelompokModalEl.style.display = "block"),
          hide: () => (kelompokModalEl.style.display = "none"),
        };
      }
      kelompokModalEl.addEventListener("hidden.bs.modal", function () {
        if (!window.modalClosedByFunction) {
          resetKelompokForm();
        }
        window.modalClosedByFunction = false;
      });
    } catch (error) {
      console.error("Error initializing modal:", error);
    }
  }

  const kelompokForm = document.getElementById("kelompokForm");
  if (kelompokForm) {
    kelompokForm.addEventListener("submit", handleKelompokFormSubmit);
  }

  if (jenisSidang) {
    jenisSidang.addEventListener("change", handleJenisSidangChange);
  }

  fetchMahasiswaData();
  fetchDosenData();
  updateToggleButtonsVisibility();
});

// NEW FUNCTION to get and display suggested nomor kelompok
async function fetchAndSuggestNomorKelompok() {
    const suggestionContainer = document.getElementById('nomor-kelompok-suggestion');
    const suggestionLink = document.getElementById('apply-suggestion-link');
    const nomorKelompokInput = document.getElementById('nomor_kelompok');

    const tahunAjaran = document.getElementById("tahun_ajaran")?.value;
    const jenisSidang = document.getElementById("jenis_sidang")?.value;
    let idMatkul = document.getElementById("id_matkul")?.value;
    
    // For TA, matkul is always 2006
    if (jenisSidang === "Tugas Akhir") {
        idMatkul = "2006";
    }

    // Hide suggestion if context is not complete
    if (!tahunAjaran || !jenisSidang || !idMatkul) {
        suggestionContainer.style.display = 'none';
        return;
    }

    try {
        const url = `../../control/dosen/kelompok/get_suggested_kelompok_id.php?tahun_ajaran=${tahunAjaran}&jenis_sidang=${jenisSidang}&id_matkul=${idMatkul}`;
        const response = await fetch(url);
        const result = await response.json();

        if (result && result.suggestion) {
            suggestionLink.textContent = result.suggestion;
            suggestionContainer.style.display = 'block';

            // Make the suggestion clickable
            suggestionLink.onclick = (e) => {
                e.preventDefault();
                nomorKelompokInput.value = result.suggestion;
                suggestionContainer.style.display = 'none'; // Hide after applying
            };
        } else {
            suggestionContainer.style.display = 'none';
        }
    } catch (error) {
        console.error("Error fetching suggestion:", error);
        suggestionContainer.style.display = 'none';
    }
}

function showMatkulField(show) {
  const matkulGroup = document.getElementById("matkul-group");
  const idMatkul = document.getElementById("id_matkul");
  if (matkulGroup) matkulGroup.style.display = show ? "block" : "none";
  if (idMatkul) idMatkul.required = !!show;
  if (!show && idMatkul) idMatkul.value = "";
}

function showPembimbingField(show) {
  const dosenWrapperGroup = document.getElementById("dosen-wrapper-group");
  if (dosenWrapperGroup)
    dosenWrapperGroup.style.display = show ? "block" : "none";
}

function handleJenisSidangChange() {
  const jenisSidang = document.getElementById("jenis_sidang").value;
  const prodi = document.getElementById("kelompok_prodi").value;

  // Validate prodi selection first
  if (!prodi) {
    Swal.fire({
      title: "Pilih Prodi Terlebih Dahulu",
      text: "Silakan pilih Program Studi terlebih dahulu sebelum memilih jenis sidang.",
      icon: "warning",
      confirmButtonText: "OK",
      confirmButtonColor: "#4B68FB",
    });

    // Reset jenis sidang selection
    document.getElementById("jenis_sidang").value = "";
    return;
  }

  if (jenisSidang === "Semester") {
    showMatkulField(true);
    showPembimbingField(false);
    fetchMataKuliah();
    resetDosenInputs();
  } else if (jenisSidang === "Tugas Akhir") {
    showMatkulField(false);
    showPembimbingField(true);
    resetDosenInputs();
    const idMatkul = document.getElementById("id_matkul");
    if (idMatkul) idMatkul.value = "2006";
    // Call the suggestion function as the context is now complete for TA
    fetchAndSuggestNomorKelompok();
  } else {
    showMatkulField(false);
    showPembimbingField(false);
    resetDosenInputs();
  }
}

async function fetchMahasiswaData() {
  try {
    const tahunAjaran = document.getElementById("tahun_ajaran")?.value || "";
    const jenisSidang = document.getElementById("jenis_sidang")?.value || "";
    const prodi = document.getElementById("kelompok_prodi")?.value || "";
    let idMatkul = document.getElementById("id_matkul")?.value || "";

    // Validate prodi selection first
    if (!prodi) {
      mahasiswaData = [];
      updateAvailabilityIndicator();
      return; // Don't fetch data if prodi is not selected
    }

    if (jenisSidang === "Tugas Akhir") idMatkul = "2006";

    const form = document.getElementById("kelompokForm");
    const isEditMode = form?.dataset.mode === "edit";

    let url = "../../control/dosen/kelompok/get_mahasiswa.php?";
    const params = [];

    if (tahunAjaran)
      params.push(`tahun_ajaran=${encodeURIComponent(tahunAjaran)}`);
    if (jenisSidang)
      params.push(`jenis_sidang=${encodeURIComponent(jenisSidang)}`);
    if (idMatkul) params.push(`id_matkul=${encodeURIComponent(idMatkul)}`);
    if (prodi) params.push(`prodi=${encodeURIComponent(prodi)}`);

    if (isEditMode) {
      params.push(`edit_mode=true`);
      params.push(
        `current_nomor_kelompok=${encodeURIComponent(
          form.dataset.nomor_kelompok || ""
        )}`
      );
      params.push(
        `current_tahun_ajaran=${encodeURIComponent(
          form.dataset.tahun_ajaran || ""
        )}`
      );
      params.push(
        `current_jenis_sidang=${encodeURIComponent(
          form.dataset.jenis_sidang || ""
        )}`
      );
      params.push(
        `current_id_matkul=${encodeURIComponent(form.dataset.id_matkul || "")}`
      );
    }

    url += params.join("&");
    console.log("Fetching mahasiswa data with URL:", url);

    const response = await fetch(url);
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

    const responseText = await response.text();
    try {
      mahasiswaData = JSON.parse(responseText);
      updateAvailabilityIndicator();
    } catch (parseError) {
      console.error(
        "JSON Parse Error for mahasiswa data:",
        parseError,
        "\nResponse:",
        responseText
      );
      mahasiswaData = [];
      updateAvailabilityIndicator();
    }
  } catch (error) {
    console.error("Error fetching mahasiswa data:", error);
    mahasiswaData = [];
    updateAvailabilityIndicator();
    alert("Gagal memuat data mahasiswa untuk autocomplete.");
  }
}

function updateAvailabilityIndicator() {
  const indicator = document.getElementById("mahasiswa-availability");
  if (!indicator) return;

  const count = mahasiswaData.length;
  const prodi = document.getElementById("kelompok_prodi")?.value || "";

  if (!prodi) {
    indicator.innerHTML =
      '<span style="color: #6c757d;">Pilih Program Studi terlebih dahulu</span>';
    return;
  }

  if (count === 0) {
    indicator.innerHTML =
      '<span style="color: #dc3545;">Tidak ada mahasiswa yang tersedia</span>';
  } else if (count <= 5) {
    indicator.innerHTML = `<span style="color: #ffc107;">Hanya ${count} mahasiswa tersedia</span>`;
  } else {
    indicator.innerHTML = `<span style="color: #28a745;">${count} mahasiswa tersedia</span>`;
  }
}

async function refreshMahasiswaData() {
  await fetchMahasiswaData();
  resetAnggotaInputs();
  updateToggleButtonsVisibility();
}

function setupFormChangeListeners() {
  const tahunAjaranSelect = document.getElementById("tahun_ajaran");
  const jenisSidangSelect = document.getElementById("jenis_sidang");
  const idMatkulSelect = document.getElementById("id_matkul");
  const prodiSelect = document.getElementById("kelompok_prodi");

  if (tahunAjaranSelect)
    tahunAjaranSelect.addEventListener("change", refreshMahasiswaData);
  if (jenisSidangSelect)
    jenisSidangSelect.addEventListener("change", refreshMahasiswaData);
  if (idMatkulSelect)
    idMatkulSelect.addEventListener("change", refreshMahasiswaData);
  if (prodiSelect)
    prodiSelect.addEventListener("change", filterMahasiswaByProdi);
}

async function fetchDosenData() {
  try {
    const response = await fetch("../../control/dosen/kelompok/get_dosen.php");
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    dosenData = await response.json();
  } catch (error) {
    console.error("Error fetching dosen data:", error);
    dosenData = [];
    alert("Gagal memuat data dosen untuk autocomplete.");
  }
}

function openKelompokModal() {
  resetToCreateMode();
  switchTab("tambah");

  if (kelompokModalInstance) {
    kelompokModalInstance.show();
  } else {
    document.getElementById("kelompokModal").style.display = "block";
  }
  setupFormChangeListeners();

  // Show initial message for prodi selection
  const indicator = document.getElementById("mahasiswa-availability");
  if (indicator)
    indicator.innerHTML =
      '<span style="color: #6c757d;">Pilih Program Studi terlebih dahulu</span>';
}

function switchTab(tabName) {
  document
    .querySelectorAll(".modal-tab-content")
    .forEach((content) => content.classList.remove("active"));
  document
    .querySelectorAll(".modal-tab")
    .forEach((tab) => tab.classList.remove("active"));

  document.getElementById(tabName + "-tab").classList.add("active");
  document
    .querySelector(`.modal-tab[onclick="switchTab('${tabName}')"]`)
    .classList.add("active");

  if (tabName === "daftar") {
    loadKelompokList();
    setupKelompokFilters();
  } else if (tabName === "tambah") {
    const form = document.getElementById("kelompokForm");
    if (form?.dataset.mode !== "edit") {
      resetToCreateMode();
    }
    setupFormChangeListeners();

    // Show initial message for prodi selection when switching to tambah tab
    const indicator = document.getElementById("mahasiswa-availability");
    if (indicator)
      indicator.innerHTML =
        '<span style="color: #6c757d;">Pilih Program Studi terlebih dahulu</span>';
  }
}

function filterMahasiswaByProdi() {
  const prodi = document.getElementById("kelompok_prodi").value;

  // Validate prodi selection first
  if (!prodi) {
    Swal.fire({
      title: "Pilih Prodi Terlebih Dahulu",
      text: "Silakan pilih Program Studi terlebih dahulu.",
      icon: "warning",
      confirmButtonText: "OK",
      confirmButtonColor: "#4B68FB",
    });
    return;
  }

  currentProdi = prodi;
  selectedMahasiswa = {};
  refreshMahasiswaData();

  // If "Tugas Akhir" is already selected, re-fetch the suggestion
  if (document.getElementById("jenis_sidang").value === 'Tugas Akhir') {
      fetchAndSuggestNomorKelompok();
  }
}

function highlightText(text, query) {
  if (!query) return text;
  const regex = new RegExp(
    `(${query.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")})`,
    "gi"
  );
  return text.replace(regex, "<mark>$1</mark>");
}

function searchMahasiswa(input, anggotaIndex) {
  const query = input.value.trim().toLowerCase();
  const dropdown = document.getElementById(`autocomplete_${anggotaIndex}`);
  const namaDisplay = document.getElementById(`anggota_nama_${anggotaIndex}`);
  const prodi = document.getElementById("kelompok_prodi")?.value;

  // Validate prodi selection first
  if (!prodi) {
    dropdown.innerHTML =
      '<div class="autocomplete-item">Pilih Program Studi terlebih dahulu</div>';
    dropdown.style.display = "block";
    if (namaDisplay)
      namaDisplay.textContent = "Pilih Program Studi terlebih dahulu";
    return;
  }

  if (query.length === 0) {
    dropdown.style.display = "none";
    if (namaDisplay) namaDisplay.textContent = "Nama akan muncul otomatis";
    return;
  }

  const selectedNIMs = Array.from(
    document.querySelectorAll('input[name="anggota[]"]')
  )
    .map((inp) => inp.value.trim())
    .filter((nim) => nim !== "" && nim !== input.value.trim());

  const finalFilteredMahasiswa = mahasiswaData.filter(
    (mhs) =>
      !selectedNIMs.includes(String(mhs.nim)) &&
      (String(mhs.nim).toLowerCase().includes(query) ||
        mhs.nama_mhs.toLowerCase().includes(query))
  );

  if (finalFilteredMahasiswa.length > 0) {
    dropdown.innerHTML = "";
    finalFilteredMahasiswa.forEach((mhs) => {
      const item = document.createElement("div");
      item.className = "autocomplete-item";
      item.innerHTML = `<div class="nim">${highlightText(
        String(mhs.nim),
        query
      )}</div><div class="nama">${highlightText(mhs.nama_mhs, query)}</div>`;
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
  document.getElementById(`anggota_${anggotaIndex}`).value = mahasiswa.nim;
  document.getElementById(`anggota_nama_${anggotaIndex}`).textContent =
    mahasiswa.nama_mhs;
  document.getElementById(`autocomplete_${anggotaIndex}`).style.display =
    "none";
  selectedMahasiswa[anggotaIndex] = {
    nim: mahasiswa.nim,
    nama: mahasiswa.nama_mhs,
  };
}

function addAnggota() {
  const prodi = document.getElementById("kelompok_prodi")?.value;

  // Validate prodi selection first
  if (!prodi) {
    Swal.fire({
      title: "Pilih Prodi Terlebih Dahulu",
      text: "Silakan pilih Program Studi terlebih dahulu sebelum menambahkan anggota.",
      icon: "warning",
      confirmButtonText: "OK",
      confirmButtonColor: "#4B68FB",
    });
    return;
  }

  anggotaCount++;
  const wrapper = document.getElementById("anggota-wrapper");
  const div = document.createElement("div");
  div.className = "anggota-form-group";
  div.id = `anggota-form-${anggotaCount}`;
  div.innerHTML = `
        <label for="anggota_${anggotaCount}">Anggota ${anggotaCount}:</label>
        <div class="anggota-input-group">
            <div class="input-container">
        <input type="text" id="anggota_${anggotaCount}" name="anggota[]" placeholder="Masukkan NIM atau nama" oninput="searchMahasiswa(this, ${anggotaCount})" autocomplete="off" />
        <div class="autocomplete-dropdown" id="autocomplete_${anggotaCount}"></div>
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
    document.getElementById(`anggota-form-${anggotaCount}`).remove();
    delete selectedMahasiswa[anggotaCount];
    anggotaCount--;
    updateToggleButtonsVisibility();
  }
}

function resetAnggotaInputs() {
  const wrapper = document.getElementById("anggota-wrapper");
  const prodi = document.getElementById("kelompok_prodi")?.value;
  const defaultMessage = prodi
    ? "Nama akan muncul otomatis"
    : "Pilih Program Studi terlebih dahulu";

  wrapper.innerHTML = `
        <div class="anggota-form-group" id="anggota-form-1">
            <label for="anggota_1">Anggota 1:</label>
            <div class="anggota-input-group">
                <div class="input-container">
          <input type="text" id="anggota_1" name="anggota[]" placeholder="Masukkan NIM atau nama" oninput="searchMahasiswa(this, 1)" autocomplete="off" />
          <div class="autocomplete-dropdown" id="autocomplete_1"></div>
                </div>
                <div class="anggota-nama-display" id="anggota_nama_1">${defaultMessage}</div>
                <div class="form-toggle-buttons">
                    <button type="button" onclick="addAnggota()">+</button>
                    <button type="button" onclick="removeAnggota()" style="display: none;">-</button>
                </div>
            </div>
        </div>`;
  anggotaCount = 1;
  selectedMahasiswa = {};
  updateToggleButtonsVisibility();
}

// Dosen (Pembimbing) Section
function resetDosenInputs() {
  const dosenWrapper = document.getElementById("dosen-wrapper");
  if (!dosenWrapper) return;

  const prodi = document.getElementById("kelompok_prodi")?.value;
  const defaultMessage = prodi
    ? "Nama dosen akan muncul otomatis"
    : "Pilih Program Studi terlebih dahulu";

  dosenWrapper.innerHTML = `
    <div class="anggota-form-group" id="dosen-form-1">
      <label for="dosen_pembimbing_1">Pembimbing 1:</label>
      <div class="anggota-input-group">
        <div class="input-container">
          <input type="text" id="dosen_pembimbing_1" name="dosen_pembimbing[]" placeholder="Masukkan NIP atau nama dosen" autocomplete="off" oninput="searchDosen(this, 1)" />
          <div class="autocomplete-dropdown" id="autocomplete_dosen_1"></div>
        </div>
        <div class="anggota-nama-display" id="dosen_nama_display_1">${defaultMessage}</div>
        <div class="form-toggle-buttons">
          <button type="button" onclick="addDosen()">+</button>
          <button type="button" onclick="removeDosen()" style="display:none;">-</button>
        </div>
      </div>
      <input type="hidden" id="dosen_nomor_hidden_1" name="dosen_nomor_hidden[]" />
    </div>`;
  dosenCount = 1;
  updateToggleButtonsVisibility();
}

function addDosen() {
  const prodi = document.getElementById("kelompok_prodi")?.value;

  // Validate prodi selection first
  if (!prodi) {
    Swal.fire({
      title: "Pilih Prodi Terlebih Dahulu",
      text: "Silakan pilih Program Studi terlebih dahulu sebelum menambahkan pembimbing.",
      icon: "warning",
      confirmButtonText: "OK",
      confirmButtonColor: "#4B68FB",
    });
    return;
  }

  dosenCount++;
  const wrapper = document.getElementById("dosen-wrapper");
  const div = document.createElement("div");
  div.className = "anggota-form-group";
  div.id = `dosen-form-${dosenCount}`;
  div.innerHTML = `
      <label for="dosen_pembimbing_${dosenCount}">Pembimbing ${dosenCount}:</label>
      <div class="anggota-input-group">
        <div class="input-container">
          <input type="text" id="dosen_pembimbing_${dosenCount}" name="dosen_pembimbing[]" placeholder="Masukkan NIP atau nama dosen" autocomplete="off" oninput="searchDosen(this, ${dosenCount})" />
        <div class="autocomplete-dropdown" id="autocomplete_dosen_${dosenCount}"></div>
        </div>
        <div class="anggota-nama-display" id="dosen_nama_display_${dosenCount}">Nama dosen akan muncul otomatis</div>
        <div class="form-toggle-buttons">
          <button type="button" onclick="addDosen()">+</button>
          <button type="button" onclick="removeDosen()">-</button>
        </div>
      </div>
    <input type="hidden" id="dosen_nomor_hidden_${dosenCount}" name="dosen_nomor_hidden[]" />`;
  wrapper.appendChild(div);
  updateToggleButtonsVisibility();
}

function removeDosen() {
  if (dosenCount > 1) {
    document.getElementById(`dosen-form-${dosenCount}`).remove();
    dosenCount--;
    updateToggleButtonsVisibility();
  }
}

function searchDosen(input, index) {
  const query = input.value.trim().toLowerCase();
  const dropdown = document.getElementById(`autocomplete_dosen_${index}`);
  const prodi = document.getElementById("kelompok_prodi")?.value;
  const namaDisplay = document.getElementById(`dosen_nama_display_${index}`);

  // Validate prodi selection first
  if (!prodi) {
    dropdown.innerHTML =
      '<div class="autocomplete-item">Pilih Program Studi terlebih dahulu</div>';
    dropdown.style.display = "block";
    if (namaDisplay)
      namaDisplay.textContent = "Pilih Program Studi terlebih dahulu";
    document.getElementById(`dosen_nomor_hidden_${index}`).value = "";
    return;
  }

  if (query.length === 0) {
    dropdown.style.display = "none";
    if (namaDisplay)
      namaDisplay.textContent = "Nama dosen akan muncul otomatis";
    document.getElementById(`dosen_nomor_hidden_${index}`).value = "";
    return;
  }

  const selectedNIPs = Array.from(
    document.querySelectorAll('input[name="dosen_nomor_hidden[]"]')
  )
    .map((inp) => inp.value.trim())
    .filter((nip) => nip !== "");

  // Filter dosen: tidak boleh dosen login, tidak boleh duplikat, harus cocok query
  const finalFilteredDosen = dosenData.filter((dosen) => {
    const dosenNIP = String(dosen.nomor_dosen);
    const loginNIP = String(window.nomorDosenLogin || "");

    // Exclude dosen login
    if (dosenNIP === loginNIP) {
      return false;
    }

    // Exclude already selected NIPs
    if (selectedNIPs.includes(dosenNIP)) {
      return false;
    }

    // Match query
    return (
      dosenNIP.toLowerCase().includes(query) ||
      dosen.nama_dosen.toLowerCase().includes(query)
    );
  });

  if (finalFilteredDosen.length > 0) {
    dropdown.innerHTML = "";
    finalFilteredDosen.forEach((dosen) => {
      const item = document.createElement("div");
      item.className = "autocomplete-item";
      item.innerHTML = `<div class="nim">${highlightText(
        String(dosen.nomor_dosen),
        query
      )}</div><div class="nama">${highlightText(
        dosen.nama_dosen,
        query
      )}</div>`;
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

function updateToggleButtonsVisibility() {
  document.querySelectorAll("#anggota-wrapper .form-toggle-buttons").forEach((btnGroup, index, arr) => {
      btnGroup.style.display =index === arr.length - 1 ? "inline-flex" : "none";
      const removeBtn = btnGroup.querySelector(
        'button[onclick="removeAnggota()"]'
      );
      if (removeBtn)
        removeBtn.style.display = anggotaCount > 1 ? "block" : "none";
    });

  document.querySelectorAll("#dosen-wrapper .form-toggle-buttons").forEach((btnGroup, index, arr) => {
      btnGroup.style.display = index === arr.length - 1 ? "inline-flex" : "none";
      const removeBtn = btnGroup.querySelector(
        'button[onclick="removeDosen()"]'
      );
      if (removeBtn)
        removeBtn.style.display = dosenCount > 1 ? "block" : "none";
    });
}

function resetKelompokForm() {
  const form = document.getElementById("kelompokForm");
  if (form) {
    form.reset();
    delete form.dataset.mode;
    delete form.dataset.nomor_kelompok;
    delete form.dataset.tahun_ajaran;
    delete form.dataset.jenis_sidang;
    delete form.dataset.id_matkul;
  }
  updateModalUI("create");
  resetAnggotaInputs();
  resetDosenInputs();
  showMatkulField(false);
  showPembimbingField(false);
  anggotaCount = 1;
  dosenCount = 1;
  selectedMahasiswa = {};
  document.getElementById('nomor-kelompok-suggestion').style.display = 'none';

  const indicator = document.getElementById("mahasiswa-availability");
  if (indicator)
    indicator.innerHTML =
      '<span style="color: #6c757d;">ℹ️ Pilih Program Studi terlebih dahulu</span>';
  document.querySelectorAll(".error-message").forEach((el) => el.remove());
  document
    .querySelectorAll(".is-invalid")
    .forEach((el) => el.classList.remove("is-invalid"));
}

function resetToCreateMode() {
  resetKelompokForm();
  document.getElementById("nomor_kelompok").value = ""; // Clear the input field

  // Show initial message for prodi selection
  const indicator = document.getElementById("mahasiswa-availability");
  if (indicator)
    indicator.innerHTML =
      '<span style="color: #6c757d;"> Pilih Program Studi terlebih dahulu</span>';
}

window.closeKelompokModal = function () {
  window.modalClosedByFunction = true;
  resetToCreateMode();
  if (kelompokModalInstance) {
    kelompokModalInstance.hide();
  }
};

async function loadKelompokList() {
  const container = document.getElementById("kelompok-list-container");
  container.innerHTML =
    '<p class="text-center text-muted">Memuat daftar kelompok...</p>';
  try {
    const timestamp = new Date().getTime();
    const response = await fetch(
      `../../control/dosen/kelompok/get_kelompok_list.php?t=${timestamp}`
    );
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    const data = await response.json();
    if (!Array.isArray(data)) throw new Error("Expected array response");

    window.allKelompokData = data;

    renderKelompokList(data);
    
  } catch (error) {
    console.error("Error fetching kelompok data:", error);
    container.innerHTML = `<p class="text-center text-danger">Gagal memuat daftar kelompok: ${error.message}</p>`;
  }
}

function renderKelompokList(kelompokData) {
  const container = document.getElementById("kelompok-list-container");
  const filterSemester = document.getElementById("filter-semester")?.checked;
  const filterTugasAkhir = document.getElementById("filter-tugas-akhir")?.checked;
  const status = document.getElementById("filter-status")?.value;

  let filteredData = kelompokData;
  if (filterSemester !== filterTugasAkhir) {
    // Only one is checked
    filteredData = kelompokData.filter(
      (k) =>
        (filterSemester && k.jenis_sidang === "Semester") ||
        (filterTugasAkhir && k.jenis_sidang === "Tugas Akhir")
    );
  }

  if (filteredData.length === 0) {
    container.innerHTML = `<p class="text-center text-muted">${
      kelompokData.length === 0 ? "Belum ada kelompok yang dibuat." : "Tidak ada kelompok yang sesuai filter."
    }</p>`;
    return;
  }

      if (filteredData.status_ajuan) {
    filteredData = kelompokData.filter(
      (k) =>
        status === "all" ||
        (status === "pending" && k.pengajuan_status?.status_ajuan === "Pending") ||
        (status === "draft" && k.pengajuan_status?.status_ajuan === "Draft") ||
        (status === "approved" && k.pengajuan_status?.status_ajuan === "Approved") ||
        (status === "rejected" && k.pengajuan_status?.status_ajuan === "Rejected") ||
        (status === "none" && !k.pengajuan_status)
);
}
    
  
  container.innerHTML = filteredData
    .map((kelompok) => {
      const anggotaList = kelompok.anggota
        .map((a) => `${a.nim} - ${a.nama_mhs}`)
        .join("<br>");
      let pengajuanInfo =
        "<span class='badge bg-secondary'>Belum Ada Pengajuan</span>";
      let locked = false;

      if (kelompok.pengajuan_status) { const { status_ajuan, nama_pengaju, nim_pengaju } = kelompok.pengajuan_status;
        if (status_ajuan === "Pending") {
          pengajuanInfo = `<span class='badge bg-warning text-dark'>Status: ${status_ajuan}</span> <br><small>Oleh: ${nama_pengaju} (${nim_pengaju})</small>`;
          locked = true;
        } else if (status_ajuan === "Draft") {
          pengajuanInfo = `<span class='badge bg-secondary'>Draft</span> <br><small>Oleh: ${nama_pengaju} (${nim_pengaju})</small>`;
          // locked = false; // Draft tidak mengunci
        } else if (status_ajuan === "Approved") {
          pengajuanInfo = `<span class='badge text-bg-success'>Status: ${status_ajuan}</span> <br><small>Oleh: ${nama_pengaju} (${nim_pengaju})</small>`;
          locked = true;
        } else if (status_ajuan === "Rejected") {
          pengajuanInfo = `<span class='badge bg-danger'>Ditolak</span> <br><small>Oleh: ${nama_pengaju} (${nim_pengaju})</small>`;
        }
      }

      return `
      <div class="kelompok-list-item ${locked ? "kelompok-locked" : ""} ${
        kelompok.pengajuan_status
          ? `kelompok-status-${kelompok.pengajuan_status.status_ajuan?.toLowerCase()}`
          : ""
      }">
      <div class="kelompok-list-header d-flex justify-content-between align-items-center">
        <div>
            <div class="kelompok-list-title">Kelompok ${
              kelompok.nomor_kelompok
            } (${kelompok.jenis_sidang})</div>
            <div class="kelompok-list-prodi">${kelompok.nama_matkul} - ${
        kelompok.tahun_ajaran
      }</div>
        </div>
        <div class="btn-group" role="group">
            <button class="btn btn-link text-primary p-0 me-2" title="Edit Kelompok" onclick="editKelompok(${
              kelompok.nomor_kelompok
            }, '${kelompok.tahun_ajaran}', '${kelompok.jenis_sidang}', ${
        kelompok.id_matkul
      })" ${locked ? "disabled" : ""}>
            <i class="bi bi-pencil-fill"></i>
          </button>
            <button class="btn btn-link text-danger p-0" title="Hapus Kelompok" onclick="deleteKelompok(${
              kelompok.nomor_kelompok
            }, '${kelompok.tahun_ajaran}', '${kelompok.jenis_sidang}', ${
        kelompok.id_matkul
      })" ${locked ? "disabled" : ""}>
            <i class="bi bi-trash-fill"></i>
          </button>
        </div>
      </div>
        <div class="kelompok-list-anggota"><strong>Anggota:</strong><br>${anggotaList}</div>
        <div class="kelompok-list-pengajuan mt-2">${pengajuanInfo}</div>
      </div>`;
    })
    .join("");
}

function setupKelompokFilters() {
  document
    .querySelectorAll("#filter-semester, #filter-tugas-akhir")
    .forEach((el) => {
      el.removeEventListener("change", enforceAtLeastOneFilter);
      el.addEventListener("change", enforceAtLeastOneFilter);
      el.removeEventListener("change", applyKelompokFilters); // prevent multiple listeners
      el.addEventListener("change", applyKelompokFilters);
    });
    document.getElementById("filter-status")?.addEventListener("change", applyKelompokFilters);
}

function enforceAtLeastOneFilter(e) {
  const semester = document.getElementById("filter-semester");
  const ta = document.getElementById("filter-tugas-akhir");
  // Jika user mencoba uncheck dan yang lain juga sudah uncheck, batalkan
  if (!semester.checked && !ta.checked) {
    // Kembalikan checkbox yang baru saja di-uncheck ke checked
    e.target.checked = true;
  }
}

function applyKelompokFilters() {
  if (window.allKelompokData) {
    renderKelompokList(window.allKelompokData);
  }
}

async function fetchMataKuliah() {
  const idMatkul = document.getElementById("id_matkul");
  if (!idMatkul) return;

  const prodi = document.getElementById("kelompok_prodi")?.value;

  // Validate prodi selection first
  if (!prodi) {
    idMatkul.innerHTML =
      '<option value="">-- Pilih Prodi Terlebih Dahulu --</option>';
    return;
  }

  const tahunAjaran =
    document.getElementById("tahun_ajaran")?.value || new Date().getFullYear();
  try {
    const response = await fetch(
      `../../control/dosen/kelompok/get_matkul_by_jenis.php?jenis_sidang=Semester&tahun_ajaran=${tahunAjaran}`
    );
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
    const data = await response.json();
    idMatkul.innerHTML = '<option value="">-- Pilih Mata Kuliah --</option>';
    data.forEach((matkul) => {
      idMatkul.add(new Option(matkul.nama_matkul, matkul.id_matkul));
    });
    // Add onchange event to trigger suggestion
    idMatkul.onchange = fetchAndSuggestNomorKelompok;
  } catch (e) {
    console.error("Error fetching mata kuliah:", e);
    idMatkul.innerHTML = '<option value="">(Gagal memuat data)</option>';
  }
}

async function handleKelompokFormSubmit(event) {
  event.preventDefault();

  // Additional prodi validation before form validation
  const prodi = document.getElementById("kelompok_prodi")?.value;
  if (!prodi) {
    Swal.fire({
      title: "Pilih Program Studi Terlebih Dahulu",
      text: "Silakan pilih Program Studi terlebih dahulu sebelum menyimpan kelompok.",
      icon: "warning",
      confirmButtonText: "OK",
      confirmButtonColor: "#4B68FB",
    });
    return;
  }

  if (!validateKelompokForm()) return;

  const kelompokForm = document.getElementById("kelompokForm");
  const formMode = kelompokForm.dataset.mode || "create";
  const formData = new FormData(kelompokForm);

  if (document.getElementById("jenis_sidang").value === "Tugas Akhir") {
    formData.set("id_matkul", 2006);
    const dosenInputs = document.querySelectorAll(
      'input[name="dosen_nomor_hidden[]"]'
    );
    formData.delete("nomor_dosen[]");
    dosenInputs.forEach((input) => {
      if (input.value.trim() !== "")
        formData.append("nomor_dosen[]", input.value.trim());
    });
  }

  let url = `../../control/dosen/kelompok/kelompok_${formMode}.php`;
  if (formMode === "edit") {
    formData.append("nomor_kelompok", kelompokForm.dataset.nomor_kelompok);
    formData.append("tahun_ajaran", kelompokForm.dataset.tahun_ajaran);
    formData.append("jenis_sidang", kelompokForm.dataset.jenis_sidang);
    formData.append("id_matkul_original", kelompokForm.dataset.id_matkul); // original ID for WHERE clause
  }

  try {
    const response = await fetch(url, { method: "POST", body: formData });
    const result = await response.json();

    if (result.success || result.status === "ok") {
      Swal.fire({
        title: "Sukses!",
        text:
          result.message ||
          `Kelompok berhasil di${formMode === "edit" ? "perbarui" : "buat"}!`,
        icon: "success",
        confirmButtonText: "OK",
        confirmButtonColor: "#4B68FB",
      }).then(() => {
        if (kelompokModalInstance) kelompokModalInstance.hide();
        loadKelompokList(); // Refresh list
        switchTab("daftar"); // Switch to list view
      });
    } else {
      Swal.fire({
        title: "Gagal",
        text: result.message || "Gagal memproses kelompok.",
        icon: "error",
        confirmButtonText: "OK",
        confirmButtonColor: "#4B68FB",
      });
    }
  } catch (error) {
    console.error("Error processing kelompok:", error);
    Swal.fire({
      title: "Terjadi Kesalahan",
      text: "Tidak dapat terhubung ke server. " + error.message,
      icon: "error",
      confirmButtonText: "OK",
      confirmButtonColor: "#4B68FB",
    });
  }
}

function validateKelompokForm() {
  let isValid = true;
  document.querySelectorAll(".error-message").forEach((el) => el.remove());
  document
    .querySelectorAll(".is-invalid")
    .forEach((el) => el.classList.remove("is-invalid"));

  const showError = (fieldId, message) => {
    isValid = false;
    const field = document.getElementById(fieldId);
    if (field) {
      field.classList.add("is-invalid");
      const errorDiv = document.createElement("div");
      errorDiv.className = "error-message text-danger small mt-1";
      errorDiv.textContent = message;
      field.parentElement.append(errorDiv);
    }
  };

  if (!document.getElementById("kelompok_prodi").value)
    showError(
      "kelompok_prodi",
      "Program Studi harus dipilih terlebih dahulu sebelum melanjutkan."
    );
  if (!document.getElementById("tahun_ajaran").value)
    showError("tahun_ajaran", "Tahun Ajaran harus dipilih.");
  
  const nomorKelompokInput = document.getElementById("nomor_kelompok");
  if (!nomorKelompokInput.value) {
    showError("nomor_kelompok", "Nomor Kelompok harus diisi.");
  } else if (!/^[1-9]\d*$/.test(nomorKelompokInput.value)) {
    showError("nomor_kelompok", "Nomor Kelompok harus berupa angka positif.");
  }

  const jenisSidang = document.getElementById("jenis_sidang").value;
  if (!jenisSidang) showError("jenis_sidang", "Jenis Sidang harus dipilih.");

  if (
    jenisSidang === "Semester" &&
    !document.getElementById("id_matkul").value
  ) {
    showError("id_matkul", "Mata Kuliah harus dipilih untuk Sidang Semester.");
  }

  const nimInputs = document.querySelectorAll('input[name="anggota[]"]');
  const nimSet = new Set();
  let hasAnggota = false;
  nimInputs.forEach((input) => {
    const nim = input.value.trim();
    if (nim) {
      hasAnggota = true;
      if (nimSet.has(nim)) {
        showError(input.id, "NIM tidak boleh duplikat.");
      }
      nimSet.add(nim);
    }
  });

  if (!hasAnggota) showError("anggota_1", "Minimal harus ada satu anggota.");
  if (nimSet.size > 5)
    showError("anggota-wrapper", "Maksimal 5 anggota per kelompok.");

  return isValid;
}


document.addEventListener("click", function (event) {
  document.querySelectorAll(".autocomplete-dropdown").forEach((dropdown) => {
    const input = dropdown.previousElementSibling;
    if (!dropdown.contains(event.target) && event.target !== input) {
      dropdown.style.display = "none";
    }
  });
});

window.deleteKelompok = async function (
  nomor_kelompok,
  tahun_ajaran,
  jenis_sidang,
  id_matkul
) {
  Swal.fire({
    title: "Yakin ingin menghapus kelompok ini?",
    text: "Tindakan ini tidak dapat dibatalkan!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Ya, hapus!",
    cancelButtonText: "Batal",
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        const response = await fetch(
          "../../control/dosen/kelompok/kelompok_delete.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              nomor_kelompok,
              tahun_ajaran,
              jenis_sidang,
              id_matkul,
            }),
          }
        );
        const res = await response.json();
        if (res.status === "ok") {
          Swal.fire("Sukses!", "Kelompok berhasil dihapus!", "success");
          loadKelompokList(); // Refresh the list
        } else {
          Swal.fire(
            "Gagal",
            res.message || "Gagal menghapus kelompok",
            "error"
          );
        }
      } catch (error) {
        Swal.fire(
          "Terjadi Kesalahan",
          "Tidak dapat terhubung ke server.",
          "error"
        );
      }
    }
  });
};

window.editKelompok = async function (
  nomor_kelompok,
  tahun_ajaran,
  jenis_sidang,
  id_matkul
) {
  try {
    const url = `../../control/dosen/kelompok/kelompok_read.php?nomor_kelompok=${nomor_kelompok}&tahun_ajaran=${tahun_ajaran}&jenis_sidang=${jenis_sidang}&id_matkul=${id_matkul}`;
    const response = await fetch(url);
    const result = await response.json();

    if (result.status !== "ok" || !result.data || result.data.length === 0) {
      throw new Error(result.message || "Kelompok data tidak ditemukan");
    }

    switchTab("tambah");
    await populateEditForm(result.data, {
      nomor_kelompok,
      tahun_ajaran,
      jenis_sidang,
      id_matkul,
    });

    if (kelompokModalInstance) kelompokModalInstance.show();
  } catch (error) {
    console.error("Error loading kelompok for edit:", error);
    Swal.fire("Terjadi Kesalahan", error.message, "error");
  }
};

function updateModalUI(mode) {
  const modalTitle = document.querySelector("#kelompokModal .modal-title");
  const submitButton = document.querySelector(
    "#kelompokForm button[type='submit']"
  );
  const tambahTabBtn = document.getElementById("tambah-tab-btn");

  if (mode === "edit") {
    if (modalTitle) modalTitle.textContent = "Edit Kelompok";
    if (submitButton) submitButton.textContent = "Update Kelompok";
    if (tambahTabBtn) tambahTabBtn.textContent = "Edit Kelompok";
  } else {
    if (modalTitle) modalTitle.textContent = "Tambah Kelompok Baru";
    if (submitButton) submitButton.textContent = "Buat Kelompok";
    if (tambahTabBtn) tambahTabBtn.textContent = "Tambah Kelompok";
  }
}

async function populateEditForm(kelompokData, keys) {
  const { nomor_kelompok, tahun_ajaran, jenis_sidang, id_matkul } = keys;
  const form = document.getElementById("kelompokForm");
  form.dataset.mode = "edit";
  form.dataset.nomor_kelompok = nomor_kelompok;
  form.dataset.tahun_ajaran = tahun_ajaran;
  form.dataset.jenis_sidang = jenis_sidang;
  form.dataset.id_matkul = id_matkul;

  updateModalUI("edit");

  document.getElementById("nomor_kelompok").value = nomor_kelompok;
  document.getElementById("tahun_ajaran").value = tahun_ajaran;
  document.getElementById("jenis_sidang").value = jenis_sidang;

  if (kelompokData.length > 0 && kelompokData[0].prodi) {
    const prodiMap = {
      TRPL: "Rekayasa Perangkat Lunak",
      RPL: "Rekayasa Perangkat Lunak",
    };
    document.getElementById("kelompok_prodi").value =
      prodiMap[kelompokData[0].prodi] || kelompokData[0].prodi;
  }

  await fetchMahasiswaData(); // Fetch available mahasiswa including current members

  if (jenis_sidang === "Semester") {
    showMatkulField(true);
    await fetchMataKuliah();
    document.getElementById("id_matkul").value = id_matkul;
  } else {
    showMatkulField(false);
  }

  if (jenis_sidang === "Tugas Akhir") {
    showPembimbingField(true);
    await populatePembimbingData(keys);
  } else {
    showPembimbingField(false);
  }

  populateAnggotaData(kelompokData);
}

function populateAnggotaData(anggotaData) {
  resetAnggotaInputs();
  anggotaData.forEach((student, index) => {
    if (index > 0) addAnggota();
    const i = index + 1;
    document.getElementById(`anggota_${i}`).value = student.nim;
    document.getElementById(`anggota_nama_${i}`).textContent = student.nama_mhs;
    selectedMahasiswa[i] = { nim: student.nim, nama: student.nama_mhs };
  });
  anggotaCount = anggotaData.length > 0 ? anggotaData.length : 1;
  updateToggleButtonsVisibility();
}

async function populatePembimbingData(keys) {
  try {
    const response = await fetch(
      "../../control/dosen/kelompok/get_pembimbing.php",
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(keys),
      }
    );
    const result = await response.json();
    if (result.status !== "ok") throw new Error(result.message);

    resetDosenInputs();
    if (result.data && result.data.length > 0) {
      result.data.forEach((dosen, index) => {
        if (index > 0) addDosen();
        const i = index + 1;
        document.getElementById(`dosen_pembimbing_${i}`).value =
          dosen.nomor_dosen;
        document.getElementById(`dosen_nama_display_${i}`).textContent =
          dosen.nama_dosen;
        document.getElementById(`dosen_nomor_hidden_${i}`).value =
          dosen.nomor_dosen;
      });
    }
  } catch (error) {
    console.error("Error loading pembimbing data:", error);
  }
}