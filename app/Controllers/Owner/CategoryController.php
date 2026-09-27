<?php

namespace App\Controllers\Owner;

use App\Core\Auth;
use App\Core\Controller;

class CategoryController extends Controller
{
    public function index(): void
    {
        // Data kategori ATK representatif UD. LAU Store
        $categories = [
            ['id' => 1, 'name' => 'Alat Tulis', 'slug' => 'alat-tulis', 'icon' => '✏️', 'products_count' => 84],
            ['id' => 2, 'name' => 'Kertas & Buku', 'slug' => 'kertas-buku', 'icon' => '📚', 'products_count' => 62],
            ['id' => 3, 'name' => 'Perlengkapan Kantor', 'slug' => 'perlengkapan-kantor', 'icon' => '📁', 'products_count' => 45],
            ['id' => 4, 'name' => 'Kebutuhan Sekolah', 'slug' => 'kebutuhan-sekolah', 'icon' => '🎒', 'products_count' => 29],
        ];

        $this->view('owner.categories.index', [
            'title'      => 'Kategori Produk — ' . config('app.name'),
            'categories' => $categories,
        ], 'layouts.admin');
    }

    public function store(): void
    {
        $name = trim((string) $this->input('name'));
        // Simpan kategori baru...
        $this->redirect('owner/categories');
    }

    public function update(string $id): void
    {
        $this->redirect('owner/categories');
    }

    public function destroy(string $id): void
    {
        $this->redirect('owner/categories');
    }
}