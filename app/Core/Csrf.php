<?php

namespace App\Core;

/**
 * Csrf — proteksi Cross-Site Request Forgery.
 */
class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function verify(?string $token): bool
    {
        // Pengecualian khusus rute logout agar tidak memicu 403
        $uri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
        if ($uri === 'logout' || str_ends_with($uri, '/logout')) {
            return true;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($token) || empty($_SESSION[self::SESSION_KEY])) {
            return false;
        }

        return hash_equals($_SESSION[self::SESSION_KEY], $token);
    }

    public static function field(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');
        return "<input type=\"hidden\" name=\"_csrf_token\" value=\"{$token}\">";
    }
}