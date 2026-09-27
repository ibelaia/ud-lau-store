<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= e($title ?? 'Daftar Booking & Pickup — UD. LAU STORE') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow-x: hidden;
        }
        .mobile-container {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            min-height: 100vh;
            background: #ffffff;
            padding-bottom: 75px;
            position: relative;
            box-shadow: 0 0 20px rgba(0,0,0,0.04);
        }
        .owner-topbar {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 0.65rem 0.85rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .content-wrap {
            padding: 0.75rem;
        }
        .summary-card {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.65rem;
        }
        .summary-box {
            flex: 1;
            padding: 0.55rem 0.7rem;
            border-radius: 10px;
        }
        .order-card {
            background: #ffffff;
            border: 1px solid #eef2f6;
            border-radius: 12px;
            padding: 0.75rem;
            margin-bottom: 0.55rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .owner-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;
            height: 60px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-around;
            z-index: 100;
            padding: 2px 4px 4px;
        }
        .owner-nav-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #64748b;
            font-size: 0.62rem;
            font-weight: 600;
            gap: 2px;
            width: 54px;
        }
        .owner-nav-btn.active {
            color: #065f46;
            font-weight: 800;
        }
        .fab-container {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: -18px;
            width: 54px;
            text-decoration: none;
        }
        .fab-button {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #065f46;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            font-weight: 300;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(6, 95, 70, 0.35);
            border: 2.5px solid #ffffff;
        }
        .fab-label {
            font-size: 0.62rem;
            font-weight: 700;
            color: #64748b;
            margin-top: 2px;
        }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(3px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .modal-box {
            background: #ffffff;
            border-radius: 16px;
            width: 100%;
            max-width: 360px;
            padding: 1.25rem;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            animation: popIn 0.2s ease-out forwards;
        }
        .detail-modal-box {
            background: #ffffff;
            border-radius: 16px;
            width: 100%;
            max-width: 420px;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
            animation: popIn 0.2s ease-out forwards;
        }
        @keyframes popIn {
            from { transform: scale(0.92); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>

<div class="mobile-container">

    <header class="owner-topbar">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <span style="font-size: 1.15rem; cursor: pointer; color: #1e293b;">☰</span>
            <span style="font-size: 1.05rem; font-weight: 800; color: #065f46; letter-spacing: -0.5px;">LAU STORE</span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.4rem;">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">💬</div>
            <div style="width: 32px; height: 32px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">🔔</div>
            <div style="width: 30px; height: 30px; border-radius: 50%; background: #d1fae5; color: #065f46; font-weight: 800; font-size: 0.78rem; display: flex; align-items: center; justify-content: center; border: 1px solid #a7f3d0;">O</div>
        </div>
    </header>

    <div class="content-wrap">
        <div style="margin-bottom: 0.65rem;">
            <h2 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0;">Daftar Booking & Pickup 🛍️</h2>
            <p style="font-size: 0.68rem; color: #64748b; margin: 1px 0 0;">Kelola pesanan online untuk pengambilan langsung di toko</p>
        </div>

        <div class="summary-card">
            <div class="summary-box" style="background: #fef3c7; border: 1px solid #fde68a;">
                <span style="font-size: 0.62rem; font-weight: 700; color: #b45309; display: block;">⏳ Pesanan Baru Masuk</span>
                <span style="font-size: 1.05rem; font-weight: 800; color: #92400e;"><?= (int)($waitingCount ?? 0) ?> Pesanan</span>
            </div>
            <div class="summary-box" style="background: #ecfdf5; border: 1px solid #a7f3d0;">
                <span style="font-size: 0.62rem; font-weight: 700; color: #065f46; display: block;">🛍️ Siap Diambil di Toko</span>
                <span style="font-size: 1.05rem; font-weight: 800; color: #047857;"><?= (int)($readyCount ?? 0) ?> Pesanan</span>
            </div>
        </div>

        <form method="GET" action="<?= base_url('owner/orders') ?>" style="margin-bottom: 0.65rem;">
            <div style="display: flex; gap: 0.35rem;">
                <input type="search" name="q" value="<?= e($q ?? '') ?>" placeholder="Cari Kode atau Nama Customer..." style="flex: 1; height: 34px; padding: 0 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.75rem; outline: none; background: #fff;">
                <button type="submit" style="background: #065f46; color: #fff; border: none; border-radius: 8px; padding: 0 0.85rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">Cari</button>
            </div>
        </form>

        <?php $currentStatus = strtolower(trim((string)($selectedStatus ?? $status ?? 'semua'))); ?>
        <div style="display: flex; gap: 0.35rem; overflow-x: auto; padding-bottom: 0.35rem; margin-bottom: 0.65rem; scrollbar-width: none;">
            <a href="<?= base_url('owner/orders') ?>" style="flex: 0 0 auto; text-decoration: none; padding: 0.3rem 0.65rem; border-radius: 16px; font-size: 0.68rem; font-weight: 700; <?= in_array($currentStatus, ['', 'semua', 'all']) ? 'background: #065f46; color: #fff;' : 'background: #f1f5f9; color: #475569;' ?>">
                Semua
            </a>
            <a href="<?= base_url('owner/orders?status=menunggu') ?>" style="flex: 0 0 auto; text-decoration: none; padding: 0.3rem 0.65rem; border-radius: 16px; font-size: 0.68rem; font-weight: 600; <?= $currentStatus === 'menunggu' ? 'background: #d97706; color: #fff;' : 'background: #fef3c7; color: #92400e;' ?>">
                ⏳ Menunggu
            </a>
            <a href="<?= base_url('owner/orders?status=siap_diambil') ?>" style="flex: 0 0 auto; text-decoration: none; padding: 0.3rem 0.65rem; border-radius: 16px; font-size: 0.68rem; font-weight: 600; <?= in_array($currentStatus, ['siap_diambil', 'ready', 'siap']) ? 'background: #059669; color: #fff;' : 'background: #dcfce7; color: #166534;' ?>">
                🛍️ Siap Diambil
            </a>
            <a href="<?= base_url('owner/orders?status=selesai') ?>" style="flex: 0 0 auto; text-decoration: none; padding: 0.3rem 0.65rem; border-radius: 16px; font-size: 0.68rem; font-weight: 600; <?= in_array($currentStatus, ['selesai', 'completed']) ? 'background: #334155; color: #fff;' : 'background: #f1f5f9; color: #475569;' ?>">
                ✅ Selesai
            </a>
            <a href="<?= base_url('owner/orders?status=batal') ?>" style="flex: 0 0 auto; text-decoration: none; padding: 0.3rem 0.65rem; border-radius: 16px; font-size: 0.68rem; font-weight: 600; <?= in_array($currentStatus, ['batal', 'cancelled']) ? 'background: #dc2626; color: #fff;' : 'background: #fee2e2; color: #991b1b;' ?>">
                ✕ Batal
            </a>
        </div>

        <div>
            <?php if (empty($orders)): ?>
                <div style="text-align: center; padding: 2.5rem 1rem; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                    <span style="font-size: 2.2rem; display: block; margin-bottom: 0.3rem;">📭</span>
                    <h4 style="font-size: 0.85rem; font-weight: 800; color: #334155; margin: 0;">Tidak Ada Pesanan</h4>
                    <p style="color: #64748b; font-size: 0.7rem; margin: 2px 0 0;">Belum ada pesanan pada kategori filter ini.</p>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): 
                    $oId       = (int)($order['id'] ?? 0);
                    $code      = $order['booking_code'] ?? ('BK-' . $oId);
                    $custName  = $order['customer_name'] ?? 'Pelanggan';
                    $custPhone = $order['customer_phone'] ?? '';
                    $totalAmt  = (float)($order['total_amount'] ?? 0);
                    $payMethod = $order['payment_method'] ?? 'Bayar Tunai di Toko (Cash)';
                    $itemsDesc = $order['items_summary'] ?? 'Kebutuhan ATK';
                    $dateStr   = $order['created_at'] ?? date('d M');
                    $rawStatus = strtolower(trim((string)($order['status'] ?? 'pending')));

                    $statusLabel = 'Menunggu Konfirmasi';
                    $badgeBg     = '#fef3c7';
                    $badgeColor  = '#b45309';

                    if (in_array($rawStatus, ['ready', 'siap_diambil', 'siap'])) {
                        $statusLabel = 'Siap Diambil';
                        $badgeBg     = '#dcfce7';
                        $badgeColor  = '#15803d';
                    } elseif (in_array($rawStatus, ['completed', 'selesai', 'done'])) {
                        $statusLabel = 'Selesai / Diterima';
                        $badgeBg     = '#f1f5f9';
                        $badgeColor  = '#475569';
                    } elseif (in_array($rawStatus, ['cancelled', 'dibatalkan', 'batal'])) {
                        $statusLabel = 'Dibatalkan';
                        $badgeBg     = '#fee2e2';
                        $badgeColor  = '#dc2626';
                    }

                    $waPhone = preg_replace('/[^0-9]/', '', $custPhone);
                    if (str_starts_with($waPhone, '0')) {
                        $waPhone = '62' . substr($waPhone, 1);
                    }

                    $orderJson = htmlspecialchars(json_encode([
                        'id'         => $oId,
                        'code'       => $code,
                        'name'       => $custName,
                        'phone'      => $custPhone,
                        'waPhone'    => $waPhone,
                        'total'      => number_format($totalAmt, 0, ',', '.'),
                        'payment'    => $payMethod,
                        'date'       => date('d F Y, H:i', strtotime($dateStr)),
                        'status'     => $statusLabel,
                        'badgeBg'    => $badgeBg,
                        'badgeColor' => $badgeColor,
                        'items'      => $order['items_list'] ?? []
                    ]), ENT_QUOTES, 'UTF-8');
                ?>
                    <div class="order-card">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.45rem; border-bottom: 1px dashed #f1f5f9; margin-bottom: 0.45rem;">
                            <div>
                                <span style="font-size: 0.82rem; font-weight: 800; color: #0f172a;"><?= e($code) ?></span>
                                <span style="font-size: 0.6rem; color: #64748b; background: #f1f5f9; padding: 1px 5px; border-radius: 4px; margin-left: 3px;">
                                    📅 <?= date('d M', strtotime($dateStr)) ?>
                                </span>
                            </div>
                            <span style="font-size: 0.62rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; background: <?= $badgeBg ?>; color: <?= $badgeColor ?>;">
                                <?= $statusLabel ?>
                            </span>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                            <h4 style="font-size: 0.85rem; font-weight: 800; color: #1e293b; margin: 0;">
                                <?= e($custName) ?>
                            </h4>
                            <?php if (!empty($custPhone)): ?>
                                <a href="https://wa.me/<?= $waPhone ?>" target="_blank" style="display: inline-flex; align-items: center; gap: 3px; font-size: 0.68rem; font-weight: 700; color: #059669; text-decoration: none; background: #ecfdf5; padding: 2px 6px; border-radius: 4px; border: 1px solid #a7f3d0;">
                                    💬 <?= e($custPhone) ?>
                                </a>
                            <?php endif; ?>
                        </div>

                        <div style="font-size: 0.72rem; color: #475569; margin-bottom: 0.45rem; line-height: 1.35;">
                            📦 <?= e($itemsDesc) ?>
                        </div>

                        <div style="padding: 0.45rem 0.6rem; background: #f8fafc; border-radius: 6px; margin-bottom: 0.55rem; display: flex; justify-content: space-between; align-items: center; border: 1px solid #f1f5f9;">
                            <div>
                                <span style="font-size: 0.58rem; color: #64748b; display: block;">Total Tagihan (<?= e($payMethod) ?>)</span>
                                <span style="font-size: 0.88rem; font-weight: 800; color: #065f46;">
                                    Rp <?= number_format($totalAmt, 0, ',', '.') ?>
                                </span>
                            </div>
                            <button type="button" onclick="openOrderDetail(<?= $orderJson ?>)" style="background: #ffffff; border: 1.5px solid #065f46; color: #065f46; padding: 4px 10px; border-radius: 6px; font-size: 0.68rem; font-weight: 700; cursor: pointer;">
                                🔍 Rincian
                            </button>
                        </div>

                        <div style="display: flex; gap: 0.4rem;">
                            <?php if (in_array($rawStatus, ['pending', 'menunggu', 'processing', 'diproses'])): ?>
                                <a href="<?= base_url('owner/orders?action=update_status&id=' . $oId . '&to=ready') ?>" style="flex: 1.6; height: 34px; background: #065f46; color: #fff; border-radius: 6px; font-size: 0.72rem; font-weight: 700; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                                    ✅ Terima & Siapkan
                                </a>
                                <button type="button" onclick="showAppConfirm('Batalkan Pesanan', 'Apakah Anda yakin ingin membatalkan pesanan ini? Stok barang akan otomatis dikembalikan ke toko.', '<?= base_url('owner/orders?action=update_status&id=' . $oId . '&to=cancelled') ?>', 'Batalkan', '#dc2626')" style="flex: 1; height: 34px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; font-size: 0.72rem; font-weight: 700; cursor: pointer;">
                                    Batalkan
                                </button>

                            <?php elseif (in_array($rawStatus, ['ready', 'siap_diambil', 'siap'])): ?>
                                <a href="<?= base_url('owner/orders?action=update_status&id=' . $oId . '&to=completed') ?>" style="flex: 2; height: 34px; background: #065f46; color: #fff; border-radius: 6px; font-size: 0.75rem; font-weight: 800; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                                    🎉 Serahkan Barang & Selesai
                                </a>
                                <button type="button" onclick="showAppConfirm('Batalkan Pesanan', 'Apakah Anda yakin ingin membatalkan pesanan ini? Stok barang akan otomatis dikembalikan ke toko.', '<?= base_url('owner/orders?action=update_status&id=' . $oId . '&to=cancelled') ?>', 'Batalkan', '#dc2626')" style="flex: 1; height: 34px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; font-size: 0.72rem; font-weight: 700; cursor: pointer;">
                                    Batalkan
                                </button>

                            <?php elseif (in_array($rawStatus, ['completed', 'selesai', 'done'])): ?>
                                <div style="width: 100%; height: 32px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; color: #166534;">
                                    ✓ Transaksi Tuntas & Barang Sudah Diambil
                                </div>

                            <?php elseif (in_array($rawStatus, ['cancelled', 'dibatalkan', 'batal'])): ?>
                                <div style="flex: 1; height: 32px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; color: #991b1b;">
                                    ✕ Dibatalkan
                                </div>
                                <button type="button" onclick="showAppConfirm('Hapus Riwayat', 'Apakah Anda yakin ingin menghapus data pesanan ini secara permanen dari database?', '<?= base_url('owner/orders?action=delete_order&id=' . $oId) ?>', 'Hapus Permanen', '#dc2626')" style="padding: 0 0.75rem; height: 32px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; color: #64748b; font-size: 0.7rem; font-weight: 700; cursor: pointer;">
                                    🗑️ Hapus
                                </button>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <nav class="owner-bottom-bar">
        <a href="<?= base_url('owner/dashboard') ?>" class="owner-nav-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>Beranda</span>
        </a>
        <a href="<?= base_url('owner/orders') ?>" class="owner-nav-btn active">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            <span>Booking</span>
        </a>
        <div class="fab-container">
            <a href="<?= base_url('owner/products/create') ?>" class="fab-button" title="Tambah Produk">+</a>
            <span class="fab-label">Tambah</span>
        </div>
        <a href="<?= base_url('owner/products') ?>" class="owner-nav-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
            <span>Katalog</span>
        </a>
        <a href="<?= base_url('profile') ?>" class="owner-nav-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Profile</span>
        </a>
    </nav>

</div>

<!-- Modal Pop-up Konfirmasi Aksi -->
<div id="customModal" class="modal-overlay">
    <div class="modal-box">
        <div style="font-size: 2.2rem; margin-bottom: 0.35rem;">⚠️</div>
        <h3 id="modalTitle" style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0 0 0.35rem;">Konfirmasi</h3>
        <p id="modalDesc" style="font-size: 0.75rem; color: #64748b; line-height: 1.45; margin: 0 0 1.25rem;">Keterangan konfirmasi...</p>
        
        <div style="display: flex; gap: 0.5rem;">
            <button type="button" onclick="closeAppConfirm()" style="flex: 1; height: 38px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">
                Kembali
            </button>
            <a id="modalActionBtn" href="#" style="flex: 1.3; height: 38px; background: #dc2626; color: #ffffff; border-radius: 8px; font-size: 0.78rem; font-weight: 800; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                Lanjutkan
            </a>
        </div>
    </div>
</div>

<!-- Modal Pop-up Detail Pemesan & Rincian Barang -->
<div id="orderDetailModal" class="modal-overlay">
    <div class="detail-modal-box">
        <div style="padding: 0.85rem 1rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #ffffff;">
            <div>
                <span id="dtlCode" style="font-size: 0.88rem; font-weight: 800; color: #065f46;"></span>
                <span id="dtlStatus" style="font-size: 0.65rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; margin-left: 6px;"></span>
            </div>
            <button type="button" onclick="closeOrderDetail()" style="background: none; border: none; font-size: 1.15rem; color: #64748b; cursor: pointer; padding: 0;">✕</button>
        </div>

        <div style="padding: 1rem; overflow-y: auto; flex: 1; text-align: left; background: #f8fafc;">
            <div style="background: #ffffff; border: 1px solid #eef2f6; border-radius: 10px; padding: 0.75rem; margin-bottom: 0.75rem;">
                <span style="font-size: 0.62rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; display: block; margin-bottom: 4px;">Informasi Pemesan</span>
                <h4 id="dtlName" style="font-size: 0.9rem; font-weight: 800; color: #0f172a; margin: 0 0 2px;"></h4>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                    <span id="dtlDate" style="font-size: 0.7rem; color: #64748b;"></span>
                    <a id="dtlWaLink" href="#" target="_blank" style="display: none; align-items: center; gap: 3px; font-size: 0.68rem; font-weight: 700; color: #059669; text-decoration: none; background: #ecfdf5; padding: 2px 6px; border-radius: 4px; border: 1px solid #a7f3d0;">
                        💬 Hubungi WhatsApp
                    </a>
                </div>
            </div>

            <div style="background: #ffffff; border: 1px solid #eef2f6; border-radius: 10px; padding: 0.75rem; margin-bottom: 0.75rem;">
                <span style="font-size: 0.62rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; display: block; margin-bottom: 6px;">Daftar Barang yang Dipesan</span>
                <div id="dtlItemsContainer" style="display: flex; flex-direction: column; gap: 0.55rem;"></div>
            </div>

            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 10px; padding: 0.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                    <span style="font-size: 0.68rem; color: #047857; font-weight: 600;">Metode Pembayaran:</span>
                    <span id="dtlPayment" style="font-size: 0.72rem; font-weight: 800; color: #065f46;"></span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.75rem; color: #0f172a; font-weight: 700;">Total Pembayaran:</span>
                    <span id="dtlTotal" style="font-size: 1rem; font-weight: 800; color: #065f46;"></span>
                </div>
            </div>
        </div>

        <div style="padding: 0.75rem 1rem; background: #ffffff; border-top: 1px solid #f1f5f9; text-align: right;">
            <button type="button" onclick="closeOrderDetail()" style="width: 100%; height: 36px; background: #065f46; color: #ffffff; border: none; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">
                Tutup Rincian
            </button>
        </div>
    </div>
</div>

<script>
function showAppConfirm(title, message, targetUrl, btnLabel, btnColor) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalDesc').textContent = message;
    
    const actionBtn = document.getElementById('modalActionBtn');
    actionBtn.href = targetUrl;
    actionBtn.textContent = btnLabel || 'Lanjutkan';
    actionBtn.style.background = btnColor || '#dc2626';

    document.getElementById('customModal').style.display = 'flex';
}

function closeAppConfirm() {
    document.getElementById('customModal').style.display = 'none';
}

function openOrderDetail(data) {
    document.getElementById('dtlCode').textContent = data.code;
    const stEl = document.getElementById('dtlStatus');
    stEl.textContent = data.status;
    stEl.style.background = data.badgeBg;
    stEl.style.color = data.badgeColor;

    document.getElementById('dtlName').textContent = data.name;
    document.getElementById('dtlDate').textContent = '🕒 ' + data.date;
    document.getElementById('dtlPayment').textContent = data.payment;
    document.getElementById('dtlTotal').textContent = 'Rp ' + data.total;

    const waLink = document.getElementById('dtlWaLink');
    if (data.phone) {
        waLink.href = 'https://wa.me/' + data.waPhone;
        waLink.style.display = 'inline-flex';
    } else {
        waLink.style.display = 'none';
    }

    const container = document.getElementById('dtlItemsContainer');
    container.innerHTML = '';

    if (data.items && data.items.length > 0) {
        data.items.forEach(it => {
            const row = document.createElement('div');
            row.style.cssText = 'display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #f1f5f9; padding-bottom: 0.4rem;';
            
            const left = document.createElement('div');
            left.style.cssText = 'display: flex; align-items: center; gap: 0.5rem;';
            
            if (it.image) {
                const img = document.createElement('img');
                img.src = it.image;
                img.style.cssText = 'width: 32px; height: 32px; border-radius: 6px; object-fit: cover; border: 1px solid #e2e8f0;';
                left.appendChild(img);
            } else {
                const box = document.createElement('div');
                box.style.cssText = 'width: 32px; height: 32px; border-radius: 6px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;';
                box.textContent = '📦';
                left.appendChild(box);
            }

            const txtWrap = document.createElement('div');
            txtWrap.innerHTML = `<div style="font-size: 0.78rem; font-weight: 700; color: #1e293b;">${it.name}</div>
                                 <div style="font-size: 0.65rem; color: #64748b;">${it.qty} pcs x Rp ${Number(it.price).toLocaleString('id-ID')}</div>`;
            left.appendChild(txtWrap);

            const right = document.createElement('div');
            right.style.cssText = 'font-size: 0.78rem; font-weight: 800; color: #065f46;';
            right.textContent = 'Rp ' + Number(it.subtotal).toLocaleString('id-ID');

            row.appendChild(left);
            row.appendChild(right);
            container.appendChild(row);
        });
    } else {
        container.innerHTML = '<span style="font-size: 0.72rem; color: #94a3b8;">Tidak ada rincian item.</span>';
    }

    document.getElementById('orderDetailModal').style.display = 'flex';
}

function closeOrderDetail() {
    document.getElementById('orderDetailModal').style.display = 'none';
}
</script>

</body>
</html>