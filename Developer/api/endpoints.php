<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/api_bootstrap.php';
require_once __DIR__ . '/db_helper.php';

try {
    $pdo = getDbConnection();
    ensureDeveloperTables($pdo);

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $payload = getRequestPayload();
    $action = $payload['action'] ?? ($method === 'GET' ? 'list' : 'create');

    // GET list/get may stay public; every other action (create, update, delete) requires DEV session with at least L3, or SuperAdmin.
    if ($method !== 'GET' || !in_array($action, ['list', 'get'], true)) {
        $_vp_user = vp_api_guard('DEV', [
            'min_clearance' => 'L3',
            'require_csrf'  => true
        ]);
    }

    switch ($action) {
        case 'list':
            $stmt = $pdo->query("SELECT * FROM `developer_endpoints` ORDER BY `id` ASC");
            $endpoints = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($endpoints as &$ep) {
                if (!empty($ep['parameters_json'])) {
                    $ep['parameters'] = json_decode($ep['parameters_json'], true) ?: [];
                } else {
                    $ep['parameters'] = [];
                }
            }
            unset($ep);
            sendJsonSuccess($endpoints, 'Endpoints retrieved successfully');
            break;

        case 'get':
            $id = (int)($payload['id'] ?? 0);
            $slug = trim($payload['slug'] ?? '');
            if ($id > 0) {
                $stmt = $pdo->prepare("SELECT * FROM `developer_endpoints` WHERE `id` = :id LIMIT 1");
                $stmt->execute([':id' => $id]);
            } else {
                $stmt = $pdo->prepare("SELECT * FROM `developer_endpoints` WHERE `endpoint_slug` = :slug LIMIT 1");
                $stmt->execute([':slug' => $slug]);
            }
            $endpoint = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$endpoint) {
                sendJsonError('Endpoint not found', 404);
            }
            $endpoint['parameters'] = !empty($endpoint['parameters_json']) ? json_decode($endpoint['parameters_json'], true) : [];
            sendJsonSuccess($endpoint, 'Endpoint retrieved');
            break;

        case 'create':
            $title = trim($payload['title'] ?? '');
            $path = trim($payload['path'] ?? '');
            $httpMethod = strtoupper(trim($payload['method'] ?? 'GET'));
            $classification = trim($payload['classification'] ?? 'Internal');
            $rateLimit = trim($payload['rate_limit'] ?? '10k/min');
            $targetHardware = trim($payload['target_hardware'] ?? 'PROD-1001');
            $description = trim($payload['description'] ?? '');

            if ($title === '' || $path === '') {
                sendJsonError('Title and path are required fields');
            }

            // Generate slug if empty
            $slug = trim($payload['endpoint_slug'] ?? '');
            if ($slug === '') {
                $slug = 'endpoint-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower($title));
                $slug = trim($slug, '-');
            }

            // Ensure unique slug
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM `developer_endpoints` WHERE `endpoint_slug` = :slug");
            $stmt->execute([':slug' => $slug]);
            if ($stmt->fetchColumn() > 0) {
                $slug .= '-' . rand(100, 999);
            }

            $paramsJson = $payload['parameters_json'] ?? null;
            if (is_array($paramsJson)) {
                $paramsJson = json_encode($paramsJson, JSON_UNESCAPED_UNICODE);
            }

            // Default snippets if empty
            $curlSnippet = trim($payload['curl_snippet'] ?? '');
            if ($curlSnippet === '') {
                $curlSnippet = "curl -X {$httpMethod} \"https://developer.vostokpribor.local{$path}\" \\\n  -H \"Authorization: Bearer vk_live_9a41c2e8f10b\" \\\n  -H \"Accept: application/json\"";
            }
            $pythonSnippet = trim($payload['python_snippet'] ?? '');
            if ($pythonSnippet === '') {
                $pythonSnippet = "import requests\n\nurl = \"https://developer.vostokpribor.local{$path}\"\nheaders = {\"Authorization\": \"Bearer vk_live_9a41c2e8f10b\", \"Accept\": \"application/json\"}\nresp = requests." . strtolower($httpMethod) . "(url, headers=headers)\nprint(resp.json())";
            }
            $nodeSnippet = trim($payload['node_snippet'] ?? '');
            if ($nodeSnippet === '') {
                $nodeSnippet = "const fetch = require('node-fetch');\n\nasync function callApi() {\n  const res = await fetch('https://developer.vostokpribor.local{$path}', {\n    method: '{$httpMethod}',\n    headers: { 'Authorization': 'Bearer vk_live_9a41c2e8f10b' }\n  });\n  console.log(await res.json());\n}\ncallApi();";
            }
            $goSnippet = trim($payload['go_snippet'] ?? '');
            if ($goSnippet === '') {
                $goSnippet = "package main\n\nimport \"net/http\"\n\nfunc main() {\n  req, _ := http.NewRequest(\"{$httpMethod}\", \"https://developer.vostokpribor.local{$path}\", nil)\n  req.Header.Set(\"Authorization\", \"Bearer vk_live_9a41c2e8f10b\")\n}";
            }

            $stmt = $pdo->prepare("
                INSERT INTO `developer_endpoints` 
                (`endpoint_slug`, `method`, `path`, `title`, `description`, `classification`, `rate_limit`, `target_hardware`, `parameters_json`, `curl_snippet`, `python_snippet`, `node_snippet`, `go_snippet`, `is_active`)
                VALUES
                (:slug, :method, :path, :title, :description, :classification, :rate_limit, :target_hardware, :parameters_json, :curl, :python, :node, :go, 1)
            ");
            $stmt->execute([
                ':slug' => $slug,
                ':method' => $httpMethod,
                ':path' => $path,
                ':title' => $title,
                ':description' => $description,
                ':classification' => $classification,
                ':rate_limit' => $rateLimit,
                ':target_hardware' => $targetHardware,
                ':parameters_json' => $paramsJson,
                ':curl' => $curlSnippet,
                ':python' => $pythonSnippet,
                ':node' => $nodeSnippet,
                ':go' => $goSnippet
            ]);

            $newId = (int)$pdo->lastInsertId();
            sendJsonSuccess(['id' => $newId, 'slug' => $slug], 'API Endpoint created successfully in database');
            break;

        case 'update':
            $id = (int)($payload['id'] ?? 0);
            if ($id <= 0) {
                sendJsonError('Endpoint ID is required for update');
            }

            $title = trim($payload['title'] ?? '');
            $path = trim($payload['path'] ?? '');
            $httpMethod = strtoupper(trim($payload['method'] ?? 'GET'));
            $classification = trim($payload['classification'] ?? 'Internal');
            $rateLimit = trim($payload['rate_limit'] ?? '10k/min');
            $targetHardware = trim($payload['target_hardware'] ?? 'PROD-1001');
            $description = trim($payload['description'] ?? '');

            if ($title === '' || $path === '') {
                sendJsonError('Title and path are required');
            }

            $paramsJson = $payload['parameters_json'] ?? null;
            if (is_array($paramsJson)) {
                $paramsJson = json_encode($paramsJson, JSON_UNESCAPED_UNICODE);
            }

            $curlSnippet = $payload['curl_snippet'] ?? null;
            $pythonSnippet = $payload['python_snippet'] ?? null;
            $nodeSnippet = $payload['node_snippet'] ?? null;
            $goSnippet = $payload['go_snippet'] ?? null;

            $stmt = $pdo->prepare("
                UPDATE `developer_endpoints`
                SET `method` = :method,
                    `path` = :path,
                    `title` = :title,
                    `description` = :description,
                    `classification` = :classification,
                    `rate_limit` = :rate_limit,
                    `target_hardware` = :target_hardware,
                    `parameters_json` = COALESCE(:parameters_json, `parameters_json`),
                    `curl_snippet` = COALESCE(:curl, `curl_snippet`),
                    `python_snippet` = COALESCE(:python, `python_snippet`),
                    `node_snippet` = COALESCE(:node, `node_snippet`),
                    `go_snippet` = COALESCE(:go, `go_snippet`)
                WHERE `id` = :id
            ");
            $stmt->execute([
                ':id' => $id,
                ':method' => $httpMethod,
                ':path' => $path,
                ':title' => $title,
                ':description' => $description,
                ':classification' => $classification,
                ':rate_limit' => $rateLimit,
                ':target_hardware' => $targetHardware,
                ':parameters_json' => $paramsJson,
                ':curl' => $curlSnippet,
                ':python' => $pythonSnippet,
                ':node' => $nodeSnippet,
                ':go' => $goSnippet
            ]);

            sendJsonSuccess(['id' => $id], 'API Endpoint updated successfully in database');
            break;

        case 'delete':
            $id = (int)($payload['id'] ?? 0);
            if ($id <= 0) {
                sendJsonError('Endpoint ID is required for deletion');
            }

            $stmt = $pdo->prepare("DELETE FROM `developer_endpoints` WHERE `id` = :id");
            $stmt->execute([':id' => $id]);

            sendJsonSuccess(['id' => $id], 'Endpoint removed from database');
            break;

        default:
            sendJsonError('Invalid action for endpoints service');
            break;
    }
} catch (Throwable $e) {
    sendJsonError('Database error: ' . $e->getMessage(), 500);
}
