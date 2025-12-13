<?php
namespace App\Controllers;

use App\Auth\Auth;
use Exception;

class ProfileController
{
    /**
     * Show profile.
     */
    public function show()
    {
        $user = auth()->user();
        view('user/profile/show', ['user' => $user]);
    }

    /**
     * Show edit profile form.
     */
    public function edit()
    {
        $user = auth()->user();
        view('user/profile/edit', ['user' => $user]);
    }

    /**
     * Update profile.
     */
    public function update()
    {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';

        if (empty($name) || empty($email)) {
            $_SESSION['error'] = 'الاسم والبريد الإلكتروني مطلوبان';
            header('Location: ' . route('profile.edit'));
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'البريد الإلكتروني غير صحيح';
            header('Location: ' . route('profile.edit'));
            exit;
        }

        $auth = Auth::instance();
        $result = $auth->updateProfile([
            'name' => $name,
            'email' => $email
        ]);

        if ($result) {
            $_SESSION['success'] = 'تم تحديث الملف الشخصي بنجاح';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء التحديث';
        }

        header('Location: ' . route('profile'));
        exit;
    }

    /**
     * Update password.
     */
    public function updatePassword()
    {
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['error'] = 'جميع الحقول مطلوبة';
            header('Location: ' . route('profile.edit'));
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'كلمات المرور الجديدة غير متطابقة';
            header('Location: ' . route('profile.edit'));
            exit;
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
            header('Location: ' . route('profile.edit'));
            exit;
        }

        try {
            $auth = Auth::instance();
            $result = $auth->updatePassword($oldPassword, $newPassword);

            if ($result) {
                $_SESSION['success'] = 'تم تحديث كلمة المرور بنجاح';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: ' . route('profile.edit'));
        exit;
    }
}
