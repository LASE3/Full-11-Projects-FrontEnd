-- ============================================================================
-- Migration 007: SuperAdmin System Account
-- Creates EMP-0001 "Администратор" as the universal system SuperAdmin.
-- Idempotent: safe to run multiple times.
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Add is_system_account column to employees if it doesn't exist
ALTER TABLE employees
    ADD COLUMN IF NOT EXISTS `is_system_account` TINYINT(1) NOT NULL DEFAULT 0
    COMMENT 'Set to 1 for system/service accounts excluded from HR headcount';

-- 2. Add must_change_password column to employee_accounts if missing
ALTER TABLE employee_accounts
    ADD COLUMN IF NOT EXISTS `must_change_password` TINYINT(1) NOT NULL DEFAULT 0
    COMMENT '1 = user must change password at next login';

-- 3. Add totp_secret column to employee_accounts if missing (SuperAdmin 2FA)
ALTER TABLE employee_accounts
    ADD COLUMN IF NOT EXISTS `totp_secret` VARCHAR(64) NULL DEFAULT NULL
    COMMENT 'TOTP secret for 2FA (NULL = 2FA not enabled)';

-- 4. Insert EMP-0001 system account
INSERT INTO employees (
    emp_id, full_name, job_title, department_code, email,
    clearance_level, employment_status, is_system_account
) VALUES (
    'EMP-0001',
    'Администратор',
    'System Administrator',
    'EXE',
    'admin@gmail.com',
    'L4',
    'Active',
    1
) ON DUPLICATE KEY UPDATE
    full_name          = 'Администратор',
    job_title          = 'System Administrator',
    clearance_level    = 'L4',
    employment_status  = 'Active',
    is_system_account  = 1;

-- 5. Ensure SuperAdmin role exists
INSERT INTO roles (role_name, description)
VALUES ('SuperAdmin', 'Unrestricted system-wide administrative access')
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- 6. Assign SuperAdmin role to EMP-0001
INSERT INTO employee_roles (emp_id, role_id)
SELECT 'EMP-0001', role_id FROM roles WHERE role_name = 'SuperAdmin'
ON DUPLICATE KEY UPDATE role_id = VALUES(role_id);

-- 7. Create/update employee_account for EMP-0001
INSERT INTO employee_accounts (emp_id, username, password_hash, status, must_change_password)
VALUES (
    'EMP-0001',
    'admin',
    '$2y$10$IvBRwu9FeDoyqTupw2q0De80dqn7k/QtkXO//kK9VYN7eTojeDenS',
    'Active',
    0
) ON DUPLICATE KEY UPDATE
    password_hash = VALUES(password_hash),
    status   = 'Active';

SET FOREIGN_KEY_CHECKS = 1;
