<?php

namespace App\Models;

use App\Core\Model;

class CategoryModel extends Model
{
    protected string $table = 'categories';
    protected string $primaryKey = 'id';
    protected bool $softDelete = true;

    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug";
        $sql .= $this->softDelete ? ' AND deleted_at IS NULL' : '';
        $sql .= " LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        $res = $stmt->fetch();
        return $res ?: null;
    }
}