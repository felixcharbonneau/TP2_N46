<?php
namespace controllers;

class StudentsController {
    private $students;
    
    public function __construct() {
        
    }
    
    /**
     * Afficher la page de connexion
     */
    public function index() {
        require VIEWS_PATH . 'Admin/' . 'Students.php';
    }
}