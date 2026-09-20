<?php
/**
 * VOSTOKPRIBOR Auth Guard
 * Include this at the top of protected pages to enforce database authentication.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isAuthenticated = !empty($_SESSION['vostok_authenticated']) && $_SESSION['vostok_authenticated'] === true && !empty($_SESSION['vostok_user']);
$currentUser = $isAuthenticated ? $_SESSION['vostok_user'] : [
    'user_id' => 'EMP-1001',
    'full_name' => 'Viktor Sokolov',
    'role_name' => 'Chief Executive Officer (CEO)',
    'clearance_level' => 'L4',
    'department_code' => 'EXE',
    'email' => 'viktor.sokolov@vostokpribor.local'
];

/**
 * Require valid authentication or redirect to login
 * @param string $systemId Optional system code to check authorization against
 * @param string $loginPath Relative path to login.php
 */
function requireAuth($systemId = '', $loginPath = 'login.php') {
    global $isAuthenticated, $currentUser;
    
    if (!$isAuthenticated) {
        header("Location: " . $loginPath);
        exit;
    }

    if (!empty($systemId) && !empty($currentUser)) {
        require_once __DIR__ . '/../config/db.php';
        $userRecord = queryUserByCredentials($currentUser['user_id']);
        if ($userRecord) {
            $check = checkSystemAuthorization($userRecord, $systemId);
            if (!$check['authorized']) {
                header("Location: " . $loginPath . "?error=" . urlencode("Access Denied: " . $check['reason']));
                exit;
            }
        }
    }
}
