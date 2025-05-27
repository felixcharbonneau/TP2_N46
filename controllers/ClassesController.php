<?php
namespace controllers;

use models\Student;

/**
 * Controlleur des groupes
 */
class ClassesController {
    public function __construct() {
        
    }
    
    /**
     * Afficher la page de connexion
     */
    public function index() {
        if(isset($_SESSION['user_role'])) {
            switch($_SESSION['user_role']){
                case 'Admin':
                    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                    if ($page < 1) $page = 1;

                    $searchValue = isset($_GET['query']) ? $_GET['query'] : '';
                    $totalCount = \models\Classes::getTotal($searchValue);
                    $perPage = 10;
                    $maxPage = max(1, ceil($totalCount / $perPage));

                    if ($page > $maxPage) {
                        // Redirect or adjust to last available page
                        $page = $maxPage;
                    }

                    if ($totalCount === 0) {
                        $classes = [];
                        $studentsByGroup = [];
                    } else {
                        $classes = \models\Classes::getAll($page, $searchValue);
                        $studentsByGroup = [];
                        if ($classes) {
                            $groupIds = array_map(fn($class) => $class->id, $classes);
                            $studentsByGroup = \models\Student::getStudentsInGroups($groupIds);
                        }
                    }

                    $students = \models\Student::getAll();
                    $teachers = \models\Teachers::getAll();
                    $courses = \models\Cours::getAll();

                    $editToken = hash('sha256', $_SESSION['csrf_token'] . "AdminEditClassForm");
                    $deleteToken = hash('sha256', $_SESSION['csrf_token'] . "AdminDeleteClassForm");
                    $addToken = hash('sha256', $_SESSION['csrf_token'] . "AdminAddClassForm");
                    $removeToken = hash('sha256', $_SESSION['csrf_token'] . "TeacherRemoveStudentForm");
                    $addStudentToken = hash('sha256', $_SESSION['csrf_token'] . "TeacherAddStudentForm");

                    include_once VIEWS_PATH . 'Admin/' . 'Classes.php';

                    break;
                case 'Teacher':
                    $removeToken = hash('sha256', $_SESSION['csrf_token'] . "TeacherRemoveStudentForm");
                    $addStudentToken = hash('sha256', $_SESSION['csrf_token'] . "TeacherAddStudentForm");
                    $classes = \models\Classes::getAllFromTeacher($_SESSION['user_id']);
                    if ($classes) {
                        $groupIds = array_map(fn($class) => $class->id, $classes);
                        $studentsByGroup = \models\Student::getStudentsInGroups($groupIds);
                    } else {
                        $studentsByGroup = [];
                    }
                    $students = \models\Student::getAll();
                    require VIEWS_PATH . 'Teacher/' . 'Classes.php';
                    break;
                case 'Student':
                    $classes = \models\Classes::getAllFromStudent($_SESSION['user_id']);
                    if ($classes) {
                        $groupIds = array_map(fn($class) => $class->id, $classes);
                        $studentsByGroup = \models\Student::getStudentsInGroups($groupIds);
                    } else {
                        $studentsByGroup = [];
                    }
                    require VIEWS_PATH . 'Student/' . 'Classes.php';
                    break;
                default:
                    require VIEWS_PATH . 'ErrorRights.php';
            }
        }else{
            require VIEWS_PATH . 'ErrorRights.php';
        }
    }

