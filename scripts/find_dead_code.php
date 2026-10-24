<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Dead Code Scanner & Quality Gate
 * Specification: Item 4.8
 *
 * Verifies:
 * 1. Zero remnants of "simulation bar" (vp-sim, theme-sim, simulation bar, injectSimBar, sim-status)
 * 2. Zero javascript:void(0) links in application
 * 3. Zero href="#" placeholder links in HTML/PHP views
 * 4. Zero leftover console.log / console.debug / console.info in client scripts
 * 5. Zero var_dump / var_export debug statements in application code
 * 6. Zero commented-out PHP code statements
 * 7. Zero unused global PHP functions in application code
 * 8. Zero unreferenced CSS class rules in stylesheets
 */

$root = dirname(__DIR__);
$failures = [];
$checksCount = 0;

function gateCheck(string $label, bool $pass, array $details = []): void {
    global $failures, $checksCount;
    $checksCount++;
    if ($pass) {
        echo "  [PASS] {$label}\n";
    } else {
        echo "  [FAIL] {$label}\n";
        foreach (array_slice($details, 0, 10) as $d) {
            echo "         - {$d}\n";
        }
        if (count($details) > 10) {
            echo "         ... and " . (count($details) - 10) . " more\n";
        }
        $failures[] = "{$label} (" . count($details) . " violations)";
    }
}

echo "========================================================================\n";
echo "              VOSTOKPRIBOR DEAD CODE & CLUTTER CI GATE                  \n";
echo "========================================================================\n\n";

// Gather all source files
$allFiles = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS));
foreach ($it as $f) {
    if (!$f->isFile()) continue;
    $p = str_replace('\\', '/', $f->getPathname());
    if (preg_match('#/(node_modules|vendor|\.git|\.gemini|scratch|storage|release)/#', $p)) continue;
    $allFiles[] = $p;
}

// -----------------------------------------------------------------------------
// CHECK 1: Words "simulation bar" and simulation artifacts
// -----------------------------------------------------------------------------
$simMatches = [];
foreach ($allFiles as $p) {
    $ext = pathinfo($p, PATHINFO_EXTENSION);
    if (!in_array($ext, ['php', 'js', 'css', 'html'])) continue;
    if (basename($p) === 'find_dead_code.php') continue;
    $c = file_get_contents($p);
    if (preg_match_all('/(vp-sim|theme-sim|simulation[ -]bar|injectSimBar|sim-status)/i', $c, $m, PREG_OFFSET_CAPTURE)) {
        foreach ($m[0] as $match) {
            $line = substr_count(substr($c, 0, $match[1]), "\n") + 1;
            $simMatches[] = str_replace($root . '/', '', $p) . ":$line: " . trim($match[0]);
        }
    }
}
gateCheck("Zero remnants of simulation-bar code or terminology", empty($simMatches), $simMatches);

// -----------------------------------------------------------------------------
// CHECK 2: javascript:void(0)
// -----------------------------------------------------------------------------
$voidMatches = [];
foreach ($allFiles as $p) {
    $ext = pathinfo($p, PATHINFO_EXTENSION);
    if (!in_array($ext, ['php', 'html', 'js'])) continue;
    if (basename($p) === 'find_dead_code.php') continue;
    $c = file_get_contents($p);
    if (stripos($c, 'javascript:void(0)') !== false) {
        $lines = explode("\n", $c);
        foreach ($lines as $i => $l) {
            if (stripos($l, 'javascript:void(0)') !== false) {
                $voidMatches[] = str_replace($root . '/', '', $p) . ":" . ($i + 1) . ": " . trim($l);
            }
        }
    }
}
gateCheck("Zero javascript:void(0) links in application", empty($voidMatches), $voidMatches);

// -----------------------------------------------------------------------------
// CHECK 3: href="#"
// -----------------------------------------------------------------------------
$hashMatches = [];
foreach ($allFiles as $p) {
    $ext = pathinfo($p, PATHINFO_EXTENSION);
    if (!in_array($ext, ['php', 'html'])) continue;
    if (basename($p) === 'find_dead_code.php') continue;
    $c = file_get_contents($p);
    if (preg_match_all('/href=[\'"]#[\'"]/', $c, $m, PREG_OFFSET_CAPTURE)) {
        foreach ($m[0] as $match) {
            $line = substr_count(substr($c, 0, $match[1]), "\n") + 1;
            $hashMatches[] = str_replace($root . '/', '', $p) . ":$line";
        }
    }
}
gateCheck("Zero href=\"#\" placeholder links in HTML/PHP views", empty($hashMatches), $hashMatches);

