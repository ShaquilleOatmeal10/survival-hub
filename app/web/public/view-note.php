<?php

require_once __DIR__ . '/../src/Database/Database.php';
require_once __DIR__ . '/../src/Notes/NoteRepository.php';
require_once __DIR__ . '/../src/Tags/TagRepository.php';
require_once __DIR__ . '/../src/Links/LinkRepository.php';

use App\Database\Database;
use App\Notes\NoteRepository;
use App\Tags\TagRepository;
use App\Links\LinkRepository;

$config = require __DIR__ . '/../../../config/app/app.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die('<h1>Invalid note ID</h1>');
}

try {
    $database = new Database($config);
    $pdo = $database->getConnection();

    $noteRepository = new NoteRepository($pdo);
    $tagRepository = new TagRepository($pdo);
    $linkRepository = new LinkRepository($pdo);

    $note = $noteRepository->getById($id);

    if (!$note) {
        die('<h1>Note not found</h1>');
    }

    $tags = $tagRepository->getByItem('note', $id);
    $linkedNotes = $linkRepository->getOutgoingLinks('note', $id, 'note');
} catch (Throwable $e) {
    die('<h1>Error</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
}

require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/nav.php';
?>

<h1><?php echo htmlspecialchars($note['title']); ?></h1>

<p>
    <strong>Priority:</strong>
    <?php echo htmlspecialchars($note['priority']); ?>
</p>

<?php if (!empty($tags)): ?>
    <p>
        <strong>Tags:</strong>
        <?php
        $tagNames = array_map(
            fn($tag) => htmlspecialchars($tag['name']),
            $tags
        );
        echo implode(', ', $tagNames);
        ?>
    </p>
<?php endif; ?>

<p>
    <strong>Created:</strong>
    <?php echo htmlspecialchars($note['created_at']); ?>
    <br>
    <strong>Updated:</strong>
    <?php echo htmlspecialchars($note['updated_at']); ?>
</p>

<hr>

<div>
    <?php echo nl2br(htmlspecialchars($note['content'])); ?>
</div>

<?php if (!empty($linkedNotes)): ?>
    <hr>
    <h2>Linked Notes</h2>
    <ul>
        <?php foreach ($linkedNotes as $linkedNote): ?>
            <li>
                <a href="view-note.php?id=<?php echo (int) $linkedNote['note_id']; ?>">
                    <?php echo htmlspecialchars($linkedNote['title']); ?>
                </a>
                (<?php echo htmlspecialchars($linkedNote['link_type']); ?>)
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p>
    <a href="notes.php">Back to Notes</a>
</p>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
