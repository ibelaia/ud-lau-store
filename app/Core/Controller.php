<?php

namespace App\Core;

/**
 * Controller — kelas dasar untuk seluruh Controller.
 * Sesuai CS-04: Controller TIDAK boleh berisi query SQL langsung;
 * seluruh akses data wajib lewat Model.
 */
abstract class Controller
{
    protected Request $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    /**
     * Render view dengan layout utama.
     * $view menggunakan notasi titik, contoh: 'products.index'
     * akan me-load app/Views/products/index.php
     */
    protected function view(string $view, array $data = [], string $layout = 'layouts.main'): void
    {
        extract($data);

        $viewPath = $this->resolveViewPath($view);
        if (!file_exists($viewPath)) {
            http_response_code(500);
            die("View tidak ditemukan: {$view}");
        }

        // Render konten view ke dalam buffer, lalu suntikkan ke layout
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        $layoutPath = $this->resolveViewPath($layout);
        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }

    /**
     * Render view TANPA layout (contoh: halaman login, partial AJAX).
     */
    protected function viewOnly(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = $this->resolveViewPath($view);
        if (!file_exists($viewPath)) {
            http_response_code(500);
            die("View tidak ditemukan: {$view}");
        }
        require $viewPath;
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . base_url($path));
        exit;
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $this->request->input($key, $default);
    }

    private function resolveViewPath(string $view): string
    {
        $relativePath = str_replace('.', '/', $view) . '.php';
        return dirname(__DIR__) . '/Views/' . $relativePath;
    }
}
