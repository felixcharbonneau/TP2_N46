<?php
namespace controllers;

class HomeController {
    public function __construct() {

    }
    /**
     * Afficher la page de connexion
     */
    public function index() {
        // Inclure la vue
        require VIEWS_PATH . '/Admin/Home.php';
    }

}