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

    /**
     * Obtention d'un cours
     * @param $id du cours a obtenir
     * @return false|Cours
     */
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

    /**
     * Obtention de tous les cours
     * @param $page a prendre
     * @param $searchValue valeur de recherche
     * @return array|false
     */
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

    /**
     * Mise a jour d'un cours
     * @param int $id du cours a modifier
     * @param string $numero nouveau numero
     * @param string $nom nouveau nom
     * @param string $description nouvelle description
     * @param int|null $idDepartement id du nouveau département
     * @param string $modifiedBy responsable de la modification
     * @return bool
     */
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

    /**
     * Ajout d'un nouveau cours
     * @param $nom du nouveau cours
     * @param $numero du nouveau cours
     * @param $description du nouveau cours
     * @param $idDepartement du nouveau cours
     * @param $createdBy créateur du nouveau cours
     * @return bool
     */
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

    /**
     * Suppression d'un cours
     * @param $id du cours a supprimer
     * @return bool succes de la requete
     */
    public static function delete($id) {
        $stmt = DatabaseConnexion::getInstance()->prepare('
            DELETE FROM Cours
            WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
