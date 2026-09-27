<?php

namespace App\Core;

class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function login(array $userData): void
    {
        $_SESSION['user'] = [
            'id'   => $userData['id'],
            'name' => $userData['name'],
            'role' => strtolower((string) $userData['role']),
        ];
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
    }
}