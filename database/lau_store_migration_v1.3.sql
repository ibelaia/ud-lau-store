-- ============================================================
-- SQL MIGRATION — UD. LAU STORE
-- Sesuai ERD v1.2 (struktur/relasi FINAL, tidak berubah)
-- Versi file: v1.3 — Implementation Notes (bukan revisi ERD)
-- Charset: utf8mb4 | Engine: InnoDB | PK: BIGINT UNSIGNED AUTO_INCREMENT
--
-- Catatan implementasi v1.2 -> v1.3 (tidak mengubah entity/relasi):
--   1. activity_logs.actor_type: ENUM -> VARCHAR(20) (fleksibel untuk
--      nilai baru seperti 'cron', 'api' tanpa ALTER TABLE)
--   2. Soft delete (deleted_at): sudah sejak awal hanya di tabel Master
--      (users, categories, products, payment_methods, banners) —
--      dikonfirmasi, tidak ada perubahan
--   3. FK orders -> users & orders -> payment_methods: sudah ON DELETE
--      RESTRICT sejak v1.2 — dikonfirmasi, tidak ada perubahan
--
-- STATUS: BASELINE FINAL. Perubahan struktural berikutnya hanya jika
-- ditemukan bug nyata saat implementasi atau ada kebutuhan bisnis baru.
-- ============================================================

