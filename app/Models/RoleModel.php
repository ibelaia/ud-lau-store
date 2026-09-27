<?php

namespace App\Models;

use App\Core\Model;

/**
 * RoleModel — lookup role. Dipakai saat register (cari role_id untuk
 * 'customer') tanpa hardcode angka ID di Controller.
 */
class RoleModel extends Model
{
    protected string $table = 'roles';

    public function findByName(string $name): ?array
    {
        $result = $this->query(
            'SELECT * FROM roles WHERE name = :name LIMIT 1',
            ['name' => $name]
        );

        return $result[0] ?? null;
    }
}
