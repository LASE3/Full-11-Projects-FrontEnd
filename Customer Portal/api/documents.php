<?php
declare(strict_types=1);

/**
 * Customer Portal - Technical Documents API
 * Location: Customer Portal/api/documents.php
 * Methods: GET, POST, PUT/PATCH, DELETE
 */

require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('CUS', ['customer' => true]);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/I18n.php';
require_once __DIR__ . '/helpers/AuditLogger.php';
require_once __DIR__ . '/helpers/CustomerSession.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$cusId = getActiveCustomerPortalId($pdo);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    if ($method === 'GET') {
        $docId = $_GET['id'] ?? ($_GET['doc_id'] ?? null);

        if ($docId) {
            $stmt = $pdo->prepare("
                SELECT d.*, p.project_name
                FROM documents d
                LEFT JOIN projects p ON d.related_prj_id = p.prj_id
                WHERE d.doc_id = :did 
                  AND (d.related_cus_id = :cid OR d.related_prj_id IN (SELECT prj_id FROM projects WHERE cus_id = :cid2))
            ");
            $stmt->execute([':did' => $docId, ':cid' => $cusId, ':cid2' => $cusId]);
            $doc = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$doc) {
                Response::error("Document #{$docId} not found or unauthorized.", 404);
            }

            Response::success($doc, "Document details loaded");
        } else {
            $folder = trim($_GET['folder'] ?? '');
            $search = trim($_GET['search'] ?? '');

            $sql = "
                SELECT d.*, p.project_name
                FROM documents d
                LEFT JOIN projects p ON d.related_prj_id = p.prj_id
                WHERE (d.related_cus_id = :cid OR d.related_prj_id IN (SELECT prj_id FROM projects WHERE cus_id = :cid2))
            ";
            $params = [':cid' => $cusId, ':cid2' => $cusId];

            if (!empty($folder) && $folder !== 'all') {
                $sql .= " AND d.folder = :folder";
                $params[':folder'] = $folder;
            }
            if (!empty($search)) {
                $sql .= " AND (d.file_name LIKE :search OR d.description LIKE :search2 OR d.doc_id LIKE :search3)";
                $params[':search'] = "%{$search}%";
                $params[':search2'] = "%{$search}%";
                $params[':search3'] = "%{$search}%";
            }

            $sql .= " ORDER BY d.created_at DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Response::success($docs, "Customer documents loaded");
        }
    }

    if ($method === 'POST') {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?: $_POST;

        $fileName = trim($data['file_name'] ?? '');
        if (empty($fileName)) {
            Response::error("File name is required.", 400);
        }

        $docId = 'DOC-' . date('Y') . '-' . str_pad((string)mt_rand(100, 999), 3, '0', STR_PAD_LEFT);
        $description = trim($data['description'] ?? 'Technical Passport & Specification Dossier');
        $classification = in_array($data['classification'] ?? '', ['Public', 'Internal', 'Confidential', 'TopSecret']) ? $data['classification'] : 'Confidential';
        $folder = trim($data['folder'] ?? 'projects');
        $department = trim($data['department'] ?? 'ENG');
        $fileSize = trim($data['file_size'] ?? '2.4 MB');
        $fileHash = hash('sha256', $fileName . microtime(true));
        $status = trim($data['status'] ?? 'Approved');
        $projectRef = trim($data['project_ref'] ?? '');
        $relatedPrjId = !empty($data['related_prj_id']) ? trim($data['related_prj_id']) : null;

        $stmt = $pdo->prepare("
            INSERT INTO documents (
                doc_id, file_name, description, classification, folder, department,
                file_size, file_hash, status, retention_period, project_ref,
                customer_ref, owning_system, related_prj_id, related_cus_id, created_at, updated_at
            ) VALUES (
                :doc_id, :file_name, :description, :classification, :folder, :department,
                :file_size, :file_hash, :status, '7y', :project_ref,
                :customer_ref, 'Customer Portal', :related_prj_id, :related_cus_id, NOW(), NOW()
            )
        ");
        $stmt->execute([
            ':doc_id'         => $docId,
            ':file_name'      => $fileName,
            ':description'   => $description,
            ':classification' => $classification,
            ':folder'         => $folder,
            ':department'     => $department,
            ':file_size'      => $fileSize,
            ':file_hash'      => $fileHash,
            ':status'         => $status,
            ':project_ref'    => $projectRef,
            ':customer_ref'   => "Customer {$cusId}",
            ':related_prj_id' => $relatedPrjId,
            ':related_cus_id' => $cusId
        ]);

        AuditLogger::logSecurityEvent('DOC_REGISTERED', 'CUS', "Registered document {$docId} for {$cusId}", 'Low', null, $cusId);

        Response::success([
            'doc_id'    => $docId,
            'file_name' => $fileName,
            'status'    => $status
        ], "Document #{$docId} created successfully", 201);
    }

    if ($method === 'PUT' || $method === 'PATCH') {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?: [];

        $docId = trim($data['doc_id'] ?? ($_GET['id'] ?? ''));
        if (empty($docId)) {
            Response::error("Document ID is required.", 400);
        }

        // Verify ownership
        $chk = $pdo->prepare("
            SELECT doc_id FROM documents 
            WHERE doc_id = :did 
              AND (related_cus_id = :cid OR related_prj_id IN (SELECT prj_id FROM projects WHERE cus_id = :cid2))
        ");
        $chk->execute([':did' => $docId, ':cid' => $cusId, ':cid2' => $cusId]);
        if (!$chk->fetchColumn()) {
            Response::error("Document not found or access denied.", 404);
        }

        $fields = [];
        $params = [':did' => $docId];

        if (isset($data['file_name'])) {
            $fields[] = "file_name = :file_name";
            $params[':file_name'] = trim($data['file_name']);
        }
        if (isset($data['description'])) {
            $fields[] = "description = :description";
            $params[':description'] = trim($data['description']);
        }
        if (isset($data['classification'])) {
            $fields[] = "classification = :classification";
            $params[':classification'] = trim($data['classification']);
        }
        if (isset($data['folder'])) {
            $fields[] = "folder = :folder";
            $params[':folder'] = trim($data['folder']);
        }
        if (isset($data['status'])) {
            $fields[] = "status = :status";
            $params[':status'] = trim($data['status']);
        }

        if (empty($fields)) {
            Response::error("No fields provided to update.", 400);
        }

        $fields[] = "updated_at = NOW()";
        $sql = "UPDATE documents SET " . implode(", ", $fields) . " WHERE doc_id = :did";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        AuditLogger::logSecurityEvent('DOC_UPDATED', 'CUS', "Updated document {$docId}", 'Low', null, $cusId);

        Response::success(['doc_id' => $docId], "Document #{$docId} updated successfully");
    }

    if ($method === 'DELETE') {
        $docId = trim($_GET['id'] ?? ($_GET['doc_id'] ?? ''));
        if (empty($docId)) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            $docId = trim($data['doc_id'] ?? '');
        }

        if (empty($docId)) {
            Response::error("Document ID is required.", 400);
        }

        // Verify ownership
        $chk = $pdo->prepare("
            SELECT doc_id, file_name FROM documents 
            WHERE doc_id = :did 
              AND (related_cus_id = :cid OR related_prj_id IN (SELECT prj_id FROM projects WHERE cus_id = :cid2))
        ");
        $chk->execute([':did' => $docId, ':cid' => $cusId, ':cid2' => $cusId]);
        $existing = $chk->fetch(PDO::FETCH_ASSOC);

        if (!$existing) {
            Response::error("Document not found or access denied.", 404);
        }

        $del = $pdo->prepare("DELETE FROM documents WHERE doc_id = :did");
        $del->execute([':did' => $docId]);

        // Attempt to remove physical file in uploads/ if exists
        $filePath = __DIR__ . '/../uploads/' . basename($existing['file_name']);
        if (file_exists($filePath)) {
            @unlink($filePath);
        }

        AuditLogger::logSecurityEvent('DOC_DELETED', 'CUS', "Deleted document {$docId}", 'Medium', null, $cusId);

        Response::success(['doc_id' => $docId], "Document #{$docId} deleted successfully");
    }
} catch (Exception $e) {
    Response::error("Documents API Error: " . $e->getMessage(), 500);
}
