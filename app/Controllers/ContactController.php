<?php
namespace App\Controllers;

use App\Models\ContactMessage;

class ContactController
{
    /**
     * Show contact form.
     */
    public function index()
    {
        view('contact');
    }

    /**
     * Submit contact message.
     */
    public function submit()
    {
        $userId = auth()->id();
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $type = $_POST['type'] ?? 'other';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';

        // Validation
        if (empty($subject) || empty($message)) {
            $_SESSION['error'] = 'الموضوع والرسالة مطلوبان';
            header('Location: ' . route('contact'));
            exit;
        }

        // If not logged in, require name and email
        if (!$userId) {
            if (empty($name) || empty($email)) {
                $_SESSION['error'] = 'الاسم والبريد الإلكتروني مطلوبان';
                header('Location: ' . route('contact'));
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'البريد الإلكتروني غير صحيح';
                header('Location: ' . route('contact'));
                exit;
            }
        }

        $data = [
            'user_id' => $userId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'type' => $type,
            'subject' => $subject,
            'message' => $message
        ];

        $messageId = ContactMessage::create($data);

        if ($messageId) {
            $_SESSION['success'] = 'تم إرسال رسالتك بنجاح، سنتواصل معك قريباً';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء إرسال الرسالة';
        }

        header('Location: ' . route('contact'));
        exit;
    }
}
