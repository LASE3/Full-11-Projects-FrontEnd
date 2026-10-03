<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Centralized Auth Guard & SSO Rehydration Engine
 * Enforces database authentication, restores sessions via persistent SSO cookies,
 * and validates system access using database role_system_access permissions.
 */

require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    if (!headers_sent()) {
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        $currSys = $_SESSION['vostok_current_system'] ?? '';
        $sameSite = in_array($currSys, ['ADM', 'FIN', 'HR'], true) ? 'Strict' : 'Lax';

        session_set_cookie_params([
            'lifetime' => 28800, // 8 hours
            'path'     => '/',
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => $sameSite,
        ]);
    }
    session_start();
}

// Auto-rehydrate session from persistent SSO cookie if session is missing
if (empty($_SESSION['vostok_authenticated']) || empty($_SESSION['vostok_user'])) {
    $cookieUser = verifySsoCookie();
    if ($cookieUser) {
        $_SESSION['vostok_authenticated'] = true;
        $_SESSION['vostok_user'] = [
            'account_id'        => $cookieUser['account_id'] ?? null,
            'user_id'           => $cookieUser['user_id'] ?? $cookieUser['emp_id'] ?? $cookieUser['cus_id'] ?? '',
            'emp_id'            => $cookieUser['emp_id'] ?? null,
            'cus_id'            => $cookieUser['cus_id'] ?? null,
            'username'          => $cookieUser['username'] ?? '',
            'full_name'         => $cookieUser['full_name'],
            'email'             => $cookieUser['email'],
            'role_name'         => $cookieUser['role_name'] ?? 'Authorized User',
            'role_id'           => $cookieUser['role_id'] ?? null,
            'department_code'   => $cookieUser['department_code'] ?? 'GEN',
            'clearance_level'   => $cookieUser['clearance_level'] ?? 'L1',
            'account_type'      => $cookieUser['account_type'],
            'authorized_system' => 'ALL',
            'login_time'        => date('Y-m-d H:i:s'),
        ];
        if (($cookieUser['account_type'] ?? '') === 'Customer' && !empty($cookieUser['cus_id'])) {
            $_SESSION['cus_id'] = $cookieUser['cus_id'];
        }
    }
}

$isAuthenticated = !empty($_SESSION['vostok_authenticated']) && $_SESSION['vostok_authenticated'] === true && !empty($_SESSION['vostok_user']);
$currentUser     = $isAuthenticated ? $_SESSION['vostok_user'] : null;

/**
 * Canonicalize system identifier across various formats into standard 11 codes:
 * WEB, SHP, CUS, EMP, CRM, HR, FIN, IT, DOC, DEV, ADM
 */
function canonicalSystemCode($sys): string
{
    $code = strtoupper(trim((string)$sys));
    $map = [
        'ADM' => 'ADM', 'ADMIN' => 'ADM', 'SYS11' => 'ADM', 'SYS-11' => 'ADM', 'SYSTEM11' => 'ADM', 'GOV' => 'ADM', 'ADMIN & GOVERNANCE PORTAL' => 'ADM',
        'CRM' => 'CRM', 'SYS05' => 'CRM', 'SYS-05' => 'CRM', 'SYSTEM05' => 'CRM', 'CRM SYSTEM' => 'CRM',
        'CUS' => 'CUS', 'CUSTOMER' => 'CUS', 'PORTAL' => 'CUS', 'SYS03' => 'CUS', 'SYS-03' => 'CUS', 'SYSTEM03' => 'CUS', 'CUSTOMER PORTAL' => 'CUS',
        'DEV' => 'DEV', 'DEVELOPER' => 'DEV', 'SYS10' => 'DEV', 'SYS-10' => 'DEV', 'SYSTEM10' => 'DEV', 'DEVELOPER PORTAL' => 'DEV',
        'EMP' => 'EMP', 'EMPLOYEE' => 'EMP', 'INTRANET' => 'EMP', 'SYS04' => 'EMP', 'SYS-04' => 'EMP', 'SYSTEM04' => 'EMP', 'EMPLOYEE INTRANET' => 'EMP',
        'DOC' => 'DOC', 'FILE' => 'DOC', 'FILES' => 'DOC', 'FILE CENTER' => 'DOC', 'FILECENTER' => 'DOC', 'FILE_CENTER' => 'DOC', 'SYS06' => 'DOC', 'SYS-06' => 'DOC', 'SYSTEM06' => 'DOC', 'SYS09' => 'DOC', 'SYS-09' => 'DOC', 'SYSTEM09' => 'DOC',
        'FIN' => 'FIN', 'FINANCE' => 'FIN', 'BILLING' => 'FIN', 'SYS07' => 'FIN', 'SYS-07' => 'FIN', 'SYSTEM07' => 'FIN', 'FINANCE & BILLING' => 'FIN',
        'HR'  => 'HR',  'HR SYSTEM' => 'HR',
        'IT'  => 'IT',  'HELPDESK' => 'IT', 'IT HELPDESK' => 'IT', 'SYS08' => 'IT', 'SYS-08' => 'IT', 'SYSTEM08' => 'IT',
        'SHP' => 'SHP', 'SHOP' => 'SHP', 'STORE' => 'SHP', 'B2B' => 'SHP', 'SYS02' => 'SHP', 'SYS-02' => 'SHP', 'SYSTEM02' => 'SHP', 'ONLINE SHOP B2B' => 'SHP',
        'WEB' => 'WEB', 'CORP' => 'WEB', 'PLATFORM' => 'WEB', 'SYS01' => 'WEB', 'SYS-01' => 'WEB', 'SYSTEM01' => 'WEB', 'CORPORATE WEB PLATFORM' => 'WEB'
    ];
    return $map[$code] ?? $code;
}

