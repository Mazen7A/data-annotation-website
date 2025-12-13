<?php
namespace App\Controllers;

use App\Auth\Auth;
use Exception;

class AuthController
{
    /**
     * Handle user login.
     */
    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'يرجى إدخال البريد الإلكتروني وكلمة المرور';
            header('Location: ' . route('login'));
            exit;
        }

        $auth = Auth::instance();
        $user = $auth->login($email, $password);

        if ($user) {
            $_SESSION['success'] = 'تم تسجيل الدخول بنجاح';
            
            // Redirect based on role
            if ($user->role === 'manager') {
                header('Location: ' . route('manager.dashboard'));
            } else {
                header('Location: ' . route('dashboard'));
            }
            exit;
        }

        $_SESSION['error'] = 'البريد الإلكتروني أو كلمة المرور غير صحيحة';
        header('Location: ' . route('login'));
        exit;
    }

    /**
     * Handle user registration.
     */
    public function register()
    {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'جميع الحقول مطلوبة';
            header('Location: ' . route('register'));
            exit;
        }

        if ($password !== $confirmPassword) {
            $_SESSION['error'] = 'كلمات المرور غير متطابقة';
            header('Location: ' . route('register'));
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
            header('Location: ' . route('register'));
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'البريد الإلكتروني غير صحيح';
            header('Location: ' . route('register'));
            exit;
        }

        try {
            $auth = Auth::instance();
            $user = $auth->register($name, $email, $password);

            $_SESSION['success'] = 'تم التسجيل بنجاح! مرحباً بك';
            header('Location: ' . route('dashboard'));
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . route('register'));
            exit;
        }
    }

    /**
     * Handle logout.
     */
    public function logout()
    {
        $auth = Auth::instance();
        $auth->logout();
        
        session_destroy();
        header('Location: ' . route('home'));
        exit;
    }
}
