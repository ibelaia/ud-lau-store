<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= e($title ?? 'Katalog Produk ATK — Panel Owner') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            padding-bottom: 75px;
            width: 100%;
            overflow-x: hidden;
        }
        .container {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            padding: 0.75rem 0.85rem;
        }
        .owner-topbar {
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 0.85rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            max-width: 480px;
            margin: 0 auto;
        }
        .owner-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;
            height: 65px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-around;
            z-index: 100;
            padding: 4px 4px 6px;
            box-sizing: border-box;
        }
        .owner-nav-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #64748b;
            font-size: 0.65rem;
            font-weight: 600;
            gap: 2px;
            width: 58px;
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
            margin-top: -20px;
            width: 58px;
            text-decoration: none;
        }
        .fab-button {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #065f46;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 300;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(6, 95, 70, 0.35);
            border: 3px solid #ffffff;
        }
        .fab-label {
            font-size: 0.65rem;
            font-weight: 700;
            color: #64748b;
            margin-top: 2px;
            text-align: center;
            display: block;
        }
        .fab-label.active {
            color: #065f46;
            font-weight: 800;
        }
    </style>
</head>
<body>

<!-- Header Atas Panel Owner -->
<header class="owner-topbar">
    <div style="display: flex; align-items: center; gap: 0.75rem;">
        <span style="font-size: 1.25rem; cursor: pointer; color: #1e293b;">☰</span>
        <span style="font-size: 1.15rem; font-weight: 800; color: #065f46; letter-spacing: -0.5px;">LAU STORE</span>
    </div>
    <div style="display: flex; align-items: center; gap: 0.5rem;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 0.95rem;">💬</div>
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 0.95rem;">🔔</div>
        <div style="width: 34px; height: 34px; border-radius: 50%; background: #d1fae5; color: #065f46; font-weight: 800; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; border: 1px solid #a7f3d0;">O</div>
    </div>
</header>

<!-- Konten Katalog Owner -->
<main class="container">

    <!-- Judul & Tombol Tambah -->
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; margin-bottom: 0.85rem;">
        <div>
            <h2 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">Katalog Produk ATK 📦</h2>
            <p style="font-size: 0.72rem; color: #64748b; margin: 2px 0 0;">Total <?= count($products ?? []) ?> item terdaftar</p>
        </div>

        <a href="<?= base_url('owner/products/create') ?>" style="display: inline-flex; align-items: center; gap: 0.25rem; background: #065f46; color: #ffffff; text-decoration: none; padding: 0.45rem 0.85rem; border-radius: 8px; font-size: 0.75rem; font-weight: 700; box-shadow: 0 2px 6px rgba(6,95,70,0.2);">
            + Tambah
        </a>
    </div>

    <!-- Kotak Pencarian -->
    <form method="GET" action="<?= base_url('owner/products') ?>" style="margin-bottom: 0.85rem; width: 100%;">
        <div style="display: flex; gap: 0.4rem; width: 100%;">
            <input type="search" name="q" value="<?= e($q ?? '') ?>" placeholder="Cari nama barang ATK..." style="flex: 1; min-width: 0; height: 38px; padding: 0 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.82rem; outline: none; background: #fff;">
            <button type="submit" style="background: #065f46; color: #fff; border: none; border-radius: 8px; padding: 0 1rem; font-size: 0.8rem; font-weight: 700; cursor: pointer;">Cari</button>
        </div>
    </form>

    <!-- Filter Kategori -->
    <div style="display: flex; gap: 0.4rem; overflow-x: auto; padding-bottom: 0.4rem; margin-bottom: 0.85rem; scrollbar-width: none;">
        <a href="<?= base_url('owner/products') ?>" style="flex: 0 0 auto; text-decoration: none; padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.72rem; font-weight: 700; <?= empty($selectedCategory) ? 'background: #065f46; color: #fff;' : 'background: #f1f5f9; color: #475569;' ?>">
            Semua
        </a>
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): 
                $catId = is_array($cat) ? (string)$cat['id'] : (string)$cat->id;
                $catName = is_array($cat) ? $cat['name'] : $cat->name;
                $isActive = ((string)($selectedCategory ?? '') === $catId);
            ?>
                <a href="<?= base_url('owner/products?category=' . $catId) ?>" style="flex: 0 0 auto; text-decoration: none; padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.72rem; font-weight: 600; <?= $isActive ? 'background: #065f46; color: #fff;' : 'background: #f1f5f9; color: #475569;' ?>">
                    <?= e($catName) ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Daftar List Produk -->
    <div style="display: flex; flex-direction: column; gap: 0.65rem; width: 100%;">
        <?php if (empty($products)): ?>
            <div style="text-align: center; padding: 2.5rem 1rem; background: #fff; border-radius: 14px; border: 1px dashed #cbd5e1;">
                <span style="font-size: 2.2rem; display: block; margin-bottom: 0.5rem;">🔍</span>
                <p style="color: #64748b; font-size: 0.8rem; margin: 0;">Produk tidak ditemukan.</p>
            </div>
        <?php else: ?>
            <?php foreach ($products as $item): 
                $pId = is_array($item) ? ($item['id'] ?? 0) : ($item->id ?? 0);
                $pName = is_array($item) ? ($item['name'] ?? '') : ($item->name ?? '');
                $pCategory = is_array($item) ? ($item['category_name'] ?? 'ATK') : ($item->category_name ?? 'ATK');
                $pPrice = is_array($item) ? (float)($item['price'] ?? 0) : (float)($item->price ?? 0);
                $pStock = is_array($item) ? (int)($item['stock'] ?? 0) : (int)($item->stock ?? 0);
            ?>
                <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 12px; padding: 0.75rem; box-shadow: 0 1px 4px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; width: 100%;">
                    <div style="display: flex; align-items: center; gap: 0.65rem; min-width: 0; flex: 1;">
                        <div style="width: 42px; height: 42px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
                            📄
                        </div>
                        <div style="min-width: 0; flex: 1;">
                            <h4 style="font-size: 0.82rem; font-weight: 700; color: #0f172a; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= e($pName) ?>
                            </h4>
                            <span style="font-size: 0.68rem; color: #64748b; display: block; margin: 1px 0 3px;">
                                <?= e($pCategory) ?>
                            </span>
                            <div style="display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                <span style="font-size: 0.82rem; font-weight: 800; color: #065f46;">
                                    Rp <?= number_format($pPrice, 0, ',', '.') ?>
                                </span>
                                <span style="font-size: 0.62rem; padding: 1px 6px; border-radius: 4px; font-weight: 700; background: #d1fae5; color: #065f46;">
                                    Stok: <?= $pStock ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.35rem; flex-shrink: 0;">
                        <a href="<?= base_url('owner/products/' . $pId . '/edit') ?>" style="display: block; padding: 0.32rem 0.75rem; background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; border-radius: 6px; font-size: 0.68rem; font-weight: 700; text-decoration: none; text-align: center;">
                            Edit
                        </a>
                        <button type="button" onclick="openDeleteModal('<?= $pId ?>', '<?= addslashes(e($pName)) ?>')" style="padding: 0.32rem 0.75rem; background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; border-radius: 6px; font-size: 0.68rem; font-weight: 700; cursor: pointer;">
                            Hapus
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</main>

