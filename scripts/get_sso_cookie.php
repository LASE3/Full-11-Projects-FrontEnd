<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

$identifier = $argv[1] ?? 'EMP-0001';
$user = queryUserByCredentials($identifier);

if (!$user) {
    fwrite(STDERR, "User not found: {$identifier}\n");
    exit(1);
}

// For testing purposes, if requested, bypass must_change_password by overriding it in cookie payload
if (isset($argv[2]) && $argv[2] === '--active') {
    $user['must_change_password'] = 0;
}

echo createSsoCookie($user);
