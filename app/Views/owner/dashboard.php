<?php
// Format nomor tampilan agar selalu rapi dengan awalan 08... saat diedit Owner
$formattedShopWa = preg_replace('/[^0-9]/', '', (string)($shopWhatsapp ?? '085272270131'));
if (str_starts_with($formattedShopWa, '62')) {
    $formattedShopWa = '0' . substr($formattedShopWa, 2);
}
?>

<!-- 1. Header Greeting & Quick Action -->
<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
    <div>
        <h2 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0 0 0.2rem;">
            Halo, Owner 👋
        </h2>
        <span style="font-size: 0.75rem; color: #64748b; font-weight: 500;">
            <?= e($today ?? date('d F Y')) ?>
        </span>
    </div>

    <div style="position: relative;">
        <button type="button" onclick="document.getElementById('quickActionMenu').classList.toggle('active')" style="display: flex; align-items: center; gap: 4px; background: #e0f2fe; color: #0369a1; border: none; padding: 0.45rem 0.8rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
            + Quick Action
        </button>
        <div id="quickActionMenu" style="display: none; position: absolute; right: 0; top: 35px; width: 195px; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); z-index: 50; overflow: hidden;">
            <a href="<?= base_url('owner/products/create') ?>" style="display: block; padding: 0.7rem 0.85rem; font-size: 0.78rem; font-weight: 600; text-decoration: none; color: #1e293b; border-bottom: 1px solid #f1f5f9;">➕ Tambah Produk</a>
            <a href="<?= base_url('owner/orders') ?>" style="display: block; padding: 0.7rem 0.85rem; font-size: 0.78rem; font-weight: 600; text-decoration: none; color: #1e293b; border-bottom: 1px solid #f1f5f9;">🔍 Cek Booking</a>
            <button type="button" onclick="openWaSettingModal()" style="display: block; width: 100%; text-align: left; padding: 0.7rem 0.85rem; font-size: 0.78rem; font-weight: 700; color: #059669; background: none; border: none; cursor: pointer;">📱 Ganti No. WhatsApp</button>
        </div>
    </div>
</div>

<!-- Notifikasi Berhasil Simpan No WA -->
<?php if (isset($_GET['msg']) && $_GET['msg'] === 'wa_updated'): ?>
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 0.65rem 0.85rem; border-radius: 10px; font-size: 0.75rem; font-weight: 700; margin-bottom: 1.1rem; display: flex; align-items: center; justify-content: space-between;">
        <span>✓ Nomor WhatsApp toko berhasil disimpan!</span>
        <button type="button" onclick="this.parentElement.style.display='none'" style="background: none; border: none; font-size: 1rem; color: #065f46; cursor: pointer;">✕</button>
    </div>
<?php endif; ?>

