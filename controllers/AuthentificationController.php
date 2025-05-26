<?php

namespace controllers;

class AuthentificationController {


    public function __construct() {
    }

    protected function doExit() {
        exit();
    }

    protected function doRedirect(string $url) {
        header("Location: $url");
        $this->doExit();
    }

    public function disconnect() {
        if (isset($_SESSION)) {
            session_unset();
            session_destroy();
        }
        $this->doRedirect("/");
    }

    public function login() : void {
        $isValid = false;

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
                    $_SESSION['user_role'] = 'Student';
                    break;
                default:
                    $user = null;
            }
        }

        if (isset($user) && $user != null) {
            try {
                $isValid = $user->connexion($_POST['password']);
            } catch (\Throwable $e) {
                echo "Error during login: " . $e->getMessage();
                $this->doExit();
            }

        } else {
            $isValid = false;
            if (isset($_SESSION)) {
                session_unset();
                session_destroy();
            }
        }

        if ($isValid) {
            $_SESSION['created'] = time();
            $_SESSION['user_email'] = $user->email;
            if ($_SESSION['user_role'] !== 'Admin') {
                $_SESSION['user_id'] = $user->id;
            }
            \libs\Security::generateCSRFToken();
            $this->doRedirect("/home");
        } else {
            // Debugging failed login
            sleep(2);
            $this->doRedirect("/connexion?error=1");
            $this->doExit();
        }
    }
}
