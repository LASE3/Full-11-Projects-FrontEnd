<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR API Authorization Coverage Scanner
 * Location: scripts/list_unauthorized_endpoints.php
 *
 * Scans every [dir]/api/*.php and api/v1/* endpoint handling POST/PUT/PATCH/DELETE
 * or by-id GET requests to ensure vp_authorize() is called.
 * Exits with status code 1 if any unauthorized write or by-id read endpoints remain.
 */

$root = dirname(__DIR__);
$apiFiles = [];

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
foreach ($it as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $path = str_replace('\\', '/', $file->getPathname());
    
    // Ignore vendor, node_modules, tests, storage, scripts
    if (preg_match('#/(vendor|node_modules|tests|storage|scripts|\.git)/#', $path)) {
        continue;
    }

    // Match */api/*.php and api/v1/*
    if (preg_match('#(/api/|/api/v1/)#', $path)) {
        $apiFiles[] = $path;
    }
}

sort($apiFiles);

$unauthorized = [];
$exemptFiles = [
    // Helper classes, not HTTP endpoints
    'db.php',
    'db_helper.php',
    'AuditLogger.php',
    'I18n.php',
    'Response.php',
    'Validator.php',
    'CustomerSession.php',
    // Public unauthenticated endpoints
    'csrf.php',
    'auth.php',
    'check_auth.php',
    'logout.php',
    'contact.php',
    'careers.php',
];

foreach ($apiFiles as $filePath) {
    $baseName = basename($filePath);
    if (in_array($baseName, $exemptFiles, true)) {
        continue;
    }
    if (str_contains($filePath, '/helpers/')) {
        continue;
    }

    $content = file_get_contents($filePath);
    if ($content === false) {
        continue;
    }

    // Check if file handles POST, PUT, PATCH, DELETE or by-id GET
    $handlesWrite = (bool)preg_match('/(POST|PUT|PATCH|DELETE|\$_POST|INSERT\s+INTO|UPDATE\s+|DELETE\s+FROM)/i', $content);
    $handlesById = (bool)preg_match('/\$_GET\[\s*[\'"](?:id|cus_id|emp_id|tkt_id|doc_id|inv_id|opp_id|lead_id|quote_id|contract_id|project_id|cred_id|key_id|account_id)[\'"]\s*\]/i', $content);

    if (!$handlesWrite && !$handlesById) {
        continue;
    }

    // Check if vp_authorize is called
    $callsAuthorize = (bool)preg_match('/\bvp_authorize\s*\(/', $content);

    if (!$callsAuthorize) {
        $relPath = str_replace($root . '/', '', $filePath);
        $reasons = [];
        if ($handlesWrite) $reasons[] = 'handles write (POST/PUT/PATCH/DELETE/mutation)';
        if ($handlesById) $reasons[] = 'handles by-id GET';
        $unauthorized[] = [
            'file' => $relPath,
            'reasons' => implode(', ', $reasons),
        ];
    }
}

echo "========================================================================\n";
echo "      VOSTOKPRIBOR API AUTHORIZATION COVERAGE AUDIT                     \n";
echo "========================================================================\n\n";

if (empty($unauthorized)) {
    echo "  [PASS] All " . count($apiFiles) . " API files properly enforce authorization (0 unwired).\n\n";
    echo "========================================================================\n";
    echo "                      AUDIT RESULT: PASS (0 UNWIRED)\n";
    echo "========================================================================\n";
    exit(0);
}

echo "  [FAIL] Found " . count($unauthorized) . " API endpoints lacking vp_authorize:\n\n";
foreach ($unauthorized as $u) {
    echo "  - {$u['file']} ({$u['reasons']})\n";
}

echo "\n========================================================================\n";
echo "                      AUDIT RESULT: FAIL (" . count($unauthorized) . " UNWIRED)\n";
echo "========================================================================\n";
exit(1);
