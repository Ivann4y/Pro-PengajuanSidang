<?php

class Admin extends User {
    protected $table = 'Admin';
    protected $primaryKey = 'id_admin';
    protected $fillable = [
        'id_admin', 'nama_admin', 'email', 'password_hash', 
        'username', 'role', 'created_at'
    ];
    
    public function getDashboardStats() {
        $stats = [];
        
        // Count total students
        $sql = "SELECT COUNT(*) as count FROM Mahasiswa";
        $result = $this->db->fetchOne($sql);
        $stats['total_mahasiswa'] = $result ? $result['count'] : 0;
        
        // Count total lecturers
        $sql = "SELECT COUNT(*) as count FROM Dosen";
        $result = $this->db->fetchOne($sql);
        $stats['total_dosen'] = $result ? $result['count'] : 0;
        
        // Count pending submissions
        $sql = "SELECT COUNT(*) as count FROM Sidang WHERE status_ajuan = 'Pending'";
        $result = $this->db->fetchOne($sql);
        $stats['pengajuan_pending'] = $result ? $result['count'] : 0;
        
        // Count approved submissions
        $sql = "SELECT COUNT(*) as count FROM Sidang WHERE status_ajuan = 'Approved'";
        $result = $this->db->fetchOne($sql);
        $stats['pengajuan_approved'] = $result ? $result['count'] : 0;
        
        // Count ongoing defenses
        $sql = "SELECT COUNT(*) as count FROM Sidang WHERE status_sidang = 1";
        $result = $this->db->fetchOne($sql);
        $stats['sidang_berlangsung'] = $result ? $result['count'] : 0;
        
        // Count completed defenses
        $sql = "SELECT COUNT(*) as count FROM Sidang WHERE status_sidang = 1 AND status_ajuan = 'Completed'";
        $result = $this->db->fetchOne($sql);
        $stats['sidang_selesai'] = $result ? $result['count'] : 0;
        
        return $stats;
    }
    
    public function getPengajuanPending() {
        $sql = "SELECT s.*, k.nomor_kelompok, k.tahun_ajaran, k.jenis_sidang, 
                       mk.nama_matkul, m.nama_mhs, m.nim
                FROM Sidang s 
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                JOIN Mahasiswa m ON k.nim = m.nim 
                WHERE s.status_ajuan = 'Pending'
                ORDER BY s.waktu_pengumpulan ASC";
        return $this->db->fetchAll($sql);
    }
    
    public function getPengajuanApproved() {
        $sql = "SELECT s.*, k.nomor_kelompok, k.tahun_ajaran, k.jenis_sidang, 
                       mk.nama_matkul, m.nama_mhs, m.nim
                FROM Sidang s 
                JOIN Kelompok k ON s.id_kelompok = k.id_kelompok 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                JOIN Mahasiswa m ON k.nim = m.nim 
                WHERE s.status_ajuan = 'Approved'
                ORDER BY s.waktu_pengumpulan ASC";
        return $this->db->fetchAll($sql);
    }
    
    public function approvePengajuan($idSidang) {
        $sql = "UPDATE Sidang SET status_ajuan = 'Approved' WHERE id_sidang = ?";
        return $this->db->execute($sql, [$idSidang]);
    }
    
    public function rejectPengajuan($idSidang, $alasan = '') {
        $sql = "UPDATE Sidang SET status_ajuan = 'Rejected' WHERE id_sidang = ?";
        $result = $this->db->execute($sql, [$idSidang]);
        
        if ($result && !empty($alasan)) {
            // Add rejection note to Detail_Sidang
            $detailSql = "INSERT INTO Detail_Sidang (id_sidang, catatan) VALUES (?, ?)";
            $this->db->execute($detailSql, [$idSidang, $alasan]);
        }
        
        return $result;
    }
    
    public function getAllDosen() {
        $sql = "SELECT * FROM Dosen ORDER BY nama_dosen";
        return $this->db->fetchAll($sql);
    }
    
