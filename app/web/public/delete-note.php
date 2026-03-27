<?php

require_once __DIR__ . '/../src/Database/Database.php';
require_once __DIR__ . '/../src/Notes/NoteRepository.php';

use App\Database\Database;
use App\Notes\NoteRepository;

$config = require __DIR__ . '/../../../config/app/app.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die('<h1>Invalid note ID</h1>');
}

$error = null;

try {
    $database = new Database($config);
    $pdo = $database->getConnection();

    $noteRepository = new NoteRepository($pdo);
    $note = $noteRepository->getById($id);

    if (!$note) {
        die('<h1>Note not found</h1>');
    }
} catch (Throwable $e) {
    die('<h1>Error</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $noteRepository->delete($id);
        header('Location: notes.php');
        exit;
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}

require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/nav.php';
?>

<h1>Delete Note</h1>

<?php if ($error): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<p>Are you sure you want to delete this note?</p>

<p>
    <strong><?php echo htmlspecialchars($note['title']); ?></strong>
</p>

<form method="POST">
    <button type="submit">Yes, Delete Note</button>
</form>

<br>

<p>
    <a href="view-note.php?id=<?php echo (int) $note['id']; ?>">Cancel</a>
</p>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
