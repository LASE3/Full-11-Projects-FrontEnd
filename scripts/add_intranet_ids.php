<?php
/**
 * Adds data-binding wrapper IDs for the Intranet pages.
 * Run once: php scripts/add_intranet_ids.php
 */

// Dashboard.php — Wrap the feed cards column with id="announcements-feed"
$dash = __DIR__ . '/../Employee Intranet/Dashboard.php';
if (file_exists($dash)) {
    $c = file_get_contents($dash);
    if (!str_contains($c, 'id="announcements-feed"')) {
        // Find the feed column container (line 517 area: div with flex column)
        $marker = 'LEFT COLUMN: ANNOUNCEMENT FEED';
        $pos = strpos($c, $marker);
        if ($pos !== false) {
            // Find the next <div after this comment
            $divPos = strpos($c, '<div', $pos);
            if ($divPos !== false) {
                // Add id to this div
                $c = substr_replace($c, '<div id="announcements-feed"', $divPos, 4);
                file_put_contents($dash, $c);
                echo "  OK    Dashboard.php → id=\"announcements-feed\"\n";
            }
        }
    } else {
        echo "  DONE  Dashboard.php\n";
    }
}

echo "Done.\n";
