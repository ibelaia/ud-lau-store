<?php
$st = strtolower($order['status'] ?? 'menunggu');
$statusLabel = 'Menunggu Konfirmasi';
$badgeBg = '#fef3c7';
$badgeColor = '#d97706';

if (in_array($st, ['siap', 'ready'])) {
    $statusLabel = 'Barang Siap Diambil di Toko';
    $badgeBg = '#dbeafe';
    $badgeColor = '#2563eb';
} elseif (in_array($st, ['selesai', 'completed'])) {
    $statusLabel = 'Selesai (Sudah Diambil)';
    $badgeBg = '#d1fae5';
    $badgeColor = '#059669';
} elseif (in_array($st, ['dibatalkan', 'cancelled'])) {
    $statusLabel = 'Pesanan Dibatalkan';
    $badgeBg = '#fee2e2';
    $badgeColor = '#dc2626';
}
?>

<div style="max-width: 480px; margin: 0 auto; padding-bottom: 30px;">
    
    <!-- Topbar Navigasi -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
        <a href="<?= base_url('orders') ?>" style="width: 34px; height: 34px; border-radius: 50%; background: #ffffff; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #0f172a; font-weight: 800;">
            &larr;
        </a>
        <span style="font-size: 0.85rem; font-weight: 800; color: #0f172a;">Detail Nota Booking</span>
        <div style="width: 34px;"></div>
    </div>

    <!-- Status Banner -->
    <div style="background: <?= $badgeBg ?>; border-radius: 14px; padding: 0.85rem 1rem; margin-bottom: 1rem; text-align: center;">
        <span style="font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 2px;">Status Saat Ini</span>
        <span style="font-size: 0.95rem; font-weight: 800; color: <?= $badgeColor ?>;">
            <?= $statusLabel ?>
        </span>
    </div>

    <!-- Kartu Informasi Booking -->
    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 1.15rem; margin-bottom: 1rem; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
        <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 0.75rem; margin-bottom: 0.75rem;">
            <span style="font-size: 0.75rem; color: #64748b;">Nomor Booking:</span>
            <span style="font-size: 0.88rem; font-weight: 800; color: #065f46;"><?= e($order['order_code'] ?? $order['booking_code'] ?? '') ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.75rem;">
            <span style="color: #64748b;">Waktu Pemesanan:</span>
            <span style="font-weight: 700; color: #1e293b;"><?= !empty($order['created_at']) ? date('d M Y, H:i', strtotime($order['created_at'])) : '-' ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.75rem;">
            <span style="color: #64748b;">Rencana Pengambilan:</span>
            <span style="font-weight: 800; color: #0f172a;"><?= !empty($order['pickup_date']) ? date('d M Y', strtotime($order['pickup_date'])) : '-' ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 0.75rem;">
            <span style="color: #64748b;">Metode Pembayaran:</span>
            <span style="font-weight: 700; color: #1e293b;"><?= e($order['payment_method'] ?? 'Bayar Tunai di Toko (Cash)') ?></span>
        </div>
    </div>

    <!-- Rincian Item Barang -->
    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 1.15rem; margin-bottom: 1rem; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
        <h3 style="font-size: 0.85rem; font-weight: 800; color: #0f172a; margin: 0 0 0.85rem;">Barang yang Dipesan</h3>
        
        <div style="display: flex; flex-direction: column; gap: 0.75rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
            <?php foreach ($items as $it): ?>
                <?php 
                    $qty = (int)($it['quantity'] ?? $it['qty'] ?? 1); 
                    $price = (float)($it['price'] ?? 0);
                ?>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 0.8rem; font-weight: 700; color: #1e293b;"><?= e($it['product_name'] ?? 'Produk ATK') ?></div>
                        <div style="font-size: 0.68rem; color: #94a3b8;"><?= $qty ?> x Rp <?= number_format($price, 0, ',', '.') ?></div>
                    </div>
                    <span style="font-size: 0.82rem; font-weight: 800; color: #0f172a;">
                        Rp <?= number_format($qty * $price, 0, ',', '.') ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.85rem;">
            <span style="font-size: 0.85rem; font-weight: 800; color: #0f172a;">Total Tagihan:</span>
            <span style="font-size: 1.15rem; font-weight: 800; color: #065f46;">
                Rp <?= number_format((float)($order['total_amount'] ?? $order['total_price'] ?? 0), 0, ',', '.') ?>
            </span>
        </div>
    </div>

    <!-- Petunjuk Pengambilan -->
    <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 14px; padding: 0.85rem 1rem; margin-bottom: 1.25rem;">
        <h4 style="margin: 0 0 4px; font-size: 0.78rem; font-weight: 800; color: #92400e;">💡 Petunjuk Pengambilan:</h4>
        <p style="margin: 0; font-size: 0.72rem; color: #78350f; line-height: 1.5;">
            Tunjukkan Kode Booking <b><?= e($order['order_code'] ?? '') ?></b> ke kasir toko UD. LAU Store saat mengambil barang. Siapkan uang tunai sesuai total tagihan.
        </p>
    </div>

    <!-- Tombol Hubungi Toko -->
    <a href="<?= base_url('chat') ?>" style="display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; height: 42px; background: #065f46; color: #ffffff; border-radius: 12px; font-size: 0.82rem; font-weight: 700; text-decoration: none; box-shadow: 0 2px 6px rgba(6,95,70,0.25);">
        💬 Chat Pemilik Toko
    </a>

</div>