<?php
namespace models;
use PDO;

class DatabaseConnexion{
    public static $instance = null;
    private $pdo; // PDO object

    public function __construct(){
        $config = require CONFIG_PATH . 'database.php';
        try {
            // Use the global PDO class (without the 'models' namespace)
            $this->pdo = new \PDO("mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}", $config['username'], $config['password']);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(){
        if (self::$instance === null) {
            self::$instance = new DatabaseConnexion();
        }
        return self::$instance->pdo; // Return the PDO object directly
    }
}
