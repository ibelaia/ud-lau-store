<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Middleware;

/**
 * ProgrammerMiddleware — hanya boleh diakses akun dengan role 'programmer'.
 * Sesuai Rule 1 & Rule 2 Entity Ownership Matrix: Programmer non-operational
 * (tidak menyentuh produk/pesanan), Owner non-technical (tidak menyentuh
 * maintenance/backup/restore).
 */
class ProgrammerMiddleware extends Middleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            header('Location: ' . base_url('login'));
            exit;
        }

        if (!Auth::is('programmer')) {
            http_response_code(403);
            require dirname(__DIR__) . '/Views/errors/403.php';
            exit;
        }
    }
}
