-- Migration 012: Create customer_service_requests table
-- Idempotent schema migration for Customer Portal Service Requests

CREATE TABLE IF NOT EXISTS `customer_service_requests` (
  `request_id` varchar(20) NOT NULL,
  `cus_id` varchar(10) NOT NULL,
  `service_type` varchar(100) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `facility_location` varchar(200) DEFAULT NULL,
  `priority` enum('Standard','High','Critical') DEFAULT 'Standard',
  `requested_date` date DEFAULT NULL,
  `status` enum('Submitted','Under Review','Personnel Assigned','In Progress','Completed','Cancelled') DEFAULT 'Submitted',
  `assigned_emp_id` varchar(10) DEFAULT NULL,
  `assigned_at` datetime DEFAULT NULL,
  `hr_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`request_id`),
  KEY `idx_csr_cus_id` (`cus_id`),
  KEY `idx_csr_assigned_emp` (`assigned_emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `customer_service_requests` (`request_id`, `cus_id`, `service_type`, `title`, `description`, `facility_location`, `priority`, `requested_date`, `status`, `assigned_emp_id`, `assigned_at`, `hr_notes`, `created_at`, `updated_at`) VALUES
('SRV-2026-001', 'CUS-1001', 'Calibration & Metrology', 'High-Pressure Turbine Sensor On-Site Recalibration', 'Annual recalibration for 14 VP-SPT-900 pressure transmitters in Bay 3.', 'Atyrau Refining Facility, Unit 4', 'High', '2026-10-15', 'Personnel Assigned', 'EMP-1004', '2026-09-28 10:30:00', 'Assigned Senior Metrologist Elena Morozova for dispatch.', '2026-09-27 11:20:00', '2026-09-30 00:23:28'),
('SRV-2026-002', 'CUS-1002', 'Telemetry & SCADA Integration', 'Modbus TCP Telemetry Gateway Firmware Commissioning', 'Integrate new fiber optic telemetry gateways with remote monitoring station.', 'Aktau Maritime Terminal, Berth 2', 'Critical', '2026-10-08', 'Under Review', NULL, NULL, 'Pending HR engineer assignment based on offshore clearance.', '2026-09-29 06:15:00', '2026-09-30 00:23:28'),
('SRV-2026-003', 'CUS-1003', 'Preventative Maintenance', 'Biannual Flow Meter Diagnostic & Seal Inspection', 'Routine preventative diagnostic on ultrasonic flow measurement lines.', 'Karaganda Metallurgy Complex', 'Standard', '2026-10-22', 'Submitted', NULL, NULL, 'Queued for operational schedule review.', '2026-09-29 13:40:00', '2026-09-30 00:23:28')
ON DUPLICATE KEY UPDATE title=VALUES(title);
