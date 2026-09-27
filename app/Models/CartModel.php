<?php

namespace App\Models;

use App\Core\Model;

class CartModel extends Model
{
    protected string $table = 'carts';
    protected string $primaryKey = 'id';
    protected bool $softDelete = false;

    /**
     * Dapatkan ID keranjang aktif (atau buat baru jika belum ada)
     */
    public function getActiveCartId(?int $userId, string $sessionId): int
    {
        if ($userId) {
            $sql = "SELECT id FROM carts WHERE user_id = :user_id AND status = 'active' LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            $cart = $stmt->fetch();

            if ($cart) return (int) $cart['id'];

            // Jika sebelumnya punya keranjang guest, tautkan ke user
            $sqlGuest = "SELECT id FROM carts WHERE session_id = :session_id AND user_id IS NULL AND status = 'active' LIMIT 1";
            $stmtGuest = $this->db->prepare($sqlGuest);
            $stmtGuest->execute(['session_id' => $sessionId]);
            $guestCart = $stmtGuest->fetch();

            if ($guestCart) {
                $this->update($guestCart['id'], ['user_id' => $userId]);
                return (int) $guestCart['id'];
            }

            return $this->create(['user_id' => $userId, 'session_id' => $sessionId, 'status' => 'active']);
        }

        // Untuk tamu (guest)
        $sql = "SELECT id FROM carts WHERE session_id = :session_id AND user_id IS NULL AND status = 'active' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['session_id' => $sessionId]);
        $cart = $stmt->fetch();

        if ($cart) return (int) $cart['id'];

        return $this->create(['session_id' => $sessionId, 'status' => 'active']);
    }

    /**
     * Ambil item dalam keranjang
     */
    public function getItems(int $cartId): array
    {
        $sql = "SELECT ci.*, p.name, p.slug, (p.stock - p.reserved_stock) AS available_stock, pi.image_path
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.id
                LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
                WHERE ci.cart_id = :cart_id
                ORDER BY ci.id DESC";

        return $this->query($sql, ['cart_id' => $cartId]);
    }

    /**
     * Tambah produk ke keranjang
     */
    public function addItem(int $cartId, int $productId, int $qty, float $price): void
    {
        $sql = "SELECT id, qty FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['cart_id' => $cartId, 'product_id' => $productId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $newQty = $existing['qty'] + $qty;
            $updateSql = "UPDATE cart_items SET qty = :qty, price = :price WHERE id = :id";
            $this->execute($updateSql, ['qty' => $newQty, 'price' => $price, 'id' => $existing['id']]);
        } else {
            $insertSql = "INSERT INTO cart_items (cart_id, product_id, qty, price) VALUES (:cart_id, :product_id, :qty, :price)";
            $this->execute($insertSql, [
                'cart_id'    => $cartId,
                'product_id' => $productId,
                'qty'        => $qty,
                'price'      => $price
            ]);
        }
    }
}