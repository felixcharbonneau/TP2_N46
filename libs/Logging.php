<?php
namespace libs;
use models\DatabaseConnexion;

class Logging {
    public static function log($message) {
        $db = DatabaseConnexion::getInstance();
        $stmt = $db->prepare('INSERT INTO logs (message, created_at) VALUES (:message, NOW())');
        $stmt->execute(['message' => $message]);
    }
}