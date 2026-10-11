<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR - Automated Error Leakage Sanitizer
 * Location: scripts/sanitize_all_getmessage.php
 *
 * Replaces client-facing $e->getMessage() leakages with error_log() + friendly messages.
 */

$rootDir = realpath(__DIR__ . '/..');
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootDir));

$modifiedCount = 0;

foreach ($iterator as $file) {
    if ($file->getExtension() !== 'php') continue;
    $filePath = $file->getRealPath();
    if (str_contains($filePath, 'tests') || str_contains($filePath, '_trash') || str_contains($filePath, 'scripts')) continue;

    $content = file_get_contents($filePath);
    $orig = $content;

    // Pattern 1: Response::error("Prefix: " . $e->getMessage(), CODE);
    $content = preg_replace_callback(
        '/Response::error\(\s*(["\'])(.*?)\s*:\s*\1\s*\.\s*\$e->getMessage\(\)\s*,\s*(\d+)\s*\);/',
        function ($m) {
            $prefix = $m[2];
            $code = $m[3];
            return "error_log(\"{$prefix}: \" . \$e->getMessage());\n        Response::error(\"{$prefix}. Please try again later.\", {$code});";
        },
        $content
    );

    // Pattern 2: Response::error("Prefix " . $e->getMessage(), CODE);
    $content = preg_replace_callback(
        '/Response::error\(\s*(["\'])(.*?)\1\s*\.\s*\$e->getMessage\(\)\s*,\s*(\d+)\s*\);/',
        function ($m) {
            $prefix = trim($m[2]);
            $code = $m[3];
            return "error_log(\"{$prefix}: \" . \$e->getMessage());\n        Response::error(\"{$prefix}\", {$code});";
        },
        $content
    );

    // Pattern 3: sendJsonError('Prefix: ' . $e->getMessage(), CODE);
    $content = preg_replace_callback(
        '/sendJsonError\(\s*(["\'])(.*?)\s*:\s*\1\s*\.\s*\$e->getMessage\(\)\s*,\s*(\d+)\s*\);/',
        function ($m) {
            $prefix = $m[2];
            $code = $m[3];
            return "error_log(\"{$prefix}: \" . \$e->getMessage());\n    sendJsonError(\"{$prefix}. An internal error occurred.\", {$code});";
        },
        $content
    );

    // Pattern 4: apiError("Prefix: " . $e->getMessage(), CODE);
    $content = preg_replace_callback(
        '/apiError\(\s*(["\'])(.*?)\s*:\s*\1\s*\.\s*\$e->getMessage\(\)\s*,\s*(\d+)\s*\);/',
        function ($m) {
            $prefix = $m[2];
            $code = $m[3];
            return "error_log(\"{$prefix}: \" . \$e->getMessage());\n    apiError(\"{$prefix}. Please try again later.\", {$code});";
        },
        $content
    );

    // Pattern 5: jsonResponse(['success' => false, 'error' => $e->getMessage()], CODE);
    $content = preg_replace(
        '/jsonResponse\(\[\s*[\'"]success[\'"]\s*=>\s*false,\s*[\'"]error[\'"]\s*=>\s*\$e->getMessage\(\)\s*\],\s*(\d+)\);/',
        "error_log(\"API error: \" . \$e->getMessage());\n        jsonResponse(['success' => false, 'error' => 'An internal server error occurred.'], $1);",
        $content
    );

    // Pattern 6: jsonRes(['success' => false, 'error' => '...: ' . $e->getMessage()], CODE);
    $content = preg_replace_callback(
        '/jsonRes\(\[\s*[\'"]success[\'"]\s*=>\s*false,\s*[\'"]error[\'"]\s*=>\s*[\'"](.*?)\s*:\s*[\'"]\s*\.\s*\$e->getMessage\(\)\s*\],\s*(\d+)\);/',
        function ($m) {
            $prefix = $m[1];
            $code = $m[2];
            return "error_log(\"{$prefix}: \" . \$e->getMessage());\n    jsonRes(['success' => false, 'error' => '{$prefix}. Please try again later.'], {$code});";
        },
        $content
    );

    // Pattern 7: 'error' => '...: ' . $e->getMessage()
    $content = preg_replace_callback(
        '/[\'"]error[\'"]\s*=>\s*[\'"](.*?)\s*:\s*[\'"]\s*\.\s*\$e->getMessage\(\)/',
        function ($m) {
            $prefix = $m[1];
            return "'error' => '{$prefix}. Please contact system support.'";
        },
        $content
    );

    // Pattern 8: return ['success' => false, 'message' => "Database error: " . $e->getMessage()];
    $content = preg_replace(
        '/return\s*\[\s*[\'"]success[\'"]\s*=>\s*false,\s*[\'"]message[\'"]\s*=>\s*["\']Database error:\s*["\']\s*\.\s*\$e->getMessage\(\)\s*\];/',
        "error_log(\"Database error: \" . \$e->getMessage());\n    return ['success' => false, 'message' => 'A database error occurred.'];",
        $content
    );

    // Pattern 9: return ['success' => false, 'message' => $e->getMessage()];
    $content = preg_replace(
        '/return\s*\[\s*[\'"]success[\'"]\s*=>\s*false,\s*[\'"]message[\'"]\s*=>\s*\$e->getMessage\(\)\s*\];/',
        "error_log(\"Operation failure: \" . \$e->getMessage());\n    return ['success' => false, 'message' => 'The operation could not be completed.'];",
        $content
    );

    // Pattern 10: return ['success' => false, 'error' => $e->getMessage()];
    $content = preg_replace(
        '/return\s*\[\s*[\'"]success[\'"]\s*=>\s*false,\s*[\'"]error[\'"]\s*=>\s*\$e->getMessage\(\)\s*\];/',
        "error_log(\"Operation error: \" . \$e->getMessage());\n    return ['success' => false, 'error' => 'The requested operation could not be completed.'];",
        $content
    );

    // Pattern 11: jsonReply / crm_jsonReply / shop_jsonReply / intra_jsonReply / cus_jsonReply (['success' => false, 'error' => $e->getMessage()], CODE)
    $content = preg_replace_callback(
        '/(crm_|shop_|intra_|cus_|sa|json)?(jsonReply|Reply|Response)\(\s*\[\s*[\'"]success[\'"]\s*=>\s*false,\s*[\'"]error[\'"]\s*=>\s*\$e->getMessage\(\)\s*\]\s*,\s*(\d+)\s*\);/',
        function ($m) {
            $fn = ($m[1] ?: '') . $m[2];
            $code = $m[3];
            return "error_log(\"API error: \" . \$e->getMessage());\n        {$fn}(['success' => false, 'error' => 'An error occurred while processing the request.'], {$code});";
        },
        $content
    );

    // Pattern 12: saReply(false, "...: " . $e->getMessage(), ...)
    $content = preg_replace_callback(
        '/saReply\(\s*false,\s*["\'](.*?)\s*:\s*["\']\s*\.\s*\$e->getMessage\(\)\s*,\s*\[\],\s*(\d+),\s*\$isAjax\);/',
        function ($m) {
            $prefix = $m[1];
            $code = $m[2];
            return "error_log(\"{$prefix}: \" . \$e->getMessage());\n    saReply(false, \"{$prefix}. Operation rejected by security policy.\", [], {$code}, \$isAjax);";
        },
        $content
    );

    // Pattern 14: $tableError = $e->getMessage();
    $content = str_replace(
        '$tableError = $e->getMessage();',
        "error_log('SuperAdmin table query failure: ' . \$e->getMessage());\n        \$tableError = 'Unable to query table data. Please verify database connectivity.';",
        $content
    );

    // Pattern 15: 'error' => $e->getMessage(),
    $content = preg_replace(
        '/[\'"]error[\'"]\s*=>\s*\$e->getMessage\(\)\s*,/',
        "'error'   => 'Integration service failure occurred.',",
        $content
    );

    if ($content !== $orig) {
        file_put_contents($filePath, $content);
        $modifiedCount++;
        echo "Sanitized: " . str_replace($rootDir . DIRECTORY_SEPARATOR, '', $filePath) . "\n";
    }
}

echo "\nSanitization complete. Files updated: {$modifiedCount}\n";
