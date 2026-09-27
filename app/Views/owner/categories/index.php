<div class="categories-management-wrapper" style="padding-bottom: 2.5rem;">

    <!-- Header Halaman -->
    <div style="margin-bottom: 1.25rem;">
        <h2 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0 0 0.15rem;">
            Kategori Produk 📂
        </h2>
        <p style="font-size: 0.75rem; color: #64748b; margin: 0;">
            Kelompokkan jenis barang ATK agar pembeli mudah menemukan barang
        </p>
    </div>

    <!-- Form Cepat Tambah Kategori -->
    <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 16px; padding: 1rem; margin-bottom: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
        <h3 style="font-size: 0.84rem; font-weight: 700; color: #0f172a; margin: 0 0 0.65rem;">
            + Tambah Kategori Baru
        </h3>

        <form method="POST" action="<?= base_url('owner/categories') ?>">
            <?= csrf_field() ?>
            <div style="display: flex; gap: 0.5rem;">
                <input type="text" name="name" placeholder="Nama Kategori (contoh: Perlengkapan Menggambar)" required style="flex: 1; padding: 0.65rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.82rem; outline: none; background: #fff; font-family: inherit;">
                <button type="submit" style="background: #065f46; color: #ffffff; border: none; border-radius: 10px; padding: 0 1rem; font-size: 0.8rem; font-weight: 700; cursor: pointer; white-space: nowrap;">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <!-- Daftar Kategori yang Ada -->
    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        <?php foreach ($categories as $cat): ?>
            <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 14px; padding: 0.9rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between;">
                
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #f0fdf4; border: 1px solid #dcfce7; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <?= $cat['icon'] ?>
                    </div>
                    <div>
                        <h4 style="font-size: 0.88rem; font-weight: 700; color: #0f172a; margin: 0 0 0.15rem;">
                            <?= e($cat['name']) ?>
                        </h4>
                        <span style="font-size: 0.7rem; color: #64748b; font-weight: 500;">
                            <?= (int)$cat['products_count'] ?> item terdaftar &bull; <code style="font-size: 0.65rem; color: #059669;"><?= e($cat['slug']) ?></code>
                        </span>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.4rem;">
                    <form method="POST" action="<?= base_url('owner/categories/' . $cat['id']) ?>" onsubmit="return confirm('Hapus kategori ini?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" style="width: 32px; height: 32px; background: #fee2e2; color: #dc2626; border: none; border-radius: 8px; font-size: 0.8rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                            🗑️
                        </button>
                    </form>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

</div>