<?php
/**
 * Adds data-binding IDs to HTML elements across all four systems.
 * Handles both plain <tbody> and <tbody class="..."> variants.
 * Run once: php scripts/add_data_ids.php
 */

$replacements = [
    // ─── CRM ──────────────────────────────────────
    ['file' => __DIR__ . '/../CRM/Customers.php',
     'search' => ['<tbody>'],
     'id' => 'customers-tbody'],

    ['file' => __DIR__ . '/../CRM/SalesForecast.php',
     'search' => ['<tbody>'],
     'id' => 'forecast-tbody'],

    // ─── Customer Portal ──────────────────────────
    ['file' => __DIR__ . '/../Customer Portal/ProjectListAndDetail.php',
     'search' => ['<tbody class=', '<tbody>'],
     'id' => 'projects-tbody'],

    ['file' => __DIR__ . '/../Customer Portal/Invoices.php',
     'search' => ['<tbody class=', '<tbody>'],
     'id' => 'invoices-tbody'],

    ['file' => __DIR__ . '/../Customer Portal/SupportTicketView.php',
     'search' => ['<tbody class=', '<tbody>'],
     'id' => 'tickets-list'],

    // ─── Employee Intranet ────────────────────────
    ['file' => __DIR__ . '/../Employee Intranet/EmployeeDirectory.php',
     'search' => ['<tbody class=', '<tbody>'],
     'id' => 'directory-tbody'],

    ['file' => __DIR__ . '/../Employee Intranet/PoliciesAndForms.php',
     'search' => ['<tbody class=', '<tbody>'],
     'id' => 'leaves-tbody'],
];

foreach ($replacements as $r) {
    $file = $r['file'];
    $base = basename($file);
    $id   = $r['id'];

    if (!file_exists($file)) {
        echo "  SKIP  $base — file not found\n";
        continue;
    }

    $content = file_get_contents($file);

    if (str_contains($content, "id=\"$id\"")) {
        echo "  DONE  $base (already has id=\"$id\")\n";
        continue;
    }

    $patched = false;
    foreach ($r['search'] as $needle) {
        $pos = strpos($content, $needle);
        if ($pos === false) continue;

        // Find the end of this opening tag (the > character)
        $endTag = strpos($content, '>', $pos);
        if ($endTag === false) continue;

        // Get the full opening tag
        $openTag = substr($content, $pos, $endTag - $pos + 1);

        // Build new tag with id injected
        if (str_contains($openTag, '<tbody>')) {
            $newTag = "<tbody id=\"$id\">";
        } else {
            // <tbody class="..."> → <tbody id="..." class="...">
            $newTag = str_replace('<tbody ', "<tbody id=\"$id\" ", $openTag);
            if ($newTag === $openTag) {
                $newTag = str_replace('<tbody', "<tbody id=\"$id\"", $openTag);
            }
        }

        // Replace only first occurrence
        $content = substr_replace($content, $newTag, $pos, strlen($openTag));
        $patched = true;
        break;
    }

    if ($patched) {
        file_put_contents($file, $content);
        echo "  OK    $base → id=\"$id\"\n";
    } else {
        echo "  WARN  $base — no matching element found\n";
    }
}

echo "\nDone.\n";
