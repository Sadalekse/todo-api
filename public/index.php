<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\AuthController;
use App\Controller\TaskController;
use App\Middleware\AuthMiddleware;

header('Content-Type: application/json');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$authController = new AuthController();
$taskController = new TaskController();
$authMiddleware = new AuthMiddleware();

switch (true) {
    // Регистрация
    case $uri === '/register' && $method === 'POST':
        $authController->register();
        break;

    // Вход
    case $uri === '/login' && $method === 'POST':
        $authController->login();
        break;

    // Список задач
    case $uri === '/tasks' && $method === 'GET':
        $user = $authMiddleware->authenticate();
        $taskController->index($user);
        break;

    // Создание задачи
    case $uri === '/tasks' && $method === 'POST':
        $user = $authMiddleware->authenticate();
        $taskController->create($user);
        break;

    // Просмотр, обновление, удаление задачи по ID
    case preg_match('#^/tasks/(\d+)$#', $uri, $matches) && $method === 'GET':
        $user = $authMiddleware->authenticate();
        $taskController->show($user, (int)$matches[1]);
        break;

    case preg_match('#^/tasks/(\d+)$#', $uri, $matches) && $method === 'PUT':
        $user = $authMiddleware->authenticate();
        $taskController->update($user, (int)$matches[1]);
        break;

    case preg_match('#^/tasks/(\d+)$#', $uri, $matches) && $method === 'DELETE':
        $user = $authMiddleware->authenticate();
        $taskController->delete($user, (int)$matches[1]);
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
        break;
}
