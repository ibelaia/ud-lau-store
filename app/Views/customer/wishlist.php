<div class="wishlist-page-wrapper" style="padding-bottom: 5.5rem;">

    <!-- Header Halaman -->
    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
        <a href="<?= base_url('products') ?>" style="width: 36px; height: 36px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #1e293b; font-weight: 700;">
            &larr;
        </a>
        <div>
            <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">Produk Favorit ❤️</h2>
            <p style="font-size: 0.72rem; color: #64748b; margin: 0.15rem 0 0;">Daftar simpanan produk untuk langsung dibooking</p>
        </div>
    </div>

    <!-- State Kosong (Ditampilkan jika kosong saat dimuat atau saat semua item dihapus) -->
    <div id="wishlist-empty-box" style="display: <?= empty($products) ? 'block' : 'none' ?>; text-align: center; padding: 3.5rem 1.5rem; background: #ffffff; border-radius: 16px; border: 1px dashed #cbd5e1; margin-top: 0.5rem;">
        <div style="font-size: 3rem; margin-bottom: 0.5rem;">💖</div>
        <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-bottom: 0.35rem;">Belum Ada Produk Favorit</h3>
        <p style="font-size: 0.78rem; color: #64748b; line-height: 1.4; margin-bottom: 1.25rem;">
            Ketuk ikon hati pada barang kebutuhan Anda agar tersimpan di sini dan siap dibooking sewaktu-waktu.
        </p>
        <a href="<?= base_url('products') ?>" style="display: inline-block; padding: 0.65rem 1.35rem; font-size: 0.82rem; font-weight: 700; text-decoration: none; border-radius: 10px; background: #065f46; color: #fff;">
            Cari Produk Sekarang
        </a>
    </div>

    <?php 
        $totalWishlist = 0;
        if (!empty($products)) {
            foreach ($products as $prod) {
                $totalWishlist += (float)($prod['price'] ?? 0);
            }
        }
    ?>

    <!-- Kontainer Konten Jika Ada Produk -->
    <div id="wishlist-content-wrap" style="display: <?= !empty($products) ? 'block' : 'none' ?>;">
        <!-- Daftar Item Favorit -->
        <div id="wishlist-items-container" style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $prod): 
                    $pPrice = (float)($prod['price'] ?? 0);
                    $rawImg = $prod['image'] ?? $prod['primary_image'] ?? '';
                    $cardImg = '';
                    if (!empty($rawImg)) {
                        $decoded = json_decode($rawImg, true);
                        $targetFile = is_array($decoded) ? ($decoded[0] ?? '') : $rawImg;
                        if (!empty($targetFile)) {
                            $cardImg = str_starts_with($targetFile, 'http') ? $targetFile : (function_exists('upload_url') ? upload_url($targetFile) : base_url($targetFile));
                        }
                    }
                ?>
                    <div id="wishlist-row-<?= (int)$prod['id'] ?>" data-price="<?= $pPrice ?>" style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 14px; padding: 0.9rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; transition: opacity 0.25s ease, transform 0.25s ease;">
                        
                        <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 0;">
                            <div style="width: 48px; height: 48px; border-radius: 10px; background: #f8fafc; border: 1px solid #eef2f6; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden;">
                                <?php if (!empty($cardImg)): ?>
                                    <img src="<?= $cardImg ?>" alt="<?= e($prod['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <span style="font-size: 1.4rem;">📦</span>
                                <?php endif; ?>
                            </div>
                            <div style="min-width: 0;">
                                <h4 style="font-size: 0.85rem; font-weight: 700; color: #0f172a; margin: 0 0 0.2rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?= e($prod['name']) ?>
                                </h4>
                                <span style="font-size: 0.85rem; font-weight: 800; color: #065f46; display: block;">
                                    Rp <?= number_format($pPrice, 0, ',', '.') ?>
                                </span>
                            </div>
                        </div>

                        <!-- Tombol Hapus Favorit Interaktif -->
                        <button type="button" onclick="removeWishlistItem(<?= (int)$prod['id'] ?>)" title="Hapus dari Favorit" style="background: #fee2e2; border: none; color: #dc2626; width: 32px; height: 32px; border-radius: 8px; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            ✕
                        </button>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Info Ambil di Toko -->
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 0.85rem; margin-bottom: 1.25rem;">
            <div style="display: flex; gap: 0.5rem; align-items: flex-start;">
                <span style="font-size: 1.1rem;">🏪</span>
                <div>
                    <strong style="font-size: 0.8rem; color: #166534; display: block;">Pesanan Khusus Produk Favorit</strong>
                    <span style="font-size: 0.72rem; color: #15803d; line-height: 1.35; display: block; margin-top: 0.15rem;">
                        Semua barang di atas akan dibooking sekaligus untuk Anda ambil di UD. LAU Store tanpa memengaruhi keranjang belanja.
                    </span>
                </div>
            </div>
        </div>

        <!-- Floating Bottom Bar: Total & Booking Sekarang -->
        <div id="wishlist-bottom-bar" style="position: fixed; bottom: 68px; left: 50%; transform: translateX(-50%); width: 100%; max-width: 480px; background: #ffffff; border-top: 1px solid #e2e8f0; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; z-index: 90; box-shadow: 0 -4px 12px rgba(0,0,0,0.04); box-sizing: border-box;">
            <div>
                <span style="font-size: 0.68rem; color: #64748b; display: block; font-weight: 600;">Total Tagihan:</span>
                <span id="wishlist-total-display" style="font-size: 1.15rem; font-weight: 800; color: #065f46;">
                    Rp <?= number_format((float)$totalWishlist, 0, ',', '.') ?>
                </span>
            </div>

            <a href="<?= base_url('checkout?source=wishlist') ?>" style="background: #065f46; color: #ffffff; text-decoration: none; padding: 0.65rem 1.25rem; border-radius: 10px; font-size: 0.82rem; font-weight: 700; box-shadow: 0 4px 12px rgba(6, 95, 70, 0.25);">
                Booking Sekarang &rarr;
            </a>
        </div>
    </div>

