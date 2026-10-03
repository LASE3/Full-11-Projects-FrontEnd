<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR API Bootstrap — Central Auth Guard
 *
 * Provides vp_api_guard() which MUST be called at the very top of every
 * protected API file. Validates:
 *  - Active session or valid SSO cookie with active jti
 *  - Employee account_status = 'Active'
 *  - System authorization (role_system_access)
 *  - Minimum clearance level
 *  - Role requirement (e.g. SuperAdmin, L4)
 *  - CSRF token for state-changing methods (POST/PUT/PATCH/DELETE)
 *
 * Public endpoints (auth.php, logout.php, check_auth.php, contact.php,
 * careers.php) do NOT call this function.
 */

if (!function_exists('getDbConnection')) {
    require_once __DIR__ . '/../config/db.php';
}
if (!function_exists('requireApiAuth')) {
    require_once __DIR__ . '/../includes/auth_guard.php';
}

/**
 * Clearance level numeric values for comparison.
 */
function vp_clearance_level(string $level): int
{
    return match (strtoupper(trim($level))) {
        'L1'    => 1,
        'L2'    => 2,
        'L3'    => 3,
        'L4'    => 4,
        default => 0,
    };
}

/**
 * Central API guard. Call at the top of every protected API file.
 *
 * @param string $system   One of: WEB SHP CUS EMP CRM HR FIN IT DOC DEV ADM
 * @param array  $opts     Options:
 *   'min_clearance' => 'L3'          Minimum clearance level required
 *   'require_role'  => 'SuperAdmin'  Specific role required (any role in list)
 *   'require_roles' => ['SuperAdmin','FinanceAdmin']  Any of these roles
 *   'csrf'          => false         Set to false to skip CSRF (GET endpoints)
 *   'customer'      => true          Require Customer account type
 *   'employee'      => true          Require Employee account type
 *
 * @return array The authenticated user array (from session)
 */
function vp_api_guard(string $system, array $opts = []): array
{
    // Always set JSON content type for API responses
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }

    // Step 1: Require valid authentication
    $user = requireApiAuth($system);

    // Step 2: Account type enforcement
    if (!empty($opts['customer']) && ($user['account_type'] ?? '') !== 'Customer') {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Customer session required'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if (!empty($opts['employee']) && ($user['account_type'] ?? '') !== 'Employee') {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Employee session required'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Step 3: SuperAdmin short-circuit — bypasses all clearance/role checks
    if (isSuperAdmin($user)) {
        // Still enforce CSRF for SuperAdmin on mutating requests
        vp_enforce_csrf($opts);
        return $user;
    }

    // Step 4: Clearance level enforcement
    if (!empty($opts['min_clearance'])) {
        $userLevel   = vp_clearance_level($user['clearance_level'] ?? 'L1');
        $required    = vp_clearance_level($opts['min_clearance']);
        if ($userLevel < $required) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error'   => 'Forbidden',
                'message' => "Minimum clearance {$opts['min_clearance']} required for this action.",
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // Step 5: Role enforcement
    $requiredRoles = [];
    if (!empty($opts['require_role'])) {
        $requiredRoles[] = $opts['require_role'];
    }
    if (!empty($opts['require_roles'])) {
        $requiredRoles = array_merge($requiredRoles, (array)$opts['require_roles']);
    }

    if (!empty($requiredRoles)) {
        $empId = $user['emp_id'] ?? null;
        $hasRole = false;

        if ($empId) {
            try {
                $pdo  = getDbConnection();
                $in   = implode(',', array_fill(0, count($requiredRoles), '?'));
                $stmt = $pdo->prepare("
                    SELECT 1 FROM employee_roles er
                    JOIN roles r ON er.role_id = r.role_id
                    WHERE er.emp_id = ? AND r.role_name IN ({$in})
                    LIMIT 1
                ");
                $stmt->execute(array_merge([$empId], $requiredRoles));
                $hasRole = (bool)$stmt->fetchColumn();
            } catch (Throwable $e) {
                error_log('vp_api_guard role check error: ' . $e->getMessage());
            }
        }

        // Also allow if role_name in session matches
        if (!$hasRole) {
            $sessionRole = $user['role_name'] ?? '';
            $hasRole = in_array($sessionRole, $requiredRoles, true);
        }

        if (!$hasRole) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'error'   => 'Forbidden',
                'message' => 'Insufficient role permissions for this action.',
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // Step 6: CSRF enforcement for mutating methods
    vp_enforce_csrf($opts);

    return $user;
}

/**
 * Enforce CSRF token for state-changing HTTP methods.
 * Skipped when $opts['csrf'] === false explicitly.
 * Exempt methods: GET, HEAD, OPTIONS.
 */
function vp_enforce_csrf(array $opts): void
{
    // Allow explicit opt-out (e.g. for pure GET endpoints)
    if (isset($opts['csrf']) && $opts['csrf'] === false) {
        return;
    }

    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
        return;
    }

    if (!verifyCsrfToken()) {
        http_response_code(403);
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode([
            'success' => false,
            'error'   => 'CSRF token missing or invalid',
            'message' => 'Include X-CSRF-Token header or csrf_token field in your request.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * Require a valid customer session (for Customer Portal API endpoints).
 * Returns the authenticated customer ID.
 * Never falls back to a default customer.
 */
function vp_require_customer_session(): string
{
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $cusId = $_SESSION['cus_id'] ?? null;

    if (empty($cusId)) {
        // Also try vostok_user session
        $user = $_SESSION['vostok_user'] ?? null;
        if ($user && ($user['account_type'] ?? '') === 'Customer') {
            $cusId = $user['cus_id'] ?? $user['user_id'] ?? null;
        }
    }

    if (empty($cusId)) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error'   => 'Authentication required',
            'message' => 'Customer session required. Please log in.',
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    return (string)$cusId;
}
