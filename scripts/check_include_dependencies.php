<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Static Include Dependency Checker
 * Specification: Phase 1.2
 *
 * Enforces rule: Any PHP file calling a function defined in includes/*
 * must load it (directly or through includes/api_bootstrap.php / includes/auth_guard.php).
 */

$rootDir = realpath(__DIR__ . '/..');
echo "====================================================\n";
echo " VOSTOKPRIBOR INCLUDE DEPENDENCY CHECKER            \n";
echo " Target Directory: {$rootDir}\n";
echo "====================================================\n\n";

$includesDir = $rootDir . DIRECTORY_SEPARATOR . 'includes';
$includeFiles = glob($includesDir . '/*.php');

$functionSource = [];
$bootstrapIncludes = [
    'api_bootstrap.php',
    'auth_guard.php',
    'authorize.php',
    'api_errors.php',
    'id_generator.php',
];

foreach ($includeFiles as $incPath) {
    $baseName = basename($incPath);
    $code = file_get_contents($incPath);
    $tokens = token_get_all($code);
    $count = count($tokens);

    for ($i = 0; $i < $count; $i++) {
        if (is_array($tokens[$i]) && $tokens[$i][0] === T_FUNCTION) {
            $j = $i + 1;
            while ($j < $count && is_array($tokens[$j]) && ($tokens[$j][0] === T_WHITESPACE || $tokens[$j][0] === T_COMMENT)) {
                $j++;
            }
            if ($j < $count && is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                $fnName = strtolower($tokens[$j][1]);
                $functionSource[$fnName] = $baseName;
            }
        }
    }
}

echo "Indexed " . count($functionSource) . " functions defined across includes/*.\n";

$errors = [];
$scannedCount = 0;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir, RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }

    $filePath = $file->getRealPath();
    $relPath = str_replace($rootDir . DIRECTORY_SEPARATOR, '', $filePath);

    // Skip vendor, .git, includes itself, tests, and scripts
    if (str_starts_with($relPath, 'includes' . DIRECTORY_SEPARATOR) ||
        str_starts_with($relPath, 'vendor' . DIRECTORY_SEPARATOR) ||
        str_starts_with($relPath, '.git' . DIRECTORY_SEPARATOR) ||
        str_starts_with($relPath, 'storage' . DIRECTORY_SEPARATOR) ||
        str_starts_with($relPath, 'scripts' . DIRECTORY_SEPARATOR)) {
        continue;
    }

    $scannedCount++;
    $code = file_get_contents($filePath);
    $tokens = token_get_all($code);
    $count = count($tokens);

    // Identify what includes are loaded in this file
    $loadsBootstrap = str_contains($code, 'api_bootstrap.php');
    $loadsAuthGuard = str_contains($code, 'auth_guard.php');
    $loadsHeadSecurity = str_contains($code, 'head_security.php');

    for ($i = 0; $i < $count; $i++) {
        if (is_array($tokens[$i]) && $tokens[$i][0] === T_STRING) {
            $candidate = strtolower($tokens[$i][1]);

            if (!isset($functionSource[$candidate])) {
                continue;
            }

            // Check if this token is actually a function call: followed by '(' and not preceded by function, ->, ::
            $prev = $i - 1;
            while ($prev >= 0 && is_array($tokens[$prev]) && ($tokens[$prev][0] === T_WHITESPACE || $tokens[$prev][0] === T_COMMENT)) {
                $prev--;
            }
            if ($prev >= 0 && is_array($tokens[$prev]) && in_array($tokens[$prev][0], [T_FUNCTION, T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR, T_DOUBLE_COLON], true)) {
                continue; // Method call or definition
            }

            $next = $i + 1;
            while ($next < $count && is_array($tokens[$next]) && ($tokens[$next][0] === T_WHITESPACE || $tokens[$next][0] === T_COMMENT)) {
                $next++;
            }
            if ($next >= $count || $tokens[$next] !== '(') {
                continue; // Not a call
            }

            $sourceInc = $functionSource[$candidate];

            // Verify if source include is reachable
            $isReachable = false;
            if (str_contains($code, $sourceInc)) {
                $isReachable = true;
            } elseif (in_array($sourceInc, $bootstrapIncludes, true) && ($loadsBootstrap || $loadsAuthGuard || $loadsHeadSecurity)) {
                $isReachable = true;
            }

            if (!$isReachable) {
                $line = $tokens[$i][2];
                $errors[] = [
                    'file'     => $relPath,
                    'line'     => $line,
                    'function' => $tokens[$i][1],
                    'required' => $sourceInc,
                ];
            }
        }
    }
}

echo "Scanned {$scannedCount} application PHP files.\n";
echo "Violations found: " . count($errors) . "\n\n";

if (!empty($errors)) {
    echo "=== MISSING INCLUDE DEPENDENCIES ===\n";
    foreach ($errors as $err) {
        echo "  - [{$err['file']}:{$err['line']}]: calls '{$err['function']}()' without loading 'includes/{$err['required']}' (or api_bootstrap.php)\n";
    }
    echo "\nStatic dependency check: FAIL\n";
    exit(1);
}

echo "Static dependency check: PASS (All include dependencies satisfied)\n";
exit(0);
