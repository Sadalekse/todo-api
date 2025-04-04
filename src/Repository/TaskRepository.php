<?php

namespace App\Repository;

use App\Database;
use PDO;

class TaskRepository {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAllByUser(int $userId, ?string $status = null, ?string $deadline = null, ?string $sortBy = null, ?string $sortOrder = null): array
    {
    $sql = "SELECT * FROM tasks WHERE user_id = :user_id";
    $params = ['user_id' => $userId];

    if ($status) {
        $sql .= " AND status = :status";
        $params['status'] = $status;
    }

    if ($deadline) {
        $sql .= " AND deadline = :deadline";
        $params['deadline'] = $deadline;
    }

    if (in_array($sortBy, ['created_at', 'deadline']) && in_array(strtolower($sortOrder), ['asc', 'desc'])) {
        $sql .= " ORDER BY $sortBy " . strtoupper($sortOrder);
    } else {
        $sql .= " ORDER BY created_at DESC";
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
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

