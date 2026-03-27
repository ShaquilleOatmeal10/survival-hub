<?php

require_once __DIR__ . '/../src/Database/Database.php';
require_once __DIR__ . '/../src/Notes/NoteRepository.php';

use App\Database\Database;
use App\Notes\NoteRepository;

$config = require __DIR__ . '/../../../config/app/app.php';

try {
    $database = new Database($config);
    $pdo = $database->getConnection();

    $noteRepository = new NoteRepository($pdo);
    $notes = $noteRepository->getAll();
} catch (Throwable $e) {
    die('<h1>Error</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes</title>
</head>
<body>
    <h1>Notes</h1>

    <p><a href="create-note.php">Create a note</a></p>

    <?php if (empty($notes)): ?>
        <p>No notes found.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($notes as $note): ?>
                <li>
                    <strong><?php echo htmlspecialchars($note['title']); ?></strong>
                    (<?php echo htmlspecialchars($note['priority']); ?>)
                    <br>
                    <?php echo nl2br(htmlspecialchars($note['content'])); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
