<style>
    /* Sembunyikan bottom navbar customer & hilangkan padding body saat masuk chat */
    html, body {
        height: 100% !important;
        overflow: hidden !important;
        padding-bottom: 0 !important;
    }
    .bottom-nav {
        display: none !important;
    }
    .header-top {
        display: none !important;
    }
    .main-content {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
        height: 100% !important;
    }
    .search-bar, 
    .search-box, 
    .search-container,
    form[action*="products"] {
        display: none !important;
    }

    /* Kunci layar chat memenuhi viewport layar HP tanpa terganggu navbar */
    .customer-chat-viewport {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 480px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        z-index: 100;
        box-shadow: 0 0 20px rgba(0,0,0,0.05);
    }

    .chat-scroll-area {
        flex: 1 1 auto;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 0.85rem;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        background: #f1f5f9;
    }

    .chat-input-footer {
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        padding: 0.5rem 0.75rem;
        display: flex;
        gap: 0.45rem;
        align-items: center;
        flex-shrink: 0;
        position: sticky;
        bottom: 0;
        z-index: 20;
    }
</style>

<div class="customer-chat-viewport">
    
    <!-- Top Bar Messenger Toko -->
    <div style="background: #065f46; color: #fff; padding: 0.75rem 1rem; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0;">
        <div style="display: flex; align-items: center; gap: 0.65rem;">
            <a href="<?= base_url('products') ?>" style="color: #fff; text-decoration: none; font-size: 1.25rem; font-weight: 800; display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; line-height: 1;">&larr;</a>
            <div style="width: 32px; height: 32px; border-radius: 50%; background: #047857; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; border: 1.5px solid #a7f3d0;">
                🏪
            </div>
            <div>
                <h4 style="margin: 0; font-size: 0.88rem; font-weight: 700; color: #ffffff;">UD. LAU STORE</h4>
                <span style="font-size: 0.62rem; color: #a7f3d0; display: block;">Respon cepat via aplikasi</span>
            </div>
        </div>
        <span style="font-size: 0.62rem; background: rgba(255,255,255,0.18); padding: 2px 7px; border-radius: 10px; font-weight: 600;">Official Store</span>
    </div>

    <!-- Banner Info Produk Jika Datang Dari Produk Tertentu -->
    <?php if (!empty($contextProduct)): ?>
        <div style="background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 0.45rem 0.85rem; display: flex; align-items: center; justify-content: space-between; gap: 0.6rem; flex-shrink: 0;">
            <div style="display: flex; align-items: center; gap: 0.55rem; min-width: 0;">
                <div style="width: 34px; height: 34px; border-radius: 6px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; border: 1px solid #e2e8f0;">
                    <?php if (!empty($contextProduct['primary_image'])): ?>
                        <img src="<?= upload_url($contextProduct['primary_image']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <span style="font-size: 0.9rem;">📦</span>
                    <?php endif; ?>
                </div>
                <div style="min-width: 0; line-height: 1.2;">
                    <h5 style="margin: 0; font-size: 0.75rem; font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px;">
                        <?= e($contextProduct['name']) ?>
                    </h5>
                    <span style="font-size: 0.72rem; font-weight: 800; color: #065f46;">
                        Rp <?= number_format((float)$contextProduct['price'], 0, ',', '.') ?>
                    </span>
                </div>
            </div>
            <a href="<?= base_url('products/' . ($contextProduct['slug'] ?? $contextProduct['id'])) ?>" style="font-size: 0.65rem; color: #065f46; font-weight: 700; text-decoration: none; padding: 3px 7px; background: #f0fdf4; border-radius: 6px; border: 1px solid #bbf7d0; white-space: nowrap;">
                Detail ↗
            </a>
        </div>
    <?php endif; ?>

    <!-- Area Percakapan Bubble Chat -->
    <div id="chatBox" class="chat-scroll-area">
        <?php if (empty($messages)): ?>
            <div id="chatEmptyPlaceholder" style="text-align: center; color: #94a3b8; font-size: 0.75rem; margin: auto; max-width: 250px;">
                <div style="font-size: 2.2rem; margin-bottom: 0.35rem;">💬</div>
                Silakan tanyakan ketersediaan produk. Pesan Anda langsung dijawab oleh pemilik toko.
            </div>
        <?php else: ?>
            <?php foreach ($messages as$msg): ?>
                <?php $isMe = ((int)$msg['sender_id'] === (int)\App\Core\Auth::id()); ?>
                <div class="chat-message-row" data-msg-id="<?= (int)$msg['id'] ?>" style="max-width: 82\%; align-self: <?= $isMe ? 'flex-end' : 'flex-start' ?>;">
                    
                    <?php if (!empty($msg['product_name'])): ?>
                        <div style="background: <?= $isMe ? '#044e39' : '#e2e8f0' ?>; color: <?= $isMe ? '#d1fae5' : '#334155' ?>; font-size: 0.65rem; font-weight: 700; padding: 3px 8px; border-radius: 8px 8px 0 0; line-height: 1.2;">
                            📎 <?= e($msg['product_name']) ?>
                        </div>
                    <?php endif; ?>

                    <div style="padding: 0.55rem 0.8rem; border-radius: <?= $isMe ? (!empty($msg['product_name']) ? '0 0 2px 12px' : '12px 12px 2px 12px') : (!empty($msg['product_name']) ? '0 0 12px 2px' : '12px 12px 12px 2px') ?>; background: <?=$isMe ? '#065f46' : '#ffffff' ?>; color: <?= $isMe ? '#ffffff' : '#0f172a' ?>; font-size: 0.78rem; line-height: 1.4; border: <?= $isMe ? 'none' : '1px solid #e2e8f0' ?>; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                        <?= nl2br(e($msg['message'])) ?>
                        <span style="display: block; font-size: 0.58rem; text-align: right; margin-top: 3px; color: <?= $isMe ? '#a7f3d0' : '#94a3b8' ?>;">
                            <?= date('H:i', strtotime($msg['created_at'])) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Kotak Ketik Pesan (Tepat di Atas Keyboard HP) -->
    <form id="customerChatForm" method="POST" action="<?= base_url('chat/send') ?>" class="chat-input-footer">
        <?= csrf_field() ?>
        <input type="hidden" name="receiver_id" value="<?= $ownerId ?>">
        <input type="hidden" name="product_id" value="<?= $productId ?>">
        <input 
            type="text" 
            id="customerMessageInput" 
            name="message" 
            placeholder="Tulis pertanyaan..." 
            required 
            autocomplete="off" 
            style="flex: 1; height: 38px; border-radius: 20px; border: 1.5px solid #cbd5e1; padding: 0 0.95rem; font-size: 0.8rem; outline: none; background: #f8fafc;"
            onfocus="scrollBottomDelayed()">
        <button type="submit" style="width: 38px; height: 38px; border-radius: 50%; border: none; background: #065f46; color: #fff; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(6, 95, 70, 0.3);">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
            </svg>
        </button>
    </form>

