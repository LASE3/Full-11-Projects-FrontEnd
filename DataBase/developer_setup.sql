-- =====================================================================
-- VOSTOKPRIBOR SYSTEM 10: DEVELOPER & API PORTAL DATABASE SCHEMA
-- Compatible with MySQL / MariaDB (phpMyAdmin)
-- Database: `vostokpribor`
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `vostokpribor` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `vostokpribor`;

-- ---------------------------------------------------------------------
-- Table 1: `developer_endpoints`
-- Dynamic API Endpoints for Dashboard.php
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `developer_endpoints`;
CREATE TABLE `developer_endpoints` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `endpoint_slug` VARCHAR(64) NOT NULL UNIQUE,
  `method` VARCHAR(10) NOT NULL DEFAULT 'GET',
  `path` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `classification` VARCHAR(64) NOT NULL DEFAULT 'Internal',
  `rate_limit` VARCHAR(50) NOT NULL DEFAULT '10k/min',
  `target_hardware` VARCHAR(50) NOT NULL DEFAULT 'PROD-1001',
  `parameters_json` LONGTEXT NULL,
  `curl_snippet` TEXT NULL,
  `python_snippet` TEXT NULL,
  `node_snippet` TEXT NULL,
  `go_snippet` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table 2: `developer_api_keys`
-- Cryptographic tokens & Partner Key Vault for credentials.php
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `developer_api_keys`;
CREATE TABLE `developer_api_keys` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `key_identifier` VARCHAR(50) NOT NULL UNIQUE,
  `label` VARCHAR(150) NOT NULL,
  `partner_id` VARCHAR(50) NOT NULL DEFAULT 'CUS-1002',
  `partner_name` VARCHAR(150) NOT NULL DEFAULT 'BaltNord Process Systems',
  `token_prefix` VARCHAR(32) NOT NULL,
  `token_full` VARCHAR(255) NOT NULL,
  `environment` ENUM('Production', 'Sandbox', 'Staging') NOT NULL DEFAULT 'Production',
  `rate_limit` VARCHAR(50) NOT NULL DEFAULT '10,000 req/min',
  `rate_limit_value` INT(11) NOT NULL DEFAULT 10000,
  `classification` VARCHAR(50) NOT NULL DEFAULT 'Confidential',
  `scopes` VARCHAR(255) NOT NULL DEFAULT 'telemetry:read,scada:ingest',
  `status` ENUM('Active', 'Revoked', 'Suspended') NOT NULL DEFAULT 'Active',
  `usage_count` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table 3: `developer_sandbox_presets`
-- Test request presets for sandbox.php
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `developer_sandbox_presets`;
CREATE TABLE `developer_sandbox_presets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(150) NOT NULL,
  `method` VARCHAR(10) NOT NULL DEFAULT 'GET',
  `url` VARCHAR(255) NOT NULL,
  `sample_body` TEXT NULL,
  `description` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table 4: `developer_sandbox_logs`
-- Real-time sandbox test dispatch history
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `developer_sandbox_logs`;
CREATE TABLE `developer_sandbox_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `method` VARCHAR(10) NOT NULL,
  `url` VARCHAR(500) NOT NULL,
  `request_body` LONGTEXT NULL,
  `status_code` INT(11) NOT NULL DEFAULT 200,
  `response_time_ms` INT(11) NOT NULL DEFAULT 25,
  `response_size` VARCHAR(30) NOT NULL DEFAULT '512 B',
  `response_body` LONGTEXT NULL,
  `executed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table 5: `developer_webhooks`
-- Outbound Webhook Delivery Ledger for metrics.php
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `developer_webhooks`;
CREATE TABLE `developer_webhooks` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `delivery_id` VARCHAR(50) NOT NULL UNIQUE,
  `event_type` VARCHAR(100) NOT NULL,
  `target_endpoint` VARCHAR(255) NOT NULL,
  `status_code` VARCHAR(50) NOT NULL DEFAULT '200 OK',
  `latency_ms` INT(11) NOT NULL DEFAULT 35,
  `status` ENUM('Delivered', 'Failed', 'Pending') NOT NULL DEFAULT 'Delivered',
  `classification` VARCHAR(50) NOT NULL DEFAULT 'Internal',
  `payload` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table 6: `developer_partner_applications`
-- Client clearance applications for partner-registration.php
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `developer_partner_applications`;
CREATE TABLE `developer_partner_applications` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` VARCHAR(50) NOT NULL UNIQUE,
  `company_name` VARCHAR(150) NOT NULL,
  `partner_id` VARCHAR(50) NULL,
  `contact_name` VARCHAR(100) NOT NULL,
  `contact_email` VARCHAR(150) NOT NULL,
  `project_ref` VARCHAR(255) NULL,
  `target_environment` ENUM('sandbox', 'staging', 'production') NOT NULL DEFAULT 'sandbox',
  `requested_scopes` TEXT NOT NULL,
  `public_key` TEXT NULL,
  `compliance_doc` TINYINT(1) NOT NULL DEFAULT 1,
  `compliance_iec` TINYINT(1) NOT NULL DEFAULT 1,
  `compliance_nda` TINYINT(1) NOT NULL DEFAULT 1,
  `status` ENUM('In Review', 'Approved', 'Rejected') NOT NULL DEFAULT 'In Review',
  `assigned_engineer` VARCHAR(100) NOT NULL DEFAULT 'Jonas Richter (EMP-1020)',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table 7: `developer_guides`
