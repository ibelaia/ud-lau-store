<?php
// Deteksi Customer ID & Hitung Pesan Belum Dibaca
$customerUnreadChat = 0;
$cUserId = 0;

if (class_exists('\App\Core\Auth') && \App\Core\Auth::check()) {
    $cUserId = (int)\App\Core\Auth::id();
} elseif (isset($_SESSION['user']['id'])) {
    $cUserId = (int)$_SESSION['user']['id'];
} elseif (isset($_SESSION['user_id'])) {
    $cUserId = (int)$_SESSION['user_id'];
}

if ($cUserId > 0 && class_exists('\App\Models\MessageModel')) {
    $msgModel = new \App\Models\MessageModel();
    $customerUnreadChat = $msgModel->countUnread($cUserId);
}

// Deteksi Total Keranjang & Wishlist
$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += (int)($item['quantity'] ?? 1);
    }
}

$wishlistCount = 0;
if ($cUserId > 0 && class_exists('\App\Models\WishlistModel')) {
    $wishlistModel = new \App\Models\WishlistModel();
    $wishlistCount = $wishlistModel->countByUser($cUserId);
}

// WhatsApp Toko
$storePhone = '6281234567890';
if (class_exists('\App\Models\SettingModel')) {
    $settingModel = new \App\Models\SettingModel();
    $storePhone = $settingModel->get('store_phone', '6281234567890');
}
$cleanPhone = preg_replace('/[^0-9]/', '', $storePhone);
if (str_starts_with($cleanPhone, '0')) {
    $cleanPhone = '62' . substr($cleanPhone, 1);
}

// Deteksi URI Aktif
$currentUri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
$isHome     = empty($currentUri) || $currentUri === 'home';
$isCatalog  = str_contains($currentUri, 'products');
$isWishlist = str_contains($currentUri, 'wishlist');
$isCart     = str_contains($currentUri, 'cart');
$isProfile  = str_contains($currentUri, 'profile') || str_contains($currentUri, 'login');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= e($title ?? 'UD. LAU STORE — ATK & Kebutuhan Sekolah') ?></title>
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
            padding: 0 0 75px 0;
            overflow-x: hidden;
        }
        .header-top {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 0.65rem 0.85rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            max-width: 480px;
            margin: 0 auto;
            width: 100%;
        }
        .main-content {
            max-width: 480px;
            margin: 0 auto;
            padding: 0.75rem 0.85rem;
            width: 100%;
        }
        .header-btn {
            position: relative;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #334155;
            text-decoration: none;
            flex-shrink: 0;
        }
        .badge-red {
            position: absolute;
            top: -3px;
            right: -3px;
            background: #e11d48;
            color: #ffffff;
            font-size: 0.58rem;
            font-weight: 800;
            min-width: 15px;
            height: 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 3px;
            border: 2px solid #ffffff;
        }
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;
            height: 62px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-around;
            z-index: 90;
            padding: 0 4px;
        }
        .bottom-nav a {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #64748b;
            font-size: 0.65rem;
            font-weight: 600;
            gap: 2px;
            width: 58px;
            position: relative;
        }
        .bottom-nav a.active {
            color: #065f46;
            font-weight: 800;
        }
    </style>
</head>
<body>

    <!-- Header Atas Ramping Ala Aplikasi Mobile -->
    <header class="header-top">
        <a href="<?= base_url('/') ?>" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none; min-width: 0;">
            <div style="width: 32px; height: 32px; border-radius: 7px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0;">
                📚
            </div>
            <div style="min-width: 0; line-height: 1.15;">
                <div style="font-size: 0.88rem; font-weight: 800; color: #065f46; letter-spacing: -0.3px;">UD. LAU STORE</div>
                <div style="font-size: 0.6rem; color: #64748b; font-weight: 600; white-space: nowrap;">ATK & Kebutuhan Sekolah</div>
            </div>
        </a>

        <div style="display: flex; align-items: center; gap: 0.35rem; flex-shrink: 0;">
            <!-- WhatsApp -->
            <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= urlencode('Halo UD. LAU Store, saya ingin bertanya.') ?>" target="_blank" class="header-btn" style="background: #ecfdf5; border-color: #a7f3d0; color: #059669;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
            </a>

            <!-- Chat Toko + Notifikasi Merah -->
            <a href="<?= $cUserId > 0 ? base_url('chat') : base_url('login') ?>" class="header-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                <?php if ($customerUnreadChat > 0): ?>
                    <span class="badge-red">
                        <?= $customerUnreadChat > 99 ? '99+' : $customerUnreadChat ?>
                    </span>
                <?php endif; ?>
            </a>

            <!-- Keranjang -->
            <a href="<?= base_url('cart') ?>" class="header-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <?php if ($cartCount > 0): ?>
                    <span class="badge-red">
                        <?= $cartCount > 99 ? '99+' : $cartCount ?>
                    </span>
                <?php endif; ?>
            </a>
        </div>
    </header>

    <!-- Konten Utama -->
    <main class="main-content">
        <?= $content ?>
    </main>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <a href="<?= base_url('/') ?>" class="<?= $isHome ? 'active' : '' ?>">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>Beranda</span>
        </a>

        <a href="<?= base_url('products') ?>" class="<?= $isCatalog ? 'active' : '' ?>">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
            <span>Katalog</span>
        </a>

        <a href="<?= base_url('wishlist') ?>" class="<?= $isWishlist ? 'active' : '' ?>">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
            <span>Favorit</span>
            <?php if ($wishlistCount > 0): ?>
                <span class="badge-red" style="top: -3px; right: 9px;">
                    <?= $wishlistCount > 99 ? '99+' : $wishlistCount ?>
                </span>
            <?php endif; ?>
        </a>

        <a href="<?= base_url('cart') ?>" class="<?= $isCart ? 'active' : '' ?>">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            <span>Keranjang</span>
        </a>

        <a href="<?= $cUserId > 0 ? base_url('profile') : base_url('login') ?>" class="<?= $isProfile ? 'active' : '' ?>">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span><?= $cUserId > 0 ? 'Profile' : 'Masuk' ?></span>
        </a>
    </nav>

</body>
</html>