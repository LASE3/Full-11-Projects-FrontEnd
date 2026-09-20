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
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];

    try {
        $pdo = new PDO($dsn, VP_DB_USER, VP_DB_PASS, $options);
    } catch (PDOException $e) {
        // Fallback: Try auto-running migration if database does not exist
        try {
            $rootPdo = new PDO("mysql:host=" . VP_DB_HOST . ";port=" . VP_DB_PORT . ";charset=utf8mb4", VP_DB_USER, VP_DB_PASS, $options);
            $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `" . VP_DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo = new PDO($dsn, VP_DB_USER, VP_DB_PASS, $options);
            // Run seeder
            $migrator = __DIR__ . '/../DataBase/migrate.php';
            if (file_exists($migrator)) {
                require_once $migrator;
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
        $sourceIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $stmt = $pdo->prepare("
            INSERT INTO authentication_events 
            (`account_type`, `employee_account_id`, `customer_account_id`, `system_id`, `event_type`, `source_ip`, `success`, `details`)
            VALUES (?, ?, ?, ?, 'LOGIN_ATTEMPT', ?, ?, ?)
        ");
        $empAccId = ($accountType === 'Employee') ? $accountId : null;
        $cusAccId = ($accountType === 'Customer') ? $accountId : null;
        $stmt->execute([$accountType, $empAccId, $cusAccId, $systemId, $sourceIp, $success ? 1 : 0, $details]);
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
        $sessionId = session_id();
        if (empty($sessionId)) return;

        $sourceIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

        $stmt = $pdo->prepare("
            INSERT INTO user_sessions 
            (`session_id`, `account_type`, `employee_account_id`, `customer_account_id`, `system_id`, `ip_address`, `user_agent`, `status`, `expires_at`)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Active', DATE_ADD(NOW(), INTERVAL 8 HOUR))
            ON DUPLICATE KEY UPDATE `status`='Active', `expires_at`=DATE_ADD(NOW(), INTERVAL 8 HOUR)
        ");
        $empAccId = ($accountType === 'Employee') ? $accountId : null;
        $cusAccId = ($accountType === 'Customer') ? $accountId : null;
        $stmt->execute([$sessionId, $accountType, $empAccId, $cusAccId, $systemId, $sourceIp, $userAgent]);
    } catch (Exception $e) {
        error_log("Failed to register user session: " . $e->getMessage());
    }
}
