<div class="dashboard-header">
    <h2>Halo, <?= e($user['name']) ?></h2>
    <p><?= e($today) ?> &mdash; Versi Aplikasi: <?= e($appVersion) ?></p>
</div>

<div class="summary-grid">
    <?php foreach ($systemStatus as $item): ?>
        <div class="summary-card">
            <i class="ti <?= e($item['icon']) ?>" aria-hidden="true"></i>
            <p class="summary-label"><?= e($item['label']) ?></p>
            <p class="summary-value" style="font-size:1.1rem;">
                <?= e($item['value']) ?>
                <?php if ($item['ok']): ?>
                    <span style="color:var(--color-success);font-size:0.7rem;">&#9679; OK</span>
                <?php endif; ?>
            </p>
        </div>
    <?php endforeach; ?>
</div>

<div class="dashboard-panel">
    <h3>Activity Log Terbaru</h3>
    <ul class="activity-list">
        <?php foreach ($recentActivity as $activity): ?>
            <li>
                <span><?= e($activity['text']) ?></span>
                <span class="activity-time"><?= e($activity['time']) ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
