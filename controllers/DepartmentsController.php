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
        if(isset($_SESSION['user_role'])) {
            switch($_SESSION['user_role']){
                case 'Admin':
                    require VIEWS_PATH . 'Admin/' . 'Departments.php';
                    break;
                case 'Teacher':
                    require VIEWS_PATH . 'ErrorRights.php';
                    break;
                case 'Student':
                    require VIEWS_PATH . 'ErrorRights.php';
                    break;
                default:
                    require VIEWS_PATH . 'ErrorRights.php';
        }
        }else{
            require VIEWS_PATH . 'ErrorRights.php';
        }
    }
}