<!-- Bottom Navbar Khusus Panel Owner -->
<nav class="owner-bottom-bar">
    <a href="<?= base_url('owner/dashboard') ?>" class="owner-nav-btn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span>Beranda</span>
    </a>
    <a href="<?= base_url('owner/orders') ?>" class="owner-nav-btn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        <span>Booking</span>
    </a>
    <div class="fab-container">
        <a href="<?= base_url('owner/products/create') ?>" class="fab-button" title="Tambah Produk">+</a>
        <span class="fab-label">Tambah</span>
    </div>
    <a href="<?= base_url('owner/products') ?>" class="owner-nav-btn active">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
        <span>Katalog</span>
    </a>
    <a href="<?= base_url('profile') ?>" class="owner-nav-btn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>Profile</span>
    </a>
</nav>

<!-- Modal Hapus -->
<div id="deleteModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; padding: 1rem; backdrop-filter: blur(2px);">
    <div style="background: #ffffff; border-radius: 16px; max-width: 320px; width: 100%; padding: 1.25rem; text-align: center;">
        <h3 style="margin: 0 0 0.35rem; font-size: 1rem; font-weight: 800; color: #0f172a;">Hapus Produk?</h3>
        <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 1rem;">Hapus <strong id="deleteProductName"></strong> dari katalog?</p>
        <form id="deleteForm" method="POST" action="" style="display: flex; gap: 0.5rem; margin: 0;">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="DELETE">
            <button type="button" onclick="closeDeleteModal()" style="flex: 1; padding: 0.6rem; border-radius: 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-size: 0.75rem; font-weight: 700; cursor: pointer;">Batal</button>
            <button type="submit" style="flex: 1; padding: 0.6rem; border-radius: 8px; border: none; background: #dc2626; color: #ffffff; font-size: 0.75rem; font-weight: 700; cursor: pointer;">Ya, Hapus</button>
        </form>
    </div>
</div>

<script>
function openDeleteModal(id, name) {
    document.getElementById('deleteProductName').textContent = `"${name}"`;
    document.getElementById('deleteForm').action = `<?= base_url('owner/products/') ?>` + id;
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
</script>

</body>
</html>