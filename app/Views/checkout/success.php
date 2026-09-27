<div style="padding: 1.5rem 1rem; max-width: 480px; margin: 0 auto; text-align: center;">
    <!-- Icon Berhasil -->
    <div style="width: 70px; height: 70px; background: #ecfdf5; color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 1rem auto; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);">
        ✓
    </div>

    <h2 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 0.35rem;">
        Booking Berhasil Dikonfirmasi!
    </h2>
    <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 1.25rem; line-height: 1.45;">
        Pesanan Anda telah diterima dan langsung disiapkan oleh pemilik toko UD. LAU Store.
    </p>

    <!-- Ringkasan Struk Transaksi -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; text-align: left; margin-bottom: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
        <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #cbd5e1; padding-bottom: 0.75rem; margin-bottom: 0.75rem;">
            <span style="font-size: 0.8rem; color: #64748b;">Kode Booking:</span>
            <strong style="font-size: 0.95rem; color: var(--primary);"><?= e($order['order_code']) ?></strong>
        </div>

        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.82rem;">
            <span style="color: #64748b;">Rencana Pengambilan:</span>
            <strong style="color: #0f172a;"><?= date('d M Y', strtotime($order['pickup_date'])) ?></strong>
        </div>

        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.82rem;">
            <span style="color: #64748b;">Metode Pembayaran:</span>
            <strong style="color: #0f172a;"><?= e($order['payment_method']) ?></strong>
        </div>

        <div style="display: flex; justify-content: space-between; padding-top: 0.75rem; border-top: 1px solid #f1f5f9; font-size: 0.95rem;">
            <span style="font-weight: 700; color: #0f172a;">Total Pembayaran:</span>
            <strong style="color: #059669; font-size: 1.15rem;">
                Rp <?= number_format((float)($order['total_amount'] ?? $order['total_price'] ?? 0), 0, ',', '.') ?>
            </strong>
        </div>
    </div>

    <!-- Kotak Petunjuk Dinamis Berdasarkan Metode Bayar -->
    <?php 
        $pm = strtolower((string)($order['payment_method'] ?? ''));
    ?>
    <?php if (strpos($pm, 'transfer') !== false): ?>
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 1rem; text-align: left; margin-bottom: 1.5rem;">
            <div style="font-size: 0.82rem; font-weight: 700; color: #1e40af; margin-bottom: 0.35rem;">
                🏦 Petunjuk Pembayaran Transfer Bank:
            </div>
            <p style="font-size: 0.76rem; color: #1e3a8a; margin: 0 0 0.5rem; line-height: 1.45;">
                Silakan transfer ke rekening resmi UD. LAU Store:<br>
                <strong>BCA: 123-456-7890</strong> a/n <strong>UD. LAU STORE</strong>
            </p>
            <span style="font-size: 0.72rem; color: #3b82f6; display: block;">
                *Tunjukkan bukti transfer dan kode booking saat mengambil barang di toko.
            </span>
        </div>
    <?php elseif (strpos($pm, 'qris') !== false): ?>
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 1rem; text-align: left; margin-bottom: 1.5rem;">
            <div style="font-size: 0.82rem; font-weight: 700; color: #1e40af; margin-bottom: 0.35rem;">
                📱 Petunjuk Pembayaran QRIS:
            </div>
            <p style="font-size: 0.76rem; color: #1e3a8a; margin: 0; line-height: 1.45;">
                Anda dapat melakukan scan QRIS UD. LAU Store di meja kasir saat pengambilan barang, atau menunjukkan bukti bayar QRIS jika sudah melakukan transaksi terlebih dahulu.
            </p>
        </div>
    <?php else: ?>
        <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; padding: 1rem; text-align: left; margin-bottom: 1.5rem;">
            <div style="font-size: 0.82rem; font-weight: 700; color: #b45309; margin-bottom: 0.35rem;">
                💵 Petunjuk Pembayaran Tunai (Cash):
            </div>
            <p style="font-size: 0.76rem; color: #92400e; margin: 0; line-height: 1.45;">
                Harap siapkan <strong>uang pas</strong> sejumlah <strong>Rp <?= number_format((float)($order['total_amount'] ?? $order['total_price'] ?? 0), 0, ',', '.') ?></strong> dan sebutkan <strong>Kode Booking</strong> kepada kasir saat mengambil pesanan.
            </p>
        </div>
    <?php endif; ?>

    <!-- Tombol Aksi Navigasi -->
    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        <a href="<?= base_url('orders') ?>" class="btn-primary" style="display: block; padding: 0.8rem; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 0.88rem; text-align: center; background: var(--primary); color: #fff;">
            Lihat Daftar Riwayat Pesanan
        </a>
        <a href="<?= base_url('') ?>" style="display: block; padding: 0.65rem; color: #64748b; text-decoration: none; font-size: 0.82rem; font-weight: 600;">
            Kembali ke Beranda
        </a>
    </div>
</div>