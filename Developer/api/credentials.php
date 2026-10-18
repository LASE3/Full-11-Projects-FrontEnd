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
            $stmt = $pdo->query("SELECT * FROM `developer_api_keys` ORDER BY `id` DESC");
            $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($keys as &$k) {
                unset($k['token_full'], $k['token_hash']);
            }
            unset($k);

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
            vp_authorize($_vp_user, 'developer_credential:read', $key, $pdo);
            unset($key['token_full'], $key['token_hash']);
            sendJsonSuccess($key, 'API Key retrieved');
            break;

        case 'create':
            vp_authorize($_vp_user, 'developer_credential:create', $payload, $pdo);
            $label = trim((string)($payload['label'] ?? ''));
            if ($label === '') {
                $label = 'Custom Enterprise Key';
            }
            $partnerId = trim((string)($payload['partner_id'] ?? ''));
            if ($partnerId === '') {
                $partnerId = (string)($_vp_user['partner_id'] ?? $_vp_user['cus_id'] ?? $_vp_user['emp_id'] ?? 'DEV-INTERNAL');
            }
            $env = trim((string)($payload['environment'] ?? 'Production'));
            if (!in_array($env, ['Production', 'Sandbox', 'Staging'], true)) {
                $env = 'Production';
            }

            $rateLimitStr = trim((string)($payload['rate_limit'] ?? '10,000'));
            $rateLimitClean = (int)preg_replace('/[^0-9]/', '', $rateLimitStr);
            if ($rateLimitClean <= 0) {
                $rateLimitClean = 10000;
            }
            $rateLimit = number_format($rateLimitClean) . ' req/min';

            $scopes = trim((string)($payload['scopes'] ?? 'telemetry:read,scada:ingest'));
            $partnerName = trim((string)($payload['partner_name'] ?? 'BaltNord Process Systems'));
            $classification = ($env === 'Sandbox') ? 'Internal QA' : 'Confidential';

            // Generate unique Key ID and cryptographic token
            $keyIdent = 'KEY-' . rand(1000, 9999);
            $randomHex = bin2hex(random_bytes(16));
            $tokenPrefix = 'vk_' . ($env === 'Sandbox' ? 'test' : 'live') . '_' . substr($randomHex, 0, 8);
            $fullToken = 'vk_' . ($env === 'Sandbox' ? 'test' : 'live') . '_' . $randomHex;
            $tokenHash = password_hash($fullToken, PASSWORD_BCRYPT);

            $accId = !empty($_vp_user['account_id']) ? (int)$_vp_user['account_id'] : null;
            $userId = (string)($_vp_user['emp_id'] ?? $_vp_user['cus_id'] ?? $_vp_user['user_id'] ?? '');

            $stmt = $pdo->prepare("
                INSERT INTO `developer_api_keys`
                (`key_identifier`, `label`, `partner_id`, `partner_name`, `created_by_account_id`, `created_by_user_id`, `token_prefix`, `token_hash`, `token_full`, `environment`, `rate_limit`, `rate_limit_value`, `classification`, `scopes`, `status`, `usage_count`)
                VALUES
                (:key_id, :label, :partner_id, :partner_name, :acc_id, :user_id, :token_prefix, :token_hash, NULL, :env, :rate_limit, :rate_val, :classification, :scopes, 'Active', 0)
            ");
            $stmt->execute([
                ':key_id' => $keyIdent,
                ':label' => $label,
                ':partner_id' => $partnerId,
                ':partner_name' => $partnerName,
                ':acc_id' => $accId,
                ':user_id' => $userId ?: null,
                ':token_prefix' => $tokenPrefix,
                ':token_hash' => $tokenHash,
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

            $chk = $pdo->prepare("SELECT * FROM `developer_api_keys` WHERE id = ? LIMIT 1");
            $chk->execute([$id]);
            $key = $chk->fetch(PDO::FETCH_ASSOC);
            if (!$key) {
                sendJsonError('API Key not found', 404);
            }
            vp_authorize($_vp_user, 'developer_credential:update', $key, $pdo);

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
                sendJsonError('Key ID is required to revoke', 422);
            }

            $chk = $pdo->prepare("SELECT * FROM `developer_api_keys` WHERE id = ? LIMIT 1");
            $chk->execute([$id]);
            $key = $chk->fetch(PDO::FETCH_ASSOC);
            if (!$key) {
                sendJsonError('API Key not found', 404);
            }
            vp_authorize($_vp_user, 'developer_credential:revoke', $key, $pdo);

            $stmt = $pdo->prepare("UPDATE `developer_api_keys` SET `status` = 'Revoked' WHERE `id` = :id");
            $stmt->execute([':id' => $id]);

            sendJsonSuccess(['id' => $id], 'Cryptographic key revoked across all regional gateways');
            break;

        case 'delete':
            $id = (int)($payload['id'] ?? 0);
            if ($id <= 0) {
                sendJsonError('Key ID is required to delete', 422);
            }

            $chk = $pdo->prepare("SELECT * FROM `developer_api_keys` WHERE id = ? LIMIT 1");
            $chk->execute([$id]);
            $key = $chk->fetch(PDO::FETCH_ASSOC);
            if (!$key) {
                sendJsonError('API Key not found', 404);
            }
            vp_authorize($_vp_user, 'developer_credential:delete', $key, $pdo);

            $stmt = $pdo->prepare("DELETE FROM `developer_api_keys` WHERE `id` = :id");
            $stmt->execute([':id' => $id]);

            sendJsonSuccess(['id' => $id], 'API Key permanently removed from database');
            break;

        default:
            sendJsonError('Invalid action for credentials service', 422);
            break;
    }
} catch (VpAuthorizationException $e) {
    sendJsonError($e->getMessage(), $e->getCode() ?: 403);
} catch (Throwable $e) {
    error_log("Developer credentials API error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
    sendJsonError('An internal server error occurred while processing credentials.', 500);
}
