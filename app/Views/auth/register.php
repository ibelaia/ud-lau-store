<style>
/* Hilangkan ikon mata bawaan Edge / Chrome */
input::-ms-reveal,
input::-ms-clear {
    display: none !important;
}

.req-item {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    color: #94a3b8;
    transition: all 0.2s ease;
}

.req-item.valid {
    color: #16a34a !important;
    font-weight: 700 !important;
}

.req-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 17px;
    height: 17px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #94a3b8;
    font-size: 0.7rem;
    line-height: 1;
    transition: all 0.2s ease;
}

.req-item.valid .req-icon {
    background: #dcfce7 !important;
    color: #16a34a !important;
}
</style>

<div class="auth-wrapper" style="padding: 1.5rem 0 3rem;">
    <div class="auth-card" style="background: #ffffff; border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow-sm);">
        
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; background: var(--primary-light); border-radius: 50%; margin-bottom: 0.5rem;">
                <span style="font-size: 1.5rem;">📝</span>
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--dark); margin-bottom: 0.25rem;">Daftar Akun</h2>
            <p style="font-size: 0.8rem; color: var(--muted); margin: 0;">Buat akun untuk booking online & cek riwayat pesanan</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 0.65rem 0.85rem; border-radius: 8px; font-size: 0.8rem; margin-bottom: 1rem;">
                <?php foreach ($errors as$fieldErrors): ?>
                    <?php foreach ($fieldErrors as$msg): ?>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2px;">
                            <span>⚠️</span>
                            <span><?= e($msg) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= base_url('register') ?>" id="registerForm">
            <?= csrf_field() ?>
            
            <div class="form-group" style="margin-bottom: 0.85rem;">
                <label for="name" style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--dark); margin-bottom: 0.35rem;">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="<?= e($old['name'] ?? '') ?>" placeholder="Nama lengkap Anda" required style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.88rem; outline: none; font-family: inherit; box-sizing: border-box;">
            </div>

            <div class="form-group" style="margin-bottom: 0.85rem;">
                <label for="email" style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--dark); margin-bottom: 0.35rem;">Alamat Email</label>
                <input type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" placeholder="nama@email.com" required style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.88rem; outline: none; font-family: inherit; box-sizing: border-box;">
            </div>

            <div class="form-group" style="margin-bottom: 0.85rem;">
                <label for="phone" style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--dark); margin-bottom: 0.35rem;">Nomor WhatsApp / HP</label>
                <input type="tel" id="phone" name="phone" value="<?= e($old['phone'] ?? '') ?>" placeholder="08xxxxxxxxxx" required style="width: 100%; padding: 0.7rem 0.85rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.88rem; outline: none; font-family: inherit; box-sizing: border-box;">
            </div>

            <div class="form-group" style="margin-bottom: 0.85rem;">
                <label for="reg_password" style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--dark); margin-bottom: 0.35rem;">Password</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" id="reg_password" name="password" placeholder="Buat password baru" required style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 0.85rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.88rem; outline: none; font-family: inherit; box-sizing: border-box;">
                    <button type="button" onclick="togglePasswordVisibility('reg_password', this)" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: #64748b; padding: 4px; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>

                <!-- Indikator Syarat Password Otomatis Centang -->
                <div id="passwordRequirements" style="margin-top: 0.5rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem 0.85rem;">
                    <div style="font-weight: 700; color: #334155; margin-bottom: 0.5rem; font-size: 0.75rem;">Syarat Password:</div>
                    
                    <div id="req-length" class="req-item">
                        <span class="req-icon">●</span>
                        <span>Minimal 8 karakter</span>
                    </div>
                    
                    <div id="req-uppercase" class="req-item">
                        <span class="req-icon">●</span>
                        <span>Minimal 1 huruf kapital (A-Z)</span>
                    </div>
                    
                    <div id="req-number" class="req-item" style="margin-bottom: 0;">
                        <span class="req-icon">●</span>
                        <span>Minimal 1 angka (0-9)</span>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label for="reg_password_confirmation" style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--dark); margin-bottom: 0.35rem;">Konfirmasi Password</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" id="reg_password_confirmation" name="password_confirmation" placeholder="Ulangi password di atas" required style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 0.85rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.88rem; outline: none; font-family: inherit; box-sizing: border-box;">
                    <button type="button" onclick="togglePasswordVisibility('reg_password_confirmation', this)" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: #64748b; padding: 4px; display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                <div id="matchMessage" style="font-size: 0.75rem; margin-top: 0.4rem; font-weight: 600; display: none;"></div>
            </div>

            <button type="submit" id="submitBtn" class="btn-primary" style="width: 100%; padding: 0.8rem; background: var(--primary); color: #fff; border: none; border-radius: 8px; font-size: 0.9rem; font-weight: 700; cursor: pointer; transition: background 0.15s;">
                Daftar Akun Sekarang
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border); font-size: 0.82rem; color: var(--muted);">
            Sudah memiliki akun? 
            <a href="<?= base_url('login') ?>" style="color: var(--primary); font-weight: 700; text-decoration: none;">Masuk di Sini</a>
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

const passInput = document.getElementById('reg_password');
const confInput = document.getElementById('reg_password_confirmation');
const itemLen   = document.getElementById('req-length');
const itemUpper = document.getElementById('req-uppercase');
const itemNum   = document.getElementById('req-number');
const matchTxt  = document.getElementById('matchMessage');

function setStatus(element, isValid) {
    if (!element) return;
    const icon = element.querySelector('.req-icon');
    if (isValid) {
        element.classList.add('valid');
        if (icon) icon.innerHTML = '✔';
    } else {
        element.classList.remove('valid');
        if (icon) icon.innerHTML = '●';
    }
}

function runRealtimeValidation() {
    const val = passInput ? passInput.value : '';
    
    // 1. Cek Minimal 8 Karakter
    setStatus(itemLen, val.length >= 8);

    // 2. Cek Huruf Kapital (A-Z)
    setStatus(itemUpper, /[A-Z]/.test(val));

    // 3. Cek Angka (0-9)
    setStatus(itemNum, /[0-9]/.test(val));

    // 4. Cek Konfirmasi Password
    if (!confInput || !matchTxt) return;
    const confirmVal = confInput.value;

    if (!confirmVal) {
        matchTxt.style.display = 'none';
        return;
    }

    matchTxt.style.display = 'block';
    if (val === confirmVal) {
        matchTxt.style.color = '#16a34a';
        matchTxt.textContent = '✔ Konfirmasi password cocok.';
    } else {
        matchTxt.style.color = '#dc2626';
        matchTxt.textContent = '✕ Konfirmasi password tidak cocok.';
    }
}

// Pasang pendengar ke kedua input agar saling update real-time
['input', 'keyup', 'change', 'paste'].forEach(evt => {
    if (passInput) passInput.addEventListener(evt, runRealtimeValidation);
    if (confInput) confInput.addEventListener(evt, runRealtimeValidation);
});

// Jalankan sekali saat halaman pertama kali termuat
document.addEventListener('DOMContentLoaded', runRealtimeValidation);
</script>