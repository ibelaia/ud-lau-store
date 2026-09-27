<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Middleware;

/**
 * AuthMiddleware — memastikan pengguna sudah login, apapun rolenya.
 * Dipakai untuk route yang butuh login tapi tidak spesifik ke satu role,
 * misal: checkout, riwayat pesanan, edit profil (FR-012, FR-015, FR-016).
 */
class AuthMiddleware extends Middleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            header('Location: ' . base_url('login'));
            exit;
        }
    }
}
