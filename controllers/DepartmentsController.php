<?php
namespace controllers;

class DepartmentsController {
    
    public function __construct() {

    }
    /**
     * Afficher la page de connexion
     */
    public function index() {
        require VIEWS_PATH . 'Admin/' . 'Departments.php';
    }
}