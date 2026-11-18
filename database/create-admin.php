<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/connection.php';

if ($argc !== 4) {
    fwrite(STDERR, "Usage: php database/create-admin.php <username> <email> <password>\n");
    exit(1);
}

[$script, $username, $email, $password] = $argv;
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
    fwrite(STDERR, "Use a valid email and a password of at least 12 characters.\n");
    exit(1);
}

try {
    Database::getInstance()->register('admin', $username, $password, ['email' => $email]);
    fwrite(STDOUT, "Admin account created.\n");
} catch (Throwable $exception) {
    fwrite(STDERR, "Could not create the admin account. Check for a duplicate username or email.\n");
    exit(1);
}