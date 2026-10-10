<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Cross-System Integration & Orchestration Service
 * Connects all 11 enterprise front-end systems:
 * 1. Store System (SHP) -> Invoicing System (FIN) -> File Management System (DOC)
 * 2. Customer Portal (CUS) <-> HR System (HR) Service Requests & Technician Dispatch
 * 3. HR System (HR) -> Customer Portal (CUS) Published Job Postings
 * 4. HR System (HR) -> SuperAdmin Privileged Equal Manager Appointments
 * 5. Universal Event Auditing in system_integration_logs
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/AuditLogger.php';
require_once __DIR__ . '/integration_bus.php';

/**
 * Generate official sequential Invoice ID (INV-2026-xxx) via locked counter
 */
function vostok_getNextInvoiceId(PDO $pdo): string
{
    $stmt = $pdo->prepare("SELECT next_val FROM id_counters WHERE name = 'invoices' FOR UPDATE");
    $stmt->execute();
    $nextVal = (int)$stmt->fetchColumn();
    if ($nextVal < 1) $nextVal = 11;
    $pdo->prepare("UPDATE id_counters SET next_val = ? WHERE name = 'invoices'")->execute([$nextVal + 1]);
    return sprintf('INV-2026-%03d', $nextVal);
}

/**
 * Generate official sequential Document ID (DOC-2026-xxx) via locked counter
 */
function vostok_getNextDocumentId(PDO $pdo): string
{
    $stmt = $pdo->prepare("SELECT next_val FROM id_counters WHERE name = 'documents' FOR UPDATE");
    $stmt->execute();
    $nextVal = (int)$stmt->fetchColumn();
    if ($nextVal < 1) $nextVal = 16;
    $pdo->prepare("UPDATE id_counters SET next_val = ? WHERE name = 'documents'")->execute([$nextVal + 1]);
    return sprintf('DOC-2026-%03d', $nextVal);
}

/**
 * Automated Workflow: Store System Order -> Invoicing System Invoice -> File Center Vault Document
 *
 * @param PDO $pdo Active database connection
 * @param int $orderId Order ID from orders table
 * @param string $cusId Customer ID (e.g. CUS-1001)
 * @param float $totalAmount Total monetary value
 * @param array $items Optional array of items [['prod_id' => ..., 'name' => ..., 'qty' => ..., 'price' => ...]]
 * @return array{success: bool, inv_id: string, doc_id: string, file_name: string, file_path: string}
 */
