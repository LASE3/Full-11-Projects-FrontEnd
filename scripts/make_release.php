<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Enterprise Ecosystem — Release Archive Builder
 * Creates a clean, production-ready deliverable archive.
 *
 * Excludes:
 *  - Version control (.git)
 *  - Developer environments / IDEs (.vscode, .idea, .gemini)
 *  - Dependencies (node_modules)
 *  - Secrets & local credentials (.env, storage/credentials/)
 *  - Temporary files, test outputs, scratch dumps (*.log, *.tmp, scratch/)
 *  - Stray upload files (keeps only .gitkeep in upload directories)
 *  - Existing release archives (*.zip)
 */

if (!extension_loaded('zip')) {
    fwrite(STDERR, "Error: PHP Zip extension is required to build the release archive.\n");
    exit(1);
}

$rootDir = dirname(__DIR__);
$outputZip = $argv[1] ?? ($rootDir . '/vostokpribor_release.zip');

if (file_exists($outputZip)) {
    unlink($outputZip);
}

$zip = new ZipArchive();
if ($zip->open($outputZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    fwrite(STDERR, "Error: Could not create zip archive at: {$outputZip}\n");
    exit(1);
}

$excludedPatterns = [
    '/^\.git(\/|\\\\|$)/',
    '/^\.vscode(\/|\\\\|$)/',
    '/^\.idea(\/|\\\\|$)/',
    '/^\.gemini(\/|\\\\|$)/',
    '/^node_modules(\/|\\\\|$)/',
    '/^_trash(\/|\\\\|$)/',
    '/^scratch(\/|\\\\|$)/',
    '/^storage\/credentials(\/|\\\\|$)/',
    '/^\.env$/',
    '/^vostokpribor_release\.zip$/',
    '/\.zip$/i',
    '/\.log$/i',
    '/playwright-report(\/|\\\\|$)/',
    '/test-results(\/|\\\\|$)/',
];

$uploadDirPatterns = [
    'CRM/uploads/',
    'Online Shop B2B/uploads/',
    'File Center/uploads/',
];

$filesAdded = 0;
$totalBytes = 0;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $item) {
    $realPath = $item->getRealPath();
    $relPath = str_replace('\\', '/', substr($realPath, strlen(realpath($rootDir)) + 1));
    
    // Check exclusion patterns
    $skip = false;
    foreach ($excludedPatterns as $pattern) {
        if (preg_match($pattern, $relPath)) {
            $skip = true;
            break;
        }
    }
    if ($skip) {
        continue;
    }

    // Handle uploads directory: only include .gitkeep
    foreach ($uploadDirPatterns as $upPattern) {
        if (str_starts_with($relPath, $upPattern) && basename($relPath) !== '.gitkeep') {
            $skip = true;
            break;
        }
    }
    if ($skip) {
        continue;
    }

    if ($item->isDir()) {
        $zip->addEmptyDir($relPath);
    } elseif ($item->isFile()) {
        $zip->addFile($realPath, $relPath);
        $filesAdded++;
        $totalBytes += $item->getSize();
    }
}

$zip->close();

$archiveSize = filesize($outputZip);

echo "========================================================================\n";
echo "              VOSTOKPRIBOR RELEASE ARCHIVE GENERATOR                    \n";
echo "========================================================================\n";
echo "  Archive Destination : " . basename($outputZip) . "\n";
echo "  Files Packaged      : {$filesAdded}\n";
echo "  Uncompressed Size   : " . number_format($totalBytes / 1024 / 1024, 2) . " MB\n";
echo "  Compressed Archive  : " . number_format($archiveSize / 1024 / 1024, 2) . " MB\n";
echo "========================================================================\n";
echo "RELEASE ARCHIVE BUILT SUCCESSFULLY!\n";
