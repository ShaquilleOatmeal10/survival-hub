<?php

require_once __DIR__ . '/../src/Database/Database.php';

use App\Database\Database;

$config = require __DIR__ . '/../../../config/app/app.php';

try {
    $database = new Database($config);
    $pdo = $database->getConnection();

    $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    $tables = $result->fetchAll();

    echo "<h1>Database connection successful</h1>";
    echo "<h2>Tables:</h2>";
    echo "<ul>";

    foreach ($tables as $table) {
        echo "<li>" . htmlspecialchars($table['name']) . "</li>";
    }

    echo "</ul>";
} catch (Throwable $e) {
    echo "<h1>Database connection failed</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
