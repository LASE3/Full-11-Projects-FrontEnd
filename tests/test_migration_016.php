<?php
/**
 * Test Migration 016 portability and idempotency on MariaDB
 */
require_once __DIR__ . '/../config/db.php';
$pdo = getDbConnection();

echo "========================================================================\n";
echo "   MIGRATION 016 IDEMPOTENCY & CONSTRAINT INTEGRITY TEST                \n";
echo "========================================================================\n\n";

// Use robust SQL statement splitter (same as DataBase/build.php)
function parseSqlStatements(string $sql): array {
    $statements = [];
    $current = '';
    $len = strlen($sql);
    $inSingleQuote = false;
    $inDoubleQuote = false;
    $inBacktick = false;
    $inLineComment = false;
    $inBlockComment = false;

    for ($i = 0; $i < $len; $i++) {
        $c = $sql[$i];
        $next = ($i + 1 < $len) ? $sql[$i + 1] : '';

        if (!$inSingleQuote && !$inDoubleQuote && !$inBacktick) {
            if (!$inBlockComment && !$inLineComment) {
                if ($c === '-' && $next === '-') {
                    $inLineComment = true;
                    $i++;
                    continue;
                }
                if ($c === '#') {
                    $inLineComment = true;
                    continue;
                }
                if ($c === '/' && $next === '*') {
                    $inBlockComment = true;
                    $i++;
                    continue;
                }
            } elseif ($inLineComment) {
                if ($c === "\n" || $c === "\r") {
                    $inLineComment = false;
                }
                continue;
            } elseif ($inBlockComment) {
                if ($c === '*' && $next === '/') {
                    $inBlockComment = false;
                    $i++;
                }
                continue;
            }
        }

        if (!$inLineComment && !$inBlockComment) {
            if ($c === "'" && !$inDoubleQuote && !$inBacktick) {
                $escaped = ($i > 0 && $sql[$i - 1] === '\\');
                if (!$escaped) {
                    $inSingleQuote = !$inSingleQuote;
                }
            } elseif ($c === '"' && !$inSingleQuote && !$inBacktick) {
                $escaped = ($i > 0 && $sql[$i - 1] === '\\');
                if (!$escaped) {
                    $inDoubleQuote = !$inDoubleQuote;
                }
            } elseif ($c === '`' && !$inSingleQuote && !$inDoubleQuote) {
                $inBacktick = !$inBacktick;
            }
        }

        if ($c === ';' && !$inSingleQuote && !$inDoubleQuote && !$inBacktick && !$inLineComment && !$inBlockComment) {
            $stmt = trim($current);
            if ($stmt !== '') {
                $statements[] = $stmt;
            }
            $current = '';
        } else {
            if (!$inLineComment && !$inBlockComment) {
                $current .= $c;
            }
        }
    }

    $stmt = trim($current);
    if ($stmt !== '') {
        $statements[] = $stmt;
    }

    return $statements;
}

$sql = file_get_contents(__DIR__ . '/../DataBase/migrations/016_strict_sql_mode_and_widen_columns.sql');
$statements = parseSqlStatements($sql);

echo "Running Migration 016 pass 1 (" . count($statements) . " statements)...\n";
foreach ($statements as $stmt) {
    $pdo->exec($stmt);
}
echo "Pass 1 completed successfully.\n";

echo "Running Migration 016 pass 2 (Idempotency test, " . count($statements) . " statements)...\n";
foreach ($statements as $stmt) {
    $pdo->exec($stmt);
}
echo "Pass 2 completed successfully.\n";

// Verify all 8 constraints exist in information_schema.REFERENTIAL_CONSTRAINTS
$fks = $pdo->query("SELECT CONSTRAINT_NAME, TABLE_NAME, REFERENCED_TABLE_NAME 
    FROM information_schema.REFERENTIAL_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
      AND REFERENCED_TABLE_NAME = 'systems_catalog'")->fetchAll(PDO::FETCH_ASSOC);

$expectedFks = [
    'fk_api_access_logs_system_id',
    'fk_audit_logs_system_id',
    'fk_authentication_events_system_id',
    'fk_document_access_log_system_id',
    'fk_it_assets_system_id',
    'fk_role_system_access_system_id',
    'fk_security_events_source_system_id',
    'fk_user_sessions_system_id'
];

$foundFkNames = array_column($fks, 'CONSTRAINT_NAME');
sort($expectedFks);
sort($foundFkNames);

$missing = array_diff($expectedFks, $foundFkNames);
if (empty($missing) && count($foundFkNames) === 8) {
    echo "\n[PASS] All 8 foreign keys exist and reference systems_catalog:\n";
    foreach ($fks as $fk) {
        echo "  - {$fk['TABLE_NAME']}.{$fk['CONSTRAINT_NAME']} -> {$fk['REFERENCED_TABLE_NAME']}\n";
    }
    echo "\nSTATUS: 100% PASS - MIGRATION 016 FULLY PORTABLE AND IDEMPOTENT\n";
    exit(0);
} else {
    echo "\n[FAIL] Missing or unexpected FKs: " . implode(', ', $missing) . "\n";
    exit(1);
}
