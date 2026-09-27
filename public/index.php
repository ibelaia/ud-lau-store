<?php

require dirname(__DIR__) . '/app/Core/bootstrap.php';

/** @var \App\Core\Router $router */
$router = require dirname(__DIR__) . '/routes/web.php';

$router->dispatch(
    $_SERVER['REQUEST_URI'] ?? '/',
    $_SERVER['REQUEST_METHOD'] ?? 'GET'
);
