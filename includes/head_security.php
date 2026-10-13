<?php
declare(strict_types=1);

if (!function_exists('getCsrfToken')) {
    require_once __DIR__ . '/auth_guard.php';
}

$csrfToken = getCsrfToken();

// Compute relative asset prefix based on the current executing script
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$isSubDir = false;
$knownSystems = [
    'Admin & Governance Portal', 'CRM', 'Customer Portal', 'Developer',
    'Employee Intranet', 'File Center', 'Finance & Billing', 'HR System',
    'IT Helpdesk', 'Online Shop B2B', 'VOSTOKPRIBOR Corporate Web Platform', 'account'
];

foreach ($knownSystems as $sys) {
    if (strpos($scriptName, '/' . $sys . '/') !== false || strpos(urldecode($scriptName), '/' . $sys . '/') !== false) {
        $isSubDir = true;
        break;
    }
}

$assetPrefix = $isSubDir ? '../assets/' : 'assets/';
?>
<meta name="csrf-token" content="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
<script src="<?= $assetPrefix ?>js/vp-core.js"></script>
