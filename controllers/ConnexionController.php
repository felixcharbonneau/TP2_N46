<?php
namespace controllers;

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