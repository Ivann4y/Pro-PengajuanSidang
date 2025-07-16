<?php

namespace App\Controllers;

use App\Models\Mahasiswa;
use App\Models\Sidang;
use App\Models\Penilaian;
use App\Models\Notifikasi;
use App\Models\Kelompok;
use App\Services\AuthService;
use App\Services\FileUploadService;
use App\Middleware\SessionMiddleware;

class MahasiswaController extends Controller
{
    private $mahasiswaModel;
    private $sidangModel;
    private $penilaianModel;
    private $notifikasiModel;
    private $kelompokModel;
    private $authService;
    private $fileUploadService;

    public function __construct()
    {
        parent::__construct();
        $this->mahasiswaModel = new Mahasiswa();
        $this->sidangModel = new Sidang();
        $this->penilaianModel = new Penilaian();
        $this->notifikasiModel = new Notifikasi();
        $this->kelompokModel = new Kelompok();
        $this->authService = new AuthService();
        $this->fileUploadService = new FileUploadService();
        
        // Apply session middleware
        $sessionMiddleware = new SessionMiddleware();
        $sessionMiddleware->handle();
        
        // Check if user is logged in and is a student
        if (!$this->authService->isLoggedIn() || $_SESSION['role'] !== 'mahasiswa') {
            header('Location: /login?role=mahasiswa');
            exit;
        }
    }

    /**
     * Display student dashboard
     */
    public function dashboard()
    {
        $nim = $_SESSION['user_id'];
        $mahasiswa = $this->mahasiswaModel->getByNim($nim);
        
        // Get upcoming thesis defense
        $sidangMendatang = $this->sidangModel->getSidangMendatangByMahasiswa($nim);
        
        // Get thesis submission status
        $statusPengajuan = $this->sidangModel->getStatusPengajuanByMahasiswa($nim);
        
        // Get pending tasks
        $tanggungan = $this->sidangModel->getTanggunganByMahasiswa($nim);
        
        // Get notifications
        $notifikasi = $this->notifikasiModel->getByMahasiswa($nim, 5);
        
        $this->view->render('mahasiswa/dashboard', [
            'mahasiswa' => $mahasiswa,
            'sidangMendatang' => $sidangMendatang,
            'statusPengajuan' => $statusPengajuan,
            'tanggungan' => $tanggungan,
            'notifikasi' => $notifikasi
        ]);
    }

    /**
     * Display thesis submission form
     */
    public function pengajuan()
    {
        $nim = $_SESSION['user_id'];
        $mahasiswa = $this->mahasiswaModel->getByNim($nim);
        $kelompok = $this->kelompokModel->getByMahasiswa($nim);
        
        // Check if student already has a pending submission
        $existingPengajuan = $this->sidangModel->getPengajuanByMahasiswa($nim);
        
        $this->view->render('mahasiswa/pengajuan', [
            'mahasiswa' => $mahasiswa,
            'kelompok' => $kelompok,
            'existingPengajuan' => $existingPengajuan
        ]);
    }

