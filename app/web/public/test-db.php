<?php

require_once __DIR__ . '/../src/Database/Database.php';

use App\Database\Database;

$config = require __DIR__ . '/../../../config/app/app.php';

try {
    $database = new Database($config);
    $pdo = $database->getConnection();

    $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    $tables = $result->fetchAll();
} catch (Throwable $e) {
    $error = $e->getMessage();
}

require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../templates/nav.php';
?>

<h1>Database Test</h1>

<?php if (!empty($error)): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php else: ?>
    <p>Database connection successful.</p>
    <h2>Tables</h2>
    <ul>
        <?php foreach ($tables as $table): ?>
            <li><?php echo htmlspecialchars($table['name']); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
