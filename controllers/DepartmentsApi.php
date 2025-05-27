<?php
namespace controllers;
use models\Department;

/**
 * Api pour les départments
 */
class DepartmentsApi {

    public function __construct() {
        
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type');
    }

    /**
     * Optention de tous les départements
     */
    public function getDepartments(){
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
        $query = isset($_GET['query']) ? $_GET['query'] : '';
        $page = isset($_GET['page']) ? $_GET['page'] : null;
        $departments = Department::getAll($page, $query);
        
        return json_encode($departments);
    }

    /**
     * Optention d'un seul département
     * @param $id du département
     */
    public function getDepartment($id){
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
        $department = Department::get($id);
        return json_encode($department);
    }

    /**
     * Création d'un département
     */
    public function createDepartment(){
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
        $data = json_decode(file_get_contents('php://input'), true);
        if($data === null){
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Aucunes données envoyées']);
        }
        if (!isset($data['nom']) || !isset($data['code']) || !isset($data['description'])) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Données manquantes']);
        }
        if (strlen($data['nom']) > 50 || strlen($data['code']) > 50) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Le nom ou la code ne doit pas dépasser 50 caractères']);
        }


        $department = Department::create($data['nom'], $data['code'], $data['description'], isset($_SESSION['user_email'])? $_SESSION['user_email'] : "system");

        if (!$department) {
            header('HTTP/1.1 404 Not Found');
            return json_encode(['error' => 'Erreur lors de la création du département']);
        }else{
            header('HTTP/1.1 204 No Content');
            return '';
        }
    }

    /**
     * Mise a jours d'un département
     * @param $id du département a mettre a jours
     */
    public function updateDepartment($id){
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
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['nom']) || !isset($data['code']) || !isset($data['description'])) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Données manquantes']);
        }
        if (strlen($data['nom']) > 50 || strlen($data['code']) > 50) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Le nom ou la code ne doit pas dépasser 50 ou 255 caractères respectivement']);
        }
        $department = Department::update($id, $data);
        if (!$department) {
            header('HTTP/1.1 404 Not Found');
            return json_encode(['error' => 'Erreur lors de la mise à jour du département']);
        }else{
            header('HTTP/1.1 204 No Content');
            return '';
        }
    }

    /**
     * Suppression d'un departement
     * @param $id du departement a supprimer
     */
    public function deleteDepartment($id){
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
        $department = Department::delete($id);
        if (!$department) {
            header('HTTP/1.1 404 Not Found');
            return json_encode(['error' => 'Erreur lors de la suppression du département']);
        }else{
            header('HTTP/1.1 204 No Content');
            return '';
        }
    }
}