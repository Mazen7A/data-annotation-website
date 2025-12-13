<?php
namespace App\Models;

use App\Database\DB;
use PDO;

class Project
{
    /**
     * Get all projects.
     *
     * @return array
     */
    public static function getAll()
    {
        $db = DB::getConnection();
        $stmt = $db->query("
            SELECT p.*, u.name as creator_name 
            FROM projects p
            LEFT JOIN users u ON p.created_by = u.id
            ORDER BY p.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get project by ID.
     *
     * @param int $id
     * @return object|null
     */
    public static function getById($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT p.*, u.name as creator_name 
            FROM projects p
            LEFT JOIN users u ON p.created_by = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Create a new project.
     *
     * @param array $data
     * @return int|false Project ID or false on failure
     */
    public static function create($data)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            INSERT INTO projects (
                name, summary, description, category, image_url,
                latitude, longitude, location_name, created_by
            ) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['name'],
            $data['summary'] ?? null,
            $data['description'] ?? null,
            $data['category'] ?? null,
            $data['image_url'] ?? null,
            $data['latitude'] ?? null,
            $data['longitude'] ?? null,
            $data['location_name'] ?? null,
            $data['created_by']
        ]);

        return $result ? $db->lastInsertId() : false;
    }

    /**
     * Update project.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public static function update($id, $data)
    {
        $db = DB::getConnection();
        
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, [
                'name',
                'summary',
                'description',
                'category',
                'image_url',
                'total_questions',
                'latitude',
                'longitude',
                'location_name'
            ])) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE projects SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        
        return $stmt->execute($values);
    }

    /**
     * Delete project.
     *
     * @param int $id
     * @return bool
     */
    public static function delete($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("DELETE FROM projects WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get projects by creator.
     *
     * @param int $userId
     * @return array
     */
    public static function getByCreator($userId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM projects WHERE created_by = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get projects by category.
     *
     * @param string $category
     * @return array
     */
    public static function getByCategory($category)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM projects WHERE category = ? ORDER BY created_at DESC");
        $stmt->execute([$category]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Increment question count.
     *
     * @param int $projectId
     * @return bool
     */
    public static function incrementQuestionCount($projectId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("UPDATE projects SET total_questions = total_questions + 1 WHERE id = ?");
        return $stmt->execute([$projectId]);
    }

    /**
     * Decrement question count.
     *
     * @param int $projectId
     * @return bool
     */
    public static function decrementQuestionCount($projectId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("UPDATE projects SET total_questions = total_questions - 1 WHERE id = ? AND total_questions > 0");
        return $stmt->execute([$projectId]);
    }

    /**
     * Count total projects.
     *
     * @return int
     */
    public static function count()
    {
        $db = DB::getConnection();
        $stmt = $db->query("SELECT COUNT(*) as total FROM projects");
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    /**
     * Search projects.
     *
     * @param string $query
     * @return array
     */
    public static function search($query)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT * FROM projects 
            WHERE name LIKE ? OR description LIKE ? OR category LIKE ?
            ORDER BY created_at DESC
        ");
        $searchTerm = "%$query%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get project counts by category.
     *
     * @return array
     */
    public static function getCategoryStats()
    {
        $db = DB::getConnection();
        $stmt = $db->query("
            SELECT category, COUNT(*) as count 
            FROM projects 
            GROUP BY category
        ");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
