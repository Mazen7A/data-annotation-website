<?php
namespace App\Auth;

use App\Models\User;
use Exception;

class Auth
{
    /**
     * Holds the single instance.
     *
     * @var Auth
     */
    private static $instance;

    /**
     * Retrieve the single instance of Auth.
     *
     * @return Auth
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Check if any user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Determine if the authenticated user is a regular user or manager.
     *
     * @return bool
     */
    public function isUser()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Determine if the authenticated user is a manager.
     *
     * @return bool
     */
    public function isManager()
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        $user = $this->user();
        return $user && $user->role === 'manager';
    }

    /**
     * Alias for isManager() to maintain compatibility.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->isManager();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return User|null
     */
    public function user()
    {
        if ($this->isUser()) {
            return User::getById($_SESSION['user_id']);
        }
        return null;
    }

    /**
     * Get user ID.
     *
     * @return int|null
     */
    public function id()
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Login a user by email and password.
     *
     * @param string $email
     * @param string $password
     * @return User|false
     */
    public function login($email, $password)
    {
        // Logout any existing session
        $this->logout();

        $user = User::getByEmail($email);
        
        if ($user && password_verify($password, $user->password)) {
            $_SESSION['user_id'] = $user->id;
            session_regenerate_id(true);
            return $user;
        }
        
        return false;
    }

    /**
     * Register a new user.
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @param string $role
     * @return User
     * @throws Exception If the email is already registered or registration fails.
     */
    public function register($name, $email, $password, $role = 'user')
    {
        $existing = User::getByEmail($email);
        if ($existing) {
            throw new Exception("البريد الإلكتروني مسجل مسبقاً");
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $userId = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => $hashedPassword,
            'role'     => $role
        ]);

        if ($userId) {
            $_SESSION['user_id'] = $userId;
            session_regenerate_id(true);
            return User::getById($userId);
        }

        throw new Exception("فشل التسجيل، حاول مرة أخرى");
    }

    /**
     * Update the profile for the authenticated user.
     *
     * @param array $data
     * @return bool
     */
    public function updateProfile(array $data)
    {
        if (!$this->isUser()) {
            return false;
        }

        $user = $this->user();
        if (!$user) {
            return false;
        }

        return User::update($user->id, $data);
    }

    /**
     * Update the password for the authenticated user.
     *
     * @param string $oldPassword
     * @param string $newPassword
     * @return bool
     * @throws Exception If the old password is incorrect.
     */
    public function updatePassword($oldPassword, $newPassword)
    {
        if (!$this->isUser()) {
            return false;
        }

        $user = $this->user();
        if (!$user) {
            return false;
        }

        if (password_verify($oldPassword, $user->password)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            return User::update($user->id, ['password' => $hashedPassword]);
        }

        throw new Exception("كلمة المرور القديمة غير صحيحة");
    }

    /**
     * Delete the authenticated account.
     *
     * @return bool
     */
    public function deleteAccount()
    {
        if (!$this->isUser()) {
            return false;
        }

        $user = $this->user();
        if (!$user) {
            return false;
        }

        $result = User::delete($user->id);
        if ($result) {
            unset($_SESSION['user_id']);
        }

        return $result;
    }

    /**
     * Log out the authenticated user.
     *
     * @return void
     */
    public function logout()
    {
        if (isset($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
        }
    }
}
