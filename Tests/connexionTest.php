<?php
use PHPUnit\Framework\TestCase;

//executer avec:     docker exec -it tp2_n46-php-1 ./vendor/bin/phpunit tests/connexionTest.php (depend du nom du docker)
//Je les run sur docker parce que sa prend une connexion a la bd absolument pour tester mon modele
//Puisque toutes les méthodes performent des lectures/écritures
class connexionTest extends TestCase
{
    protected $pdo;

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(__DIR__) . '/');
        }
        if (!defined('APP_PATH')) {
            define('APP_PATH', ROOT_PATH . 'app/');
        }
        if (!defined('CONFIG_PATH')) {
            define('CONFIG_PATH', ROOT_PATH . 'config/');
        }
        if (!defined('CONTROLLERS_PATH')) {
            define('CONTROLLERS_PATH', ROOT_PATH . 'controllers/');
        }
        if (!defined('MODELS_PATH')) {
            define('MODELS_PATH', ROOT_PATH . 'models/');
        }
        if (!defined('VIEWS_PATH')) {
            define('VIEWS_PATH', ROOT_PATH . 'views/');
        }
        if (!defined('ROUTES_PATH')) {
            define('ROUTES_PATH', ROOT_PATH . 'routes/');
        }

        $this->pdo = \models\DatabaseConnexion::getInstance();
    }
    public function testPasswordVerify(){




    }


}
