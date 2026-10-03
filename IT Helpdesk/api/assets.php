<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR IT Helpdesk - Asset Management API
 * Full CRUD for IT Hardware, Nodes & Industrial Edge Devices
 */

require_once __DIR__ . '/db_helper.php';
require_once __DIR__ . '/../../includes/auth_guard.php';

$user = requireApiAuth('IT');

$pdo = getItDb();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$payload = getRequestPayload();

// 1. GET: Fetch Assets
if ($method === 'GET') {
    $assetId = (int)($payload['asset_id'] ?? 0);

    if ($assetId > 0) {
        $stmt = $pdo->prepare("SELECT * FROM it_assets WHERE asset_id = :id");
        $stmt->execute([':id' => $assetId]);
        $asset = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$asset) {
            sendJsonError("Asset ID #{$assetId} not found.", 404);
        }
        sendJsonSuccess($asset, "Asset details retrieved.");
    }

    $search = trim((string)($payload['search'] ?? ''));
    $params = [];
    $where = [];

    if ($search !== '') {
        $where[] = "(asset_tag LIKE :s OR hostname LIKE :s OR ip_address LIKE :s OR device_model LIKE :s OR location LIKE :s)";
        $params[':s'] = "%{$search}%";
    }

    $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    $sql = "SELECT * FROM it_assets {$whereSql} ORDER BY asset_id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $assets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendJsonSuccess($assets, "Retrieved " . count($assets) . " hardware assets.");
}

// 2. POST: Create, Update, Delete
$action = trim((string)($_GET['action'] ?? ($payload['action'] ?? 'create')));

if ($action === 'create') {
    $tag = trim((string)($payload['asset_tag'] ?? ''));
    $model = trim((string)($payload['device_model'] ?? ''));
    $type = trim((string)($payload['asset_type'] ?? 'Industrial Gateway'));
    $location = trim((string)($payload['location'] ?? 'Central Datacenter'));
    $ip = trim((string)($payload['ip_address'] ?? '10.240.0.100'));
    $mac = trim((string)($payload['mac_address'] ?? ''));
    $firmware = trim((string)($payload['firmware_version'] ?? 'v1.0.0'));
    $os = trim((string)($payload['operating_system'] ?? 'Embedded Linux'));
    $health = trim((string)($payload['health_status'] ?? 'Online (Active)'));
    $status = trim((string)($payload['status'] ?? 'Active'));
    $criticality = trim((string)($payload['criticality'] ?? 'High'));
    $notes = trim((string)($payload['notes'] ?? ''));

    if ($tag === '' && $model === '') {
        sendJsonError("Asset tag or device model is required.");
    }

    if ($tag === '') {
        $tag = "VP-DEV-" . rand(100, 999);
    }

    $serial = "SN-" . strtoupper(bin2hex(random_bytes(4)));

    $stmt = $pdo->prepare("
        INSERT INTO it_assets 
        (asset_tag, device_model, asset_type, location, ip_address, mac_address, firmware_version, operating_system, serial_number, health_status, status, criticality, notes, assigned_date, last_seen_at)
        VALUES 
        (:tag, :model, :type, :loc, :ip, :mac, :fw, :os, :sn, :health, :status, :crit, :notes, CURDATE(), NOW())
    ");

    $stmt->execute([
        ':tag'    => $tag,
        ':model'  => $model,
        ':type'   => $type,
        ':loc'    => $location,
        ':ip'     => $ip,
        ':mac'    => $mac,
        ':fw'     => $firmware,
        ':os'     => $os,
        ':sn'     => $serial,
        ':health' => $health,
        ':status' => $status,
        ':crit'   => $criticality,
        ':notes'  => $notes,
    ]);

    $id = (int)$pdo->lastInsertId();
    sendJsonSuccess(['asset_id' => $id, 'asset_tag' => $tag], "Device [{$tag}] registered successfully.");
}

if ($action === 'update') {
    $id = (int)($payload['asset_id'] ?? ($payload['id'] ?? 0));
    if ($id <= 0) {
        sendJsonError("Asset ID is required for update.");
    }

    $tag = trim((string)($payload['asset_tag'] ?? ''));
    $model = trim((string)($payload['device_model'] ?? ''));
    $type = trim((string)($payload['asset_type'] ?? ''));
    $location = trim((string)($payload['location'] ?? ''));
    $ip = trim((string)($payload['ip_address'] ?? ''));
    $mac = trim((string)($payload['mac_address'] ?? ''));
    $firmware = trim((string)($payload['firmware_version'] ?? ''));
    $health = trim((string)($payload['health_status'] ?? 'Online (Active)'));
    $notes = trim((string)($payload['notes'] ?? ''));

    $stmt = $pdo->prepare("
        UPDATE it_assets 
        SET asset_tag = :tag,
            device_model = :model,
            asset_type = :type,
            location = :loc,
            ip_address = :ip,
            mac_address = :mac,
            firmware_version = :fw,
            health_status = :health,
            notes = :notes,
            last_seen_at = NOW()
        WHERE asset_id = :id
    ");

    $stmt->execute([
        ':id'     => $id,
        ':tag'    => $tag,
        ':model'  => $model,
        ':type'   => $type,
        ':loc'    => $location,
        ':ip'     => $ip,
        ':mac'    => $mac,
        ':fw'     => $firmware,
        ':health' => $health,
        ':notes'  => $notes,
    ]);

    sendJsonSuccess(['asset_id' => $id, 'asset_tag' => $tag], "Asset updated successfully.");
}

if ($action === 'delete') {
    $id = (int)($payload['asset_id'] ?? ($payload['id'] ?? 0));
    if ($id <= 0) {
        sendJsonError("Asset ID is required for delete.");
    }

    $stmt = $pdo->prepare("DELETE FROM it_assets WHERE asset_id = :id");
    $stmt->execute([':id' => $id]);

    sendJsonSuccess(['asset_id' => $id], "Asset deleted from inventory.");
}

sendJsonError("Invalid asset action: {$action}");
