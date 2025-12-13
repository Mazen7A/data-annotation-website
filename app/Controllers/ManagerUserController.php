<?php
namespace App\Controllers;

use App\Models\User;

class ManagerUserController
{
    /**
     * List all users.
     */
    public function index()
    {
        $users = User::getAll();
        view('manager/users/index', ['users' => $users]);
    }

    /**
     * Show user details.
     */
    public function show()
    {
        $userId = $_GET['id'] ?? 0;
        $user = User::getById($userId);

        if (!$user) {
            $_SESSION['error'] = 'المستخدم غير موجود';
            header('Location: ' . route('manager.users'));
            exit;
        }

        // Get user statistics
        $sessions = \App\Models\Session::getByUser($userId);
        $answers = \App\Models\Answer::getByUser($userId);
        $avgScore = \App\Models\Review::getAverageScoreForUser($userId);

        view('manager/users/show', [
            'user' => $user,
            'sessions' => $sessions,
            'answers' => $answers,
            'avgScore' => $avgScore
        ]);
    }

    /**
     * Update user role.
     */
    public function updateRole()
    {
        $userId = $_POST['id'] ?? 0;
        $role = $_POST['role'] ?? '';

        if (!in_array($role, ['user', 'manager'])) {
            $_SESSION['error'] = 'الدور غير صحيح';
            header('Location: ' . route('manager.users'));
            exit;
        }

        $result = User::update($userId, ['role' => $role]);

        if ($result) {
            $_SESSION['success'] = 'تم تحديث دور المستخدم بنجاح';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء التحديث';
        }

        header('Location: ' . route('manager.users'));
        exit;
    }

    /**
     * Delete user.
     */
    public function delete()
    {
        $userId = $_POST['id'] ?? 0;
        
        // Prevent deleting yourself
        if ($userId == auth()->id()) {
            $_SESSION['error'] = 'لا يمكنك حذف حسابك الخاص';
            header('Location: ' . route('manager.users'));
            exit;
        }

        $result = User::delete($userId);

        if ($result) {
            $_SESSION['success'] = 'تم حذف المستخدم بنجاح';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء الحذف';
        }

        header('Location: ' . route('manager.users'));
        exit;
    }
}
