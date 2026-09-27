<?php
// Hitung jumlah jenis barang unik di session keranjang
$cartBadgeCount = !empty($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<header class="site-header">
    <div class="header-top">
        <a href="<?= base_url('/') ?>" class="header-logo">
            <img src="<?= asset('img/logo-header.png') ?>" alt="<?= e(config('app.brand_name')) ?> - <?= e(config('app.brand_subtitle')) ?>">
        </a>
    </div>

    <form method="GET" action="<?= base_url('products') ?>" class="header-search">
        <i class="ti ti-search" aria-hidden="true"></i>
        <input type="search" name="q" placeholder="Cari alat tulis...">
    </form>

    <div class="header-shortcuts">
        <a href="<?= base_url('cart') ?>" class="shortcut-item">
            <span class="shortcut-icon-wrap">
                <i class="ti ti-shopping-cart" aria-hidden="true"></i>
                <?php if ($cartBadgeCount > 0): ?>
                    <span class="shortcut-badge"><?= $cartBadgeCount ?></span>
                <?php endif; ?>
            </span>
            <span>Keranjang</span>
        </a>
        <a href="https://wa.me/62812xxxxxxxx" target="_blank" rel="noopener" class="shortcut-item">
            <i class="ti ti-brand-whatsapp" aria-hidden="true" style="color:var(--color-success)"></i>
            <span>Hubungi Kami</span>
        </a>
        <a href="<?= \App\Core\Auth::check() ? base_url('profile') : base_url('login') ?>" class="shortcut-item">
            <i class="ti ti-user" aria-hidden="true"></i>
            <span>Profil</span>
        </a>
    </div>
</header>