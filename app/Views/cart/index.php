<div class="cart-wrapper" style="padding-bottom: 2rem;">
    <!-- Header Keranjang -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">Keranjang Booking 🛒</h2>
        <span style="font-size: 0.78rem; color: #64748b; font-weight: 600;">
            <?= count($items ?? []) ?> Produk
        </span>
    </div>

    <?php if (empty($items)): ?>
        <div style="text-align: center; padding: 3rem 1.5rem; background: #ffffff; border-radius: 14px; border: 1px dashed #cbd5e1; margin-top: 0.5rem;">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">🛒</div>
            <h4 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 0.35rem;">Keranjang Masih Kosong</h4>
            <p style="font-size: 0.78rem; color: #64748b; line-height: 1.4; margin-bottom: 1.25rem;">
                Belum ada produk yang Anda pilih. Silakan kunjungi katalog untuk memilih barang.
            </p>
            <a href="<?= base_url('products') ?>" style="display: inline-block; padding: 0.6rem 1.25rem; font-size: 0.85rem; font-weight: 700; text-decoration: none; border-radius: 8px; background: var(--primary); color: #fff;">
                Mulai Belanja Sekarang
            </a>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
            <?php foreach ($items as $item): ?>
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 0.85rem; display: flex; gap: 0.85rem; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                    
                    <div style="width: 55px; height: 55px; border-radius: 8px; background: #f8fafc; overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; font-size: 1.5rem;">
                        <?= $item['image'] ?>
                    </div>

                    <div style="flex: 1; min-width: 0;">
                        <h4 style="font-size: 0.85rem; font-weight: 700; color: #0f172a; margin: 0 0 0.25rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?= e($item['name']) ?>
                        </h4>
                        <div style="font-size: 0.85rem; font-weight: 800; color: var(--primary); margin-bottom: 0.4rem;">
                            Rp <?= number_format((float)$item['price'], 0, ',', '.') ?>
                        </div>

                        <!-- Tombol Tambah / Kurang Qty -->
                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                            <form method="POST" action="<?= base_url('cart/add') ?>" style="margin: 0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                <input type="hidden" name="quantity" value="-1">
                                <button type="submit" style="width: 26px; height: 26px; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 6px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; line-height: 1;">-</button>
                            </form>

                            <span style="font-size: 0.85rem; font-weight: 700; min-width: 24px; text-align: center; color: #0f172a;">
                                <?= (int) $item['quantity'] ?>
                            </span>

                            <form method="POST" action="<?= base_url('cart/add') ?>" style="margin: 0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" style="width: 26px; height: 26px; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 6px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; line-height: 1;">+</button>
                            </form>
                        </div>
                    </div>

                    <div style="text-align: right; flex-shrink: 0;">
                        <span style="font-size: 0.9rem; font-weight: 800; color: #0f172a; display: block; margin-bottom: 0.35rem;">
                            Rp <?= number_format((float)$item['subtotal'], 0, ',', '.') ?>
                        </span>
                        <a href="<?= base_url('cart/remove/' . $item['id']) ?>" style="color: #ef4444; font-size: 0.72rem; text-decoration: none; font-weight: 600;">
                            Hapus
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div style="display: flex; justify-content: space-between; font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 1rem;">
                <span>Total Pembayaran:</span>
                <span style="color: var(--primary);">Rp <?= number_format((float)($totalPrice ?? 0), 0, ',', '.') ?></span>
            </div>

            <a href="<?= base_url('checkout') ?>" style="display: block; width: 100%; padding: 0.85rem; background: var(--primary); color: #fff; text-align: center; border-radius: 8px; font-size: 0.95rem; font-weight: 700; text-decoration: none; box-sizing: border-box;">
                Lanjut Booking Pickup
            </a>
        </div>
    <?php endif; ?>
</div>