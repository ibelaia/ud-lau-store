<?php

namespace App\Models;

use App\Core\Model;

class MessageModel extends Model
{
    protected string $table = 'messages';

    /**
     * Ambil riwayat chat antara customer dan owner
     */
    public function getConversation(int $userId, int $ownerId): array
    {
        return $this->query(
            "SELECT m.*, p.name AS product_name, p.slug AS product_slug, p.price AS product_price
             FROM {$this->table} m 
             LEFT JOIN products p ON m.product_id = p.id 
             WHERE (m.sender_id = :u1 AND m.receiver_id = :o1) 
                OR (m.sender_id = :o2 AND m.receiver_id = :u2) 
             ORDER BY m.created_at ASC",
            [
                'u1' => $userId, 'o1' => $ownerId,
                'o2' => $ownerId, 'u2' => $userId
            ]
        );
    }

    /**
     * Hitung pesan masuk yang belum dibaca untuk penerima
     */
    public function countUnread(int $userId): int
    {
        $res = $this->query(
            "SELECT COUNT(*) as total FROM {$this->table} WHERE receiver_id = :u AND is_read = 0",
            ['u' => $userId]
        );
        return (int)($res[0]['total'] ?? 0);
    }

    /**
     * Tandai semua pesan masuk untuk user tertentu sebagai terbaca
     */
    public function markAllAsReadForUser(int $receiverId): void
    {
        $this->query(
            "UPDATE {$this->table} SET is_read = 1 WHERE receiver_id = :r AND is_read = 0",
            ['r' => $receiverId]
        );
    }

    /**
     * Tandai pesan dari pengirim spesifik ke penerima sebagai terbaca
     */
    public function markAsRead(int $senderId, int $receiverId): void
    {
        $this->query(
            "UPDATE {$this->table} SET is_read = 1 WHERE sender_id = :s AND receiver_id = :r AND is_read = 0",
            ['s' => $senderId, 'r' => $receiverId]
        );
    }

    /**
     * Tandai pesan dari customer spesifik ke owner sebagai terbaca
     */
    public function markCustomerAsReadByOwner(int $customerId, int $ownerId): void
    {
        $this->markAsRead($customerId, $ownerId);
    }

    /**
     * Kirim pesan baru
     */
    public function sendMessage(int $senderId, int $receiverId, ?int $productId, string $message): bool
    {
        $this->query(
            "INSERT INTO {$this->table} (sender_id, receiver_id, product_id, message, is_read, created_at) 
             VALUES (:s, :r, :p, :m, 0, NOW())",
            [
                's' => $senderId,
                'r' => $receiverId,
                'p' => $productId ?: null,
                'm' => $message
            ]
        );
        return true;
    }

    /**
     * Cari ID akun owner toko utama
     */
    public function getOwnerId(): int
    {
        $res = $this->query(
            "SELECT u.id FROM users u 
             LEFT JOIN roles r ON u.role_id = r.id 
             WHERE LOWER(r.name) = 'owner' OR LOWER(r.name) = 'admin' 
             ORDER BY u.id ASC LIMIT 1"
        );
        return (int)($res[0]['id'] ?? 1);
    }

    /**
     * Ambil ID Owner aktif berdasarkan percakapan terakhir user
     */
    public function getLatestOwnerIdForUser(int $userId): int
    {
        $res = $this->query(
            "SELECT sender_id FROM {$this->table} WHERE receiver_id = :u ORDER BY id DESC LIMIT 1",
            ['u' => $userId]
        );
        if (!empty($res[0]['sender_id'])) {
            return (int)$res[0]['sender_id'];
        }
        return $this->getOwnerId();
    }

    /**
     * Ambil daftar percakapan inbox untuk owner
     */
    public function getConversationsForOwner(int $ownerId): array
    {
        return $this->query(
            "SELECT u.id AS customer_id, u.name AS customer_name,
                    (SELECT message FROM messages WHERE (sender_id = u.id AND receiver_id = :o1) OR (sender_id = :o2 AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) AS last_message,
                    (SELECT created_at FROM messages WHERE (sender_id = u.id AND receiver_id = :o3) OR (sender_id = :o4 AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) AS last_time,
                    (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = :o5 AND is_read = 0) AS unread_count
             FROM users u
             WHERE u.id IN (
                 SELECT DISTINCT sender_id FROM messages WHERE receiver_id = :o6
                 UNION
                 SELECT DISTINCT receiver_id FROM messages WHERE sender_id = :o7
             ) AND u.id != :o8
             ORDER BY last_time DESC",
            [
                'o1' => $ownerId, 'o2' => $ownerId, 'o3' => $ownerId,
                'o4' => $ownerId, 'o5' => $ownerId, 'o6' => $ownerId, 
                'o7' => $ownerId, 'o8' => $ownerId
            ]
        );
    }

    /**
     * Ambil info customer berdasarkan ID
     */
    public function getCustomerById(int $customerId): ?array
    {
        $res = $this->query("SELECT id, name, email, phone FROM users WHERE id = :id LIMIT 1", ['id' => $customerId]);
        return $res[0] ?? null;
    }
}