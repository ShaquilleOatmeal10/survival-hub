<?php

require_once __DIR__ . '/../src/Database/Database.php';
require_once __DIR__ . '/../src/Notes/NoteRepository.php';
require_once __DIR__ . '/../src/Tags/TagRepository.php';

use App\Database\Database;
use App\Notes\NoteRepository;
use App\Tags\TagRepository;

$config = require __DIR__ . '/../../../config/app/app.php';

try {
    $database = new Database($config);
    $pdo = $database->getConnection();

    $noteRepository = new NoteRepository($pdo);
    $tagRepository = new TagRepository($pdo);

    $notes = $noteRepository->getAll();

    foreach ($notes as &$note) {
        $note['tags'] = $tagRepository->getByItem('note', (int) $note['id']);
    }
    unset($note);
} catch (Throwable $e) {
    die('<h1>Error</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
}

require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/nav.php';
?>

<h1>Notes</h1>

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

                <?php if (!empty($note['tags'])): ?>
                    <br>
                    <em>Tags:
                        <?php
                        $tagNames = array_map(
                            fn($tag) => htmlspecialchars($tag['name']),
                            $note['tags']
                        );
                        echo implode(', ', $tagNames);
                        ?>
                    </em>
                <?php endif; ?>
            </li>
            <br>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
