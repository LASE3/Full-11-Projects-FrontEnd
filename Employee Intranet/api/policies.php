<?php

declare(strict_types=1);

/**
 * Class 4: Employee Intranet - Policies & Documents API
 * Location: Employee Intranet/api/policies.php
 * Methods: GET, POST, PUT, PATCH, DELETE
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('EMP', []);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/I18n.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$lang = $_GET['lang'] ?? 'en';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$currentEmpId = $_SESSION['emp_id'] ?? ($_SESSION['vostok_user']['emp_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? 'EMP-1004'));

if ($method === 'GET') {
    $category = trim($_GET['category'] ?? ($_GET['folder'] ?? 'all'));
    $query = trim($_GET['query'] ?? ($_GET['search'] ?? ''));
    $docIdQuery = trim($_GET['id'] ?? ($_GET['doc_id'] ?? ''));

    try {
        $sql = "
            SELECT 
                d.doc_id,
                d.doc_id AS id,
                d.file_name,
                d.description,
                d.classification,
                d.folder,
                d.department,
                d.file_size,
                d.status,
                d.owner_emp_id,
                d.created_at,
                d.updated_at,
                e.full_name AS owner_name,
                e.job_title AS owner_role,
                p.policy_id,
                p.effective_date
            FROM documents d
            LEFT JOIN employees e ON d.owner_emp_id = e.emp_id
            LEFT JOIN internal_policies p ON d.doc_id = p.doc_id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($docIdQuery)) {
            $sql .= " AND d.doc_id = :did";
            $params[':did'] = $docIdQuery;
        } else {
            if (!empty($category) && $category !== 'all') {
                $folderMap = [
                    'security'  => ['governance', 'policies'],
                    'hr'        => ['hr'],
                    'it'        => ['projects', 'it'],
                    'expense'   => ['finance'],
                    'templates' => ['operations', 'contracts']
                ];
                if (isset($folderMap[$category])) {
                    $inPlaceholders = [];
                    foreach ($folderMap[$category] as $idx => $fld) {
                        $pKey = ":cat_{$idx}";
                        $inPlaceholders[] = $pKey;
                        $params[$pKey] = $fld;
                    }
                    $sql .= " AND d.folder IN (" . implode(',', $inPlaceholders) . ")";
                } else {
                    $sql .= " AND d.folder = :folder";
                    $params[':folder'] = $category;
                }
            }

            if (!empty($query)) {
                $sql .= " AND (d.doc_id LIKE :q OR d.file_name LIKE :q OR d.description LIKE :q OR d.folder LIKE :q OR d.department LIKE :q)";
                $params[':q'] = "%{$query}%";
            }
        }

        $sql .= " ORDER BY d.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $catNameMap = [
            'governance' => 'Security Policies',
            'policies'   => 'Corporate Policies',
            'hr'         => 'HR Policies',
            'projects'   => 'IT Standards',
            'finance'    => 'Expense Forms',
            'operations' => 'Templates & Ops',
            'contracts'  => 'Contracts & Agreements'
        ];

        $catKeyMap = [
            'governance' => 'security',
            'policies'   => 'security',
            'hr'         => 'hr',
            'projects'   => 'it',
            'finance'    => 'expense',
            'operations' => 'templates',
            'contracts'  => 'templates'
        ];

        foreach ($docs as &$d) {
            $folder = strtolower($d['folder'] ?? 'operations');
            $d['category'] = $catNameMap[$folder] ?? ucfirst($folder);
            $d['categoryKey'] = $catKeyMap[$folder] ?? 'templates';

            $cClass = match ($d['classification']) {
                'TopSecret'    => 'restricted',
                'Confidential' => 'confidential',
                'Internal'     => 'internal',
                default        => 'public'
            };
            $d['classCode'] = $cClass;

            $ext = strtoupper(pathinfo($d['file_name'] ?? 'file.pdf', PATHINFO_EXTENSION));
            $d['format'] = $ext ?: 'PDF';
            $d['version'] = 'v' . (date('y', strtotime($d['created_at'] ?? 'now')) - 20) . '.' . (int)substr(preg_replace('/\D/', '', $d['doc_id'] ?? '1'), -1);
            $d['updated'] = date('Y-m-d', strtotime($d['updated_at'] ?? ($d['created_at'] ?? 'now')));
            $d['size'] = $d['file_size'] ?: '1.2 MB';
            $d['title'] = $d['description'] ?: preg_replace('/[_-]/', ' ', pathinfo($d['file_name'] ?? '', PATHINFO_FILENAME));
            $d['author'] = $d['owner_name'] ?: ($d['department'] . ' Department');
            $d['download_url'] = "api/export.php?type=policies&doc_id=" . urlencode($d['doc_id']);
        }

        Response::success($docs, "Policies and documents loaded", 200, ['total' => count($docs)]);
    } catch (Exception $e) {
        Response::error("Failed to load documents: " . $e->getMessage(), 500);
    }
}

if ($method === 'POST') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true) ?: $_POST;

    $fileName = trim($data['file_name'] ?? ($data['title'] ?? ''));
    $description = trim($data['description'] ?? $fileName);
    $classification = trim($data['classification'] ?? 'Internal');
    $folder = trim($data['folder'] ?? ($data['categoryKey'] ?? 'policies'));
    $dept = trim($data['department'] ?? 'ENG');
    $fileSize = trim($data['file_size'] ?? ($data['size'] ?? '1.5 MB'));
    $docId = trim($data['doc_id'] ?? ($data['id'] ?? ''));

    if (empty($fileName)) {
        Response::error("File name / title is required.", 422);
    }

    if (empty($docId)) {
        $year = date('Y');
        $maxNum = (int)$pdo->query("SELECT MAX(CAST(SUBSTRING(doc_id, 10) AS UNSIGNED)) FROM documents WHERE doc_id LIKE 'DOC-{$year}-%'")->fetchColumn();
        $nextNum = sprintf('%03d', max(1, $maxNum + 1));
        $docId = "DOC-{$year}-{$nextNum}";
    }

    // Normalize classification
    if (!in_array($classification, ['Public', 'Internal', 'Confidential', 'TopSecret'], true)) {
        $cMap = [
            'restricted' => 'TopSecret',
            'topsecret' => 'TopSecret',
            'confidential' => 'Confidential',
            'internal' => 'Internal',
            'public' => 'Public'
        ];
        $classification = $cMap[strtolower($classification)] ?? 'Internal';
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO documents (doc_id, file_name, description, classification, folder, department, file_size, status, owner_emp_id, created_at, updated_at)
            VALUES (:did, :fn, :desc, :cls, :fld, :dept, :size, 'Active', :owner, NOW(), NOW())
        ");
        $stmt->execute([
            ':did'   => $docId,
            ':fn'    => $fileName,
            ':desc'  => $description,
            ':cls'   => $classification,
            ':fld'   => $folder,
            ':dept'  => $dept,
            ':size'  => $fileSize,
            ':owner' => $currentEmpId
        ]);

        // If folder is policies or governance, register in internal_policies too
        if (in_array(strtolower($folder), ['policies', 'governance', 'security'])) {
            $pStmt = $pdo->prepare("INSERT INTO internal_policies (title, doc_id, effective_date) VALUES (:title, :did, CURDATE())");
            $pStmt->execute([':title' => $description, ':did' => $docId]);
        }

        AuditLogger::logAction(
            $currentEmpId,
            null,
            'Employee Intranet',
            'EMP',
            'CREATE_POLICY_DOCUMENT',
            'documents',
            $docId,
            ['file_name' => $fileName, 'classification' => $classification],
            'SUCCESS'
        );

        Response::success(['doc_id' => $docId], "Policy document registered successfully", 201);
    } catch (Exception $e) {
        Response::error("Failed to register document: " . $e->getMessage(), 500);
    }
}

if ($method === 'PUT' || $method === 'PATCH') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true) ?: $_POST;

    $docId = trim($data['doc_id'] ?? ($data['id'] ?? ''));
    if (empty($docId)) {
        Response::error("Document ID is required.", 422);
    }

    $fields = [];
    $params = [':did' => $docId];

    if (isset($data['file_name']) || isset($data['title'])) {
        $fields[] = "file_name = :fn";
        $params[':fn'] = trim($data['file_name'] ?? $data['title']);
    }
    if (isset($data['description'])) {
        $fields[] = "description = :desc";
        $params[':desc'] = trim($data['description']);
    }
    if (isset($data['classification'])) {
        $cls = trim($data['classification']);
        $cMap = ['restricted' => 'TopSecret', 'topsecret' => 'TopSecret', 'confidential' => 'Confidential', 'internal' => 'Internal', 'public' => 'Public'];
        $cls = $cMap[strtolower($cls)] ?? 'Internal';
        $fields[] = "classification = :cls";
        $params[':cls'] = $cls;
    }
    if (isset($data['folder']) || isset($data['categoryKey'])) {
        $fields[] = "folder = :fld";
        $params[':fld'] = trim($data['folder'] ?? $data['categoryKey']);
    }
    if (isset($data['department'])) {
        $fields[] = "department = :dept";
        $params[':dept'] = trim($data['department']);
    }

    if (empty($fields)) {
        Response::error("No fields specified for update.", 422);
    }

    $fields[] = "updated_at = NOW()";

    try {
        $sql = "UPDATE documents SET " . implode(", ", $fields) . " WHERE doc_id = :did";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        AuditLogger::logAction(
            $currentEmpId,
            null,
            'Employee Intranet',
            'EMP',
            'UPDATE_POLICY_DOCUMENT',
            'documents',
            $docId,
            $data,
            'SUCCESS'
        );

        Response::success(['doc_id' => $docId], "Document updated successfully");
    } catch (Exception $e) {
        Response::error("Failed to update document: " . $e->getMessage(), 500);
    }
}

if ($method === 'DELETE') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true) ?: [];
    $docId = trim($_GET['id'] ?? ($data['doc_id'] ?? ($data['id'] ?? '')));

    if (empty($docId)) {
        Response::error("Document ID is required for deletion.", 422);
    }

    try {
        $pdo->prepare("DELETE FROM internal_policies WHERE doc_id = ?")->execute([$docId]);
        $stmt = $pdo->prepare("DELETE FROM documents WHERE doc_id = ?");
        $stmt->execute([$docId]);

        AuditLogger::logAction(
            $currentEmpId,
            null,
            'Employee Intranet',
            'EMP',
            'DELETE_DOCUMENT',
            'documents',
            $docId,
            [],
            'SUCCESS'
        );

        Response::success(['doc_id' => $docId], "Document deleted successfully");
    } catch (Exception $e) {
        Response::error("Failed to delete document: " . $e->getMessage(), 500);
    }
}
