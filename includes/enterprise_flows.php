<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Enterprise Business Flows Engine
 * Implements centralized transactional workflows:
 * - Flow D: vp_process_order() (SHP -> CRM + FIN + DOC + OPS + CUS)
 * - Flow E: vp_reconcile_payment() (FIN -> CUS + DOC, with Separation of Duties)
 * - Flow F: vp_create_ticket() (CUS/All -> IT -> Escalation -> ADM)
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/integration_bus.php';
require_once __DIR__ . '/AuditLogger.php';

/**
 * Flow D: Process Order across SHP, FIN, CRM, OPS, CUS, and DOC
 *
 * @param PDO $pdo Active PDO connection
 * @param int|string $orderId Order ID
 * @return array<string, mixed> Processing summary
 */
function vp_process_order(PDO $pdo, int|string $orderId): array
{
    $startedTx = false;
    if (!$pdo->inTransaction()) {
        $pdo->beginTransaction();
        $startedTx = true;
    }

    try {
        // 1. Fetch order
        $orderStmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = :id FOR UPDATE");
        $orderStmt->execute([':id' => $orderId]);
        $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            throw new InvalidArgumentException("Order #{$orderId} does not exist.");
        }

        $cusId = $order['cus_id'];

        // 2. Fetch order items and resolve prices without flat fallback
        $itemsStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = :id");
        $itemsStmt->execute([':id' => $orderId]);
        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($items)) {
            throw new InvalidArgumentException("Order #{$orderId} has no line items.");
        }

        $totalOrderAmount = 0.0;
        $resolvedLines = [];

        foreach ($items as $item) {
            $prodId = $item['prod_id'];
            $qty = (int)$item['quantity'];
            $unitPrice = (float)($item['unit_price'] ?? 0.0);

            if ($unitPrice <= 0.0) {
                // Check customer custom pricing first
                $cpStmt = $pdo->prepare("SELECT special_price FROM customer_pricing WHERE cus_id = ? AND prod_id = ? LIMIT 1");
                $cpStmt->execute([$cusId, $prodId]);
                $customPrice = $cpStmt->fetchColumn();

                if ($customPrice !== false && (float)$customPrice > 0.0) {
                    $unitPrice = (float)$customPrice;
                } else {
                    // Check standard catalog product price
                    $pStmt = $pdo->prepare("SELECT price FROM products WHERE prod_id = ? LIMIT 1");
                    $pStmt->execute([$prodId]);
                    $prodPrice = $pStmt->fetchColumn();

                    if ($prodPrice !== false && (float)$prodPrice > 0.0) {
                        $unitPrice = (float)$prodPrice;
                    } else {
                        // REJECT: No flat 2500.00 fallback allowed!
                        throw new InvalidArgumentException("Item {$prodId} has no established price in products or customer_pricing. Order rejected.");
                    }
                }

                // Update unit_price in order_items
                $updItem = $pdo->prepare("UPDATE order_items SET unit_price = ? WHERE order_id = ? AND prod_id = ?");
                $updItem->execute([$unitPrice, $orderId, $prodId]);
            }

            $lineTotal = round($unitPrice * $qty, 2);
            $totalOrderAmount += $lineTotal;

            $resolvedLines[] = [
                'prod_id'    => $prodId,
                'qty'        => $qty,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal
            ];
        }

        // Update total_amount in orders
        $updOrder = $pdo->prepare("UPDATE orders SET total_amount = ?, status = 'Processing' WHERE order_id = ?");
        $updOrder->execute([$totalOrderAmount, $orderId]);

        // 3. Generate sequential invoice (INV-2026-xxx) via vp_next_id
        $invId = vp_next_id($pdo, 'invoices', 'INV-2026-', 3);

        // Check if customer has an active project link
        $prjStmt = $pdo->prepare("SELECT prj_id FROM projects WHERE cus_id = ? ORDER BY prj_id DESC LIMIT 1");
        $prjStmt->execute([$cusId]);
        $linkedPrjId = $prjStmt->fetchColumn() ?: null;

        $insInv = $pdo->prepare("
            INSERT INTO invoices 
            (inv_id, cus_id, prj_id, created_by_emp_id, total_value, currency, payment_status, issued_at, due_date, payment_terms, notes)
            VALUES 
            (:id, :cid, :pid, 'SHP-SYSTEM', :val, 'EUR', 'Pending', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'Net-30', :notes)
        ");
        $insInv->execute([
            ':id'    => $invId,
            ':cid'   => $cusId,
            ':pid'   => $linkedPrjId,
            ':val'   => $totalOrderAmount,
            ':notes' => "Automated B2B Purchase Order #{$orderId}"
        ]);

        // Insert invoice line items
        $insItem = $pdo->prepare("
            INSERT INTO invoice_items 
            (inv_id, description, part_number, qty, unit_price, total_price)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        foreach ($resolvedLines as $idx => $line) {
            $insItem->execute([
                $invId,
                "B2B Equipment Component {$line['prod_id']}",
                (string)$line['prod_id'],
                $line['qty'],
                $line['unit_price'],
                $line['line_total']
            ]);
        }

        // 4. Create billing document in File Center (DOC-2026-xxx) via vp_next_id
        $docId = vp_next_id($pdo, 'documents', 'DOC-2026-', 3);

        $insDoc = $pdo->prepare("
            INSERT INTO documents 
            (doc_id, file_name, description, classification, folder, department, file_size, status, retention_period, customer_ref, owner_emp_id, related_prj_id, related_cus_id, created_at)
            VALUES 
            (:did, :fname, :desc, 'Confidential', 'invoices', 'FIN', '245 KB', 'Approved', '7y', :cref, 'EMP-1004', :pid, :cid, NOW())
            ON DUPLICATE KEY UPDATE file_name = VALUES(file_name)
        ");
        $insDoc->execute([
            ':did'   => $docId,
            ':fname' => "Invoice-{$invId}-Order-{$orderId}.pdf",
            ':desc'  => "Automated commercial billing invoice for B2B Order #{$orderId}",
            ':cref'  => $cusId,
            ':pid'   => $linkedPrjId,
            ':cid'   => $cusId
        ]);

        // 5. Write CRM crm_activities entry on the customer
        $crmAct = $pdo->prepare("
            INSERT INTO crm_activities (activity_type, title, description, cus_id, emp_id, created_at)
            VALUES ('Order Placed', 'B2B Purchase Order', :desc, :cid, 'EMP-1006', NOW())
        ");
        $crmAct->execute([
            ':desc' => "Commercial B2B order #{$orderId} placed for €{$totalOrderAmount}. Invoice {$invId} generated.",
            ':cid'  => $cusId
        ]);

        // 6. Create OPS fulfilment task in ops_tasks
        $opsTaskId = 'OPS-ORD-' . $orderId;
        $insOps = $pdo->prepare("
            INSERT INTO ops_tasks 
            (task_id, order_id, prj_id, task_type, assigned_emp_id, status, created_at)
            VALUES 
            (:tid, :oid, :pid, 'Fulfilment', 'EMP-1012', 'Pending', NOW())
            ON DUPLICATE KEY UPDATE status = 'Pending'
        ");
        $insOps->execute([
            ':tid' => $opsTaskId,
            ':oid' => (string)$orderId,
            ':pid' => $linkedPrjId
        ]);

        // 7. Create portal_notifications row for the customer
        $accStmt = $pdo->prepare("SELECT account_id FROM customer_accounts WHERE cus_id = ? LIMIT 1");
        $accStmt->execute([$cusId]);
        $portalUserId = (int)($accStmt->fetchColumn() ?: 1);

        $notifStmt = $pdo->prepare("
            INSERT INTO portal_notifications 
            (portal_user_id, system_code, title, message, related_entity_type, related_entity_id, is_read, severity, created_at)
            VALUES 
            (:uid, 'SHP', :title, :msg, 'Order', :oid, 0, 'info', NOW())
        ");
        $notifStmt->execute([
            ':uid'   => $portalUserId,
            ':title' => "Order Confirmed: #{$orderId}",
            ':msg'   => "Your order #{$orderId} has been confirmed. Invoice {$invId} has been issued.",
            ':oid'   => (string)$orderId
        ]);

        // 8. Emit all required inter-system events:
        // SHP->FIN, SHP->CRM, SHP->OPS (EMP), SHP->CUS, FIN->DOC
        vp_emit($pdo, 'SHP_TO_FIN', 'SHP', 'FIN', 'ORDER_INVOICED', [
            'order_id'     => $orderId,
            'inv_id'       => $invId,
            'cus_id'       => $cusId,
            'total_amount' => $totalOrderAmount,
            'summary'      => "Invoice {$invId} generated for Order #{$orderId} (€{$totalOrderAmount})"
        ], $cusId, 201);

        vp_emit($pdo, 'SHP_TO_CRM', 'SHP', 'CRM', 'ORDER_ACTIVITY_LOGGED', [
            'order_id'     => $orderId,
            'cus_id'       => $cusId,
            'total_amount' => $totalOrderAmount,
            'summary'      => "Order #{$orderId} recorded in CRM pipeline for {$cusId}"
        ], $cusId, 201);

        vp_emit($pdo, 'SHP_TO_OPS', 'SHP', 'EMP', 'ORDER_FULFILMENT_QUEUED', [
            'order_id'     => $orderId,
            'ops_task_id'  => $opsTaskId,
            'cus_id'       => $cusId,
            'summary'      => "Fulfilment task {$opsTaskId} dispatched to Operations queue"
        ], $cusId, 201);

        vp_emit($pdo, 'SHP_TO_CUS', 'SHP', 'CUS', 'ORDER_CONFIRMED', [
            'order_id'     => $orderId,
            'inv_id'       => $invId,
            'cus_id'       => $cusId,
            'summary'      => "Order confirmation and invoice dispatched to {$cusId}"
        ], 'SHP-SYSTEM', 200);

        vp_emit($pdo, 'FIN_TO_DOC', 'FIN', 'DOC', 'INVOICE_DOCUMENT_STORED', [
            'inv_id'   => $invId,
            'doc_id'   => $docId,
            'cus_id'   => $cusId,
            'summary'  => "Billing invoice document {$docId} archived in File Center"
        ], 'FIN-SYSTEM', 201);

        if ($startedTx) {
            $pdo->commit();
        }

        return [
            'success'      => true,
            'order_id'     => $orderId,
            'inv_id'       => $invId,
            'invoice_id'   => $invId,
            'doc_id'       => $docId,
            'ops_task_id'  => $opsTaskId,
            'total_amount' => $totalOrderAmount,
            'status'       => 'Processing'
        ];
    } catch (Throwable $e) {
        if ($startedTx && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

/**
 * Flow E: Reconcile Payment (FIN -> CUS + DOC, with Separation of Duties)
 *
 * @param PDO $pdo Active PDO connection
 * @param string $invId Invoice ID
 * @param float $amount Payment amount
 * @param string $method Payment method (e.g. WireTransfer, CreditCard)
 * @param string $actor Acting user (e.g. EMP-1010, CUS-1001)
 * @return array<string, mixed> Reconciliation result
 */
function vp_reconcile_payment(
    PDO $pdo,
    string $invId,
    float $amount,
    string $method,
    string $actor
): array {
    if ($amount <= 0.0) {
        throw new InvalidArgumentException("Payment amount must be greater than zero.");
    }

    $startedTx = false;
    if (!$pdo->inTransaction()) {
        $pdo->beginTransaction();
        $startedTx = true;
    }

    try {
        // 1. Fetch invoice FOR UPDATE
        $invStmt = $pdo->prepare("SELECT * FROM invoices WHERE inv_id = :id FOR UPDATE");
        $invStmt->execute([':id' => $invId]);
        $inv = $invStmt->fetch(PDO::FETCH_ASSOC);

        if (!$inv) {
            throw new InvalidArgumentException("Invoice '{$invId}' not found.");
        }

        // 2. Separation of duties check:
        // The person who created the invoice cannot reconcile its payment.
        $creator = $inv['created_by_emp_id'] ?? null;
        if (empty($creator)) {
            // Check audit logs for invoice creation actor
            $creatorStmt = $pdo->prepare("
                SELECT actor_emp_id FROM audit_logs 
                WHERE target_entity_id = :id AND (action LIKE '%INVOICE%' OR action LIKE '%CREATE%') AND actor_emp_id IS NOT NULL 
                ORDER BY audit_id ASC LIMIT 1
            ");
            $creatorStmt->execute([':id' => $invId]);
            $creator = $creatorStmt->fetchColumn() ?: null;
        }

        if ($creator && $creator === $actor) {
            throw new InvalidArgumentException("Separation of duties violation: Invoice creator ({$actor}) cannot reconcile payments on invoice {$invId}.");
        }

        // 3. Record payment
        $txRef = 'TX-PAY-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $insPay = $pdo->prepare("
            INSERT INTO payments 
            (tx_reference, sender_name, inv_id, amount, payment_date, method, remittance_memo, bank_gateway, reconciled)
            VALUES 
            (:tx, :sender, :iid, :amt, CURDATE(), :method, :memo, 'SPFS / Central Clearing', 1)
        ");
        $insPay->execute([
            ':tx'     => $txRef,
            ':sender' => $actor,
            ':iid'    => $invId,
            ':amt'    => $amount,
            ':method' => $method,
            ':memo'   => "Electronic settlement for invoice {$invId} by {$actor}"
        ]);
        $paymentId = (int)$pdo->lastInsertId();

        // 4. Calculate total settled amount
        $sumStmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0.0) FROM payments WHERE inv_id = ?");
        $sumStmt->execute([$invId]);
        $totalSettled = (float)$sumStmt->fetchColumn();

        $totalValue = (float)$inv['total_value'];
        $newStatus = ($totalSettled >= $totalValue) ? 'Paid' : 'Pending';
        $paidDate = ($newStatus === 'Paid') ? date('Y-m-d') : null;

        $updInv = $pdo->prepare("UPDATE invoices SET payment_status = :st, paid_at = :pa WHERE inv_id = :iid");
        $updInv->execute([
            ':st'  => $newStatus,
            ':pa'  => $paidDate,
            ':iid' => $invId
        ]);

        // 5. Write audit row in audit_logs
        AuditLogger::logAction(
            str_starts_with($actor, 'EMP-') ? $actor : null,
            str_starts_with($actor, 'CUS-') ? $actor : ($inv['cus_id'] ?? null),
            'Finance & Billing',
            'FIN',
            'RECONCILE_PAYMENT',
            'invoices',
            $invId,
            [
                'payment_id'   => $paymentId,
                'amount'       => $amount,
                'total_settled'=> $totalSettled,
                'status'       => $newStatus
            ],
            'SUCCESS'
        );

        // 6. Notify customer via portal_notifications
        $accStmt = $pdo->prepare("SELECT account_id FROM customer_accounts WHERE cus_id = ? LIMIT 1");
        $accStmt->execute([$inv['cus_id']]);
        $portalUserId = (int)($accStmt->fetchColumn() ?: 1);

        $notifStmt = $pdo->prepare("
            INSERT INTO portal_notifications 
            (portal_user_id, system_code, title, message, related_entity_type, related_entity_id, is_read, severity, created_at)
            VALUES 
            (:uid, 'FIN', :title, :msg, 'Invoice', :iid, 0, 'info', NOW())
        ");
        $notifStmt->execute([
            ':uid'   => $portalUserId,
            ':title' => "Payment Reconciled: {$invId}",
            ':msg'   => "Payment of €{$amount} reconciled for invoice {$invId}. New status: {$newStatus}.",
            ':iid'   => $invId
        ]);

        // 7. Emit FIN->CUS and FIN->DOC
        vp_emit($pdo, 'FIN_TO_CUS', 'FIN', 'CUS', 'PAYMENT_RECONCILED', [
            'inv_id'         => $invId,
            'cus_id'         => $inv['cus_id'],
            'amount'         => $amount,
            'payment_status' => $newStatus,
            'summary'        => "Payment of €{$amount} reconciled for invoice {$invId} ({$newStatus})"
        ], $actor, 200);

        vp_emit($pdo, 'FIN_TO_DOC', 'FIN', 'DOC', 'PAYMENT_RECEIPT_ARCHIVED', [
            'inv_id'         => $invId,
            'payment_id'     => $paymentId,
            'summary'        => "Payment remittance memo archived for invoice {$invId}"
        ], $actor, 200);

        if ($startedTx) {
            $pdo->commit();
        }

        return [
            'success'        => true,
            'payment_id'     => $paymentId,
            'tx_reference'   => $txRef,
            'inv_id'         => $invId,
            'payment_status' => $newStatus,
            'total_settled'  => $totalSettled
        ];
    } catch (Throwable $e) {
        if ($startedTx && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

/**
 * Flow F: Create Ticket with SLA calculation, auto-assignment, and critical escalation
 *
 * @param PDO $pdo Active PDO connection
 * @param array<string, mixed> $data Ticket data
 * @param string $actor Acting user (e.g. CUS-1001, EMP-1016)
 * @return string Generated Ticket ID (TKT-2026-xxx)
 */
function vp_create_ticket(PDO $pdo, array $data, string $actor): string
{
    $title    = trim((string)($data['title'] ?? 'Technical Service Request'));
    $desc     = trim((string)($data['description'] ?? ''));
    $priority = ucfirst(strtolower(trim((string)($data['priority'] ?? 'Medium'))));
    if (!in_array($priority, ['Low', 'Medium', 'High', 'Critical'], true)) {
        $priority = 'Medium';
    }

    $sourceSys = canonicalSystemCode((string)($data['source_system'] ?? (str_starts_with($actor, 'CUS-') ? 'CUS' : 'EMP')));

    $startedTx = false;
    if (!$pdo->inTransaction()) {
        $pdo->beginTransaction();
        $startedTx = true;
    }

    try {
        // 1. Next TKT ID via vp_next_id
        $tktId = vp_next_id($pdo, 'tickets', 'TKT-2026-', 3);

        // 2. SLA deadline lookup from sla_policies
        $slaStmt = $pdo->prepare("SELECT resolution_time_hours, resolution_time_minutes FROM sla_policies WHERE priority = ? LIMIT 1");
        $slaStmt->execute([$priority]);
        $slaRow = $slaStmt->fetch(PDO::FETCH_ASSOC);

        $hours = match ($priority) {
            'Critical' => 2,
            'High'     => 8,
            'Medium'   => 24,
            default    => 72
        };
        if ($slaRow) {
            if (!empty($slaRow['resolution_time_hours'])) {
                $hours = (int)$slaRow['resolution_time_hours'];
            } elseif (!empty($slaRow['resolution_time_minutes'])) {
                $hours = max(1, (int)round($slaRow['resolution_time_minutes'] / 60));
            }
        }
        $slaDeadline = date('Y-m-d H:i:s', strtotime("+{$hours} hours"));

        // 3. Auto-assign IT specialist
        $itTechs = $pdo->query("
            SELECT emp_id FROM employees 
            WHERE department_code IN ('ITD', 'IT') AND employment_status = 'Active' 
            ORDER BY emp_id ASC
        ")->fetchAll(PDO::FETCH_COLUMN);

        $tktNum = (int)preg_replace('/\D/', '', $tktId);
        $assignedTech = !empty($itTechs) ? $itTechs[$tktNum % count($itTechs)] : 'EMP-1004';

        // 4. Resolve requester info
        $reqType = str_starts_with($actor, 'CUS-') ? 'Customer' : 'Employee';
        $reqCusId = ($reqType === 'Customer') ? $actor : ($data['cus_id'] ?? null);
        $reqEmpId = ($reqType === 'Employee') ? $actor : ($data['emp_id'] ?? null);

        $reqName = (string)($data['requester_name'] ?? 'Authorized Requester');
        $reqRole = (string)($data['requester_role'] ?? 'Specialist');
        $reqDept = (string)($data['requester_dept'] ?? 'Operations');

        // 5. Insert ticket
        $tktStatus = ($priority === 'Critical') ? 'Escalated' : 'Open';

        $insTkt = $pdo->prepare("
            INSERT INTO tickets 
            (tkt_id, requester_type, requester_cus_id, requester_emp_id, source_system, title, description, requester_name, requester_role, requester_dept, priority, assigned_emp_id, status, sla_deadline, created_at)
            VALUES 
            (:tid, :rtype, :rcid, :reid, :src, :title, :desc, :rname, :rrole, :rdept, :prio, :ass, :st, :sla, NOW())
        ");
        $insTkt->execute([
            ':tid'   => $tktId,
            ':rtype' => $reqType,
            ':rcid'  => $reqCusId,
            ':reid'  => $reqEmpId,
            ':src'   => $sourceSys,
            ':title' => $title,
            ':desc'  => $desc,
            ':rname' => $reqName,
            ':rrole' => $reqRole,
            ':rdept' => $reqDept,
            ':prio'  => $priority,
            ':ass'   => $assignedTech,
            ':st'    => $tktStatus,
            ':sla'   => $slaDeadline
        ]);

        // 6. If priority = Critical, insert ticket_escalations and emit governance event to ADM
        if ($priority === 'Critical') {
            $insEsc = $pdo->prepare("
                INSERT INTO ticket_escalations 
                (tkt_id, escalated_to_emp_id, escalated_at, reason, status)
                VALUES 
                (:tid, 'EMP-1004', NOW(), 'Critical incident automatic escalation to Executive Governance', 'Escalated')
            ");
            $insEsc->execute([':tid' => $tktId]);

            vp_emit($pdo, 'IT_TO_ADM', 'IT', 'ADM', 'CRITICAL_TICKET_ESCALATED', [
                'tkt_id'      => $tktId,
                'priority'    => 'Critical',
                'governance'  => true,
                'severity'    => 'Critical',
                'description' => "CRITICAL INCIDENT: {$title} auto-escalated to Executive Governance [{$tktId}]",
                'summary'     => "Critical ticket {$tktId} auto-escalated to Executive Governance"
            ], $actor, 201);
        }

        // 7. Customer notification if requester is customer
        if (!empty($reqCusId)) {
            $accStmt = $pdo->prepare("SELECT account_id FROM customer_accounts WHERE cus_id = ? LIMIT 1");
            $accStmt->execute([$reqCusId]);
            $uid = (int)($accStmt->fetchColumn() ?: 1);

            $pdo->prepare("
                INSERT INTO portal_notifications 
                (portal_user_id, system_code, title, message, related_entity_type, related_entity_id, is_read, severity, created_at)
                VALUES 
                (:uid, 'IT', :title, :msg, 'Ticket', :tid, 0, :sev, NOW())
            ")->execute([
                ':uid'   => $uid,
                ':title' => "Support Ticket Registered: {$tktId}",
                ':msg'   => "Your service ticket {$tktId} has been logged with {$priority} priority. SLA Target: {$slaDeadline}.",
                ':tid'   => $tktId,
                ':sev'   => ($priority === 'Critical') ? 'critical' : 'info'
            ]);

            vp_emit($pdo, 'CUS_TO_IT', 'CUS', 'IT', 'TICKET_CREATED', [
                'tkt_id'   => $tktId,
                'cus_id'   => $reqCusId,
                'priority' => $priority,
                'summary'  => "Customer {$reqCusId} opened ticket {$tktId} ({$priority})"
            ], $actor, 201);
        } else {
            vp_emit($pdo, 'EMP_TO_ADM', 'EMP', 'ADM', 'INTERNAL_TICKET_CREATED', [
                'tkt_id'   => $tktId,
                'emp_id'   => $reqEmpId,
                'priority' => $priority,
                'summary'  => "Internal employee ticket {$tktId} dispatched to IT"
            ], $actor, 201);
        }

        if ($startedTx) {
            $pdo->commit();
        }

        return $tktId;
    } catch (Throwable $e) {
        if ($startedTx && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}
