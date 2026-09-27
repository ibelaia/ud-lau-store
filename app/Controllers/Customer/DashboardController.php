<?php

namespace App\Controllers\Customer;

use App\Core\Auth;
use App\Core\Controller;

/**
 * Stub — layout Dashboard Customer, data dummy (Sprint UI, belum tersambung
 * OrderModel karena belum ada). Tidak ada query/logic apa pun di sini.
 */
class DashboardController extends Controller
{
    public function index(): void
    {
        // Dummy — akan diganti query OrderModel real saat Sprint Order dikerjakan.
        $orderSummary = [
            'belum_bayar'   => 1,
            'diproses'      => 2,
            'siap_diambil'  => 1,
        ];

        $this->view('customer.dashboard', [
            'title'         => 'Dashboard Saya — ' . config('app.name'),
            'user'          => Auth::user(),
            'orderSummary'  => $orderSummary,
        ]);
    }
}
