<?php
namespace models;
use PDO;
/**
 * Classe de connexion à la base de données
 */
class DatabaseConnexion{
    public static $instance = null;//< instance de la connexion
    private $pdo;//< objet pdo

    /**
     * Constructeur
     */
    public function __construct(){
        $config = require 'config/database.php';
        try {
            $this->pdo = new \PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']};port=3306",
                $config['username'],
                $config['password'],
                [\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true, \PDO::ATTR_PERSISTENT => false]
            );
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    /**
     * Renvoie l'instance de la connexion à la base de données
     * @return PDO l'instance de la connexion à la base de données
     */
    public static function getInstance(){
        if (self::$instance === null) {
            self::$instance = new DatabaseConnexion();
        }
        return self::$instance->pdo; 
    }
}
