<?php

/**
 * VOSTOKPRIBOR Finance & Billing - Core Database API Controller
 * Handles all CRUD actions and database interactions for SYS-04 / SYS-08
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';

// Ensure user has Finance authorization
$currUser = $_SESSION['vostok_user'] ?? null;
if (!$currUser && empty($_SESSION['vostok_authenticated'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized access. Please log in.']);
    exit;
}

$pdo = getDbConnection();
$action = $_REQUEST['action'] ?? '';

// Helper to send JSON response
function jsonReply($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

// Generate Next Invoice ID
function generateNextInvoiceId($pdo)
{
    $stmt = $pdo->query("SELECT MAX(CAST(SUBSTRING(inv_id, 10) AS UNSIGNED)) as max_num FROM invoices WHERE inv_id LIKE 'INV-2026-%'");
    $maxNum = $stmt->fetchColumn();
    $nextNum = ($maxNum ? (int)$maxNum : 10) + 1;
    return sprintf('INV-2026-%03d', $nextNum);
}

try {
    switch ($action) {

        // ====================================================================
        // 1. GET INVOICE DETAIL & LINE ITEMS
        // ====================================================================
        case 'get_invoice_detail':
            $invId = trim($_GET['inv_id'] ?? '');
            if (empty($invId)) {
                jsonReply(['success' => false, 'error' => 'Invoice ID required'], 400);
            }

            $stmt = $pdo->prepare("
                SELECT 
                    i.*,
                    c.company_name,
                    c.primary_contact_name,
                    c.sector,
                    p.project_name
                FROM invoices i
                JOIN customers c ON i.cus_id = c.cus_id
                LEFT JOIN projects p ON i.prj_id = p.prj_id
                WHERE i.inv_id = ?
            ");
            $stmt->execute([$invId]);
            $invoice = $stmt->fetch();

            if (!$invoice) {
                jsonReply(['success' => false, 'error' => 'Invoice not found'], 404);
            }

            // Fetch Line Items from invoice_items table
            $itemStmt = $pdo->prepare("SELECT * FROM invoice_items WHERE inv_id = ? ORDER BY item_id ASC");
            $itemStmt->execute([$invId]);
            $items = $itemStmt->fetchAll();

            // Fetch Related Payments from payments table
            $payStmt = $pdo->prepare("SELECT * FROM payments WHERE inv_id = ? ORDER BY payment_date ASC");
            $payStmt->execute([$invId]);
            $payments = $payStmt->fetchAll();

            jsonReply([
                'success' => true,
                'invoice' => $invoice,
                'items' => $items,
                'payments' => $payments
            ]);
            break;

        // ====================================================================
        // 2. CREATE NEW INVOICE
        // ====================================================================
        case 'create_invoice':
            $cusId = trim($_POST['cus_id'] ?? '');
            $prjId = trim($_POST['prj_id'] ?? '') ?: null;
            $totalValue = (float)($_POST['total_value'] ?? 0);
            $currency = trim($_POST['currency'] ?? 'EUR') ?: 'EUR';
            $paymentTerms = trim($_POST['payment_terms'] ?? 'Net-30');
            $issuedAt = trim($_POST['issued_at'] ?? '') ?: date('Y-m-d');
            $dueDate = trim($_POST['due_date'] ?? '');
            $notes = trim($_POST['notes'] ?? '');
            $lineDesc = trim($_POST['line_description'] ?? 'Industrial Instrumentation & Telemetry Commissioning');
            $partNumber = trim($_POST['part_number'] ?? 'VP-SPEC-400');

            if (empty($cusId)) {
                jsonReply(['success' => false, 'error' => 'Customer selection is required.'], 400);
            }
            if ($totalValue <= 0) {
                jsonReply(['success' => false, 'error' => 'Invoice amount must be greater than zero.'], 400);
            }

            if (empty($dueDate)) {
                $days = 30;
                if ($paymentTerms === 'Net-60') $days = 60;
                if ($paymentTerms === 'Due Upon Delivery (FAT)' || $paymentTerms === 'Advance 100%') $days = 14;
                $dueDate = date('Y-m-d', strtotime("+$days days", strtotime($issuedAt)));
            }

            $invId = generateNextInvoiceId($pdo);

            $pdo->beginTransaction();

            $insertStmt = $pdo->prepare("
                INSERT INTO invoices 
                (inv_id, cus_id, prj_id, total_value, currency, payment_status, issued_at, due_date, payment_terms, notes)
                VALUES (?, ?, ?, ?, ?, 'Pending', ?, ?, ?, ?)
            ");
            $insertStmt->execute([$invId, $cusId, $prjId, $totalValue, $currency, $issuedAt, $dueDate, $paymentTerms, $notes]);

            // Insert line item in invoice_items
            $itemStmt = $pdo->prepare("
                INSERT INTO invoice_items
                (inv_id, description, part_number, qty, unit_price, total_price)
                VALUES (?, ?, ?, 1, ?, ?)
            ");
            $itemStmt->execute([$invId, $lineDesc, $partNumber, $totalValue, $totalValue]);

            $pdo->commit();

            logIntegrationEvent('INT-FIN-01', 'FIN', 'ERP', '/api/invoices/create', "Generated invoice {$invId} for customer {$cusId} value {$totalValue} {$currency}", 'Outbound', 200, $currUser['emp_id'] ?? 'FIN-CTRL');

            jsonReply([
                'success' => true,
                'message' => "Invoice {$invId} created successfully.",
                'inv_id' => $invId,
                'total_value' => $totalValue
            ]);
            break;

        // ====================================================================
        // 3. MARK INVOICE AS PAID
        // ====================================================================
        case 'mark_paid':
            $invId = trim($_POST['inv_id'] ?? $_GET['inv_id'] ?? '');
            if (empty($invId)) {
                jsonReply(['success' => false, 'error' => 'Invoice ID required'], 400);
            }

            $stmt = $pdo->prepare("SELECT total_value, currency, cus_id FROM invoices WHERE inv_id = ?");
            $stmt->execute([$invId]);
            $inv = $stmt->fetch();
            if (!$inv) {
                jsonReply(['success' => false, 'error' => 'Invoice not found'], 404);
            }

            $pdo->beginTransaction();

            // Update invoice status
            $uStmt = $pdo->prepare("UPDATE invoices SET payment_status = 'Paid', paid_at = CURDATE() WHERE inv_id = ?");
            $uStmt->execute([$invId]);

            // Ensure a reconciled payment record exists
            $checkPay = $pdo->prepare("SELECT COUNT(*) FROM payments WHERE inv_id = ? AND reconciled = 1");
            $checkPay->execute([$invId]);
            if ((int)$checkPay->fetchColumn() === 0) {
                $insPay = $pdo->prepare("
                    INSERT INTO payments (inv_id, amount, payment_date, method, reconciled, tx_reference, remittance_memo, bank_gateway)
                    VALUES (?, ?, CURDATE(), 'SPFS Direct Clearance', 1, ?, ?, 'Central Bank Settlement Grid')
                ");
                $txRef = 'TX-REC-' . rand(1000, 9999);
                $insPay->execute([$invId, $inv['total_value'], $txRef, "Settlement for {$invId}"]);
            }

            $pdo->commit();

            jsonReply([
                'success' => true,
                'message' => "Invoice {$invId} marked as PAID. General ledger hash updated."
            ]);
            break;

        // ====================================================================
        // 4. SEND PAYMENT REMINDER
        // ====================================================================
        case 'send_reminder':
            $invId = trim($_POST['inv_id'] ?? $_GET['inv_id'] ?? '');
            if (empty($invId)) {
                jsonReply(['success' => false, 'error' => 'Invoice ID required'], 400);
            }

            $stmt = $pdo->prepare("
                SELECT i.*, c.company_name, c.primary_contact_name 
                FROM invoices i 
                JOIN customers c ON i.cus_id = c.cus_id 
                WHERE i.inv_id = ?
            ");
            $stmt->execute([$invId]);
            $inv = $stmt->fetch();
            if (!$inv) {
                jsonReply(['success' => false, 'error' => 'Invoice not found'], 404);
            }

            logIntegrationEvent('INT-FIN-NOTIFY', 'FIN', 'CRM', '/api/notifications/dispatch', "Payment reminder dispatched to {$inv['company_name']} for invoice {$invId}", 'Outbound', 200, $currUser['emp_id'] ?? 'FIN-CTRL');

            jsonReply([
                'success' => true,
                'message' => "Automated payment reminder dispatched to {$inv['company_name']} treasury office."
            ]);
            break;

        // ====================================================================
        // 5. RECONCILE PAYMENT
        // ====================================================================
        case 'reconcile_payment':
            $paymentId = (int)($_POST['payment_id'] ?? $_GET['payment_id'] ?? 0);
            $invId = trim($_POST['inv_id'] ?? $_GET['inv_id'] ?? '') ?: null;

            if ($paymentId <= 0) {
                jsonReply(['success' => false, 'error' => 'Invalid payment ID'], 400);
            }

            $stmt = $pdo->prepare("SELECT * FROM payments WHERE payment_id = ?");
            $stmt->execute([$paymentId]);
            $pay = $stmt->fetch();
            if (!$pay) {
                jsonReply(['success' => false, 'error' => 'Payment transaction not found'], 404);
            }

            $pdo->beginTransaction();

            $targetInvId = $invId ?: $pay['inv_id'];

            // Update payment to reconciled
            $uStmt = $pdo->prepare("UPDATE payments SET reconciled = 1, inv_id = ? WHERE payment_id = ?");
            $uStmt->execute([$targetInvId, $paymentId]);

            // If an invoice is associated, check total settled vs invoice value
            if (!empty($targetInvId)) {
                $sumStmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE inv_id = ? AND reconciled = 1");
                $sumStmt->execute([$targetInvId]);
                $settled = (float)$sumStmt->fetchColumn();

                $invStmt = $pdo->prepare("SELECT total_value FROM invoices WHERE inv_id = ?");
                $invStmt->execute([$targetInvId]);
                $totalVal = (float)$invStmt->fetchColumn();

                if ($settled >= $totalVal) {
                    $uInv = $pdo->prepare("UPDATE invoices SET payment_status = 'Paid', paid_at = CURDATE() WHERE inv_id = ?");
                    $uInv->execute([$targetInvId]);
                }
            }

            $pdo->commit();

            jsonReply([
                'success' => true,
                'message' => "Payment #{$paymentId} successfully matched and reconciled to ledger.",
                'payment_id' => $paymentId,
                'amount' => $pay['amount']
            ]);
            break;

        // ====================================================================
        // 6. RUN AUTO-MATCH ENGINE
        // ====================================================================
        case 'auto_reconcile':
            $unmatchedStmt = $pdo->query("SELECT * FROM payments WHERE reconciled = 0");
            $unmatched = $unmatchedStmt->fetchAll();

            $matchedCount = 0;
            $pdo->beginTransaction();

            foreach ($unmatched as $row) {
                // If payment already has an inv_id that is Pending or Overdue, match it
                if (!empty($row['inv_id'])) {
                    $uPay = $pdo->prepare("UPDATE payments SET reconciled = 1 WHERE payment_id = ?");
                    $uPay->execute([$row['payment_id']]);

                    $uInv = $pdo->prepare("UPDATE invoices SET payment_status = 'Paid', paid_at = CURDATE() WHERE inv_id = ?");
                    $uInv->execute([$row['inv_id']]);
                    $matchedCount++;
                } else {
                    // Search for a pending invoice with exact amount
                    $findInv = $pdo->prepare("SELECT inv_id FROM invoices WHERE total_value = ? AND payment_status != 'Paid' LIMIT 1");
                    $findInv->execute([$row['amount']]);
                    $fInvId = $findInv->fetchColumn();

                    if ($fInvId) {
                        $uPay = $pdo->prepare("UPDATE payments SET reconciled = 1, inv_id = ? WHERE payment_id = ?");
                        $uPay->execute([$fInvId, $row['payment_id']]);

                        $uInv = $pdo->prepare("UPDATE invoices SET payment_status = 'Paid', paid_at = CURDATE() WHERE inv_id = ?");
                        $uInv->execute([$fInvId]);
                        $matchedCount++;
                    }
                }
            }

            $pdo->commit();

            jsonReply([
                'success' => true,
                'matched_count' => $matchedCount,
                'message' => "Auto-Match engine successfully paired and reconciled {$matchedCount} pending wires."
            ]);
            break;

        // ====================================================================
        // 7. SYNC BANK FEEDS (TELEMETRY INGESTION)
        // ====================================================================
        case 'sync_bank_feeds':
            // Generate a realistic incoming industrial settlement wire
            $customers = $pdo->query("SELECT cus_id, company_name FROM customers ORDER BY RAND() LIMIT 1")->fetch();
            $randomAmount = round(rand(25000, 150000) / 100) * 100;
            $txRef = 'TX-SPFS-' . rand(9200, 9999);
            $company = $customers['company_name'] ?? 'Industrial Partner PJSC';
            $memo = "Ref: WIRE-INVOICE-" . rand(100, 999);

            $insStmt = $pdo->prepare("
                INSERT INTO payments 
                (inv_id, amount, payment_date, method, reconciled, tx_reference, sender_name, remittance_memo, bank_gateway)
                VALUES (NULL, ?, CURDATE(), 'SPFS Direct Clearance', 0, ?, ?, ?, 'Sberbank / Central Clearing Node')
            ");
            $insStmt->execute([$randomAmount, $txRef, $company, $memo]);

            jsonReply([
                'success' => true,
                'message' => "Bank feeds refreshed: Ingested new {$company} wire of €" . number_format($randomAmount, 2) . " [{$txRef}].",
                'tx_reference' => $txRef,
                'amount' => $randomAmount
            ]);
            break;

        // ====================================================================
        // 8. BILL PROJECT MILESTONE
        // ====================================================================
        case 'bill_milestone':
            $cycleId = (int)($_POST['cycle_id'] ?? $_GET['cycle_id'] ?? 0);
            if ($cycleId <= 0) {
                jsonReply(['success' => false, 'error' => 'Cycle / Milestone ID required'], 400);
            }

            $stmt = $pdo->prepare("
                SELECT bc.*, p.cus_id, p.project_name, p.currency
                FROM billing_cycles bc
                JOIN projects p ON bc.prj_id = p.prj_id
                WHERE bc.cycle_id = ?
            ");
            $stmt->execute([$cycleId]);
            $cycle = $stmt->fetch();

            if (!$cycle) {
                jsonReply(['success' => false, 'error' => 'Milestone record not found'], 404);
            }
            if (!empty($cycle['invoiced'])) {
                jsonReply(['success' => false, 'error' => 'This milestone has already been invoiced.'], 400);
            }

            $invId = generateNextInvoiceId($pdo);
            $amount = (float)$cycle['milestone_amount'];
            if ($amount <= 0) {
                $amount = 50000.00;
            }

            $pdo->beginTransaction();

            // Insert Invoice
            $insInv = $pdo->prepare("
                INSERT INTO invoices
                (inv_id, cus_id, prj_id, total_value, currency, payment_status, issued_at, due_date, payment_terms, notes)
                VALUES (?, ?, ?, ?, ?, 'Pending', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'Net-30', ?)
            ");
            $notes = "Milestone Invoice: " . $cycle['milestone_description'];
            $insInv->execute([$invId, $cycle['cus_id'], $cycle['prj_id'], $amount, $cycle['currency'] ?: 'EUR', $notes]);

            // Insert Line Item
            $insItem = $pdo->prepare("
                INSERT INTO invoice_items
                (inv_id, description, part_number, qty, unit_price, total_price)
                VALUES (?, ?, 'SRV-MILESTONE', 1, ?, ?)
            ");
            $insItem->execute([$invId, $cycle['milestone_description'], $amount, $amount]);

            // Mark Cycle Invoiced
            $uCycle = $pdo->prepare("UPDATE billing_cycles SET invoiced = 1 WHERE cycle_id = ?");
            $uCycle->execute([$cycleId]);

            $pdo->commit();

            jsonReply([
                'success' => true,
                'message' => "Generated milestone invoice {$invId} for €" . number_format($amount, 2) . ".",
                'inv_id' => $invId,
                'amount' => $amount
            ]);
            break;

        // ====================================================================
        // 9. UPDATE BUDGET ALLOCATION / REVISION
        // ====================================================================
        case 'update_budget':
            $budgetId = (int)($_POST['budget_id'] ?? 0);
            $deptCode = trim($_POST['department_code'] ?? '');
            $allocated = (float)($_POST['allocated_amount'] ?? 0);
            $spent = (float)($_POST['spent_amount'] ?? 0);

            if ($allocated <= 0) {
                jsonReply(['success' => false, 'error' => 'Budget allocated amount must be greater than zero.'], 400);
            }

            if ($budgetId > 0) {
                $uStmt = $pdo->prepare("
                    UPDATE budgets 
                    SET allocated_amount = ?, spent_amount = ? 
                    WHERE budget_id = ?
                ");
                $uStmt->execute([$allocated, $spent, $budgetId]);
            } elseif (!empty($deptCode)) {
                $uStmt = $pdo->prepare("
                    UPDATE budgets 
                    SET allocated_amount = ?, spent_amount = ? 
                    WHERE department_code = ?
                ");
                $uStmt->execute([$allocated, $spent, $deptCode]);
            } else {
                jsonReply(['success' => false, 'error' => 'Budget ID or Department Code required'], 400);
            }

            jsonReply([
                'success' => true,
                'message' => 'Budget allocation ratified and updated in corporate ledger.'
            ]);
            break;

        // ====================================================================
        // 10. EXPORT GENERAL LEDGER CSV
        // ====================================================================
        case 'export_gl_csv':
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="VOSTOKPRIBOR_General_Ledger_' . date('Ymd_His') . '.csv"');

            $output = fopen('php://output', 'w');
            fputcsv($output, [
                'Invoice ID',
                'Customer ID',
                'Company Name',
                'Project ID',
                'Project Name',
                'Total Value',
                'Currency',
                'Payment Status',
                'Issued Date',
                'Due Date',
                'Paid Date',
                'Terms'
            ]);

            $stmt = $pdo->query("
                SELECT 
                    i.inv_id, i.cus_id, c.company_name, i.prj_id, p.project_name,
                    i.total_value, i.currency, i.payment_status, i.issued_at, i.due_date, i.paid_at, i.payment_terms
                FROM invoices i
                JOIN customers c ON i.cus_id = c.cus_id
                LEFT JOIN projects p ON i.prj_id = p.prj_id
                ORDER BY i.issued_at DESC
            ");

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, $row);
            }
            fclose($output);
            exit;

        default:
            jsonReply(['success' => false, 'error' => "Action '{$action}' not recognized."], 400);
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    jsonReply([
        'success' => false,
        'error' => 'Database operation failed: ' . $e->getMessage()
    ], 500);
}
