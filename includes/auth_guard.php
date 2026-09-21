<?php
/**
 * VOSTOKPRIBOR Centralized Auth Guard & SSO Rehydration Engine
 * Enforces database authentication, restores sessions via persistent SSO cookies,
 * and grants universal bypass to Executive SuperAdmin (L4).
 */

require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    if (!headers_sent()) {
        session_set_cookie_params([
            'lifetime' => 86400 * 30, // 30 days
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax'
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
            'account_id'        => $cookieUser['account_id'],
            'user_id'           => $cookieUser['user_id'],
            'username'          => $cookieUser['username'],
            'full_name'         => $cookieUser['full_name'],
            'email'             => $cookieUser['email'],
            'role_name'         => $cookieUser['role_name'],
            'department_code'   => $cookieUser['department_code'],
            'clearance_level'   => $cookieUser['clearance_level'],
            'account_type'      => $cookieUser['account_type'],
            'authorized_system' => 'ALL',
            'login_time'        => date('Y-m-d H:i:s')
        ];
    }
}

$isAuthenticated = !empty($_SESSION['vostok_authenticated']) && $_SESSION['vostok_authenticated'] === true && !empty($_SESSION['vostok_user']);
$currentUser     = $isAuthenticated ? $_SESSION['vostok_user'] : null;

// Universal L4 bypass removed: Each system strictly requires its own explicit login event.

/**
 * Canonicalize system identifier across various formats
 */
function canonicalSystemCode($sys) {
    $code = strtoupper(trim((string)$sys));
    $map = [
        'ADM' => 'ADM', 'ADMIN' => 'ADM', 'SYS11' => 'ADM', 'SYS-11' => 'ADM', 'SYSTEM11' => 'ADM', 'GOV' => 'ADM', 'ADMIN & GOVERNANCE PORTAL' => 'ADM',
        'CRM' => 'CRM', 'SYS05' => 'CRM', 'SYS-05' => 'CRM', 'SYSTEM05' => 'CRM', 'CRM SYSTEM' => 'CRM',
        'CUS' => 'CUS', 'CUSTOMER' => 'CUS', 'PORTAL' => 'CUS', 'SYS03' => 'CUS', 'SYS-03' => 'CUS', 'SYSTEM03' => 'CUS', 'CUSTOMER PORTAL' => 'CUS',
        'DEV' => 'DEV', 'DEVELOPER' => 'DEV', 'SYS10' => 'DEV', 'SYS-10' => 'DEV', 'SYSTEM10' => 'DEV', 'DEVELOPER PORTAL' => 'DEV',
        'EMP' => 'EMP', 'EMPLOYEE' => 'EMP', 'INTRANET' => 'EMP', 'SYS04' => 'EMP', 'SYS-04' => 'EMP', 'SYSTEM04' => 'EMP', 'EMPLOYEE INTRANET' => 'EMP',
        'DOC' => 'DOC', 'FILE' => 'DOC', 'FILES' => 'DOC', 'FILE CENTER' => 'DOC', 'SYS09' => 'DOC', 'SYS-09' => 'DOC', 'SYSTEM09' => 'DOC',
        'FIN' => 'FIN', 'FINANCE' => 'FIN', 'BILLING' => 'FIN', 'SYS07' => 'FIN', 'SYS-07' => 'FIN', 'SYSTEM07' => 'FIN', 'FINANCE & BILLING' => 'FIN',
        'HR'  => 'HR',  'HR SYSTEM' => 'HR', 'SYS06' => 'HR', 'SYS-06' => 'HR', 'SYSTEM06' => 'HR',
        'IT'  => 'IT',  'HELPDESK' => 'IT', 'IT HELPDESK' => 'IT', 'SYS08' => 'IT', 'SYS-08' => 'IT', 'SYSTEM08' => 'IT',
        'SHP' => 'SHP', 'SHOP' => 'SHP', 'STORE' => 'SHP', 'B2B' => 'SHP', 'SYS02' => 'SHP', 'SYS-02' => 'SHP', 'SYSTEM02' => 'SHP', 'ONLINE SHOP B2B' => 'SHP',
        'WEB' => 'WEB', 'CORP' => 'WEB', 'PLATFORM' => 'WEB', 'SYS01' => 'WEB', 'SYS-01' => 'WEB', 'SYSTEM01' => 'WEB', 'CORPORATE WEB PLATFORM' => 'WEB'
    ];
    return $map[$code] ?? $code;
}

/**
 * Require valid authentication for a specific system or redirect to login.
 * Each of the 11 enterprise systems requires its own active login session.
 * 
 * @param string $systemId System code to verify authorization and active login for
 * @param string $loginPath Relative path to login.php
 */
function requireAuth($systemId = '', $loginPath = 'login.php') {
    global $isAuthenticated, $currentUser;
    
    // Check if session or SSO rehydration exists
    if (!$isAuthenticated || empty($currentUser)) {
        header("Location: " . $loginPath);
        exit;
    }

    if (empty($systemId)) {
        return;
    }

    $canonicalSys = canonicalSystemCode($systemId);

    // Strictly enforce system-specific login session flag.
    // Even SuperAdmin (L4) must log in explicitly on each system's login page.
    if (empty($_SESSION['vostok_system_' . $canonicalSys])) {
        header("Location: " . $loginPath);
        exit;
    }
}
