<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR IT Helpdesk - Knowledge Base API
 * Full CRUD for SOPs, Runbooks & Field Guides
 */

require_once __DIR__ . '/db_helper.php';

$pdo = getItDb();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$payload = getRequestPayload();

// 1. GET: Fetch KB Articles
if ($method === 'GET') {
    $kbId = (int)($payload['kb_id'] ?? 0);

    if ($kbId > 0) {
        $stmt = $pdo->prepare("SELECT * FROM knowledge_base_articles WHERE kb_id = :id");
        $stmt->execute([':id' => $kbId]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$article) {
            sendJsonError("Article not found.", 404);
        }

        // Increment views
        $pdo->prepare("UPDATE knowledge_base_articles SET views_count = views_count + 1 WHERE kb_id = :id")->execute([':id' => $kbId]);

        sendJsonSuccess($article, "Article retrieved.");
    }

    $category = trim((string)($payload['category'] ?? ''));
    $search = trim((string)($payload['search'] ?? ''));
    $where = [];
    $params = [];

    if ($category !== '' && $category !== 'all') {
        $where[] = "category = :cat";
        $params[':cat'] = $category;
    }

    if ($search !== '') {
        $where[] = "(title LIKE :s OR content LIKE :s OR summary LIKE :s OR article_code LIKE :s)";
        $params[':s'] = "%{$search}%";
    }

    $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    $sql = "SELECT * FROM knowledge_base_articles {$whereSql} ORDER BY kb_id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendJsonSuccess($articles, "Loaded " . count($articles) . " KB articles.");
}

// 2. POST: Create, Update, Delete
$action = trim((string)($payload['action'] ?? 'create'));

if ($action === 'create') {
    $title = trim((string)($payload['title'] ?? ''));
    $category = trim((string)($payload['category'] ?? 'General Support'));
    $summary = trim((string)($payload['summary'] ?? ''));
    $content = trim((string)($payload['content'] ?? ''));
    $tags = trim((string)($payload['tags'] ?? ''));
    $code = trim((string)($payload['article_code'] ?? ''));

    if ($title === '') {
        sendJsonError("Article title is required.");
    }

    if ($code === '') {
        $code = "KB-" . rand(1000, 9999);
    }

    $stmt = $pdo->prepare("
        INSERT INTO knowledge_base_articles 
        (article_code, title, summary, content, category, tags, created_by_emp_id, updated_at)
        VALUES 
        (:code, :title, :summary, :content, :cat, :tags, 'EMP-1018', NOW())
    ");
    $stmt->execute([
        ':code'    => $code,
        ':title'   => $title,
        ':summary' => $summary,
        ':content' => $content,
        ':cat'     => $category,
        ':tags'    => $tags,
    ]);

    $id = (int)$pdo->lastInsertId();
    sendJsonSuccess(['kb_id' => $id, 'article_code' => $code], "Knowledge base article [{$code}] published.");
}

if ($action === 'update') {
    $id = (int)($payload['kb_id'] ?? ($payload['article_id'] ?? ($payload['id'] ?? 0)));
    if ($id <= 0) {
        sendJsonError("KB ID is required for update.");
    }

    $title = trim((string)($payload['title'] ?? ''));
    $category = trim((string)($payload['category'] ?? ''));
    $summary = trim((string)($payload['summary'] ?? ''));
    $content = trim((string)($payload['content'] ?? ''));
    $tags = trim((string)($payload['tags'] ?? ''));

    $stmt = $pdo->prepare("
        UPDATE knowledge_base_articles 
        SET title = :title,
            category = :cat,
            summary = :summary,
            content = :content,
            tags = :tags,
            updated_at = NOW()
        WHERE kb_id = :id
    ");
    $stmt->execute([
        ':id'      => $id,
        ':title'   => $title,
        ':cat'     => $category,
        ':summary' => $summary,
        ':content' => $content,
        ':tags'    => $tags,
    ]);

    sendJsonSuccess(['kb_id' => $id], "Article updated successfully.");
}

if ($action === 'delete') {
    $id = (int)($payload['kb_id'] ?? ($payload['article_id'] ?? ($payload['id'] ?? 0)));
    if ($id <= 0) {
        sendJsonError("KB ID is required for delete.");
    }

    $stmt = $pdo->prepare("DELETE FROM knowledge_base_articles WHERE kb_id = :id");
    $stmt->execute([':id' => $id]);

    sendJsonSuccess(['kb_id' => $id], "Article deleted from Knowledge Base.");
}

sendJsonError("Invalid knowledge action: {$action}");
