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

    public function getById(int $id): array|null
    {
        $statement = $this->pdo->prepare(
            "SELECT id, title, content, priority, is_pinned, created_at, updated_at
             FROM notes
             WHERE id = :id
             LIMIT 1"
        );

        $statement->execute([
            ':id' => $id,
        ]);

        $note = $statement->fetch();

        return $note ?: null;
    }

    public function create(
        string $title,
        string $content,
        string $priority = 'reference',
        int $isPinned = 0,
        array $tagIds = []
    ): int {
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

        $noteId = (int) $this->pdo->lastInsertId();

        foreach ($tagIds as $tagId) {
            $tagStatement = $this->pdo->prepare(
                "INSERT INTO item_tags (item_type, item_id, tag_id)
                 VALUES (:item_type, :item_id, :tag_id)"
            );

            $tagStatement->execute([
                ':item_type' => 'note',
                ':item_id' => $noteId,
                ':tag_id' => (int) $tagId,
            ]);
        }

        return $noteId;
    }

    public function update(int $id, string $title, string $content, string $priority): void
    {
        $statement = $this->pdo->prepare(
            "UPDATE notes
             SET title = :title,
                 content = :content,
                 priority = :priority,
                 updated_at = :updated_at
             WHERE id = :id"
        );

        $statement->execute([
            ':title' => $title,
            ':content' => $content,
            ':priority' => $priority,
            ':updated_at' => date('Y-m-d H:i:s'),
            ':id' => $id,
        ]);
    }

public function delete(int $id): void
{
    $this->pdo->beginTransaction();

    try {
        $deleteTags = $this->pdo->prepare(
            "DELETE FROM item_tags
             WHERE item_type = :item_type
               AND item_id = :item_id"
        );

        $deleteTags->execute([
            ':item_type' => 'note',
            ':item_id' => $id,
        ]);

        $deleteLinks = $this->pdo->prepare(
            "DELETE FROM links
             WHERE (from_type = :from_type AND from_id = :from_id)
                OR (to_type = :to_type AND to_id = :to_id)"
        );

        $deleteLinks->execute([
            ':from_type' => 'note',
            ':from_id' => $id,
            ':to_type' => 'note',
            ':to_id' => $id,
        ]);

        $deleteNote = $this->pdo->prepare(
            "DELETE FROM notes
             WHERE id = :id"
        );

        $deleteNote->execute([
            ':id' => $id,
        ]);

        $this->pdo->commit();
    } catch (\Throwable $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}

}
