<?php
namespace App\Models;

use App\Database\DB;
use PDO;

class QuestionOption
{
    /**
     * Get options by question.
     *
     * @param int $questionId
     * @return array
     */
    public static function getByQuestion($questionId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM question_options WHERE question_id = ? ORDER BY id ASC");
        $stmt->execute([$questionId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Create a new option.
     *
     * @param array $data
     * @return int|false Option ID or false on failure
     */
    public static function create($data)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            INSERT INTO question_options (question_id, option_text, is_correct) 
            VALUES (?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['question_id'],
            $data['option_text'],
            $data['is_correct'] ?? 0
        ]);

        return $result ? $db->lastInsertId() : false;
    }

    /**
     * Update option.
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
            if (in_array($key, ['option_text', 'is_correct'])) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE question_options SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        
        return $stmt->execute($values);
    }

    /**
     * Delete option.
     *
     * @param int $id
     * @return bool
     */
    public static function delete($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("DELETE FROM question_options WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Delete all options for a question.
     *
     * @param int $questionId
     * @return bool
     */
    public static function deleteByQuestion($questionId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("DELETE FROM question_options WHERE question_id = ?");
        return $stmt->execute([$questionId]);
    }

    /**
     * Get correct options for a question.
     *
     * @param int $questionId
     * @return array
     */
    public static function getCorrectOptions($questionId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM question_options WHERE question_id = ? AND is_correct = 1");
        $stmt->execute([$questionId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
