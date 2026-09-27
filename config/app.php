<?php

use App\Core\Env;

return [
    'name'    => Env::get('APP_NAME', 'UD. LAU Store'),
    'env'     => Env::get('APP_ENV', 'local'),
    // 'url' di sini HANYA dipakai oleh base_url() saat konteks CLI
    // (cron/backup/worker). Untuk request web, base URL dideteksi
    // otomatis dari $_SERVER — lihat app/Helpers/functions.php.
    'url'     => Env::get('APP_URL', 'http://localhost:8000'),
    'key'     => Env::get('APP_KEY', ''),
    // APP_DEBUG variabel TERPISAH dari APP_ENV — sengaja, supaya bisa
    // debug di staging tanpa perlu APP_ENV=local, dan supaya production
    // TIDAK PERNAH menampilkan detail error ke user kecuali eksplisit
    // diaktifkan (defense in depth).
    'debug'   => filter_var(Env::get('APP_DEBUG', 'true'), FILTER_VALIDATE_BOOLEAN),
    // Dipakai bootstrap.php (date_default_timezone_set) dan Database.php
    // (SET time_zone sesi MySQL) — supaya created_at/updated_at,
    // order_status_logs, stock_logs semuanya konsisten WIB.
    'timezone' => Env::get('APP_TIMEZONE', 'Asia/Jakarta'),

    'session' => [
        'name'     => Env::get('SESSION_NAME', 'lau_store_session'),
        'lifetime' => (int) Env::get('SESSION_LIFETIME', 7200),
    ],

    'upload' => [
        // Sesuai BR-45: ukuran maksimal foto produk 3 MB
        'max_size'      => (int) Env::get('UPLOAD_MAX_SIZE', 3145728),
        'allowed_types' => ['image/jpeg', 'image/png', 'image/webp'], // BR-44
    ],
];
