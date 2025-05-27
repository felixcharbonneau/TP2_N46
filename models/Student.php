<?php
namespace models;
use models\DatabaseConnexion;
use libs\Security;

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
        string $createdBy = 'system',
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

    // Check if any result is returned
    if ($stmt->rowCount() > 0) {
        // Fetch the result once
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Pass the fetched data to the Student constructor
        return new Student(
            $result['id'],
            $result['nom'],
            $result['prenom'],
            $result['email'],
            $result['dateNaissance'],
            $result['createdBy'],
            $result['modifiedBy'],
            $result['da'],
            $result['dateInscription']
        );
    }

    // Return false if no result is found
    return false;
}

    public static function getStudentsInGroups(array $groupIds): array
    {
        if (empty($groupIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($groupIds), '?'));

        $sql = "SELECT eg.idGroupe, e.id, e.nom, e.prenom, e.email
                FROM Etudiant e
                JOIN EtudiantGroupe eg ON e.id = eg.idEtudiant
                WHERE eg.idGroupe IN ($placeholders)";

        $pdo = DatabaseConnexion::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($groupIds);

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $studentsByGroup = [];
        foreach ($result as $row) {
            $groupId = $row['idGroupe'];
            if (!isset($studentsByGroup[$groupId])) {
                $studentsByGroup[$groupId] = [];
            }
            $studentsByGroup[$groupId][] = $row;
        }

        return $studentsByGroup;
    }

    public static function removeFromGroup(int $studentId, int $groupId): bool
    {
        $pdo = DatabaseConnexion::getInstance();
        $stmt = $pdo->prepare("DELETE FROM EtudiantGroupe WHERE idEtudiant = ? AND idGroupe = ?");
        return $stmt->execute([$studentId, $groupId]);
    }

    public static function addToGroup(int $studentId, int $groupId): bool
    {
        $pdo = DatabaseConnexion::getInstance();

        try {
            // Prepare the SQL statement
            $stmt = $pdo->prepare("INSERT INTO EtudiantGroupe (idEtudiant, idGroupe) VALUES (:studentId, :groupId)");

            // Bind parameters
            $stmt->bindParam(':studentId', $studentId, \PDO::PARAM_INT);
            $stmt->bindParam(':groupId', $groupId, \PDO::PARAM_INT);

            // Execute the statement and check if it was successful
            if ($stmt->execute()) {
                return true;
            } else {
                // Log an error message if execution fails
                error_log("Failed to add student (ID: $studentId) to group (ID: $groupId)");
                return false;
            }
        } catch (PDOException $e) {
            // Log any exceptions
            error_log("Error adding student to group: " . $e->getMessage());
            return false;
        }
    }





    /**
     * Vérifie les informations de connexion de l'étudiant
     * @param string $password Le mot de passe de l'étudiant
     * @return bool true si les informations de connexion sont valides, sinon false
     */
    public function connexion($password) {
        $stmt = DatabaseConnexion::getInstance()->prepare('SELECT password,salt FROM Etudiant WHERE email = :email');
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
/**
 * Récupère tous les étudiants
 * @param int|null $page La page à récupérer (null pour tout récupérer sans pagination)
 * @param string $searchValue La valeur de recherche facultative
 * @return array Un tableau d'objets Student et des métadonnées si pagination activée
 */
public static function getAll($page = null, $searchValue = '')
{
    $students = array();
    $ValuePerPage = 25;
    $params = [];

    $baseQuery = 'SELECT id, nom, prenom, email, dateNaissance, createdBy, modifiedBy, da, dateInscription FROM Etudiant';

    if (!empty($searchValue)) {
        $baseQuery .= ' WHERE nom LIKE :searchValue OR prenom LIKE :searchValue OR da LIKE :searchValue OR email LIKE :searchValue';
        $params[':searchValue'] = '%' . $searchValue . '%';
    }

    if (!is_null($page)) {
        $pageStart = ($page - 1) * $ValuePerPage;
        $baseQuery .= ' LIMIT :pageStart, :ValuePerPage';
    }

    $query = DatabaseConnexion::getInstance()->prepare($baseQuery);

    foreach ($params as $key => $value) {
        $query->bindValue($key, $value, \PDO::PARAM_STR);
    }

    if (!is_null($page)) {
        $query->bindValue(':pageStart', $pageStart, \PDO::PARAM_INT);
        $query->bindValue(':ValuePerPage', $ValuePerPage, \PDO::PARAM_INT);
    }

    $query->execute();

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

    if (is_null($page)) {
        return $students;
    }

    $count = self::getTotal($searchValue);
    $nbPage = max(1, ceil($count / $ValuePerPage));
    $page = max(1, min($page, $nbPage));

    return [
        'students' => $students,
        'total' => $count,
        'page' => $page,
        'perPage' => $ValuePerPage,
        'nbPage' => $nbPage
    ];
}

    /**
     * Récupère un étudiant par son ID
     * @param int $id L'ID de l'étudiant
     * @return Student|false L'objet Student ou false si l'étudiant n'existe pas
     * @throws \Exception Si une erreur se produit lors de la récupération de l'étudiant
     */
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
    /**
     * Supprime un étudiant par son ID
     * @param int $id L'ID de l'étudiant à supprimer
     * @return bool true si la suppression a réussi, false sinon
     */
    public static function delete($id) {
        $stmt = DatabaseConnexion::getInstance()->prepare('DELETE FROM Etudiant WHERE id = :id');
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
    /**
     * Crée un nouvel étudiant
     * @param array $data Les données de l'étudiant (nom, prenom, dateNaissance)
     * @return Student|false L'objet Student créé ou false en cas d'erreur
     * @throws \Exception Si une erreur se produit lors de la création de l'étudiant
     */
    public static function create($data){
        $da = self::generateUniqueDa(DatabaseConnexion::getInstance());
        $email = $da . "@etu.cegep-lanaudiere.qc.ca";
        $date = new \DateTime($data['dateNaissance']);  
        $formatted = $date->format('Y-m-d');
        $password = $date->format('Y') . $date->format('m') . $date->format('d'); // e.g. "20250205"

        $stmt = DatabaseConnexion::getInstance()->prepare('
            INSERT INTO Etudiant (nom, prenom, email, dateNaissance, createdBy, modifiedBy, da, dateInscription,password,salt)
            VALUES (:nom, :prenom, :email, :dateNaissance, :createdBy, NULL, :da, :dateInscription, :password,:salt)'
        );
        $stmt->bindValue(':nom', $data['nom']);
        $stmt->bindValue(':prenom', $data['prenom']);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':dateNaissance', $data['dateNaissance']);
        $stmt->bindValue(':da', $da);
        $stmt->bindValue(':createdBy', "system"); ///a modifier
        $stmt->bindValue(':dateInscription', date('Y-m-d H:i:s'));
        $salt = bin2hex(random_bytes(8));
        $config = require CONFIG_PATH . 'database.php';
        $pepper = $config['pepper'];
        $hashed_password = password_hash($password.$salt.$pepper, PASSWORD_BCRYPT);

        $stmt->bindValue(':password', $hashed_password);
        $stmt->bindValue(':salt', $salt);

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
     * Met à jour les informations d'un étudiant
     * @param int $id L'ID de l'étudiant à mettre à jour
     * @param array $data Les données à mettre à jour (nom, prenom, dateNaissance, password)
     * @return bool true si la mise à jour a réussi, false sinon
     */
    public static function update($id,$data) {
        $sql = '
        UPDATE Etudiant
        SET nom = :nom,
            prenom = :prenom,
            dateNaissance = :dateNaissance,
            modifiedBy = :modifiedBy';
        if (!empty($data['password'])) {
            $sql .= ', password = :password, salt = :salt';
        }
        $sql .= ' WHERE id = :id';

        $stmt = DatabaseConnexion::getInstance()->prepare($sql);
        $stmt->bindValue(':nom', $data['nom']);
        $stmt->bindValue(':prenom', $data['prenom']);
        $stmt->bindValue(':dateNaissance', $data['dateNaissance']);
        $stmt->bindValue(':modifiedBy', "system"); ///a modifier
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        if (!empty($data['password'])) {
            $salt = bin2hex(random_bytes(8));
            $config = require CONFIG_PATH . 'database.php';
            $pepper = $config['pepper'];
            $hashed_password = password_hash($data['password'].$salt.$pepper, PASSWORD_BCRYPT);

            $stmt->bindValue(':password', $hashed_password);
            $stmt->bindValue(':salt', $salt);
        }

        return $stmt->execute();
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

    /**
     * Nombre total d'étudiants
     * @param $searchValue valeur de recherche
     * @return mixed
     */
    public static function getTotal($searchValue = '') {
        if(!$searchValue){
            $stmt = DatabaseConnexion::getInstance()->prepare('SELECT COUNT(*) FROM Etudiant');
        }else{
            $stmt = DatabaseConnexion::getInstance()->prepare('SELECT COUNT(*) FROM Etudiant WHERE nom LIKE :searchValue OR prenom LIKE :searchValue OR da LIKE :searchValue OR email LIKE :searchValue');
            $stmt->bindValue(':searchValue', '%' . $searchValue . '%', \PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
}