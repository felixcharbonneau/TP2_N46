<?php
namespace libs;
use models\DatabaseConnexion;

define('CSRF_TOKEN_NAME', 'csrf_token');

class Security {
    /**
     * Vérifier si l'utilisateur est connecté
     * @return bool True si l'utilisateur est connecté, sinon false
     */
    public static function isAuthenticated() {
        return isset($_SESSION['user_email']) && !empty($_SESSION['user_email']);
    }

    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    public static function verifyPassword($passw, $salt,$storedHash) {
        $config = require CONFIG_PATH . 'database.php';
        $pepper = '23jiasf98A?&S&*dsnj21ASUIDhui12';
        return password_verify($passw . $salt . $pepper, $storedHash);
    }

    public static function generateCSRFToken() {
        $token = self::generateToken();
        $_SESSION[CSRF_TOKEN_NAME] = $token;
        return $token;
    }

    public static function verifyCSRFToken($token, $constant) {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            return false;
        }
        
        $stored = $_SESSION[CSRF_TOKEN_NAME];
        $stored = hash('sha256', $stored . $constant);

        return hash_equals($stored, $token);
    }

    public static function generateApiToken() {
        return self::generateToken(64);
    }

    public static function validateApiToken($token) {
        $db = Database::getInstance();
        
        $sql = "SELECT a.*, u.* FROM ApiTokens a 
                JOIN utilisateurs u ON a.utilisateur_id = u.id 
                WHERE a.token = :token AND a.actif = 1 
                AND (a.date_expiration IS NULL OR a.date_expiration > NOW())";
        
        $result = $db->fetchOne($sql, ['token' => $token]);
        
        return $result ? $result : false;
    }

}