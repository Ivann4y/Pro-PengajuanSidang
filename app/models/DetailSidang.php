<?php

class DetailSidang extends Model {
    protected $table = 'Detail_Sidang';
    protected $primaryKey = 'id_detail';
    protected $fillable = [
        'id_detail', 'id_sidang', 'status_revisi', 'catatan'
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
    
    public function getMahasiswa() {
        $sql = "SELECT DISTINCT m.* 
                FROM Mahasiswa m 
                JOIN Kelompok k ON m.nim = k.nim 
                JOIN Sidang s ON k.id_kelompok = s.id_kelompok 
                WHERE s.id_sidang = ?
                ORDER BY m.nama_mhs";
        return $this->db->fetchAll($sql, [$this->id_sidang]);
    }
    
    public function addCatatan($idSidang, $catatan) {
        $detailData = [
            'id_sidang' => $idSidang,
            'catatan' => $catatan,
            'status_revisi' => 0 // Default: belum direvisi
        ];
        
        return $this->create($detailData);
    }
    
    public function updateStatusRevisi($idSidang, $statusRevisi) {
        $sql = "UPDATE Detail_Sidang SET status_revisi = ? WHERE id_sidang = ?";
        return $this->db->execute($sql, [$statusRevisi, $idSidang]);
    }
    
    public function updateCatatan($idSidang, $catatan) {
        $sql = "UPDATE Detail_Sidang SET catatan = ? WHERE id_sidang = ?";
        return $this->db->execute($sql, [$catatan, $idSidang]);
    }
    
    public function getDetailBySidang($idSidang) {
        $sql = "SELECT * FROM Detail_Sidang WHERE id_sidang = ?";
        return $this->db->fetchOne($sql, [$idSidang]);
    }
    
    public function getSidangMembutuhkanRevisi() {
        $sql = "SELECT ds.*, s.judul, s.status_ajuan, k.nomor_kelompok, 
                       k.tahun_ajaran, k.jenis_sidang, mk.nama_matkul
                FROM Detail_Sidang ds 
                JOIN Sidang s ON ds.id_sidang = s.id_sidang 
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                WHERE ds.status_revisi = 0
                ORDER BY s.waktu_pengumpulan DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function getSidangSudahDirevisi() {
        $sql = "SELECT ds.*, s.judul, s.status_ajuan, k.nomor_kelompok, 
                       k.tahun_ajaran, k.jenis_sidang, mk.nama_matkul
                FROM Detail_Sidang ds 
                JOIN Sidang s ON ds.id_sidang = s.id_sidang 
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                WHERE ds.status_revisi = 1
                ORDER BY s.waktu_pengumpulan DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function getStatusRevisiDisplay() {
        return $this->status_revisi == 1 ? 'Sudah Direvisi' : 'Belum Direvisi';
    }
    
    public function isRevisiSelesai() {
        return $this->status_revisi == 1;
    }
    
    public function isRevisiPending() {
        return $this->status_revisi == 0;
    }
    
    public function getCatatanFormatted() {
        if (empty($this->catatan)) {
            return 'Tidak ada catatan';
        }
        
        return nl2br(htmlspecialchars($this->catatan));
    }
} 