// -----------------------------------------------------------------------------
// CHECK 4: console.log / console.debug / console.info
// -----------------------------------------------------------------------------
$consoleMatches = [];
foreach ($allFiles as $p) {
    $ext = pathinfo($p, PATHINFO_EXTENSION);
    if (!in_array($ext, ['js', 'php'])) continue;
    // Exclude tests and CI scripts that run in node/cli
    if (preg_match('#/(tests|scripts)/#', $p)) continue;
    if (basename($p) === 'find_dead_code.php') continue;
    $c = file_get_contents($p);
    if (preg_match_all('/console\.(log|debug|info)\s*\(/', $c, $m, PREG_OFFSET_CAPTURE)) {
        foreach ($m[0] as $match) {
            $line = substr_count(substr($c, 0, $match[1]), "\n") + 1;
            $consoleMatches[] = str_replace($root . '/', '', $p) . ":$line: " . trim($match[0]);
        }
    }
}
gateCheck("Zero leftover console.log / debug / info in client scripts", empty($consoleMatches), $consoleMatches);

// -----------------------------------------------------------------------------
// CHECK 5: var_dump / print_r in application PHP files
// -----------------------------------------------------------------------------
$dumpMatches = [];
foreach ($allFiles as $p) {
    if (pathinfo($p, PATHINFO_EXTENSION) !== 'php') continue;
    if (preg_match('#/(tests|scripts)/#', $p)) continue;
    if (basename($p) === 'find_dead_code.php') continue;
    $c = file_get_contents($p);
    $tokens = token_get_all($c);
    foreach ($tokens as $idx => $tok) {
        if (is_array($tok) && $tok[0] === T_STRING && in_array(strtolower($tok[1]), ['var_dump', 'var_export'])) {
            $dumpMatches[] = str_replace($root . '/', '', $p) . ":{$tok[2]}: {$tok[1]}";
        }
    }
}
gateCheck("Zero var_dump / var_export debug statements in application code", empty($dumpMatches), $dumpMatches);

// -----------------------------------------------------------------------------
// CHECK 6: Commented-out executable code blocks
// -----------------------------------------------------------------------------
$commentedCode = [];
foreach ($allFiles as $p) {
    if (pathinfo($p, PATHINFO_EXTENSION) !== 'php') continue;
    if (preg_match('#/(tests|scripts|DataBase/migrations)/#', $p)) continue;
    if (basename($p) === 'find_dead_code.php') continue;
    $c = file_get_contents($p);
    $lines = explode("\n", $c);
    foreach ($lines as $i => $l) {
        $trimmed = trim($l);
        if (preg_match('/^(\/\/|\#)\s*(\$[a-zA-Z0-9_]+(\s*=\s*|\->)|require_once|include_once|header\(|echo\s+[\'"])/', $trimmed)) {
            $commentedCode[] = str_replace($root . '/', '', $p) . ":" . ($i + 1) . ": " . $trimmed;
        }
    }
}
gateCheck("Zero commented-out PHP code statements", empty($commentedCode), $commentedCode);

// -----------------------------------------------------------------------------
// CHECK 7: Unused Global PHP Functions
// -----------------------------------------------------------------------------
$globalFuncs = [];
$sourceConcat = '';
foreach ($allFiles as $p) {
    $ext = pathinfo($p, PATHINFO_EXTENSION);
    if (!in_array($ext, ['php', 'js'])) continue;
    $c = file_get_contents($p);
    $sourceConcat .= "\n" . $c;

    if ($ext === 'php') {
        if (preg_match('#/(tests|scripts)/#', $p)) continue;
        $tokens = token_get_all($c);
        $count = count($tokens);
        $braceDepth = 0;
        $classDepths = [];
        $pendingClass = false;

        for ($i = 0; $i < $count; $i++) {
            $t = $tokens[$i];
            if (is_array($t)) {
                if ($t[0] === T_CLASS || $t[0] === T_TRAIT || $t[0] === T_INTERFACE || (defined('T_ENUM') && $t[0] === T_ENUM)) {
                    $prev = null;
                    for ($k = $i - 1; $k >= 0; $k--) {
                        if (is_array($tokens[$k]) && $tokens[$k][0] === T_WHITESPACE) continue;
                        $prev = $tokens[$k];
                        break;
                    }
                    if ($prev === null || !(is_array($prev) && $prev[0] === T_DOUBLE_COLON)) {
                        $pendingClass = true;
                    }
                } elseif ($t[0] === T_CURLY_OPEN || $t[0] === T_DOLLAR_OPEN_CURLY_BRACES) {
                    $braceDepth++;
                } elseif ($t[0] === T_FUNCTION && empty($classDepths)) {
                    for ($j = $i + 1; $j < $count; $j++) {
                        if (is_array($tokens[$j])) {
                            if ($tokens[$j][0] === T_WHITESPACE) continue;
                            if ($tokens[$j][0] === T_STRING) {
                                $funcName = $tokens[$j][1];
                                $globalFuncs[$funcName] = [
                                    'file' => str_replace($root . '/', '', $p),
                                    'line' => $tokens[$j][2]
                                ];
                            }
                            break;
                        } elseif ($tokens[$j] === '&') {
                            continue;
                        } else {
                            break;
                        }
                    }
                }
            } else {
                if ($t === '{') {
                    $braceDepth++;
                    if ($pendingClass) {
                        $classDepths[] = $braceDepth;
                        $pendingClass = false;
                    }
                } elseif ($t === '}') {
                    if (!empty($classDepths) && end($classDepths) === $braceDepth) {
                        array_pop($classDepths);
                    }
                    $braceDepth--;
                }
            }
        }
    }
}

