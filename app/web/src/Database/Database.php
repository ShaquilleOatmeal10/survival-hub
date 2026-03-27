<?php

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    private PDO $connection;

    public function __construct(array $config)
    {
        $databasePath = $config['database_path'] ?? null;

        if (!$databasePath) {
            throw new \InvalidArgumentException('Database path is missing from configuration.');
        }

        $this->connection = $this->connect($databasePath);
    }

    private function connect(string $databasePath): PDO
    {
        try {
            $pdo = new PDO('sqlite:' . $databasePath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $pdo;
        } catch (PDOException $e) {
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
