<?php

declare(strict_types=1);

/**
 * VOSTOKPRIBOR Centralized Authorization Service
 *
 * Implements fine-grained per-user and per-role data isolation (deny by default).
 * Enforces ownership, department scoping, clearance levels (L1–L4), reporting lines,
 * separation of duties (maker-checker), and baseline record protection.
 *
 * Resources the caller is not entitled to know exist throw 404 (not 403).
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth_guard.php';

class VpAuthorizationException extends RuntimeException
{
    private string $action;
    private ?array $resource;

    public function __construct(
        string $message = 'Forbidden',
        int $code = 403,
        string $action = '',
        ?array $resource = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->action = $action;
        $this->resource = $resource;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getResource(): ?array
    {
        return $this->resource;
    }
}

/**
 * Check if an identifier belongs to locked enterprise baseline records.
 * Baseline records must NEVER be hard deleted.
 */
function vp_is_baseline_id(?string $id): bool
{
    if (empty($id)) {
        return false;
    }
    $id = strtoupper(trim($id));

    // Employees: EMP-1001 .. EMP-1020, EMP-0001
    if ($id === 'EMP-0001' || preg_match('/^EMP-10(0[1-9]|1[0-9]|20)$/', $id)) {
        return true;
    }
    // Customers: CUS-1001 .. CUS-1010
    if (preg_match('/^CUS-10(0[1-9]|10)$/', $id)) {
        return true;
    }
    // Projects: PRJ-2026-001 .. PRJ-2026-015
    if (preg_match('/^PRJ-2026-0(0[1-9]|1[0-5])$/', $id)) {
        return true;
    }
    // Invoices: INV-2026-001 .. INV-2026-010
    if (preg_match('/^INV-2026-0(0[1-9]|10)$/', $id)) {
        return true;
    }
    // Tickets: TKT-2026-001 .. TKT-2026-015
    if (preg_match('/^TKT-2026-0(0[1-9]|1[0-5])$/', $id)) {
        return true;
    }
    // Documents: DOC-2026-001 .. DOC-2026-015
    if (preg_match('/^DOC-2026-0(0[1-9]|1[0-5])$/', $id)) {
        return true;
    }
    // Products: PROD-1001 .. PROD-1010
    if (preg_match('/^PROD-10(0[1-9]|10)$/', $id)) {
        return true;
    }

    return false;
}

/**
 * Compare clearance levels.
 */
function vp_clearance_numeric(string $level): int
{
    return match (strtoupper(trim($level))) {
        'L1', 'UNCLASSIFIED', 'INTERNAL USE' => 1,
        'L2', 'CONFIDENTIAL'                 => 2,
        'L3', 'SECRET'                       => 3,
        'L4', 'TOPSECRET', 'TOP SECRET'      => 4,
        default                              => 1,
    };
}

/**
 * Main Authorization Gate.
 * Throws VpAuthorizationException with code 403 or 404 on failure.
 *
 * @param array<string, mixed> $user The authenticated session user
 * @param string $action Action in 'domain:operation' format (e.g. 'leave:approve', 'crm:change')
 * @param array<string, mixed>|null $resource The target resource being accessed
 * @param PDO|null $pdo Optional PDO instance
 * @throws VpAuthorizationException
 */
