<?php

namespace App\Tags;

use PDO;

class TagRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function getAll(): array
    {
        $statement = $this->pdo->query(
            "SELECT id, name, created_at
             FROM tags
             ORDER BY name ASC"
        );

        return $statement->fetchAll();
    }

    public function create(string $name): void
    {
        $name = trim($name);

        $statement = $this->pdo->prepare(
            "INSERT INTO tags (name, created_at)
             VALUES (:name, :created_at)"
        );

        $statement->execute([
            ':name' => $name,
            ':created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