-- Statutory specifications & protocols for guides.php
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `developer_guides`;
CREATE TABLE `developer_guides` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `guide_code` VARCHAR(50) NOT NULL UNIQUE,
  `section_number` INT(11) NOT NULL DEFAULT 1,
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL DEFAULT 'Security',
  `classification` VARCHAR(50) NOT NULL DEFAULT 'Confidential',
  `icon` VARCHAR(50) NOT NULL DEFAULT 'lock',
  `summary` TEXT NOT NULL,
  `code_snippet` TEXT NULL,
  `footer_note` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SEED DATA INSERTION (Populating real data to replace hardcoded copy)
-- =====================================================================

-- 1. Insert Endpoints
INSERT INTO `developer_endpoints` (`endpoint_slug`, `method`, `path`, `title`, `description`, `classification`, `rate_limit`, `target_hardware`, `parameters_json`, `curl_snippet`, `python_snippet`, `node_snippet`, `go_snippet`, `is_active`) VALUES
('endpoint-optical', 'GET', '/v1/sensors/optical/telemetry', 'PROD-1001 Optical Sensor Package Telemetry', 'Retrieves high-frequency telemetry streams from field-deployed optical inspection and sensor apparatus (PROD-1001), including spectral resolution peak, focal plane operating temperature, and signal-to-noise ratio.', 'Internal • PROD-1001', '10k/min', 'PROD-1001', 
'[{"name":"device_id","type":"string","required":true,"description":"Assigned hardware serial or ID (e.g. PROD-1001-KZ)"},{"name":"sample_window_sec","type":"integer","required":false,"description":"Window for rolling average (1 to 60, default: 5)"}]',
'curl -X GET "https://developer.vostokpribor.local/v1/sensors/optical/telemetry?device_id=PROD-1001-KZ" \\\n  -H "Authorization: Bearer vk_live_9a41c2e8f10b" \\\n  -H "Accept: application/json"',
'import requests\n\nurl = "https://developer.vostokpribor.local/v1/sensors/optical/telemetry"\nheaders = {\n    "Authorization": "Bearer vk_live_9a41c2e8f10b",\n    "Accept": "application/json"\n}\nparams = {"device_id": "PROD-1001-KZ"}\n\nresponse = requests.get(url, headers=headers, params=params)\ndata = response.json()\nprint("Wavelength Peak (nm):", data["spectral_resolution_nm"])',
'const fetch = require("node-fetch");\n\nasync function getOpticalTelemetry() {\n  const url = new URL("https://developer.vostokpribor.local/v1/sensors/optical/telemetry");\n  url.searchParams.set("device_id", "PROD-1001-KZ");\n\n  const res = await fetch(url, {\n    headers: {\n      "Authorization": "Bearer vk_live_9a41c2e8f10b",\n      "Accept": "application/json"\n    }\n  });\n  const data = await res.json();\n  console.log(data);\n}\ngetOpticalTelemetry();',
'package main\n\nimport (\n    "fmt"\n    "net/http"\n    "io"\n)\n\nfunc main() {\n    url := "https://developer.vostokpribor.local/v1/sensors/optical/telemetry?device_id=PROD-1001-KZ"\n    req, _ := http.NewRequest("GET", url, nil)\n    req.Header.Set("Authorization", "Bearer vk_live_9a41c2e8f10b")\n\n    client := &http.Client{}\n    resp, err := client.Do(req)\n    if err != nil { panic(err) }\n    defer resp.Body.Close()\n\n    body, _ := io.ReadAll(resp.Body)\n    fmt.Println(string(body))\n}',
1),

