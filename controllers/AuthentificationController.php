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
        require VIEWS_PATH . 'Connexion.php';
    }

    /**
     * Afficher la page de connexion
     */
    public function login() {
        $isValid = false;
        if (isset($_POST['role'] )){
            switch ($_POST['role']){
                case 'Admin':
                    $user = \models\Admin::selectByEmail($_POST['email']);
                    $_SESSION['user_role'] = 'Admin';
                    break;
                case 'Teacher':
                    $user = \models\Teacher::selectByEmail($_POST['email']);
                    break;
                case 'Student':
                    $user = \models\Student::selectByEmail($_POST['email']);
                    break;
                default:
                    $user = null;
            }
        }
        if (isset($user) && $user != null) {
            $isValid = $user->connexion($_POST['password']);

        } else {
            $isValid = false;
            if (isset($_SESSION)) {
                session_unset();
                session_destroy();
            }
        }
        /**
         * * Si l'utilisateur est valide, on le redirige vers la page d'accueil
         * * Sinon, on le redirige vers la page de connexion avec un message d'erreur
         */
        if ($isValid) {
            $token = \libs\Security::generateToken(64);
            $_SESSION['user_email'] = $user->email;
            $user->token = $token;
            header("Location: /home");
        } else {
            sleep(2);
            header("Location: /Connexion?error=1");
        }
    }
}