<?php
namespace App\Models;

use App\Database\DB;
use PDO;

class BankQuestionOption
{
    public static function create(array $data)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            INSERT INTO bank_question_options (bank_question_id, option_text, is_correct)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([
            $data['bank_question_id'],
            $data['option_text'],
            $data['is_correct'] ?? 0
        ]);
    }

    public static function getByQuestion($bankQuestionId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM bank_question_options WHERE bank_question_id = ? ORDER BY id ASC");
        $stmt->execute([$bankQuestionId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function deleteByQuestion($bankQuestionId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("DELETE FROM bank_question_options WHERE bank_question_id = ?");
        return $stmt->execute([$bankQuestionId]);
    }
}
