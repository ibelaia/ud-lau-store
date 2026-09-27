# UD. LAU Store — Skeleton PHP Native MVC

Skeleton ini mencakup Tahap 1–5 dari roadmap implementasi:
struktur folder, koneksi database, router, base Controller, base Model,
serta middleware autentikasi dasar (Auth/Owner/Programmer).

## Struktur Folder

```
lau_store/
├── app/
│   ├── Controllers/       # Controller (termasuk subfolder Owner/, Programmer/)
│   ├── Models/             # Model (akses data, wajib pakai PDO + prepared statement)
│   ├── Views/               # Tampilan (layouts, partials/header-footer-navbar, home, errors)
│   ├── Core/                 # Router, Controller, Model, Database, Auth, Env, Request,
│   │                          # Validator, Csrf
│   ├── Helpers/               # functions.php: base_url, asset, upload_url, config, csrf_field, e
│   └── Middleware/           # AuthMiddleware, OwnerMiddleware, ProgrammerMiddleware
├── config/                    # app.php, database.php
├── public/                     # Document root (index.php, .htaccess, assets)
├── routes/                      # web.php — seluruh pemetaan URL
├── storage/                      # logs, cache
├── uploads/                       # foto produk, banner, bukti bayar, backup
└── vendor/                         # (kosong — proyek ini sengaja tanpa composer)
```

## Prinsip: Environment Ready, Bukan Localhost First

Seluruh URL (aset, upload, redirect) dibentuk otomatis dari request yang
masuk lewat `base_url()` / `asset()` / `upload_url()` di
`app/Helpers/functions.php` — **tidak ada URL/path yang di-hardcode**.
Kode yang sama berjalan tanpa perubahan di 3 environment:

| Environment | Contoh URL | Kegunaan |
|---|---|---|
| **Local** | `http://localhost` / `http://lau-store.test` | Coding & debugging |
| **Ngrok** | `https://xxxxx.ngrok-free.app` | Demo ke client, tes dari HP, tes dari komputer lain |
| **Hosting** | `https://udlaustore.com` | Production akhir |

**Prasyarat wajib:** document root web server (virtual host Local, tunnel
Ngrok ke virtual host yang sama, dan konfigurasi domain Hosting) **harus
selalu mengarah ke folder `/public`** — bukan ke root project. Dengan
begitu aplikasi tidak pernah butuh prefix folder di URL manapun.

**Penanganan khusus Ngrok:** Ngrok men-terminate HTTPS di proxy-nya lalu
meneruskan ke server lokal sebagai HTTP biasa — `$_SERVER['HTTPS']` tidak
ke-set meski URL publik `https://`. `base_url()` sudah menangani ini
dengan membaca header `X-Forwarded-Proto`, jadi asset/redirect tetap
benar sebagai `https://` saat diakses lewat Ngrok.

**Upload gambar:** simpan path **relatif** di database (contoh:
`products/pensil.jpg`), bukan URL lengkap. Tampilkan dengan
`upload_url($product['image_path'])` di View — otomatis menghasilkan
URL penuh sesuai environment saat itu.

### Setup Symlink Storage (wajib sebelum upload berfungsi)

File upload disimpan fisik di `storage/uploads/` — **terpisah dari
source code** — lalu diakses browser lewat `public/storage/` (symlink).
Pengecualian: `storage/backups/` **TIDAK** di-symlink dan tidak pernah
bisa diakses lewat URL publik (berisi dump seluruh database).

**Local (Linux/Mac):**
```
cd lau_store
ln -s ../storage/uploads public/storage
```

**Local (Windows, jalankan sebagai Administrator):**
```
cd lau_store
mklink /D public\storage ..\storage\uploads
```

**Hosting (cPanel dsb.):** biasanya tersedia menu "Create Symlink", atau
via SSH jalankan perintah yang sama seperti Local (Linux/Mac) di atas.

Struktur folder upload:
```
storage/
├── uploads/              → di-symlink ke public/storage (PUBLIK)
│   ├── products/
│   ├── banners/
│   ├── payment-proofs/
│   └── avatars/
└── backups/              → TIDAK di-symlink (PRIVAT, hanya via route Programmer)
```

### Checklist "Definition of Done" per fitur

Setiap fitur yang selesai dikerjakan wajib dicek di 3 environment
sebelum dianggap selesai:

- [ ] **Local** — logika aplikasi berjalan benar
- [ ] **Ngrok** — tidak ada asset/link yang salah protokol (http di
      halaman https) atau 404 karena path hardcode
- [ ] **Hosting** *(saat deployment akhir)* — berjalan tanpa perlu
      mengubah satu baris kode pun

## Sprint 1 — Authentication (Status Saat Ini)

**Sudah dikerjakan:**
- [x] Struktur tabel `users` + `roles` divalidasi dari migration v1.3
- [x] Seed 3 roles (`database/lau_store_seed_roles.sql`)
- [x] `UserModel`, `RoleModel`
- [x] `AuthController` — login, register, logout
- [x] View `auth.login`, `auth.register` (mobile-first)
- [x] Session (`Auth` class), Middleware (`AuthMiddleware`, `OwnerMiddleware`, `ProgrammerMiddleware`)
- [x] Seed akun test Owner & Programmer (`database/seed_test_accounts.php`)

