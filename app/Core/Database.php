<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database — koneksi PDO singleton.
 * Sesuai CS-02: akses database wajib pakai PDO, bukan mysqli.
 * Sesuai CS-03: seluruh query lewat Model wajib prepared statement.
 */
class Database
{
    private static ?PDO $instance = null;

    public static function connection(): PDO
    {
        if (self::$instance === null) {
            $config = config('database');
            $appConfig = config('app');

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );

                // Samakan timezone SESI MySQL dengan timezone aplikasi.
                // Tanpa ini, CURRENT_TIMESTAMP di kolom created_at/updated_at
                // ikut timezone SERVER MySQL (sering default UTC), sementara
                // PHP sudah Asia/Jakarta — order_status_logs & stock_logs bisa
                // tercatat dengan jam yang tidak konsisten satu sama lain.
                $timezone = new \DateTimeZone($appConfig['timezone'] ?? 'Asia/Jakarta');
                $offsetSeconds = $timezone->getOffset(new \DateTime('now', $timezone));
                $sign = $offsetSeconds >= 0 ? '+' : '-';
                $absOffset = abs($offsetSeconds);
                $offsetString = sprintf('%s%02d:%02d', $sign, intdiv($absOffset, 3600), intdiv($absOffset % 3600, 60));
                self::$instance->exec("SET time_zone = '{$offsetString}'");
            } catch (PDOException $e) {
                // Di production, jangan tampilkan detail koneksi ke user (NFR-13/keamanan).
                if ($appConfig['debug']) {
                    die('Koneksi database gagal: ' . $e->getMessage());
                }
                die('Terjadi kesalahan pada server. Silakan coba lagi nanti.');
            }
        }

        return self::$instance;
    }
}
