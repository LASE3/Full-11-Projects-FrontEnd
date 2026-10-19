<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR - Touch Target Optimization CSS Integrity Gate
 * Ensures assets/css/responsive-core.css retains the "Touch Target Optimization"
 * block requiring >= 44x44px controls under @media (pointer: coarse), (max-width: 768px).
 */

$cssPath = dirname(__DIR__) . '/assets/css/responsive-core.css';

if (!file_exists($cssPath)) {
    fwrite(STDERR, "[FAIL] assets/css/responsive-core.css not found!\n");
    exit(1);
}

$css = file_get_contents($cssPath);

$errors = [];

// 1. Must contain "Touch Target Optimization" header
if (!str_contains($css, 'Touch Target Optimization')) {
    $errors[] = "Missing 'Touch Target Optimization' comment block header.";
}

// 2. Must contain media query covering pointer: coarse and max-width: 768px
if (!preg_match('/@media\s*\([^\)]*pointer\s*:\s*coarse[^\)]*\)\s*,\s*\([^\)]*max-width\s*:\s*768px[^\)]*\)/i', $css)) {
    $errors[] = "Missing '@media (pointer: coarse), (max-width: 768px)' media query block.";
}

// 3. Must specify min-height: 44px and min-width: 44px
if (!preg_match('/min-height\s*:\s*44px\s*!important/i', $css) || !preg_match('/min-width\s*:\s*44px\s*!important/i', $css)) {
    $errors[] = "Missing 'min-height: 44px !important' or 'min-width: 44px !important' rules.";
}

if (!empty($errors)) {
    fwrite(STDERR, "[FAIL] Touch Target Optimization CSS check failed:\n");
    foreach ($errors as $e) {
        fwrite(STDERR, "  - {$e}\n");
    }
    exit(1);
}

echo "[PASS] assets/css/responsive-core.css 'Touch Target Optimization' block is intact and verified.\n";
exit(0);
