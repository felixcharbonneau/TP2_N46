<?php
namespace controllers;

class TeachersController {    
    public function __construct() {
        
    }
    
    /**
     * Afficher la page de connexion
     */
    public function index() {
        if(isset($_SESSION['user_role'])) {
            switch($_SESSION['user_role']){
                case 'Admin':
                    require VIEWS_PATH . 'Admin/' . 'Teachers.php';
                    break;
                case 'Student':
                case 'Teacher':
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