    /**
     * Handle thesis submission
     */
    public function submitPengajuan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /mahasiswa/pengajuan');
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid request token');
            header('Location: /mahasiswa/pengajuan');
            exit;
        }

        $nim = $_SESSION['user_id'];
        $judul = $this->sanitizeInput($_POST['judul'] ?? '');
        $abstrak = $this->sanitizeInput($_POST['abstrak'] ?? '');
        $metodologi = $this->sanitizeInput($_POST['metodologi'] ?? '');
        $kelompokId = (int)($_POST['kelompok_id'] ?? 0);

        // Validate input
        if (empty($judul) || empty($abstrak) || empty($metodologi) || $kelompokId <= 0) {
            $this->setFlashMessage('error', 'Semua field harus diisi');
            header('Location: /mahasiswa/pengajuan');
            exit;
        }

        // Handle file upload
        $laporanPath = '';
        if (isset($_FILES['laporan']) && $_FILES['laporan']['error'] === UPLOAD_ERR_OK) {
            $laporanPath = $this->fileUploadService->uploadDocument(
                $_FILES['laporan'],
                'laporan',
                $nim
            );
            
            if (!$laporanPath) {
                $this->setFlashMessage('error', 'Gagal mengupload file laporan');
                header('Location: /mahasiswa/pengajuan');
                exit;
            }
        } else {
            $this->setFlashMessage('error', 'File laporan wajib diupload');
            header('Location: /mahasiswa/pengajuan');
            exit;
        }

        // Create thesis submission
        $pengajuanData = [
            'nim' => $nim,
            'judul' => $judul,
            'abstrak' => $abstrak,
            'metodologi' => $metodologi,
            'kelompok_id' => $kelompokId,
            'laporan_path' => $laporanPath,
            'status' => 'pending',
            'tanggal_pengajuan' => date('Y-m-d H:i:s')
        ];

        $pengajuanId = $this->sidangModel->createPengajuan($pengajuanData);
        
        if ($pengajuanId) {
            // Send notification to admin (contoh: ke semua admin, bisa di-loop jika multi admin)
            $adminList = $this->adminModel->getAll();
            foreach ($adminList as $admin) {
                $this->notifikasiModel->createNotifikasi([
                    'user_id' => $admin['id_admin'],
                    'user_role' => 'admin',
                    'judul' => 'Pengajuan Sidang Baru',
                    'pesan' => "Mahasiswa {$mahasiswa['nama']} telah mengajukan sidang dengan judul: {$judul}",
                    'url' => '/admin/daftar-pengajuan',
                    'status' => 'unread'
                ]);
            }
            // Notifikasi ke mahasiswa sendiri
            $this->notifikasiModel->createNotifikasi([
                'user_id' => $nim,
                'user_role' => 'mahasiswa',
                'judul' => 'Pengajuan Sidang Dikirim',
                'pesan' => 'Pengajuan sidang Anda telah dikirim dan menunggu verifikasi.',
                'url' => '/mahasiswa/kelola-pengajuan',
                'status' => 'unread'
            ]);

            // Notifikasi ke dosen pembimbing/penguji
            $kelompok = $this->kelompokModel->getByMahasiswa($nim);
            if ($kelompok && !empty($kelompok['dosen_pembimbing'])) {
                $dosenIds = explode(',', $kelompok['dosen_pembimbing']);
                foreach ($dosenIds as $dosenId) {
                    $this->notifikasiModel->createNotifikasi([
                        'user_id' => trim($dosenId),
                        'user_role' => 'dosen',
                        'judul' => 'Pengaju an Sidang Baru',
                        'pesan' => "Ada pengajuan sidang baru dari mahasiswa {$mahasiswa['nama']}.",
                        'url' => '/dosen/daftar-pengajuan',
                        'status' => 'unread'
                    ]);
                }
            }

            $this->setFlashMessage('success', 'Pengajuan sidang berhasil dikirim');
            header('Location: /mahasiswa/kelola-pengajuan');
        } else {
            $this->setFlashMessage('error', 'Gagal mengirim pengajuan sidang');
            header('Location: /mahasiswa/pengajuan');
        }
        exit;
    }

    /**
     * Display thesis management page
     */
    public function kelolaPengajuan()
    {
        $nim = $_SESSION['user_id'];
        $pengajuan = $this->sidangModel->getPengajuanByMahasiswa($nim);
        $mahasiswa = $this->mahasiswaModel->getByNim($nim);
        
        $this->view->render('mahasiswa/kelola-pengajuan', [
            'pengajuan' => $pengajuan,
            'mahasiswa' => $mahasiswa
        ]);
    }

    /**
     * Display thesis submission edit form
     */
    public function editPengajuan($id)
    {
        $nim = $_SESSION['user_id'];
        $pengajuan = $this->sidangModel->getPengajuanById($id);
        
        // Check if pengajuan belongs to current student
        if (!$pengajuan || $pengajuan['nim'] !== $nim) {
            $this->setFlashMessage('error', 'Pengajuan tidak ditemukan');
            header('Location: /mahasiswa/kelola-pengajuan');
            exit;
        }

        // Check if pengajuan can be edited (only pending status)
        if ($pengajuan['status'] !== 'pending') {
            $this->setFlashMessage('error', 'Pengajuan tidak dapat diedit');
            header('Location: /mahasiswa/kelola-pengajuan');
            exit;
        }

        $mahasiswa = $this->mahasiswaModel->getByNim($nim);
        $kelompok = $this->kelompokModel->getByMahasiswa($nim);
        
        $this->view->render('mahasiswa/edit-pengajuan', [
            'pengajuan' => $pengajuan,
            'mahasiswa' => $mahasiswa,
            'kelompok' => $kelompok
        ]);
    }

    /**
     * Handle thesis submission update
     */
    public function updatePengajuan($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /mahasiswa/edit-pengajuan/' . $id);
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid request token');
            header('Location: /mahasiswa/edit-pengajuan/' . $id);
            exit;
        }

        $nim = $_SESSION['user_id'];
        $pengajuan = $this->sidangModel->getPengajuanById($id);
        
        // Check if pengajuan belongs to current student
        if (!$pengajuan || $pengajuan['nim'] !== $nim) {
            $this->setFlashMessage('error', 'Pengajuan tidak ditemukan');
            header('Location: /mahasiswa/kelola-pengajuan');
            exit;
        }

        $judul = $this->sanitizeInput($_POST['judul'] ?? '');
        $abstrak = $this->sanitizeInput($_POST['abstrak'] ?? '');
        $metodologi = $this->sanitizeInput($_POST['metodologi'] ?? '');

        // Validate input
        if (empty($judul) || empty($abstrak) || empty($metodologi)) {
            $this->setFlashMessage('error', 'Semua field harus diisi');
            header('Location: /mahasiswa/edit-pengajuan/' . $id);
            exit;
        }

        $updateData = [
            'judul' => $judul,
            'abstrak' => $abstrak,
            'metodologi' => $metodologi,
            'tanggal_update' => date('Y-m-d H:i:s')
        ];

        // Handle new file upload if provided
        if (isset($_FILES['laporan']) && $_FILES['laporan']['error'] === UPLOAD_ERR_OK) {
            $laporanPath = $this->fileUploadService->uploadDocument(
                $_FILES['laporan'],
                'laporan',
                $nim
            );
            
            if ($laporanPath) {
                $updateData['laporan_path'] = $laporanPath;
            }
        }

        $success = $this->sidangModel->updatePengajuan($id, $updateData);
        
        if ($success) {
            $this->setFlashMessage('success', 'Pengajuan sidang berhasil diperbarui');
            header('Location: /mahasiswa/kelola-pengajuan');
        } else {
            $this->setFlashMessage('error', 'Gagal memperbarui pengajuan sidang');
            header('Location: /mahasiswa/edit-pengajuan/' . $id);
        }
        exit;
    }

    /**
     * Display thesis defense details
     */
    public function detailSidang($id)
    {
        $nim = $_SESSION['user_id'];
        $sidang = $this->sidangModel->getById($id);
        
        // Check if sidang belongs to current student
        if (!$sidang || $sidang['nim'] !== $nim) {
            $this->setFlashMessage('error', 'Sidang tidak ditemukan');
            header('Location: /mahasiswa/dashboard');
            exit;
        }

        $mahasiswa = $this->mahasiswaModel->getByNim($nim);
        $penilaian = $this->penilaianModel->getBySidang($id);
        $detailSidang = $this->sidangModel->getDetailSidang($id);
        
        $this->view->render('mahasiswa/detail-sidang', [
            'sidang' => $sidang,
            'mahasiswa' => $mahasiswa,
            'penilaian' => $penilaian,
            'detailSidang' => $detailSidang
        ]);
    }

    /**
     * Display thesis defense schedule
     */
    public function sidang()
    {
        $nim = $_SESSION['user_id'];
        $sidang = $this->sidangModel->getSidangByMahasiswa($nim);
        $mahasiswa = $this->mahasiswaModel->getByNim($nim);
        
        $this->view->render('mahasiswa/sidang', [
            'sidang' => $sidang,
            'mahasiswa' => $mahasiswa
        ]);
    }

    /**
     * Display final grades
     */
    public function nilaiAkhir()
    {
        $nim = $_SESSION['user_id'];
        $nilai = $this->penilaianModel->getNilaiAkhirByMahasiswa($nim);
        $mahasiswa = $this->mahasiswaModel->getByNim($nim);
        
        $this->view->render('mahasiswa/nilai-akhir', [
            'nilai' => $nilai,
            'mahasiswa' => $mahasiswa
        ]);
    }

    /**
     * Display notifications
     */
    public function notifikasi()
    {
        $nim = $_SESSION['user_id'];
        $notifikasi = $this->notifikasiModel->getByMahasiswa($nim);
        $mahasiswa = $this->mahasiswaModel->getByNim($nim);
        
        $this->view->render('mahasiswa/notifikasi', [
            'notifikasi' => $notifikasi,
            'mahasiswa' => $mahasiswa
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotifikasiRead($id)
    {
        $nim = $_SESSION['user_id'];
        $success = $this->notifikasiModel->markAsRead($id, $nim, 'mahasiswa');
        
        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menandai notifikasi sebagai dibaca']);
        }
    }

    /**
     * Display profile page
     */
    public function profil()
    {
        $nim = $_SESSION['user_id'];
        $mahasiswa = $this->mahasiswaModel->getByNim($nim);
        
        $this->view->render('mahasiswa/profil', [
            'mahasiswa' => $mahasiswa
        ]);
    }

    /**
     * Handle profile update
     */
    public function updateProfil()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /mahasiswa/profil');
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid request token');
            header('Location: /mahasiswa/profil');
            exit;
        }

        $nim = $_SESSION['user_id'];
        $nama = $this->sanitizeInput($_POST['nama'] ?? '');
        $email = $this->sanitizeInput($_POST['email'] ?? '');
        $no_hp = $this->sanitizeInput($_POST['no_hp'] ?? '');

        // Validate input
        if (empty($nama) || empty($email)) {
            $this->setFlashMessage('error', 'Nama dan email harus diisi');
            header('Location: /mahasiswa/profil');
            exit;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlashMessage('error', 'Format email tidak valid');
            header('Location: /mahasiswa/profil');
            exit;
        }

        $updateData = [
            'nama' => $nama,
            'email' => $email,
            'no_hp' => $no_hp
        ];

        // Handle profile picture upload
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $fotoPath = $this->fileUploadService->uploadImage(
                $_FILES['foto'],
                'profil',
                $nim
            );
            
            if ($fotoPath) {
                $updateData['foto'] = $fotoPath;
            }
        }

        $success = $this->mahasiswaModel->update($nim, $updateData);
        
        if ($success) {
            $this->setFlashMessage('success', 'Profil berhasil diperbarui');
        } else {
            $this->setFlashMessage('error', 'Gagal memperbarui profil');
        }
        
        header('Location: /mahasiswa/profil');
        exit;
    }

    /**
     * Display revision submission form
     */
    public function perbaikan($sidangId)
    {
        $nim = $_SESSION['user_id'];
        $sidang = $this->sidangModel->getById($sidangId);
        
        // Check if sidang belongs to current student
        if (!$sidang || $sidang['nim'] !== $nim) {
            $this->setFlashMessage('error', 'Sidang tidak ditemukan');
            header('Location: /mahasiswa/dashboard');
            exit;
        }

        $mahasiswa = $this->mahasiswaModel->getByNim($nim);
        
        $this->view->render('mahasiswa/perbaikan', [
            'sidang' => $sidang,
            'mahasiswa' => $mahasiswa
        ]);
    }

    /**
     * Handle revision submission
     */
    public function submitPerbaikan($sidangId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /mahasiswa/perbaikan/' . $sidangId);
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid request token');
            header('Location: /mahasiswa/perbaikan/' . $sidangId);
            exit;
        }

        $nim = $_SESSION['user_id'];
        $sidang = $this->sidangModel->getById($sidangId);
        
        // Check if sidang belongs to current student
        if (!$sidang || $sidang['nim'] !== $nim) {
            $this->setFlashMessage('error', 'Sidang tidak ditemukan');
            header('Location: /mahasiswa/dashboard');
            exit;
        }

        $catatan = $this->sanitizeInput($_POST['catatan'] ?? '');

        // Handle file upload
        $revisiPath = '';
        if (isset($_FILES['file_revisi']) && $_FILES['file_revisi']['error'] === UPLOAD_ERR_OK) {
            $revisiPath = $this->fileUploadService->uploadDocument(
                $_FILES['file_revisi'],
                'revisi',
                $nim
            );
            
            if (!$revisiPath) {
                $this->setFlashMessage('error', 'Gagal mengupload file revisi');
                header('Location: /mahasiswa/perbaikan/' . $sidangId);
                exit;
            }
        } else {
            $this->setFlashMessage('error', 'File revisi wajib diupload');
            header('Location: /mahasiswa/perbaikan/' . $sidangId);
            exit;
        }

        // Create revision record
        $revisiData = [
            'sidang_id' => $sidangId,
            'nim' => $nim,
            'file_revisi' => $revisiPath,
            'catatan' => $catatan,
            'tanggal_submit' => date('Y-m-d H:i:s'),
            'status' => 'pending'
        ];

        $revisiId = $this->sidangModel->createRevisi($revisiData);
        
        if ($revisiId) {
            // Update sidang status
            $this->sidangModel->updateStatus($sidangId, 'revisi_submitted');
            
            // Send notification to all examiners (dosen penguji bisa lebih dari satu, pisahkan dengan koma)
            $pengujiList = array_map('trim', explode(',', $sidang['dosen_penguji']));
            foreach ($pengujiList as $pengujiNip) {
                if ($pengujiNip) {
                    $this->notifikasiModel->createNotifikasi([
                        'user_id' => $pengujiNip,
                        'user_role' => 'dosen',
                        'judul' => 'Revisi Sidang Dikirim',
                        'pesan' => 'Mahasiswa telah mengirimkan revisi sidang. Silakan cek dokumen revisi.',
                        'url' => '/dosen/dokumen-revisi',
                        'status' => 'unread'
                    ]);
                }
            }
            $this->setFlashMessage('success', 'Revisi berhasil dikirim');
            header('Location: /mahasiswa/perbaikan/' . $sidangId);
        } else {
            $this->setFlashMessage('error', 'Gagal mengirim revisi');
            header('Location: /mahasiswa/perbaikan/' . $sidangId);
        }
        exit;
    }

    /**
     * Download document
     */
    public function downloadDocument($type, $filename)
    {
        $nim = $_SESSION['user_id'];
        
        // Validate file access
        if (!$this->fileUploadService->validateFileAccess($type, $filename, $nim)) {
            $this->setFlashMessage('error', 'Akses file ditolak');
            header('Location: /mahasiswa/dashboard');
            exit;
        }

        $this->fileUploadService->downloadFile($type, $filename);
    }
} 