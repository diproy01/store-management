<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_file(__DIR__ . '/config.php')) {
    header('Location: install.php');
    exit;
}
require __DIR__ . '/config.php';
$expected = $config['setup_token'] ?? null;
$provided = $_POST['setup_token'] ?? null;
if (!is_string($expected) || strlen($expected) < 32
    || !is_string($provided) || !hash_equals($expected, $provided)) {
    header('Location: install.php?invalid=1', true, 303);
    exit;
}

$status = 500;
$message = 'Setup could not initialize the configured database. Check that it exists and that the configured database account can create tables.';
try {
    $connection = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);
    $connection->set_charset('utf8mb4');
    $lock = 'store-setup:' . substr(hash('sha256', $config['database']), 0, 48);
    if ((int) $connection->execute_query('SELECT GET_LOCK(?, 5)', [$lock])->fetch_row()[0] !== 1) {
        $message = 'Another setup request is running. Try again in a moment.';
        throw new RuntimeException('Setup lock is busy.');
    }

    $schema = file_get_contents(__DIR__ . '/schema.sql');
    if ($schema === false) {
        throw new RuntimeException('Unable to read schema.sql.');
    }
    $tables = array_column($connection->query('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"')->fetch_all(), 0);
    if ($tables === []) {
        $connection->multi_query($schema);
        while ($connection->more_results()) {
            $connection->next_result();
        }
    } else {
        if (in_array('stock_user', $tables, true)
            && (int) $connection->query('SELECT COUNT(*) FROM stock_user')->fetch_row()[0] > 0) {
            header('Location: index.php', true, 303);
            exit;
        }
        // Recover databases initialized by an earlier attempt that failed to publish config.php.
        preg_match_all('/^CREATE TABLE `([^\x60]+)`/m', $schema, $matches);
        sort($tables);
        sort($matches[1]);
        if ($tables !== $matches[1]
            || (int) $connection->query('SELECT COUNT(*) FROM store_details')->fetch_row()[0] !== 1) {
            $status = 409;
            $message = 'Select an empty database or the database from an unfinished installation with no users. Existing data was preserved.';
            throw new RuntimeException('Database is not eligible for setup.');
        }
    }

    session_regenerate_id(true);
    $_SESSION['setup_authorization'] = hash('sha256', serialize($config));
    header('Location: user_details.php', true, 303);
} catch (mysqli_sql_exception | RuntimeException $exception) {
    error_log('Store Management setup: ' . $exception->getMessage());
    http_response_code($status);
    echo '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<p><a href="install.php">Return to database setup</a></p>';
}
