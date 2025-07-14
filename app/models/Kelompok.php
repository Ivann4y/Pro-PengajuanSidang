<?php

class Kelompok extends Model {
    protected $table = 'Kelompok';
    protected $primaryKey = 'id_kelompok';
    protected $fillable = [
        'id_kelompok', 'nomor_kelompok', 'nim', 'tahun_ajaran', 
        'jenis_sidang', 'id_matkul'
    ];
    
    public function getMahasiswa() {
        $sql = "SELECT m.* 
                FROM Mahasiswa m 
                JOIN Kelompok k ON m.nim = k.nim 
                WHERE k.nomor_kelompok = ? AND k.tahun_ajaran = ? 
                AND k.jenis_sidang = ? AND k.id_matkul = ?
                ORDER BY m.nama_mhs";
        return $this->db->fetchAll($sql, [
            $this->nomor_kelompok, 
            $this->tahun_ajaran, 
            $this->jenis_sidang, 
            $this->id_matkul
        ]);
    }
    
    public function getMataKuliah() {
        $sql = "SELECT * FROM MataKuliah WHERE id_matkul = ?";
        return $this->db->fetchOne($sql, [$this->id_matkul]);
    }
    
    public function getBimbingan() {
        $sql = "SELECT b.*, d.nama_dosen, d.email
                FROM Bimbingan b 
                JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen 
                WHERE b.id_kelompok = ?";
        return $this->db->fetchAll($sql, [$this->id_kelompok]);
    }
    
    public function getPembimbing() {
        $sql = "SELECT d.* 
                FROM Bimbingan b 
                JOIN Dosen d ON b.nomor_dosen = d.nomor_dosen 
                WHERE b.id_kelompok = ? AND b.isPembimbing = 1";
        return $this->db->fetchOne($sql, [$this->id_kelompok]);
    }
    
    public function getSidang() {
        $sql = "SELECT * FROM Sidang WHERE id_kelompok = ?";
        return $this->db->fetchOne($sql, [$this->id_kelompok]);
    }
    
    public function createKelompok($data) {
        $kelompokData = [
            'nomor_kelompok' => $data['nomor_kelompok'],
            'nim' => $data['nim'],
            'tahun_ajaran' => $data['tahun_ajaran'],
            'jenis_sidang' => $data['jenis_sidang'],
            'id_matkul' => $data['id_matkul']
        ];
        
        return $this->create($kelompokData);
    }
    
    public function addMahasiswa($nim) {
        $kelompokData = [
            'nomor_kelompok' => $this->nomor_kelompok,
            'nim' => $nim,
            'tahun_ajaran' => $this->tahun_ajaran,
            'jenis_sidang' => $this->jenis_sidang,
            'id_matkul' => $this->id_matkul
        ];
        
        return $this->create($kelompokData);
    }
    
    public function removeMahasiswa($nim) {
        $sql = "DELETE FROM Kelompok 
                WHERE nomor_kelompok = ? AND nim = ? AND tahun_ajaran = ? 
                AND jenis_sidang = ? AND id_matkul = ?";
        return $this->db->execute($sql, [
            $this->nomor_kelompok, $nim, $this->tahun_ajaran, 
            $this->jenis_sidang, $this->id_matkul
        ]);
    }
    
    public function assignPembimbing($nomorDosen) {
        $bimbinganModel = new Bimbingan();
        $bimbinganData = [
            'id_kelompok' => $this->id_kelompok,
            'nomor_dosen' => $nomorDosen,
            'isPembimbing' => 1
        ];
        
        return $bimbinganModel->create($bimbinganData);
    }
    
    public function getJumlahAnggota() {
        $sql = "SELECT COUNT(*) as count 
                FROM Kelompok 
                WHERE nomor_kelompok = ? AND tahun_ajaran = ? 
                AND jenis_sidang = ? AND id_matkul = ?";
        $result = $this->db->fetchOne($sql, [
            $this->nomor_kelompok, $this->tahun_ajaran, 
            $this->jenis_sidang, $this->id_matkul
        ]);
        return $result ? $result['count'] : 0;
    }
    
    public function isMahasiswaInKelompok($nim) {
        $sql = "SELECT COUNT(*) as count 
                FROM Kelompok 
                WHERE nomor_kelompok = ? AND nim = ? AND tahun_ajaran = ? 
                AND jenis_sidang = ? AND id_matkul = ?";
        $result = $this->db->fetchOne($sql, [
            $this->nomor_kelompok, $nim, $this->tahun_ajaran, 
            $this->jenis_sidang, $this->id_matkul
        ]);
        return $result && $result['count'] > 0;
    }
    
    public function getJenisSidangDisplay() {
        $jenisMap = [
            'Tugas Akhir' => 'Tugas Akhir',
            'Semester' => 'Proyek Semester'
        ];
        
        return $jenisMap[$this->jenis_sidang] ?? $this->jenis_sidang;
    }
    
    public function getKelompokDisplayName() {
        $mataKuliah = $this->getMataKuliah();
        $namaMatkul = $mataKuliah ? $mataKuliah['nama_matkul'] : 'Unknown';
        
        return "Kelompok {$this->nomor_kelompok} - {$namaMatkul} ({$this->jenis_sidang})";
    }
    
    public function hasSidang() {
        $sidang = $this->getSidang();
        return $sidang !== false;
    }
    
    public function getSidangStatus() {
        $sidang = $this->getSidang();
        if (!$sidang) {
            return 'Belum Ada Pengajuan';
        }
        
        return $sidang['status_ajuan'];
    }
} 