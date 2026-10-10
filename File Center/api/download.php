<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR File Center - Authorized Secure Document Download Endpoint
 * Location: File Center/api/download.php
 * Serves verified physical document files with clearance checks, tenant isolation, and audit logging.
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../includes/document_storage.php';

// Authenticate session (allows EMP, CUS, or SuperAdmin)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentUser = $_SESSION['vostok_user'] ?? null;
if (!$currentUser) {
    // Check if token present in Authorization header
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $m)) {
        require_once __DIR__ . '/../../includes/api_bootstrap.php';
        $currentUser = vp_api_guard('DOC', []);
    }
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
    vp_serve_document($pdo, $docId, $currentUser, $inline);
} catch (Throwable $e) {
    error_log("Document Download Exception: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error'   => 'Failed to retrieve requested document file.'
    ]);
    exit;
}
