<?php

class Notifikasi extends Model {
    protected $table = 'Notifikasi';
    protected $primaryKey = 'id_notifikasi';
    protected $fillable = [
        'id_notifikasi', 'penerima', 'pesan', 'waktu', 
        'status_baca', 'pengirim'
    ];
    
    public function createNotifikasi($data) {
        $notifikasiData = [
            'penerima' => $data['penerima'],
            'pesan' => $data['pesan'],
            'waktu' => $data['waktu'] ?? date('Y-m-d H:i:s'),
            'status_baca' => $data['status_baca'] ?? 0,
            'pengirim' => $data['pengirim']
        ];
        
        return $this->create($notifikasiData);
    }
    
    public function getNotifikasiByPenerima($penerima) {
        $sql = "SELECT * FROM Notifikasi WHERE penerima = ? ORDER BY waktu DESC";
        return $this->db->fetchAll($sql, [$penerima]);
    }
    
    public function getNotifikasiUnread($penerima) {
        $sql = "SELECT * FROM Notifikasi WHERE penerima = ? AND status_baca = 0 ORDER BY waktu DESC";
        return $this->db->fetchAll($sql, [$penerima]);
    }
    
    public function getCountUnread($penerima) {
        $sql = "SELECT COUNT(*) as count FROM Notifikasi WHERE penerima = ? AND status_baca = 0";
        $result = $this->db->fetchOne($sql, [$penerima]);
        return $result ? $result['count'] : 0;
    }
    
    public function markAsRead($idNotifikasi, $penerima) {
        $sql = "UPDATE Notifikasi SET status_baca = 1 WHERE id_notifikasi = ? AND penerima = ?";
        return $this->db->execute($sql, [$idNotifikasi, $penerima]);
    }
    
    public function markAllAsRead($penerima) {
        $sql = "UPDATE Notifikasi SET status_baca = 1 WHERE penerima = ?";
        return $this->db->execute($sql, [$penerima]);
    }
    
    public function deleteNotifikasi($idNotifikasi, $penerima) {
        $sql = "DELETE FROM Notifikasi WHERE id_notifikasi = ? AND penerima = ?";
        return $this->db->execute($sql, [$idNotifikasi, $penerima]);
    }
    
    public function sendNotifikasiPengajuan($nim, $status) {
        $mahasiswaModel = new Mahasiswa();
        $mahasiswa = $mahasiswaModel->findByUsername($nim);
        
        if (!$mahasiswa) {
            return false;
        }
        
        $pesan = '';
        switch ($status) {
            case 'Approved':
                $pesan = "Pengajuan sidang Anda telah disetujui. Silakan cek jadwal sidang.";
                break;
            case 'Rejected':
                $pesan = "Pengajuan sidang Anda ditolak. Silakan cek detail dan lakukan perbaikan.";
                break;
            case 'Pending':
                $pesan = "Pengajuan sidang Anda sedang dalam proses review.";
                break;
        }
        
        $notifikasiData = [
            'penerima' => $nim,
            'pesan' => $pesan,
            'pengirim' => 'Admin'
        ];
        
        return $this->createNotifikasi($notifikasiData);
    }
    
    public function sendNotifikasiPenjadwalan($idSidang, $nomorDosen) {
        $sidangModel = new Sidang();
        $sidang = $sidangModel->find($idSidang);
        
        if (!$sidang) {
            return false;
        }
        
        $dosenModel = new Dosen();
        $dosen = $dosenModel->find($nomorDosen);
        
        if (!$dosen) {
            return false;
        }
        
        $pesan = "Anda telah ditugaskan sebagai penguji untuk sidang: {$sidang['judul']}";
        
        $notifikasiData = [
            'penerima' => $dosen['username'],
            'pesan' => $pesan,
            'pengirim' => 'Admin'
        ];
        
        return $this->createNotifikasi($notifikasiData);
    }
    
    public function sendNotifikasiPenilaian($idSidang, $nim) {
        $sidangModel = new Sidang();
        $sidang = $sidangModel->find($idSidang);
        
        if (!$sidang) {
            return false;
        }
        
        $pesan = "Penilaian untuk sidang '{$sidang['judul']}' telah selesai. Silakan cek nilai akhir Anda.";
        
        $notifikasiData = [
            'penerima' => $nim,
            'pesan' => $pesan,
            'pengirim' => 'Dosen'
        ];
        
        return $this->createNotifikasi($notifikasiData);
    }
    
    public function sendNotifikasiRevisi($idSidang, $nim, $catatan) {
        $sidangModel = new Sidang();
        $sidang = $sidangModel->find($idSidang);
        
        if (!$sidang) {
            return false;
        }
        
        $pesan = "Sidang '{$sidang['judul']}' memerlukan revisi. Catatan: {$catatan}";
        
        $notifikasiData = [
            'penerima' => $nim,
            'pesan' => $pesan,
            'pengirim' => 'Dosen'
        ];
        
        return $this->createNotifikasi($notifikasiData);
    }
    
    public function getWaktuDisplay() {
        $waktu = new DateTime($this->waktu);
        $sekarang = new DateTime();
        $selisih = $sekarang->diff($waktu);
        
        if ($selisih->days > 0) {
            return $selisih->days . ' hari yang lalu';
        } elseif ($selisih->h > 0) {
            return $selisih->h . ' jam yang lalu';
        } elseif ($selisih->i > 0) {
            return $selisih->i . ' menit yang lalu';
        } else {
            return 'Baru saja';
        }
    }
    
    public function isUnread() {
        return $this->status_baca == 0;
    }
    
    public function getStatusBacaDisplay() {
        return $this->status_baca == 1 ? 'Sudah Dibaca' : 'Belum Dibaca';
    }
} 