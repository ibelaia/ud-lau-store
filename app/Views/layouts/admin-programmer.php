<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? config('app.name')) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <img src="<?= asset('img/logo-header.png') ?>" alt="<?= e(config('app.brand_name')) ?>">
            </div>
            <nav class="sidebar-nav">
                <a href="<?= base_url('programmer/dashboard') ?>" class="sidebar-link active">
                    <i class="ti ti-layout-dashboard" aria-hidden="true"></i> Ringkasan Sistem
                </a>
                <a href="#" class="sidebar-link">
                    <i class="ti ti-database-export" aria-hidden="true"></i> Backup &amp; Restore
                </a>
                <a href="<?= base_url('programmer/activity-logs') ?>" class="sidebar-link">
                    <i class="ti ti-history" aria-hidden="true"></i> Activity Log
                </a>
                <a href="#" class="sidebar-link">
                    <i class="ti ti-tool" aria-hidden="true"></i> Maintenance
                </a>
                <a href="<?= base_url('programmer/settings') ?>" class="sidebar-link">
                    <i class="ti ti-settings" aria-hidden="true"></i> Pengaturan
                </a>
            </nav>
            <form method="POST" action="<?= base_url('logout') ?>" class="sidebar-logout-form">
                <?= csrf_field() ?>
                <button type="submit" class="sidebar-link sidebar-logout">
                    <i class="ti ti-logout" aria-hidden="true"></i> Logout
                </button>
            </form>
        </aside>

        <div class="admin-main">
            <?= $content ?>
        </div>
    </div>

    <nav class="admin-mobile-nav">
        <a href="<?= base_url('programmer/dashboard') ?>"><i class="ti ti-layout-dashboard" aria-hidden="true"></i></a>
        <a href="#"><i class="ti ti-database-export" aria-hidden="true"></i></a>
        <a href="<?= base_url('programmer/activity-logs') ?>"><i class="ti ti-history" aria-hidden="true"></i></a>
        <a href="#"><i class="ti ti-tool" aria-hidden="true"></i></a>
        <a href="<?= base_url('programmer/settings') ?>"><i class="ti ti-settings" aria-hidden="true"></i></a>
    </nav>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
