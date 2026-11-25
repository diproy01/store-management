<?php
use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/auth.php';

function isLocalAppHost(string $host): bool
{
    if (in_array(strtolower($host), ['localhost', '[::1]'], true)) {
        return true;
    }
    if (!filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return false;
    }
    [$first, $second] = array_map('intval', explode('.', $host));
    return in_array($first, [10, 127], true)
        || ($first === 172 && $second >= 16 && $second <= 31)
        || ($first === 192 && $second === 168);
}

function storeMailSettings(array $config): ?array
{
    $sender = loginEmail($config['mail_from'] ?? null);
    $url = $config['app_url'] ?? '';
    if ($sender === null || !is_string($url) || !filter_var($url, FILTER_VALIDATE_URL)
        || strpbrk($url, "?#\r\n") !== false || parse_url($url, PHP_URL_USER) !== null) {
        return null;
    }
    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
    $localHttp = $scheme === 'http' && ($config['allow_local_http'] ?? false) === true
        && isLocalAppHost((string) parse_url($url, PHP_URL_HOST));
    if ($scheme !== 'https' && !$localHttp) {
        return null;
    }

    $smtp = $config['smtp'] ?? null;
    if (!is_array($smtp)
        || !is_string($smtp['host'] ?? null) || trim($smtp['host']) === ''
        || !is_int($smtp['port'] ?? null) || $smtp['port'] < 1 || $smtp['port'] > 65535
        || !in_array($smtp['encryption'] ?? null, ['starttls', 'smtps'], true)
        || !is_string($smtp['username'] ?? null) || trim($smtp['username']) === ''
        || !is_string($smtp['password'] ?? null) || $smtp['password'] === '') {
        return null;
    }
    return ['sender' => $sender, 'url' => rtrim($url, '/'), 'smtp' => $smtp];
}

function sendStorePasswordLink(array $settings, string $email, string $token, bool $invite = false): bool
{
    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (!is_file($autoload)) {
        error_log('Store Management SMTP: Composer dependencies are missing. Run composer install.');
        return false;
    }
    require_once $autoload;

    $smtp = $settings['smtp'];
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $smtp['host'];
        $mail->Port = $smtp['port'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['username'];
        $mail->Password = $smtp['password'];
        $mail->SMTPSecure = $smtp['encryption'] === 'smtps'
            ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Timeout = 10;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($settings['sender']);
        $mail->addAddress($email);
        $mail->Subject = $invite ? 'Set your Store Management password' : 'Reset your Store Management password';
        $link = $settings['url'] . '/forget_pass.php?token=' . $token;
        $mail->Body = "Use this one-time link within 30 minutes:\n\n$link\n";
        return $mail->send();
    } catch (MailException $exception) {
        error_log('Store Management SMTP: ' . $exception->getMessage());
        return false;
    }
}
