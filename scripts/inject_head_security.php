<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR - Injects includes/head_security.php into all HTML-rendering PHP pages.
 * Specification: Phase 1.1 & 1.3
 */

$rootDir = realpath(__DIR__ . '/..');
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir, FilesystemIterator::SKIP_DOTS)
);

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

$updatedCount = 0;

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
    if (stripos($content, '<html') === false) {
        continue;
    }

    // Already includes head_security.php
    if (strpos($content, 'head_security.php') !== false) {
        continue;
    }

    $relPath = str_replace(str_replace('\\', '/', $rootDir) . '/', '', $filePath);
    $isRoot = (dirname($relPath) === '.');
    $includeCode = $isRoot
        ? "<?php require_once __DIR__ . '/includes/head_security.php'; ?>"
        : "<?php require_once __DIR__ . '/../includes/head_security.php'; ?>";

    // 1. If page already has a <meta name="csrf-token" tag, replace it
    if (preg_match('/<meta\s+name=["\']csrf-token["\'][^>]*>\s*/i', $content, $m)) {
        $newContent = str_replace($m[0], $includeCode . "\n", $content);
    } elseif (preg_match('/<head[^>]*>/i', $content, $m)) {
        // 2. Otherwise insert right after <head>
        $pos = strpos($content, $m[0]) + strlen($m[0]);
        $newContent = substr($content, 0, $pos) . "\n  " . $includeCode . substr($content, $pos);
    } else {
        echo "Warning: Could not find <head> or meta tag in {$relPath}\n";
        continue;
    }

    file_put_contents($filePath, $newContent);
    $updatedCount++;
    echo "Injected head_security.php into {$relPath}\n";
}

echo "\nTotal pages updated: {$updatedCount}\n";
