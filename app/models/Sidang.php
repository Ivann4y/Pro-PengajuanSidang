<?php

class Sidang extends Model {
    protected $table = 'Sidang';
    protected $primaryKey = 'id_sidang';
    protected $fillable = [
        'id_sidang', 'id_kelompok', 'judul', 'status_ajuan', 
        'waktu_pengumpulan', 'dok_laporan', 'status_sidang'
    ];
    
    public function getKelompok() {
        $sql = "SELECT k.*, mk.nama_matkul 
                FROM Kelompok k 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                WHERE k.id_kelompok = ?";
        return $this->db->fetchOne($sql, [$this->id_kelompok]);
    }
    
    public function getMahasiswa() {
        $sql = "SELECT m.* 
                FROM Mahasiswa m 
                JOIN Kelompok k ON m.nim = k.nim 
                WHERE k.id_kelompok = ?
                ORDER BY m.nama_mhs";
        return $this->db->fetchAll($sql, [$this->id_kelompok]);
    }
    
    public function getPenjadwalan() {
        $sql = "SELECT p.*, d.nama_dosen, d.prodi
                FROM Penjadwalan p 
                JOIN Dosen d ON p.nomor_dosen = d.nomor_dosen 
                WHERE p.id_sidang = ?
                ORDER BY p.peran_dosen DESC";
        return $this->db->fetchAll($sql, [$this->id_sidang]);
    }
    
    public function getPenilaian() {
        $sql = "SELECT p.*, d.nama_dosen, m.nama_mhs
                FROM Penilaian p 
                JOIN Dosen d ON p.nomor_dosen = d.nomor_dosen 
                JOIN Mahasiswa m ON p.nim = m.nim 
                WHERE p.id_sidang = ?
                ORDER BY p.id_penilaian";
        return $this->db->fetchAll($sql, [$this->id_sidang]);
    }
    
    public function getDetailSidang() {
        $sql = "SELECT * FROM Detail_Sidang WHERE id_sidang = ?";
        return $this->db->fetchOne($sql, [$this->id_sidang]);
    }
    
    public function submitPengajuan($data) {
        $pengajuanData = [
            'id_kelompok' => $data['id_kelompok'],
            'judul' => $data['judul'],
            'status_ajuan' => 'Pending',
            'waktu_pengumpulan' => date('Y-m-d H:i:s'),
            'dok_laporan' => $data['dok_laporan'] ?? null,
            'status_sidang' => 0
        ];
        
        return $this->create($pengajuanData);
    }
    
    public function approvePengajuan() {
        $sql = "UPDATE Sidang SET status_ajuan = 'Approved' WHERE id_sidang = ?";
        return $this->db->execute($sql, [$this->id_sidang]);
    }
    
    public function rejectPengajuan($alasan = '') {
        $sql = "UPDATE Sidang SET status_ajuan = 'Rejected' WHERE id_sidang = ?";
        $result = $this->db->execute($sql, [$this->id_sidang]);
        
        if ($result && !empty($alasan)) {
            $this->addCatatan($alasan);
        }
        
        return $result;
    }
    
    public function startSidang() {
        $sql = "UPDATE Sidang SET status_sidang = 1 WHERE id_sidang = ?";
        return $this->db->execute($sql, [$this->id_sidang]);
    }
    
    public function completeSidang() {
        $sql = "UPDATE Sidang SET status_ajuan = 'Completed' WHERE id_sidang = ?";
        return $this->db->execute($sql, [$this->id_sidang]);
    }
    
    public function addCatatan($catatan) {
        $detailModel = new DetailSidang();
        $detailData = [
            'id_sidang' => $this->id_sidang,
            'catatan' => $catatan
        ];
        
        return $detailModel->create($detailData);
    }
    
    public function updateDokumen($dokLaporan) {
        $sql = "UPDATE Sidang SET dok_laporan = ? WHERE id_sidang = ?";
        return $this->db->execute($sql, [$dokLaporan, $this->id_sidang]);
    }
    
    public function getStatusDisplay() {
        $statusMap = [
            'Pending' => 'Menunggu Persetujuan',
            'Approved' => 'Disetujui',
            'Rejected' => 'Ditolak',
            'Completed' => 'Selesai'
        ];
        
        return $statusMap[$this->status_ajuan] ?? $this->status_ajuan;
    }
    
    public function getStatusSidangDisplay() {
        return $this->status_sidang == 1 ? 'Berlangsung' : 'Belum Dimulai';
    }
    
    public function isPending() {
        return $this->status_ajuan === 'Pending';
    }
    
    public function isApproved() {
        return $this->status_ajuan === 'Approved';
    }
    
    public function isRejected() {
        return $this->status_ajuan === 'Rejected';
    }
    
    public function isCompleted() {
        return $this->status_ajuan === 'Completed';
    }
    
    public function isOngoing() {
        return $this->status_sidang == 1;
    }
    
    public function canBeScheduled() {
        return $this->isApproved() && !$this->isOngoing();
    }
    
    public function canBeEvaluated() {
        return $this->isApproved() && $this->isOngoing();
    }
} 