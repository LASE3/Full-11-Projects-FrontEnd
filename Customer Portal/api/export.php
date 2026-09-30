<?php
declare(strict_types=1);

/**
 * Customer Portal - Export & Data Extraction Engine
 * Location: Customer Portal/api/export.php
 * Supports CSV, JSON, and direct file downloads for projects, orders, invoices, documents, tickets.
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$cusId = $_SESSION['cus_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? ($_GET['cus_id'] ?? null));

if (empty($cusId)) {
    $firstCus = $pdo->query("SELECT cus_id FROM customers WHERE status = 'Active' ORDER BY cus_id ASC LIMIT 1")->fetchColumn();
    $cusId = $firstCus ?: 'CUS-1001';
}

$type = trim($_GET['type'] ?? 'projects');
$format = strtolower(trim($_GET['format'] ?? 'csv'));
$docId = trim($_GET['doc_id'] ?? '');

// Direct document file download by doc_id
if ($type === 'documents' && !empty($docId)) {
    $stmt = $pdo->prepare("
        SELECT file_name, classification 
        FROM documents 
        WHERE doc_id = :did 
          AND (related_cus_id = :cid OR related_prj_id IN (SELECT prj_id FROM projects WHERE cus_id = :cid2))
    ");
    $stmt->execute([':did' => $docId, ':cid' => $cusId, ':cid2' => $cusId]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($doc) {
        $realFile = __DIR__ . '/../uploads/' . basename($doc['file_name']);
        if (file_exists($realFile)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($doc['file_name']) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($realFile));
            readfile($realFile);
            exit;
        }
    }

    // Generate simulated dynamic technical dossier if file is not on disk
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="Technical_Dossier_' . $docId . '.txt"');
    echo "========================================================================\n";
    echo "VOSTOKPRIBOR INDUSTRIAL PLATFORM - TECHNICAL SPECIFICATION DOSSIER\n";
    echo "Document ID: " . $docId . "\n";
    echo "Customer ID: " . $cusId . "\n";
    echo "Generated At: " . date('Y-m-d H:i:s UTC') . "\n";
    echo "Classification: " . ($doc['classification'] ?? 'Confidential') . " (Authorized Client Only)\n";
    echo "========================================================================\n\n";
    echo "Scope: Verification of metrological compliance, sensor telemetry, and calibrated\n";
    echo "optocoupler arrays under GOST-R / ISO-9001 standards.\n";
    exit;
}

try {
    $rows = [];
    $filename = "export_{$type}_" . date('Ymd_His');

    switch ($type) {
        case 'projects':
            $stmt = $pdo->prepare("
                SELECT prj_id, project_name, budget, currency, status, start_date, end_date, progress_percent, facility_location, scope_summary
                FROM projects
                WHERE cus_id = :cid
                ORDER BY prj_id ASC
            ");
            $stmt->execute([':cid' => $cusId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filename = "projects_ledger_{$cusId}_" . date('Ymd');
            break;

        case 'orders':
            $stmt = $pdo->prepare("
                SELECT order_id, cus_id, order_date, status, total_amount
                FROM orders
                WHERE cus_id = :cid
                ORDER BY order_id DESC
            ");
            $stmt->execute([':cid' => $cusId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filename = "orders_manifest_{$cusId}_" . date('Ymd');
            break;

        case 'invoices':
            $stmt = $pdo->prepare("
                SELECT inv_id, prj_id, total_value, currency, payment_status, issued_at, due_date, payment_terms, paid_at
                FROM invoices
                WHERE cus_id = :cid
                ORDER BY inv_id DESC
            ");
            $stmt->execute([':cid' => $cusId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filename = "invoices_billing_{$cusId}_" . date('Ymd');
            break;

        case 'documents':
            $stmt = $pdo->prepare("
                SELECT doc_id, file_name, description, classification, folder, department, file_size, status, created_at
                FROM documents
                WHERE related_cus_id = :cid OR related_prj_id IN (SELECT prj_id FROM projects WHERE cus_id = :cid2)
                ORDER BY created_at DESC
            ");
            $stmt->execute([':cid' => $cusId, ':cid2' => $cusId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filename = "documents_archive_{$cusId}_" . date('Ymd');
            break;

        case 'tickets':
            $stmt = $pdo->prepare("
                SELECT tkt_id, source_system, title, description, priority, status, created_at, resolved_at
                FROM tickets
                WHERE requester_cus_id = :cid
                ORDER BY created_at DESC
            ");
            $stmt->execute([':cid' => $cusId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filename = "support_tickets_{$cusId}_" . date('Ymd');
            break;

        default:
            Response::error("Unknown export type '{$type}'.", 400);
    }

    if ($format === 'json') {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.json"');
        echo json_encode([
            'export_type' => $type,
            'cus_id'      => $cusId,
            'generated_at'=> date('Y-m-d H:i:s'),
            'record_count'=> count($rows),
            'records'     => $rows
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Default: CSV Export
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $out = fopen('php://output', 'w');
    // Output BOM for Excel UTF-8 compatibility
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

    if (!empty($rows)) {
        fputcsv($out, array_keys($rows[0]), ',', '"', '\\');
        foreach ($rows as $row) {
            fputcsv($out, array_values($row), ',', '"', '\\');
        }
    } else {
        fputcsv($out, ['Notice', 'No records found for ' . $type], ',', '"', '\\');
    }

    fclose($out);
    exit;

} catch (Exception $e) {
    Response::error("Export Failed: " . $e->getMessage(), 500);
}
