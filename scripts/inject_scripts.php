<?php

/**
 * One-time script: injects api-client.js + system data script into PHP pages.
 * Handles both app.js-style and portal.js/dashboard.js-style pages.
 * Run from project root: php scripts/inject_scripts.php
 */

// Pages in the Customer Portal, Intranet, and Shop use different JS names
// We look for </body> as the universal insertion point when no app.js marker exists.

$systems = [
    [
        'dir'         => __DIR__ . '/../CRM',
        'api_css'     => '../assets/css/api-ui.css',
        'api_js'      => '../assets/js/api-client.js',
        'data_js'     => 'js/crm-data.js',
        'markers'     => ['src="js/app.js"'],
        'skip'        => ['login.php', 'index.php', 'Integrations.php'],
    ],
    [
        'dir'         => __DIR__ . '/../Customer Portal',
        'api_css'     => '../assets/css/api-ui.css',
        'api_js'      => '../assets/js/api-client.js',
        'data_js'     => 'js/portal-data.js',
        'markers'     => [
            'src="js/app.js"',
            'src="js/portal.js"',
            'src="js/dashboard.js"',
            'src="js/invoices.js"',
            'src="js/projects.js"',
            'src="js/tickets.js"'
        ],
        'skip'        => ['login.php', 'index.php', 'Integrations.php'],
    ],
    [
        'dir'         => __DIR__ . '/../Employee Intranet',
        'api_css'     => '../assets/css/api-ui.css',
        'api_js'      => '../assets/js/api-client.js',
        'data_js'     => 'js/intranet-data.js',
        'markers'     => [
            'src="js/app.js"',
            'src="js/portal.js"',
            'src="js/dashboard.js"',
            'src="js/directory.js"',
            'src="js/intranet.js"'
        ],
        'skip'        => ['login.php', 'index.php', 'Integrations.php'],
    ],
    [
        'dir'         => __DIR__ . '/../Online Shop B2B',
        'api_css'     => '../assets/css/api-ui.css',
        'api_js'      => '../assets/js/api-client.js',
        'data_js'     => 'js/shop-data.js',
        'markers'     => ['src="js/app.js"', 'src="js/shop.js"', 'src="js/dashboard.js"'],
        'skip'        => ['login.php', 'index.php', 'Integrations.php', 'register.php', 'signup.php'],
    ],
];

foreach ($systems as $sys) {
    $files = glob($sys['dir'] . '/*.php');
    foreach ($files as $file) {
        $base = basename($file);
        if (in_array($base, $sys['skip'])) {
            echo "  SKIP  $base\n";
            continue;
        }

        $content = file_get_contents($file);

        // Already patched?
        if (str_contains($content, 'api-client.js')) {
            echo "  DONE  $base (already patched)\n";
            continue;
        }

        $inject = "\n  <link rel=\"stylesheet\" href=\"{$sys['api_css']}\">"
            . "\n  <script src=\"{$sys['api_js']}\"></script>"
            . "\n  <script src=\"{$sys['data_js']}\"></script>";

        // Try each marker; first match wins
        $patched = false;
        foreach ($sys['markers'] as $marker) {
            if (str_contains($content, $marker)) {
                // Insert AFTER the matched script tag
                $tag = '<script ' . $marker . '></script>';
                if (str_contains($content, $tag)) {
                    $content = str_replace($tag, $tag . $inject, $content);
                } else {
                    // Marker found but tag structure differs — look for closing angle bracket
                    $content = str_replace($marker . '">', $marker . '">' . $inject . "\n  <!-- api injected -->", $content);
                }
                $patched = true;
                break;
            }
        }

        // Universal fallback: inject just before </body>
        if (!$patched) {
            if (str_contains($content, '</body>')) {
                $content = str_replace('</body>', $inject . "\n</body>", $content);
                $patched = true;
            }
        }

        if ($patched) {
            file_put_contents($file, $content);
            echo "  OK    $base\n";
        } else {
            echo "  FAIL  $base — no insertion point found\n";
        }
    }
}

echo "\nDone. All pages patched.\n";
