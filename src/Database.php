<?php

namespace App;

use PDO;
use PDOException;

class Database {
    public static function connect(): PDO {
        $config = require __DIR__ . '/config.php';

        try {
            return new PDO(
                "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8",
                $config['db']['user'],
                $config['db']['pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'DB connection error']);
            exit;
        }
    }
}
