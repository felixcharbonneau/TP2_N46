<?php
namespace models;

class Department{
    public int $id;//<l'identifiant du département
    public string $nom;//<le nom du département
    public string $code;//<le code du département
    public string $description;//<la description du département
    private ?string $createdBy;//<l'utilisateur ayant créé le département
    private ?string $modifiedBy;
    /**
     * Constructeur
     * @param int $id l'identifiant du département
     * @param string $nom le nom du département
     * @param string $code le code du département
     * @param string $description la description du département
     * @param string $createdBy l'utilisateur ayant créé le département
     * @param string $modifiedBy l'utilisateur ayant modifié le département
     */
    public function __construct($id, $nom, $code, $description, $createdBy, $modifiedBy) {
        $this->id = $id;
        $this->nom = $nom;
        $this->code = $code;
        $this->description = $description;
        $this->createdBy = $createdBy;
        $this->modifiedBy = $modifiedBy;
    }
    /**
     * Sélectionne un département par son identifiant
     * @param int $id l'identifiant du département
     * @return Department|false le département ou false si non trouvé
     */
    public static function get($id) {
        $stmt = DatabaseConnexion::getInstance()->prepare('
        SELECT id, nom, code, description, createdBy, modifiedBy
        FROM Departement
        WHERE id = :id
    ');
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC); // On récupère UNE seule fois la ligne
            return new Department(
                $row['id'],
                $row['nom'],
                $row['code'],
                $row['description'],
                $row['createdBy'],
                $row['modifiedBy']
            );
        }
        return false;
    }

public static function getAll($page = null, $searchValue = '') {
    $departments = array();
    $ValuePerPage = 25;

    // Cas où on veut tout récupérer (page null ou 0)
    $noPagination = is_null($page) || $page === 0;

    if ($noPagination) {
        if (!$searchValue) {
            $stmt = DatabaseConnexion::getInstance()->prepare(
                'SELECT id, nom, code, description, createdBy, modifiedBy FROM Departement'
            );
        } else {
            $stmt = DatabaseConnexion::getInstance()->prepare(
                'SELECT id, nom, code, description, createdBy, modifiedBy FROM Departement 
                 WHERE nom LIKE :query OR code LIKE :query'
            );
            $stmt->bindValue(':query', '%' . $searchValue . '%', \PDO::PARAM_STR);
        }
    } else {
        $pageStart = ($page - 1) * $ValuePerPage;
        if (!$searchValue) {
            $stmt = DatabaseConnexion::getInstance()->prepare(
                'SELECT id, nom, code, description, createdBy, modifiedBy 
                 FROM Departement 
                 LIMIT :pageStart, :ValuePerPage'
            );
        } else {
            $stmt = DatabaseConnexion::getInstance()->prepare(
                'SELECT id, nom, code, description, createdBy, modifiedBy 
                 FROM Departement 
                 WHERE nom LIKE :query OR code LIKE :query 
                 LIMIT :pageStart, :ValuePerPage'
            );
            $stmt->bindValue(':query', '%' . $searchValue . '%', \PDO::PARAM_STR);
        }
        $stmt->bindValue(':pageStart', $pageStart, \PDO::PARAM_INT);
        $stmt->bindValue(':ValuePerPage', $ValuePerPage, \PDO::PARAM_INT);
    }

    $stmt->execute();
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $departments[] = new Department(
            $row['id'],
            $row['nom'],
            $row['code'],
            $row['description'],
            $row['createdBy'],
            $row['modifiedBy']
        );
    }

    // En cas de noPagination, inutile de renvoyer pagination info
    if ($noPagination) {
        return ['departments' => $departments];
    }

    $count = self::getTotal($searchValue);
    $nbPage = ceil($count / $ValuePerPage);
    $nbPage = max($nbPage, 1); // minimum 1
    $page = min(max($page, 1), $nbPage); // page >= 1 et <= nbPage

    return [
        'departments' => $departments,
        'total' => $count,
        'page' => $page,
        'perPage' => $ValuePerPage,
        'nbPage' => $nbPage
    ];
}

    public static function create($nom, $code, $description, $createdBy) {
        $stmt = DatabaseConnexion::getInstance()->prepare('INSERT INTO Departement (nom, code, description, createdBy) VALUES (:nom, :code, :description, :createdBy)');
        $stmt->execute([
            'nom' => $nom,
            'code' => $code,
            'description' => $description,
            'createdBy' => $createdBy
        ]);
        return self::get(DatabaseConnexion::getInstance()->lastInsertId()); 
    }
    public static function getTotal($searchValue = '') {
        if ($searchValue) {
            $stmt = DatabaseConnexion::getInstance()->prepare('SELECT COUNT(*) FROM Departement WHERE nom LIKE :query OR code LIKE :query');
            $stmt->bindValue(':query', '%' . $searchValue . '%', \PDO::PARAM_STR);
        } else {
            $stmt = DatabaseConnexion::getInstance()->prepare('SELECT COUNT(*) FROM Departement');
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function delete(int $id): bool
    {
        $stmt = DatabaseConnexion::getInstance()->prepare('DELETE FROM Departement WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
    public static function update($id, $data) {
        $pdo = DatabaseConnexion::getInstance();
        $sql = "UPDATE Departement SET nom = :nom, code = :code, description = :description WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':nom' => $data['nom'] ?? null,
            ':code' => $data['code'] ?? null,
            ':description' => $data['description'] ?? null,
            ':id' => $id,
        ]);

        return $result; // true ou false
    }


}