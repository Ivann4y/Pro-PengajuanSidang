<?php

class Penjadwalan extends Model {
    protected $table = 'Penjadwalan';
    protected $primaryKey = 'id_sidang'; // Composite key with nomor_dosen
    protected $fillable = [
        'id_sidang', 'nomor_dosen', 'peran_dosen'
    ];
    
    public function getSidang() {
        $sql = "SELECT s.*, k.nomor_kelompok, k.tahun_ajaran, k.jenis_sidang, 
                       mk.nama_matkul
                FROM Sidang s 
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                WHERE s.id_sidang = ?";
        return $this->db->fetchOne($sql, [$this->id_sidang]);
    }
    
    public function getDosen() {
        $sql = "SELECT * FROM Dosen WHERE nomor_dosen = ?";
        return $this->db->fetchOne($sql, [$this->nomor_dosen]);
    }
    
    public function getMahasiswa() {
        $sql = "SELECT DISTINCT m.* 
                FROM Mahasiswa m 
                JOIN Kelompok k ON m.nim = k.nim 
                JOIN Sidang s ON k.id_kelompok = s.id_kelompok 
                WHERE s.id_sidang = ?
                ORDER BY m.nama_mhs";
        return $this->db->fetchAll($sql, [$this->id_sidang]);
    }
    
    public function createPenjadwalan($idSidang, $nomorDosen, $peranDosen) {
        // Check if already scheduled
        $sql = "SELECT COUNT(*) as count FROM Penjadwalan 
                WHERE id_sidang = ? AND nomor_dosen = ?";
        $result = $this->db->fetchOne($sql, [$idSidang, $nomorDosen]);
        
        if ($result && $result['count'] > 0) {
            return false; // Already scheduled
        }
        
        $penjadwalanData = [
            'id_sidang' => $idSidang,
            'nomor_dosen' => $nomorDosen,
            'peran_dosen' => $peranDosen
        ];
        
        return $this->create($penjadwalanData);
    }
    
    public function updatePeranDosen($idSidang, $nomorDosen, $peranDosen) {
        $sql = "UPDATE Penjadwalan SET peran_dosen = ? 
                WHERE id_sidang = ? AND nomor_dosen = ?";
        return $this->db->execute($sql, [$peranDosen, $idSidang, $nomorDosen]);
    }
    
    public function removePenjadwalan($idSidang, $nomorDosen) {
        $sql = "DELETE FROM Penjadwalan WHERE id_sidang = ? AND nomor_dosen = ?";
        return $this->db->execute($sql, [$idSidang, $nomorDosen]);
    }
    
    public function getPenjadwalanBySidang($idSidang) {
        $sql = "SELECT p.*, d.nama_dosen, d.prodi, d.email
                FROM Penjadwalan p 
                JOIN Dosen d ON p.nomor_dosen = d.nomor_dosen 
                WHERE p.id_sidang = ?
                ORDER BY p.peran_dosen DESC";
        return $this->db->fetchAll($sql, [$idSidang]);
    }
    
    public function getPenjadwalanByDosen($nomorDosen) {
        $sql = "SELECT p.*, s.judul, s.status_ajuan, s.waktu_pengumpulan,
                       k.nomor_kelompok, k.tahun_ajaran, k.jenis_sidang, 
                       mk.nama_matkul
                FROM Penjadwalan p 
                JOIN Sidang s ON p.id_sidang = s.id_sidang 
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                WHERE p.nomor_dosen = ?
                ORDER BY s.waktu_pengumpulan DESC";
        return $this->db->fetchAll($sql, [$nomorDosen]);
    }
    
    public function getPengujiBySidang($idSidang) {
        $sql = "SELECT p.*, d.nama_dosen, d.prodi, d.email
                FROM Penjadwalan p 
                JOIN Dosen d ON p.nomor_dosen = d.nomor_dosen 
                WHERE p.id_sidang = ? AND p.peran_dosen = 1
                ORDER BY d.nama_dosen";
        return $this->db->fetchAll($sql, [$idSidang]);
    }
    
    public function getPembimbingBySidang($idSidang) {
        $sql = "SELECT p.*, d.nama_dosen, d.prodi, d.email
                FROM Penjadwalan p 
                JOIN Dosen d ON p.nomor_dosen = d.nomor_dosen 
                WHERE p.id_sidang = ? AND p.peran_dosen = 0
                ORDER BY d.nama_dosen";
        return $this->db->fetchAll($sql, [$idSidang]);
    }
    
    public function isDosenScheduled($nomorDosen, $idSidang) {
        $sql = "SELECT COUNT(*) as count FROM Penjadwalan 
                WHERE nomor_dosen = ? AND id_sidang = ?";
        $result = $this->db->fetchOne($sql, [$nomorDosen, $idSidang]);
        return $result && $result['count'] > 0;
    }
    
    public function getJumlahPenjadwalan($nomorDosen) {
        $sql = "SELECT COUNT(DISTINCT id_sidang) as count 
                FROM Penjadwalan WHERE nomor_dosen = ?";
        $result = $this->db->fetchOne($sql, [$nomorDosen]);
        return $result ? $result['count'] : 0;
    }
    
    public function getPeranDosenDisplay() {
        return $this->peran_dosen == 1 ? 'Penguji' : 'Pembimbing';
    }
    
    public function isPenguji() {
        return $this->peran_dosen == 1;
    }
    
    public function isPembimbing() {
        return $this->peran_dosen == 0;
    }
} 