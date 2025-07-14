<?php

class Bimbingan extends Model {
    protected $table = 'Bimbingan';
    protected $primaryKey = 'id_bimbingan';
    protected $fillable = [
        'id_bimbingan', 'id_kelompok', 'nomor_dosen', 'isPembimbing'
    ];
    
    public function getDosen() {
        $sql = "SELECT * FROM Dosen WHERE nomor_dosen = ?";
        return $this->db->fetchOne($sql, [$this->nomor_dosen]);
    }
    
    public function getKelompok() {
        $sql = "SELECT k.*, mk.nama_matkul 
                FROM Kelompok k 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                WHERE k.id_kelompok = ?";
        return $this->db->fetchOne($sql, [$this->id_kelompok]);
    }
    
    public function getMahasiswaBimbingan() {
        $sql = "SELECT DISTINCT m.*, k.nomor_kelompok, k.tahun_ajaran, 
                       k.jenis_sidang, mk.nama_matkul
                FROM Mahasiswa m 
                JOIN Kelompok k ON m.nim = k.nim 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                WHERE k.id_kelompok = ?
                ORDER BY m.nama_mhs";
        return $this->db->fetchAll($sql, [$this->id_kelompok]);
    }
    
    public function assignPembimbing($idKelompok, $nomorDosen) {
        // Check if already assigned
        $sql = "SELECT COUNT(*) as count FROM Bimbingan 
                WHERE id_kelompok = ? AND nomor_dosen = ? AND isPembimbing = 1";
        $result = $this->db->fetchOne($sql, [$idKelompok, $nomorDosen]);
        
        if ($result && $result['count'] > 0) {
            return false; // Already assigned
        }
        
        $bimbinganData = [
            'id_kelompok' => $idKelompok,
            'nomor_dosen' => $nomorDosen,
            'isPembimbing' => 1
        ];
        
        return $this->create($bimbinganData);
    }
    
    public function removePembimbing($idKelompok, $nomorDosen) {
        $sql = "DELETE FROM Bimbingan 
                WHERE id_kelompok = ? AND nomor_dosen = ? AND isPembimbing = 1";
        return $this->db->execute($sql, [$idKelompok, $nomorDosen]);
    }
    
    public function getBimbinganByDosen($nomorDosen) {
        $sql = "SELECT b.*, k.nomor_kelompok, k.tahun_ajaran, k.jenis_sidang, 
                       mk.nama_matkul, m.nama_mhs, m.nim
                FROM Bimbingan b 
                JOIN Kelompok k ON b.id_kelompok = k.id_kelompok 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                JOIN Mahasiswa m ON k.nim = m.nim 
                WHERE b.nomor_dosen = ? AND b.isPembimbing = 1
                ORDER BY k.tahun_ajaran DESC, k.nomor_kelompok";
        return $this->db->fetchAll($sql, [$nomorDosen]);
    }
    
    public function getBimbinganByKelompok($idKelompok) {
        $sql = "SELECT b.*, d.nama_dosen, d.email, d.prodi
                FROM Bimbingan b 
                JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen 
                WHERE b.id_kelompok = ? AND b.isPembimbing = 1";
        return $this->db->fetchAll($sql, [$idKelompok]);
    }
    
    public function getPembimbingByKelompok($idKelompok) {
        $sql = "SELECT d.* 
                FROM Bimbingan b 
                JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen 
                WHERE b.id_kelompok = ? AND b.isPembimbing = 1";
        return $this->db->fetchOne($sql, [$idKelompok]);
    }
    
    public function isPembimbing($nomorDosen, $idKelompok) {
        $sql = "SELECT COUNT(*) as count FROM Bimbingan 
                WHERE nomor_dosen = ? AND id_kelompok = ? AND isPembimbing = 1";
        $result = $this->db->fetchOne($sql, [$nomorDosen, $idKelompok]);
        return $result && $result['count'] > 0;
    }
    
    public function getJumlahBimbingan($nomorDosen) {
        $sql = "SELECT COUNT(DISTINCT id_kelompok) as count 
                FROM Bimbingan 
                WHERE nomor_dosen = ? AND isPembimbing = 1";
        $result = $this->db->fetchOne($sql, [$nomorDosen]);
        return $result ? $result['count'] : 0;
    }
    
    public function getJumlahMahasiswaBimbingan($nomorDosen) {
        $sql = "SELECT COUNT(DISTINCT k.nim) as count 
                FROM Bimbingan b 
                JOIN Kelompok k ON b.id_kelompok = k.id_kelompok 
                WHERE b.nomor_dosen = ? AND b.isPembimbing = 1";
        $result = $this->db->fetchOne($sql, [$nomorDosen]);
        return $result ? $result['count'] : 0;
    }
} 