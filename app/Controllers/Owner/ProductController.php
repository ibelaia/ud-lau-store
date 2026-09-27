<?php

namespace App\Controllers\Owner;

use App\Core\Controller;
use PDO;
use Throwable;

class ProductController extends Controller
{
    private function getPdo(): PDO
    {
        if (class_exists('\App\Core\Database')) {
            if (method_exists('\App\Core\Database', 'connect')) {
                return \App\Core\Database::connect();
            }
            if (method_exists('\App\Core\Database', 'getConnection')) {
                return \App\Core\Database::getConnection();
            }
        }

        $host   = function_exists('config') ? config('database.host', '127.0.0.1') : '127.0.0.1';
        $dbName = function_exists('config') ? config('database.database', 'lau_store') : 'lau_store';
        $user   = function_exists('config') ? config('database.username', 'root') : 'root';
        $pass   = function_exists('config') ? config('database.password', '') : '';

        $pdo = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    private function resolveCategoryId(PDO $db, string $selectedCat, string $newCatName): int
    {
        $catName = trim($newCatName);
        if (($selectedCat === 'new' || $selectedCat === '+new' || empty($selectedCat)) && !empty($catName)) {
            try {
                $stmtCheck = $db->prepare("SELECT id FROM categories WHERE LOWER(name) = LOWER(:name) LIMIT 1");
                $stmtCheck->execute(['name' => $catName]);
                $existingId = $stmtCheck->fetchColumn();

                if ($existingId) {
                    return (int)$existingId;
                }

                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $catName), '-'));
                if (empty($slug)) {
                    $slug = 'kategori-' . time();
                }

                $catCols = $db->query("DESCRIBE categories")->fetchAll(PDO::FETCH_COLUMN);

                if (in_array('slug', $catCols)) {
                    $stmtInsert = $db->prepare("INSERT INTO categories (name, slug) VALUES (:name, :slug)");
                    $stmtInsert->execute(['name' => $catName, 'slug' => $slug]);
                } else {
                    $stmtInsert = $db->prepare("INSERT INTO categories (name) VALUES (:name)");
                    $stmtInsert->execute(['name' => $catName]);
                }

                return (int)$db->lastInsertId();
            } catch (Throwable $th) {
                $fallback = $db->query("SELECT id FROM categories LIMIT 1")->fetchColumn();
                return $fallback ? (int)$fallback : 1;
            }
        }

        $id = (int)$selectedCat;
        if ($id <= 0) {
            $fallback = $db->query("SELECT id FROM categories LIMIT 1")->fetchColumn();
            return $fallback ? (int)$fallback : 1;
        }

        return $id;
    }

    public function index(): void
    {
        $keyword = trim((string)$this->input('q'));
        $db = $this->getPdo();
        $products = [];

        try {
            $sql = "
                SELECT p.*, c.name AS category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE 1=1
            ";
            $params = [];

            if (!empty($keyword)) {
                $sql .= " AND (p.name LIKE :kw OR p.description LIKE :kw OR c.name LIKE :kw)";
                $params['kw'] = '%' . $keyword . '%';
            }

            $sql .= " ORDER BY p.id DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $th) {
            $products = [];
        }

        $this->view('owner.products.index', [
            'title'    => 'Katalog Produk — UD. LAU STORE',
            'products' => $products,
            'q'        => $keyword,
        ], '');
    }

    public function create(): void
    {
        $db = $this->getPdo();
        $categories = [];

        try {
            $categories = $db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $th) {
            $categories = [];
        }

        $this->view('owner.products.create', [
            'title'      => 'Tambah Produk — UD. LAU STORE',
            'categories' => $categories,
        ], '');
    }

    public function store(): void
    {
        $db = $this->getPdo();

        $name            = trim((string)$this->input('name'));
        $rawPrice        = (float)$this->input('price');
        $rawOrigPrice    = (float)$this->input('original_price');
        $discountPercent = (int)$this->input('discount_percent');
        $stock           = max(0, (int)$this->input('stock'));
        $description     = trim((string)$this->input('description'));
        $catInput        = trim((string)$this->input('category_id'));
        $newCatName      = trim((string)$this->input('new_category_name'));
        $isFeatured      = !empty($this->input('is_featured')) ? 1 : 0;

        $originalPrice = max(0, $rawOrigPrice);
        $price         = max(0, $rawPrice);

        if ($price <= 0 && $originalPrice > 0) {
            $price = $originalPrice;
        }

        if ($discountPercent < 0 || $discountPercent > 99) {
            $discountPercent = 0;
        }

        $categoryId = $this->resolveCategoryId($db, $catInput, $newCatName);

        $uploadedImages = [];
        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = (defined('FCPATH') ? FCPATH : dirname(__DIR__, 3) . '/public/') . 'uploads/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['images']['tmp_name'] as $k => $tmpName) {
                if (!empty($tmpName) && is_uploaded_file($tmpName)) {
                    $ext = strtolower(pathinfo($_FILES['images']['name'][$k], PATHINFO_EXTENSION));
                    $filename = 'prod_' . time() . '_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($tmpName, $uploadDir . $filename)) {
                        $uploadedImages[] = 'uploads/products/' . $filename;
                    }
                }
            }
        }

        $imageJson = !empty($uploadedImages) ? json_encode($uploadedImages) : '';

        try {
            $cols = $db->query("DESCRIBE products")->fetchAll(PDO::FETCH_COLUMN);

            $fields = ['name', 'category_id', 'price', 'stock', 'description'];
            $placeholders = [':name', ':category_id', ':price', ':stock', ':description'];
            $params = [
                'name'        => $name,
                'category_id' => $categoryId,
                'price'       => $price,
                'stock'       => $stock,
                'description' => $description
            ];

            // Generate kode produk unik otomatis untuk menghindari error duplicate entry
            if (in_array('product_code', $cols)) {
                $fields[] = 'product_code';
                $placeholders[] = ':product_code';
                $params['product_code'] = 'PRD-' . strtoupper(bin2hex(random_bytes(3)));
            } elseif (in_array('code', $cols)) {
                $fields[] = 'code';
                $placeholders[] = ':code';
                $params['code'] = 'PRD-' . strtoupper(bin2hex(random_bytes(3)));
            }

            // Slug otomatis
            if (in_array('slug', $cols)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
                if (empty($slug)) $slug = 'produk-' . time();
                $fields[] = 'slug';
                $placeholders[] = ':slug';
                $params['slug'] = $slug . '-' . uniqid();
            }

            if (in_array('image', $cols)) {
                $fields[] = 'image';
                $placeholders[] = ':image';
                $params['image'] = $imageJson;
            }

            if (in_array('is_featured', $cols)) {
                $fields[] = 'is_featured';
                $placeholders[] = ':is_featured';
                $params['is_featured'] = $isFeatured;
            }
            if (in_array('original_price', $cols)) {
                $fields[] = 'original_price';
                $placeholders[] = ':original_price';
                $params['original_price'] = $originalPrice;
            }
            if (in_array('discount_percent', $cols)) {
                $fields[] = 'discount_percent';
                $placeholders[] = ':discount_percent';
                $params['discount_percent'] = $discountPercent;
            }
            if (in_array('created_at', $cols)) {
                $fields[] = 'created_at';
                $placeholders[] = 'NOW()';
            }
            if (in_array('updated_at', $cols)) {
                $fields[] = 'updated_at';
                $placeholders[] = 'NOW()';
            }

            $sql = "INSERT INTO products (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            $this->redirect('owner/products');
            return;
        } catch (Throwable $th) {
            die("<div style='font-family:sans-serif;padding:2rem;background:#fee2e2;color:#991b1b;border-radius:8px;'>
                    <h3>Gagal Menyimpan Produk ke Database!</h3>
                    <p><b>Pesan Error:</b> " . htmlspecialchars($th->getMessage()) . "</p>
                    <p><a href='" . base_url('owner/products/create') . "'>&larr; Kembali ke Form Tambah Produk</a></p>
                 </div>");
        }
    }

    public function edit(string $id): void
    {
        $db = $this->getPdo();
        $pId = (int)$id;
        $product = null;
        $categories = [];

        try {
            $stmt = $db->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $pId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            $categories = $db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $th) {
        }

        if (!$product) {
            $this->redirect('owner/products');
            return;
        }

        $this->view('owner.products.edit', [
            'title'      => 'Edit Produk — UD. LAU STORE',
            'product'    => $product,
            'categories' => $categories,
        ], '');
    }

    public function update(string $id): void
    {
        $db = $this->getPdo();
        $pId = (int)$id;

        $name            = trim((string)$this->input('name'));
        $rawPrice        = (float)$this->input('price');
        $rawOrigPrice    = (float)$this->input('original_price');
        $discountPercent = (int)$this->input('discount_percent');
        $stock           = max(0, (int)$this->input('stock'));
        $description     = trim((string)$this->input('description'));
        $catInput        = trim((string)$this->input('category_id'));
        $newCatName      = trim((string)$this->input('new_category_name'));
        $isFeatured      = !empty($this->input('is_featured')) ? 1 : 0;

        $originalPrice = max(0, $rawOrigPrice);
        $price         = max(0, $rawPrice);

        if ($price <= 0 && $originalPrice > 0) {
            $price = $originalPrice;
        }

        if ($discountPercent < 0 || $discountPercent > 99) {
            $discountPercent = 0;
        }

        $categoryId = $this->resolveCategoryId($db, $catInput, $newCatName);

        $existingImages = $this->input('existing_images') ?? [];
        if (!is_array($existingImages)) $existingImages = [];

        if (!empty($_FILES['images']['name'][0])) {
            $uploadDir = (defined('FCPATH') ? FCPATH : dirname(__DIR__, 3) . '/public/') . 'uploads/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($_FILES['images']['tmp_name'] as $k => $tmpName) {
                if (!empty($tmpName) && is_uploaded_file($tmpName)) {
                    $ext = strtolower(pathinfo($_FILES['images']['name'][$k], PATHINFO_EXTENSION));
                    $filename = 'prod_' . time() . '_' . uniqid() . '.' . $ext;
                    if (move_uploaded_file($tmpName, $uploadDir . $filename)) {
                        $existingImages[] = 'uploads/products/' . $filename;
                    }
                }
            }
        }

        $imageJson = !empty($existingImages) ? json_encode(array_values($existingImages)) : '';

        try {
            $cols = $db->query("DESCRIBE products")->fetchAll(PDO::FETCH_COLUMN);

            $updates = [
                'name = :name',
                'category_id = :category_id',
                'price = :price',
                'stock = :stock',
                'description = :description'
            ];
            $params = [
                'name'        => $name,
                'category_id' => $categoryId,
                'price'       => $price,
                'stock'       => $stock,
                'description' => $description,
                'id'          => $pId
            ];

            if (in_array('image', $cols)) {
                $updates[] = 'image = :image';
                $params['image'] = $imageJson;
            }

            if (in_array('is_featured', $cols)) {
                $updates[] = 'is_featured = :is_featured';
                $params['is_featured'] = $isFeatured;
            }
            if (in_array('original_price', $cols)) {
                $updates[] = 'original_price = :original_price';
                $params['original_price'] = $originalPrice;
            }
            if (in_array('discount_percent', $cols)) {
                $updates[] = 'discount_percent = :discount_percent';
                $params['discount_percent'] = $discountPercent;
            }
            if (in_array('updated_at', $cols)) {
                $updates[] = 'updated_at = NOW()';
            }

            $sql = "UPDATE products SET " . implode(', ', $updates) . " WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            $this->redirect('owner/products');
            return;
        } catch (Throwable $th) {
            die("<div style='font-family:sans-serif;padding:2rem;background:#fee2e2;color:#991b1b;border-radius:8px;'>
                    <h3>Gagal Memperbarui Produk!</h3>
                    <p><b>Pesan Error:</b> " . htmlspecialchars($th->getMessage()) . "</p>
                    <p><a href='" . base_url('owner/products/' . $pId . '/edit') . "'>&larr; Kembali</a></p>
                 </div>");
        }
    }

    public function delete(string $id): void
    {
        $db = $this->getPdo();
        $pId = (int)$id;

        if ($pId > 0) {
            try {
                $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
                $stmt->execute(['id' => $pId]);
            } catch (Throwable $th) {
            }
        }

        $this->redirect('owner/products');
    }
}