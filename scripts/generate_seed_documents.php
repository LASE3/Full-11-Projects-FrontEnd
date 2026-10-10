<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Enterprise Ecosystem
 * Script: scripts/generate_seed_documents.php
 * Generates small, real placeholder files (valid PDF/XLSX with document title and metadata)
 * for the 15 baseline documents in storage/documents/, calculates their real SHA-256 hashes,
 * file sizes, and MIME types, updates the database and prepares migration data.
 */

require_once __DIR__ . '/../config/db.php';

$pdo = getDbConnection();

// Ensure storage directory exists
$storageDir = dirname(__DIR__) . '/storage/documents';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}

// Ensure .htaccess in storage
$htaccess = dirname(__DIR__) . '/storage/.htaccess';
if (!file_exists($htaccess)) {
    file_put_contents($htaccess, "# Deny direct web access to all stored enterprise documents\nRequire all denied\n<FilesMatch \".*\">\n    Require all denied\n</FilesMatch>\n<IfModule mod_php.c>\n    php_flag engine off\n</IfModule>\n<IfModule mod_php8.c>\n    php_flag engine off\n</IfModule>\n");
}

function createMinimalPdf(string $title, string $docId, string $classification): string
{
    $cleanTitle = preg_replace('/[^\x20-\x7E]/', '', $title);
    $content = "BT\n/F1 16 Tf\n50 750 Td\n(VOSTOKPRIBOR ENTERPRISE DOCUMENT) Tj\n";
    $content .= "/F1 12 Tf\n0 -25 Td\n(Document ID: {$docId}) Tj\n";
    $content .= "0 -20 Td\n(Title: {$cleanTitle}) Tj\n";
    $content .= "0 -20 Td\n(Security Classification: {$classification}) Tj\n";
    $content .= "0 -20 Td\n(Verified Cryptographic Artifact - Station Almaty) Tj\nET";

    $len = strlen($content);
    $pdf = "%PDF-1.4\n";
    $offsets = [0];

    $offsets[] = strlen($pdf);
    $pdf .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";

    $offsets[] = strlen($pdf);
    $pdf .= "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";

    $offsets[] = strlen($pdf);
    $pdf .= "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n";

    $offsets[] = strlen($pdf);
    $pdf .= "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";

    $offsets[] = strlen($pdf);
    $pdf .= "5 0 obj\n<< /Length {$len} >>\nstream\n{$content}\nendstream\nendobj\n";

    $xrefStart = strlen($pdf);
    $pdf .= "xref\n0 6\n0000000000 65535 f \n";
    for ($i = 1; $i <= 5; $i++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
    }
    $pdf .= "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n{$xrefStart}\n%%EOF";
    return $pdf;
}

function createMinimalXlsx(string $title, string $docId, string $classification): string
{
    $tempFile = tempnam(sys_get_temp_dir(), 'xlsx_');
    $zip = new ZipArchive();
    $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml" ContentType="application/xml"/>'
        . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
        . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
        . '</Types>';
    $zip->addFromString('[Content_Types].xml', $contentTypes);

    $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
        . '</Relationships>';
    $zip->addFromString('_rels/.rels', $rels);

    $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<sheets><sheet name="Baseline" sheetId="1" r:id="rId1"/></sheets>'
        . '</workbook>';
    $zip->addFromString('xl/workbook.xml', $workbook);

    $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
        . '</Relationships>';
    $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

    $cleanTitle = htmlspecialchars(preg_replace('/[^\x20-\x7E]/', '', $title));
    $cleanDoc = htmlspecialchars($docId);
    $cleanClass = htmlspecialchars($classification);

    $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<sheetData>'
        . '<row r="1"><c r="A1" t="inlineStr"><is><t>VOSTOKPRIBOR ENTERPRISE DOCUMENT</t></is></c></row>'
        . '<row r="2"><c r="A2" t="inlineStr"><is><t>Doc ID: ' . $cleanDoc . '</t></is></c></row>'
        . '<row r="3"><c r="A3" t="inlineStr"><is><t>Title: ' . $cleanTitle . '</t></is></c></row>'
        . '<row r="4"><c r="A4" t="inlineStr"><is><t>Classification: ' . $cleanClass . '</t></is></c></row>'
        . '</sheetData></worksheet>';
    $zip->addFromString('xl/worksheets/sheet1.xml', $sheet);

    $zip->close();
    $data = file_get_contents($tempFile);
    @unlink($tempFile);
    return $data;
}

// Fetch baseline 15 documents from database
$docs = $pdo->query("SELECT doc_id, file_name, classification, owner_emp_id FROM documents WHERE doc_id BETWEEN 'DOC-2026-001' AND 'DOC-2026-015' ORDER BY doc_id ASC")->fetchAll(PDO::FETCH_ASSOC);

echo "Generating real baseline seed document files..." . PHP_EOL;

$finfo = new finfo(FILEINFO_MIME_TYPE);

$results = [];

foreach ($docs as $d) {
    $docId = $d['doc_id'];
    $fileName = $d['file_name'];
    $class = $d['classification'];
    $owner = $d['owner_emp_id'] ?: 'EMP-1004';

    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $diskName = $docId . '_' . $fileName;
    $diskPath = $storageDir . '/' . $diskName;
    $relativeStoragePath = 'storage/documents/' . $diskName;

    if ($ext === 'xlsx') {
        $content = createMinimalXlsx($fileName, $docId, $class);
    } else {
        $content = createMinimalPdf($fileName, $docId, $class);
    }

    file_put_contents($diskPath, $content);

    $realHash = hash_file('sha256', $diskPath);
    $realSize = filesize($diskPath);
    $formattedSize = round($realSize / 1024, 1) . ' KB';
    $realMime = $finfo->file($diskPath) ?: ($ext === 'xlsx' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'application/pdf');

    // Update documents table
    $upd = $pdo->prepare("
        UPDATE documents 
        SET file_hash = :hash,
            storage_path = :path,
            mime = :mime,
            file_size = :size
        WHERE doc_id = :id
    ");
    $upd->execute([
        ':hash' => $realHash,
        ':path' => $relativeStoragePath,
        ':mime' => $realMime,
        ':size' => $formattedSize,
        ':id'   => $docId
    ]);

    // Upsert version 1 in document_versions
    $vChk = $pdo->prepare("SELECT version_id FROM document_versions WHERE doc_id = ? AND version_number = 1");
    $vChk->execute([$docId]);
    if ($vChk->fetchColumn()) {
        $vUpd = $pdo->prepare("UPDATE document_versions SET file_path = ?, uploaded_at = NOW() WHERE doc_id = ? AND version_number = 1");
        $vUpd->execute([$relativeStoragePath, $docId]);
    } else {
        $vIns = $pdo->prepare("INSERT INTO document_versions (doc_id, version_number, uploaded_by_emp_id, uploaded_at, file_path) VALUES (?, 1, ?, NOW(), ?)");
        $vIns->execute([$docId, $owner, $relativeStoragePath]);
    }

    $results[$docId] = [
        'file_name' => $fileName,
        'hash'      => $realHash,
        'size'      => $formattedSize,
        'path'      => $relativeStoragePath,
        'mime'      => $realMime
    ];

    echo "  [{$docId}] {$fileName} -> {$formattedSize} | SHA-256: " . substr($realHash, 0, 16) . "... ({$realMime})" . PHP_EOL;
}

echo "All 15 baseline documents generated and verified in storage/documents/." . PHP_EOL;
