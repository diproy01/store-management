<?php
session_start();
require_once __DIR__ . '/lib/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
$email = loginEmail($_POST['email'] ?? null);
$password = $_POST['password'] ?? null;
if ($email !== null && validStorePassword($password)) {
    try {
        $db = storeAuthDb();
        $user = $db->execute_query(
            "SELECT id, username, password FROM stock_user WHERE email = ? AND user_type = 'admin'",
            [$email]
        )->fetch_assoc();
        if ($user && $user['password'] !== null && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION = ['id' => (int) $user['id'], 'username' => $user['username'], 'usertype' => 'admin',
                'credential_version' => hash('sha256', $user['password'])];
            header('Location: add_sales.php', true, 303);
            exit;
        }
    } catch (mysqli_sql_exception | RuntimeException $exception) {
        error_log('Store Management login: ' . $exception->getMessage());
        header('Location: index.php?unavailable=1', true, 303);
        exit;
    }
}
header('Location: index.php?invalid=1', true, 303);
