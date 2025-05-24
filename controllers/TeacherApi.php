<?php
namespace controllers;
use models\Teachers;

class TeacherApi {

    public function __construct() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type');
    }

    /**
     * Récupère tous les Enseignants
     * @return string JSON contenant la liste des enseignants
     */
    public function getTeachers($page = 1) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $query = isset($_GET['query']) ? $_GET['query'] : '';
        if($query) {
            $teachers = Teachers::getAll($page, $query);
        } else {
            $teachers = Teachers::getAll($page);
        }
        return json_encode($teachers);
    }

    /**
     * Récupère un Enseignant par son ID
     * @param int $id L'ID de l'enseignant
     * @return string JSON contenant les informations de l'enseignant
     */
    public function getTeacher($id) {
        $teacher = Teachers::get($id);
        if (!$teacher) {
            header('HTTP/1.1 404 Not Found');
            return json_encode(['error' => 'Enseignant non trouvé']);
        }
        header('HTTP/1.1 204');
        return json_encode($teacher);
    }

    /**
     * Ajoute un nouvel Enseignant
     * @return string JSON contenant les informations de l'enseignant créé
     */
    public function createTeacher() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nom']) || !isset($data['prenom']) || !isset($data['dateNaissance'])) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Données manquantes']);
            exit();
        }


        // Validation des données (par exemple, longueur du nom/prénom, format de l'email, etc.)
        if (strlen($data['nom']) > 50 || strlen($data['prenom']) > 50) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Le nom ou prénom ne doit pas dépasser 50 caractères']);
        }
        // Appel à la méthode du modèle pour créer l'enseignant
        $teacher = Teachers::add($data['nom'], $data['prenom'], $data['dateNaissance'], date('Y-m-d'), $_SESSION['user_email'] ?? 0, $data['idDepartement'] ?? null);
        if (!$teacher) {
            header('HTTP/1.1 500 Internal Server Error');
            return json_encode(['error' => 'Erreur lors de la création de l\'enseignant']);
        } else {
            header('HTTP/1.1 201 Created');
            return json_encode($teacher);  // Retourne l'enseignant créé
        }
    }

    /**
     * Met à jour un Enseignant
     * @param int $id L'ID de l'enseignant à mettre à jour
     * @return string JSON contenant le résultat de la mise à jour
     */
    public function updateTeacher($id) {
        $data = json_decode(file_get_contents('php://input'), true);

        // Vérification des données requises
        if (!isset($data['nom']) || !isset($data['prenom']) || !isset($data['dateNaissance'])
            || empty($data['nom']) || empty($data['prenom']) || empty($data['dateNaissance'])) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Données manquantes']);
        }

        // Validation des données
        if (strlen($data['nom']) > 50 || strlen($data['prenom']) > 50) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Le nom ou prénom ne doit pas dépasser 50 caractères']);
        }
        // Appel à la méthode du modèle pour mettre à jour l'enseignant
        $teacher = Teachers::update($id, $data['nom'], $data['prenom'], $data['dateNaissance'], $_SESSION['user_email'] ?? 0);
        if (!$teacher) {
            header('HTTP/1.1 404 Not Found');
            return json_encode(['error' => 'Erreur lors de la mise à jour de l\'enseignant']);
        } else {
            header('HTTP/1.1 204 No Content');
            return json_encode($teacher);  // Pas de contenu retourné pour la mise à jour
        }
    }

    /**
     * Supprime un Enseignant
     * @param int $id L'ID de l'enseignant à supprimer
     * @return string JSON contenant le résultat de la suppression
     */
    public function deleteTeacher($id) {
        $teacher = Teachers::delete($id);

        if (!$teacher) {
            header('HTTP/1.1 404 Not Found');
            return json_encode(['error' => 'Enseignant non trouvé']);
        } else {
            header('HTTP/1.1 204 No Content');
            return '';  // Pas de contenu retourné pour la suppression
        }

        // Si une erreur inconnue se produit
        header('HTTP/1.1 500 Internal Server Error');
        return json_encode(['error' => 'Erreur lors de la suppression de l\'enseignant']);
    }
}
