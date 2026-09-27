<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\MessageModel;
use App\Models\ProductModel;

class ChatController extends Controller
{
    private MessageModel $messageModel;
    private ProductModel $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->messageModel = new MessageModel();
        $this->productModel = new ProductModel();
    }

    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('login');
            return;
        }

        $userId  = (int) Auth::id();
        $ownerId = $this->messageModel->getLatestOwnerIdForUser($userId);

        // Tandai terbaca saat customer membuka obrolan
        $this->messageModel->markAsRead($ownerId, $userId);

        $messages = $this->messageModel->getConversation($userId, $ownerId);

        $productId = (int) $this->input('product_id', 0);
        $contextProduct = null;
        if ($productId > 0) {
            $contextProduct = $this->productModel->find($productId);
        }

        $this->view('customer.chat', [
            'title'          => 'Tanya UD. LAU Store — Messenger',
            'messages'       => $messages,
            'ownerId'        => $ownerId,
            'contextProduct' => $contextProduct,
            'productId'      => $productId
        ]);
    }

    public function send(): void
    {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'));

        if (!Auth::check()) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'require_login' => true, 'redirect_url' => base_url('login')]);
                exit;
            }
            $this->redirect('login');
            return;
        }

        $senderId = (int) Auth::id();

        // Cari ID target penerima (Owner)
        $receiverId = (int) $this->input('receiver_id');
        if ($receiverId <= 0) {
            $receiverId = $this->messageModel->getLatestOwnerIdForUser($senderId);
        }

        $productId = (int) $this->input('product_id', 0);
        $message   = trim((string) $this->input('message'));

        $sent = false;
        if (!empty($message) && $receiverId > 0) {
            $sent = $this->messageModel->sendMessage($senderId, $receiverId, $productId > 0 ? $productId : null, $message);
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$sent]);
            exit;
        }

        $this->redirect('chat' . ($productId > 0 ? '?product_id=' . $productId : ''));
    }

    public function unreadCount(): void
    {
        header('Content-Type: application/json');
        if (!Auth::check()) {
            echo json_encode(['count' => 0]);
            exit;
        }

        $userId = (int) Auth::id();
        $count = $this->messageModel->countUnread($userId);
        echo json_encode(['count' => $count]);
        exit;
    }
}