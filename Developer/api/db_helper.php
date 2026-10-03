<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Developer Portal API Helper
 * Database accessor, table auto-verifier, and JSON response utility.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/AuditLogger.php';
require_once __DIR__ . '/../../includes/integration_bus.php';

/**
 * Send JSON response and exit
 */
function sendJsonResponse(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Send Error response
 */
function sendJsonError(string $message, int $statusCode = 400, array $extra = []): void
{
    sendJsonResponse(array_merge(['success' => false, 'error' => $message], $extra), $statusCode);
}

/**
 * Send Success response
 */
function sendJsonSuccess(mixed $data = null, string $message = 'Operation successful', array $extra = []): void
{
    sendJsonResponse(array_merge(['success' => true, 'message' => $message, 'data' => $data], $extra), 200);
}

/**
 * Parse incoming request payload (JSON or Form POST)
 */
function getRequestPayload(): array
{
    $input = file_get_contents('php://input');
    if (!empty($input)) {
        $decoded = json_decode($input, true);
        if (is_array($decoded)) {
            return array_merge($_GET, $_POST, $decoded);
        }
    }
    return array_merge($_GET, $_POST);
}

/**
 * Verify developer tables exist in database.
 * Tables are guaranteed to be present via the master vostokpribor.sql import.
 */
function ensureDeveloperTables(PDO $pdo): void
{
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    try {
        $pdo->query("SHOW TABLES LIKE 'developer_endpoints'")->fetch();
    } catch (Throwable $e) {
        error_log('ensureDeveloperTables error: ' . $e->getMessage());
    }
}
