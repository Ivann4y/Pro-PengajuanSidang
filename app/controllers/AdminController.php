<?php

namespace App\Controllers;

use App\Models\Admin;
use App\Models\Sidang;
use App\Models\Penjadwalan;
use App\Models\Notifikasi;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Kelompok;
use App\Services\AuthService;
use App\Services\FileUploadService;
use App\Middleware\SessionMiddleware;

class AdminController extends Controller
{
    private $adminModel;
    private $sidangModel;
    private $penjadwalanModel;
    private $notifikasiModel;
    private $mahasiswaModel;
    private $dosenModel;
    private $kelompokModel;
    private $authService;
    private $fileUploadService;

    public function __construct()
    {
        parent::__construct();
        $this->adminModel = new Admin();
        $this->sidangModel = new Sidang();
        $this->penjadwalanModel = new Penjadwalan();
        $this->notifikasiModel = new Notifikasi();
        $this->mahasiswaModel = new Mahasiswa();
        $this->dosenModel = new Dosen();
        $this->kelompokModel = new Kelompok();
        $this->authService = new AuthService();
        $this->fileUploadService = new FileUploadService();
        
        // Apply session middleware
        $sessionMiddleware = new SessionMiddleware();
        $sessionMiddleware->handle();
        
        // Check if user is logged in and is an admin
        if (!$this->authService->isLoggedIn() || $_SESSION['role'] !== 'admin') {
            header('Location: /login?role=admin');
            exit;
        }
    }

    /**
     * Display admin dashboard
     */
    public function dashboard()
    {
        $adminId = $_SESSION['user_id'];
        $admin = $this->adminModel->getById($adminId);
        
        // Get statistics
        $totalMahasiswa = $this->mahasiswaModel->getCount();
        $totalDosen = $this->dosenModel->getCount();
        $totalSidang = $this->sidangModel->getCount();
        $pengajuanPending = $this->sidangModel->getCountByStatus('pending');
        $sidangHariIni = $this->sidangModel->getCountSidangHariIni();
        
        // Get recent activities
        $aktivitasTerbaru = $this->sidangModel->getAktivitasTerbaru(10);
        
        // Get notifications
        $notifikasi = $this->notifikasiModel->getByAdmin($adminId, 5);
        
        $this->view->render('admin/dashboard', [
            'admin' => $admin,
            'totalMahasiswa' => $totalMahasiswa,
            'totalDosen' => $totalDosen,
            'totalSidang' => $totalSidang,
            'pengajuanPending' => $pengajuanPending,
            'sidangHariIni' => $sidangHariIni,
            'aktivitasTerbaru' => $aktivitasTerbaru,
            'notifikasi' => $notifikasi
        ]);
    }

    /**
     * Display thesis submission list
     */
    public function daftarPengajuan()
    {
        $pengajuan = $this->sidangModel->getAllPengajuan();
        $admin = $this->adminModel->getById($_SESSION['user_id']);
        
        $this->view->render('admin/daftar-pengajuan', [
            'pengajuan' => $pengajuan,
            'admin' => $admin
        ]);
    }

    /**
     * Display thesis submission detail
     */
    public function detailPengajuan($id)
    {
        $pengajuan = $this->sidangModel->getPengajuanById($id);
        
        if (!$pengajuan) {
            $this->setFlashMessage('error', 'Pengajuan tidak ditemukan');
            header('Location: /admin/daftar-pengajuan');
            exit;
        }

        $admin = $this->adminModel->getById($_SESSION['user_id']);
        $mahasiswa = $this->mahasiswaModel->getByNim($pengajuan['nim']);
        $dosen = $this->dosenModel->getByNip($pengajuan['dosen_id']);
        
        $this->view->render('admin/detail-pengajuan', [
            'pengajuan' => $pengajuan,
            'admin' => $admin,
            'mahasiswa' => $mahasiswa,
            'dosen' => $dosen
        ]);
    }

    /**
     * Handle thesis submission approval/rejection
     */
    public function evaluasiPengajuan($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/detail-pengajuan/' . $id);
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid request token');
            header('Location: /admin/detail-pengajuan/' . $id);
            exit;
        }

