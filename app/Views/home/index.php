<!-- Hero Banner Slider -->
<section class="banner-carousel">
    <div class="promo-card">
        <div class="promo-badge">PESAN & AMBIL DI TOKO</div>
        <h2>Kebutuhan Sekolah & Kantor Lengkap</h2>
        <p>Pesan online dari HP, bayar & ambil langsung tanpa antre di toko UD. LAU.</p>
        <div class="promo-features">
            <span>⚡ Siap Cepat</span>
            <span>💰 Harga Murah</span>
            <span>🛡️ Asli & Berkualitas</span>
        </div>
    </div>
</section>

<!-- Kategori Pilihan -->
<section class="home-section">
    <div class="section-title-wrap">
        <h3>Kategori Pilihan</h3>
        <a href="<?= base_url('products') ?>" class="link-more">Lihat Semua</a>
    </div>
    <div class="category-scroll">
        <a href="<?= base_url('products') ?>" class="category-pill active">
            <span class="pill-icon">✨</span>
            <span class="pill-name">Semua</span>
        </a>
        <?php foreach ($categories as $cat): ?>
            <a href="<?= base_url('products?kategori=' . ($cat['id'] ?? '')) ?>" class="category-pill">
                <span class="pill-icon">📁</span>
                <span class="pill-name"><?= e($cat['name']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Produk Unggulan / Terlaris -->
<section class="home-section">
    <div class="section-title-wrap">
        <div>
            <h3 style="font-size: 1rem; margin: 0;">🔥 Produk Unggulan</h3>
            <p class="section-subtitle" style="font-size: 0.72rem; margin: 2px 0 0;">Paling banyak dibooking minggu ini</p>
        </div>
        <a href="<?= base_url('products?sort=terlaris') ?>" class="link-more" style="font-size: 0.75rem;">Lihat Semua</a>
    </div>

    <div class="product-cards-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.65rem;">
        <?php if (empty($topProducts)): ?>
            <div class="empty-box" style="grid-column: span 2;">Belum ada produk unggulan.</div>
        <?php else: ?>
            <?php foreach ($topProducts as $prod): ?>
                <?php 
                    $isFav       = !empty($_SESSION['wishlist'][(int)$prod['id']]); 
                    $stockVal    = (int)($prod['stock'] ?? $prod['available_stock'] ?? 0);
                    $price       = (float)($prod['price'] ?? 0);
                    $origPrice   = (float)($prod['original_price'] ?? 0);
                    $discPercent = (int)($prod['discount_percent'] ?? 0);
                    $isFeatured  = !empty($prod['is_featured']);

                    // Resolusi Gambar
                    $cardImg = '';
                    $rawImg  = $prod['image'] ?? $prod['primary_image'] ?? '';
                    if (!empty($rawImg)) {
                        $decoded = json_decode($rawImg, true);
                        $targetFile = is_array($decoded) ? ($decoded[0] ?? '') : $rawImg;
                        if (!empty($targetFile)) {
                            $cardImg = str_starts_with($targetFile, 'http') ? $targetFile : base_url($targetFile);
                        }
                    }
                ?>
                <div class="product-item-card" style="background: #fff; border: 1px solid #eef2f6; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; position: relative; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
                    
                    <!-- Tombol Favorit -->
                    <button type="button" onclick="toggleFavorite(this, <?= (int)$prod['id'] ?>)" title="Favorit" style="position: absolute; top: 6px; right: 6px; z-index: 5; width: 28px; height: 28px; border-radius: 50%; background: rgba(255, 255, 255, 0.95); border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.8rem; padding: 0;">
                        <?= $isFav ? '❤️' : '🤍' ?>
                    </button>

                    <!-- Link Detail Produk -->
                    <a href="<?= base_url('products/' . (int)$prod['id']) ?>" class="card-link" style="text-decoration: none; color: inherit; display: block;">
                        <div class="product-thumb" style="height: 115px; background: #f8fafc; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                            
                            <!-- Badge Unggulan / Diskon -->
                            <?php if ($isFeatured): ?>
                                <span class="badge-tag" style="position: absolute; top: 6px; left: 6px; font-size: 0.6rem; padding: 2px 6px; border-radius: 4px; z-index: 2; background: #ea580c; color: #fff; font-weight: 800; box-shadow: 0 2px 4px rgba(234,88,12,0.25);">
                                    Unggulan ⭐
                                </span>
                            <?php elseif ($discPercent > 0): ?>
                                <span class="badge-tag" style="position: absolute; top: 6px; left: 6px; font-size: 0.6rem; padding: 2px 6px; border-radius: 4px; z-index: 2; background: #dc2626; color: #fff; font-weight: 800;">
                                    -<?= $discPercent ?>%
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($cardImg)): ?>
                                <img src="<?= $cardImg ?>" alt="<?= e($prod['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div style="text-align: center;">
                                    <span style="font-size: 1.6rem; display: block;">📦</span>
                                    <span style="font-size: 0.62rem; font-weight: 700; color: #94a3b8;">UD. LAU</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="padding: 0.55rem 0.55rem 0.35rem;">
                            <span style="display: block; font-size: 0.62rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">
                                <?= e($prod['category_name'] ?? 'ATK') ?>
                            </span>
                            <h4 style="font-size: 0.78rem; font-weight: 700; color: #0f172a; margin: 0 0 4px; line-height: 1.25; height: 2.5em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                <?= e($prod['name']) ?>
                            </h4>

                            <!-- Rincian Harga & Diskon -->
                            <div style="margin-bottom: 4px;">
                                <div style="display: flex; align-items: baseline; gap: 4px;">
                                    <span style="font-size: 0.88rem; font-weight: 800; color: #065f46;">
                                        Rp <?= number_format($price, 0, ',', '.') ?>
                                    </span>
                                </div>
                                <?php if ($origPrice > $price): ?>
                                    <div style="display: flex; align-items: center; gap: 4px; margin-top: 1px;">
                                        <span style="font-size: 0.65rem; color: #94a3b8; text-decoration: line-through;">
                                            Rp <?= number_format($origPrice, 0, ',', '.') ?>
                                        </span>
                                        <?php if ($discPercent > 0): ?>
                                            <span style="font-size: 0.6rem; color: #dc2626; font-weight: 800; background: #fee2e2; padding: 1px 4px; border-radius: 3px;">
                                                -<?= $discPercent ?>%
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div style="display: flex; justify-content: flex-end;">
                                <span style="font-size: 0.6rem; padding: 1px 5px; border-radius: 4px; font-weight: 700; <?= $stockVal <= 0 ? 'background: #fee2e2; color: #dc2626;' : 'background: #ecfdf5; color: #059669;' ?>">
                                    <?= $stockVal <= 0 ? 'Habis' : 'Tersedia' ?>
                                </span>
                            </div>
                        </div>
                    </a>

                    <!-- Tombol Aksi Compact -->
                    <div style="display: flex; gap: 0.35rem; padding: 0 0.55rem 0.55rem;">
                        <?php if ($stockVal > 0): ?>
                            <form method="POST" action="<?= base_url('cart/add') ?>" style="margin: 0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= (int)$prod['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" title="Tambah ke Keranjang" style="width: 32px; height: 32px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; color: #065f46; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.85rem; padding: 0;">
                                    🛒
                                </button>
                            </form>

                            <form method="POST" action="<?= base_url('cart/add') ?>" style="flex: 1; margin: 0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= (int)$prod['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="buy_now" value="1">
                                <button type="submit" style="width: 100%; height: 32px; border-radius: 6px; border: none; background: #065f46; color: #ffffff; font-size: 0.72rem; font-weight: 700; cursor: pointer; padding: 0;">
                                    Booking
                                </button>
                            </form>
                        <?php else: ?>
                            <button type="button" disabled style="width: 100%; height: 32px; border-radius: 6px; border: 1px solid #cbd5e1; background: #f1f5f9; color: #94a3b8; font-size: 0.7rem; font-weight: 700;">
                                Stok Habis
                            </button>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Produk Terbaru -->
