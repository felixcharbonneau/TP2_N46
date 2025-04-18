<?php
namespace models;
use models\DatabaseConnexion;

/**
 * Classe représentant un étudiant
 */
class Student extends User {
    public string $da;//<le numéro d'admission de l'étudiant
    public string $dateInscription;//<la date d'inscription de l'étudiant
    public function __construct(
        int $id,
        string $nom,
        string $prenom,
        string $email,
        string $dateNaissance,
        string $createdBy,
        ?string $modifiedBy = '',
        string $da = '',
        string $dateInscription = ''
    ) {
        parent::__construct($id, $nom, $prenom, $email, $dateNaissance, $createdBy, $modifiedBy);
        $this->da = $da;
        $this->dateInscription = $dateInscription;
    }
    /**
     * Sélectionne un étudiant par son Courriel
     * @param string $email l'email de l'étudiant
     */
    public static function selectByEmail($email) {
        $stmt = DatabaseConnexion::getInstance()->prepare('
            SELECT id, nom, prenom, email, dateNaissance, createdBy, modifiedBy, da, dateInscription
            FROM Etudiant
            WHERE email = :email'
        );
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() > 0) {
            return new Student(
                $stmt->fetch(\PDO::FETCH_ASSOC)['id'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['nom'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['prenom'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['email'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['dateNaissance'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['createdBy'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['modifiedBy'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['da'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['dateInscription']
            );
        }
        return false;
    }

    public function connexion($password) {
        $stmt = DatabaseConnexion::getInstance()->prepare('SELECT password FROM Etudiant WHERE email = :email');
        $stmt->bindValue(':email', $this->email);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $hashedPassword = $stmt->fetch(\PDO::FETCH_ASSOC)['password'];
            return password_verify($password, $hashedPassword);
        }
        return false;
    }
    /**
     * Récupere tous les étudiants
     * @return array un tableau d'objets Student
     */
    public static function getAll($searchValue = '') {
        $students = array();
        if(!$searchValue){
            $query = DatabaseConnexion::getInstance()->query('SELECT id, nom, prenom, email, dateNaissance, createdBy, modifiedBy, da, dateInscription FROM Etudiant');
        }else{
            $query = DatabaseConnexion::getInstance()->prepare('SELECT id, nom, prenom, email, dateNaissance, createdBy, modifiedBy, da, dateInscription FROM Etudiant WHERE nom LIKE :searchValue OR prenom LIKE :searchValue OR da LIKE :searchValue OR email LIKE :searchValue');
            $query->bindValue(':searchValue', '%' . $searchValue . '%', \PDO::PARAM_STR);
            $query->execute();
        }
        if ($query && $query->rowCount() > 0) {
            while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
                $students[] = new Student(
                    $row['id'],
                    $row['nom'],
                    $row['prenom'],
                    $row['email'],
                    $row['dateNaissance'],
                    $row['createdBy'],
                    $row['modifiedBy'],
                    $row['da'],
                    $row['dateInscription']
                );
            }
        }
    
        return $students;
    }
    public static function get($id){
        $stmt = DatabaseConnexion::getInstance()->prepare('SELECT id, nom, prenom, email, dateNaissance, createdBy, modifiedBy, da, dateInscription FROM Etudiant WHERE id = :id');
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            return "Etudiant non trouvé";
        }
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return new Student(
            $data['id'],
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['dateNaissance'],
            $data['createdBy'],
            $data['modifiedBy'],
            $data['da'],
            $data['dateInscription']
        );
    }
    public static function delete($id) {
        $stmt = DatabaseConnexion::getInstance()->prepare('DELETE FROM Etudiant WHERE id = :id');
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function create($data){
        $da = self::generateUniqueDa(DatabaseConnexion::getInstance());
        $email = $da . "@etu.cegep-lanaudiere.qc.ca";
        $date = new \DateTime($data['dateNaissance']);  
        $formatted = $date->format('Y-m-d');    
        $password = password_hash($date->format('Y') . 
                                  $date->format('m') . 
                                  $date->format('d'), PASSWORD_DEFAULT);
        $stmt = DatabaseConnexion::getInstance()->prepare('
            INSERT INTO Etudiant (nom, prenom, email, dateNaissance, createdBy, modifiedBy, da, dateInscription,password)
            VALUES (:nom, :prenom, :email, :dateNaissance, :createdBy, NULL, :da, :dateInscription, :password)'
        );
        $stmt->bindValue(':nom', $data['nom']);
        $stmt->bindValue(':prenom', $data['prenom']);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':dateNaissance', $data['dateNaissance']);
        $stmt->bindValue(':da', $da);
        $stmt->bindValue(':createdBy', "system"); ///a modifier
        $stmt->bindValue(':dateInscription', date('Y-m-d H:i:s'));
        $stmt->bindValue(':password', $password);

        error_log("Nom: " . $data['nom']);
        error_log("Prénom: " . $data['prenom']);
        error_log("Email: " . $email);
        error_log("DA: " . $da);
        error_log("Date de naissance: " . $data['dateNaissance']);

        $stmt->execute();
        $id = DatabaseConnexion::getInstance()->lastInsertId();
        if (!$id) {
            return false;
        }
        return new Student(
            $id,
            $data['nom'],
            $data['prenom'],
            $email,
            $data['dateNaissance'],
            "system", //a modifier
            null,
            $da,
            date('Y-m-d H:i:s')
        );
    }

    /**
     * génere un nombre entre 1000000 et 9999999
     */
    public static function generateDa($length = 7) {
        $min = pow(10, $length - 1); 
        $max = pow(10, $length) - 1; 
        return rand($min, $max);
    }
    /**
     * Générer un numéro d'admission unique pour l'étudiant
     */
    public static function generateUniqueDa($pdo) {
        do {
            $randomNumber = self::generateDa();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Etudiant WHERE DA = ?");
            $stmt->execute([$randomNumber]);
            $count = $stmt->fetchColumn();
        } while ($count > 0);
    
        return (string)$randomNumber;
    }
    
}