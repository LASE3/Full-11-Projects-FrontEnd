<?php
/**
 * Test all PHP pages across all 11 enterprise portals
 */
$baseDir = dirname(__DIR__);

$systems = [
    'Admin & Governance Portal',
    'CRM',
    'Customer Portal',
    'Developer',
    'Employee Intranet',
    'File Center',
    'Finance & Billing',
    'HR System',
    'IT Helpdesk',
    'Online Shop B2B',
    'VOSTOKPRIBOR Corporate Web Platform'
];

$errors = [];
$tested = 0;

foreach ($systems as $sys) {
    $sysDir = $baseDir . DIRECTORY_SEPARATOR . $sys;
    if (!is_dir($sysDir)) continue;

    $files = glob($sysDir . DIRECTORY_SEPARATOR . '*.php');
    foreach ($files as $file) {
        $rel = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $file);
        $tested++;

        // Lint check
        $cmd = 'php -l ' . escapeshellarg($file);
        exec($cmd . ' 2>&1', $lintOut, $lintCode);
        if ($lintCode !== 0) {
            $errors[] = "[LINT] $rel: " . implode(" ", $lintOut);
            unset($lintOut);
            continue;
        }
        unset($lintOut);

        // Simulated run with session
        $runScript = "<?php
            \$_SESSION = [
                'vostok_authenticated' => true,
                'vostok_system_ADM' => true,
                'vostok_system_CRM' => true,
                'vostok_system_CUS' => true,
                'vostok_system_DEV' => true,
                'vostok_system_EMP' => true,
                'vostok_system_DOC' => true,
                'vostok_system_FIN' => true,
                'vostok_system_HR'  => true,
                'vostok_system_IT'  => true,
                'vostok_system_SHP' => true,
                'vostok_system_WEB' => true,
                'vostok_user' => [
                    'account_id' => 1,
                    'user_id' => 'EMP-0001',
                    'emp_id' => 'EMP-0001',
                    'cus_id' => 'CUS-1001',
                    'username' => 'admin',
                    'full_name' => 'System Administrator',
                    'email' => 'admin@gmail.com',
                    'role_name' => 'Executive SuperAdmin',
                    'department_code' => 'EXE',
                    'clearance_level' => 'L4',
                    'account_type' => 'Executive'
                ],
                'emp_id' => 'EMP-0001',
                'cus_id' => 'CUS-1001'
            ];
            \$_SERVER['REQUEST_METHOD'] = 'GET';
            \$_SERVER['HTTP_HOST'] = 'localhost';
            \$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
            \$_SERVER['REQUEST_URI'] = '/' . escapeshellarg(\$argv[1]);
            ob_start();
            try {
                include \$argv[1];
                \$out = ob_get_clean();
                echo 'PAGE_OK';
            } catch (Throwable \$e) {
                ob_end_clean();
                echo 'EXCEPTION: ' . \$e->getMessage() . ' in ' . \$e->getFile() . ':' . \$e->getLine();
            }
        ";

        $tempFile = $baseDir . DIRECTORY_SEPARATOR . 'scratch' . DIRECTORY_SEPARATOR . 'runner.php';
        file_put_contents($tempFile, $runScript);

        $cmd = 'php ' . escapeshellarg($tempFile) . ' ' . escapeshellarg($file);
        exec($cmd . ' 2>&1', $runOut, $runCode);
        $res = implode("\n", $runOut);

        if (str_contains($res, 'EXCEPTION:') || str_contains($res, 'Fatal error') || str_contains($res, 'Parse error')) {
            $errors[] = "[RUNTIME] $rel: " . substr($res, 0, 300);
        }
        unset($runOut);
    }
}

echo "Tested $tested pages across all 11 systems.\n";
if (empty($errors)) {
    echo "SUCCESS: ALL PAGES PASSED WITHOUT ERRORS!\n";
} else {
    echo "FOUND " . count($errors) . " ERRORS:\n";
    foreach ($errors as $e) {
        echo "- $e\n";
    }
}