<section class="home-section">
    <div class="section-title-wrap">
        <div>
            <h3 style="font-size: 1rem; margin: 0;">✨ Produk Terbaru</h3>
            <p class="section-subtitle" style="font-size: 0.72rem; margin: 2px 0 0;">Stok ATK yang baru masuk</p>
        </div>
        <a href="<?= base_url('products?sort=terbaru') ?>" class="link-more" style="font-size: 0.75rem;">Lihat Semua</a>
    </div>

    <div class="product-cards-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.65rem;">
        <?php if (empty($newProducts)): ?>
            <div class="empty-box" style="grid-column: span 2;">Belum ada produk baru.</div>
        <?php else: ?>
            <?php foreach ($newProducts as $prod): ?>
                <?php 
                    $isFav       = !empty($_SESSION['wishlist'][(int)$prod['id']]); 
                    $stockVal    = (int)($prod['stock'] ?? $prod['available_stock'] ?? 0);
                    $price       = (float)($prod['price'] ?? 0);
                    $origPrice   = (float)($prod['original_price'] ?? 0);
                    $discPercent = (int)($prod['discount_percent'] ?? 0);
                    $isFeatured  = !empty($prod['is_featured']);

                    // Resolusi Gambar
                    $cardImg = '';
                    $rawImg  = $prod['image'] ?? $prod['primary_image'] ?? '';
                    if (!empty($rawImg)) {
                        $decoded = json_decode($rawImg, true);
                        $targetFile = is_array($decoded) ? ($decoded[0] ?? '') : $rawImg;
                        if (!empty($targetFile)) {
                            $cardImg = str_starts_with($targetFile, 'http') ? $targetFile : base_url($targetFile);
                        }
                    }
                ?>
                <div class="product-item-card" style="background: #fff; border: 1px solid #eef2f6; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; position: relative; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
                    
                    <!-- Tombol Favorit -->
                    <button type="button" onclick="toggleFavorite(this, <?= (int)$prod['id'] ?>)" title="Favorit" style="position: absolute; top: 6px; right: 6px; z-index: 5; width: 28px; height: 28px; border-radius: 50%; background: rgba(255, 255, 255, 0.95); border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.8rem; padding: 0;">
                        <?= $isFav ? '❤️' : '🤍' ?>
                    </button>

                    <!-- Link Detail Produk -->
                    <a href="<?= base_url('products/' . (int)$prod['id']) ?>" class="card-link" style="text-decoration: none; color: inherit; display: block;">
                        <div class="product-thumb" style="height: 115px; background: #f8fafc; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                            
                            <!-- Badge Selektif: Unggulan atau Diskon atau Baru -->
                            <?php if ($isFeatured): ?>
                                <span class="badge-tag" style="position: absolute; top: 6px; left: 6px; font-size: 0.6rem; padding: 2px 6px; border-radius: 4px; z-index: 2; background: #ea580c; color: #fff; font-weight: 800;">
                                    Unggulan ⭐
                                </span>
                            <?php elseif ($discPercent > 0): ?>
                                <span class="badge-tag" style="position: absolute; top: 6px; left: 6px; font-size: 0.6rem; padding: 2px 6px; border-radius: 4px; z-index: 2; background: #dc2626; color: #fff; font-weight: 800;">
                                    -<?= $discPercent ?>%
                                </span>
                            <?php else: ?>
                                <span class="badge-tag" style="position: absolute; top: 6px; left: 6px; font-size: 0.6rem; padding: 2px 6px; border-radius: 4px; z-index: 2; background: #059669; color: #fff; font-weight: 700;">
                                    Baru
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($cardImg)): ?>
                                <img src="<?= $cardImg ?>" alt="<?= e($prod['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div style="text-align: center;">
                                    <span style="font-size: 1.6rem; display: block;">📦</span>
                                    <span style="font-size: 0.62rem; font-weight: 700; color: #94a3b8;">UD. LAU</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="padding: 0.55rem 0.55rem 0.35rem;">
                            <span style="display: block; font-size: 0.62rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">
                                <?= e($prod['category_name'] ?? 'ATK') ?>
                            </span>
                            <h4 style="font-size: 0.78rem; font-weight: 700; color: #0f172a; margin: 0 0 4px; line-height: 1.25; height: 2.5em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                <?= e($prod['name']) ?>
                            </h4>

                            <!-- Rincian Harga & Diskon -->
                            <div style="margin-bottom: 4px;">
                                <div style="display: flex; align-items: baseline; gap: 4px;">
                                    <span style="font-size: 0.88rem; font-weight: 800; color: #065f46;">
                                        Rp <?= number_format($price, 0, ',', '.') ?>
                                    </span>
                                </div>
                                <?php if ($origPrice > $price): ?>
                                    <div style="display: flex; align-items: center; gap: 4px; margin-top: 1px;">
                                        <span style="font-size: 0.65rem; color: #94a3b8; text-decoration: line-through;">
                                            Rp <?= number_format($origPrice, 0, ',', '.') ?>
                                        </span>
                                        <?php if ($discPercent > 0): ?>
                                            <span style="font-size: 0.6rem; color: #dc2626; font-weight: 800; background: #fee2e2; padding: 1px 4px; border-radius: 3px;">
                                                -<?= $discPercent ?>%
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div style="display: flex; justify-content: flex-end;">
                                <span style="font-size: 0.6rem; padding: 1px 5px; border-radius: 4px; font-weight: 700; <?= $stockVal <= 0 ? 'background: #fee2e2; color: #dc2626;' : 'background: #ecfdf5; color: #059669;' ?>">
                                    <?= $stockVal <= 0 ? 'Habis' : 'Tersedia' ?>
                                </span>
                            </div>
                        </div>
                    </a>

                    <!-- Tombol Aksi Compact -->
                    <div style="display: flex; gap: 0.35rem; padding: 0 0.55rem 0.55rem;">
                        <?php if ($stockVal > 0): ?>
                            <form method="POST" action="<?= base_url('cart/add') ?>" style="margin: 0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= (int)$prod['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" title="Tambah ke Keranjang" style="width: 32px; height: 32px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; color: #065f46; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.85rem; padding: 0;">
                                    🛒
                                </button>
                            </form>

                            <form method="POST" action="<?= base_url('cart/add') ?>" style="flex: 1; margin: 0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= (int)$prod['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="buy_now" value="1">
                                <button type="submit" style="width: 100%; height: 32px; border-radius: 6px; border: none; background: #065f46; color: #ffffff; font-size: 0.72rem; font-weight: 700; cursor: pointer; padding: 0;">
                                    Booking
                                </button>
                            </form>
                        <?php else: ?>
                            <button type="button" disabled style="width: 100%; height: 32px; border-radius: 6px; border: 1px solid #cbd5e1; background: #f1f5f9; color: #94a3b8; font-size: 0.7rem; font-weight: 700;">
                                Stok Habis
                            </button>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Script AJAX Favorit Instan + Redirect Login Jika Belum Masuk Akun -->
