<?php

class Mahasiswa extends User {
    protected $table = 'Mahasiswa';
    protected $primaryKey = 'nim';
    protected $fillable = [
        'nim', 'nama_mhs', 'prodi', 'email', 'password_hash', 
        'username', 'role', 'created_at', 'jenis_kelamin', 'no_telepon'
    ];
    
    public function getKelompok() {
        $sql = "SELECT k.*, mk.nama_matkul 
                FROM Kelompok k 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                WHERE k.nim = ? 
                ORDER BY k.tahun_ajaran DESC, k.nomor_kelompok";
        return $this->db->fetchAll($sql, [$this->nim]);
    }
    
    public function getSidang() {
        $sql = "SELECT s.*, k.nomor_kelompok, k.tahun_ajaran, k.jenis_sidang, mk.nama_matkul
                FROM Sidang s 
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                WHERE k.nim = ? 
                ORDER BY s.waktu_pengumpulan DESC";
        return $this->db->fetchAll($sql, [$this->nim]);
    }
    
    public function getSidangByKelompok($idKelompok) {
        $sql = "SELECT s.*, k.nomor_kelompok, k.tahun_ajaran, k.jenis_sidang, mk.nama_matkul
                FROM Sidang s 
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                WHERE k.id_kelompok = ? AND k.nim = ?";
        return $this->db->fetchOne($sql, [$idKelompok, $this->nim]);
    }
    
    public function getNilaiAkhir($idSidang) {
        $sql = "WITH NilaiPerDosen AS (
                    SELECT
                        (n_dokumen * 0.25 + n_presentasi * 0.25 + n_tanyajawab * 0.30 + n_proyek * 0.20) AS nilai_dosen,
                        bobot_penilaian
                    FROM Penilaian
                    WHERE id_sidang = ? AND nim = ?
                )
                SELECT
                    SUM(nilai_dosen * bobot_penilaian) / SUM(bobot_penilaian) AS nilai_akhir_weighted
                FROM NilaiPerDosen";
        
        $result = $this->db->fetchOne($sql, [$idSidang, $this->nim]);
        return $result ? $result['nilai_akhir_weighted'] : null;
    }
    
    public function getPenilaian($idSidang) {
        $sql = "SELECT p.*, d.nama_dosen 
                FROM Penilaian p 
                JOIN Dosen d ON p.nomor_dosen = d.nomor_dosen 
                WHERE p.id_sidang = ? AND p.nim = ?";
        return $this->db->fetchAll($sql, [$idSidang, $this->nim]);
    }
    
    public function getNotifikasi() {
        $sql = "SELECT * FROM Notifikasi WHERE penerima = ? ORDER BY waktu DESC";
        return $this->db->fetchAll($sql, [$this->nim]);
    }
    
    public function getNotifikasiUnread() {
        $sql = "SELECT COUNT(*) as count FROM Notifikasi WHERE penerima = ? AND status_baca = 0";
        $result = $this->db->fetchOne($sql, [$this->nim]);
        return $result ? $result['count'] : 0;
    }
    
    public function markNotifikasiAsRead($idNotifikasi) {
        $sql = "UPDATE Notifikasi SET status_baca = 1 WHERE id_notifikasi = ? AND penerima = ?";
        return $this->db->execute($sql, [$idNotifikasi, $this->nim]);
    }
    
    public function getDashboardStats() {
        $stats = [];
        
        // Count ongoing defenses
        $sql = "SELECT COUNT(*) as count
                FROM Sidang s
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
                WHERE k.nim = ? AND s.status_sidang = 1";
        $result = $this->db->fetchOne($sql, [$this->nim]);
        $stats['sidang_berlangsung'] = $result ? $result['count'] : 0;
        
        // Count pending evaluations
        $sql = "SELECT COUNT(DISTINCT s.id_sidang) as count
                FROM Sidang s
                JOIN Detail_Sidang ds ON s.id_sidang = ds.id_sidang
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
                WHERE k.nim = ? AND s.status_sidang = 1 AND ds.status_revisi = 0";
        $result = $this->db->fetchOne($sql, [$this->nim]);
        $stats['menunggu_penilaian'] = $result ? $result['count'] : 0;
        
        // Count upcoming defenses
        $sql = "SELECT COUNT(*) as count
                FROM Sidang s
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok
                WHERE k.nim = ? AND s.status_ajuan = 'Approved' AND s.status_sidang = 0";
        $result = $this->db->fetchOne($sql, [$this->nim]);
        $stats['sidang_mendatang'] = $result ? $result['count'] : 0;
        
        return $stats;
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
        $allowedFields = ['nama_mhs', 'email', 'no_telepon', 'jenis_kelamin'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));
        
        return $this->update($this->nim, $updateData);
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
        return $this->updatePassword($this->nim, $newPassword);
    }
} 