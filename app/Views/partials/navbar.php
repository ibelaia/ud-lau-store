<?php
/**
 * Bottom navigation customer, sesuai Wireflow Tahap 4:
 * Home | Favorite | Cart | Profile.
 * Reusable — dipakai di seluruh halaman customer lewat layout utama.
 */
?>
<nav class="bottom-nav">
    <a href="<?= base_url('/') ?>">Home</a>
    <a href="<?= base_url('wishlist') ?>">Favorite</a>
    <a href="<?= base_url('cart') ?>">Cart</a>
    <?php if (\App\Core\Auth::check()): ?>
        <a href="<?= base_url('profile') ?>">Profile</a>
        <form method="POST" action="<?= base_url('logout') ?>" class="logout-form">
            <?= csrf_field() ?>
            <button type="submit" class="link-button">Logout</button>
        </form>
    <?php else: ?>
        <a href="<?= base_url('login') ?>">Login</a>
    <?php endif; ?>
</nav>
