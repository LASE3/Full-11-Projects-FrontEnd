<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Centralized API Error Handling
 *
 * Ensures VpAuthorizationException maps strictly to 403 or 404, never 500.
 * Logs unexpected exceptions with file and line to PHP error_log.
 * Never leaks database details, SQL, paths, or traces to the HTTP client.
 */

if (!class_exists('VpAuthorizationException', false)) {
    require_once __DIR__ . '/authorize.php';
}

function vp_handle_api_exception(Throwable $e, ?PDO $pdo = null): void
{
    if ($pdo instanceof PDO && $pdo->inTransaction()) {
        try {
            $pdo->rollBack();
        } catch (Throwable) {
            // Transaction might already be rolled back
        }
    }

    if ($e instanceof VpAuthorizationException) {
        $code = $e->getCode();
        if ($code < 400 || $code > 499) {
            $code = 403;
        }
        http_response_code($code);
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode([
            'success' => false,
            'error'   => $e->getMessage(),
            'message' => $e->getMessage(),
            'code'    => $code,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($e instanceof InvalidArgumentException) {
        http_response_code(400);
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode([
            'success' => false,
            'error'   => $e->getMessage(),
            'message' => $e->getMessage(),
            'code'    => 400,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Unexpected internal server errors: log with file and line
    error_log(sprintf('[API Exception] %s: %s in %s on line %d', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));

    http_response_code(500);
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode([
        'success' => false,
        'error'   => 'Internal server error',
        'message' => 'Database or system operation failed. Please contact system support.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Global exception handler for API context
if (PHP_SAPI !== 'cli') {
    set_exception_handler(function (Throwable $e) {
        vp_handle_api_exception($e);
    });
}
