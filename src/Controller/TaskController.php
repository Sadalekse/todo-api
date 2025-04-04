<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Dto\TaskRequest;
use App\Validator\ValidationException;
use App\Logger;

class TaskController {
    private $repo;

    public function __construct() {
        $this->repo = new TaskRepository();
    }

    public function index(array $user) {
        $userId = $user['sub'];
    
        $status = $_GET['status'] ?? null;
        $deadline = $_GET['deadline'] ?? null;
        $sortBy = $_GET['sort'] ?? null; // created_at или deadline
        $sortOrder = $_GET['order'] ?? 'desc'; // asc / desc
    
        $tasks = $this->repo->getAllByUser($userId, $status, $deadline, $sortBy, $sortOrder);
        echo json_encode($tasks);
    }
    

    public function create(array $user) {
        $userId = $user['sub'];
        $input = json_decode(file_get_contents('php://input'), true);
    
        try {
            $task = new TaskRequest($input);
    
            $this->repo->createTask(
                $userId,
                $task->title,
                $task->description,
                $task->status,
                $task->deadline
            );
    
            Logger::info("User $userId created task: {$task->title}");
            http_response_code(201);
            echo json_encode(['message' => 'Task created']);
        } catch (ValidationException $e) {
            Logger::error("Validation failed on create by user $userId: " . json_encode($e->errors));
            http_response_code(400);
            echo json_encode(['errors' => $e->errors]);
        }
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
            Logger::error("Unauthorized update attempt by user $userId on task $taskId");
            http_response_code(404);
            echo json_encode(['error' => 'Task not found']);
            return;
        }
    
        $input = json_decode(file_get_contents('php://input'), true);
    
        try {
            $request = new TaskRequest(array_merge($task, $input));
    
            $this->repo->updateTask(
                $taskId,
                $request->title,
                $request->description,
                $request->status,
                $request->deadline
            );
    
            Logger::info("User $userId updated task $taskId");
            echo json_encode(['message' => 'Task updated']);
        } catch (ValidationException $e) {
            Logger::error("Validation failed on update by user $userId: " . json_encode($e->errors));
            http_response_code(400);
            echo json_encode(['errors' => $e->errors]);
        }
    }
    
    
    public function delete(array $user, int $taskId) {
        $userId = $user['sub'];
        $task = $this->repo->getTaskById($taskId);
    
        if (!$task || $task['user_id'] !== $userId) {
            Logger::error("Unauthorized delete attempt by user $userId on task $taskId");
            http_response_code(404);
            echo json_encode(['error' => 'Task not found']);
            return;
        }
    
        $this->repo->deleteTask($taskId);
        Logger::info("User $userId deleted task $taskId");
        echo json_encode(['message' => 'Task deleted']);
    }
    
    
}
