<div class="cart-container" style="padding-bottom: 5.5rem;">
    <!-- Header Halaman -->
    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
        <a href="<?= base_url('products') ?>" style="width: 36px; height: 36px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #1e293b; font-weight: 700;">
            &larr;
        </a>
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">Keranjang Booking</h2>
            <p style="font-size: 0.72rem; color: #64748b; margin: 0.15rem 0 0;">Periksa daftar ATK sebelum membuat pesanan pickup</p>
        </div>
    </div>

    <?php if (empty($items)): ?>
        <!-- Keranjang Kosong -->
        <div style="text-align: center; padding: 3.5rem 1.5rem; background: #ffffff; border-radius: 16px; border: 1px dashed #cbd5e1; margin-top: 0.5rem;">
            <div style="font-size: 3rem; margin-bottom: 0.5rem;">🛒</div>
            <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 0.35rem;">Keranjang Masih Kosong</h3>
            <p style="font-size: 0.78rem; color: #64748b; line-height: 1.4; margin-bottom: 1.25rem;">
                Belum ada produk ATK yang dipilih. Jelajahi katalog toko kami untuk memesan kebutuhan Anda.
            </p>
            <a href="<?= base_url('products') ?>" style="display: inline-block; padding: 0.65rem 1.35rem; font-size: 0.82rem; font-weight: 700; text-decoration: none; border-radius: 10px; background: #065f46; color: #fff;">
                Mulai Belanja
            </a>
        </div>
    <?php else: ?>
        <!-- Daftar Barang di Keranjang -->
        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
            <?php 
            $subtotal = 0;
            foreach ($items as $item): 
                $lineTotal = (float)$item['price'] * (int)$item['qty'];
                $subtotal += $lineTotal;
            ?>
                <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 14px; padding: 0.9rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                    
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 0;">
                        <div style="width: 48px; height: 48px; border-radius: 10px; background: #f8fafc; border: 1px solid #eef2f6; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                            <?php if (!empty($item['image'])): ?>
                                <img src="<?= upload_url($item['image']) ?>" alt="<?= e($item['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <span style="font-size: 1.4rem;">📦</span>
                            <?php endif; ?>
                        </div>
                        <div style="min-width: 0;">
                            <h4 style="font-size: 0.85rem; font-weight: 700; color: #0f172a; margin: 0 0 0.2rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= e($item['name']) ?>
                            </h4>
                            <div style="font-size: 0.75rem; color: #64748b;">
                                Rp <?= number_format((float)$item['price'], 0, ',', '.') ?> &times; <?= (int)$item['qty'] ?>
                            </div>
                            <span style="font-size: 0.85rem; font-weight: 800; color: #065f46; display: block; margin-top: 0.15rem;">
                                Rp <?= number_format($lineTotal, 0, ',', '.') ?>
                            </span>
                        </div>
                    </div>

                    <!-- Tombol Hapus Item -->
                    <form method="POST" action="<?= base_url('cart/remove') ?>" style="margin: 0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>">
                        <button type="submit" title="Hapus" style="background: #fee2e2; border: none; color: #dc2626; width: 32px; height: 32px; border-radius: 8px; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                            ✕
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Info Ambil di Toko -->
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 0.85rem; margin-bottom: 1.25rem;">
            <div style="display: flex; gap: 0.5rem; align-items: flex-start;">
                <span style="font-size: 1.1rem;">🏪</span>
                <div>
                    <strong style="font-size: 0.8rem; color: #166534; display: block;">Metode: Ambil Sendiri di Toko</strong>
                    <span style="font-size: 0.72rem; color: #15803d; line-height: 1.35; display: block; margin-top: 0.15rem;">
                        Barang disiapkan staf UD. LAU Store. Anda tinggal bayar/tunjukkan kode booking di kasir. Bebas ongkir!
                    </span>
                </div>
            </div>
        </div>

        <!-- Floating Bottom Bar: Total & Lanjut Booking -->
        <div style="position: fixed; bottom: 64px; left: 50%; transform: translateX(-50%); width: 100%; max-width: 480px; background: #ffffff; border-top: 1px solid #e2e8f0; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; z-index: 90; box-shadow: 0 -4px 12px rgba(0,0,0,0.04);">
            <div>
                <span style="font-size: 0.68rem; color: #64748b; display: block; font-weight: 600;">Total Tagihan:</span>
                <span style="font-size: 1.15rem; font-weight: 800; color: #065f46;">
                    Rp <?= number_format((float)($total ?? $subtotal), 0, ',', '.') ?>
                </span>
            </div>

            <a href="<?= base_url('checkout') ?>" style="background: #065f46; color: #ffffff; text-decoration: none; padding: 0.65rem 1.25rem; border-radius: 10px; font-size: 0.82rem; font-weight: 700; box-shadow: 0 4px 12px rgba(6, 95, 70, 0.25);">
                Lanjut Booking &rarr;
            </a>
        </div>
    <?php endif; ?>
</div>