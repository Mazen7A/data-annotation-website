<?php
namespace App\Models;

use App\Database\DB;
use PDO;

class Session
{
    /**
     * Get or create a session for user and project.
     *
     * @param int $userId
     * @param int $projectId
     * @return object|null
     */
    public static function getOrCreate($userId, $projectId)
    {
        $db = DB::getConnection();
        
        // Try to get existing session
        $stmt = $db->prepare("
            SELECT s.*, p.name as project_name, p.total_questions,
            CASE WHEN s.completed_at IS NOT NULL THEN 'completed' ELSE 'in_progress' END as status
            FROM sessions s
            LEFT JOIN projects p ON s.project_id = p.id
            WHERE s.user_id = ? AND s.project_id = ?
        ");
        $stmt->execute([$userId, $projectId]);
        $session = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($session) {
            return $session;
        }
        
        // Validate user exists before creating session
        $userCheck = $db->prepare("SELECT id FROM users WHERE id = ?");
        $userCheck->execute([$userId]);
        if (!$userCheck->fetch()) {
            // User doesn't exist, return null
            error_log("Attempted to create session for non-existent user ID: $userId");
            return null;
        }
        
        // Create new session
        $stmt = $db->prepare("
            INSERT INTO sessions (user_id, project_id, progress) 
            VALUES (?, ?, 0)
        ");
        
        if ($stmt->execute([$userId, $projectId])) {
            return self::getOrCreate($userId, $projectId);
        }
        
        return null;
    }

    /**
     * Get session by ID.
     *
     * @param int $id
     * @return object|null
     */
    public static function getById($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT s.*, p.name as project_name, p.total_questions,
            CASE WHEN s.completed_at IS NOT NULL THEN 'completed' ELSE 'in_progress' END as status
            FROM sessions s
            LEFT JOIN projects p ON s.project_id = p.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Update session progress.
     *
     * @param int $id
     * @param float $progress
     * @return bool
     */
    public static function updateProgress($id, $progress)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("UPDATE sessions SET progress = ? WHERE id = ?");
        return $stmt->execute([$progress, $id]);
    }

    /**
     * Mark session as completed.
     *
     * @param int $id
     * @return bool
     */
    public static function complete($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("UPDATE sessions SET progress = 100, completed_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get sessions by user.
     *
     * @param int $userId
     * @return array
     */
    public static function getByUser($userId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT s.*, p.name as project_name, p.total_questions, p.category,
            CASE WHEN s.completed_at IS NOT NULL THEN 'completed' ELSE 'in_progress' END as status
            FROM sessions s
            LEFT JOIN projects p ON s.project_id = p.id
            WHERE s.user_id = ?
            ORDER BY s.started_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get active sessions for user (not completed).
     *
     * @param int $userId
     * @return array
     */
    public static function getActive($userId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT s.*, p.name as project_name, p.total_questions, p.category,
            'in_progress' as status
            FROM sessions s
            LEFT JOIN projects p ON s.project_id = p.id
            WHERE s.user_id = ? AND s.completed_at IS NULL
            ORDER BY s.started_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get completed sessions for user.
     *
     * @param int $userId
     * @return array
     */
    public static function getCompleted($userId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT s.*, p.name as project_name, p.total_questions, p.category,
            'completed' as status
            FROM sessions s
            LEFT JOIN projects p ON s.project_id = p.id
            WHERE s.user_id = ? AND s.completed_at IS NOT NULL
            ORDER BY s.completed_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Calculate and update progress based on answered questions.
     *
     * @param int $sessionId
     * @return bool
     */
    public static function calculateProgress($sessionId)
    {
        $db = DB::getConnection();
        
        // Get session
        $session = self::getById($sessionId);
        if (!$session) {
            return false;
        }
        
        // Count answered questions
        $stmt = $db->prepare("
            SELECT COUNT(*) as answered 
            FROM answers a
            JOIN questions q ON a.question_id = q.id
            WHERE a.user_id = ? AND q.project_id = ?
        ");
        $stmt->execute([$session->user_id, $session->project_id]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        
        $totalQuestions = $session->total_questions;
        if ($totalQuestions == 0) {
            return false;
        }
        
        $progress = ($result->answered / $totalQuestions) * 100;
        
        // Update progress
        self::updateProgress($sessionId, $progress);
        
        // Mark as complete if 100%
        if ($progress >= 100) {
            self::complete($sessionId);
        }
        
        return true;
    }

    /**
     * Count completed sessions by user.
     *
     * @param int $userId
     * @return int
     */
    public static function countCompleted($userId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM sessions WHERE user_id = ? AND completed_at IS NOT NULL");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    /**
     * Count active (in-progress) sessions by user.
     *
     * @param int $userId
     * @return int
     */
    public static function countActive($userId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM sessions WHERE user_id = ? AND completed_at IS NULL");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    /**
     * Count total completed sessions.
     *
     * @return int
     */
    public static function count()
    {
        $db = DB::getConnection();
        $stmt = $db->query("SELECT COUNT(*) as total FROM sessions WHERE completed_at IS NOT NULL");
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    /**
     * Count sessions by project.
     *
     * @param int $projectId
     * @return int
     */
    public static function countByProject($projectId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM sessions WHERE project_id = ?");
        $stmt->execute([$projectId]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }
}