<script>
const IS_USER_LOGGED_IN = <?= (isset($_SESSION['user']) || isset($_SESSION['user_id']) || (class_exists('\App\Core\Auth') && \App\Core\Auth::check())) ? 'true' : 'false' ?>;
const LOGIN_URL = '<?= base_url('login') ?>';

function toggleFavorite(btn, productId) {
    if (!IS_USER_LOGGED_IN) {
        window.location.href = LOGIN_URL;
        return;
    }

    const formData = new FormData();
    formData.append('product_id', productId);

    fetch('<?= base_url('wishlist/toggle') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.require_login && data.redirect_url) {
            window.location.href = data.redirect_url;
            return;
        }

        if (data.success) {
            btn.innerHTML = data.is_favorite ? '❤️' : '🤍';

            // Perbarui angka notifikasi pada navbar bawah secara instan
            const favBadge = document.getElementById('nav-fav-badge');
            if (favBadge && typeof data.favorite_count !== 'undefined') {
                const count = parseInt(data.favorite_count, 10);
                if (count > 0) {
                    favBadge.textContent = count > 99 ? '99+' : count;
                    favBadge.style.display = 'flex';
                } else {
                    favBadge.style.display = 'none';
                }
            }
        }
    })
    .catch(err => {
        window.location.href = LOGIN_URL;
    });
}
</script>