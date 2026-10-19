<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Environment & Configuration Preflight Checker
 * Location: scripts/check_env.php
 *
 * Verifies required environment variables before any build or test steps run.
 * Prints clear diagnostics in seconds if any required secret or parameter is missing.
 */

echo "========================================================================\n";
echo "       VOSTOKPRIBOR ENVIRONMENT & CONFIGURATION PREFLIGHT CHECK          \n";
echo "========================================================================\n\n";

// Load .env if present (local development convenience)
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') continue;
            $pos = strpos($line, '=');
            if ($pos === false) continue;
            $k = trim(substr($line, 0, $pos));
            $v = trim(substr($line, $pos + 1));
            if (strlen($v) >= 2 && (($v[0] === '"' && $v[-1] === '"') || ($v[0] === "'" && $v[-1] === "'"))) {
                $v = substr($v, 1, -1);
            }
            if ($k !== '' && getenv($k) === false) {
                putenv("{$k}={$v}");
                $_ENV[$k] = $v;
            }
        }
    }
}

$required = [
    'DB_USER' => [
        'desc' => 'Database application user (e.g. vostok_app)',
        'validator' => fn($val) => !empty(trim((string)$val)),
        'error' => 'Must not be empty',
    ],
    'DB_PASS' => [
        'desc' => 'Database application password',
        'validator' => fn($val) => $val !== false && $val !== null,
        'error' => 'Must be defined (can be empty string for blank root)',
    ],
    'VOSTOK_SSO_SECRET' => [
        'desc' => 'SSO cryptographic signing secret (>=32 chars)',
        'validator' => fn($val) => is_string($val) && strlen($val) >= 32,
        'error' => 'Must be at least 32 characters long',
    ],
];

$optional = [
    'APP_ENV' => 'Runtime environment (local, test, production)',
    'BASE_URL' => 'Application base URL (e.g. http://127.0.0.1:8080/)',
    'TEST_ADMIN_PASSWORD' => 'Seeded test SuperAdmin credential',
    'DB_HOST' => 'Database hostname (default: 127.0.0.1)',
    'DB_PORT' => 'Database port (default: 3306)',
    'DB_NAME' => 'Primary database name (default: vostokpribor)',
    'TEST_DB_NAME' => 'Testing database name (default: vostokpribor_test)',
];

$errors = [];

printf("%-22s | %-10s | %-40s\n", "Variable", "Status", "Details");
echo str_repeat("-", 80) . "\n";

foreach ($required as $var => $spec) {
    $val = getenv($var);
    if ($val === false || $val === null) {
        printf("%-22s | %-10s | %-40s\n", $var, "MISSING", "Required variable is not set!");
        $errors[] = "Required variable '{$var}' is not set. {$spec['desc']}.";
    } elseif (!($spec['validator']($val))) {
        printf("%-22s | %-10s | %-40s\n", $var, "INVALID", $spec['error']);
        $errors[] = "Variable '{$var}' is invalid: {$spec['error']}.";
    } else {
        $masked = ($var === 'DB_PASS' || $var === 'VOSTOK_SSO_SECRET')
            ? substr((string)$val, 0, 4) . '...' . substr((string)$val, -4) . ' (len ' . strlen((string)$val) . ')'
            : (string)$val;
        printf("%-22s | %-10s | %-40s\n", $var, "OK", $masked);
    }
}

foreach ($optional as $var => $desc) {
    $val = getenv($var);
    if ($val === false || $val === null) {
        printf("%-22s | %-10s | %-40s\n", $var, "OPTIONAL", "(not set - will use default)");
    } else {
        $display = ($var === 'TEST_ADMIN_PASSWORD')
            ? substr((string)$val, 0, 3) . '*** (len ' . strlen((string)$val) . ')'
            : (string)$val;
        printf("%-22s | %-10s | %-40s\n", $var, "OK", $display);
    }
}

echo str_repeat("-", 80) . "\n\n";

if (!empty($errors)) {
    fwrite(STDERR, "[FATAL] Preflight environment check FAILED with " . count($errors) . " error(s):\n");
    foreach ($errors as $e) {
        fwrite(STDERR, "  - {$e}\n");
    }
    fwrite(STDERR, "\nPlease set the missing environment variable(s) or configure your CI workflow.\n");
    exit(1);
}

// Verify config/db.php loads cleanly
try {
    require_once dirname(__DIR__) . '/config/db.php';
    echo "[PASS] config/db.php loaded and validated successfully without exceptions.\n";
} catch (Throwable $t) {
    fwrite(STDERR, "[FATAL] config/db.php threw an uncaught exception: " . $t->getMessage() . "\n");
    exit(1);
}

echo "[PASS] All required environment configurations verified successfully.\n";
exit(0);
