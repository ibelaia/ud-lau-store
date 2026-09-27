<?php

namespace App\Middleware;

use App\Core\Auth;

class OwnerMiddleware
{
    public function handle(): bool
    {
        // 1. Wajib sudah login
        if (!Auth::check()) {
            header('Location: ' . base_url('login'));
            exit;
        }

        // 2. Cek apakah rolenya adalah 'owner'
        $role = strtolower((string) Auth::role());
        if ($role !== 'owner') {
            http_response_code(403);
            require BASE_PATH . '/app/Views/errors/403.php';
            exit;
        }

        return true;
    }
}