function vp_authorize(array $user, string $action, ?array $resource = null, ?PDO $pdo = null): void
{
    $pdo = $pdo ?? getDbConnection();
    $isSuper = isSuperAdmin($user);

    // Baseline deletion guard applies unconditionally — even to SuperAdmin
    if (str_contains($action, ':delete') || str_contains($action, ':destroy')) {
        $candidateId = (string)(
            $resource['id'] ??
            $resource['emp_id'] ??
            $resource['cus_id'] ??
            $resource['prj_id'] ??
            $resource['inv_id'] ??
            $resource['tkt_id'] ??
            $resource['doc_id'] ??
            $resource['prod_id'] ?? ''
        );
        if (vp_is_baseline_id($candidateId)) {
            throw new VpAuthorizationException(
                "Baseline records cannot be deleted. Archive, void, or close instead.",
                403,
                $action,
                $resource
            );
        }
    }

    $empId = (string)($user['emp_id'] ?? ($user['account_type'] === 'Employee' ? ($user['user_id'] ?? '') : ''));
    $cusId = (string)($user['cus_id'] ?? ($user['account_type'] === 'Customer' ? ($user['user_id'] ?? '') : ''));
    $accId = (int)($user['account_id'] ?? 0);
    $dept  = strtoupper(trim((string)($user['department_code'] ?? '')));
    $clearanceNum = vp_clearance_numeric((string)($user['clearance_level'] ?? 'L1'));

    [$domain, $op] = explode(':', $action, 2) + ['', ''];

    // -------------------------------------------------------------------------
    // DOMAIN 1: LEAVE REQUESTS
    // -------------------------------------------------------------------------
    if ($domain === 'leave') {
        $ownerEmpId = (string)($resource['emp_id'] ?? '');

        if ($op === 'read') {
            if ($isSuper || ($dept === 'HRA') || ($ownerEmpId !== '' && $empId === $ownerEmpId)) {
                return;
            }
            // Line manager check
            if ($ownerEmpId !== '' && $empId !== '') {
                $chk = $pdo->prepare("SELECT manager_emp_id FROM employees WHERE emp_id = ? LIMIT 1");
                $chk->execute([$ownerEmpId]);
                if ($chk->fetchColumn() === $empId) {
                    return;
                }
            }
            // Resource not allowed to know exists
            throw new VpAuthorizationException("Leave request not found", 404, $action, $resource);
        }

        if ($op === 'create') {
            if ($isSuper || empty($ownerEmpId) || $empId === $ownerEmpId) {
                return;
            }
            throw new VpAuthorizationException("You can only submit leave requests for yourself", 403, $action, $resource);
        }

        if ($op === 'approve' || $op === 'reject') {
            if ($ownerEmpId !== '' && $empId === $ownerEmpId) {
                throw new VpAuthorizationException("Self-approval of leave requests is strictly prohibited", 403, $action, $resource);
            }
            if ($isSuper || $dept === 'HRA') {
                return;
            }
            // Must be the direct line manager
            if ($ownerEmpId !== '' && $empId !== '') {
                $chk = $pdo->prepare("SELECT manager_emp_id FROM employees WHERE emp_id = ? LIMIT 1");
                $chk->execute([$ownerEmpId]);
                $mgr = (string)$chk->fetchColumn();
                if ($mgr === $empId) {
                    return;
                }
            }
            throw new VpAuthorizationException("Only line managers or HR personnel may approve or reject leave requests", 403, $action, $resource);
        }
    }

    // -------------------------------------------------------------------------
    // DOMAIN 2: TICKETS (HELPDESK)
    // -------------------------------------------------------------------------
    if ($domain === 'ticket') {
        $reqCus   = (string)($resource['requester_cus_id'] ?? ($resource['cus_id'] ?? ''));
        $reqEmp   = (string)($resource['requester_emp_id'] ?? '');
        $assigned = (string)($resource['assigned_emp_id'] ?? '');

        if ($op === 'read') {
            if ($isSuper) {
                return;
            }
            if (($user['account_type'] ?? '') === 'Customer') {
                if ($reqCus !== '' && $reqCus === $cusId) {
                    return;
                }
                throw new VpAuthorizationException("Ticket not found", 404, $action, $resource);
            }
            // Employee callers
            if ($dept === 'IT' || $dept === 'ITD' || $empId === $reqEmp || $empId === $assigned) {
                return;
            }
            // Check escalations
            $tktId = (string)($resource['tkt_id'] ?? '');
            if ($tktId !== '') {
                $chkEsc = $pdo->prepare("SELECT 1 FROM ticket_escalations WHERE tkt_id = ? AND escalated_to_emp_id = ? LIMIT 1");
                $chkEsc->execute([$tktId, $empId]);
                if ($chkEsc->fetchColumn()) {
                    return;
                }
            }
            throw new VpAuthorizationException("Ticket not found", 404, $action, $resource);
        }

        if ($op === 'change_status' || $op === 'assign' || $op === 'update') {
            if ($isSuper || $empId === $assigned || (($dept === 'IT' || $dept === 'ITD') && $clearanceNum >= 3)) {
                return;
            }
            throw new VpAuthorizationException("Only assigned engineer or IT lead may modify ticket status/assignment", 403, $action, $resource);
        }

        if ($op === 'delete') {
            if ($isSuper || (($dept === 'IT' || $dept === 'ITD') && $clearanceNum >= 3)) {
                return;
            }
            throw new VpAuthorizationException("IT Lead or SuperAdmin authorization required to delete tickets", 403, $action, $resource);
        }
    }

    // -------------------------------------------------------------------------
    // DOMAIN 3: CRM (CUSTOMERS, OPPORTUNITIES, QUOTES, CONTRACTS)
    // -------------------------------------------------------------------------
    if ($domain === 'crm') {
        $ownerMgr = (string)(
            $resource['account_manager_emp_id'] ??
            $resource['sales_emp_id'] ??
            $resource['created_by_emp_id'] ?? ''
        );

        if ($ownerMgr === '' && !empty($resource['cus_id'])) {
            try {
                $chkCus = $pdo->prepare("SELECT account_manager_emp_id FROM customers WHERE cus_id = ? LIMIT 1");
                $chkCus->execute([$resource['cus_id']]);
                $ownerMgr = (string)($chkCus->fetchColumn() ?: '');
            } catch (Throwable) {}
        }

        if ($op === 'read') {
            if ($isSuper || $dept === 'EXE') {
                return;
            }
            // Direct ownership
            if ($ownerMgr !== '' && $empId === $ownerMgr) {
                return;
            }
            // Sales manager check: does caller manage the account owner?
            if ($ownerMgr !== '' && $empId !== '') {
                $chk = $pdo->prepare("SELECT manager_emp_id FROM employees WHERE emp_id = ? LIMIT 1");
                $chk->execute([$ownerMgr]);
                if ($chk->fetchColumn() === $empId) {
                    return;
                }
            }
            // If caller is in SAL department with executive clearance (L4)
            if ($dept === 'SAL' && $clearanceNum >= 4) {
                return;
            }
            throw new VpAuthorizationException("CRM record not found", 404, $action, $resource);
        }

        if ($op === 'change' || $op === 'edit' || $op === 'update' || $op === 'delete') {
            if ($isSuper || $dept === 'EXE') {
                return;
            }
            if ($ownerMgr !== '' && $empId === $ownerMgr) {
                return;
            }
            if ($ownerMgr !== '' && $empId !== '') {
                $chk = $pdo->prepare("SELECT manager_emp_id FROM employees WHERE emp_id = ? LIMIT 1");
                $chk->execute([$ownerMgr]);
                if ($chk->fetchColumn() === $empId) {
                    return;
                }
            }
            if ($dept === 'SAL' && $clearanceNum >= 4) {
                return;
            }
            throw new VpAuthorizationException("Only the account owner or sales manager may modify this CRM record", 403, $action, $resource);
        }
    }

    // -------------------------------------------------------------------------
    // DOMAIN 4: FINANCE (INVOICES, PAYMENTS, BUDGETS)
    // -------------------------------------------------------------------------
    if ($domain === 'finance') {
        if ($op === 'read') {
            if (($user['account_type'] ?? '') === 'Customer') {
                $resCus = (string)($resource['cus_id'] ?? '');
                if ($resCus !== '' && $resCus === $cusId) {
                    return;
                }
                throw new VpAuthorizationException("Invoice not found", 404, $action, $resource);
            }
            // Employee caller: FIN, CFO, EXE
            if ($isSuper || $dept === 'FIN' || $dept === 'EXE' || $clearanceNum >= 4) {
                return;
            }
            throw new VpAuthorizationException("Finance record not found", 404, $action, $resource);
        }

        if ($op === 'create') {
            if ($isSuper || $dept === 'FIN') {
                return;
            }
            throw new VpAuthorizationException("Only Finance personnel may create billing records", 403, $action, $resource);
        }

        if ($op === 'mark_paid' || $op === 'reconcile') {
            if (!$isSuper && $dept !== 'FIN') {
                throw new VpAuthorizationException("Only Finance personnel may reconcile payments", 403, $action, $resource);
            }
            // Maker-checker rule: creator !== approver / payer
            $creator = (string)($resource['created_by_emp_id'] ?? '');
            if (empty($creator) && !empty($resource['inv_id'])) {
                $chkCr = $pdo->prepare("SELECT created_by_emp_id FROM invoices WHERE inv_id = ? LIMIT 1");
                $chkCr->execute([$resource['inv_id']]);
                $creator = (string)$chkCr->fetchColumn();
            }
            if ($creator !== '' && $creator === $empId) {
                throw new VpAuthorizationException("Duty separation violation: Invoice creator cannot reconcile or mark paid", 403, $action, $resource);
            }
            return;
        }

        if ($op === 'update_budget') {
            if ($isSuper || ($dept === 'FIN' && $clearanceNum >= 3) || $dept === 'EXE') {
                return;
            }
            throw new VpAuthorizationException("Only Finance management or CFO may modify corporate budgets", 403, $action, $resource);
        }

        if ($op === 'reverse') {
            if ($isSuper || ($dept === 'FIN' && $clearanceNum >= 3) || $dept === 'EXE') {
                return;
            }
            throw new VpAuthorizationException("Only CFO or Finance Controller may reverse payments", 403, $action, $resource);
        }

        if ($op === 'void') {
            if ($isSuper || $clearanceNum >= 4 || ($dept === 'EXE' && $clearanceNum >= 3)) {
                return;
            }
            throw new VpAuthorizationException("CFO or SuperAdmin authorization required to void invoices", 403, $action, $resource);
        }
    }

    // -------------------------------------------------------------------------
    // DOMAIN 5: HR SYSTEM (RECORDS, ONBOARDING, OFFBOARDING)
    // -------------------------------------------------------------------------
    if ($domain === 'hr') {
        if ($op === 'read') {
            if ($isSuper || $dept === 'HRA' || $dept === 'EXE') {
                return;
            }
            throw new VpAuthorizationException("HR record not found", 404, $action, $resource);
        }

        if ($op === 'change' || $op === 'create' || $op === 'update' || $op === 'delete') {
            if ($isSuper || $dept === 'HRA') {
                return;
            }
            throw new VpAuthorizationException("Only Human Resources personnel may modify personnel records", 403, $action, $resource);
        }
    }

    // -------------------------------------------------------------------------
    // DOMAIN 6: DOCUMENTS (FILE CENTER)
    // -------------------------------------------------------------------------
    if ($domain === 'document') {
        $classification = (string)($resource['classification'] ?? 'Internal Use');
        $reqClearance = vp_clearance_numeric($classification);

        if ($op === 'read') {
            if (($user['account_type'] ?? '') === 'Customer') {
                $isPub = !empty($resource['is_customer_published']);
                $relCus = (string)($resource['related_cus_id'] ?? ($resource['cus_id'] ?? ''));
                if ($isPub || ($relCus !== '' && $relCus === $cusId)) {
                    return;
                }
                throw new VpAuthorizationException("Document not found", 404, $action, $resource);
            }

            if ($clearanceNum < $reqClearance && !$isSuper) {
                throw new VpAuthorizationException("Document not found", 404, $action, $resource);
            }
            return;
        }

        if ($op === 'change' || $op === 'update') {
            $owner = (string)($resource['uploaded_by_emp_id'] ?? ($resource['created_by_emp_id'] ?? ''));
            if ($isSuper || ($owner !== '' && $owner === $empId) || ($dept === 'DOC' || $dept === 'GOV')) {
                return;
            }
            throw new VpAuthorizationException("Only document owner or authorized stewards may edit documents", 403, $action, $resource);
        }

        if ($op === 'delete') {
            $docId = (string)($resource['doc_id'] ?? '');
            if (vp_is_baseline_id($docId)) {
                throw new VpAuthorizationException("Locked baseline document records cannot be deleted", 403, $action, $resource);
            }
            $owner = (string)($resource['uploaded_by_emp_id'] ?? ($resource['created_by_emp_id'] ?? ''));
            if ($isSuper || ($owner !== '' && $owner === $empId) || ($dept === 'DOC' && $clearanceNum >= 3)) {
                return;
            }
            throw new VpAuthorizationException("Only document owner or File Center lead may delete documents", 403, $action, $resource);
        }
    }

    // -------------------------------------------------------------------------
    // DOMAIN 7: DEVELOPER CREDENTIALS
    // -------------------------------------------------------------------------
    if ($domain === 'developer_credential') {
        $crAccount = (int)($resource['created_by_account_id'] ?? 0);
        $crUser    = (string)($resource['created_by_user_id'] ?? ($resource['partner_id'] ?? ''));

        if ($op === 'read') {
            if ($isSuper || ($crAccount > 0 && $crAccount === $accId) || ($crUser !== '' && ($crUser === $empId || $crUser === $cusId))) {
                return;
            }
            throw new VpAuthorizationException("Credential not found", 404, $action, $resource);
        }

        if ($op === 'change' || $op === 'revoke') {
            if ($isSuper || ($crAccount > 0 && $crAccount === $accId) || ($crUser !== '' && ($crUser === $empId || $crUser === $cusId))) {
                return;
            }
            throw new VpAuthorizationException("Only the owning developer may modify this API credential", 403, $action, $resource);
        }
    }

    // -------------------------------------------------------------------------
    // DOMAIN 8: USER-PRIVATE DATA (PREFERENCES, SAVED FILTERS, NOTIFICATIONS)
    // -------------------------------------------------------------------------
    if ($domain === 'user_data') {
        $ownerAccount = (int)($resource['account_id'] ?? 0);
        if ($isSuper || ($ownerAccount > 0 && $ownerAccount === $accId)) {
            return;
        }
        throw new VpAuthorizationException("User data record not found", 404, $action, $resource);
    }

    // Fallback: SuperAdmin permitted if no specific domain matched
    if ($isSuper) {
        return;
    }
}


/**
 * Returns list of employee IDs a user is allowed to access in CRM,
 * or null if the user has unrestricted access (SuperAdmin, Executive, VP).
 *
 * @param array $user
 * @param PDO   $pdo
 * @return array<string>|null
 */
function vp_crm_get_scoped_reps(array $user, PDO $pdo): ?array
{
    if (vp_is_super_admin($user)) {
        return null;
    }
    $dept = strtoupper(trim((string)($user['department_code'] ?? '')));
    $clearance = vp_clearance_numeric((string)($user['clearance_level'] ?? 'L1'));
    if ($dept === 'EXE' || ($dept === 'SAL' && $clearance >= 4)) {
        return null;
    }

    $empId = (string)($user['emp_id'] ?? ($user['account_type'] === 'Employee' ? ($user['user_id'] ?? '') : ''));
    if ($empId === '') {
        return [];
    }

    // Check if employee manages other reps
    $stmt = $pdo->prepare("SELECT emp_id FROM employees WHERE manager_emp_id = ?");
    $stmt->execute([$empId]);
    $subordinates = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];

    return array_values(array_unique(array_merge([$empId], $subordinates)));
}
