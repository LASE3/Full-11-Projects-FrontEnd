-- ============================================================================
-- Migration 006: Baseline Schema Adjustments & Foreign Keys
-- Idempotent schema adjustments for locked baseline
-- ============================================================================

-- 1. Repoint any remaining references from legacy departments ('IT', 'LOG', 'QA') to 'ITD' / 'OPS'
UPDATE department_boards SET department_code = 'ITD' WHERE department_code IN ('IT', 'QA');
UPDATE department_boards SET department_code = 'OPS' WHERE department_code = 'LOG';

UPDATE devices SET department_code = 'ITD' WHERE department_code IN ('IT', 'QA');
UPDATE devices SET department_code = 'OPS' WHERE department_code = 'LOG';

UPDATE it_assets SET department_code = 'ITD' WHERE department_code IN ('IT', 'QA');
UPDATE it_assets SET department_code = 'OPS' WHERE department_code = 'LOG';

UPDATE employees SET department_code = 'ITD' WHERE department_code IN ('IT', 'QA');
UPDATE employees SET department_code = 'OPS' WHERE department_code = 'LOG';

-- Delete legacy departments
DELETE FROM departments WHERE dept_code IN ('IT', 'LOG', 'QA');

-- Ensure departments table has the exact 8 canonical departments with exact targets
INSERT INTO departments (dept_code, dept_name, main_function, employee_count_target) VALUES
('EXE', 'Executive Leadership', 'Corporate strategy, executive oversight, and governance', 5),
('SAL', 'Sales & Enterprise Relations', 'Global business development, CRM pipeline, and enterprise accounts', 16),
('OPS', 'Operations & Logistics', 'Supply chain, procurement, fulfilment, and logistics operations', 20),
('ENG', 'Engineering & Design', 'Product development, systems architecture, and technical integration', 16),
('FIN', 'Finance & Billing', 'Financial accounting, commercial billing, audit, and cashflow control', 10),
('HRA', 'Human Resources & Admin', 'Workforce administration, recruitment, onboarding, and compliance', 8),
('ITD', 'Information Technology & Security', 'IT infrastructure, helpdesk support, telemetry, and cybersecurity', 14),
('GOV', 'Governance, Risk & Compliance', 'Enterprise risk management, regulatory compliance, and security oversight', 6)
ON DUPLICATE KEY UPDATE 
    dept_name = VALUES(dept_name),
    main_function = VALUES(main_function),
    employee_count_target = VALUES(employee_count_target);

-- 2. Adjust projects status enum to support 'Contract Review' with space if desired
ALTER TABLE projects MODIFY COLUMN status ENUM('Planning','Procurement','Design','Integration','Testing','Execution','ContractReview','Contract Review','Maintenance','Closed') DEFAULT 'Planning';

-- 3. Document Classification View matching PDF vocab (L1-L4 <-> Public/Internal/Confidential/TopSecret)
CREATE OR REPLACE VIEW v_document_classifications AS
SELECT 
    doc_id,
    file_name,
    description,
    classification,
    CASE classification
        WHEN 'Public' THEN 'L1'
        WHEN 'Internal' THEN 'L2'
        WHEN 'Confidential' THEN 'L3'
        WHEN 'TopSecret' THEN 'L4'
        ELSE 'L2'
    END AS clearance_level,
    CASE classification
        WHEN 'Public' THEN 'L1 - Public'
        WHEN 'Internal' THEN 'L2 - Internal'
        WHEN 'Confidential' THEN 'L3 - Confidential'
        WHEN 'TopSecret' THEN 'L4 - Top Secret'
        ELSE classification
    END AS classification_label,
    folder,
    department,
    file_size,
    status,
    related_prj_id,
    related_cus_id,
    owner_emp_id,
    created_at,
    updated_at
FROM documents;

-- 4. Ticket Priority & Classification View matching PDF vocab
CREATE OR REPLACE VIEW v_ticket_priorities AS
SELECT 
    tkt_id,
    title,
    priority,
    CASE priority
        WHEN 'Low' THEN 'P4'
        WHEN 'Medium' THEN 'P3'
        WHEN 'High' THEN 'P2'
        WHEN 'Critical' THEN 'P1'
        ELSE 'P3'
    END AS priority_code,
    status,
    source_system,
    assigned_emp_id,
    requester_cus_id,
    requester_emp_id,
    sla_deadline,
    created_at
FROM tickets;
