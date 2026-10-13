<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR — Password Change Gateway
 * Location: Admin & Governance Portal/ChangePassword.php
 * Note: Delegates to the neutral shared account/change-password.php endpoint.
 *
 * Enforces:
 *  - Minimum 14 characters length rule
 *  - CSRF validation via csrf_token
 *  - Action logging via AuditLogger
 */

require_once __DIR__ . '/../account/change-password.php';
