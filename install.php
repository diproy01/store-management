<?php
session_start();
if (is_file(__DIR__ . '/config.php')) {
    require __DIR__ . '/config.php';
}
$enabled = is_string($config['setup_token'] ?? null) && strlen($config['setup_token']) >= 32;
$error = isset($_GET['invalid']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Store Management - Setup</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div id="content">
    <h1>Set up Store Management</h1>
    <?php if (!$enabled): ?>
        <p>Create an empty database, copy <code>config.php.sample</code> to <code>config.php</code>,
            and fill in your database settings and setup token. See <code>README.md</code> for the commands.</p>
        <?php if (isset($config)): ?>
            <p><a href="index.php">Go to login</a></p>
        <?php endif; ?>
    <?php else: ?>
        <?php if ($error): ?>
            <p class="error-box round" role="alert">The setup token did not match.</p>
        <?php endif; ?>
        <form id="login-form" method="post" action="setup_page.php">
            <p>Setup will initialize the database named in your configuration.</p>
            <p>
                <label for="setup-token">Setup token</label>
                <input type="password" class="round full-width-input" id="setup-token"
                       name="setup_token" required autocomplete="off">
            </p>
            <button type="submit" class="button round blue">Continue setup</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
