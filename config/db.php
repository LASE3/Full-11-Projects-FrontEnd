<?php

declare(strict_types=1);

ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');
if (PHP_SAPI !== 'cli' && !headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}

/**
 * VOSTOKPRIBOR Database Connection & Authorization Service
 * Connects to MySQL/MariaDB database 'vostokpribor' using PDO.
 *
 * SECURITY: All credentials and secrets are loaded strictly from
 * environment variables (see .env.example). There are no hardcoded
 * default credentials, no fallback passwords, and no hardcoded
 * signing secrets in this file. Missing required configuration is
 * treated as a fatal startup error rather than a silent, insecure
 * fallback.
 */

/**
 * Minimal .env loader (no external dependencies).
 * Loads KEY=VALUE pairs from a .env file into the process environment
 * if present. Real environment variables (e.g. set by the host/container)
 * always take precedence and are never overwritten.
 */
function loadEnvFile(string $path): void
{
    if (!is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));

        if ($key === '' || getenv($key) !== false) {
            continue; // real environment variables always win
        }

        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[-1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
    }
}

loadEnvFile(__DIR__ . '/../.env');

/**
 * Fetch a required environment variable or fail hard.
 * Missing required configuration must never fall back to a weak default.
 */
function requireEnv(string $key): string
{
    $value = getenv($key);
    if ($value === false) {
        error_log("Startup error: required environment variable '{$key}' is not set.");
        throw new RuntimeException(
            "Server misconfiguration: required environment variable '{$key}' is not set."
        );
    }
    if ($key !== 'DB_PASS' && trim($value) === '') {
        error_log("Startup error: required environment variable '{$key}' is empty.");
        throw new RuntimeException(
            "Server misconfiguration: required environment variable '{$key}' is not set."
        );
    }
    return $value;
}

/**
 * Fetch a non-sensitive, optional environment variable with a safe default.
 * Only used for values that are not credentials or secrets.
 */
function optionalEnv(string $key, string $default): string
{
    $value = getenv($key);
    return ($value === false || $value === '') ? $default : $value;
}

// ---------------------------------------------------------------------
// Database configuration
// Host/port/database name are non-secret and may use sane defaults.
// Credentials (user/password) are REQUIRED from the environment —
// there is no root/blank fallback.
// ---------------------------------------------------------------------
define('VP_DB_HOST', optionalEnv('DB_HOST', '127.0.0.1'));
define('VP_DB_PORT', optionalEnv('DB_PORT', '3306'));
define('VP_DB_NAME', optionalEnv('DB_NAME', 'vostokpribor'));
$dbUserVal = requireEnv('DB_USER');
$appEnvVal = optionalEnv('APP_ENV', 'local');
if (strtolower(trim($dbUserVal)) === 'root' && strtolower(trim($appEnvVal)) !== 'local') {
    throw new RuntimeException("Security violation: DB_USER=root is prohibited unless APP_ENV=local. Configure least-privilege vostok_app.");
}
define('VP_DB_USER', $dbUserVal);
define('VP_DB_PASS', requireEnv('DB_PASS'));

/**
 * Get or create the active PDO database connection.
 */
function getDbConnection(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . VP_DB_HOST . ';port=' . VP_DB_PORT . ';dbname=' . VP_DB_NAME . ';charset=utf8mb4';
    $mysqlInitAttr = defined('Pdo\\Mysql::ATTR_INIT_COMMAND')
        ? \Pdo\Mysql::ATTR_INIT_COMMAND
        : (defined('PDO::MYSQL_ATTR_INIT_COMMAND') ? PDO::MYSQL_ATTR_INIT_COMMAND : 1002);

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        $mysqlInitAttr => 'SET NAMES utf8mb4',
    ];

    try {
        $pdo = new PDO($dsn, VP_DB_USER, VP_DB_PASS, $options);
    } catch (PDOException $e) {
        error_log('Database connection failure: ' . $e->getMessage());
        throw new RuntimeException('Unable to connect to VOSTOKPRIBOR core database.', 0, $e);
    }

    return $pdo;
}

/**
 * Look up user record across employee_accounts and customer_accounts.
 *
 * @param string $userId Username, Employee ID, Customer ID, or Email
 * @return array<string, mixed>|null
 */
