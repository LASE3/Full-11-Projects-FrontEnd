<?php
$files = [
    "CRM/api/activities.php",
    "CRM/api/contracts.php",
    "CRM/api/customers.php",
    "CRM/api/export.php",
    "CRM/api/forecasts.php",
    "CRM/api/leads.php",
    "CRM/api/opportunities.php",
    "CRM/api/projects.php",
    "CRM/api/quotes.php",
    "CRM/api/search.php",
    "CRM/api/upload.php",
    "Customer Portal/api/account.php",
    "Customer Portal/api/careers.php",
    "Customer Portal/api/dashboard.php",
    "Customer Portal/api/documents.php",
    "Customer Portal/api/export.php",
    "Customer Portal/api/invoices.php",
    "Customer Portal/api/orders.php",
    "Customer Portal/api/projects.php",
    "Customer Portal/api/services.php",
    "Customer Portal/api/stats.php",
    "Customer Portal/api/tickets.php",
    "Customer Portal/api/upload.php",
    "Employee Intranet/api/announcements.php",
    "Employee Intranet/api/directory.php",
    "Employee Intranet/api/export.php",
    "Employee Intranet/api/leaves.php",
    "Employee Intranet/api/policies.php",
    "Employee Intranet/api/stats.php",
    "Employee Intranet/api/upload.php",
    "Online Shop B2B/api/export.php",
    "Online Shop B2B/api/orders.php",
    "Online Shop B2B/api/products.php",
    "Online Shop B2B/api/projects.php",
    "Online Shop B2B/api/quotes.php",
    "Online Shop B2B/api/stats.php",
    "Online Shop B2B/api/upload.php",
    "api/notifications.php"
];

$base = dirname(__DIR__);
foreach ($files as $f) {
    $full = $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $f);
    if (!file_exists($full)) {
        echo "MISSING: $f\n";
        continue;
    }
    $cmd = 'php ' . escapeshellarg($full);
    exec($cmd . ' 2>&1', $out, $code);
    $outputStr = implode("\n", $out);
    if ($code !== 0 || str_contains($outputStr, 'Fatal error') || str_contains($outputStr, 'Parse error')) {
        echo "FAIL [$code]: $f\n" . substr($outputStr, 0, 300) . "\n---\n";
    } else {
        echo "OK: $f\n";
    }
    unset($out);
}
