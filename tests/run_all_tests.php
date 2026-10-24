<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Master Test Suite Entry Point
 * Executes complete acceptance suite and validation matrix.
 */

$phpBin = getenv('PHP_BINARY') ?: (defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : 'php');

$suites = [
    'Acceptance Suite' => __DIR__ . '/test_all_acceptance.php',
    'Input Length Validation' => __DIR__ . '/test_input_length_validation.php',
    'Customer Password Isolation' => __DIR__ . '/test_customer_password_isolation.php',
    'Isolation Matrix' => __DIR__ . '/test_isolation_matrix.php',
    'Password Policy' => __DIR__ . '/test_password_policy.php',
    'Migration 016' => __DIR__ . '/test_migration_016.php',
    'Maker-Checker' => __DIR__ . '/test_maker_checker.php',
    'Phase 4 Ownership' => __DIR__ . '/test_phase4_ownership_and_developer.php',
    'Touch Target CSS' => __DIR__ . '/test_touch_target_css.php',
    'Security Headers & Cookie Hardening' => __DIR__ . '/test_security_headers_cookies.php',
    'SQL Injection Regression' => __DIR__ . '/test_sqli_regression.php',
];

$failedSuites = [];

foreach ($suites as $name => $suiteFile) {
    echo "\n>>> RUNNING: {$name} (" . basename($suiteFile) . ")\n";
    $exitCode = 0;
    passthru(escapeshellarg($phpBin) . ' ' . escapeshellarg($suiteFile), $exitCode);
    if ($exitCode !== 0) {
        $failedSuites[] = $name;
    }
}

if (!empty($failedSuites)) {
    echo "\n[FAIL] Test suites failed: " . implode(', ', $failedSuites) . "\n";
    exit(1);
}

echo "\n[PASS] All test suites passed successfully!\n";
exit(0);
