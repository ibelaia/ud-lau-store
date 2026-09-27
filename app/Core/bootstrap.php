<?php

/**
 * bootstrap.php — titik masuk inisialisasi aplikasi.
 * Dipanggil sekali dari public/index.php sebelum routing dijalankan.
 */

define('BASE_PATH', dirname(__DIR__, 2));

// ------------------------------------------------------------
// Autoloader sederhana (PSR-4 manual, tanpa composer)
// Memetakan namespace App\ ke folder app/
// ------------------------------------------------------------
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// ------------------------------------------------------------
// Load .env
// ------------------------------------------------------------
use App\Core\Env;

Env::load(BASE_PATH . '/.env');

// ------------------------------------------------------------
// Helper global (base_url, asset, upload_url, e)
// Environment-agnostic: mendeteksi protokol & host dari request,
// bukan dari APP_URL yang di-hardcode. Lihat komentar di file ini
// untuk penjelasan penanganan Ngrok (X-Forwarded-Proto).
// ------------------------------------------------------------
require BASE_PATH . '/app/Helpers/functions.php';

// ------------------------------------------------------------
// Konfigurasi error reporting sesuai environment (NFR-13 semangat keamanan:
// jangan bocorkan detail error ke user di production)
// ------------------------------------------------------------
$appConfig = config('app');

if ($appConfig['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// ------------------------------------------------------------
// Timezone — supaya created_at/updated_at, order_status_logs, stock_logs
// semuanya konsisten WIB (Asia/Jakarta), bukan default server (sering UTC).
// ------------------------------------------------------------
date_default_timezone_set($appConfig['timezone']);

// ------------------------------------------------------------
// Session (NFR-12: batas waktu otomatis logout setelah tidak aktif)
// ------------------------------------------------------------
session_name($appConfig['session']['name']);
session_set_cookie_params([
    'lifetime' => $appConfig['session']['lifetime'],
    'path'     => '/',
    'httponly' => true,       // Cegah akses cookie sesi lewat JavaScript (mitigasi XSS)
    'samesite' => 'Lax',
    // 'secure' otomatis true saat HTTPS (termasuk Ngrok lewat X-Forwarded-Proto)
    // supaya cookie sesi tidak pernah dikirim polos lewat koneksi tidak terenkripsi.
    // Sengaja TIDAK true saat Local http://localhost, karena browser akan
    // menolak cookie 'secure' di koneksi non-HTTPS (login jadi tidak bisa jalan).
    'secure'   => is_https(),
]);
session_start();

// Auto-logout jika sesi tidak aktif melebihi batas waktu
if (isset($_SESSION['last_activity']) &&
    (time() - $_SESSION['last_activity']) > $appConfig['session']['lifetime']
) {
    $_SESSION = [];
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();
