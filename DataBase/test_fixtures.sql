-- ============================================================================
-- TEST FIXTURES & HISTORICAL LEGACY DATA
-- Moved out of baseline production seed
-- ============================================================================

-- Legacy & Fixture Tickets
INSERT INTO tickets (tkt_id, requester_type, title, priority, status) VALUES ('TKT-2026-016', 'Customer', 'Critical Turbine Telemetry Dropout (Acceptance Test)', 'Critical', 'Escalated') ON DUPLICATE KEY UPDATE status = VALUES(status);
INSERT INTO tickets (tkt_id, requester_type, title, priority, status) VALUES ('TKT-2026-017', 'Employee', 'Provision access for EMP-1096', 'Medium', 'Open') ON DUPLICATE KEY UPDATE status = VALUES(status);
INSERT INTO tickets (tkt_id, requester_type, title, priority, status) VALUES ('TKT-2026-018', 'Employee', 'Revoke access and decommission equipment for EMP-1096', 'High', 'Open') ON DUPLICATE KEY UPDATE status = VALUES(status);
INSERT INTO tickets (tkt_id, requester_type, title, priority, status) VALUES ('TKT-2026-019', 'Customer', 'Critical Turbine Telemetry Dropout (Acceptance Test)', 'Critical', 'Escalated') ON DUPLICATE KEY UPDATE status = VALUES(status);

