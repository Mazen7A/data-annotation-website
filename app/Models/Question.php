<?php
namespace App\Models;

use App\Database\DB;
use PDO;

class Question
{
    /**
     * Get questions by project.
     *
     * @param int $projectId
     * @return array
     */
    public static function getByProject($projectId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM questions WHERE project_id = ? ORDER BY id ASC");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get question by ID with options.
     *
     * @param int $id
     * @return object|null
     */
    public static function getById($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->execute([$id]);
        $question = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($question && in_array($question->question_type, ['mcq', 'true_false'])) {
            $question->options = QuestionOption::getByQuestion($id);
        }
        
        return $question;
    }

    /**
     * Create a new question.
     *
     * @param array $data
     * @return int|false Question ID or false on failure
     */
    public static function create($data)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            INSERT INTO questions (project_id, question_text, question_type, category, media_url) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['project_id'],
            $data['question_text'],
            $data['question_type'],
            $data['category'] ?? null,
            $data['media_url'] ?? null
        ]);

        return $result ? $db->lastInsertId() : false;
    }

    /**
     * Update question.
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
            if (in_array($key, ['question_text', 'question_type', 'category', 'media_url'])) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE questions SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        
        return $stmt->execute($values);
    }

    /**
     * Delete question.
     *
     * @param int $id
     * @return bool
     */
    public static function delete($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("DELETE FROM questions WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get questions by type.
     *
     * @param int $projectId
     * @param string $type
     * @return array
     */
    public static function getByType($projectId, $type)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM questions WHERE project_id = ? AND question_type = ? ORDER BY id ASC");
        $stmt->execute([$projectId, $type]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get questions by category.
     *
     * @param int $projectId
     * @param string $category
     * @return array
     */
    public static function getByCategory($projectId, $category)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM questions WHERE project_id = ? AND category = ? ORDER BY id ASC");
        $stmt->execute([$projectId, $category]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Count questions in a project.
     *
     * @param int $projectId
     * @return int
     */
    public static function countByProject($projectId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM questions WHERE project_id = ?");
        $stmt->execute([$projectId]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    /**
     * Get next unanswered question for user in project.
     *
     * @param int $projectId
     * @param int $userId
     * @return object|null
     */
    public static function getNextUnanswered($projectId, $userId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT q.* FROM questions q
            LEFT JOIN answers a ON q.id = a.question_id AND a.user_id = ?
            WHERE q.project_id = ? AND a.id IS NULL
            ORDER BY q.id ASC
            LIMIT 1
        ");
        $stmt->execute([$userId, $projectId]);
        $question = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($question && in_array($question->question_type, ['mcq', 'true_false'])) {
            $question->options = QuestionOption::getByQuestion($question->id);
        }
        
        return $question;
    }

    /**
     * Count questions by category for a project.
     *
     * @param int $projectId
     * @return array
     */
    public static function countByCategory($projectId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT category, COUNT(*) as count 
            FROM questions 
            WHERE project_id = ? 
            GROUP BY category
            ORDER BY count DESC
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Count questions by type for a project.
     *
     * @param int $projectId
     * @return array keyed by type
     */
    public static function countByType($projectId): array
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT question_type, COUNT(*) as count
            FROM questions
            WHERE project_id = ?
            GROUP BY question_type
        ");
        $stmt->execute([$projectId]);
        $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
        $result = [
            'mcq' => 0,
            'true_false' => 0,
            'open' => 0,
            'list' => 0
        ];
        foreach ($rows as $row) {
            $result[$row->question_type] = (int) $row->count;
        }
        return $result;
    }
}