/**
 * Require valid authentication for a specific system or redirect to login.
 */
function requireAuth(string $systemId = '', string $loginPath = 'login.php'): void
{
    global $isAuthenticated, $currentUser;

    if (empty($systemId)) {
        return;
    }

    $canonicalSys = canonicalSystemCode($systemId);

    // Corporate Web Platform (WEB) is public company presentation
    if ($canonicalSys === 'WEB') {
        return;
    }

    if (!$isAuthenticated || empty($currentUser)) {
        header("Location: " . $loginPath);
        exit;
    }

    // Role system access authorization check
    $check = checkSystemAuthorization($currentUser, $canonicalSys);
    if (!$check['authorized']) {
        header("Location: " . $loginPath . "?error=" . urlencode($check['reason']));
        exit;
    }
}

/**
 * Require authentication for API endpoints, returning 401/403 JSON responses.
 *
 * @param string|null $requiredSystem System code if specific authorization is required
 * @return array<string, mixed> The authenticated user
 */
function requireApiAuth(?string $requiredSystem = null): array
{
    global $currentUser, $isAuthenticated;

    if (!$isAuthenticated || empty($currentUser)) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'error'   => 'Authentication required',
            'message' => 'Unauthorized: Please log in.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($requiredSystem !== null) {
        $canonical = canonicalSystemCode($requiredSystem);
        $check = checkSystemAuthorization($currentUser, $canonical);
        if (!$check['authorized']) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'error'   => 'Forbidden',
                'message' => 'Forbidden: ' . $check['reason']
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    return $currentUser;
}

/**
 * Enforce tenant isolation for customer accounts.
 * Returns the customer ID to use for queries.
 *
 * @param array<string, mixed> $user The authenticated user
 * @param string|null $requestedCusId Optional cus_id requested in query/payload
 * @return string Verified customer ID
 */
function enforceCustomerTenant(array $user, ?string $requestedCusId = null): string
{
    if (($user['account_type'] ?? '') === 'Customer') {
        $ownCusId = $user['cus_id'] ?? $user['user_id'] ?? '';
        if ($requestedCusId !== null && $requestedCusId !== '' && $requestedCusId !== $ownCusId) {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'error'   => 'Tenant isolation violation',
                'message' => 'Forbidden: Cross-tenant data access is strictly prohibited.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        return $ownCusId;
    }

    // For employee accounts, allow requested customer or default
    return $requestedCusId ?: ($user['cus_id'] ?? 'CUS-1001');
}

/**
 * Check if the user has the SuperAdmin role in employee_roles.
 * Strictly checks the SuperAdmin role — NEVER an email address.
 */
function isSuperAdmin(?array $user = null): bool
{
    if ($user === null) {
        $user = $_SESSION['vostok_user'] ?? null;
    }
    if (!$user || !is_array($user)) {
        return false;
    }

    $empId = $user['emp_id'] ?? ($user['account_type'] === 'Employee' ? ($user['user_id'] ?? null) : null);
    if ($empId) {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("
                SELECT 1 FROM employee_roles er
                JOIN roles r ON er.role_id = r.role_id
                WHERE er.emp_id = ? AND (r.role_name = 'SuperAdmin' OR r.role_name = 'Executive SuperAdmin')
                LIMIT 1
            ");
            $stmt->execute([$empId]);
            if ($stmt->fetchColumn()) {
                return true;
            }
        } catch (Throwable) {
            // fallback
        }
    }

    $role = (string)($user['role_name'] ?? '');
    return (strcasecmp($role, 'SuperAdmin') === 0 || strcasecmp($role, 'Executive SuperAdmin') === 0);
}

/**
 * Generate CSRF token in current session.
 */
function getCsrfToken(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token from POST body or header.
 */
function verifyCsrfToken(?string $token = null): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if ($token === null) {
            $raw = (string)file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            $token = $decoded['csrf_token'] ?? null;
        }
    }
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], (string)$token);
}

/**
 * Require SuperAdmin (L4/role=1) or redirect.
 * Used by pages like SuperAdminConsole.php that must never be accessed by non-SuperAdmins.
 */
function requireSuperAdmin(string $systemId = 'ADM', string $loginPath = 'login.php'): void
{
    global $isAuthenticated, $currentUser;

    requireAuth($systemId, $loginPath);

    if (!isSuperAdmin($currentUser)) {
        http_response_code(403);
        echo '<h1>403 Forbidden</h1><p>SuperAdmin clearance (L4) required for this page.</p>';
        exit;
    }
}

/**
 * Enforce tenant isolation for customer accounts (alias for enforceCustomerTenant).
 * Throws a RuntimeException if the requested cus_id does not match the authenticated customer.
 *
 * @param string $requestedCusId   The cus_id being requested
 * @param array<string,mixed> $user The authenticated user
 */
function enforceTenantIsolation(string $requestedCusId, array $user): void
{
    if (($user['account_type'] ?? '') === 'Customer') {
        $ownCusId = $user['cus_id'] ?? $user['user_id'] ?? '';
        if ($requestedCusId !== '' && $requestedCusId !== $ownCusId) {
            throw new \RuntimeException(
                "Tenant isolation violation: {$user['account_type']} {$ownCusId} attempted to access {$requestedCusId}"
            );
        }
    }
}

// Note: verifyUserPassword() is defined in config/db.php and available globally.


