<?php
$pId    = is_array($product) ? ($product['id'] ?? 0) : ($product->id ?? 0);
$pName  = is_array($product) ? ($product['name'] ?? '') : ($product->name ?? '');
$pPrice = is_array($product) ? (float)($product['price'] ?? 0) : (float)($product->price ?? 0);
$pStock = is_array($product) ? (int)($product['stock'] ?? 0) : (int)($product->stock ?? 0);
$pCat   = is_array($product) ? ($product['category_name'] ?? 'Alat Tulis Kantor') : ($product->category_name ?? 'Alat Tulis Kantor');
$pDesc  = is_array($product) ? ($product['description'] ?? '') : ($product->description ?? '');
$pCode  = is_array($product) ? ($product['code'] ?? ('ATK' . str_pad((string)$pId, 4, '0', STR_PAD_LEFT))) : ('ATK' . str_pad((string)$pId, 4, '0', STR_PAD_LEFT));

$rawImg = is_array($product) ? ($product['image'] ?? '') : ($product->image ?? '');
$galleryImages = [];
if (!empty($rawImg)) {
    $decoded = json_decode($rawImg, true);
    if (is_array($decoded) && !empty($decoded)) {
        $galleryImages =$decoded;
    } else {
        $galleryImages[] =$rawImg;
    }
}
if (!empty($images) && empty($galleryImages)) {
    $galleryImages =$images;
}
$totalImages = count($galleryImages);

// Validasi login tanpa simbol rentan error
$isLoggedIn = false;
if (isset($_SESSION['user'])) {$isLoggedIn = true;
}
if (isset($_SESSION['user_id'])) {$isLoggedIn = true;
}
if (class_exists('\App\Core\Auth') && \App\Core\Auth::check()) {
    $isLoggedIn = true;
}

$fullChatUrl =$isLoggedIn ? base_url('chat?product_id=' . $pId) : base_url('login');$ownerTargetId = 1;
if (class_exists('\App\Models\MessageModel')) {
    $ownerTargetId = (new \App\Models\MessageModel())->getOwnerId();
}
?>

