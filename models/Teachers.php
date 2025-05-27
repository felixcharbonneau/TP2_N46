<?php
namespace models;
use models\DatabaseConnexion;
use libs\Security;
use PDO;

/**
 * Classe représentant un enseignant
 */
class Teachers extends User {
    public string $dateEmbauche;//<la date d'embauche de l'enseignant
    public ?int $idDepartement;//< id du département de l'enseignant

    public function __construct(
        int $id,
        string $nom,
        string $prenom,
        string $dateNaissance,
        string $email,
        string $dateEmbauche,
        string $createdBy = 'system',
        ?int $idDepartement = null,
        ?string $modifiedBy = ''
    ) {
        parent::__construct($id, $nom, $prenom, $email, $dateNaissance, $createdBy, $modifiedBy);
        $this->dateEmbauche = $dateEmbauche;
        $this->idDepartement = $idDepartement;
    }

    /**
     * Sélectionne un étudiant par son Courriel
     * @param string $email l'email de l'étudiant
     */
public static function selectByEmail($email) {
    $stmt = DatabaseConnexion::getInstance()->prepare('
        SELECT id, nom, prenom, email, dateNaissance, createdBy, modifiedBy, dateEmbauche, idDepartement
        FROM Enseignant
        WHERE email = :email'
    );
    $stmt->execute(['email' => $email]);

    // Check if a row was found
    if ($stmt->rowCount() > 0) {
        $result = $stmt->fetch(\PDO::FETCH_ASSOC); // Fetch the result once

        // Ensure the correct assignment of variables
        return new Teachers(
            $result['id'], // id
            $result['nom'], // nom
            $result['prenom'], // prenom
            $result['dateNaissance'], // dateNaissance
            $result['email'], // email
            $result['dateEmbauche'], // dateEmbauche
            $result['createdBy'],
            $result['idDepartement'] ? (int) $result['idDepartement'] : null ,
            $result['modifiedBy'] // modifiedBy

        );
    }

    return false; // Return false if no teacher is found
}



    /**
     * Vérifie les informations de connexion de l'enseignant
     * @param string $password Le mot de passe de l'enseignant
     * @return bool true si les informations de connexion sont valides, sinon false
     */
    public function connexion($password) {
        $stmt = DatabaseConnexion::getInstance()->prepare('SELECT password, salt FROM Enseignant WHERE email = :email');
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
     * Obtention de tous les enseignants
     * @param $page des enseignants a rechercher
     * @param $searchValue valeur de recherche
     * @return array
     */
public static function getAll($page = null, $searchValue = '')
{
    $teachers = array();
    $ValuePerPage = 25; // Number of results per page
    $params = [];

    // Base query to fetch all teachers
    $baseQuery = 'SELECT id, nom, prenom, email, dateNaissance, createdBy, modifiedBy, dateEmbauche, idDepartement FROM Enseignant';

    // If search value is provided, add the WHERE clause
    if (!empty($searchValue)) {
        $baseQuery .= ' WHERE nom LIKE :searchValue OR prenom LIKE :searchValue OR email LIKE :searchValue';
        $params[':searchValue'] = '%' . $searchValue . '%';
    }

    // Only apply pagination if the page is provided
    if (!is_null($page)) {
        $pageStart = ($page - 1) * $ValuePerPage;
        $baseQuery .= ' LIMIT :pageStart, :ValuePerPage';
    }

    // Prepare the query
    $query = DatabaseConnexion::getInstance()->prepare($baseQuery);

    // Bind parameters for search
    foreach ($params as $key => $value) {
        $query->bindValue($key, $value, \PDO::PARAM_STR);
    }

    // Bind pagination parameters
    if (!is_null($page)) {
        $query->bindValue(':pageStart', $pageStart, \PDO::PARAM_INT);
        $query->bindValue(':ValuePerPage', $ValuePerPage, \PDO::PARAM_INT);
    }

    // Execute the query
    $query->execute();

    // Fetch the results and create Teacher objects
    while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
        $teachers[] = new Teachers(
            $row['id'],
            $row['nom'],
            $row['prenom'],
            $row['dateNaissance'],
            $row['email'],
            $row['dateEmbauche'],
            $row['createdBy'],
            isset($row['idDepartement']) ? (int)$row['idDepartement'] : null,
            $row['modifiedBy'] ?? ''
        );
    }

    // If no page is set, return the teachers array without pagination info
    if (is_null($page)) {
        return $teachers;
    }

    // Get the total number of teachers for pagination
    $count = self::getTotal($searchValue);
    $nbPage = max(1, ceil($count / $ValuePerPage)); // Calculate total number of pages
    $page = max(1, min($page, $nbPage)); // Ensure current page is within valid range

    // Return data with pagination information
    return [
        'teachers' => $teachers,
        'total' => $count,
        'page' => $page,
        'perPage' => $ValuePerPage,
        'nbPage' => $nbPage
    ];
}

    /**
     * OBtention d'un seul enseignant
     * @param $id de l'enseignant a obtenir
     * @return false|Teachers
     */
    public static function get($id) {
        $stmt = DatabaseConnexion::getInstance()->prepare('
        SELECT id, nom, prenom, dateNaissance, email, dateEmbauche, createdBy, modifiedBy, idDepartement
        FROM Enseignant
        WHERE id = :id
    ');
        $stmt->execute(['id' => $id]);
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return new Teachers(
                $row['id'],
                $row['nom'],
                $row['prenom'],
                $row['email'],          // email avant dateNaissance
                $row['dateNaissance'],
                $row['dateEmbauche'],
                $row['createdBy'] ?? 'system',
                isset($row['idDepartement']) ? (int)$row['idDepartement'] : null,
                $row['modifiedBy'] ?? ''
            );
        }
        return false;
    }

