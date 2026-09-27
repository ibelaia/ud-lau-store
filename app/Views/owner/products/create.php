<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= e($title ?? 'Tambah Produk — UD. LAU STORE') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow-x: hidden;
        }
        .mobile-container {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            min-height: 100vh;
            background: #f8fafc;
            padding-bottom: 80px;
            position: relative;
            box-shadow: 0 0 24px rgba(0,0,0,0.03);
        }
        .owner-topbar {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .content-wrap {
            padding: 0.85rem 1rem;
        }
        .section-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #eef2f6;
            padding: 1rem;
            margin-bottom: 0.85rem;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.02);
            overflow: hidden;
        }
        .field-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.78rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.4rem;
        }
        .input-box {
            width: 100%;
            height: 42px;
            padding: 0 0.85rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.82rem;
            color: #0f172a;
            font-family: inherit;
            background: #ffffff;
            outline: none;
            transition: all 0.2s ease;
        }
        .input-box:focus {
            border-color: #065f46;
            box-shadow: 0 0 0 3px rgba(6, 95, 70, 0.08);
        }
        .input-group-custom {
            display: flex;
            align-items: center;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            background: #ffffff;
            overflow: hidden;
            width: 100%;
            min-width: 0;
            transition: all 0.2s ease;
        }
        .input-group-custom:focus-within {
            border-color: #065f46;
            box-shadow: 0 0 0 3px rgba(6, 95, 70, 0.08);
        }
        .input-prefix {
            padding: 0 0.65rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            background: #f8fafc;
            border-right: 1px solid #e2e8f0;
            height: 42px;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }
        .input-group-custom input {
            border: none;
            height: 42px;
            padding: 0 0.75rem;
            flex: 1;
            min-width: 0;
            width: 100%;
            outline: none;
            font-size: 0.85rem;
            font-family: inherit;
            font-weight: 600;
            color: #0f172a;
            background: transparent;
        }
        .photo-pill {
            font-size: 0.68rem;
            font-weight: 700;
            color: #065f46;
            background: #ecfdf5;
            padding: 2px 8px;
            border-radius: 20px;
            border: 1px solid #a7f3d0;
        }
        .upload-card-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            border-radius: 12px;
            border: 1.5px dashed #cbd5e1;
            background: #f8fafc;
            color: #065f46;
            cursor: pointer;
            flex-shrink: 0;
            transition: all 0.2s ease;
            gap: 2px;
        }
        .upload-card-btn:hover {
            border-color: #065f46;
            background: #f0fdf4;
        }
        .img-thumb-item {
            position: relative;
            width: 72px;
            height: 72px;
            border-radius: 12px;
            overflow: hidden;
            border: 1.5px solid #e2e8f0;
            flex-shrink: 0;
            background: #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }
        .img-thumb-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .btn-del-photo {
            position: absolute;
            top: 3px;
            right: 3px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(15, 23, 42, 0.7);
            color: #ffffff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            cursor: pointer;
        }
        .owner-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;
            height: 60px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-around;
            z-index: 100;
            padding: 2px 4px 4px;
        }
        .owner-nav-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #64748b;
            font-size: 0.62rem;
            font-weight: 600;
            gap: 2px;
            width: 54px;
        }
        .owner-nav-btn.active {
            color: #065f46;
            font-weight: 800;
        }
        .fab-container {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: -18px;
            width: 54px;
            text-decoration: none;
        }
        .fab-button {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #065f46;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            font-weight: 300;
            box-shadow: 0 4px 10px rgba(6, 95, 70, 0.35);
            border: 2.5px solid #ffffff;
        }
        .fab-label {
            font-size: 0.62rem;
            font-weight: 700;
            color: #64748b;
            margin-top: 2px;
        }
    </style>
</head>
<body>

