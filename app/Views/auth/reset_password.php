<style>
input::-ms-reveal, input::-ms-clear { display: none !important; }
.req-item { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; font-size: 0.75rem; color: #94a3b8; transition: all 0.2s; }
.req-item.valid { color: #16a34a !important; font-weight: 700; }
.req-icon { width: 17px; height: 17px; border-radius: 50%; background: #e2e8f0; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem; color: #94a3b8; }
.req-item.valid .req-icon { background: #dcfce7 !important; color: #16a34a !important; }
</style>

<div class="auth-wrapper" style="padding: 1.5rem 0 3rem;">
    <div class="auth-card" style="background: #ffffff; border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; box-shadow: var(--shadow-sm);">
        
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; background: #ecfdf5; border-radius: 50%; margin-bottom: 0.5rem;">
                <span style="font-size: 1.5rem;">🔒</span>
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--dark); margin-bottom: 0.25rem;">Buat Password Baru</h2>
            <p style="font-size: 0.8rem; color: var(--muted); margin: 0;">Silakan masukkan password baru untuk akun Anda</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 0.65rem 0.85rem; border-radius: 8px; font-size: 0.8rem; margin-bottom: 1rem;">
                <?php foreach ($errors as $msg): ?>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2px;">
                        <span>⚠️</span><span><?= e($msg) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= base_url('reset-password') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= e($token ?? '') ?>">

            <div class="form-group" style="margin-bottom: 0.85rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--dark); margin-bottom: 0.35rem;">Password Baru</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" id="new_pass" name="password" placeholder="Minimal 8 karakter" required style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 0.85rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.88rem; outline: none; box-sizing: border-box;">
                    <button type="button" onclick="togglePass('new_pass', this)" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: #64748b;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>

                <div style="margin-top: 0.5rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.75rem 0.85rem;">
                    <div style="font-weight: 700; color: #334155; margin-bottom: 0.5rem; font-size: 0.75rem;">Syarat Password:</div>
                    <div id="r-len" class="req-item"><span class="req-icon">●</span><span>Minimal 8 karakter</span></div>
                    <div id="r-up" class="req-item"><span class="req-icon">●</span><span>Minimal 1 huruf kapital (A-Z)</span></div>
                    <div id="r-num" class="req-item" style="margin-bottom: 0;"><span class="req-icon">●</span><span>Minimal 1 angka (0-9)</span></div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--dark); margin-bottom: 0.35rem;">Konfirmasi Password Baru</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" id="new_conf" name="password_confirmation" placeholder="Ulangi password baru" required style="width: 100%; padding: 0.7rem 2.5rem 0.7rem 0.85rem; border: 1px solid var(--border); border-radius: 8px; font-size: 0.88rem; outline: none; box-sizing: border-box;">
                    <button type="button" onclick="togglePass('new_conf', this)" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: #64748b;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>
                <div id="matchNotice" style="font-size: 0.75rem; margin-top: 0.4rem; font-weight: 600; display: none;"></div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; padding: 0.8rem; background: var(--primary); color: #fff; border: none; border-radius: 8px; font-size: 0.9rem; font-weight: 700; cursor: pointer;">
                Perbarui Password Sekarang
            </button>
        </form>
    </div>
</div>

<script>
function togglePass(id, btn) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}
const p = document.getElementById('new_pass');
const c = document.getElementById('new_conf');
const m = document.getElementById('matchNotice');

function validate() {
    const val = p.value;
    setValid('r-len', val.length >= 8);
    setValid('r-up', /[A-Z]/.test(val));
    setValid('r-num', /[0-9]/.test(val));

    if (!c.value) { m.style.display = 'none'; return; }
    m.style.display = 'block';
    if (val === c.value) {
        m.style.color = '#16a34a'; m.textContent = '✔ Konfirmasi password cocok.';
    } else {
        m.style.color = '#dc2626'; m.textContent = '✕ Konfirmasi password tidak cocok.';
    }
}
function setValid(id, ok) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.toggle('valid', ok);
    el.querySelector('.req-icon').innerHTML = ok ? '✔' : '●';
}
p.addEventListener('input', validate);
c.addEventListener('input', validate);
</script>