        $pengajuan = $this->sidangModel->getPengajuanById($id);
        
        if (!$pengajuan) {
            $this->setFlashMessage('error', 'Pengajuan tidak ditemukan');
            header('Location: /admin/daftar-pengajuan');
            exit;
        }

        $status = $this->sanitizeInput($_POST['status'] ?? '');
        $komentar = $this->sanitizeInput($_POST['komentar'] ?? '');

        // Validate status
        if (!in_array($status, ['approved', 'rejected'])) {
            $this->setFlashMessage('error', 'Status tidak valid');
            header('Location: /admin/detail-pengajuan/' . $id);
            exit;
        }

        $updateData = [
            'status' => $status,
            'komentar_admin' => $komentar,
            'tanggal_evaluasi_admin' => date('Y-m-d H:i:s')
        ];

        $success = $this->sidangModel->updatePengajuan($id, $updateData);
        
        if ($success) {
            // Send notification to student
            $this->notifikasiModel->create([
                'user_id' => $pengajuan['nim'],
                'user_type' => 'mahasiswa',
                'title' => 'Hasil Evaluasi Pengajuan',
                'message' => "Pengajuan sidang Anda telah {$status} oleh admin",
                'type' => 'evaluasi_pengajuan_admin',
                'is_read' => 0
            ]);

            $this->setFlashMessage('success', 'Evaluasi pengajuan berhasil disimpan');
            header('Location: /admin/daftar-pengajuan');
        } else {
            $this->setFlashMessage('error', 'Gagal menyimpan evaluasi');
            header('Location: /admin/detail-pengajuan/' . $id);
        }
        exit;
    }

    /**
     * Display thesis scheduling page
     */
    public function penjadwalan()
    {
        $pengajuanApproved = $this->sidangModel->getPengajuanByStatus('approved');
        $jadwalSidang = $this->penjadwalanModel->getAllJadwal();
        $admin = $this->adminModel->getById($_SESSION['user_id']);
        
        $this->view->render('admin/penjadwalan', [
            'pengajuanApproved' => $pengajuanApproved,
            'jadwalSidang' => $jadwalSidang,
            'admin' => $admin
        ]);
    }

    /**
     * Handle thesis scheduling
     */
    public function createPenjadwalan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/penjadwalan');
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid request token');
            header('Location: /admin/penjadwalan');
            exit;
        }

        $pengajuanId = (int)($_POST['pengajuan_id'] ?? 0);
        $tanggal = $this->sanitizeInput($_POST['tanggal'] ?? '');
        $waktu = $this->sanitizeInput($_POST['waktu'] ?? '');
        $ruangan = $this->sanitizeInput($_POST['ruangan'] ?? '');
        $dosenPenguji = $this->sanitizeInput($_POST['dosen_penguji'] ?? '');

        // Validate input
        if ($pengajuanId <= 0 || empty($tanggal) || empty($waktu) || empty($ruangan)) {
            $this->setFlashMessage('error', 'Semua field harus diisi');
            header('Location: /admin/penjadwalan');
            exit;
        }

        // Validate date format
        if (!strtotime($tanggal)) {
            $this->setFlashMessage('error', 'Format tanggal tidak valid');
            header('Location: /admin/penjadwalan');
            exit;
        }

        // Check if time slot is available
        $waktuSidang = $tanggal . ' ' . $waktu;
        if ($this->penjadwalanModel->isTimeSlotOccupied($waktuSidang, $ruangan)) {
            $this->setFlashMessage('error', 'Jadwal dan ruangan sudah terisi');
            header('Location: /admin/penjadwalan');
            exit;
        }

        $jadwalData = [
            'pengajuan_id' => $pengajuanId,
            'tanggal_sidang' => $tanggal,
            'waktu_sidang' => $waktu,
            'ruangan' => $ruangan,
            'dosen_penguji' => $dosenPenguji,
            'status' => 'scheduled',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $jadwalId = $this->penjadwalanModel->create($jadwalData);
        
        if ($jadwalId) {
            // Update pengajuan status
            $this->sidangModel->updatePengajuan($pengajuanId, ['status' => 'scheduled']);
            
            // Get pengajuan details for notification
            $pengajuan = $this->sidangModel->getPengajuanById($pengajuanId);
            
            // Send notification to student
            $this->notifikasiModel->create([
                'user_id' => $pengajuan['nim'],
                'user_type' => 'mahasiswa',
                'title' => 'Jadwal Sidang Telah Ditetapkan',
                'message' => "Sidang Anda telah dijadwalkan pada {$tanggal} pukul {$waktu} di ruangan {$ruangan}",
                'type' => 'jadwal_sidang',
                'is_read' => 0
            ]);

            // Send notification to lecturer
            if ($pengajuan['dosen_id']) {
                $this->notifikasiModel->create([
                    'user_id' => $pengajuan['dosen_id'],
                    'user_type' => 'dosen',
                    'title' => 'Jadwal Sidang Baru',
                    'message' => "Sidang mahasiswa telah dijadwalkan pada {$tanggal} pukul {$waktu}",
                    'type' => 'jadwal_sidang_dosen',
                    'is_read' => 0
                ]);
            }

            $this->setFlashMessage('success', 'Jadwal sidang berhasil dibuat');
            header('Location: /admin/penjadwalan');
        } else {
            $this->setFlashMessage('error', 'Gagal membuat jadwal sidang');
            header('Location: /admin/penjadwalan');
        }
        exit;
    }

    /**
     * Handle schedule update
     */
    public function updatePenjadwalan($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/penjadwalan');
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid request token');
            header('Location: /admin/penjadwalan');
            exit;
        }

        $jadwal = $this->penjadwalanModel->getById($id);
        
        if (!$jadwal) {
            $this->setFlashMessage('error', 'Jadwal tidak ditemukan');
            header('Location: /admin/penjadwalan');
            exit;
        }

        $tanggal = $this->sanitizeInput($_POST['tanggal'] ?? '');
        $waktu = $this->sanitizeInput($_POST['waktu'] ?? '');
        $ruangan = $this->sanitizeInput($_POST['ruangan'] ?? '');
        $dosenPenguji = $this->sanitizeInput($_POST['dosen_penguji'] ?? '');

        // Validate input
        if (empty($tanggal) || empty($waktu) || empty($ruangan)) {
            $this->setFlashMessage('error', 'Semua field harus diisi');
            header('Location: /admin/penjadwalan');
            exit;
        }

        // Check if time slot is available (excluding current schedule)
        $waktuSidang = $tanggal . ' ' . $waktu;
        if ($this->penjadwalanModel->isTimeSlotOccupied($waktuSidang, $ruangan, $id)) {
            $this->setFlashMessage('error', 'Jadwal dan ruangan sudah terisi');
            header('Location: /admin/penjadwalan');
            exit;
        }

        $updateData = [
            'tanggal_sidang' => $tanggal,
            'waktu_sidang' => $waktu,
            'ruangan' => $ruangan,
            'dosen_penguji' => $dosenPenguji,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $success = $this->penjadwalanModel->update($id, $updateData);
        
        if ($success) {
            // Get pengajuan details for notification
            $pengajuan = $this->sidangModel->getPengajuanById($jadwal['pengajuan_id']);
            
            // Send notification to student
            $this->notifikasiModel->create([
                'user_id' => $pengajuan['nim'],
                'user_type' => 'mahasiswa',
                'title' => 'Jadwal Sidang Diperbarui',
                'message' => "Jadwal sidang Anda telah diperbarui menjadi {$tanggal} pukul {$waktu} di ruangan {$ruangan}",
                'type' => 'update_jadwal_sidang',
                'is_read' => 0
            ]);

            $this->setFlashMessage('success', 'Jadwal sidang berhasil diperbarui');
        } else {
            $this->setFlashMessage('error', 'Gagal memperbarui jadwal sidang');
        }
        
        header('Location: /admin/penjadwalan');
        exit;
    }

    /**
     * Handle schedule deletion
     */
    public function deletePenjadwalan($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/penjadwalan');
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid request token');
            header('Location: /admin/penjadwalan');
            exit;
        }

        $jadwal = $this->penjadwalanModel->getById($id);
        
        if (!$jadwal) {
            $this->setFlashMessage('error', 'Jadwal tidak ditemukan');
            header('Location: /admin/penjadwalan');
            exit;
        }

        $success = $this->penjadwalanModel->delete($id);
        
        if ($success) {
            // Update pengajuan status back to approved
            $this->sidangModel->updatePengajuan($jadwal['pengajuan_id'], ['status' => 'approved']);
            
            // Get pengajuan details for notification
            $pengajuan = $this->sidangModel->getPengajuanById($jadwal['pengajuan_id']);
            
            // Send notification to student
            $this->notifikasiModel->create([
                'user_id' => $pengajuan['nim'],
                'user_type' => 'mahasiswa',
                'title' => 'Jadwal Sidang Dibatalkan',
                'message' => "Jadwal sidang Anda telah dibatalkan",
                'type' => 'batal_jadwal_sidang',
                'is_read' => 0
            ]);

            $this->setFlashMessage('success', 'Jadwal sidang berhasil dihapus');
        } else {
            $this->setFlashMessage('error', 'Gagal menghapus jadwal sidang');
        }
        
        header('Location: /admin/penjadwalan');
        exit;
    }

    /**
     * Display thesis defense list
     */
    public function daftarSidang()
    {
        $sidang = $this->sidangModel->getAllSidang();
        $admin = $this->adminModel->getById($_SESSION['user_id']);
        
        $this->view->render('admin/daftar-sidang', [
            'sidang' => $sidang,
            'admin' => $admin
        ]);
    }

    /**
     * Display thesis defense detail
     */
    public function detailSidang($id)
    {
        $sidang = $this->sidangModel->getById($id);
        
        if (!$sidang) {
            $this->setFlashMessage('error', 'Sidang tidak ditemukan');
            header('Location: /admin/daftar-sidang');
            exit;
        }

        $admin = $this->adminModel->getById($_SESSION['user_id']);
        $mahasiswa = $this->mahasiswaModel->getByNim($sidang['nim']);
        $dosen = $this->dosenModel->getByNip($sidang['dosen_id']);
        $penilaian = $this->penilaianModel->getBySidang($id);
        $jadwal = $this->penjadwalanModel->getBySidang($id);
        
        $this->view->render('admin/detail-sidang', [
            'sidang' => $sidang,
            'admin' => $admin,
            'mahasiswa' => $mahasiswa,
            'dosen' => $dosen,
            'penilaian' => $penilaian,
            'jadwal' => $jadwal
        ]);
    }

    /**
     * Display final grades management
     */
    public function nilaiAkhir()
    {
        $nilai = $this->penilaianModel->getAllNilaiAkhir();
        $admin = $this->adminModel->getById($_SESSION['user_id']);
        
        $this->view->render('admin/nilai-akhir', [
            'nilai' => $nilai,
            'admin' => $admin
        ]);
    }

    /**
     * Display notifications
     */
    public function notifikasi()
    {
        $adminId = $_SESSION['user_id'];
        $notifikasi = $this->notifikasiModel->getByAdmin($adminId);
        $admin = $this->adminModel->getById($adminId);
        
        $this->view->render('admin/notifikasi', [
            'notifikasi' => $notifikasi,
            'admin' => $admin
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotifikasiRead($id)
    {
        $adminId = $_SESSION['user_id'];
        $success = $this->notifikasiModel->markAsRead($id, $adminId, 'admin');
        
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
        $adminId = $_SESSION['user_id'];
        $admin = $this->adminModel->getById($adminId);
        
        $this->view->render('admin/profil', [
            'admin' => $admin
        ]);
    }

    /**
     * Handle profile update
     */
    public function updateProfil()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/profil');
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid request token');
            header('Location: /admin/profil');
            exit;
        }

        $adminId = $_SESSION['user_id'];
        $nama = $this->sanitizeInput($_POST['nama'] ?? '');
        $email = $this->sanitizeInput($_POST['email'] ?? '');
        $no_hp = $this->sanitizeInput($_POST['no_hp'] ?? '');

        // Validate input
        if (empty($nama) || empty($email)) {
            $this->setFlashMessage('error', 'Nama dan email harus diisi');
            header('Location: /admin/profil');
            exit;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlashMessage('error', 'Format email tidak valid');
            header('Location: /admin/profil');
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
                $adminId
            );
            
            if ($fotoPath) {
                $updateData['foto'] = $fotoPath;
            }
        }

        $success = $this->adminModel->update($adminId, $updateData);
        
        if ($success) {
            $this->setFlashMessage('success', 'Profil berhasil diperbarui');
        } else {
            $this->setFlashMessage('error', 'Gagal memperbarui profil');
        }
        
        header('Location: /admin/profil');
        exit;
    }

    /**
     * Display user management
     */
    public function kelolaUser()
    {
        $mahasiswa = $this->mahasiswaModel->getAll();
        $dosen = $this->dosenModel->getAll();
        $admin = $this->adminModel->getById($_SESSION['user_id']);
        
        $this->view->render('admin/kelola-user', [
            'mahasiswa' => $mahasiswa,
            'dosen' => $dosen,
            'admin' => $admin
        ]);
    }

    /**
     * Handle user creation
     */
    public function createUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/kelola-user');
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid request token');
            header('Location: /admin/kelola-user');
            exit;
        }

        $userType = $this->sanitizeInput($_POST['user_type'] ?? '');
        $nama = $this->sanitizeInput($_POST['nama'] ?? '');
        $email = $this->sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate input
        if (empty($userType) || empty($nama) || empty($email) || empty($password)) {
            $this->setFlashMessage('error', 'Semua field harus diisi');
            header('Location: /admin/kelola-user');
            exit;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlashMessage('error', 'Format email tidak valid');
            header('Location: /admin/kelola-user');
            exit;
        }

        // Validate password strength
        if (!$this->validatePasswordStrength($password)) {
            $this->setFlashMessage('error', 'Password tidak memenuhi kriteria keamanan');
            header('Location: /admin/kelola-user');
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userData = [
            'nama' => $nama,
            'email' => $email,
            'password' => $hashedPassword
        ];

        $success = false;
        switch ($userType) {
            case 'mahasiswa':
                $userData['nim'] = $this->sanitizeInput($_POST['nim'] ?? '');
                if (empty($userData['nim'])) {
                    $this->setFlashMessage('error', 'NIM harus diisi');
                    header('Location: /admin/kelola-user');
                    exit;
                }
                $success = $this->mahasiswaModel->create($userData);
                break;
            case 'dosen':
                $userData['nip'] = $this->sanitizeInput($_POST['nip'] ?? '');
                if (empty($userData['nip'])) {
                    $this->setFlashMessage('error', 'NIP harus diisi');
                    header('Location: /admin/kelola-user');
                    exit;
                }
                $success = $this->dosenModel->create($userData);
                break;
            default:
                $this->setFlashMessage('error', 'Tipe user tidak valid');
                header('Location: /admin/kelola-user');
                exit;
        }

        if ($success) {
            $this->setFlashMessage('success', 'User berhasil dibuat');
        } else {
            $this->setFlashMessage('error', 'Gagal membuat user');
        }
        
        header('Location: /admin/kelola-user');
        exit;
    }

    /**
     * Download document
     */
    public function downloadDocument($type, $filename)
    {
        $adminId = $_SESSION['user_id'];
        
        // Validate file access
        if (!$this->fileUploadService->validateFileAccess($type, $filename, $adminId, 'admin')) {
            $this->setFlashMessage('error', 'Akses file ditolak');
            header('Location: /admin/dashboard');
            exit;
        }

        $this->fileUploadService->downloadFile($type, $filename);
    }
} 