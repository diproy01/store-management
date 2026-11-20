<?php
session_start();

$configPath = __DIR__ . '/config.php';
if (is_file($configPath)) {
    header('Location: index.php');
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

$host = $_SESSION['host'];
$user = $_SESSION['user'];
$pass = $_SESSION['pass'];

try {
    $connection = new mysqli($host, $user, $pass);
    $connection->set_charset('utf8mb4');
    if (!isset($_POST['select_box'])) {
        $connection->query("CREATE DATABASE `$name` CHARACTER SET utf8mb4");
    }
    $connection->select_db($name);
    if ($connection->query('SHOW TABLES')->num_rows !== 0) {
        http_response_code(409);
        exit('This installer requires an empty database. To use existing data, configure config.php manually.');
    }

    $schema = file_get_contents(__DIR__ . '/schema.sql');
    if ($schema === false) {
        throw new RuntimeException('Unable to read schema.sql.');
    }
    $connection->multi_query($schema);
    while ($connection->more_results()) {
        $connection->next_result();
    }

    $config = ['database' => $name, 'host' => $host, 'username' => $user, 'password' => $pass];
    if (file_put_contents($configPath, "<?php\n\$config = " . var_export($config, true) . ";\n", LOCK_EX) === false) {
        throw new RuntimeException('Unable to write config.php.');
    }
} catch (mysqli_sql_exception | RuntimeException $exception) {
    error_log($exception->getMessage());
    http_response_code(500);
    exit('Setup failed. Check the database settings, permissions, and PHP error log.');
}

$_SESSION['db_name'] = $name;
header('Location: user_details.php');
