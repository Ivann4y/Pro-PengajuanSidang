// Mahasiswa Pengajuan JavaScript
let currentPengajuanData = null;

// Load all pengajuan data for logged-in mahasiswa
async function loadPengajuanList() {
  try {
    const response = await fetch("../../control/get_pengajuan_mahasiswa.php");
    const data = await response.json();

    if (data.error) {
      showError(data.error);
      return;
    }

    renderPengajuanList(data.data);
  } catch (error) {
    console.error("Error loading pengajuan list:", error);
    showError("Gagal memuat data pengajuan");
  }
}

// Render pengajuan list
function renderPengajuanList(pengajuanList) {
  const container = document.getElementById("pengajuan-list");
  if (!container) return;

  if (pengajuanList.length === 0) {
    container.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Belum ada pengajuan sidang. Silakan buat pengajuan baru.
            </div>
        `;
    return;
  }

  const html = pengajuanList
    .map(
      (item) => `
        <div class="card mb-3 pengajuan-card" data-key="${
          item.nomor_kelompok
        }_${item.tahun_ajaran}_${item.jenis_sidang}_${item.id_matkul}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-users"></i>
                    Kelompok ${item.nomor_kelompok} - ${item.nama_matkul}
                </h6>
                <span class="badge ${getStatusBadgeClass(
                  item.status_display
                )}">${item.status_display}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tahun Ajaran:</strong> ${
                          item.tahun_ajaran
                        }</p>
                        <p><strong>Jenis Sidang:</strong> ${
                          item.jenis_sidang
                        }</p>
                        <p><strong>Jumlah Anggota:</strong> ${
                          item.jumlah_anggota
                        } orang</p>
                    </div>
                    <div class="col-md-6">
                        ${
                          item.pengajuan
                            ? `
                            <p><strong>Judul:</strong> ${
                              item.pengajuan.judul || "-"
                            }</p>
                            <p><strong>Submitter:</strong> ${
                              item.pengajuan.nama_submitter || "-"
                            }</p>
                            <p><strong>Tanggal Submit:</strong> ${
                              formatDate(item.pengajuan.tanggal_submit) || "-"
                            }</p>
                        `
                            : `
                            <p><em>Belum ada pengajuan</em></p>
                        `
                        }
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary btn-sm" onclick="viewPengajuanDetail('${
                      item.nomor_kelompok
                    }', '${item.tahun_ajaran}', '${item.jenis_sidang}', '${
        item.id_matkul
      }')">
                        <i class="fas fa-eye"></i> Lihat Detail
                    </button>
                    ${
                      item.can_edit
                        ? `
                        <button class="btn btn-warning btn-sm" onclick="editPengajuan('${item.nomor_kelompok}', '${item.tahun_ajaran}', '${item.jenis_sidang}', '${item.id_matkul}')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    `
                        : ""
                    }
                    ${
                      item.can_submit
                        ? `
                        <button class="btn btn-success btn-sm" onclick="submitPengajuan('${item.nomor_kelompok}', '${item.tahun_ajaran}', '${item.jenis_sidang}', '${item.id_matkul}')">
                            <i class="fas fa-paper-plane"></i> Submit
                        </button>
                    `
                        : ""
                    }
                </div>
            </div>
        </div>
    `
    )
    .join("");

  container.innerHTML = html;
}

// Get status badge class
function getStatusBadgeClass(status) {
  switch (status) {
    case "Belum ada pengajuan":
      return "badge-secondary";
    case "Draft (sedang diedit)":
      return "badge-warning";
    case "Menunggu Review Dosen":
      return "badge-info";
    case "Disetujui":
      return "badge-success";
    case "Ditolak":
      return "badge-danger";
    default:
      return "badge-secondary";
  }
}

// Format date
function formatDate(dateString) {
  if (!dateString) return "-";
  const date = new Date(dateString);
  return date.toLocaleDateString("id-ID", {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

// View pengajuan detail
async function viewPengajuanDetail(
  nomor_kelompok,
  tahun_ajaran,
  jenis_sidang,
  id_matkul
) {
  try {
    const params = new URLSearchParams({
      nomor_kelompok: nomor_kelompok,
      tahun_ajaran: tahun_ajaran,
      jenis_sidang: jenis_sidang,
      id_matkul: id_matkul,
    });

    const response = await fetch(
      `../../control/get_pengajuan_kelompok.php?${params}`
    );
    const data = await response.json();

    if (data.error) {
      showError(data.error);
      return;
    }

    showPengajuanDetailModal(data);
  } catch (error) {
    console.error("Error loading pengajuan detail:", error);
    showError("Gagal memuat detail pengajuan");
  }
}

// Show pengajuan detail modal
function showPengajuanDetailModal(data) {
  const {
    pengajuan,
    anggota,
    matkul_info,
    can_edit,
    can_submit,
    is_submitter,
    has_draft,
    has_final,
  } = data;

  const modal = document.getElementById("pengajuanDetailModal");
  if (!modal) return;

  // Populate modal content
  document.getElementById("modal-nomor-kelompok").textContent =
    data.nomor_kelompok;
  document.getElementById("modal-tahun-ajaran").textContent = data.tahun_ajaran;
  document.getElementById("modal-jenis-sidang").textContent = data.jenis_sidang;
  document.getElementById("modal-nama-matkul").textContent =
    matkul_info?.nama_matkul || data.id_matkul;

  // Status info
  let statusText = "Belum ada pengajuan";
  if (has_final) {
    statusText = `Final: ${pengajuan.status}`;
  } else if (has_draft) {
    statusText = "Draft (sedang diedit)";
  }
  document.getElementById("modal-status").textContent = statusText;

  // Pengajuan data
  if (pengajuan) {
    document.getElementById("modal-judul").textContent = pengajuan.judul || "-";
    document.getElementById("modal-deskripsi").textContent =
      pengajuan.deskripsi || "-";
    document.getElementById("modal-tanggal-pengajuan").textContent =
      formatDate(pengajuan.tanggal_pengajuan) || "-";
    document.getElementById("modal-submitter").textContent =
      pengajuan.nama_submitter || pengajuan.submitter_nim || "-";

    if (pengajuan.file_path) {
      document.getElementById("modal-file").innerHTML = `
                <a href="views/mahasiswa/download_document.php?file=${pengajuan.file_path}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-download"></i> Download File
                </a>
            `;
    } else {
      document.getElementById("modal-file").textContent = "Tidak ada file";
    }
  } else {
    document.getElementById("modal-judul").textContent = "-";
    document.getElementById("modal-deskripsi").textContent = "-";
    document.getElementById("modal-tanggal-pengajuan").textContent = "-";
    document.getElementById("modal-submitter").textContent = "-";
    document.getElementById("modal-file").textContent = "Tidak ada file";
  }

  // Anggota list
  const anggotaList = document.getElementById("modal-anggota-list");
  anggotaList.innerHTML = anggota
    .map(
      (ang) => `
        <div class="anggota-item d-flex justify-content-between align-items-center p-2 border-bottom">
            <span>${ang.nama} (${ang.nim})</span>
            ${
              ang.nim === pengajuan?.submitter_nim
                ? '<span class="badge badge-primary">Submitter</span>'
                : ""
            }
        </div>
    `
    )
    .join("");

  // Action buttons
  const actionButtons = document.getElementById("modal-action-buttons");
  actionButtons.innerHTML = "";

  if (can_edit) {
    actionButtons.innerHTML += `
            <button class="btn btn-warning btn-sm" onclick="editPengajuan('${data.nomor_kelompok}', '${data.tahun_ajaran}', '${data.jenis_sidang}', '${data.id_matkul}')">
                <i class="fas fa-edit"></i> Edit
            </button>
        `;
  }

  if (can_submit) {
    actionButtons.innerHTML += `
            <button class="btn btn-success btn-sm" onclick="submitPengajuan('${data.nomor_kelompok}', '${data.tahun_ajaran}', '${data.jenis_sidang}', '${data.id_matkul}')">
                <i class="fas fa-paper-plane"></i> Submit
            </button>
        `;
  }

  // Show modal
  const modalInstance = new bootstrap.Modal(modal);
  modalInstance.show();
}

// Edit pengajuan
function editPengajuan(nomor_kelompok, tahun_ajaran, jenis_sidang, id_matkul) {
  // Redirect to edit page or open edit modal
  window.location.href = `views/mahasiswa/mEditPengajuan.php?nomor_kelompok=${nomor_kelompok}&tahun_ajaran=${tahun_ajaran}&jenis_sidang=${jenis_sidang}&id_matkul=${id_matkul}`;
}

// Submit pengajuan
async function submitPengajuan(
  nomor_kelompok,
  tahun_ajaran,
  jenis_sidang,
  id_matkul
) {
  // Show confirmation dialog
  if (
    !confirm(
      "Apakah Anda yakin ingin submit pengajuan ini? Setelah submit, pengajuan tidak dapat diedit lagi."
    )
  ) {
    return;
  }

  // Redirect to edit page for submission
  window.location.href = `views/mahasiswa/mEditPengajuan.php?nomor_kelompok=${nomor_kelompok}&tahun_ajaran=${tahun_ajaran}&jenis_sidang=${jenis_sidang}&id_matkul=${id_matkul}`;
}

// Show error message
function showError(message) {
  const alertDiv = document.createElement("div");
  alertDiv.className = "alert alert-danger alert-dismissible fade show";
  alertDiv.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;

  const container = document.getElementById("alert-container");
  if (container) {
    container.appendChild(alertDiv);

    // Auto remove after 5 seconds
    setTimeout(() => {
      alertDiv.remove();
    }, 5000);
  }
}

// Show success message
function showSuccess(message) {
  const alertDiv = document.createElement("div");
  alertDiv.className = "alert alert-success alert-dismissible fade show";
  alertDiv.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;

  const container = document.getElementById("alert-container");
  if (container) {
    container.appendChild(alertDiv);

    // Auto remove after 5 seconds
    setTimeout(() => {
      alertDiv.remove();
    }, 5000);
  }
}

// Initialize when page loads
document.addEventListener("DOMContentLoaded", function () {
  loadPengajuanList();

  // Setup search functionality
  const searchInput = document.getElementById("search-pengajuan");
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      const query = this.value.toLowerCase();
      const cards = document.querySelectorAll(".pengajuan-card");

      cards.forEach((card) => {
        const text = card.textContent.toLowerCase();
        if (text.includes(query)) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });
    });
  }

  // Setup filter functionality
  const filterSelect = document.getElementById("filter-status");
  if (filterSelect) {
    filterSelect.addEventListener("change", function () {
      const filterValue = this.value;
      const cards = document.querySelectorAll(".pengajuan-card");

      cards.forEach((card) => {
        const statusBadge = card.querySelector(".badge");
        const status = statusBadge ? statusBadge.textContent : "";

        if (filterValue === "" || status.includes(filterValue)) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });
    });
  }
});
