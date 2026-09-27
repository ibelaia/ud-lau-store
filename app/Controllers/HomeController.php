<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class HomeController extends Controller
{
    private CategoryModel $categoryModel;
    private ProductModel $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
    }

    public function index(): void
    {
      $categories = $this->categoryModel->all(['column' => 'id', 'direction' => 'ASC']);
        $newProducts  = $this->productModel->getLatest(6);
        $topProducts  = $this->productModel->getTopSelling(6);

        $this->view('home.index', [
            'title'        => config('app.name'),
            'categories'   => $categories,
            'newProducts'  => $newProducts,
            'topProducts'  => $topProducts,
        ]);
    }
}