<?php

require_once __DIR__ . '/../src/Database/Database.php';
require_once __DIR__ . '/../src/Notes/NoteRepository.php';

use App\Database\Database;
use App\Notes\NoteRepository;

$config = require __DIR__ . '/../../../config/app/app.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $priority = $_POST['priority'] ?? 'reference';

    if ($title === '' || $content === '') {
        $error = 'Title and content are required.';
    } else {
        try {
            $database = new Database($config);
            $pdo = $database->getConnection();

            $noteRepository = new NoteRepository($pdo);
            $noteRepository->create($title, $content, $priority);

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

    <button type="submit">Save Note</button>
</form>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
