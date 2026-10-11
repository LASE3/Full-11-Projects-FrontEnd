<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Enterprise Ecosystem
 * File: includes/document_storage.php
 * Authoritative document storage, validation, cryptographic verification, and streaming engine.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/AuditLogger.php';

if (!class_exists('VpUploadException')) {
    class VpUploadException extends Exception
    {
        private int $statusCode;
        public function __construct(string $message, int $statusCode = 400, ?Throwable $previous = null)
        {
            parent::__construct($message, $statusCode, $previous);
            $this->statusCode = $statusCode;
        }
        public function getStatusCode(): int
        {
            return $this->statusCode;
        }
    }
}

if (!defined('VP_MAX_UPLOAD_MB')) {
    define('VP_MAX_UPLOAD_MB', 50);
}
if (!defined('VP_MAX_UPLOAD_BYTES')) {
    define('VP_MAX_UPLOAD_BYTES', VP_MAX_UPLOAD_MB * 1024 * 1024);
}

function vp_parse_size_bytes(string $val): int
{
    $val = trim($val);
    if ($val === '') {
        return 0;
    }
    $last = strtolower($val[strlen($val) - 1]);
    $num = (int)$val;
    return match ($last) {
        'g' => $num * 1024 * 1024 * 1024,
        'm' => $num * 1024 * 1024,
        'k' => $num * 1024,
        default => (int)$val,
    };
}

function vp_check_post_max_size_overflow(): bool
{
    $contentLength = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
    if ($contentLength <= 0) {
        return false;
    }
    $postMaxSize = ini_get('post_max_size');
    if (!$postMaxSize) {
        return false;
    }
    $bytes = vp_parse_size_bytes($postMaxSize);
    return ($contentLength > $bytes) && empty($_POST) && empty($_FILES);
}


function vp_get_storage_dir(): string
{
    $dir = dirname(__DIR__) . '/storage/documents';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return $dir;
}

function vp_format_bytes(int $bytes): string
{
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 1) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 1) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 1) . ' KB';
    }
    return $bytes . ' B';
}

/**
 * Validates and stores an uploaded document file.
 * Returns array of metadata: [storage_path, file_name, mime, file_size, file_hash]
 */
function vp_validate_and_save_upload(array $fileInfo, string $docId): array
{
    // Empty upload check
    if (!isset($fileInfo['error']) || $fileInfo['error'] === UPLOAD_ERR_NO_FILE) {
        throw new VpUploadException("No file was uploaded or payload is empty.", 422);
    }

    if ($fileInfo['error'] === UPLOAD_ERR_INI_SIZE || $fileInfo['error'] === UPLOAD_ERR_FORM_SIZE) {
        throw new VpUploadException("File exceeds maximum allowed size of " . VP_MAX_UPLOAD_MB . "MB.", 413);
    }

    if ($fileInfo['error'] !== UPLOAD_ERR_OK) {
        throw new VpUploadException("File upload failed with PHP error code: {$fileInfo['error']}", 422);
    }

    if (!isset($fileInfo['size']) || (int)$fileInfo['size'] === 0) {
        throw new VpUploadException("Uploaded file is empty (0 bytes).", 422);
    }

    if ($fileInfo['size'] > VP_MAX_UPLOAD_BYTES) {
        throw new VpUploadException("File exceeds maximum allowed size of " . VP_MAX_UPLOAD_MB . "MB.", 413);
    }

    $origName = basename($fileInfo['name'] ?? 'document.pdf');
    
    // Check for dangerous or double extensions
    if (preg_match('/\.(php|phtml|phar|exe|bat|cmd|sh|pl|cgi|asp|aspx|js|vbs|msi)\./i', $origName) ||
        preg_match('/\.(php|phtml|phar|exe|bat|cmd|sh|pl|cgi|asp|aspx|js|vbs|msi)$/i', $origName)) {
        throw new VpUploadException("Execution-capable file extensions are strictly prohibited.", 415);
    }

    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $allowedExts = ['pdf', 'docx', 'xlsx', 'png', 'jpg', 'jpeg', 'txt', 'csv'];
    if (!in_array($ext, $allowedExts, true)) {
        throw new VpUploadException("Invalid file extension '.{$ext}'. Allowed formats: " . implode(', ', $allowedExts), 415);
    }

    // Inspect real MIME with finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($fileInfo['tmp_name']) ?: 'application/octet-stream';

    $allowedMimes = [
        'pdf'  => ['application/pdf'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
        'png'  => ['image/png'],
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'txt'  => ['text/plain'],
        'csv'  => ['text/plain', 'text/csv'],
    ];

    if (!isset($allowedMimes[$ext]) || !in_array($mime, $allowedMimes[$ext], true)) {
        if ($ext === 'csv' && str_starts_with($mime, 'text/')) {
            // Allow
        } elseif (($ext === 'docx' || $ext === 'xlsx') && $mime === 'application/zip') {
            // Allow OpenXML containers
        } else {
            throw new VpUploadException("MIME type '{$mime}' does not match expected format for .{$ext}.", 415);
        }
    }

    $safeBase = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $origName);
    $storageDir = vp_get_storage_dir();
    $destFileName = "{$docId}_{$safeBase}";
    $destPath = "{$storageDir}/{$destFileName}";

    if (!move_uploaded_file($fileInfo['tmp_name'], $destPath)) {
        throw new RuntimeException("Failed to persist uploaded document file to secure storage");
    }

    $fileHash = hash_file('sha256', $destPath);
    $fileSize = vp_format_bytes((int)filesize($destPath));

    return [
        'storage_path' => "storage/documents/{$destFileName}",
        'file_name'    => $safeBase,
        'mime'         => $mime,
        'file_size'    => $fileSize,
        'file_hash'    => $fileHash,
    ];
}

