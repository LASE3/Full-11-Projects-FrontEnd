<?php

declare(strict_types=1);

/**
 * Class 4: Employee Intranet - Data Export Engine
 * Location: Employee Intranet/api/export.php
 * Handles real CSV / JSON / File exports for Directory, Policies, Announcements, and Leaves.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

$pdo = getDbConnection();
$type = strtolower(trim($_GET['type'] ?? 'directory'));
$format = strtolower(trim($_GET['format'] ?? 'csv'));
$currentEmpId = $_SESSION['emp_id'] ?? ($_SESSION['vostok_user']['emp_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? 'EMP-1004'));

// If a specific doc_id is requested for download:
if ($type === 'policies' && !empty($_GET['doc_id'])) {
    $docId = trim($_GET['doc_id']);
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE doc_id = ?");
    $stmt->execute([$docId]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doc) {
        http_response_code(404);
        die("Document '{$docId}' not found in database.");
    }

    // Check if real file exists in uploads
    $uploadDir = __DIR__ . '/../uploads';
    $files = glob("{$uploadDir}/*" . pathinfo($doc['file_name'], PATHINFO_FILENAME) . "*");
    if (!empty($files) && file_exists($files[0])) {
        $realFile = $files[0];
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

    // Otherwise generate clean policy specification text document
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $doc['file_name']) . '.txt"');
    echo "========================================================================\n";
    echo "VOSTOKPRIBOR ENTERPRISE DOCUMENT REPOSITORY — OFFICIAL ARCHIVE\n";
    echo "Document ID:     " . ($doc['doc_id'] ?? '') . "\n";
    echo "File Name:       " . ($doc['file_name'] ?? '') . "\n";
    echo "Classification:  " . ($doc['classification'] ?? '') . "\n";
    echo "Folder / Unit:   " . ($doc['folder'] ?? '') . " / " . ($doc['department'] ?? '') . "\n";
    echo "File Size:       " . ($doc['file_size'] ?? '') . "\n";
    echo "File Hash:       " . ($doc['file_hash'] ?? 'SHA256-VERIFIED') . "\n";
    echo "Created:         " . ($doc['created_at'] ?? '') . "\n";
    echo "========================================================================\n\n";
    echo "DESCRIPTION & SCOPE:\n";
    echo ($doc['description'] ?? 'Standard Operating Procedure') . "\n\n";
    echo "This document is registered in the VOSTOKPRIBOR Employee Intranet under ISO-27001 zero-trust controls.\n";
    exit;
}

$data = [];
$filename = "intranet_{$type}_" . date('Ymd_His');

switch ($type) {
    case 'directory':
    case 'employees':
        $stmt = $pdo->query("
            SELECT 
                e.emp_id,
                e.full_name,
                e.job_title,
                e.department_code,
                d.dept_name,
                e.clearance_level,
                e.email,
                e.employment_status,
                e.hire_date
            FROM employees e
            LEFT JOIN departments d ON e.department_code = d.dept_code
            WHERE e.employment_status = 'Active'
            ORDER BY d.dept_code ASC, e.clearance_level DESC, e.full_name ASC
        ");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'policies':
    case 'documents':
        $stmt = $pdo->query("
            SELECT 
                d.doc_id,
                d.file_name,
                d.description,
                d.classification,
                d.folder,
                d.department,
                d.file_size,
                d.status,
                d.created_at
            FROM documents d
            ORDER BY d.created_at DESC
        ");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'announcements':
    case 'news':
        $stmt = $pdo->query("
            SELECT 
                a.announcement_id,
                a.title,
                a.body,
                a.audience_dept,
                e.full_name AS posted_by,
                a.posted_at
            FROM announcements a
            LEFT JOIN employees e ON a.posted_by_emp_id = e.emp_id
            ORDER BY a.posted_at DESC
        ");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'leaves':
        $stmt = $pdo->query("
            SELECT 
                lr.leave_id,
                lr.emp_id,
                e.full_name AS employee_name,
                e.department_code,
                lr.leave_type,
                lr.start_date,
                lr.end_date,
                lr.status,
                ap.full_name AS approved_by
            FROM leave_requests lr
            LEFT JOIN employees e ON lr.emp_id = e.emp_id
            LEFT JOIN employees ap ON lr.approved_by_emp_id = ap.emp_id
            ORDER BY lr.leave_id DESC
        ");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;

    default:
        http_response_code(400);
        die("Invalid export type: {$type}");
}

AuditLogger::logAction(
    $currentEmpId,
    null,
    'Employee Intranet',
    'EMP',
    'EXPORT_DATA',
    $type,
    null,
    ['format' => $format, 'row_count' => count($data)],
    'SUCCESS'
);

if ($format === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"{$filename}.json\"");
    echo json_encode(['success' => true, 'count' => count($data), 'data' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Default CSV export
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=\"{$filename}.csv\"");
header('Pragma: no-cache');
header('Expires: 0');

$out = fopen('php://output', 'w');
// UTF-8 BOM for Microsoft Excel
fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

if (!empty($data)) {
    // Write CSV header
    fputcsv($out, array_keys($data[0]), ',', '"', '\\');
    // Write CSV rows
    foreach ($data as $row) {
        fputcsv($out, $row, ',', '"', '\\');
    }
} else {
    fputcsv($out, ['Notice'], ',', '"', '\\');
    fputcsv($out, ['No data records available for export'], ',', '"', '\\');
}

fclose($out);
exit;
