<?php
$dir = dirname(__DIR__);
$pattern = '/\b(Danilov|Rostova|Gazprom|Norilsk|Severstal|Omsk|Marchenko|Cherepovets|RU-SVR)\b/i';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$matches = [];

foreach ($iterator as $file) {
    if (!$file->isFile()) continue;
    $path = str_replace('\\', '/', $file->getPathname());
    if (str_contains($path, '/.git/') || str_contains($path, '/DataBase/') || str_contains($path, '/storage/') || str_contains($path, '/_trash/')) {
        continue;
    }
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    if (!in_array($ext, ['php', 'html', 'js', 'json', 'css'])) continue;

    $lines = file($path);
    foreach ($lines as $lineNum => $line) {
        if (preg_match($pattern, $line, $m)) {
            $rel = str_replace(str_replace('\\', '/', $dir) . '/', '', $path);
            $matches[$rel][] = ($lineNum + 1) . ': [' . $m[1] . '] ' . trim($line);
        }
    }
}

foreach ($matches as $file => $occurrences) {
    echo "FILE: $file (" . count($occurrences) . " matches)\n";
    foreach (array_slice($occurrences, 0, 5) as $occ) {
        echo "  $occ\n";
    }
    if (count($occurrences) > 5) {
        echo "  ... and " . (count($occurrences) - 5) . " more\n";
    }
}
echo "TOTAL FILES: " . count($matches) . "\n";
