<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use PDO;
use Throwable;

class ProductController extends Controller
{
    private ProductModel $productModel;
    private CategoryModel $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    private function getPdo(): PDO
    {
        $ref = new \ReflectionClass($this->productModel);
        if ($ref->hasProperty('db')) {
            $prop = $ref->getProperty('db');
            $prop->setAccessible(true);
            $db = $prop->getValue($this->productModel);
            if ($db instanceof PDO) {
                return $db;
            }
        }

        if (method_exists('\App\Core\Database', 'connect')) {
            return \App\Core\Database::connect();
        }
        if (method_exists('\App\Core\Database', 'getConnection')) {
            return \App\Core\Database::getConnection();
        }

        $host   = function_exists('config') ? config('database.host', '127.0.0.1') : '127.0.0.1';
        $dbName = function_exists('config') ? config('database.database', 'lau_store') : 'lau_store';
        $user   = function_exists('config') ? config('database.username', 'root') : 'root';
        $pass   = function_exists('config') ? config('database.password', '') : '';

        $pdo = new PDO(
            "mysql:host={$host};dbname={$dbName};charset=utf8mb4",
            $user,
            $pass
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    public function index(): void
    {
        $keyword    = trim((string)$this->input('q'));
        $categoryId = trim((string)($this->input('kategori') ?: $this->input('category')));
        $products   = [];
        $categories = [];

        try {
            $db = $this->getPdo();

            // Ambil daftar kategori
            $categories = $db->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

            // Query produk lengkap dengan badge unggulan & diskon
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

            if (!empty($categoryId) && !in_array($categoryId, ['semua', 'all'])) {
                if (is_numeric($categoryId)) {
                    $sql .= " AND p.category_id = :cid";
                    $params['cid'] = (int)$categoryId;
                } else {
                    $sql .= " AND (LOWER(c.name) = :cname OR LOWER(c.slug) = :cname)";
                    $params['cname'] = strtolower($categoryId);
                }
            }

            // Utamakan Produk Unggulan di paling atas, baru produk terbaru
            $cols = $db->query("DESCRIBE products")->fetchAll(PDO::FETCH_COLUMN);
            if (in_array('is_featured', $cols)) {
                $sql .= " ORDER BY p.is_featured DESC, p.id DESC";
            } else {
                $sql .= " ORDER BY p.id DESC";
            }

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Throwable $th) {
            $products = [];
        }

        $this->view('products.index', [
            'title'            => 'Katalog Produk ATK — ' . (function_exists('config') ? config('app.name', 'UD. LAU STORE') : 'UD. LAU STORE'),
            'products'         => $products,
            'categories'       => $categories,
            'q'                => $keyword,
            'selectedCategory' => $categoryId,
        ]);
    }

    public function show(string $idOrSlug): void
    {
        $product = null;

        try {
            $db = $this->getPdo();

            if (is_numeric($idOrSlug)) {
                $stmt = $db->prepare("
                    SELECT p.*, c.name AS category_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.id = :id 
                    LIMIT 1
                ");
                $stmt->execute(['id' => (int)$idOrSlug]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            if (!$product) {
                $cols = $db->query("DESCRIBE products")->fetchAll(PDO::FETCH_COLUMN);
                if (in_array('slug', $cols)) {
                    $stmt = $db->prepare("
                        SELECT p.*, c.name AS category_name 
                        FROM products p 
                        LEFT JOIN categories c ON p.category_id = c.id 
                        WHERE p.slug = :slug 
                        LIMIT 1
                    ");
                    $stmt->execute(['slug' => $idOrSlug]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }

            if (!$product) {
                $decoded = urldecode($idOrSlug);
                $stmt = $db->prepare("
                    SELECT p.*, c.name AS category_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.name = :name 
                    LIMIT 1
                ");
                $stmt->execute(['name' => $decoded]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
            }

        } catch (Throwable $th) {
            $product = null;
        }

        if (!$product) {
            $this->redirect('products');
            return;
        }

        $images = [];
        $rawImg = $product['image'] ?? '';
        if (!empty($rawImg)) {
            $decoded = json_decode($rawImg, true);
            if (is_array($decoded) && !empty($decoded)) {
                $images = $decoded;
            } else {
                $images[] = $rawImg;
            }
        }

        $this->view('products.show', [
            'title'   => ($product['name'] ?? 'Detail Produk') . ' — ' . (function_exists('config') ? config('app.name', 'UD. LAU STORE') : 'UD. LAU STORE'),
            'product' => $product,
            'images'  => $images,
        ]);
    }
}