<!-- 2. Kartu Metrik 2x2 Clean -->
<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.85rem; margin-bottom: 1.4rem;">
    <!-- Pendapatan Hari Ini -->
    <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 16px; padding: 1.1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
        <div style="width: 32px; height: 32px; border-radius: 8px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; margin-bottom: 0.85rem;">
            <span style="font-size: 1rem;">💵</span>
        </div>
        <span style="display: block; font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.35rem;">
            Pendapatan Hari Ini
        </span>
        <div style="font-size: 1.12rem; font-weight: 800; color: #0f172a; margin-bottom: 0.35rem; letter-spacing: -0.02em;">
            <?= e($summary[0]['value'] ?? 'Rp 0') ?>
        </div>
        <span style="font-size: 0.68rem; color: #059669; font-weight: 700;">
            ↑ Berjalan <span style="font-weight: 500; color: #94a3b8;">hari ini</span>
        </span>
    </div>

    <!-- Keuntungan -->
    <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 16px; padding: 1.1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
        <div style="width: 32px; height: 32px; border-radius: 8px; background: #f0fdfa; display: flex; align-items: center; justify-content: center; margin-bottom: 0.85rem;">
            <span style="font-size: 1rem;">📈</span>
        </div>
        <span style="display: block; font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.35rem;">
            Keuntungan
        </span>
        <div style="font-size: 1.12rem; font-weight: 800; color: #0f172a; margin-bottom: 0.35rem; letter-spacing: -0.02em;">
            <?= e($summary[1]['value'] ?? 'Rp 0') ?>
        </div>
        <span style="font-size: 0.68rem; color: #059669; font-weight: 700;">
            ↑ Estimasi <span style="font-weight: 500; color: #94a3b8;">keuntungan</span>
        </span>
    </div>

    <!-- Booking Hari Ini -->
    <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 16px; padding: 1.1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
        <div style="width: 32px; height: 32px; border-radius: 8px; background: #fef2f2; display: flex; align-items: center; justify-content: center; margin-bottom: 0.85rem;">
            <span style="font-size: 1rem;">📅</span>
        </div>
        <span style="display: block; font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.35rem;">
            Booking Hari Ini
        </span>
        <div style="font-size: 1.12rem; font-weight: 800; color: #0f172a; margin-bottom: 0.35rem;">
            <?= e($summary[2]['value'] ?? '0') ?>
        </div>
        <span style="font-size: 0.68rem; color: #059669; font-weight: 700;">
            Pesanan <span style="font-weight: 500; color: #94a3b8;">tercatat</span>
        </span>
    </div>

    <!-- Produk Aktif -->
    <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 16px; padding: 1.1rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
        <div style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; display: flex; align-items: center; justify-content: center; margin-bottom: 0.85rem;">
            <span style="font-size: 1rem;">📦</span>
        </div>
        <span style="display: block; font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.35rem;">
            Produk Aktif
        </span>
        <div style="font-size: 1.12rem; font-weight: 800; color: #0f172a; margin-bottom: 0.35rem;">
            <?= e($summary[3]['value'] ?? '0') ?>
        </div>
        <span style="font-size: 0.68rem; color: #94a3b8; font-weight: 600;">
            Katalog barang
        </span>
    </div>
</div>

<!-- 3. Grafik Penjualan -->
<div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 16px; padding: 1.25rem; margin-bottom: 1.4rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
        <h3 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0;">Grafik Penjualan</h3>
        <span style="font-size: 0.72rem; color: #065f46; font-weight: 700; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 3px 10px; border-radius: 6px;">
            6 Bulan Terakhir
        </span>
    </div>

    <div style="display: flex; height: 160px; gap: 0.5rem; position: relative;">
        <div style="display: flex; flex-direction: column; justify-content: space-between; font-size: 0.65rem; color: #94a3b8; font-weight: 600; padding-bottom: 22px;">
            <span>40 jt</span>
            <span>30 jt</span>
            <span>20 jt</span>
            <span>10 jt</span>
            <span>0</span>
        </div>

        <div style="flex: 1; display: flex; justify-content: space-around; align-items: flex-end; border-left: 1px solid #f1f5f9; border-bottom: 1px solid #e2e8f0; padding-bottom: 0; padding-left: 0.5rem;">
            <?php foreach (($salesChart ?? []) as $bar): 
                $barHeight = max(6, (int)($bar['height'] * 1.15));
            ?>
                <div style="display: flex; flex-direction: column; align-items: center; width: 12%; position: relative;">
                    <div style="background: <?= $bar['height'] > 6 ? '#065f46' : '#cbd5e1' ?>; width: 8px; border-radius: 4px; height: <?= $barHeight ?>px; transition: height 0.3s ease;"></div>
                    <span style="font-size: 0.68rem; color: <?= $bar['height'] > 6 ? '#065f46; font-weight: 800;' : '#64748b; font-weight: 600;' ?> margin-top: 8px; margin-bottom: -22px;">
                        <?= e($bar['label']) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- 4. Widget Stok Menipis -->
<div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 16px; padding: 1.25rem; margin-bottom: 1.4rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
        <h3 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0;">Stok Menipis</h3>
        <a href="<?= base_url('owner/products') ?>" style="font-size: 0.75rem; color: #059669; font-weight: 700; text-decoration: none;">Lihat Semua</a>
    </div>

    <div style="display: flex; justify-content: space-between; font-size: 0.7rem; color: #94a3b8; font-weight: 600; padding-bottom: 0.5rem; border-bottom: 1px solid #f8fafc;">
        <span>Produk</span>
        <span>Stok</span>
    </div>

    <div style="display: flex; flex-direction: column; gap: 0.85rem; padding: 0.75rem 0;">
        <?php if (empty($lowStock)): ?>
            <p style="font-size: 0.78rem; color: #64748b; margin: 0; text-align: center; padding: 0.5rem 0;">
                Semua stok produk aman (di atas 5 pcs).
            </p>
        <?php else: ?>
            <?php foreach ($lowStock as $item): ?>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #e0e7ff; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">✏️</div>
                        <span style="font-size: 0.82rem; font-weight: 600; color: #1e293b;"><?= e($item['name']) ?></span>
                    </div>
                    <span style="font-size: 0.88rem; font-weight: 800; color: #dc2626;"><?= (int)$item['stock'] ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- 5. Widget Barang Terjual Hari Ini -->
