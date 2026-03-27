<?php

require_once __DIR__ . '/../src/Database/Database.php';
require_once __DIR__ . '/../src/Tags/TagRepository.php';

use App\Database\Database;
use App\Tags\TagRepository;

$config = require __DIR__ . '/../../../config/app/app.php';

try {
    $database = new Database($config);
    $pdo = $database->getConnection();

    $tagRepository = new TagRepository($pdo);
    $tags = $tagRepository->getAll();
} catch (Throwable $e) {
    die('<h1>Error</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
}

require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/nav.php';
?>

<h1>Tags</h1>

<p><a href="create-tag.php">Create a tag</a></p>

<?php if (empty($tags)): ?>
    <p>No tags found.</p>
<?php else: ?>
    <ul>
        <?php foreach ($tags as $tag): ?>
            <li><?php echo htmlspecialchars($tag['name']); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
