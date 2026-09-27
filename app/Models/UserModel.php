<?php

namespace App\Models;

use App\Core\Model;

/**
 * UserModel — contoh konkret penggunaan Base Model.
 * Merepresentasikan tabel `users` (Master Data → soft delete aktif,
 * sesuai keputusan implementasi: hanya tabel Master yang pakai deleted_at).
 *
 * Kelas Model untuk modul-modul berikutnya (Tahap 8+) mengikuti pola
 * yang sama seperti ini.
 */
class UserModel extends Model
{
    protected string $table = 'users';
    protected bool $softDelete = true;

    /**
     * Dipakai saat login. Langsung JOIN ke roles supaya AuthController
     * tahu role_name-nya dalam satu query (untuk redirect dashboard).
     */
    public function findByEmail(string $email): ?array
    {
        $result = $this->query(
            'SELECT users.*, roles.name AS role_name
             FROM users
             JOIN roles ON roles.id = users.role_id
             WHERE users.email = :email AND users.deleted_at IS NULL
             LIMIT 1',
            ['email' => $email]
        );

        return $result[0] ?? null;
    }

    public function findByUsername(string $username): ?array
    {
        $result = $this->query(
            'SELECT users.*, roles.name AS role_name
             FROM users
             JOIN roles ON roles.id = users.role_id
             WHERE users.username = :username AND users.deleted_at IS NULL
             LIMIT 1',
            ['username' => $username]
        );

        return $result[0] ?? null;
    }

    /**
     * Ambil user beserta nama role-nya (JOIN ke tabel roles).
     * Dipakai saat login untuk menentukan redirect dashboard mana.
     */
    public function findWithRole(int $id): ?array
    {
        $result = $this->query(
            'SELECT users.*, roles.name AS role_name
             FROM users
             JOIN roles ON roles.id = users.role_id
             WHERE users.id = :id AND users.deleted_at IS NULL
             LIMIT 1',
            ['id' => $id]
        );

        return $result[0] ?? null;
    }
}
