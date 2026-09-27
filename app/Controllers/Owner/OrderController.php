<?php

namespace App\Controllers\Owner;

use App\Core\Controller;
use PDO;
use Throwable;

class OrderController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

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
        $db = $this->getPdo();

        $action       = trim((string)$this->input('action'));
        $targetId     = (int)($this->input('id') ?? $this->input('order_id') ?? 0);
        $targetStatus = strtolower(trim((string)($this->input('to') ?? $this->input('update_status') ?? '')));

        if ($action === 'delete_order' && $targetId > 0) {
            try {
                $stmtDelItems = $db->prepare("DELETE FROM order_items WHERE order_id = :oid");
                $stmtDelItems->execute(['oid' => $targetId]);

                $stmtDelOrder = $db->prepare("DELETE FROM orders WHERE id = :id");
                $stmtDelOrder->execute(['id' => $targetId]);
            } catch (Throwable $th) {
            }
            $this->redirect('owner/orders?status=batal');
            return;
        }

        if ($action === 'update_status' && $targetId > 0 && !empty($targetStatus)) {
            $this->processStatusChange($db, $targetId, $targetStatus);

            if (in_array($targetStatus, ['ready', 'siap_diambil', 'siap'])) {
                $this->redirect('owner/orders?status=siap_diambil');
                return;
            } elseif (in_array($targetStatus, ['completed', 'selesai'])) {
                $this->redirect('owner/orders?status=selesai');
                return;
            } elseif (in_array($targetStatus, ['cancelled', 'batal', 'dibatalkan'])) {
                $this->redirect('owner/orders?status=batal');
                return;
            }
            $this->redirect('owner/orders');
            return;
        }

        $waitingCount = 0;
        $readyCount   = 0;
        try {
            $stmtWait = $db->query("SELECT COUNT(*) FROM orders WHERE LOWER(status) IN ('pending', 'menunggu', 'processing', 'diproses')");
            $waitingCount = (int)$stmtWait->fetchColumn();

            $stmtReady = $db->query("SELECT COUNT(*) FROM orders WHERE LOWER(status) IN ('ready', 'siap_diambil', 'siap diambil', 'siap')");
            $readyCount = (int)$stmtReady->fetchColumn();
        } catch (Throwable $th) {
            $waitingCount = 0;
            $readyCount   = 0;
        }

        $keyword     = trim((string)$this->input('q'));
        $statusParam = strtolower(trim((string)$this->input('status')));
        $orders      = [];

        try {
            $cols = $db->query("DESCRIBE orders")->fetchAll(PDO::FETCH_COLUMN);

            $codeCol  = in_array('booking_code', $cols) ? 'o.booking_code' : (in_array('code', $cols) ? 'o.code' : 'CONCAT("BK-", o.id)');
            $totalCol = in_array('total_amount', $cols) ? 'o.total_amount' : (in_array('total_price', $cols) ? 'o.total_price' : '0');
            $payCol   = in_array('payment_method', $cols) ? 'o.payment_method' : '"Bayar Tunai di Toko (Cash)"';
            
            $sql = "
                SELECT 
                    o.*,
                    {$codeCol} AS booking_code,
                    {$totalCol} AS total_amount,
                    {$payCol} AS payment_method,
                    COALESCE(u.name, " . (in_array('customer_name', $cols) ? 'o.customer_name' : '"Pelanggan"') . ") AS customer_name,
                    COALESCE(u.phone, " . (in_array('customer_phone', $cols) ? 'o.customer_phone' : '""') . ") AS customer_phone
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                WHERE 1=1
            ";

            $params = [];

            if (!empty($statusParam) && !in_array($statusParam, ['semua', 'all'])) {
                if ($statusParam === 'menunggu') {
                    $sql .= " AND LOWER(o.status) IN ('pending', 'menunggu', 'processing', 'diproses')";
                } elseif (in_array($statusParam, ['siap_diambil', 'ready'])) {
                    $sql .= " AND LOWER(o.status) IN ('ready', 'siap_diambil', 'siap diambil', 'siap')";
                } elseif (in_array($statusParam, ['selesai', 'completed'])) {
                    $sql .= " AND LOWER(o.status) IN ('completed', 'selesai', 'done')";
                } elseif (in_array($statusParam, ['batal', 'cancelled'])) {
                    $sql .= " AND LOWER(o.status) IN ('cancelled', 'dibatalkan', 'batal')";
                }
            }

            if (!empty($keyword)) {
                $searchConditions = [];
                if (in_array('booking_code', $cols)) $searchConditions[] = "o.booking_code LIKE :kw";
                if (in_array('code', $cols)) $searchConditions[] = "o.code LIKE :kw";
                if (in_array('customer_name', $cols)) $searchConditions[] = "o.customer_name LIKE :kw";
                $searchConditions[] = "u.name LIKE :kw";
                
                if (!empty($searchConditions)) {
                    $sql .= " AND (" . implode(' OR ', $searchConditions) . ")";
                    $params['kw'] = '%' . $keyword . '%';
                }
            }

            $sql .= " ORDER BY o.id DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $rawOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rawOrders as $item) {
                $orderId     = (int)$item['id'];
                $itemSummary = 'Kebutuhan ATK';
                $calcTotal   = 0;
                $details     = [];

                try {
                    $itemCols = $db->query("DESCRIBE order_items")->fetchAll(PDO::FETCH_COLUMN);
                    $priceCol = in_array('price', $itemCols) ? 'oi.price' : 'p.price';

                    $stmtItems = $db->prepare("
                        SELECT oi.quantity, {$priceCol} AS item_price, p.name, p.image 
                        FROM order_items oi 
                        LEFT JOIN products p ON oi.product_id = p.id 
                        WHERE oi.order_id = :oid
                    ");
                    $stmtItems->execute(['oid' => $orderId]);
                    $orderItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($orderItems)) {
                        $parts = [];
                        foreach ($orderItems as $oi) {
                            $qty   = (int)($oi['quantity'] ?? 1);
                            $price = (float)($oi['item_price'] ?? 0);
                            $calcTotal += ($qty * $price);
                            $parts[] = ($oi['name'] ?? 'Barang') . ' (' . $qty . ' pcs)';

                            $imgSrc = '';
                            $rawImg = $oi['image'] ?? '';
                            if (!empty($rawImg)) {
                                $dec = json_decode($rawImg, true);
                                $imgSrc = is_array($dec) ? ($dec[0] ?? '') : $rawImg;
                                if (!empty($imgSrc) && !str_starts_with($imgSrc, 'http')) {
                                    $imgSrc = base_url($imgSrc);
                                }
                            }

                            $details[] = [
                                'name'     => $oi['name'] ?? 'Produk ATK',
                                'qty'      => $qty,
                                'price'    => $price,
                                'subtotal' => $qty * $price,
                                'image'    => $imgSrc
                            ];
                        }
                        $itemSummary = implode(', ', $parts);
                    }
                } catch (Throwable $thItems) {
                }

                if ((float)($item['total_amount'] ?? 0) <= 0 && $calcTotal > 0) {
                    $item['total_amount'] = $calcTotal;
                }

                $item['items_summary'] = $itemSummary;
                $item['items_list']    = $details;
                $orders[] = $item;
            }

        } catch (Throwable $th) {
            $orders = [];
        }

        $this->view('owner.orders.index', [
            'title'          => 'Daftar Booking & Pickup — UD. LAU STORE',
            'orders'         => $orders,
            'q'              => $keyword,
            'selectedStatus' => $statusParam,
            'waitingCount'   => $waitingCount,
            'readyCount'     => $readyCount,
        ], '');
    }

    private function processStatusChange(PDO $db, int $orderId, string $reqStatus): void
    {
        if ($orderId <= 0 || empty($reqStatus)) return;

        try {
            $stmtCol = $db->query("SHOW COLUMNS FROM orders LIKE 'status'");
            $colInfo = $stmtCol->fetch(PDO::FETCH_ASSOC);
            $type = strtolower($colInfo['Type'] ?? '');

            $targetStatus = $reqStatus;
            if (str_contains($type, "'siap_diambil'")) {
                if (in_array($reqStatus, ['ready', 'siap', 'siap_diambil'])) {
                    $targetStatus = 'siap_diambil';
                } elseif (in_array($reqStatus, ['completed', 'selesai'])) {
                    $targetStatus = 'selesai';
                } elseif (in_array($reqStatus, ['cancelled', 'batal', 'dibatalkan'])) {
                    $targetStatus = 'dibatalkan';
                }
            } elseif (str_contains($type, "'ready'")) {
                if (in_array($reqStatus, ['ready', 'siap', 'siap_diambil'])) {
                    $targetStatus = 'ready';
                } elseif (in_array($reqStatus, ['completed', 'selesai'])) {
                    $targetStatus = 'completed';
                } elseif (in_array($reqStatus, ['cancelled', 'batal', 'dibatalkan'])) {
                    $targetStatus = 'cancelled';
                }
            }

            $stmt = $db->prepare("UPDATE orders SET status = :status WHERE id = :id");
            $stmt->execute([
                'status' => $targetStatus,
                'id'     => $orderId
            ]);

            if (in_array($reqStatus, ['cancelled', 'batal', 'dibatalkan'])) {
                try {
                    $stmtItems = $db->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = :oid");
                    $stmtItems->execute(['oid' => $orderId]);
                    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($items as $it) {
                        $stmtRestock = $db->prepare("UPDATE products SET stock = stock + :qty WHERE id = :pid");
                        $stmtRestock->execute([
                            'qty' => (int)$it['quantity'],
                            'pid' => (int)$it['product_id']
                        ]);
                    }
                } catch (Throwable $thItems) {
                }
            }
        } catch (Throwable $th) {
        }
    }
}