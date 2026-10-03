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
    $action = $payload['action'] ?? ($method === 'GET' ? 'list_presets' : 'dispatch');

    switch ($action) {
        case 'list_presets':
            $stmt = $pdo->query("SELECT * FROM `developer_sandbox_presets` ORDER BY `id` ASC");
            $presets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendJsonSuccess($presets, 'Sandbox presets retrieved');
            break;

        case 'create_preset':
            $title = trim($payload['title'] ?? '');
            $url = trim($payload['url'] ?? '');
            $reqMethod = strtoupper(trim($payload['method'] ?? 'GET'));
            $sampleBody = trim($payload['sample_body'] ?? '');
            $description = trim($payload['description'] ?? '');

            if ($title === '' || $url === '') {
                sendJsonError('Title and URL are required');
            }

            $stmt = $pdo->prepare("
                INSERT INTO `developer_sandbox_presets` (`title`, `method`, `url`, `sample_body`, `description`)
                VALUES (:title, :method, :url, :body, :desc)
            ");
            $stmt->execute([
                ':title' => $title,
                ':method' => $reqMethod,
                ':url' => $url,
                ':body' => $sampleBody,
                ':desc' => $description
            ]);

            sendJsonSuccess(['id' => (int)$pdo->lastInsertId()], 'Sandbox preset created successfully in database');
            break;

        case 'update_preset':
            $id = (int)($payload['id'] ?? 0);
            if ($id <= 0) {
                sendJsonError('Preset ID is required');
            }

            $title = trim($payload['title'] ?? '');
            $url = trim($payload['url'] ?? '');
            $reqMethod = strtoupper(trim($payload['method'] ?? 'GET'));
            $sampleBody = trim($payload['sample_body'] ?? '');
            $description = trim($payload['description'] ?? '');

            $stmt = $pdo->prepare("
                UPDATE `developer_sandbox_presets`
                SET `title` = :title, `method` = :method, `url` = :url, `sample_body` = :body, `description` = :desc
                WHERE `id` = :id
            ");
            $stmt->execute([
                ':id' => $id,
                ':title' => $title,
                ':method' => $reqMethod,
                ':url' => $url,
                ':body' => $sampleBody,
                ':desc' => $description
            ]);

            sendJsonSuccess(['id' => $id], 'Sandbox preset updated successfully');
            break;

        case 'delete_preset':
            $id = (int)($payload['id'] ?? 0);
            if ($id <= 0) {
                sendJsonError('Preset ID is required');
            }

            $stmt = $pdo->prepare("DELETE FROM `developer_sandbox_presets` WHERE `id` = :id");
            $stmt->execute([':id' => $id]);
            sendJsonSuccess(['id' => $id], 'Preset removed from database');
            break;

        case 'dispatch':
            $url = trim($payload['url'] ?? '/v1/sensors/optical/telemetry?device_id=PROD-1001-KZ');
            $reqMethod = strtoupper(trim($payload['method'] ?? 'GET'));
            $body = trim($payload['body'] ?? '');
            $startTime = microtime(true);

            // Response resolution logic
            $statusCode = 200;
            $statusText = 'OK';
            $responseData = [];

            if (str_contains($url, '/v1/sensors/optical/telemetry')) {
                parse_str(parse_url($url, PHP_URL_QUERY) ?? '', $queryParams);
                $deviceId = $queryParams['device_id'] ?? 'PROD-1001-KZ';
                $statusCode = 200;
                $statusText = 'OK';
                $responseData = [
                    'device_id' => $deviceId,
                    'sensor_series' => 'Industrial Optical Sensor Package',
                    'calibration_epoch' => time() - 3600,
                    'station' => 'ALMATY-CENTRAL',
                    'telemetry' => [
                        'spectral_resolution_nm' => round(0.04 + (mt_rand(1, 10) / 1000), 4),
                        'focal_plane_temp_c' => round(18.0 + (mt_rand(-10, 10) / 10), 1),
                        'dispersion_coefficient' => 1.0024,
                        'optical_throughput_percent' => round(99.70 + (mt_rand(0, 25) / 100), 2),
                        'snr_db' => round(68.0 + (mt_rand(0, 15) / 10), 1),
                    ],
                    'status' => 'NOMINAL_OPERATIONAL',
                    'jurisdiction_merkle_root' => '0x' . bin2hex(random_bytes(16))
                ];
            } elseif (str_contains($url, '/v1/devices/geodetic/measurements')) {
                parse_str(parse_url($url, PHP_URL_QUERY) ?? '', $queryParams);
                $unit = $queryParams['unit'] ?? 'PROD-1002';
                $statusCode = 200;
                $statusText = 'OK';
                $responseData = [
                    'unit_id' => $unit . '-UST-04',
                    'apparatus' => 'Precision Geodetic Measurement Kit',
                    'laser_interferometer' => 'STABLE',
                    'azimuth_arcsec' => round(142.8800 + (mt_rand(1, 99) / 10000), 4),
                    'zenith_angle_deg' => 44.1029,
                    'distance_vector_meters' => round(1840.4500 + (mt_rand(1, 50) / 10000), 4),
                    'refraction_index' => 1.000277,
                    'calibration_valid' => true
                ];
            } elseif (str_contains($url, '/v1/scada/ingest/frames')) {
                $statusCode = 201;
                $statusText = 'Created';
                $parsedBody = json_decode($body, true) ?: [];
                $facilityId = $parsedBody['facility_id'] ?? 'ALMATY-CENTRAL-01';
                $protocol = $parsedBody['protocol'] ?? 'MODBUS-TCP';
                $responseData = [
                    'frame_ack' => 'ACK-SCADA-' . rand(10000, 99999),
                    'facility_id' => $facilityId,
                    'protocol' => $protocol,
                    'buffered_lines' => 1,
                    'ring_buffer_utilization' => rand(10, 25) . '%',
                    'audit_escrow_timestamp' => time()
                ];
            } elseif (str_contains($url, '/v1/b2b/orders/create')) {
                $statusCode = 200;
                $statusText = 'OK';
                $parsedBody = json_decode($body, true) ?: [];
                $customerId = $parsedBody['customer_id'] ?? 'CUS-1002';
                $items = $parsedBody['items'] ?? [['prod_id' => 'PROD-1001', 'qty' => 4]];
                $responseData = [
                    'order_id' => 'ORD-2026-' . rand(1000, 9999),
                    'customer_id' => $customerId,
                    'customer_name' => 'BaltNord Process Systems',
                    'total_eur' => 240000.00,
                    'items' => $items,
                    'invoice_ref' => 'INV-2026-0' . rand(10, 99),
                    'fulfillment_status' => 'PROCESSING_OPS'
                ];
            } else {
                // Generic handler checking database endpoints
                $statusCode = 200;
                $statusText = 'OK';
                $responseData = [
                    'request_uri' => $url,
                    'method' => $reqMethod,
                    'gateway' => 'developer.vostokpribor.local',
                    'authenticated_as' => 'CUS-1002 (BaltNord Process Systems)',
                    'payload_echo' => json_decode($body, true) ?: $body,
                    'timestamp_utc' => time(),
                    'enclave_receipt' => 'GATEWAY-EXEC-' . bin2hex(random_bytes(8))
                ];
            }

            $elapsedMs = (int)round((microtime(true) - $startTime) * 1000);
            if ($elapsedMs <= 0) {
                $elapsedMs = rand(15, 35);
            }
            $jsonString = json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $sizeBytes = strlen($jsonString) . ' B';

            // Log to developer_sandbox_logs table in database
            try {
                $logStmt = $pdo->prepare("
                    INSERT INTO `developer_sandbox_logs` (`method`, `url`, `request_body`, `status_code`, `response_time_ms`, `response_size`, `response_body`)
                    VALUES (:method, :url, :req_body, :status, :resp_time, :resp_size, :resp_body)
                ");
                $logStmt->execute([
                    ':method' => $reqMethod,
                    ':url' => $url,
                    ':req_body' => $body ?: null,
                    ':status' => $statusCode,
                    ':resp_time' => $elapsedMs,
                    ':resp_size' => $sizeBytes,
                    ':resp_body' => $jsonString
                ]);
            } catch (Throwable $logErr) {
                error_log('Sandbox log insertion error: ' . $logErr->getMessage());
            }

            // Flow J: Every API call writes to api_access_logs
            try {
                $apiLog = $pdo->prepare("
                    INSERT INTO api_access_logs (system_id, endpoint, http_method, source_ip, status_code, response_time_ms, success)
                    VALUES ('DEV', :ep, :method, :ip, :status, :resp_time, :success)
                ");
                $apiLog->execute([
                    ':ep' => $url,
                    ':method' => $reqMethod,
                    ':ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                    ':status' => $statusCode,
                    ':resp_time' => $elapsedMs,
                    ':success' => $statusCode < 400 ? 1 : 0
                ]);
            } catch (Throwable $apiErr) {
                error_log('API access log insertion error: ' . $apiErr->getMessage());
            }

            // Flow J: Sandbox / webhook errors create an IT ticket and emit DEV->IT
            if ($statusCode >= 400 || !empty($payload['simulate_error']) || str_contains($url, 'error')) {
                try {
                    require_once __DIR__ . '/../../includes/enterprise_flows.php';
                    $errTkt = vp_create_ticket($pdo, [
                        'title' => "Gateway / Webhook Failure on endpoint {$url}",
                        'source_system' => 'DEV',
                        'priority' => 'High',
                        'requester_name' => 'Developer Gateway Daemon',
                        'requester_role' => 'API Gateway Subsystem',
                        'category' => 'Infrastructure',
                        'description' => "Webhook/Sandbox dispatch failure on endpoint {$url}. Status code {$statusCode}. Remediation required."
                    ], 'EMP-1020');

                    require_once __DIR__ . '/../../includes/integration_bus.php';
                    vp_emit($pdo, 'DEV_TO_IT', 'DEV', 'IT', 'sandbox_error_dispatched', [
                        'endpoint' => $url,
                        'status_code' => $statusCode,
                        'ticket_id' => $errTkt
                    ], 'EMP-1020');
                } catch (Throwable $tktErr) {
                    error_log('Failed to create IT ticket for sandbox error: ' . $tktErr->getMessage());
                }
            }

            sendJsonSuccess([
                'status' => $statusCode,
                'statusText' => $statusText,
                'time' => $elapsedMs . 'ms',
                'size' => $sizeBytes,
                'data' => $responseData
            ], 'Dispatch completed');
            break;

        case 'list_logs':
            $limit = min(50, max(5, (int)($payload['limit'] ?? 10)));
            $stmt = $pdo->prepare("SELECT * FROM `developer_sandbox_logs` ORDER BY `id` DESC LIMIT :limit");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendJsonSuccess($logs, 'Sandbox logs retrieved');
            break;

        case 'clear_logs':
            $pdo->exec("TRUNCATE TABLE `developer_sandbox_logs`");
            sendJsonSuccess(null, 'Sandbox execution logs cleared');
            break;

        default:
            sendJsonError('Invalid action for sandbox service');
            break;
    }
} catch (Throwable $e) {
    sendJsonError('Database error: ' . $e->getMessage(), 500);
}
