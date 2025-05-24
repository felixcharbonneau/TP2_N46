<?php
namespace models;
use PDO;
use models\Student;
use models\Teachers;
/**
 * Classe pour des groupes d'étudiants
 * @package models
 */
class Classes{
    public int $id;//<l'identifiant du groupe
    public int $numero;//<le numéro du groupe
    public string $nom;//<le nom du groupe
    public $description;//<la description du groupe
    public int $coursID;//<l'identifiant du cours
    public ?int $enseignantID;//<l'identifiant de l'enseignant
    public $etudiants;//<les étudiants du groupe
    public string $createdBy;//<l'utilisateur ayant créé le groupe
    public ?string $modifiedBy;//<l'utilisateur ayant modifié le groupe
    /**
     * Constructeur
     * @param int $id l'identifiant du groupe
     * @param int $numero le numéro du groupe
     * @param string $nom le nom du groupe
     * @param string $description la description du groupe
     * @param int $coursID l'identifiant du cours
     * @param ?int $enseignantID l'identifiant de l'enseignant
     * @param string $createdBy l'utilisateur ayant créé le groupe
     * @param string $modifiedBy l'utilisateur ayant modifié le groupe
     * @param array $etudiants les étudiants du groupe
     */
    public function __construct($id, $numero, $nom, $description, $coursID, $enseignantID, $createdBy, $modifiedBy){
        $this->id = $id;
        $this->numero = $numero;
        $this->nom = $nom;
        $this->description = $description;
        $this->coursID = $coursID;
        $this->enseignantID = $enseignantID;
        $this->createdBy = $createdBy;
        $this->modifiedBy = $modifiedBy ?? '';
    }
    /**
     * Selectionne un groupe par son identifiant
     * @param int $id l'identifiant du groupe
     */
    public static function get($id) {
        $stmt = DatabaseConnexion::getInstance()->prepare('
            SELECT id, numero, nom, description,idCours, idEnseignant, createdBy, modifiedBy
            FROM Groupe
            WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        if ($stmt->rowCount() > 0) {
            return new Classes(
                $stmt->fetch(\PDO::FETCH_ASSOC)['id'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['numero'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['nom'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['description'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['idCours'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['idEnseignant'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['createdBy'],
                $stmt->fetch(\PDO::FETCH_ASSOC)['modifiedBy']
            );
        }
        return false;
    }
    /**
     * Sélectionne tous les groupes
     * @param int $page le numéro de la page
     * @param string $searchValue la valeur de recherche
     * @return array un tableau d'objets Classes
     */
    public static function getAll($page, $searchValue = '') {
        $classes = array();
        $ValuePerPage = 25;
        $pageStart = ($page - 1) * 25;
        $stmt = DatabaseConnexion::getInstance()->prepare('
            SELECT id, numero, nom, description, idCours, idEnseignant, createdBy, modifiedBy
            FROM Groupe
            WHERE nom LIKE :searchValue OR numero LIKE :searchValue
            LIMIT :pageStart, :ValuePerPage'
        );
        $stmt->bindValue(':searchValue', '%' . $searchValue . '%', \PDO::PARAM_STR);
        $stmt->bindValue(':pageStart', $pageStart, \PDO::PARAM_INT);
        $stmt->bindValue(':ValuePerPage', $ValuePerPage, \PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $classes[] = new Classes(
                    $row['id'],
                    $row['numero'],
                    $row['nom'],
                    $row['description'],
                    $row['idCours'],
                    $row['idEnseignant'],
                    $row['createdBy'],
                    $row['modifiedBy']
                );
            }
        }
        return $classes;
    }
    /**
     * Sélectionne tous les groupes d'un enseignant
     * @param int $enseignantID l'identifiant de l'enseignant
     */
    public static function delete($id){
        $stmt = DatabaseConnexion::getInstance()->prepare('
            DELETE FROM Groupe
            WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
    /**
     * Ajoute un groupe
     * @param int $numero le numéro du groupe
     * @param string $nom le nom du groupe
     * @param string $description la description du groupe
     * @param int $coursID l'identifiant du cours
     * @param int $enseignantID l'identifiant de l'enseignant
     * @param string $createdBy l'utilisateur ayant créé le groupe
     */
    public static function add($numero, $nom, $description, $coursID, $enseignantID, $createdBy){
        $stmt = DatabaseConnexion::getInstance()->prepare('
            INSERT INTO Groupe (numero, nom, description, idCours, idEnseignant, createdBy)
            VALUES (:numero, :nom, :description, :idCours, :idEnseignant, :createdBy)'
        );
        $stmt->execute([
            'numero' => $numero,
            'nom' => $nom,
            'description' => $description,
            'idCours' => $coursID,
            'idEnseignant' => $enseignantID,
            'createdBy' => $createdBy
        ]);
        return $stmt->rowCount() > 0;
    }
    /**
     * Met à jour un groupe
     * @param int $id l'identifiant du groupe
     * @param int $numero le numéro du groupe
     * @param string $nom le nom du groupe
     * @param string $description la description du groupe
     * @param int $coursID l'identifiant du cours
     * @param int $enseignantID l'identifiant de l'enseignant
     * @param string $modifiedBy l'utilisateur ayant modifié le groupe
     * @return bool true si la mise à jour a réussi, sinon false
     */
    public static function update($id, $numero, $nom, $description, $coursID, $enseignantID, $modifiedBy){
        $stmt = DatabaseConnexion::getInstance()->prepare('
            UPDATE Groupe
            SET numero = :numero, nom = :nom, description = :description, idCours = :idCours, idEnseignant = :idEnseignant, modifiedBy = :modifiedBy
            WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'numero' => $numero,
            'nom' => $nom,
            'description' => $description,
            'idCours' => $coursID,
            'idEnseignant' => $enseignantID,
            'modifiedBy' => $modifiedBy
        ]);
        return $stmt->rowCount() > 0;
    }
    public static function getAllFromTeacher($teacherID){
        $stmt = DatabaseConnexion::getInstance()->prepare('SELECT id,numero,nom,description,idCours,idEnseignant,createdBy,modifiedBy
        FROM Groupe WHERE idEnseignant = :teacherId');
        $stmt->bindValue(':teacherId', $teacherID, \PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $classes[] = new Classes(
                    $row['id'],
                    $row['numero'],
                    $row['nom'],
                    $row['description'],
                    $row['idCours'],
                    $row['idEnseignant'],
                    $row['createdBy'],
                    $row['modifiedBy']
                );
            }
            return $classes;
        }
        return false;
    }

}