<div class="mobile-container">

    <header class="owner-topbar">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <span style="font-size: 1.15rem; cursor: pointer; color: #1e293b;">☰</span>
            <span style="font-size: 1.05rem; font-weight: 800; color: #065f46; letter-spacing: -0.5px;">LAU STORE</span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.4rem;">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">💬</div>
            <div style="width: 32px; height: 32px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">🔔</div>
            <div style="width: 30px; height: 30px; border-radius: 50%; background: #d1fae5; color: #065f46; font-weight: 800; font-size: 0.78rem; display: flex; align-items: center; justify-content: center; border: 1px solid #a7f3d0;">O</div>
        </div>
    </header>

    <div class="content-wrap">
        <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.85rem;">
            <a href="<?= base_url('owner/products') ?>" style="width: 34px; height: 34px; border-radius: 10px; background: #ffffff; border: 1px solid #cbd5e1; display: flex; align-items: center; justify-content: center; text-decoration: none; color: #0f172a; font-weight: 800; font-size: 1rem;">
                &larr;
            </a>
            <div>
                <h2 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0;">Tambah Produk ATK 📦</h2>
                <p style="font-size: 0.68rem; color: #64748b; margin: 1px 0 0;">Tambahkan barang baru ke katalog toko UD. LAU</p>
            </div>
        </div>

        <form method="POST" action="<?= base_url('owner/products') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Card 1: Foto Produk -->
            <div class="section-card">
                <div class="field-label">
                    <span>Galeri Foto Produk</span>
                    <span id="photoCountBadge" class="photo-pill">0 Foto</span>
                </div>

                <div style="display: flex; gap: 0.55rem; overflow-x: auto; padding: 0.4rem 0 0.6rem; scrollbar-width: none;" id="galleryContainer">
                    <div class="upload-card-btn" onclick="document.getElementById('realImageInput').click()">
                        <span style="font-size: 1.25rem; font-weight: 300;">+</span>
                        <span style="font-size: 0.62rem; font-weight: 700;">Tambah</span>
                    </div>
                </div>

                <input type="file" id="realImageInput" name="images[]" accept="image/*" multiple style="display: none;">
                <span style="font-size: 0.65rem; color: #94a3b8; display: block; margin-top: 2px;">Foto pertama otomatis dijadikan sampul produk (Cover).</span>
            </div>

            <!-- Card 2: Status Unggulan -->
            <div class="section-card" style="background: #fffbeb; border: 1.5px solid #fef3c7;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-size: 1.25rem;">⭐</span>
                        <div>
                            <span style="font-size: 0.82rem; font-weight: 800; color: #92400e; display: block;">Jadikan Produk Unggulan</span>
                            <span style="font-size: 0.65rem; color: #b45309;">Akan tampil di baris teratas dengan label oranye "Unggulan"</span>
                        </div>
                    </div>
                    <label style="position: relative; display: inline-block; width: 44px; height: 24px; cursor: pointer;">
                        <input type="checkbox" name="is_featured" value="1" style="opacity: 0; width: 0; height: 0;" onchange="this.nextElementSibling.style.background = this.checked ? '#d97706' : '#cbd5e1'; this.nextElementSibling.firstElementChild.style.transform = this.checked ? 'translateX(20px)' : 'translateX(0)';">
                        <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #cbd5e1; transition: .3s; border-radius: 34px; display: block;">
                            <span style="position: absolute; height: 18px; width: 18px; left: 3px; bottom: 3px; background: white; transition: .3s; border-radius: 50%; display: block;"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Card 3: Informasi Dasar & Kategori Baru -->
            <div class="section-card">
                <div style="margin-bottom: 0.85rem;">
                    <label class="field-label">Nama Barang</label>
                    <input type="text" name="name" class="input-box" placeholder="Contoh: Kertas HVS A4 75 GSM PaperOne" required>
                </div>

                <div style="margin-bottom: 0.85rem;">
                    <label class="field-label">Kategori</label>
                    <select id="categorySelect" name="category_id" class="input-box" onchange="toggleNewCategoryField()" required style="cursor: pointer;">
                        <option value="">-- Pilih Kategori --</option>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as$cat): 
                                $cId = is_array($cat) ? $cat['id'] :$cat->id;
                                $cName = is_array($cat) ? $cat['name'] :$cat->name;
                            ?>
                                <option value="<?= $cId ?>"><?= e($cName) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <option value="new" style="font-weight: 800; color: #065f46;">➕ + Kategori Baru...</option>
                    </select>
                </div>

                <div id="newCategoryWrapper" style="display: none; background: #ecfdf5; border: 1.5px dashed #a7f3d0; border-radius: 12px; padding: 0.75rem; margin-top: 0.5rem;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: #065f46; margin-bottom: 0.35rem;">Nama Kategori Baru</label>
                    <input type="text" id="newCategoryInput" name="new_category_name" class="input-box" placeholder="Contoh: Buku Tipis" style="background: #ffffff;">
                    <span style="font-size: 0.65rem; color: #047857; display: block; margin-top: 3px;">Kategori baru akan otomatis ditambahkan ke database.</span>
                </div>
            </div>

            <!-- Card 4: Harga, Diskon & Stok -->
            <div class="section-card">
                <div style="display: flex; gap: 0.55rem; margin-bottom: 0.75rem;">
                    <div style="flex: 1.3; min-width: 0;">
                        <label class="field-label">Harga Asli (Normal)</label>
                        <div class="input-group-custom">
                            <span class="input-prefix">Rp</span>
                            <input type="number" id="origPriceInput" name="original_price" placeholder="0" min="0" oninput="calcDiscount()">
                        </div>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <label class="field-label">Diskon (%)</label>
                        <div class="input-group-custom">
                            <input type="number" id="discPercentInput" name="discount_percent" placeholder="0" min="0" max="99" oninput="calcDiscount()">
                            <span class="input-prefix" style="border-right: none; border-left: 1px solid #e2e8f0;">%</span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 0.55rem;">
                    <div style="flex: 1.3; min-width: 0;">
                        <label class="field-label" style="color: #065f46;">
                            <span>Harga Jual / Promo</span>
                            <span id="discBadge" style="display: none; background: #fee2e2; color: #dc2626; font-size: 0.65rem; padding: 1px 6px; border-radius: 4px; font-weight: 800;">HEMAT</span>
                        </label>
                        <div class="input-group-custom" style="border-color: #a7f3d0; background: #f0fdf4;">
                            <span class="input-prefix" style="color: #065f46; background: #ecfdf5; border-color: #a7f3d0;">Rp</span>
                            <input type="number" id="finalPriceInput" name="price" placeholder="0" min="0" required oninput="calcFromFinal()">
                        </div>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <label class="field-label">Jumlah Stok</label>
                        <div class="input-group-custom">
                            <input type="number" name="stock" placeholder="0" min="0" required>
                            <span class="input-prefix" style="border-right: none; border-left: 1px solid #e2e8f0; font-size: 0.7rem;">Pcs</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 5: Deskripsi -->
            <div class="section-card">
                <label class="field-label">Deskripsi Barang</label>
                <textarea name="description" class="input-box" style="height: 85px; padding: 0.65rem 0.85rem; resize: none; line-height: 1.45;" placeholder="Tuliskan spesifikasi produk..."></textarea>
            </div>

            <div style="display: flex; gap: 0.55rem; margin-top: 0.5rem;">
                <a href="<?= base_url('owner/products') ?>" style="flex: 1; height: 42px; background: #ffffff; color: #475569; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 0.8rem; font-weight: 700; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                    Batal
                </a>
                <button type="submit" style="flex: 2; height: 42px; background: #065f46; color: #ffffff; border: none; border-radius: 10px; font-size: 0.82rem; font-weight: 800; cursor: pointer; box-shadow: 0 4px 12px rgba(6, 95, 70, 0.25);">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>

    <nav class="owner-bottom-bar">
        <a href="<?= base_url('owner/dashboard') ?>" class="owner-nav-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>Beranda</span>
        </a>
        <a href="<?= base_url('owner/orders') ?>" class="owner-nav-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            <span>Booking</span>
        </a>
        <div class="fab-container">
            <a href="<?= base_url('owner/products/create') ?>" class="fab-button" title="Tambah Produk">+</a>
            <span class="fab-label">Tambah</span>
        </div>
        <a href="<?= base_url('owner/products') ?>" class="owner-nav-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
            <span>Katalog</span>
        </a>
        <a href="<?= base_url('profile') ?>" class="owner-nav-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Profile</span>
        </a>
    </nav>

