<?php
namespace App\Controllers;

use App\Models\Project;
use App\Models\Session;
use App\Models\ProjectComment;

class ProjectController
{
    /**
     * List all projects.
     */
    public function index()
    {
        $projects = Project::getAll();
        
        // Get user's sessions to show progress
        $userId = auth()->id();
        $userSessions = [];
        if ($userId) {
            $sessions = Session::getByUser($userId);
            foreach ($sessions as $session) {
                $userSessions[$session->project_id] = $session;
            }
        }
        
        view('user/projects/index', [
            'projects' => $projects,
            'userSessions' => $userSessions
        ]);
    }

    /**
     * Show project details.
     */
    public function show()
    {
        $projectId = $_GET['id'] ?? 0;
        $project = Project::getById($projectId);
        
        if (!$project) {
            $_SESSION['error'] = 'المشروع غير موجود';
            header('Location: ' . route('projects'));
            exit;
        }
        
        // Get user's session if authenticated
        $session = null;
        if (auth()->check()) {
            $userId = auth()->id();
            $session = Session::getOrCreate($userId, $projectId);
        }

        // Get project comments
        $comments = ProjectComment::getByProject($projectId);
        
        // Get category statistics
        $categoryStats = \App\Models\Question::countByCategory($projectId);
        $typeStats = \App\Models\Question::countByType($projectId);

        // User answer stats
        $answersWithStatus = [];
        $answerStats = ['total' => 0, 'correct' => 0, 'incorrect' => 0, 'pending' => 0];
        if (auth()->check()) {
            $answersWithStatus = \App\Models\Answer::getByUserAndProjectWithStatus(auth()->id(), $projectId);
            foreach ($answersWithStatus as $ans) {
                $answerStats['total']++;
                if ($ans->status['type'] === 'correct') {
                    $answerStats['correct']++;
                } elseif ($ans->status['type'] === 'incorrect') {
                    $answerStats['incorrect']++;
                } else {
                    $answerStats['pending']++;
                }
            }
        }
        
        view('user/projects/show', [
            'project' => $project,
            'session' => $session,
            'comments' => $comments,
            'categoryStats' => $categoryStats,
            'typeStats' => $typeStats,
            'answersWithStatus' => $answersWithStatus,
            'answerStats' => $answerStats
        ]);
    }

    /**
     * Start a project session.
     */
    public function start()
    {
        $projectId = $_POST['project_id'] ?? 0;
        $userId = auth()->id();
        
        $project = Project::getById($projectId);
        if (!$project) {
            $_SESSION['error'] = 'المشروع غير موجود';
            header('Location: ' . route('projects'));
            exit;
        }
        
        // Create or get session
        $session = Session::getOrCreate($userId, $projectId);
        
        if ($session) {
            $_SESSION['success'] = 'تم بدء المشروع بنجاح';
            header('Location: ' . route('questions', ['session_id' => $session->id]));
        } else {
            $_SESSION['error'] = 'حدث خطأ، حاول مرة أخرى';
            header('Location: ' . route('projects.show', ['id' => $projectId]));
        }
        exit;
    }

    /**
     * Add a comment to the project.
     */
    public function addComment()
    {
        $projectId = $_POST['project_id'] ?? 0;
        $commentText = $_POST['comment'] ?? '';
        $userId = auth()->id();

        if (!$userId || !$projectId || empty($commentText)) {
            $_SESSION['error'] = 'الرجاء كتابة تعليق';
            header('Location: ' . route('projects.show', ['id' => $projectId]));
            exit;
        }

        if (ProjectComment::create($projectId, $userId, $commentText)) {
            $_SESSION['success'] = 'تم إضافة التعليق بنجاح';
        } else {
            $_SESSION['error'] = 'فشل إضافة التعليق';
        }

        header('Location: ' . route('projects.show', ['id' => $projectId]));
        exit;
    }

    /**
     * Delete a comment (only by owner).
     */
    public function deleteComment()
    {
        $commentId = $_POST['comment_id'] ?? 0;
        $projectId = $_POST['project_id'] ?? 0;
        $userId = auth()->id();

        if (!$userId || !$commentId) {
            $_SESSION['error'] = 'طلب غير صالح';
            header('Location: ' . route('projects.show', ['id' => $projectId]));
            exit;
        }

        // Get the comment to verify ownership
        $db = \App\Database\DB::getConnection();
        $stmt = $db->prepare("SELECT user_id FROM project_comments WHERE id = ?");
        $stmt->execute([$commentId]);
        $comment = $stmt->fetch(\PDO::FETCH_OBJ);

        if (!$comment) {
            $_SESSION['error'] = 'التعليق غير موجود';
            header('Location: ' . route('projects.show', ['id' => $projectId]));
            exit;
        }

        // Check if user is the owner
        if ($comment->user_id != $userId) {
            $_SESSION['error'] = 'غير مصرح لك بحذف هذا التعليق';
            header('Location: ' . route('projects.show', ['id' => $projectId]));
            exit;
        }

        if (ProjectComment::delete($commentId)) {
            $_SESSION['success'] = 'تم حذف التعليق بنجاح';
        } else {
            $_SESSION['error'] = 'فشل حذف التعليق';
        }

        header('Location: ' . route('projects.show', ['id' => $projectId]));
        exit;
    }
}
