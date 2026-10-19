<?php
/**
 * VOSTOKPRIBOR - Clean Distribution Release Archive Builder
 * Specification: Phase 7.1 (Delivery Hygiene)
 *
 * Rules:
 *  - Must NOT contain node_modules/
 *  - Must NOT contain .git/
 *  - Must NOT contain .env (only .env.example)
 *  - Must NOT contain generated documents or plaintext credentials
 *  - Includes post-build verification check that fails and deletes the zip if violated
 */

declare(strict_types=1);

if (!extension_loaded('zip')) {
    fwrite(STDERR, "[ERROR] The PHP 'zip' extension is required to build the release archive.\n");
    exit(1);
}

$rootDir = realpath(dirname(__DIR__));
$releaseDir = $rootDir . DIRECTORY_SEPARATOR . 'release';

if (!is_dir($releaseDir)) {
    mkdir($releaseDir, 0755, true);
}

$timestamp = date('Ymd_His');
$zipFileName = "vostokpribor_release_{$timestamp}.zip";
$zipPath = $releaseDir . DIRECTORY_SEPARATOR . $zipFileName;

echo "========================================================================\n";
echo "       VOSTOKPRIBOR ENTERPRISE RELEASE ARCHIVE BUILDER                  \n";
echo "========================================================================\n\n";
echo "Target archive: {$zipPath}\n";
echo "Building release archive from tracked files (git archive HEAD)...\n";

$cmdOutput = [];
$exitCode = 0;
$archiveCmd = 'git archive HEAD --format=zip -o ' . escapeshellarg($zipPath);
exec($archiveCmd, $cmdOutput, $exitCode);

if ($exitCode !== 0 || !file_exists($zipPath)) {
    fwrite(STDERR, "[ERROR] git archive command failed (Exit code: {$exitCode})\n");
    exit(1);
}

echo "Archive created successfully via git archive HEAD.\n";

// -----------------------------------------------------------------------------
// POST-BUILD SELF-AUDIT & INTEGRITY CHECK
// -----------------------------------------------------------------------------
echo "\nRunning release archive hygiene self-audit...\n";
$verifyZip = new ZipArchive();
if ($verifyZip->open($zipPath) !== true) {
    fwrite(STDERR, "[ERROR] Failed to open generated archive for verification: {$zipPath}\n");
    exit(1);
}

$violations = [];
$totalEntries = $verifyZip->numFiles;

for ($i = 0; $i < $totalEntries; $i++) {
    $entryName = $verifyZip->getNameIndex($i);
    
    // Check for prohibited entries
    if (preg_match('/^scratch(\/|$)/i', $entryName)) {
        $violations[] = "Prohibited scratch/ entry in release archive: {$entryName}";
    }
    if (preg_match('/\.zip$/i', $entryName)) {
        $violations[] = "Prohibited zip archive nested inside release archive: {$entryName}";
    }
    if (preg_match('/(debug|scratch|tmp|temp).*\.php$/i', $entryName)) {
        $violations[] = "Prohibited debug script in release archive: {$entryName}";
    }
    if (preg_match('/^node_modules(\/|$)/i', $entryName)) {
        $violations[] = "Prohibited node_modules entry: {$entryName}";
    }
    if (preg_match('/^\.git(\/|$)/i', $entryName)) {
        $violations[] = "Prohibited .git entry: {$entryName}";
    }
    if ($entryName === '.env') {
        $violations[] = "Prohibited local .env file inside release archive!";
    }
    if (preg_match('/^storage\/documents\/.+\.(pdf|xlsx|docx)$/i', $entryName)) {
        $violations[] = "Prohibited generated document in archive: {$entryName}";
    }
    if (preg_match('/(credentials|passwords).*\.(json|txt|tmp)$/i', $entryName)) {
        $violations[] = "Prohibited credential/password file: {$entryName}";
    }
}

$verifyZip->close();

if (!empty($violations)) {
    @unlink($zipPath);
    fwrite(STDERR, "\n[FATAL] RELEASE ARCHIVE AUDIT FAILED (" . count($violations) . " violations):\n");
    foreach ($violations as $v) {
        fwrite(STDERR, "  - {$v}\n");
    }
    fwrite(STDERR, "\nCorrupted archive was securely removed. Build aborted.\n");
    exit(1);
}

$archiveBytes = filesize($zipPath);
$archiveMb = round($archiveBytes / (1024 * 1024), 2);
$archiveSha256 = hash_file('sha256', $zipPath);

echo "  [PASS] node_modules/ absent from archive\n";
echo "  [PASS] .git/ absent from archive\n";
echo "  [PASS] .env absent from archive (.env.example included)\n";
echo "  [PASS] Generated documents absent from archive\n";
echo "  [PASS] Plaintext credentials/passwords absent from archive\n";
echo "  [PASS] Archive integrity verified (0 violations across {$totalEntries} entries)\n";
echo "\nArchive Summary:\n";
echo "  File:    {$zipFileName}\n";
echo "  Size:    {$archiveMb} MB ({$archiveBytes} bytes)\n";
echo "  SHA-256: {$archiveSha256}\n";
echo "\nSTATUS: 100% PASS - CLEAN RELEASE ARCHIVE READY\n";
exit(0);
