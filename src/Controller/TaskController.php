<?php

namespace App\Controller;

use App\Repository\TaskRepository;

class TaskController {
    private $repo;

    public function __construct() {
        $this->repo = new TaskRepository();
    }

    public function index(array $user) {
        $userId = $user['sub'];
        $tasks = $this->repo->getAllByUser($userId);
        echo json_encode($tasks);
    }

    public function create(array $user) {
        $userId = $user['sub'];
        $input = json_decode(file_get_contents('php://input'), true);
    
        $title = trim($input['title'] ?? '');
        $description = trim($input['description'] ?? '');
        $status = $input['status'] ?? 'в работе';
        $deadline = $input['deadline'] ?? null;
    
        $allowedStatuses = ['в работе', 'завершено', 'дедлайн'];
    
        if ($title === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Title is required']);
            return;
        }
    
        if (!in_array($status, $allowedStatuses)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid status']);
            return;
        }
    
        $this->repo->createTask($userId, $title, $description, $status, $deadline);
    
        http_response_code(201);
        echo json_encode(['message' => 'Task created']);
    }

    public function show(array $user, int $taskId) {
        $userId = $user['sub'];
        $task = $this->repo->getTaskById($taskId);
    
        if (!$task || $task['user_id'] !== $userId) {
            http_response_code(404);
            echo json_encode(['error' => 'Task not found']);
            return;
        }
    
        echo json_encode($task);
    }
    
    public function update(array $user, int $taskId) {
        $userId = $user['sub'];
        $task = $this->repo->getTaskById($taskId);
    
        if (!$task || $task['user_id'] !== $userId) {
            http_response_code(404);
            echo json_encode(['error' => 'Task not found']);
            return;
        }
    
        $input = json_decode(file_get_contents('php://input'), true);
    
        $title = trim($input['title'] ?? $task['title']);
        $description = trim($input['description'] ?? $task['description']);
        $status = $input['status'] ?? $task['status'];
        $deadline = $input['deadline'] ?? $task['deadline'];
    
        $allowedStatuses = ['в работе', 'завершено', 'дедлайн'];
    
        if (!in_array($status, $allowedStatuses)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid status']);
            return;
        }
    
        $this->repo->updateTask($taskId, $title, $description, $status, $deadline);
        echo json_encode(['message' => 'Task updated']);
    }
    
    public function delete(array $user, int $taskId) {
        $userId = $user['sub'];
        $task = $this->repo->getTaskById($taskId);
    
        if (!$task || $task['user_id'] !== $userId) {
            http_response_code(404);
            echo json_encode(['error' => 'Task not found']);
            return;
        }
    
        $this->repo->deleteTask($taskId);
        echo json_encode(['message' => 'Task deleted']);
    }
    
}
