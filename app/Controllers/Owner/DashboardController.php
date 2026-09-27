<?php

namespace App\Controllers\Owner;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\ProductModel;
use PDO;
use Throwable;

class DashboardController extends Controller
{
    private function getPdo(): PDO
    {
        $productModel = class_exists('\App\Models\ProductModel') ? new ProductModel() : null;
        if ($productModel) {
            $ref = new \ReflectionClass($productModel);
            if ($ref->hasProperty('db')) {
                $prop =$ref->getProperty('db');
                $prop->setAccessible(true);$db = $prop->getValue($productModel);
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

        $host   = function_exists('config') ? config('database.host', '127.0.0.1') : '127.0.0.1';
        $dbName = function_exists('config') ? config('database.database', 'lau_store') : 'lau_store';
        $user   = function_exists('config') ? config('database.username', 'root') : 'root';
        $pass   = function_exists('config') ? config('database.password', '') : '';

        $pdo = new PDO(
            "mysql:host={$host};dbname={$dbName};charset=utf8mb4",
            $user,$pass
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    public function index(): void
    {
        $db = $this->getPdo();$waSettingFile = dirname(__DIR__, 3) . '/storage/wa_setting.txt';

        // 1. Simpan Nomor WhatsApp Baru
        $action = trim((string)($this->input('action') ?? $_GET['action'] ?? $_POST['action'] ?? ''));
        if ($action === 'update_whatsapp') {
            $newWa = trim((string)($this->input('shop_whatsapp') ?? $_GET['shop_whatsapp'] ?? $_POST['shop_whatsapp'] ?? ''));
            if (!empty($newWa)) {
                // A. Simpan ke database
                try {
                    $db->exec("
                        CREATE TABLE IF NOT EXISTS system_settings (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            `key` VARCHAR(100) NOT NULL UNIQUE,
                            `value` TEXT NULL,
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                    ");

                    $stmt =$db->prepare("
                        INSERT INTO system_settings (`key`, `value`) 
                        VALUES ('shop_whatsapp', :val) 
                        ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)
                    ");
                    $stmt->execute(['val' =>$newWa]);
                } catch (Throwable $e) {
                }

                // B. Simpan cadangan ke file lokal agar selalu sinkron
                try {
                    if (!is_dir(dirname($waSettingFile))) {
                        mkdir(dirname($waSettingFile), 0777, true);
                    }
                    file_put_contents($waSettingFile,$newWa);
                } catch (Throwable $e) {
                }
            }

            $this->redirect('owner/dashboard?msg=wa_updated');
            return;
        }

        // 2. Baca Nomor WhatsApp Saat Ini
        $shopWhatsapp = '085272270131';

        // Cek file lokal terlebih dahulu
        if (file_exists($waSettingFile)) {
            $savedFileWa = trim((string)file_get_contents($waSettingFile));
            if (!empty($savedFileWa)) {
                $shopWhatsapp =$savedFileWa;
            }
        }

        // Cek database
        try {
            $stmtWa =$db->prepare("SELECT `value` FROM system_settings WHERE `key` = 'shop_whatsapp' LIMIT 1");
            $stmtWa->execute();
            $valWa =$stmtWa->fetchColumn();
            if (!empty($valWa)) {
                $shopWhatsapp = trim((string)$valWa);
            }
        } catch (Throwable $th) {
        }

        $summary = [             ['label' => 'Pendapatan Hari Ini', 'value' => 'Rp 0', 'icon' => 'ti-cash'],             ['label' => 'Keuntungan',          'value' => 'Rp 0', 'icon' => 'ti-trending-up'],             ['label' => 'Booking Hari Ini',    'value' => '0',    'icon' => 'ti-calendar-check'],             ['label' => 'Produk Aktif',        'value' => '0',    'icon' => 'ti-package'],         ];$lowStock       = [];
        $recentBookings = [];$recentActivity = [];
        $salesChart     = [];$soldItemsToday = [];

        try {
            $todayDate = date('Y-m-d');

            // Hitung Pendapatan Hari Ini
            $stmtRev =$db->prepare("
                SELECT IFNULL(SUM(COALESCE(total_amount, total_price, 0)), 0) 
                FROM orders 
                WHERE DATE(created_at) = :today 
                  AND LOWER(status) NOT IN ('dibatalkan', 'cancelled', 'batal')
            ");
            $stmtRev->execute(['today' =>$todayDate]);
            $todayRevenue = (float)$stmtRev->fetchColumn();

            // Hitung Booking Hari Ini
            $stmtBooking =$db->prepare("
                SELECT COUNT(*) 
                FROM orders 
                WHERE DATE(created_at) = :today
            ");
            $stmtBooking->execute(['today' =>$todayDate]);
            $todayBookingCount = (int)$stmtBooking->fetchColumn();

            // Hitung Produk Aktif
            $activeProductsCount = (int)$db->query("SELECT COUNT(*) FROM products")->fetchColumn();

            // Estimasi Keuntungan
            $estimatedProfit =$todayRevenue * 0.3;

            $summary = [
                ['label' => 'Pendapatan Hari Ini', 'value' => 'Rp ' . number_format($todayRevenue, 0, ',', '.'), 'icon' => 'ti-cash'],
                ['label' => 'Keuntungan',          'value' => 'Rp ' . number_format($estimatedProfit, 0, ',', '.'), 'icon' => 'ti-trending-up'],
                ['label' => 'Booking Hari Ini',    'value' => (string)$todayBookingCount, 'icon' => 'ti-calendar-check'],
                ['label' => 'Produk Aktif',        'value' => (string)$activeProductsCount, 'icon' => 'ti-package'],
            ];

            // Query Stok Menipis
            $stmtLow =$db->query("
                SELECT name, stock 
                FROM products 
                WHERE stock <= 5 
                ORDER BY stock ASC 
                LIMIT 5
            ");
            $lowStock =$stmtLow->fetchAll(PDO::FETCH_ASSOC);

            // Query Barang Terjual Hari Ini
            $stmtSold =$db->prepare("
                SELECT 
                    p.name,
                    COALESCE(c.name, 'ATK') AS category_name,
                    SUM(oi.quantity) AS total_sold,
                    SUM(oi.quantity * COALESCE(oi.price, p.price)) AS subtotal
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                JOIN products p ON oi.product_id = p.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE DATE(o.created_at) = :today
                  AND LOWER(o.status) NOT IN ('dibatalkan', 'cancelled', 'batal')
                GROUP BY p.id, p.name, c.name
                ORDER BY total_sold DESC
                LIMIT 6
            ");
            $stmtSold->execute(['today' =>$todayDate]);
            $soldItemsToday =$stmtSold->fetchAll(PDO::FETCH_ASSOC);

            // Query 5 Booking Terakhir
            $cols =$db->query("DESCRIBE orders")->fetchAll(PDO::FETCH_COLUMN);
            $custCol = in_array('customer_name',$cols)
                ? "COALESCE(u.name, o.customer_name, 'Pelanggan Toko')"
                : "COALESCE(u.name, 'Pelanggan Toko')";

            $orderClause = in_array('updated_at',$cols) ? "ORDER BY o.updated_at DESC, o.id DESC" : "ORDER BY o.id DESC";

            $sqlRecent = "
                SELECT 
                    o.id,
                    CONCAT('BK-', o.id) AS order_code,
                    o.status,
                    {$custCol} AS customer_name
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                {$orderClause}
                LIMIT 5
            ";
            $rawRecent = $db->query($sqlRecent)->fetchAll(PDO::FETCH_ASSOC);

            $statusLabelMap = [
                'menunggu'     => 'Menunggu',
                'pending'      => 'Menunggu',
                'diproses'     => 'Diproses',
                'processing'   => 'Diproses',
                'siap'         => 'Siap Diambil',
                'ready'        => 'Siap Diambil',
                'siap_diambil' => 'Siap Diambil',
                'selesai'      => 'Selesai',
                'completed'    => 'Selesai',
                'dibatalkan'   => 'Dibatalkan',
                'cancelled'    => 'Dibatalkan',
                'batal'        => 'Dibatalkan',
            ];

            foreach ($rawRecent as $rb) {$st = strtolower(trim((string)($rb['status'] ?? 'menunggu')));$recentBookings[] = [
                    'id'         => $rb['id'],
                    'code'       => $rb['order_code'],
                    'customer'   => $rb['customer_name'],
                    'status'     => $statusLabelMap[$st] ?? (empty($st) ? 'Menunggu' : ucfirst($st)),
                    'raw_status' => $st
                ];
            }

            // Grafik Penjualan 6 Bulan
            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            for ($i = 5; $i >= 0; $i--) {
                $mTime = strtotime("-$i month");
                $mNum  = (int)date('n', $mTime);$mYear = date('Y', $mTime);$mLabel = $monthNames[$mNum - 1];

                $stmtM =$db->prepare("
                    SELECT IFNULL(SUM(COALESCE(total_amount, total_price, 0)), 0) 
                    FROM orders 
                    WHERE MONTH(created_at) = :m AND YEAR(created_at) = :y 
                      AND LOWER(status) NOT IN ('dibatalkan', 'cancelled', 'batal')
                ");
                $stmtM->execute(['m' => $mNum, 'y' =>$mYear]);
                $mTotal = (float)$stmtM->fetchColumn();

                $calcHeight = min(100, max(8, ($mTotal / 40000000) * 100));

                $salesChart[] = [
                    'label'  => $mLabel,
                    'height' => (int)$calcHeight,
                ];
            }

        } catch (Throwable $th) {
        }

        $this->view('owner.dashboard', [
            'title'          => 'Dashboard Owner — ' . (function_exists('config') ? config('app.name', 'UD. LAU STORE') : 'UD. LAU STORE'),
            'user'           => Auth::user(),
            'today'          => date('d F Y'),
            'summary'        => $summary,
            'lowStock'       => $lowStock,
            'soldItemsToday' => $soldItemsToday,
            'recentBookings' => $recentBookings,
            'salesChart'     => $salesChart,
            'shopWhatsapp'   => $shopWhatsapp,
        ], 'layouts.admin');
    }
}