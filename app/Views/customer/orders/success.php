<div style="padding: 1.5rem 1rem; max-width: 480px; margin: 0 auto; text-align: center;">
    <div style="width: 68px; height: 68px; background: #ecfdf5; color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 1rem auto; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);">
        ✓
    </div>

    <h2 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 0.35rem;">
        Pesanan Berhasil Dibuat!
    </h2>
    <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 1.5rem; line-height: 1.45;">
        Booking Anda telah masuk ke sistem dan langsung dipersiapkan oleh toko UD. LAU Store.
    </p>

    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; text-align: left; margin-bottom: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #cbd5e1; padding-bottom: 0.75rem; margin-bottom: 0.75rem;">
            <span style="font-size: 0.8rem; color: #64748b;">Nomor Booking</span>
            <strong style="font-size: 0.9rem; color: var(--primary);"><?= e($order['order_code']) ?></strong>
        </div>

        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.82rem;">
            <span style="color: #64748b;">Rencana Pickup:</span>
            <strong style="color: #0f172a;"><?= date('d M Y', strtotime($order['pickup_date'])) ?></strong>
        </div>

        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.82rem;">
            <span style="color: #64748b;">Metode Bayar:</span>
            <strong style="color: #0f172a;"><?= e($order['payment_method']) ?></strong>
        </div>

        <div style="display: flex; justify-content: space-between; padding-top: 0.75rem; border-top: 1px solid #f1f5f9; font-size: 0.95rem;">
            <span style="font-weight: 700; color: #0f172a;">Total Tagihan:</span>
            <strong style="color: #059669; font-size: 1.15rem;">
                Rp <?= number_format((float)($order['total_amount'] ?? $order['total_price'] ?? 0), 0, ',', '.') ?>
            </strong>
        </div>
    </div>

    <!-- Kotak Pesan Shopee/Lazada -->
    <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; padding: 0.9rem; text-align: left; margin-bottom: 1.5rem;">
        <div style="font-size: 0.82rem; font-weight: 700; color: #b45309; margin-bottom: 0.25rem;">
            💡 Instruksi Pengambilan di Toko:
        </div>
        <p style="font-size: 0.76rem; color: #92400e; margin: 0; line-height: 1.45;">
            Tunjukkan Kode Booking di atas kepada kasir toko UD. LAU Store. 
            <strong>Jangan lupa membawa uang pas</strong> sesuai nominal tagihan saat mengambil barang.
        </p>
    </div>

    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        <a href="<?= base_url('orders') ?>" class="btn-primary" style="display: block; padding: 0.8rem; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 0.88rem; text-align: center; background: var(--primary); color: #fff;">
            Lihat Riwayat Pesanan
        </a>
        <a href="<?= base_url('') ?>" style="display: block; padding: 0.65rem; color: #64748b; text-decoration: none; font-size: 0.82rem; font-weight: 600;">
            Kembali ke Beranda
        </a>
    </div>
</div>