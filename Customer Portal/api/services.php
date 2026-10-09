<?php

declare(strict_types=1);

/**
 * Customer Portal - Industrial Service Requests API
 * Connects Customer Portal (SYS-03) with HR System (SYS-06)
 */

require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('CUS', ['customer' => true]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../includes/integration_service.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = getDbConnection();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Authenticate session
if (empty($_SESSION['vostok_authenticated']) && !isSuperAdmin()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit;
}

require_once __DIR__ . '/helpers/CustomerSession.php';

// Resolve customer ID
$cusId = getActiveCustomerPortalId($pdo);

if ($method === 'GET') {
    $sql = "
        SELECT 
            csr.*,
            e.full_name AS assigned_engineer_name,
            e.job_title AS assigned_engineer_title,
            e.email AS assigned_engineer_email
        FROM customer_service_requests csr
        LEFT JOIN employees e ON csr.assigned_emp_id = e.emp_id
        WHERE csr.cus_id = ?
        ORDER BY csr.created_at DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cusId]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success'  => true,
        'cus_id'   => $cusId,
        'requests' => $requests,
        'count'    => count($requests)
    ]);
    exit;
}

if ($method === 'POST') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true) ?: $_POST;

    $data['cus_id'] = $cusId;
    $res = vostok_createCustomerServiceRequest($pdo, $data);

    if ($res['success']) {
        http_response_code(201);
    } else {
        http_response_code(400);
    }
    echo json_encode($res);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
