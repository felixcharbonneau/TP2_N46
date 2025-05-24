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
        if(isset($_SESSION['user_role'])) {
            switch($_SESSION['user_role']){
                case 'Admin':
                    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                    $searchValue = isset($_GET['query']) ? $_GET['query'] : '';                    
                    $courses = \models\Cours::getAll($page, $searchValue);

                    $editToken = hash('sha256', $_SESSION['csrf_token'] . "AdminEditCourseForm");
                    $deleteToken = hash('sha256', $_SESSION['csrf_token'] . "AdminDeleteCourseForm");
                    $addToken = hash('sha256', $_SESSION['csrf_token'] . "AdminAddCourseForm");

                    include_once VIEWS_PATH . 'Admin/' . 'Courses.php';
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
    public function edit() {
        if(isset($_SESSION['user_role'])) {
            switch($_SESSION['user_role']){
                case 'Admin':
                    if (!isset($_GET['id'], $_GET['numero'], $_GET['nom'], $_GET['description'], $_GET['idDepartement'])) {
                        $missingParams = [];
                        if (!isset($_GET['id'])) $missingParams[] = 'id';
                        if (!isset($_GET['numero'])) $missingParams[] = 'numero';
                        if (!isset($_GET['nom'])) $missingParams[] = 'nom';
                        if (!isset($_GET['description'])) $missingParams[] = 'description';
                        if (!isset($_GET['idDepartement'])) $missingParams[] = 'idDepartement';

                        if (!empty($missingParams)) {
                            die('Error: Parametres manquants. Les paramètres manquants sont: ' . implode(', ', $missingParams) . '.');
                        }
                    }
                    if(isset($_GET['csrf_token']) && isset($_SESSION['csrf_token']) && \libs\Security::verifyCSRFToken($_GET['csrf_token'], "AdminEditCourseForm")){
                        $id = intval($_GET['id']);
                        $numero = $_GET['numero'];
                        $nom = $_GET['nom'];
                        $description = $_GET['description'];
                        $idDepartement = intval($_GET['idDepartement']);
                        $modifiedBy = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';

                        $course = \models\Cours::update($id, $numero, $nom, $description, $idDepartement, $modifiedBy);

                        header('Location: /Courses?page=' . $_GET['page']);
                    }else{
                        die('Error: Token CSRF invalide.');
                    }
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
    public function add() {
        if (isset($_SESSION['user_role'])) {
            switch ($_SESSION['user_role']) {
                case 'Admin':
                    if (!isset($_POST['numero'], $_POST['nom'], $_POST['description'], $_POST['idDepartement'])) {
                        $missingParams = [];
                        if (!isset($_POST['numero'])) $missingParams[] = 'numero';
                        if (!isset($_POST['nom'])) $missingParams[] = 'nom';
                        if (!isset($_POST['description'])) $missingParams[] = 'description';
                        if (!isset($_POST['idDepartement'])) $missingParams[] = 'idDepartement';

                        if (!empty($missingParams)) {
                            die('Error: Parametres manquants. Les paramètres manquants sont: ' . implode(', ', $missingParams) . '.');
                        }
                    }

                    if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token']) && \libs\Security::verifyCSRFToken($_POST['csrf_token'], "AdminAddCourseForm")) {
                        $numero = $_POST['numero'];
                        $nom = $_POST['nom'];
                        $description = $_POST['description'];
                        $idDepartement = intval($_POST['idDepartement']);
                        $createdBy = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';

                        $course = \models\Cours::add($numero, $nom, $description, $idDepartement, $createdBy);

                        header('Location: /Courses');
                    } else {
                        die('Error: Token CSRF invalide.');
                    }
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
        } else {
            require VIEWS_PATH . 'ErrorRights.php';
        }
    }
    public function delete() {
        if (isset($_SESSION['user_role'])) {
            switch ($_SESSION['user_role']) {
                case 'Admin':
                    if (!isset($_POST['id'])) {
                        die('Error: Parametre manquant. Le paramètre manquant est: id.');
                    }
                    if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token']) && \libs\Security::verifyCSRFToken($_POST['csrf_token'], "AdminDeleteCourseForm")) {
                        $id = intval($_POST['id']);
                        $course = \models\Cours::delete($id);
                        header('Location: /Courses?page=' . $_POST['page']);
                    } else {
                        die('Error: Token CSRF invalide.');
                    }
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
        } else {
            require VIEWS_PATH . 'ErrorRights.php';
        }
    }
}