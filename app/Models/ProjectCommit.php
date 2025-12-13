<?php
namespace App\Models;

use App\Database\DB;
use PDO;

class ProjectCommit
{
    /**
     * Create a new commit.
     *
     * @param int $projectId
     * @param int $userId
     * @param string $message
     * @return int|false Commit ID or false on failure
     */
    public static function create($projectId, $userId, $message)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            INSERT INTO project_commits (project_id, user_id, message) 
            VALUES (?, ?, ?)
        ");
        
        $result = $stmt->execute([$projectId, $userId, $message]);
        return $result ? $db->lastInsertId() : false;
    }

    /**
     * Get commits by project.
     *
     * @param int $projectId
     * @return array
     */
    public static function getByProject($projectId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT pc.*, u.name as user_name 
            FROM project_commits pc
            LEFT JOIN users u ON pc.user_id = u.id
            WHERE pc.project_id = ?
            ORDER BY pc.created_at DESC
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get recent commits across all projects.
     *
     * @param int $limit
     * @return array
     */
    public static function getRecent($limit = 10)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT pc.*, u.name as user_name, p.name as project_name 
            FROM project_commits pc
            LEFT JOIN users u ON pc.user_id = u.id
            LEFT JOIN projects p ON pc.project_id = p.id
            ORDER BY pc.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get commits by user.
     *
     * @param int $userId
     * @return array
     */
    public static function getByUser($userId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT pc.*, p.name as project_name 
            FROM project_commits pc
            LEFT JOIN projects p ON pc.project_id = p.id
            WHERE pc.user_id = ?
            ORDER BY pc.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get daily commit stats for the last 7 days.
     *
     * @return array
     */
    public static function getDailyStats()
    {
        $db = DB::getConnection();
        $stmt = $db->query("
            SELECT DATE(created_at) as date, COUNT(*) as count 
            FROM project_commits 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
