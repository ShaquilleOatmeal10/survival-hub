<?php

require_once __DIR__ . '/../src/Database/Database.php';
require_once __DIR__ . '/../src/Tags/TagRepository.php';

use App\Database\Database;
use App\Tags\TagRepository;

$config = require __DIR__ . '/../../../config/app/app.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        $error = 'Tag name is required.';
    } else {
        try {
            $database = new Database($config);
            $pdo = $database->getConnection();

            $tagRepository = new TagRepository($pdo);
            $tagRepository->create($name);

            header('Location: tags.php');
            exit;
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/nav.php';
?>

<h1>Create Tag</h1>

<?php if ($error): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST" action="">
    <div>
        <label for="name">Tag Name</label><br>
        <input type="text" id="name" name="name" required>
    </div>

    <br>

    <button type="submit">Save Tag</button>
</form>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
