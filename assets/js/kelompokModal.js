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
  const matkulGroup = document.getElementById("matkul-group");
  const idMatkul = document.getElementById("id_matkul");
  const dosenWrapperGroup = document.getElementById("dosen-wrapper-group");

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
        // Fallback: create a simple modal-like behavior
        kelompokModalInstance = {
          show: () => (kelompokModalEl.style.display = "block"),
          hide: () => (kelompokModalEl.style.display = "none"),
        };
      }
      kelompokModalEl.addEventListener("hidden.bs.modal", function () {
        // Only reset if not already handled by closeKelompokModal
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
    jenisSidang.addEventListener("change", function () {
      if (jenisSidang.value === "Semester") {
        showMatkulField(true);
        fetchMataKuliah();
        showPembimbingField(false);
        resetDosenInputs();
      } else if (jenisSidang.value === "Tugas Akhir") {
        showMatkulField(false);
        showPembimbingField(true);
        resetDosenInputs();
      } else {
        showMatkulField(false);
        showPembimbingField(false);
        resetDosenInputs();
      }
      // Update kelompok ID when jenis sidang changes
      setNextKelompokId();
    });
  }

  fetchMahasiswaData();
  fetchDosenData();
  updateToggleButtonsVisibility();
});

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
  const jenisSidang = document.getElementById("jenis_sidang");
  const matkulGroup = document.getElementById("matkul-group");
  const dosenWrapperGroup = document.getElementById("dosen-wrapper-group");

  if (jenisSidang.value === "Semester") {
    showMatkulField(true);
    showPembimbingField(false);
    // Fetch mata kuliah for Semester
    fetchMataKuliah();
  } else if (jenisSidang.value === "Tugas Akhir") {
    showMatkulField(false);
    showPembimbingField(true);
    // Set id_matkul to 2006 for Tugas Akhir
    const idMatkul = document.getElementById("id_matkul");
    if (idMatkul) {
      idMatkul.value = "2006";
    }
  } else {
    showMatkulField(false);
    showPembimbingField(false);
  }

  // Update next kelompok ID when jenis sidang changes
  setNextKelompokId();
}

async function fetchMahasiswaData() {
  try {
    const tahunAjaran = document.getElementById("tahun_ajaran")?.value || "";
    const jenisSidang = document.getElementById("jenis_sidang")?.value || "";
    const prodi = document.getElementById("kelompok_prodi")?.value || "";
    let idMatkul = document.getElementById("id_matkul")?.value || "";

    // For Tugas Akhir, use fixed matkul ID
    if (jenisSidang === "Tugas Akhir") idMatkul = "2006";

    // Check if we're in edit mode
    const form = document.getElementById("kelompokForm");
    const isEditMode = form?.dataset.mode === "edit";

    // Build URL with available parameters
    let url = "../../control/get_mahasiswa.php?";
    const params = [];

    if (tahunAjaran)
      params.push(`tahun_ajaran=${encodeURIComponent(tahunAjaran)}`);
    if (jenisSidang)
      params.push(`jenis_sidang=${encodeURIComponent(jenisSidang)}`);
    if (idMatkul) params.push(`id_matkul=${encodeURIComponent(idMatkul)}`);
    if (prodi) params.push(`prodi=${encodeURIComponent(prodi)}`);

    // Add edit mode parameters if in edit mode
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
      console.log(`Loaded ${mahasiswaData.length} available students`);

      // Update availability indicator
      updateAvailabilityIndicator();
    } catch (parseError) {
      console.error("JSON Parse Error for mahasiswa data:", parseError);
      console.error("Response text:", responseText);
      mahasiswaData = []; // Fallback to empty array
      updateAvailabilityIndicator();
    }
  } catch (error) {
    console.error("Error fetching mahasiswa data:", error);
    mahasiswaData = []; // Fallback to empty array
    updateAvailabilityIndicator();
    alert("Gagal memuat data mahasiswa untuk autocomplete.");
  }
}

// Function to update availability indicator
function updateAvailabilityIndicator() {
  const indicator = document.getElementById("mahasiswa-availability");
  if (!indicator) return;

  const count = mahasiswaData.length;
  const prodi = document.getElementById("kelompok_prodi")?.value || "";

  if (count === 0) {
    indicator.innerHTML =
      '<span style="color: #dc3545;">⚠️ Tidak ada mahasiswa yang tersedia</span>';
  } else if (count <= 5) {
    indicator.innerHTML = `<span style="color: #ffc107;">⚠️ Hanya ${count} mahasiswa tersedia</span>`;
  } else {
    indicator.innerHTML = `<span style="color: #28a745;">✓ ${count} mahasiswa tersedia</span>`;
  }

  // Add TRPL alias information if applicable
  if (prodi && isRPLProdi(prodi)) {
    const trplCount = mahasiswaData.filter(
      (mhs) => mhs.prodi && mhs.prodi.toLowerCase().includes("trpl")
    ).length;
    const rplCount = mahasiswaData.filter(
      (mhs) =>
        mhs.prodi &&
        mhs.prodi.toLowerCase().includes("rekayasa perangkat lunak")
    ).length;

    if (trplCount > 0 || rplCount > 0) {
      const aliasInfo = [];
      if (trplCount > 0) aliasInfo.push(`${trplCount} TRPL`);
      if (rplCount > 0) aliasInfo.push(`${rplCount} RPL`);

      indicator.innerHTML += `<br><small style="color: #6c757d;">📋 Termasuk: ${aliasInfo.join(
        ", "
      )}</small>`;
    }
  }
}

// Helper function to check if prodi is RPL-related
function isRPLProdi(prodi) {
  const rplAliases = [
    "rekayasa perangkat lunak",
    "trpl",
    "rpl",
    "teknologi rekayasa perangkat lunak",
  ];
  return rplAliases.includes(prodi.toLowerCase().trim());
}

// Function to refresh mahasiswa data when form parameters change
async function refreshMahasiswaData() {
  // Check if we're in edit mode
  const form = document.getElementById("kelompokForm");
  const isEditMode = form?.dataset.mode === "edit";

  // Store current anggota data if in edit mode
  let currentAnggotaData = [];
  if (isEditMode) {
    const anggotaInputs = document.querySelectorAll('input[name="anggota[]"]');
    const anggotaNamaDisplays = document.querySelectorAll(
      '[id^="anggota_nama_"]'
    );

    anggotaInputs.forEach((input, index) => {
      if (input.value.trim() && anggotaNamaDisplays[index]) {
        currentAnggotaData.push({
          nim: input.value.trim(),
          nama: anggotaNamaDisplays[index].textContent,
          index: index + 1,
        });
      }
    });
  }

  await fetchMahasiswaData();

  // Clear selected mahasiswa tracking since available students may have changed
  selectedMahasiswa = {};

  if (isEditMode && currentAnggotaData.length > 0) {
    // In edit mode, restore the anggota data
    currentAnggotaData.forEach((anggota, index) => {
      if (index > 0) {
        addAnggota(); // Add new anggota field for additional students
      }

      const anggotaIndex = index + 1;
      const nimInput = document.getElementById(`anggota_${anggotaIndex}`);
      const namaDisplay = document.getElementById(
        `anggota_nama_${anggotaIndex}`
      );

      if (nimInput && namaDisplay) {
        nimInput.value = anggota.nim;
        namaDisplay.textContent = anggota.nama;

        // Mark this student as selected
        selectedMahasiswa[anggotaIndex] = {
          nim: anggota.nim,
          nama: anggota.nama,
          prodi: "", // We'll get this from the fetched data if needed
        };
      }
    });

    // Update anggota count
    anggotaCount = currentAnggotaData.length;
  } else {
    // Clear all anggota inputs since the available students may have changed
    resetAnggotaInputs();
  }

  // Update availability indicator
  updateToggleButtonsVisibility();
}

