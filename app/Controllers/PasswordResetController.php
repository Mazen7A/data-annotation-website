<?php

namespace App\Controllers;

use App\Models\User;
use App\Database\DB;

class PasswordResetController
{
    /**
     * Show forgot password form.
     */
    public function showForgotForm()
    {
        view('auth/forgot-password');
    }

    /**
     * Send reset code (display on screen - simulating email).
     */
    public function sendResetCode()
    {
        // allow fallback to previously stored email so "إعادة إرسال" يعمل حتى لو لم يظهر الحقل
        $email = strtolower(trim($_POST['email'] ?? ''));
        if (empty($email) && !empty($_SESSION['reset_email'])) {
            $email = strtolower(trim($_SESSION['reset_email']));
        }

        if (empty($email)) {
            $_SESSION['error'] = 'البريد الإلكتروني مطلوب';
            header('Location: ' . route('password.forgot'));
            exit;
        }

        // Check if user exists
        $user = User::findByEmail($email);
        if (!$user) {
            $_SESSION['error'] = 'البريد الإلكتروني غير مسجل في النظام';
            header('Location: ' . route('password.forgot'));
            exit;
        }

        // Generate 6-digit code
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store code in database with 15-minute expiration
        $db = DB::getConnection();
        
        // Delete old codes for this email
        $stmt = $db->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->execute([$email]);
        
        // Insert new code
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        $stmt = $db->prepare("
            INSERT INTO password_resets (email, code, expires_at) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$email, $code, $expiresAt]);

        // Store code in session to display (simulating email)
        $_SESSION['reset_code'] = $code;
        $_SESSION['reset_email'] = $email;
        $_SESSION['success'] = 'تم إنشاء رمز التحقق بنجاح';

        header('Location: ' . route('password.reset'));
        exit;
    }

    /**
     * Show reset password form.
     */
    public function showResetForm()
    {
        // Check if we have a code in session (from sendResetCode)
        $displayCode = $_SESSION['reset_code'] ?? null;
        $email = $_SESSION['reset_email'] ?? '';

        view('auth/reset-password', [
            'display_code' => $displayCode,
            'email' => $email
        ]);
    }

    /**
     * Reset password with code validation.
     */
    public function resetPassword()
    {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $code = trim($_POST['code'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validation
        if (empty($email) || empty($code) || empty($password)) {
            $_SESSION['error'] = 'جميع الحقول مطلوبة';
            header('Location: ' . route('password.reset'));
            exit;
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['error'] = 'كلمات المرور غير متطابقة';
            header('Location: ' . route('password.reset'));
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
            header('Location: ' . route('password.reset'));
            exit;
        }

        // Verify code
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT * FROM password_resets 
            WHERE email = ? AND code = ? AND expires_at > NOW()
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$email, $code]);
        $reset = $stmt->fetch();

        // Fallback: إذا لم توجد سجلات (مثلاً لم تُنشأ الجدول أو تعذر إدراج الرمز) حاول مطابقة الرمز المخزن في السيشن
        if (!$reset) {
            $sessionCode = $_SESSION['reset_code'] ?? null;
            $sessionEmail = $_SESSION['reset_email'] ?? null;
            $sessionValid = $sessionCode && $sessionEmail && $sessionEmail === $email && $sessionCode === $code;
            if (!$sessionValid) {
                $_SESSION['error'] = 'رمز التحقق غير صحيح أو منتهي الصلاحية';
                header('Location: ' . route('password.reset'));
                exit;
            }
        }

        // Check if user exists
        $user = User::findByEmail($email);
        if (!$user) {
            $_SESSION['error'] = 'البريد الإلكتروني غير مسجل في النظام';
            header('Location: ' . route('password.reset'));
            exit;
        }

        // Update password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $email]);

        // Delete used code
        $stmt = $db->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->execute([$email]);

        // Clear session
        unset($_SESSION['reset_email']);

        $_SESSION['success'] = 'تم تغيير كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول';
        header('Location: ' . route('login'));
        exit;
    }
}
