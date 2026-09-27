<?php
$pId         = is_array($product) ? ($product['id'] ?? '') : ($product->id ?? '');
$pName       = is_array($product) ? ($product['name'] ?? '') : ($product->name ?? '');
$pCatId      = is_array($product) ? ($product['category_id'] ?? 0) : ($product->category_id ?? 0);
$pPrice      = is_array($product) ? ($product['price'] ?? 0) : ($product->price ?? 0);
$pOrigPrice  = is_array($product) ? ($product['original_price'] ?? $pPrice) : ($product->original_price ?? $pPrice);
$pDiscount   = is_array($product) ? ($product['discount_percent'] ?? 0) : ($product->discount_percent ?? 0);
$pFeatured   = is_array($product) ? ($product['is_featured'] ?? 0) : ($product->is_featured ?? 0);
$pStock      = is_array($product) ? ($product['stock'] ?? 0) : ($product->stock ?? 0);
$pDesc       = is_array($product) ? ($product['description'] ?? '') : ($product->description ?? '');
$rawImg      = is_array($product) ? ($product['image'] ?? '') : ($product->image ?? '');

$existingImages = [];
if (!empty($rawImg)) {
    $decoded = json_decode($rawImg, true);
    if (is_array($decoded) && !empty($decoded)) {
        $existingImages = $decoded;
    } else {
        $existingImages[] = $rawImg;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= e($title ?? 'Edit Produk — UD. LAU STORE') ?></title>
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
        }
        .input-group-custom input {
            border: none;
            height: 42px;
            padding: 0 0.75rem;
            flex: 1;
            outline: none;
            font-size: 0.85rem;
            font-family: inherit;
            font-weight: 600;
            color: #0f172a;
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
        .cover-tag {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(6, 95, 70, 0.9);
            color: #ffffff;
            font-size: 0.55rem;
            font-weight: 800;
            text-align: center;
            padding: 2px 0;
            letter-spacing: 0.5px;
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
        .btn-del-photo:hover {
            background: #dc2626;
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
                <h2 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0;">Edit Produk ATK ✏️</h2>
                <p style="font-size: 0.68rem; color: #64748b; margin: 1px 0 0;">Perbarui foto, kategori, diskon, dan stok</p>
            </div>
        </div>

        <form method="POST" action="<?= base_url('owner/products/' . $pId) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">

            <!-- Card 1: Galeri Foto -->
            <div class="section-card">
                <div class="field-label">
                    <span>Galeri Foto Produk</span>
                    <span id="photoCountBadge" class="photo-pill"><?= count($existingImages) ?> Foto</span>
                </div>

                <div style="display: flex; gap: 0.55rem; overflow-x: auto; padding: 0.4rem 0 0.6rem; scrollbar-width: none;" id="galleryContainer">
                    <div class="upload-card-btn" onclick="document.getElementById('realImageInput').click()">
                        <span style="font-size: 1.25rem; font-weight: 300;">+</span>
                        <span style="font-size: 0.62rem; font-weight: 700;">Tambah</span>
                    </div>

                    <?php foreach ($existingImages as $idx => $eImg): ?>
                        <div class="img-thumb-item existing-img-box" id="exist-box-<?= $idx ?>">
                            <img src="<?= base_url($eImg) ?>">
                            <?php if ($idx === 0): ?>
                                <span class="cover-tag">COVER</span>
                            <?php endif; ?>
                            <input type="hidden" name="existing_images[]" value="<?= e($eImg) ?>">
                            <button type="button" class="btn-del-photo" onclick="removeExistingImg('exist-box-<?= $idx ?>')">✕</button>
                        </div>
                    <?php endforeach; ?>
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
                        <input type="checkbox" name="is_featured" value="1" <?= (!empty($pFeatured)) ? 'checked' : '' ?> style="opacity: 0; width: 0; height: 0;" onchange="this.nextElementSibling.style.background = this.checked ? '#d97706' : '#cbd5e1'; this.nextElementSibling.firstElementChild.style.transform = this.checked ? 'translateX(20px)' : 'translateX(0)';">
                        <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: <?= (!empty($pFeatured)) ? '#d97706' : '#cbd5e1' ?>; transition: .3s; border-radius: 34px; display: block;">
                            <span style="position: absolute; height: 18px; width: 18px; left: 3px; bottom: 3px; background: white; transition: .3s; border-radius: 50%; transform: <?= (!empty($pFeatured)) ? 'translateX(20px)' : 'translateX(0)' ?>; display: block;"></span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Card 3: Informasi Dasar & Kategori Baru -->
            <div class="section-card">
                <div style="margin-bottom: 0.85rem;">
                    <label class="field-label">Nama Barang</label>
                    <input type="text" name="name" class="input-box" value="<?= e($pName) ?>" placeholder="Contoh: Kertas HVS A4 75 GSM" required>
                </div>

                <div style="margin-bottom: 0.85rem;">
                    <label class="field-label">Kategori</label>
                    <select id="categorySelect" name="category_id" class="input-box" onchange="toggleNewCategoryField()" required style="cursor: pointer;">
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): 
                                $cId = is_array($cat) ? $cat['id'] : $cat->id;
                                $cName = is_array($cat) ? $cat['name'] : $cat->name;
                            ?>
                                <option value="<?= $cId ?>" <?= ((int)$pCatId === (int)$cId) ? 'selected' : '' ?>>
                                    <?= e($cName) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <option value="new" style="font-weight: 800; color: #065f46;">➕ + Kategori Baru...</option>
                    </select>
                </div>

                <div id="newCategoryWrapper" style="display: none; background: #ecfdf5; border: 1.5px dashed #a7f3d0; border-radius: 12px; padding: 0.75rem; margin-top: 0.5rem;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 800; color: #065f46; margin-bottom: 0.35rem;">Nama Kategori Baru</label>
                    <input type="text" id="newCategoryInput" name="new_category_name" class="input-box" placeholder="Contoh: Buku Tebal" style="background: #ffffff;">
                    <span style="font-size: 0.65rem; color: #047857; display: block; margin-top: 3px;">Kategori ini otomatis disimpan ke database sistem.</span>
                </div>
            </div>

            <!-- Card 4: Harga Asli, Diskon & Harga Jual -->
            <div class="section-card">
                <div style="display: flex; gap: 0.55rem; margin-bottom: 0.75rem;">
                    <div style="flex: 1.4;">
                        <label class="field-label">Harga Asli (Normal)</label>
                        <div class="input-group-custom">
                            <span class="input-prefix">Rp</span>
                            <input type="number" id="origPriceInput" name="original_price" value="<?= (int)$pOrigPrice ?>" placeholder="0" oninput="calcDiscount()">
                        </div>
                    </div>
                    <div style="flex: 1;">
                        <label class="field-label">Diskon (%)</label>
                        <div class="input-group-custom">
                            <input type="number" id="discPercentInput" name="discount_percent" value="<?= (int)$pDiscount ?>" placeholder="0" min="0" max="99" oninput="calcDiscount()">
                            <span class="input-prefix" style="border-right: none; border-left: 1px solid #e2e8f0;">%</span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 0.55rem;">
                    <div style="flex: 1.4;">
                        <label class="field-label" style="color: #065f46;">
                            <span>Harga Jual / Promo</span>
                            <span id="discBadge" style="<?= ($pDiscount > 0) ? 'display: inline-block;' : 'display: none;' ?> background: #fee2e2; color: #dc2626; font-size: 0.65rem; padding: 1px 6px; border-radius: 4px; font-weight: 800;">-<?= (int)$pDiscount ?>%</span>
                        </label>
                        <div class="input-group-custom" style="border-color: #a7f3d0; background: #f0fdf4;">
                            <span class="input-prefix" style="color: #065f46; background: #ecfdf5; border-color: #a7f3d0;">Rp</span>
                            <input type="number" id="finalPriceInput" name="price" value="<?= (int)$pPrice ?>" placeholder="0" required oninput="calcFromFinal()">
                        </div>
                    </div>
                    <div style="flex: 1;">
                        <label class="field-label">Jumlah Stok</label>
                        <div class="input-group-custom">
                            <input type="number" name="stock" value="<?= (int)$pStock ?>" placeholder="0" required>
                            <span class="input-prefix" style="border-right: none; border-left: 1px solid #e2e8f0; font-size: 0.7rem;">Pcs</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 5: Keterangan -->
            <div class="section-card">
                <label class="field-label">Deskripsi Barang</label>
                <textarea name="description" class="input-box" style="height: 85px; padding: 0.65rem 0.85rem; resize: none; line-height: 1.45;" placeholder="Tuliskan spesifikasi produk..."><?= e($pDesc) ?></textarea>
            </div>

            <div style="display: flex; gap: 0.55rem; margin-top: 0.5rem;">
                <a href="<?= base_url('owner/products') ?>" style="flex: 1; height: 42px; background: #ffffff; color: #475569; border: 1.5px solid #cbd5e1; border-radius: 10px; font-size: 0.8rem; font-weight: 700; text-decoration: none; display: flex; align-items: center; justify-content: center;">
                    Batal
                </a>
                <button type="submit" style="flex: 2; height: 42px; background: #065f46; color: #ffffff; border: none; border-radius: 10px; font-size: 0.82rem; font-weight: 800; cursor: pointer; box-shadow: 0 4px 12px rgba(6, 95, 70, 0.25);">
                    Simpan Perubahan
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
        <a href="<?= base_url('owner/products') ?>" class="owner-nav-btn active">
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
    const orig = parseFloat(document.getElementById('origPriceInput').value) || 0;
    const disc = parseFloat(document.getElementById('discPercentInput').value) || 0;
    const badge = document.getElementById('discBadge');

    if (orig > 0 && disc > 0) {
        const finalPrice = Math.round(orig - (orig * (disc / 100)));
        document.getElementById('finalPriceInput').value = finalPrice;
        badge.style.display = 'inline-block';
        badge.textContent = `-${disc}%`;
    } else if (orig > 0) {
        document.getElementById('finalPriceInput').value = orig;
        badge.style.display = 'none';
    }
}

function calcFromFinal() {
    const orig = parseFloat(document.getElementById('origPriceInput').value) || 0;
    const finalP = parseFloat(document.getElementById('finalPriceInput').value) || 0;
    const badge = document.getElementById('discBadge');

    if (orig > 0 && finalP < orig) {
        const diff = orig - finalP;
        const disc = Math.round((diff / orig) * 100);
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

function removeExistingImg(elementId) {
    const el = document.getElementById(elementId);
    if (el) el.remove();
    updateTotalPhotoCount();
}

function renderNewUploads() {
    document.querySelectorAll('.new-upload-box').forEach(el => el.remove());

    Array.from(dt.files).forEach((file, idx) => {
        const itemBox = document.createElement('div');
        itemBox.className = 'img-thumb-item new-upload-box';

        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        itemBox.appendChild(img);

        const delBtn = document.createElement('button');
        delBtn.type = 'button';
        delBtn.className = 'btn-del-photo';
        delBtn.innerHTML = '✕';
        delBtn.onclick = () => removeNewUpload(idx);
        itemBox.appendChild(delBtn);

        gallery.appendChild(itemBox);
    });

    updateTotalPhotoCount();
}

function updateTotalPhotoCount() {
    const existingCount = document.querySelectorAll('.existing-img-box').length;
    const newCount = dt.files.length;
    countBadge.textContent = `${existingCount + newCount} Foto`;
}
</script>

</body>
</html>