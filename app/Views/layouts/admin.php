<?php
$ownerUnread = 0;
if (class_exists('\App\Models\MessageModel') && class_exists('\App\Core\Auth') && \App\Core\Auth::check()) {
    $msgModel = new \App\Models\MessageModel();
    $ownerUnread = $msgModel->countUnread((int)\App\Core\Auth::id());
}

$currentUri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
$isCreateProduct = str_contains($currentUri, 'owner/products/create');
$isOwnerProducts = str_contains($currentUri, 'owner/products') && !$isCreateProduct;
$isChatActive    = str_contains($currentUri, 'owner/chat');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= e($title ?? 'Panel Owner — UD. LAU STORE') ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding-bottom: 90px;
            overflow-x: hidden;
        }
        .owner-topbar {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 0.85rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .topbar-brand {
            font-size: 1.15rem;
            font-weight: 800;
            color: #065f46;
            letter-spacing: -0.5px;
            text-decoration: none;
        }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .icon-circle-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #334155;
            text-decoration: none;
            position: relative;
        }
        .badge-dot {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #e11d48;
            color: #ffffff;
            font-size: 0.6rem;
            font-weight: 800;
            min-width: 16px;
            height: 16px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 3px;
            border: 2px solid #ffffff;
        }
        .avatar-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #d1fae5;
            color: #065f46;
            font-weight: 800;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #a7f3d0;
        }
        .sidebar-drawer {
            position: fixed;
            top: 0;
            left: -290px;
            width: 275px;
            height: 100vh;
            background: #064e3b;
            color: #ffffff;
            z-index: 999;
            transition: left 0.3s ease;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 20px rgba(0,0,0,0.25);
            padding: 1.25rem 1rem;
            box-sizing: border-box;
        }
        .sidebar-drawer.open {
            left: 0;
        }
        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 998;
            display: none;
        }
        .sidebar-backdrop.show {
            display: block;
        }
        .drawer-menu-item {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.75rem 0.85rem;
            border-radius: 10px;
            color: #ecfdf5;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
            transition: background 0.2s;
        }
        .drawer-menu-item:hover, .drawer-menu-item.active {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
        }
        
        /* Bottom Nav Bar Owner */
        .owner-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;
            height: 68px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            z-index: 90;
            padding: 0 4px 8px;
            box-sizing: border-box;
        }
        .owner-nav-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #64748b;
            font-size: 0.65rem;
            font-weight: 600;
            gap: 3px;
            width: 58px;
            position: relative;
        }
        .owner-nav-btn.active {
            color: #065f46;
            font-weight: 800;
        }

        /* FAB Tambah Center Item */
        .owner-fab-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            width: 58px;
            margin-bottom: 2px;
        }
        .owner-fab-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #065f46;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 400;
            box-shadow: 0 4px 12px rgba(6, 95, 70, 0.35);
            border: 3px solid #ffffff;
            margin-bottom: 2px;
        }
        .owner-fab-text {
            font-size: 0.65rem;
            font-weight: 700;
            color: #64748b;
            line-height: 1;
        }
        .owner-fab-link.active .owner-fab-text {
            color: #065f46;
            font-weight: 800;
        }
    </style>
</head>
<body>

<div id="drawerBackdrop" class="sidebar-backdrop" onclick="toggleDrawer(false)"></div>

<aside id="ownerSidebar" class="sidebar-drawer">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">
        <div>
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #fff;">LAU STORE</h3>
            <span style="font-size: 0.68rem; color: #a7f3d0; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Panel Pemilik Toko</span>
        </div>
        <button onclick="toggleDrawer(false)" style="background: none; border: none; color: #fff; font-size: 1.35rem; cursor: pointer; line-height: 1;">✕</button>
    </div>

    <div style="flex: 1; overflow-y: auto;">
        <a href="<?= base_url('owner/dashboard') ?>" class="drawer-menu-item <?= str_contains($currentUri, 'dashboard') ? 'active' : '' ?>">
            <span>📊</span> Dashboard Utama
        </a>

        <a href="<?= base_url('owner/chat') ?>" class="drawer-menu-item <?= $isChatActive ? 'active' : '' ?>" style="justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.85rem;">
                <span>💬</span> Chat Pelanggan
            </div>
            <span id="drawerOwnerBadge" style="background: #e11d48; color: #ffffff; font-size: 0.65rem; font-weight: 800; padding: 2px 7px; border-radius: 10px; display: <?= $ownerUnread > 0 ? 'inline-block' : 'none' ?>;">
                <?= $ownerUnread ?> baru
            </span>
        </a>

        <a href="<?= base_url('owner/orders') ?>" class="drawer-menu-item <?= str_contains($currentUri, 'orders') || str_contains($currentUri, 'booking') ? 'active' : '' ?>">
            <span>📑</span> Booking & Kasir Pickup
        </a>
        <a href="<?= base_url('owner/products') ?>" class="drawer-menu-item <?= $isOwnerProducts ? 'active' : '' ?>">
            <span>📦</span> Manajemen Stok ATK
        </a>
        <a href="<?= base_url('owner/categories') ?>" class="drawer-menu-item <?= str_contains($currentUri, 'categories') ? 'active' : '' ?>">
            <span>📁</span> Kategori Produk
        </a>

        <!-- MENU METODE PEMBAYARAN -->
        <a href="<?= base_url('owner/payments') ?>" class="drawer-menu-item <?= str_contains($currentUri, 'payments') ? 'active' : '' ?>">
            <span>💳</span> Metode Pembayaran
        </a>

        <a href="<?= base_url('owner/reports') ?>" class="drawer-menu-item <?= str_contains($currentUri, 'reports') ? 'active' : '' ?>">
            <span>📈</span> Laporan Penjualan
        </a>
        <a href="<?= base_url('/') ?>" target="_blank" class="drawer-menu-item" style="opacity: 0.85;">
            <span>↗</span> Lihat Toko Publik
        </a>
    </div>

    <div style="padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
        <a href="<?= base_url('logout') ?>" style="display: flex; align-items: center; gap: 0.6rem; color: #fca5a5; text-decoration: none; font-size: 0.85rem; font-weight: 700; padding: 0.65rem; border-radius: 8px; background: rgba(225, 29, 72, 0.15);">
            <span>⎋</span> Keluar (Logout)
        </a>
    </div>
