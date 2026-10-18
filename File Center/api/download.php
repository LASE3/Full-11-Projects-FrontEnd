<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR File Center - Authorized Secure Document Download Endpoint
 * Location: File Center/api/download.php
 * Serves verified physical document files with clearance checks, tenant isolation, and audit logging.
 */

require_once __DIR__ . '/../../includes/api_bootstrap.php';
require_once __DIR__ . '/../../includes/document_storage.php';

// Authenticate session (allows EMP, CUS, or SuperAdmin)
$currentUser = null;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['vostok_user'])) {
    $currentUser = $_SESSION['vostok_user'];
} else {
    // Fall back to requireApiAuth
    $currentUser = requireApiAuth();
}

if (!$currentUser) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error'   => 'Unauthorized: Active VOSTOKPRIBOR session or API Bearer token required.'
    ]);
    exit;
}

$pdo = getDbConnection();
$docId = trim((string)($_GET['doc_id'] ?? ($_GET['id'] ?? '')));

if (empty($docId)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error'   => 'Document ID (doc_id) parameter is required.'
    ]);
    exit;
}

$inline = (!empty($_GET['inline']) && $_GET['inline'] !== '0' && $_GET['inline'] !== 'false');

try {
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE doc_id = :id LIMIT 1");
    $stmt->execute([':id' => $docId]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doc) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => "Document {$docId} not found."]);
        exit;
    }

    vp_authorize($currentUser, 'document:read', $doc, $pdo);
    vp_serve_document($pdo, $docId, $currentUser, $inline);
} catch (VpAuthorizationException $e) {
    vp_handle_api_exception($e, $pdo);
} catch (Throwable $e) {
    error_log("Document Download Exception: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error'   => 'Failed to retrieve requested document file.'
    ]);
    exit;
}