function queryUserByCredentials(string $userId): ?array
{
    $userId = trim($userId);
    if ($userId === '') {
        return null;
    }

    try {
        $pdo = getDbConnection();

        $stmt = $pdo->prepare("
            SELECT
                ea.account_id,
                ea.emp_id,
                ea.username,
                ea.password_hash,
                ea.status AS account_status,
                COALESCE(ea.must_change_password, 0) AS must_change_password,
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
            WHERE LOWER(TRIM(ea.username)) = LOWER(:u1)
               OR LOWER(TRIM(ea.emp_id)) = LOWER(:u2)
               OR LOWER(TRIM(e.email)) = LOWER(:u3)
            ORDER BY (r.role_name = 'SuperAdmin') DESC, ea.account_id ASC
            LIMIT 1
        ");
        $stmt->execute([':u1' => $userId, ':u2' => $userId, ':u3' => $userId]);
        $emp = $stmt->fetch();

        if ($emp) {
            return $emp;
        }

        $stmtCus = $pdo->prepare("
            SELECT
                ca.account_id,
                ca.cus_id,
                ca.username,
                ca.password_hash,
                ca.status AS account_status,
                COALESCE(ca.must_change_password, 0) AS must_change_password,
                c.company_name,
                c.primary_contact_name,
                c.sector,
                ca.email,
                'L1' AS clearance_level,
                'CUS' AS department_code,
                r.role_id,
                r.role_name,
                'Customer' AS account_type
            FROM customer_accounts ca
            JOIN customers c ON ca.cus_id = c.cus_id
            LEFT JOIN roles r ON r.role_name IN ('Customer Client Account', 'Customer Client', 'Customer')
            WHERE LOWER(TRIM(ca.username)) = LOWER(:u1)
               OR LOWER(TRIM(ca.cus_id)) = LOWER(:u2)
               OR LOWER(TRIM(ca.email)) = LOWER(:u3)
            LIMIT 1
        ");
        $stmtCus->execute([':u1' => $userId, ':u2' => $userId, ':u3' => $userId]);
        $cus = $stmtCus->fetch();

        if ($cus) {
            $cus['full_name'] = $cus['primary_contact_name'] . ' (' . $cus['company_name'] . ')';
            $cus['emp_id'] = $cus['cus_id'];
            return $cus;
        }
    } catch (Throwable $e) {
        error_log('queryUserByCredentials error: ' . $e->getMessage());
    }

    return null;
}

/**
 * Verify a user's password against the stored bcrypt hash.
 *
 * SECURITY: Strict hash verification ONLY. No fallbacks allowed.
 * Only password_verify() may succeed.
 *
 * @param array<string, mixed> $user
 */
function verifyUserPassword(array $user, string $password): bool
{
    if ($password === '' || empty($user['password_hash'])) {
        return false;
    }

    return password_verify($password, (string)$user['password_hash']);
}

/**
 * Check if the user is authorized to access the specified system.
 * Uses role_system_access table rather than static departmental arrays.
 *
 * @param array<string, mixed> $user
 * @return array{authorized: bool, reason: string}
 */
function checkSystemAuthorization(array $user, string $systemId): array
{
    $systemId = strtoupper(trim($systemId));
    if ($systemId === '') {
        return ['authorized' => true, 'reason' => 'Global access granted'];
    }

    // Corporate Web Platform (WEB) is public company presentation
    if ($systemId === 'WEB') {
        return ['authorized' => true, 'reason' => 'Public corporate portal'];
    }

    try {
        $pdo = getDbConnection();

        // 1. Check SuperAdmin role in employee_roles
        $empId = $user['emp_id'] ?? ($user['account_type'] === 'Employee' ? ($user['user_id'] ?? null) : null);
        if ($empId) {
            $saStmt = $pdo->prepare("
                SELECT 1 FROM employee_roles er
                JOIN roles r ON er.role_id = r.role_id
                WHERE er.emp_id = ? AND (r.role_name = 'SuperAdmin' OR r.role_name = 'Executive SuperAdmin')
                LIMIT 1
            ");
            $saStmt->execute([$empId]);
            if ($saStmt->fetchColumn()) {
                return ['authorized' => true, 'reason' => 'SuperAdmin unrestricted governance clearance'];
            }
        }

        // 2. Customer accounts check
        if (($user['account_type'] ?? null) === 'Customer') {
            $roleId = $user['role_id'] ?? null;
            if (!$roleId) {
                $roleId = (int)$pdo->query("SELECT role_id FROM roles WHERE role_name IN ('Customer Client Account', 'Customer Client') LIMIT 1")->fetchColumn();
                if (!$roleId) $roleId = 9;
            }
            $stmt = $pdo->prepare('SELECT access_level FROM role_system_access WHERE role_id = ? AND system_id = ?');
            $stmt->execute([$roleId, $systemId]);
            $access = $stmt->fetchColumn();
            if ($access) {
                return ['authorized' => true, 'reason' => "Customer authorized via role access ({$access})"];
            }
            return ['authorized' => false, 'reason' => 'Customer accounts are restricted from internal enterprise portals'];
        }

        // 3. Employee role_system_access check
        if ($empId) {
            $stmt = $pdo->prepare("
                SELECT rsa.access_level, r.role_name
                FROM employee_roles er
                JOIN role_system_access rsa ON er.role_id = rsa.role_id
                JOIN roles r ON er.role_id = r.role_id
                WHERE er.emp_id = ? AND rsa.system_id = ?
                LIMIT 1
            ");
            $stmt->execute([$empId, $systemId]);
            $row = $stmt->fetch();
            if ($row) {
                return ['authorized' => true, 'reason' => "Authorized via role {$row['role_name']} ({$row['access_level']})"];
            }
        } elseif (!empty($user['role_id'])) {
            $stmt = $pdo->prepare('SELECT access_level FROM role_system_access WHERE role_id = ? AND system_id = ?');
            $stmt->execute([$user['role_id'], $systemId]);
            $access = $stmt->fetchColumn();
            if ($access) {
                return ['authorized' => true, 'reason' => "Authorized via role access ({$access})"];
            }
        }
    } catch (Throwable $e) {
        error_log('checkSystemAuthorization error: ' . $e->getMessage());
    }

    return [
        'authorized' => false,
        'reason' => "Insufficient clearance or no role permissions configured for system {$systemId}.",
    ];
}

/**
 * Check if login attempts from identifier or IP exceed safety threshold.
 */
function isLoginRateLimited(string $identifier, string $ip): bool
{
    if (defined('VP_APP_ENV') && VP_APP_ENV === 'test') {
        return false;
    }
    if (getenv('APP_ENV') === 'test') {
        return false;
    }
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM authentication_events
            WHERE success = 0
              AND occurred_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
              AND details LIKE :ident
        ");
        $stmt->execute([
            ':ident' => "%{$identifier}%"
        ]);
        return (int)$stmt->fetchColumn() >= 25;
    } catch (Throwable $e) {
        error_log('Rate limit check error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Log an authentication event in the authentication_events table.
 */
function logAuthenticationEvent(
    string $accountType,
    int|string|null $accountId = null,
    string $systemId = '',
    bool $success = false,
    string $details = '',
    ?string $ip = null
): void {
    try {
        $pdo = getDbConnection();
        $empAccId = ($accountType === 'Employee') ? $accountId : null;
        $cusAccId = ($accountType === 'Customer') ? $accountId : null;
        $eventType = $success ? 'LOGIN_SUCCESS' : 'LOGIN_FAILURE';
        $ip = $ip ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

        $stmt = $pdo->prepare("
            INSERT INTO authentication_events
            (`account_type`, `employee_account_id`, `customer_account_id`, `system_id`, `event_type`, `success`, `ip_address`, `details`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$accountType, $empAccId, $cusAccId, $systemId, $eventType, $success ? 1 : 0, $ip, $details]);
    } catch (Throwable $e) {
        error_log('Failed to log authentication event: ' . $e->getMessage());
    }
}

/**
 * Register an active session in the user_sessions table with jti token tracking.
 */
function registerUserSession(string $accountType, int|string $accountId, string $systemId, ?string $jti = null): ?string
{
    try {
        $pdo = getDbConnection();
        $empAccId = ($accountType === 'Employee') ? $accountId : null;
        $cusAccId = ($accountType === 'Customer') ? $accountId : null;

        $stmt = $pdo->prepare("
            INSERT INTO user_sessions
            (`account_type`, `employee_account_id`, `customer_account_id`, `system_id`, `started_at`, `ended_at`, `status`, `jti`)
            VALUES (?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 8 HOUR), 'Active', ?)
        ");
        $stmt->execute([$accountType, $empAccId, $cusAccId, $systemId, $jti]);
        return (string)$pdo->lastInsertId();
    } catch (Throwable $e) {
        error_log('Failed to register user session: ' . $e->getMessage());
        return null;
    }
}

// ---------------------------------------------------------------------
// SSO signing secret — REQUIRED from environment. Fail fast if < 32 chars.
// ---------------------------------------------------------------------
if (!defined('VOSTOK_SSO_SECRET')) {
    $ssoSecret = requireEnv('VOSTOK_SSO_SECRET');
    if (strlen($ssoSecret) < 32) {
        throw new RuntimeException("Server misconfiguration: VOSTOK_SSO_SECRET must be at least 32 characters long.");
    }
    define('VOSTOK_SSO_SECRET', $ssoSecret);
}

/**
 * Generate and set a persistent cross-system SSO cookie (root path '/', max 8h).
 *
 * @param array<string, mixed> $user
 */
function createSsoCookie(array $user, ?string $jti = null): string
{
    $userId = (string)($user['user_id'] ?? $user['emp_id'] ?? $user['cus_id'] ?? '');
    $accId = (string)($user['account_id'] ?? 0);
    $time = time();
    $clearance = (string)($user['clearance_level'] ?? 'L1');
    if ($jti === null || $jti === '') {
        $jti = bin2hex(random_bytes(16));
    }

    $payload = "{$userId}|{$accId}|{$clearance}|{$time}|{$jti}";
    $signature = hash_hmac('sha256', $payload, VOSTOK_SSO_SECRET);
    $token = base64_encode("{$payload}|{$signature}");

    if (!headers_sent()) {
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        $currentSys = $_SESSION['vostok_current_system'] ?? '';
        $sameSite = in_array($currentSys, ['ADM', 'FIN', 'HR'], true) ? 'Strict' : 'Lax';

        setcookie('vostok_sso_token', $token, [
            'expires'  => time() + 28800, // 8 hours
            'path'     => '/',
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => $sameSite,
        ]);
    }
    return $token;
}

/**
 * Verify a persistent SSO cookie and return the authenticated user record.
 * Validates HMAC, 8-hour lifetime, and active session status via jti.
 *
 * @return array<string, mixed>|null
 */
function verifySsoCookie(?string $token = null): ?array
{
    if ($token === null) {
        $token = $_COOKIE['vostok_sso_token'] ?? '';
    }
    if ($token === '') {
        return null;
    }

    $raw = base64_decode($token, true);
    if (!$raw) {
        return null;
    }

    $parts = explode('|', $raw);
    if (count($parts) === 6) {
        [$userId, $accId, $clearance, $time, $jti, $sig] = $parts;
    } elseif (count($parts) === 5) {
        [$userId, $accId, $clearance, $time, $sig] = $parts;
        $jti = null;
    } else {
        return null;
    }

    // 8-hour maximum lifetime
    if (time() - (int)$time > 28800 || (int)$time > time() + 300) {
        return null;
    }

    $payloadToSign = ($jti !== null)
        ? "{$userId}|{$accId}|{$clearance}|{$time}|{$jti}"
        : "{$userId}|{$accId}|{$clearance}|{$time}";
    $expectedSig = hash_hmac('sha256', $payloadToSign, VOSTOK_SSO_SECRET);
    if (!hash_equals($expectedSig, $sig)) {
        return null;
    }

    // Check jti revocation in user_sessions if present
    if ($jti !== null) {
        try {
            $pdo = getDbConnection();
            $sessStmt = $pdo->prepare("SELECT status, ended_at FROM user_sessions WHERE jti = ? ORDER BY session_id DESC LIMIT 1");
            $sessStmt->execute([$jti]);
            $sess = $sessStmt->fetch();
            if ($sess) {
                if ($sess['status'] !== 'Active') {
                    return null;
                }
                if ($sess['ended_at'] && strtotime((string)$sess['ended_at']) <= time()) {
                    return null;
                }
            }
        } catch (Throwable $e) {
            error_log('SSO jti verification error: ' . $e->getMessage());
        }
    }

    $user = queryUserByCredentials($userId);
    if (!$user) {
        return null;
    }
    if (isset($user['account_status']) && strtolower((string)$user['account_status']) !== 'active') {
        return null;
    }
    if (isset($user['employment_status']) && strtolower((string)$user['employment_status']) !== 'active') {
        return null;
    }

    return $user;
}

/**
 * Clear the persistent SSO cookie.
 */
function clearSsoCookie(): void
{
    if (!headers_sent()) {
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        setcookie('vostok_sso_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}

/**
 * Legacy integration log helper - delegates to vp_emit if available or writes directly.
 */
function logIntegrationEvent(
    string $linkCode,
    string $source,
    string $target,
    string $endpoint,
    string $payloadSummary,
    string $direction,
    int $statusCode = 200,
    string $actor = 'SYSTEM'
): bool {
    try {
        $pdo = getDbConnection();
        require_once __DIR__ . '/../includes/integration_bus.php';
        vp_emit(
            $pdo,
            $linkCode,
            $source,
            $target,
            'INTEGRATION_EVENT',
            [
                'endpoint'        => $endpoint,
                'summary'         => $payloadSummary,
                'direction'       => $direction,
                'api_protocol'    => 'REST / JSON HTTPS'
            ],
            $actor,
            $statusCode
        );
        return true;
    } catch (Throwable $e) {
        error_log('Failed in logIntegrationEvent: ' . $e->getMessage());
        return false;
    }
}

require_once __DIR__ . '/../includes/id_generator.php';
