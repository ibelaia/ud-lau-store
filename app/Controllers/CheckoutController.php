<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\ProductModel;
use PDO;
use Throwable;

class CheckoutController extends Controller
{
    private ProductModel $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new ProductModel();
    }

    private function getPdo(): PDO
    {
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

    public function instant(): void
    {
        if (class_exists('\App\Core\Auth') && !Auth::check()) {
            $this->redirect('login');
            return;
        }

        $productId = (int)($this->input('product_id') ?? $_GET['product_id'] ?? 0);
        $qty       = max(1, (int)($this->input('qty') ?? $_GET['qty'] ?? 1));

        if ($productId > 0) {
            $product = $this->productModel->find($productId);
            if ($product) {
                $_SESSION['cart'][$productId] = $qty;
            }
        }

        $this->redirect('checkout');
    }

    public function storeInstant(): void
    {
        $this->instant();
    }

    public function index(): void
    {
        if (class_exists('\App\Core\Auth') && !Auth::check()) {
            $this->redirect('login');
            return;
        }

        $cartSession = $_SESSION['cart'] ?? [];
        $items = [];
        $total = 0;

        if (!empty($cartSession)) {
            foreach ($cartSession as $productId => $qty) {
                $product = $this->productModel->find((int)$productId);
                if ($product) {
                    $price = (float)$product['price'];
                    $qty = (int)$qty;
                    $subtotal = $price * $qty;
                    $total += $subtotal;

                    $items[] = [
                        'id'         => $product['id'],
                        'name'       => $product['name'],
                        'price'      => $price,
                        'qty'        => $qty,
                        'subtotal'   => $subtotal,
                        'image_path' => $product['image'] ?? null,
                    ];
                }
            }
        }

        if (empty($items)) {
            $this->redirect('cart');
            return;
        }

        $db = $this->getPdo();
        $paymentMethods = [];
        try {
            $paymentMethods = $db->query("SELECT * FROM payment_methods ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $th) {
            $paymentMethods = [
                ['id' => 1, 'name' => 'Transfer Bank', 'is_active' => 1],
                ['id' => 2, 'name' => 'QRIS (ShopeePay/GoPay/OVO/BCA)', 'is_active' => 1],
                ['id' => 3, 'name' => 'Bayar Tunai di Toko (Cash)', 'is_active' => 1],
            ];
        }

        $this->view('checkout.index', [
            'title'          => 'Booking — ' . config('app.name'),
            'items'          => $items,
            'total'          => $total,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function store(): void
    {
        if (class_exists('\App\Core\Auth') && !Auth::check()) {
            $this->redirect('login');
            return;
        }

        $userId = 0;
        if (class_exists('\App\Core\Auth') && Auth::id()) {
            $userId = (int)Auth::id();
        }
        if ($userId === 0 && isset($_SESSION['user']['id'])) {
            $userId = (int)$_SESSION['user']['id'];
        }
        if ($userId === 0 && isset($_SESSION['user_id'])) {
            $userId = (int)$_SESSION['user_id'];
        }

        if ($userId <= 0) {
            $this->redirect('login');
            return;
        }

        $cartSession = $_SESSION['cart'] ?? [];
        if (empty($cartSession)) {
            $this->redirect('cart');
            return;
        }

        $pickupDate = $this->input('pickup_date') ?: date('Y-m-d');
        $rawPaymentId = (int)$this->input('payment_method', 1);
        $orderCode = 'BK-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        $orderItems = [];
        $totalAmount = 0;

        foreach ($cartSession as $productId => $qty) {
            $product = $this->productModel->find((int)$productId);
            if ($product) {
                $price = (float)$product['price'];
                $quantity = (int)$qty;
                $subtotal = $price * $quantity;
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'product_id' => (int)$product['id'],
                    'price'      => $price,
                    'qty'        => $quantity,
                    'subtotal'   => $subtotal
                ];
            }
        }

        if (empty($orderItems)) {
            $this->redirect('cart');
            return;
        }

        try {
            $db = $this->getPdo();

            $stmtPm = $db->prepare("SELECT id, name, is_active FROM payment_methods WHERE id = :id LIMIT 1");
            $stmtPm->execute(['id' => $rawPaymentId]);
            $pmRow = $stmtPm->fetch(PDO::FETCH_ASSOC);

            if (!$pmRow || (int)($pmRow['is_active'] ?? 1) === 0) {
                die('Metode pembayaran yang Anda pilih saat ini sedang tidak tersedia. Silakan pilih metode lain.');
            }

            $paymentMethodId = (int)$pmRow['id'];
            $paymentMethodName = $pmRow['name'];

            $orderCols = $db->query("DESCRIBE orders")->fetchAll(PDO::FETCH_COLUMN);

            $orderData = [];
            if (in_array('user_id', $orderCols)) $orderData['user_id'] = $userId;
            if (in_array('customer_id', $orderCols)) $orderData['customer_id'] = $userId;
            if (in_array('order_code', $orderCols)) $orderData['order_code'] = $orderCode;
            elseif (in_array('booking_code', $orderCols)) $orderData['booking_code'] = $orderCode;
            elseif (in_array('code', $orderCols)) $orderData['code'] = $orderCode;

            if (in_array('total_price', $orderCols)) $orderData['total_price'] = $totalAmount;
            if (in_array('total_amount', $orderCols)) $orderData['total_amount'] = $totalAmount;
            if (in_array('total', $orderCols)) $orderData['total'] = $totalAmount;

            if (in_array('payment_method_id', $orderCols)) $orderData['payment_method_id'] = $paymentMethodId;
            if (in_array('payment_method', $orderCols)) $orderData['payment_method'] = $paymentMethodName;

            if (in_array('pickup_date', $orderCols)) $orderData['pickup_date'] = $pickupDate;
            if (in_array('status', $orderCols)) $orderData['status'] = 'menunggu';
            if (in_array('created_at', $orderCols)) $orderData['created_at'] = date('Y-m-d H:i:s');

            $db->beginTransaction();

            $colNames = implode(', ', array_keys($orderData));
            $colPlaceholders = ':' . implode(', :', array_keys($orderData));
            $stmtOrder = $db->prepare("INSERT INTO orders ($colNames) VALUES ($colPlaceholders)");
            $stmtOrder->execute($orderData);

            $orderId = (int)$db->lastInsertId();
            $itemCols = $db->query("DESCRIBE order_items")->fetchAll(PDO::FETCH_COLUMN);

            $stmtStock = $db->prepare("UPDATE products SET stock = GREATEST(0, stock - :qty) WHERE id = :product_id");

            foreach ($orderItems as $item) {
                $itemData = [];
                if (in_array('order_id', $itemCols)) $itemData['order_id'] = $orderId;
                if (in_array('product_id', $itemCols)) $itemData['product_id'] = $item['product_id'];
                if (in_array('price', $itemCols)) $itemData['price'] = $item['price'];
                if (in_array('qty', $itemCols)) $itemData['qty'] = $item['qty'];
                if (in_array('quantity', $itemCols)) $itemData['quantity'] = $item['qty'];
                if (in_array('subtotal', $itemCols)) $itemData['subtotal'] = $item['subtotal'];

                $itemColNames = implode(', ', array_keys($itemData));
                $itemPlaceholders = ':' . implode(', :', array_keys($itemData));
                $stmtItem = $db->prepare("INSERT INTO order_items ($itemColNames) VALUES ($itemPlaceholders)");
                $stmtItem->execute($itemData);

                $stmtStock->execute([
                    'qty'        => $item['qty'],
                    'product_id' => $item['product_id'],
                ]);
            }

            unset($_SESSION['cart']);
            $db->commit();

            $this->redirect('orders?success=1&code=' . urlencode($orderCode));
        } catch (Throwable $th) {
            if (isset($db) && $db instanceof PDO && $db->inTransaction()) {
                $db->rollBack();
            }
            die('Gagal memproses booking: ' . $th->getMessage());
        }
    }
}