<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR File Center - Documents API
 * Location: File Center/api/documents.php
 * Methods: GET, POST, PUT, DELETE
 */

require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('DOC', []);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../../includes/id_generator.php';
require_once __DIR__ . '/../../includes/document_storage.php';

$pdo = getApiPdo();
$method = $_SERVER['REQUEST_METHOD'];

// Handle JSON payload for POST/PUT
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

// Check method override header or action parameter
if ($method === 'POST') {
    if ($action === 'delete') {
        $method = 'DELETE';
    } elseif ($action === 'update' || $action === 'edit') {
        $method = 'PUT';
    }
}

try {
    // -------------------------------------------------------------
    // GET: Query documents, statistics, or single document
    // -------------------------------------------------------------
    if ($method === 'GET') {
        // Return summary statistics
        if ($action === 'stats') {
            $totalDocs = (int)$pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn();
            
            // Total volume in GB
            $docsSizes = $pdo->query("SELECT file_size FROM documents")->fetchAll(PDO::FETCH_COLUMN);
            $totalMB = 0;
            foreach ($docsSizes as $sz) {
                if (preg_match('/([\d\.]+)\s*(MB|KB|GB)/i', (string)$sz, $m)) {
                    $num = (float)$m[1];
                    $unit = strtoupper($m[2]);
                    if ($unit === 'GB') $totalMB += $num * 1024;
                    elseif ($unit === 'MB') $totalMB += $num;
                    elseif ($unit === 'KB') $totalMB += $num / 1024;
                } else {
                    $totalMB += 2.0; // default average
                }
            }
            $vaultGb = round(840.0 + ($totalMB / 1024.0), 1);

            // Pending approvals count
            $pendingApprovals = (int)$pdo->query("
                SELECT COUNT(*) FROM documents 
                WHERE status IN ('In Review', 'Pending')
            ")->fetchColumn();

            // Legal holds count
            $legalHolds = (int)$pdo->query("
                SELECT COUNT(*) FROM documents WHERE is_legal_hold = 1
            ")->fetchColumn();

            // Hash integrity count
            $validHashes = (int)$pdo->query("
                SELECT COUNT(*) FROM documents WHERE file_hash IS NOT NULL AND CHAR_LENGTH(file_hash) = 64
            ")->fetchColumn();
            $integrityPct = $totalDocs > 0 ? round(($validHashes / $totalDocs) * 100.0, 1) : 100.0;

            // Counts by classification
            $classCounts = [
                'all'                 => $totalDocs,
                'highly-confidential' => (int)$pdo->query("SELECT COUNT(*) FROM documents WHERE classification = 'TopSecret'")->fetchColumn(),
                'confidential'        => (int)$pdo->query("SELECT COUNT(*) FROM documents WHERE classification = 'Confidential'")->fetchColumn(),
                'internal'            => (int)$pdo->query("SELECT COUNT(*) FROM documents WHERE classification = 'Internal'")->fetchColumn(),
                'public'              => (int)$pdo->query("SELECT COUNT(*) FROM documents WHERE classification = 'Public'")->fetchColumn(),
            ];

            // Counts by partition folder
            $folders = ['governance', 'projects', 'contracts', 'finance', 'hr', 'operations'];
            $folderCounts = ['all' => $totalDocs];
            foreach ($folders as $f) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE folder = :f");
                $stmt->execute([':f' => $f]);
                $folderCounts[$f] = (int)$stmt->fetchColumn();
            }

            apiSuccess([
                'total_documents'        => $totalDocs,
                'vault_volume_gb'        => $vaultGb,
                'pending_approvals'      => $pendingApprovals,
                'legal_holds_count'      => $legalHolds,
                'integrity_percentage'   => $integrityPct,
                'classification_counts'  => $classCounts,
                'folder_counts'          => $folderCounts,
            ]);
        }

        // Return next available DOC ID
        if ($action === 'next_id') {
            $nextId = vp_next_id($pdo, 'DOC', 'DOC-2026-', 3);
            apiSuccess(['next_doc_id' => $nextId]);
        }

        // Single Document details
        $docId = $req['id'] ?? $req['doc_id'] ?? null;
        if ($docId) {
            $stmt = $pdo->prepare("
                SELECT 
                    d.*,
                    e.full_name AS custodian_name,
                    e.job_title AS custodian_job,
                    e.department_code AS custodian_dept,
                    c.company_name AS customer_name,
                    p.project_name AS project_name
                FROM documents d
                LEFT JOIN employees e ON d.owner_emp_id = e.emp_id
                LEFT JOIN customers c ON d.related_cus_id = c.cus_id
                LEFT JOIN projects p ON d.related_prj_id = p.prj_id
                WHERE d.doc_id = :id
            ");
            $stmt->execute([':id' => $docId]);
            $doc = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$doc) {
                apiError('Document not found: ' . htmlspecialchars($docId), 404);
            }

            // Normalise classification string for frontend
            $doc['class_slug'] = match (strtolower($doc['classification'])) {
                'topsecret'    => 'highly-confidential',
                'confidential' => 'confidential',
                'internal'     => 'internal',
                'public'       => 'public',
                default        => strtolower($doc['classification']),
            };
            $doc['class_display'] = match (strtolower($doc['classification'])) {
                'topsecret'    => 'Highly Confidential',
                'confidential' => 'Confidential',
                'internal'     => 'Internal',
                'public'       => 'Public',
                default        => ucfirst($doc['classification']),
            };

            // Fetch approval info if available
            $appStmt = $pdo->prepare("
                SELECT da.*, e.full_name AS reviewer_name 
                FROM document_approvals da
                LEFT JOIN employees e ON da.reviewer_emp_id = e.emp_id
                WHERE da.doc_id = :id 
                ORDER BY da.approval_id DESC LIMIT 1
            ");
            $appStmt->execute([':id' => $docId]);
            $doc['latest_approval'] = $appStmt->fetch(PDO::FETCH_ASSOC) ?: null;

            // Fetch versions
            $vStmt = $pdo->prepare("SELECT * FROM document_versions WHERE doc_id = :id ORDER BY version_number DESC");
            $vStmt->execute([':id' => $docId]);
            $doc['versions'] = $vStmt->fetchAll(PDO::FETCH_ASSOC);

            // Log inspection/view
            logDocumentAction($pdo, $docId, 'View', "Inspected document details in vault drawer");

            apiSuccess($doc);
        }

        // List all documents with filters
        $q = trim((string)($req['q'] ?? ''));
        $classification = trim((string)($req['classification'] ?? 'all'));
        $folder = trim((string)($req['folder'] ?? 'all'));
        $status = trim((string)($req['status'] ?? 'all'));

        $sql = "
            SELECT 
                d.*,
                e.full_name AS custodian_name,
                e.job_title AS custodian_job,
                c.company_name AS customer_name,
                p.project_name AS project_name
            FROM documents d
            LEFT JOIN employees e ON d.owner_emp_id = e.emp_id
            LEFT JOIN customers c ON d.related_cus_id = c.cus_id
            LEFT JOIN projects p ON d.related_prj_id = p.prj_id
            WHERE 1=1
        ";
        $params = [];

        if ($q !== '') {
            $sql .= " AND (d.doc_id LIKE :q1 OR d.file_name LIKE :q2 OR d.description LIKE :q3 OR d.project_ref LIKE :q4 OR e.full_name LIKE :q5)";
            $wildcard = "%{$q}%";
            $params[':q1'] = $wildcard;
            $params[':q2'] = $wildcard;
            $params[':q3'] = $wildcard;
            $params[':q4'] = $wildcard;
            $params[':q5'] = $wildcard;
        }

        if ($classification !== 'all' && $classification !== '') {
            $mappedClass = match ($classification) {
                'highly-confidential' => 'TopSecret',
                'confidential'        => 'Confidential',
                'internal'            => 'Internal',
                'public'              => 'Public',
                default               => ucfirst($classification),
            };
            $sql .= " AND d.classification = :classification";
            $params[':classification'] = $mappedClass;
        }

        if ($folder !== 'all' && $folder !== '') {
            $sql .= " AND d.folder = :folder";
            $params[':folder'] = $folder;
        }

        if ($status !== 'all' && $status !== '') {
            $sql .= " AND d.status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY CAST(SUBSTRING(d.doc_id, 10) AS UNSIGNED) ASC, d.doc_id ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map classification helper keys for frontend
        foreach ($docs as &$doc) {
            $doc['class_slug'] = match (strtolower($doc['classification'])) {
                'topsecret'    => 'highly-confidential',
                'confidential' => 'confidential',
                'internal'     => 'internal',
                'public'       => 'public',
                default        => strtolower($doc['classification']),
            };
            $doc['class_display'] = match (strtolower($doc['classification'])) {
                'topsecret'    => 'Highly Confidential',
                'confidential' => 'Confidential',
                'internal'     => 'Internal',
                'public'       => 'Public',
                default        => ucfirst($doc['classification']),
            };
            $doc['formatted_date'] = substr($doc['created_at'], 0, 10);
            $doc['custodian_display'] = $doc['custodian_name'] 
                ? "{$doc['custodian_name']} ({$doc['owner_emp_id']})" 
                : ($doc['owner_emp_id'] ?? 'Farida Iskakova (EMP-1019)');
        }

        apiSuccess($docs);
    }

    // -------------------------------------------------------------
    // POST: Ingest / Actions / Create a new document in Database
    // -------------------------------------------------------------
    if ($method === 'POST') {
        // Toggle Legal Preservation Hold
        if ($action === 'toggle_legal_hold') {
            $docId = trim((string)($req['doc_id'] ?? $req['id'] ?? ''));
            if (empty($docId)) apiError('doc_id required for legal hold toggle', 422);

            $stmt = $pdo->prepare("SELECT is_legal_hold, file_name FROM documents WHERE doc_id = :id");
            $stmt->execute([':id' => $docId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) apiError("Document {$docId} not found", 404);

            $newHold = empty($row['is_legal_hold']) ? 1 : 0;
            $upd = $pdo->prepare("UPDATE documents SET is_legal_hold = :h, updated_at = NOW() WHERE doc_id = :id");
            $upd->execute([':h' => $newHold, ':id' => $docId]);

            logDocumentAction(
                $pdo,
                $docId,
                'LEGAL_HOLD',
                ($newHold ? 'Applied' : 'Released') . " Statutory Preservation Hold on '{$row['file_name']}'"
            );

            apiSuccess([
                'doc_id' => $docId,
                'is_legal_hold' => $newHold,
            ], "Legal hold updated to: " . ($newHold ? 'ACTIVE' : 'RELEASED'));
        }

        // Approval Decision (Approve / Reject)
        if ($action === 'approve' || $action === 'reject') {
            $docId = trim((string)($req['doc_id'] ?? $req['id'] ?? ''));
            if (empty($docId)) apiError('doc_id required for approval action', 422);

            $stmt = $pdo->prepare("SELECT status, file_name FROM documents WHERE doc_id = :id");
            $stmt->execute([':id' => $docId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) apiError("Document {$docId} not found", 404);

            $newStatus = ($action === 'approve') ? 'Approved' : 'Rejected';
            $upd = $pdo->prepare("UPDATE documents SET status = :s, updated_at = NOW() WHERE doc_id = :id");
            $upd->execute([':s' => $newStatus, ':id' => $docId]);

            // Update latest approval stage if exists
            $appUpd = $pdo->prepare("
                UPDATE document_approvals 
                SET decision = :d, decision_date = NOW(), comments = :c
                WHERE doc_id = :id 
                ORDER BY approval_id DESC LIMIT 1
            ");
            $appUpd->execute([
                ':d' => $newStatus,
                ':c' => "Executive review marked as {$newStatus}",
                ':id' => $docId
            ]);

            logDocumentAction(
                $pdo,
                $docId,
                strtoupper($action),
                "Document '{$row['file_name']}' status updated to '{$newStatus}'"
            );

            apiSuccess([
                'doc_id' => $docId,
                'status' => $newStatus,
            ], "Document {$docId} decision recorded: {$newStatus}");
        }

        $title = trim((string)($req['title'] ?? $req['file_name'] ?? ''));
        if (empty($title)) {
            // Check if file uploaded and take basename
            if (!empty($_FILES['file']['name'])) {
                $title = basename($_FILES['file']['name']);
            } elseif (!empty($_FILES['document']['name'])) {
                $title = basename($_FILES['document']['name']);
            } else {
                apiError('Document Title / Filename is required.', 422);
            }
        }

        // Auto-generate doc_id if not provided
        $docId = trim((string)($req['doc_id'] ?? ''));
        if (empty($docId)) {
            $docId = vp_next_id($pdo, 'DOC', 'DOC-2026-', 3);
        }

        // Ensure unique doc_id
        $existsStmt = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE doc_id = :id");
        $existsStmt->execute([':id' => $docId]);
        if ($existsStmt->fetchColumn() > 0) {
            apiError("Document with ID {$docId} already exists in database.", 409);
        }

        $description = trim((string)($req['description'] ?? ''));
        $rawClass = trim((string)($req['classification'] ?? 'internal'));
        $classification = match (strtolower($rawClass)) {
            'highly-confidential', 'topsecret' => 'TopSecret',
            'confidential'                      => 'Confidential',
            'public'                            => 'Public',
            default                             => 'Internal',
        };

        // Partition folder deduction
        $folder = trim((string)($req['folder'] ?? ''));
        $department = trim((string)($req['department'] ?? 'ENG'));
        if (empty($folder)) {
            $folder = match ($department) {
                'EXE'   => 'governance',
                'ENG'   => 'projects',
                'SAL'   => 'contracts',
                'FIN'   => 'finance',
                'HR'    => 'hr',
                'OPS'   => 'operations',
                default => 'projects',
            };
        }

        $owningSystem = trim((string)($req['owning_system'] ?? 'File Center'));
        $projectRef = trim((string)($req['project_ref'] ?? $req['meta_project_ref'] ?? ''));
        $customerRef = trim((string)($req['customer_ref'] ?? $req['meta_customer_ref'] ?? ''));
        $retention = trim((string)($req['retention_period'] ?? $req['retention'] ?? '7y'));
        $status = trim((string)($req['status'] ?? ($classification === 'TopSecret' ? 'In Review' : 'Approved')));
        $custodian = getCurrentCustodian();
        $ownerEmpId = trim((string)($req['owner_emp_id'] ?? $custodian['emp_id']));

        // Verify ownerEmpId exists in employees table, fallback to first valid employee
        $chkEmp = $pdo->prepare("SELECT emp_id FROM employees WHERE emp_id = :e");
        $chkEmp->execute([':e' => $ownerEmpId]);
        if (!$chkEmp->fetchColumn()) {
            $fallbackEmp = $pdo->query("SELECT emp_id FROM employees ORDER BY emp_id ASC LIMIT 1")->fetchColumn();
            $ownerEmpId = $fallbackEmp ?: 'EMP-1019';
        }

        // Match formal project foreign key if available
        $relatedPrjId = null;
        if (preg_match('/(PRJ-2026-\d{3})/i', $projectRef, $pm)) {
            $chkPrj = $pdo->prepare("SELECT prj_id FROM projects WHERE prj_id = :p");
            $chkPrj->execute([':p' => strtoupper($pm[1])]);
            if ($chkPrj->fetchColumn()) {
                $relatedPrjId = strtoupper($pm[1]);
            }
        }

        // Match formal customer foreign key if available
        $relatedCusId = null;
        if (preg_match('/(CUS-\d{4})/i', $customerRef, $cm)) {
            $chkCus = $pdo->prepare("SELECT cus_id FROM customers WHERE cus_id = :c");
            $chkCus->execute([':c' => strtoupper($cm[1])]);
            if ($chkCus->fetchColumn()) {
                $relatedCusId = strtoupper($cm[1]);
            }
        }

        // Handle physical file storage
        $storedData = null;
        if (!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $storedData = vp_validate_and_save_upload($_FILES['file'], $docId);
        } elseif (!empty($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $storedData = vp_validate_and_save_upload($_FILES['document'], $docId);
        } else {
            $storedData = vp_generate_default_document_file($docId, $title, $classification);
        }

        $storagePath = $storedData['storage_path'];
        $mime = $storedData['mime'];
        $fileSize = $storedData['file_size'];
        $fileHash = $storedData['file_hash'];
        $fileName = $storedData['file_name'];

        // Insert into `documents`
        $insertStmt = $pdo->prepare("
            INSERT INTO documents (
                doc_id, file_name, description, classification, folder, department,
                owning_system, owner_emp_id, related_prj_id, related_cus_id,
                project_ref, customer_ref, file_size, file_hash, storage_path, mime, status,
                retention_period, is_legal_hold, created_at
            ) VALUES (
                :doc_id, :file_name, :description, :classification, :folder, :department,
                :owning_system, :owner_emp_id, :related_prj_id, :related_cus_id,
                :project_ref, :customer_ref, :file_size, :file_hash, :storage_path, :mime, :status,
                :retention_period, 0, NOW()
            )
        ");
        $insertStmt->execute([
            ':doc_id'           => $docId,
            ':file_name'        => $fileName,
            ':description'     => $description ?: 'Registered via File Center secure ingestion enclave',
            ':classification'   => $classification,
            ':folder'           => $folder,
            ':department'       => $department,
            ':owning_system'    => $owningSystem,
            ':owner_emp_id'     => $ownerEmpId,
            ':related_prj_id'   => $relatedPrjId,
            ':related_cus_id'   => $relatedCusId,
            ':project_ref'      => $projectRef,
            ':customer_ref'     => $customerRef,
            ':file_size'        => $fileSize,
            ':file_hash'        => $fileHash,
            ':storage_path'     => $storagePath,
            ':mime'             => $mime,
            ':status'           => $status,
            ':retention_period' => $retention,
        ]);

        // Create version 1 in document_versions
        try {
            $vStmt = $pdo->prepare("
                INSERT INTO document_versions 
                (doc_id, version_number, uploaded_by_emp_id, uploaded_at, file_path)
                VALUES (:doc_id, 1, :emp_id, NOW(), :path)
            ");
            $vStmt->execute([
                ':doc_id' => $docId,
                ':emp_id' => $ownerEmpId,
                ':path'   => $storagePath,
            ]);
        } catch (Throwable $ve) {
            error_log('Notice: version creation skipped: ' . $ve->getMessage());
        }

        // If status is In Review or Pending, create initial document_approvals row
        if (in_array($status, ['In Review', 'Pending'], true)) {
            try {
                $appStmt = $pdo->prepare("
                    INSERT INTO document_approvals 
                    (doc_id, reviewer_emp_id, stage, stage_name, comments, decision, decision_date)
                    VALUES (:doc_id, :reviewer, 2, 'Project Manager Signoff', 'Awaiting review and signature', 'Pending', NOW())
                ");
                $appStmt->execute([
                    ':doc_id'   => $docId,
                    ':reviewer' => $ownerEmpId,
                ]);
            } catch (Throwable $ae) {
                error_log('Notice: approval creation skipped: ' . $ae->getMessage());
            }
        }

        // Audit Trail log
        logDocumentAction(
            $pdo,
            $docId,
            'INGEST_DRAFT',
            "Uploaded and ingested new document '{$fileName}' into vault partition '{$folder}'",
            $ownerEmpId
        );

        apiSuccess([
            'doc_id'         => $docId,
            'file_name'      => $fileName,
            'classification' => $classification,
            'folder'         => $folder,
            'department'     => $department,
            'file_size'      => $fileSize,
            'file_hash'      => $fileHash,
            'storage_path'   => $storagePath,
            'status'         => $status,
        ], "Document {$docId} created and secured in database successfully!", 201);
    }

    // -------------------------------------------------------------
    // PUT: Edit / Update document metadata in Database
    // -------------------------------------------------------------
    if ($method === 'PUT') {
        $docId = trim((string)($req['doc_id'] ?? $req['id'] ?? ''));
        if (empty($docId)) {
            apiError('Document ID (doc_id) is required for update.');
        }

        // Check if document exists
        $checkStmt = $pdo->prepare("SELECT * FROM documents WHERE doc_id = :id");
        $checkStmt->execute([':id' => $docId]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            apiError("Document {$docId} not found in database.", 404);
        }

        $title = isset($req['file_name']) ? trim((string)$req['file_name']) : $existing['file_name'];
        $description = isset($req['description']) ? trim((string)$req['description']) : $existing['description'];
        $folder = isset($req['folder']) ? trim((string)$req['folder']) : $existing['folder'];
        $department = isset($req['department']) ? trim((string)$req['department']) : $existing['department'];
        $projectRef = isset($req['project_ref']) ? trim((string)$req['project_ref']) : $existing['project_ref'];
        $customerRef = isset($req['customer_ref']) ? trim((string)$req['customer_ref']) : $existing['customer_ref'];
        $status = isset($req['status']) ? trim((string)$req['status']) : $existing['status'];
        $retention = isset($req['retention_period']) ? trim((string)$req['retention_period']) : $existing['retention_period'];

        $classification = $existing['classification'];
        if (isset($req['classification'])) {
            $rawClass = trim((string)$req['classification']);
            $classification = match (strtolower($rawClass)) {
                'highly-confidential', 'topsecret' => 'TopSecret',
                'confidential'                      => 'Confidential',
                'public'                            => 'Public',
                default                             => 'Internal',
            };
        }

        $updateStmt = $pdo->prepare("
            UPDATE documents SET 
                file_name = :file_name,
                description = :description,
                classification = :classification,
                folder = :folder,
                department = :department,
                project_ref = :project_ref,
                customer_ref = :customer_ref,
                status = :status,
                retention_period = :retention_period,
                updated_at = NOW()
            WHERE doc_id = :doc_id
        ");
        $updateStmt->execute([
            ':file_name'        => $title,
            ':description'     => $description,
            ':classification'   => $classification,
            ':folder'           => $folder,
            ':department'       => $department,
            ':project_ref'      => $projectRef,
            ':customer_ref'     => $customerRef,
            ':status'           => $status,
            ':retention_period' => $retention,
            ':doc_id'           => $docId,
        ]);

        // Audit Trail log
        logDocumentAction(
            $pdo,
            $docId,
            'Edit',
            "Updated document metadata: title='{$title}', status='{$status}', classification='{$classification}'"
        );

        apiSuccess([
            'doc_id'         => $docId,
            'file_name'      => $title,
            'status'         => $status,
            'classification' => $classification,
        ], "Document {$docId} updated successfully in database.");
    }

    // -------------------------------------------------------------
    // DELETE: Delete document from Database
    // -------------------------------------------------------------
    if ($method === 'DELETE') {
        $docId = trim((string)($req['doc_id'] ?? $req['id'] ?? ''));
        if (empty($docId)) {
            apiError('Document ID (doc_id) is required for deletion.');
        }

        $checkStmt = $pdo->prepare("SELECT doc_id, file_name, is_legal_hold, storage_path FROM documents WHERE doc_id = :id");
        $checkStmt->execute([':id' => $docId]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$existing) {
            apiError("Document {$docId} not found.", 404);
        }

        if (!empty($existing['is_legal_hold'])) {
            apiError("Cannot delete {$docId}: Document is protected under an active Statutory Legal Preservation Hold. Release hold first.", 403);
        }

        // Clean up linked tables or cascade
        $pdo->beginTransaction();
        try {
            $delApp = $pdo->prepare("DELETE FROM document_approvals WHERE doc_id = :id");
            $delApp->execute([':id' => $docId]);

            $delVer = $pdo->prepare("DELETE FROM document_versions WHERE doc_id = :id");
            $delVer->execute([':id' => $docId]);

            $delLog = $pdo->prepare("DELETE FROM document_access_log WHERE doc_id = :id");
            $delLog->execute([':id' => $docId]);

            $delDoc = $pdo->prepare("DELETE FROM documents WHERE doc_id = :id");
            $delDoc->execute([':id' => $docId]);

            $pdo->commit();

            // Unlink physical file from storage if present
            if (!empty($existing['storage_path'])) {
                $targetFile = dirname(__DIR__, 2) . '/' . ltrim($existing['storage_path'], '/\\');
                if (file_exists($targetFile)) {
                    @unlink($targetFile);
                }
            }
        } catch (Throwable $de) {
            $pdo->rollBack();
            throw $de;
        }

        apiSuccess(null, "Document {$docId} ('{$existing['file_name']}') successfully deleted from database.");
    }

    apiError('Unsupported HTTP method: ' . $method, 405);
} catch (Throwable $e) {
    error_log("API Error in documents.php: " . $e->getMessage());
    apiError("Operation failed. An internal error occurred.", 500);
}
