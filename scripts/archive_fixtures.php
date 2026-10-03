<?php
require_once __DIR__ . '/../config/db.php';
$pdo = getDbConnection();

$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

$fixturesSql = "-- ============================================================================\n";
$fixturesSql .= "-- TEST FIXTURES & HISTORICAL LEGACY DATA\n";
$fixturesSql .= "-- Moved out of baseline production seed\n";
$fixturesSql .= "-- ============================================================================\n\n";

// 1. Projects outside PRJ-2026-001..015
$stmt = $pdo->query("SELECT * FROM projects WHERE prj_id NOT BETWEEN 'PRJ-2026-001' AND 'PRJ-2026-015'");
$legacyProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!empty($legacyProjects)) {
    $fixturesSql .= "-- Legacy Projects\n";
    foreach ($legacyProjects as $p) {
        $fixturesSql .= sprintf(
            "INSERT INTO projects (prj_id, project_name, cus_id, project_manager_emp_id, budget, currency, status) VALUES ('%s', '%s', '%s', '%s', %.2f, '%s', '%s') ON DUPLICATE KEY UPDATE project_name = VALUES(project_name);\n",
            $p['prj_id'], addslashes($p['project_name'] ?? ''), $p['cus_id'], $p['project_manager_emp_id'] ?? 'EMP-1016', (float)($p['budget'] ?? 0), $p['currency'] ?? 'EUR', $p['status'] ?? 'Execution'
        );
    }
    $fixturesSql .= "\n";
}
// Clean up non-baseline projects
$pdo->exec("DELETE FROM billing_cycles WHERE prj_id NOT BETWEEN 'PRJ-2026-001' AND 'PRJ-2026-015'");
$pdo->exec("DELETE FROM ops_tasks WHERE prj_id NOT BETWEEN 'PRJ-2026-001' AND 'PRJ-2026-015'");
$pdo->exec("DELETE FROM projects WHERE prj_id NOT BETWEEN 'PRJ-2026-001' AND 'PRJ-2026-015'");

// 2. Invoices outside INV-2026-001..010
$stmt = $pdo->query("SELECT * FROM invoices WHERE inv_id NOT BETWEEN 'INV-2026-001' AND 'INV-2026-010'");
$legacyInvoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!empty($legacyInvoices)) {
    $fixturesSql .= "-- Legacy & Fixture Invoices\n";
    foreach ($legacyInvoices as $inv) {
        $fixturesSql .= sprintf(
            "INSERT INTO invoices (inv_id, cus_id, prj_id, total_value, currency, payment_status) VALUES ('%s', '%s', '%s', %.2f, '%s', '%s') ON DUPLICATE KEY UPDATE total_value = VALUES(total_value);\n",
            $inv['inv_id'], $inv['cus_id'], $inv['prj_id'], (float)$inv['total_value'], $inv['currency'], $inv['payment_status']
        );
    }
    $fixturesSql .= "\n";
}
$pdo->exec("DELETE FROM invoice_items WHERE inv_id NOT BETWEEN 'INV-2026-001' AND 'INV-2026-010'");
$pdo->exec("DELETE FROM payments WHERE inv_id NOT BETWEEN 'INV-2026-001' AND 'INV-2026-010'");
$pdo->exec("DELETE FROM invoices WHERE inv_id NOT BETWEEN 'INV-2026-001' AND 'INV-2026-010'");

// 3. Tickets outside TKT-2026-001..015
$stmt = $pdo->query("SELECT * FROM tickets WHERE tkt_id NOT BETWEEN 'TKT-2026-001' AND 'TKT-2026-015'");
$legacyTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!empty($legacyTickets)) {
    $fixturesSql .= "-- Legacy & Fixture Tickets\n";
    foreach ($legacyTickets as $t) {
        $fixturesSql .= sprintf(
            "INSERT INTO tickets (tkt_id, requester_type, title, priority, status) VALUES ('%s', '%s', '%s', '%s', '%s') ON DUPLICATE KEY UPDATE status = VALUES(status);\n",
            $t['tkt_id'], $t['requester_type'] ?? 'Employee', addslashes($t['title'] ?? ''), $t['priority'] ?? 'Medium', $t['status'] ?? 'Open'
        );
    }
    $fixturesSql .= "\n";
}
$pdo->exec("DELETE FROM ticket_comments WHERE tkt_id NOT BETWEEN 'TKT-2026-001' AND 'TKT-2026-015'");
$pdo->exec("DELETE FROM ticket_escalations WHERE tkt_id NOT BETWEEN 'TKT-2026-001' AND 'TKT-2026-015'");
$pdo->exec("DELETE FROM tickets WHERE tkt_id NOT BETWEEN 'TKT-2026-001' AND 'TKT-2026-015'");

