<div style="max-width: 480px; margin: 0 auto;">
    
    <div style="margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0 0 4px; display: flex; align-items: center; gap: 6px;">
            Pesan Masuk Pelanggan 💬
        </h2>
        <p style="font-size: 0.78rem; color: #64748b; margin: 0;">
            Balas pertanyaan ketersediaan stok & booking pelanggan
        </p>
    </div>

    <div id="inboxListContainer" style="display: flex; flex-direction: column; gap: 0.65rem;">
        <?php if (empty($conversations)): ?>
            <div style="background: #ffffff; border-radius: 14px; border: 1px dashed #cbd5e1; padding: 2.5rem 1rem; text-align: center;">
                <div style="font-size: 2.2rem; margin-bottom: 0.5rem;">📭</div>
                <div style="font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 4px;">Belum Ada Pesan Masuk</div>
                <div style="font-size: 0.72rem; color: #94a3b8;">Pertanyaan dari pelanggan di toko akan muncul di sini secara otomatis.</div>
            </div>
        <?php else: ?>
            <?php foreach ($conversations as $conv): ?>
                <?php 
                    $unread = (int)($conv['unread_count'] ?? 0); 
                    $custId = (int)$conv['customer_id'];
                ?>
                <a href="<?= base_url('owner/chat/' . $custId) ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 1rem; background: #ffffff; border-radius: 14px; border: 1px solid <?= $unread > 0 ? '#a7f3d0' : '#f1f5f9' ?>; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.02); transition: all 0.2s;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0; flex: 1;">
                        <!-- Avatar Inisial -->
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #d1fae5; color: #065f46; font-size: 0.95rem; font-weight: 800; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #a7f3d0;">
                            <?= strtoupper(substr($conv['customer_name'] ?? 'U', 0, 1)) ?>
                        </div>

                        <!-- Isi Nama & Cuplikan Pesan -->
                        <div style="min-width: 0; flex: 1;">
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 4px; margin-bottom: 2px;">
                                <h4 style="margin: 0; font-size: 0.88rem; font-weight: <?= $unread > 0 ? '800' : '700' ?>; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?= e($conv['customer_name']) ?>
                                </h4>
                                <?php if (!empty($conv['last_time'])): ?>
                                    <span style="font-size: 0.65rem; color: #94a3b8; flex-shrink: 0;">
                                        <?= date('H:i', strtotime($conv['last_time'])) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <p style="margin: 0; font-size: 0.75rem; color: <?= $unread > 0 ? '#047857' : '#64748b' ?>; font-weight: <?= $unread > 0 ? '700' : '400' ?>; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= e($conv['last_message'] ?? 'Memulai percakapan...') ?>
                            </p>
                        </div>
                    </div>

                    <!-- Badge Unread Tiap Pelanggan -->
                    <?php if ($unread > 0): ?>
                        <div style="background: #e11d48; color: #ffffff; font-size: 0.65rem; font-weight: 800; min-width: 18px; height: 18px; border-radius: 9px; display: flex; align-items: center; justify-content: center; padding: 0 4px; margin-left: 0.5rem; flex-shrink: 0;">
                            <?= $unread > 99 ? '99+' : $unread ?>
                        </div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<script>
// Auto update inbox list setiap 3 detik jika ada pesan masuk baru
setInterval(() => {
    fetch(window.location.href, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newList = doc.getElementById('inboxListContainer');
        const curList = document.getElementById('inboxListContainer');
        if (newList && curList) {
            curList.innerHTML = newList.innerHTML;
        }
    })
    .catch(() => {});
}, 3000);
</script>