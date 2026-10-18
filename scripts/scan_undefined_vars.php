<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Static Code Quality & Undefined Variable Scanner
 * Location: scripts/scan_undefined_vars.php
 *
 * Scans PHP source files for:
 * 1. Syntax errors via `php -l`
 * 2. Undefined variable reads via AST / token stream inspection
 */

$rootDir = realpath(__DIR__ . '/..');
echo "====================================================\n";
echo " VOSTOKPRIBOR STATIC VARIABLE & SYNTAX CHECKER       \n";
echo " Target Directory: {$rootDir}\n";
echo "====================================================\n\n";

$superglobals = [
    'GLOBALS', '_SERVER', '_GET', '_POST', '_FILES', '_COOKIE',
    '_SESSION', '_REQUEST', '_ENV', 'argv', 'argc', 'this', 'http_response_code'
];

$errors = [];
$syntaxErrors = [];
$totalFiles = 0;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir, RecursiveDirectoryIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }

    $filePath = $file->getRealPath();
    $relPath = str_replace($rootDir . DIRECTORY_SEPARATOR, '', $filePath);

    // Skip ignored directories
    if (str_contains($relPath, '_trash') || str_contains($relPath, '.git') || str_contains($relPath, 'vendor')) {
        continue;
    }

    $totalFiles++;

    // 1. PHP Syntax Check
    $lintOut = [];
    $lintRet = 0;
    $phpBin = getenv('PHP_BINARY') ?: (PHP_BINARY ?: 'php');
    exec(escapeshellarg($phpBin) . " -l " . escapeshellarg($filePath) . " 2>&1", $lintOut, $lintRet);
    if ($lintRet !== 0) {
        $syntaxErrors[] = "Syntax error in {$relPath}: " . implode("\n", $lintOut);
        continue;
    }

    // 2. Token Stream Inspection for Undefined Variables
    $code = file_get_contents($filePath);
    try {
        $tokens = token_get_all($code, TOKEN_PARSE);
    } catch (Throwable $e) {
        $syntaxErrors[] = "Token parse failure in {$relPath}: " . $e->getMessage();
        continue;
    }

    $assigned = array_fill_keys($superglobals, true);

    // If file includes shared context engines, import their exported context globals
    if (str_contains($code, 'customer_context.php')) {
        $assigned['pdo'] = true;
        $assigned['currentUser'] = true;
        $assigned['currUser'] = true;
        $assigned['customer'] = true;
        $assigned['cusId'] = true;
        $assigned['isCustomer'] = true;
        $assigned['canImpersonate'] = true;
        $assigned['isSuperAdmin'] = true;
        $assigned['badgeProjects'] = true;
        $assigned['badgeInvoices'] = true;
        $assigned['badgeTickets'] = true;
        $assigned['badgeDocs'] = true;
        $assigned['badgeOrders'] = true;
        $assigned['badgeServices'] = true;
    }
    if (str_contains($code, 'auth_guard.php')) {
        $assigned['currentUser'] = true;
        $assigned['currUser'] = true;
        $assigned['isAuthenticated'] = true;
    }

    $count = count($tokens);
    $inFunction = false;
    $functionAssigned = [];
    $functionParams = [];
    $bracketDepth = 0;
    $functionDepth = 0;

    for ($i = 0; $i < $count; $i++) {
        $token = $tokens[$i];

        // Track function boundaries
        if (is_array($token) && ($token[0] === T_FUNCTION || $token[0] === T_FN)) {
            // Collect function params
            $fnParams = [];
            $j = $i + 1;
            while ($j < $count && $tokens[$j] !== '(') {
                $j++;
            }
            if ($j < $count && $tokens[$j] === '(') {
                $parenDepth = 1;
                $j++;
                while ($j < $count && $parenDepth > 0) {
                    if ($tokens[$j] === '(') {
                        $parenDepth++;
                    } elseif ($tokens[$j] === ')') {
                        $parenDepth--;
                    } elseif (is_array($tokens[$j]) && $tokens[$j][0] === T_VARIABLE) {
                        $pName = substr($tokens[$j][1], 1);
                        $fnParams[$pName] = true;
                    }
                    $j++;
                }
            }
            $functionParams[] = $fnParams;
            continue;
        }

        if (is_array($token) && $token[0] === T_GLOBAL) {
            // Collect global variables
            $j = $i + 1;
            while ($j < $count && $tokens[$j] !== ';') {
                if (is_array($tokens[$j]) && $tokens[$j][0] === T_VARIABLE) {
                    $gName = substr($tokens[$j][1], 1);
                    $assigned[$gName] = true;
                    if (!empty($functionParams)) {
                        $functionParams[count($functionParams) - 1][$gName] = true;
                    }
                }
                $j++;
            }
            continue;
        }

        if (is_array($token) && $token[0] === T_CATCH) {
            // Catch variable: catch (Throwable $e)
            $j = $i + 1;
            while ($j < $count && $tokens[$j] !== ')') {
                if (is_array($tokens[$j]) && $tokens[$j][0] === T_VARIABLE) {
                    $cName = substr($tokens[$j][1], 1);
                    $assigned[$cName] = true;
                    if (!empty($functionParams)) {
                        $functionParams[count($functionParams) - 1][$cName] = true;
                    }
                }
                $j++;
            }
            continue;
        }

        // Handle foreach ($arr as $k => $v) or foreach ($arr as $v)
        if (is_array($token) && $token[0] === T_AS) {
            $j = $i + 1;
            while ($j < $count && $tokens[$j] !== ')' && $tokens[$j] !== '{' && $tokens[$j] !== ':') {
                if (is_array($tokens[$j]) && $tokens[$j][0] === T_VARIABLE) {
                    $loopVar = substr($tokens[$j][1], 1);
                    $assigned[$loopVar] = true;
                    if (!empty($functionParams)) {
                        $functionParams[count($functionParams) - 1][$loopVar] = true;
                    }
                }
                $j++;
            }
            continue;
        }

        // Handle parse_str($str, $result)
        if (is_array($token) && $token[0] === T_STRING && strtolower($token[1]) === 'parse_str') {
            $j = $i + 1;
            if ($j < $count && $tokens[$j] === '(') {
                $argIdx = 0;
                $parenLvl = 1;
                $j++;
                while ($j < $count && $parenLvl > 0) {
                    if ($tokens[$j] === '(') {
                        $parenLvl++;
                    } elseif ($tokens[$j] === ')') {
                        $parenLvl--;
                    } elseif ($tokens[$j] === ',' && $parenLvl === 1) {
                        $argIdx++;
                    } elseif ($argIdx === 1 && is_array($tokens[$j]) && $tokens[$j][0] === T_VARIABLE) {
                        $psVar = substr($tokens[$j][1], 1);
                        $assigned[$psVar] = true;
                        if (!empty($functionParams)) {
                            $functionParams[count($functionParams) - 1][$psVar] = true;
                        }
                    }
                    $j++;
                }
            }
        }

        // Handle preg_match($pattern, $subject, $matches)
        if (is_array($token) && $token[0] === T_STRING && in_array(strtolower($token[1]), ['preg_match', 'preg_match_all'], true)) {
            // Find 3rd argument
            $j = $i + 1;
            if ($j < $count && $tokens[$j] === '(') {
                $argIdx = 0;
                $parenLvl = 1;
                $j++;
                while ($j < $count && $parenLvl > 0) {
                    if ($tokens[$j] === '(') {
                        $parenLvl++;
                    } elseif ($tokens[$j] === ')') {
                        $parenLvl--;
                    } elseif ($tokens[$j] === ',' && $parenLvl === 1) {
                        $argIdx++;
                    } elseif ($argIdx === 2 && is_array($tokens[$j]) && $tokens[$j][0] === T_VARIABLE) {
                        $mVar = substr($tokens[$j][1], 1);
                        $assigned[$mVar] = true;
                        if (!empty($functionParams)) {
                            $functionParams[count($functionParams) - 1][$mVar] = true;
                        }
                    }
                    $j++;
                }
            }
        }

        // Handle array destructuring [$a, $b] = ... or list($a, $b) = ...
        if ($token === '[' || (is_array($token) && $token[0] === T_LIST)) {
            $destructVars = [];
            $j = $i + 1;
            $depth = 1;
            while ($j < $count && $depth > 0) {
                if ($tokens[$j] === '[' || $tokens[$j] === '(') {
                    $depth++;
                } elseif ($tokens[$j] === ']' || $tokens[$j] === ')') {
                    $depth--;
                } elseif (is_array($tokens[$j]) && $tokens[$j][0] === T_VARIABLE) {
                    $destructVars[] = substr($tokens[$j][1], 1);
                }
                $j++;
            }
            while ($j < $count && is_array($tokens[$j]) && ($tokens[$j][0] === T_WHITESPACE || $tokens[$j][0] === T_COMMENT)) {
                $j++;
            }
            if ($j < $count && $tokens[$j] === '=') {
                foreach ($destructVars as $dVar) {
                    $assigned[$dVar] = true;
                    if (!empty($functionParams)) {
                        $functionParams[count($functionParams) - 1][$dVar] = true;
                    }
                }
            }
        }

        // Variable detection
        if (is_array($token) && $token[0] === T_VARIABLE) {
            // Skip static class properties: self::$prop, Class::$prop
            $prevCheck = $i - 1;
            while ($prevCheck >= 0 && is_array($tokens[$prevCheck]) && ($tokens[$prevCheck][0] === T_WHITESPACE || $tokens[$prevCheck][0] === T_COMMENT)) $prevCheck--;
            if ($prevCheck >= 0 && is_array($tokens[$prevCheck]) && $tokens[$prevCheck][0] === T_DOUBLE_COLON) continue;

            $varName = substr($token[1], 1);
            $line = $token[2];

            // Look backward to check if preceded by isset, empty, static, global, or function param
            $prevNonWs = null;
            for ($k = $i - 1; $k >= 0; $k--) {
                if (!is_array($tokens[$k]) || ($tokens[$k][0] !== T_WHITESPACE && $tokens[$k][0] !== T_COMMENT && $tokens[$k][0] !== T_DOC_COMMENT)) {
                    $prevNonWs = $tokens[$k];
                    break;
                }
            }

            // Look forward to check if followed by assignment operators (=, +=, -=, etc.) or foreach
            $nextNonWs = null;
            $nextIdx = $i + 1;
            for ($k = $i + 1; $k < $count; $k++) {
                if (!is_array($tokens[$k]) || ($tokens[$k][0] !== T_WHITESPACE && $tokens[$k][0] !== T_COMMENT && $tokens[$k][0] !== T_DOC_COMMENT)) {
                    $nextNonWs = $tokens[$k];
                    $nextIdx = $k;
                    break;
                }
            }

            // Assignment operator check (=, +=, -=, .=, *=, /=, &=, |=, ^=, <<=, >>=, ??=, =>)
            $isAssign = false;
            if (is_string($nextNonWs) && $nextNonWs === '=') {
                $isAssign = true;
            } elseif (is_array($nextNonWs) && in_array($nextNonWs[0], [
                T_PLUS_EQUAL, T_MINUS_EQUAL, T_MUL_EQUAL, T_DIV_EQUAL, T_CONCAT_EQUAL,
                T_MOD_EQUAL, T_AND_EQUAL, T_OR_EQUAL, T_XOR_EQUAL, T_SL_EQUAL, T_SR_EQUAL,
                T_COALESCE_EQUAL, T_DOUBLE_ARROW
            ], true)) {
                $isAssign = true;
            } elseif (is_array($prevNonWs) && in_array($prevNonWs[0], [T_STATIC, T_AS], true)) {
                $isAssign = true;
            } elseif (is_array($prevNonWs) && in_array($prevNonWs[0], [T_ISSET, T_EMPTY], true)) {
                continue; // isset/empty does not read undefined var
            }

            // Check if inside isset(...) or empty(...)
            $inIssetOrEmpty = false;
            for ($k = $i - 1; $k >= max(0, $i - 10); $k--) {
                if (is_array($tokens[$k]) && in_array($tokens[$k][0], [T_ISSET, T_EMPTY], true)) {
                    $inIssetOrEmpty = true;
                    break;
                }
                if ($tokens[$k] === ';' || $tokens[$k] === '{' || $tokens[$k] === '}') {
                    break;
                }
            }
            if ($inIssetOrEmpty) {
                continue;
            }

            // Check if class property declaration (e.g. private string $action; protected int $code;)
            $isPropertyDecl = false;
            for ($k = $i - 1; $k >= max(0, $i - 8); $k--) {
                if (is_array($tokens[$k]) && in_array($tokens[$k][0], [T_PRIVATE, T_PROTECTED, T_PUBLIC, T_VAR], true)) {
                    $isPropertyDecl = true;
                    break;
                }
                if ($tokens[$k] === ';' || $tokens[$k] === '{' || $tokens[$k] === '}' || $tokens[$k] === '(') {
                    break;
                }
            }
            if ($isPropertyDecl) {
                continue;
            }

            if ($isAssign) {
                $assigned[$varName] = true;
                if (!empty($functionParams)) {
                    $functionParams[count($functionParams) - 1][$varName] = true;
                }
            } else {
                // Variable read! Check if assigned in current scope or enclosing function params
                $known = isset($assigned[$varName]);
                if (!$known && !empty($functionParams)) {
                    foreach ($functionParams as $scope) {
                        if (isset($scope[$varName])) {
                            $known = true;
                            break;
                        }
                    }
                }

                // If completely unassigned and not a common superglobal / loop variable
                if (!$known) {
                    $errors[] = [
                        'file' => $relPath,
                        'line' => $line,
                        'var'  => '$' . $varName,
                    ];
                }
            }
        }
    }
}

echo "Total PHP files scanned: {$totalFiles}\n";
echo "Syntax Errors: " . count($syntaxErrors) . "\n";
echo "Potential Unassigned Variable Reads: " . count($errors) . "\n\n";

if (!empty($syntaxErrors)) {
    echo "=== SYNTAX ERRORS ===\n";
    foreach ($syntaxErrors as $se) {
        echo "  - {$se}\n";
    }
}

// Group findings by file
$byFile = [];
foreach ($errors as $e) {
    $byFile[$e['file']][] = "{$e['var']} (line {$e['line']})";
}

if (!empty($byFile)) {
    echo "=== POTENTIAL UNASSIGNED VARIABLES ===\n";
    foreach ($byFile as $f => $vars) {
        $uniqueVars = array_unique($vars);
        echo "  [{$f}]: " . implode(', ', array_slice($uniqueVars, 0, 10)) . "\n";
    }
}

$exitCode = (count($syntaxErrors) > 0 || count($errors) > 0) ? 1 : 0;
echo "\nScan complete. Status: " . ($exitCode === 0 ? "PASS" : "FAIL") . "\n";
exit($exitCode);