<div class="product-detail-wrapper" style="padding-bottom: 5rem; margin: -0.85rem -0.85rem 0 -0.85rem;">

    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background: #ffffff; border-bottom: 1px solid #f1f5f9; position: sticky; top: 0; z-index: 50;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <a href="<?= base_url('products') ?>" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; text-decoration: none; color: #0f172a; font-weight: 800; font-size: 1rem;">
                &larr;
            </a>
            <span style="font-size: 0.85rem; font-weight: 700; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                Detail Produk
            </span>
        </div>

        <a href="<?= $fullChatUrl ?>" title="Buka Halaman Chat Penuh" style="display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background: #f1f5f9; border: 1px solid #e2e8f0; text-decoration: none; color: #334155;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
        </a>
    </div>

    <div style="position: relative; width: 100%; aspect-ratio: 1 / 1; background: #ffffff; border-bottom: 1px solid #eef2f6; overflow: hidden;">
        <?php if ($totalImages > 0): ?>
            <div id="shopeeSlider" style="display: flex; width: 100%; height: 100%; overflow-x: auto; scroll-snap-type: x mandatory; scrollbar-width: none; -webkit-overflow-scrolling: touch; scroll-behavior: smooth;" onscroll="onSliderScroll()">
                <?php foreach ($galleryImages as $index =>$img): ?>
                    <div style="flex: 0 0 100%; width: 100%; height: 100%; scroll-snap-align: start; display: flex; align-items: center; justify-content: center; background: #ffffff;">
                        <img src="<?= base_url($img) ?>" alt="<?= e($pName) . ' foto ' . ($index + 1) ?>" style="width: 100%; height: 100%; object-fit: contain;">
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="slideBadge" style="position: absolute; bottom: 12px; right: 12px; background: rgba(15, 23, 42, 0.65); color: #ffffff; font-size: 0.72rem; font-weight: 800; padding: 3px 9px; border-radius: 20px; backdrop-filter: blur(4px); z-index: 10; letter-spacing: 0.5px;">
                1/<?= $totalImages ?>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 100%; background: #f8fafc;">
                <span style="font-size: 4.5rem;">📦</span>
                <span style="font-size: 0.78rem; color: #94a3b8; font-weight: 700; margin-top: 4px;">UD. LAU STORE</span>
            </div>
        <?php endif; ?>
    </div>

    <div style="padding: 0.85rem;">

        <div style="background: #ffffff; border-radius: 14px; border: 1px solid #f1f5f9; padding: 1rem; margin-bottom: 0.75rem; box-shadow: 0 1px 4px rgba(0,0,0,0.02);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.4rem;">
                <span style="font-size: 0.7rem; font-weight: 700; color: #047857; background: #ecfdf5; padding: 2px 7px; border-radius: 5px; display: inline-block;">
                    <?= e($pCat) ?>
                </span>
                <span style="font-size: 0.68rem; color: #94a3b8;">
                    Kode: <?= e($pCode) ?>
                </span>
            </div>

            <h1 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0 0 0.5rem; line-height: 1.35;">
                <?= e($pName) ?>
            </h1>

            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.35rem; border-top: 1px dashed #f1f5f9;">
                <span style="font-size: 1.3rem; font-weight: 800; color: #065f46;">
                    Rp <?= number_format($pPrice, 0, ',', '.') ?>
                </span>
                <span style="font-size: 0.7rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; <?= $pStock > 0 ? 'background: #d1fae5; color: #065f46;' : 'background: #fee2e2; color: #dc2626;' ?>">
                    <?= $pStock > 0 ? '✓ Stok Siap di Toko' : '✗ Stok Habis' ?>
                </span>
            </div>
        </div>

        <div style="background: #ffffff; border-radius: 14px; border: 1px solid #f1f5f9; padding: 1rem; margin-bottom: 0.75rem; box-shadow: 0 1px 4px rgba(0,0,0,0.02);">
            <h3 style="font-size: 0.85rem; font-weight: 800; color: #0f172a; margin: 0 0 0.4rem;">Deskripsi Produk</h3>
            <p style="font-size: 0.78rem; color: #475569; line-height: 1.6; margin: 0; white-space: pre-line;">
                <?= !empty($pDesc) ? e($pDesc) : 'Tidak ada keterangan tambahan untuk produk ini.' ?>
            </p>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.85rem; margin-bottom: 1rem;">
            <?php if ($pStock > 0): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #1e293b;">Jumlah Beli:</span>
                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                        <button type="button" onclick="changeQty(-1)" style="width: 32px; height: 32px; border-radius: 8px; border: 1.5px solid #cbd5e1; background: #f8fafc; font-size: 1.1rem; font-weight: 800; cursor: pointer;">-</button>
                        <input type="number" id="buyQty" value="1" min="1" max="<?= $pStock ?>" style="width: 45px; height: 32px; text-align: center; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem; font-weight: 800; outline: none;" readonly>
                        <button type="button" onclick="changeQty(1)" style="width: 32px; height: 32px; border-radius: 8px; border: 1.5px solid #cbd5e1; background: #f8fafc; font-size: 1.1rem; font-weight: 800; cursor: pointer;">+</button>
                    </div>
                </div>

                <div style="display: flex; gap: 0.4rem; align-items: center;">
                    <button type="button" onclick="openMiniChat()" title="Tanya Produk Ini via Chat" style="width: 42px; height: 42px; border-radius: 10px; background: #f8fafc; border: 1.5px solid #cbd5e1; display: flex; align-items: center; justify-content: center; color: #065f46; cursor: pointer; flex-shrink: 0; padding: 0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </button>

                    <form method="POST" action="<?= base_url('cart/add') ?>" style="flex: 1; margin: 0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= $pId ?>">
                        <input type="hidden" name="quantity" id="cartQuantityInput" value="1">
                        <button type="submit" style="width: 100%; height: 42px; background: #ecfdf5; color: #065f46; border: 1.5px solid #a7f3d0; border-radius: 10px; font-size: 0.76rem; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 4px; padding: 0 4px;">
                            🛒 + Keranjang
                        </button>
                    </form>

                    <form method="POST" action="<?= base_url('cart/add') ?>" style="flex: 1.3; margin: 0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= $pId ?>">
                        <input type="hidden" name="quantity" id="bookingQuantityInput" value="1">
                        <input type="hidden" name="buy_now" value="1">
                        <button type="submit" style="width: 100%; height: 42px; background: #065f46; color: #ffffff; border: none; border-radius: 10px; font-size: 0.78rem; font-weight: 800; cursor: pointer; padding: 0 4px;">
                            Langsung Booking
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <button type="button" onclick="openMiniChat()" title="Chat Toko" style="width: 42px; height: 42px; border-radius: 10px; background: #f8fafc; border: 1.5px solid #cbd5e1; display: flex; align-items: center; justify-content: center; color: #065f46; font-size: 1.1rem; cursor: pointer;">
                        💬
                    </button>
                    <div style="flex: 1; height: 42px; background: #f1f5f9; color: #94a3b8; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.82rem; font-weight: 700;">
                        Stok Produk Habis
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

