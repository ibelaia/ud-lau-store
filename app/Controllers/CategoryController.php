<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class CategoryController extends Controller
{
    private CategoryModel $categoryModel;
    private ProductModel $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
    }

    public function show(string $slug): void
    {
        $category = $this->categoryModel->findBySlug($slug);

        if (!$category) {
            $this->view('errors.404', ['title' => 'Kategori Tidak Ditemukan']);
            return;
        }

        $products = $this->productModel->search(['category_id' => $category['id']]);

        $this->view('products.index', [
            'title'            => 'Kategori: ' . $category['name'] . ' - ' . config('app.name'),
            'products'         => $products,
            'categories'       => $this->categoryModel->all(['column' => 'id', 'direction' => 'ASC']),
            'selectedCategory' => $category['id'],
            'currentCategory'  => $category,
            'keyword'          => '',
            'sort'             => 'terbaru'
        ]);
    }
}