    /**
     * Ajout d'un enseignant
     * @param $nom du nouvel enseignant
     * @param $prenom du nouvel enseignant
     * @param $dateNaissance du nouvel enseignant
     * @param $dateEmbauche du nouvel enseignant
     * @param $createdBy du nouvel enseignant
     * @param $idDepartement du nouvel enseignant
     * @return bool|void
     * @throws \Random\RandomException
     */
    public static function add($nom, $prenom, $dateNaissance, $dateEmbauche, $createdBy, $idDepartement = null) {
        try {
            $pdo = DatabaseConnexion::getInstance();

            // Generate base email
            $baseEmail = strtolower($nom . '.' . $prenom . '@cegep-lanaudiere.qc.ca');
            $email = $baseEmail;
            $suffix = 1;

            $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM Enseignant WHERE email = :email');
            do {
                $checkStmt->bindValue(':email', $email);
                $checkStmt->execute();
                if ($checkStmt->fetchColumn() > 0) {
                    $email = strtolower($nom . '.' . $prenom . $suffix . '@cegep-lanaudiere.qc.ca');
                    $suffix++;
                } else {
                    break;
                }
            } while (true);
            $stmt = $pdo->prepare('
            INSERT INTO Enseignant (nom, prenom, salt, dateNaissance, email, password, dateEmbauche, createdBy, idDepartement)
            VALUES (:nom, :prenom, :salt, :dateNaissance, :email, :password, :dateEmbauche, :createdBy, :idDepartement)'
            );

            $salt = bin2hex(random_bytes(8));
            $config = require CONFIG_PATH . 'database.php';
            $pepper = $config['pepper'];
            $hashed_password = password_hash("password" . $salt . $pepper, PASSWORD_BCRYPT);

            $stmt->bindValue(':nom', $nom);
            $stmt->bindValue(':prenom', $prenom);
            $stmt->bindValue(':salt', $salt);
            $stmt->bindValue(':dateNaissance', $dateNaissance);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':password', $hashed_password);
            $stmt->bindValue(':dateEmbauche', $dateEmbauche);
            $stmt->bindValue(':createdBy', $createdBy);
            $stmt->bindValue(':idDepartement', $idDepartement ?? null, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            // Log or display error
            error_log("PDO Error in Enseignant::add(): " . $e->getMessage());
            die("An error occurred while adding the teacher: " . $e->getMessage()); // For dev only — remove or disable in production
        }
    }

    /**
     * Mise a jour d'un enseignant
     * @param $id de l'enseignant mis a jours
     * @param $nom de l'enseignant mis a jours
     * @param $prenom de l'enseignant mis a jours
     * @param $dateNaissance de l'enseignant mis a jours
     * @param $modifiedBy de l'enseignant mis a jours
     * @param $idDepartement de l'enseignant mis a jours
     * @param $password de l'enseignant mis a jours
     * @return bool
     * @throws \Random\RandomException
     */
    public static function update($id, $nom, $prenom, $dateNaissance, $modifiedBy, $idDepartement, $password = null) {
        // Start building the query and params array
        $query = '
        UPDATE Enseignant
        SET nom = :nom, prenom = :prenom, dateNaissance = :dateNaissance, modifiedBy = :modifiedBy, idDepartement = :idDepartement';

        // Add password only if provided
        if ($password !== null)
        {
            $query .= ', password = :password, salt = :salt';

        }
        $query .= ' WHERE id = :id';

        $stmt = DatabaseConnexion::getInstance()->prepare($query);
        if ($password !== null) {
            $salt = bin2hex(random_bytes(8));
            $config = require CONFIG_PATH . 'database.php';
            $pepper = $config['pepper'];
            $hashed_password = password_hash($password.$salt.$pepper, PASSWORD_BCRYPT);

            $stmt->bindValue(':password', $hashed_password);
            $stmt->bindValue(':salt', $salt);
        }
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':nom', $nom);
        $stmt->bindValue(':prenom', $prenom);
        $stmt->bindValue(':dateNaissance', $dateNaissance);
        $stmt->bindValue(':modifiedBy', $modifiedBy);

        if ($idDepartement) {
            $stmt->bindValue(':idDepartement', (int)$idDepartement);
        } else {
            $stmt->bindValue(':idDepartement', null, PDO::PARAM_NULL);
        }


        return $stmt->execute();
    }

    /**
     * Suppression d'un enseignant
     * @param $id de l'enseignant a supprimer
     * @return bool
     */
    public static function delete($id) {
        $stmt = DatabaseConnexion::getInstance()->prepare('
            DELETE FROM Enseignant
            WHERE id = :id'
        );
        return $stmt->execute(['id' => (int)$id]);
    }
    public static function getTotal($searchValue = '')
{
    $params = [];
    $baseQuery = 'SELECT COUNT(id) FROM Enseignant';

    // Add WHERE clause if search value is provided
    if (!empty($searchValue)) {
        $baseQuery .= ' WHERE nom LIKE :searchValue OR prenom LIKE :searchValue OR email LIKE :searchValue';
        $params[':searchValue'] = '%' . $searchValue . '%';
    }

    // Prepare and execute the query to count total teachers
    $query = DatabaseConnexion::getInstance()->prepare($baseQuery);
    foreach ($params as $key => $value) {
        $query->bindValue($key, $value, \PDO::PARAM_STR);
    }

    $query->execute();
    return $query->fetchColumn(); // Return the total count of teachers
}


}