<div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 16px; padding: 1.25rem; margin-bottom: 1.4rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
        <div>
            <h3 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0;">Barang Terjual Hari Ini</h3>
            <span style="font-size: 0.68rem; color: #64748b;">Rincian barang yang laku keluar toko</span>
        </div>
        <span style="font-size: 0.72rem; font-weight: 700; color: #065f46; background: #ecfdf5; padding: 3px 8px; border-radius: 6px;">
            <?= count($soldItemsToday) ?> Macam
        </span>
    </div>

    <div style="display: flex; justify-content: space-between; font-size: 0.7rem; color: #94a3b8; font-weight: 600; padding-bottom: 0.5rem; border-bottom: 1px solid #f8fafc;">
        <span>Nama Barang</span>
        <span>Terjual / Omzet</span>
    </div>

    <div style="display: flex; flex-direction: column; gap: 0.75rem; padding: 0.65rem 0 0;">
        <?php if (empty($soldItemsToday)): ?>
            <p style="font-size: 0.78rem; color: #64748b; margin: 0; text-align: center; padding: 0.5rem 0;">
                Belum ada produk yang terjual hari ini.
            </p>
        <?php else: ?>
            <?php foreach ($soldItemsToday as $si): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f8fafc; padding-bottom: 0.65rem;">
                    <div style="display: flex; align-items: center; gap: 0.65rem; min-width: 0;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #ecfdf5; color: #065f46; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0;">
                            🛍️
                        </div>
                        <div style="min-width: 0;">
                            <h4 style="font-size: 0.82rem; font-weight: 700; color: #1e293b; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 170px;">
                                <?= e($si['name']) ?>
                            </h4>
                            <span style="font-size: 0.68rem; color: #64748b;"><?= e($si['category_name']) ?></span>
                        </div>
                    </div>
                    <div style="text-align: right; flex-shrink: 0;">
                        <span style="font-size: 0.82rem; font-weight: 800; color: #065f46; display: block;">
                            <?= (int)$si['total_sold'] ?> <span style="font-size: 0.7rem; font-weight: 600; color: #64748b;">pcs</span>
                        </span>
                        <span style="font-size: 0.68rem; font-weight: 600; color: #94a3b8;">
                            Rp <?= number_format((float)$si['subtotal'], 0, ',', '.') ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- 6. Widget Booking Terbaru -->
