<?php
declare(strict_types=1);

/**
 * Customer Portal - Account & Profile Management API
 * Location: Customer Portal/api/account.php
 * Methods: GET, POST, PUT/PATCH
 */

require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('CUS', ['customer' => true]);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
// Derive cus_id strictly from the authenticated session — NEVER fall back to a default.
$cusId = $_vp_user['cus_id'] ?? ($_vp_user['user_id'] ?? null);
if (empty($cusId)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Customer session required'], JSON_UNESCAPED_UNICODE);
    exit;
}
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    if ($method === 'GET') {
        $stmt = $pdo->prepare("
            SELECT 
                c.cus_id,
                c.company_name,
                c.sector,
                c.primary_contact_name,
                c.primary_contact_email,
                c.phone,
                c.headquarters,
                c.tax_id,
                c.health_score,
                c.account_tier,
                c.status,
                c.onboarded_at,
                ca.username,
                ca.email AS account_email,
                ca.mfa_enabled,
                ca.last_login,
                e.full_name AS account_manager_name,
                e.email AS account_manager_email,
                e.job_title AS account_manager_title
            FROM customers c
            LEFT JOIN customer_accounts ca ON c.cus_id = ca.cus_id
            LEFT JOIN employees e ON c.account_manager_emp_id = e.emp_id
            WHERE c.cus_id = :cid
            LIMIT 1
        ");
        $stmt->execute([':cid' => $cusId]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$account) {
            Response::error("Customer account not found.", 404);
        }

        Response::success($account, "Customer profile loaded");
    }

    if ($method === 'POST' || $method === 'PUT' || $method === 'PATCH') {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true) ?: $_POST;

        $fields = [];
        $params = [':cid' => $cusId];

        if (isset($data['company_name'])) {
            $fields[] = "company_name = :company_name";
            $params[':company_name'] = trim($data['company_name']);
        }
        if (isset($data['primary_contact_name'])) {
            $fields[] = "primary_contact_name = :primary_contact_name";
            $params[':primary_contact_name'] = trim($data['primary_contact_name']);
        }
        if (isset($data['primary_contact_email'])) {
            $fields[] = "primary_contact_email = :primary_contact_email";
            $params[':primary_contact_email'] = trim($data['primary_contact_email']);
        }
        if (isset($data['phone'])) {
            $fields[] = "phone = :phone";
            $params[':phone'] = trim($data['phone']);
        }
        if (isset($data['headquarters'])) {
            $fields[] = "headquarters = :headquarters";
            $params[':headquarters'] = trim($data['headquarters']);
        }
        if (isset($data['tax_id'])) {
            $fields[] = "tax_id = :tax_id";
            $params[':tax_id'] = trim($data['tax_id']);
        }
        if (isset($data['sector'])) {
            $fields[] = "sector = :sector";
            $params[':sector'] = trim($data['sector']);
        }

        $pdo->beginTransaction();

        if (!empty($fields)) {
            $sql = "UPDATE customers SET " . implode(", ", $fields) . " WHERE cus_id = :cid";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        }

        // Handle password update if provided
        if (!empty($data['new_password'])) {
            $newPassword = $data['new_password'];
            $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
            $pwdStmt = $pdo->prepare("UPDATE customer_accounts SET password_hash = :hash WHERE cus_id = :cid");
            $pwdStmt->execute([':hash' => $hash, ':cid' => $cusId]);
        }

        // Update session user full_name if contact name changed
        if (!empty($data['primary_contact_name'])) {
            $_SESSION['vostok_user']['full_name'] = trim($data['primary_contact_name']);
        }

        $pdo->commit();

        AuditLogger::logSecurityEvent('ACCOUNT_UPDATED', 'CUS', "Customer {$cusId} updated profile details", 'Low', null, $cusId);

        Response::success(['cus_id' => $cusId], "Account settings updated successfully in database");
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    Response::error("Account API Error: " . $e->getMessage(), 500);
}