('endpoint-geodetic', 'GET', '/v1/devices/geodetic/measurements', 'PROD-1002 Precision Geodetic Measurement Kit Vectors', 'Provides distance vectors, laser interferometer precision readings, and atmospheric refraction indices for geodetic surveying instrumentation deployed with CUS-1001 (Aral Geomatics) and CUS-1002 (BaltNord).', 'Internal • PROD-1002', '5k/min', 'PROD-1002',
'[{"name":"unit","type":"string","required":true,"description":"Hardware device identifier (e.g. PROD-1002)"},{"name":"include_raw","type":"boolean","required":false,"description":"Flag to append raw interferometer telemetry frames"}]',
'curl -X GET "https://developer.vostokpribor.local/v1/devices/geodetic/measurements?unit=PROD-1002" \\\n  -H "Authorization: Bearer vk_live_9a41c2e8f10b"',
'import requests\n\nurl = "https://developer.vostokpribor.local/v1/devices/geodetic/measurements"\nheaders = {"Authorization": "Bearer vk_live_9a41c2e8f10b"}\nparams = {"unit": "PROD-1002"}\n\nresp = requests.get(url, headers=headers, params=params)\nprint("Calibration Status:", resp.json()["calibration_valid"])',
'const res = await fetch("https://developer.vostokpribor.local/v1/devices/geodetic/measurements?unit=PROD-1002", {\n  headers: { "Authorization": "Bearer vk_live_9a41c2e8f10b" }\n});\nconsole.log(await res.json());',
'package main\n\nimport "net/http"\n\nfunc main() {\n  req, _ := http.NewRequest("GET", "https://developer.vostokpribor.local/v1/devices/geodetic/measurements?unit=PROD-1002", nil)\n  req.Header.Set("Authorization", "Bearer vk_live_9a41c2e8f10b")\n}',
1),

('endpoint-scada', 'POST', '/v1/scada/ingest/frames', 'PROD-1004 SCADA High-Speed Ingestion Bridge', 'Ingests Modbus-TCP, OPC-UA, and telemetry frames directly into System 11 Ingestion Bridges with microsecond timestamp validation.', 'Confidential • PROD-1004', '50k/min', 'PROD-1004',
'[{"name":"facility_id","type":"string","required":true,"description":"Enclave node code (e.g. ALMATY-CENTRAL-01)"},{"name":"protocol","type":"string","required":true,"description":"MODBUS-TCP, OPC-UA, or PROFINET"},{"name":"payload_hex","type":"hex-string","required":true,"description":"Raw industrial telemetry frame"}]',
'curl -X POST "https://developer.vostokpribor.local/v1/scada/ingest/frames" \\\n  -H "Authorization: Bearer vk_live_9a41c2e8f10b" \\\n  -H "Content-Type: application/json" \\\n  -d \'{\n    "facility_id": "ALMATY-CENTRAL-01",\n    "protocol": "MODBUS-TCP",\n    "plc_register": "40001",\n    "payload_hex": "0A2B4C"\n  }\'',
'import requests\n\npayload = {\n    "facility_id": "ALMATY-CENTRAL-01",\n    "protocol": "MODBUS-TCP",\n    "plc_register": "40001",\n    "payload_hex": "0A2B4C"\n}\nheaders = {"Authorization": "Bearer vk_live_9a41c2e8f10b"}\nresp = requests.post("https://developer.vostokpribor.local/v1/scada/ingest/frames", json=payload, headers=headers)\nprint("Ingestion Receipt:", resp.json()["frame_ack"])',
'const payload = {\n  facility_id: "ALMATY-CENTRAL-01",\n  protocol: "MODBUS-TCP",\n  plc_register: "40001",\n  payload_hex: "0A2B4C"\n};\nconst res = await fetch("https://developer.vostokpribor.local/v1/scada/ingest/frames", {\n  method: "POST",\n  headers: { "Authorization": "Bearer vk_live_9a41c2e8f10b", "Content-Type": "application/json" },\n  body: JSON.stringify(payload)\n});',
'package main\n\nimport "net/http"\n\nfunc main() {\n  // SCADA high-speed frame dispatch in Go\n  http.Post("https://developer.vostokpribor.local/v1/scada/ingest/frames", "application/json", nil)\n}',
1);

