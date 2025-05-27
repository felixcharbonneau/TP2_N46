<?php
namespace libs;
require_once __DIR__ . '/../vendor/autoload.php';

use models\DatabaseConnexion;

define('CSRF_TOKEN_NAME', 'csrf_token');

class Security {

    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    public static function getJwt() {
        if (isset($_COOKIE['jwt'])) {
            $jwt = $_COOKIE['jwt'];
            if ($jwt) {
                return $jwt;
            }
        }
        return false;
    }

    public static function verifyPassword($passw, $salt,$storedHash) {
        $config = require CONFIG_PATH . 'database.php';
        $pepper = $config['pepper'];
        return password_verify($passw . $salt . $pepper, $storedHash);
    }

    public static function generateCSRFToken() {
        $token = self::generateToken();
        $_SESSION[CSRF_TOKEN_NAME] = $token;
        return $token;
    }

    public static function verifyCSRFToken($token, $constant) {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            return false;
        }
        
        $stored = $_SESSION[CSRF_TOKEN_NAME];
        $stored = hash('sha256', $stored . $constant);

        return hash_equals($stored, $token);
    }

    public static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function generateJWT($data, $role) {
        $config = require CONFIG_PATH . 'database.php';
        $secretKey = $config['secretKey'];
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600;

        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => [
                'email' => $data['email'],
                'role' => $role
            ]
        ];

        $base64UrlHeader = self::base64UrlEncode(json_encode($header));
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload));
        $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $secretKey, true);
        $base64UrlSignature = self::base64UrlEncode($signature);

        return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
    }



    public static function base64UrlDecode($data) {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $data .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public static function validateJWT($jwt) {
        $config = require CONFIG_PATH . 'database.php';
        $secretKey = $config['secretKey'];

        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return false;
        }

        list($headerB64, $payloadB64, $signatureB64) = $parts;

        $header = json_decode(self::base64UrlDecode($headerB64), true);
        $payload = json_decode(self::base64UrlDecode($payloadB64), true);
        $signature = self::base64UrlDecode($signatureB64);

        if (!$header || !$payload || !$signature) {
            return false;
        }

        // Verify signature
        $expectedSig = hash_hmac('sha256', "$headerB64.$payloadB64", $secretKey, true);
        if (!hash_equals($expectedSig, $signature)) {
            return false;
        }

        // Verify expiry
        if (isset($payload['exp']) && time() >= $payload['exp']) {
            return false;
        }

        return $payload['data'] ?? null;
    }

}