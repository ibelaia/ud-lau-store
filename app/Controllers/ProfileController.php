<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\UserModel;
use PDO;
use Throwable;

class ProfileController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
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

    public function edit(): void
    {
        if (class_exists('\App\Core\Auth') && !Auth::check()) {
            $this->redirect('login');
            return;
        }

        $userId = 0;
        if (class_exists('\App\Core\Auth') && Auth::id()) {
            $userId = (int)Auth::id();
        } elseif (isset($_SESSION['user']['id'])) {
            $userId = (int)$_SESSION['user']['id'];
        } elseif (isset($_SESSION['user_id'])) {
            $userId = (int)$_SESSION['user_id'];
        }

        if ($userId <= 0) {
            $this->redirect('login');
            return;
        }

        $userData = null;
        try {
            $db = $this->getPdo();
            $stmt = $db->prepare("
                SELECT u.*, r.name AS role_name 
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE u.id = :id 
                LIMIT 1
            ");
            $stmt->execute(['id' => $userId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $th) {
            $userData = $this->userModel->find($userId);
        }

        if (!$userData) {
            $this->redirect('login');
            return;
        }

        // Deteksi hak akses akun secara akurat
        $roleName = strtolower(trim((string)($userData['role_name'] ?? $userData['role'] ?? '')));
        $email = strtolower(trim((string)($userData['email'] ?? '')));
        $isOwner = ($roleName === 'owner' || $roleName === 'admin' || str_contains($email, 'owner@'));

        // Jika Owner yang login: gunakan layouts.admin agar navbar tetap navbar Owner (ada tombol Tambah + Dashboard)
        // Jika Customer yang login: gunakan layouts.main
        $layout = $isOwner ? 'layouts.admin' : 'layouts.main';

        $this->view('customer.profile', [
            'title'   => 'Profil ' . ($isOwner ? 'Owner' : 'Saya') . ' — ' . config('app.name'),
            'user'    => $userData,
            'isOwner' => $isOwner,
        ], $layout);
    }

    public function update(): void
    {
        if (class_exists('\App\Core\Auth') && !Auth::check()) {
            $this->redirect('login');
            return;
        }

        $userId = class_exists('\App\Core\Auth') ? (int)Auth::id() : 0;
        if ($userId === 0 && isset($_SESSION['user']['id'])) {
            $userId = (int)$_SESSION['user']['id'];
        }

        $name = trim((string) $this->input('name'));
        $phone = trim((string) $this->input('phone', ''));

        if (!empty($name) && $userId > 0) {
            $updateData = ['name' => $name];
            if (!empty($phone)) {
                $updateData['phone'] = $phone;
            }

            try {
                $this->userModel->update($userId, $updateData);
            } catch (Throwable $th) {
                $db = $this->getPdo();
                $db->prepare("UPDATE users SET name = :n WHERE id = :id")->execute(['n' => $name, 'id' => $userId]);
            }

            if (isset($_SESSION['user'])) {
                $_SESSION['user']['name'] = $name;
            }
        }

        $this->redirect('profile');
    }
}