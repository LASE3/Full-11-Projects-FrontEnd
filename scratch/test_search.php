<?php
require_once __DIR__ . '/../config/db.php';
$pdo = getDbConnection();

echo "--- CHECKING CRM/api/search.php queries ---\n";
try {
    $term = '%test%';
    $cStmt = $pdo->prepare("
        SELECT cus_id as id, company_name as title, sector as subtitle, 'Customer' as type, CONCAT('CustomerDetail.php?id=', cus_id) as url 
        FROM `customers` 
        WHERE company_name LIKE :t1 OR sector LIKE :t2 OR cus_id LIKE :t3 
        LIMIT 5
    ");
    $cStmt->execute([':t1' => $term, ':t2' => $term, ':t3' => $term]);
    echo "1. Customers OK\n";

    $oStmt = $pdo->prepare("
        SELECT opp_id as id, opp_title as title, CONCAT('$', FORMAT(estimated_value, 2)) as subtitle, 'Opportunity' as type, 'Opportunities.php' as url 
        FROM `opportunities` 
        WHERE opp_title LIKE :t1 OR stage LIKE :t2 
        LIMIT 5
    ");
    $oStmt->execute([':t1' => $term, ':t2' => $term]);
    echo "2. Opportunities OK\n";

    $lStmt = $pdo->prepare("
        SELECT lead_id as id, company_name as title, full_name as subtitle, 'Lead' as type, 'Leads.php' as url 
        FROM `leads` 
        WHERE company_name LIKE :t1 OR full_name LIKE :t2 OR equipment_scope LIKE :t3 
        LIMIT 5
    ");
    $lStmt->execute([':t1' => $term, ':t2' => $term, ':t3' => $term]);
    echo "3. Leads OK\n";

    $conStmt = $pdo->prepare("
        SELECT contract_id as id, contract_ref as title, title as subtitle, 'Contract' as type, 'QuotesAndContracts.php' as url 
        FROM `contracts` 
        WHERE contract_ref LIKE :t1 OR title LIKE :t2 
        LIMIT 5
    ");
    $conStmt->execute([':t1' => $term, ':t2' => $term]);
    echo "4. Contracts OK\n";

    $pStmt = $pdo->prepare("
        SELECT prj_id as id, project_name as title, facility_location as subtitle, 'Project' as type, 'Projects.php' as url 
        FROM `projects` 
        WHERE project_name LIKE :t1 OR prj_id LIKE :t2 
        LIMIT 5
    ");
    $pStmt->execute([':t1' => $term, ':t2' => $term]);
    echo "5. Projects OK\n";

} catch (Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}
