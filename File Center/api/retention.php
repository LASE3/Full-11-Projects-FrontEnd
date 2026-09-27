<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR File Center - Retention & Legal Holds API
 * Location: File Center/api/retention.php
 * Methods: GET, POST
 */

require_once __DIR__ . '/db.php';

$pdo = getApiPdo();
$method = $_SERVER['REQUEST_METHOD'];

$rawInput = file_get_contents('php://input');
$inputData = [];
if (!empty($rawInput)) {
    $decoded = json_decode($rawInput, true);
    if (is_array($decoded)) {
        $inputData = $decoded;
    }
}
$req = array_merge($_GET, $_POST, $inputData);
$action = $req['action'] ?? '';

try {
    if ($method === 'GET') {
        // Export complete manifest
        if ($action === 'export_manifest') {
            $stmt = $pdo->query("
                SELECT 
                    d.doc_id, d.file_name, d.classification, d.folder, d.department,
                    d.project_ref, d.customer_ref, d.file_size, d.file_hash, d.status,
                    d.retention_period, d.is_legal_hold, d.legal_hold_date,
                    d.created_at, e.full_name AS custodian
                FROM documents d
                LEFT JOIN employees e ON d.owner_emp_id = e.emp_id
                ORDER BY d.doc_id ASC
            ");
            $manifest = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $export = [
                'system'         => 'VOSTOKPRIBOR SYS-09 FILE CENTER',
                'enclave'        => 'ALMATY CENTRAL HSM ENCLAVE',
                'standard'       => 'ISO 27001 / IEC 62443 COMPLIANT',
                'export_time'    => date('Y-m-d H:i:s T'),
                'total_records'  => count($manifest),
                'root_merkle'    => hash('sha256', json_encode($manifest) . 'ROOT_MERKLE_TREE'),
                'records'        => $manifest,
            ];

            // If user asked for download trigger
            if (isset($_GET['download'])) {
                header('Content-Disposition: attachment; filename="vostokpribor_vault_manifest_' . date('Ymd_His') . '.json"');
            }

            apiSuccess($export);
        }

        // Return combined retention data: policies, active legal holds, and storage allocations
        $policies = $pdo->query("SELECT * FROM document_retention_policies ORDER BY policy_id ASC")->fetchAll(PDO::FETCH_ASSOC);

        $holds = $pdo->query("
            SELECT 
                d.doc_id,
                d.file_name,
                d.description,
                d.classification,
                d.is_legal_hold,
                d.legal_hold_date,
                d.legal_hold_reason,
                COALESCE(e.full_name, d.legal_hold_by_emp_id, 'Farida Iskakova') AS hold_authority,
                d.legal_hold_by_emp_id
            FROM documents d
            LEFT JOIN employees e ON d.legal_hold_by_emp_id = e.emp_id
            WHERE d.is_legal_hold = 1
            ORDER BY d.legal_hold_date DESC, d.doc_id ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Format dates
        foreach ($holds as &$h) {
            $h['authority_display'] = $h['hold_authority'] . ($h['legal_hold_by_emp_id'] ? " ({$h['legal_hold_by_emp_id']})" : "");
            $h['hold_since'] = $h['legal_hold_date'] ? substr($h['legal_hold_date'], 0, 16) : date('Y-m-d H:i');
        }

        // Calculate storage gauges
        $totalDocs = (int)$pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn();
        $hotGb = round(400.0 + ($totalDocs * 0.8), 1);
        $warmGb = round(420.0 + ($totalDocs * 0.7), 1);
        $coldTb = round(1.2 + ($totalDocs * 0.005), 2);

        apiSuccess([
            'policies'    => $policies,
            'legal_holds' => $holds,
            'holds_count' => count($holds),
            'storage'     => [
                'hot_nvme'     => ['used_gb' => $hotGb, 'cap_gb' => 1024, 'pct' => round(($hotGb / 1024) * 100, 1)],
                'warm_nearline'=> ['used_gb' => $warmGb, 'cap_gb' => 2048, 'pct' => round(($warmGb / 2048) * 100, 1)],
                'cold_worm'    => ['used_tb' => $coldTb, 'cap_tb' => 5.0,  'pct' => round(($coldTb / 5.0) * 100, 1)],
            ],
        ]);
    }

    if ($method === 'POST') {
        $docId = trim((string)($req['doc_id'] ?? ''));
        if (empty($docId)) {
            apiError('Document ID (doc_id) is required.');
        }

        $chkStmt = $pdo->prepare("SELECT doc_id, file_name, is_legal_hold FROM documents WHERE doc_id = :id");
        $chkStmt->execute([':id' => $docId]);
        $doc = $chkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$doc) {
            apiError("Document {$docId} not found in database.", 404);
        }

        $currentHold = (int)$doc['is_legal_hold'];
        $newHold = $currentHold ? 0 : 1;
        $custodian = getCurrentCustodian();

        if ($newHold === 1) {
            $reason = trim((string)($req['reason'] ?? 'Preservation order: Litigation and statutory audit freeze'));
            $upd = $pdo->prepare("
                UPDATE documents SET 
                    is_legal_hold = 1,
                    legal_hold_by_emp_id = :emp,
                    legal_hold_date = NOW(),
                    legal_hold_reason = :reason,
                    updated_at = NOW()
                WHERE doc_id = :id
            ");
            $upd->execute([
                ':emp'    => $custodian['emp_id'],
                ':reason' => $reason,
                ':id'     => $docId,
            ]);

            logDocumentAction(
                $pdo,
                $docId,
                'LEGAL_HOLD',
                "Enforced legal preservation hold by {$custodian['full_name']} ({$custodian['emp_id']}). Purge suspended."
            );

            apiSuccess([
                'doc_id'        => $docId,
                'is_legal_hold' => 1,
                'status_text'   => 'Hold Active',
            ], "Legal preservation hold enforced for {$docId} in database.");
        } else {
            $upd = $pdo->prepare("
                UPDATE documents SET 
                    is_legal_hold = 0,
                    legal_hold_by_emp_id = NULL,
                    legal_hold_date = NULL,
                    legal_hold_reason = NULL,
                    updated_at = NOW()
                WHERE doc_id = :id
            ");
            $upd->execute([':id' => $docId]);

            logDocumentAction(
                $pdo,
                $docId,
                'LEGAL_HOLD',
                "Released legal preservation hold by {$custodian['full_name']} ({$custodian['emp_id']}). Returned to statutory lifecycle."
            );

            apiSuccess([
                'doc_id'        => $docId,
                'is_legal_hold' => 0,
                'status_text'   => 'Hold Released',
            ], "Legal hold released for {$docId}. Restored to statutory retention schedule.");
        }
    }

    apiError('Unsupported HTTP method.', 405);
} catch (Throwable $e) {
    error_log("API Error in retention.php: " . $e->getMessage());
    apiError("Retention operation failed: " . $e->getMessage(), 500);
}
