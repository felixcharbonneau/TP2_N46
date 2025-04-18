<?php
namespace controllers;

/**
 * Controlleur pour la gestion des cours
 */
class CoursesController {
    
    public function __construct() {

    }
    /**
     * Afficher la page de connexion
     */
    public function index() {
        require VIEWS_PATH . 'Admin/' . 'Courses.php';
    }
}