<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR IT Helpdesk (SYS-08) - Database & API Helper
 */

require_once __DIR__ . '/../../config/db.php';

function getItDb(): PDO
{
    $pdo = getDbConnection();
    ensureItTablesExist($pdo);
    return $pdo;
}

function ensureItTablesExist(PDO $pdo): void
{
    // Ensure resolved_at accepts NULL to prevent 1067 zero-date error on legacy MySQL
    try {
        $pdo->exec("ALTER TABLE `tickets` MODIFY COLUMN `resolved_at` TIMESTAMP NULL DEFAULT NULL");
    } catch (Throwable $e) {}

    // Ensure tickets columns
    try {
        $pdo->exec("ALTER TABLE `tickets` 
            ADD COLUMN IF NOT EXISTS `title` VARCHAR(255) NULL AFTER `source_system`,
            ADD COLUMN IF NOT EXISTS `description` TEXT NULL AFTER `title`,
            ADD COLUMN IF NOT EXISTS `requester_name` VARCHAR(150) NULL AFTER `description`,
            ADD COLUMN IF NOT EXISTS `requester_role` VARCHAR(150) NULL AFTER `requester_name`,
            ADD COLUMN IF NOT EXISTS `requester_dept` VARCHAR(100) NULL AFTER `requester_role`,
            ADD COLUMN IF NOT EXISTS `resolution_notes` TEXT NULL AFTER `status`,
            ADD COLUMN IF NOT EXISTS `sla_deadline` TIMESTAMP NULL AFTER `resolved_at`");
    } catch (Throwable $e) {}

    // Ensure ticket_comments columns
    try {
        $pdo->exec("ALTER TABLE `ticket_comments` 
            ADD COLUMN IF NOT EXISTS `author_name` VARCHAR(100) NULL AFTER `author_emp_id`,
            ADD COLUMN IF NOT EXISTS `author_role` VARCHAR(100) NULL AFTER `author_name`,
            ADD COLUMN IF NOT EXISTS `author_type` ENUM('tech', 'requester', 'system') NOT NULL DEFAULT 'tech' AFTER `author_role`");
    } catch (Throwable $e) {}

    // Ensure it_assets columns
    try {
        $pdo->exec("ALTER TABLE `it_assets`
            ADD COLUMN IF NOT EXISTS `asset_tag` VARCHAR(50) NULL AFTER `asset_id`,
            ADD COLUMN IF NOT EXISTS `device_model` VARCHAR(150) NULL AFTER `asset_type`,
            ADD COLUMN IF NOT EXISTS `location` VARCHAR(150) NULL AFTER `ip_address`,
            ADD COLUMN IF NOT EXISTS `mac_address` VARCHAR(50) NULL AFTER `ip_address`,
            ADD COLUMN IF NOT EXISTS `firmware_version` VARCHAR(50) NULL AFTER `os_version`,
            ADD COLUMN IF NOT EXISTS `health_status` VARCHAR(50) NOT NULL DEFAULT 'Nominal'");
    } catch (Throwable $e) {}

    // Ensure knowledge_base_articles columns
    try {
        $pdo->exec("ALTER TABLE `knowledge_base_articles`
            ADD COLUMN IF NOT EXISTS `article_code` VARCHAR(50) NULL AFTER `kb_id`,
            ADD COLUMN IF NOT EXISTS `summary` VARCHAR(255) NULL AFTER `title`,
            ADD COLUMN IF NOT EXISTS `tags` VARCHAR(255) NULL AFTER `category`,
            ADD COLUMN IF NOT EXISTS `views_count` INT(11) NOT NULL DEFAULT 0");
    } catch (Throwable $e) {}

    // Ensure it_assets notes column
    try {
        $pdo->exec("ALTER TABLE `it_assets` ADD COLUMN IF NOT EXISTS `notes` TEXT NULL");
    } catch (Throwable $e) {}

    // Ensure sla_policies description column
    try {
        $pdo->exec("ALTER TABLE `sla_policies` ADD COLUMN IF NOT EXISTS `description` VARCHAR(255) NULL");
    } catch (Throwable $e) {}

    // Ensure ticket_escalations status column
    try {
        $pdo->exec("ALTER TABLE `ticket_escalations` ADD COLUMN IF NOT EXISTS `status` ENUM('Pending','Acknowledged','Resolved','Escalated') NOT NULL DEFAULT 'Escalated'");
    } catch (Throwable $e) {}
}

function sendJsonSuccess(mixed $data = [], string $message = ''): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success'   => true,
        'message'   => $message,
        'data'      => $data,
        'timestamp' => date('Y-m-d H:i:s'),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function sendJsonError(string $error, int $statusCode = 400, mixed $extra = []): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success'   => false,
        'error'     => $error,
        'extra'     => $extra,
        'timestamp' => date('Y-m-d H:i:s'),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function getRequestPayload(): array
{
    $input = file_get_contents('php://input');
    if (!empty($input)) {
        $json = json_decode($input, true);
        if (is_array($json)) {
            return $json;
        }
    }
    return $_POST ?: $_GET;
}