</div>

<script>
const dt = new DataTransfer();
const fileInput = document.getElementById('realImageInput');
const gallery = document.getElementById('galleryContainer');
const countBadge = document.getElementById('photoCountBadge');

function toggleNewCategoryField() {
    const sel = document.getElementById('categorySelect');
    const wrap = document.getElementById('newCategoryWrapper');
    const input = document.getElementById('newCategoryInput');
    if (sel.value === 'new') {
        wrap.style.display = 'block';
        input.required = true;
        input.focus();
    } else {
        wrap.style.display = 'none';
        input.required = false;
        input.value = '';
    }
}

function calcDiscount() {
    let orig = parseFloat(document.getElementById('origPriceInput').value) || 0;
    let disc = parseFloat(document.getElementById('discPercentInput').value) || 0;
    const badge = document.getElementById('discBadge');
    const finalInput = document.getElementById('finalPriceInput');

    if (disc > 99) {
        disc = 99;
        document.getElementById('discPercentInput').value = 99;
    }
    if (disc < 0) {
        disc = 0;
        document.getElementById('discPercentInput').value = 0;
    }

    if (orig > 0 && disc > 0) {
        const finalPrice = Math.max(0, Math.round(orig - (orig * (disc / 100))));
        finalInput.value = finalPrice;
        badge.style.display = 'inline-block';
        badge.textContent = `-${disc}%`;
    } else if (orig > 0) {
        finalInput.value = orig;
        badge.style.display = 'none';
    }
}

