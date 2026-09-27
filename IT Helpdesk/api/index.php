<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR IT Helpdesk API Gateway Router
 */

require_once __DIR__ . '/db_helper.php';

$pdo = getItDb();

$ticketsCount = (int)$pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
$openTickets = (int)$pdo->query("SELECT COUNT(*) FROM tickets WHERE status != 'Resolved'")->fetchColumn();
$assetsCount = (int)$pdo->query("SELECT COUNT(*) FROM it_assets")->fetchColumn();
$kbCount = (int)$pdo->query("SELECT COUNT(*) FROM knowledge_base_articles")->fetchColumn();

sendJsonSuccess([
    'system'       => 'VOSTOKPRIBOR IT Helpdesk & Support Operations (SYS-08)',
    'api_version'  => '2.4.0',
    'status'       => 'ONLINE',
    'stats'        => [
        'total_tickets'  => $ticketsCount,
        'open_tickets'   => $openTickets,
        'managed_assets' => $assetsCount,
        'kb_articles'    => $kbCount,
    ],
    'endpoints'    => [
        'tickets'   => 'api/tickets.php',
        'comments'  => 'api/comments.php',
        'assets'    => 'api/assets.php',
        'knowledge' => 'api/knowledge.php',
        'sla'       => 'api/sla.php',
    ],
], 'IT Helpdesk API Router active and connected to MySQL database.');
