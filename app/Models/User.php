<?php
namespace App\Models;

use App\Database\DB;
use PDO;

class User
{
    public $id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $created_at;

    /**
     * Get all users.
     *
     * @return array
     */
    public static function getAll()
    {
        $db = DB::getConnection();
        $stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Get user by ID.
     *
     * @param int $id
     * @return object|null
     */
    public static function getById($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Get user by email.
     *
     * @param string $email
     * @return object|null
     */
    public static function getByEmail($email)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Find user by email (alias for getByEmail).
     *
     * @param string $email
     * @return object|null
     */
    public static function findByEmail($email)
    {
        return self::getByEmail($email);
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return int|false User ID or false on failure
     */
    public static function create($data)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            INSERT INTO users (name, email, password, role) 
            VALUES (?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['name'],
            $data['email'],
            $data['password'],
            $data['role'] ?? 'user'
        ]);

        return $result ? $db->lastInsertId() : false;
    }

    /**
     * Update user.
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
            if (in_array($key, ['name', 'email', 'password', 'role'])) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        
        return $stmt->execute($values);
    }

    /**
     * Delete user.
     *
     * @param int $id
     * @return bool
     */
    public static function delete($id)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get users by role.
     *
     * @param string $role
     * @return array
     */
    public static function getByRole($role)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE role = ? ORDER BY created_at DESC");
        $stmt->execute([$role]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Count total users.
     *
     * @return int
     */
    public static function count()
    {
        $db = DB::getConnection();
        $stmt = $db->query("SELECT COUNT(*) as total FROM users");
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    /**
     * Count users by role.
     *
     * @param string $role
     * @return int
     */
    public static function countByRole($role)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM users WHERE role = ?");
        $stmt->execute([$role]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }
    /**
     * Get top contributors based on answer count.
     *
     * @param int $limit
     * @return array
     */
    public static function getTopContributors($limit = 3)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("
            SELECT u.*, COUNT(a.id) as answer_count 
            FROM users u 
            LEFT JOIN answers a ON u.id = a.user_id 
            GROUP BY u.id 
            ORDER BY answer_count DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
