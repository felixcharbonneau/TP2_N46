<?php
namespace controllers;
/**
 * Controleur de l'authentification des usagers
 */
class AuthentificationController{

    public function __construct() {

    }

    /**
     * Afficher la page de connexion
     */
    public function disconnect() {
        if (isset($_SESSION)) {
            session_unset();
            session_destroy();
        }
        header("Location: /");
        exit();
        require 'views/Connexion.php';
        exit();
    }

    /**
     * Afficher la page de connexion
     */
public function login() {
    $isValid = false;

    // Check if 'role' is set in POST request
    if (isset($_POST['role'])) {
        switch ($_POST['role']) {
            case 'Admin':
                $user = \models\Admin::selectByEmail($_POST['email']);
                $_SESSION['user_role'] = 'Admin';
                break;
            case 'Teacher':
                $user = \models\Teachers::selectByEmail($_POST['email']);
                $_SESSION['user_role'] = 'Teacher';
                break;
            case 'Student':
                $user = \models\Student::selectByEmail($_POST['email']);
                break;
            default:
                $user = null;
        }
    }

    // Debug: Check if user object was fetched properly
    if (isset($user) && $user != null) {
        $isValid = $user->connexion($_POST['password']);
    } else {
        $isValid = false;
        if (isset($_SESSION)) {
            session_unset();
            session_destroy();
        }
    }

    // If user is valid, redirect to home
    if ($isValid) {
        $_SESSION['user_email'] = $user->email;
        if ($_SESSION['user_role'] !== 'Admin') {
            $_SESSION['user_id'] = $user->id;
        }
        \libs\Security::generateCSRFToken();
        header("Location: /home");
        exit();
    } else {
        // Debugging failed login
        sleep(2);
        exit();
    }
}




}