<?php

namespace App\Controllers\Programmer;

use App\Core\Auth;
use App\Core\Controller;

/**
 * Dashboard Programmer — layout sama dengan Owner (konsisten visual),
 * isi beda: informasi TEKNIS sistem, bukan angka bisnis (Programmer
 * tidak perlu lihat keuntungan/penjualan).
 *
 * SENGAJA tidak ada logic apa pun (tidak ada cek DB/server sungguhan) —
 * murni array statis untuk kebutuhan tampilan (Opsi A: v1 sederhana).
 */
class DashboardController extends Controller
{
    public function index(): void
    {
        $systemStatus = [
            ['label' => 'Database',   'value' => 'Terhubung', 'icon' => 'ti-database', 'ok' => true],
            ['label' => 'Server',     'value' => 'Online',    'icon' => 'ti-server',   'ok' => true],
            ['label' => 'Storage',    'value' => '2.1 GB',    'icon' => 'ti-cloud',    'ok' => true],
            ['label' => 'Total User', 'value' => '24',        'icon' => 'ti-users',    'ok' => true],
        ];

        $recentActivity = [
            ['text' => 'Owner login', 'time' => '09.02'],
            ['text' => 'Owner mengubah harga produk', 'time' => '09.15'],
            ['text' => 'Backup manual dijalankan', 'time' => '08.00'],
        ];

        $this->view('programmer.dashboard', [
            'title'          => 'Dashboard Programmer — ' . config('app.name'),
            'user'           => Auth::user(),
            'today'          => date('d F Y'),
            'systemStatus'   => $systemStatus,
            'recentActivity' => $recentActivity,
            'appVersion'     => 'v1.0.0-auth',
        ], 'layouts.admin-programmer');
    }
}
