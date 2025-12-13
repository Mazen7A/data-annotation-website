<?php
namespace App\Controllers;

use App\Models\ContactMessage;

class ManagerContactController
{
    /**
     * List all contact messages.
     */
    public function index()
    {
        $status = $_GET['status'] ?? null;
        $type = $_GET['type'] ?? null;

        if ($status) {
            $messages = ContactMessage::getByStatus($status);
        } elseif ($type) {
            $messages = ContactMessage::getByType($type);
        } else {
            $messages = ContactMessage::getAll();
        }

        view('manager/messages/index', ['messages' => $messages]);
    }

    /**
     * Show message details.
     */
    public function show()
    {
        $messageId = $_GET['id'] ?? 0;
        $message = ContactMessage::getById($messageId);

        if (!$message) {
            $_SESSION['error'] = 'الرسالة غير موجودة';
            header('Location: ' . route('manager.messages'));
            exit;
        }

        view('manager/messages/show', ['message' => $message]);
    }

    /**
     * Update message status.
     */
    public function updateStatus()
    {
        $messageId = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? '';

        if (!in_array($status, ['pending', 'in_progress', 'resolved'])) {
            $_SESSION['error'] = 'الحالة غير صحيحة';
            header('Location: ' . route('manager.messages'));
            exit;
        }

        $result = ContactMessage::updateStatus($messageId, $status);

        if ($result) {
            $_SESSION['success'] = 'تم تحديث حالة الرسالة';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء التحديث';
        }

        header('Location: ' . route('manager.messages.show', ['id' => $messageId]));
        exit;
    }

    /**
     * Add reply to message.
     */
    public function reply()
    {
        $messageId = $_POST['id'] ?? 0;
        $reply = $_POST['reply'] ?? '';

        if (empty(trim($reply))) {
            $_SESSION['error'] = 'الرد مطلوب';
            header('Location: ' . route('manager.messages.show', ['id' => $messageId]));
            exit;
        }

        $result = ContactMessage::addReply($messageId, $reply);

        if ($result) {
            $_SESSION['success'] = 'تم إضافة الرد بنجاح';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء إضافة الرد';
        }

        header('Location: ' . route('manager.messages.show', ['id' => $messageId]));
        exit;
    }

    /**
     * Delete message.
     */
    public function delete()
    {
        $messageId = $_POST['id'] ?? 0;
        $result = ContactMessage::delete($messageId);

        if ($result) {
            $_SESSION['success'] = 'تم حذف الرسالة بنجاح';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء الحذف';
        }

        header('Location: ' . route('manager.messages'));
        exit;
    }
}
