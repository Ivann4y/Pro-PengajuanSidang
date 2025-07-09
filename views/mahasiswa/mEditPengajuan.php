<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is mahasiswa
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || 
    !isset($_SESSION['role']) || $_SESSION['role'] !== 'mahasiswa') {
    $_SESSION['login_error'] = 'Anda harus login untuk mengakses halaman ini.';
    header("Location: ../../index.php");
    exit();
}

$nim_mahasiswa_logged_in = $_SESSION['nim'] ?? '';
if (empty($nim_mahasiswa_logged_in)) {
    die("Error: NIM mahasiswa tidak ditemukan dalam session. Silakan login kembali.");
}

// Get parameters from URL
$nomor_kelompok = $_GET['nomor_kelompok'] ?? '';
$tahun_ajaran = $_GET['tahun_ajaran'] ?? '';
$jenis_sidang = $_GET['jenis_sidang'] ?? '';
$id_matkul = $_GET['id_matkul'] ?? '';

if (empty($nomor_kelompok) || empty($tahun_ajaran) || empty($jenis_sidang) || empty($id_matkul)) {
    die("Error: Parameter tidak lengkap");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mahasiswa - Edit Pengajuan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../assets/css/mPengajuan.css">
    <link rel="stylesheet" href="../../extra/style.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <div id="NavSide">
        <div id="main-sidebar" class="NavSide__sidebar">
            <div class="NavSide__sidebar-brand">
                <img src="../../assets/img/WhiteAstra.png" alt="AstraTech Logo">
            </div>
            <ul class="NavSide__sidebar-nav">
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mBeranda.php"><span class="NavSide__sidebar-title fw-semibold">Beranda</span></a>
                </li>
                <li class="NavSide__sidebar-item NavSide__sidebar-item--active">
                    <b></b><b></b>
                    <a href="mPengajuan.php"><span class="NavSide__sidebar-title fw-semibold">Pengajuan</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="mSidang.php"><span class="NavSide__sidebar-title fw-semibold">Sidang</span></a>
                </li>
                <li class="NavSide__sidebar-item">
                    <b></b><b></b>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#logMBeranda"><span class="NavSide__sidebar-title fw-semibold">Keluar</span></a>
                </li>
            </ul>
        </div>

        <div class="NavSide__topbar">
            <div class="NavSide__toggle">
                <i class="bi bi-list open"></i>
                <i class="bi bi-x-lg close"></i>
            </div>
            <div class="header-icons">
                <i class="bi bi-bell-fill"></i>
                <div class="profile-icon">
                    <i class="bi bi-person-fill fs-5"></i>
                </div> 
            </div>
        

        <main class="NavSide__main-content" id="mEditPengajuan">
            <div class="container-fluid">
                <div class="row">
                    <div class="dashboard-header">
                        <h2 class="text-heading" style="color:black;">
                            Edit Pengajuan Sidang
                        </h2>
                        <p class="text-subheading">Edit pengajuan untuk kelompok <?php echo htmlspecialchars($nomor_kelompok); ?></p>
                    </div>
                </div>

                <!-- Alert Container -->
                <div id="alert-container"></div>

                <!-- Loading Spinner -->
                <div id="loading-spinner" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data pengajuan...</p>
                </div>

                <!-- Edit Form -->
                <div id="edit-form-container" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-edit"></i>
                                Form Edit Pengajuan
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="edit-pengajuan-form">
                                <input type="hidden" id="nomor-kelompok" value="<?php echo htmlspecialchars($nomor_kelompok); ?>">
                                <input type="hidden" id="tahun-ajaran" value="<?php echo htmlspecialchars($tahun_ajaran); ?>">
                                <input type="hidden" id="jenis-sidang" value="<?php echo htmlspecialchars($jenis_sidang); ?>">
                                <input type="hidden" id="id-matkul" value="<?php echo htmlspecialchars($id_matkul); ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="judul" class="form-label">Judul Laporan <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="judul" name="judul" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="file" class="form-label">File Laporan <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" id="file" name="file" accept=".pdf,.docx,.doc" required>
                                            <div class="form-text">Format: PDF, DOCX, DOC (Maksimal 10MB)</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi Singkat <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Info Kelompok</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <p><strong>Nomor Kelompok:</strong><br><?php echo htmlspecialchars($nomor_kelompok); ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <p><strong>Tahun Ajaran:</strong><br><?php echo htmlspecialchars($tahun_ajaran); ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <p><strong>Jenis Sidang:</strong><br><?php echo htmlspecialchars($jenis_sidang); ?></p>
                                        </div>
                                        <div class="col-md-3">
                                            <p><strong>Mata Kuliah:</strong><br><span id="nama-matkul-display">Loading...</span></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Anggota Kelompok</label>
                                    <div id="anggota-list" class="border rounded p-2">
                                        <!-- Anggota list will be populated here -->
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="mPengajuan.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    <div>
                                        <button type="button" class="btn btn-warning" onclick="saveDraft()">
                                            <i class="fas fa-save"></i> Simpan Draft
                                        </button>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-paper-plane"></i> Submit Final
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logMBeranda" tabindex="-1" aria-labelledby="logMBerandaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logMBerandaLabel">Konfirmasi Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin keluar?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="../../logout.php" class="btn btn-primary">Ya, Keluar</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar Toggle Logic
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector(".NavSide__toggle");
            const sidebar = document.getElementById("main-sidebar");
            
            if (menuToggle) {
                menuToggle.onclick = function() {
                    menuToggle.classList.toggle("NavSide__toggle--active");
                    sidebar.classList.toggle("NavSide__sidebar--active-mobile");
                };
            }
        });
    </script>
    
    <script>
        // Load pengajuan data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadPengajuanData();
        });
        
        async function loadPengajuanData() {
            const nomor_kelompok = document.getElementById('nomor-kelompok').value;
            const tahun_ajaran = document.getElementById('tahun-ajaran').value;
            const jenis_sidang = document.getElementById('jenis-sidang').value;
            const id_matkul = document.getElementById('id-matkul').value;
            
            try {
                const params = new URLSearchParams({
                    nomor_kelompok: nomor_kelompok,
                    tahun_ajaran: tahun_ajaran,
                    jenis_sidang: jenis_sidang,
                    id_matkul: id_matkul
                });
                
                const response = await fetch(`../../control/get_pengajuan_kelompok.php?${params}`);
                const data = await response.json();
                
                if (data.error) {
                    showError(data.error);
                    return;
                }
                
                populateForm(data);
                
            } catch (error) {
                console.error('Error loading pengajuan data:', error);
                showError('Gagal memuat data pengajuan');
            }
        }
        
        function populateForm(data) {
            const { pengajuan, anggota, matkul_info, can_edit } = data;
            
            // Hide loading spinner and show form
            document.getElementById('loading-spinner').style.display = 'none';
            document.getElementById('edit-form-container').style.display = 'block';
            
            // Populate mata kuliah name
            document.getElementById('nama-matkul-display').textContent = matkul_info?.nama_matkul || data.id_matkul;
            
            // Populate form fields if pengajuan exists
            if (pengajuan) {
                document.getElementById('judul').value = pengajuan.judul || '';
                document.getElementById('deskripsi').value = pengajuan.deskripsi || '';
            }
            
            // Populate anggota list
            const anggotaList = document.getElementById('anggota-list');
            anggotaList.innerHTML = anggota.map(ang => `
                <div class="anggota-item d-flex justify-content-between align-items-center p-2 border-bottom">
                    <span>${ang.nama} (${ang.nim})</span>
                    ${ang.nim === pengajuan?.submitter_nim ? '<span class="badge badge-primary">Submitter</span>' : ''}
                </div>
            `).join('');
            
            // Disable form if cannot edit
            if (!can_edit) {
                document.getElementById('edit-pengajuan-form').querySelectorAll('input, textarea').forEach(field => {
                    field.disabled = true;
                });
                document.querySelectorAll('button[type="submit"], button[onclick="saveDraft()"]').forEach(btn => {
                    btn.disabled = true;
                });
                showError('Tidak dapat mengedit pengajuan ini');
            }
        }
        
        // Handle form submission
        document.getElementById('edit-pengajuan-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            await submitPengajuan();
        });
        
        async function saveDraft() {
            const formData = new FormData();
            formData.append('nomor_kelompok', document.getElementById('nomor-kelompok').value);
            formData.append('tahun_ajaran', document.getElementById('tahun-ajaran').value);
            formData.append('jenis_sidang', document.getElementById('jenis-sidang').value);
            formData.append('id_matkul', document.getElementById('id-matkul').value);
            formData.append('judul', document.getElementById('judul').value);
            formData.append('deskripsi', document.getElementById('deskripsi').value);
            
            const fileInput = document.getElementById('file');
            if (fileInput.files[0]) {
                // Upload file first if there's a new file
                const uploadFormData = new FormData();
                uploadFormData.append('file', fileInput.files[0]);
                
                try {
                    const uploadResponse = await fetch('../../upload_file.php', {
                        method: 'POST',
                        body: uploadFormData
                    });
                    
                    const uploadResult = await uploadResponse.json();
                    
                    if (!uploadResult.success) {
                        showError(uploadResult.error || 'Gagal upload file');
                        return;
                    }
                    
                    formData.append('file_path', uploadResult.file_path);
                } catch (error) {
                    showError('Gagal upload file');
                    return;
                }
            }
            
            try {
                const response = await fetch('../../control/save_pengajuan_draft.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showSuccess('Draft berhasil disimpan');
                } else {
                    showError(result.error || 'Gagal menyimpan draft');
                }
                
            } catch (error) {
                console.error('Error saving draft:', error);
                showError('Gagal menyimpan draft');
            }
        }
        
        async function submitPengajuan() {
            // Show confirmation dialog
            if (!confirm('Apakah Anda yakin ingin submit pengajuan ini? Setelah submit, pengajuan tidak dapat diedit lagi.')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('nomor_kelompok', document.getElementById('nomor-kelompok').value);
            formData.append('tahun_ajaran', document.getElementById('tahun-ajaran').value);
            formData.append('jenis_sidang', document.getElementById('jenis-sidang').value);
            formData.append('id_matkul', document.getElementById('id-matkul').value);
            formData.append('judul', document.getElementById('judul').value);
            formData.append('deskripsi', document.getElementById('deskripsi').value);
            
            const fileInput = document.getElementById('file');
            if (fileInput.files[0]) {
                // Upload file first
                const uploadFormData = new FormData();
                uploadFormData.append('file', fileInput.files[0]);
                
                try {
                    const uploadResponse = await fetch('../../upload_file.php', {
                        method: 'POST',
                        body: uploadFormData
                    });
                    
                    const uploadResult = await uploadResponse.json();
                    
                    if (!uploadResult.success) {
                        showError(uploadResult.error || 'Gagal upload file');
                        return;
                    }
                    
                    formData.append('file_path', uploadResult.file_path);
                } catch (error) {
                    showError('Gagal upload file');
                    return;
                }
            }
            
            try {
                const response = await fetch('../../control/submit_pengajuan_final.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showSuccess('Pengajuan berhasil disubmit');
                    setTimeout(() => {
                        window.location.href = 'mPengajuan.php';
                    }, 2000);
                } else {
                    showError(result.error || 'Gagal submit pengajuan');
                }
                
            } catch (error) {
                console.error('Error submitting pengajuan:', error);
                showError('Gagal submit pengajuan');
            }
        }
        
        function showError(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            `;
            
            const container = document.getElementById('alert-container');
            if (container) {
                container.appendChild(alertDiv);
                
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            }
        }
        
        function showSuccess(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            `;
            
            const container = document.getElementById('alert-container');
            if (container) {
                container.appendChild(alertDiv);
                
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            }
        }
    </script>
</body>
</html>