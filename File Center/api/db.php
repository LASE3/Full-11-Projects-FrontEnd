<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR File Center - Database & API Response Helper
 * Location: File Center/api/db.php
 */

require_once __DIR__ . '/../../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Set standardized JSON headers and return PDO instance
 */
function getApiPdo(): PDO
{
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    return getDbConnection();
}

/**
 * Send JSON response and exit
 */
function sendJsonResponse(bool $success, mixed $data = null, string $message = '', int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode([
        'success'   => $success,
        'message'   => $message,
        'data'      => $data,
        'timestamp' => date('Y-m-d H:i:s'),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Send success JSON response
 */
function apiSuccess(mixed $data = null, string $message = 'Success', int $statusCode = 200): void
{
    sendJsonResponse(true, $data, $message, $statusCode);
}

/**
 * Send error JSON response
 */
function apiError(string $message = 'An error occurred', int $statusCode = 400, mixed $data = null): void
{
    sendJsonResponse(false, $data, $message, $statusCode);
}

/**
 * Get current authenticated custodian user info
 */
function getCurrentCustodian(): array
{
    if (!empty($_SESSION['vostok_user'])) {
        return [
            'emp_id'          => $_SESSION['vostok_user']['user_id'] ?? $_SESSION['vostok_user']['emp_id'] ?? 'EMP-1019',
            'full_name'       => $_SESSION['vostok_user']['full_name'] ?? 'Farida Iskakova',
            'role'            => $_SESSION['vostok_user']['role_name'] ?? 'Lead Custodian',
            'clearance_level' => $_SESSION['vostok_user']['clearance_level'] ?? 'L3',
        ];
    }
    return [
        'emp_id'          => 'EMP-1019',
        'full_name'       => 'Farida Iskakova',
        'role'            => 'Lead Custodian',
        'clearance_level' => 'L3',
    ];
}

/**
 * Log action into document_access_log
 */
function logDocumentAction(
    PDO $pdo,
    string $docId,
    string $accessType,
    string $notes = '',
    ?string $empId = null,
    string $systemId = 'DOC'
): void {
    try {
        $custodian = getCurrentCustodian();
        $actorEmpId = $empId ?: $custodian['emp_id'];
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $stmt = $pdo->prepare("
            INSERT INTO document_access_log 
            (doc_id, accessed_by_emp_id, system_id, source_ip, access_type, notes, success, accessed_at)
            VALUES (:doc_id, :emp_id, :system_id, :ip, :access_type, :notes, 1, NOW())
        ");
        $stmt->execute([
            ':doc_id'      => $docId,
            ':emp_id'      => $actorEmpId,
            ':system_id'   => $systemId,
            ':ip'          => $ip,
            ':access_type' => $accessType,
            ':notes'       => $notes,
        ]);
    } catch (Throwable $e) {
        error_log('Failed to log document access: ' . $e->getMessage());
    }
}
