<?php
namespace App\Models;

use App\Database\DB;
use PDO;

class Setting
{
    /**
     * Get setting by key.
     *
     * @param string $key
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("SELECT setting_value, setting_type FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$result) {
            return $default;
        }
        
        // Parse value based on type
        switch ($result->setting_type) {
            case 'boolean':
                return $result->setting_value === 'true' || $result->setting_value === '1';
            case 'json':
                return json_decode($result->setting_value, true);
            default:
                return $result->setting_value;
        }
    }

    /**
     * Set setting value.
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return bool
     */
    public static function set($key, $value, $type = 'text')
    {
        $db = DB::getConnection();
        
        // Convert value based on type
        if ($type === 'boolean') {
            $value = $value ? 'true' : 'false';
        } elseif ($type === 'json') {
            $value = json_encode($value);
        }
        
        $stmt = $db->prepare("
            INSERT INTO settings (setting_key, setting_value, setting_type) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()
        ");
        
        return $stmt->execute([$key, $value, $type, $value]);
    }

    /**
     * Get all settings.
     *
     * @return array
     */
    public static function getAll()
    {
        $db = DB::getConnection();
        $stmt = $db->query("SELECT * FROM settings ORDER BY setting_key");
        $results = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        $settings = [];
        foreach ($results as $result) {
            $value = $result->setting_value;
            
            // Parse value based on type
            switch ($result->setting_type) {
                case 'boolean':
                    $value = $value === 'true' || $value === '1';
                    break;
                case 'json':
                    $value = json_decode($value, true);
                    break;
            }
            
            $settings[$result->setting_key] = $value;
        }
        
        return $settings;
    }

    /**
     * Delete setting.
     *
     * @param string $key
     * @return bool
     */
    public static function delete($key)
    {
        $db = DB::getConnection();
        $stmt = $db->prepare("DELETE FROM settings WHERE setting_key = ?");
        return $stmt->execute([$key]);
    }

    /**
     * Get theme settings.
     *
     * @return array
     */
    public static function getThemeSettings()
    {
        return [
            'default_theme' => self::get('default_theme', 'purple'),
            'default_mode' => self::get('default_mode', 'auto'),
            'available_themes' => self::get('available_themes', ['purple', 'blue', 'green']),
            'enable_animations' => self::get('enable_animations', true),
            'primary_color' => self::get('primary_color', '#667eea'),
            'secondary_color' => self::get('secondary_color', '#764ba2')
        ];
    }
}