// Add event listeners for form parameter changes
function setupFormChangeListeners() {
  const tahunAjaranSelect = document.getElementById("tahun_ajaran");
  const jenisSidangSelect = document.getElementById("jenis_sidang");
  const idMatkulSelect = document.getElementById("id_matkul");
  const prodiSelect = document.getElementById("kelompok_prodi");

  if (tahunAjaranSelect) {
    tahunAjaranSelect.addEventListener("change", refreshMahasiswaData);
  }

  if (jenisSidangSelect) {
    jenisSidangSelect.addEventListener("change", refreshMahasiswaData);
  }

  if (idMatkulSelect) {
    idMatkulSelect.addEventListener("change", refreshMahasiswaData);
  }

  // Note: prodiSelect has onchange="filterMahasiswaByProdi()" in HTML, so no need for additional listener
}

async function fetchDosenData() {
  try {
    const response = await fetch("../../control/get_dosen.php");
    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

    const responseText = await response.text();
    try {
      dosenData = JSON.parse(responseText);
    } catch (parseError) {
      console.error("JSON Parse Error for dosen data:", parseError);
      console.error("Response text:", responseText);
      dosenData = []; // Fallback to empty array
    }
  } catch (error) {
    console.error("Error fetching dosen data:", error);
    dosenData = []; // Fallback to empty array
    alert("Gagal memuat data dosen untuk autocomplete.");
  }
}

function openKelompokModal() {
  // Reset form to create mode
  resetToCreateMode();

  // Switch to tambah tab
  switchTab("tambah");

  // Show modal
  const kelompokModalEl = document.getElementById("kelompokModal");
  if (kelompokModalInstance) {
    kelompokModalInstance.show();
  } else {
    kelompokModalEl.style.display = "block";
    kelompokModalEl.classList.add("show");
    document.body.classList.add("modal-open");
  }

  // Initialize form data
  fetchMahasiswaData();
  fetchDosenData();
  fetchMataKuliah();
  setupFormChangeListeners();
}

async function setNextKelompokId() {
  try {
    const jenisSidang = document.getElementById("jenis_sidang");
    const idMatkul = document.getElementById("id_matkul");
    const tahunAjaran = document.getElementById("tahun_ajaran");

    // Get current values
    const tahunAjaranValue = tahunAjaran
      ? tahunAjaran.value
      : new Date().getFullYear();
    const jenisSidangValue = jenisSidang ? jenisSidang.value : "";
    const idMatkulValue = idMatkul ? idMatkul.value : "";

    // For Tugas Akhir, use fixed matkul ID
    const finalIdMatkul =
      jenisSidangValue === "Tugas Akhir" ? "2006" : idMatkulValue;

    if (!jenisSidangValue || !finalIdMatkul) {
      // Set default value if not ready
      const nomorKelompokField = document.getElementById("nomor_kelompok");
      if (nomorKelompokField) {
        nomorKelompokField.value = "1";
      }
      return;
    }

    const response = await fetch(
      `../../control/get_next_kelompok_id.php?tahun_ajaran=${tahunAjaranValue}&jenis_sidang=${jenisSidangValue}&id_matkul=${finalIdMatkul}`
    );
    if (!response.ok) throw new Error("Failed to fetch next Kelompok ID");

    const responseText = await response.text();
    let data;
    try {
      data = JSON.parse(responseText);
    } catch (parseError) {
      console.error("JSON Parse Error for next kelompok ID:", parseError);
      console.error("Response text:", responseText);
      // Try to extract number from response text
      const numberMatch = responseText.match(/\d+/);
      data = { next_nomor: numberMatch ? numberMatch[0] : "1" };
    }

    const nomorKelompokField = document.getElementById("nomor_kelompok");
    if (nomorKelompokField && data.next_nomor) {
      nomorKelompokField.value = data.next_nomor;
    }
  } catch (e) {
    console.error("Error setting next kelompok ID:", e);
    const nomorKelompokField = document.getElementById("nomor_kelompok");
    if (nomorKelompokField) {
      nomorKelompokField.value = "1";
    }
  }
}

function switchTab(tabName) {
  // Hide all tab contents
  const tabContents = document.querySelectorAll(".modal-tab-content");
  tabContents.forEach((content) => {
    content.classList.remove("active");
  });

  // Remove active class from all tabs
  const tabs = document.querySelectorAll(".modal-tab");
  tabs.forEach((tab) => {
    tab.classList.remove("active");
  });

  // Show selected tab content
  const selectedTabContent = document.getElementById(tabName + "-tab");
  if (selectedTabContent) {
    selectedTabContent.classList.add("active");
  }

  // Add active class to selected tab
  const selectedTab = document.querySelector(
    `.modal-tab[onclick="switchTab('${tabName}')"]`
  );
  if (selectedTab) {
    selectedTab.classList.add("active");
  }

  // Load data based on tab
  if (tabName === "daftar") {
    loadKelompokList();
    setupKelompokFilters();
  } else if (tabName === "tambah") {
    // Check if we're in edit mode
    const form = document.getElementById("kelompokForm");
    const isEditMode = form && form.dataset.mode === "edit";

    if (!isEditMode) {
      // Only reset form if not in edit mode
      resetToCreateMode();
    }

    fetchMahasiswaData();
    fetchDosenData();
    fetchMataKuliah();
    setupFormChangeListeners();
  }
}

function filterMahasiswaByProdi() {
  const prodiSelect = document.getElementById("kelompok_prodi");
  currentProdi = prodiSelect.value;
  // Clear selected mahasiswa tracking when prodi changes
  selectedMahasiswa = {};
  // Refresh mahasiswa data with new prodi filter
  refreshMahasiswaData();
}

