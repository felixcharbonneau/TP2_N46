<?php
namespace controllers;
/**
 * Classe de controleur pour les départements
 */
class DepartmentsController { 
    public function __construct() {

    }
    /**
     * Afficher la page de connexion
     */
    public function index() {
        if (isset($_SESSION['user_role'])) {
            switch ($_SESSION['user_role']) {
                case 'Admin':
                    require VIEWS_PATH . 'Admin/' . 'Departments.php';
                    break;
                case 'Teacher':
                case 'Student':
                default:
                    \libs\Logging::log("[SECURITY] Accès interdit à la page Departments par le rôle '" . $_SESSION['user_role'] . "'. IP : " . ($_SERVER['REMOTE_ADDR'] ?? 'Inconnue'));
                    require VIEWS_PATH . 'ErrorRights.php';
                    break;
            }
        } else {
            \libs\Logging::log("[SECURITY] Accès non connecté à la page Departments. IP : " . ($_SERVER['REMOTE_ADDR'] ?? 'Inconnue'));
            require VIEWS_PATH . 'ErrorRights.php';
        }
    }

}