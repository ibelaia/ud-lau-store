<style>
    /* Hilangkan scroll body dan sembunyikan bottom bar khusus di ruang chat */
    html, body {
        height: 100% !important;
        overflow: hidden !important;
        padding-bottom: 0 !important;
    }
    .owner-bottom-bar {
        display: none !important;
    }
    main {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
    }

    /* Kunci kontainer chat di layar ponsel maupun desktop */
    .chat-app-viewport {
        position: fixed;
        top: 58px;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 480px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        z-index: 80;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
    }

    .chat-header-area {
        background: #065f46;
        color: #ffffff;
        padding: 0.65rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .chat-message-list {
        flex: 1 1 auto;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        padding: 0.85rem 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        background: #f1f5f9;
    }

    .chat-footer-form {
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        padding: 0.5rem 0.75rem;
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-shrink: 0;
        position: sticky;
        bottom: 0;
        z-index: 10;
    }
</style>

<div class="chat-app-viewport">
    
    <!-- Header Chat Customer -->
    <div class="chat-header-area">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="<?= base_url('owner/chat') ?>" style="color: #ffffff; text-decoration: none; font-size: 1.25rem; font-weight: 800; display: flex; align-items: center; justify-content: center; line-height: 1;">&larr;</a>
            <div>
                <h4 style="margin: 0; font-size: 0.9rem; font-weight: 700; color: #ffffff; line-height: 1.2;"><?= e($customer['name']) ?></h4>
                <span style="font-size: 0.65rem; color: #a7f3d0;">Pelanggan UD. LAU Store</span>
            </div>
        </div>
    </div>

    <!-- Bubble List Chat -->
    <div id="ownerChatBox" class="chat-message-list">
        <?php foreach ($messages as $msg): ?>
            <?php $isOwner = ((int)$msg['sender_id'] === (int)\App\Core\Auth::id()); ?>
            <div class="owner-msg-row" data-id="<?= (int)$msg['id'] ?>" style="max-width: 82%; align-self: <?= $isOwner ? 'flex-end' : 'flex-start' ?>;">
                <?php if (!empty($msg['product_name'])): ?>
                    <div style="background: <?= $isOwner ? '#044e39' : '#e2e8f0' ?>; color: <?= $isOwner ? '#d1fae5' : '#334155' ?>; font-size: 0.65rem; font-weight: 700; padding: 4px 8px; border-radius: 8px 8px 0 0; line-height: 1.2;">
                        📎 <?= e($msg['product_name']) ?>
                    </div>
                <?php endif; ?>
                <div style="padding: 0.55rem 0.8rem; border-radius: <?= $isOwner ? '12px 12px 2px 12px' : '12px 12px 12px 2px' ?>; background: <?= $isOwner ? '#065f46' : '#ffffff' ?>; color: <?= $isOwner ? '#ffffff' : '#0f172a' ?>; font-size: 0.78rem; line-height: 1.4; border: <?= $isOwner ? 'none' : '1px solid #e2e8f0' ?>; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                    <?= nl2br(e($msg['message'])) ?>
                    <span style="display: block; font-size: 0.58rem; text-align: right; margin-top: 3px; color: <?= $isOwner ? '#a7f3d0' : '#94a3b8' ?>;">
                        <?= date('H:i', strtotime($msg['created_at'])) ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Bar Pengetikan Pesan -->
    <form id="ownerChatForm" method="POST" action="<?= base_url('owner/chat/reply') ?>" class="chat-footer-form">
        <?= csrf_field() ?>
        <input type="hidden" name="customer_id" value="<?= $customerId ?>">
        <input 
            type="text" 
            name="message" 
            id="ownerMessageInput"
            placeholder="Tulis balasan untuk pelanggan..." 
            required 
            autocomplete="off" 
            style="flex: 1; height: 38px; border-radius: 20px; border: 1.5px solid #cbd5e1; padding: 0 0.95rem; font-size: 0.8rem; outline: none; background: #f8fafc;"
            onfocus="scrollBottomDelayed()">
        <button type="submit" style="width: 38px; height: 38px; border-radius: 50%; border: none; background: #065f46; color: #ffffff; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(6,95,70,0.3); flex-shrink: 0;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
            </svg>
        </button>
    </form>

</div>

<script>
const box = document.getElementById('ownerChatBox');

function scrollToBottom() {
    if (box) box.scrollTop = box.scrollHeight;
}

function scrollBottomDelayed() {
    setTimeout(scrollToBottom, 250);
}

document.addEventListener('DOMContentLoaded', scrollToBottom);
window.addEventListener('resize', scrollToBottom);

// Auto-polling pembaruan chat masuk dari customer setiap 3 detik
setInterval(() => {
    fetch(window.location.href, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newBox = doc.getElementById('ownerChatBox');
        if (newBox && box) {
            const currentCount = box.querySelectorAll('.owner-msg-row').length;
            const newCount = newBox.querySelectorAll('.owner-msg-row').length;
            if (newCount > currentCount) {
                box.innerHTML = newBox.innerHTML;
                scrollToBottom();
            }
        }
    })
    .catch(() => {});
}, 3000);
</script>