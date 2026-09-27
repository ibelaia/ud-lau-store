<div class="dash-welcome">
    <h2>Halo, <?= e($user['name']) ?></h2>
</div>

<h3 class="section-title">Pesanan Saya</h3>
<div class="order-status-grid">
    <a href="<?= base_url('orders') ?>?status=belum_bayar" class="order-status-card">
        <span class="order-status-count"><?= (int) $orderSummary['belum_bayar'] ?></span>
        <span>Belum Bayar</span>
    </a>
    <a href="<?= base_url('orders') ?>?status=diproses" class="order-status-card">
        <span class="order-status-count"><?= (int) $orderSummary['diproses'] ?></span>
        <span>Diproses</span>
    </a>
    <a href="<?= base_url('orders') ?>?status=siap_diambil" class="order-status-card">
        <span class="order-status-count"><?= (int) $orderSummary['siap_diambil'] ?></span>
        <span>Siap Diambil</span>
    </a>
    <a href="<?= base_url('orders') ?>" class="order-status-card">
        <i class="ti ti-history" aria-hidden="true"></i>
        <span>Riwayat</span>
    </a>
</div>

<h3 class="section-title">Menu</h3>
<div class="dash-menu-list">
    <a href="<?= base_url('checkout') ?>" class="dash-menu-item">
        <i class="ti ti-shopping-cart" aria-hidden="true"></i> Booking Baru
        <i class="ti ti-chevron-right" aria-hidden="true" style="margin-left:auto;"></i>
    </a>
    <a href="<?= base_url('profile') ?>" class="dash-menu-item">
        <i class="ti ti-user" aria-hidden="true"></i> Profil
        <i class="ti ti-chevron-right" aria-hidden="true" style="margin-left:auto;"></i>
    </a>
    <form method="POST" action="<?= base_url('logout') ?>">
        <?= csrf_field() ?>
        <button type="submit" class="dash-menu-item dash-menu-logout">
            <i class="ti ti-logout" aria-hidden="true"></i> Logout
        </button>
    </form>
</div>