**Belum (menunggu Sprint berikutnya, BUKAN bug):**
- [ ] `owner/dashboard` dan `programmer/dashboard` — route sudah ada di
      `routes/web.php`, tapi Controller-nya belum dibuat (Sprint 2). Login
      sebagai Owner/Programmer akan **500 error setelah redirect** — ini
      **normal**, bukan bug Authentication.
- [ ] `ProfileController` (link "Profile" di navbar belum ada halamannya)

### Cara Testing Manual (checklist sebelum lanjut Sprint 2)

Urutan menjalankan:
1. Import `database/lau_store_migration_v1.3.sql`
2. Import `database/lau_store_seed_roles.sql`
3. `php database/seed_test_accounts.php` (dari root project, lewat CLI)
4. `cd public && php -S localhost:8000`

Lalu cek satu per satu:
- [ ] **Register**: `/register` → isi form → berhasil, otomatis login, redirect ke `/`
- [ ] **Register validasi**: submit form kosong → muncul pesan error per field
- [ ] **Register duplikat**: daftar dengan email yang sama 2x → ditolak ("sudah terdaftar")
- [ ] **Login customer**: `/login` pakai akun yang baru didaftarkan → redirect ke `/`
- [ ] **Login salah password**: → pesan "Email atau password salah"
- [ ] **Login Owner**: `owner@udlaustore.test` / `Owner12345` → redirect ke `/owner/dashboard`
      (expected: 500, karena Sprint 2 belum dikerjakan — bukan bug)
- [ ] **Login Programmer**: `programmer@udlaustore.test` / `Programmer12345` → redirect ke
      `/programmer/dashboard` (expected: 500, sama seperti di atas)
- [ ] **Logout**: tombol Logout di navbar → sesi berakhir, kembali ke halaman publik
- [ ] **Middleware**: coba akses `/checkout` tanpa login → auto-redirect ke `/login`
- [ ] **CSRF**: coba submit form login dari luar aplikasi (atau hapus `_csrf_token` manual
      di DevTools) → harus ditolak 403, bukan diproses

## Cara Menjalankan (Lokal)

1. Salin `.env.example` menjadi `.env`, sesuaikan kredensial database.
2. Import `lau_store_migration_v1.3.sql` ke MySQL.
3. Arahkan document root web server ke folder `public/`.
   - Contoh cepat pakai PHP built-in server (untuk development):
     ```
     cd lau_store/public
     php -S localhost:8000
     ```
4. Buka `http://localhost:8000` — jika muncul halaman "Selamat datang di UD. LAU Store",
   skeleton berjalan dengan benar (Database, Router, Controller, View sudah tersambung).

## Konvensi yang Dipakai (Coding Standard, lihat CS-01 s/d CS-06)

- **MVC sederhana**: Controller tidak pernah berisi query SQL — semua lewat Model.
- **PDO + prepared statement** wajib di seluruh Model (lihat `app/Core/Model.php`).
- **Konfigurasi sensitif** di `.env`, dimuat lewat `App\Core\Env` — tidak pernah di-hardcode.
- **Soft delete** hanya di tabel Master (`users, categories, products, payment_methods, banners`).
  Tabel transaksi (`orders, payments, order_items, stock_logs`, dst) tidak pernah soft delete.
- **`APP_DEBUG` terpisah dari `APP_ENV`** — production tidak pernah menampilkan
  detail error ke user kecuali `APP_DEBUG=true` diaktifkan eksplisit.
- **Timezone `Asia/Jakarta`** konsisten di PHP (`date_default_timezone_set`)
  maupun sesi MySQL (`SET time_zone`), supaya `created_at`, `order_status_logs`,
  `stock_logs` semuanya sinkron — lihat `app/Core/Database.php`.
- **Role** disimpan di session sebagai string: `'customer' | 'owner' | 'programmer'`,
  sesuai `roles.name` di database.
- **CSRF**: setiap `<form method="POST">` WAJIB menyertakan `<?= csrf_field() ?>` —
  diverifikasi otomatis oleh `Router::dispatch()` untuk method POST/PUT/DELETE.
- **Validasi input** pakai `App\Core\Validator` (lihat contoh di docblock class-nya),
  bukan pengecekan manual `if (empty(...))` bertebaran di Controller.

## Status Roadmap (Revisi Environment-Ready)

- [x] Requirement, ERD, Migration SQL (v1.3, final — design freeze)
- [x] Skeleton MVC (folder, Router, base Controller, base Model, Middleware)
- [x] Setup Environment (helper `base_url()`/`asset()`/`upload_url()`,
      environment-agnostic — Local/Ngrok/Hosting tanpa ubah kode)
- [ ] Import Database (`lau_store_migration_v1.3.sql` ke MySQL)
- [ ] Test Database Connection
- [ ] Jalankan Skeleton MVC (verifikasi Router+Controller+View+DB tersambung)
- [ ] Authentication (login, logout, session, redirect per role, remember login)
- [ ] Dashboard sederhana (Owner/Programmer/Customer)
- [ ] Master Data (Category → Product → Product Image → Payment Method → Banner → Contact)
- [ ] Customer Module (Home, Detail Produk, Cart, Wishlist, Checkout)
- [ ] Order Module (Order, Payment, Upload Bukti, Verifikasi, Pickup)
- [ ] Inventory (Stock Log, Reserved Stock, Release Stock, Adjustment)
- [ ] Programmer Module (Import Excel, Backup, Activity Log, System Setting)
- [ ] Testing (Blackbox, UAT, Fix)
- [ ] Deployment Production
