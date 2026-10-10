<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('DEV', []);

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
                sendJsonError('Company name, contact name, and engineering email are required', 422);
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

            // Flow J: Create CRM lead tagged source_page='Developer Portal'
            try {
                $stmtLead = $pdo->prepare("
                    INSERT INTO leads (full_name, email, company_name, source_page, status, assigned_sales_emp_id, message, created_at)
                    VALUES (?, ?, ?, 'Developer Portal', 'New', 'EMP-1007', ?, NOW())
                ");
                $stmtLead->execute([$contactName, $contactEmail, $company, "Partner registration: {$ticketId} (Env: {$targetEnv})"]);
                $leadId = (int)$pdo->lastInsertId();

                $stmtAct = $pdo->prepare("
                    INSERT INTO crm_activities (activity_type, title, description, emp_id, created_at)
                    VALUES ('Partner Registration', ?, ?, 'EMP-1020', NOW())
                ");
                $stmtAct->execute(["Developer Portal Registration", "Developer Portal partner clearance requested by {$company} ({$contactName})"]);

                require_once __DIR__ . '/../../includes/integration_bus.php';
                vp_emit($pdo, 'DEV_TO_CRM', 'DEV', 'CRM', 'partner_registered_lead', [
                    'company' => $company,
                    'email'   => $contactEmail,
                    'ticket'  => $ticketId,
                    'lead_id' => $leadId
                ], 'DEV-SYSTEM');
            } catch (Throwable $leadErr) {
                error_log("Failed to create CRM lead from Developer Portal: " . $leadErr->getMessage());
            }

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

            // Fetch existing record if company is blank
            if ($company === '') {
                $currApp = $pdo->query("SELECT company_name, partner_id FROM developer_partner_applications WHERE id = {$id}")->fetch(PDO::FETCH_ASSOC);
                if ($currApp) {
                    $company = $currApp['company_name'] ?? 'Enterprise Partner';
                    $partnerId = $currApp['partner_id'] ?? 'CUS-1002';
                }
            }

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

            $credsData = null;
            if ($status === 'Approved') {
                $partnerCusId = $partnerId ?? 'CUS-1002';
                $stmtP = $pdo->prepare("SELECT partner_id FROM api_partners WHERE partner_name = ? OR cus_id = ? LIMIT 1");
                $stmtP->execute([$company, $partnerCusId]);
                $existingPid = $stmtP->fetchColumn();
                if (!$existingPid) {
                    $insP = $pdo->prepare("INSERT INTO api_partners (cus_id, partner_name, registered_at, status) VALUES (?, ?, NOW(), 'Active')");
                    $insP->execute([$partnerCusId, $company]);
                    $partnerDbId = (int)$pdo->lastInsertId();
                } else {
                    $partnerDbId = (int)$existingPid;
                }

                $rawSecret = 'vp_live_' . bin2hex(random_bytes(20));
                $hashedSecret = password_hash($rawSecret, PASSWORD_BCRYPT);
                $expiresAt = date('Y-m-d H:i:s', strtotime('+1 year'));

                $insCred = $pdo->prepare("
                    INSERT INTO api_credentials (partner_id, api_key_hash, created_at, expires_at, revoked)
                    VALUES (?, ?, NOW(), ?, 0)
                ");
                $insCred->execute([$partnerDbId, $hashedSecret, $expiresAt]);
                $credId = (int)$pdo->lastInsertId();

                $keyIdent = 'VP-KEY-' . rand(1000, 9999);
                $insDevKey = $pdo->prepare("
                    INSERT INTO developer_api_keys (key_identifier, label, partner_id, partner_name, token_prefix, token_full, environment, rate_limit, rate_limit_value, classification, scopes, status, created_at, expires_at)
                    VALUES (?, ?, ?, ?, ?, ?, 'Production', '10,000 req/min', 10000, 'Confidential', 'telemetry:read,scada:ingest', 'Active', NOW(), ?)
                ");
                $insDevKey->execute([
                    $keyIdent,
                    "Production API Key - {$company}",
                    $partnerCusId,
                    $company,
                    substr($rawSecret, 0, 10),
                    $rawSecret,
                    $expiresAt
                ]);

                require_once __DIR__ . '/../../includes/integration_bus.php';
                vp_emit($pdo, 'DEV_TO_CRM', 'DEV', 'CRM', 'partner_approved_credentials', [
                    'partner_id' => $partnerDbId,
                    'company' => $company,
                    'credential_id' => $credId
                ], 'EMP-1020');

                $credsData = [
                    'partner_id' => $partnerDbId,
                    'credential_id' => $credId,
                    'api_key' => $rawSecret, // Shown once
                    'expires_at' => $expiresAt
                ];
            }

            sendJsonSuccess(['id' => $id, 'status' => $status, 'credentials' => $credsData], 'Application status updated in database');
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
