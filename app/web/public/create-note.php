<?php

require_once __DIR__ . '/../src/Database/Database.php';
require_once __DIR__ . '/../src/Notes/NoteRepository.php';
require_once __DIR__ . '/../src/Tags/TagRepository.php';

use App\Database\Database;
use App\Notes\NoteRepository;
use App\Tags\TagRepository;

$config = require __DIR__ . '/../../../config/app/app.php';

$error = null;

try {
    $database = new Database($config);
    $pdo = $database->getConnection();

    $tagRepository = new TagRepository($pdo);
    $tags = $tagRepository->getAll();
} catch (Throwable $e) {
    die('<h1>Error</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $priority = $_POST['priority'] ?? 'reference';
    $tagIds = $_POST['tags'] ?? [];

    if ($title === '' || $content === '') {
        $error = 'Title and content are required.';
    } else {
        try {
            $noteRepository = new NoteRepository($pdo);
            $noteRepository->create($title, $content, $priority, 0, $tagIds);

            header('Location: notes.php');
            exit;
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/nav.php';
?>

<h1>Create Note</h1>

<?php if ($error): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST" action="">
    <div>
        <label for="title">Title</label><br>
        <input type="text" id="title" name="title" required>
    </div>

    <br>

    <div>
        <label for="content">Content</label><br>
        <textarea id="content" name="content" rows="10" cols="50" required></textarea>
    </div>

    <br>

    <div>
        <label for="priority">Priority</label><br>
        <select id="priority" name="priority">
            <option value="reference">Reference</option>
            <option value="important">Important</option>
            <option value="critical">Critical</option>
        </select>
    </div>

    <br>

    <div>
        <label for="tags">Tags</label><br>
        <select id="tags" name="tags[]" multiple size="5">
            <?php foreach ($tags as $tag): ?>
                <option value="<?php echo (int) $tag['id']; ?>">
                    <?php echo htmlspecialchars($tag['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <br>

    <button type="submit">Save Note</button>
</form>

<p>Hold Ctrl (or Cmd on Mac) to select multiple tags.</p>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
