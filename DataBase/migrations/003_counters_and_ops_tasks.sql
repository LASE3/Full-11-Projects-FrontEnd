-- Migration 003: Create counters and ops_tasks tables
-- Database: active connection target

-- 1. id_counters for locked atomic sequence generation
CREATE TABLE IF NOT EXISTS `id_counters` (
    `name` VARCHAR(64) NOT NULL PRIMARY KEY,
    `next_val` INT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initialize common counters if not present
INSERT INTO `id_counters` (`name`, `next_val`) VALUES
('customers', 1011),
('projects', 16),
('tickets', 16),
('documents', 16),
('invoices', 11),
('orders', 1001),
('leads', 100),
('ops_tasks', 1)
ON DUPLICATE KEY UPDATE `name`=`name`;

-- 2. assignment_counters for round-robin employee assignment
CREATE TABLE IF NOT EXISTS `assignment_counters` (
    `dept` VARCHAR(16) NOT NULL PRIMARY KEY,
    `last_index` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `assignment_counters` (`dept`, `last_index`) VALUES
('SAL', 0),
('ENG', 0),
('OPS', 0),
('ITD', 0)
ON DUPLICATE KEY UPDATE `dept`=`dept`;

-- 3. ops_tasks table for OPS fulfilment, procurement, and shipment queue
CREATE TABLE IF NOT EXISTS `ops_tasks` (
    `task_id` VARCHAR(32) NOT NULL PRIMARY KEY,
    `order_id` VARCHAR(32) NULL,
    `prj_id` VARCHAR(32) NULL,
    `task_type` ENUM('Procurement','Shipment','Fulfilment') NOT NULL,
    `assigned_emp_id` VARCHAR(32) NULL,
    `status` VARCHAR(32) NOT NULL DEFAULT 'Pending',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ops_order` (`order_id`),
    INDEX `idx_ops_prj` (`prj_id`),
    INDEX `idx_ops_emp` (`assigned_emp_id`),
    INDEX `idx_ops_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