<div id="miniChatModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(2px); z-index: 999; align-items: flex-start; justify-content: center; padding: 12px; box-sizing: border-box;">
    <div style="background: #ffffff; width: 100%; max-width: 390px; height: 320px; max-height: 46vh; border-radius: 14px; display: flex; flex-direction: column; box-shadow: 0 10px 25px rgba(0,0,0,0.25); overflow: hidden; margin-top: 15px;">
        
        <div style="background: #065f46; color: #ffffff; padding: 7px 12px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="font-size: 0.95rem;">🏪</span>
                <span style="font-size: 0.78rem; font-weight: 800; color: #ffffff;">Tanya Produk Toko</span>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <a href="<?= $fullChatUrl ?>" title="Layar Penuh" style="color: #ffffff; text-decoration: none; font-size: 0.65rem; background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 4px; font-weight: 600;">
                    Penuh ↗
                </a>
                <button type="button" onclick="closeMiniChat()" style="background: none; border: none; color: #ffffff; font-size: 1.15rem; cursor: pointer; padding: 0; line-height: 1;">✕</button>
            </div>
        </div>

        <div style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 5px 12px; display: flex; align-items: center; justify-content: space-between; gap: 6px; flex-shrink: 0;">
            <div style="min-width: 0; line-height: 1.2;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #0f172a; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 190px;">
                    <?= e($pName) ?>
                </span>
                <span style="font-size: 0.7rem; font-weight: 800; color: #065f46;">
                    Rp <?= number_format($pPrice, 0, ',', '.') ?>
                </span>
            </div>
            <button type="button" onclick="sendAutoTemplate('Halo kak, apakah barang ini ready stok?')" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; font-size: 0.65rem; font-weight: 700; padding: 3px 7px; border-radius: 5px; cursor: pointer; white-space: nowrap; flex-shrink: 0;">
                Tanya Stok
            </button>
        </div>

        <div id="miniChatMessages" style="flex: 1; overflow-y: auto; padding: 8px 12px; display: flex; flex-direction: column; gap: 6px; background: #f1f5f9;">
            <div style="align-self: flex-start; max-width: 85%; background: #ffffff; color: #1e293b; padding: 5px 9px; border-radius: 8px 8px 8px 2px; font-size: 0.72rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); line-height: 1.35;">
                Ada yang bisa kami bantu seputar produk <strong><?= e($pName) ?></strong>?
            </div>
        </div>

        <form id="activeMiniChatForm" onsubmit="event.preventDefault(); sendMiniMessage();" style="background: #ffffff; border-top: 1px solid #e2e8f0; padding: 6px 10px; display: flex; gap: 6px; align-items: center; flex-shrink: 0; margin: 0;">
            <?= csrf_field() ?>
            <input type="hidden" id="miniReceiverId" value="<?= $ownerTargetId ?>">
            <input type="hidden" id="miniProductId" value="<?= $pId ?>">
            <input type="text" id="miniChatInput" placeholder="Ketik pertanyaan..." autocomplete="off" style="flex: 1; height: 32px; border: 1.5px solid #cbd5e1; border-radius: 16px; padding: 0 10px; font-size: 0.75rem; outline: none;">
            <button type="submit" style="width: 32px; height: 32px; border-radius: 50%; background: #065f46; color: #ffffff; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
            </button>
        </form>

    </div>
