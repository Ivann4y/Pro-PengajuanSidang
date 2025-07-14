<?php

namespace App\Controllers;

use App\Models\Dosen;
use App\Models\Sidang;
use App\Models\Penilaian;
use App\Models\Notifikasi;
use App\Models\Kelompok;
use App\Services\AuthService;
use App\Services\FileUploadService;
use App\Middleware\SessionMiddleware;

class DosenController extends Controller
{
    private $dosenModel;
    private $sidangModel;
    private $penilaianModel;
    private $notifikasiModel;
    private $kelompokModel;
    private $authService;
    private $fileUploadService;

    public function __construct()
    {
        parent::__construct();
        $this->dosenModel = new Dosen();
        $this->sidangModel = new Sidang();
        $this->penilaianModel = new Penilaian();
        $this->notifikasiModel = new Notifikasi();
        $this->kelompokModel = new Kelompok();
        $this->authService = new AuthService();
        $this->fileUploadService = new FileUploadService();
        
        // Apply session middleware
        $sessionMiddleware = new SessionMiddleware();
        $sessionMiddleware->handle();
        
        // Check if user is logged in and is a lecturer
        if (!$this->authService->isLoggedIn() || $_SESSION['role'] !== 'dosen') {
            header('Location: /login?role=dosen');
            exit;
        }
    }

    /**
     * Display lecturer dashboard
     */
    public function dashboard()
    {
        $nip = $_SESSION['user_id'];
        $dosen = $this->dosenModel->getByNip($nip);
        
        // Get upcoming thesis defenses
        $sidangMendatang = $this->sidangModel->getSidangMendatangByDosen($nip);
        
        // Get pending evaluations
        $evaluasiPending = $this->sidangModel->getEvaluasiPendingByDosen($nip);
        
        // Get notifications
        $notifikasi = $this->notifikasiModel->getByDosen($nip, 5);
        
        $this->view->render('dosen/dashboard', [
            'dosen' => $dosen,
            'sidangMendatang' => $sidangMendatang,
            'evaluasiPending' => $evaluasiPending,
            'notifikasi' => $notifikasi
        ]);
    }

    /**
     * Display thesis submission list
     */
    public function daftarPengajuan()
    {
        $nip = $_SESSION['user_id'];
        $pengajuan = $this->sidangModel->getPengajuanByDosen($nip);
        $dosen = $this->dosenModel->getByNip($nip);
        
        $this->view->render('dosen/daftar-pengajuan', [
            'pengajuan' => $pengajuan,
            'dosen' => $dosen
        ]);
    }

    /**
     * Display thesis submission detail
     */
    public function detailPengajuan($id)
    {
        $nip = $_SESSION['user_id'];
        $pengajuan = $this->sidangModel->getPengajuanById($id);
        
        // Check if lecturer has access to this submission
        if (!$pengajuan || $pengajuan['dosen_id'] !== $nip) {
            $this->setFlashMessage('error', 'Pengajuan tidak ditemukan');
            header('Location: /dosen/daftar-pengajuan');
            exit;
        }

        $dosen = $this->dosenModel->getByNip($nip);
        $mahasiswa = $this->sidangModel->getMahasiswaByPengajuan($id);
        
        $this->view->render('dosen/detail-pengajuan', [
            'pengajuan' => $pengajuan,
            'dosen' => $dosen,
            'mahasiswa' => $mahasiswa
        ]);
    }

    /**
     * Handle thesis submission approval/rejection
     */
    public function evaluasiPengajuan($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dosen/detail-pengajuan/' . $id);
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid request token');
            header('Location: /dosen/detail-pengajuan/' . $id);
            exit;
        }

        $nip = $_SESSION['user_id'];
        $pengajuan = $this->sidangModel->getPengajuanById($id);
        
        // Check if lecturer has access to this submission
        if (!$pengajuan || $pengajuan['dosen_id'] !== $nip) {
            $this->setFlashMessage('error', 'Pengajuan tidak ditemukan');
            header('Location: /dosen/daftar-pengajuan');
            exit;
        }

