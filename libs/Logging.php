<?php
namespace libs;
use models\DatabaseConnexion;

class Logging {
    public static function log($message, $ip = null) {
        if ($ip === null) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        }
        $createdBy = 'unknown';
        if (session_status() === PHP_SESSION_ACTIVE) {
            if (!empty($_SESSION['user_email'])) {
                $createdBy = $_SESSION['user_email'];
            }
        }
        $db = DatabaseConnexion::getInstance();
        $stmt = $db->prepare('INSERT INTO Log (message, ip, createdBy, createdOn) VALUES (:message, :ip, :createdBy, NOW())');
        $stmt->execute([
            'message' => $message,
            'ip' => $ip,
            'createdBy' => $createdBy,
        ]);
    }


}