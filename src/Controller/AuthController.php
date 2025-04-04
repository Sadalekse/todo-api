<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController {
    private $userRepo;
    private $secret;

    public function __construct() {
        $this->userRepo = new UserRepository();
        $this->secret = require __DIR__ . '/../config.php';
        $this->secret = $this->secret['jwt_secret'];
    }

    public function register() {
        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid email']);
            return;
        }

        if (strlen($password) < 6) {
            http_response_code(400);
            echo json_encode(['error' => 'Password too short']);
            return;
        }

        if ($this->userRepo->findByEmail($email)) {
            http_response_code(409);
            echo json_encode(['error' => 'Email already in use']);
            return;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $this->userRepo->create($email, $passwordHash);

        http_response_code(201);
        echo json_encode(['message' => 'User registered']);
    }

    public function login() {
        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? '';
        $password = $input['password'] ?? '';

        $user = $this->userRepo->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return;
        }

        $payload = [
            'sub' => $user['id'],
            'email' => $user['email'],
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24), // 24 часа
        ];

        $jwt = JWT::encode($payload, $this->secret, 'HS256');

        echo json_encode(['token' => $jwt]);
    }
}
