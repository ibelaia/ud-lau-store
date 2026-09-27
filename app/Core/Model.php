<?php

namespace App\Core;

use PDO;

/**
 * Model — kelas dasar untuk seluruh Model.
 * Sesuai CS-03: seluruh query WAJIB prepared statement, tidak ada
 * string concatenation langsung ke SQL.
 * Sesuai CS-04: query SQL hanya boleh ada di sini/Model turunannya,
 * tidak pernah di Controller atau View.
 *
 * Setiap Model turunan wajib mengisi properti $table, dan opsional
 * $softDelete (true jika tabel punya kolom deleted_at — hanya berlaku
 * untuk tabel Master sesuai keputusan implementasi: users, categories,
 * products, payment_methods, banners).
 */
abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected bool $softDelete = false;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function find(int|string $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $sql .= $this->softDelete ? ' AND deleted_at IS NULL' : '';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function all(array $orderBy = []): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $sql .= $this->softDelete ? ' WHERE deleted_at IS NULL' : '';

        if (!empty($orderBy)) {
            $column = preg_replace('/[^a-zA-Z0-9_]/', '', $orderBy['column'] ?? 'id');
            $direction = strtoupper($orderBy['direction'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';
            $sql .= " ORDER BY {$column} {$direction}";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function where(string $column, string $operator, mixed $value): array
    {
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
        $allowedOperators = ['=', '!=', '>', '<', '>=', '<=', 'LIKE'];
        if (!in_array($operator, $allowedOperators, true)) {
            $operator = '=';
        }

        $sql = "SELECT * FROM {$this->table} WHERE {$column} {$operator} :value";
        $sql .= $this->softDelete ? ' AND deleted_at IS NULL' : '';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => $value]);

        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":{$col}", $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function update(int|string $id, array $data): bool
    {
        $assignments = array_map(fn($col) => "{$col} = :{$col}", array_keys($data));

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = :id',
            $this->table,
            implode(', ', $assignments),
            $this->primaryKey
        );

        $data['id'] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Hapus data. Jika $softDelete true (hanya untuk tabel Master),
     * hapus dengan mengisi deleted_at, bukan menghapus baris fisik.
     * Tabel transaksi (orders, payments, order_items, stock_logs, dst)
     * tidak pernah memakai soft delete — sesuai keputusan implementasi.
     */
    public function delete(int|string $id): bool
    {
        if ($this->softDelete) {
            $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE {$this->primaryKey} = :id";
        } else {
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    protected function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    protected function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
