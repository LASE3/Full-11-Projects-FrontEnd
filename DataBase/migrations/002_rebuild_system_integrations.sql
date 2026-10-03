-- Migration 002: Rebuild system_integrations matrix according to locked baseline specifications
-- Replaces SYS-style codes with canonical codes (WEB, SHP, CUS, EMP, CRM, HR, FIN, IT, DOC, DEV, ADM)
-- Fixes corrupted arrow characters and establishes bidirectional & governance links

SET NAMES utf8mb4;

TRUNCATE TABLE `system_integrations`;

INSERT INTO `system_integrations` 
(`integration_id`, `link_code`, `source_system_id`, `target_system_id`, `api_protocol`, `authentication_method`, `data_exchanged`, `direction`, `required_clearance`, `status`, `created_at`) 
VALUES
(1,  'WEB_TO_CRM', 'WEB', 'CRM', 'REST / JSON HTTPS', 'API Key & Captcha Verification', 'Public commercial leads, RFQ inquiries, equipment interests', 'Outbound (WEB -> CRM)', 'L1', 'Active', NOW()),
(2,  'WEB_TO_SHP', 'WEB', 'SHP', 'REST / JSON HTTPS', 'Public Catalog Token', 'Public product catalog info, product availability, storefront routing', 'Outbound (WEB -> SHP)', 'L1', 'Active', NOW()),
(3,  'CRM_TO_SHP', 'CRM', 'SHP', 'REST / JSON HTTPS', 'Mutual HMAC-SHA256 & Service Token', 'Commercial client accounts, customized wholesale pricing, credit limits', 'Outbound (CRM -> SHP)', 'L2', 'Active', NOW()),
(4,  'CRM_TO_FIN', 'CRM', 'FIN', 'REST / JSON RPC',   'HMAC-SHA256 & L2 Finance Token', 'Won opportunities billing milestones, contract values, payment terms', 'Outbound (CRM -> FIN)', 'L2', 'Active', NOW()),
(5,  'CRM_TO_DOC', 'CRM', 'DOC', 'Document Ingestion REST API', 'Bearer API Key & SSO', 'Customer dossier, contract agreements, project SOW stubs', 'Outbound (CRM -> DOC)', 'L2', 'Active', NOW()),
(6,  'CRM_TO_CUS', 'CRM', 'CUS', 'REST / JSON HTTPS', 'SSO Session Token & API Key', 'Customer onboarding accounts, invite tokens, SLA profiles, project tracking', 'Outbound (CRM -> CUS)', 'L2', 'Active', NOW()),
(7,  'SHP_TO_CRM', 'SHP', 'CRM', 'REST / HTTPS JSON', 'Storefront API Token', 'B2B purchase orders, buyer commercial telemetry, client activity log', 'Outbound (SHP -> CRM)', 'L2', 'Active', NOW()),
(8,  'SHP_TO_FIN', 'SHP', 'FIN', 'REST / JSON RPC',   'Internal Service Token', 'Committed purchase orders, commercial invoice generation, tax records', 'Outbound (SHP -> FIN)', 'L2', 'Active', NOW()),
(9,  'SHP_TO_CUS', 'SHP', 'CUS', 'REST / Webhook',     'Customer Session Token', 'Order confirmation notices, delivery status telemetry, shipment tracking', 'Outbound (SHP -> CUS)', 'L1', 'Active', NOW()),
(10, 'SHP_TO_OPS', 'SHP', 'EMP', 'REST Event Bus / Queue', 'Service Bus Token', 'Warehouse fulfilment tasks, inventory depletion alerts, shipment dispatch', 'Outbound (SHP -> OPS)', 'L2', 'Active', NOW()),
(11, 'FIN_TO_CUS', 'FIN', 'CUS', 'REST / JSON HTTPS', 'Customer Session Token', 'Commercial invoice issuance, payment status updates, settlement receipts', 'Outbound (FIN -> CUS)', 'L1', 'Active', NOW()),
(12, 'FIN_TO_DOC', 'FIN', 'DOC', 'Document Archive API', 'Service Token', 'Billing archive records, tax invoices, fiscal settlement statements', 'Outbound (FIN -> DOC)', 'L2', 'Active', NOW()),
(13, 'DOC_TO_CUS', 'DOC', 'CUS', 'REST / JSON HTTPS', 'Customer Auth Token', 'Published customer calibration certificates, technical specs, user manuals', 'Outbound (DOC -> CUS)', 'L1', 'Active', NOW()),
(14, 'IT_TO_CUS',  'IT',  'CUS', 'REST / JSON HTTPS', 'Customer Session Token', 'Helpdesk ticket status updates, resolution notes, customer SLA notifications', 'Outbound (IT -> CUS)', 'L1', 'Active', NOW()),
(15, 'CUS_TO_IT',  'CUS', 'IT',  'REST / JSON HTTPS', 'Customer Auth Token', 'Customer technical support requests, incident reports, SLA inquiries', 'Outbound (CUS -> IT)', 'L1', 'Active', NOW()),
(16, 'HR_TO_EMP',  'HR',  'EMP', 'REST / JSON Webhook', 'Internal System Token', 'New employee announcements, corporate directory sync, organization updates', 'Outbound (HR -> EMP)', 'L1', 'Active', NOW()),
(17, 'HR_TO_IT',   'HR',  'IT',  'REST / JSON HTTPS', 'Internal Service Token', 'Employee onboarding IT access provisioning tickets, offboarding asset recovery', 'Outbound (HR -> IT)', 'L2', 'Active', NOW()),
(18, 'HR_TO_ADM',  'HR',  'ADM', 'Mutual HMAC-SHA256 & L4 SuperAdmin', 'Audit Vault Token', 'Executive appointments, clearance level audits, orphaned account reports', 'Outbound (HR -> ADM)', 'L3', 'Active', NOW()),
(19, 'HR_TO_DOC',  'HR',  'DOC', 'Document Ingestion API', 'HR Service Token', 'Personnel employment contracts, confidentiality NDAs, policy sign-offs', 'Outbound (HR -> DOC)', 'L2', 'Active', NOW()),
(20, 'EMP_TO_DOC', 'EMP', 'DOC', 'Document Ingestion API', 'SSO Session Token', 'Internal department policies, employee form submissions, compliance memos', 'Outbound (EMP -> DOC)', 'L1', 'Active', NOW()),
(21, 'DOC_TO_EMP', 'DOC', 'EMP', 'REST / JSON HTTPS', 'SSO Session Token', 'Corporate document catalog, internal knowledge base, standard operating procedures', 'Outbound (DOC -> EMP)', 'L1', 'Active', NOW()),
(22, 'DEV_TO_SHP', 'DEV', 'SHP', 'REST / JSON HTTPS', 'Bearer API Key', 'B2B eCommerce API telemetry, SKU specifications, developer storefront bindings', 'Outbound (DEV -> SHP)', 'L2', 'Active', NOW()),
(23, 'SHP_TO_DEV', 'SHP', 'DEV', 'REST Webhook / JSON', 'Partner API Secret', 'eCommerce API error logs, webhook delivery receipts, partner store metrics', 'Outbound (SHP -> DEV)', 'L2', 'Active', NOW()),
(24, 'DEV_TO_CRM', 'DEV', 'CRM', 'REST / JSON HTTPS', 'Service Token', 'Developer partner applications, sandbox leads, technical integration dossiers', 'Outbound (DEV -> CRM)', 'L2', 'Active', NOW()),
(25, 'CRM_TO_DEV', 'CRM', 'DEV', 'REST / JSON HTTPS', 'Service Token', 'Approved partner tier entitlements, enterprise API access credentials', 'Outbound (CRM -> DEV)', 'L2', 'Active', NOW()),
(26, 'DEV_TO_IT',  'DEV', 'IT',  'Syslog / REST Webhook', 'Bearer API Token', 'Sandbox telemetry alerts, webhook errors, continuous deployment incident tickets', 'Outbound (DEV -> IT)', 'L2', 'Active', NOW()),
(27, 'IT_TO_DEV',  'IT',  'DEV', 'REST / JSON HTTPS', 'IT Support Token', 'Bug tracking reports, infrastructure telemetry tickets, platform defect logs', 'Outbound (IT -> DEV)', 'L2', 'Active', NOW()),
(28, 'DEV_TO_OPS', 'DEV', 'EMP', 'REST Event Bus / Queue', 'Service Token', 'Edge device firmware updates, telemetry collection triggers, automated hardware tests', 'Outbound (DEV -> OPS)', 'L2', 'Active', NOW()),
(29, 'OPS_TO_DEV', 'EMP', 'DEV', 'REST Webhook / JSON', 'Device Gateway Token', 'Industrial device telemetry feedback, QA test bench results, diagnostic data', 'Outbound (OPS -> DEV)', 'L2', 'Active', NOW()),
(30, 'WEB_TO_ADM', 'WEB', 'ADM', 'REST / JSON HTTPS', 'Universal Gateway Router', 'Web platform visitor analytics, perimeter security events, public portal health', 'Outbound (WEB -> ADM)', 'L2', 'Active', NOW()),
(31, 'SHP_TO_ADM', 'SHP', 'ADM', 'REST / JSON HTTPS', 'Mutual HMAC-SHA256', 'High-value transaction alerts, store security events, storefront governance', 'Outbound (SHP -> ADM)', 'L3', 'Active', NOW()),
(32, 'CUS_TO_ADM', 'CUS', 'ADM', 'REST / JSON HTTPS', 'SecOps Event Bridge', 'Cross-tenant IDOR violations, authentication anomalies, portal governance alerts', 'Outbound (CUS -> ADM)', 'L3', 'Active', NOW()),
(33, 'EMP_TO_ADM', 'EMP', 'ADM', 'REST / JSON HTTPS', 'Audit Vault Token', 'Internal security incidents, employee policy exceptions, privileged actions', 'Outbound (EMP -> ADM)', 'L2', 'Active', NOW()),
(34, 'CRM_TO_ADM', 'CRM', 'ADM', 'REST / JSON HTTPS', 'Audit Vault Token', 'Client contract exceptions, high-risk pipeline changes, CRM security logs', 'Outbound (CRM -> ADM)', 'L3', 'Active', NOW()),
(35, 'FIN_TO_ADM', 'FIN', 'ADM', 'REST / JSON RPC',   'Mutual HMAC-SHA256 & L4', 'Financial reconciliation anomalies, payment segregation violations, audit flags', 'Outbound (FIN -> ADM)', 'L4', 'Active', NOW()),
(36, 'IT_TO_ADM',  'IT',  'ADM', 'REST / JSON HTTPS', 'SecOps Event Bridge', 'Critical ticket escalations, infrastructure breach alerts, SLA penalties', 'Outbound (IT -> ADM)', 'L3', 'Active', NOW()),
(37, 'DOC_TO_ADM', 'DOC', 'ADM', 'Document Audit API', 'Audit Vault Token', 'Confidential and TopSecret document access audits, classification overrides', 'Outbound (DOC -> ADM)', 'L3', 'Active', NOW()),
(38, 'DEV_TO_ADM', 'DEV', 'ADM', 'REST / JSON HTTPS', 'Mutual HMAC-SHA256', 'API credential generation, developer partner approvals, anomalous rate limits', 'Outbound (DEV -> ADM)', 'L3', 'Active', NOW())
ON DUPLICATE KEY UPDATE
    `source_system_id` = VALUES(`source_system_id`),
    `target_system_id` = VALUES(`target_system_id`),
    `api_protocol` = VALUES(`api_protocol`),
    `authentication_method` = VALUES(`authentication_method`),
    `data_exchanged` = VALUES(`data_exchanged`),
    `direction` = VALUES(`direction`),
    `required_clearance` = VALUES(`required_clearance`),
    `status` = VALUES(`status`);
