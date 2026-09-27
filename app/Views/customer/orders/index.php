<div class="customer-orders-wrapper" style="max-width: 480px; margin: 0 auto; padding-bottom: 3.5rem;">

    <!-- Header Halaman -->
    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
        <a href="<?= base_url('products') ?>" style="width: 36px; height: 36px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #1e293b; font-weight: 700;">
            &larr;
        </a>
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">Riwayat Pesanan & Booking</h2>
            <p style="font-size: 0.72rem; color: #64748b; margin: 0.15rem 0 0;">Daftar pengambilan barang Anda di UD. LAU Store</p>
        </div>
    </div>

    <?php 
        $successCode = $_GET['code'] ?? null;
        $latestOrder = null;
        if (!empty($successCode) && !empty($orders)) {
            foreach ($orders as $o) {
                if (($o['order_code'] ?? '') === $successCode) {
                    $latestOrder = $o;
                    break;
                }
            }
            if (!$latestOrder) {
                $latestOrder = $orders[0] ?? null;
            }
        }
    ?>

    <!-- Banner Notifikasi Sukses Setelah Checkout -->
    <?php if (isset($_GET['success']) && $latestOrder): ?>
        <?php 
            $pm = strtolower((string)($latestOrder['payment_method'] ?? ''));
        ?>
        <div style="background: #ffffff; border: 2px solid #10b981; border-radius: 16px; padding: 1.25rem; margin-bottom: 1.5rem; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.15);">
            <div style="text-align: center; margin-bottom: 1rem;">
                <div style="width: 52px; height: 52px; background: #ecfdf5; color: #10b981; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 0.5rem;">
                    ✓
                </div>
                <h3 style="font-size: 1.1rem; font-weight: 800; color: #065f46; margin: 0 0 0.25rem;">Booking Berhasil Dikonfirmasi!</h3>
                <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Pesanan Anda telah masuk dan langsung disiapkan pemilik toko.</p>
            </div>

            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 0.85rem; margin-bottom: 1rem; font-size: 0.8rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                    <span style="color: #64748b;">Nomor Booking:</span>
                    <strong style="color: #065f46;"><?= e($latestOrder['order_code']) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                    <span style="color: #64748b;">Jadwal Ambil:</span>
                    <strong><?= e($latestOrder['pickup_date']) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                    <span style="color: #64748b;">Metode Bayar:</span>
                    <strong><?= e($latestOrder['payment_method']) ?></strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding-top: 0.4rem; border-top: 1px solid #e2e8f0; font-size: 0.9rem;">
                    <span style="font-weight: 700; color: #0f172a;">Total Tagihan:</span>
                    <strong style="color: #059669; font-size: 1.05rem;">
                        Rp <?= number_format((float)($latestOrder['total_amount'] ?? 0), 0, ',', '.') ?>
                    </strong>
                </div>
            </div>

            <?php if (str_contains($pm, 'transfer')): ?>
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 0.75rem; font-size: 0.75rem; color: #1e3a8a; line-height: 1.45;">
                    <strong>🏦 Pembayaran Transfer Bank:</strong><br>
                    Silakan transfer ke <strong>BCA: 123-456-7890</strong> a/n <strong>UD. LAU STORE</strong> dan tunjukkan bukti transfer saat mengambil barang di toko.
                </div>
            <?php elseif (str_contains($pm, 'qris')): ?>
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 0.75rem; font-size: 0.75rem; color: #1e3a8a; line-height: 1.45;">
                    <strong>📱 Pembayaran QRIS:</strong><br>
                    Silakan tunjukkan Kode Booking dan lakukan scan kode QRIS resmi toko di meja kasir saat mengambil barang.
                </div>
            <?php else: ?>
                <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 0.75rem; font-size: 0.75rem; color: #92400e; line-height: 1.45;">
                    <strong>💵 Pembayaran Tunai di Toko (Cash):</strong><br>
                    Harap <strong>membawa uang pas</strong> sejumlah <strong>Rp <?= number_format((float)($latestOrder['total_amount'] ?? 0), 0, ',', '.') ?></strong> dan tunjukkan nomor booking di atas ke kasir.
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Tab Filter Status -->
    <div style="display: flex; gap: 0.4rem; overflow-x: auto; padding-bottom: 0.65rem; margin-bottom: 0.85rem; scrollbar-width: none; -webkit-overflow-scrolling: touch;">
        <button type="button" onclick="filterStatus('all')" class="status-tab active" data-status="all" style="white-space: nowrap; padding: 5px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; border: 1px solid #cbd5e1; background: #065f46; color: #ffffff; cursor: pointer;">
            Semua
        </button>
        <button type="button" onclick="filterStatus('menunggu')" class="status-tab" data-status="menunggu" style="white-space: nowrap; padding: 5px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; border: 1px solid #cbd5e1; background: #ffffff; color: #64748b; cursor: pointer;">
            Menunggu
        </button>
        <button type="button" onclick="filterStatus('siap')" class="status-tab" data-status="siap" style="white-space: nowrap; padding: 5px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; border: 1px solid #cbd5e1; background: #ffffff; color: #64748b; cursor: pointer;">
            Siap Diambil
        </button>
        <button type="button" onclick="filterStatus('selesai')" class="status-tab" data-status="selesai" style="white-space: nowrap; padding: 5px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; border: 1px solid #cbd5e1; background: #ffffff; color: #64748b; cursor: pointer;">
            Selesai
        </button>
        <button type="button" onclick="filterStatus('batal')" class="status-tab" data-status="batal" style="white-space: nowrap; padding: 5px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; border: 1px solid #cbd5e1; background: #ffffff; color: #64748b; cursor: pointer;">
            Dibatalkan
        </button>
    </div>

    <!-- Kontainer List Pesanan -->
    <?php if (empty($orders)): ?>
        <div style="text-align: center; padding: 3rem 1.5rem; background: #ffffff; border-radius: 16px; border: 1px dashed #cbd5e1; margin-top: 0.5rem;">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">📦</div>
            <h4 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 0.35rem;">Belum Ada Booking Aktif</h4>
            <p style="font-size: 0.78rem; color: #64748b; line-height: 1.4; margin-bottom: 1.25rem;">
                Pilih kebutuhan ATK Anda dari katalog kami, lalu ambil langsung di toko fisik tanpa repot antre.
            </p>
            <a href="<?= base_url('products') ?>" style="display: inline-block; padding: 0.65rem 1.35rem; font-size: 0.82rem; font-weight: 700; text-decoration: none; border-radius: 10px; background: #065f46; color: #fff;">
                Mulai Belanja Sekarang
            </a>
        </div>
    <?php else: ?>
        <div id="orderListContainer" style="display: flex; flex-direction: column; gap: 0.85rem;">
            <?php foreach ($orders as $order): ?>
                <?php
                    $st = strtolower((string)($order['status_raw'] ?? 'menunggu'));
                    
                    $group = 'menunggu';
                    $badgeBg = '#fef3c7';
                    $badgeColor = '#b45309';

                    if (in_array($st, ['siap', 'ready'])) {
                        $group = 'siap';
                        $badgeBg = '#dbeafe';
                        $badgeColor = '#1d4ed8';
                    } elseif (in_array($st, ['selesai', 'completed'])) {
                        $group = 'selesai';
                        $badgeBg = '#d1fae5';
                        $badgeColor = '#065f46';
                    } elseif (in_array($st, ['dibatalkan', 'cancelled', 'batal'])) {
                        $group = 'batal';
                        $badgeBg = '#fee2e2';
                        $badgeColor = '#dc2626';
                    }
                ?>
                <a href="<?= base_url('orders/' . $order['id']) ?>" class="order-item-card" data-order-id="<?= (int)$order['id'] ?>" data-group="<?= $group ?>" style="display: block; background: #ffffff; border: 1px solid #f1f5f9; border-radius: 14px; padding: 1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.03); text-decoration: none; transition: transform 0.15s;">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.65rem; border-bottom: 1px solid #f8fafc; margin-bottom: 0.65rem;">
                        <div>
                            <span style="font-size: 0.84rem; font-weight: 800; color: #0f172a; display: block; letter-spacing: -0.2px;"><?= e($order['order_code']) ?></span>
                            <span style="font-size: 0.68rem; color: #94a3b8;"><?= e($order['created_at']) ?></span>
                        </div>
                        <span class="status-badge-indicator" style="font-size: 0.68rem; font-weight: 700; padding: 4px 10px; border-radius: 6px; background: <?= $badgeBg ?>; color: <?= $badgeColor ?>;">
                            <?= e($order['order_status']) ?>
                        </span>
                    </div>

                    <div style="margin-bottom: 0.75rem;">
                        <p style="font-size: 0.8rem; font-weight: 600; color: #334155; margin: 0; line-height: 1.4;">
                            🛍️ <?= e($order['items_summary']) ?>
                        </p>
                        <span style="font-size: 0.7rem; color: #64748b; margin-top: 0.3rem; display: block;">
                            Jadwal Ambil: <strong style="color: #0f172a;"><?= e($order['pickup_date']) ?></strong>
                        </span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.65rem; border-top: 1px dashed #e2e8f0;">
                        <div>
                            <span style="font-size: 0.68rem; color: #94a3b8; display: block;">Total Bayar (<?= e($order['payment_method']) ?>)</span>
                            <span style="font-size: 0.95rem; font-weight: 800; color: #059669;">
                                Rp <?= number_format((float)($order['total_amount'] ?? 0), 0, ',', '.') ?>
                            </span>
                        </div>
                        <div style="font-size: 0.7rem; font-weight: 700; color: #065f46; background: #ecfdf5; padding: 4px 8px; border-radius: 6px; border: 1px solid #a7f3d0;">
                            Lihat Nota &rarr;
                        </div>
                    </div>

                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function filterStatus(status) {
    const tabs = document.querySelectorAll('.status-tab');
    tabs.forEach(t => {
        if (t.dataset.status === status) {
            t.style.background = '#065f46';
            t.style.color = '#ffffff';
            t.style.borderColor = '#065f46';
        } else {
            t.style.background = '#ffffff';
            t.style.color = '#64748b';
            t.style.borderColor = '#cbd5e1';
        }
    });

    const cards = document.querySelectorAll('.order-item-card');
    cards.forEach(card => {
        if (status === 'all' || card.dataset.group === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Auto-polling status pesanan real-time tanpa refresh
const statusTextConfig = {
    'menunggu':   { text: 'Menunggu Konfirmasi', bg: '#fef3c7', color: '#b45309', group: 'menunggu' },
    'pending':    { text: 'Menunggu Konfirmasi', bg: '#fef3c7', color: '#b45309', group: 'menunggu' },
    'siap':       { text: 'Siap Diambil',        bg: '#dbeafe', color: '#1d4ed8', group: 'siap' },
    'ready':      { text: 'Siap Diambil',        bg: '#dbeafe', color: '#1d4ed8', group: 'siap' },
    'selesai':    { text: 'Selesai',             bg: '#d1fae5', color: '#065f46', group: 'selesai' },
    'completed':  { text: 'Selesai',             bg: '#d1fae5', color: '#065f46', group: 'selesai' },
    'dibatalkan': { text: 'Dibatalkan',          bg: '#fee2e2', color: '#dc2626', group: 'batal' },
    'cancelled':  { text: 'Dibatalkan',          bg: '#fee2e2', color: '#dc2626', group: 'batal' }
};

setInterval(function() {
    fetch('<?= base_url('orders/check-statuses') ?>', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        if (!data || typeof data !== 'object') return;

        document.querySelectorAll('.order-item-card').forEach(card => {
            const orderId = parseInt(card.dataset.orderId);
            if (data[orderId]) {
                const currentStatus = data[orderId];
                const badge = card.querySelector('.status-badge-indicator');
                const conf = statusTextConfig[currentStatus];

                if (badge && conf && badge.textContent.trim() !== conf.text) {
                    badge.textContent = conf.text;
                    badge.style.background = conf.bg;
                    badge.style.color = conf.color;
                    card.dataset.group = conf.group;
                }
            }
        });
    })
    .catch(err => console.error('Status sync error:', err));
}, 3000);
</script>