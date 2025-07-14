<?php

class Dosen extends User {
    protected $table = 'Dosen';
    protected $primaryKey = 'nomor_dosen';
    protected $fillable = [
        'nomor_dosen', 'nama_dosen', 'isPembimbing', 'prodi', 'isPenguji',
        'email', 'password_hash', 'username', 'role', 'created_at', 
        'jenis_kelamin', 'no_telepon'
    ];
    
    public function getBimbingan() {
        $sql = "SELECT b.*, k.nomor_kelompok, k.tahun_ajaran, k.jenis_sidang, 
                       mk.nama_matkul, m.nama_mhs, m.nim
                FROM Bimbingan b 
                JOIN Kelompok k ON b.id_kelompok = k.id_kelompok 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                JOIN Mahasiswa m ON k.nim = m.nim 
                WHERE b.nomor_dosen = ? AND b.isPembimbing = 1
                ORDER BY k.tahun_ajaran DESC, k.nomor_kelompok";
        return $this->db->fetchAll($sql, [$this->nomor_dosen]);
    }
    
    public function getPenjadwalan() {
        $sql = "SELECT p.*, s.judul, s.status_ajuan, k.nomor_kelompok, 
                       k.tahun_ajaran, k.jenis_sidang, mk.nama_matkul
                FROM Penjadwalan p 
                JOIN Sidang s ON p.id_sidang = s.id_sidang 
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                WHERE p.nomor_dosen = ?
                ORDER BY s.waktu_pengumpulan DESC";
        return $this->db->fetchAll($sql, [$this->nomor_dosen]);
    }
    
    public function getPenilaian() {
        $sql = "SELECT p.*, s.judul, k.nomor_kelompok, k.tahun_ajaran, 
                       k.jenis_sidang, mk.nama_matkul, m.nama_mhs
                FROM Penilaian p 
                JOIN Sidang s ON p.id_sidang = s.id_sidang 
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                JOIN Mahasiswa m ON p.nim = m.nim 
                WHERE p.nomor_dosen = ?
                ORDER BY p.id_penilaian DESC";
        return $this->db->fetchAll($sql, [$this->nomor_dosen]);
    }
    
    public function getPenilaianBySidang($idSidang) {
        $sql = "SELECT p.*, m.nama_mhs, m.nim
                FROM Penilaian p 
                JOIN Mahasiswa m ON p.nim = m.nim 
                WHERE p.id_sidang = ? AND p.nomor_dosen = ?";
        return $this->db->fetchAll($sql, [$idSidang, $this->nomor_dosen]);
    }
    
    public function getSidangYangDievaluasi() {
        $sql = "SELECT DISTINCT s.*, k.nomor_kelompok, k.tahun_ajaran, 
                       k.jenis_sidang, mk.nama_matkul
                FROM Sidang s 
                JOIN Penjadwalan p ON s.id_sidang = p.id_sidang 
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                WHERE p.nomor_dosen = ? AND s.status_ajuan = 'Approved'
                ORDER BY s.waktu_pengumpulan DESC";
        return $this->db->fetchAll($sql, [$this->nomor_dosen]);
    }
    
    public function getMahasiswaBimbingan() {
        $sql = "SELECT DISTINCT m.*, k.nomor_kelompok, k.tahun_ajaran, 
                       k.jenis_sidang, mk.nama_matkul
                FROM Mahasiswa m 
                JOIN Kelompok k ON m.nim = k.nim 
                JOIN Bimbingan b ON k.id_kelompok = b.id_kelompok 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                WHERE b.nomor_dosen = ? AND b.isPembimbing = 1
                ORDER BY k.tahun_ajaran DESC, m.nama_mhs";
        return $this->db->fetchAll($sql, [$this->nomor_dosen]);
    }
    
    public function getNotifikasi() {
        $sql = "SELECT * FROM Notifikasi WHERE penerima = ? ORDER BY waktu DESC";
        return $this->db->fetchAll($sql, [$this->nomor_dosen]);
    }
    
