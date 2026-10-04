<?php
declare(strict_types=1);
/**
 * VOSTOKPRIBOR — SuperAdmin Change Password
 * Location: Admin & Governance Portal/ChangePassword.php
 * Access:   Any authenticated employee (session required).
 *           SuperAdmin (EMP-0001) is prompted on first login if must_change_password = 1.
 *
 * Rules enforced:
 *  - New password ≥ 14 characters
 *  - New password ≠ username, emp_id, or email
 *  - Old password must be verified
 *  - Cannot self-delete account via this form
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';
require_once __DIR__ . '/../includes/AuditLogger.php';

if (empty($_SESSION['vostok_authenticated']) || empty($_SESSION['vostok_user'])) {
    header("Location: ../index.php");
    exit;
}

$pdo   = getDbConnection();
$user  = $_SESSION['vostok_user'] ?? [];
$isCustomer = (($user['account_type'] ?? '') === 'Customer');
$userId = $user['emp_id'] ?? ($user['cus_id'] ?? ($user['user_id'] ?? ''));

// Load account data
if ($isCustomer) {
    $accStmt = $pdo->prepare("SELECT account_id, username, email, password_hash, must_change_password FROM customer_accounts WHERE cus_id = ? LIMIT 1");
    $accStmt->execute([$userId]);
} else {
    $accStmt = $pdo->prepare("SELECT ea.account_id, ea.username, e.email, ea.password_hash, ea.must_change_password FROM employee_accounts ea LEFT JOIN employees e ON ea.emp_id = e.emp_id WHERE ea.emp_id = ? LIMIT 1");
    $accStmt->execute([$userId]);
}
$account = $accStmt->fetch(PDO::FETCH_ASSOC);

if (!$account) {
    http_response_code(403);
    echo '<h1>No account found for session user.</h1>';
    exit;
}

$error   = '';
$success = '';
$csrfToken = getCsrfToken();

$isJson = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
       || (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json'))
       || (str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true);
    if (is_array($jsonData)) {
        $_POST = array_merge($_POST, $jsonData);
    }

    // CSRF check
    if (!verifyCsrfToken($_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''))) {
        $error = 'Invalid or missing CSRF token.';
    } else {
        $oldPass  = $_POST['old_password'] ?? '';
        $newPass  = $_POST['new_password'] ?? '';
        $confPass = $_POST['confirm_password'] ?? '';

        if (!password_verify($oldPass, $account['password_hash'])) {
            $error = 'Current password is incorrect.';
        } elseif (strlen($newPass) < 14) {
            $error = 'New password must be at least 14 characters.';
        } elseif ($newPass !== $confPass) {
            $error = 'New password and confirmation do not match.';
        } elseif (
            stripos($newPass, $account['username'] ?? '') !== false ||
            stripos($newPass, (string)$userId) !== false ||
            (!empty($account['email']) && stripos($newPass, $account['email']) !== false)
        ) {
            $error = 'Password must not contain your username, ID, or email address.';
        } else {
            $hash = password_hash($newPass, PASSWORD_BCRYPT, ['cost' => 12]);
            if ($isCustomer) {
                $pdo->prepare("UPDATE customer_accounts SET password_hash = ?, must_change_password = 0 WHERE cus_id = ?")->execute([$hash, $userId]);
                AuditLogger::logAction(null, $userId, 'Customer Portal', 'CUS', 'PASSWORD_CHANGED', 'customer_accounts', (string)$account['account_id']);
            } else {
                $pdo->prepare("UPDATE employee_accounts SET password_hash = ?, must_change_password = 0 WHERE emp_id = ?")->execute([$hash, $userId]);
                AuditLogger::logAction($userId, null, 'Governance', 'ADM', 'PASSWORD_CHANGED', 'employee_accounts', (string)$account['account_id']);
            }

            $success = 'Password changed successfully.';

            // Update session flag
            if (isset($_SESSION['vostok_user']['must_change_password'])) {
                $_SESSION['vostok_user']['must_change_password'] = 0;
            }
        }
    }

    if ($isJson) {
        header('Content-Type: application/json; charset=utf-8');
        if ($error) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $error]);
        } else {
            echo json_encode(['success' => true, 'message' => $success]);
        }
        exit;
    }
}

$mustChange = (bool)($account['must_change_password'] ?? false);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Change Password — VOSTOKPRIBOR</title>
    <meta name="description" content="Secure password change for VOSTOKPRIBOR administrators"/>
    <style>
        :root {
            --bg: #0a0f1e; --bg2: #111827; --accent: #6366f1; --accent2: #818cf8;
            --danger: #ef4444; --ok: #10b981; --text: #e2e8f0; --muted: #94a3b8;
            --border: #1e293b; --radius: 8px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: var(--bg); color: var(--text); font-family: 'Segoe UI', system-ui, sans-serif;
               display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card {
            background: var(--bg2); border: 1px solid var(--border); border-radius: 12px;
            padding: 36px 40px; width: 100%; max-width: 460px;
        }
        h1 { font-size: 20px; font-weight: 700; color: var(--accent2); margin-bottom: 6px; }
        .sub { color: var(--muted); font-size: 13px; margin-bottom: 24px; }
        .warn-banner {
            background: rgba(239,68,68,.12); border: 1px solid rgba(239,68,68,.4);
            color: #fca5a5; padding: 12px 16px; border-radius: var(--radius);
            margin-bottom: 20px; font-size: 13px;
        }
        label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 5px; }
        input[type=password] {
            width: 100%; background: #1e293b; color: var(--text); border: 1px solid var(--border);
            padding: 10px 14px; border-radius: var(--radius); font-size: 14px; margin-bottom: 16px;
        }
        input[type=password]:focus { outline: 2px solid var(--accent); }
        .btn {
            width: 100%; background: var(--accent); color: #fff; border: none;
            padding: 12px; border-radius: var(--radius); font-size: 15px; font-weight: 600;
            cursor: pointer; transition: opacity 0.15s;
        }
        .btn:hover { opacity: 0.85; }
        .error { background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3); color: var(--danger);
                 padding: 10px 14px; border-radius: var(--radius); font-size: 13px; margin-bottom: 14px; }
        .success { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3); color: var(--ok);
                   padding: 10px 14px; border-radius: var(--radius); font-size: 13px; margin-bottom: 14px; }
        .rules { color: var(--muted); font-size: 12px; margin-top: 16px; }
        .rules li { margin: 3px 0 3px 18px; }
        .back { display: inline-block; margin-top: 20px; color: var(--accent2); text-decoration: none; font-size: 13px; }
        .strength { height: 4px; border-radius: 2px; margin-bottom: 16px; background: #1e293b; position: relative; }
        .strength-bar { height: 100%; border-radius: 2px; transition: width 0.3s, background 0.3s; width: 0; }
    </style>
</head>
<body>
<div class="card">
    <h1>🔐 Change Password</h1>
    <div class="sub"><?= htmlspecialchars($user['full_name'] ?? $empId) ?> — <?= htmlspecialchars($empId) ?></div>

    <?php if ($mustChange): ?>
    <div class="warn-banner">⚠ You must set a new password before continuing.</div>
    <?php endif; ?>

    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="success">✓ <?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="POST" action="" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>"/>

        <label for="old_password">Current Password</label>
        <input type="password" id="old_password" name="old_password" required autocomplete="current-password"/>

        <label for="new_password">New Password <span style="color:var(--muted)">(min. 14 characters)</span></label>
        <input type="password" id="new_password" name="new_password" required autocomplete="new-password"
               minlength="14" oninput="updateStrength(this.value)"/>
        <div class="strength"><div class="strength-bar" id="strength-bar"></div></div>

        <label for="confirm_password">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password"/>

        <button type="submit" class="btn">Change Password</button>
    </form>

    <ul class="rules">
        <li>Minimum 14 characters</li>
        <li>Must not contain your username, employee ID, or email</li>
        <li>Current password is required to confirm identity</li>
    </ul>

    <?php if (!$mustChange): ?>
    <a href="mainDashboard.php" class="back">← Back to Dashboard</a>
    <?php endif; ?>
</div>

<script>
function updateStrength(val) {
    const bar = document.getElementById('strength-bar');
    let score = 0;
    if (val.length >= 14) score++;
    if (val.length >= 20) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const pct = (score / 5) * 100;
    const colors = ['#ef4444','#f59e0b','#eab308','#22c55e','#10b981'];
    bar.style.width = pct + '%';
    bar.style.background = colors[Math.max(0, score - 1)] || '#ef4444';
}
</script>
</body>
</html>
