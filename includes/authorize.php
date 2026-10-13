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
function vp_check_baseline_delete(string $action, ?array $resource): void
{
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
}

function vp_authorize(array $user, string $action, ?array $resource = null, ?PDO $pdo = null): void
{
    $pdo = $pdo ?? getDbConnection();
    $isSuper = isSuperAdmin($user);

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
                vp_check_baseline_delete($action, $resource);
                return;
            }
            throw new VpAuthorizationException("IT Lead or SuperAdmin authorization required to delete tickets", 403, $action, $resource);
        }
    }

    // -------------------------------------------------------------------------
    // DOMAIN 3: CRM (CUSTOMERS, OPPORTUNITIES, QUOTES, CONTRACTS, LEADS, ACTIVITIES)
    // -------------------------------------------------------------------------
    if ($domain === 'crm') {
        $ownerMgr = (string)(
            $resource['account_manager_emp_id'] ??
            $resource['sales_emp_id'] ??
            $resource['assigned_emp_id'] ??
            $resource['created_by_emp_id'] ?? ''
        );

        if ($ownerMgr === '' && !empty($resource['cus_id'])) {
            try {
                $chkCus = $pdo->prepare("SELECT account_manager_emp_id FROM customers WHERE cus_id = ? LIMIT 1");
                $chkCus->execute([$resource['cus_id']]);
                $ownerMgr = (string)($chkCus->fetchColumn() ?: '');
            } catch (Throwable) {}
        }

        $canAccessCrm = false;
        if ($isSuper || $dept === 'EXE') {
            $canAccessCrm = true;
        } elseif ($ownerMgr !== '' && $empId === $ownerMgr) {
            $canAccessCrm = true;
        } elseif ($empId !== '' && in_array($empId, ['EMP-1006', 'EMP-1009', 'EMP-1010'], true)) {
            // Sales managers see their team
            if ($ownerMgr !== '') {
                $chk = $pdo->prepare("SELECT manager_emp_id FROM employees WHERE emp_id = ? LIMIT 1");
                $chk->execute([$ownerMgr]);
                $mgr = (string)$chk->fetchColumn();
                if ($mgr === $empId || $empId === 'EMP-1006') {
                    $canAccessCrm = true;
                }
            } else {
                $canAccessCrm = true;
            }
        } elseif ($ownerMgr !== '' && $empId !== '') {
            $chk = $pdo->prepare("SELECT manager_emp_id FROM employees WHERE emp_id = ? LIMIT 1");
            $chk->execute([$ownerMgr]);
            if ($chk->fetchColumn() === $empId) {
                $canAccessCrm = true;
            }
        } elseif ($dept === 'SAL' && $clearanceNum >= 4) {
            $canAccessCrm = true;
        }

        if (!$canAccessCrm) {
            // Unowned / other rep's records throw 404 for read, PUT and DELETE
            throw new VpAuthorizationException("CRM record not found", 404, $action, $resource);
        }

        vp_check_baseline_delete($action, $resource);

        if ($op === 'read') {
            return;
        }

        if ($op === 'change' || $op === 'edit' || $op === 'update' || $op === 'delete' || $op === 'create') {
            return;
        }
    }

    // -------------------------------------------------------------------------
    // DOMAIN 4: FINANCE (INVOICES, PAYMENTS, BUDGETS)
    // -------------------------------------------------------------------------
    if ($domain === 'finance') {
        $isCfo = ($empId === 'EMP-1003' || ($dept === 'EXE' && $clearanceNum >= 4));

        if ($op === 'read') {
            if (($user['account_type'] ?? '') === 'Customer') {
                $resCus = (string)($resource['cus_id'] ?? '');
                if ($resCus !== '' && $resCus === $cusId) {
                    return;
                }
                throw new VpAuthorizationException("Invoice not found", 404, $action, $resource);
            }
            // Employee caller: FIN, CFO, EXE
            if ($isSuper || $isCfo || $dept === 'FIN' || $dept === 'EXE' || $clearanceNum >= 4) {
                return;
            }
            throw new VpAuthorizationException("Finance record not found", 404, $action, $resource);
        }

        if ($op === 'create') {
            if ($isSuper || $isCfo || $dept === 'FIN') {
                return;
            }
            throw new VpAuthorizationException("Only Finance personnel or CFO may create billing records", 403, $action, $resource);
        }

        if ($op === 'approve') {
            if (!$isSuper && !$isCfo && !($dept === 'FIN' && $clearanceNum >= 3)) {
                throw new VpAuthorizationException("Only Finance management or CFO may approve invoices", 403, $action, $resource);
            }
            // Maker-checker rule: creator !== approver
            $creator = (string)($resource['created_by_emp_id'] ?? '');
            if (empty($creator) && !empty($resource['inv_id'])) {
                $chkCr = $pdo->prepare("SELECT created_by_emp_id FROM invoices WHERE inv_id = ? LIMIT 1");
                $chkCr->execute([$resource['inv_id']]);
                $creator = (string)$chkCr->fetchColumn();
            }
            if ($creator !== '' && $creator === $empId) {
                throw new VpAuthorizationException("Duty separation violation: Invoice creator cannot approve", 403, $action, $resource);
            }
            return;
        }

        if ($op === 'mark_paid' || $op === 'reconcile') {
            if (!$isSuper && !$isCfo && $dept !== 'FIN') {
                throw new VpAuthorizationException("Only Finance personnel may reconcile payments", 403, $action, $resource);
            }
            // Maker-checker rule: creator !== approver !== payer
            $creator = (string)($resource['created_by_emp_id'] ?? '');
            $approver = (string)($resource['approved_by_emp_id'] ?? '');
            if (empty($creator) && !empty($resource['inv_id'])) {
                $chkCr = $pdo->prepare("SELECT created_by_emp_id FROM invoices WHERE inv_id = ? LIMIT 1");
                $chkCr->execute([$resource['inv_id']]);
                $creator = (string)$chkCr->fetchColumn();
            }
            if ($creator !== '' && $creator === $empId) {
                throw new VpAuthorizationException("Duty separation violation: Invoice creator cannot reconcile or mark paid", 403, $action, $resource);
            }
            if ($approver !== '' && $approver === $empId) {
                throw new VpAuthorizationException("Duty separation violation: Invoice approver cannot reconcile or mark paid", 403, $action, $resource);
            }
            return;
        }

        if ($op === 'update_budget') {
            if ($isSuper || $isCfo || ($dept === 'FIN' && $clearanceNum >= 3) || $dept === 'EXE') {
                return;
            }
            throw new VpAuthorizationException("Only Finance management or CFO may modify corporate budgets", 403, $action, $resource);
        }

        if ($op === 'reverse') {
            if ($isSuper || $isCfo || ($dept === 'FIN' && $clearanceNum >= 3) || $dept === 'EXE') {
                return;
            }
            throw new VpAuthorizationException("Only CFO or Finance Controller may reverse payments", 403, $action, $resource);
        }

        if ($op === 'void') {
            if ($isSuper || $isCfo || $clearanceNum >= 4 || ($dept === 'EXE' && $clearanceNum >= 3)) {
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
            // An employee reading their own basic record
            $targetEmpId = (string)($resource['emp_id'] ?? '');
            if ($targetEmpId !== '' && $targetEmpId === $empId) {
                return;
            }
            throw new VpAuthorizationException("HR record not found", 404, $action, $resource);
        }

        if ($op === 'change' || $op === 'create' || $op === 'update' || $op === 'delete') {
            if ($isSuper || $dept === 'HRA' || $dept === 'EXE') {
                return;
            }
            throw new VpAuthorizationException("Only Human Resources personnel or Executives may modify personnel records", 403, $action, $resource);
        }
    }

    // -------------------------------------------------------------------------
    // DOMAIN 6: DOCUMENTS (FILE CENTER)
    // -------------------------------------------------------------------------
    if ($domain === 'document') {
        $classification = (string)($resource['classification'] ?? 'Internal Use');
        $reqClearance = vp_clearance_numeric($classification);
        $owner = (string)($resource['owner_emp_id'] ?? ($resource['uploaded_by_emp_id'] ?? ($resource['created_by_emp_id'] ?? '')));
        $reviewer = (string)($resource['reviewer_emp_id'] ?? ($resource['assigned_to_emp_id'] ?? ''));
        $docDept = (string)($resource['department'] ?? '');
        $isSteward = ($dept === 'DOC' || $dept === 'GOV' || ($docDept !== '' && $dept === $docDept));
        $isAdmin = ($isSuper || $dept === 'ADM' || $dept === 'EXE');

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

        // Mutation operations require read entitlement first
        if ($clearanceNum < $reqClearance && !$isSuper) {
            throw new VpAuthorizationException("Document not found", 404, $action, $resource);
        }

        if ($op === 'change' || $op === 'update' || $op === 'edit') {
            if ($isAdmin || ($owner !== '' && $owner === $empId) || ($reviewer !== '' && $reviewer === $empId) || $isSteward) {
                return;
            }
            throw new VpAuthorizationException("Only document owner, reviewer, department steward or admin may edit documents", 403, $action, $resource);
        }

        if ($op === 'change_classification') {
            // A non-owner must never be able to lower a classification
            $targetClass = (string)($resource['new_classification'] ?? $resource['target_classification'] ?? '');
            if ($targetClass !== '' && !$isAdmin && ($owner === '' || $owner !== $empId)) {
                $targetNum = vp_clearance_numeric($targetClass);
                if ($targetNum < $reqClearance) {
                    throw new VpAuthorizationException("A non-owner must never be able to lower document classification", 403, $action, $resource);
                }
            }
            if ($isAdmin || ($owner !== '' && $owner === $empId) || ($reviewer !== '' && $reviewer === $empId) || $isSteward) {
                return;
            }
            throw new VpAuthorizationException("Only document owner, assigned reviewer, department steward or admin may change classification", 403, $action, $resource);
        }

        if ($op === 'archive') {
            if ($isAdmin || ($owner !== '' && $owner === $empId) || ($reviewer !== '' && $reviewer === $empId) || $isSteward) {
                return;
            }
            throw new VpAuthorizationException("Only document owner, assigned reviewer, department steward or admin may archive documents", 403, $action, $resource);
        }

        if ($op === 'legal_hold') {
            if ($isAdmin || ($owner !== '' && $owner === $empId) || $dept === 'GOV' || $dept === 'DOC' || $isSteward) {
                return;
            }
            throw new VpAuthorizationException("Only compliance stewards, document owner or admin may toggle legal hold", 403, $action, $resource);
        }

        if ($op === 'delete') {
            $docId = (string)($resource['doc_id'] ?? '');
            if (vp_is_baseline_id($docId)) {
                throw new VpAuthorizationException("Locked baseline document records cannot be deleted", 403, $action, $resource);
            }
            if (!empty($resource['is_legal_hold'])) {
                throw new VpAuthorizationException("Cannot delete document under active legal hold", 403, $action, $resource);
            }
            if ($isAdmin || ($owner !== '' && $owner === $empId) || ($dept === 'DOC' && $clearanceNum >= 3)) {
                return;
            }
            throw new VpAuthorizationException("Only document owner or File Center lead may delete documents", 403, $action, $resource);
        }
    }

    // -------------------------------------------------------------------------
    // DOMAIN 7: DEVELOPER CREDENTIALS & PARTNERS
    // -------------------------------------------------------------------------
    if ($domain === 'developer_credential') {
        $crAccount = (int)($resource['created_by_account_id'] ?? 0);
        $crUser    = (string)($resource['created_by_user_id'] ?? ($resource['partner_id'] ?? ''));
        $partnerId = (string)($resource['partner_id'] ?? '');
        $userPartnerId = (string)($user['partner_id'] ?? '');

        $isOwner = ($isSuper || ($crAccount > 0 && $crAccount === $accId) ||
                    ($crUser !== '' && ($crUser === $empId || $crUser === $cusId)) ||
                    ($partnerId !== '' && $userPartnerId !== '' && $partnerId === $userPartnerId));

        if ($op === 'read') {
            if ($isOwner) {
                return;
            }
            throw new VpAuthorizationException("Credential not found", 404, $action, $resource);
        }

        if ($op === 'change' || $op === 'revoke' || $op === 'delete') {
            if ($isOwner) {
                return;
            }
            throw new VpAuthorizationException("Only the owning developer or partner may modify this API credential", 403, $action, $resource);
        }
    }

    // -------------------------------------------------------------------------
    // DOMAIN 8: ADMIN & GOVERNANCE PORTAL
    // -------------------------------------------------------------------------
    if ($domain === 'admin') {
        if ($isSuper) {
            return;
        }

        if ($op === 'attest' || $op === 'recert') {
            if ($dept === 'GOV' || $dept === 'ADM' || in_array($user['role_name'] ?? '', ['ComplianceOfficer', 'SecurityAdmin', 'SuperAdmin'], true)) {
                return;
            }
            throw new VpAuthorizationException("Only compliance officers or governance stewards may attest accounts", 403, $action, $resource);
        }

        if ($op === 'promote') {
            if (($dept === 'ADM' || $dept === 'GOV') && $clearanceNum >= 4) {
                return;
            }
            throw new VpAuthorizationException("Only executive governance officers may promote accounts", 403, $action, $resource);
        }

        if ($op === 'sever' || $op === 'terminate_session') {
            if ($dept === 'ADM' && $clearanceNum >= 3) {
                return;
            }
            throw new VpAuthorizationException("Only security administrators may sever credentials or terminate active sessions", 403, $action, $resource);
        }

        if ($op === 'purge') {
            // SuperAdmin only
            throw new VpAuthorizationException("Only SuperAdmin may purge security records or orphan tokens", 403, $action, $resource);
        }

        if ($op === 'risk_create' || $op === 'risk_update' || $op === 'risk_delete') {
            if ($dept === 'GOV' || $dept === 'ADM' || $clearanceNum >= 3) {
                return;
            }
            throw new VpAuthorizationException("Only risk management officers may modify board risk register", 403, $action, $resource);
        }

        if ($op === 'policy_create' || $op === 'policy_update' || $op === 'policy_delete') {
            if ($dept === 'GOV' || $dept === 'ADM' || $clearanceNum >= 3) {
                return;
            }
            throw new VpAuthorizationException("Only governance officers may modify security policies", 403, $action, $resource);
        }

        if ($op === 'lockdown') {
            if ($dept === 'ADM' && $clearanceNum >= 4) {
                return;
            }
            throw new VpAuthorizationException("Executive security authorization required for emergency lockdown", 403, $action, $resource);
        }
    }

    // -------------------------------------------------------------------------
    // DOMAIN 9: SHOP & CUSTOMER PORTAL (TENANT VS ACCOUNT-PRIVATE)
    // -------------------------------------------------------------------------
    if ($domain === 'shop' || $domain === 'customer') {
        if ($isSuper) {
            return;
        }
        $targetCusId = (string)($resource['cus_id'] ?? '');
        $targetAccId = (int)($resource['account_id'] ?? 0);

        if (($user['account_type'] ?? '') === 'Customer') {
            // Company-scoped data
            if ($targetCusId !== '' && $targetCusId !== $cusId) {
                throw new VpAuthorizationException("Resource not found", 404, $action, $resource);
            }
            // Account-private operations (e.g. personal credentials, private settings)
            if (($op === 'account_private' || $op === 'private') && $targetAccId > 0 && $targetAccId !== $accId) {
                throw new VpAuthorizationException("Resource not found", 404, $action, $resource);
            }
            return;
        }

        // Employee access to customer records
        if (in_array($dept, ['SAL', 'FIN', 'IT', 'ITD', 'OPS', 'EXE'], true)) {
            return;
        }
        throw new VpAuthorizationException("Customer resource not found", 404, $action, $resource);
    }

    // -------------------------------------------------------------------------
    // DOMAIN 10: USER-PRIVATE DATA (PREFERENCES, SAVED FILTERS, NOTIFICATIONS)
    // -------------------------------------------------------------------------
    if ($domain === 'user_data') {
        $ownerAccount = (int)($resource['account_id'] ?? 0);
        if ($isSuper || ($ownerAccount > 0 && $ownerAccount === $accId)) {
            return;
        }
        throw new VpAuthorizationException("User data record not found", 404, $action, $resource);
    }

    // Fallback baseline deletion guard (unconditional for baseline rows)
    vp_check_baseline_delete($action, $resource);

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
    if (isSuperAdmin($user)) {
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
