<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Customer Portal - API Customer Session & Identity Resolver
 */

function getActiveCustomerPortalId(PDO $pdo): string
{
    if (!empty($_GET['cus_id']) && !str_starts_with((string)$_GET['cus_id'], 'EMP-')) {
        $_SESSION['cus_id'] = (string)$_GET['cus_id'];
        return (string)$_GET['cus_id'];
    }
    if (!empty($_SESSION['cus_id']) && !str_starts_with((string)$_SESSION['cus_id'], 'EMP-')) {
        return (string)$_SESSION['cus_id'];
    }
    if (!empty($_SESSION['vostok_user']['cus_id']) && !str_starts_with((string)$_SESSION['vostok_user']['cus_id'], 'EMP-')) {
        $_SESSION['cus_id'] = (string)$_SESSION['vostok_user']['cus_id'];
        return (string)$_SESSION['cus_id'];
    }
    if (!empty($_SESSION['vostok_user']['user_id']) && str_starts_with((string)$_SESSION['vostok_user']['user_id'], 'CUS-')) {
        $_SESSION['cus_id'] = (string)$_SESSION['vostok_user']['user_id'];
        return (string)$_SESSION['cus_id'];
    }
    try {
        $cid = $pdo->query("SELECT cus_id FROM customers ORDER BY cus_id ASC LIMIT 1")->fetchColumn();
        if ($cid) {
            $_SESSION['cus_id'] = (string)$cid;
            return (string)$cid;
        }
    } catch (Throwable $e) {}
    $_SESSION['cus_id'] = 'CUS-1001';
    return 'CUS-1001';
}
