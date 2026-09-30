<?php
$hrDir = __DIR__ . '/../HR System';
$files = [
    'Dashboard.php',
    'EmployeeRecords.php',
    'LeaveManagement.php',
    'JobPostings.php',
    'OnboardingTracker.php',
    'Training.php',
    'OrgStructure.php',
    'Offboarding.php',
    'ServiceRequests.php',
    'Integrations.php'
];

foreach ($files as $f) {
    $path = "$hrDir/$f";
    if (!file_exists($path)) {
        echo "FILE MISSING: $f\n";
        continue;
    }
    $content = file_get_contents($path);
    preg_match('/<nav class="sidebar-nav">(.*?)<\/nav>/s', $content, $m);
    if (!$m) {
        echo "$f: NO <nav class=\"sidebar-nav\"> FOUND!\n";
        continue;
    }
    preg_match_all('/<a [^>]*href="([^"]+)"[^>]*>.*?<span class="sidebar-icon material-symbols-outlined">([^<]+)<\/span>.*?<span>([^<]+)<\/span>/s', $m[1], $matches, PREG_SET_ORDER);
    echo "=== $f (Links found: " . count($matches) . ") ===\n";
    foreach ($matches as $match) {
        echo "  [href: {$match[1]}] [icon: {$match[2]}] [text: {$match[3]}]\n";
    }
}
