<?php
session_start();

define('ROOT_PATH', dirname(__DIR__) . '/');
define('APP_PATH', ROOT_PATH . 'app/');
define('CONFIG_PATH', ROOT_PATH . 'config/');
define('CONTROLLERS_PATH', ROOT_PATH . 'controllers/');
define('MODELS_PATH', ROOT_PATH . 'models/');
define('VIEWS_PATH', ROOT_PATH . 'views/');
define('ROUTES_PATH', ROOT_PATH . 'routes/');

//Chargement automatique des classes
spl_autoload_register(function ($className) {
    $file = ROOT_PATH . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) {
        require $file;
    } else {
        echo "fichier introuvable: " . $file . "<br>";
    }
});

$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/'; 

$url = substr($requestUri, strlen($basePath));
$url = parse_url($url, PHP_URL_PATH);

if (strpos($url, 'api/') === 0) {
    //route API
    include_once ROUTES_PATH . 'api.php';

    $apiPath = substr($url, 4);
    $pathParts = explode('/', $apiPath);
    $resource = $pathParts[0] ?? '';
    $id = $pathParts[1] ?? null;


    $method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($resource === 'students') {
            $apiController = new \controllers\StudentsApi();
            if ($method === 'get') {
                if ($id) {
                    echo $apiController->getStudent($id);
                } else {
                    echo $apiController->getStudents();
                }
            } elseif ($method === 'post') {
                echo $apiController->createStudent();
            } elseif ($method === 'put' && $id) {
                echo $apiController->updateStudent($id);
            } elseif ($method === 'delete' && $id) {
                echo $apiController->deleteStudent($id);
            }
        }elseif ($resource === 'departments') {
            $apiController = new \controllers\DepartmentsApi();
            if ($method === 'get') {
                if ($id) {
                    echo $apiController->getDepartment($id);
                } else {
                    echo $apiController->getDepartments();
                }
            } elseif ($method === 'post') {
                echo $apiController->createDepartment();
            } elseif ($method === 'put' && $id) {
                echo $apiController->updateDepartment($id);
            } elseif ($method === 'delete' && $id) {
                echo $apiController->deleteDepartment($id);
            }
        }elseif ($resource === 'teachers') {
            $apiController = new \controllers\TeacherApi();
            if ($method === 'get') {
                if ($id) {
                    echo $apiController->getTeachers($id);
                } else {
                    echo $apiController->getTeachers();
                }
            } elseif ($method === 'post') {
                echo $apiController->createTeacher();
            } elseif ($method === 'put' && $id) {
                echo $apiController->updateTeacher($id);
            } elseif ($method === 'delete' && $id) {
                echo $apiController->deleteTeacher($id);
            }
        }  else {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'Ressource non trouvée']);
        }
}else{
    // Route Web standard
    $controller = 'connexion';
    $action = 'index';

    if ($url !== '') {
        $parts = explode('/', $url);
        $controller = $parts[0] ?: 'connexion';
        $action = $parts[1] ?? 'index';
    }

    $controllerClass = '\\controllers\\' . ucfirst($controller) . 'Controller';
    if (class_exists($controllerClass)) {
        $controllerInstance = new $controllerClass();
        if (method_exists($controllerInstance, $action)) {
            call_user_func([$controllerInstance, $action]);
        } else {
            header('HTTP/1.1 404 Not Found');
            echo 'Action non trouvée';
        }
    } else {
        header('HTTP/1.1 404 Not Found');
        echo 'Contrôleur non trouvé';
    }
}