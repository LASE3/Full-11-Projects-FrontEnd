<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR ENTERPRISE AUTOMATED DATA ISOLATION TEST SUITE
 * Location: tests/test_isolation_matrix.php
 *
 * Verifies per-user data isolation, ownership matrix, maker-checker separation,
 * tenant scoping, and parameter tampering prevention across all 11 systems.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/authorize.php';

$pdo = getDbConnection();

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;
$testResults = [];

function assertIsolation(string $title, bool $condition, string $details = ''): void {
    global $totalTests, $passedTests, $failedTests, $testResults;
    $totalTests++;
    if ($condition) {
        $passedTests++;
        $testResults[] = ['status' => 'PASS', 'title' => $title, 'details' => $details];
        echo "  [PASS] $title\n";
    } else {
        $failedTests++;
        $testResults[] = ['status' => 'FAIL', 'title' => $title, 'details' => $details];
        echo "  [FAIL] $title: $details\n";
    }
}

echo "========================================================================\n";
echo "      VOSTOKPRIBOR PHASE 2 - DATA ISOLATION & MATRIX TESTS              \n";
echo "========================================================================\n\n";

// Fetch test personas from database
$emp1001 = $pdo->query("SELECT * FROM employees WHERE emp_id = 'EMP-1001'")->fetch(PDO::FETCH_ASSOC); // EXE, L4
$emp1006 = $pdo->query("SELECT * FROM employees WHERE emp_id = 'EMP-1006'")->fetch(PDO::FETCH_ASSOC); // SAL Manager, L3
$emp1022 = $pdo->query("SELECT * FROM employees WHERE emp_id = 'EMP-1022'")->fetch(PDO::FETCH_ASSOC); // SAL Rep A, L2 (reports to EMP-1006)
$emp1023 = $pdo->query("SELECT * FROM employees WHERE emp_id = 'EMP-1023'")->fetch(PDO::FETCH_ASSOC); // SAL Rep B, L2 (reports to EMP-1006)
$emp1011 = $pdo->query("SELECT * FROM employees WHERE emp_id = 'EMP-1011'")->fetch(PDO::FETCH_ASSOC); // OPS Logistics Lead, L3
$emp1013 = $pdo->query("SELECT * FROM employees WHERE department_code = 'FIN' AND clearance_level IN ('L3', 'L4') LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: $pdo->query("SELECT * FROM employees WHERE emp_id = 'EMP-1013'")->fetch(PDO::FETCH_ASSOC); // FIN Controller, L3
$emp1014 = $pdo->query("SELECT * FROM employees WHERE department_code = 'FIN' AND clearance_level = 'L2' LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: $pdo->query("SELECT * FROM employees WHERE emp_id = 'EMP-1014'")->fetch(PDO::FETCH_ASSOC); // FIN Accountant, L2
$emp1015 = $pdo->query("SELECT * FROM employees WHERE department_code = 'HRA' LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: $pdo->query("SELECT * FROM employees WHERE emp_id = 'EMP-1015'")->fetch(PDO::FETCH_ASSOC); // HRA Specialist, L2
$emp1016 = $pdo->query("SELECT * FROM employees WHERE emp_id = 'EMP-1016'")->fetch(PDO::FETCH_ASSOC); // HRA Lead, L3
$emp1017 = $pdo->query("SELECT * FROM employees WHERE department_code = 'ITD' AND clearance_level IN ('L3', 'L4') LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: $pdo->query("SELECT * FROM employees WHERE emp_id = 'EMP-1017'")->fetch(PDO::FETCH_ASSOC); // ITD IT Lead, L3

$cus1001 = $pdo->query("SELECT * FROM customers WHERE cus_id = 'CUS-1001'")->fetch(PDO::FETCH_ASSOC);
$cus1002 = $pdo->query("SELECT * FROM customers WHERE cus_id = 'CUS-1002'")->fetch(PDO::FETCH_ASSOC);

$acc1 = $pdo->query("SELECT * FROM customer_accounts WHERE cus_id = 'CUS-1001' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$acc2 = $pdo->query("SELECT * FROM customer_accounts WHERE cus_id = 'CUS-1002' LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// Synthesize user session arrays
function makeUser(array $emp, string $accountType = 'Employee'): array {
    return array_merge($emp, [
        'account_type' => $accountType,
        'user_id' => $emp['emp_id'] ?? $emp['cus_id'] ?? '',
        'must_change_password' => 0
    ]);
}
function makeCusUser(array $cus, array $acc): array {
    return array_merge($cus, [
        'account_type' => 'Customer',
        'account_id'   => $acc['account_id'] ?? 1,
        'user_id'      => $cus['cus_id'],
        'cus_id'       => $cus['cus_id'],
        'username'     => $acc['username'] ?? 'client',
        'must_change_password' => 0
    ]);
}

$u1001 = makeUser($emp1001);
$u1006 = makeUser($emp1006);
$u1022 = makeUser($emp1022);
$u1023 = makeUser($emp1023);
$u1011 = makeUser($emp1011);
$u1013 = makeUser($emp1013);
$u1014 = makeUser($emp1014);
$u1015 = makeUser($emp1015);
$u1016 = makeUser($emp1016);
$u1017 = makeUser($emp1017);
$uCus1 = makeCusUser($cus1001, $acc1 ?: ['account_id' => 1, 'username' => 'aral']);
$uCus2 = makeCusUser($cus1002, $acc2 ?: ['account_id' => 2, 'username' => 'baltnord']);

// -----------------------------------------------------------------------------
// SECTION 1: LEAVE REQUESTS MATRIX
// -----------------------------------------------------------------------------
echo "--- DOMAIN 1: LEAVE REQUESTS ---\n";

// Clean any old test leaves
$pdo->exec("DELETE FROM leave_requests WHERE emp_id IN ('EMP-1022', 'EMP-1023') AND reason LIKE '%IsolationTest%'");

// 1.1 Create leave request for Rep A (EMP-1022)
$pdo->prepare("
    INSERT INTO leave_requests (emp_id, leave_type, start_date, end_date, reason, status)
    VALUES ('EMP-1022', 'Annual', '2026-11-01', '2026-11-05', 'IsolationTest-Annual-1', 'Pending')
")->execute();
$leaveId = (int)$pdo->lastInsertId();
$leaveRow = $pdo->query("SELECT * FROM leave_requests WHERE leave_id = $leaveId")->fetch(PDO::FETCH_ASSOC);

// Test 1.1: Self-approval rejected (403)
try {
    vp_authorize($u1022, 'leave:approve', $leaveRow);
    assertIsolation("1.1 Self-approval of leave blocked", false, "Allowed self-approval");
} catch (VpAuthorizationException $e) {
    assertIsolation("1.1 Self-approval of leave blocked (403)", $e->getCode() === 403, "Code: " . $e->getCode());
}

// Test 1.2: Peer approval rejected (403) - EMP-1023 is peer of EMP-1022
try {
    vp_authorize($u1023, 'leave:approve', $leaveRow);
    assertIsolation("1.2 Peer approval of leave blocked", false, "Allowed peer approval");
} catch (VpAuthorizationException $e) {
    assertIsolation("1.2 Peer approval of leave blocked (403)", $e->getCode() === 403, "Code: " . $e->getCode());
}

// Test 1.3: Line manager approval allowed (200) - EMP-1006 manages EMP-1022
try {
    vp_authorize($u1006, 'leave:approve', $leaveRow);
    assertIsolation("1.3 Line manager (EMP-1006) approval allowed", true);
} catch (VpAuthorizationException $e) {
    assertIsolation("1.3 Line manager approval allowed", false, $e->getMessage());
}

// Test 1.4: HR Specialist approval allowed (200) - EMP-1015 / EMP-1016 is in HRA
try {
    vp_authorize($u1015, 'leave:approve', $leaveRow);
    assertIsolation("1.4 HR Specialist (HRA dept) approval allowed", true);
} catch (VpAuthorizationException $e) {
    assertIsolation("1.4 HR Specialist approval allowed", false, $e->getMessage());
}

// Test 1.5: Non-authorized peer cannot read leave details (404)
try {
    vp_authorize($u1023, 'leave:read', $leaveRow);
    assertIsolation("1.5 Unrelated peer reading leave gets 404", false, "Read permitted to peer");
} catch (VpAuthorizationException $e) {
    assertIsolation("1.5 Unrelated peer reading leave gets 404 (deny by default)", $e->getCode() === 404, "Code: " . $e->getCode());
}

// -----------------------------------------------------------------------------
// SECTION 2: IT HELPDESK TICKETS MATRIX
// -----------------------------------------------------------------------------
echo "\n--- DOMAIN 2: IT HELPDESK TICKETS ---\n";

// Test 2.1: Baseline ticket delete blocked globally (403)
$baselineTkt = $pdo->query("SELECT * FROM tickets WHERE tkt_id = 'TKT-2026-001'")->fetch(PDO::FETCH_ASSOC);
try {
    vp_authorize($u1001, 'ticket:delete', $baselineTkt);
    assertIsolation("2.1 Baseline ticket TKT-2026-001 deletion blocked", false, "Delete allowed on baseline");
} catch (VpAuthorizationException $e) {
    assertIsolation("2.1 Baseline ticket TKT-2026-001 deletion blocked (403)", $e->getCode() === 403, "Code: " . $e->getCode());
}

// Create a non-baseline transient ticket for CUS-1001
$pdo->prepare("
    INSERT INTO tickets (tkt_id, title, requester_type, requester_cus_id, priority, status, source_system)
    VALUES ('TKT-2026-999', 'Isolation Test Ticket', 'Customer', 'CUS-1001', 'Medium', 'Open', 'CUS')
    ON DUPLICATE KEY UPDATE requester_cus_id = 'CUS-1001', source_system = 'CUS'
")->execute();
$nonBaseTkt = $pdo->query("SELECT * FROM tickets WHERE tkt_id = 'TKT-2026-999'")->fetch(PDO::FETCH_ASSOC);

// Test 2.2: Cross-tenant customer ticket probing returns 404
try {
    vp_authorize($uCus2, 'ticket:read', $nonBaseTkt);
    assertIsolation("2.2 Cross-tenant customer reading ticket gets 404", false, "Cross-tenant read allowed");
} catch (VpAuthorizationException $e) {
    assertIsolation("2.2 Cross-tenant customer reading ticket gets 404", $e->getCode() === 404, "Code: " . $e->getCode());
}

// Test 2.3: Owning customer can read ticket (200)
try {
    vp_authorize($uCus1, 'ticket:read', $nonBaseTkt);
    assertIsolation("2.3 Owning customer can read own ticket (200)", true);
} catch (VpAuthorizationException $e) {
    assertIsolation("2.3 Owning customer can read own ticket", false, $e->getMessage());
}

// Test 2.4: IT Lead can delete non-baseline ticket (200)
try {
    vp_authorize($u1017, 'ticket:delete', $nonBaseTkt);
    assertIsolation("2.4 IT Lead can delete non-baseline ticket", true);
} catch (VpAuthorizationException $e) {
    assertIsolation("2.4 IT Lead can delete non-baseline ticket", false, $e->getMessage());
}

// Clean up non-baseline ticket
$pdo->exec("DELETE FROM tickets WHERE tkt_id = 'TKT-2026-999'");

// -----------------------------------------------------------------------------
// SECTION 3: FINANCE & BILLING MATRIX (MAKER-CHECKER)
// -----------------------------------------------------------------------------
echo "\n--- DOMAIN 3: FINANCE & BILLING (MAKER-CHECKER) ---\n";

// Test 3.1: Operations employee (EMP-1011, Role 8 / OPS) barred from Finance actions (403)
try {
    vp_authorize($u1011, 'finance:mark_paid', ['inv_id' => 'INV-2026-001', 'created_by_emp_id' => 'EMP-1014']);
    assertIsolation("3.1 Operations employee barred from mark_paid", false, "Allowed OPS mark_paid");
} catch (VpAuthorizationException $e) {
    assertIsolation("3.1 Operations employee barred from mark_paid (403)", $e->getCode() === 403);
}

// Test 3.2: Maker-Checker separation: Invoice creator cannot mark it paid (403)
$invoiceMock = ['inv_id' => 'INV-2026-TEST1', 'created_by_emp_id' => $u1014['emp_id'], 'cus_id' => 'CUS-1001'];
try {
    vp_authorize($u1014, 'finance:mark_paid', $invoiceMock);
    assertIsolation("3.2 Invoice creator (EMP-1014) cannot mark own invoice paid", false, "Allowed self-mark-paid");
} catch (VpAuthorizationException $e) {
    assertIsolation("3.2 Maker-checker: Invoice creator barred from marking paid (403)", $e->getCode() === 403);
}

// Test 3.3: Controller (EMP-1013) who did not create the invoice CAN mark it paid (200)
try {
    vp_authorize($u1013, 'finance:mark_paid', $invoiceMock);
    assertIsolation("3.3 Duty separation: Controller (EMP-1013) can mark invoice paid (200)", true);
} catch (VpAuthorizationException $e) {
    assertIsolation("3.3 Controller can mark invoice paid", false, $e->getMessage());
}

// Test 3.4: Only CFO / SuperAdmin can void invoices (403 for accountant, 200 for SuperAdmin/CFO)
try {
    vp_authorize($u1014, 'finance:void', $invoiceMock);
    assertIsolation("3.4 Accountant barred from voiding invoice", false, "Accountant voided invoice");
} catch (VpAuthorizationException $e) {
    assertIsolation("3.4 Accountant barred from voiding invoice (403)", $e->getCode() === 403);
}

try {
    vp_authorize($u1001, 'finance:void', $invoiceMock);
    assertIsolation("3.5 SuperAdmin / Executive allowed to void invoice (200)", true);
} catch (VpAuthorizationException $e) {
    assertIsolation("3.5 SuperAdmin allowed to void invoice", false, $e->getMessage());
}

// -----------------------------------------------------------------------------
// SECTION 4: CRM OWNERSHIP & SCOPING MATRIX
// -----------------------------------------------------------------------------
echo "\n--- DOMAIN 4: CRM OWNERSHIP & SCOPING ---\n";

// Customer assigned to Rep A (EMP-1022) vs Rep B (EMP-1023)
$cusRepA = ['cus_id' => 'CUS-TEST-A', 'company_name' => 'Rep A Account', 'account_manager_emp_id' => 'EMP-1022'];
$cusRepB = ['cus_id' => 'CUS-TEST-B', 'company_name' => 'Rep B Account', 'account_manager_emp_id' => 'EMP-1023'];

// Test 4.1: Rep A editing Rep B's customer account is blocked (403)
try {
    vp_authorize($u1022, 'crm:update', $cusRepB);
    assertIsolation("4.1 Rep A editing Rep B's customer account is blocked", false, "Allowed cross-rep customer edit");
} catch (VpAuthorizationException $e) {
    assertIsolation("4.1 Rep A editing Rep B's customer account blocked (403)", $e->getCode() === 403);
}

// Test 4.2: Rep A reading Rep B's private opportunity returns 404
$oppRepB = ['opp_id' => 9999, 'cus_id' => 'CUS-1001', 'sales_emp_id' => 'EMP-1023', 'account_manager_emp_id' => 'EMP-1023'];
try {
    vp_authorize($u1022, 'crm:read', $oppRepB);
    assertIsolation("4.2 Rep A reading Rep B's opportunity returns 404", false, "Allowed cross-rep opportunity read");
} catch (VpAuthorizationException $e) {
    assertIsolation("4.2 Rep A reading Rep B's opportunity returns 404 (deny by default)", $e->getCode() === 404);
}

// Test 4.3: Sales Manager (EMP-1006) manages both Rep A and Rep B -> Allowed to read (200)
try {
    vp_authorize($u1006, 'crm:read', $oppRepB);
    assertIsolation("4.3 Sales Manager (EMP-1006) can view team opportunities (200)", true);
} catch (VpAuthorizationException $e) {
    assertIsolation("4.3 Sales Manager can view team opportunities", false, $e->getMessage());
}

// Test 4.4: Sales Manager (EMP-1006) can modify team customer (200)
try {
    vp_authorize($u1006, 'crm:update', $cusRepA);
    assertIsolation("4.4 Sales Manager can update team member customer (200)", true);
} catch (VpAuthorizationException $e) {
    assertIsolation("4.4 Sales Manager can update team member customer", false, $e->getMessage());
}

// Test 4.5: Baseline customer deletion blocked (403)
$baseCust = $pdo->query("SELECT * FROM customers WHERE cus_id = 'CUS-1001'")->fetch(PDO::FETCH_ASSOC);
try {
    vp_authorize($u1001, 'crm:delete', $baseCust);
    assertIsolation("4.5 Baseline customer CUS-1001 deletion blocked", false, "Allowed baseline customer deletion");
} catch (VpAuthorizationException $e) {
    assertIsolation("4.5 Baseline customer CUS-1001 deletion blocked (403)", $e->getCode() === 403);
}

// -----------------------------------------------------------------------------
// SECTION 5: EMPLOYEE DIRECTORY DATA MASKING
// -----------------------------------------------------------------------------
echo "\n--- DOMAIN 5: EMPLOYEE DIRECTORY FIELD MASKING ---\n";

// Test 5.1: Non-HR employee directory API masking
$subScript = __DIR__ . '/_sub_dir_test.php';
file_put_contents($subScript, '<?php
error_reporting(0);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$_SESSION["vostok_user"] = ' . var_export($u1011, true) . ';
$_SESSION["vostok_authenticated"] = true;
$_SESSION["vostok_current_system"] = "EMP";
$GLOBALS["currentUser"] = $_SESSION["vostok_user"];
$GLOBALS["isAuthenticated"] = true;
include __DIR__ . "/../Employee Intranet/api/directory.php";
');
$dirRaw = shell_exec('C:\\xampp\\php\\php.exe ' . escapeshellarg($subScript));
@unlink($subScript);
$jsonStart = strpos((string)$dirRaw, '{');
$dirJson = ($jsonStart !== false) ? json_decode(substr((string)$dirRaw, $jsonStart), true) : null;
$firstDir = $dirJson['data'][0] ?? [];

assertIsolation("5.1 Directory returns data array", !empty($dirJson['data']));
assertIsolation("5.2 Sensitive clearance_level stripped for non-HR user", !isset($firstDir['clearance_level']), "clearance_level was present");
assertIsolation("5.3 Sensitive hire_date stripped for non-HR user", !isset($firstDir['hire_date']), "hire_date was present");
assertIsolation("5.4 Sensitive manager_emp_id stripped for non-HR user", !isset($firstDir['manager_emp_id']), "manager_emp_id was present");
assertIsolation("5.5 Public full_name, email, job_title preserved", isset($firstDir['full_name'], $firstDir['email'], $firstDir['job_title']));

// -----------------------------------------------------------------------------
// SECTION 6: TWO-LEVEL CUSTOMER SCOPING (COMPANY VS ACCOUNT_ID)
// -----------------------------------------------------------------------------
echo "\n--- DOMAIN 6: TWO-LEVEL CUSTOMER SCOPING ---\n";

// Test 6.1: Company shared data is accessible by cus_id
$invoiceCus1 = ['inv_id' => 'INV-2026-001', 'cus_id' => 'CUS-1001'];
try {
    vp_authorize($uCus1, 'finance:read', $invoiceCus1);
    assertIsolation("6.1 Customer A can read company invoice for CUS-1001", true);
} catch (VpAuthorizationException $e) {
    assertIsolation("6.1 Customer A can read company invoice", false, $e->getMessage());
}

// Test 6.2: Customer B cannot read Customer A's invoice (404)
try {
    vp_authorize($uCus2, 'finance:read', $invoiceCus1);
    assertIsolation("6.2 Customer B reading Customer A invoice gets 404", false, "Cross-company invoice leak");
} catch (VpAuthorizationException $e) {
    assertIsolation("6.2 Customer B reading Customer A invoice gets 404", $e->getCode() === 404);
}

// Test 6.3: User-private preferences isolated by account_id
$prefUser1 = ['pref_id' => 10, 'account_id' => $uCus1['account_id'], 'theme' => 'dark'];
try {
    vp_authorize($uCus1, 'user_data:read', $prefUser1);
    assertIsolation("6.3 User can read own private preferences (account_id scoped)", true);
} catch (VpAuthorizationException $e) {
    assertIsolation("6.3 User reading own preferences", false, $e->getMessage());
}

// Test 6.4: Another user cannot read private preferences (404)
try {
    vp_authorize($uCus2, 'user_data:read', $prefUser1);
    assertIsolation("6.4 Cross-user probing of private preferences returns 404", false, "Private user data leaked");
} catch (VpAuthorizationException $e) {
    assertIsolation("6.4 Cross-user probing of private preferences returns 404", $e->getCode() === 404);
}

// -----------------------------------------------------------------------------
// SECTION 7: PARAMETER TAMPERING & MASS ASSIGNMENT
// -----------------------------------------------------------------------------
echo "\n--- DOMAIN 7: PARAMETER TAMPERING & MASS ASSIGNMENT ---\n";

// Test 7.1: enforceTenantIsolation throws RuntimeException when requestedCusId differs from own
try {
    enforceTenantIsolation('CUS-1002', $uCus1);
    assertIsolation("7.1 Parameter tampering ?cus_id=CUS-1002 blocked", false, "Allowed tampered cus_id");
} catch (Throwable $e) {
    assertIsolation("7.1 Parameter tampering ?cus_id=CUS-1002 blocked", true);
}

// Test 7.2: Leave request creation mass-assignment rejection
$tamperedLeaveInput = [
    'emp_id'               => 'EMP-1001', // Spoofed to CEO
    'status'               => 'Approved', // Spoofed to bypass approval
    'approved_by_emp_id'   => 'EMP-1001',
    'leave_type'           => 'Sick',
    'start_date'           => '2026-12-01',
    'end_date'             => '2026-12-02',
    'reason'               => 'Tamper test'
];

// Check authorize guard on create
try {
    vp_authorize($u1022, 'leave:create', ['emp_id' => $tamperedLeaveInput['emp_id']]);
    assertIsolation("7.2 Creating leave for another employee rejected", false, "Created leave for other employee");
} catch (VpAuthorizationException $e) {
    assertIsolation("7.2 Creating leave for another employee rejected (403)", $e->getCode() === 403);
}

// Clean up any test records
$pdo->exec("DELETE FROM leave_requests WHERE reason LIKE '%IsolationTest%'");

// -----------------------------------------------------------------------------
// SUMMARY REPORT
// -----------------------------------------------------------------------------
echo "\n========================================================================\n";
echo "                      TEST EXECUTION SUMMARY                            \n";
echo "========================================================================\n";
echo "  TOTAL TESTS : $totalTests\n";
echo "  PASSED      : $passedTests\n";
echo "  FAILED      : $failedTests\n";
echo "  SUCCESS RATE: " . number_format(($passedTests / max(1, $totalTests)) * 100, 1) . "%\n";
echo "========================================================================\n";

if ($failedTests > 0) {
    exit(1);
}
exit(0);
