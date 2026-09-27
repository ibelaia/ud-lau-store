<div style="width: 100%; box-sizing: border-box;">

    <!-- Judul Halaman -->
    <div style="margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">
            Pemberitahuan Toko 🔔
        </h2>
        <p style="font-size: 0.75rem; color: #64748b; margin: 2px 0 0;">
            Aktivitas booking, peringatan stok, dan riwayat toko
        </p>
    </div>

    <!-- List Notifikasi -->
    <div style="display: flex; flex-direction: column; gap: 0.65rem;">
        <?php if (empty($notifications)): ?>
            <div style="text-align: center; padding: 3rem 1rem; background: #ffffff; border-radius: 16px; border: 1px dashed #cbd5e1;">
                <span style="font-size: 2.2rem; display: block; margin-bottom: 0.5rem;">🔕</span>
                <p style="color: #64748b; font-size: 0.8rem; margin: 0;">Belum ada notifikasi baru.</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $notif): ?>
                <a href="<?= $notif['link'] ?>" style="display: flex; align-items: flex-start; gap: 0.75rem; background: #ffffff; border: 1px solid <?= $notif['is_read'] ? '#f1f5f9' : '#bbf7d0' ?>; border-radius: 14px; padding: 0.85rem; text-decoration: none; box-shadow: 0 1px 4px rgba(0,0,0,0.02); box-sizing: border-box;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: <?= $notif['bg'] ?>; font-size: 1.25rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <?= $notif['icon'] ?>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                            <h4 style="font-size: 0.86rem; font-weight: 700; color: #0f172a; margin: 0;">
                                <?= e($notif['title']) ?>
                            </h4>
                            <span style="font-size: 0.65rem; color: #94a3b8; flex-shrink: 0; margin-left: 6px;">
                                <?= e($notif['time']) ?>
                            </span>
                        </div>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0; line-height: 1.35;">
                            <?= e($notif['message']) ?>
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>