<?php

declare(strict_types=1);

require_once __DIR__ . '/db_helper.php';

try {
    $pdo = getDbConnection();
    ensureDeveloperTables($pdo);

    $payload = getRequestPayload();
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $action = $payload['action'] ?? ($method === 'GET' ? 'list' : 'create');

    switch ($action) {
        case 'list':
            $stmt = $pdo->query("SELECT * FROM `developer_partner_applications` ORDER BY `id` DESC");
            $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendJsonSuccess($apps, 'Partner clearance applications retrieved');
            break;

        case 'get':
            $id = (int)($payload['id'] ?? 0);
            $stmt = $pdo->prepare("SELECT * FROM `developer_partner_applications` WHERE `id` = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $app = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$app) {
                sendJsonError('Application not found', 404);
            }
            sendJsonSuccess($app, 'Application retrieved');
            break;

        case 'create':
            $company = trim($payload['company_name'] ?? '');
            $partnerId = trim($payload['partner_id'] ?? '');
            $contactName = trim($payload['contact_name'] ?? '');
            $contactEmail = trim($payload['contact_email'] ?? '');
            $projectRef = trim($payload['project_ref'] ?? '');
            $targetEnv = trim($payload['target_environment'] ?? 'sandbox');
            $publicKey = trim($payload['public_key'] ?? '');

            $scopes = $payload['requested_scopes'] ?? 'telemetry:read, orders:read_write';
            if (is_array($scopes)) {
                $scopes = implode(', ', $scopes);
            }

            $compDoc = !empty($payload['compliance_doc']) ? 1 : 0;
            $compIec = !empty($payload['compliance_iec']) ? 1 : 0;
            $compNda = !empty($payload['compliance_nda']) ? 1 : 0;

            if ($company === '' || $contactName === '' || $contactEmail === '') {
                sendJsonError('Company name, contact name, and engineering email are required');
            }

            $ticketId = 'ENCLAVE-REQ-' . rand(100000, 999999);

            $stmt = $pdo->prepare("
                INSERT INTO `developer_partner_applications`
                (`ticket_id`, `company_name`, `partner_id`, `contact_name`, `contact_email`, `project_ref`, `target_environment`, `requested_scopes`, `public_key`, `compliance_doc`, `compliance_iec`, `compliance_nda`, `status`, `assigned_engineer`, `created_at`)
                VALUES
                (:ticket, :company, :partner, :contact, :email, :proj, :env, :scopes, :key, :cdoc, :ciec, :cnda, 'In Review', 'Jonas Richter (EMP-1020)', NOW())
            ");
            $stmt->execute([
                ':ticket' => $ticketId,
                ':company' => $company,
                ':partner' => $partnerId ?: null,
                ':contact' => $contactName,
                ':email' => $contactEmail,
                ':proj' => $projectRef ?: null,
                ':env' => $targetEnv,
                ':scopes' => $scopes,
                ':key' => $publicKey ?: null,
                ':cdoc' => $compDoc,
                ':ciec' => $compIec,
                ':cnda' => $compNda
            ]);

            $newId = (int)$pdo->lastInsertId();
            sendJsonSuccess([
                'id' => $newId,
                'ticket_id' => $ticketId,
                'company_name' => $company,
                'contact_name' => $contactName,
                'contact_email' => $contactEmail,
                'target_environment' => $targetEnv,
                'status' => 'In Review'
            ], 'Partner registration application successfully saved in database');
            break;

        case 'update':
            $id = (int)($payload['id'] ?? 0);
            if ($id <= 0) {
                sendJsonError('Valid Application ID is required');
            }

            $status = trim($payload['status'] ?? 'In Review');
            $assigned = trim($payload['assigned_engineer'] ?? 'Jonas Richter (EMP-1020)');
            $company = trim($payload['company_name'] ?? '');
            $contact = trim($payload['contact_name'] ?? '');
            $email = trim($payload['contact_email'] ?? '');

            $stmt = $pdo->prepare("
                UPDATE `developer_partner_applications`
                SET `status` = :status,
                    `assigned_engineer` = :assigned,
                    `company_name` = COALESCE(NULLIF(:company, ''), `company_name`),
                    `contact_name` = COALESCE(NULLIF(:contact, ''), `contact_name`),
                    `contact_email` = COALESCE(NULLIF(:email, ''), `contact_email`)
                WHERE `id` = :id
            ");
            $stmt->execute([
                ':id' => $id,
                ':status' => $status,
                ':assigned' => $assigned,
                ':company' => $company,
                ':contact' => $contact,
                ':email' => $email
            ]);

            sendJsonSuccess(['id' => $id, 'status' => $status], 'Application status updated in database');
            break;

        case 'delete':
            $id = (int)($payload['id'] ?? 0);
            if ($id <= 0) {
                sendJsonError('Valid Application ID is required');
            }

            $stmt = $pdo->prepare("DELETE FROM `developer_partner_applications` WHERE `id` = :id");
            $stmt->execute([':id' => $id]);

            sendJsonSuccess(['id' => $id], 'Application record deleted from database');
            break;

        default:
            sendJsonError('Invalid action for partners service');
            break;
    }
} catch (Throwable $e) {
    sendJsonError('Database error: ' . $e->getMessage(), 500);
}
