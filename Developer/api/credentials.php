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
            $stmt = $pdo->query("SELECT * FROM `developer_api_keys` ORDER BY `id` DESC");
            $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Compute live aggregate stats
            $activeCount = 0;
            $prodCount = 0;
            $sandboxCount = 0;
            $aggregatedQuota = 0;

            foreach ($keys as $k) {
                if ($k['status'] === 'Active') {
                    $activeCount++;
                    if ($k['environment'] === 'Production') {
                        $prodCount++;
                    } else {
                        $sandboxCount++;
                    }
                    $aggregatedQuota += (int)($k['rate_limit_value'] ?? 10000);
                }
            }

            $stats = [
                'active_keys_total' => $activeCount,
                'prod_keys' => $prodCount,
                'sandbox_keys' => $sandboxCount,
                'aggregated_quota' => number_format($aggregatedQuota),
                'security_tier' => 'LEVEL 3'
            ];

            sendJsonSuccess(['keys' => $keys, 'stats' => $stats], 'API Keys retrieved successfully');
            break;

        case 'get':
            $id = (int)($payload['id'] ?? 0);
            $stmt = $pdo->prepare("SELECT * FROM `developer_api_keys` WHERE `id` = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $key = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$key) {
                sendJsonError('API Key not found', 404);
            }
            sendJsonSuccess($key, 'API Key retrieved');
            break;

        case 'create':
            $label = trim($payload['label'] ?? '');
            if ($label === '') {
                $label = 'Custom Enterprise Service';
            }
            $env = trim($payload['environment'] ?? 'Production');
            if (!in_array($env, ['Production', 'Sandbox', 'Staging'], true)) {
                $env = 'Production';
            }

            $rateLimitStr = trim($payload['rate_limit'] ?? '10,000');
            $rateLimitClean = (int)preg_replace('/[^0-9]/', '', $rateLimitStr);
            if ($rateLimitClean <= 0) {
                $rateLimitClean = 10000;
            }
            $rateLimit = number_format($rateLimitClean) . ' req/min';

            $scopes = trim($payload['scopes'] ?? 'telemetry:read,scada:ingest');
            $partnerId = trim($payload['partner_id'] ?? 'CUS-1002');
            $partnerName = trim($payload['partner_name'] ?? 'BaltNord Process Systems');
            $classification = ($env === 'Sandbox') ? 'Internal QA' : 'Confidential';

            // Generate unique Key ID and cryptographic token
            $keyIdent = 'KEY-' . rand(1000, 9999);
            $randomHex = bin2hex(random_bytes(16));
            $tokenPrefix = 'vk_' . ($env === 'Sandbox' ? 'test' : 'live') . '_' . substr($randomHex, 0, 8);
            $fullToken = 'vk_' . ($env === 'Sandbox' ? 'test' : 'live') . '_' . $randomHex;

            $stmt = $pdo->prepare("
                INSERT INTO `developer_api_keys`
                (`key_identifier`, `label`, `partner_id`, `partner_name`, `token_prefix`, `token_full`, `environment`, `rate_limit`, `rate_limit_value`, `classification`, `scopes`, `status`, `usage_count`)
                VALUES
                (:key_id, :label, :partner_id, :partner_name, :token_prefix, :token_full, :env, :rate_limit, :rate_val, :classification, :scopes, 'Active', 0)
            ");
            $stmt->execute([
                ':key_id' => $keyIdent,
                ':label' => $label,
                ':partner_id' => $partnerId,
                ':partner_name' => $partnerName,
                ':token_prefix' => $tokenPrefix,
                ':token_full' => $fullToken,
                ':env' => $env,
                ':rate_limit' => $rateLimit,
                ':rate_val' => $rateLimitClean,
                ':classification' => $classification,
                ':scopes' => $scopes
            ]);

            $newId = (int)$pdo->lastInsertId();
            sendJsonSuccess([
                'id' => $newId,
                'key_identifier' => $keyIdent,
                'token_full' => $fullToken,
                'token_prefix' => $tokenPrefix,
                'label' => $label,
                'environment' => $env,
                'rate_limit' => $rateLimit
            ], 'API Key generated and saved to database successfully');
            break;

        case 'update':
            $id = (int)($payload['id'] ?? 0);
            if ($id <= 0) {
                sendJsonError('Valid Key ID is required for update');
            }

            $label = trim($payload['label'] ?? '');
            $env = trim($payload['environment'] ?? 'Production');
            $status = trim($payload['status'] ?? 'Active');
            $scopes = trim($payload['scopes'] ?? 'telemetry:read');
            $rateLimitStr = trim($payload['rate_limit'] ?? '10,000');
            $rateLimitClean = (int)preg_replace('/[^0-9]/', '', $rateLimitStr);
            if ($rateLimitClean <= 0) {
                $rateLimitClean = 10000;
            }
            $rateLimit = number_format($rateLimitClean) . ' req/min';

            $stmt = $pdo->prepare("
                UPDATE `developer_api_keys`
                SET `label` = :label,
                    `environment` = :env,
                    `status` = :status,
                    `scopes` = :scopes,
                    `rate_limit` = :rate_limit,
                    `rate_limit_value` = :rate_val
                WHERE `id` = :id
            ");
            $stmt->execute([
                ':id' => $id,
                ':label' => $label,
                ':env' => $env,
                ':status' => $status,
                ':scopes' => $scopes,
                ':rate_limit' => $rateLimit,
                ':rate_val' => $rateLimitClean
            ]);

            sendJsonSuccess(['id' => $id], 'API Key updated successfully in database');
            break;

        case 'revoke':
            $id = (int)($payload['id'] ?? 0);
            if ($id <= 0) {
                sendJsonError('Key ID is required to revoke');
            }

            $stmt = $pdo->prepare("UPDATE `developer_api_keys` SET `status` = 'Revoked' WHERE `id` = :id");
            $stmt->execute([':id' => $id]);

            sendJsonSuccess(['id' => $id], 'Cryptographic key revoked across all regional gateways');
            break;

        case 'delete':
            $id = (int)($payload['id'] ?? 0);
            if ($id <= 0) {
                sendJsonError('Key ID is required to delete');
            }

            $stmt = $pdo->prepare("DELETE FROM `developer_api_keys` WHERE `id` = :id");
            $stmt->execute([':id' => $id]);

            sendJsonSuccess(['id' => $id], 'API Key permanently removed from database');
            break;

        default:
            sendJsonError('Invalid action for credentials service');
            break;
    }
} catch (Throwable $e) {
    sendJsonError('Database error: ' . $e->getMessage(), 500);
}