    public function getNotifikasiUnread() {
        $sql = "SELECT COUNT(*) as count FROM Notifikasi WHERE penerima = ? AND status_baca = 0";
        $result = $this->db->fetchOne($sql, [$this->nomor_dosen]);
        return $result ? $result['count'] : 0;
    }
    
    public function markNotifikasiAsRead($idNotifikasi) {
        $sql = "UPDATE Notifikasi SET status_baca = 1 WHERE id_notifikasi = ? AND penerima = ?";
        return $this->db->execute($sql, [$idNotifikasi, $this->nomor_dosen]);
    }
    
    public function getDashboardStats() {
        $stats = [];
        
        // Count supervision assignments
        $sql = "SELECT COUNT(DISTINCT b.id_kelompok) as count
                FROM Bimbingan b 
                WHERE b.nomor_dosen = ? AND b.isPembimbing = 1";
        $result = $this->db->fetchOne($sql, [$this->nomor_dosen]);
        $stats['bimbingan_aktif'] = $result ? $result['count'] : 0;
        
        // Count evaluation assignments
        $sql = "SELECT COUNT(DISTINCT p.id_sidang) as count
                FROM Penjadwalan p 
                JOIN Sidang s ON p.id_sidang = s.id_sidang 
                WHERE p.nomor_dosen = ? AND s.status_ajuan = 'Approved'";
        $result = $this->db->fetchOne($sql, [$this->nomor_dosen]);
        $stats['sidang_dievaluasi'] = $result ? $result['count'] : 0;
        
        // Count pending evaluations
        $sql = "SELECT COUNT(DISTINCT p.id_sidang) as count
                FROM Penjadwalan p 
                JOIN Sidang s ON p.id_sidang = s.id_sidang 
                WHERE p.nomor_dosen = ? AND s.status_ajuan = 'Approved' 
                AND s.status_sidang = 1";
        $result = $this->db->fetchOne($sql, [$this->nomor_dosen]);
        $stats['evaluasi_pending'] = $result ? $result['count'] : 0;
        
        return $stats;
    }
    
    public function isPembimbing() {
        return $this->isPembimbing == 1;
    }
    
    public function isPenguji() {
        return $this->isPenguji == 1;
    }
    
    public function getProdiDisplayName() {
        $prodiMap = [
            'TRPL' => 'Teknologi Rekayasa Perangkat Lunak (TRPL)',
            'MI' => 'Manajemen Informatika (MI)',
            'TRL' => 'Teknologi Rekayasa Logistik (TRL)',
            'MO' => 'Mesin Otomotif (MO)'
        ];
        
        return $prodiMap[$this->prodi] ?? $this->prodi;
    }
    
    public function getJenisKelaminDisplay() {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }
    
    public function updateProfile($data) {
        $allowedFields = ['nama_dosen', 'email', 'no_telepon', 'jenis_kelamin'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));
        
        return $this->update($this->nomor_dosen, $updateData);
    }
    
    public function changePassword($oldPassword, $newPassword) {
        // Verify old password
        if (!password_verify($oldPassword, $this->password_hash)) {
            return false;
        }
        
        // Validate new password
        if (!$this->isPasswordValid($newPassword)) {
            return false;
        }
        
        // Update password
        return $this->updatePassword($this->nomor_dosen, $newPassword);
    }
    
    public function submitPenilaian($idSidang, $nim, $data) {
        $penilaianData = [
            'id_sidang' => $idSidang,
            'nim' => $nim,
            'nomor_dosen' => $this->nomor_dosen,
            'n_dokumen' => $data['n_dokumen'],
            'n_presentasi' => $data['n_presentasi'],
            'n_tanyajawab' => $data['n_tanyajawab'],
            'n_proyek' => $data['n_proyek'],
            'bobot_penilaian' => $data['bobot_penilaian'] ?? 1.0,
            'catatan' => $data['catatan'] ?? ''
        ];
        
        $penilaianModel = new Penilaian();
        return $penilaianModel->create($penilaianData);
    }
} 