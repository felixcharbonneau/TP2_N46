<?php
namespace controllers;
use models\Student;
/**
 * Api des étudiants
 */
class StudentsApi {
    /**
     * Constructeur de la classe
     * Définit les en-têtes de la réponse HTTP
     */
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
    public function getStudents($page = 1) {
        $jwt = \libs\Security::getJwt();
        $decoded = \libs\Security::validateJwt($jwt);
        if (!$decoded || !isset($decoded['role'])) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP inconnue';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'User-Agent inconnu';

            \libs\Logging::log("Accès non autorisé : JWT invalide ou champ 'role' manquant. IP : {$ip}, User-Agent : {$userAgent}");

            header('HTTP/1.1 401 Unauthorized');
            return json_encode(['error' => 'Unauthorized access.']);
        }

        $allowedRoles = ['Admin', 'Teacher', 'Student'];
        if (!in_array($decoded['role'], $allowedRoles)) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP inconnue';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'User-Agent inconnu';

            \libs\Logging::log("Accès non autorisé : Role invalide pour requete API. IP : {$ip}, User-Agent : {$userAgent}");

            header('HTTP/1.1 403 Forbidden');
            return json_encode(['error' => 'Forbidden: Access denied.']);
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $query = isset($_GET['query']) ? $_GET['query'] : '';
        if($query){
            $students = Student::getAll($page, $query);
        }else{
            $students = Student::getAll($page);
        }
        return json_encode($students);
    }
    /**
     * Récupère un étudiant par son ID
     * @param int $id L'ID de l'étudiant
     * @return string JSON contenant les informations de l'étudiant
     */
    public function getStudent($id) {
        $jwt = \libs\Security::getJwt();
        $decoded = \libs\Security::validateJwt($jwt);
        if (!$decoded || !isset($decoded['role'])) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP inconnue';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'User-Agent inconnu';

            \libs\Logging::log("Accès non autorisé : JWT invalide ou champ 'role' manquant. IP : {$ip}, User-Agent : {$userAgent}");

            header('HTTP/1.1 401 Unauthorized');
            return json_encode(['error' => 'Unauthorized access.']);
        }

        $allowedRoles = ['Admin', 'Teacher', 'Student'];
        if (!in_array($decoded['role'], $allowedRoles)) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP inconnue';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'User-Agent inconnu';

            \libs\Logging::log("Accès non autorisé : Role invalide pour requete API. IP : {$ip}, User-Agent : {$userAgent}");

            header('HTTP/1.1 403 Forbidden');
            return json_encode(['error' => 'Forbidden: Access denied.']);
        }
        $student = Student::get($id);
        return json_encode($student);
    }
    /**
     * Ajoute un nouvel étudiant
     * @param array $data Les données de l'étudiant à ajouter
     * @return string JSON contenant les informations de l'étudiant créé
     */
    public function createStudent() {
        $jwt = \libs\Security::getJwt();
        $decoded = \libs\Security::validateJwt($jwt);
        if (!$decoded || !isset($decoded['role'])) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP inconnue';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'User-Agent inconnu';

            \libs\Logging::log("Accès non autorisé : JWT invalide ou champ 'role' manquant. IP : {$ip}, User-Agent : {$userAgent}");

            header('HTTP/1.1 401 Unauthorized');
            return json_encode(['error' => 'Unauthorized access.']);
        }

        $allowedRoles = ['Admin'];
        if (!in_array($decoded['role'], $allowedRoles)) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP inconnue';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'User-Agent inconnu';

            \libs\Logging::log("Accès non autorisé : Role invalide pour requete API. IP : {$ip}, User-Agent : {$userAgent}");

            header('HTTP/1.1 403 Forbidden');
            return json_encode(['error' => 'Forbidden: Access denied.']);
        }
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
        $jwt = \libs\Security::getJwt();
        $decoded = \libs\Security::validateJwt($jwt);
        if (!$decoded || !isset($decoded['role'])) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP inconnue';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'User-Agent inconnu';

            \libs\Logging::log("Accès non autorisé : JWT invalide ou champ 'role' manquant. IP : {$ip}, User-Agent : {$userAgent}");

            header('HTTP/1.1 401 Unauthorized');
            return json_encode(['error' => 'Unauthorized access.']);
        }

        $allowedRoles = ['Admin'];
        if (!in_array($decoded['role'], $allowedRoles)) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP inconnue';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'User-Agent inconnu';

            \libs\Logging::log("Accès non autorisé : Role invalide pour requete API. IP : {$ip}, User-Agent : {$userAgent}");

            header('HTTP/1.1 403 Forbidden');
            return json_encode(['error' => 'Forbidden: Access denied.']);
        }
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
    /**
     * Met à jour un étudiant
     * @param int $id L'ID de l'étudiant à mettre à jour
     * @return string JSON contenant le résultat de la mise à jour
     */
    public function updateStudent($id) {
        $jwt = \libs\Security::getJwt();
        $decoded = \libs\Security::validateJwt($jwt);
        if (!$decoded || !isset($decoded['role'])) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP inconnue';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'User-Agent inconnu';

            \libs\Logging::log("Accès non autorisé : JWT invalide ou champ 'role' manquant. IP : {$ip}, User-Agent : {$userAgent}");

            header('HTTP/1.1 401 Unauthorized');
            return json_encode(['error' => 'Unauthorized access.']);
        }

        $allowedRoles = ['Admin'];
        if (!in_array($decoded['role'], $allowedRoles)) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP inconnue';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'User-Agent inconnu';

            \libs\Logging::log("Accès non autorisé : Role invalide pour requete API. IP : {$ip}, User-Agent : {$userAgent}");

            header('HTTP/1.1 403 Forbidden');
            return json_encode(['error' => 'Forbidden: Access denied.']);
        }
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['nom']) || !isset($data['prenom']) || !isset($data['dateNaissance'])
            || empty($data['nom']) || empty($data['dateNaissance']) || empty($data['prenom'])) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Données manquantes']);
        }
        if (strlen($data['nom']) > 50 || strlen($data['prenom']) > 50 ){
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Le nom ou prénom ne doit pas dépasser 50 caractères']);
        }

        $student = Student::update($id, $data);
        if (!$student) {
            header('HTTP/1.1 404 Not Found');
            return json_encode(['error' => 'Erreur lors de la mise à jour de l\'étudiant']);
        }else{
            header('HTTP/1.1 204 No Content');
            return '';
        }
    }

}