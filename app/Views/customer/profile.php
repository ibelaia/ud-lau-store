<div class="customer-profile-wrapper" style="max-width: 480px; margin: 0 auto; padding-bottom: 2rem;">

    <!-- Kartu Informasi Pengguna -->
    <div style="background: #ffffff; border-radius: 20px; border: 1px solid #f1f5f9; padding: 1.5rem 1rem; text-align: center; margin-bottom: 1.25rem; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        
        <?php
            $userName = $user['name'] ?? 'Pelanggan Setia';
            $userEmail = $user['email'] ?? '-';
            $userPhone = $user['phone'] ?? '';
            $initial = strtoupper(substr($userName, 0, 1));
            $isOwnerAcc = !empty($isOwner);
        ?>

        <div style="width: 70px; height: 70px; border-radius: 50%; background: <?= $isOwnerAcc ? '#d1fae5' : '#e0f2fe' ?>; color: <?= $isOwnerAcc ? '#065f46' : '#0369a1' ?>; display: inline-flex; align-items: center; justify-content: center; font-size: 1.75rem; font-weight: 800; margin-bottom: 0.75rem;">
            <?= e($initial) ?>
        </div>

        <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0 0 0.25rem;">
            <?= e($userName) ?>
        </h2>
        <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.65rem;">
            <?= e($userEmail) ?>
        </p>

        <?php if ($isOwnerAcc): ?>
            <span style="display: inline-block; background: #065f46; color: #ffffff; font-size: 0.68rem; font-weight: 800; padding: 3px 12px; border-radius: 20px; letter-spacing: 0.5px;">
                OWNER
            </span>
        <?php else: ?>
            <span style="display: inline-block; background: #e0f2fe; color: #0369a1; font-size: 0.68rem; font-weight: 800; padding: 3px 12px; border-radius: 20px; letter-spacing: 0.5px;">
                PELANGGAN
            </span>
        <?php endif; ?>

    </div>

    <?php if ($isOwnerAcc): ?>
        <!-- Menu Pintas ke Dashboard Toko (Hanya Muncul jika Akun Pemilik) -->
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 14px; padding: 0.85rem 1rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <strong style="font-size: 0.8rem; color: #065f46; display: block;">Mode Pemilik Toko</strong>
                <span style="font-size: 0.7rem; color: #047857;">Buka panel kontrol pesanan & produk</span>
            </div>
            <a href="<?= base_url('owner/dashboard') ?>" style="padding: 5px 12px; background: #065f46; color: #ffffff; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-decoration: none;">
                Buka Panel &rarr;
            </a>
        </div>
    <?php endif; ?>

    <!-- Menu Aktivitas Belanja (Selalu Muncul di Jalur Toko) -->
    <h3 style="font-size: 0.85rem; font-weight: 800; color: #0f172a; margin: 0 0 0.65rem 0.25rem;">Aktivitas Belanja</h3>
    <div style="background: #ffffff; border-radius: 16px; border: 1px solid #f1f5f9; overflow: hidden; margin-bottom: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
        <a href="<?= base_url('orders') ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 1rem; text-decoration: none; color: #1e293b; border-bottom: 1px solid #f8fafc; font-size: 0.82rem; font-weight: 600;">
            <span style="display: flex; align-items: center; gap: 8px;">🛍️ Riwayat Booking & Pesanan</span>
            <span style="color: #94a3b8;">&rarr;</span>
        </a>
        <a href="<?= base_url('wishlist') ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 1rem; text-decoration: none; color: #1e293b; border-bottom: 1px solid #f8fafc; font-size: 0.82rem; font-weight: 600;">
            <span style="display: flex; align-items: center; gap: 8px;">❤️ Produk Favorit</span>
            <span style="color: #94a3b8;">&rarr;</span>
        </a>
        <a href="<?= base_url('chat') ?>" style="display: flex; align-items: center; justify-content: space-between; padding: 0.85rem 1rem; text-decoration: none; color: #1e293b; font-size: 0.82rem; font-weight: 600;">
            <span style="display: flex; align-items: center; gap: 8px;">💬 Chat Pemilik Toko</span>
            <span style="color: #94a3b8;">&rarr;</span>
        </a>
    </div>

    <!-- Form Data Pengguna -->
    <h3 style="font-size: 0.85rem; font-weight: 800; color: #0f172a; margin: 0 0 0.65rem 0.25rem;">Informasi Akun</h3>
    <form action="<?= base_url('profile') ?>" method="POST" style="background: #ffffff; border-radius: 16px; border: 1px solid #f1f5f9; padding: 1.15rem; margin-bottom: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
        <?= function_exists('csrf_field') ? csrf_field() : '' ?>
        <div style="margin-bottom: 0.85rem;">
            <label style="display: block; font-size: 0.72rem; font-weight: 700; color: #475569; margin-bottom: 4px;">Nama Lengkap</label>
            <input type="text" name="name" value="<?= e($userName) ?>" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0.55rem 0.75rem; font-size: 0.8rem; outline: none; box-sizing: border-box;">
        </div>
        <div style="margin-bottom: 1rem;">
            <label style="display: block; font-size: 0.72rem; font-weight: 700; color: #475569; margin-bottom: 4px;">Nomor WhatsApp / HP</label>
            <input type="text" name="phone" value="<?= e($userPhone) ?>" placeholder="Contoh: 08123456789" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0.55rem 0.75rem; font-size: 0.8rem; outline: none; box-sizing: border-box;">
        </div>
        <button type="submit" style="width: 100%; height: 38px; background: #065f46; color: #ffffff; border: none; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">
            Simpan Perubahan
        </button>
    </form>

    <!-- Tombol Logout -->
    <a href="<?= base_url('logout') ?>" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 44px; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 14px; color: #dc2626; font-size: 0.82rem; font-weight: 700; text-decoration: none; box-sizing: border-box;">
        Keluar dari Akun (Logout)
    </a>

</div>