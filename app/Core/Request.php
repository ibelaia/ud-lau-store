<?php

namespace App\Core;

/**
 * Request — pembungkus akses input HTTP ($_GET, $_POST, $_FILES).
 * Controller tidak lagi mengakses superglobal secara langsung.
 */
class Request
{
    public function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    public function only(array $keys): array
    {
        return array_intersect_key($this->all(), array_flip($keys));
    }

    public function has(string $key): bool
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    public function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    public function hasFile(string $key): bool
    {
        return isset($_FILES[$key]) && $_FILES[$key]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    public function method(): string
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        return $method;
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public function userAgent(): ?string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }
}
