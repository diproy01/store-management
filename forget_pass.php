<?php
require_once __DIR__ . '/lib/account_mail.php';
session_start();

if (!is_file(__DIR__ . '/config.php')) {
    http_response_code(404);
    exit;
}
require __DIR__ . '/config.php';
$settings = storeMailSettings($config);
if ($settings === null) {
    http_response_code(404);
    exit('Email recovery is unavailable. Contact the server administrator.');
}
header('Referrer-Policy: no-referrer');
header('Cache-Control: no-store');
$_SESSION['recovery_csrf'] ??= bin2hex(random_bytes(32));
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$settingPassword = $isPost ? isset($_POST['reset_token']) : isset($_GET['token']);
$token = $isPost ? ($_POST['reset_token'] ?? '') : ($_GET['token'] ?? '');
$validToken = is_string($token) && preg_match('/\A[a-f0-9]{64}\z/', $token) === 1;
$message = '';
$isError = false;
if ($settingPassword && !$validToken) {
    $message = 'The password link is invalid. Request a new link.';
    $isError = true;
    $settingPassword = false;
} elseif ($isPost) {
    $csrf = $_POST['csrf'] ?? null;
    if (!is_string($csrf) || !hash_equals($_SESSION['recovery_csrf'], $csrf)) {
        http_response_code(403);
        $message = 'Your session expired. Open the email link again or request a new one.';
        $isError = true;
        $settingPassword = false;
    } else {
        try {
            $db = storeAuthDb();
            if ($settingPassword) {
                $password = $_POST['password'] ?? null;
                if (!validStorePassword($password) || $password !== ($_POST['confirmation'] ?? null)) {
                    $message = 'Enter matching passwords of at least 8 characters and no more than 72 bytes.';
                    $isError = true;
                } else {
                    $db->execute_query(
                        "UPDATE stock_user SET password = ?, reset_token_hash = NULL, reset_expires_at = NULL
                         WHERE reset_token_hash = ? AND reset_expires_at > UTC_TIMESTAMP() AND user_type = 'admin'",
                        [password_hash($password, PASSWORD_DEFAULT), hash('sha256', $token)]
                    );
                    if ($db->affected_rows === 1) {
                        $_SESSION = [];
                        session_regenerate_id(true);
                        header('Location: index.php?reset=1', true, 303);
                        exit;
                    }
                    $message = 'This reset link is expired or already used. Request a new link.';
                    $settingPassword = false;
                    $isError = true;
                }
            } else {
                $message = 'If an admin account exists for that email, a reset link will be sent.';
                $email = loginEmail($_POST['email'] ?? null);
                if ($email !== null) {
                    $newToken = bin2hex(random_bytes(32));
                    $digest = hash('sha256', $newToken);
                    $db->execute_query(
                        "UPDATE stock_user SET reset_token_hash = ?, reset_expires_at = UTC_TIMESTAMP() + INTERVAL 30 MINUTE
                         WHERE email = ? AND user_type = 'admin'
                           AND (reset_expires_at IS NULL OR reset_expires_at <= UTC_TIMESTAMP() + INTERVAL 29 MINUTE)",
                        [$digest, $email]
                    );
                    if ($db->affected_rows === 1) {
                        if (!sendStorePasswordLink($settings, $email, $newToken)) {
                            $db->execute_query(
                                'UPDATE stock_user SET reset_token_hash=NULL, reset_expires_at=NULL WHERE reset_token_hash=?',
                                [$digest]
                            );
                            error_log('Store Management: password reset mail transport rejected a message.');
                        }
                    }
                }
            }
        } catch (mysqli_sql_exception | RuntimeException $exception) {
            error_log('Store Management password reset: ' . $exception->getMessage());
            $message = 'Password recovery is temporarily unavailable.';
            $isError = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Store Management - Password recovery</title>
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
<main class="auth-card" aria-labelledby="auth-title">
    <h1 id="auth-title"><?= $settingPassword ? 'Set your password' : 'Request a password reset' ?></h1>
    <?php if ($message !== ''): ?><p class="auth-message" role="<?= $isError ? 'alert' : 'status' ?>"><?= $escape($message) ?></p><?php endif; ?>
    <form action="forget_pass.php" method="post">
        <input type="hidden" name="csrf" value="<?= $escape($_SESSION['recovery_csrf']) ?>">
        <?php if ($settingPassword): ?>
            <input type="hidden" name="reset_token" value="<?= $escape($token) ?>">
            <p class="auth-hint">Use at least 8 characters and no more than 72 bytes.</p>
            <p><label for="password">New password</label>
                <input id="password" type="password" name="password" autocomplete="new-password" minlength="8" maxlength="72" required></p>
            <p><label for="confirmation">Confirm password</label>
                <input id="confirmation" type="password" name="confirmation" autocomplete="new-password" minlength="8" maxlength="72" required></p>
        <?php else: ?>
            <p><label for="email">Email address</label>
                <input id="email" type="email" name="email" autocomplete="email" required></p>
        <?php endif; ?>
        <button type="submit"><?= $settingPassword ? 'Save password' : 'Send reset link' ?></button>
        <a class="auth-link" href="index.php">Back to sign in</a>
    </form>
</main>
</body>
</html>
