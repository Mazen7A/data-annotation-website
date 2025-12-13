<?php
namespace App\Database;

use PDO;
use PDOException;

class DB
{
    // Holds the single instance of the PDO connection.
    private static $instance = null;

    // Private constructor prevents external instantiation.
    private function __construct() {}

    /**
     * Returns a PDO connection. If it doesn't exist, it creates one.
     *
     * @return PDO
     */
    public static function getConnection()
    {
        if (self::$instance === null) {
            try {
                // Database configuration
                $host     = 'localhost';
                $dbname   = 'saudi_culture';
                $username = 'root';         // Change as necessary
                $password = '';             // Change as necessary
                $dsn      = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";


                // Create a new PDO instance.
                self::$instance = new PDO($dsn, $username, $password);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                // If there is an error, display it and exit.
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