// Helper function to highlight matching text
function highlightText(text, query) {
  if (!query || query.trim() === "") {
    return text;
  }

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

  if (query.length === 0) {
    dropdown.style.display = "none";
    namaDisplay.textContent = "Nama akan muncul otomatis";
    return;
  }

  // Get current filter values
  const tahunAjaran = document.getElementById("tahun_ajaran").value;
  const jenisSidang = document.getElementById("jenis_sidang").value;
  const idMatkul = document.getElementById("id_matkul").value;
  const prodi = document.getElementById("kelompok_prodi").value;

  // Filter mahasiswa based on current form values
  let filteredMahasiswa = mahasiswaData.filter((mhs) => {
    const normalizedProdi =
      prodi === "Rekayasa Perangkat Lunak" ? "TRPL" : prodi;
    const rplAliases = [
      "TRPL",
      "RPL",
      "Rekayasa Perangkat Lunak",
      "Teknologi Rekayasa Perangkat Lunak",
    ];

    if (rplAliases.includes(normalizedProdi)) {
      return rplAliases.includes(mhs.prodi);
    } else {
      return mhs.prodi === normalizedProdi;
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
    document.querySelectorAll('input[name="anggota[]"]')
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

      // Highlight matching text
      const highlightedNim = highlightText(String(mhs.nim), query);
      const highlightedNama = highlightText(mhs.nama_mhs, query);

      item.innerHTML = `<div class="nim">${highlightedNim}</div><div class="nama">${highlightedNama}</div>`;
      item.onclick = () => selectMahasiswa(mhs, anggotaIndex);
      dropdown.appendChild(item);
    });
    dropdown.style.display = "block";
  } else {
    if (query.length > 0) {
      dropdown.innerHTML =
        '<div class="autocomplete-item">Tidak ada mahasiswa yang cocok dengan pencarian</div>';
    } else {
      dropdown.innerHTML =
        '<div class="autocomplete-item">Tidak ada mahasiswa yang tersedia untuk prodi ini</div>';
    }
    dropdown.style.display = "block";
  }
}

function selectMahasiswa(mahasiswa, anggotaIndex) {
  document.getElementById(`anggota_${anggotaIndex}`).value = mahasiswa.nim;
  document.getElementById(`anggota_nama_${anggotaIndex}`).textContent =
    mahasiswa.nama_mhs;
  document.getElementById(`autocomplete_${anggotaIndex}`).style.display =
    "none";

  // Track selected mahasiswa
  selectedMahasiswa[anggotaIndex] = {
    nim: mahasiswa.nim,
    nama: mahasiswa.nama_mhs,
    prodi: mahasiswa.prodi,
  };
}

function addAnggota() {
  anggotaCount++;
  console.log(`[DEBUG] addAnggota called, new anggotaCount: ${anggotaCount}`);

  const wrapper = document.getElementById("anggota-wrapper");
  if (!wrapper) {
    console.error("[ERROR] anggota-wrapper not found in addAnggota");
    return;
  }

  const div = document.createElement("div");
  div.className = "anggota-form-group";
  div.id = "anggota-form-" + anggotaCount;
  div.innerHTML = `
        <label for="anggota_${anggotaCount}">Anggota ${anggotaCount}:</label>
        <div class="anggota-input-group">
            <div class="input-container">
                <input type="text" id="anggota_${anggotaCount}" name="anggota[]" placeholder="Masukkan NIM atau nama" oninput="searchMahasiswa(this, ${anggotaCount})" />
                <div class="autocomplete-dropdown" id="autocomplete_${anggotaCount}" style="display: none;"></div>
            </div>
            <div class="anggota-nama-display" id="anggota_nama_${anggotaCount}">Nama akan muncul otomatis</div>
            <div class="form-toggle-buttons">
                <button type="button" onclick="addAnggota()">+</button>
                <button type="button" onclick="removeAnggota()">-</button>
            </div>
        </div>`;

  wrapper.appendChild(div);

  // Verify the element was created
  const newInput = document.getElementById(`anggota_${anggotaCount}`);
  const newDisplay = document.getElementById(`anggota_nama_${anggotaCount}`);
  console.log(`[DEBUG] Created elements for anggota ${anggotaCount}:`, {
    newInput,
    newDisplay,
  });

  updateToggleButtonsVisibility();
}

function removeAnggota() {
  if (anggotaCount > 1) {
    document.getElementById("anggota-form-" + anggotaCount).remove();
    // Clear tracking for removed anggota
    delete selectedMahasiswa[anggotaCount];
    anggotaCount--;
  }
  updateToggleButtonsVisibility();
}

