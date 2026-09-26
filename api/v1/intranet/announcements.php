<?php

/**
 * Class 4: Employee Intranet - Announcements API
 * Location: api/v1/intranet/announcements.php
 * Methods: GET, POST
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/I18n.php';
require_once __DIR__ . '/../../helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$empId   = $_SESSION['emp_id'] ?? ($_GET['emp_id'] ?? 'EMP-1004');
$empDept = $_SESSION['department_code'] ?? ($_GET['dept'] ?? 'ENG');
$lang    = $_GET['lang'] ?? 'en';
$method  = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                a.announcement_id,
                a.title,
                a.body,
                a.posted_at,
                a.audience_dept,
                e.full_name AS posted_by_name,
                e.job_title AS posted_by_role,
                COALESCE(d.dept_name, 'All Enterprise Personnel') AS target_department_name
            FROM announcements a
            JOIN employees e ON a.posted_by_emp_id = e.emp_id
            LEFT JOIN departments d ON a.audience_dept = d.dept_code
            WHERE a.audience_dept IS NULL OR a.audience_dept = :dept
            ORDER BY a.posted_at DESC
        ");
        $stmt->execute([':dept' => $empDept]);
        $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($announcements as &$item) {
            if ($item['audience_dept']) {
                $item['target_department_display'] = I18n::translate($item['audience_dept'], $lang);
            } else {
                $item['target_department_display'] = $lang === 'ar' ? 'كافة موظفي المؤسسة' : 'All Enterprise Personnel';
            }
        }

        Response::success($announcements, "Intranet announcements loaded");
    } catch (Exception $e) {
        Response::error("Failed to load announcements: " . $e->getMessage(), 500);
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $title = trim($data['title'] ?? '');
    $body  = trim($data['body'] ?? '');
    $targetDept = !empty($data['audience_dept']) ? trim($data['audience_dept']) : null;
    $authorEmp = $empId ?: ($data['posted_by_emp_id'] ?? 'EMP-1001');

    if (empty($title) || empty($body)) {
        Response::error("Title and announcement body are required.", 422);
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO announcements (title, body, posted_by_emp_id, audience_dept, posted_at)
            VALUES (:title, :body, :emp, :dept, NOW())
        ");
        $stmt->execute([
            ':title' => $title,
            ':body'  => $body,
            ':emp'   => $authorEmp,
            ':dept'  => $targetDept
        ]);
        $newId = $pdo->lastInsertId();

        AuditLogger::logAction(
            $authorEmp,
            null,
            'Employee Intranet',
            'EMP',
            'PUBLISH_CORPORATE_ANNOUNCEMENT',
            'announcements',
            (string)$newId,
            ['title' => $title, 'audience_dept' => $targetDept],
            'SUCCESS'
        );

        Response::success(['announcement_id' => (int)$newId], "Announcement published successfully", 201);
    } catch (Exception $e) {
        Response::error("Failed to post announcement: " . $e->getMessage(), 500);
    }
}
