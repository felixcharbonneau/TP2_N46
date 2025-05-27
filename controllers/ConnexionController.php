<?php
namespace controllers;
/**
 * Controlleur pour la page de connexion
 */
class ConnexionController {
    public function __construct() {

    }
    /**
     * Afficher la page de connexion
     */
    public function index() {
        // Inclure la vue
        require VIEWS_PATH . 'Connexion.php';
    }
}