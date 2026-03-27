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
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $priority = trim($_POST['priority'] ?? 'normal');

    if ($title === '' || $content === '') {
        $error = 'Title and content are required.';
    } else {
        try {
            $noteRepository->update($id, $title, $content, $priority);
            header('Location: view-note.php?id=' . $id);
            exit;
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/nav.php';
?>

<h1>Edit Note</h1>

<?php if ($error): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST">
    <div>
        <label>Title</label><br>
        <input type="text" name="title" value="<?php echo htmlspecialchars($note['title']); ?>" required>
    </div>

    <br>

    <div>
        <label>Content</label><br>
        <textarea name="content" rows="10" cols="50" required><?php echo htmlspecialchars($note['content']); ?></textarea>
    </div>

    <br>

    <div>
        <label>Priority</label><br>
        <select name="priority">
            <option value="reference" <?php if ($note['priority'] === 'reference') echo 'selected'; ?>>Reference</option>
            <option value="important" <?php if ($note['priority'] === 'important') echo 'selected'; ?>>Important</option>
            <option value="critical" <?php if ($note['priority'] === 'critical') echo 'selected'; ?>>Critical</option>
        </select>
    </div>

    <br>

    <button type="submit">Update Note</button>
</form>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
