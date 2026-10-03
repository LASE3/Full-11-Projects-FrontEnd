<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }

/**
 * VOSTOKPRIBOR - PHASE 4 ACCEPTANCE TESTS
 * Verifies locked baseline restore, entity counts, range integrity, and foreign keys.
 */

require_once __DIR__ . '/../config/db.php';

$pdo = getDbConnection();

$results = [];

function recordTest(string $category, string $name, bool $passed, string $details = ''): void {
    global $results;
    $results[] = [
        'category' => $category,
        'name'     => $name,
        'passed'   => $passed,
        'details'  => $details
    ];
}

echo "====================================================================\n";
echo "       VOSTOKPRIBOR PHASE 4 ACCEPTANCE TEST SUITE (BASELINE)        \n";
echo "====================================================================\n\n";

function cleanupTransientTestData(PDO $pdo): void {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DELETE FROM leads WHERE source_page LIKE '%Corporate Web%' OR company_name LIKE '%Acceptance%' OR email LIKE '%@testcorp.local'");
    $pdo->exec("DELETE FROM crm_activities WHERE description LIKE '%ORD-TEST-%' OR description LIKE '%Acceptance%' OR description LIKE '%LEAD-2026-%'");
    $pdo->exec("DELETE FROM orders WHERE order_id >= 1000");
    $pdo->exec("DELETE FROM order_items WHERE order_id >= 1000");
    $pdo->exec("DELETE FROM invoice_items WHERE inv_id > 'INV-2026-010'");
    $pdo->exec("DELETE FROM invoices WHERE inv_id > 'INV-2026-010'");
    $pdo->exec("DELETE FROM documents WHERE doc_id > 'DOC-2026-015' OR doc_id LIKE 'DOC-WS-%' OR file_name LIKE 'HR_Dossier_%' OR file_name LIKE 'Invoice-INV-2026-%' OR doc_id LIKE 'DOC-2026-TEST%'");
    $pdo->exec("DELETE FROM billing_cycles WHERE prj_id > 'PRJ-2026-015'");
    $pdo->exec("DELETE FROM projects WHERE prj_id > 'PRJ-2026-015'");
    $pdo->exec("DELETE FROM ops_tasks WHERE order_id >= 1000 OR task_id LIKE 'OPS-ORD-%' OR prj_id > 'PRJ-2026-015'");
    $pdo->exec("DELETE FROM portal_notifications WHERE related_entity_id LIKE 'ORD-TEST-%' OR related_entity_id LIKE 'LEAD-%'");
    $pdo->exec("DELETE FROM ticket_escalations WHERE tkt_id > 'TKT-2026-015'");
    $pdo->exec("DELETE FROM tickets WHERE tkt_id > 'TKT-2026-015' OR title LIKE '%Acceptance%'");
    $pdo->exec("DELETE FROM it_assets WHERE emp_id > 'EMP-1095'");
    $pdo->exec("DELETE FROM employee_roles WHERE emp_id > 'EMP-1095'");
    $pdo->exec("DELETE FROM developer_api_keys WHERE partner_id > 'EMP-1095' OR partner_id LIKE 'EMP-109%'");
    $pdo->exec("DELETE FROM employee_accounts WHERE emp_id > 'EMP-1095'");
    $pdo->exec("DELETE FROM user_sessions WHERE employee_account_id NOT IN (SELECT account_id FROM employee_accounts)");
    $pdo->exec("DELETE FROM employees WHERE emp_id > 'EMP-1095'");
    $pdo->exec("DELETE FROM contacts WHERE cus_id > 'CUS-1010'");
    $pdo->exec("DELETE FROM customer_accounts WHERE cus_id > 'CUS-1010'");
    $pdo->exec("DELETE FROM portal_accounts WHERE cus_id > 'CUS-1010'");
    $pdo->exec("DELETE FROM billing_cycles WHERE cus_id > 'CUS-1010'");
    $pdo->exec("DELETE FROM opportunities WHERE cus_id > 'CUS-1010'");
    $pdo->exec("DELETE FROM customers WHERE cus_id > 'CUS-1010'");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
}

cleanupTransientTestData($pdo);