function calcFromFinal() {
    let orig = parseFloat(document.getElementById('origPriceInput').value) || 0;
    let finalP = parseFloat(document.getElementById('finalPriceInput').value) || 0;
    const badge = document.getElementById('discBadge');

    if (finalP < 0) {
        finalP = 0;
        document.getElementById('finalPriceInput').value = 0;
    }

    if (orig > 0 && finalP < orig) {
        const diff = orig - finalP;
        let disc = Math.round((diff / orig) * 100);
        if (disc > 99) disc = 99;
        document.getElementById('discPercentInput').value = disc;
        badge.style.display = 'inline-block';
        badge.textContent = `-${disc}%`;
    } else {
        document.getElementById('discPercentInput').value = 0;
        badge.style.display = 'none';
    }
}

fileInput.addEventListener('change', function() {
    for (let i = 0; i < this.files.length; i++) {
        dt.items.add(this.files[i]);
    }
    this.files = dt.files;
    renderNewUploads();
});

function removeNewUpload(index) {
    dt.items.remove(index);
    fileInput.files = dt.files;
    renderNewUploads();
}

function renderNewUploads() {
    document.querySelectorAll('.new-upload-box').forEach(el => el.remove());

    Array.from(dt.files).forEach((file, idx) => {
        const itemBox = document.createElement('div');
        itemBox.className = 'img-thumb-item new-upload-box';

        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        itemBox.appendChild(img);

        if (idx === 0) {
            const coverTag = document.createElement('span');
            coverTag.textContent = 'COVER';
            coverTag.style.cssText = 'position: absolute; bottom: 0; left: 0; right: 0; background: #065f46; color: #fff; font-size: 0.55rem; font-weight: 800; text-align: center; padding: 2px 0;';
            itemBox.appendChild(coverTag);
        }

        const delBtn = document.createElement('button');
        delBtn.type = 'button';
        delBtn.className = 'btn-del-photo';
        delBtn.innerHTML = '✕';
        delBtn.onclick = () => removeNewUpload(idx);
        itemBox.appendChild(delBtn);

        gallery.appendChild(itemBox);
    });

    countBadge.textContent = `${dt.files.length} Foto`;
}
</script>

</body>
</html>