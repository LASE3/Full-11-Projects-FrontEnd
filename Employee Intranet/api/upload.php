<?php

declare(strict_types=1);

/**
 * Class 4: Employee Intranet - Document Upload API
 * Location: Employee Intranet/api/upload.php
 * Handles real document, policy, and attachment uploads.
 */

require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('EMP', []);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/integration_bus.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

$pdo = getDbConnection();
$currentEmpId = $_SESSION['emp_id'] ?? ($_SESSION['vostok_user']['emp_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? 'EMP-1004'));
$dept = $_SESSION['department_code'] ?? ($_SESSION['vostok_user']['department_code'] ?? 'ENG');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error("Method not allowed. Use POST.", 405);
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $errCode = $_FILES['file']['error'] ?? 'NO_FILE';
    Response::error("Upload failed with error code: {$errCode}", 400);
}

$file = $_FILES['file'];
$origName = basename($file['name']);
$tmpPath = $file['tmp_name'];
$fileSizeBytes = (int)$file['size'];

// Max size 50 MB
if ($fileSizeBytes > 50 * 1024 * 1024) {
    Response::error("File exceeds maximum allowable size of 50 MB.", 422);
}

$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
$allowed = ['pdf', 'docx', 'xlsx', 'pptx', 'csv', 'txt', 'png', 'jpg', 'jpeg', 'zip'];
if (!in_array($ext, $allowed, true)) {
    Response::error("File type .{$ext} is not authorized for intranet archival.", 422);
}

$uploadDir = __DIR__ . '/../uploads';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0777, true);
}

$safeBase = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($origName, PATHINFO_FILENAME));
$timestamp = date('Ymd_His');
$savedName = "{$safeBase}_{$timestamp}.{$ext}";
$targetPath = "{$uploadDir}/{$savedName}";

if (!move_uploaded_file($tmpPath, $targetPath)) {
    Response::error("Failed to persist file to server uploads repository.", 500);
}

$hash = hash_file('sha256', $targetPath);

// Format file size
if ($fileSizeBytes >= 1048576) {
    $sizeStr = round($fileSizeBytes / 1048576, 1) . ' MB';
} elseif ($fileSizeBytes >= 1024) {
    $sizeStr = round($fileSizeBytes / 1024, 0) . ' KB';
} else {
    $sizeStr = $fileSizeBytes . ' B';
}

$title = trim($_POST['title'] ?? ($_POST['description'] ?? pathinfo($origName, PATHINFO_FILENAME)));
$folder = trim($_POST['folder'] ?? ($_POST['categoryKey'] ?? 'policies'));
$classification = trim($_POST['classification'] ?? 'Internal');

// Generate unique doc_id
$year = date('Y');
$docId = vp_next_id($pdo, 'documents', "DOC-{$year}-", 3);

// Normalize classification
if (!in_array($classification, ['Public', 'Internal', 'Confidential', 'TopSecret'], true)) {
    $cMap = [
        'restricted'   => 'TopSecret',
        'topsecret'    => 'TopSecret',
        'confidential' => 'Confidential',
        'internal'     => 'Internal',
        'public'       => 'Public'
    ];
    $classification = $cMap[strtolower($classification)] ?? 'Internal';
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO documents 
        (doc_id, file_name, description, classification, folder, department, file_size, file_hash, status, owner_emp_id, created_at, updated_at)
        VALUES 
        (:did, :fn, :desc, :cls, :fld, :dept, :sz, :hash, 'Active', :owner, NOW(), NOW())
    ");
    $stmt->execute([
        ':did'   => $docId,
        ':fn'    => $origName,
        ':desc'  => $title,
        ':cls'   => $classification,
        ':fld'   => $folder,
        ':dept'  => $dept,
        ':sz'    => $sizeStr,
        ':hash'  => $hash,
        ':owner' => $currentEmpId
    ]);

    if (in_array(strtolower($folder), ['policies', 'governance', 'security'])) {
        $pStmt = $pdo->prepare("INSERT INTO internal_policies (title, doc_id, effective_date) VALUES (:t, :d, CURDATE())");
        $pStmt->execute([':t' => $title, ':d' => $docId]);
    }

    AuditLogger::logAction(
        $currentEmpId,
        null,
        'Employee Intranet',
        'EMP',
        'UPLOAD_DOCUMENT',
        'documents',
        $docId,
        ['file_name' => $origName, 'sha256' => $hash, 'size' => $sizeStr],
        'SUCCESS'
    );

    // Cross-system integration: EMP -> DOC
    vp_emit($pdo, 'EMP_TO_DOC', 'EMP', 'DOC', 'DOCUMENT_UPLOADED', [
        'doc_id'    => $docId,
        'folder'    => $folder,
        'file_name' => $origName
    ], $currentEmpId);

    Response::success([
        'doc_id'    => $docId,
        'file_name' => $origName,
        'saved_as'  => $savedName,
        'size'      => $sizeStr,
        'hash'      => $hash
    ], "Document uploaded and registered successfully in database", 201);
} catch (Exception $e) {
    Response::error("Database registration failed: " . $e->getMessage(), 500);
}
