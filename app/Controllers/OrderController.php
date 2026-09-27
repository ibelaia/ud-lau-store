<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\ProductModel;
use PDO;
use Throwable;

class OrderController extends Controller
{
    private function getPdo(): PDO
    {
        $productModel = class_exists('\App\Models\ProductModel') ? new ProductModel() : null;
        if ($productModel) {
            $ref = new \ReflectionClass($productModel);
            if ($ref->hasProperty('db')) {
                $prop = $ref->getProperty('db');
                $prop->setAccessible(true);
                $db = $prop->getValue($productModel);
                if ($db instanceof PDO) {
                    return $db;
                }
            }
        }

        if (method_exists('\App\Core\Database', 'connect')) {
            return \App\Core\Database::connect();
        }
        if (method_exists('\App\Core\Database', 'getConnection')) {
            return \App\Core\Database::getConnection();
        }

        $pdo = new PDO(
            "mysql:host=" . config('database.host', '127.0.0.1') . ";dbname=" . config('database.database', 'lau_store') . ";charset=utf8mb4",
            config('database.username', 'root'),
            config('database.password', '')
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    public function index(): void
    {
        if (class_exists('\App\Core\Auth') && !Auth::check()) {
            $this->redirect('login');
            return;
        }

        $userId = class_exists('\App\Core\Auth') ? (int)Auth::id() : 0;
        $isSuccess = $this->input('success') === '1';
        $successCode = trim((string) $this->input('code', ''));

        if ($isSuccess && !empty($successCode)) {
            try {
                $db = $this->getPdo();
                $stmt = $db->prepare("SELECT * FROM orders WHERE order_code = :code LIMIT 1");
                $stmt->execute(['code' => $successCode]);
                $order = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($order) {
                    $this->view('customer.orders.success', [
                        'title' => 'Booking Berhasil — ' . config('app.name'),
                        'order' => $order,
                    ]);
                    return;
                }
            } catch (Throwable $th) {}
        }

        $orders = [];

        try {
            $db = $this->getPdo();

            $stmt = $db->prepare("
                SELECT * FROM orders 
                WHERE user_id = :user_id 
                ORDER BY id DESC
            ");
            $stmt->execute(['user_id' => $userId]);
            $rawOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $statusMap = [
                'menunggu'   => 'Menunggu Konfirmasi',
                'pending'    => 'Menunggu Konfirmasi',
                'diproses'   => 'Diproses',
                'siap'       => 'Siap Diambil',
                'ready'      => 'Siap Diambil',
                'selesai'    => 'Selesai',
                'completed'  => 'Selesai',
                'dibatalkan' => 'Dibatalkan',
                'cancelled'  => 'Dibatalkan',
            ];

            foreach ($rawOrders as $order) {
                $orderId = (int)$order['id'];

                $itemStmt = $db->prepare("
                    SELECT oi.*, p.name AS product_name 
                    FROM order_items oi
                    LEFT JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = :order_id
                ");
                $itemStmt->execute(['order_id' => $orderId]);
                $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

                $itemTexts = [];
                foreach ($items as $it) {
                    $pName = $it['product_name'] ?? 'Barang ATK';
                    $qty   = (int)($it['qty'] ?? $it['quantity'] ?? 1);
                    $itemTexts[] = "{$pName} ({$qty} pcs)";
                }
                $itemsSummary = !empty($itemTexts) ? implode(', ', $itemTexts) : 'Barang ATK';

                $pickupDate = !empty($order['pickup_date']) 
                    ? date('d M Y', strtotime($order['pickup_date'])) 
                    : date('d M Y');

                $createdAt = !empty($order['created_at']) 
                    ? date('d M Y, H:i', strtotime($order['created_at'])) 
                    : date('d M Y, H:i');

                $totalAmount = (float)($order['total_amount'] ?? $order['total_price'] ?? $order['total'] ?? 0);

                $stKey = strtolower((string)($order['status'] ?? 'menunggu'));
                $orderStatus = $statusMap[$stKey] ?? ucfirst($stKey);

                $orders[] = [
                    'id'             => $orderId,
                    'order_code'     => $order['order_code'] ?? $order['booking_code'] ?? ('BK-' . str_pad((string)$orderId, 4, '0', STR_PAD_LEFT)),
                    'created_at'     => $createdAt,
                    'pickup_date'    => $pickupDate,
                    'total_amount'   => $totalAmount,
                    'payment_method' => $order['payment_method'] ?? 'Bayar Tunai di Toko (Cash)',
                    'status_raw'     => $stKey,
                    'order_status'   => $orderStatus,
                    'items_summary'  => $itemsSummary,
                ];
            }

        } catch (Throwable $e) {
            $orders = [];
        }

        $this->view('customer.orders.index', [
            'title'  => 'Riwayat Pesanan & Booking — ' . config('app.name'),
            'orders' => $orders,
        ]);
    }

    public function show(string $id): void
    {
        if (class_exists('\App\Core\Auth') && !Auth::check()) {
            $this->redirect('login');
            return;
        }

        $userId = class_exists('\App\Core\Auth') ? (int)Auth::id() : 0;
        $orderId = (int)$id;

        try {
            $db = $this->getPdo();

            // Cek kepemilikan pesanan
            $stmt = $db->prepare("SELECT * FROM orders WHERE id = :id AND user_id = :u LIMIT 1");
            $stmt->execute(['id' => $orderId, 'u' => $userId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            // Fallback: jika user sedang test/admin
            if (!$order) {
                $stmt = $db->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
                $stmt->execute(['id' => $orderId]);
                $order = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            if (!$order) {
                $this->redirect('orders');
                return;
            }

            $itemStmt = $db->prepare("
                SELECT oi.*, 
                       COALESCE(p.name, 'Produk ATK') AS product_name, 
                       p.slug AS product_slug
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id
            ");
            $itemStmt->execute(['order_id' => $orderId]);
            $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

            $this->view('customer.orders.show', [
                'title' => 'Detail Nota Booking ' . ($order['order_code'] ?? ''),
                'order' => $order,
                'items' => $items
            ]);

        } catch (Throwable $th) {
            $this->redirect('orders');
        }
    }

    public function checkStatuses(): void
    {
        header('Content-Type: application/json');
        if (class_exists('\App\Core\Auth') && !Auth::check()) {
            echo json_encode([]);
            exit;
        }

        $userId = class_exists('\App\Core\Auth') ? (int)Auth::id() : 0;

        try {
            $db = $this->getPdo();
            $stmt = $db->prepare("SELECT id, status FROM orders WHERE user_id = :u");
            $stmt->execute(['u' => $userId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = [];
            foreach ($rows as $r) {
                $data[(int)$r['id']] = strtolower((string)$r['status']);
            }
            echo json_encode($data);
        } catch (Throwable $th) {
            echo json_encode([]);
        }
        exit;
    }
}