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

    public function getByItem(string $itemType, int $itemId): array
    {
        $statement = $this->pdo->prepare(
            "SELECT tags.id, tags.name
             FROM tags
             INNER JOIN item_tags ON tags.id = item_tags.tag_id
             WHERE item_tags.item_type = :item_type
               AND item_tags.item_id = :item_id
             ORDER BY tags.name ASC"
        );

        $statement->execute([
            ':item_type' => $itemType,
            ':item_id' => $itemId,
        ]);

        return $statement->fetchAll();
    }
}
