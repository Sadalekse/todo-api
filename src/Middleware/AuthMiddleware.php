<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    private $secret;

    public function __construct() {
        $config = require __DIR__ . '/../config.php';
        $this->secret = $config['jwt_secret'];
    }

    public function authenticate(): ?array {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $jwt = $matches[1];
            try {
                $decoded = JWT::decode($jwt, new Key($this->secret, 'HS256'));
                return (array) $decoded;
            } catch (\Exception $e) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid or expired token']);
                exit;
            }
        }

        http_response_code(401);
        echo json_encode(['error' => 'Authorization header missing or malformed']);
        exit;
    }
}
