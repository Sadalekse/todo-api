<?php

namespace App\Repository;

use App\Database;
use PDO;

class TaskRepository {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAllByUser(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTaskById(int $taskId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$taskId]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        return $task ?: null;
    }

    public function createTask(int $userId, string $title, string $description, string $status, ?string $deadline): void {
        $stmt = $this->db->prepare(
            "INSERT INTO tasks (user_id, title, description, status, deadline) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $title, $description, $status, $deadline]);
    }

    public function updateTask(int $taskId, string $title, string $description, string $status, ?string $deadline): void {
        $stmt = $this->db->prepare(
            "UPDATE tasks SET title = ?, description = ?, status = ?, deadline = ? WHERE id = ?"
        );
        $stmt->execute([$title, $description, $status, $deadline, $taskId]);
    }

    public function deleteTask(int $taskId): void {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$taskId]);
    }
}

