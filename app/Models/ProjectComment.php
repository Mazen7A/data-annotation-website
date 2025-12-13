<?php
namespace App\Models;

use App\Database\DB;
use PDO;

class ProjectComment
{
    /**
     * Create a new project comment.
     *
     * @param int $projectId
     * @param int $userId
     * @param string $comment
     * @return int|false Comment ID or false on failure
     */
    public static function create($projectId, $userId, $comment)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            INSERT INTO project_comments (project_id, user_id, comment, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $projectId,
            $userId,
            $comment
        ]);

        return $result ? $db->lastInsertId() : false;
    }

    /**
     * Get comments by project ID.
     *
     * @param int $projectId
     * @return array
     */
    public static function getByProject($projectId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT c.*, u.name as user_name 
            FROM project_comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.project_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Delete comment.
     *
     * @param int $id
     * @return bool
     */
    public static function delete($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("DELETE FROM project_comments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