-- 2. Insert API Keys
INSERT INTO `developer_api_keys` (`key_identifier`, `label`, `partner_id`, `partner_name`, `token_prefix`, `token_full`, `environment`, `rate_limit`, `rate_limit_value`, `classification`, `scopes`, `status`, `usage_count`) VALUES
('KEY-9842', 'BaltNord Primary ERP Sync', 'CUS-1002', 'BaltNord Process Systems', 'vk_live_9a41c2e8', 'vk_live_9a41c2e8f10b7a89d4e12c5b38af1009', 'Production', '10,000 req/min', 10000, 'Confidential', 'telemetry:read,scada:ingest,orders:write', 'Active', 42890),
('KEY-4109', 'BaltNord QA / Sandbox Ingestion', 'CUS-1002', 'BaltNord Process Systems', 'vk_test_3f7b99c1', 'vk_test_3f7b99c1e04a88bc92d110fc6e7a2014', 'Sandbox', '2,500 req/min', 2500, 'Internal QA', 'telemetry:read,scada:ingest', 'Active', 12450),
('KEY-4419', 'IoT Sensor Continuous Telemetry Pipeline', 'CUS-1001', 'Aral Geomatics Automation Labs', 'vk_live_7e810a9c', 'vk_live_7e810a9cf29188e7b4119d45e99aa871', 'Production', '50,000 req/min', 50000, 'Confidential', 'telemetry:read', 'Active', 382100),
('KEY-1108', 'Almaty Logistics Inbound Feeder', 'CUS-1003', 'Steppe Mining SCADA Engineering', 'vk_live_2b9044cc', 'vk_live_2b9044cc4e1178a9c2288019aa673199', 'Production', '10,000 req/min', 10000, 'Internal', 'scada:ingest', 'Active', 14350);

-- 3. Insert Sandbox Presets
INSERT INTO `developer_sandbox_presets` (`title`, `method`, `url`, `sample_body`, `description`) VALUES
('PROD-1001 Optical Telemetry', 'GET', '/v1/sensors/optical/telemetry?device_id=PROD-1001-KZ', '', 'Fetch real-time spectral resolution, focal temp, and SNR ratio'),
('PROD-1002 Geodetic Vectors', 'GET', '/v1/devices/geodetic/measurements?unit=PROD-1002', '', 'Interferometer calibration vector and atmospheric refraction indices'),
('PROD-1004 SCADA Frame', 'POST', '/v1/scada/ingest/frames', '{\n  "facility_id": "ALMATY-CENTRAL-01",\n  "protocol": "MODBUS-TCP",\n  "plc_register": "40001",\n  "payload_hex": "0A2B4C"\n}', 'Direct Modbus/OPC-UA industrial telemetry frame dispatch'),
('B2B Order Create', 'POST', '/v1/b2b/orders/create', '{\n  "customer_id": "CUS-1002",\n  "items": [\n    {"prod_id": "PROD-1001", "qty": 4},\n    {"prod_id": "PROD-1004", "qty": 2}\n  ]\n}', 'B2B equipment procurement order generation with invoice ref');

