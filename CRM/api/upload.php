<?php

declare(strict_types=1);

/**
 * Class 5: CRM Platform - Secure File Upload & Document Intake API
 * Location: CRM/api/upload.php
 * Methods: POST
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$empId = $_SESSION['emp_id'] ?? ($_SESSION['vostok_user']['emp_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? 'EMP-1006'));

if ($method !== 'POST') {
    Response::error("Method not allowed. Only POST is accepted for file upload.", 405);
}

if (empty($_FILES['file']) && empty($_FILES['document']) && empty($_FILES['upload'])) {
    Response::error("No file payload uploaded under 'file', 'document', or 'upload'.", 400);
}

$fileField = !empty($_FILES['file']) ? 'file' : (!empty($_FILES['document']) ? 'document' : 'upload');
$uploadedFile = $_FILES[$fileField];

if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
    $errMap = [
        UPLOAD_ERR_INI_SIZE   => 'Uploaded file exceeds upload_max_filesize limit.',
        UPLOAD_ERR_FORM_SIZE  => 'Uploaded file exceeds MAX_FILE_SIZE specified in HTML form.',
        UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
        UPLOAD_ERR_NO_FILE    => 'No file was submitted.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder on server.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
    ];
    $msg = $errMap[$uploadedFile['error']] ?? "Unknown upload error code: {$uploadedFile['error']}";
    Response::error($msg, 400);
}

// Maximum 25MB file size
$maxBytes = 25 * 1024 * 1024;
if ($uploadedFile['size'] > $maxBytes) {
    Response::error("File size exceeds 25MB limit.", 413);
}

$origName = basename($uploadedFile['name']);
$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

$allowedExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'rtf', 'png', 'jpg', 'jpeg', 'webp', 'svg', 'json', 'zip'];
if (!in_array($ext, $allowedExts, true)) {
    Response::error("File type .{$ext} is not allowed for security reasons.", 415);
}

$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

$sanitizedStem = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
$uniqueFilename = 'CRM_' . date('Ymd_His') . '_' . substr(md5(uniqid('', true)), 0, 8) . '_' . substr($sanitizedStem, 0, 32) . '.' . $ext;
$destPath = $uploadDir . $uniqueFilename;

if (!move_uploaded_file($uploadedFile['tmp_name'], $destPath)) {
    Response::error("Failed to save uploaded file on server.", 500);
}

$fileUrl = 'uploads/' . $uniqueFilename;
$targetCusId = trim((string)($_POST['cus_id'] ?? $_POST['id'] ?? ''));

// Log upload in AuditLogger
AuditLogger::logAction(
    $empId,
    !empty($targetCusId) ? $targetCusId : null,
    'CRM Platform',
    'CRM',
    'UPLOAD_DOCUMENT',
    'documents',
    $uniqueFilename,
    [
        'original_name' => $origName,
        'size_bytes'    => $uploadedFile['size'],
        'file_path'     => $fileUrl,
        'mime_type'     => $uploadedFile['type'] ?? 'application/octet-stream'
    ],
    'SUCCESS'
);

// If uploaded for a customer, add to crm_activities
if (!empty($targetCusId)) {
    try {
        $pdo->prepare("
            INSERT INTO `crm_activities` (activity_type, title, description, cus_id, emp_id)
            VALUES ('Document', 'Document Uploaded', :desc, :cid, :eid)
        ")->execute([
            ':desc' => "Uploaded document: {$origName} (" . round($uploadedFile['size'] / 1024, 1) . " KB)",
            ':cid'  => $targetCusId,
            ':eid'  => $empId
        ]);
    } catch (Throwable $e) {
        // non-blocking
    }
}

Response::success([
    'file_name'     => $uniqueFilename,
    'original_name' => $origName,
    'file_url'      => $fileUrl,
    'file_size'     => $uploadedFile['size'],
    'extension'     => $ext
], "File '{$origName}' uploaded successfully.", 201);
