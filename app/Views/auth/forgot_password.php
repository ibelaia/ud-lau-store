<div class="auth-wrapper" style="padding: 1.5rem 0 3rem;">
    <div class="auth-card" style="background: #ffffff; border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow-sm);">
        
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; background: #ecfdf5; border-radius: 50%; margin-bottom: 0.5rem;">
                <span style="font-size: 1.5rem;">🔑</span>
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--dark); margin-bottom: 0.25rem;">Lupa Password</h2>
            <p style="font-size: 0.8rem; color: var(--muted); margin: 0;">Masukkan email akun Anda untuk proses pemulihan</p>
        </div>

        <?php if (!empty($error)): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 0.65rem 0.85rem; border-radius: 8px; font-size: 0.8rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>⚠️</span>
                <span><?= e($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 0.85rem; border-radius: 8px; font-size: 0.8rem; margin-bottom: 1rem; line-height: 1.4;">
                <div style="font-weight: 700; margin-bottom: 4px;">✅ Tautan Reset Terkirim!</div>
                <div><?= e($success) ?></div>

                <?php if (!empty($resetLink)): ?>
                    <!-- Muncul otomatis selama testing lokal/offline jika SMTP Gmail belum aktif -->
                    <div style="margin-top: 10px; padding: 8px; background: #ffffff; border: 1px dashed #16a34a; border-radius: 6px;">
                        <span style="font-size: 0.72rem; color: #475569; display: block; margin-bottom: 4px;">Mode Pengujian Lokal (Klik untuk langsung ganti password):</span>
                        <a href="<?= $resetLink ?>" style="display: block; text-align: center; background: #065f46; color: #ffffff; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 0.78rem;">
                            Buka Halaman Buat Password Baru &rarr;
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= base_url('forgot-password') ?>">
            <?= csrf_field() ?>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="email" style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--dark); margin-bottom: 0.35rem;">Alamat Email</label>
                <input type="email" id="email" name="email" value="<?= e($email ?? '') ?>" placeholder="nama@email.com" required style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.88rem; outline: none; transition: border-color 0.2s; font-family: inherit; box-sizing: border-box;">
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 0.8rem; background: var(--primary); color: #fff; border: none; border-radius: 8px; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: background 0.15s;">
                Kirim Permintaan Reset
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border); font-size: 0.82rem; color: var(--muted);">
            Ingat password Anda? 
            <a href="<?= base_url('login') ?>" style="color: var(--primary); font-weight: 700; text-decoration: none;">Kembali ke Login</a>
        </div>
    </div>
</div>