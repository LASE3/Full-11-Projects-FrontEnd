-- =========================================================================
-- VOSTOKPRIBOR ENTERPRISE DATABASE MIGRATION
-- Migration 017: Password History and Policy Enforcement
-- Enforces no-reuse of the last 5 passwords and tracks password lineage.
-- =========================================================================

CREATE TABLE IF NOT EXISTS `password_history` (
    `history_id` INT AUTO_INCREMENT PRIMARY KEY,
    `account_type` ENUM('Employee', 'Customer') NOT NULL,
    `account_id` INT NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_acc_hist` (`account_type`, `account_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