function vostok_generateInvoiceAndFileForOrder(PDO $pdo, int $orderId, string $cusId, float $totalAmount, array $items = []): array
{
    try {
        // 1. Fetch customer details
        $stmtCus = $pdo->prepare("SELECT * FROM customers WHERE cus_id = ?");
        $stmtCus->execute([$cusId]);
        $customer = $stmtCus->fetch(PDO::FETCH_ASSOC) ?: [
            'cus_id' => $cusId,
            'company_name' => 'Industrial Client Organization',
            'primary_contact_name' => 'Procurement Officer',
            'primary_contact_email' => 'billing@client-org.kz',
            'phone' => '+7 (727) 349-8800',
            'headquarters' => 'Kazakhstan'
        ];
        $customerEmail = $customer['primary_contact_email'] ?? ($customer['email'] ?? 'billing@client-org.kz');
        $customerLocation = $customer['headquarters'] ?? ($customer['country'] ?? 'Kazakhstan');

        // 2. Fetch order items if not supplied
        if (empty($items)) {
            $stmtItems = $pdo->prepare("
                SELECT oi.quantity, oi.unit_price, p.prod_id, p.name, p.model_number, p.category
                FROM order_items oi
                LEFT JOIN products p ON oi.prod_id = p.prod_id
                WHERE oi.order_id = ?
            ");
            $stmtItems->execute([$orderId]);
            $rawItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rawItems as $ri) {
                $items[] = [
                    'prod_id'    => $ri['prod_id'] ?? 'VP-ITEM',
                    'name'       => $ri['name'] ?? 'Industrial Measurement Unit',
                    'model'      => $ri['model_number'] ?? 'VP-STD',
                    'qty'        => (int)($ri['quantity'] ?? 1),
                    'unit_price' => (float)($ri['unit_price'] ?? 0),
                    'total'      => (float)($ri['quantity'] ?? 1) * (float)($ri['unit_price'] ?? 0)
                ];
            }
        }

        // 3. Generate Next Invoice ID
        $invId = vostok_getNextInvoiceId($pdo);
        $issuedAt = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime('+30 days'));

        // 4. Insert into invoices table
        $insInv = $pdo->prepare("
            INSERT INTO invoices 
            (inv_id, cus_id, prj_id, total_value, currency, payment_status, issued_at, due_date, payment_terms, notes)
            VALUES (?, ?, NULL, ?, 'USD', 'Pending', ?, ?, 'Net-30', ?)
        ");
        $insInv->execute([
            $invId,
            $cusId,
            $totalAmount,
            $issuedAt,
            $dueDate,
            "Automated Commercial Invoice for B2B Store Order #{$orderId}"
        ]);

        // 5. Insert line items into invoice_items
        $insItem = $pdo->prepare("
            INSERT INTO invoice_items 
            (inv_id, description, part_number, qty, unit_price, total_price)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        if (!empty($items)) {
            foreach ($items as $it) {
                $desc = $it['name'] ?? 'Industrial Automation Component';
                $part = $it['model'] ?? $it['prod_id'] ?? 'VP-STD';
                $qty  = (int)($it['qty'] ?? 1);
                $uprc = (float)($it['unit_price'] ?? $totalAmount);
                $tot  = $qty * $uprc;
                $insItem->execute([$invId, $desc, $part, $qty, $uprc, $tot]);
            }
        } else {
            $insItem->execute([$invId, "B2B Store Order #{$orderId} Procurement Package", 'VP-B2B-ORD', 1, $totalAmount, $totalAmount]);
        }

        // 6. Build the physical HTML invoice file
        $invoiceRowsHtml = '';
        $subtotal = 0;
        foreach ($items as $idx => $it) {
            $lineTot = (float)($it['qty'] ?? 1) * (float)($it['unit_price'] ?? 0);
            $subtotal += $lineTot;
            $invoiceRowsHtml .= "
                <tr>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #334155; color: #94a3b8; font-size: 13px;'>" . ($idx + 1) . "</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #334155; font-size: 13px;'>
                        <strong style='color: #f1f5f9;'>" . htmlspecialchars((string)($it['name'] ?? 'Industrial Unit')) . "</strong>
                        <div style='color: #64748b; font-size: 11px; margin-top: 2px;'>PN: " . htmlspecialchars((string)($it['model'] ?? $it['prod_id'] ?? 'VP-STD')) . "</div>
                    </td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #334155; text-align: center; color: #cbd5e1; font-size: 13px;'>" . (int)($it['qty'] ?? 1) . "</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #334155; text-align: right; color: #cbd5e1; font-size: 13px;'>$" . number_format((float)($it['unit_price'] ?? 0), 2) . "</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #334155; text-align: right; color: #f1f5f9; font-weight: 600; font-size: 13px;'>$" . number_format($lineTot, 2) . "</td>
                </tr>
            ";
        }
        if (empty($invoiceRowsHtml)) {
            $invoiceRowsHtml = "
                <tr>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #334155; color: #94a3b8; font-size: 13px;'>1</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #334155; font-size: 13px;'>
                        <strong style='color: #f1f5f9;'>B2B Store Order #{$orderId} Procurement Package</strong>
                        <div style='color: #64748b; font-size: 11px; margin-top: 2px;'>PN: VP-B2B-ORD</div>
                    </td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #334155; text-align: center; color: #cbd5e1; font-size: 13px;'>1</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #334155; text-align: right; color: #cbd5e1; font-size: 13px;'>$" . number_format($totalAmount, 2) . "</td>
                    <td style='padding: 10px 12px; border-bottom: 1px solid #334155; text-align: right; color: #f1f5f9; font-weight: 600; font-size: 13px;'>$" . number_format($totalAmount, 2) . "</td>
                </tr>
            ";
        }

        $formattedTotal = number_format($totalAmount, 2);
        $fileName = "Invoice-{$invId}-Order-{$orderId}.html";

        $htmlInvoice = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Commercial Invoice {$invId} · VOSTOKPRIBOR</title>
    <style>
        body { font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif; background: #0b0f19; color: #e2e8f0; margin: 0; padding: 40px 20px; }
        .invoice-card { max-width: 820px; margin: 0 auto; background: #131b2e; border: 1px solid #1e293b; border-radius: 12px; padding: 40px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        .inv-header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 24px; border-bottom: 2px solid #2563eb; }
        .brand-title { font-size: 24px; font-weight: 800; letter-spacing: 1.5px; color: #f8fafc; margin: 0 0 4px 0; }
        .brand-sub { font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: #38bdf8; font-weight: 700; }
        .inv-badge { text-align: right; }
        .inv-badge-title { font-size: 22px; font-weight: 800; color: #f8fafc; margin: 0; letter-spacing: 1px; }
        .inv-badge-id { font-size: 14px; color: #60a5fa; font-weight: 600; margin-top: 4px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin: 32px 0; font-size: 13px; line-height: 1.6; }
        .info-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #64748b; font-weight: 700; margin-bottom: 8px; }
        .info-val { color: #cbd5e1; }
        .table-wrap { width: 100%; border-collapse: collapse; margin: 24px 0; }
        .table-wrap th { background: #1e293b; color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; padding: 12px; text-align: left; }
        .summary-box { display: flex; justify-content: flex-end; margin-top: 24px; }
        .summary-table { width: 300px; font-size: 14px; }
        .summary-table td { padding: 8px 12px; }
        .total-row { font-size: 18px; font-weight: 800; color: #38bdf8; border-top: 2px solid #2563eb; }
        .remittance { margin-top: 40px; padding: 20px; background: #0f172a; border: 1px solid #1e293b; border-radius: 8px; font-size: 12px; color: #94a3b8; line-height: 1.6; }
        .digital-seal { display: inline-block; padding: 4px 10px; background: rgba(37,99,235,0.15); border: 1px solid #2563eb; color: #60a5fa; border-radius: 4px; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="invoice-card">
        <div class="inv-header">
            <div>
                <div class="brand-title">VOSTOKPRIBOR</div>
                <div class="brand-sub">Industrial Telemetry &amp; Metrology Systems</div>
                <div style="font-size: 12px; color: #94a3b8; margin-top: 8px;">
                    Almaty High-Tech Industrial Zone, Bld. 14/2<br>
                    Republic of Kazakhstan · Tax ID: KZ-990421008819
                </div>
            </div>
            <div class="inv-badge">
                <div class="inv-badge-title">COMMERCIAL INVOICE</div>
                <div class="inv-badge-id">{$invId}</div>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">Linked Order: #{$orderId}</div>
                <div class="digital-seal">SYS-02 STORE GENERATED</div>
            </div>
        </div>

        <div class="info-grid">
            <div>
                <div class="info-label">Billed To (Client / Customer)</div>
                <div class="info-val">
                    <strong style="color: #f8fafc; font-size: 15px;">{$customer['company_name']}</strong><br>
                    Attn: {$customer['primary_contact_name']}<br>
                    Customer ID: {$cusId}<br>
                    Email: {$customerEmail}<br>
                    Location: {$customerLocation}
                </div>
            </div>
            <div>
                <div class="info-label">Invoice &amp; Payment Terms</div>
                <div class="info-val">
                    <strong>Issued Date:</strong> {$issuedAt}<br>
                    <strong>Payment Due Date:</strong> {$dueDate}<br>
                    <strong>Terms:</strong> Net-30 Days B2B Commercial<br>
                    <strong>Payment Status:</strong> <span style="color: #f59e0b; font-weight: 700;">Pending Settlement</span><br>
                    <strong>Currency:</strong> USD ($)
                </div>
            </div>
        </div>

        <table class="table-wrap">
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th>Product / Item Description</th>
                    <th style="text-align: center; width: 60px;">Qty</th>
                    <th style="text-align: right; width: 120px;">Unit Price</th>
                    <th style="text-align: right; width: 120px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                {$invoiceRowsHtml}
            </tbody>
        </table>

        <div class="summary-box">
            <table class="summary-table">
                <tr>
                    <td style="color: #94a3b8;">Subtotal:</td>
                    <td style="text-align: right; color: #cbd5e1;">\${$formattedTotal}</td>
                </tr>
                <tr>
                    <td style="color: #94a3b8;">VAT / Export Tax (0%):</td>
                    <td style="text-align: right; color: #cbd5e1;">\$0.00</td>
                </tr>
                <tr class="total-row">
                    <td>Total Due:</td>
                    <td style="text-align: right;">\${$formattedTotal}</td>
                </tr>
            </table>
        </div>

        <div class="remittance">
            <strong style="color: #cbd5e1;">Payment Remittance Instructions:</strong><br>
            Bank: Halyk Bank of Kazakhstan (Commercial Operations Div)<br>
            SWIFT: HSBKKZKX · IBAN: KZ896010002003847291<br>
            Beneficiary: JSC VOSTOKPRIBOR INDUSTRIAL AUTOMATION<br>
            Reference: <strong>{$invId} / ORD-{$orderId}</strong>
        </div>
    </div>
</body>
</html>
HTML;

        // 7. Save file in File Center and client repositories
        $fileCenterDir = __DIR__ . '/../File Center/uploads/invoices';
        if (!is_dir($fileCenterDir)) {
            @mkdir($fileCenterDir, 0777, true);
        }
        $fileCenterPath = $fileCenterDir . '/' . $fileName;
        file_put_contents($fileCenterPath, $htmlInvoice);

        // Copy to Store & Customer Portal uploads for instant retrieval
        $shopUploadsDir = __DIR__ . '/../Online Shop B2B/uploads/invoices';
        if (!is_dir($shopUploadsDir)) @mkdir($shopUploadsDir, 0777, true);
        @file_put_contents($shopUploadsDir . '/' . $fileName, $htmlInvoice);

        $cusUploadsDir = __DIR__ . '/../Customer Portal/uploads/invoices';
        if (!is_dir($cusUploadsDir)) @mkdir($cusUploadsDir, 0777, true);
        @file_put_contents($cusUploadsDir . '/' . $fileName, $htmlInvoice);

        $fileSizeFormatted = round(strlen($htmlInvoice) / 1024, 1) . ' KB';
        $fileHash = hash('sha256', $htmlInvoice);

        // 8. Register in documents table (File Center SYS-09)
        $docId = vostok_getNextDocumentId($pdo);
        $insDoc = $pdo->prepare("
            INSERT INTO documents 
            (doc_id, file_name, description, classification, folder, department, file_size, file_hash, status, retention_period, customer_ref, owning_system, owner_emp_id, related_cus_id, created_at, updated_at)
            VALUES (?, ?, ?, 'Internal', 'finance', 'FIN', ?, ?, 'Approved', '7y', ?, 'File Center', 'EMP-1003', ?, NOW(), NOW())
        ");
        $insDoc->execute([
            $docId,
            $fileName,
            "Commercial Invoice {$invId} for B2B Order #{$orderId} ({$cusId})",
            $fileSizeFormatted,
            $fileHash,
            $cusId,
            $cusId
        ]);

        // 9. Record cross-system integration logs via vp_emit
        try {
            vp_emit(
                $pdo,
                'SHP_TO_FIN',
                'SHP',
                'FIN',
                'INVOICE_GENERATED',
                [
                    'inv_id'       => $invId,
                    'order_id'     => $orderId,
                    'cus_id'       => $cusId,
                    'total_amount' => $totalAmount,
                    'summary'      => "Store System automatically generated invoice {$invId} for Order #{$orderId} value \${$totalAmount}",
                    'endpoint'     => '/api/invoices/create'
                ],
                $cusId,
                201
            );

            vp_emit(
                $pdo,
                'FIN_TO_DOC',
                'FIN',
                'DOC',
                'DOCUMENT_INGESTED',
                [
                    'doc_id'    => $docId,
                    'inv_id'    => $invId,
                    'file_name' => $fileName,
                    'summary'   => "Invoice file {$fileName} ingested into File Center vault as {$docId}",
                    'endpoint'  => '/api/documents/ingest'
                ],
                'SYSTEM',
                201
            );
        } catch (Throwable $e) {
            error_log("Failed in order integration logging: " . $e->getMessage());
        }

        return [
            'success'   => true,
            'inv_id'    => $invId,
            'doc_id'    => $docId,
            'file_name' => $fileName,
            'file_path' => $fileCenterPath
        ];
    } catch (Throwable $e) {
        error_log("vostok_generateInvoiceAndFileForOrder error: " . $e->getMessage());
        return [
            'success' => false,
            'error'   => $e->getMessage(),
            'inv_id'  => '',
            'doc_id'  => '',
            'file_name' => '',
            'file_path' => ''
        ];
    }
}

/**
 * Customer Portal -> HR System: Create Customer Service Request
 */
function vostok_createCustomerServiceRequest(PDO $pdo, array $data): array
{
    try {
        $cusId = trim((string)($data['cus_id'] ?? ''));
        if (empty($cusId)) {
            return ['success' => false, 'message' => 'Customer ID is required.'];
        }

        // Generate next request ID (SRV-2026-xxx)
        $reqId = vp_next_id($pdo, 'service_requests', 'SRV-2026-', 3);

        $serviceType = trim((string)($data['service_type'] ?? 'Calibration & Metrology'));
        $title       = trim((string)($data['title'] ?? 'Technical Service Dispatch Request'));
        $description = trim((string)($data['description'] ?? ''));
        $location    = trim((string)($data['facility_location'] ?? 'Customer Industrial Site'));
        $priority    = in_array($data['priority'] ?? '', ['Critical', 'High', 'Standard']) ? $data['priority'] : 'Standard';
        $reqDate     = !empty($data['requested_date']) ? $data['requested_date'] : date('Y-m-d', strtotime('+7 days'));

        $stmt = $pdo->prepare("
            INSERT INTO customer_service_requests
            (request_id, cus_id, service_type, title, description, facility_location, priority, requested_date, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Submitted', NOW())
        ");
        $stmt->execute([$reqId, $cusId, $serviceType, $title, $description, $location, $priority, $reqDate]);

        try {
            vp_emit(
                $pdo,
                'CUS_TO_IT',
                'CUS',
                'IT',
                'SERVICE_REQUEST_SUBMITTED',
                [
                    'request_id'   => $reqId,
                    'cus_id'       => $cusId,
                    'service_type' => $serviceType,
                    'summary'      => "Customer {$cusId} submitted service request {$reqId} ({$serviceType})",
                    'endpoint'     => '/api/service-requests/submit'
                ],
                $cusId,
                201
            );
        } catch (Throwable $e) {
            error_log("Failed to emit CUS_TO_IT: " . $e->getMessage());
        }

        return [
            'success'    => true,
            'request_id' => $reqId,
            'message'    => "Service request {$reqId} submitted and transmitted to HR Operations for engineering dispatch."
        ];
    } catch (Throwable $e) {
        error_log("vostok_createCustomerServiceRequest error: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * HR System -> Assign Engineer/Specialist to Customer Service Request
 */
function vostok_assignServiceRequestPersonnel(PDO $pdo, string $requestId, string $empId, ?string $notes = null): array
{
    try {
        // Fetch employee name
        $stmtEmp = $pdo->prepare("SELECT full_name, job_title, email FROM employees WHERE emp_id = ?");
        $stmtEmp->execute([$empId]);
        $emp = $stmtEmp->fetch(PDO::FETCH_ASSOC);

        if (!$emp) {
            return ['success' => false, 'message' => "Employee {$empId} not found."];
        }

        $stmt = $pdo->prepare("
            UPDATE customer_service_requests
            SET assigned_emp_id = ?,
                assigned_at = NOW(),
                status = 'Personnel Assigned',
                hr_notes = ?
            WHERE request_id = ?
        ");
        $stmt->execute([$empId, $notes, $requestId]);

        try {
            vp_emit(
                $pdo,
                'HR_TO_EMP',
                'HR',
                'EMP',
                'PERSONNEL_ASSIGNED',
                [
                    'request_id' => $requestId,
                    'emp_id'     => $empId,
                    'summary'    => "Assigned {$emp['full_name']} ({$empId}) to customer service request {$requestId}",
                    'endpoint'   => '/api/service-requests/assign'
                ],
                'SYSTEM',
                200
            );
        } catch (Throwable $e) {
            error_log("Failed to emit HR_TO_EMP: " . $e->getMessage());
        }

        return [
            'success' => true,
            'message' => "Assigned {$emp['full_name']} ({$emp['job_title']}) to request {$requestId}."
        ];
    } catch (Throwable $e) {
        error_log("vostok_assignServiceRequestPersonnel error: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Fetch Published Job Postings for Customer Portal & Corporate Platform
 */
function vostok_getPublishedJobPostings(PDO $pdo): array
{
    try {
        $stmt = $pdo->query("
            SELECT jp.*, d.dept_name
            FROM job_postings jp
            LEFT JOIN departments d ON jp.department_code = d.dept_code
            WHERE jp.is_published = 1
            ORDER BY jp.posting_id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        error_log("vostok_getPublishedJobPostings error: " . $e->getMessage());
        return [];
    }
}

/**
 * Super-Administrator Privilege: Appoint another manager with equal privileges via HR System
 * Strictly checked: ONLY SuperAdmin (SuperAdmin role in employee_roles) is authorized to invoke!
 */
function vostok_appointEqualManager(PDO $pdo, string $actingUserId, string $targetEmpId, string $newRoleTitle, ?string $deptCode = null): array
{
    try {
        // Look up acting user
        $acting = queryUserByCredentials($actingUserId);
        $actingClearance = $acting['clearance_level'] ?? '';
        $actingEmpId = $acting['emp_id'] ?? '';

        // STRICT SECURITY ENFORCEMENT:
        // Only the SuperAdmin role has authority to appoint managers with equal privileges!
        $isSuperAdmin = $acting && isSuperAdmin($acting);

        if (!$isSuperAdmin) {
            return [
                'success' => false,
                'error'   => 'Security Authorization Failure: Only the SuperAdmin role has authority to appoint managers with equal privileges.'
            ];
        }

        // Verify target employee exists
        $stmtTarget = $pdo->prepare("SELECT * FROM employees WHERE emp_id = ?");
        $stmtTarget->execute([$targetEmpId]);
        $target = $stmtTarget->fetch(PDO::FETCH_ASSOC);

        if (!$target) {
            return ['success' => false, 'error' => "Target employee {$targetEmpId} not found in directory."];
        }

        $pdo->beginTransaction();

        $targetDept = $deptCode ?: ($target['department_code'] ?: 'EXE');

        // Elevate clearance to L4 and set title
        $updEmp = $pdo->prepare("
            UPDATE employees
            SET clearance_level = 'L4',
                job_title = ?,
                department_code = ?,
                employment_status = 'Active'
            WHERE emp_id = ?
        ");
        $updEmp->execute([$newRoleTitle, $targetDept, $targetEmpId]);

        // Assign Executive Role 1 (Executive SuperAdmin / Equal Manager)
        $delRole = $pdo->prepare("DELETE FROM employee_roles WHERE emp_id = ?");
        $delRole->execute([$targetEmpId]);

        $insRole = $pdo->prepare("INSERT INTO employee_roles (emp_id, role_id, granted_at) VALUES (?, 1, NOW())");
        $insRole->execute([$targetEmpId]);

        // Record in audit_logs
        $auditMsg = "Super-Administrator ({$actingEmail}) appointed {$target['full_name']} ({$targetEmpId}) as {$newRoleTitle} with equal L4 Executive clearance.";
        $auditStmt = $pdo->prepare("
            INSERT INTO audit_logs 
            (actor_emp_id, actor_system, system_id, action, target_entity_type, target_entity_id, new_values, result, occurred_at)
            VALUES (?, 'HR System', 'HR', 'APPOINT_EQUAL_MANAGER', 'employees', ?, ?, 'SUCCESS', NOW())
        ");
        $auditStmt->execute([$actingEmpId ?: 'EMP-0001', $targetEmpId, json_encode(['job_title' => $newRoleTitle, 'clearance_level' => 'L4', 'role_id' => 1])]);

        // Record in system_integration_logs via vp_emit
        try {
            vp_emit(
                $pdo,
                'HR_TO_ADM',
                'HR',
                'ADM',
                'EQUAL_MANAGER_APPOINTED',
                [
                    'target_emp_id' => $targetEmpId,
                    'new_role'      => $newRoleTitle,
                    'governance'    => true,
                    'severity'      => 'High',
                    'summary'       => $auditMsg,
                    'description'   => $auditMsg,
                    'endpoint'      => '/api/managers/appoint'
                ],
                $actingEmpId ?: 'EMP-1004',
                200
            );
        } catch (Throwable $e) {
            error_log("Failed to emit HR_TO_ADM: " . $e->getMessage());
        }

        $pdo->commit();

        return [
            'success' => true,
            'message' => "Manager {$target['full_name']} ({$targetEmpId}) was successfully appointed as '{$newRoleTitle}' with equal L4 privileges."
        ];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("vostok_appointEqualManager error: " . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