-- 4. Insert Sandbox Logs
INSERT INTO `developer_sandbox_logs` (`method`, `url`, `request_body`, `status_code`, `response_time_ms`, `response_size`, `response_body`) VALUES
('GET', '/v1/sensors/optical/telemetry?device_id=PROD-1001-KZ', NULL, 200, 24, '842 B', '{\n  "device_id": "PROD-1001-KZ",\n  "sensor_series": "Industrial Optical Sensor Package",\n  "calibration_epoch": 1789128000,\n  "station": "ALMATY-CENTRAL",\n  "telemetry": {\n    "spectral_resolution_nm": 0.04,\n    "focal_plane_temp_c": 18.2,\n    "dispersion_coefficient": 1.0024,\n    "optical_throughput_percent": 99.82,\n    "snr_db": 68.4\n  },\n  "status": "NOMINAL_OPERATIONAL",\n  "jurisdiction_merkle_root": "0x4a8c911f...c892"\n}'),
('GET', '/v1/devices/geodetic/measurements?unit=PROD-1002', NULL, 200, 31, '710 B', '{\n  "unit_id": "PROD-1002-UST-04",\n  "apparatus": "Precision Geodetic Measurement Kit",\n  "laser_interferometer": "STABLE",\n  "azimuth_arcsec": 142.8812,\n  "zenith_angle_deg": 44.1029,\n  "distance_vector_meters": 1840.4502,\n  "refraction_index": 1.000277,\n  "calibration_valid": true\n}'),
('POST', '/v1/scada/ingest/frames', '{"facility_id":"ALMATY-CENTRAL-01","protocol":"MODBUS-TCP","plc_register":"40001","payload_hex":"0A2B4C"}', 201, 18, '412 B', '{\n  "frame_ack": "ACK-SCADA-89102",\n  "facility_id": "ALMATY-CENTRAL-01",\n  "protocol": "MODBUS-TCP",\n  "buffered_lines": 1,\n  "ring_buffer_utilization": "14%",\n  "audit_escrow_timestamp": 1789128842\n}');

-- 5. Insert Webhooks
INSERT INTO `developer_webhooks` (`delivery_id`, `event_type`, `target_endpoint`, `status_code`, `latency_ms`, `status`, `classification`, `payload`, `created_at`) VALUES
('WH-2026-9081', 'telemetry.vibration.alert', 'https://api.baltnord.lv/v1/vostok/events', '200 OK', 42, 'Delivered', 'Internal', '{"event": "vibration_threshold_exceeded", "device": "PROD-1001-KZ", "amplitude_g": 4.12}', '2026-09-11 16:42:10'),
('WH-2026-9080', 'order.status.dispatched', 'https://api.baltnord.lv/v1/vostok/orders', '200 OK', 38, 'Delivered', 'Internal', '{"order_id": "ORD-2026-9904", "status": "DISPATCHED", "carrier": "Trans-Caspian Freight"}', '2026-09-11 15:18:22'),
('WH-2026-9079', 'scada.emergency.trip', 'https://gateway.almaty-logistics.kz/wh', '200 OK', 18, 'Delivered', 'Confidential', '{"facility": "ALMATY-CENTRAL-01", "circuit": "FEEDER-04", "trip_reason": "THERMAL_OVERLOAD"}', '2026-09-11 14:05:01'),
('WH-2026-9078', 'telemetry.pressure.warning', 'https://api.baltnord.lv/v1/vostok/events', '504 TIMEOUT', 3002, 'Failed', 'Confidential', '{"sensor": "BARO-991", "reading_kpa": 1042.8}', '2026-09-11 12:30:15'),
('WH-2026-9077', 'catalog.price_index.updated', 'https://b2b.vostokpribor.local/sync', '200 OK', 24, 'Delivered', 'Internal', '{"catalog_version": "2026.4", "items_updated": 142}', '2026-09-11 10:15:44');

