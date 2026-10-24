<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Cross-System Integration & Orchestration Service
 * Connects all 11 enterprise front-end systems:
 * 1. Store System (SHP) -> Invoicing System (FIN) -> File Management System (DOC)
 * 2. Customer Portal (CUS) <-> HR System (HR) Service Requests & Technician Dispatch
 * 3. HR System (HR) -> Customer Portal (CUS) Published Job Postings
 * 4. HR System (HR) -> SuperAdmin Privileged Equal Manager Appointments
 * 5. Universal Event Auditing in system_integration_logs
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/AuditLogger.php';
require_once __DIR__ . '/integration_bus.php';

/**
 * Generate official sequential Invoice ID (INV-2026-xxx) via locked counter
 */
/**
 * Generate official sequential Document ID (DOC-2026-xxx) via locked counter
 */
/**
 * Automated Workflow: Store System Order -> Invoicing System Invoice -> File Center Vault Document
 *
 * @param PDO $pdo Active database connection
 * @param int $orderId Order ID from orders table
 * @param string $cusId Customer ID (e.g. CUS-1001)
 * @param float $totalAmount Total monetary value
 * @param array $items Optional array of items [['prod_id' => ..., 'name' => ..., 'qty' => ..., 'price' => ...]]
 * @return array{success: bool, inv_id: string, doc_id: string, file_name: string, file_path: string}
 */
/**
 * Customer Portal -> HR System: Create Customer Service Request
 */
function vostok_createCustomerServiceRequest(PDO $pdo, array $data): array
{
    try {
        $cusId = trim((string)($data['cus_id'] ?? ''));
        if (empty($cusId)) {
            return ['success' => false, 'message' => 'Customer ID is required.'];
        }

        // Generate next request ID (SRV-2026-xxx)
        $reqId = vp_next_id($pdo, 'service_requests', 'SRV-2026-', 3);

        $serviceType = trim((string)($data['service_type'] ?? 'Calibration & Metrology'));
        $title       = trim((string)($data['title'] ?? 'Technical Service Dispatch Request'));
        $description = trim((string)($data['description'] ?? ''));
        $location    = trim((string)($data['facility_location'] ?? 'Customer Industrial Site'));
        $priority    = in_array($data['priority'] ?? '', ['Critical', 'High', 'Standard']) ? $data['priority'] : 'Standard';
        $reqDate     = !empty($data['requested_date']) ? $data['requested_date'] : date('Y-m-d', strtotime('+7 days'));

        $stmt = $pdo->prepare("
            INSERT INTO customer_service_requests
            (request_id, cus_id, service_type, title, description, facility_location, priority, requested_date, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Submitted', NOW())
        ");
        $stmt->execute([$reqId, $cusId, $serviceType, $title, $description, $location, $priority, $reqDate]);

        try {
            vp_emit(
                $pdo,
                'CUS_TO_IT',
                'CUS',
                'IT',
                'SERVICE_REQUEST_SUBMITTED',
                [
                    'request_id'   => $reqId,
                    'cus_id'       => $cusId,
                    'service_type' => $serviceType,
                    'summary'      => "Customer {$cusId} submitted service request {$reqId} ({$serviceType})",
                    'endpoint'     => '/api/service-requests/submit'
                ],
                $cusId,
                201
            );
        } catch (Throwable $e) {
            error_log("Failed to emit CUS_TO_IT: " . $e->getMessage());
        }

        return [
            'success'    => true,
            'request_id' => $reqId,
            'message'    => "Service request {$reqId} submitted and transmitted to HR Operations for engineering dispatch."
        ];
    } catch (Throwable $e) {
        error_log("vostok_createCustomerServiceRequest error: " . $e->getMessage());
        error_log("Operation failure: " . $e->getMessage());
    return ['success' => false, 'message' => 'The operation could not be completed.'];
    }
}

/**
 * HR System -> Assign Engineer/Specialist to Customer Service Request
 */
/**
 * Fetch Published Job Postings for Customer Portal & Corporate Platform
 */
function vostok_getPublishedJobPostings(PDO $pdo): array
{
    try {
        $stmt = $pdo->query("
            SELECT jp.*, d.dept_name
            FROM job_postings jp
            LEFT JOIN departments d ON jp.department_code = d.dept_code
            WHERE jp.is_published = 1
            ORDER BY jp.posting_id DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        error_log("vostok_getPublishedJobPostings error: " . $e->getMessage());
        return [];
    }
}

/**
 * Super-Administrator Privilege: Appoint another manager with equal privileges via HR System
 * Strictly checked: ONLY SuperAdmin (SuperAdmin role in employee_roles) is authorized to invoke!
 */
