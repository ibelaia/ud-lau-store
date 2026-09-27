<?php

use App\Core\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\OwnerMiddleware;
use App\Middleware\ProgrammerMiddleware;

$router = new Router();

// ------------------------------------------------------------
// Publik — tidak perlu login
// ------------------------------------------------------------
$router->get('/', 'HomeController@index');
$router->get('/products', 'ProductController@index');
$router->get('/products/{slug}', 'ProductController@show');
$router->get('/categories/{slug}', 'CategoryController@show');
$router->get('/cart', 'CartController@index');
$router->post('/cart/add', 'CartController@add');
$router->post('/cart/remove', 'CartController@remove');
$router->post('/cart/remove/{id}', 'CartController@remove');
$router->get('/cart/remove/{id}', 'CartController@remove');

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');

// Pemulihan Kata Sandi (Forgot / Reset Password)
$router->get('/forgot-password', 'AuthController@showForgotPassword');
$router->post('/forgot-password', 'AuthController@sendResetLink');
$router->get('/reset-password', 'AuthController@showResetPassword');
$router->post('/reset-password', 'AuthController@resetPassword');

// Logout mendukung GET dan POST agar tidak terganjal CSRF 403
$router->post('/logout', 'AuthController@logout');
$router->get('/logout', 'AuthController@logout');

// ------------------------------------------------------------
// Wajib login (Customer / Global)
// ------------------------------------------------------------
$router->get('/checkout', 'CheckoutController@index', [AuthMiddleware::class]);
$router->post('/checkout', 'CheckoutController@store', [AuthMiddleware::class]);
$router->get('/checkout/instant', 'CheckoutController@instant', [AuthMiddleware::class]);
$router->post('/checkout/instant', 'CheckoutController@storeInstant', [AuthMiddleware::class]);

// Pesanan & Polling Real-time Status Customer
$router->get('/orders', 'OrderController@index', [AuthMiddleware::class]);
$router->get('/orders/check-statuses', 'OrderController@checkStatuses', [AuthMiddleware::class]);
$router->get('/orders/{id}', 'OrderController@show', [AuthMiddleware::class]);
$router->get('/orders/([0-9]+)', 'OrderController@show', [AuthMiddleware::class]);

$router->get('/wishlist', 'WishlistController@index', [AuthMiddleware::class]);
$router->post('/wishlist/toggle', 'WishlistController@toggle', [AuthMiddleware::class]);
$router->get('/profile', 'ProfileController@edit', [AuthMiddleware::class]);
$router->put('/profile', 'ProfileController@update', [AuthMiddleware::class]);
$router->post('/profile', 'ProfileController@update', [AuthMiddleware::class]);
$router->get('/customer/dashboard', 'Customer\\DashboardController@index', [AuthMiddleware::class]);

// Endpoint Chat & Cek Unread Real-time
$router->get('/chat', 'ChatController@index', [AuthMiddleware::class]);
$router->post('/chat/send', 'ChatController@send', [AuthMiddleware::class]);
$router->get('/chat/unread-count', 'ChatController@unreadCount', [AuthMiddleware::class]);

// ------------------------------------------------------------
// Khusus Owner
// ------------------------------------------------------------
$router->get('/owner/dashboard', 'Owner\\DashboardController@index', [OwnerMiddleware::class]);
$router->get('/owner/products', 'Owner\\ProductController@index', [OwnerMiddleware::class]);
$router->get('/owner/products/create', 'Owner\\ProductController@create', [OwnerMiddleware::class]);
$router->post('/owner/products', 'Owner\\ProductController@store', [OwnerMiddleware::class]);
$router->get('/owner/products/{id}/edit', 'Owner\\ProductController@edit', [OwnerMiddleware::class]);
$router->put('/owner/products/{id}', 'Owner\\ProductController@update', [OwnerMiddleware::class]);
$router->delete('/owner/products/{id}', 'Owner\\ProductController@destroy', [OwnerMiddleware::class]);
$router->post('/owner/products/import', 'Owner\\ProductImportController@store', [OwnerMiddleware::class]);

$router->get('/owner/categories', 'Owner\\CategoryController@index', [OwnerMiddleware::class]);

// Pengaturan Metode Pembayaran Dinamis Toko
$router->get('/owner/payments', 'Owner\\PaymentSettingController@index', [OwnerMiddleware::class]);
$router->post('/owner/payments/toggle', 'Owner\\PaymentSettingController@toggle', [OwnerMiddleware::class]);

// Orders Owner
$router->get('/owner/orders', 'Owner\\OrderController@index', [OwnerMiddleware::class]);
$router->get('/owner/orders/update-status', 'Owner\\OrderController@updateStatus', [OwnerMiddleware::class]);
$router->get('/owner/orders/{id}', 'Owner\\OrderController@show', [OwnerMiddleware::class]);
$router->get('/owner/orders/([0-9]+)', 'Owner\\OrderController@show', [OwnerMiddleware::class]);
$router->post('/owner/orders/{id}/verify-payment', 'Owner\\OrderController@verifyPayment', [OwnerMiddleware::class]);
$router->post('/owner/orders/{id}/status', 'Owner\\OrderController@updateStatus', [OwnerMiddleware::class]);
$router->post('/owner/orders/([0-9]+)/status', 'Owner\\OrderController@updateStatus', [OwnerMiddleware::class]);

$router->get('/owner/banners', 'Owner\\BannerController@index', [OwnerMiddleware::class]);
$router->get('/owner/contacts', 'Owner\\ContactController@index', [OwnerMiddleware::class]);
$router->get('/owner/payment-methods', 'Owner\\PaymentMethodController@index', [OwnerMiddleware::class]);
$router->get('/owner/reports', 'Owner\\ReportController@index', [OwnerMiddleware::class]);
$router->get('/owner/notifications', 'Owner\\NotificationController@index', [OwnerMiddleware::class]);

// Owner Chat Room & Inbox
$router->get('/owner/chat', 'OwnerChatController@index', [OwnerMiddleware::class]);
$router->get('/owner/chat/{id}', 'OwnerChatController@detail', [OwnerMiddleware::class]);
$router->get('/owner/chat/([0-9]+)', 'OwnerChatController@detail', [OwnerMiddleware::class]);
$router->post('/owner/chat/reply', 'OwnerChatController@reply', [OwnerMiddleware::class]);

// ------------------------------------------------------------
// Khusus Programmer
// ------------------------------------------------------------
$router->get('/programmer/dashboard', 'Programmer\\DashboardController@index', [ProgrammerMiddleware::class]);
$router->post('/programmer/maintenance', 'Programmer\\MaintenanceController@toggle', [ProgrammerMiddleware::class]);
$router->post('/programmer/backup', 'Programmer\\BackupController@store', [ProgrammerMiddleware::class]);
$router->post('/programmer/restore', 'Programmer\\BackupController@restore', [ProgrammerMiddleware::class]);
$router->get('/programmer/activity-logs', 'Programmer\\ActivityLogController@index', [ProgrammerMiddleware::class]);
$router->get('/programmer/settings', 'Programmer\\SettingController@index', [ProgrammerMiddleware::class]);

return $router;