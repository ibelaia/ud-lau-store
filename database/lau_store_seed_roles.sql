-- ============================================================
-- SEED — ROLES (data awal wajib, terpisah dari migration v1.3)
-- Migration v1.3 hanya berisi struktur (DDL), TIDAK diubah.
-- File ini murni data (DML), aman dijalankan setelah migration.
-- ============================================================

USE lau_store;

INSERT INTO roles (name) VALUES
('customer'),
('owner'),
('programmer');

-- Verifikasi cepat setelah dijalankan:
-- SELECT * FROM roles;
-- Harus muncul 3 baris: customer, owner, programmer.
