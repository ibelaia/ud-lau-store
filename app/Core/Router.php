<?php

namespace App\Core;

/**
 * Router — pemetaan URL ke Controller@method.
 */
class Router
{
    private array $routes = [
        'GET'    => [],
        'POST'   => [],
        'PUT'    => [],
        'DELETE' => [],
    ];

    public function get(string $path, string $action, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $action, $middleware);
    }

    public function post(string $path, string $action, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $action, $middleware);
    }

    public function put(string $path, string $action, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $action, $middleware);
    }

    public function delete(string $path, string $action, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $action, $middleware);
    }

    private function addRoute(string $method, string $path, string $action, array $middleware): void
    {
        $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', trim($path, '/'));
        $pattern = '#^' . $pattern . '$#';

        $this->routes[$method][] = [
            'pattern'    => $pattern,
            'action'     => $action,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $uri, string $method): void
    {
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $cleanUri = trim(parse_url($uri, PHP_URL_PATH), '/');

        // Verifikasi CSRF untuk method POST/PUT/DELETE
        if (in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
            $excludedRoutes = [
                'login',
                'register',
                'cart/add',
                'cart/remove',
                'wishlist/toggle',
                'chat/send',
                'owner/chat/reply',
            ];

            if (!in_array($cleanUri, $excludedRoutes, true)) {
                $token = $_POST['_csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
                if (!Csrf::verify($token)) {
                    $this->abort(403, 'Penyebab 403: Token keamanan (CSRF) tidak valid atau kedaluwarsa.');
                    return;
                }
            }
        }

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $cleanUri, $matches)) {
                array_shift($matches);

                foreach ($route['middleware'] as $middlewareClass) {
                    $middlewareInstance = new $middlewareClass();
                    $middlewareInstance->handle();
                }

                [$controllerName, $methodName] = explode('@', $route['action']);
                $controllerClass = "App\\Controllers\\{$controllerName}";

                if (!class_exists($controllerClass)) {
                    $this->abort(500, "Controller {$controllerClass} tidak ditemukan.");
                }

                $controller = new $controllerClass();

                if (!method_exists($controller, $methodName)) {
                    $this->abort(500, "Method {$methodName} tidak ditemukan di {$controllerClass}.");
                }

                call_user_func_array([$controller, $methodName], $matches);
                return;
            }
        }

        $this->abort(404, 'Halaman tidak ditemukan.');
    }

    private function abort(int $statusCode, string $message): void
    {
        http_response_code($statusCode);
        echo "<div style='font-family: sans-serif; padding: 20px; background: #fff3cd; border: 2px solid #ffeeba; color: #856404; margin: 20px; border-radius: 8px;'>";
        echo "<h2>Status {$statusCode}</h2>";
        echo "<p><strong>Penyebab:</strong> {$message}</p>";
        echo "</div>";
        exit;
    }
}