<div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 16px; padding: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
        <h3 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0;">Booking Terbaru</h3>
        <a href="<?= base_url('owner/orders') ?>" style="font-size: 0.75rem; color: #059669; font-weight: 700; text-decoration: none;">Lihat Semua</a>
    </div>

    <div style="display: flex; flex-direction: column; gap: 0.85rem;">
        <?php if (empty($recentBookings)): ?>
            <p style="font-size: 0.78rem; color: #64748b; margin: 0; text-align: center; padding: 0.5rem 0;">
                Belum ada pesanan booking masuk.
            </p>
        <?php else: ?>
            <?php foreach ($recentBookings as $booking): 
                $st = strtolower(trim((string)($booking['status'] ?? '')));
                $rawSt = strtolower(trim((string)($booking['raw_status'] ?? $st)));

                $isCancelled = in_array($st, ['dibatalkan', 'batal', 'cancelled']) || in_array($rawSt, ['dibatalkan', 'batal', 'cancelled']);
                $isCompleted = in_array($st, ['selesai', 'completed']) || in_array($rawSt, ['selesai', 'completed']);
                $isReady     = in_array($st, ['siap diambil', 'siap_diambil', 'ready', 'siap']) || in_array($rawSt, ['siap diambil', 'siap_diambil', 'ready', 'siap']);
            ?>
                <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 0.65rem; border-bottom: 1px solid #f8fafc; gap: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.65rem; min-width: 0;">
                        <span style="background: #e0f2fe; color: #0369a1; font-size: 0.7rem; font-weight: 700; padding: 4px 8px; border-radius: 6px; white-space: nowrap; flex-shrink: 0;">
                            <?= e($booking['code']) ?>
                        </span>
                        <div style="min-width: 0;">
                            <h4 style="font-size: 0.82rem; font-weight: 700; color: #1e293b; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= e($booking['customer']) ?></h4>
                            <span style="font-size: 0.68rem; color: #94a3b8; white-space: nowrap;">
                                <?= $isCancelled ? 'Dibatalkan' : ($isCompleted ? 'Selesai' : ($isReady ? 'Siap Diambil' : 'Menunggu')) ?>
                            </span>
                        </div>
                    </div>
                    <div style="flex-shrink: 0;">
                        <?php if ($isCancelled): ?>
                            <span style="font-size: 0.72rem; font-weight: 700; color: #64748b; background: #f1f5f9; border: 1px solid #e2e8f0; padding: 3px 8px; border-radius: 6px; white-space: nowrap; display: inline-block;">
                                Dibatalkan
                            </span>
                        <?php elseif ($isCompleted): ?>
                            <span style="font-size: 0.72rem; font-weight: 800; color: #059669; background: #ecfdf5; padding: 3px 8px; border-radius: 6px; border: 1px solid #a7f3d0; white-space: nowrap; display: inline-block;">
                                Lunas
                            </span>
                        <?php elseif ($isReady): ?>
                            <span style="font-size: 0.72rem; font-weight: 700; color: #059669; background: #dcfce7; padding: 3px 8px; border-radius: 6px; white-space: nowrap; display: inline-block;">
                                Siap Diambil
                            </span>
                        <?php else: ?>
                            <span style="font-size: 0.72rem; font-weight: 700; color: #e11d48; background: #fff1f2; padding: 3px 8px; border-radius: 6px; white-space: nowrap; display: inline-block;">
                                Belum Lunas
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Pop-up Ganti No. WhatsApp Toko dengan Pembersihan Format Otomatis -->
<div id="waSettingModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(3px); z-index: 1000; align-items: center; justify-content: center; padding: 1rem; box-sizing: border-box;">
    <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 360px; padding: 1.25rem; box-shadow: 0 10px 25px rgba(0,0,0,0.15); box-sizing: border-box;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.45rem;">
                <span style="font-size: 1.2rem;">📱</span>
                <h3 style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0;">Kontak WhatsApp Toko</h3>
            </div>
            <button type="button" onclick="closeWaSettingModal()" style="background: none; border: none; font-size: 1.1rem; color: #64748b; cursor: pointer; padding: 0;">✕</button>
        </div>

        <p style="font-size: 0.72rem; color: #64748b; line-height: 1.4; margin: 0 0 1rem;">
            Nomor ini akan terhubung langsung ke tombol WhatsApp di header semua halaman pelanggan.
        </p>

        <!-- Dikirim via GET agar selalu sinkron dan aman tanpa error 404 -->
        <form method="GET" action="<?= base_url('owner/dashboard') ?>" style="margin: 0;">
            <input type="hidden" name="action" value="update_whatsapp">

            <label style="display: block; font-size: 0.72rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem;">
                Nomor WhatsApp Toko
            </label>
            <div style="position: relative; margin-bottom: 1.1rem;">
                <input 
                    type="tel" 
                    id="inputShopWhatsapp"
                    name="shop_whatsapp" 
                    value="<?= e($formattedShopWa) ?>" 
                    required 
                    placeholder="08xxxxxxxxxx" 
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    style="width: 100%; height: 40px; padding: 0 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; font-weight: 700; color: #065f46; letter-spacing: 0.02em; outline: none; box-sizing: border-box;">
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="button" onclick="closeWaSettingModal()" style="flex: 1; height: 36px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="flex: 1.3; height: 36px; background: #065f46; color: #ffffff; border: none; border-radius: 8px; font-size: 0.75rem; font-weight: 800; cursor: pointer;">
                    Simpan Nomor
                </button>
            </div>
        </form>
    </div>
</div>

<style>
#quickActionMenu.active {
    display: block !important;
}
</style>

<script>
function openWaSettingModal() {
    document.getElementById('quickActionMenu').classList.remove('active');
    document.getElementById('waSettingModal').style.display = 'flex';
}

function closeWaSettingModal() {
    document.getElementById('waSettingModal').style.display = 'none';
}
</script>