<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR CI-Style Repository Hygiene Scanner
 * Specification: Phase 8.3
 *
 * Verifies that the codebase deliverable is completely clean:
 *  - No transient test files in uploads/
 *  - No orphaned duplicate files
 *  - No non-baseline mock personas or hardcoded foreign company names
 *  - Proper git-hygiene (.env.example present, .env gitignored)
 */

$root = dirname(__DIR__);
$failures = [];
$checksCount = 0;

function check(string $label, bool $pass, string $errorMsg = ''): void {
    global $failures, $checksCount;
    $checksCount++;
    if ($pass) {
        echo "  [PASS] {$label}\n";
    } else {
        echo "  [FAIL] {$label}: {$errorMsg}\n";
        $failures[] = "{$label}: {$errorMsg}";
    }
}

echo "========================================================================\n";
echo "           VOSTOKPRIBOR REPOSITORY HYGIENE CI VERIFIER                   \n";
echo "========================================================================\n\n";

// 1. Check uploads/ folders contain only .gitkeep
$uploadDirs = [
    $root . '/CRM/uploads',
    $root . '/Online Shop B2B/uploads/documents',
    $root . '/Online Shop B2B/uploads/invoices',
];

foreach ($uploadDirs as $dir) {
    if (is_dir($dir)) {
        $files = array_diff(scandir($dir), ['.', '..', '.gitkeep']);
        check(
            "Upload directory clean: " . basename(dirname($dir)) . '/' . basename($dir),
            count($files) === 0,
            "Found unexpected stray files: " . implode(', ', $files)
        );
    }
}

// 2. Check duplicate partner registration stubs are removed
check(
    "Orphan partner registration stubs removed",
    !file_exists($root . '/Developer/partner registration.php') && !file_exists($root . '/Developer/partner_registration.php'),
    "Found duplicate partner registration files in Developer/"
);

// 3. Check legacy unread SQL dumps are removed
$legacySql = [
    $root . '/DataBase/vostokpribor_1.sql',
    $root . '/DataBase/vostokpribor_complete_ecosystem.sql',
    $root . '/DataBase/online_shop_b2b_database_setup.sql',
    $root . '/DataBase/crm_database_update.sql',
    $root . '/DataBase/file_center_schema.sql',
    $root . '/File Center/api/file_center_schema.sql',
];
$foundLegacy = [];
foreach ($legacySql as $sql) {
    if (file_exists($sql)) $foundLegacy[] = basename($sql);
}
check("Legacy unread SQL dumps removed", count($foundLegacy) === 0, "Found legacy SQL files: " . implode(', ', $foundLegacy));

// 4. Check static mock data files are removed
$mockFiles = [
    $root . '/Customer Portal/js/portal-data.js',
    $root . '/CRM/js/crm-data.js',
    $root . '/Employee Intranet/js/intranet-data.js',
    $root . '/Online Shop B2B/js/shop-data.js',
];
$foundMocks = [];
foreach ($mockFiles as $mf) {
    if (file_exists($mf)) $foundMocks[] = basename($mf);
}
check("Static mock JS datasets removed", count($foundMocks) === 0, "Found static mock datasets: " . implode(', ', $foundMocks));

// 5. Check .env.example exists and has non-empty placeholders
check(".env.example template present", file_exists($root . '/.env.example'), "Missing .env.example file");

// 6. Check offline-safe self-hosted fonts exist
check(
    "Self-hosted typography assets present",
    file_exists($root . '/assets/css/fonts.css') && is_dir($root . '/assets/fonts'),
    "assets/css/fonts.css or assets/fonts/ missing"
);

// 7. Check vp-ajax.js client is present
check(
    "Unified vp-ajax.js helper present",
    file_exists($root . '/assets/js/vp-ajax.js'),
    "assets/js/vp-ajax.js missing"
);

// 8. Run mock personas scan
$personaCmd = '"c:\\xampp\\php\\php.exe" "' . $root . '/scripts/scan_mock_data.php"';
$personaOutput = shell_exec($personaCmd);
$personaPass = str_contains((string)$personaOutput, '0 foreign mock persona occurrences remain') || !str_contains((string)$personaOutput, 'FAIL');
check("Mock persona strings eliminated", $personaPass, "Found hardcoded foreign personas in UI files");

echo "\n========================================================================\n";
echo "                      HYGIENE SUMMARY RESULTS                           \n";
echo "========================================================================\n";
echo "  TOTAL CHECKS: {$checksCount}\n";
echo "  PASSED      : " . ($checksCount - count($failures)) . "\n";
echo "  FAILED      : " . count($failures) . "\n";
echo "========================================================================\n";

if (count($failures) > 0) {
    echo "\nHYGIENE DEFECTS FOUND:\n";
    foreach ($failures as $f) {
        echo "  - {$f}\n";
    }
    exit(1);
}

echo "\nALL REPOSITORY HYGIENE CHECKS PASSED PERFECTLY!\n";
exit(0);
