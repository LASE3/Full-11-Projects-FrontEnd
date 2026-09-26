<?php
/**
 * Class 3: Customer Portal - Projects API (with Anti-IDOR Tenant Enforcement)
 * Location: api/v1/customer/projects.php
 * Methods: GET
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../helpers/Response.php';
require_once __DIR__ . '/../../helpers/I18n.php';
require_once __DIR__ . '/../../helpers/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$cusId = $_SESSION['cus_id'] ?? ($_GET['cus_id'] ?? 'CUS-1001');
$lang  = $_GET['lang'] ?? 'en';
$prjId = $_GET['id'] ?? null;

try {
    if ($prjId) {
        // 1. Check ownership (Anti-IDOR Protection)
        $checkStmt = $pdo->prepare("SELECT cus_id FROM projects WHERE prj_id = :pid");
        $checkStmt->execute([':pid' => $prjId]);
        $owner = $checkStmt->fetchColumn();

        if ($owner === false) {
            Response::error("Project not found.", 404);
        }

        if ($owner !== $cusId) {
            // Log security incident for Cyber Range tracking
            AuditLogger::logSecurityEvent(
                'IDOR_CROSS_TENANT_PROBE',
                'CUS',
                "Customer {$cusId} probed Project {$prjId} belonging to {$owner}.",
                'High',
                null,
                $cusId
            );
            Response::error("Forbidden: You do not have permission to view this project.", 403);
        }

        // 2. Fetch Project Detail
        $stmt = $pdo->prepare("
            SELECT 
                p.prj_id, p.budget, p.currency, p.status, p.start_date, p.end_date,
                e.full_name AS project_manager_name,
                e.email AS project_manager_email,
                e.job_title AS project_manager_title
            FROM projects p
            LEFT JOIN employees e ON p.project_manager_emp_id = e.emp_id
            WHERE p.prj_id = :pid AND p.cus_id = :cid
        ");
        $stmt->execute([':pid' => $prjId, ':cid' => $cusId]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        $project['status_display'] = I18n::translate($project['status'], $lang);

        // 3. Milestones / Billing Cycles
        $cyclesStmt = $pdo->prepare("
            SELECT cycle_id, milestone_description, scheduled_date, invoiced
            FROM billing_cycles
            WHERE prj_id = :pid
            ORDER BY scheduled_date ASC
        ");
        $cyclesStmt->execute([':pid' => $prjId]);
        $project['milestones'] = $cyclesStmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. Linked Invoices
        $invStmt = $pdo->prepare("
            SELECT inv_id, total_value, currency, payment_status, issued_at, paid_at
            FROM invoices
            WHERE prj_id = :pid
        ");
        $invStmt->execute([':pid' => $prjId]);
        $invoices = $invStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($invoices as &$inv) {
            $inv['status_display'] = I18n::translate($inv['payment_status'], $lang);
        }
        $project['invoices'] = $invoices;

        // 5. Technical Documents
        $docStmt = $pdo->prepare("
            SELECT doc_id, file_name, classification, created_at
            FROM documents
            WHERE related_prj_id = :pid
        ");
        $docStmt->execute([':pid' => $prjId]);
        $project['documents'] = $docStmt->fetchAll(PDO::FETCH_ASSOC);

        Response::success($project, "Project dossier loaded successfully");
    } else {
        // List all projects belonging to customer
        $stmt = $pdo->prepare("
            SELECT 
                p.prj_id,
                p.budget,
                p.currency,
                p.status,
                p.start_date,
                p.end_date,
                e.full_name AS project_manager_name,
                (SELECT COUNT(*) FROM invoices WHERE prj_id = p.prj_id) AS invoices_count,
                (SELECT COUNT(*) FROM billing_cycles WHERE prj_id = p.prj_id) AS milestones_count
            FROM projects p
            LEFT JOIN employees e ON p.project_manager_emp_id = e.emp_id
            WHERE p.cus_id = :cid
            ORDER BY p.prj_id ASC
        ");
        $stmt->execute([':cid' => $cusId]);
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($projects as &$p) {
            $p['status_display'] = I18n::translate($p['status'], $lang);
        }

        Response::success($projects, "Customer projects retrieved");
    }
} catch (Exception $e) {
    Response::error("Failed to load projects: " . $e->getMessage(), 500);
}
