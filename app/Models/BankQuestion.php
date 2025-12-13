<?php
namespace App\Models;

use App\Database\DB;
use PDO;

class BankQuestion
{
    public static function getAll()
    {
        $db = DB::getConnection();
        $stmt = $db->query("SELECT * FROM bank_questions ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function filter($type = null, $category = null, $search = null)
    {
        $db = DB::getConnection();
        $where = [];
        $params = [];

        if ($type) {
            $where[] = "question_type = ?";
            $params[] = $type;
        }
        if ($category) {
            $where[] = "category LIKE ?";
            $params[] = '%' . $category . '%';
        }
        if ($search) {
            $where[] = "(question_text LIKE ?)";
            $params[] = '%' . $search . '%';
        }

        $sql = "SELECT * FROM bank_questions";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= " ORDER BY created_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function getById($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM bank_questions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public static function countByType(): array
    {
        $db = DB::getConnection();
        $stmt = $db->query("
            SELECT question_type, COUNT(*) as total
            FROM bank_questions
            GROUP BY question_type
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
        $result = ['mcq' => 0, 'true_false' => 0, 'open' => 0, 'list' => 0];
        foreach ($rows as $row) {
            $result[$row->question_type] = (int)$row->total;
        }
        return $result;
    }

    public static function create(array $data)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            INSERT INTO bank_questions (question_text, question_type, category, media_url)
            VALUES (?, ?, ?, ?)
        ");
        $ok = $stmt->execute([
            $data['question_text'],
            $data['question_type'],
            $data['category'] ?? null,
            $data['media_url'] ?? null
        ]);
        return $ok ? $db->lastInsertId() : false;
    }

    public static function delete($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("DELETE FROM bank_questions WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
