<?php

namespace App\Controllers\Owner;

use App\Core\Auth;
use App\Core\Controller;
use PDO;
use Throwable;

class PaymentSettingController extends Controller
{
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

    public function index(): void
    {
        if (class_exists('\App\Core\Auth') && !Auth::check()) {
            $this->redirect('login');
            return;
        }

        $db = $this->getPdo();
        $methods = $db->query("SELECT * FROM payment_methods ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

        $this->view('owner.payments.index', [
            'title'   => 'Metode Pembayaran Toko — ' . config('app.name'),
            'methods' => $methods,
            'success' => $_GET['success'] ?? null
        ], 'layouts.admin');
    }

    public function toggle(): void
    {
        if (class_exists('\App\Core\Auth') && !Auth::check()) {
            $this->redirect('login');
            return;
        }

        $methodId = (int)$this->input('id');
        $isActive = (int)$this->input('is_active') === 1 ? 1 : 0;

        try {
            $db = $this->getPdo();
            $stmt = $db->prepare("UPDATE payment_methods SET is_active = :status WHERE id = :id");
            $stmt->execute(['status' => $isActive, 'id' => $methodId]);
        } catch (Throwable $th) {
            // Abaikan kesalahan penulisan ke database
        }

        $this->redirect('owner/payments?success=1');
    }
}