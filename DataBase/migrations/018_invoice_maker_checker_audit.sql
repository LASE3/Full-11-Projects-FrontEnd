-- =====================================================================
-- Migration 018: Invoices Maker-Checker Audit Columns, Constraints, and Timestamps
-- Specification: Phase 2.1
-- Ensures invoices table has:
--   - created_by_emp_id VARCHAR(10) NULL
--   - approved_by_emp_id VARCHAR(10) NULL
--   - paid_by_emp_id VARCHAR(10) NULL
--   - approved_at DATETIME NULL
--   - created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
-- Foreign keys and indexes on created_by, approved_by, paid_by
-- Baseline rows INV-2026-001..010 backfilled with NULL
-- =====================================================================

SET @dbname = DATABASE();

-- 1. approved_by_emp_id
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
      AND TABLE_NAME = 'invoices'
      AND COLUMN_NAME = 'approved_by_emp_id'
  ) > 0,
  'SELECT 1',
  'ALTER TABLE invoices ADD COLUMN approved_by_emp_id VARCHAR(10) NULL AFTER created_by_emp_id'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. paid_by_emp_id
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
      AND TABLE_NAME = 'invoices'
      AND COLUMN_NAME = 'paid_by_emp_id'
  ) > 0,
  'SELECT 1',
  'ALTER TABLE invoices ADD COLUMN paid_by_emp_id VARCHAR(10) NULL AFTER approved_by_emp_id'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3. approved_at
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
      AND TABLE_NAME = 'invoices'
      AND COLUMN_NAME = 'approved_at'
  ) > 0,
  'SELECT 1',
  'ALTER TABLE invoices ADD COLUMN approved_at DATETIME NULL AFTER paid_by_emp_id'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4. created_at
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
      AND TABLE_NAME = 'invoices'
      AND COLUMN_NAME = 'created_at'
  ) > 0,
  'SELECT 1',
  'ALTER TABLE invoices ADD COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 5. Indexes
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = @dbname
      AND TABLE_NAME = 'invoices'
      AND INDEX_NAME = 'idx_invoices_created_by'
  ) > 0,
  'SELECT 1',
  'ALTER TABLE invoices ADD INDEX idx_invoices_created_by (created_by_emp_id)'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = @dbname
      AND TABLE_NAME = 'invoices'
      AND INDEX_NAME = 'idx_invoices_approved_by'
  ) > 0,
  'SELECT 1',
  'ALTER TABLE invoices ADD INDEX idx_invoices_approved_by (approved_by_emp_id)'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = @dbname
      AND TABLE_NAME = 'invoices'
      AND INDEX_NAME = 'idx_invoices_paid_by'
  ) > 0,
  'SELECT 1',
  'ALTER TABLE invoices ADD INDEX idx_invoices_paid_by (paid_by_emp_id)'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 6. Foreign Keys
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE TABLE_SCHEMA = @dbname
      AND TABLE_NAME = 'invoices'
      AND CONSTRAINT_NAME = 'fk_invoices_created_by'
  ) > 0,
  'SELECT 1',
  'ALTER TABLE invoices ADD CONSTRAINT fk_invoices_created_by FOREIGN KEY (created_by_emp_id) REFERENCES employees (emp_id) ON UPDATE CASCADE ON DELETE SET NULL'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE TABLE_SCHEMA = @dbname
      AND TABLE_NAME = 'invoices'
      AND CONSTRAINT_NAME = 'fk_invoices_approved_by'
  ) > 0,
  'SELECT 1',
  'ALTER TABLE invoices ADD CONSTRAINT fk_invoices_approved_by FOREIGN KEY (approved_by_emp_id) REFERENCES employees (emp_id) ON UPDATE CASCADE ON DELETE SET NULL'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE TABLE_SCHEMA = @dbname
      AND TABLE_NAME = 'invoices'
      AND CONSTRAINT_NAME = 'fk_invoices_paid_by'
  ) > 0,
  'SELECT 1',
  'ALTER TABLE invoices ADD CONSTRAINT fk_invoices_paid_by FOREIGN KEY (paid_by_emp_id) REFERENCES employees (emp_id) ON UPDATE CASCADE ON DELETE SET NULL'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 7. Backfill baseline rows with NULL
UPDATE invoices 
SET created_by_emp_id = NULL,
    approved_by_emp_id = NULL,
    paid_by_emp_id = NULL,
    approved_at = NULL
WHERE inv_id BETWEEN 'INV-2026-001' AND 'INV-2026-010';

-- 8. Assign Role 6 (Finance Operations & Controller) to FIN department staff
INSERT IGNORE INTO `employee_roles` (`emp_id`, `role_id`, `granted_at`, `granted_by_emp_id`)
SELECT e.`emp_id`, 6, NOW(), 'EMP-1003'
FROM `employees` e
WHERE e.`department_code` = 'FIN';

