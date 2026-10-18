<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Shared Account Services - Neutral Password Management
 * Location: account/change-password.php
 * Serves employees and external customers with strict password governance.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/AuditLogger.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getDbConnection();

// Ensure user is authenticated
if (empty($_SESSION['vostok_authenticated']) || empty($_SESSION['vostok_user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['vostok_user'];
$isCustomer = (($user['account_type'] ?? '') === 'Customer');
$accountId = (int)($user['account_id'] ?? 0);
$userId = $isCustomer ? ($user['cus_id'] ?? $user['user_id'] ?? '') : ($user['emp_id'] ?? $user['user_id'] ?? '');

if ($accountId <= 0) {
    http_response_code(403);
    echo '<h1>No account identifier found in authenticated session.</h1>';
    exit;
}

// Load account record strictly by account_id — never use WHERE cus_id / LIMIT 1
if ($isCustomer) {
    $accStmt = $pdo->prepare("
        SELECT ca.account_id, ca.cus_id, ca.username, ca.email, ca.password_hash, ca.must_change_password, c.company_name 
        FROM customer_accounts ca 
        JOIN customers c ON ca.cus_id = c.cus_id 
        WHERE ca.account_id = ?
    ");
    $accStmt->execute([$accountId]);
} else {
    $accStmt = $pdo->prepare("
        SELECT ea.account_id, ea.emp_id, ea.username, e.email, ea.password_hash, ea.must_change_password 
        FROM employee_accounts ea 
        LEFT JOIN employees e ON ea.emp_id = e.emp_id 
        WHERE ea.account_id = ?
    ");
    $accStmt->execute([$accountId]);
}
$account = $accStmt->fetch(PDO::FETCH_ASSOC);

if (!$account) {
    http_response_code(403);
    echo '<h1>No account found for session user.</h1>';
    exit;
}

$error = '';
$success = '';
$csrfToken = getCsrfToken();

$isJson = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
       || (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json'))
       || (str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json'));

// Resolve return destination
$returnTo = $_GET['return_to'] ?? ($_SESSION['vostok_return_to'] ?? '');
if (empty($returnTo)) {
    $originSys = $_SESSION['vostok_current_system'] ?? '';
    $returnTo = match ($originSys) {
        'CUS' => '../Customer Portal/Dashboard.php',
        'SHP' => '../Online Shop B2B/Dashboard.php',
        'EMP' => '../Employee Intranet/Dashboard.php',
        'CRM' => '../CRM/Dashboard.php',
        'HR'  => '../HR System/Dashboard.php',
        'FIN' => '../Finance & Billing/Dashboard.php',
        'IT'  => '../IT Helpdesk/Dashboard.php',
        'DOC' => '../File Center/Dashboard.php',
        'DEV' => '../Developer/Dashboard.php',
        'ADM' => '../Admin & Governance Portal/mainDashboard.php',
        default => $isCustomer ? '../Customer Portal/Dashboard.php' : '../Employee Intranet/Dashboard.php'
    };
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true);
    if (is_array($jsonData)) {
        $_POST = array_merge($_POST, $jsonData);
    }

    $submittedCsrf = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!verifyCsrfToken($submittedCsrf)) {
        $error = 'Invalid or missing CSRF token.';
    } else {
        $oldPass  = (string)($_POST['old_password'] ?? '');
        $newPass  = (string)($_POST['new_password'] ?? '');
        $confPass = (string)($_POST['confirm_password'] ?? '');

        // 1. Current password verification strictly against this specific account
        if (!password_verify($oldPass, $account['password_hash'])) {
            $error = 'Current password is incorrect.';
        } elseif (strlen($newPass) < 14) {
            // 2. Minimum 14 characters
            $error = 'New password must be at least 14 characters in length.';
        } elseif ($newPass !== $confPass) {
            $error = 'New password and confirmation do not match.';
        } elseif (password_verify($newPass, $account['password_hash'])) {
            $error = 'New password cannot be the same as your current password.';
        } else {
            // 3. Deny-list check (common passwords, username, company name, email)
            $denyWords = ['password', '123456', '12345678', '123456789', 'admin', 'administrator', 'qwerty', 'letmein', 'welcome', 'vostokpribor', 'vostok'];
            $newLower = strtolower($newPass);
            $containsDeny = false;
            foreach ($denyWords as $dw) {
                if (str_contains($newLower, $dw)) {
                    $containsDeny = true;
                    break;
                }
            }

            // Also check company name in deny-list (compacted and word tokens >= 3 chars)
            $companyName = strtolower(trim((string)($account['company_name'] ?? '')));
            if ($companyName !== '') {
                $compClean = preg_replace('/[^a-z0-9]/', '', $companyName);
                if ($compClean !== '' && strlen($compClean) >= 3 && str_contains($newLower, $compClean)) {
                    $containsDeny = true;
                }
                $compWords = preg_split('/[^a-z0-9]+/', $companyName, -1, PREG_SPLIT_NO_EMPTY);
                if (is_array($compWords)) {
                    foreach ($compWords as $cw) {
                        if (strlen($cw) >= 3 && !in_array($cw, ['group', 'ltd', 'inc', 'corp', 'gmbh', 'systems', 'the'], true) && str_contains($newLower, $cw)) {
                            $containsDeny = true;
                            break;
                        }
                    }
                }
            }

            $userLower = strtolower((string)($account['username'] ?? ''));
            $idLower   = strtolower((string)$userId);
            $emailPrefix = '';
            if (!empty($account['email'])) {
                $emailParts = explode('@', (string)$account['email']);
                $emailPrefix = strtolower($emailParts[0] ?? '');
            }

            if ($containsDeny) {
                $error = 'Password contains a common, breached, or forbidden phrase (e.g. company name, common words).';
            } elseif ($userLower !== '' && str_contains($newLower, $userLower)) {
                $error = 'Password must not contain your username.';
            } elseif ($idLower !== '' && str_contains($newLower, $idLower)) {
                $error = 'Password must not contain your account identifier.';
            } elseif ($emailPrefix !== '' && str_contains($newLower, $emailPrefix)) {
                $error = 'Password must not contain your email address.';
            } else {
                // 4. History check (no reuse of the last 5 passwords for this account)
                $accType = $isCustomer ? 'Customer' : 'Employee';
                $histStmt = $pdo->prepare("SELECT password_hash FROM password_history WHERE account_type = :type AND account_id = :aid ORDER BY history_id DESC LIMIT 5");
                $histStmt->execute([':type' => $accType, ':aid' => $accountId]);
                $recentHashes = $histStmt->fetchAll(PDO::FETCH_COLUMN);

                $reused = false;
                foreach ($recentHashes as $pastHash) {
                    if (password_verify($newPass, (string)$pastHash)) {
                        $reused = true;
                        break;
                    }
                }

                if ($reused) {
                    $error = 'You cannot reuse any of your last 5 passwords.';
                } else {
                    // Success path: Store old password into history, set new password for THIS account_id only
                    $newHash = password_hash($newPass, PASSWORD_BCRYPT, ['cost' => 12]);
                    
                    // Archive existing password into history
                    $insHist = $pdo->prepare("INSERT INTO password_history (account_type, account_id, password_hash, created_at) VALUES (?, ?, ?, NOW())");
                    $insHist->execute([$accType, $accountId, $account['password_hash']]);

                    if ($isCustomer) {
                        $pdo->prepare("UPDATE customer_accounts SET password_hash = ?, must_change_password = 0 WHERE account_id = ?")->execute([$newHash, $accountId]);
                        // Revoke active sessions for THIS account only (other accounts in the company remain active)
                        $pdo->prepare("UPDATE user_sessions SET status = 'Revoked', ended_at = CURRENT_TIMESTAMP WHERE customer_account_id = ? AND status = 'Active'")->execute([$accountId]);
                        AuditLogger::logAction(null, (string)$account['cus_id'], 'Customer Portal', 'CUS', 'PASSWORD_CHANGED', 'customer_accounts', (string)$accountId);
                    } else {
                        $pdo->prepare("UPDATE employee_accounts SET password_hash = ?, must_change_password = 0 WHERE account_id = ?")->execute([$newHash, $accountId]);
                        // Revoke active sessions for THIS account only
                        $pdo->prepare("UPDATE user_sessions SET status = 'Revoked', ended_at = CURRENT_TIMESTAMP WHERE employee_account_id = ? AND status = 'Active'")->execute([$accountId]);
                        AuditLogger::logAction((string)$account['emp_id'], null, 'Governance', 'ADM', 'PASSWORD_CHANGED', 'employee_accounts', (string)$accountId);
                    }

                    $success = 'Password successfully updated.';
                    $_SESSION['vostok_user']['must_change_password'] = 0;
                    
                    // Re-issue SSO token with new jti and register new active session for this user
                    $newJti = bin2hex(random_bytes(16));
                    createSsoCookie($_SESSION['vostok_user'], $newJti);
                    registerUserSession($accType, $accountId, $originSys ?: ($isCustomer ? 'CUS' : 'EMP'), $newJti);

                    if (!$isJson) {
                        header("Location: " . $returnTo);
                        exit;
                    }
                }
            }
        }
    }

    if ($isJson) {
        header('Content-Type: application/json; charset=utf-8');
        if ($error) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $error], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(200);
            echo json_encode(['success' => true, 'message' => $success, 'redirect' => $returnTo], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }
}

$mustChange = (bool)($account['must_change_password'] ?? false);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once __DIR__ . '/../includes/head_security.php'; ?>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Change Password — VOSTOKPRIBOR</title>
    <meta name="description" content="Secure account password update for VOSTOKPRIBOR enterprise users"/>
    <style>
        :root {
            --bg: #090d16; --bg2: #131b2e; --accent: #3b82f6; --accent2: #60a5fa;
            --danger: #ef4444; --ok: #10b981; --text: #e2e8f0; --muted: #94a3b8;
            --border: #1e293b; --radius: 8px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: var(--bg); color: var(--text); font-family: 'Segoe UI', system-ui, sans-serif;
            display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px;
        }
        .card {
            background: var(--bg2); border: 1px solid var(--border); border-radius: 12px;
            padding: 36px 40px; width: 100%; max-width: 480px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5);
        }
        .logo-row { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
        .logo-row img { width: 32px; height: 32px; }
        .logo-title { font-size: 14px; font-weight: 700; letter-spacing: 0.05em; color: var(--accent2); text-transform: uppercase; }
        h1 { font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 6px; }
        .sub { color: var(--muted); font-size: 13px; margin-bottom: 24px; }
        .warn-banner {
            background: rgba(239,68,68,.12); border: 1px solid rgba(239,68,68,.4);
            color: #fca5a5; padding: 12px 16px; border-radius: var(--radius);
            margin-bottom: 20px; font-size: 13px;
        }
        label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 6px; font-weight: 500; }
        input[type=password] {
            width: 100%; background: #0c1220; color: var(--text); border: 1px solid var(--border);
            padding: 11px 14px; border-radius: var(--radius); font-size: 14px; margin-bottom: 16px;
            transition: border-color 0.15s;
        }
        input[type=password]:focus { outline: none; border-color: var(--accent); }
        .btn {
            width: 100%; background: var(--accent); color: #fff; border: none;
            padding: 12px; border-radius: var(--radius); font-size: 15px; font-weight: 600;
            cursor: pointer; transition: background 0.15s; margin-top: 8px;
        }
        .btn:hover { background: var(--accent2); }
        .error { background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3); color: var(--danger);
                 padding: 10px 14px; border-radius: var(--radius); font-size: 13px; margin-bottom: 16px; }
        .success { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3); color: var(--ok);
                   padding: 10px 14px; border-radius: var(--radius); font-size: 13px; margin-bottom: 16px; }
        .rules { color: var(--muted); font-size: 12px; margin-top: 20px; border-top: 1px solid var(--border); padding-top: 16px; }
        .rules li { margin: 4px 0 4px 18px; }
        .back { display: inline-block; margin-top: 20px; color: var(--accent2); text-decoration: none; font-size: 13px; }
        .back:hover { text-decoration: underline; }
        .strength { height: 4px; border-radius: 2px; margin-top: -10px; margin-bottom: 16px; background: #0c1220; overflow: hidden; }
        .strength-bar { height: 100%; border-radius: 2px; transition: width 0.3s, background 0.3s; width: 0; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo-row">
        <img src="../assets/logo.svg" alt="VOSTOKPRIBOR"/>
        <div class="logo-title">VOSTOKPRIBOR Access Security</div>
    </div>

    <h1>Account Password</h1>
    <div class="sub"><?= htmlspecialchars($user['full_name'] ?? $userId) ?> (<?= htmlspecialchars($userId) ?>)</div>

    <?php if ($mustChange): ?>
    <div class="warn-banner">⚠ Initial password change is required before accessing enterprise applications.</div>
    <?php endif; ?>

    <?php if ($error): ?><div class="error" id="error-alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success" id="success-alert">✓ <?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="POST" action="" autocomplete="off" id="changePasswordForm">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"/>

        <label for="old_password">Current Password</label>
        <input type="password" id="old_password" name="old_password" required autocomplete="current-password"/>

        <label for="new_password">New Password <span style="color:var(--muted)">(minimum 14 characters)</span></label>
        <input type="password" id="new_password" name="new_password" required autocomplete="new-password"
               minlength="14" oninput="updateStrength(this.value)"/>
        <div class="strength"><div class="strength-bar" id="strength-bar"></div></div>

        <label for="confirm_password">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password"/>

        <button type="submit" class="btn" id="submitBtn">Update Password</button>
    </form>

    <ul class="rules">
        <li>Length: Minimum 14 characters (length-based security)</li>
        <li>Deny-list: No common passwords, company names, or usernames</li>
        <li>History: Cannot reuse any of your last 5 passwords</li>
    </ul>

    <?php if (!$mustChange): ?>
    <a href="<?= htmlspecialchars($returnTo) ?>" class="back" id="backLink">← Return to Application</a>
    <?php endif; ?>
</div>

<script>
function updateStrength(val) {
    const bar = document.getElementById('strength-bar');
    let score = 0;
    if (val.length >= 14) score += 2;
    if (val.length >= 18) score += 1;
    if (val.length >= 24) score += 1;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score += 1;
    const pct = Math.min(100, (score / 5) * 100);
    const colors = ['#ef4444','#f59e0b','#eab308','#22c55e','#10b981'];
    bar.style.width = pct + '%';
    bar.style.background = colors[Math.max(0, score - 1)] || '#ef4444';
}
</script>
</body>
</html>
