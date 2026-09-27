<div class="auth-wrapper" style="padding: 1.5rem 0 3rem;">
    <div class="auth-card" style="background: #ffffff; border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow-sm);">
        
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; background: var(--primary-light); border-radius: 50%; margin-bottom: 0.5rem;">
                <span style="font-size: 1.5rem;">🔐</span>
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--dark); margin-bottom: 0.25rem;">Selamat Datang</h2>
            <p style="font-size: 0.8rem; color: var(--muted); margin: 0;">Masuk untuk melanjutkan pesanan & kelola toko</p>
        </div>

        <?php if (!empty($errors['email'][0])): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 0.65rem 0.85rem; border-radius: 8px; font-size: 0.8rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>⚠️</span>
                <span><?= e($errors['email'][0]) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= base_url('login') ?>">
            <?= csrf_field() ?>

            <div class="form-group" style="margin-bottom: 1rem;">
                <label for="email" style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--dark); margin-bottom: 0.35rem;">Email atau No. Handphone</label>
                <input type="text" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" placeholder="contoh: nama@email.com" required style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.88rem; outline: none; transition: border-color 0.2s; font-family: inherit; box-sizing: border-box;">
            </div>

            <div class="form-group" style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <label for="login_password" style="font-size: 0.8rem; font-weight: 600; color: var(--dark); margin: 0;">Password</label>
                    <a href="<?= base_url('forgot-password') ?>" style="font-size: 0.75rem; color: var(--primary); text-decoration: none; font-weight: 600;">Lupa?</a>
                </div>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" id="login_password" name="password" placeholder="Masukkan password Anda" required style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 0.85rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.88rem; outline: none; transition: border-color 0.2s; font-family: inherit; box-sizing: border-box;">
                    <button type="button" onclick="togglePasswordVisibility('login_password', this)" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: #64748b; padding: 4px; display: flex; align-items: center; justify-content: center;">
                        <svg class="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: var(--dark); cursor: pointer; user-select: none;">
                    <input type="checkbox" name="remember_me" value="1" style="accent-color: var(--primary); width: 15px; height: 15px;">
                    <span>Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 0.8rem; background: var(--primary); color: #fff; border: none; border-radius: 8px; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: background 0.15s;">
                Masuk Sekarang
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border); font-size: 0.82rem; color: var(--muted);">
            Belum punya akun? 
            <a href="<?= base_url('register') ?>" style="color: var(--primary); font-weight: 700; text-decoration: none;">Daftar Akun Baru</a>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(fieldId, btn) {
    const input = document.getElementById(fieldId);
    if (!input) return;
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    
    btn.innerHTML = isPassword 
        ? `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`
        : `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
}
</script>