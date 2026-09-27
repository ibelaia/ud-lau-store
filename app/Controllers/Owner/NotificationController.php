<?php

namespace App\Controllers\Owner;

use App\Core\Auth;
use App\Core\Controller;

class NotificationController extends Controller
{
    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('login');
            return;
        }

        // Dummy/sample daftar notifikasi aktivitas terkini toko
        $notifications = [
            [
                'id'         => 1,
                'type'       => 'order',
                'title'      => 'Booking Baru Masuk',
                'message'    => 'Pelanggan telah membuat pesanan ATK baru untuk diambil di toko.',
                'time'       => '5 menit yang lalu',
                'is_read'    => false,
                'icon'       => '📑',
                'bg'         => '#eff6ff',
                'link'       => base_url('owner/orders')
            ],
            [
                'id'         => 2,
                'type'       => 'stock',
                'title'      => 'Stok Menipis',
                'message'    => 'Kertas HVS A4 75 GSM tersisa kurang dari batas minimum stok.',
                'time'       => '1 jam yang lalu',
                'is_read'    => false,
                'icon'       => '⚠️',
                'bg'         => '#fffbeb',
                'link'       => base_url('owner/products')
            ],
            [
                'id'         => 3,
                'type'       => 'chat',
                'title'      => 'Pesan Pelanggan Baru',
                'message'    => 'okho laia menanyakan ketersediaan stok barang melalui chat.',
                'time'       => '2 jam yang lalu',
                'is_read'    => true,
                'icon'       => '💬',
                'bg'         => '#ecfdf5',
                'link'       => base_url('owner/chat')
            ]
        ];

        $this->view('owner.notifications.index', [
            'title'         => 'Pemberitahuan — Panel Owner',
            'notifications' => $notifications
        ], 'layouts.admin');
    }
}   