</div>

<script>
const IS_USER_LOGGED_IN = <?= $isLoggedIn ? 'true' : 'false' ?>;
const LOGIN_URL = '<?= base_url('login') ?>';
const CURRENT_PRODUCT_ID = <?= (int)$pId ?>;
const OWNER_TARGET_ID = <?= (int)$ownerTargetId ?>;

function openMiniChat() {
    if (!IS_USER_LOGGED_IN) {
        window.location.href = LOGIN_URL;
        return;
    }
    const modal = document.getElementById('miniChatModal');
    if (modal) {
        modal.style.display = 'flex';
        const input = document.getElementById('miniChatInput');
        if (input) setTimeout(() => input.focus(), 100);
        scrollChatToBottom();
    }
}

function closeMiniChat() {
    const modal = document.getElementById('miniChatModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function scrollChatToBottom() {
    const box = document.getElementById('miniChatMessages');
    if (box) {
        box.scrollTop = box.scrollHeight;
    }
}

function sendAutoTemplate(txt) {
    const input = document.getElementById('miniChatInput');
    if (input) {
        input.value = txt;
        sendMiniMessage();
    }
}

function sendMiniMessage() {
    const input = document.getElementById('miniChatInput');
    const msg = input.value.trim();
    if (!msg) return;

    const box = document.getElementById('miniChatMessages');
    const outMsg = document.createElement('div');
    outMsg.style.cssText = 'align-self: flex-end; max-width: 85%; background: #065f46; color: #ffffff; padding: 5px 9px; border-radius: 8px 8px 2px 8px; font-size: 0.72rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); line-height: 1.35;';
    outMsg.textContent = msg;
    box.appendChild(outMsg);
    scrollChatToBottom();
    input.value = '';

    const formEl = document.getElementById('activeMiniChatForm');
    const formData = new FormData(formEl);
    formData.append('message', msg);
    formData.append('product_id', CURRENT_PRODUCT_ID);
    formData.append('receiver_id', OWNER_TARGET_ID);

    fetch('<?= base_url('chat/send') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data && data.require_login && data.redirect_url) {
            window.location.href = data.redirect_url;
        }
    })
    .catch(e => console.error(e));
}

const totalImagesCount = <?= (int)$totalImages ?>;
const sliderEl = document.getElementById('shopeeSlider');
const badgeEl = document.getElementById('slideBadge');

function onSliderScroll() {
    if (!sliderEl || totalImagesCount <= 1) return;
    const width = sliderEl.clientWidth;
    const current = Math.round(sliderEl.scrollLeft / width) + 1;
    if (badgeEl) {
        badgeEl.textContent = `${current}/${totalImagesCount}`;
    }
}

function changeQty(diff) {
    const input = document.getElementById('buyQty');
    const cartInput = document.getElementById('cartQuantityInput');
    const bookingInput = document.getElementById('bookingQuantityInput');
    let max = parseInt(input.getAttribute('max')) || 999;
    let val = parseInt(input.value) + diff;
    if (val < 1) val = 1;
    if (val > max) val = max;
    input.value = val;
    if (cartInput) cartInput.value = val;
    if (bookingInput) bookingInput.value = val;
}
</script>