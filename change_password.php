<?php
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/lib/auth.php';

$_SESSION['password_csrf'] ??= bin2hex(random_bytes(32));
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? null;
    if (!is_string($token) || !hash_equals($_SESSION['password_csrf'], $token)) {
        http_response_code(403);
        exit;
    }
    $old = $_POST['old_pass'] ?? null;
    $new = $_POST['new_pass'] ?? null;
    $user = $db->connection->execute_query('SELECT password FROM stock_user WHERE id=?', [$_SESSION['id']])->fetch_assoc();
    if (!validStorePassword($old) || !$user || $user['password'] === null || !password_verify($old, $user['password'])) {
        $message = 'The current password is incorrect.';
    } elseif (!validStorePassword($new) || $new !== ($_POST['confirm_pass'] ?? null)) {
        $message = 'Enter matching new passwords of at least 8 characters and no more than 72 bytes.';
    } else {
        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $db->connection->execute_query(
            'UPDATE stock_user SET password=?, reset_token_hash=NULL, reset_expires_at=NULL WHERE id=? AND BINARY password=?',
            [$newHash, $_SESSION['id'], $user['password']]
        );
        if ($db->connection->affected_rows === 1) {
            session_regenerate_id(true);
            $_SESSION['credential_version'] = hash('sha256', $newHash);
            $message = 'Password changed.';
        } else {
            $message = 'The password changed in another request. Retry with your current password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Store Management - Change password</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div id="content">
    <h1>Change password</h1>
    <?php if ($message !== ''): ?><p role="status"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <form id="login-form" method="post">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['password_csrf'], ENT_QUOTES, 'UTF-8') ?>">
        <p><label for="old-pass">Current password</label>
            <input id="old-pass" name="old_pass" type="password" autocomplete="current-password" required></p>
        <p><label for="new-pass">New password</label>
            <input id="new-pass" name="new_pass" type="password" autocomplete="new-password" minlength="8" maxlength="72" required></p>
        <p><label for="confirm-pass">Confirm new password</label>
            <input id="confirm-pass" name="confirm_pass" type="password" autocomplete="new-password" minlength="8" maxlength="72" required></p>
        <button type="submit" class="button round blue">Save password</button>
        <a href="dashboard.php">Dashboard</a>
    </form>
</div>
</body>
</html>
