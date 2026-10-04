<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/api_bootstrap.php';
require_once __DIR__ . '/db_helper.php';

try {
    $pdo = getDbConnection();
    ensureDeveloperTables($pdo);

    $payload = getRequestPayload();
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
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
            $stmt = $pdo->query("SELECT * FROM `developer_guides` ORDER BY `section_number` ASC, `id` ASC");
            $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendJsonSuccess($guides, 'Integration guides retrieved');
            break;

        case 'get':
            $id = (int)($payload['id'] ?? 0);
            $stmt = $pdo->prepare("SELECT * FROM `developer_guides` WHERE `id` = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $guide = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$guide) {
                sendJsonError('Guide not found', 404);
            }
            sendJsonSuccess($guide, 'Guide retrieved');
            break;

        case 'create':
            $title = trim($payload['title'] ?? '');
            $category = trim($payload['category'] ?? 'General Standard');
            $classification = trim($payload['classification'] ?? 'Internal Standard');
            $icon = trim($payload['icon'] ?? 'description');
            $summary = trim($payload['summary'] ?? '');
            $snippet = trim($payload['code_snippet'] ?? '');
            $footerNote = trim($payload['footer_note'] ?? '');
            $section = (int)($payload['section_number'] ?? 1);

            if ($title === '' || $summary === '') {
                sendJsonError('Title and summary are required');
            }

            $guideCode = 'DOC-010-' . strtoupper(preg_replace('/[^a-z0-9]+/i', '-', substr($title, 0, 12)));
            $guideCode = trim($guideCode, '-') . '-' . rand(100, 999);

            $stmt = $pdo->prepare("
                INSERT INTO `developer_guides`
                (`guide_code`, `section_number`, `title`, `category`, `classification`, `icon`, `summary`, `code_snippet`, `footer_note`)
                VALUES
                (:code, :sec, :title, :cat, :class, :icon, :summary, :snippet, :footer)
            ");
            $stmt->execute([
                ':code' => $guideCode,
                ':sec' => $section,
                ':title' => $title,
                ':cat' => $category,
                ':class' => $classification,
                ':icon' => $icon,
                ':summary' => $summary,
                ':snippet' => $snippet ?: null,
                ':footer' => $footerNote ?: null
            ]);

            sendJsonSuccess(['id' => (int)$pdo->lastInsertId(), 'code' => $guideCode], 'Guide created successfully in database');
            break;

        case 'update':
            $id = (int)($payload['id'] ?? 0);
            if ($id <= 0) {
                sendJsonError('Guide ID is required');
            }

            $title = trim($payload['title'] ?? '');
            $category = trim($payload['category'] ?? 'General Standard');
            $classification = trim($payload['classification'] ?? 'Internal Standard');
            $icon = trim($payload['icon'] ?? 'description');
            $summary = trim($payload['summary'] ?? '');
            $snippet = trim($payload['code_snippet'] ?? '');
            $footerNote = trim($payload['footer_note'] ?? '');
            $section = (int)($payload['section_number'] ?? 1);

            $stmt = $pdo->prepare("
                UPDATE `developer_guides`
                SET `section_number` = :sec,
                    `title` = :title,
                    `category` = :cat,
                    `classification` = :class,
                    `icon` = :icon,
                    `summary` = :summary,
                    `code_snippet` = :snippet,
                    `footer_note` = :footer
                WHERE `id` = :id
            ");
            $stmt->execute([
                ':id' => $id,
                ':sec' => $section,
                ':title' => $title,
                ':cat' => $category,
                ':class' => $classification,
                ':icon' => $icon,
                ':summary' => $summary,
                ':snippet' => $snippet ?: null,
                ':footer' => $footerNote ?: null
            ]);

            sendJsonSuccess(['id' => $id], 'Guide updated successfully in database');
            break;

        case 'delete':
            $id = (int)($payload['id'] ?? 0);
            if ($id <= 0) {
                sendJsonError('Guide ID is required');
            }

            $stmt = $pdo->prepare("DELETE FROM `developer_guides` WHERE `id` = :id");
            $stmt->execute([':id' => $id]);

            sendJsonSuccess(['id' => $id], 'Guide deleted from database');
            break;

        default:
            sendJsonError('Invalid action for guides service');
            break;
    }
} catch (Throwable $e) {
    sendJsonError('Database error: ' . $e->getMessage(), 500);
}
