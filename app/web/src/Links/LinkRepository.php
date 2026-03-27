<?php

namespace App\Links;

use PDO;

class LinkRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function create(
        string $fromType,
        int $fromId,
        string $toType,
        int $toId,
        string $linkType = 'related'
    ): void {
        $statement = $this->pdo->prepare(
            "INSERT INTO links (from_type, from_id, to_type, to_id, link_type, created_at)
             VALUES (:from_type, :from_id, :to_type, :to_id, :link_type, :created_at)"
        );

        $statement->execute([
            ':from_type' => $fromType,
            ':from_id' => $fromId,
            ':to_type' => $toType,
            ':to_id' => $toId,
            ':link_type' => $linkType,
            ':created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getOutgoingLinks(string $fromType, int $fromId, string $toType): array
    {
        $statement = $this->pdo->prepare(
            "SELECT links.id, links.link_type, notes.id AS note_id, notes.title
             FROM links
             INNER JOIN notes ON notes.id = links.to_id
             WHERE links.from_type = :from_type
               AND links.from_id = :from_id
               AND links.to_type = :to_type
             ORDER BY notes.title ASC"
        );

        $statement->execute([
            ':from_type' => $fromType,
            ':from_id' => $fromId,
            ':to_type' => $toType,
        ]);

        return $statement->fetchAll();
    }
}
