<?php

namespace App\Models;

use App\Core\Model;

class ProductImageModel extends Model
{
    protected string $table = 'product_images';

    public function getByProductId(int $productId): array
    {
        // Ganti 'produk_id' menjadi 'product_id'
        return $this->query(
            "SELECT * FROM {$this->table} WHERE product_id = :product_id ORDER BY id ASC",
            ['product_id' => $productId]
        );
    }
}