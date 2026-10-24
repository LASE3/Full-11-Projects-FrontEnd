<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR CI Placeholder Safety Scanner
 *
 * Scans all PHP code for repeated named placeholders in SQL statements.
 * Under PDO::ATTR_EMULATE_PREPARES = false (native prepared statements),
 * repeating a placeholder name (:name) triggers SQLSTATE[HY093]: Invalid parameter number.
 * Every placeholder occurrence in a query must be uniquely named (:s1, :s2, etc.).
 */

$rootDir = realpath(dirname(__DIR__));
$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$violations = [];

foreach ($it as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $path = str_replace('\\', '/', $file->getPathname());
    if (preg_match('#/(vendor|node_modules|tests|storage|scripts|\.git|\.gemini)/#', $path)) {
        continue;
    }

    $content = file_get_contents($path);

    // 1. Scan individual string literals
    $tokens = token_get_all($content);
    $count = count($tokens);

    for ($i = 0; $i < $count; $i++) {
        $tok = $tokens[$i];
        if (is_array($tok) && ($tok[0] === T_CONSTANT_ENCAPSED_STRING || $tok[0] === T_ENCAPSED_AND_WHITESPACE)) {
            $str = $tok[1];
            if (preg_match_all('/(?<!:):([a-zA-Z_][a-zA-Z0-9_]*)/', $str, $matches)) {
                $counts = array_count_values($matches[1]);
                foreach ($counts as $name => $occ) {
                    if ($occ > 1) {
                        $violations[] = [
                            'file' => str_replace($rootDir . '/', '', $path),
                            'line' => $tok[2],
                            'placeholder' => ':' . $name,
                            'occurrences' => $occ,
                            'snippet' => trim(substr($str, 0, 120))
                        ];
                    }
                }
            }
        }
    }

    // 2. Scan concatenated query variables ($sql .= ..., $query .= ...)
    $lines = explode("\n", $content);
    $vars = [];

    foreach ($lines as $lineNum => $line) {
        if (preg_match('/prepare\s*\(\s*\$([a-zA-Z0-9_]+)/', $line, $m)) {
            $sqlVar = '$' . $m[1];
            if (isset($vars[$sqlVar])) {
                $counts = array_count_values($vars[$sqlVar]['placeholders']);
                foreach ($counts as $p => $c) {
                    if ($c > 1) {
                        $violations[] = [
                            'file' => str_replace($rootDir . '/', '', $path),
                            'line' => $lineNum + 1,
                            'placeholder' => $p,
                            'occurrences' => $c,
                            'snippet' => "Concatenated variable {$sqlVar} prepared at line " . ($lineNum + 1)
                        ];
                    }
                }
            }
            unset($vars[$sqlVar]);
        }

        if (preg_match('/(\$[a-zA-Z0-9_]+)\s*(\.=|=(?!=))\s*(.*)/', $line, $m)) {
            $vName = $m[1];
            $op = $m[2];
            $rhs = $m[3];

            if ($op === '=') {
                $vars[$vName] = ['placeholders' => [], 'lines' => []];
            }
            if (!isset($vars[$vName])) {
                $vars[$vName] = ['placeholders' => [], 'lines' => []];
            }

            if (preg_match_all('/(?<!:):([a-zA-Z_][a-zA-Z0-9_]*)/', $rhs, $pMatches)) {
                foreach ($pMatches[1] as $pName) {
                    $vars[$vName]['placeholders'][] = ':' . $pName;
                    $vars[$vName]['lines'][] = $lineNum + 1;
                }
            }
        }
    }
}

echo "========================================================================\n";
echo "       VOSTOKPRIBOR SQL NAMED PLACEHOLDER UNIQUENESS SCANNER            \n";
echo "========================================================================\n";

if (!empty($violations)) {
    echo "FAILED: Found " . count($violations) . " repeated named placeholder(s):\n";
    foreach ($violations as $v) {
        echo "  - {$v['file']}:{$v['line']} -> {$v['placeholder']} ({$v['occurrences']}x)\n";
        echo "    Context: {$v['snippet']}\n";
    }
    echo "========================================================================\n";
    exit(1);
}

echo "PASSED: All SQL queries verified: ZERO repeated named placeholders detected.\n";
echo "========================================================================\n";
exit(0);
