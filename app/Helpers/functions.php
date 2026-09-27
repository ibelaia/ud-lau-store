<?php

/**
 * functions.php — helper global environment-agnostic.
 *
 * Prinsip: "Environment Ready", bukan "Localhost First". Seluruh URL
 * (aset, upload, redirect) dibentuk otomatis dari request yang masuk,
 * BUKAN dari APP_URL yang di-hardcode — supaya kode yang sama berjalan
 * tanpa perubahan di:
 *   - Local        (http://localhost / http://lau-store.test)
 *   - Ngrok        (https://xxxxx.ngrok-free.app)
 *   - Hosting      (https://udlaustore.com)
 *
 * PRASYARAT PENTING: document root web server (Local via virtual host,
 * Ngrok via tunnel ke virtual host yang sama, dan Hosting via konfigurasi
 * domain) HARUS selalu mengarah ke folder /public — bukan ke root project.
 * Dengan begitu, aplikasi tidak pernah butuh prefix folder ("/lau-store")
 * di URL manapun, di environment manapun.
 */

if (!function_exists('config')) {
    /**
     * Ambil nilai konfigurasi dengan notasi titik, contoh: config('app.debug').
     * File config di-cache per request agar tidak require berulang kali.
     */
    function config(string $key, mixed $default = null): mixed
    {
        static $cache = [];

        [$file, $property] = array_pad(explode('.', $key, 2), 2, null);

        if (!isset($cache[$file])) {
            $path = BASE_PATH . "/config/{$file}.php";
            $cache[$file] = file_exists($path) ? require $path : [];
        }

        if ($property === null) {
            return $cache[$file];
        }

        return $cache[$file][$property] ?? $default;
    }
}

if (!function_exists('is_https')) {
    /**
     * Deteksi apakah request saat ini HTTPS — termasuk saat di belakang
     * proxy (Ngrok, load balancer hosting) yang meneruskan lewat
     * X-Forwarded-Proto. Dipakai untuk flag 'secure' pada cookie sesi.
     */
    function is_https(): bool
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }

        return !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https';
    }
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        static $cached = null;

        if ($cached === null) {
            // Konteks CLI (cron job, script backup, worker) — $_SERVER tidak
            // tersedia, WAJIB pakai APP_URL dari .env sebagai fallback.
            if (PHP_SAPI === 'cli' || empty($_SERVER['HTTP_HOST'])) {
                $cached = rtrim(\App\Core\Env::get('APP_URL', 'http://localhost'), '/');
            } else {
                $protocol = 'http';

                if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                    $protocol = 'https';
                } elseif (
                    !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
                    strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https'
                ) {
                    // PENTING untuk Ngrok & kebanyakan hosting/load balancer:
                    // TLS di-terminate di proxy, permintaan diteruskan ke server
                    // sebagai HTTP biasa. $_SERVER['HTTPS'] TIDAK ke-set, tapi
                    // header X-Forwarded-Proto tetap menandakan permintaan asli
                    // memakai HTTPS. Tanpa pengecekan ini, asset/redirect akan
                    // salah dibuat sebagai http:// meski halaman diakses https://
                    // (mixed content) — ini persis kelas bug yang ingin dihindari.
                    $protocol = 'https';
                }

                $host = $_SERVER['HTTP_HOST'];
                $cached = "{$protocol}://{$host}";
            }
        }

        $path = ltrim($path, '/');
        return $path === '' ? $cached : "{$cached}/{$path}";
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('upload_url')) {
    /**
     * Membentuk URL lengkap dari path relatif yang disimpan di database.
     * Contoh: DB menyimpan "products/pensil.jpg", bukan URL lengkap —
     * supaya tidak perlu migrasi data saat pindah environment/domain.
     *
     * File fisik disimpan di storage/uploads/, tetapi diakses browser
     * lewat public/storage/ (symlink) — supaya folder upload TIDAK
     * bercampur dengan source code. Lihat README bagian "Setup Symlink".
     *
     * PENGECUALIAN: file backup database (storage/backups/) SENGAJA
     * TIDAK memakai helper ini / TIDAK di-symlink ke public — backup
     * berisi seluruh isi database dan tidak boleh bisa diakses lewat URL
     * publik. Download backup hanya lewat route khusus dengan middleware
     * ProgrammerMiddleware (lihat Tahap 9 — Programmer Module).
     */
    function upload_url(string $relativePath): string
    {
        return base_url('storage/' . ltrim($relativePath, '/'));
    }
}

if (!function_exists('csrf_field')) {
    /** Cetak <input type="hidden"> token CSRF — wajib disertakan di setiap <form method="POST">. */
    function csrf_field(): string
    {
        return \App\Core\Csrf::field();
    }
}

if (!function_exists('e')) {
    /** Shortcut escape output di View, mencegah XSS. */
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}
