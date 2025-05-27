<?php
namespace controllers;
use Firebase\JWT\JWT;
use models\Teachers;

/**
 * Api des enseignants
 */
class TeacherApi {
    /**
     * Constructeur
     */
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

        // Fetch teachers
        if ($query) {
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
            || empty($data['nom']) || empty($data['prenom']) || empty($data['dateNaissance'])) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['error' => 'Données manquantes']);
        }

        $teacher = Teachers::update(
            $id,
            $data['nom'],
            $data['prenom'],
            $data['dateNaissance'],
            $_SESSION['user_email'] ?? null,
            $data['departement'] ?? null,
            $data['password'] ?? null
        );

        if (!$teacher) {
            header('HTTP/1.1 400 Bad request');
            echo json_encode(['error' => 'Erreur lors de la mise à jour de l\'enseignant']);
            exit;
        }
        header('Content-Type: application/json');
        // On retourne les données mises à jour (tableau) avec 200 OK
        echo json_encode($teacher);
        exit;
    }


    /**
     * Supprime un Enseignant
     * @param int $id L'ID de l'enseignant à supprimer
     * @return string JSON contenant le résultat de la suppression
     */
    public function deleteTeacher($id) {
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
