<?php
namespace controllers;
/**
 * Information sur l'utilisateur connecté
 */
class MeController {
    public function __construct() {

    }
    /**
     * Afficher les infomations de l'utilisateur
     */
    public function index() {
        // Inclure la vue
        switch ($_SESSION['user_role']) {
            case 'Admin':
                require VIEWS_PATH . 'ErrorRights.php';
                break;
            case 'Teacher':
                $teacher = \models\Teachers::get($_SESSION['user_id']);
                $department = \models\Department::get($teacher->idDepartement);
                require VIEWS_PATH . '/Teacher/Me.php';
                break;
            case 'Student':
                $student = \models\Student::get($_SESSION['user_id']);
                require VIEWS_PATH . '/Student/Me.php';
                break;
        }
    }
}