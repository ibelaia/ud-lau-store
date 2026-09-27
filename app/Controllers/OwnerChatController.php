<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\MessageModel;

class OwnerChatController extends Controller
{
    private MessageModel $messageModel;

    public function __construct()
    {
        parent::__construct();
        $this->messageModel = new MessageModel();
    }

    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('login');
            return;
        }

        $ownerId = (int) Auth::id();

        // Tampilkan daftar inbox TANPA menghapus status unread
        $conversations = $this->messageModel->getConversationsForOwner($ownerId);

        $this->view('owner.chat_inbox', [
            'title'         => 'Inbox Pesan Pelanggan — Owner',
            'conversations' => $conversations
        ], 'layouts.admin');
    }

    public function detail(string $customerId): void
    {
        if (!Auth::check()) {
            $this->redirect('login');
            return;
        }

        $ownerId = (int) Auth::id();
        $custId  = (int) $customerId;

        $cust = $this->messageModel->getCustomerById($custId);
        if (!$cust) {
            $this->redirect('owner/chat');
            return;
        }

        // Pesan baru ditandai terbaca SAAT owner benar-benar membuka ruang chat customer ini
        $this->messageModel->markCustomerAsReadByOwner($custId, $ownerId);
        $messages = $this->messageModel->getConversation($custId, $ownerId);

        $this->view('owner.chat_room', [
            'title'      => 'Chat dengan ' . $cust['name'],
            'customer'   => $cust,
            'customerId' => $custId,
            'messages'   => $messages
        ], 'layouts.admin');
    }

    public function reply(): void
    {
        if (!Auth::check()) {
            $this->redirect('login');
            return;
        }

        $ownerId    = (int) Auth::id();
        $receiverId = (int) $this->input('customer_id');
        $message    = trim((string) $this->input('message'));

        if (!empty($message) && $receiverId > 0) {
            $this->messageModel->sendMessage($ownerId, $receiverId, null, $message);
        }

        $this->redirect('owner/chat/' . $receiverId);
    }
}