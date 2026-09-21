<?php
/**
 * VOSTOKPRIBOR Database Connection & Authorization Service
 * Connects to MySQL/MariaDB database 'vostokpribor' using PDO.
 */

// Database configuration
define('VP_DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('VP_DB_PORT', getenv('DB_PORT') ?: '3306');
define('VP_DB_NAME', getenv('DB_NAME') ?: 'vostokpribor');
define('VP_DB_USER', getenv('DB_USER') ?: 'root');
define('VP_DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');

/**
 * Get or create the active PDO database connection
 * @return PDO
 */
function getDbConnection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = "mysql:host=" . VP_DB_HOST . ";port=" . VP_DB_PORT . ";dbname=" . VP_DB_NAME . ";charset=utf8mb4";
    $mysqlInitAttr = defined('Pdo\Mysql::ATTR_INIT_COMMAND') ? \Pdo\Mysql::ATTR_INIT_COMMAND : (defined('PDO::MYSQL_ATTR_INIT_COMMAND') ? PDO::MYSQL_ATTR_INIT_COMMAND : 1002);
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        $mysqlInitAttr => "SET NAMES utf8mb4"
    ];

    try {
        $pdo = new PDO($dsn, VP_DB_USER, VP_DB_PASS, $options);
    } catch (PDOException $e) {
        // Fallback: Try auto-running migration if database does not exist
        try {
            $rootPdo = new PDO("mysql:host=" . VP_DB_HOST . ";port=" . VP_DB_PORT . ";charset=utf8mb4", VP_DB_USER, VP_DB_PASS, $options);
            $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `" . VP_DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo = new PDO($dsn, VP_DB_USER, VP_DB_PASS, $options);
            // Run seeder from consolidated master SQL
            $masterSql = __DIR__ . '/../DataBase/vostokpribor_master.sql';
            if (file_exists($masterSql)) {
                $pdo->exec(file_get_contents($masterSql));
            }
        } catch (Exception $fallbackEx) {
            error_log("Database connection failure: " . $e->getMessage());
            throw new Exception("Unable to connect to VOSTOKPRIBOR core database: " . $e->getMessage());
        }
    }

    return $pdo;
}

/**
 * Look up user record across employee_accounts and customer_accounts
 * @param string $userId Username, Employee ID, Customer ID, or Email
 * @return array|null
 */
function queryUserByCredentials($userId) {
    $userId = trim($userId);
    if (empty($userId)) return null;

    try {
        $pdo = getDbConnection();

        // 1. Search employee accounts
        $stmt = $pdo->prepare("
            SELECT 
                ea.account_id,
                ea.emp_id,
                ea.username,
                ea.password_hash,
                ea.status AS account_status,
                e.full_name,
                e.job_title,
                e.department_code,
                e.clearance_level,
                e.email,
                e.employment_status,
                r.role_id,
                r.role_name,
                'Employee' AS account_type
            FROM employee_accounts ea
            JOIN employees e ON ea.emp_id = e.emp_id
            LEFT JOIN employee_roles er ON ea.emp_id = er.emp_id
            LEFT JOIN roles r ON er.role_id = r.role_id
            WHERE ea.username = :u1 
               OR ea.emp_id = :u2 
               OR e.email = :u3
            LIMIT 1
        ");
        $stmt->execute([':u1' => $userId, ':u2' => $userId, ':u3' => $userId]);
        $emp = $stmt->fetch();

        if ($emp) {
            return $emp;
        }

        // 2. Search customer accounts
        $stmtCus = $pdo->prepare("
            SELECT 
                ca.account_id,
                ca.cus_id,
                ca.username,
                ca.password_hash,
                ca.status AS account_status,
                c.company_name,
                c.primary_contact_name,
                c.sector,
                ca.email,
                'L1' AS clearance_level,
                'CUS' AS department_code,
                9 AS role_id,
                'Customer Client' AS role_name,
                'Customer' AS account_type
            FROM customer_accounts ca
            JOIN customers c ON ca.cus_id = c.cus_id
            WHERE ca.username = :u1 
               OR ca.cus_id = :u2 
               OR ca.email = :u3
            LIMIT 1
        ");
        $stmtCus->execute([':u1' => $userId, ':u2' => $userId, ':u3' => $userId]);
        $cus = $stmtCus->fetch();

        if ($cus) {
            $cus['full_name'] = $cus['primary_contact_name'] . ' (' . $cus['company_name'] . ')';
            $cus['emp_id'] = $cus['cus_id'];
            return $cus;
        }

    } catch (Exception $e) {
        error_log("queryUserByCredentials error: " . $e->getMessage());
    }

    return null;
}

/**
 * Verify user password against database hash or standard defaults
 * @param array $user
 * @param string $password
 * @return bool
 */
function verifyUserPassword($user, $password) {
    if (empty($password) || empty($user['password_hash'])) {
        return false;
    }

    // 1. Standard bcrypt verification
    if (password_verify($password, $user['password_hash'])) {
        return true;
    }

    // 2. Fallback for demo / development convenience passwords
    $acceptableFallbacks = [
        'admin1234',
        'AdminPass2026!',
        'Vostok2026!',
        'ClientPass2026!',
        'CustomerPass2026!',
        'EmpPass2026!',
        'SalesPass2026!',
        'HrPass2026!',
        'FinPass2026!',
        'TechPass2026!',
        'DevPass2026!'
    ];

    if (in_array($password, $acceptableFallbacks) || !empty($user['is_fallback'])) {
        return true;
    }

    return false;
}

/**
 * Check if the user is authorized to access the specified system
 * @param array $user
 * @param string $systemId (ADM, CRM, CUS, DEV, EMP, DOC, FIN, HR, IT, SHP, WEB)
 * @return array ['authorized' => bool, 'reason' => string]
 */
function checkSystemAuthorization($user, $systemId) {
    $systemId = strtoupper(trim($systemId));
    if (empty($systemId)) {
        return ['authorized' => true, 'reason' => 'Global access granted'];
    }

    // Clearance L4 (Executive) has full clearance across ALL systems
    if (!empty($user['clearance_level']) && $user['clearance_level'] === 'L4') {
        return ['authorized' => true, 'reason' => 'Executive L4 unrestricted clearance'];
    }

    // Customers can access CUS, SHP, and WEB
    if ($user['account_type'] === 'Customer') {
        if (in_array($systemId, ['CUS', 'SHP', 'WEB'])) {
            return ['authorized' => true, 'reason' => 'Customer portal access granted'];
        }
        return ['authorized' => false, 'reason' => 'Customer accounts are restricted from internal enterprise portals'];
    }

    // Check role_system_access table
    if (!empty($user['role_id'])) {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("SELECT access_level FROM role_system_access WHERE role_id = ? AND system_id = ?");
            $stmt->execute([$user['role_id'], $systemId]);
            $access = $stmt->fetchColumn();
            if ($access) {
                return ['authorized' => true, 'reason' => "Authorized via role access ({$access})"];
            }
        } catch (Exception $e) {
            error_log("checkSystemAuthorization error: " . $e->getMessage());
        }
    }

    // Department-based heuristics
    $dept = $user['department_code'] ?? '';
    $allowedByDept = [
        'EXE' => ['ADM', 'CRM', 'CUS', 'DEV', 'EMP', 'DOC', 'FIN', 'HR', 'IT', 'SHP', 'WEB'],
        'SAL' => ['CRM', 'SHP', 'EMP', 'DOC', 'CUS'],
        'ENG' => ['DEV', 'IT', 'EMP', 'DOC', 'WEB'],
        'OPS' => ['SHP', 'EMP', 'DOC', 'FIN'],
        'FIN' => ['FIN', 'ADM', 'EMP', 'DOC', 'CRM'],
        'HR'  => ['HR', 'EMP', 'DOC', 'ADM'],
        'IT'  => ['IT', 'DEV', 'DOC', 'EMP', 'ADM']
    ];

    if (isset($allowedByDept[$dept]) && in_array($systemId, $allowedByDept[$dept])) {
        return ['authorized' => true, 'reason' => "Departmental {$dept} authorization"];
    }

    // Default: Employee Intranet and File Center are open to all active employees
    if (in_array($systemId, ['EMP', 'DOC', 'WEB'])) {
        return ['authorized' => true, 'reason' => 'Company-wide employee resource'];
    }

    return [
        'authorized' => false, 
        'reason' => "Insufficient clearance ({$user['clearance_level']}) or role permissions for system {$systemId}."
    ];
}

/**
 * Log an authentication event in authentication_events table
 */
function logAuthenticationEvent($accountType, $accountId, $systemId, $success, $details = '') {
    try {
        $pdo = getDbConnection();
        $empAccId = ($accountType === 'Employee') ? $accountId : null;
        $cusAccId = ($accountType === 'Customer') ? $accountId : null;
        $eventType = $success ? 'LOGIN_SUCCESS' : 'LOGIN_FAILURE';

        $stmt = $pdo->prepare("
            INSERT INTO authentication_events 
            (`account_type`, `employee_account_id`, `customer_account_id`, `system_id`, `event_type`, `success`)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$accountType, $empAccId, $cusAccId, $systemId, $eventType, $success ? 1 : 0]);
    } catch (Exception $e) {
        error_log("Failed to log authentication event: " . $e->getMessage());
    }
}

/**
 * Register active session in user_sessions table
 */
function registerUserSession($accountType, $accountId, $systemId) {
    try {
        $pdo = getDbConnection();
        $empAccId = ($accountType === 'Employee') ? $accountId : null;
        $cusAccId = ($accountType === 'Customer') ? $accountId : null;

        $stmt = $pdo->prepare("
            INSERT INTO user_sessions 
            (`account_type`, `employee_account_id`, `customer_account_id`, `system_id`, `started_at`, `ended_at`, `status`)
            VALUES (?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 8 HOUR), 'Active')
        ");
        $stmt->execute([$accountType, $empAccId, $cusAccId, $systemId]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Failed to register user session: " . $e->getMessage());
        return null;
    }
}

if (!defined('VOSTOK_SSO_SECRET')) {
    define('VOSTOK_SSO_SECRET', 'vostok_secret_industrial_token_2026_x7a9');
}

/**
 * Generate and set persistent cross-system SSO cookie (root path '/')
 */
function createSsoCookie($user) {
    $userId = $user['user_id'] ?? $user['emp_id'] ?? '';
    $accId = $user['account_id'] ?? 0;
    $time = time();
    $clearance = $user['clearance_level'] ?? 'L1';

    $payload = "{$userId}|{$accId}|{$clearance}|{$time}";
    $signature = hash_hmac('sha256', $payload, VOSTOK_SSO_SECRET);
    $token = base64_encode("{$payload}|{$signature}");

    if (!headers_sent()) {
        setcookie('vostok_sso_token', $token, [
            'expires'  => time() + (86400 * 30), // 30 days
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    return $token;
}

/**
 * Verify persistent SSO cookie and return authenticated user record
 */
function verifySsoCookie($token = null) {
    if ($token === null) {
        $token = $_COOKIE['vostok_sso_token'] ?? '';
    }
    if (empty($token)) {
        return null;
    }
    $raw = base64_decode($token, true);
    if (!$raw) return null;

    $parts = explode('|', $raw);
    if (count($parts) !== 5) return null;

    list($userId, $accId, $clearance, $time, $sig) = $parts;
    if (time() - (int)$time > (86400 * 30)) {
        return null;
    }

    $expectedSig = hash_hmac('sha256', "{$userId}|{$accId}|{$clearance}|{$time}", VOSTOK_SSO_SECRET);
    if (!hash_equals($expectedSig, $sig)) {
        return null;
    }

    $user = queryUserByCredentials($userId);
    if (!$user) return null;
    if (isset($user['account_status']) && strtolower($user['account_status']) !== 'active') return null;
    if (isset($user['employment_status']) && strtolower($user['employment_status']) !== 'active') return null;

    return $user;
}

/**
 * Clear persistent SSO cookie
 */
function clearSsoCookie() {
    if (!headers_sent()) {
        setcookie('vostok_sso_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
}

/**
 * Log an inter-system data integration event into system_integration_logs
 */
function logIntegrationEvent($linkCode, $source, $target, $endpoint, $payloadSummary, $direction, $statusCode = 200, $actor = 'SYSTEM') {
    try {
        $pdo = getDbConnection();
        $protocol = 'REST / JSON HTTPS';
        $stmt = $pdo->prepare("
            INSERT INTO system_integration_logs 
            (link_code, source_system_id, target_system_id, api_protocol, endpoint, payload_summary, direction, status_code, actor_id, executed_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$linkCode, $source, $target, $protocol, $endpoint, $payloadSummary, $direction, $statusCode, $actor]);
        return true;
    } catch (Exception $e) {
        error_log("Failed to log integration event: " . $e->getMessage());
        return false;
    }
}

