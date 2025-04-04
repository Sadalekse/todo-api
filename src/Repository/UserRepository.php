<?php

namespace App\Repository;

use App\Database;
use PDO;

class UserRepository {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function create(string $email, string $passwordHash): bool {
        $stmt = $this->db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        return $stmt->execute([$email, $passwordHash]);
    }
}
