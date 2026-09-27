<div class="products-page-wrapper" style="padding-bottom: 2rem;">

    <!-- Filter Tab Kategori Scrollable -->
    <div style="display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.5rem; margin-bottom: 1rem; scrollbar-width: none;">
        <a href="<?= base_url('products') ?>" style="flex: 0 0 auto; text-decoration: none; padding: 0.4rem 0.9rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; <?= empty($selectedCategory) ? 'background: #065f46; color: #fff;' : 'background: #f8fafc; color: #475569; border: 1px solid #e2e8f0;' ?>">
            ✨ Semua
        </a>
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): 
                $cId = is_array($cat) ? $cat['id'] : $cat->id;
                $cName = is_array($cat) ? $cat['name'] : $cat->name;
                $isActive = ((string)($selectedCategory ?? '') === (string)$cId);
            ?>
                <a href="<?= base_url('products?kategori=' . $cId) ?>" style="flex: 0 0 auto; text-decoration: none; padding: 0.4rem 0.9rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; <?= $isActive ? 'background: #065f46; color: #fff;' : 'background: #f8fafc; color: #475569; border: 1px solid #e2e8f0;' ?>">
                    📁 <?= e($cName) ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Judul List & Total Produk -->
    <div style="margin-bottom: 0.85rem;">
        <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Ditemukan <?= count($products ?? []) ?> produk</span>
    </div>

    <!-- Grid List Kartu Produk Customer -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.65rem;">
        <?php if (empty($products)): ?>
            <div style="grid-column: span 2; text-align: center; padding: 3rem 1rem; background: #fff; border-radius: 14px; border: 1px dashed #cbd5e1;">
                <span style="font-size: 2.5rem; display: block; margin-bottom: 0.5rem;">📦</span>
                <p style="color: #64748b; font-size: 0.85rem; margin: 0;">Produk tidak ditemukan.</p>
            </div>
        <?php else: ?>
            <?php foreach ($products as $item): 
                $pId       = is_array($item) ? ($item['id'] ?? 0) : ($item->id ?? 0);
                $pName     = is_array($item) ? ($item['name'] ?? '') : ($item->name ?? '');
                $pPrice    = is_array($item) ? (float)($item['price'] ?? 0) : (float)($item->price ?? 0);
                $origPrice = is_array($item) ? (float)($item['original_price'] ?? 0) : (float)($item->original_price ?? 0);
                $disc      = is_array($item) ? (int)($item['discount_percent'] ?? 0) : (int)($item->discount_percent ?? 0);
                $pStock    = is_array($item) ? (int)($item['stock'] ?? $item['available_stock'] ?? 0) : (int)($item->stock ?? $item->available_stock ?? 0);
                $pCat      = is_array($item) ? ($item['category_name'] ?? 'Peralatan Sekolah') : ($item->category_name ?? 'Peralatan Sekolah');
                $isFeat    = is_array($item) ? !empty($item['is_featured']) : !empty($item->is_featured);
                $isFav     = !empty($_SESSION['wishlist'][$pId]);

                $rawImg   = is_array($item) ? ($item['image'] ?? '') : ($item->image ?? '');
                $coverImg = '';
                if (!empty($rawImg)) {
                    $dec = json_decode($rawImg, true);
                    $coverImg = is_array($dec) ? ($dec[0] ?? '') : $rawImg;
                }
            ?>
                <div style="background: #ffffff; border: 1px solid #eef2f6; border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; position: relative; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
                    
                    <!-- Badge Unggulan Oranye -->
                    <?php if ($isFeat): ?>
                        <span style="position: absolute; top: 6px; left: 6px; z-index: 5; background: #ea580c; color: #ffffff; font-size: 0.62rem; font-weight: 800; padding: 2px 6px; border-radius: 5px; box-shadow: 0 2px 4px rgba(234, 88, 12, 0.3);">
                            Unggulan ⭐
                        </span>
                    <?php endif; ?>

                    <!-- Tombol Favorit -->
                    <button type="button" onclick="toggleFavorite(this, <?= $pId ?>)" title="Favorit" style="position: absolute; top: 6px; right: 6px; z-index: 5; width: 28px; height: 28px; border-radius: 50%; background: rgba(255, 255, 255, 0.95); border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.8rem; padding: 0;">
                        <?= $isFav ? '❤️' : '🤍' ?>
                    </button>

                    <!-- Link ke Detail Produk -->
                    <a href="<?= base_url('products/' . $pId) ?>" style="text-decoration: none; color: inherit; display: block;">
                        <div style="height: 130px; background: #f8fafc; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                            <?php if (!empty($coverImg)): ?>
                                <img src="<?= str_starts_with($coverImg, 'http') ? $coverImg : base_url($coverImg) ?>" alt="<?= e($pName) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div style="text-align: center;">
                                    <span style="font-size: 1.8rem; display: block;">📦</span>
                                    <span style="font-size: 0.62rem; font-weight: 700; color: #94a3b8;">UD. LAU</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="padding: 0.65rem 0.65rem 0.35rem;">
                            <span style="display: block; font-size: 0.62rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 2px;">
                                <?= e($pCat) ?>
                            </span>
                            <h4 style="font-size: 0.82rem; font-weight: 700; color: #0f172a; margin: 0 0 4px; line-height: 1.3; height: 2.6em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                <?= e($pName) ?>
                            </h4>

                            <div style="margin-bottom: 4px;">
                                <span style="font-size: 0.88rem; font-weight: 800; color: #065f46; display: block;">
                                    Rp <?= number_format($pPrice, 0, ',', '.') ?>
                                </span>

                                <!-- Harga Coret & Badge Diskon -->
                                <?php if ($disc > 0 && $origPrice > $pPrice): ?>
                                    <div style="display: flex; align-items: center; gap: 4px; margin-top: 1px;">
                                        <span style="font-size: 0.62rem; color: #94a3b8; text-decoration: line-through;">
                                            Rp <?= number_format($origPrice, 0, ',', '.') ?>
                                        </span>
                                        <span style="font-size: 0.58rem; font-weight: 800; color: #dc2626; background: #fee2e2; padding: 1px 4px; border-radius: 4px;">
                                            -<?= $disc ?>%
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <span style="font-size: 0.6rem; padding: 1px 5px; border-radius: 4px; font-weight: 700; display: inline-block; <?= $pStock <= 0 ? 'background: #fee2e2; color: #dc2626;' : 'background: #ecfdf5; color: #059669;' ?>">
                                <?= $pStock <= 0 ? 'Habis' : 'Tersedia' ?>
                            </span>
                        </div>
                    </a>

                    <!-- Tombol Aksi Compact (Keranjang & Booking) -->
                    <div style="display: flex; gap: 0.35rem; padding: 0 0.65rem 0.65rem; margin-top: 0.35rem;">
                        <?php if ($pStock > 0): ?>
                            <form method="POST" action="<?= base_url('cart/add') ?>" style="margin: 0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= $pId ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" title="Tambah ke Keranjang" style="width: 32px; height: 32px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; color: #065f46; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.85rem; padding: 0;">
                                    🛒
                                </button>
                            </form>

                            <form method="POST" action="<?= base_url('checkout/instant') ?>" style="flex: 1; margin: 0;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= $pId ?>">
                                <input type="hidden" name="quantity" value="1">
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

</div>

<script>
const IS_USER_LOGGED_IN = <?= (isset($_SESSION['user']) || isset($_SESSION['user_id']) || (class_exists('\App\Core\Auth') && \App\Core\Auth::check())) ? 'true' : 'false' ?>;
const LOGIN_URL = '<?= base_url('login') ?>';

function toggleFavorite(btn, productId) {
    // 1. Arahkan langsung ke login jika belum masuk akun
    if (!IS_USER_LOGGED_IN) {
        window.location.href = LOGIN_URL;
        return;
    }

    // 2. Kirim request AJAX jika sudah login
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
            // Ubah ikon hati secara instan
            btn.innerHTML = data.is_favorite ? '❤️' : '🤍';

            // Sinkronkan badge angka notifikasi di navbar bawah
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
        console.error(err);
        window.location.href = LOGIN_URL;
    });
}
</script>