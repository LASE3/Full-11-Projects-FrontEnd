-- ============================================================================
-- Migration 010: SuperAdmin Single Account & Credential Consolidation
-- Collapses EMP-0001 employee_accounts into a single authoritative record.
-- Supports login via 'admin@gmail.com', 'admin', or 'EMP-0001'.
-- Idempotent and safe to run on any build.
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Ensure EMP-0001 employee record is authoritative
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
    email              = 'admin@gmail.com',
    clearance_level    = 'L4',
    employment_status  = 'Active',
    is_system_account  = 1;

-- 2. Collapse all employee_accounts for EMP-0001 into one authoritative account
DELETE FROM employee_accounts WHERE emp_id = 'EMP-0001' OR username = 'admin';

INSERT INTO employee_accounts (
    emp_id, username, password_hash, status, must_change_password
) VALUES (
    'EMP-0001',
    'admin',
    '$2y$10$UCivQf67Lbox5RmiG9mUdum1ETNbIVcvW1tqlzEtzOZMIT0v9OSOW', -- АдминистраторX0001
    'Active',
    0
);

-- 5. Ensure SuperAdmin role exists and is assigned
INSERT INTO roles (role_name, description)
VALUES ('SuperAdmin', 'Unrestricted system-wide administrative access')
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO employee_roles (emp_id, role_id)
SELECT 'EMP-0001', role_id FROM roles WHERE role_name = 'SuperAdmin'
ON DUPLICATE KEY UPDATE role_id = VALUES(role_id);

SET FOREIGN_KEY_CHECKS = 1;
