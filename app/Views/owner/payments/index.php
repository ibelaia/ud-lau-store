<style>
/* Desain Switch Toggle Interaktif Modern */
.switch-toggle {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 26px;
    flex-shrink: 0;
}
.switch-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}
.slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: #cbd5e1;
    transition: .3s;
    border-radius: 30px;
}
.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
}
input:checked + .slider {
    background-color: #065f46;
}
input:checked + .slider:before {
    transform: translateX(22px);
}
</style>

<div style="padding-bottom: 2rem;">
    <!-- Header Halaman -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.15rem; margin-bottom: 1.15rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
        <div style="display: flex; align-items: center; gap: 0.85rem;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: #ecfdf5; border: 1px solid #a7f3d0; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                💳
            </div>
            <div>
                <h2 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0 0 3px;">Metode Pembayaran</h2>
                <p style="font-size: 0.73rem; color: #64748b; margin: 0; line-height: 1.35;">Atur ketersediaan metode pembayaran bagi pelanggan di kasir checkout.</p>
            </div>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 0.75rem 0.95rem; border-radius: 10px; font-size: 0.78rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <span>✅</span>
            <span>Status pembayaran berhasil diperbarui.</span>
        </div>
    <?php endif; ?>

    <!-- Daftar Kartu Pembayaran -->
    <div style="display: flex; flex-direction: column; gap: 0.85rem;">
        <?php foreach ($methods as $m): ?>
            <?php 
                $isActive = (int)$m['is_active'] === 1;
                $icon = match((int)$m['id']) {
                    1 => '🏦',
                    2 => '📱',
                    default => '💵'
                };
            ?>
            <div style="background: #ffffff; border: 1px solid <?= $isActive ? '#a7f3d0' : '#e2e8f0' ?>; border-radius: 14px; padding: 0.95rem 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.03); display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; transition: border-color 0.2s;">
                <!-- Kiri: Ikon & Teks Info -->
                <div style="display: flex; align-items: center; gap: 0.85rem; min-width: 0; flex: 1;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: <?= $isActive ? '#f0fdf4' : '#f8fafc' ?>; border: 1px solid <?= $isActive ? '#bbf7d0' : '#e2e8f0' ?>; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                        <?= $icon ?>
                    </div>
                    <div style="min-width: 0; flex: 1;">
                        <h4 style="font-size: 0.85rem; font-weight: 700; color: <?= $isActive ? '#0f172a' : '#64748b' ?>; margin: 0 0 3px; line-height: 1.3; word-break: break-word;">
                            <?= e($m['name']) ?>
                        </h4>
                        <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.68rem; font-weight: 700; padding: 2px 7px; border-radius: 6px; background: <?= $isActive ? '#dcfce7' : '#f1f5f9' ?>; color: <?= $isActive ? '#15803d' : '#94a3b8' ?>;">
                            <span style="font-size: 0.5rem;"><?= $isActive ? '●' : '○' ?></span>
                            <?= $isActive ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </div>
                </div>

                <!-- Kanan: Switch Toggle Button -->
                <form method="POST" action="<?= base_url('owner/payments/toggle') ?>" style="margin: 0; flex-shrink: 0;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                    <input type="hidden" name="is_active" value="<?= $isActive ? 0 : 1 ?>">
                    <label class="switch-toggle" title="<?= $isActive ? 'Klik untuk mematikan' : 'Klik untuk mengaktifkan' ?>">
                        <input type="checkbox" <?= $isActive ? 'checked' : '' ?> onchange="this.form.submit()">
                        <span class="slider"></span>
                    </label>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>