$unusedPhp = [];
foreach ($globalFuncs as $fn => $info) {
    $pattern = '/\b' . preg_quote($fn, '/') . '\b/';
    preg_match_all($pattern, $sourceConcat, $fnMatches);
    if (count($fnMatches[0]) <= 1) {
        $unusedPhp[] = "{$info['file']}:{$info['line']}: function {$fn}()";
    }
}
gateCheck("Zero unused global PHP functions in application code", empty($unusedPhp), $unusedPhp);

// -----------------------------------------------------------------------------
// CHECK 8: Unused CSS Classes
// -----------------------------------------------------------------------------
$unusedCss = [];
$protectedPrefixes = [
    'vp-toast', 'severity-', 'stripe-', 'sla-badge-', 'badge-', 'priority-',
    'tier-strip-', 'scope-', 'status-', 'role-', 'clearance-', 'touch-target',
    'invoice-checkbox', 'customer-email', 'scope-card', 'dev-', 'auth-',
    'kpi-trend', 'nav-', 'stat-card', 'btn-', 'filter-', 'theme-'
];

$excludedCssFiles = [
    'fonts.css', 'responsive-core.css', 'auth.css', 'superAdmin.css', 'change-password.css'
];

$cssFiles = [];
foreach ($allFiles as $p) {
    if (pathinfo($p, PATHINFO_EXTENSION) !== 'css') continue;
    if (preg_match('#/(node_modules|scratch|tests)/#', $p)) continue;
    if (in_array(basename($p), $excludedCssFiles, true)) continue;
    $cssFiles[] = $p;
}

foreach ($cssFiles as $cssFile) {
    $css = file_get_contents($cssFile);
    preg_match_all('/(?:\/\*[\s\S]*?\*\/\s*)?([^{}@]+)\{([^{}]+)\}/', $css, $ruleMatches, PREG_SET_ORDER);
    foreach ($ruleMatches as $rm) {
        $selector = trim($rm[1]);
        if (str_starts_with($selector, '@')) continue;
        preg_match_all('/\.([a-zA-Z_-][a-zA-Z0-9_-]*)/', $selector, $clsMatches);
        $classes = array_unique($clsMatches[1]);
        if (empty($classes)) continue;

        $allDead = true;
        foreach ($classes as $cls) {
            foreach ($protectedPrefixes as $pref) {
                if (str_starts_with($cls, $pref)) {
                    $allDead = false;
                    break 2;
                }
            }
            if (str_contains($sourceConcat, $cls)) {
                $allDead = false;
                break;
            }
        }

        if ($allDead) {
            $unusedCss[] = str_replace($root . '/', '', $cssFile) . ": selector '{$selector}'";
        }
    }
}
gateCheck("Zero unreferenced CSS class rules in stylesheets", empty($unusedCss), $unusedCss);

// -----------------------------------------------------------------------------
// SUMMARY
// -----------------------------------------------------------------------------
echo "\n========================================================================\n";
echo "                   DEAD CODE GATE SUMMARY RESULTS                       \n";
echo "========================================================================\n";
echo "  TOTAL GATES: {$checksCount}\n";
echo "  PASSED     : " . ($checksCount - count($failures)) . "\n";
echo "  FAILED     : " . count($failures) . "\n";
echo "========================================================================\n";

if (!empty($failures)) {
    echo "\nDEAD CODE DEFECTS DETECTED:\n";
    foreach ($failures as $f) {
        echo "  - {$f}\n";
    }
    exit(1);
}

echo "\nALL DEAD CODE & CLUTTER CHECKS PASSED (0 DEFECTS)!\n";
exit(0);