</div>

<!-- Script Hapus Favorit Realtime -->
<script>
let currentWishlistTotal = <?= (float)$totalWishlist ?>;

function formatRupiah(num) {
    return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function removeWishlistItem(productId) {
    const targetRow = document.getElementById('wishlist-row-' + productId);
    if (!targetRow) return;

    const itemPrice = parseFloat(targetRow.getAttribute('data-price')) || 0;

    // Animasi visual saat dihapus
    targetRow.style.opacity = '0.4';
    targetRow.style.pointerEvents = 'none';

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
            // Hapus baris dari DOM
            targetRow.remove();

            // Hitung ulang total harga
            currentWishlistTotal = Math.max(0, currentWishlistTotal - itemPrice);
            const totalDisplay = document.getElementById('wishlist-total-display');
            if (totalDisplay) {
                totalDisplay.textContent = formatRupiah(currentWishlistTotal);
            }

            // Perbarui badge angka pada bilah navigasi bawah
            const favBadge = document.getElementById('nav-fav-badge');
            const totalRemaining = typeof data.favorite_count !== 'undefined' ? parseInt(data.favorite_count, 10) : document.querySelectorAll('[id^="wishlist-row-"]').length;

            if (favBadge) {
                if (totalRemaining > 0) {
                    favBadge.textContent = totalRemaining > 99 ? '99+' : totalRemaining;
                    favBadge.style.display = 'flex';
                } else {
                    favBadge.style.display = 'none';
                }
            }

            // Jika barang sudah habis, sembunyikan baris booking bawah & tampilkan empty state
            if (totalRemaining <= 0) {
                const contentWrap = document.getElementById('wishlist-content-wrap');
                const emptyBox = document.getElementById('wishlist-empty-box');
                const bottomBar = document.getElementById('wishlist-bottom-bar');

                if (contentWrap) contentWrap.style.display = 'none';
                if (bottomBar) bottomBar.style.display = 'none';
                if (emptyBox) emptyBox.style.display = 'block';
            }
        } else {
            targetRow.style.opacity = '1';
            targetRow.style.pointerEvents = 'auto';
        }
    })
    .catch(err => {
        console.error(err);
        targetRow.style.opacity = '1';
        targetRow.style.pointerEvents = 'auto';
    });
}
</script>