-- Migration 013: Extend invoices and tickets status enums
-- Supports voiding invoices and closing tickets across SuperAdminConsole and Finance API

ALTER TABLE `invoices` MODIFY COLUMN `payment_status` ENUM('Paid','Pending','Overdue','Void') NOT NULL DEFAULT 'Pending';
ALTER TABLE `tickets` MODIFY COLUMN `status` ENUM('Open','InProgress','Investigating','Escalated','Resolved','Closed') NOT NULL DEFAULT 'Open';
