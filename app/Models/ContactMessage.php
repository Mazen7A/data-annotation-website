<?php
namespace App\Models;

use App\Database\DB;
use PDO;

class ContactMessage
{
    /**
     * Create a new contact message.
     *
     * @param array $data
     * @return int|false Message ID or false on failure
     */
    public static function create($data)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            INSERT INTO contact_messages (user_id, name, email, phone, type, subject, message) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['user_id'] ?? null,
            $data['name'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['type'],
            $data['subject'],
            $data['message']
        ]);

        return $result ? $db->lastInsertId() : false;
    }

    /**
     * Get all messages.
     *
     * @return array
     */
    public static function getAll()
    {
        $db = DB::getConnection();
        $stmt = $db->query("
            SELECT cm.*, u.name as user_name 
            FROM contact_messages cm
            LEFT JOIN users u ON cm.user_id = u.id
            ORDER BY cm.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get message by ID.
     *
     * @param int $id
     * @return object|null
     */
    public static function getById($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT cm.*, u.name as user_name 
            FROM contact_messages cm
            LEFT JOIN users u ON cm.user_id = u.id
            WHERE cm.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Update message status.
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public static function updateStatus($id, $status)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    /**
     * Add admin reply.
     *
     * @param int $id
     * @param string $reply
     * @return bool
     */
    public static function addReply($id, $reply)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("UPDATE contact_messages SET admin_reply = ?, status = 'resolved' WHERE id = ?");
        return $stmt->execute([$reply, $id]);
    }

    /**
     * Get messages by status.
     *
     * @param string $status
     * @return array
     */
    public static function getByStatus($status)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT cm.*, u.name as user_name 
            FROM contact_messages cm
            LEFT JOIN users u ON cm.user_id = u.id
            WHERE cm.status = ?
            ORDER BY cm.created_at DESC
        ");
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get messages by type.
     *
     * @param string $type
     * @return array
     */
    public static function getByType($type)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT cm.*, u.name as user_name 
            FROM contact_messages cm
            LEFT JOIN users u ON cm.user_id = u.id
            WHERE cm.type = ?
            ORDER BY cm.created_at DESC
        ");
        $stmt->execute([$type]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get messages by user.
     *
     * @param int $userId
     * @return array
     */
    public static function getByUser($userId)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM contact_messages WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Count messages by status.
     *
     * @param string $status
     * @return int
     */
    public static function countByStatus($status)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM contact_messages WHERE status = ?");
        $stmt->execute([$status]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    /**
     * Count total messages.
     *
     * @return int
     */
    public static function count()
    {
        $db = DB::getConnection();
        $stmt = $db->query("SELECT COUNT(*) as total FROM contact_messages");
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    /**
     * Delete message.
     *
     * @param int $id
     * @return bool
     */
    public static function delete($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("DELETE FROM contact_messages WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
