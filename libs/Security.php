<?php
namespace libs;
use models\DatabaseConnexion;

define('CSRF_TOKEN_NAME', 'csrf_token');

class Security {
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    public static function verifyPassword($passw, $salt,$storedHash) {
        $config = require CONFIG_PATH . 'database.php';
        $pepper = $config['pepper'];
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

}