function resetAnggotaInputs() {
  console.log("[DEBUG] resetAnggotaInputs called");

  const wrapper = document.getElementById("anggota-wrapper");
  if (!wrapper) {
    console.error("[ERROR] anggota-wrapper not found");
    return;
  }

  wrapper.innerHTML = `
        <div class="anggota-form-group" id="anggota-form-1">
            <label for="anggota_1">Anggota 1:</label>
            <div class="anggota-input-group">
                <div class="input-container">
                    <input type="text" id="anggota_1" name="anggota[]" placeholder="Masukkan NIM atau nama" oninput="searchMahasiswa(this, 1)" />
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
  selectedMahasiswa = {};

  console.log(
    "[DEBUG] resetAnggotaInputs completed, anggotaCount:",
    anggotaCount
  );
  updateToggleButtonsVisibility();
}

// ============ DOSEN SECTION (Pembimbing) ============

function resetDosenInputs() {
  const dosenWrapper = document.getElementById("dosen-wrapper");
  if (!dosenWrapper) return;
  dosenWrapper.innerHTML = `
    <div class="anggota-form-group" id="dosen-form-1">
      <label for="dosen_pembimbing_1">Pembimbing 1:</label>
      <div class="anggota-input-group">
        <div class="input-container">
          <input type="text" id="dosen_pembimbing_1" name="dosen_pembimbing[]" placeholder="Masukkan NIP atau nama dosen" autocomplete="off" oninput="searchDosen(this, 1)" />
          <div class="autocomplete-dropdown" id="autocomplete_dosen_1" style="display: none;"></div>
        </div>
        <div class="anggota-nama-display" id="dosen_nama_display_1">Nama dosen akan muncul otomatis</div>
        <div class="form-toggle-buttons">
          <button type="button" onclick="addDosen()">+</button>
          <button type="button" onclick="removeDosen()" style="display:none;">-</button>
        </div>
      </div>
      <input type="hidden" id="dosen_nomor_hidden_1" name="dosen_nomor_hidden[]" />
    </div>
  `;
  dosenCount = 1;
  updateToggleButtonsVisibility();
}

function addDosen() {
  dosenCount++;
  const wrapper = document.getElementById("dosen-wrapper");
  const div = document.createElement("div");
  div.className = "anggota-form-group";
  div.id = "dosen-form-" + dosenCount;
  div.innerHTML = `
      <label for="dosen_pembimbing_${dosenCount}">Pembimbing ${dosenCount}:</label>
      <div class="anggota-input-group">
        <div class="input-container">
          <input type="text" id="dosen_pembimbing_${dosenCount}" name="dosen_pembimbing[]" placeholder="Masukkan NIP atau nama dosen" autocomplete="off" oninput="searchDosen(this, ${dosenCount})" />
          <div class="autocomplete-dropdown" id="autocomplete_dosen_${dosenCount}" style="display: none;"></div>
        </div>
        <div class="anggota-nama-display" id="dosen_nama_display_${dosenCount}">Nama dosen akan muncul otomatis</div>
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

  let filteredDosen = dosenData.filter(
    (dosen) =>
      String(dosen.nomor_dosen).toLowerCase().includes(query) ||
      dosen.nama_dosen.toLowerCase().includes(query)
  );

  const selectedNIPs = Array.from(
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
    (dosen) => !selectedNIPs.includes(String(dosen.nomor_dosen))
  );

  if (finalFilteredDosen.length > 0) {
    dropdown.innerHTML = "";
    finalFilteredDosen.forEach((dosen, dropdownIndex) => {
      const item = document.createElement("div");
      item.className = "autocomplete-item";
      item.dataset.nomor = dosen.nomor_dosen;
      item.dataset.nama = dosen.nama_dosen;
      item.dataset.index = dropdownIndex;

      // Highlight matching text
      const highlightedNomor = highlightText(String(dosen.nomor_dosen), query);
      const highlightedNama = highlightText(dosen.nama_dosen, query);

      item.innerHTML = `<div class="nim">${highlightedNomor}</div><div class="nama">${highlightedNama}</div>`;
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

function resetKelompokForm() {
  const form = document.getElementById("kelompokForm");
  if (form) {
    form.reset();
    // Clear edit mode
    delete form.dataset.mode;
    delete form.dataset.nomor_kelompok;
    delete form.dataset.tahun_ajaran;
    delete form.dataset.jenis_sidang;
    delete form.dataset.id_matkul;
  }

  // Reset modal UI to create mode
  updateModalUI("create");

  resetAnggotaInputs();
  resetDosenInputs();
  showMatkulField(false);
  showPembimbingField(false);

  // Reset anggota count
  anggotaCount = 1;
  dosenCount = 1;

  // Clear selected mahasiswa tracking
  selectedMahasiswa = {};

  // Clear availability indicator
  const indicator = document.getElementById("mahasiswa-availability");
  if (indicator) {
    indicator.innerHTML = "";
  }

  // Clear error messages
  document.querySelectorAll(".error-message").forEach((el) => el.remove());
  document
    .querySelectorAll(".is-invalid")
    .forEach((el) => el.classList.remove("is-invalid"));
}

// Function to reset form to create mode (for new kelompok)
function resetToCreateMode() {
  resetKelompokForm();
  setNextKelompokId();
}

// Function to close modal properly
window.closeKelompokModal = function () {
  // Set flag to prevent double reset
  window.modalClosedByFunction = true;

  // Reset form to create mode when closing
  resetToCreateMode();

  // Hide modal
  if (
    kelompokModalInstance &&
    typeof kelompokModalInstance.hide === "function"
  ) {
    kelompokModalInstance.hide();
  } else {
    // Fallback: hide modal manually
    const modalEl = document.getElementById("kelompokModal");
    if (modalEl) {
      modalEl.style.display = "none";
      modalEl.classList.remove("show");
      document.body.classList.remove("modal-open");
    }
  }
};

async function loadKelompokList() {
  console.log("loadKelompokList() called");

  const container = document.getElementById("kelompok-list-container");
  if (!container) {
    console.error("Kelompok list container not found");
    return;
  }

  container.innerHTML =
    '<p class="text-center text-muted">Memuat daftar kelompok...</p>';

  try {
    // Add timestamp to prevent caching
    const timestamp = new Date().getTime();
    console.log("Fetching kelompok list with timestamp:", timestamp);

    const response = await fetch(
      `../../control/get_kelompok_list.php?t=${timestamp}`
    );
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const responseText = await response.text();
    console.log("Response received:", responseText.substring(0, 200) + "...");

    let kelompokData;

    try {
      kelompokData = JSON.parse(responseText);
      console.log("Parsed kelompok data successfully");
    } catch (parseError) {
      console.error("JSON Parse Error for kelompok list:", parseError);
      console.error("Response text:", responseText);
      throw new Error("Invalid response format from server");
    }

    if (!Array.isArray(kelompokData)) {
      throw new Error("Expected array response from server");
    }

    // Store the data globally for filtering
    window.allKelompokData = kelompokData;

    console.log("Loaded kelompok data:", kelompokData);

    // Apply filters and render
    console.log("About to render kelompok list with data:", kelompokData);
    renderKelompokList(kelompokData);
  } catch (error) {
    console.error("Error fetching kelompok data:", error);
    container.innerHTML =
      '<p class="text-center text-danger">Gagal memuat daftar kelompok: ' +
      error.message +
      "</p>";
  }
}

// Function to render kelompok list with filtering
function renderKelompokList(kelompokData) {
  console.log("renderKelompokList called with data:", kelompokData);
  const container = document.getElementById("kelompok-list-container");
  if (!container) {
    console.error("kelompok-list-container not found");
    return;
  }

  // Get current filter values
  const filterSemester =
    document.getElementById("filter-semester")?.checked || false;
  const filterTugasAkhir =
    document.getElementById("filter-tugas-akhir")?.checked || false;

  console.log("Filter values:", { filterSemester, filterTugasAkhir });

  // Filter the data based on selected types
  let filteredData = kelompokData;
  if (!filterSemester && !filterTugasAkhir) {
    // If both filters are unchecked, show all data
    filteredData = kelompokData;
  } else if (!filterSemester || !filterTugasAkhir) {
    filteredData = kelompokData.filter((kelompok) => {
      if (filterSemester && kelompok.jenis_sidang === "Semester") return true;
      if (filterTugasAkhir && kelompok.jenis_sidang === "Tugas Akhir")
        return true;
      return false;
    });
  }

  console.log("Filtered data count:", filteredData.length);

  if (filteredData.length === 0) {
    if (kelompokData.length === 0) {
      container.innerHTML =
        '<p class="text-center text-muted">Belum ada kelompok yang dibuat.</p>';
    } else {
      container.innerHTML =
        '<p class="text-center text-muted">Tidak ada kelompok yang sesuai dengan filter yang dipilih.</p>';
    }
    return;
  }

  container.innerHTML = "";
  console.log("Rendering", filteredData.length, "kelompok items");
  filteredData.forEach((kelompok) => {
    const anggotaList = kelompok.anggota
      .map((angg) => `${angg.nim} - ${angg.nama_mhs}`)
      .join("<br>");

    // Status Pengajuan
    let pengajuanInfo =
      "<span class='badge bg-secondary'>Belum Ada Pengajuan</span>";
    let locked = false;

    if (
      kelompok.pengajuan_status &&
      ["Pending", "Draft", "Approved"].includes(
        kelompok.pengajuan_status.status_ajuan
      )
    ) {
      pengajuanInfo =
        `<span class='badge text-bg-success'>Status Pengajuan: ${kelompok.pengajuan_status.status_ajuan}</span> ` +
        `<br><small>Oleh: ${kelompok.pengajuan_status.nama_pengaju} (${kelompok.pengajuan_status.nim_pengaju})</small>`;
      locked = true;
    } else if (
      kelompok.pengajuan_status &&
      kelompok.pengajuan_status.status_ajuan === "Rejected"
    ) {
      pengajuanInfo = `<span class='badge bg-danger'>Ditolak</span> <br><small>Oleh: ${kelompok.pengajuan_status.nama_pengaju} (${kelompok.pengajuan_status.nim_pengaju})</small>`;
    }

    const kelompokItem = document.createElement("div");
    kelompokItem.className =
      "kelompok-list-item" + (locked ? " kelompok-locked" : "");
    kelompokItem.innerHTML = `
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
    })" ${locked ? "disabled style='opacity:0.5;'" : ""}>
            <i class="bi bi-pencil-fill"></i>
          </button>
          <button class="btn btn-link text-danger p-0" title="Hapus Kelompok" onclick="deleteKelompok(${
            kelompok.nomor_kelompok
          }, '${kelompok.tahun_ajaran}', '${kelompok.jenis_sidang}', ${
      kelompok.id_matkul
    }, this)" ${locked ? "disabled style='opacity:0.5;'" : ""}>
            <i class="bi bi-trash-fill"></i>
          </button>
        </div>
      </div>
      <div class="kelompok-list-anggota">
        <strong>Anggota:</strong><br>${anggotaList}
      </div>
      <div class="kelompok-list-pengajuan mt-2">
        ${pengajuanInfo}
      </div>
    `;
    container.appendChild(kelompokItem);
  });
}

// Function to setup filter event listeners
function setupKelompokFilters() {
  const filterSemester = document.getElementById("filter-semester");
  const filterTugasAkhir = document.getElementById("filter-tugas-akhir");

  if (filterSemester) {
    filterSemester.addEventListener("change", function () {
      // Prevent unchecking both filters
      if (!this.checked && !filterTugasAkhir.checked) {
        this.checked = true;
        return;
      }
      applyKelompokFilters();
    });
  }

  if (filterTugasAkhir) {
    filterTugasAkhir.addEventListener("change", function () {
      // Prevent unchecking both filters
      if (!this.checked && !filterSemester.checked) {
        this.checked = true;
        return;
      }
      applyKelompokFilters();
    });
  }
}

// Function to apply filters
function applyKelompokFilters() {
  if (window.allKelompokData) {
    renderKelompokList(window.allKelompokData);
  }
}

async function fetchMataKuliah() {
  const idMatkul = document.getElementById("id_matkul");
  if (!idMatkul) return;

  const tahunAjaran = document.getElementById("tahun_ajaran");
  const tahunAjaranValue = tahunAjaran
    ? tahunAjaran.value
    : new Date().getFullYear();

  try {
    const response = await fetch(
      `../../control/get_matkul_by_jenis.php?jenis_sidang=Semester&tahun_ajaran=${tahunAjaranValue}`
    );

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const responseText = await response.text();
    let data;

    try {
      data = JSON.parse(responseText);
    } catch (parseError) {
      console.error("JSON Parse Error for mata kuliah:", parseError);
      console.error("Response text:", responseText);
      throw new Error("Invalid response format from server");
    }

    idMatkul.innerHTML = '<option value="">-- Pilih Mata Kuliah --</option>';
    data.forEach((matkul) => {
      let option = document.createElement("option");
      option.value = matkul.id_matkul;
      option.text = matkul.nama_matkul;
      idMatkul.appendChild(option);
    });

    // Add event listener for mata kuliah selection
    idMatkul.onchange = function () {
      setNextKelompokId();
    };
  } catch (e) {
    console.error("Error fetching mata kuliah:", e);
    idMatkul.innerHTML = '<option value="">(Gagal memuat data)</option>';
  }
}

async function handleKelompokFormSubmit(event) {
  event.preventDefault();
  if (!validateKelompokForm()) return;

  const kelompokForm = document.getElementById("kelompokForm");
  const formMode = kelompokForm.dataset.mode || "create";
  const formData = new FormData(kelompokForm);

  // For anggota[]: ensure all dynamic anggota fields are included
  const nimInputs = document.querySelectorAll('input[name="anggota[]"]');
  formData.delete("anggota[]"); // Remove any default
  formData.delete("anggota_nim[]"); // Remove any default
  nimInputs.forEach((input) => {
    if (input.value.trim() !== "") {
      formData.append("anggota[]", input.value.trim());
      formData.append("anggota_nim[]", input.value.trim()); // Add both for compatibility
    }
  });

  // For nomor_dosen[]: only for Tugas Akhir
  const jenisSidang = document.getElementById("jenis_sidang");
  if (jenisSidang.value === "Tugas Akhir") {
    const dosenInputs = document.querySelectorAll(
      'input[name="dosen_nomor_hidden[]"]'
    );
    formData.delete("nomor_dosen[]");
    dosenInputs.forEach((input) => {
      if (input.value.trim() !== "") {
        formData.append("nomor_dosen[]", input.value.trim());
      }
    });
  } else {
    formData.delete("nomor_dosen[]");
  }

  // For id_matkul: if Tugas Akhir, set to 2006
  if (jenisSidang.value === "Tugas Akhir") {
    formData.set("id_matkul", 2006);
  }

  try {
    let url = "../../control/kelompok_create.php";
    if (formMode === "edit") {
      url = "../../control/kelompok_edit.php";
      // Add edit mode parameters
      formData.append("nomor_kelompok", kelompokForm.dataset.nomor_kelompok);
      formData.append("tahun_ajaran", kelompokForm.dataset.tahun_ajaran);
      formData.append("jenis_sidang", kelompokForm.dataset.jenis_sidang);
      formData.append("id_matkul", kelompokForm.dataset.id_matkul);

      console.log("[DEBUG] Edit mode - Form data being sent:");
      for (let [key, value] of formData.entries()) {
        console.log(`[DEBUG] ${key}: ${value}`);
      }
    } else {
      console.log("[DEBUG] Create mode - Form data being sent:");
      for (let [key, value] of formData.entries()) {
        console.log(`[DEBUG] ${key}: ${value}`);
      }
    }

    const response = await fetch(url, {
      method: "POST",
      body: formData,
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const responseText = await response.text();
    console.log("Response text:", responseText); // Debug log

    let result;
    try {
      result = JSON.parse(responseText);
    } catch (parseError) {
      // If not JSON, check for success indicators in plain text
      if (
        responseText.includes("success") ||
        responseText.includes("berhasil") ||
        responseText.includes("ok") ||
        responseText.includes("updated")
      ) {
        result = {
          success: true,
          message:
            formMode === "edit"
              ? "Kelompok berhasil diperbarui!"
              : "Kelompok berhasil dibuat!",
        };
      } else {
        // Check for error messages in response
        let errorMessage =
          formMode === "edit"
            ? "Gagal memperbarui kelompok."
            : "Gagal membuat kelompok.";
        if (
          responseText.includes("error") ||
          responseText.includes("gagal") ||
          responseText.includes("failed")
        ) {
          // Try to extract error message from response
          const errorMatch = responseText.match(
            /(?:error|gagal|failed)[:\s]*([^<\n]+)/i
          );
          if (errorMatch) {
            errorMessage = errorMatch[1].trim();
          }
        }
        result = { success: false, message: errorMessage };
      }
    }

    if (result.success || result.status === "ok") {
      Swal.fire({
        title: 'Sukses!',
        text: result.message || (formMode === "edit" ? "Kelompok berhasil diperbarui!" : "Kelompok berhasil dibuat!"),
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#4B68FB'
      }).then(() => {
        resetKelompokForm();
        if (
          kelompokModalInstance &&
          typeof kelompokModalInstance.hide === "function"
        ) {
          kelompokModalInstance.hide();
        } else {
          // Fallback: hide modal manually
          const modalEl = document.getElementById("kelompokModal");
          if (modalEl) {
            modalEl.style.display = "none";
            modalEl.classList.remove("show");
            document.body.classList.remove("modal-open");
          }
        }
        // Refresh both tabs with current filters
        // Add a small delay to ensure database transaction is complete
        console.log("Refreshing kelompok list after successful edit...");
        window.allKelompokData = null;
        setTimeout(() => {
          loadKelompokList();
          switchTab("daftar");
        }, 500);
        setTimeout(() => {
          console.log("Second refresh attempt...");
          loadKelompokList();
        }, 1000);
      });
    } else {
      Swal.fire({
        title: 'Gagal',
        text: result.message || "Gagal memproses kelompok.",
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#4B68FB'
      });
    }
  } catch (error) {
    console.error("Error processing kelompok:", error);
    Swal.fire({
      title: 'Terjadi Kesalahan',
      text: error.message,
      icon: 'error',
      confirmButtonText: 'OK',
      confirmButtonColor: '#4B68FB'
    });
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

  // 1. Validate Prodi
  const prodi = document.getElementById("kelompok_prodi").value;
  if (!prodi) {
    showError("kelompok_prodi", "Pilih Prodi terlebih dahulu!");
    isValid = false;
  }

  // 2. Validate Tahun Ajaran
  const tahunAjaran = document.getElementById("tahun_ajaran").value;
  if (!tahunAjaran) {
    showError("tahun_ajaran", "Pilih Tahun Ajaran!");
    isValid = false;
  } else if (
    !/^\d{4}$/.test(tahunAjaran) ||
    parseInt(tahunAjaran) < 2020 ||
    parseInt(tahunAjaran) > 2030
  ) {
    showError("tahun_ajaran", "Tahun Ajaran harus antara 2020-2030!");
    isValid = false;
  }

  // 3. Validate Nomor Kelompok
  const nomorKelompok = document.getElementById("nomor_kelompok").value;
  if (!nomorKelompok) {
    showError("nomor_kelompok", "Nomor Kelompok harus diisi!");
    isValid = false;
  } else if (!/^\d+$/.test(nomorKelompok) || parseInt(nomorKelompok) <= 0) {
    showError("nomor_kelompok", "Nomor Kelompok harus berupa angka positif!");
    isValid = false;
  }

  // 4. Validate Jenis Sidang
  const jenisSidang = document.getElementById("jenis_sidang").value;
  if (!jenisSidang) {
    showError("jenis_sidang", "Pilih Jenis Sidang!");
    isValid = false;
  } else if (!["Semester", "Tugas Akhir"].includes(jenisSidang)) {
    showError(
      "jenis_sidang",
      "Jenis Sidang harus 'Semester' atau 'Tugas Akhir'!"
    );
    isValid = false;
  }

  // 5. Validate Mata Kuliah for Semester
  const idMatkul = document.getElementById("id_matkul");
  if (jenisSidang === "Semester") {
    if (!idMatkul || !idMatkul.value) {
      showError("id_matkul", "Pilih Mata Kuliah untuk Sidang Semester!");
      isValid = false;
    } else if (!/^\d+$/.test(idMatkul.value) || parseInt(idMatkul.value) <= 0) {
      showError("id_matkul", "ID Mata Kuliah harus berupa angka positif!");
      isValid = false;
    }
  }

  // 6. Validate Anggota
  let hasAnggota = false;
  const selectedNIMs = new Set();
  const nimInputs = document.querySelectorAll('input[name="anggota[]"]');

  for (const nimInput of nimInputs) {
    const nimValue = nimInput.value.trim();
    if (nimValue !== "") {
      // Check NIM format (alphanumeric, 8-20 characters)
      if (!/^[A-Za-z0-9]{8,20}$/.test(nimValue)) {
        showError(
          nimInput.id,
          `NIM ${nimValue} harus berupa 8-20 karakter alfanumerik.`
        );
        isValid = false;
        continue;
      }

      // Check for duplicate NIMs
      if (selectedNIMs.has(nimValue)) {
        showError(nimInput.id, `NIM ${nimValue} duplikat dalam kelompok.`);
        isValid = false;
        continue;
      }

      selectedNIMs.add(nimValue);
      hasAnggota = true;
    }
  }

  if (!hasAnggota) {
    showError("anggota-wrapper", "Minimal harus ada satu anggota!");
    isValid = false;
  }

  // 7. Check maximum anggota limit
  if (selectedNIMs.size > 5) {
    showError("anggota-wrapper", "Maksimal 5 anggota per kelompok!");
    isValid = false;
  }

  // 8. Validate Dosen Pembimbing for Tugas Akhir (if any selected)
  if (jenisSidang === "Tugas Akhir") {
    const dosenInputs = document.querySelectorAll(
      'input[name="dosen_pembimbing[]"]'
    );
    const selectedDosen = new Set();

    for (const dosenInput of dosenInputs) {
      const dosenValue = dosenInput.value.trim();
      if (dosenValue !== "") {
        // Check if dosen exists in dosen data
        const foundDosen = dosenData.find(
          (dosen) => String(dosen.nomor_dosen) === dosenValue
        );
        if (!foundDosen) {
          showError(
            dosenInput.id,
            `Dosen dengan nomor ${dosenValue} tidak ditemukan.`
          );
          isValid = false;
          continue;
        }

        // Check for duplicates
        if (selectedDosen.has(dosenValue)) {
          showError(dosenInput.id, `Dosen ${dosenValue} sudah ditambahkan.`);
          isValid = false;
          continue;
        }

        selectedDosen.add(dosenValue);
      }
    }
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
      !event.target.matches('input[name="anggota[]"]') &&
      !event.target.matches('input[name="dosen_pembimbing[]"]')
    ) {
      dropdown.style.display = "none";
    }
  });
});

window.deleteKelompok = async function (
  nomor_kelompok,
  tahun_ajaran,
  jenis_sidang,
  id_matkul,
  btn
) {
  Swal.fire({
    title: "Yakin ingin menghapus kelompok ini?",
    text: "Tindakan ini tidak dapat dibatalkan!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#dc3545", // Bootstrap danger color
    cancelButtonColor: "#6c757d",  // Bootstrap secondary color
    confirmButtonText: "Ya, hapus!",
    cancelButtonText: "Batal",
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        const data = {
          nomor_kelompok: nomor_kelompok,
          tahun_ajaran: tahun_ajaran,
          jenis_sidang: jenis_sidang,
          id_matkul: id_matkul,
        };

        const response = await fetch("../../control/kelompok_delete.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(data),
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const responseText = await response.text();
        console.log("Delete response:", responseText);

        let result;
        try {
          result = JSON.parse(responseText);
        } catch (parseError) {
          // Fallback to text parsing if JSON fails
          if (
            responseText.includes("success") ||
            responseText.includes("berhasil") ||
            responseText.includes("ok")
          ) {
            result = { status: "ok", message: "Kelompok berhasil dihapus" };
          } else {
            result = { status: "error", message: "Gagal menghapus kelompok" };
          }
        }

        if (result.status === "ok") {
          Swal.fire({
            title: 'Sukses!',
            text: 'Kelompok berhasil dihapus!',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#4B68FB'
          }).then(() => {
            if (window.allKelompokData) {
              window.allKelompokData = window.allKelompokData.filter(
                (item) =>
                  !(
                    item.nomor_kelompok == nomor_kelompok &&
                    item.tahun_ajaran == tahun_ajaran &&
                    item.jenis_sidang == jenis_sidang &&
                    item.id_matkul == id_matkul
                  )
              );
              loadKelompokList();
            }
          });
        } else {
          Swal.fire({
            title: 'Gagal',
            text: result.message || 'Gagal menghapus kelompok',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#4B68FB'
          });
        }
      } catch (error) {
        console.error("Error deleting kelompok:", error);
        Swal.fire({
          title: 'Terjadi Kesalahan',
          text: error.message,
          icon: 'error',
          confirmButtonText: 'OK',
          confirmButtonColor: '#4B68FB'
        });
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
  console.log("[DEBUG][editKelompok] params:", {
    nomor_kelompok,
    tahun_ajaran,
    jenis_sidang,
    id_matkul,
  });
  try {
    // Fetch kelompok data for editing
    const response = await fetch(
      `../../control/kelompok_read.php?nomor_kelompok=${nomor_kelompok}&tahun_ajaran=${tahun_ajaran}&jenis_sidang=${jenis_sidang}&id_matkul=${id_matkul}`
    );

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const responseText = await response.text();
    let kelompokData;

    try {
      kelompokData = JSON.parse(responseText);
    } catch (parseError) {
      console.error("JSON Parse Error for kelompok data:", parseError);
      throw new Error("Invalid response format from server");
    }

    if (
      kelompokData.status !== "ok" ||
      !kelompokData.data ||
      kelompokData.data.length === 0
    ) {
      throw new Error("Kelompok data tidak ditemukan");
    }

    // Switch to edit mode
    switchTab("tambah");

    // Populate form with existing data
    await populateEditForm(
      nomor_kelompok,
      tahun_ajaran,
      jenis_sidang,
      id_matkul
    );

    // Show modal
    const kelompokModalEl = document.getElementById("kelompokModal");
    if (kelompokModalInstance) {
      kelompokModalInstance.show();
    } else {
      kelompokModalEl.style.display = "block";
      kelompokModalEl.classList.add("show");
      document.body.classList.add("modal-open");
    }
  } catch (error) {
    console.error("Error loading kelompok for edit:", error);
    Swal.fire({
      title: 'Terjadi Kesalahan',
      text: error.message,
      icon: 'error',
      confirmButtonText: 'OK',
      confirmButtonColor: '#4B68FB'
    });
  }
};

// Function to update modal UI based on mode
function updateModalUI(mode) {
  const modalTitle = document.querySelector("#kelompokModal .modal-title");
  const submitButton = document.querySelector(
    "#kelompokForm button[type='submit']"
  );
  const newKelompokBtn = document.getElementById("newKelompokBtn");
  const tambahTabBtn = document.getElementById("tambah-tab-btn");

  if (mode === "edit") {
    if (modalTitle) modalTitle.textContent = "Edit Kelompok";
    if (submitButton) submitButton.textContent = "Update Kelompok";
    if (newKelompokBtn) newKelompokBtn.style.display = "inline-block";
    if (tambahTabBtn) tambahTabBtn.textContent = "Edit Kelompok";
  } else {
    if (modalTitle) modalTitle.textContent = "Tambah Kelompok Baru";
    if (submitButton) submitButton.textContent = "Buat Kelompok";
    if (newKelompokBtn) newKelompokBtn.style.display = "none";
    if (tambahTabBtn) tambahTabBtn.textContent = "Tambah Kelompok";
  }
}

// Function to populate form with existing kelompok data
async function populateEditForm(
  nomor_kelompok,
  tahun_ajaran,
  jenis_sidang,
  id_matkul
) {
  console.log("[DEBUG] populateEditForm called with:", {
    nomor_kelompok,
    tahun_ajaran,
    jenis_sidang,
    id_matkul,
  });

  // Fetch current anggota from backend
  let kelompokData = [];
  try {
    const params = new URLSearchParams({
      nomor_kelompok,
      tahun_ajaran,
      jenis_sidang,
      id_matkul,
    });
    const response = await fetch(
      `../../control/kelompok_read.php?${params.toString()}`
    );
    const data = await response.json();
    console.log("[DEBUG] Response from kelompok_read.php:", data);

    if (data.status === "ok") {
      kelompokData = data.data;
      console.log("[DEBUG] kelompokData received:", kelompokData);
    } else {
      Swal.fire({
        title: 'Gagal',
        text: data.message || "Gagal mengambil data kelompok",
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#4B68FB'
      });
      return;
    }
  } catch (err) {
    console.error("[ERROR] Failed to fetch kelompok data:", err);
    Swal.fire({
      title: 'Terjadi Kesalahan',
      text: err.message,
      icon: 'error',
      confirmButtonText: 'OK',
      confirmButtonColor: '#4B68FB'
    });
    return;
  }

  // Set form mode to edit
  const form = document.getElementById("kelompokForm");
  form.dataset.mode = "edit";
  form.dataset.nomor_kelompok = nomor_kelompok;
  form.dataset.tahun_ajaran = tahun_ajaran;
  form.dataset.jenis_sidang = jenis_sidang;
  form.dataset.id_matkul = id_matkul;

  // Update modal UI
  updateModalUI("edit");

  // Populate basic fields
  document.getElementById("nomor_kelompok").value = nomor_kelompok;
  document.getElementById("tahun_ajaran").value = tahun_ajaran;
  document.getElementById("jenis_sidang").value = jenis_sidang;

  // Set prodi based on first student's prodi
  if (kelompokData.length > 0) {
    const firstStudent = kelompokData[0];
    if (firstStudent.prodi) {
      // Map prodi to dropdown value
      const prodiMap = {
        TRPL: "Rekayasa Perangkat Lunak",
        "Rekayasa Perangkat Lunak": "Rekayasa Perangkat Lunak",
        RPL: "Rekayasa Perangkat Lunak",
        "Teknologi Rekayasa Perangkat Lunak": "Rekayasa Perangkat Lunak",
      };
      const prodiValue = prodiMap[firstStudent.prodi] || firstStudent.prodi;
      document.getElementById("kelompok_prodi").value = prodiValue;
    }
  }

  // Handle mata kuliah field
  if (jenis_sidang === "Semester") {
    showMatkulField(true);
    // Fetch mata kuliah data first, then set the value
    await fetchMataKuliah();
    // Set the value after mata kuliah data is loaded
    setTimeout(() => {
      document.getElementById("id_matkul").value = id_matkul;
    }, 100);
  } else {
    showMatkulField(false);
  }

  // Handle pembimbing field for Tugas Akhir
  if (jenis_sidang === "Tugas Akhir") {
    showPembimbingField(true);
    // Fetch and populate pembimbing data
    populatePembimbingData(
      nomor_kelompok,
      tahun_ajaran,
      jenis_sidang,
      id_matkul
    );
  } else {
    showPembimbingField(false);
  }

  // Populate anggota
  console.log("[DEBUG] About to call populateAnggotaData with:", kelompokData);
  populateAnggotaData(kelompokData);

  // Fetch mahasiswa data to update availability indicator without clearing anggota
  await fetchMahasiswaData();

  // Add a small delay to ensure DOM elements are properly rendered
  setTimeout(() => {
    console.log("[DEBUG] Final check - anggota elements:");
    for (let i = 1; i <= anggotaCount; i++) {
      const nimInput = document.getElementById(`anggota_${i}`);
      const namaDisplay = document.getElementById(`anggota_nama_${i}`);
      console.log(`[DEBUG] Anggota ${i}:`, {
        nimInput,
        namaDisplay,
        value: nimInput?.value,
        text: namaDisplay?.textContent,
      });
    }
  }, 100);
}

// Function to populate anggota data
function populateAnggotaData(kelompokData) {
  console.log("[DEBUG] populateAnggotaData called with:", kelompokData);

  // Clear existing anggota
  resetAnggotaInputs();

  // Add anggota based on data
  kelompokData.forEach((student, index) => {
    console.log(`[DEBUG] Processing student ${index + 1}:`, student);

    if (index > 0) {
      addAnggota(); // Add new anggota field for additional students
    }

    const anggotaIndex = index + 1;
    const nimInput = document.getElementById(`anggota_${anggotaIndex}`);
    const namaDisplay = document.getElementById(`anggota_nama_${anggotaIndex}`);

    console.log(
      `[DEBUG] Looking for elements: anggota_${anggotaIndex}, anggota_nama_${anggotaIndex}`
    );
    console.log(`[DEBUG] Found elements:`, { nimInput, namaDisplay });

    if (nimInput && namaDisplay) {
      nimInput.value = student.nim;
      namaDisplay.textContent = student.nama_mhs;

      // Mark this student as selected to prevent re-selection
      selectedMahasiswa[anggotaIndex] = {
        nim: student.nim,
        nama: student.nama_mhs,
        prodi: student.prodi,
      };

      console.log(`[DEBUG] Set anggota ${anggotaIndex}:`, {
        nim: student.nim,
        nama: student.nama_mhs,
        prodi: student.prodi,
      });
    } else {
      console.error(
        `[ERROR] Could not find elements for anggota ${anggotaIndex}`
      );
    }
  });

  // Update anggota count to match the number of students
  anggotaCount = kelompokData.length;
  console.log(`[DEBUG] Updated anggotaCount to: ${anggotaCount}`);
  updateToggleButtonsVisibility();
}

// Function to populate pembimbing data
async function populatePembimbingData(
  nomor_kelompok,
  tahun_ajaran,
  jenis_sidang,
  id_matkul
) {
  try {
    console.log("[DEBUG] populatePembimbingData called with:", {
      nomor_kelompok,
      tahun_ajaran,
      jenis_sidang,
      id_matkul,
    });

    // Fetch pembimbing data using POST
    const response = await fetch("../../control/get_pembimbing.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        nomor_kelompok: nomor_kelompok,
        tahun_ajaran: tahun_ajaran,
        jenis_sidang: jenis_sidang,
        id_matkul: id_matkul,
      }),
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const result = await response.json();
    console.log("[DEBUG] get_pembimbing response:", result);

    if (result.status !== "ok") {
      throw new Error(result.message || "Failed to get pembimbing data");
    }

    const pembimbingData = result.data || [];

    // Clear existing pembimbing
    resetDosenInputs();

    // Add pembimbing based on data (only for Tugas Akhir)
    if (jenis_sidang === "Tugas Akhir" && pembimbingData.length > 0) {
      pembimbingData.forEach((dosen, index) => {
        if (index > 0) {
          addDosen(); // Add new pembimbing field for additional dosen
        }

        const dosenIndex = index + 1;
        const dosenInput = document.getElementById(
          `dosen_pembimbing_${dosenIndex}`
        );
        const namaDisplay = document.getElementById(
          `dosen_nama_display_${dosenIndex}`
        );
        const hiddenInput = document.getElementById(
          `dosen_nomor_hidden_${dosenIndex}`
        );

        if (dosenInput && namaDisplay && hiddenInput) {
          dosenInput.value = dosen.nomor_dosen;
          namaDisplay.textContent = dosen.nama_dosen;
          hiddenInput.value = dosen.nomor_dosen;
        }
      });
    } else if (jenis_sidang === "Semester") {
      console.log("[DEBUG] Semester type - no pembimbing to populate");
    }

    updateToggleButtonsVisibility();
  } catch (error) {
    console.error("Error loading pembimbing data:", error);
  }
}

// Add SweetAlert2 import at the top if not present
if (typeof Swal === 'undefined') {
  var script = document.createElement('script');
  script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
  document.head.appendChild(script);
}