// 4. Documents outside DOC-2026-001..015
$stmt = $pdo->query("SELECT * FROM documents WHERE doc_id NOT BETWEEN 'DOC-2026-001' AND 'DOC-2026-015'");
$legacyDocs = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!empty($legacyDocs)) {
    $fixturesSql .= "-- Legacy & Fixture Documents\n";
    foreach ($legacyDocs as $d) {
        $fixturesSql .= sprintf(
            "INSERT INTO documents (doc_id, file_name, classification, folder, department, status) VALUES ('%s', '%s', '%s', '%s', '%s', '%s') ON DUPLICATE KEY UPDATE file_name = VALUES(file_name);\n",
            $d['doc_id'], addslashes($d['file_name']), $d['classification'] ?? 'Internal', $d['folder'] ?? 'misc', $d['department'] ?? 'ENG', $d['status'] ?? 'Approved'
        );
    }
    $fixturesSql .= "\n";
}
$pdo->exec("DELETE FROM document_access_log WHERE doc_id NOT BETWEEN 'DOC-2026-001' AND 'DOC-2026-015'");
$pdo->exec("DELETE FROM document_versions WHERE doc_id NOT BETWEEN 'DOC-2026-001' AND 'DOC-2026-015'");
$pdo->exec("DELETE FROM document_approvals WHERE doc_id NOT BETWEEN 'DOC-2026-001' AND 'DOC-2026-015'");
$pdo->exec("DELETE FROM documents WHERE doc_id NOT BETWEEN 'DOC-2026-001' AND 'DOC-2026-015'");

// 5. Customers outside CUS-1001..1010
$stmt = $pdo->query("SELECT * FROM customers WHERE cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'");
$legacyCustomers = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!empty($legacyCustomers)) {
    $fixturesSql .= "-- Fixture Customers\n";
    foreach ($legacyCustomers as $c) {
        $fixturesSql .= sprintf(
            "INSERT INTO customers (cus_id, company_name, sector, primary_contact_name, primary_contact_email) VALUES ('%s', '%s', '%s', '%s', '%s') ON DUPLICATE KEY UPDATE company_name = VALUES(company_name);\n",
            $c['cus_id'], addslashes($c['company_name']), addslashes($c['sector'] ?? 'General'), addslashes($c['primary_contact_name'] ?? 'Contact'), $c['primary_contact_email'] ?? 'contact@fixture.local'
        );
    }
    $fixturesSql .= "\n";
}
$pdo->exec("DELETE FROM contacts WHERE cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'");
$pdo->exec("DELETE FROM customer_accounts WHERE cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'");
$pdo->exec("DELETE FROM portal_accounts WHERE cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'");
$pdo->exec("DELETE FROM customer_pricing WHERE cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'");
$pdo->exec("DELETE FROM opportunities WHERE cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'");
$pdo->exec("DELETE FROM crm_activities WHERE cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'");
$pdo->exec("DELETE FROM orders WHERE cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'");
$pdo->exec("DELETE FROM leads WHERE converted_cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'");
$pdo->exec("DELETE FROM customers WHERE cus_id NOT BETWEEN 'CUS-1001' AND 'CUS-1010'");

// 6. Employees outside EMP-1001..1095
$pdo->exec("DELETE FROM employee_roles WHERE emp_id = 'EMP-0001' OR emp_id > 'EMP-1095'");
$pdo->exec("DELETE FROM employee_accounts WHERE emp_id = 'EMP-0001' OR emp_id > 'EMP-1095'");
$pdo->exec("DELETE FROM user_sessions WHERE employee_account_id NOT IN (SELECT account_id FROM employee_accounts)");
$pdo->exec("DELETE FROM employees WHERE emp_id = 'EMP-0001' OR emp_id > 'EMP-1095'");

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

file_put_contents(__DIR__ . '/../DataBase/test_fixtures.sql', $fixturesSql);
echo "test_fixtures.sql written and non-baseline rows archived cleanly.\n";