-- 6. Insert Partner Applications
INSERT INTO `developer_partner_applications` (`ticket_id`, `company_name`, `partner_id`, `contact_name`, `contact_email`, `project_ref`, `target_environment`, `requested_scopes`, `public_key`, `compliance_doc`, `compliance_iec`, `compliance_nda`, `status`, `assigned_engineer`, `created_at`) VALUES
('ENCLAVE-REQ-981244', 'BaltNord Process Systems', 'CUS-1002', 'Kristaps Ozols', 'kristaps.ozols@baltnord.lv', 'PRJ-2026-002 (Refinery Flow Monitoring & SCADA Bridge)', 'sandbox', 'telemetry:read, orders:read_write', 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIBmQ8eBaltNordScadaGatewayKey2026', 1, 1, 1, 'In Review', 'Jonas Richter (EMP-1020)', '2026-09-11 09:12:00'),
('ENCLAVE-REQ-842109', 'Aral Geomatics Automation Labs', 'CUS-1001', 'Bauyrzhan Nurgaliyev', 'b.nurgaliyev@aral-geomatics.kz', 'PRJ-2026-001 (Geodetic Interferometer Grid Sync)', 'production', 'telemetry:read, actuator:write, audit:read', 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAICa5f82k09AralLabsKey2026', 1, 1, 1, 'Approved', 'Dana Yermak (EMP-1017)', '2026-09-08 14:30:00'),
('ENCLAVE-REQ-710293', 'Steppe Mining SCADA Engineering', 'CUS-1003', 'Aigul Sadykova', 'sadykova@steppemining.kz', 'PRJ-2026-003 (Autonomous Conveyor PLC Ingestion)', 'production', 'telemetry:read, orders:read_write', 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIK77d939SteppeKey2026', 1, 1, 1, 'Approved', 'Jonas Richter (EMP-1020)', '2026-09-05 11:20:00');

-- 7. Insert Guides
INSERT INTO `developer_guides` (`guide_code`, `section_number`, `title`, `category`, `classification`, `icon`, `summary`, `code_snippet`, `footer_note`) VALUES
('DOC-2026-010', 0, 'DOC-2026-010: API_Integration_Guide.pdf', 'Statutory Specification', 'Internal Use', 'description',
'Authoritative Baseline Specification • System 10 Reference Document.\nDefines cryptographic and telemetry contracts between client ERP systems (e.g. CUS-1002 BaltNord) and System 11 Admin & Governance pipelines. Mandates HMAC SHA-256 signatures on all webhooks.',
'SHA256: e8b94109ca82d90f23b7a1884c9820f121d5a7114b09e20a39c12b7a90f14d82\nSPEC_REVISION: 2026.4 // IEC 62443 L3 ATTESTED',
'Signatory Verification: Lead Developer Jonas Richter (EMP-1020) and Integration Engineer Dana Yermak (EMP-1017). Attested under ISO 27001 & ST RK IEC 62443.'),

('DOC-010-AUTH', 1, '1. Authentication & Token Scoping', 'Security', 'Confidential', 'lock', 
'All API requests require an HTTP Authorization header containing a bearer token issued through the Partner Credentials Vault. Tokens must be signed with ed25519 or mTLS client certificates.',
'Authorization: Bearer vk_live_9a41c2e8f10b7a89d4e12c5',
'Production keys expire every 90 days. Systems automatically reject tokens lacking valid IP whitelisting configured in the Credentials Vault.'),

('DOC-010-ERP', 2, '2. ERP Integration Standard (SAP / 1C:Enterprise / Dynamics)', 'ERP Standard', 'Internal Standard', 'sync_alt',
'Architectural standard to synchronize purchase orders, equipment fulfillment stages, and billing events directly with corporate accounting software.\n1. Register Webhook Target: Provide your HTTPS endpoint in the Webhooks console with TLS 1.3 encryption.\n2. Order Matching: Align commercial opportunities from System 05 (CRM) with Order IDs in System 02 (E-Commerce).\n3. Invoice Reconciliation: Track payments against System 07 (Finance & Billing) reference numbers (e.g. INV-2026-002).',
'POST /v1/b2b/orders/create\n{\n  "customer_id": "CUS-1002",\n  "invoice_ref": "INV-2026-002",\n  "erp_system": "SAP-S4HANA"\n}',
'Mandates TLS 1.3 encryption and bidirectional payload validation against JSON schema DOC-2026-010.'),

('DOC-010-SCADA', 3, '3. SCADA Real-Time Telemetry Pipeline (Modbus & OPC-UA)', 'SCADA Telemetry', 'Public Spec', 'sensors',
'Industrial equipment deployed with customer facilities transmits operational frames at up to 64,800 events per second. Use the high-speed batch endpoint /v1/scada/ingest/frames or connect directly to the Almaty WebSocket stream.',
'wss://developer.vostokpribor.local/v1/stream/scada/feed?facility=ALMATY-01',
'Conforms to Republic Heavy Automation Standards ST RK IEC 62443-4-2.');