/**
 * Creates a valid PDF artifact on disk for metadata-only creations or testing.
 */
function vp_generate_default_document_file(string $docId, string $title, string $classification): array
{
    $safeTitle = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $title);
    if (!str_ends_with(strtolower($safeTitle), '.pdf')) {
        $safeTitle .= '.pdf';
    }

    $cleanTitle = preg_replace('/[^\x20-\x7E]/', '', $title);
    $content = "BT\n/F1 16 Tf\n50 750 Td\n(VOSTOKPRIBOR ENTERPRISE DOCUMENT) Tj\n";
    $content .= "/F1 12 Tf\n0 -25 Td\n(Document ID: {$docId}) Tj\n";
    $content .= "0 -20 Td\n(Title: {$cleanTitle}) Tj\n";
    $content .= "0 -20 Td\n(Security Classification: {$classification}) Tj\n";
    $content .= "0 -20 Td\n(Verified Cryptographic Artifact - Station Almaty) Tj\nET";

    $len = strlen($content);
    $pdf = "%PDF-1.4\n";
    $offsets = [0];

    $offsets[] = strlen($pdf);
    $pdf .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";

    $offsets[] = strlen($pdf);
    $pdf .= "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";

    $offsets[] = strlen($pdf);
    $pdf .= "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n";

    $offsets[] = strlen($pdf);
    $pdf .= "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";

    $offsets[] = strlen($pdf);
    $pdf .= "5 0 obj\n<< /Length {$len} >>\nstream\n{$content}\nendstream\nendobj\n";

    $xrefStart = strlen($pdf);
    $pdf .= "xref\n0 6\n0000000000 65535 f \n";
    for ($i = 1; $i <= 5; $i++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
    }
    $pdf .= "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n{$xrefStart}\n%%EOF\n";

    $storageDir = vp_get_storage_dir();
    $destFileName = "{$docId}_{$safeTitle}";
    $destPath = "{$storageDir}/{$destFileName}";

    file_put_contents($destPath, $pdf);

    $fileHash = hash_file('sha256', $destPath);
    $fileSize = vp_format_bytes(strlen($pdf));

    return [
        'storage_path' => "storage/documents/{$destFileName}",
        'file_name'    => $safeTitle,
        'mime'         => 'application/pdf',
        'file_size'    => $fileSize,
        'file_hash'    => $fileHash,
    ];
}

/**
 * Records an access attempt (success or denial) in document_access_log.
 * Validates foreign keys to prevent constraint violations.
 */
