<?php

// Start session
session_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Autoloader
require_once BASE_PATH . '/app/core/Autoloader.php';

// Initialize autoloader
$autoloader = new App\Core\Autoloader();
$autoloader->register();

// Load configuration
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/config/security.php';

// Initialize router
$router = new App\Core\Router();

// Define routes
// Authentication routes
$router->add('', ['controller' => 'AuthController', 'action' => 'index']);
$router->add('login', ['controller' => 'AuthController', 'action' => 'login']);
$router->add('logout', ['controller' => 'AuthController', 'action' => 'logout']);
$router->add('forgot-password', ['controller' => 'AuthController', 'action' => 'forgotPassword']);
$router->add('reset-password', ['controller' => 'AuthController', 'action' => 'resetPassword']);

// Student routes
$router->add('mahasiswa', ['controller' => 'MahasiswaController', 'action' => 'dashboard']);
$router->add('mahasiswa/dashboard', ['controller' => 'MahasiswaController', 'action' => 'dashboard']);
$router->add('mahasiswa/pengajuan', ['controller' => 'MahasiswaController', 'action' => 'pengajuan']);
$router->add('mahasiswa/submit-pengajuan', ['controller' => 'MahasiswaController', 'action' => 'submitPengajuan']);
$router->add('mahasiswa/kelola-pengajuan', ['controller' => 'MahasiswaController', 'action' => 'kelolaPengajuan']);
$router->add('mahasiswa/edit-pengajuan/{id:\d+}', ['controller' => 'MahasiswaController', 'action' => 'editPengajuan']);
$router->add('mahasiswa/update-pengajuan/{id:\d+}', ['controller' => 'MahasiswaController', 'action' => 'updatePengajuan']);
$router->add('mahasiswa/detail-sidang/{id:\d+}', ['controller' => 'MahasiswaController', 'action' => 'detailSidang']);
$router->add('mahasiswa/sidang', ['controller' => 'MahasiswaController', 'action' => 'sidang']);
$router->add('mahasiswa/nilai-akhir', ['controller' => 'MahasiswaController', 'action' => 'nilaiAkhir']);
$router->add('mahasiswa/notifikasi', ['controller' => 'MahasiswaController', 'action' => 'notifikasi']);
$router->add('mahasiswa/mark-notifikasi-read/{id:\d+}', ['controller' => 'MahasiswaController', 'action' => 'markNotifikasiRead']);
$router->add('mahasiswa/profil', ['controller' => 'MahasiswaController', 'action' => 'profil']);
$router->add('mahasiswa/update-profil', ['controller' => 'MahasiswaController', 'action' => 'updateProfil']);
$router->add('mahasiswa/perbaikan/{sidangId:\d+}', ['controller' => 'MahasiswaController', 'action' => 'perbaikan']);
$router->add('mahasiswa/submit-perbaikan/{sidangId:\d+}', ['controller' => 'MahasiswaController', 'action' => 'submitPerbaikan']);
$router->add('mahasiswa/download/{type}/{filename}', ['controller' => 'MahasiswaController', 'action' => 'downloadDocument']);

