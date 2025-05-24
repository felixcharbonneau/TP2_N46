<?php
namespace controllers;

/**
 * Controlleur pour l'affichage de la page d'accueil
 */
class HomeController {
    public function __construct() {

    }
    /**
     * Afficher la page de connexion
     */
    public function index() {
        // Inclure la vue
        switch ($_SESSION['user_role']) {
            case 'Admin':
                require VIEWS_PATH . '/Admin/Home.php';
                break;
            case 'Teacher':
                require VIEWS_PATH . '/Teacher/Home.php';
                break;
            case 'Student':
                require VIEWS_PATH . '/Student/Home.php';
                break;
        }
    }

}