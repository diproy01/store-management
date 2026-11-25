<?php
session_start();
require_once __DIR__ . '/lib/account_mail.php';
if (($_SESSION['usertype'] ?? null) === 'admin' && is_file(__DIR__ . '/config.php')) {
    header('Location: dashboard.php');
    exit;
}
$configured = is_file(__DIR__ . '/config.php');
if ($configured) {
    require __DIR__ . '/config.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Store Management - Sign in</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
<main class="auth-card" aria-labelledby="auth-title">
    <h1 id="auth-title">Sign in to Store Management</h1>
    <?php if (!$configured || isset($_GET['unavailable'])): ?>
        <p class="auth-message" role="alert">Store Management is unavailable. Ask the server administrator to check the shell setup in README.md.</p>
    <?php elseif (isset($_GET['invalid'])): ?>
        <p class="auth-message" role="alert">The email address or password was incorrect.</p>
    <?php elseif (isset($_GET['reset'])): ?>
        <p class="auth-message" role="status">Password updated. Sign in with your new password.</p>
    <?php endif; ?>
    <?php if ($configured): ?>
        <form method="post" action="checklogin.php">
            <p><label for="email">Email address</label>
                <input id="email" name="email" type="email" required autocomplete="username"></p>
            <p><label for="password">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"></p>
            <button type="submit">Sign in</button>
            <?php if (storeMailSettings($config) !== null): ?>
                <a class="auth-link" href="forget_pass.php">Forgot your password?</a>
            <?php endif; ?>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