</div>

<script>
    const chatContainer = document.getElementById('chatBox');
    function scrollToBottom() {
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    }
    function scrollBottomDelayed() {
        setTimeout(scrollToBottom, 250);
    }
    scrollToBottom();
    window.addEventListener('resize', scrollToBottom);

    const chatForm = document.getElementById('customerChatForm');
    const msgInput = document.getElementById('customerMessageInput');

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const text = msgInput.value.trim();
        if (!text) return;

        const formData = new FormData(chatForm);

        const emptyPlc = document.getElementById('chatEmptyPlaceholder');
        if (emptyPlc) emptyPlc.remove();

        const now = new Date();
        const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');

        const outRow = document.createElement('div');
        outRow.className = 'chat-message-row';
        outRow.style.cssText = 'max-width: 82%; align-self: flex-end;';
        outRow.innerHTML = `
            <div style="padding: 0.55rem 0.8rem; border-radius: 12px 12px 2px 12px; background: #065f46; color: #ffffff; font-size: 0.78rem; line-height: 1.4; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                ${text.replace(/\n/g, '<br>')}
                <span style="display: block; font-size: 0.58rem; text-align: right; margin-top: 3px; color: #a7f3d0;">
                    ${timeStr}
                </span>
            </div>
        `;
        chatContainer.appendChild(outRow);
        scrollToBottom();
        msgInput.value = '';

        fetch('<?= base_url('chat/send') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        }).catch(err => console.error(err));
    });

    setInterval(() => {
        fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newChatBox = doc.getElementById('chatBox');
            if (newChatBox && chatContainer) {
                const currentCount = chatContainer.querySelectorAll('.chat-message-row').length;
                const newCount = newChatBox.querySelectorAll('.chat-message-row').length;
                if (newCount > currentCount) {
                    chatContainer.innerHTML = newChatBox.innerHTML;
                    scrollToBottom();
                }
            }
        })
        .catch(() => {});
    }, 3000);
</script>