// Lecturer routes
$router->add('dosen', ['controller' => 'DosenController', 'action' => 'dashboard']);
$router->add('dosen/dashboard', ['controller' => 'DosenController', 'action' => 'dashboard']);
$router->add('dosen/daftar-pengajuan', ['controller' => 'DosenController', 'action' => 'daftarPengajuan']);
$router->add('dosen/detail-pengajuan/{id:\d+}', ['controller' => 'DosenController', 'action' => 'detailPengajuan']);
$router->add('dosen/evaluasi-pengajuan/{id:\d+}', ['controller' => 'DosenController', 'action' => 'evaluasiPengajuan']);
$router->add('dosen/daftar-sidang', ['controller' => 'DosenController', 'action' => 'daftarSidang']);
$router->add('dosen/evaluasi-sidang/{id:\d+}', ['controller' => 'DosenController', 'action' => 'evaluasiSidang']);
$router->add('dosen/submit-evaluasi/{id:\d+}', ['controller' => 'DosenController', 'action' => 'submitEvaluasi']);
$router->add('dosen/nilai-akhir', ['controller' => 'DosenController', 'action' => 'nilaiAkhir']);
$router->add('dosen/notifikasi', ['controller' => 'DosenController', 'action' => 'notifikasi']);
$router->add('dosen/mark-notifikasi-read/{id:\d+}', ['controller' => 'DosenController', 'action' => 'markNotifikasiRead']);
$router->add('dosen/profil', ['controller' => 'DosenController', 'action' => 'profil']);
$router->add('dosen/update-profil', ['controller' => 'DosenController', 'action' => 'updateProfil']);
$router->add('dosen/dokumen-revisi', ['controller' => 'DosenController', 'action' => 'dokumenRevisi']);
$router->add('dosen/evaluasi-revisi/{revisiId:\d+}', ['controller' => 'DosenController', 'action' => 'evaluasiRevisi']);
$router->add('dosen/download/{type}/{filename}', ['controller' => 'DosenController', 'action' => 'downloadDocument']);

// Admin routes
$router->add('admin', ['controller' => 'AdminController', 'action' => 'dashboard']);
$router->add('admin/dashboard', ['controller' => 'AdminController', 'action' => 'dashboard']);
$router->add('admin/daftar-pengajuan', ['controller' => 'AdminController', 'action' => 'daftarPengajuan']);
$router->add('admin/detail-pengajuan/{id:\d+}', ['controller' => 'AdminController', 'action' => 'detailPengajuan']);
$router->add('admin/evaluasi-pengajuan/{id:\d+}', ['controller' => 'AdminController', 'action' => 'evaluasiPengajuan']);
$router->add('admin/penjadwalan', ['controller' => 'AdminController', 'action' => 'penjadwalan']);
$router->add('admin/create-penjadwalan', ['controller' => 'AdminController', 'action' => 'createPenjadwalan']);
$router->add('admin/update-penjadwalan/{id:\d+}', ['controller' => 'AdminController', 'action' => 'updatePenjadwalan']);
$router->add('admin/delete-penjadwalan/{id:\d+}', ['controller' => 'AdminController', 'action' => 'deletePenjadwalan']);
$router->add('admin/daftar-sidang', ['controller' => 'AdminController', 'action' => 'daftarSidang']);
$router->add('admin/detail-sidang/{id:\d+}', ['controller' => 'AdminController', 'action' => 'detailSidang']);
$router->add('admin/nilai-akhir', ['controller' => 'AdminController', 'action' => 'nilaiAkhir']);
$router->add('admin/notifikasi', ['controller' => 'AdminController', 'action' => 'notifikasi']);
$router->add('admin/mark-notifikasi-read/{id:\d+}', ['controller' => 'AdminController', 'action' => 'markNotifikasiRead']);
$router->add('admin/profil', ['controller' => 'AdminController', 'action' => 'profil']);
$router->add('admin/update-profil', ['controller' => 'AdminController', 'action' => 'updateProfil']);
$router->add('admin/kelola-user', ['controller' => 'AdminController', 'action' => 'kelolaUser']);
$router->add('admin/create-user', ['controller' => 'AdminController', 'action' => 'createUser']);
$router->add('admin/download/{type}/{filename}', ['controller' => 'AdminController', 'action' => 'downloadDocument']);

// Get the URL path
$url = $_SERVER['REQUEST_URI'] ?? '';
$url = parse_url($url, PHP_URL_PATH);
$url = trim($url, '/');

// Handle empty URL
if (empty($url)) {
    $url = '';
}

try {
    // Dispatch the route
    $router->dispatch($url);
} catch (Exception $e) {
    // Handle 404 errors
    if ($e->getCode() === 404) {
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
        echo '<p>The requested page could not be found.</p>';
        echo '<p><a href="/">Return to Home</a></p>';
    } else {
        // Handle other errors
        http_response_code(500);
        echo '<h1>500 - Internal Server Error</h1>';
        echo '<p>An error occurred while processing your request.</p>';
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            echo '<p>Error: ' . $e->getMessage() . '</p>';
        }
    }
} 