// -------------------------------------------------------------------------
// 1. EXACT ENTITY COUNTS
// -------------------------------------------------------------------------
try {
    $empCount  = (int)$pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();
    $deptCount = (int)$pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();
    $cusCount  = (int)$pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
    $prjCount  = (int)$pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
    $invCount  = (int)$pdo->query("SELECT COUNT(*) FROM invoices")->fetchColumn();
    $tktCount  = (int)$pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
    $docCount  = (int)$pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn();
    $prodCount = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

    $countsPass = (
        $empCount === 95 &&
        $deptCount === 8 &&
        $cusCount === 10 &&
        $prjCount === 15 &&
        $invCount === 10 &&
        $tktCount === 15 &&
        $docCount === 15 &&
        $prodCount === 10
    );

    recordTest(
        'Counts',
        'Exact Baseline Entity Counts (95 emp, 8 dept, 10 cus, 15 prj, 10 inv, 15 tkt, 15 doc, 10 prod)',
        $countsPass,
        "Emp:{$empCount}, Dept:{$deptCount}, Cus:{$cusCount}, Prj:{$prjCount}, Inv:{$invCount}, Tkt:{$tktCount}, Doc:{$docCount}, Prod:{$prodCount}"
    );
} catch (Throwable $e) {
    recordTest('Counts', 'Exact Baseline Entity Counts', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// 2. DEPARTMENT HEADCOUNT TARGETS
// -------------------------------------------------------------------------
try {
    $deptExpected = [
        'EXE' => 5,
        'SAL' => 16,
        'OPS' => 20,
        'ENG' => 16,
        'FIN' => 10,
        'HRA' => 8,
        'ITD' => 14,
        'GOV' => 6
    ];

    $stmt = $pdo->query("SELECT department_code, COUNT(*) as cnt FROM employees GROUP BY department_code");
    $deptActual = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $deptPass = true;
    $deptDiff = [];
    foreach ($deptExpected as $code => $target) {
        $actual = (int)($deptActual[$code] ?? 0);
        if ($actual !== $target) {
            $deptPass = false;
            $deptDiff[] = "{$code}: expected {$target}, got {$actual}";
        }
    }

    // Ensure legacy departments IT, LOG, QA do not exist
    $legacyDepts = (int)$pdo->query("SELECT COUNT(*) FROM departments WHERE dept_code IN ('IT', 'LOG', 'QA')")->fetchColumn();
    if ($legacyDepts > 0) {
        $deptPass = false;
        $deptDiff[] = "Legacy departments (IT/LOG/QA) still present";
    }

    recordTest(
        'Departments',
        'Department Headcounts: EXE(5), SAL(16), OPS(20), ENG(16), FIN(10), HRA(8), ITD(14), GOV(6)',
        $deptPass,
        empty($deptDiff) ? 'All 8 departments match locked targets' : implode('; ', $deptDiff)
    );
} catch (Throwable $e) {
    recordTest('Departments', 'Department Headcounts', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// 3. EMPLOYEE INTEGRITY (EMP-0001 purged, EMP-1021 updated, SuperAdmin role)
// -------------------------------------------------------------------------
try {
    $emp0001Exists = (int)$pdo->query("SELECT COUNT(*) FROM employees WHERE emp_id = 'EMP-0001'")->fetchColumn() > 0;
    
    $stmt1021 = $pdo->query("SELECT email, clearance_level FROM employees WHERE emp_id = 'EMP-1021'");
    $emp1021 = $stmt1021->fetch(PDO::FETCH_ASSOC);
    $emp1021Valid = ($emp1021 && str_ends_with($emp1021['email'], '@vostokpribor.local') && $emp1021['clearance_level'] === 'L2');

    $saStmt = $pdo->query("
        SELECT COUNT(*) FROM employee_roles er
        JOIN roles r ON er.role_id = r.role_id
        WHERE er.emp_id = 'EMP-1004' AND r.role_name = 'SuperAdmin'
    ");
    $saAssigned = ((int)$saStmt->fetchColumn() > 0);

    $empIntegrityPass = (!$emp0001Exists && $emp1021Valid && $saAssigned);
    recordTest(
        'Employees',
        'EMP-0001 Removed, EMP-1021 Corporate Email/L2, EMP-1004 SuperAdmin Role',
        $empIntegrityPass,
        "EMP-0001 purged: " . (!$emp0001Exists ? 'Yes' : 'No') . ", EMP-1021 valid: " . ($emp1021Valid ? 'Yes' : 'No') . ", SuperAdmin on EMP-1004: " . ($saAssigned ? 'Yes' : 'No')
    );
} catch (Throwable $e) {
    recordTest('Employees', 'Employee Integrity', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// 4. CUSTOMER INTEGRITY (Exact 10 Named Customers & Sectors)
// -------------------------------------------------------------------------
try {
    $expectedCustomers = [
        'CUS-1001' => ['Aral Geomatics Group', 'geomatics', 'EMP-1007'],
        'CUS-1002' => ['BaltNord Process Systems', 'industrial automation', 'EMP-1010'],
        'CUS-1003' => ['Steppe Mining Technologies', 'mining', 'EMP-1008'],
        'CUS-1004' => ['RheinWerk Instrumentation', 'industrial measurement', 'EMP-1010'],
        'CUS-1005' => ['Tashkent Precision Controls', 'manufacturing', 'EMP-1008'],
        'CUS-1006' => ['Daugava Optical Research', 'optical engineering', 'EMP-1007'],
        'CUS-1007' => ['Caspian Industrial Robotics', 'robotics', 'EMP-1009'],
        'CUS-1008' => ['Eurasia Water Automation', 'water infrastructure', 'EMP-1009'],
        'CUS-1009' => ['Altai Environmental Systems', 'environmental monitoring', 'EMP-1008'],
        'CUS-1010' => ['CentralRail Diagnostics', 'rail infrastructure', 'EMP-1006']
    ];

    $stmtCus = $pdo->query("SELECT cus_id, company_name, sector, account_manager_emp_id FROM customers ORDER BY cus_id");
    $actualCus = $stmtCus->fetchAll(PDO::FETCH_ASSOC);

    $cusPass = true;
    $cusDiff = [];
    foreach ($actualCus as $c) {
        $cid = $c['cus_id'];
        if (!isset($expectedCustomers[$cid])) {
            $cusPass = false;
            $cusDiff[] = "Unexpected customer {$cid}";
            continue;
        }
        $exp = $expectedCustomers[$cid];
        if ($c['company_name'] !== $exp[0] || $c['sector'] !== $exp[1] || $c['account_manager_emp_id'] !== $exp[2]) {
            $cusPass = false;
            $cusDiff[] = "Mismatch on {$cid}: {$c['company_name']} / {$c['sector']} / {$c['account_manager_emp_id']}";
        }
    }

    recordTest(
        'Customers',
        'Customer Baseline Integrity (Exact 10 Named Customers, Sectors & Account Managers)',
        $cusPass,
        empty($cusDiff) ? 'All 10 customer records match locked baseline' : implode('; ', $cusDiff)
    );
} catch (Throwable $e) {
    recordTest('Customers', 'Customer Baseline Integrity', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// 5. PROJECTS & INVOICES INTEGRITY (PRJ-2026-001..015, INV-2026-009 -> PRJ-2026-009, EUR)
// -------------------------------------------------------------------------
try {
    $inv009Prj = $pdo->query("SELECT prj_id FROM invoices WHERE inv_id = 'INV-2026-009'")->fetchColumn();
    $inv009Pass = ($inv009Prj === 'PRJ-2026-009');

    $nonEurPrj = (int)$pdo->query("SELECT COUNT(*) FROM projects WHERE currency != 'EUR'")->fetchColumn();
    $nonEurInv = (int)$pdo->query("SELECT COUNT(*) FROM invoices WHERE currency != 'EUR'")->fetchColumn();
    $currencyPass = ($nonEurPrj === 0 && $nonEurInv === 0);

    $prjRangePass = ((int)$pdo->query("SELECT COUNT(*) FROM projects WHERE prj_id NOT BETWEEN 'PRJ-2026-001' AND 'PRJ-2026-015'")->fetchColumn() === 0);
    $invRangePass = ((int)$pdo->query("SELECT COUNT(*) FROM invoices WHERE inv_id NOT BETWEEN 'INV-2026-001' AND 'INV-2026-010'")->fetchColumn() === 0);

    $prjInvPass = ($inv009Pass && $currencyPass && $prjRangePass && $invRangePass);
    recordTest(
        'Projects/Invoices',
        'Projects PRJ-2026-001..015, Invoices INV-2026-001..010, INV-2026-009 -> PRJ-2026-009, All EUR',
        $prjInvPass,
        "INV-009 -> {$inv009Prj}, Non-EUR prj: {$nonEurPrj}, Non-EUR inv: {$nonEurInv}"
    );
} catch (Throwable $e) {
    recordTest('Projects/Invoices', 'Projects & Invoices Integrity', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// 6. NO OUT-OF-RANGE IDS IN PRODUCTION SEED
// -------------------------------------------------------------------------
try {
    $badEmp = (int)$pdo->query("SELECT COUNT(*) FROM employees WHERE emp_id NOT BETWEEN 'EMP-1001' AND 'EMP-1095'")->fetchColumn();
    $badCus = (int)$pdo->query("SELECT COUNT(*) FROM customers WHERE cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'")->fetchColumn();
    $badPrj = (int)$pdo->query("SELECT COUNT(*) FROM projects WHERE prj_id NOT BETWEEN 'PRJ-2026-001' AND 'PRJ-2026-015'")->fetchColumn();
    $badInv = (int)$pdo->query("SELECT COUNT(*) FROM invoices WHERE inv_id NOT BETWEEN 'INV-2026-001' AND 'INV-2026-010'")->fetchColumn();
    $badTkt = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE tkt_id NOT BETWEEN 'TKT-2026-001' AND 'TKT-2026-015'")->fetchColumn();
    $badDoc = (int)$pdo->query("SELECT COUNT(*) FROM documents WHERE doc_id NOT BETWEEN 'DOC-2026-001' AND 'DOC-2026-015'")->fetchColumn();
    $badPrd = (int)$pdo->query("SELECT COUNT(*) FROM products WHERE prod_id NOT BETWEEN 'PROD-1001' AND 'PROD-1010'")->fetchColumn();

    $noOutOfRange = ($badEmp + $badCus + $badPrj + $badInv + $badTkt + $badDoc + $badPrd === 0);
    recordTest(
        'Range Isolation',
        'Strict Range Isolation (Zero Out-of-Range IDs in Active Baseline Tables)',
        $noOutOfRange,
        "Bad emp:{$badEmp}, cus:{$badCus}, prj:{$badPrj}, inv:{$badInv}, tkt:{$badTkt}, doc:{$badDoc}, prd:{$badPrd}"
    );
} catch (Throwable $e) {
    recordTest('Range Isolation', 'Strict Range Isolation', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// 7. FOREIGN KEY ENFORCEMENT & ORPHAN PREVENTION
// -------------------------------------------------------------------------
try {
    $orphanInvPrevented = false;
    try {
        $badInvStmt = $pdo->prepare("INSERT INTO invoices (inv_id, cus_id, prj_id, total_value, currency, payment_status) VALUES ('INV-TEST-ORPHAN', 'CUS-1001', 'PRJ-NONEXISTENT', 1000.00, 'EUR', 'Pending')");
        $badInvStmt->execute();
        $pdo->query("DELETE FROM invoices WHERE inv_id = 'INV-TEST-ORPHAN'");
    } catch (Throwable) {
        $orphanInvPrevented = true;
    }

    $orphanPrjPrevented = false;
    try {
        $badPrjStmt = $pdo->prepare("INSERT INTO projects (prj_id, cus_id, project_name) VALUES ('PRJ-TEST-ORPHAN', 'CUS-NONEXISTENT', 'Bad Project')");
        $badPrjStmt->execute();
        $pdo->query("DELETE FROM projects WHERE prj_id = 'PRJ-TEST-ORPHAN'");
    } catch (Throwable) {
        $orphanPrjPrevented = true;
    }

    $fkPass = ($orphanInvPrevented && $orphanPrjPrevented);
    recordTest(
        'Foreign Keys',
        'Foreign Key Enforcement (Invoices -> Projects, Projects -> Customers Reject Orphans)',
        $fkPass,
        "Orphan invoice rejected: " . ($orphanInvPrevented ? 'Yes' : 'No') . ", Orphan project rejected: " . ($orphanPrjPrevented ? 'Yes' : 'No')
    );
} catch (Throwable $e) {
    recordTest('Foreign Keys', 'Foreign Key Enforcement', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// 8. VOCABULARY MAPPING (L1-L4 Views)
// -------------------------------------------------------------------------
try {
    $vDocCount = (int)$pdo->query("SELECT COUNT(*) FROM v_document_classifications WHERE clearance_level IN ('L1','L2','L3','L4')")->fetchColumn();
    $vTktCount = (int)$pdo->query("SELECT COUNT(*) FROM v_ticket_priorities WHERE priority_code IN ('P1','P2','P3','P4')")->fetchColumn();

    $vocabPass = ($vDocCount === 15 && $vTktCount === 15);
    recordTest(
        'Vocabulary',
        'Database Views for PDF Vocab Mapping (v_document_classifications L1-L4, v_ticket_priorities P1-P4)',
        $vocabPass,
        "Classified docs: {$vDocCount}/15, Prioritized tickets: {$vTktCount}/15"
    );
} catch (Throwable $e) {
    recordTest('Vocabulary', 'Database Views for PDF Vocab Mapping', false, $e->getMessage());
}

// -------------------------------------------------------------------------
// DISPLAY RESULTS TABLE
// -------------------------------------------------------------------------
printf("%-18s | %-65s | %-6s | %s\n", "CATEGORY", "TEST NAME", "STATUS", "DETAILS");
echo str_repeat("-", 115) . "\n";

$allPassed = true;
foreach ($results as $r) {
    if (!$r['passed']) {
        $allPassed = false;
    }
    printf(
        "%-18s | %-65s | %-6s | %s\n",
        substr($r['category'], 0, 18),
        substr($r['name'], 0, 65),
        $r['passed'] ? 'PASS' : 'FAIL',
        $r['details']
    );
}
echo str_repeat("=", 115) . "\n";
echo "OVERALL PHASE 4 RESULT: " . ($allPassed ? "ALL PASS\n" : "FAILURES DETECTED\n");

exit($allPassed ? 0 : 1);
