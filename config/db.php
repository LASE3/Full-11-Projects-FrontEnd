<?php
declare(strict_types=1);

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
    if ($value === false || $value === '') {
        error_log("Startup error: required environment variable '{$key}' is not set.");
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
define('VP_DB_USER', requireEnv('DB_USER'));
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
        // Fallback: allow an explicit, opt-in auto-migration for local/dev setups only.
        // This never runs unless DB_AUTO_MIGRATE=true is explicitly set — it will not
        // silently re-seed a production database on a transient connection error.
        if (optionalEnv('DB_AUTO_MIGRATE', 'false') === 'true') {
            try {
                $rootPdo = new PDO(
                    'mysql:host=' . VP_DB_HOST . ';port=' . VP_DB_PORT . ';charset=utf8mb4',
                    VP_DB_USER,
                    VP_DB_PASS,
                    $options
                );
                $rootPdo->exec(
                    'CREATE DATABASE IF NOT EXISTS `' . VP_DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
                );
                $pdo = new PDO($dsn, VP_DB_USER, VP_DB_PASS, $options);

                $masterSql = __DIR__ . '/../DataBase/vostokpribor_master.sql';
                if (file_exists($masterSql)) {
                    $pdo->exec((string)file_get_contents($masterSql));
                }
            } catch (Throwable $fallbackEx) {
                error_log('Database connection failure: ' . $e->getMessage());
                throw new RuntimeException('Unable to connect to VOSTOKPRIBOR core database.', 0, $e);
            }
        } else {
            error_log('Database connection failure: ' . $e->getMessage());
            throw new RuntimeException('Unable to connect to VOSTOKPRIBOR core database.', 0, $e);
        }
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
    } catch (Throwable $e) {
        error_log('queryUserByCredentials error: ' . $e->getMessage());
    }

    return null;
}

/**
 * Verify a user's password against the stored bcrypt hash.
 *
 * SECURITY: This performs strict hash verification only. There are
 * no hardcoded fallback/demo passwords — every credential must match
 * the stored password_hash via password_verify().
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

    if (!empty($user['clearance_level']) && $user['clearance_level'] === 'L4') {
        return ['authorized' => true, 'reason' => 'Executive L4 unrestricted clearance'];
    }

    if (($user['account_type'] ?? null) === 'Customer') {
        if (in_array($systemId, ['CUS', 'SHP', 'WEB'], true)) {
            return ['authorized' => true, 'reason' => 'Customer portal access granted'];
        }
        return ['authorized' => false, 'reason' => 'Customer accounts are restricted from internal enterprise portals'];
    }

    if (!empty($user['role_id'])) {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare('SELECT access_level FROM role_system_access WHERE role_id = ? AND system_id = ?');
            $stmt->execute([$user['role_id'], $systemId]);
            $access = $stmt->fetchColumn();
            if ($access) {
                return ['authorized' => true, 'reason' => "Authorized via role access ({$access})"];
            }
        } catch (Throwable $e) {
            error_log('checkSystemAuthorization error: ' . $e->getMessage());
        }
    }

    $dept = (string)($user['department_code'] ?? '');
    $allowedByDept = [
        'EXE' => ['ADM', 'CRM', 'CUS', 'DEV', 'EMP', 'DOC', 'FIN', 'HR', 'IT', 'SHP', 'WEB'],
        'SAL' => ['CRM', 'SHP', 'EMP', 'DOC', 'CUS'],
        'ENG' => ['DEV', 'IT', 'EMP', 'DOC', 'WEB'],
        'OPS' => ['SHP', 'EMP', 'DOC', 'FIN'],
        'FIN' => ['FIN', 'ADM', 'EMP', 'DOC', 'CRM'],
        'HR'  => ['HR', 'EMP', 'DOC', 'ADM'],
        'IT'  => ['IT', 'DEV', 'DOC', 'EMP', 'ADM'],
    ];

    if (isset($allowedByDept[$dept]) && in_array($systemId, $allowedByDept[$dept], true)) {
        return ['authorized' => true, 'reason' => "Departmental {$dept} authorization"];
    }

    if (in_array($systemId, ['EMP', 'DOC', 'WEB'], true)) {
        return ['authorized' => true, 'reason' => 'Company-wide employee resource'];
    }

    return [
        'authorized' => false,
        'reason' => "Insufficient clearance ({$user['clearance_level']}) or role permissions for system {$systemId}.",
    ];
}

/**
 * Log an authentication event in the authentication_events table.
 */
function logAuthenticationEvent(
    string $accountType,
    int|string $accountId,
    string $systemId,
    bool $success,
    string $details = ''
): void {
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
    } catch (Throwable $e) {
        error_log('Failed to log authentication event: ' . $e->getMessage());
    }
}

/**
 * Register an active session in the user_sessions table.
 */
function registerUserSession(string $accountType, int|string $accountId, string $systemId): ?string
{
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
        return (string)$pdo->lastInsertId();
    } catch (Throwable $e) {
        error_log('Failed to register user session: ' . $e->getMessage());
        return null;
    }
}

// ---------------------------------------------------------------------
// SSO signing secret — REQUIRED from environment. There is no
// hardcoded default. If VOSTOK_SSO_SECRET is not configured, any
// code path that needs it will fail fast rather than silently signing
// tokens with a secret an attacker could read from source control.
// ---------------------------------------------------------------------
if (!defined('VOSTOK_SSO_SECRET')) {
    define('VOSTOK_SSO_SECRET', requireEnv('VOSTOK_SSO_SECRET'));
}

/**
 * Generate and set a persistent cross-system SSO cookie (root path '/').
 *
 * @param array<string, mixed> $user
 */
function createSsoCookie(array $user): string
{
    $userId = (string)($user['user_id'] ?? $user['emp_id'] ?? '');
    $accId = (string)($user['account_id'] ?? 0);
    $time = time();
    $clearance = (string)($user['clearance_level'] ?? 'L1');

    $payload = "{$userId}|{$accId}|{$clearance}|{$time}";
    $signature = hash_hmac('sha256', $payload, VOSTOK_SSO_SECRET);
    $token = base64_encode("{$payload}|{$signature}");

    if (!headers_sent()) {
        setcookie('vostok_sso_token', $token, [
            'expires'  => time() + (86400 * 30),
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
    return $token;
}

/**
 * Verify a persistent SSO cookie and return the authenticated user record.
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
    if (count($parts) !== 5) {
        return null;
    }

    [$userId, $accId, $clearance, $time, $sig] = $parts;
    if (time() - (int)$time > (86400 * 30)) {
        return null;
    }

    $expectedSig = hash_hmac('sha256', "{$userId}|{$accId}|{$clearance}|{$time}", VOSTOK_SSO_SECRET);
    if (!hash_equals($expectedSig, $sig)) {
        return null;
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
        setcookie('vostok_sso_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}

/**
 * Log an inter-system data integration event into system_integration_logs.
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
        $protocol = 'REST / JSON HTTPS';
        $stmt = $pdo->prepare("
            INSERT INTO system_integration_logs
            (link_code, source_system_id, target_system_id, api_protocol, endpoint, payload_summary, direction, status_code, actor_id, executed_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$linkCode, $source, $target, $protocol, $endpoint, $payloadSummary, $direction, $statusCode, $actor]);
        return true;
    } catch (Throwable $e) {
        error_log('Failed to log integration event: ' . $e->getMessage());
        return false;
    }
}
