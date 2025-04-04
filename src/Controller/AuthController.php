<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Dto\RegisterRequest;
use App\Dto\LoginRequest;
use App\Validator\ValidationException;

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
    
        try {
            $request = new RegisterRequest($input);
    
            if ($this->userRepo->findByEmail($request->email)) {
                http_response_code(409);
                echo json_encode(['error' => 'Email already in use']);
                return;
            }
    
            $passwordHash = password_hash($request->password, PASSWORD_DEFAULT);
            $this->userRepo->create($request->email, $passwordHash);
    
            http_response_code(201);
            echo json_encode(['message' => 'User registered']);
        } catch (ValidationException $e) {
            http_response_code(400);
            echo json_encode(['errors' => $e->errors]);
        }
    }
    
    public function login() {
        $input = json_decode(file_get_contents('php://input'), true);
    
        try {
            $request = new LoginRequest($input);
    
            $user = $this->userRepo->findByEmail($request->email);
    
            if (!$user || !password_verify($request->password, $user['password'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid credentials']);
                return;
            }
    
            $payload = [
                'sub' => $user['id'],
                'email' => $user['email'],
                'iat' => time(),
                'exp' => time() + (60 * 60 * 24),
            ];
    
            $jwt = JWT::encode($payload, $this->secret, 'HS256');
    
            echo json_encode(['token' => $jwt]);
        } catch (ValidationException $e) {
            http_response_code(400);
            echo json_encode(['errors' => $e->errors]);
        }
    }
    
}
