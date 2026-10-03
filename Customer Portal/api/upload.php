<?php
declare(strict_types=1);

/**
 * Customer Portal - Secure Document & Attachment Upload Handler
 * Location: Customer Portal/api/upload.php
 * Handles multipart/form-data file uploads and registers documents in MariaDB.
 */

require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('CUS', ['customer' => true]);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$cusId = $_SESSION['cus_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? ($_GET['cus_id'] ?? null));

if (empty($cusId)) {
    $firstCus = $pdo->query("SELECT cus_id FROM customers WHERE status = 'Active' ORDER BY cus_id ASC LIMIT 1")->fetchColumn();
    $cusId = $firstCus ?: 'CUS-1001';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error("Method not allowed. Use POST multipart/form-data.", 405);
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $errCode = $_FILES['file']['error'] ?? 'NO_FILE';
    Response::error("No file uploaded or upload error occurred (Code: {$errCode}).", 400);
}

$file = $_FILES['file'];
$origName = basename($file['name']);
$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

$allowedExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'png', 'jpg', 'jpeg', 'dwg', 'dxf', 'step', 'stp', 'zip'];
if (!in_array($ext, $allowedExts, true)) {
    Response::error("Unsupported file extension '.{$ext}'. Allowed: " . implode(', ', $allowedExts), 400);
}

$maxSize = 25 * 1024 * 1024; // 25 MB
if ($file['size'] > $maxSize) {
    Response::error("File size exceeds 25 MB limit.", 400);
}

$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Generate unique, sanitized filename
$uniqueSuffix = bin2hex(random_bytes(6));
$safeBase = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($origName, PATHINFO_FILENAME));
$targetFileName = "{$safeBase}_{$uniqueSuffix}.{$ext}";
$targetPath = $uploadDir . $targetFileName;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    Response::error("Failed to save uploaded file to storage.", 500);
}

$fileHash = hash_file('sha256', $targetPath);
$fileSizeBytes = filesize($targetPath);
$fileSizeHuman = $fileSizeBytes >= 1048576 
    ? round($fileSizeBytes / 1048576, 1) . ' MB' 
    : round($fileSizeBytes / 1024, 1) . ' KB';

// Generate document ID
$docId = 'DOC-' . date('Y') . '-' . str_pad((string)mt_rand(100, 999), 3, '0', STR_PAD_LEFT);

$description = trim($_POST['description'] ?? "Uploaded specification dossier: {$origName}");
$classification = in_array($_POST['classification'] ?? '', ['Public', 'Internal', 'Confidential', 'TopSecret']) ? $_POST['classification'] : 'Confidential';
$folder = trim($_POST['folder'] ?? 'projects');
$projectRef = trim($_POST['project_ref'] ?? '');
$relatedPrjId = !empty($_POST['related_prj_id']) ? trim($_POST['related_prj_id']) : null;

try {
    $stmt = $pdo->prepare("
        INSERT INTO documents (
            doc_id, file_name, description, classification, folder, department,
            file_size, file_hash, status, retention_period, project_ref,
            customer_ref, owning_system, related_prj_id, related_cus_id, created_at, updated_at
        ) VALUES (
            :doc_id, :file_name, :description, :classification, :folder, 'ENG',
            :file_size, :file_hash, 'Approved', '7y', :project_ref,
            :customer_ref, 'Customer Portal', :related_prj_id, :related_cus_id, NOW(), NOW()
        )
    ");
    $stmt->execute([
        ':doc_id'         => $docId,
        ':file_name'      => $targetFileName,
        ':description'   => $description,
        ':classification' => $classification,
        ':folder'         => $folder,
        ':file_size'      => $fileSizeHuman,
        ':file_hash'      => $fileHash,
        ':project_ref'    => $projectRef,
        ':customer_ref'   => "Customer {$cusId}",
        ':related_prj_id' => $relatedPrjId,
        ':related_cus_id' => $cusId
    ]);

    AuditLogger::logSecurityEvent('FILE_UPLOADED', 'CUS', "Uploaded {$origName} as {$docId} (SHA256: {$fileHash})", 'Low', null, $cusId);

    Response::success([
        'doc_id'       => $docId,
        'file_name'    => $targetFileName,
        'original_name'=> $origName,
        'file_size'    => $fileSizeHuman,
        'file_hash'    => $fileHash,
        'classification'=> $classification,
        'folder'       => $folder,
        'created_at'   => date('Y-m-d H:i:s')
    ], "File uploaded and registered as {$docId} successfully", 201);
} catch (Exception $e) {
    @unlink($targetPath);
    Response::error("Failed to register uploaded file in database: " . $e->getMessage(), 500);
}
