<?php
use PHPUnit\Framework\TestCase;

// ./vendor/bin/phpunit tests/connexionTest.php
class connexionTest extends TestCase
{
    protected $pdo;

    protected function setUp(): void
    {
        if (!defined('CONFIG_PATH')) {
            define('CONFIG_PATH', dirname(__DIR__) . '/config/');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * test vérifiant la génération de token aléatoire
     * @return void
     * @throws \Random\RandomException
     */
    public function testGenerateTokenReturnsCorrectLength()
    {
        $length = 16;
        $token = libs\Security::generateToken($length);
        $this->assertIsString($token);
        $this->assertEquals($length * 2, strlen($token));
    }

    /**
     * Tests sur les méthodes d'encodage/décodage en base 64
     */
    public function testBase64UrlEncodeDecodeRoundTrip()
    {
        $original = "Hello world! 1234";
        $encoded = libs\Security::base64UrlEncode($original);
        $decoded = libs\Security::base64UrlDecode($encoded);
        $this->assertEquals($original, $decoded);

        $this->assertStringNotContainsString('+', $encoded);
        $this->assertStringNotContainsString('/', $encoded);
        $this->assertStringNotContainsString('=', $encoded);
    }

    /**
     * Test sur la génération de Json Web Token
     */
    public function testGenerateAndValidateJWT()
    {
        $data = ['email' => 'test@example.com'];
        $role = 'Admin';

        $jwt = libs\Security::generateJWT($data, $role);
        $this->assertIsString($jwt);

        $payload = libs\Security::validateJWT($jwt);
        $this->assertIsArray($payload);
        $this->assertEquals($data['email'], $payload['email']);
        $this->assertEquals($role, $payload['role']);
    }

    /**
     * Vérifie que les méthodes de JWT retourne false pour un token invalide
     */
    public function testValidateJWTReturnsFalseForInvalidToken()
    {
        $invalidJwt = "abc.def.ghi";
        $this->assertFalse(libs\Security::validateJWT($invalidJwt));

        $emptyJwt = "";
        $this->assertFalse(libs\Security::validateJWT($emptyJwt));
    }

    /**
     * Vérification du fonctionnement des méthodes de génération de token CSRF
     */
    public function testVerifyCSRFToken()
    {
        $constant = "testFormConstant";
        $tokenRaw = libs\Security::generateToken();
        $_SESSION['csrf_token'] = $tokenRaw;

        $correctToken = hash('sha256', $tokenRaw . $constant);
        $this->assertTrue(libs\Security::verifyCSRFToken($correctToken, $constant));

        $wrongToken = 'invalidtoken';
        $this->assertFalse(libs\Security::verifyCSRFToken($wrongToken, $constant));

        unset($_SESSION['csrf_token']);
        $this->assertFalse(libs\Security::verifyCSRFToken($correctToken, $constant));
    }


}
