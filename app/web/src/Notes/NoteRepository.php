<?php

namespace App\Notes;

use PDO;

class NoteRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function getAll(): array
    {
        $statement = $this->pdo->query(
            "SELECT id, title, content, priority, is_pinned, created_at, updated_at
             FROM notes
             ORDER BY updated_at DESC"
        );

        return $statement->fetchAll();
    }

    public function create(string $title, string $content, string $priority = 'reference', int $isPinned = 0): void
    {
        $now = date('Y-m-d H:i:s');

        $statement = $this->pdo->prepare(
            "INSERT INTO notes (title, content, priority, is_pinned, created_at, updated_at)
             VALUES (:title, :content, :priority, :is_pinned, :created_at, :updated_at)"
        );

        $statement->execute([
            ':title' => $title,
            ':content' => $content,
            ':priority' => $priority,
            ':is_pinned' => $isPinned,
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);
    }
}
