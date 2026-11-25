<?php

function loginEmail(mixed $value): ?string
{
    if (!is_string($value)) {
        return null;
    }
    $value = strtolower(trim($value));
    return strlen($value) <= 254 && filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : null;
}

function validStorePassword(mixed $value): bool
{
    return is_string($value) && strlen($value) <= 72
        && !str_contains($value, "\0") && preg_match('/\A.{8,}\z/us', $value) === 1;
}

function storeAuthDb(): mysqli
{
    if (!is_file(__DIR__ . '/../config.php')) {
        throw new RuntimeException('Missing config.php. Follow the shell setup in README.md.');
    }
    require __DIR__ . '/../config.php';
    $db = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);
    $db->set_charset('utf8mb4');
    return $db;
}
