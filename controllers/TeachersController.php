<?php
namespace controllers;

class TeachersController {    
    public function __construct() {
        
    }
    
    /**
     * Afficher la page de connexion
     */
    public function index() {
        require VIEWS_PATH . 'Admin/' . 'Teachers.php';
    }
}