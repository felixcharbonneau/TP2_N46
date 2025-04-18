<?php
namespace controllers;
use models\Student;

class StudentsApi {

    public function __construct() {
        
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type');
    }
    /**
     * Récupère tous les étudiants
     * @return string JSON contenant la liste des étudiants
     */
    public function getStudents() {
        $query = isset($_GET['query']) ? $_GET['query'] : '';
        if($query){
            $students = Student::getAll($query);
        }else{
            $students = Student::getAll();
        }
        return json_encode($students);
    }
    /**
     * Récupère un étudiant par son ID
     * @param int $id L'ID de l'étudiant
     * @return string JSON contenant les informations de l'étudiant
     */
    public function getStudent($id) {
        $student = Student::get($id);
        return json_encode($student);
    }
    /**
     * Ajoute un nouvel étudiant
     * @param array $data Les données de l'étudiant à ajouter
     * @return string JSON contenant les informations de l'étudiant créé
     */
    public function createStudent() {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['nom']) || !isset($data['prenom']) || !isset($data['dateNaissance'])) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Données manquantes']);
        }
        if (strlen($data['nom']) > 50 || strlen($data['prenom']) > 50) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Le nom ou prénom ne doit pas dépasser 50 caractères']);
        }
        $student = Student::create($data);
        if (!$student) {
            header('HTTP/1.1 404 Not Found');
            return json_encode(['error' => 'Erreur lors de la création de l\'étudiant']);
        }else{
            header('HTTP/1.1 204 No Content');
            return '';
        }
    }
    /**
     * Suppression d'un étudiant
     * @param int $id L'ID de l'étudiant à supprimer
     * @return string JSON contenant le résultat de la suppression
     */
    public function deleteStudent($id) {
        $student = Student::delete($id);

        if (!$student) {
            header('HTTP/1.1 404 Not Found');
            return json_encode(['error' => 'Étudiant non trouvé']);
        }else{
            header('HTTP/1.1 204 No Content');
            return '';
        }

        header('HTTP/1.1 500 Internal Server Error');
        return json_encode(['error' => 'Erreur lors de la suppression de l\'utilisateur']);
    }

}