        $status = $this->sanitizeInput($_POST['status'] ?? '');
        $komentar = $this->sanitizeInput($_POST['komentar'] ?? '');

        // Validate status
        if (!in_array($status, ['approved', 'rejected'])) {
            $this->setFlashMessage('error', 'Status tidak valid');
            header('Location: /dosen/detail-pengajuan/' . $id);
            exit;
        }

        $updateData = [
            'status' => $status,
            'komentar_dosen' => $komentar,
            'tanggal_evaluasi' => date('Y-m-d H:i:s'),
            'dosen_id' => $nip
        ];

        $success = $this->sidangModel->updatePengajuan($id, $updateData);
        
        if ($success) {
            // Send notification to student
            $this->notifikasiModel->create([
                'user_id' => $pengajuan['nim'],
                'user_type' => 'mahasiswa',
                'title' => 'Hasil Evaluasi Pengajuan',
                'message' => "Pengajuan sidang Anda telah {$status}",
                'type' => 'evaluasi_pengajuan',
                'is_read' => 0
            ]);

            $this->setFlashMessage('success', 'Evaluasi pengajuan berhasil disimpan');
            header('Location: /dosen/daftar-pengajuan');
        } else {
            $this->setFlashMessage('error', 'Gagal menyimpan evaluasi');
            header('Location: /dosen/detail-pengajuan/' . $id);
        }
        exit;
    }

    /**
     * Display thesis defense list
     */
    public function daftarSidang()
    {
        $nip = $_SESSION['user_id'];
        $sidang = $this->sidangModel->getSidangByDosen($nip);
        $dosen = $this->dosenModel->getByNip($nip);
        
        $this->view->render('dosen/daftar-sidang', [
            'sidang' => $sidang,
            'dosen' => $dosen
        ]);
    }

    /**
     * Display thesis defense evaluation form
     */
    public function evaluasiSidang($id)
    {
        $nip = $_SESSION['user_id'];
        $sidang = $this->sidangModel->getById($id);
        
        // Check if lecturer has access to this defense
        if (!$sidang || $sidang['dosen_id'] !== $nip) {
            $this->setFlashMessage('error', 'Sidang tidak ditemukan');
            header('Location: /dosen/daftar-sidang');
            exit;
        }

        $dosen = $this->dosenModel->getByNip($nip);
        $mahasiswa = $this->sidangModel->getMahasiswaBySidang($id);
        $penilaian = $this->penilaianModel->getBySidang($id);
        
        $this->view->render('dosen/evaluasi-sidang', [
            'sidang' => $sidang,
            'dosen' => $dosen,
            'mahasiswa' => $mahasiswa,
            'penilaian' => $penilaian
        ]);
    }

    /**
     * Handle thesis defense evaluation
     */
    public function submitEvaluasi($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dosen/evaluasi-sidang/' . $id);
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid request token');
            header('Location: /dosen/evaluasi-sidang/' . $id);
            exit;
        }

        $nip = $_SESSION['user_id'];
        $sidang = $this->sidangModel->getById($id);
        
        // Check if lecturer has access to this defense
        if (!$sidang || $sidang['dosen_id'] !== $nip) {
            $this->setFlashMessage('error', 'Sidang tidak ditemukan');
            header('Location: /dosen/daftar-sidang');
            exit;
        }

        // Get evaluation scores
        $nilai_presentasi = (float)($_POST['nilai_presentasi'] ?? 0);
        $nilai_materi = (float)($_POST['nilai_materi'] ?? 0);
        $nilai_metodologi = (float)($_POST['nilai_metodologi'] ?? 0);
        $nilai_hasil = (float)($_POST['nilai_hasil'] ?? 0);
        $nilai_keseluruhan = (float)($_POST['nilai_keseluruhan'] ?? 0);
        $komentar = $this->sanitizeInput($_POST['komentar'] ?? '');
        $status = $this->sanitizeInput($_POST['status'] ?? '');

        // Validate scores
        if ($nilai_presentasi < 0 || $nilai_presentasi > 100 ||
            $nilai_materi < 0 || $nilai_materi > 100 ||
            $nilai_metodologi < 0 || $nilai_metodologi > 100 ||
            $nilai_hasil < 0 || $nilai_hasil > 100 ||
            $nilai_keseluruhan < 0 || $nilai_keseluruhan > 100) {
            $this->setFlashMessage('error', 'Nilai harus antara 0-100');
            header('Location: /dosen/evaluasi-sidang/' . $id);
            exit;
        }

        // Validate status
        if (!in_array($status, ['lulus', 'lulus_dengan_revisi', 'tidak_lulus'])) {
            $this->setFlashMessage('error', 'Status tidak valid');
            header('Location: /dosen/evaluasi-sidang/' . $id);
            exit;
        }

        // Check if evaluation already exists
        $existingPenilaian = $this->penilaianModel->getBySidang($id);
        
        $penilaianData = [
            'sidang_id' => $id,
            'dosen_id' => $nip,
            'nilai_presentasi' => $nilai_presentasi,
            'nilai_materi' => $nilai_materi,
            'nilai_metodologi' => $nilai_metodologi,
            'nilai_hasil' => $nilai_hasil,
            'nilai_keseluruhan' => $nilai_keseluruhan,
            'komentar' => $komentar,
            'status' => $status,
            'tanggal_evaluasi' => date('Y-m-d H:i:s')
        ];

        if ($existingPenilaian) {
            // Update existing evaluation
            $success = $this->penilaianModel->update($existingPenilaian['id'], $penilaianData);
        } else {
            // Create new evaluation
            $success = $this->penilaianModel->create($penilaianData);
        }

        if ($success) {
            // Update sidang status
            $this->sidangModel->updateStatus($id, $status);
            
            // Send notification to student
            $this->notifikasiModel->create([
                'user_id' => $sidang['nim'],
                'user_type' => 'mahasiswa',
                'title' => 'Hasil Evaluasi Sidang',
                'message' => "Evaluasi sidang Anda telah selesai dengan status: {$status}",
                'type' => 'evaluasi_sidang',
                'is_read' => 0
            ]);

            $this->setFlashMessage('success', 'Evaluasi sidang berhasil disimpan');
            header('Location: /dosen/daftar-sidang');
        } else {
            $this->setFlashMessage('error', 'Gagal menyimpan evaluasi');
            header('Location: /dosen/evaluasi-sidang/' . $id);
        }
        exit;
    }

    /**
     * Display final grades management
     */
    public function nilaiAkhir()
    {
        $nip = $_SESSION['user_id'];
        $nilai = $this->penilaianModel->getNilaiAkhirByDosen($nip);
        $dosen = $this->dosenModel->getByNip($nip);
        
        $this->view->render('dosen/nilai-akhir', [
            'nilai' => $nilai,
            'dosen' => $dosen
        ]);
    }

    /**
     * Display notifications
     */
    public function notifikasi()
    {
        $nip = $_SESSION['user_id'];
        $notifikasi = $this->notifikasiModel->getByDosen($nip);
        $dosen = $this->dosenModel->getByNip($nip);
        
        $this->view->render('dosen/notifikasi', [
            'notifikasi' => $notifikasi,
            'dosen' => $dosen
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotifikasiRead($id)
    {
        $nip = $_SESSION['user_id'];
        $success = $this->notifikasiModel->markAsRead($id, $nip, 'dosen');
        
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
        $nip = $_SESSION['user_id'];
        $dosen = $this->dosenModel->getByNip($nip);
        
        $this->view->render('dosen/profil', [
            'dosen' => $dosen
        ]);
    }

    /**
     * Handle profile update
     */
    public function updateProfil()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dosen/profil');
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid request token');
            header('Location: /dosen/profil');
            exit;
        }

        $nip = $_SESSION['user_id'];
        $nama = $this->sanitizeInput($_POST['nama'] ?? '');
        $email = $this->sanitizeInput($_POST['email'] ?? '');
        $no_hp = $this->sanitizeInput($_POST['no_hp'] ?? '');

        // Validate input
        if (empty($nama) || empty($email)) {
            $this->setFlashMessage('error', 'Nama dan email harus diisi');
            header('Location: /dosen/profil');
            exit;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlashMessage('error', 'Format email tidak valid');
            header('Location: /dosen/profil');
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
                $nip
            );
            
            if ($fotoPath) {
                $updateData['foto'] = $fotoPath;
            }
        }

        $success = $this->dosenModel->update($nip, $updateData);
        
        if ($success) {
            $this->setFlashMessage('success', 'Profil berhasil diperbarui');
        } else {
            $this->setFlashMessage('error', 'Gagal memperbarui profil');
        }
        
        header('Location: /dosen/profil');
        exit;
    }

    /**
     * Display revision management
     */
    public function dokumenRevisi()
    {
        $nip = $_SESSION['user_id'];
        $revisi = $this->sidangModel->getRevisiByDosen($nip);
        $dosen = $this->dosenModel->getByNip($nip);
        
        $this->view->render('dosen/dokumen-revisi', [
            'revisi' => $revisi,
            'dosen' => $dosen
        ]);
    }

    /**
     * Handle revision evaluation
     */
    public function evaluasiRevisi($revisiId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dosen/dokumen-revisi');
            exit;
        }

        // Validate CSRF token
        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid request token');
            header('Location: /dosen/dokumen-revisi');
            exit;
        }

        $nip = $_SESSION['user_id'];
        $revisi = $this->sidangModel->getRevisiById($revisiId);
        
        // Check if lecturer has access to this revision
        if (!$revisi || $revisi['dosen_id'] !== $nip) {
            $this->setFlashMessage('error', 'Revisi tidak ditemukan');
            header('Location: /dosen/dokumen-revisi');
            exit;
        }

        $status = $this->sanitizeInput($_POST['status'] ?? '');
        $komentar = $this->sanitizeInput($_POST['komentar'] ?? '');

        // Validate status
        if (!in_array($status, ['approved', 'rejected'])) {
            $this->setFlashMessage('error', 'Status tidak valid');
            header('Location: /dosen/dokumen-revisi');
            exit;
        }

        $updateData = [
            'status' => $status,
            'komentar_dosen' => $komentar,
            'tanggal_evaluasi' => date('Y-m-d H:i:s')
        ];

        $success = $this->sidangModel->updateRevisi($revisiId, $updateData);
        
        if ($success) {
            // Update sidang status based on revision result
            $sidangStatus = ($status === 'approved') ? 'completed' : 'revisi_rejected';
            $this->sidangModel->updateStatus($revisi['sidang_id'], $sidangStatus);
            
            // Send notification to student
            $this->notifikasiModel->create([
                'user_id' => $revisi['nim'],
                'user_type' => 'mahasiswa',
                'title' => 'Hasil Evaluasi Revisi',
                'message' => "Revisi sidang Anda telah {$status}",
                'type' => 'evaluasi_revisi',
                'is_read' => 0
            ]);

            $this->setFlashMessage('success', 'Evaluasi revisi berhasil disimpan');
        } else {
            $this->setFlashMessage('error', 'Gagal menyimpan evaluasi revisi');
        }
        
        header('Location: /dosen/dokumen-revisi');
        exit;
    }

    /**
     * Download document
     */
    public function downloadDocument($type, $filename)
    {
        $nip = $_SESSION['user_id'];
        
        // Validate file access
        if (!$this->fileUploadService->validateFileAccess($type, $filename, $nip, 'dosen')) {
            $this->setFlashMessage('error', 'Akses file ditolak');
            header('Location: /dosen/dashboard');
            exit;
        }

        $this->fileUploadService->downloadFile($type, $filename);
    }
} 