</aside>

<header class="owner-topbar">
    <button onclick="toggleDrawer(true)" style="background: none; border: none; font-size: 1.4rem; cursor: pointer; color: #1e293b; padding: 0; line-height: 1;">
        ☰
    </button>

    <a href="<?= base_url('owner/dashboard') ?>" class="topbar-brand">
        LAU STORE
    </a>

    <div class="topbar-right">
        <a href="<?= base_url('owner/chat') ?>" class="icon-circle-btn" title="Pesan Customer">
            💬
            <span id="topbarOwnerBadge" class="badge-dot" style="display: <?= $ownerUnread > 0 ? 'flex' : 'none' ?>;">
                <?= $ownerUnread > 99 ? '99+' : $ownerUnread ?>
            </span>
        </a>

        <a href="<?= base_url('owner/notifications') ?>" class="icon-circle-btn" title="Notifikasi">
            🔔
        </a>

        <a href="<?= base_url('profile') ?>" class="avatar-btn" style="text-decoration: none;">
            O
        </a>
    </div>
</header>

<main style="padding: 1rem 0.85rem; max-width: 480px; margin: 0 auto; width: 100%; box-sizing: border-box;">
    <?= $content ?>
</main>

<nav class="owner-bottom-bar">
    <a href="<?= base_url('owner/dashboard') ?>" class="owner-nav-btn <?= (str_contains($currentUri, 'dashboard') || empty($currentUri)) ? 'active' : '' ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span>Beranda</span>
    </a>

    <a href="<?= base_url('owner/orders') ?>" class="owner-nav-btn <?= (str_contains($currentUri, 'orders') || str_contains($currentUri, 'booking')) ? 'active' : '' ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        <span>Booking</span>
    </a>

    <a href="<?= base_url('owner/products/create') ?>" class="owner-fab-link <?= $isCreateProduct ? 'active' : '' ?>" title="Tambah Produk">
        <div class="owner-fab-circle">+</div>
        <span class="owner-fab-text">Tambah</span>
    </a>

    <a href="<?= base_url('owner/products') ?>" class="owner-nav-btn <?= $isOwnerProducts ? 'active' : '' ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
        <span>Katalog</span>
    </a>

    <a href="<?= base_url('profile') ?>" class="owner-nav-btn <?= str_contains($currentUri, 'profile') ? 'active' : '' ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>Profile</span>
    </a>
</nav>

<script>
function toggleDrawer(open) {
    const sidebar = document.getElementById('ownerSidebar');
    const backdrop = document.getElementById('drawerBackdrop');
    if (open) {
        sidebar.classList.add('open');
        backdrop.classList.add('show');
    } else {
        sidebar.classList.remove('open');
        backdrop.classList.remove('show');
    }
}

// Auto-check notifikasi chat Owner secara real-time tanpa reload
(function() {
    const topBadge = document.getElementById('topbarOwnerBadge');
    const sideBadge = document.getElementById('drawerOwnerBadge');

    function checkOwnerUnread() {
        fetch('<?= base_url('chat/unread-count') ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            const count = parseInt(data.count) || 0;

            if (topBadge) {
                if (count > 0) {
                    topBadge.textContent = count > 99 ? '99+' : count;
                    topBadge.style.display = 'flex';
                } else {
                    topBadge.style.display = 'none';
                }
            }

            if (sideBadge) {
                if (count > 0) {
                    sideBadge.textContent = count + ' baru';
                    sideBadge.style.display = 'inline-block';
                } else {
                    sideBadge.style.display = 'none';
                }
            }
        })
        .catch(() => {});
    }

    setInterval(checkOwnerUnread, 3000);
})();
</script>

</body>
</html>