<?php
namespace models;
use PDO;

//Classe pour des cours d'une école
class Cours{
    public int $id;                  //< Clée primaire
    public string $numero;            //< Numéro du cours
    public string $nom;               //< Nom du cours
    public string $description;       //< Description du cours
    public ?int $idDepartement;        //< Clée étrangère du département
    private string $createdBy;        //< Créateur de la donnée
    private ?string $modifiedBy;       //< Dernier utilisateur à avoir modifié la donnée
    //public Departement $departement;  //< Département du cours
    //Constructeur
    public function __construct(int $id, string $numero, string $nom, string $description, ?int $idDepartement,string $createdBy, ?string $modifiedBy) {
        $this->id = $id;
        $this->numero = $numero;
        $this->nom = $nom;
        $this->description = $description;
        $this->idDepartement = $idDepartement;
        $this->createdBy = $createdBy;
        $this->modifiedBy = $modifiedBy;
    }
    
   public static function get($id) {
        $stmt = DatabaseConnexion::getInstance()->prepare('
            SELECT id, numero, nom, description, idDepartement, createdBy, modifiedBy
            FROM Cours
            WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        if ($stmt->rowCount() > 0) {
            return new Cours(
                $stmt->fetch(\PDO::FETCH_ASSOC)['id'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['numero'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['nom'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['description'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['idDepartement'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['createdBy'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['modifiedBy']
            );
        }
        return false;
    }
public static function getAll($page = null, $searchValue = '') {
    $cours = array();
    $ValuePerPage = 25;
    $pdo = DatabaseConnexion::getInstance();

    $searchValue = '%' . $searchValue . '%';

    if ($page === null) {
        // Pas de pagination : on récupère tous les résultats
        $stmt = $pdo->prepare('
            SELECT id, numero, nom, description, idDepartement, createdBy, modifiedBy
            FROM Cours
            WHERE nom LIKE :searchValue OR numero LIKE :searchValue
        ');
    } else {
        // Pagination active
        $pageStart = ($page - 1) * $ValuePerPage;
        $stmt = $pdo->prepare('
            SELECT id, numero, nom, description, idDepartement, createdBy, modifiedBy
            FROM Cours
            WHERE nom LIKE :searchValue OR numero LIKE :searchValue
            LIMIT ' . (int)$pageStart . ', ' . (int)$ValuePerPage
        );
    }

    $stmt->bindValue(':searchValue', $searchValue, \PDO::PARAM_STR);
    $stmt->execute();

    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $cours[] = new Cours(
            $row['id'],
            $row['numero'],
            $row['nom'],
            $row['description'],
            $row['idDepartement'],
            $row['createdBy'],
            $row['modifiedBy']
        );
    }

    return !empty($cours) ? $cours : false;
}

    public static function update(int $id, string $numero, string $nom, string $description, ?int $idDepartement, string $modifiedBy) {
        $stmt = DatabaseConnexion::getInstance()->prepare('
            UPDATE Cours
            SET numero = :numero, nom = :nom, description = :description, idDepartement = :idDepartement, modifiedBy = :modifiedBy
            WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'numero' => $numero,
            'nom' => $nom,
            'description' => $description,
            'idDepartement' => $idDepartement,
            'modifiedBy' => $modifiedBy
        ]);
        return $stmt->rowCount() > 0;
    }
    public static function add($nom, $numero, $description, $idDepartement, $createdBy) {
        $stmt = DatabaseConnexion::getInstance()->prepare('
            INSERT INTO Cours (numero, nom, description, idDepartement, createdBy)
            VALUES (:numero, :nom, :description, :idDepartement, :createdBy)'
        );
        $stmt->execute([
            'numero' => $numero,
            'nom' => $nom,
            'description' => $description,
            'idDepartement' => $idDepartement,
            'createdBy' => $createdBy
        ]);
        return $stmt->rowCount() > 0;
    }
    public static function delete($id) {
        $stmt = DatabaseConnexion::getInstance()->prepare('
            DELETE FROM Cours
            WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
