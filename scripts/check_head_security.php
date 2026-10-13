<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Static Security Verification: Head Security Inclusion
 * Specification: Phase 1.3
 *
 * Verifies that every PHP file containing '<html' includes 'includes/head_security.php'.
 * Exits with status 1 if any page is missing the shared security head.
 */

$rootDir = realpath(__DIR__ . '/..');
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir, FilesystemIterator::SKIP_DOTS)
);

$missing = [];
$totalChecked = 0;

$excludedDirs = [
    'node_modules',
    '.git',
    'tests',
    'scratch',
    '.system_generated',
    'includes',
    'config',
    'scripts'
];

foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $filePath = str_replace('\\', '/', $file->getPathname());
    
    // Skip excluded directories
    $skip = false;
    foreach ($excludedDirs as $dir) {
        if (strpos($filePath, '/' . $dir . '/') !== false) {
            $skip = true;
            break;
        }
    }
    if ($skip) {
        continue;
    }

    $content = file_get_contents($filePath);
    if (stripos($content, '<html') !== false) {
        $totalChecked++;
        $relPath = str_replace(str_replace('\\', '/', $rootDir) . '/', '', $filePath);

        // Check if head_security.php is included
        if (strpos($content, 'head_security.php') === false) {
            $missing[] = $relPath;
        }
    }
}

echo "========================================================================\n";
echo "      VOSTOKPRIBOR HEAD SECURITY STATIC AUDIT (PHASE 1.3)               \n";
echo "========================================================================\n";
echo "Total HTML-rendering PHP pages scanned: {$totalChecked}\n";

if (!empty($missing)) {
    echo "\n[FAIL] The following " . count($missing) . " pages are MISSING 'includes/head_security.php':\n";
    foreach ($missing as $page) {
        echo "  - {$page}\n";
    }
    echo "\nExiting with status code 1.\n";
    exit(1);
}

echo "[PASS] All {$totalChecked} HTML pages properly include 'includes/head_security.php'!\n";
echo "========================================================================\n";
exit(0);
