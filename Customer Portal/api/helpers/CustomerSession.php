<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Customer Portal - API Customer Session & Identity Resolver
 */

require_once __DIR__ . '/../../../includes/auth_guard.php';

function getActiveCustomerPortalId(PDO $pdo): string
{
    $user = $_SESSION['vostok_user'] ?? [];
    if (($user['account_type'] ?? '') === 'Customer') {
        $ownCusId = (string)($user['cus_id'] ?? ($user['user_id'] ?? ''));
        if (!empty($ownCusId)) {
            $_SESSION['cus_id'] = $ownCusId;
            return $ownCusId;
        }
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => 'No customer profile bound to session.']);
        exit;
    }

    // Employee impersonation check
    if (!hasEmployeePermission($pdo, $user, 'CUSTOMER_IMPERSONATE')) {
        return '';
    }

    if (!empty($_GET['switch_cus_id']) || !empty($_POST['switch_cus_id'])) {
        $req = trim((string)($_GET['switch_cus_id'] ?? $_POST['switch_cus_id']));
        $chk = $pdo->prepare("SELECT cus_id FROM customers WHERE cus_id = ? LIMIT 1");
        $chk->execute([$req]);
        $valid = $chk->fetchColumn();
        if ($valid) {
            require_once __DIR__ . '/../../../../includes/AuditLogger.php';
            AuditLogger::logAction(
                $user['emp_id'] ?? 'EMP-0001',
                (string)$valid,
                'Customer Portal',
                'CUS',
                'CUSTOMER_IMPERSONATE_SWITCH',
                'customers',
                (string)$valid,
                ['switched_to' => (string)$valid]
            );
            $_SESSION['impersonate_cus_id'] = (string)$valid;
            $_SESSION['cus_id'] = (string)$valid;
            return (string)$valid;
        }
    }

    return (string)($_SESSION['impersonate_cus_id'] ?? '');
}
