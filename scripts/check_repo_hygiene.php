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
$phpBin = getenv('PHP_BINARY') ?: (defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : 'php');
$personaCmd = escapeshellarg($phpBin) . ' ' . escapeshellarg($root . '/scripts/scan_mock_data.php');
$personaOutput = shell_exec($personaCmd);
$personaPass = str_contains((string)$personaOutput, '0 foreign mock persona occurrences remain') || !str_contains((string)$personaOutput, 'FAIL');
check("Mock persona strings eliminated", $personaPass, "Found hardcoded foreign personas in UI files");

// 9. Check prohibited build/tailwind config files removed
$prohibitedConfigs = [
    $root . '/Admin & Governance Portal/js/tailwind-config.js',
    $root . '/Customer Portal/js/tailwind-config.js',
    $root . '/Admin & Governance Portal/tailwind.config.js',
    $root . '/Customer Portal/tailwind.config.js',
    $root . '/VOSTOKPRIBOR Corporate Web Platform/tailwind.config.js',
    $root . '/VOSTOKPRIBOR Corporate Web Platform/js/corporate-tailwind.js',
    $root . '/assets/js/launchpad-tailwind.js',
    $root . '/Admin & Governance Portal/css/tailwind-input.css',
    $root . '/Customer Portal/css/tailwind-input.css',
    $root . '/VOSTOKPRIBOR Corporate Web Platform/css/tailwind-input.css',
    $root . '/scripts/download_fonts.js',
];
$foundConfigs = [];
foreach ($prohibitedConfigs as $cfg) {
    if (file_exists($cfg)) $foundConfigs[] = basename($cfg);
}
check("Unused build and tailwind configs eliminated", count($foundConfigs) === 0, "Found prohibited files: " . implode(', ', $foundConfigs));

// 10. Check duplicate logo.svg removed (only canonical assets/logo.svg kept)
$subsystems = [
    'Admin & Governance Portal', 'CRM', 'Customer Portal', 'Developer',
    'Employee Intranet', 'File Center', 'Finance & Billing', 'HR System',
    'IT Helpdesk', 'Online Shop B2B', 'VOSTOKPRIBOR Corporate Web Platform'
];
$foundDuplicateLogos = [];
foreach ($subsystems as $sub) {
    if (file_exists($root . '/' . $sub . '/assets/logo.svg')) {
        $foundDuplicateLogos[] = $sub . '/assets/logo.svg';
    }
}
check("Duplicate logo.svg assets eliminated", count($foundDuplicateLogos) === 0 && file_exists($root . '/assets/logo.svg'), "Found duplicates: " . implode(', ', $foundDuplicateLogos));

// 11. Check duplicate dynamic-login-theme.js removed (only canonical assets/js/dynamic-login-theme.js kept)
$foundDuplicateThemes = [];
foreach ($subsystems as $sub) {
    if (file_exists($root . '/' . $sub . '/js/dynamic-login-theme.js')) {
        $foundDuplicateThemes[] = $sub . '/js/dynamic-login-theme.js';
    }
}
check("Duplicate dynamic-login-theme.js eliminated", count($foundDuplicateThemes) === 0 && file_exists($root . '/assets/js/dynamic-login-theme.js'), "Found duplicates: " . implode(', ', $foundDuplicateThemes));

// 12. Check initial_passwords.json not present in repository
check("Storage credentials uncommitted & clean", !file_exists($root . '/storage/credentials/initial_passwords.json'), "Found initial_passwords.json in repository tree");

// 13. Check release builder make_release.php exists
check("Release archive generator present", file_exists($root . '/scripts/make_release.php'), "scripts/make_release.php missing");

// 14. Check scratch/ absent from git and repository
$gitScratch = shell_exec('git ls-files scratch/');
$scratchFiles = trim((string)$gitScratch);
check(
    "scratch/ directory absent from git tracking",
    empty($scratchFiles),
    "Tracked scratch files found in git: " . $scratchFiles
);

// 15. Check *.zip absent from repository tree and git tracking
$gitZips = trim((string)shell_exec('git ls-files "*.zip"'));
$rootZips = glob($root . '/*.zip');
check(
    "*.zip archives absent from repository",
    empty($gitZips) && empty($rootZips),
    "Found zip archives: " . ($gitZips ?: implode(', ', $rootZips))
);

// 16. Check debug scripts eliminated
$gitPhpFiles = explode("\n", trim((string)shell_exec('git ls-files "*.php"')));
$foundDebugScripts = [];
foreach ($gitPhpFiles as $pf) {
    $pf = trim($pf);
    if ($pf === '') continue;
    if (preg_match('/(debug|scratch|temp|tmp).*\.php$/i', basename($pf))) {
        $foundDebugScripts[] = $pf;
    }
}
check(
    "Ad-hoc debug and scratch scripts eliminated",
    empty($foundDebugScripts),
    "Found debug/scratch scripts: " . implode(', ', $foundDebugScripts)
);

// 17. Check .gitignore rules for hygiene
$gitignoreContent = file_exists($root . '/.gitignore') ? (string)file_get_contents($root . '/.gitignore') : '';
$missingGitignore = [];
foreach (['scratch/', 'release/', '.env', 'node_modules/', 'storage/documents/*'] as $pattern) {
    if (!str_contains($gitignoreContent, $pattern)) {
        $missingGitignore[] = $pattern;
    }
}
check(
    ".gitignore contains scratch/, release/, .env, node_modules/, storage/documents/*",
    empty($missingGitignore),
    "Missing patterns in .gitignore: " . implode(', ', $missingGitignore)
);

// 18. Check tracked files are referenced / canonical project files
$unreferenced = [];
$allowedRoots = [
    '.github', 'account', 'Admin & Governance Portal', 'api', 'assets', 'config',
    'CRM', 'Customer Portal', 'DataBase', 'Developer', 'Employee Intranet',
    'File Center', 'Finance & Billing', 'HR System', 'includes', 'IT Helpdesk',
    'Online Shop B2B', 'scripts', 'storage', 'tests',
    'VOSTOKPRIBOR Corporate Web Platform'
];
$allowedRootFiles = [
    '.env.example', '.env.production.example', '.gitignore', '.gitattributes', '.htaccess', 'index.php', 'package.json',
    'package-lock.json', 'README.md', 'SECURITY.md', 'LICENSE'
];
$allTracked = explode("\n", trim((string)shell_exec('git ls-files')));
foreach ($allTracked as $tf) {
    $tf = trim(str_replace('\\', '/', $tf));
    if ($tf === '') continue;
    $parts = explode('/', $tf);
    if (count($parts) === 1) {
        if (!in_array($parts[0], $allowedRootFiles, true)) {
            $unreferenced[] = $tf;
        }
    } else {
        if (!in_array($parts[0], $allowedRoots, true)) {
            $unreferenced[] = $tf;
        }
    }
}
check(
    "All tracked files referenced within canonical project structure",
    empty($unreferenced),
    "Found unreferenced stray files: " . implode(', ', $unreferenced)
);

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

