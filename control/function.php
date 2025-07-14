<?php
/**
 * Generate breadcrumb navigation
 * @param string $current_page Current page name
 * @param string $role User role (admin, dosen, mahasiswa)
 * @param array $additional_items Additional breadcrumb items
 * @return string HTML breadcrumb
 */
function generateBreadcrumb($current_page, $role, $additional_items = []) {
    $breadcrumb_items = [];
    
    // Add home link based on role
    switch ($role) {
        case 'admin':
            $breadcrumb_items[] = '<a href="aBeranda.php"><i class="bi bi-house"></i>Beranda</a>';
            break;
        case 'dosen':
            $breadcrumb_items[] = '<a href="dBeranda.php"><i class="bi bi-house"></i>Beranda</a>';
            break;
        case 'mahasiswa':
            $breadcrumb_items[] = '<a href="mBeranda.php"><i class="bi bi-house"></i>Beranda</a>';
            break;
    }
    
    // Add additional items
    foreach ($additional_items as $item) {
        if (is_array($item)) {
            $breadcrumb_items[] = '<a href="' . $item['url'] . '">' . $item['text'] . '</a>';
        } else {
            $breadcrumb_items[] = $item;
        }
    }
    
    // Add current page
    $breadcrumb_items[] = $current_page;
    
    // Generate HTML
    $html = '<div class="breadcrumb-container breadcrumb-' . $role . '">';
    $html .= '<nav aria-label="breadcrumb">';
    $html .= '<ol class="breadcrumb">';
    
    foreach ($breadcrumb_items as $index => $item) {
        $is_last = $index === count($breadcrumb_items) - 1;
        $class = $is_last ? 'breadcrumb-item active' : 'breadcrumb-item';
        
        if ($is_last) {
            $html .= '<li class="' . $class . '" aria-current="page">' . $item . '</li>';
        } else {
            $html .= '<li class="' . $class . '">' . $item . '</li>';
        }
    }
    
    $html .= '</ol>';
    $html .= '</nav>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Get page title for breadcrumb
 * @param string $page_name Page identifier
 * @return string Page title
 */
function getPageTitle($page_name) {
    $titles = [
        // Admin pages
        'aBeranda' => 'Beranda',
        'aPenjadwalan' => 'Penjadwalan',
        'aDaftarSidang' => 'Daftar Sidang',
        'aDetailSidang' => 'Detail Sidang',
        'aEvaluasi' => 'Evaluasi',
        'aNilaiAkhir' => 'Nilai Akhir',
        'aNotifikasi' => 'Notifikasi',
        'aProfil' => 'Profil',
        
        // Dosen pages
        'dBeranda' => 'Beranda',
        'dPengajuan' => 'Pengajuan',
        'dDetailPengajuan' => 'Detail Pengajuan',
        'dDokumenRevisi' => 'Dokumen Revisi',
        'dEvaluasiSidang' => 'Evaluasi Sidang',
        'dDaftarSidang' => 'Daftar Sidang',
        'dNilaiAkhir' => 'Nilai Akhir',
        'dNotifikasi' => 'Notifikasi',
        'dProfil' => 'Profil',
        
        // Mahasiswa pages
        'mBeranda' => 'Beranda',
        'mPengajuan' => 'Pengajuan',
        'mTambahPengajuan' => 'Tambah Pengajuan',
        'mEditPengajuan' => 'Edit Pengajuan',
        'mKelolaPengajuan' => 'Kelola Pengajuan',
        'mSidang' => 'Sidang',
        'mdetailSidang' => 'Detail Sidang',
        'mPerbaikan' => 'Perbaikan',
        'mNilaiakhir' => 'Nilai Akhir',
        'mNotifikasi' => 'Notifikasi',
        'mProfil' => 'Profil',
        
        // Auth pages
        'login' => 'Login',
        'lupaPassword' => 'Lupa Password',
        'inputPasswordBaru' => 'Input Password Baru'
    ];
    
    return isset($titles[$page_name]) ? $titles[$page_name] : $page_name;
}
?>

