<?php
// scripts/bind_all_ids.php

// 1. CRM Leads.php
$leadsFile = __DIR__ . '/../CRM/Leads.php';
if (file_exists($leadsFile)) {
    $c = file_get_contents($leadsFile);
    if (!str_contains($c, 'id="leads-tbody"')) {
        $c = preg_replace('/<tbody(\s*>)/i', '<tbody id="leads-tbody"$1', $c, 1);
        file_put_contents($leadsFile, $c);
        echo "CRM Leads.php -> OK (leads-tbody)\n";
    } else {
        echo "CRM Leads.php -> ALREADY BOUND\n";
    }
}

// 2. Customer Portal Dashboard.php
$cpDash = __DIR__ . '/../Customer Portal/Dashboard.php';
if (file_exists($cpDash)) {
    $c = file_get_contents($cpDash);
    
    // Active Projects KPI
    if (!str_contains($c, 'id="kpi-active-projects"')) {
        $pattern = '/(Active\s*Projects<\/span>\s*<span\s+class="[^"]*")/is';
        if (preg_match($pattern, $c)) {
            $c = preg_replace($pattern, '$1 id="kpi-active-projects"', $c, 1);
            echo "Customer Portal Dashboard.php -> OK (kpi-active-projects)\n";
        }
    }
    
    // Pending Invoices KPI
    if (!str_contains($c, 'id="kpi-open-invoices"')) {
        $pattern = '/(Pending\s*Invoices<\/span>\s*<span\s+class="[^"]*")/is';
        if (preg_match($pattern, $c)) {
            $c = preg_replace($pattern, '$1 id="kpi-open-invoices"', $c, 1);
            echo "Customer Portal Dashboard.php -> OK (kpi-open-invoices)\n";
        }
    }

    // Support Tickets KPI
    if (!str_contains($c, 'id="kpi-open-tickets"')) {
        $pattern = '/(Support\s*Tickets<\/span>\s*<span\s+class="[^"]*")/is';
        if (preg_match($pattern, $c)) {
            $c = preg_replace($pattern, '$1 id="kpi-open-tickets"', $c, 1);
            echo "Customer Portal Dashboard.php -> OK (kpi-open-tickets)\n";
        }
    }

    file_put_contents($cpDash, $c);
}

echo "Done.\n";
