<?php

declare(strict_types=1);

/**
 * Class 5: CRM Platform - Global Omni-Search API
 * Location: CRM/api/search.php
 * Methods: GET
 */
require_once __DIR__ . '/../../includes/api_bootstrap.php';
$_vp_user = vp_api_guard('CRM', []);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/helpers/Response.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();
$q = trim((string)($_GET['q'] ?? ($_GET['query'] ?? '')));

if ($q === '') {
    Response::success([], 'Empty search query');
}

try {
    $results = [];
    $term = "%{$q}%";

    // 1. Customers
    $cStmt = $pdo->prepare("
        SELECT cus_id as id, company_name as title, sector as subtitle, 'Customer' as type, CONCAT('CustomerDetail.php?id=', cus_id) as url 
        FROM `customers` 
        WHERE company_name LIKE :t1 OR sector LIKE :t2 OR cus_id LIKE :t3 
        LIMIT 5
    ");
    $cStmt->execute([':t1' => $term, ':t2' => $term, ':t3' => $term]);
    $results = array_merge($results, $cStmt->fetchAll(PDO::FETCH_ASSOC));

    // 2. Opportunities
    $oStmt = $pdo->prepare("
        SELECT opp_id as id, opp_title as title, CONCAT('$', FORMAT(estimated_value, 2)) as subtitle, 'Opportunity' as type, 'Opportunities.php' as url 
        FROM `opportunities` 
        WHERE opp_title LIKE :t1 OR stage LIKE :t2 
        LIMIT 5
    ");
    $oStmt->execute([':t1' => $term, ':t2' => $term]);
    $results = array_merge($results, $oStmt->fetchAll(PDO::FETCH_ASSOC));

    // 3. Leads
    $lStmt = $pdo->prepare("
        SELECT lead_id as id, company_name as title, full_name as subtitle, 'Lead' as type, 'Leads.php' as url 
        FROM `leads` 
        WHERE company_name LIKE :t1 OR full_name LIKE :t2 OR equipment_scope LIKE :t3 
        LIMIT 5
    ");
    $lStmt->execute([':t1' => $term, ':t2' => $term, ':t3' => $term]);
    $results = array_merge($results, $lStmt->fetchAll(PDO::FETCH_ASSOC));

    // 4. Contracts
    $conStmt = $pdo->prepare("
        SELECT contract_id as id, contract_ref as title, title as subtitle, 'Contract' as type, 'QuotesAndContracts.php' as url 
        FROM `contracts` 
        WHERE contract_ref LIKE :t1 OR title LIKE :t2 
        LIMIT 5
    ");
    $conStmt->execute([':t1' => $term, ':t2' => $term]);
    $results = array_merge($results, $conStmt->fetchAll(PDO::FETCH_ASSOC));

    // 5. Projects
    $pStmt = $pdo->prepare("
        SELECT prj_id as id, project_name as title, facility_location as subtitle, 'Project' as type, 'Projects.php' as url 
        FROM `projects` 
        WHERE project_name LIKE :t1 OR prj_id LIKE :t2 
        LIMIT 5
    ");
    $pStmt->execute([':t1' => $term, ':t2' => $term]);
    $results = array_merge($results, $pStmt->fetchAll(PDO::FETCH_ASSOC));

    Response::success($results, "Found " . count($results) . " matching records in database");
} catch (Throwable $e) {
    Response::error("Search error: " . $e->getMessage(), 500);
}
