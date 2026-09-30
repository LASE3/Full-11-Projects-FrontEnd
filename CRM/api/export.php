<?php
declare(strict_types=1);

/**
 * Class 5: CRM Platform - Live Database CSV & JSON Data Export Engine
 * Location: CRM/api/export.php
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/I18n.php';
require_once __DIR__ . '/helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$empId = $_SESSION['emp_id'] ?? ($_SESSION['vostok_user']['emp_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? 'EMP-1006'));

$type   = strtolower(trim((string)($_GET['type'] ?? 'leads')));
$format = strtolower(trim((string)($_GET['format'] ?? 'csv')));
$cusId  = trim((string)($_GET['id'] ?? ''));

$timestamp = date('Y-m-d_His');
$filename  = "vostok_crm_{$type}_{$timestamp}";

try {
    $rows = [];

    switch ($type) {
        case 'leads':
            $stmt = $pdo->query("
                SELECT 
                    lead_id AS 'Lead ID',
                    company_name AS 'Enterprise Company',
                    full_name AS 'Contact Name',
                    email AS 'Email',
                    phone AS 'Phone',
                    equipment_scope AS 'Target Hardware Scope',
                    status AS 'Status',
                    lead_score AS 'Qualification Score',
                    estimated_value AS 'Estimated Deal Value (USD)',
                    priority AS 'Priority',
                    source_page AS 'Intake Channel',
                    created_at AS 'Date Logged'
                FROM `leads`
                ORDER BY lead_id DESC
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filename = "leads_export_{$timestamp}";
            break;

        case 'customers':
            $stmt = $pdo->query("
                SELECT 
                    cus_id AS 'Account ID',
                    company_name AS 'Enterprise Account',
                    sector AS 'Industrial Sector',
                    account_tier AS 'Account Tier',
                    health_score AS 'Account Health (%)',
                    primary_contact_name AS 'Primary Contact',
                    primary_contact_email AS 'Contact Email',
                    phone AS 'Phone',
                    headquarters AS 'Facility Headquarters',
                    status AS 'Status',
                    onboarded_at AS 'Onboarding Date'
                FROM `customers`
                ORDER BY cus_id ASC
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filename = "customers_export_{$timestamp}";
            break;

        case 'opportunities':
            $stmt = $pdo->query("
                SELECT 
                    o.opp_id AS 'Opp ID',
                    o.opp_title AS 'Deal Title',
                    o.cus_id AS 'Account ID',
                    COALESCE(c.company_name, 'Enterprise Plant') AS 'Company Name',
                    o.stage AS 'Sales Stage',
                    o.estimated_value AS 'Estimated Value (USD)',
                    o.probability_percent AS 'Win Probability (%)',
                    o.expected_close_date AS 'Expected Close',
                    IF(o.is_confidential = 1, 'CONFIDENTIAL', 'Standard') AS 'Classification',
                    o.priority AS 'Priority'
                FROM `opportunities` o
                LEFT JOIN `customers` c ON o.cus_id = c.cus_id
                ORDER BY o.opp_id DESC
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filename = "opportunities_export_{$timestamp}";
            break;

        case 'contracts':
            $stmt = $pdo->query("
                SELECT 
                    contract_id AS 'System ID',
                    contract_ref AS 'Contract Reference',
                    cus_id AS 'Account ID',
                    title AS 'Contract Title',
                    contract_value AS 'Contract Value (USD)',
                    eds_status AS 'EDS Ratification',
                    status AS 'Execution Status',
                    start_date AS 'Effective Date',
                    end_date AS 'Termination Date',
                    confidentiality_level AS 'Classification'
                FROM `contracts`
                ORDER BY contract_id DESC
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filename = "contracts_audit_export_{$timestamp}";
            break;

        case 'quotes':
            $stmt = $pdo->query("
                SELECT 
                    q.quote_id AS 'Quote ID',
                    q.quote_ref AS 'Quote Reference',
                    q.cus_id AS 'Account ID',
                    COALESCE(c.company_name, 'Client') AS 'Account Name',
                    q.equipment_scope AS 'Equipment Scope',
                    q.quantity AS 'Units',
                    q.unit_price AS 'Unit Price (USD)',
                    q.total_amount AS 'Total Price (USD)',
                    q.valid_until AS 'Validity Deadline',
                    q.status AS 'Delivery Status',
                    q.created_at AS 'Delivered Date'
                FROM `quotes` q
                LEFT JOIN `customers` c ON q.cus_id = c.cus_id
                ORDER BY q.quote_id DESC
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filename = "quotes_export_{$timestamp}";
            break;

        case 'projects':
            $stmt = $pdo->query("
                SELECT 
                    p.prj_id AS 'Project Code',
                    p.project_name AS 'Engineering Scope',
                    p.cus_id AS 'Account ID',
                    COALESCE(c.company_name, 'Industrial Client') AS 'Facility Plant',
                    p.status AS 'Commissioning Stage',
                    p.progress_percent AS 'FAT Completion (%)',
                    p.budget AS 'Committed Budget (USD)',
                    p.facility_location AS 'Deployment Location',
                    p.start_date AS 'Commissioning Start',
                    p.end_date AS 'Target Go-Live'
                FROM `projects` p
                LEFT JOIN `customers` c ON p.cus_id = c.cus_id
                ORDER BY p.prj_id DESC
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filename = "projects_gantt_matrix_{$timestamp}";
            break;

        case 'forecast':
            $stmt = $pdo->query("
                SELECT 
                    forecast_id AS 'Model ID',
                    period AS 'Quarter / Period',
                    target_quota AS 'Target Quota (USD)',
                    actual_amount AS 'Achieved Amount (USD)',
                    ROUND((actual_amount / NULLIF(target_quota, 0)) * 100, 1) AS 'Attainment Rate (%)',
                    notes AS 'Forecasting Notes'
                FROM `sales_forecasts`
                ORDER BY forecast_id DESC
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filename = "financial_forecast_model_{$timestamp}";
            break;

        case 'customer':
            if ($cusId === '') {
                Response::error("Customer ID required for dossier export", 422);
            }
            $stmtC = $pdo->prepare("SELECT * FROM `customers` WHERE `cus_id` = ?");
            $stmtC->execute([$cusId]);
            $cust = $stmtC->fetch(PDO::FETCH_ASSOC);
            if (!$cust) {
                Response::error("Customer not found", 404);
            }
            $stmtCon = $pdo->prepare("SELECT * FROM `contracts` WHERE `cus_id` = ?");
            $stmtCon->execute([$cusId]);
            $contracts = $stmtCon->fetchAll(PDO::FETCH_ASSOC);

            $stmtPrj = $pdo->prepare("SELECT * FROM `projects` WHERE `cus_id` = ?");
            $stmtPrj->execute([$cusId]);
            $projects = $stmtPrj->fetchAll(PDO::FETCH_ASSOC);

            $dossier = [
                'account'   => $cust,
                'contracts' => $contracts,
                'projects'  => $projects,
                'exported_at' => date('Y-m-d H:i:s')
            ];

            if ($format === 'json') {
                header('Content-Type: application/json; charset=utf-8');
                header("Content-Disposition: attachment; filename=\"dossier_{$cusId}_{$timestamp}.json\"");
                echo json_encode($dossier, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                exit;
            } else {
                $rows = [$cust];
                $filename = "dossier_{$cusId}_{$timestamp}";
            }
            break;

        default:
            Response::error("Invalid export type: {$type}", 400);
    }

    AuditLogger::logAction($empId, $cusId ?: null, 'CRM Platform', 'CRM', 'EXPORT_DATA', $type, null, ['type' => $type, 'count' => count($rows), 'format' => $format], 'SUCCESS');

    if ($format === 'json') {
        header('Content-Type: application/json; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}.json\"");
        echo json_encode([
            'success'     => true,
            'export_type' => $type,
            'count'       => count($rows),
            'generated_at'=> date('Y-m-d H:i:s'),
            'data'        => $rows
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Output CSV
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"{$filename}.csv\"");
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    // UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    if (!empty($rows)) {
        fputcsv($output, array_keys($rows[0]), ',', '"', '\\');
        foreach ($rows as $row) {
            fputcsv($output, $row, ',', '"', '\\');
        }
    } else {
        fputcsv($output, ['No records found in database'], ',', '"', '\\');
    }

    fclose($output);
    exit;

} catch (Throwable $e) {
    Response::error("Export failed: " . $e->getMessage(), 500);
}