    /**
     * Retirer un étudiant d'un groupe
     */
    public function removeStudent() {
        if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'Teacher' || $_SESSION['user_role'] === 'Admin')) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (
                    isset($_POST['student_id'], $_POST['group_id'], $_POST['csrf_token']) &&
                    \libs\Security::verifyCSRFToken($_POST['csrf_token'], "TeacherRemoveStudentForm")
                ) {
                    $studentId = intval($_POST['student_id']);
                    $groupId = intval($_POST['group_id']);

                    if ($studentId > 0 && $groupId > 0) {
                        $removed = \models\Student::removeFromGroup($studentId, $groupId);
                        header('Location: /classes');
                        exit;
                    } else {
                        die("Invalid student or group ID.");
                    }
                } else {
                    $message = "Token CSRF invalide détecté dans TeacherRemoveStudentForm lors de la suppression par l'utilisateur "
                        . ($_SESSION['user_email'] ?? 'inconnu')
                        . " depuis l'IP " . ($_SERVER['REMOTE_ADDR'] ?? 'inconnu');
                    \libs\Logging::log($message);

                    error_log($message);
                    die("CSRF token invalid or missing parameters.");
                }
            } else {
                die("Invalid request method.");
            }
        } else {
            require VIEWS_PATH . 'ErrorRights.php';
        }
    }


    /**
     * Ajout d'un étudiant dans un groupe
     */
    public function addStudent() {
        if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'Teacher' || $_SESSION['user_role'] === 'Admin')) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (
                    isset($_POST['student_id'], $_POST['group_id'], $_POST['csrf_token']) &&
                    \libs\Security::verifyCSRFToken($_POST['csrf_token'], "TeacherAddStudentForm")
                ) {
                    $studentId = intval($_POST['student_id']);
                    $groupId = intval($_POST['group_id']);
                    if ($studentId > 0 && $groupId > 0) {
                        $added = \models\Student::addToGroup($studentId, $groupId);
                        if ($added) {
                            header('Location: /classes');
                            exit;
                        } else {
                            $message = "Échec de l'ajout de l'étudiant (ID={$studentId}) au groupe (ID={$groupId}) par l'utilisateur "
                                . ($_SESSION['user_email'] ?? 'inconnu')
                                . " depuis l'IP " . ($_SERVER['REMOTE_ADDR'] ?? 'inconnu');
                            \libs\Logging::log($message);

                            die("Failed to add the student to the group.");
                        }
                    } else {
                        die("Invalid student or group ID.");
                    }
                } else {
                    $message = "Token CSRF invalide détecté dans TeacherAddStudentForm par l'utilisateur "
                        . ($_SESSION['user_email'] ?? 'inconnu')
                        . " depuis l'IP " . ($_SERVER['REMOTE_ADDR'] ?? 'inconnu');
                    \libs\Logging::log($message);
                    error_log($message);
                    die("CSRF token invalid or missing parameters.");
                }
            } else {
                die("Invalid request method.");
            }
        } else {
            require VIEWS_PATH . 'ErrorRights.php';
        }
    }
    /**
     * Création d'un groupe
     */
    public function add() {
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $csrfToken = $_POST['csrf_token'] ?? '';

                if (\libs\Security::verifyCSRFToken($csrfToken, "AdminAddClassForm")) {
                    $numero = $_POST['numero'] ?? '';
                    $nom = $_POST['nom'] ?? '';
                    $description = $_POST['description'] ?? '';
                    $coursID = intval($_POST['coursId'] ?? 0);
                    $enseignantID = intval($_POST['idEnseignant'] ?? 0);
                    $createdBy = $_SESSION['user_email'] ?? 'inconnu';

                    if (is_numeric($numero) && intval($numero) > 0) {
                        $numero = intval($numero);

                        if ($numero && $nom && $description && $coursID && $enseignantID) {
                            $added = \models\Classes::add($numero, $nom, $description, $coursID, $enseignantID, $createdBy);

                            if ($added) {
                                $page = intval($_POST['page'] ?? 1);
                                $redirectUrl = '/classes?page=' . $page;
                                if (!empty($_POST['query'])) {
                                    $redirectUrl .= '&query=' . urlencode($_POST['query']);
                                }
                                header('Location: ' . $redirectUrl);
                                exit;
                            } else {
                                $error = "Erreur lors de la création du groupe.";
                            }
                        } else {
                            $error = "Tous les champs sont obligatoires.";
                        }
                    } else {
                        $error = "Le numéro de groupe doit être un entier positif.";
                    }
                } else {
                    $error = "Jeton CSRF invalide.";

                    $logMessage = "Jeton CSRF invalide détecté dans AdminAddClassForm par l'utilisateur "
                        . ($_SESSION['user_email'] ?? 'inconnu') . " depuis l'IP " . ($_SERVER['REMOTE_ADDR'] ?? 'inconnu');
                    \libs\Logging::log($logMessage);
                    error_log($logMessage);
                }
                $page = intval($_POST['page'] ?? 1);
                $query = $_POST['query'] ?? '';
                $redirectUrl = '/classes?page=' . $page . '&error=' . urlencode($error);
                if (!empty($query)) {
                    $redirectUrl .= '&query=' . urlencode($query);
                }
                header('Location: ' . $redirectUrl);
                exit;
            }
        } else {
            require VIEWS_PATH . 'ErrorRights.php';
        }
    }


    public function edit() {
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin') {
            $error = "";
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $csrfToken = $_POST['csrf_token'] ?? '';

                if (\libs\Security::verifyCSRFToken($csrfToken, "AdminEditClassForm")) {
                    $id = intval($_POST['id'] ?? 0);
                    $nom = $_POST['nom'] ?? '';
                    $numero = $_POST['numero'];
                    $description = $_POST['description'] ?? '';
                    $coursID = intval($_POST['coursId'] ?? 0);
                    $enseignantID = intval($_POST['idEnseignant'] ?? 0);
                    $modifiedBy = $_SESSION['user_email'] ?? 0;

                    if (is_numeric($numero) && $numero > 0) {
                        $numero = intval($numero);
                        if ($id && $numero && $nom && $description && $coursID && $enseignantID && $modifiedBy) {
                            \models\Classes::update($id, $numero, $nom, $description, $coursID, $enseignantID, $modifiedBy);
                            header('Location: /classes?page=' . $_POST['page']);
                            exit;
                        } else {
                            $error = "Erreur : Tous les champs sont obligatoires.";
                        }
                    } else {
                        $error = "Erreur : Le numéro de groupe doit être un entier positif.";
                    }
                } else {
                    $error = "Erreur : Jeton CSRF invalide.";
                    $message = "Jeton CSRF invalide détecté dans AdminEditClassForm par l'utilisateur " . ($_SESSION['user_email'] ?? 'inconnu') . " depuis l'IP " . ($_SERVER['REMOTE_ADDR'] ?? 'inconnu');
                    \libs\Logging::log($message);
                }

                $page = intval($_POST['page'] ?? 1);
                $query = $_POST['query'] ?? '';
                $redirectUrl = '/classes?page=' . $page . '&error=' . urlencode($error);
                if (!empty($query)) {
                    $redirectUrl .= '&query=' . urlencode($query);
                }
                header('Location: ' . $redirectUrl);
                exit;
            }
        } else {
            require VIEWS_PATH . 'ErrorRights.php';
        }
    }

    public function delete() {
        if (isset($_SESSION['user_role'])) {
            switch ($_SESSION['user_role']) {
                case 'Admin':
                    if (
                        isset($_POST['id']) &&
                        isset($_POST['csrf_token']) &&
                        \libs\Security::verifyCSRFToken($_POST['csrf_token'], "AdminDeleteClassForm")
                    ) {
                        $id = intval($_POST['id']);
                        \models\Classes::delete($id);

                        $page = intval($_POST['page'] ?? 1);
                        $redirectUrl = '/classes?page=' . $page;

                        if (!empty($_POST['query'])) {
                            $redirectUrl .= '&query=' . urlencode($_POST['query']);
                        }

                        header('Location: ' . $redirectUrl);
                        exit();
                    } else {
                        $message = "Jeton CSRF invalide détecté dans AdminDeleteClassForm par l'utilisateur " . ($_SESSION['user_email'] ?? 'inconnu') . " depuis l'IP " . ($_SERVER['REMOTE_ADDR'] ?? 'inconnu');
                        \libs\Logging::log($message);

                        error_log('Erreur : Jeton CSRF invalide ou données manquantes.');
                        die('Erreur : Jeton CSRF invalide ou données manquantes.');
                    }
                    break;

                case 'Teacher':
                case 'Student':
                default:
                    require VIEWS_PATH . 'ErrorRights.php';
                    break;
            }
        } else {
            require VIEWS_PATH . 'ErrorRights.php';
        }
    }


}