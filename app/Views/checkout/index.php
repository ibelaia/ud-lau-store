<div class="cart-wrapper" style="padding-bottom: 2rem;">
    <!-- Judul Halaman -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2 style="font-size: 1.15rem; font-weight: 800; color: var(--dark); margin: 0;">Keranjang Belanja</h2>
        <span style="font-size: 0.78rem; color: var(--muted); font-weight: 600;">
            <?= count($items ?? []) ?> Produk
        </span>
    </div>

    <?php if (empty($items)): ?>
        <div style="text-align: center; padding: 3rem 1.5rem; background: #fff; border-radius: var(--radius); border: 1px dashed var(--border); margin-top: 1rem;">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">🛒</div>
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--dark); margin-bottom: 0.35rem;">Keranjang Masih Kosong</h3>
            <p style="font-size: 0.8rem; color: var(--muted); line-height: 1.4; margin-bottom: 1.25rem;">
                Belum ada perlengkapan ATK atau kebutuhan sekolah yang Anda masukkan.
            </p>
            <a href="<?= base_url('products') ?>" class="btn-primary" style="display: inline-block; width: auto; padding: 0.6rem 1.25rem; font-size: 0.85rem; font-weight: 700; text-decoration: none; border-radius: 8px;">
                Mulai Belanja Sekarang
            </a>
        </div>
    <?php else: ?>
        <!-- Daftar Produk di Keranjang -->
        <div class="cart-items-list" style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
            <?php foreach ($items as $item): ?>
                <div class="cart-card" style="background: #ffffff; border: 1px solid var(--border); border-radius: var(--radius); padding: 0.85rem; display: flex; gap: 0.85rem; align-items: center; box-shadow: var(--shadow-sm);">
                    <div style="width: 65px; height: 65px; border-radius: 8px; background: #f1f5f9; overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border);">
                        <?php if (!empty($item['image_path'])): ?>
                            <img src="<?= upload_url($item['image_path']) ?>" alt="<?= e($item['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <span style="font-size: 1.4rem;">📦</span>
                        <?php endif; ?>
                    </div>

                    <div style="flex: 1; min-width: 0;">
                        <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--dark); margin: 0 0 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?= e($item['name']) ?>
                        </h4>
                        <div style="font-size: 0.9rem; font-weight: 800; color: var(--primary); margin-bottom: 0.35rem;">
                            Rp <?= number_format((float)$item['price'], 0, ',', '.') ?>
                        </div>
                        <div style="font-size: 0.72rem; color: var(--muted);">
                            Jumlah: <strong><?= (int)$item['qty'] ?></strong> pcs
                        </div>
                    </div>

                    <div style="text-align: right; flex-shrink: 0;">
                        <span style="font-size: 0.88rem; font-weight: 800; color: var(--dark);">
                            Rp <?= number_format((float)($item['price'] * $item['qty']), 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" action="<?= base_url('checkout') ?>">
            <?= csrf_field() ?>
            
            <!-- Box Informasi Pickup Toko -->
            <div style="background: #ffffff; border: 1px solid var(--border); border-radius: var(--radius); padding: 1rem; margin-bottom: 1rem; box-shadow: var(--shadow-sm);">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <span style="font-size: 1.1rem;">🏪</span>
                    <h3 style="font-size: 0.9rem; font-weight: 700; color: var(--dark); margin: 0;">Jadwal Pengambilan (Pickup)</h3>
                </div>

                <div style="margin-bottom: 0.75rem;">
                    <label for="pickup_date" style="display: block; font-size: 0.78rem; font-weight: 600; color: var(--dark); margin-bottom: 0.35rem;">
                        Rencana Tanggal Pengambilan
                    </label>
                    <input type="date" id="pickup_date" name="pickup_date" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.85rem; outline: none; font-family: inherit; box-sizing: border-box;">
                </div>

                <div style="background: var(--primary-light); padding: 0.6rem 0.8rem; border-radius: 6px;">
                    <p style="font-size: 0.72rem; color: #065f46; margin: 0; line-height: 1.4;">
                        ℹ️ Barang akan langsung disiapkan oleh pemilik toko. Anda cukup datang ke toko UD. LAU dan menunjukkan nomor pesanan.
                    </p>
                </div>
            </div>

            <!-- Box Metode Pembayaran Dinamis -->
            <div style="background: #ffffff; border: 1px solid var(--border); border-radius: var(--radius); padding: 1rem; margin-bottom: 1.25rem; box-shadow: var(--shadow-sm);">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <span style="font-size: 1.1rem;">💳</span>
                    <h3 style="font-size: 0.9rem; font-weight: 700; color: var(--dark); margin: 0;">Pilih Metode Pembayaran</h3>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <?php 
                        $firstChecked = false;
                        $hasAvailable = false;
                        foreach ($paymentMethods as $pm):
                            $active = (int)($pm['is_active'] ?? 1) === 1;
                            if ($active) $hasAvailable = true;
                            $isChecked = false;
                            if ($active && !$firstChecked) {
                                $isChecked = true;
                                $firstChecked = true;
                            }
                    ?>
                        <label style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem 0.85rem; border: 1px solid <?= $active ? 'var(--border)' : '#e2e8f0' ?>; border-radius: 8px; <?= $active ? 'cursor: pointer;' : 'cursor: not-allowed; background: #f8fafc; opacity: 0.6;' ?> font-size: 0.85rem;">
                            <input type="radio" name="payment_method" value="<?= (int)$pm['id'] ?>" <?= $isChecked ? 'checked' : '' ?> <?= !$active ? 'disabled' : '' ?> style="margin-top: 3px; accent-color: var(--primary);">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: <?= $active ? 'var(--dark)' : '#64748b' ?>;">
                                    <?= e($pm['name']) ?>
                                </div>
                                <?php if (!$active): ?>
                                    <span style="display: inline-block; font-size: 0.72rem; color: #dc2626; font-weight: 600; margin-top: 2px;">
                                        ⚠️ Tidak tersedia untuk saat ini
                                    </span>
                                <?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; ?>

                    <?php if (!$hasAvailable): ?>
                        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 0.75rem; border-radius: 8px; font-size: 0.78rem; text-align: center; font-weight: 600;">
                            Saat ini toko belum mengaktifkan metode pembayaran apa pun. Silakan hubungi kasir/pemilik toko.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Rincian Pembayaran & Tombol Aksi -->
            <div style="background: #ffffff; border: 1px solid var(--border); border-radius: var(--radius); padding: 1rem; box-shadow: var(--shadow-sm);">
                <div style="display: flex; justify-content: space-between; font-size: 0.82rem; color: var(--muted); margin-bottom: 0.4rem;">
                    <span>Total Barang:</span>
                    <span><?= count($items) ?> item</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 1rem; font-weight: 800; color: var(--dark); margin-bottom: 1rem; padding-top: 0.5rem; border-top: 1px dashed var(--border);">
                    <span>Total Tagihan:</span>
                    <span style="color: var(--primary);">Rp <?= number_format((float)($total ?? 0), 0, ',', '.') ?></span>
                </div>

                <button type="submit" class="btn-primary" <?= !$hasAvailable ? 'disabled style="width: 100%; padding: 0.85rem; background: #cbd5e1; color: #64748b; border: none; border-radius: 8px; font-size: 0.95rem; font-weight: 700; cursor: not-allowed;"' : 'style="width: 100%; padding: 0.85rem; background: var(--primary); color: #fff; border: none; border-radius: 8px; font-size: 0.95rem; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 124, 65, 0.2);"' ?>>
                    Konfirmasi Booking Pesanan &rarr;
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>