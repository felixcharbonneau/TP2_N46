<?php
namespace controllers;

class ClassesController {    
    public function __construct() {
        
    }
    
    /**
     * Afficher la page de connexion
     */
    public function index() {
        require VIEWS_PATH . 'Admin/' . 'Classes.php';
    }
}