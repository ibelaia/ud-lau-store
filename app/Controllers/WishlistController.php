<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\ProductModel;

class WishlistController extends Controller
{
    private ProductModel $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new ProductModel();
    }

    private function isCustomerLoggedIn(): bool
    {
        if (class_exists('\App\Core\Auth') && Auth::check()) {
            return true;
        }
        return !empty($_SESSION['user']) || !empty($_SESSION['user_id']);
    }

    public function index(): void
    {
        if (!$this->isCustomerLoggedIn()) {
            $this->redirect('login');
            return;
        }

        $wishlistIds = array_keys($_SESSION['wishlist'] ?? []);
        $favoriteProducts = [];

        if (!empty($wishlistIds)) {
            foreach ($wishlistIds as $id) {
                $product = $this->productModel->find((int)$id);
                if ($product) {
                    $favoriteProducts[] = $product;
                }
            }
        }

        $this->view('customer.wishlist', [
            'title'    => 'Produk Favorit Saya — ' . config('app.name'),
            'products' => $favoriteProducts,
        ]);
    }

    public function toggle(): void
    {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        // 1. Cek Login
        if (!$this->isCustomerLoggedIn()) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success'       => false,
                    'require_login' => true,
                    'redirect_url'  => base_url('login')
                ]);
                exit;
            }

            $this->redirect('login');
            return;
        }

        // 2. Toggle Status Favorit
        $productId = (int) ($this->input('product_id') ?? $_REQUEST['product_id'] ?? 0);
        $isFav = false;

        if ($productId > 0) {
            if (!isset($_SESSION['wishlist']) || !is_array($_SESSION['wishlist'])) {
                $_SESSION['wishlist'] = [];
            }

            if (isset($_SESSION['wishlist'][$productId])) {
                unset($_SESSION['wishlist'][$productId]);
                $isFav = false;
            } else {
                $_SESSION['wishlist'][$productId] = true;
                $isFav = true;
            }
        }

        $totalFav = count($_SESSION['wishlist'] ?? []);

        // 3. Respon Sesuai Tipe Pemanggil
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success'        => true,
                'is_favorite'    => $isFav,
                'favorite_count' => $totalFav
            ]);
            exit;
        }

        // Jika dipanggil dari tombol hapus form/link biasa di halaman favorit, redirect kembali
        $referer = $_SERVER['HTTP_REFERER'] ?? base_url('wishlist');
        header("Location: {$referer}");
        exit;
    }
}