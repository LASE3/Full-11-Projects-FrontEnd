-- ============================================================================
-- Migration 009: SuperAdmin Safety Trigger & Change-Password Requirement
-- NOTE: DELIMITER syntax is NOT used — PDO multi-statement handles this.
-- The triggers are created via the build.php trigger installer instead.
-- This file handles only the column/data setup; triggers via 009_triggers.php.
-- ============================================================================

SET SQL_MODE = '';

-- 1. Ensure must_change_password column exists (idempotent with 008)
ALTER TABLE employee_accounts
    ADD COLUMN IF NOT EXISTS `must_change_password` TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE employee_accounts
    ADD COLUMN IF NOT EXISTS `totp_secret` VARCHAR(64) NULL DEFAULT NULL;

-- 2. EMP-0001 does not need to change password (was set up in 007_superadmin.sql)
UPDATE employee_accounts
SET must_change_password = 0
WHERE emp_id = 'EMP-0001';