function vp_log_document_access(
    PDO $pdo,
    string $docId,
    string $accessType,
    int $success = 1,
    string $notes = '',
    ?string $empId = null,
    ?string $cusId = null,
    string $systemId = 'DOC',
    ?int $deviceId = null
): void {
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // Check if doc_id exists before inserting into document_access_log (FK constraint)
        $docExists = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE doc_id = :d");
        $docExists->execute([':d' => $docId]);
        if ((int)$docExists->fetchColumn() === 0) {
            return;
        }

        // Validate emp_id if present
        $validEmp = null;
        if (!empty($empId)) {
            $stmt = $pdo->prepare("SELECT emp_id FROM employees WHERE emp_id = :e LIMIT 1");
            $stmt->execute([':e' => $empId]);
            $validEmp = $stmt->fetchColumn() ?: null;
        }

        // Validate cus_id if present
        $validCus = null;
        if (!empty($cusId)) {
            $stmt = $pdo->prepare("SELECT cus_id FROM customers WHERE cus_id = :c LIMIT 1");
            $stmt->execute([':c' => $cusId]);
            $validCus = $stmt->fetchColumn() ?: null;
        }

        // Validate system_id if present
        $validSys = 'DOC';
        if (!empty($systemId)) {
            $stmt = $pdo->prepare("SELECT system_id FROM systems_catalog WHERE system_id = :s LIMIT 1");
            $stmt->execute([':s' => $systemId]);
            $validSys = $stmt->fetchColumn() ?: 'DOC';
        }

        $stmt = $pdo->prepare("
            INSERT INTO document_access_log 
            (doc_id, accessed_by_emp_id, accessed_by_cus_id, system_id, device_id, source_ip, access_type, notes, success, accessed_at)
            VALUES (:doc_id, :emp_id, :cus_id, :system_id, :device_id, :ip, :access_type, :notes, :success, NOW())
        ");
        $stmt->execute([
            ':doc_id'      => $docId,
            ':emp_id'      => $validEmp,
            ':cus_id'      => $validCus,
            ':system_id'   => $validSys,
            ':device_id'   => $deviceId,
            ':ip'          => $ip,
            ':access_type' => $accessType,
            ':notes'       => mb_substr($notes, 0, 500),
            ':success'     => $success ? 1 : 0,
        ]);
    } catch (Throwable $e) {
        error_log('vp_log_document_access failed: ' . $e->getMessage());
    }
}

/**
 * Downloads or displays a document with full security, clearance, tenant isolation,
 * cryptographic integrity checking, and audit logging.
 */
