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
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
