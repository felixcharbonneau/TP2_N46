<?php
namespace models;
use models\DatabaseConnexion;
use libs\Security;

/**
 * Classe pour l'admininistrateur du système
 */
class Admin{
    public $email;//< email de l'admin

    /**
     * Constructeur de la classe Admin
     * @param string $email l'email de l'admin
     */
    public function __construct(string $email) {
        $this->email = $email;
    }
    /**
     * Sélectionne un admin par son email
     * @param string $email l'email de l'admin
     * @return Admin|false l'admin trouvé ou false si aucun admin n'est trouvé
     */
    public static function selectByEmail($email) {
        $stmt = DatabaseConnexion::getInstance()->prepare('SELECT email FROM Admin WHERE email = :email');
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() > 0) {
            return new Admin($stmt->fetch(\PDO::FETCH_ASSOC)['email']);
        }
        return false;
    }
    /**
     * Vérifie si le mot de passe est correct pour l'admin
     * @param string $password le mot de passe à vérifier
     * @return bool true si le mot de passe est correct, false sinon
     */
    public function connexion($password) {
        $stmt = DatabaseConnexion::getInstance()->prepare('SELECT password,salt FROM Admin WHERE email = :email');
        $stmt->bindValue(':email', $this->email);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $hashedPassword = $result['password'];
            $salt = $result['salt'];
            return Security::verifyPassword($password, $salt, $hashedPassword);
        }
        return false;
    }
}