<?php

namespace App\Models;

use App\Core\Model;

class ProductModel extends Model
{
    protected string $table = 'products';
    protected string $primaryKey = 'id';
    protected bool $softDelete = true;

    /**
     * Ambil produk terbaru yang aktif
     */
    public function getLatest(int $limit = 6): array
    {
        $sql = "SELECT p.*, 
                       (p.stock - p.reserved_stock) AS available_stock,
                       c.name AS category_name, 
                       pi.image_path AS primary_image
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
                WHERE p.deleted_at IS NULL 
                  AND p.is_active = 1
                ORDER BY p.id DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ambil produk unggulan / terlaris
     */
    public function getTopSelling(int $limit = 6): array
    {
        $sql = "SELECT p.*, 
                       (p.stock - p.reserved_stock) AS available_stock,
                       c.name AS category_name, 
                       pi.image_path AS primary_image
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
                WHERE p.deleted_at IS NULL 
                  AND p.is_active = 1
                ORDER BY p.is_featured DESC, p.id DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ambil detail satu produk berdasarkan slug
     */
    public function findBySlug(string $slug): ?array
    {
        $sql = "SELECT p.*, 
                       (p.stock - p.reserved_stock) AS available_stock,
                       c.name AS category_name, 
                       c.slug AS category_slug
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.slug = :slug 
                  AND p.deleted_at IS NULL 
                  AND p.is_active = 1
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Pencarian & Filter Produk (FR-008, BR-41)
     */
    public function search(array $filters = []): array
    {
        $params = [];
        $sql = "SELECT p.*, 
                       (p.stock - p.reserved_stock) AS available_stock,
                       c.name AS category_name, 
                       pi.image_path AS primary_image
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
                WHERE p.deleted_at IS NULL 
                  AND p.is_active = 1";

        if (!empty($filters['q'])) {
            $keyword = trim($filters['q']);
            $sql .= " AND (p.name LIKE :kw OR p.product_code = :exact_code)";
            $params['kw'] = "%{$keyword}%";
            $params['exact_code'] = $keyword;
        }

        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        $sql .= " ORDER BY p.id DESC";

        return $this->query($sql, $params);
    }
}