-- ============================================================================
-- Migration 008: Security Hardening & Honest Data Cleanup
-- Applied after verifying existing FK state. Idempotent.
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- =========================================================================
-- 1. PURGE FAKE HEARTBEAT TRAFFIC (honesty requirement)
-- =========================================================================
DELETE FROM system_integration_logs
WHERE endpoint = 'BASELINE_HEARTBEAT'
   OR payload_summary LIKE '%baseline link heartbeat%'
   OR payload_summary LIKE '%heartbeat telemetry%';

-- Remove legacy SYS11_TO_ALL row if it exists
DELETE FROM system_integration_logs
WHERE source_system_id = 'SYS11'
   OR target_system_id = 'ALL';

-- =========================================================================
-- 2. MARK NOT-IMPLEMENTED LINKS HONESTLY
-- =========================================================================
ALTER TABLE system_integrations
    ADD COLUMN IF NOT EXISTS `status`
    ENUM('Active','Inactive','NotImplemented') NOT NULL DEFAULT 'Active';

-- Reset all to Active first (migration 002 seeds them as Active)
UPDATE system_integrations SET status = 'Active';

-- Explicitly mark the 17 links with no real implementation yet as NotImplemented.
-- These are determined by code audit, NOT by log presence (logs are empty on fresh build).
UPDATE system_integrations SET status = 'NotImplemented'
WHERE link_code IN (
    'CRM_TO_DEV',   -- No CRM->DEV code path implemented
    'CRM_TO_SHP',   -- No CRM->SHP code path implemented
    'CUS_TO_ADM',   -- No CUS->ADM code path implemented
    'DEV_TO_ADM',   -- No DEV->ADM code path implemented
    'DEV_TO_OPS',   -- No DEV->OPS code path implemented
    'DEV_TO_SHP',   -- No DEV->SHP code path implemented
    'DOC_TO_ADM',   -- No DOC->ADM code path implemented
    'DOC_TO_EMP',   -- No DOC->EMP code path implemented
    'EMP_TO_DOC',   -- No EMP->DOC code path implemented
    'FIN_TO_ADM',   -- No FIN->ADM code path implemented
    'IT_TO_CUS',    -- No IT->CUS code path implemented
    'IT_TO_DEV',    -- No IT->DEV code path implemented
    'OPS_TO_DEV',   -- No OPS->DEV code path implemented
    'SHP_TO_ADM',   -- No SHP->ADM code path implemented
    'SHP_TO_DEV',   -- No SHP->DEV code path implemented
    'WEB_TO_ADM',   -- No WEB->ADM code path implemented
    'WEB_TO_SHP'    -- No WEB->SHP code path implemented
);

-- =========================================================================
-- 3. SECURITY COLUMNS
-- =========================================================================
ALTER TABLE employee_accounts
    ADD COLUMN IF NOT EXISTS `must_change_password` TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE employee_accounts
    ADD COLUMN IF NOT EXISTS `totp_secret` VARCHAR(64) NULL DEFAULT NULL;

ALTER TABLE employees
    ADD COLUMN IF NOT EXISTS `is_system_account` TINYINT(1) NOT NULL DEFAULT 0;

-- =========================================================================
-- 4. FK CONSTRAINTS — only add what is missing
-- Already confirmed existing: fk_inv_prj, fk_invoices_cus_id, fk_invoices_prj_id,
-- fk_billing_cycles_prj_id, fk_bc_prj, fk_ops_prj, fk_prj_pm_emp,
-- fk_projects_cus_id, fk_tkt_emp, fk_tickets_assigned_emp_id
-- All required FKs already exist. Verify with probe after this migration.
-- =========================================================================

-- =========================================================================
-- 5. SEED HYGIENE: Remove known dirty seed rows
-- =========================================================================
-- Remove duplicate Purchase_Order_Severstal_2026.pdf documents (keep lowest doc_id)
DELETE d1 FROM documents d1
INNER JOIN documents d2
WHERE d1.doc_id > d2.doc_id
  AND d1.file_name = 'Purchase_Order_Severstal_2026.pdf'
  AND d2.file_name = 'Purchase_Order_Severstal_2026.pdf';

-- Remove TEST-KEY rows if any leaked into baseline (column is key_identifier)
DELETE FROM developer_api_keys WHERE key_identifier LIKE 'TEST-KEY-%';

-- Remove DOC-2026-TEST rows if any leaked into baseline
DELETE FROM documents WHERE doc_id LIKE 'DOC-2026-TEST%';

-- =========================================================================
-- 6. LEAST-PRIVILEGE DATABASE USER
-- Creates vostok_app user if not existing; password must be changed by admin.
-- After running: UPDATE .env DB_USER=vostok_app DB_PASS=<real-secret>
-- =========================================================================
CREATE USER IF NOT EXISTS 'vostok_app'@'localhost' IDENTIFIED BY 'CHANGE_THIS_IN_ENV';
GRANT SELECT, INSERT, UPDATE, DELETE ON `vostokpribor`.* TO 'vostok_app'@'localhost';
FLUSH PRIVILEGES;

SET FOREIGN_KEY_CHECKS = 1;
