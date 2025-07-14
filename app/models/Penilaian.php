<?php

class Penilaian extends Model {
    protected $table = 'Penilaian';
    protected $primaryKey = 'id_penilaian';
    protected $fillable = [
        'id_penilaian', 'id_sidang', 'nim', 'nomor_dosen', 
        'n_dokumen', 'n_presentasi', 'n_tanyajawab', 'n_proyek', 
        'bobot_penilaian', 'catatan'
    ];
    
    public function getDosen() {
        $sql = "SELECT * FROM Dosen WHERE nomor_dosen = ?";
        return $this->db->fetchOne($sql, [$this->nomor_dosen]);
    }
    
    public function getMahasiswa() {
        $sql = "SELECT * FROM Mahasiswa WHERE nim = ?";
        return $this->db->fetchOne($sql, [$this->nim]);
    }
    
    public function getSidang() {
        $sql = "SELECT * FROM Sidang WHERE id_sidang = ?";
        return $this->db->fetchOne($sql, [$this->id_sidang]);
    }
    
    public function calculateNilaiAkhir() {
        return ($this->n_dokumen * 0.25) + 
               ($this->n_presentasi * 0.25) + 
               ($this->n_tanyajawab * 0.30) + 
               ($this->n_proyek * 0.20);
    }
    
    public function getNilaiHuruf() {
        $nilai = $this->calculateNilaiAkhir();
        
        if ($nilai >= 85) return 'A';
        if ($nilai >= 80) return 'A-';
        if ($nilai >= 75) return 'B+';
        if ($nilai >= 70) return 'B';
        if ($nilai >= 65) return 'B-';
        if ($nilai >= 60) return 'C+';
        if ($nilai >= 55) return 'C';
        if ($nilai >= 50) return 'C-';
        if ($nilai >= 45) return 'D+';
        if ($nilai >= 40) return 'D';
        return 'E';
    }
    
    public function validateNilai($nilai) {
        return $nilai >= 0 && $nilai <= 100;
    }
    
    public function submitPenilaian($data) {
        // Validate scores
        $scores = ['n_dokumen', 'n_presentasi', 'n_tanyajawab', 'n_proyek'];
        foreach ($scores as $score) {
            if (!isset($data[$score]) || !$this->validateNilai($data[$score])) {
                throw new Exception("Invalid score for {$score}");
            }
        }
        
        $penilaianData = [
            'id_sidang' => $data['id_sidang'],
            'nim' => $data['nim'],
            'nomor_dosen' => $data['nomor_dosen'],
            'n_dokumen' => $data['n_dokumen'],
            'n_presentasi' => $data['n_presentasi'],
            'n_tanyajawab' => $data['n_tanyajawab'],
            'n_proyek' => $data['n_proyek'],
            'bobot_penilaian' => $data['bobot_penilaian'] ?? 1.0,
            'catatan' => $data['catatan'] ?? ''
        ];
        
        return $this->create($penilaianData);
    }
    
    public function updatePenilaian($data) {
        // Validate scores
        $scores = ['n_dokumen', 'n_presentasi', 'n_tanyajawab', 'n_proyek'];
        foreach ($scores as $score) {
            if (isset($data[$score]) && !$this->validateNilai($data[$score])) {
                throw new Exception("Invalid score for {$score}");
            }
        }
        
        return $this->update($this->id_penilaian, $data);
    }
    
    public function getPenilaianBySidangAndMahasiswa($idSidang, $nim) {
        $sql = "SELECT * FROM Penilaian WHERE id_sidang = ? AND nim = ?";
        return $this->db->fetchAll($sql, [$idSidang, $nim]);
    }
    
    public function getPenilaianByDosen($nomorDosen) {
        $sql = "SELECT p.*, s.judul, m.nama_mhs, m.nim
                FROM Penilaian p 
                JOIN Sidang s ON p.id_sidang = s.id_sidang 
                JOIN Mahasiswa m ON p.nim = m.nim 
                WHERE p.nomor_dosen = ?
                ORDER BY p.id_penilaian DESC";
        return $this->db->fetchAll($sql, [$nomorDosen]);
    }
    
    public function getRataRataNilai($idSidang, $nim) {
        $sql = "SELECT AVG((n_dokumen * 0.25 + n_presentasi * 0.25 + n_tanyajawab * 0.30 + n_proyek * 0.20)) as rata_rata
                FROM Penilaian 
                WHERE id_sidang = ? AND nim = ?";
        $result = $this->db->fetchOne($sql, [$idSidang, $nim]);
        return $result ? round($result['rata_rata'], 2) : null;
    }
    
    public function getNilaiTerendah($idSidang, $nim) {
        $sql = "SELECT MIN((n_dokumen * 0.25 + n_presentasi * 0.25 + n_tanyajawab * 0.30 + n_proyek * 0.20)) as nilai_terendah
                FROM Penilaian 
                WHERE id_sidang = ? AND nim = ?";
        $result = $this->db->fetchOne($sql, [$idSidang, $nim]);
        return $result ? round($result['nilai_terendah'], 2) : null;
    }
    
    public function getNilaiTertinggi($idSidang, $nim) {
        $sql = "SELECT MAX((n_dokumen * 0.25 + n_presentasi * 0.25 + n_tanyajawab * 0.30 + n_proyek * 0.20)) as nilai_tertinggi
                FROM Penilaian 
                WHERE id_sidang = ? AND nim = ?";
        $result = $this->db->fetchOne($sql, [$idSidang, $nim]);
        return $result ? round($result['nilai_tertinggi'], 2) : null;
    }
} 