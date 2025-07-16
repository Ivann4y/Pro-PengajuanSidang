<?php

namespace App\Models;

use PDO;

class Notifikasi extends Model {
    protected $table = 'notifikasi';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 'user_role', 'judul', 'pesan', 'url', 'status', 'created_at'
    ];

    public function createNotifikasi($data) {
        $notifikasiData = [
            'user_id' => $data['user_id'],
            'user_role' => $data['user_role'],
            'judul' => $data['judul'],
            'pesan' => $data['pesan'],
            'url' => $data['url'] ?? null,
            'status' => $data['status'] ?? 'unread',
            'created_at' => $data['created_at'] ?? date('Y-m-d H:i:s')
        ];
        return $this->create($notifikasiData);
    }

    public function getByUser($userId, $userRole, $limit = 10) {
        $sql = "SELECT * FROM notifikasi WHERE user_id = ? AND user_role = ? ORDER BY created_at DESC" . ($limit ? " OFFSET 0 ROWS FETCH NEXT $limit ROWS ONLY" : "");
        return $this->db->fetchAll($sql, [$userId, $userRole]);
    }

    public function getUnreadByUser($userId, $userRole) {
        $sql = "SELECT * FROM notifikasi WHERE user_id = ? AND user_role = ? AND status = 'unread' ORDER BY created_at DESC";
        return $this->db->fetchAll($sql, [$userId, $userRole]);
    }

    public function getCountUnread($userId, $userRole) {
        $sql = "SELECT COUNT(*) as count FROM notifikasi WHERE user_id = ? AND user_role = ? AND status = 'unread'";
        $result = $this->db->fetchOne($sql, [$userId, $userRole]);
        return $result ? $result['count'] : 0;
    }

    public function markAsRead($notifId, $userId) {
        $sql = "UPDATE notifikasi SET status = 'read' WHERE id = ? AND user_id = ?";
        return $this->db->execute($sql, [$notifId, $userId]);
    }

    public function markAllAsRead($userId, $userRole) {
        $sql = "UPDATE notifikasi SET status = 'read' WHERE user_id = ? AND user_role = ?";
        return $this->db->execute($sql, [$userId, $userRole]);
    }

    public function deleteNotifikasi($notifId, $userId) {
        $sql = "DELETE FROM notifikasi WHERE id = ? AND user_id = ?";
        return $this->db->execute($sql, [$notifId, $userId]);
    }
} 