CREATE DATABASE IF NOT EXISTS lau_store
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE lau_store;

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. ROLES
-- ============================================================
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. USERS
-- ============================================================
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    photo VARCHAR(255) NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_users_role (role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. CATEGORIES
-- ============================================================
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. PRODUCTS
-- ============================================================
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NOT NULL,
    product_code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    description TEXT NULL,
    price DECIMAL(15,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    reserved_stock INT NOT NULL DEFAULT 0,
    minimum_stock INT NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    is_featured BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_products_reserved_stock CHECK (reserved_stock >= 0 AND reserved_stock <= stock),
    INDEX idx_products_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Catatan: available_stock TIDAK disimpan sebagai kolom.
-- Dihitung di level aplikasi/query: SELECT stock - reserved_stock AS available_stock ...

-- ============================================================
-- 5. PRODUCT_IMAGES
-- ============================================================
CREATE TABLE product_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES products(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_product_images_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. PAYMENT_METHODS
-- ============================================================
CREATE TABLE payment_methods (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    account_number VARCHAR(50) NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. SYSTEM_SETTINGS
-- ============================================================
CREATE TABLE system_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. BANNERS
-- ============================================================
CREATE TABLE banners (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_by BIGINT UNSIGNED NOT NULL,
    title VARCHAR(150) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    CONSTRAINT fk_banners_user FOREIGN KEY (created_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_banners_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. CONTACTS
-- ============================================================
CREATE TABLE contacts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    label VARCHAR(100) NOT NULL,
    value VARCHAR(255) NOT NULL,
    icon VARCHAR(100) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. CARTS
-- ============================================================
CREATE TABLE carts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    session_id VARCHAR(100) NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_carts_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_carts_user (user_id),
    INDEX idx_carts_session (session_id),
    INDEX idx_carts_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Catatan: user_id NULL = guest cart (diidentifikasi via session_id).
-- Saat guest login, aplikasi meng-attach cart ke user_id (UPDATE carts SET user_id = ? WHERE session_id = ?).

-- ============================================================
-- 11. CART_ITEMS
-- ============================================================
CREATE TABLE cart_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cart_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    price DECIMAL(15,2) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_cart_items_cart FOREIGN KEY (cart_id) REFERENCES carts(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_cart_items_product FOREIGN KEY (product_id) REFERENCES products(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_cart_items_cart (cart_id),
    INDEX idx_cart_items_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. WISHLISTS
-- ============================================================
CREATE TABLE wishlists (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_wishlists_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_wishlists_product FOREIGN KEY (product_id) REFERENCES products(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY uq_wishlist_user_product (user_id, product_id),
    INDEX idx_wishlists_user (user_id),
    INDEX idx_wishlists_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 13. ORDERS
-- ============================================================
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    payment_method_id BIGINT UNSIGNED NOT NULL,
    order_code VARCHAR(30) NOT NULL UNIQUE,
    status VARCHAR(30) NOT NULL DEFAULT 'pending_payment',
    total DECIMAL(15,2) NOT NULL,
    pickup_date DATE NULL,
    pickup_deadline DATETIME NULL,
    picked_up_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_orders_payment_method FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_orders_user (user_id),
    INDEX idx_orders_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 14. ORDER_ITEMS
-- ============================================================
CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    product_code VARCHAR(20) NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    qty INT NOT NULL,
    price DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_order_items_order (order_id),
    INDEX idx_order_items_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 15. PAYMENTS
-- ============================================================
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    verified_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_payments_order FOREIGN KEY (order_id) REFERENCES orders(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_payments_order (order_id),
    INDEX idx_payments_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 16. PAYMENT_PROOFS
-- ============================================================
CREATE TABLE payment_proofs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_id BIGINT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_payment_proofs_payment FOREIGN KEY (payment_id) REFERENCES payments(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_payment_proofs_payment (payment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 17. ORDER_STATUS_LOGS
-- ============================================================
CREATE TABLE order_status_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    old_status VARCHAR(30) NULL,
    new_status VARCHAR(30) NOT NULL,
    changed_by BIGINT UNSIGNED NULL,
    note TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_status_logs_order FOREIGN KEY (order_id) REFERENCES orders(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_order_status_logs_user FOREIGN KEY (changed_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_order_status_logs_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Catatan: old_status NULL untuk baris pertama (saat order dibuat).
-- changed_by NULL jika perubahan dipicu otomatis oleh sistem (misal auto-cancel).

-- ============================================================
-- 18. STOCK_LOGS
-- ============================================================
CREATE TABLE stock_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    order_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NULL,
    type VARCHAR(30) NOT NULL,
    previous_stock INT NOT NULL,
    qty_change INT NOT NULL,
    current_stock INT NOT NULL,
    note TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_stock_logs_product FOREIGN KEY (product_id) REFERENCES products(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_stock_logs_order FOREIGN KEY (order_id) REFERENCES orders(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_stock_logs_user FOREIGN KEY (created_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_stock_logs_product (product_id),
    INDEX idx_stock_logs_order (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Catatan: created_by NULL jika pergerakan stok dipicu otomatis oleh sistem
-- (misal release reserved_stock saat auto-cancel), bukan input manual Owner.

-- ============================================================
-- 19. ACTIVITY_LOGS
-- ============================================================
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    actor_type VARCHAR(20) NOT NULL DEFAULT 'user',
    action VARCHAR(100) NOT NULL,
    description TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_activity_logs_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_activity_logs_user (user_id),
    INDEX idx_activity_logs_actor_type (actor_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- Catatan: actor_type = 'system' untuk proses otomatis (cron auto-cancel, scheduled backup, dll),
-- user_id NULL pada kasus tersebut.

-- ============================================================
-- 20. NOTIFICATIONS
-- ============================================================
CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NULL,
    link VARCHAR(255) NULL,
    is_read BOOLEAN NOT NULL DEFAULT FALSE,
    read_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_notifications_user (user_id),
    INDEX idx_notifications_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 21. IMPORT_HISTORIES
-- ============================================================
CREATE TABLE import_histories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    filename VARCHAR(255) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'processing',
    total_rows INT NOT NULL DEFAULT 0,
    success_rows INT NOT NULL DEFAULT 0,
    failed_rows INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_import_histories_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_import_histories_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 22. IMPORT_DETAILS
-- ============================================================
CREATE TABLE import_details (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    import_history_id BIGINT UNSIGNED NOT NULL,
    row_number INT NOT NULL,
    status VARCHAR(20) NOT NULL,
    message TEXT NULL,
    CONSTRAINT fk_import_details_history FOREIGN KEY (import_history_id) REFERENCES import_histories(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_import_details_history (import_history_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 23. BACKUPS
-- ============================================================
CREATE TABLE backups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_backups_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_backups_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 24. PASSWORD_RESET_TOKENS
-- ============================================================
CREATE TABLE password_reset_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    token VARCHAR(255) NOT NULL,
    expired_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_password_reset_tokens_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_password_reset_tokens_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- END OF MIGRATION — 24 TABLES — v1.2 FINAL (DESIGN FREEZE)
-- Langkah berikutnya: import ke MySQL, uji seluruh foreign key,
-- lalu mulai coding backend PHP Native (MVC).
-- ============================================================
