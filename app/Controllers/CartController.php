<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProductModel;

class CartController extends Controller
{
    private ProductModel $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new ProductModel();
    }

    public function index(): void
    {
        $cartSession = $_SESSION['cart'] ?? [];
        $items = [];
        $totalPrice = 0;

        if (!empty($cartSession)) {
            foreach ($cartSession as $productId => $qty) {
                $product = $this->productModel->find((int)$productId);
                if ($product) {
                    $price = (float)$product['price'];
                    $subtotal = $price * (int)$qty;
                    $totalPrice += $subtotal;

                    $items[] = [
                        'id'         => $product['id'],
                        'name'       => $product['name'],
                        'price'      => $price,
                        'quantity'   => (int)$qty,
                        'subtotal'   => $subtotal,
                        'image'      => $product['image'] ?? '📦'
                    ];
                } else {
                    unset($_SESSION['cart'][$productId]);
                }
            }
        }

        $this->view('cart/index', [
            'title'      => 'Keranjang Booking — ' . config('app.name'),
            'items'      => $items,
            'totalPrice' => $totalPrice,
            'totalCount' => count($items)
        ]);
    }

    public function add(): void
    {
        $productId = (int)$this->input('product_id');
        $qty       = (int)$this->input('quantity', 1);

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $qty;
            if ($_SESSION['cart'][$productId] <= 0) {
                unset($_SESSION['cart'][$productId]);
            }
        } else {
            if ($qty > 0) {
                $_SESSION['cart'][$productId] = $qty;
            }
        }

        $this->redirect('cart');
    }

    public function remove(string $id): void
    {
        $productId = (int)$id;

        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }

        $this->redirect('cart');
    }
}