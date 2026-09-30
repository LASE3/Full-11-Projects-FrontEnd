<?php
declare(strict_types=1);

/**
 * Class 2: Online Shop B2B - Secure File Upload API
 * Location: Online Shop B2B/api/upload.php
 * Handles PO documents, technical specs, and RFQ attachments.
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$cusId = $_SESSION['cus_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? ($_POST['cus_id'] ?? 'CUS-1001'));

if ($method !== 'POST') {
    Response::error("Method not allowed. Only POST is accepted for file upload.", 405);
}

if (empty($_FILES['file']) && empty($_FILES['document']) && empty($_FILES['upload']) && empty($_FILES['po_file'])) {
    Response::error("No file payload received.", 400);
}

$fileKey = !empty($_FILES['file']) ? 'file' : (!empty($_FILES['document']) ? 'document' : (!empty($_FILES['po_file']) ? 'po_file' : 'upload'));
$file = $_FILES[$fileKey];

if ($file['error'] !== UPLOAD_ERR_OK) {
    Response::error("File upload error code: {$file['error']}", 400);
}

// 25MB Max
if ($file['size'] > 25 * 1024 * 1024) {
    Response::error("File size exceeds 25MB limit.", 413);
}

$origName = basename($file['name']);
$ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
$allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'png', 'jpg', 'jpeg', 'zip'];

if (!in_array($ext, $allowed, true)) {
    Response::error("File extension .{$ext} is not permitted.", 415);
}

// Target directory
$uploadDir = __DIR__ . '/../uploads/documents/';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0777, true);
}

$uniqueName = 'SHP-' . date('Ymd-His') . '-' . substr(md5(uniqid()), 0, 8) . '.' . $ext;
$targetPath = $uploadDir . $uniqueName;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    Response::error("Failed to store uploaded file on server filesystem.", 500);
}

$docId = 'DOC-SHP-' . rand(1000, 9999);
$description = trim((string)($_POST['description'] ?? "Uploaded via B2B Shop for Order / RFQ"));
$orderId = trim((string)($_POST['order_id'] ?? ''));
$folder = !empty($_POST['folder']) ? trim((string)$_POST['folder']) : 'Orders & Procurement';

try {
    $stmt = $pdo->prepare("
        INSERT INTO documents (
            doc_id, file_name, description, classification, folder, department,
            file_size, file_hash, status, retention_period,
            customer_ref, owning_system, related_cus_id, created_at, updated_at
        ) VALUES (
            :did, :fn, :desc, 'Confidential', :folder, 'SAL',
            :fsize, :fhash, 'Approved', '5 Years',
            :cref, 'Online Shop B2B (SYS-02)', :cid, NOW(), NOW()
        )
    ");
    $stmt->execute([
        ':did'   => $docId,
        ':fn'    => $origName,
        ':desc'  => $description . ($orderId ? " (Order #{$orderId})" : ""),
        ':folder'=> $folder,
        ':fsize' => (string)$file['size'],
        ':fhash' => hash_file('sha256', $targetPath),
        ':cref'  => $cusId,
        ':cid'   => $cusId
    ]);

    AuditLogger::logAction(
        null,
        $cusId,
        'Online Shop B2B',
        'SHP',
        'UPLOAD_PROCUREMENT_DOCUMENT',
        'documents',
        $docId,
        ['file_name' => $origName, 'order_id' => $orderId, 'file_size' => $file['size']],
        'SUCCESS'
    );

    Response::success([
        'doc_id'    => $docId,
        'file_name' => $origName,
        'file_url'  => 'uploads/documents/' . $uniqueName,
        'file_size' => $file['size'],
        'order_id'  => $orderId
    ], "Document uploaded and registered in database successfully.", 201);
} catch (Exception $e) {
    Response::error("Failed to register document in database: " . $e->getMessage(), 500);
}
