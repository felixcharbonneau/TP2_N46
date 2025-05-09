<?php
namespace models;

class Department{
    public int $id;//<l'identifiant du département
    public string $nom;//<le nom du département
    public string $code;//<le code du département
    public string $description;//<la description du département
    private string $createdBy;//<l'utilisateur ayant créé le département
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
            WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        if ($stmt->rowCount() > 0) {
            return new Department(
                $stmt->fetch(\PDO::FETCH_ASSOC)['id'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['nom'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['code'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['description'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['createdBy'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['modifiedBy']
            );
        }
        return false;
    }
    /**
     * Sélectionne tous les départements
     * @param string $query la requête de recherche
     * @return array|false un tableau de départements ou false si non trouvé
     */
    public static function getAll($page, $searchValue = '') {
        $departments = array();
        $ValuePerPage = 25;
        $pageStart = ($page - 1) * 25;

        if(!$searchValue){
            $stmt = DatabaseConnexion::getInstance()->prepare('SELECT id, nom, code, description, createdBy, modifiedBy FROM Departement LIMIT :pageStart, :ValuePerPage');
        }else{
            $stmt = DatabaseConnexion::getInstance()->prepare('SELECT id, nom, code, description, createdBy, modifiedBy FROM Departement WHERE nom LIKE :query OR code LIKE :query LiMIT :pageStart, :ValuePerPage');
            $stmt->bindValue(':query', '%' . $searchValue . '%', \PDO::PARAM_STR);
        }
        $stmt->bindValue(':pageStart', $pageStart, \PDO::PARAM_INT);
        $stmt->bindValue(':ValuePerPage', $ValuePerPage, \PDO::PARAM_INT);
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
        return $departments;
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



}