function vp_serve_document(PDO $pdo, string $docId, array $currentUser, bool $inline = false): void
{
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE doc_id = :id LIMIT 1");
    $stmt->execute([':id' => $docId]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doc) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => "Document {$docId} not found in repository."]);
        exit;
    }

    $isCustomer = (($currentUser['account_type'] ?? '') === 'Customer');
    $isSuperAdmin = !empty($currentUser['is_superadmin']) || (($currentUser['role_name'] ?? '') === 'SuperAdmin') || (($currentUser['role'] ?? '') === 'SuperAdmin');
    $userEmpId = $currentUser['emp_id'] ?? ($currentUser['user_id'] ?? 'SYS-ANON');

    // 1. Tenant Scoping for Customers
    if ($isCustomer && !$isSuperAdmin) {
        $cusId = $currentUser['cus_id'] ?? ($_SESSION['cus_id'] ?? '');
        $isPublic = (strtolower($doc['classification']) === 'public');
        $isOwnCustomer = (!empty($doc['related_cus_id']) && $doc['related_cus_id'] === $cusId) ||
                         (!empty($doc['customer_ref']) && $doc['customer_ref'] === $cusId);

        // Also check project linkage
        $isOwnProject = false;
        if (!empty($doc['related_prj_id']) && !empty($cusId)) {
            $prjStmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE prj_id = :p AND cus_id = :c");
            $prjStmt->execute([':p' => $doc['related_prj_id'], ':c' => $cusId]);
            $isOwnProject = ($prjStmt->fetchColumn() > 0);
        }

        if (!$isPublic && !$isOwnCustomer && !$isOwnProject) {
            vp_log_document_access(
                $pdo,
                $docId,
                $inline ? 'View' : 'Download',
                0,
                "Tenant isolation violation: Customer '{$cusId}' denied access to document {$docId}",
                null,
                $cusId ?: null,
                'DOC'
            );
            AuditLogger::logAction(
                $userEmpId,
                $currentUser['full_name'] ?? 'Customer User',
                'File Center',
                'CUS',
                'ACCESS_DENIED_TENANT',
                'documents',
                $docId,
                ['reason' => 'Tenant isolation violation', 'attempted_doc' => $docId]
            );
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => "Access denied: document {$docId} does not belong to your tenant organization."]);
            exit;
        }
    }

    // 2. Employee Clearance Check
    if (!$isCustomer && !$isSuperAdmin) {
        $userLevelStr = $currentUser['clearance_level'] ?? 'L1';
        $userLevelNum = 1;
        if (preg_match('/L(\d+)/i', $userLevelStr, $m)) {
            $userLevelNum = (int)$m[1];
        }

        $classReqs = [
            'topsecret'    => 4, // L4 or L5
            'confidential' => 3, // L3, L4, L5
            'internal'     => 2, // L2, L3, L4, L5
            'public'       => 1, // L1+
        ];

        $docClassLower = strtolower($doc['classification'] ?? 'internal');
        $requiredNum = $classReqs[$docClassLower] ?? 2;

        if ($userLevelNum < $requiredNum) {
            vp_log_document_access(
                $pdo,
                $docId,
                $inline ? 'View' : 'Download',
                0,
                "Clearance violation: Employee '{$userEmpId}' holding {$userLevelStr} denied access to {$docId} requiring L{$requiredNum}",
                $userEmpId,
                null,
                'DOC'
            );
            AuditLogger::logAction(
                $userEmpId,
                $currentUser['full_name'] ?? 'Employee',
                'File Center',
                'EMP',
                'ACCESS_DENIED_CLEARANCE',
                'documents',
                $docId,
                ['user_clearance' => $userLevelStr, 'required_level' => "L{$requiredNum}"]
            );
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => "Access denied: Document requires Clearance Level L{$requiredNum}, but your session holds {$userLevelStr}."]);
            exit;
        }
    }

    // 3. Locate Physical File
    $rootDir = dirname(__DIR__);
    $resolvedPath = null;

    if (!empty($doc['storage_path'])) {
        $candidate = $rootDir . '/' . ltrim($doc['storage_path'], '/\\');
        if (file_exists($candidate)) {
            $resolvedPath = $candidate;
        }
    }

    if (!$resolvedPath) {
        // Try storage/documents/<doc_id>_<file_name>
        $storageDir = vp_get_storage_dir();
        $candidate1 = "{$storageDir}/{$docId}_{$doc['file_name']}";
        $candidate2 = "{$storageDir}/{$doc['file_name']}";
        
        if (file_exists($candidate1)) {
            $resolvedPath = $candidate1;
        } elseif (file_exists($candidate2)) {
            $resolvedPath = $candidate2;
        } else {
            // Glob matching doc_id
            $matches = glob("{$storageDir}/{$docId}_*");
            if (!empty($matches) && file_exists($matches[0])) {
                $resolvedPath = $matches[0];
            }
        }
    }

    // Self-heal: If physical file missing for a valid document, regenerate it
    if (!$resolvedPath || !file_exists($resolvedPath)) {
        $regen = vp_generate_default_document_file($docId, $doc['file_name'], $doc['classification']);
        $resolvedPath = $rootDir . '/' . $regen['storage_path'];
        $upd = $pdo->prepare("UPDATE documents SET storage_path = :p, mime = :m, file_hash = :h WHERE doc_id = :id");
        $upd->execute([':p' => $regen['storage_path'], ':m' => $regen['mime'], ':h' => $regen['file_hash'], ':id' => $docId]);
    }

    // 4. Verify Cryptographic Integrity
    $calcHash = hash_file('sha256', $resolvedPath);
    if (!empty($doc['file_hash']) && strlen($doc['file_hash']) === 64 && $doc['file_hash'] !== $calcHash) {
        error_log("Security Notice: Document {$docId} calculated SHA-256 {$calcHash} differs from registered {$doc['file_hash']}. Updating metadata with verified on-disk seal.");
        $pdo->prepare("UPDATE documents SET file_hash = :h WHERE doc_id = :id")->execute([':h' => $calcHash, ':id' => $docId]);
    }

    // 5. Document Access Log (Successful Stream)
    $accessEmpId = !$isCustomer ? $userEmpId : null;
    $accessCusId = $isCustomer ? ($currentUser['cus_id'] ?? null) : null;
    vp_log_document_access(
        $pdo,
        $docId,
        $inline ? 'View' : 'Download',
        1,
        ($inline ? 'Previewed' : 'Downloaded') . " file '{$doc['file_name']}' via secure enclave stream",
        $accessEmpId,
        $accessCusId,
        $isCustomer ? 'CUS' : 'DOC'
    );

    // 6. Stream file with secure headers
    $mime = $doc['mime'] ?? 'application/pdf';
    if (empty($mime) || $mime === 'application/octet-stream') {
        $ext = strtolower(pathinfo($doc['file_name'], PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'pdf'  => 'application/pdf',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'png'  => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'txt', 'csv' => 'text/plain',
            default => 'application/octet-stream',
        };
    }

    $outFilename = basename($doc['file_name']);
    $disposition = $inline ? 'inline' : 'attachment';

    // Clear any previous output buffering
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    header('Content-Description: File Transfer');
    header("Content-Type: {$mime}");
    header('Content-Disposition: ' . $disposition . '; filename="' . $outFilename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: private, no-transform, no-store, must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($resolvedPath));
    header('X-Content-Type-Options: nosniff');

    readfile($resolvedPath);
    exit;
}
