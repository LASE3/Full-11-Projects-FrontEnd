<?php
$rootDir = realpath(__DIR__ . '/..');
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootDir));
foreach ($files as $f) {
    if ($f->getExtension() !== 'php') continue;
    $p = $f->getPathname();
    if (str_contains($p, 'tests') || str_contains($p, '_trash') || str_contains($p, 'find_getmessage_leaks.php')) continue;
    $c = file_get_contents($p);
    $lines = explode("\n", $c);
    $rel = str_replace($rootDir . DIRECTORY_SEPARATOR, '', $p);
    foreach ($lines as $i => $l) {
        if (str_contains($l, 'getMessage()') && !str_contains($l, 'error_log') && !str_contains($l, 'fwrite') && !str_contains($l, 'strpos') && !str_contains($l, 'str_contains')) {
            echo $rel . ':' . ($i + 1) . ': ' . trim($l) . "\n";
        }
    }
}