    public function getDosenByProdi($prodi) {
        $sql = "SELECT * FROM Dosen WHERE prodi = ? ORDER BY nama_dosen";
        return $this->db->fetchAll($sql, [$prodi]);
    }
    
    public function getDosenPembimbing() {
        $sql = "SELECT * FROM Dosen WHERE isPembimbing = 1 ORDER BY nama_dosen";
        return $this->db->fetchAll($sql);
    }
    
    public function getDosenPenguji() {
        $sql = "SELECT * FROM Dosen WHERE isPenguji = 1 ORDER BY nama_dosen";
        return $this->db->fetchAll($sql);
    }
    
    public function createPenjadwalan($idSidang, $nomorDosen, $peranDosen) {
        $sql = "INSERT INTO Penjadwalan (id_sidang, nomor_dosen, peran_dosen) VALUES (?, ?, ?)";
        return $this->db->execute($sql, [$idSidang, $nomorDosen, $peranDosen]);
    }
    
    public function deletePenjadwalan($idSidang, $nomorDosen) {
        $sql = "DELETE FROM Penjadwalan WHERE id_sidang = ? AND nomor_dosen = ?";
        return $this->db->execute($sql, [$idSidang, $nomorDosen]);
    }
    
    public function getPenjadwalanBySidang($idSidang) {
        $sql = "SELECT p.*, d.nama_dosen, d.prodi
                FROM Penjadwalan p 
                JOIN Dosen d ON p.nomor_dosen = d.nomor_dosen 
                WHERE p.id_sidang = ?
                ORDER BY p.peran_dosen DESC";
        return $this->db->fetchAll($sql, [$idSidang]);
    }
    
    public function getAllMahasiswa() {
        $sql = "SELECT * FROM Mahasiswa ORDER BY nama_mhs";
        return $this->db->fetchAll($sql);
    }
    
    public function getMahasiswaByProdi($prodi) {
        $sql = "SELECT * FROM Mahasiswa WHERE prodi = ? ORDER BY nama_mhs";
        return $this->db->fetchAll($sql, [$prodi]);
    }
    
    public function getAllKelompok() {
        $sql = "SELECT k.*, mk.nama_matkul, m.nama_mhs
                FROM Kelompok k 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                JOIN Mahasiswa m ON k.nim = m.nim 
                ORDER BY k.tahun_ajaran DESC, k.nomor_kelompok";
        return $this->db->fetchAll($sql);
    }
    
    public function getKelompokByTahunAjaran($tahunAjaran) {
        $sql = "SELECT k.*, mk.nama_matkul, m.nama_mhs
                FROM Kelompok k 
                JOIN MataKuliah mk ON k.id_matkul = mk.id_matkul 
                JOIN Mahasiswa m ON k.nim = m.nim 
                WHERE k.tahun_ajaran = ?
                ORDER BY k.nomor_kelompok";
        return $this->db->fetchAll($sql, [$tahunAjaran]);
    }
    
    public function getAllMataKuliah() {
        $sql = "SELECT * FROM MataKuliah ORDER BY nama_matkul";
        return $this->db->fetchAll($sql);
    }
    
    public function getAllKelas() {
        $sql = "SELECT * FROM Kelas ORDER BY nama_kelas";
        return $this->db->fetchAll($sql);
    }
    
    public function updateProfile($data) {
        $allowedFields = ['nama_admin', 'email'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));
        
        return $this->update($this->id_admin, $updateData);
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
        return $this->updatePassword($this->id_admin, $newPassword);
    }
    
    public function sendNotifikasi($penerima, $pesan) {
        $notifikasiData = [
            'penerima' => $penerima,
            'pesan' => $pesan,
            'waktu' => date('Y-m-d H:i:s'),
            'status_baca' => 0,
            'pengirim' => $this->username
        ];
        
        $notifikasiModel = new Notifikasi();
        return $notifikasiModel->create($notifikasiData);
    }
} 