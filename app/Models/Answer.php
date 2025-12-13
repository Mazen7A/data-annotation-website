<?php
namespace App\Models;

use App\Database\DB;
use PDO;

class Answer
{
    /**
     * Create a new answer.
     *
     * @param array $data
     * @return int|false Answer ID or false on failure
     */
    public static function create($data)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            INSERT INTO answers (user_id, question_id, answer_text, selected_options) 
            VALUES (?, ?, ?, ?)
        ");
        
        $selectedOptions = isset($data['selected_options']) ? json_encode($data['selected_options']) : null;
        
        $result = $stmt->execute([
            $data['user_id'],
            $data['question_id'],
            $data['answer_text'] ?? null,
            $selectedOptions
        ]);

        return $result ? $db->lastInsertId() : false;
    }

    /**
     * Get answer by ID.
     *
     * @param int $id
     * @return object|null
     */
    public static function getById($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT a.*, u.name as user_name, q.question_text, q.question_type 
            FROM answers a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN questions q ON a.question_id = q.id
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        $answer = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($answer && $answer->selected_options) {
            $answer->selected_options = json_decode($answer->selected_options);
        }
        
        return $answer;
    }

    /**
     * Get answers by user.
     *
     * @param int $userId
     * @return array
     */
    public static function getByUser($userId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT a.*, q.question_text, q.question_type, p.name as project_name 
            FROM answers a
            LEFT JOIN questions q ON a.question_id = q.id
            LEFT JOIN projects p ON q.project_id = p.id
            WHERE a.user_id = ?
            ORDER BY a.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get answers by question.
     *
     * @param int $questionId
     * @return array
     */
    public static function getByQuestion($questionId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT a.*, u.name as user_name 
            FROM answers a
            LEFT JOIN users u ON a.user_id = u.id
            WHERE a.question_id = ?
            ORDER BY a.created_at DESC
        ");
        $stmt->execute([$questionId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get answers by project.
     *
     * @param int $projectId
     * @return array
     */
    public static function getByProject($projectId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT a.*, u.name as user_name, q.question_text, q.question_type 
            FROM answers a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN questions q ON a.question_id = q.id
            WHERE q.project_id = ?
            ORDER BY a.created_at DESC
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get user's answers for a project with correctness status.
     *
     * @param int $userId
     * @param int $projectId
     * @return array
     */
    public static function getByUserAndProjectWithStatus($userId, $projectId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT a.*, q.question_text, q.question_type, q.id as qid
            FROM answers a
            JOIN questions q ON a.question_id = q.id
            WHERE a.user_id = ? AND q.project_id = ?
            ORDER BY a.created_at DESC
        ");
        $stmt->execute([$userId, $projectId]);
        $answers = $stmt->fetchAll(PDO::FETCH_OBJ);

        foreach ($answers as $answer) {
            if ($answer->selected_options) {
                $answer->selected_options = json_decode($answer->selected_options, true) ?: [];
            } else {
                $answer->selected_options = [];
            }

            $status = [
                'label' => 'قيد المراجعة',
                'type'  => 'pending',
                'is_correct' => null
            ];

            if (in_array($answer->question_type, ['mcq', 'true_false'])) {
                $correctOptions = QuestionOption::getCorrectOptions($answer->qid);
                $correctIds = array_map(fn($opt) => (int)$opt->id, $correctOptions);

                sort($correctIds);
                $selectedIds = array_map('intval', $answer->selected_options);
                sort($selectedIds);

                $isCorrect = ($selectedIds === $correctIds);
                $status = [
                    'label' => $isCorrect ? 'صحيح' : 'خاطئ',
                    'type'  => $isCorrect ? 'correct' : 'incorrect',
                    'is_correct' => $isCorrect
                ];
            }

            $answer->status = $status;
        }

        return $answers;
    }

    /**
     * Compute per-project stats for a user (correct/incorrect counts and grade%).
     *
     * @param int $userId
     * @param int $projectId
     * @return array
     */
    public static function statsByUserAndProject($userId, $projectId): array
    {
        $answers = self::getByUserAndProjectWithStatus($userId, $projectId);
        $correct = 0;
        $incorrect = 0;

        foreach ($answers as $answer) {
            if (!isset($answer->status['type'])) {
                continue;
            }
            if ($answer->status['type'] === 'correct') {
                $correct++;
            } elseif ($answer->status['type'] === 'incorrect') {
                $incorrect++;
            }
        }

        $gradedTotal = $correct + $incorrect;
        $gradePercent = $gradedTotal > 0 ? round(($correct / $gradedTotal) * 100) : 0;

        return [
            'correct' => $correct,
            'incorrect' => $incorrect,
            'total_answered' => count($answers),
            'graded_total' => $gradedTotal,
            'percent' => $gradePercent
        ];
    }

    /**
     * Check if user has answered a question.
     *
     * @param int $userId
     * @param int $questionId
     * @return bool
     */
    public static function hasAnswered($userId, $questionId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT id FROM answers WHERE user_id = ? AND question_id = ?");
        $stmt->execute([$userId, $questionId]);
        return $stmt->fetch() !== false;
    }

    /**
     * Get user's answer for a specific question.
     *
     * @param int $userId
     * @param int $questionId
     * @return object|null
     */
    public static function getUserAnswer($userId, $questionId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM answers WHERE user_id = ? AND question_id = ?");
        $stmt->execute([$userId, $questionId]);
        $answer = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($answer && $answer->selected_options) {
            $answer->selected_options = json_decode($answer->selected_options);
        }
        
        return $answer;
    }

    /**
     * Count answers by user.
     *
     * @param int $userId
     * @return int
     */
    public static function countByUser($userId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM answers WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    /**
     * Count total answers.
     *
     * @return int
     */
    public static function count()
    {
        $db = DB::getConnection();
        $stmt = $db->query("SELECT COUNT(*) as total FROM answers");
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    /**
     * Count answers by project.
     *
     * @param int $projectId
     * @return int
     */
    public static function countByProject($projectId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT COUNT(a.id) as total 
            FROM answers a
            JOIN questions q ON a.question_id = q.id
            WHERE q.project_id = ?
        ");
        $stmt->execute([$projectId]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    /**
     * Get answers without reviews.
     *
     * @param int $limit
     * @return array
     */
    public static function getUnreviewed($limit = 50)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT a.*, u.name as user_name, q.question_text, q.question_type, p.name as project_name 
            FROM answers a
            LEFT JOIN reviews r ON a.id = r.answer_id
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN questions q ON a.question_id = q.id
            LEFT JOIN projects p ON q.project_id = p.id
            WHERE r.id IS NULL
            ORDER BY a.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
