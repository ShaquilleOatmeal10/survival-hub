<?php

require_once __DIR__ . '/../src/Database/Database.php';
require_once __DIR__ . '/../src/Notes/NoteRepository.php';
require_once __DIR__ . '/../src/Links/LinkRepository.php';

use App\Database\Database;
use App\Notes\NoteRepository;
use App\Links\LinkRepository;

$config = require __DIR__ . '/../../../config/app/app.php';

$error = null;

try {
    $database = new Database($config);
    $pdo = $database->getConnection();

    $noteRepository = new NoteRepository($pdo);
    $linkRepository = new LinkRepository($pdo);

    $notes = $noteRepository->getAll();
} catch (Throwable $e) {
    die('<h1>Error</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fromId = (int) ($_POST['from_id'] ?? 0);
    $toId = (int) ($_POST['to_id'] ?? 0);
    $linkType = trim($_POST['link_type'] ?? 'related');

    if ($fromId <= 0 || $toId <= 0) {
        $error = 'Please choose both notes.';
    } elseif ($fromId === $toId) {
        $error = 'A note cannot link to itself.';
    } else {
        try {
            $linkRepository->create('note', $fromId, 'note', $toId, $linkType);
            header('Location: view-note.php?id=' . $fromId);
            exit;
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/nav.php';
?>

<h1>Link Notes</h1>

<?php if ($error): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST" action="">
    <div>
        <label for="from_id">From Note</label><br>
        <select id="from_id" name="from_id" required>
            <option value="">Select a note</option>
            <?php foreach ($notes as $note): ?>
                <option value="<?php echo (int) $note['id']; ?>">
                    <?php echo htmlspecialchars($note['title']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <br>

    <div>
        <label for="to_id">To Note</label><br>
        <select id="to_id" name="to_id" required>
            <option value="">Select a note</option>
            <?php foreach ($notes as $note): ?>
                <option value="<?php echo (int) $note['id']; ?>">
                    <?php echo htmlspecialchars($note['title']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <br>

    <div>
        <label for="link_type">Link Type</label><br>
        <select id="link_type" name="link_type">
            <option value="related">Related</option>
            <option value="supports">Supports</option>
            <option value="references">References</option>
        </select>
    </div>

    <br>

    <button type="submit">Create Link</button>
</form>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
