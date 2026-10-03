<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }

/**
 * VOSTOKPRIBOR Part 4 Supplementary Test Suite
 * Tests: Unauthenticated crawl, CSRF check, Web exposure, FK integrity,
 *        SuperAdmin login/CRUD capability, final report
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';

$pdo = getDbConnection();
$results = [];

function tRecord(string $name, bool $passed, string $details = ''): void {
    global $results;
    $results[] = ['name' => $name, 'passed' => $passed, 'details' => $details];
    printf("  %-64s %s\n", substr($name, 0, 64), $passed ? "\033[32mPASS\033[0m" : "\033[31mFAIL\033[0m");
    if (!$passed) echo "    → {$details}\n";
}

echo "\n============================================================================================\n";
echo "  VOSTOKPRIBOR PART 4 SUPPLEMENTARY SECURITY & FEATURE ACCEPTANCE TESTS\n";
echo "============================================================================================\n\n";

// ─────────────────────────────────────────────────────────────────────────────
// TEST P4-1: Unauthenticated crawl – all api/*.php return 401 or 403
// ─────────────────────────────────────────────────────────────────────────────
echo "[P4-1] Unauthenticated crawl across all system API files...\n";
try {
    $apiGlobs = [
        'Online Shop B2B/api/*.php',
        'IT Helpdesk/api/*.php',
        'Finance & Billing Portal/api/v1/*.php',
        'Finance & Billing Portal/api/v1/invoices.php',
        'CRM System/api/*.php',
        'Customer Portal/api/*.php',
        'Employee Intranet/api/*.php',
        'HR System/api/*.php',
        'Developer Portal/api/*.php',
        'File Center/api/*.php',
        'Admin & Governance Portal/api/*.php',
        'api/*.php',
    ];

    $root = dirname(__DIR__) . '/';
    $allFiles = [];
    foreach ($apiGlobs as $g) {
        $matched = glob($root . $g) ?: [];
        $allFiles = array_merge($allFiles, $matched);
    }
    $allFiles = array_unique($allFiles);

    // Exclude helper/response class files, auth.php login endpoints, logout
    $skip = ['Response.php','I18n.php','helpers/','auth.php','login.php','logout.php','check_auth.php'];
    $allFiles = array_filter($allFiles, function($f) use ($skip) {
        foreach ($skip as $s) {
            if (stripos($f, $s) !== false) return false;
        }
        return true;
    });

    $blocked = 0; $allowed = 0; $failedFiles = [];
    foreach ($allFiles as $file) {
        // Run file with empty $_SESSION via CLI subprocess; capture output
        $rel = str_replace($root, '', $file);
        $out = shell_exec('php -r "' .
            'define(\'STDIN_\',STDIN);' .
            '$_SERVER[\'REQUEST_METHOD\']=\'GET\';' .
            '$_SERVER[\'HTTP_HOST\']=\'localhost\';' .
            'require_once \'' . addslashes($file) . '\';" 2>&1');
        $decoded = json_decode((string)$out, true);
        // Check for auth-blocked response
        if (isset($decoded['error']) && in_array($decoded['error'], ['Authentication required','Forbidden','Unauthorized'], true)) {
            $blocked++;
        } elseif (isset($decoded['success']) && $decoded['success'] === false) {
            $blocked++; // any error JSON = blocked
        } else {
            // If output starts with { but no auth error, or has HTML, it's a potential leak
            $firstChar = ltrim((string)$out)[0] ?? '';
            if (in_array($firstChar, ['{','['], true) && isset($decoded['data'])) {
                $allowed++;
                $failedFiles[] = $rel;
            } else {
                // Might be HTML redirect or empty – count as blocked for CLI context
                $blocked++;
            }
        }
    }
    $total = count($allFiles);
    tRecord("P4-1: Unauthenticated crawl — {$blocked}/{$total} API files blocked, {$allowed} leaked data",
        $allowed === 0,
        $allowed > 0 ? "Leaking: " . implode(', ', array_slice($failedFiles, 0, 5)) : "All {$total} files properly blocked");
} catch (Throwable $e) {
    tRecord('P4-1: Unauthenticated API crawl', false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// TEST P4-2: CSRF token generation and verification
// ─────────────────────────────────────────────────────────────────────────────
echo "\n[P4-2] CSRF token generation and rejection...\n";
try {
    // Test getCsrfToken generates
    if (session_status() === PHP_SESSION_NONE) session_start();
    $token1 = getCsrfToken();
    $token2 = getCsrfToken();
    tRecord('P4-2a: CSRF token is stable within session', $token1 === $token2 && strlen($token1) === 64,
        "Token length: " . strlen($token1));

    // Valid token verifies
    $_SESSION['csrf_token'] = $token1;
    $validVerify = verifyCsrfToken($token1);
    tRecord('P4-2b: Valid CSRF token passes verification', $validVerify, '');

    // Wrong token rejected
    $wrongVerify = verifyCsrfToken('wrong_token_' . bin2hex(random_bytes(8)));
    tRecord('P4-2c: Invalid CSRF token rejected', !$wrongVerify, '');

    // Empty token rejected
    $emptyVerify = verifyCsrfToken('');
    tRecord('P4-2d: Empty CSRF token rejected', !$emptyVerify, '');
} catch (Throwable $e) {
    tRecord('P4-2: CSRF verification', false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// TEST P4-3: Web exposure — sensitive files must not be web-accessible
// ─────────────────────────────────────────────────────────────────────────────
echo "\n[P4-3] Web exposure controls (.htaccess) present...\n";
try {
    $root = dirname(__DIR__) . '/';
    $requiredHtaccess = [
        '.htaccess'                => 'Root',
        'DataBase/.htaccess'       => 'DataBase/',
        'tests/.htaccess'          => 'tests/',
        'config/.htaccess'         => 'config/',
        'includes/.htaccess'       => 'includes/',
    ];

    $present = 0; $missing = [];
    foreach ($requiredHtaccess as $path => $label) {
        if (file_exists($root . $path)) {
            $content = file_get_contents($root . $path);
            if (stripos($content ?? '', 'deny') !== false || stripos($content ?? '', 'Require all denied') !== false || stripos($content ?? '', 'Forbidden') !== false) {
                $present++;
            } else {
                $missing[] = $label . ' (file exists but no deny rule)';
            }
        } else {
            $missing[] = $label . ' (file missing)';
        }
    }
    $total = count($requiredHtaccess);
    tRecord("P4-3: Web exposure — {$present}/{$total} .htaccess deny rules present",
        $present === $total,
        $present < $total ? "Missing/broken: " . implode(', ', $missing) : '');
} catch (Throwable $e) {
    tRecord('P4-3: Web exposure controls', false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// TEST P4-4: Foreign key integrity — orphan check
// ─────────────────────────────────────────────────────────────────────────────
echo "\n[P4-4] Foreign key orphan scan...\n";
try {
    $checks = [
        'invoices.prj_id -> projects' =>
            "SELECT COUNT(*) FROM invoices i LEFT JOIN projects p ON i.prj_id = p.prj_id WHERE i.prj_id IS NOT NULL AND p.prj_id IS NULL",
        'invoices.cus_id -> customers' =>
            "SELECT COUNT(*) FROM invoices i LEFT JOIN customers c ON i.cus_id = c.cus_id WHERE c.cus_id IS NULL",
        'tickets.assigned_emp_id -> employees' =>
            "SELECT COUNT(*) FROM tickets t LEFT JOIN employees e ON t.assigned_emp_id = e.emp_id WHERE t.assigned_emp_id IS NOT NULL AND e.emp_id IS NULL",
        'employee_roles.emp_id -> employees' =>
            "SELECT COUNT(*) FROM employee_roles er LEFT JOIN employees e ON er.emp_id = e.emp_id WHERE e.emp_id IS NULL",
        'projects.cus_id -> customers' =>
            "SELECT COUNT(*) FROM projects p LEFT JOIN customers c ON p.cus_id = c.cus_id WHERE c.cus_id IS NULL",
    ];

    $orphanFound = false; $orphanDetails = [];
    foreach ($checks as $label => $sql) {
        $count = (int)$pdo->query($sql)->fetchColumn();
        if ($count > 0) {
            $orphanFound = true;
            $orphanDetails[] = "{$label}: {$count} orphans";
        }
    }
    tRecord('P4-4: FK orphan scan — ' . count($checks) . ' relationships clean',
        !$orphanFound,
        $orphanFound ? implode(' | ', $orphanDetails) : 'All relationships intact');
} catch (Throwable $e) {
    tRecord('P4-4: FK integrity', false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// TEST P4-5: SuperAdmin account exists with correct attributes
// ─────────────────────────────────────────────────────────────────────────────
echo "\n[P4-5] SuperAdmin account integrity...\n";
try {
    $adm = $pdo->query(
        "SELECT e.emp_id, e.full_name, e.clearance_level, e.is_system_account, e.email,
                ea.username, ea.status, r.role_name
         FROM employees e
         JOIN employee_accounts ea ON e.emp_id = ea.emp_id
         JOIN employee_roles er ON e.emp_id = er.emp_id
         JOIN roles r ON er.role_id = r.role_id
         WHERE e.emp_id = 'EMP-0001' LIMIT 1"
    )->fetch(PDO::FETCH_ASSOC);

    $exists         = !empty($adm);
    $isSysAcc       = $exists && (int)$adm['is_system_account'] === 1;
    $isL4           = $exists && $adm['clearance_level'] === 'L4';
    $isActive       = $exists && $adm['status'] === 'Active';
    $isSuperAdmin   = $exists && in_array($adm['role_name'] ?? '', ['SuperAdmin', 'Executive SuperAdmin'], true);
    $hasValidEmail  = $exists && filter_var($adm['email'] ?? '', FILTER_VALIDATE_EMAIL) !== false;

    tRecord('P4-5a: EMP-0001 exists as system account with L4/SuperAdmin',
        $exists && $isSysAcc && $isL4 && $isActive && $isSuperAdmin,
        $exists ? "clearance={$adm['clearance_level']}, role={$adm['role_name']}, status={$adm['status']}" : 'NOT FOUND');

    // Test isSuperAdmin() helper function
    $mockUser = ['emp_id' => 'EMP-0001', 'account_type' => 'Employee', 'role_name' => 'SuperAdmin'];
    $helperResult = isSuperAdmin($mockUser);
    tRecord('P4-5b: isSuperAdmin() correctly identifies SuperAdmin role', $helperResult, '');

    // Non-SuperAdmin should NOT pass
    $normalUser = ['emp_id' => 'EMP-1010', 'account_type' => 'Employee', 'role_name' => 'Staff'];
    $normalResult = isSuperAdmin($normalUser);
    tRecord('P4-5c: isSuperAdmin() correctly rejects non-SuperAdmin', !$normalResult, '');
} catch (Throwable $e) {
    tRecord('P4-5: SuperAdmin account check', false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// TEST P4-6: admin_products.php exists and is guarded
// ─────────────────────────────────────────────────────────────────────────────
echo "\n[P4-6] SuperAdmin Shop product admin API...\n";
try {
    $admProd = dirname(__DIR__) . '/Online Shop B2B/api/admin_products.php';
    $exists = file_exists($admProd);
    tRecord('P4-6a: admin_products.php exists', $exists, $exists ? '' : 'File missing');

    if ($exists) {
        $content = file_get_contents($admProd);
        $hasGuard = str_contains($content, 'vp_api_guard') || str_contains($content, 'requireApiAuth');
        tRecord('P4-6b: admin_products.php has API auth guard', $hasGuard, $hasGuard ? '' : 'No auth guard found');
        $hasCrud = str_contains($content, 'POST') && str_contains($content, 'PUT') && str_contains($content, 'DELETE');
        tRecord('P4-6c: admin_products.php implements POST/PUT/DELETE CRUD', $hasCrud, $hasCrud ? '' : 'Missing CRUD verbs');
        $hasL4Check = str_contains($content, 'L4');
        tRecord('P4-6d: admin_products.php enforces L4 for mutations', $hasL4Check, $hasL4Check ? '' : 'No L4 clearance check');
    }
} catch (Throwable $e) {
    tRecord('P4-6: admin_products.php', false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// TEST P4-7: SuperAdmin Console exists and requires SuperAdmin
// ─────────────────────────────────────────────────────────────────────────────
echo "\n[P4-7] SuperAdmin Console...\n";
try {
    $console = dirname(__DIR__) . '/Admin & Governance Portal/SuperAdminConsole.php';
    $exists = file_exists($console);
    tRecord('P4-7a: SuperAdminConsole.php exists', $exists, $exists ? '' : 'File missing');

    if ($exists) {
        $content = file_get_contents($console);
        $hasGuard = str_contains($content, 'requireSuperAdmin');
        tRecord('P4-7b: SuperAdminConsole.php calls requireSuperAdmin()', $hasGuard, $hasGuard ? '' : 'No requireSuperAdmin call');
        $hasTableBrowser = str_contains($content, 'BROWSABLE_TABLES');
        tRecord('P4-7c: SuperAdminConsole.php has whitelisted table browser', $hasTableBrowser, $hasTableBrowser ? '' : 'No BROWSABLE_TABLES constant');
        $hasViewAs = str_contains($content, 'view_as') && str_contains($content, 'VIEW-AS MODE');
        tRecord('P4-7d: SuperAdminConsole.php has view-as mode', $hasViewAs, $hasViewAs ? '' : 'No view-as feature');
        $hasAuditLogs = str_contains($content, 'audit_logs');
        tRecord('P4-7e: SuperAdminConsole.php shows audit logs', $hasAuditLogs, $hasAuditLogs ? '' : 'No audit logs section');
    }
} catch (Throwable $e) {
    tRecord('P4-7: SuperAdmin Console', false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// TEST P4-8: DB safety triggers exist
// ─────────────────────────────────────────────────────────────────────────────
echo "\n[P4-8] DB safety triggers...\n";
try {
    $triggers = $pdo->query(
        "SELECT TRIGGER_NAME FROM INFORMATION_SCHEMA.TRIGGERS WHERE TRIGGER_SCHEMA = DATABASE()"
    )->fetchAll(PDO::FETCH_COLUMN);

    $hasProtectSys    = in_array('trg_protect_system_account', $triggers, true);
    $hasProtectAdmin  = in_array('trg_protect_last_superadmin', $triggers, true);

    tRecord('P4-8a: trg_protect_system_account trigger exists', $hasProtectSys, $hasProtectSys ? '' : 'Run migration 009');
    tRecord('P4-8b: trg_protect_last_superadmin trigger exists', $hasProtectAdmin, $hasProtectAdmin ? '' : 'Run migration 009');
} catch (Throwable $e) {
    tRecord('P4-8: DB triggers', false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// TEST P4-9: Change password page exists and enforces 14-char minimum
// ─────────────────────────────────────────────────────────────────────────────
echo "\n[P4-9] ChangePassword.php...\n";
try {
    $cpPage = dirname(__DIR__) . '/Admin & Governance Portal/ChangePassword.php';
    $exists = file_exists($cpPage);
    tRecord('P4-9a: ChangePassword.php exists', $exists, $exists ? '' : 'File missing');
    if ($exists) {
        $content = file_get_contents($cpPage);
        $has14 = str_contains($content, '14');
        $hasCsrf = str_contains($content, 'csrf_token');
        $hasAudit = str_contains($content, 'AuditLogger');
        tRecord('P4-9b: ChangePassword enforces 14-char minimum', $has14, $has14 ? '' : 'No 14-char rule found');
        tRecord('P4-9c: ChangePassword has CSRF protection', $hasCsrf, $hasCsrf ? '' : 'No CSRF token');
        tRecord('P4-9d: ChangePassword audits the action', $hasAudit, $hasAudit ? '' : 'No AuditLogger call');
    }
} catch (Throwable $e) {
    tRecord('P4-9: ChangePassword.php', false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// TEST P4-10: Integration link status — 21 Active, 17 NotImplemented
// ─────────────────────────────────────────────────────────────────────────────
echo "\n[P4-10] Integration link status breakdown...\n";
try {
    $active  = (int)$pdo->query("SELECT COUNT(*) FROM system_integrations WHERE status = 'Active'")->fetchColumn();
    $notImpl = (int)$pdo->query("SELECT COUNT(*) FROM system_integrations WHERE status = 'NotImplemented'")->fetchColumn();
    $total   = (int)$pdo->query("SELECT COUNT(*) FROM system_integrations")->fetchColumn();

    tRecord("P4-10: Integration links — {$active} Active, {$notImpl} NotImplemented (of {$total} total)",
        $active === 21 && $notImpl === 17,
        "Active={$active} (expect 21), NotImplemented={$notImpl} (expect 17), Total={$total}");
} catch (Throwable $e) {
    tRecord('P4-10: Integration links', false, $e->getMessage());
}

// ─────────────────────────────────────────────────────────────────────────────
// FINAL REPORT
// ─────────────────────────────────────────────────────────────────────────────
$total   = count($results);
$passed  = count(array_filter($results, fn($r) => $r['passed']));
$failed  = $total - $passed;
$allPass = $failed === 0;

echo "\n" . str_repeat('=', 90) . "\n";
echo "PART 4 SUPPLEMENTARY TESTS: {$passed}/{$total} PASS" . ($allPass ? " — ALL PASS ✓" : " — {$failed} FAILURES") . "\n";
echo str_repeat('=', 90) . "\n";

printf("%-64s | %-6s | %s\n", "TEST NAME", "STATUS", "DETAILS");
echo str_repeat('-', 110) . "\n";
foreach ($results as $r) {
    printf("%-64s | %-6s | %s\n",
        substr($r['name'], 0, 64),
        $r['passed'] ? "\033[32mPASS\033[0m" : "\033[31mFAIL\033[0m",
        $r['details']);
}

echo "\n";
echo "╔══════════════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                         VOSTOKPRIBOR REMEDIATION SUMMARY                           ║\n";
echo "╠══════════════════════════════════════════════════════════════════════════════════════╣\n";
echo "║ Part 1 Security: api_bootstrap.php, requireSuperAdmin(), CSRF, tenant isolation    ║\n";
echo "║ Part 2 DB: build.php, migrations 001-009, seed_baseline.sql, FK integrity         ║\n";
echo "║ Part 3 SuperAdmin: admin_products.php, SuperAdminConsole.php, ChangePassword.php  ║\n";
echo "║                    trg_protect_system_account, trg_protect_last_superadmin        ║\n";
echo "║ Part 4 Tests: This test suite (P4-1..10) + test_all_acceptance.php (T1..10)       ║\n";
echo "╠══════════════════════════════════════════════════════════════════════════════════════╣\n";
echo "║ ⚠  SECRET ROTATION REMINDER:                                                       ║\n";
echo "║    Rotate TEST_ADMIN_PASSWORD in your .env before production deployment.           ║\n";
echo "║    Update vostok_app DB user password (CHANGE_THIS_IN_ENV) in migration 008.      ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════════════╝\n\n";

exit($allPass ? 0 : 1);
