<?php
namespace App\Models;

use App\Database\DB;
use PDO;

class Review
{
    /**
     * Create a new review.
     *
     * @param array $data
     * @return int|false Review ID or false on failure
     */
    public static function create($data)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            INSERT INTO reviews (answer_id, reviewer_id, score, comment) 
            VALUES (?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['answer_id'],
            $data['reviewer_id'],
            $data['score'],
            $data['comment'] ?? null
        ]);

        return $result ? $db->lastInsertId() : false;
    }

    /**
     * Get review by answer ID.
     *
     * @param int $answerId
     * @return object|null
     */
    public static function getByAnswer($answerId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT r.*, u.name as reviewer_name 
            FROM reviews r
            LEFT JOIN users u ON r.reviewer_id = u.id
            WHERE r.answer_id = ?
        ");
        $stmt->execute([$answerId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get review by ID.
     *
     * @param int $id
     * @return object|null
     */
    public static function getById($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT r.*, u.name as reviewer_name 
            FROM reviews r
            LEFT JOIN users u ON r.reviewer_id = u.id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Update review.
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
            if (in_array($key, ['score', 'comment'])) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE reviews SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        
        return $stmt->execute($values);
    }

    /**
     * Delete review.
     *
     * @param int $id
     * @return bool
     */
    public static function delete($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("DELETE FROM reviews WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get reviews by reviewer.
     *
     * @param int $reviewerId
     * @return array
     */
    public static function getByReviewer($reviewerId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT r.*, a.answer_text, u.name as user_name, q.question_text 
            FROM reviews r
            LEFT JOIN answers a ON r.answer_id = a.id
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN questions q ON a.question_id = q.id
            WHERE r.reviewer_id = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$reviewerId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get recent reviews.
     *
     * @param int $limit
     * @return array
     */
    public static function getRecent($limit = 10)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT r.*, u1.name as reviewer_name, u2.name as user_name, q.question_text 
            FROM reviews r
            LEFT JOIN users u1 ON r.reviewer_id = u1.id
            LEFT JOIN answers a ON r.answer_id = a.id
            LEFT JOIN users u2 ON a.user_id = u2.id
            LEFT JOIN questions q ON a.question_id = q.id
            ORDER BY r.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get average score for a user.
     *
     * @param int $userId
     * @return float
     */
    public static function getAverageScoreForUser($userId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT AVG(r.score) as avg_score 
            FROM reviews r
            JOIN answers a ON r.answer_id = a.id
            WHERE a.user_id = ?
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->avg_score ?? 0;
    }

    /**
     * Count total reviews.
     *
     * @return int
     */
    public static function count()
    {
        $db = DB::getConnection();
        $stmt = $db->query("SELECT COUNT(*) as total FROM reviews");
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }
}
