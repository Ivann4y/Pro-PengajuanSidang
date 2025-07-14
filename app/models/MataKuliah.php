<?php

class MataKuliah extends Model {
    protected $table = 'MataKuliah';
    protected $primaryKey = 'id_matkul';
    protected $fillable = [
        'id_matkul', 'nama_matkul'
    ];
    
    public function getKelompok() {
        $sql = "SELECT k.*, m.nama_mhs, m.nim
                FROM Kelompok k 
                JOIN Mahasiswa m ON k.nim = m.nim 
                WHERE k.id_matkul = ?
                ORDER BY k.tahun_ajaran DESC, k.nomor_kelompok";
        return $this->db->fetchAll($sql, [$this->id_matkul]);
    }
    
    public function getKelompokByTahunAjaran($tahunAjaran) {
        $sql = "SELECT k.*, m.nama_mhs, m.nim
                FROM Kelompok k 
                JOIN Mahasiswa m ON k.nim = m.nim 
                WHERE k.id_matkul = ? AND k.tahun_ajaran = ?
                ORDER BY k.nomor_kelompok";
        return $this->db->fetchAll($sql, [$this->id_matkul, $tahunAjaran]);
    }
    
    public function getSidang() {
        $sql = "SELECT s.*, k.nomor_kelompok, k.tahun_ajaran, k.jenis_sidang
                FROM Sidang s 
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
                WHERE k.id_matkul = ?
                ORDER BY s.waktu_pengumpulan DESC";
        return $this->db->fetchAll($sql, [$this->id_matkul]);
    }
    
    public function getPengampu() {
        $sql = "SELECT pk.*, d.nama_dosen, d.email
                FROM Pengampu_Kelas pk 
                JOIN Dosen d ON pk.nomor_dosen = d.nomor_dosen 
                WHERE pk.id_matkul = ?
                ORDER BY d.nama_dosen";
        return $this->db->fetchAll($sql, [$this->id_matkul]);
    }
    
    public function getJumlahKelompok($tahunAjaran = null) {
        $sql = "SELECT COUNT(DISTINCT nomor_kelompok) as count 
                FROM Kelompok 
                WHERE id_matkul = ?";
        $params = [$this->id_matkul];
        
        if ($tahunAjaran) {
            $sql .= " AND tahun_ajaran = ?";
            $params[] = $tahunAjaran;
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result ? $result['count'] : 0;
    }
    
    public function getJumlahMahasiswa($tahunAjaran = null) {
        $sql = "SELECT COUNT(DISTINCT nim) as count 
                FROM Kelompok 
                WHERE id_matkul = ?";
        $params = [$this->id_matkul];
        
        if ($tahunAjaran) {
            $sql .= " AND tahun_ajaran = ?";
            $params[] = $tahunAjaran;
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result ? $result['count'] : 0;
    }
    
    public function getJumlahSidang($tahunAjaran = null) {
        $sql = "SELECT COUNT(s.id_sidang) as count 
                FROM Sidang s 
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
                WHERE k.id_matkul = ?";
        $params = [$this->id_matkul];
        
        if ($tahunAjaran) {
            $sql .= " AND k.tahun_ajaran = ?";
            $params[] = $tahunAjaran;
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result ? $result['count'] : 0;
    }
    
    public function getStatistikSidang($tahunAjaran = null) {
        $sql = "SELECT 
                    s.status_ajuan,
                    COUNT(*) as jumlah
                FROM Sidang s 
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
                WHERE k.id_matkul = ?";
        $params = [$this->id_matkul];
        
        if ($tahunAjaran) {
            $sql .= " AND k.tahun_ajaran = ?";
            $params[] = $tahunAjaran;
        }
        
        $sql .= " GROUP BY s.status_ajuan";
        
        return $this->db->fetchAll($sql, $params);
    }
} 