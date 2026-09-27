<?php

/**
 * seed_test_accounts.php — membuat akun TEST Owner & Programmer.
 *
 * BR-23: akun Owner & Programmer TIDAK dibuat lewat form register publik
 * (yang hanya membuat akun 'customer'). Ini satu-satunya jalur pembuatan
 * akun awal mereka — jalankan SEKALI lewat CLI, lalu GANTI PASSWORD-nya.
 *
 * Cara pakai:
 *   cd lau_store
 *   php database/seed_test_accounts.php
 *
 * Aman dijalankan berulang (idempotent) — dilewati kalau email sudah ada.
 */

require dirname(__DIR__) . '/app/Core/bootstrap.php';

use App\Core\Database;

$db = Database::connection();

$accounts = [
    [
        'role_name' => 'owner',
        'name'      => 'Owner UD. LAU Store (TEST)',
        'username'  => 'owner_test',
        'email'     => 'owner@udlaustore.test',
        'password'  => 'Owner12345', // GANTI setelah login pertama
    ],
    [
        'role_name' => 'programmer',
        'name'      => 'Programmer UD. LAU Store (TEST)',
        'username'  => 'programmer_test',
        'email'     => 'programmer@udlaustore.test',
        'password'  => 'Programmer12345', // GANTI setelah login pertama
    ],
];

echo "=== SEED AKUN TEST OWNER & PROGRAMMER ===" . PHP_EOL;

foreach ($accounts as $account) {
    // Ambil role_id dari nama role (BUKAN hardcode angka)
    $roleStmt = $db->prepare('SELECT id FROM roles WHERE name = :name LIMIT 1');
    $roleStmt->execute(['name' => $account['role_name']]);
    $role = $roleStmt->fetch();

    if (!$role) {
        echo "  [GAGAL] Role '{$account['role_name']}' tidak ditemukan. Jalankan lau_store_seed_roles.sql dulu." . PHP_EOL;
        continue;
    }

    $checkStmt = $db->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $checkStmt->execute(['email' => $account['email']]);

    if ($checkStmt->fetch()) {
        echo "  [SKIP] Akun '{$account['email']}' sudah ada." . PHP_EOL;
        continue;
    }

    $insertStmt = $db->prepare(
        'INSERT INTO users (role_id, name, username, email, password, is_active)
         VALUES (:role_id, :name, :username, :email, :password, 1)'
    );
    $insertStmt->execute([
        'role_id'  => $role['id'],
        'name'     => $account['name'],
        'username' => $account['username'],
        'email'    => $account['email'],
        'password' => password_hash($account['password'], PASSWORD_BCRYPT), // BR-50
    ]);

    echo "  [OK] Akun '{$account['email']}' ({$account['role_name']}) dibuat. Password awal: {$account['password']}" . PHP_EOL;
}

echo "=== SELESAI ===" . PHP_EOL;
echo "PENTING: ganti password kedua akun ini setelah login pertama kali." . PHP_EOL;
