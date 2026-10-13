<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR File Center - Database & API Response Helper
 * Location: File Center/api/db.php
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/AuditLogger.php';
require_once __DIR__ . '/../../includes/integration_bus.php';

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
function sendJsonResponse(bool $success, mixed $data = null, string $message = '', int $statusCode = 200, array $extra = []): void
{
    http_response_code($statusCode);
    $payload = array_merge([
        'success'   => $success,
        'message'   => $message,
        'data'      => $data,
        'timestamp' => date('Y-m-d H:i:s'),
    ], $extra);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Send success JSON response
 */
function apiSuccess(mixed $data = null, string $message = 'Success', int $statusCode = 200, array $extra = []): void
{
    sendJsonResponse(true, $data, $message, $statusCode, $extra);
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

require_once __DIR__ . '/../../includes/document_storage.php';

/**
 * Log action into document_access_log
 */
function logDocumentAction(
    PDO $pdo,
    string $docId,
    string $accessType,
    string $notes = '',
    ?string $empId = null,
    string $systemId = 'DOC',
    int $success = 1,
    ?string $cusId = null
): void {
    if (!$empId && !$cusId) {
        $custodian = getCurrentCustodian();
        $empId = $custodian['emp_id'] ?? 'EMP-1019';
    }
    vp_log_document_access($pdo, $docId, $accessType, $success, $notes, $empId, $cusId, $systemId);
}

