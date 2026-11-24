<?php
session_start();

$configPath = __DIR__ . '/config.php';
if (is_file($configPath)) {
    header('Location: user_details.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['host'], $_SESSION['user'], $_SESSION['pass'])) {
    header('Location: install.php');
    exit;
}

$name = $_POST['select_box'] ?? $_POST['name'] ?? '';
if (!is_string($name) || !preg_match('/\A[A-Za-z0-9_]{1,64}\z/', $name)) {
    http_response_code(400);
    exit('Use a database name containing only letters, numbers, or underscores.');
}

$config = [
    'database' => $name,
    'host' => $_SESSION['host'],
    'username' => $_SESSION['user'],
    'password' => $_SESSION['pass'],
];
$temporaryDirectory = __DIR__ . '/.setup-' . bin2hex(random_bytes(16));
$temporaryConfig = $temporaryDirectory . '/config.php';
$message = 'PHP could not save config.php. Give the PHP service write access to the project directory during setup, then retry.';
$status = 500;
$createdDirectory = false;

try {
    // Stage the configuration before changing the database. Publish it only after the schema is ready.
    $createdDirectory = @mkdir($temporaryDirectory, 0700);
    $contents = "<?php\n\$config = " . var_export($config, true) . ";\n";
    if (!$createdDirectory || @file_put_contents($temporaryConfig, $contents) !== strlen($contents)
        || !chmod($temporaryConfig, 0600)) {
        throw new RuntimeException('Unable to stage config.php.');
    }

    $message = 'Database setup failed. Check the connection details, database privileges, and PHP error log.';
    $connection = new mysqli($config['host'], $config['username'], $config['password']);
    $connection->set_charset('utf8mb4');
    if (!isset($_POST['select_box'])) {
        $connection->query("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4");
    }
    $connection->select_db($name);

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
        // Recover databases initialized by an earlier attempt that failed to publish config.php.
        preg_match_all('/^CREATE TABLE `([^\x60]+)`/m', $schema, $matches);
        sort($tables);
        sort($matches[1]);
        if ($tables !== $matches[1]
            || (int) $connection->query('SELECT COUNT(*) FROM stock_user')->fetch_row()[0] !== 0
            || (int) $connection->query('SELECT COUNT(*) FROM store_details')->fetch_row()[0] !== 1) {
            $status = 409;
            $message = 'Select an empty database or the database from an unfinished installation with no users. Existing data was preserved.';
            throw new RuntimeException('Database is not eligible for setup.');
        }
    }

    $message = 'PHP could not publish config.php. Check directory permissions, then retry with the same database.';
    if (!@link($temporaryConfig, $configPath)) {
        throw new RuntimeException('Unable to publish config.php.');
    }
    $_SESSION['db_name'] = $name;
    header('Location: user_details.php');
} catch (mysqli_sql_exception | RuntimeException $exception) {
    error_log('Store Management setup: ' . $exception->getMessage());
    http_response_code($status);
    echo '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<p><a href="database_install.php">Return to database setup</a></p>';
} finally {
    if ($createdDirectory) {
        if (is_file($temporaryConfig)) {
            unlink($temporaryConfig);
        }
        rmdir($temporaryDirectory);
    }
}
