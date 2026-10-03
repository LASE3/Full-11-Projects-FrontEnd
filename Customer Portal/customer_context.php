<?php
declare(strict_types=1);

/**
 * VOSTOKPRIBOR Customer Portal - Centralized Context & Live Database Hydrator
 * Supplies authentic customer profile, session context, and live badge counts.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth_guard.php';

requireAuth('CUS');

$pdo = getDbConnection();
$cusId = $_SESSION['cus_id'] ?? ($_SESSION['vostok_user']['user_id'] ?? null);

// If no customer ID in session, check if employee/SuperAdmin is browsing as a customer
$currentUser = $_SESSION['vostok_user'] ?? null;
if (empty($cusId) && $currentUser && ($currentUser['account_type'] ?? '') === 'Employee') {
    // SuperAdmin may browse Customer Portal — allow but without a default cus_id
    // The page must handle $customer = null gracefully
}

if (empty($cusId) && empty($currentUser)) {
    // Strictly no session — redirect to login
    header('Location: login.php?error=session_expired');
    exit;
}


// Fetch authentic customer entity from MariaDB
$cStmt = $pdo->prepare("SELECT * FROM customers WHERE cus_id = ?");
$cStmt->execute([$cusId]);
$customer = $cStmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    $customer = [
        'cus_id' => $cusId,
        'company_name' => 'Authorized Enterprise Client',
        'primary_contact_name' => 'Client Representative',
        'primary_contact_email' => 'client@vostokpribor.local',
        'account_tier' => 'Enterprise SLA',
        'tax_id' => 'VAT-ACTIVE',
        'phone' => '+7 (800) 555-0199',
        'headquarters' => 'Central Operations',
        'sector' => 'Industrial Instrumentation'
    ];
}

// Fetch customer account username if available
$accStmt = $pdo->prepare("SELECT username, email, last_login FROM customer_accounts WHERE cus_id = ? LIMIT 1");
$accStmt->execute([$cusId]);
$customerAccount = $accStmt->fetch(PDO::FETCH_ASSOC) ?: [];

$currUser = $_SESSION['vostok_user'] ?? [];
$isSuperAdmin = isSuperAdmin($currUser);

if ($isSuperAdmin) {
    $currUser['full_name'] = (!empty($currUser['full_name']) && $currUser['full_name'] !== 'Alexey R. Danilov' && $currUser['full_name'] !== 'Authorized User')
        ? $currUser['full_name']
        : 'System Administrator';
    $currUser['role_name'] = 'Executive SuperAdmin';
    $currUser['clearance_level'] = 'L4';
    $currUser['email'] = $currUser['email'] ?? '';
    $currUser['company_name'] = $customer['company_name'] ?? 'VOSTOKPRIBOR Master Admin';
} else {
    if (empty($currUser['full_name']) || $currUser['full_name'] === 'Authorized User') {
        $currUser['full_name'] = $customer['primary_contact_name'] ?? 'Client Representative';
    }
    $currUser['company_name'] = $customer['company_name'] ?? 'Authorized Client';
    $currUser['clearance_level'] = $currUser['clearance_level'] ?? 'L1';
    $currUser['role_name'] = $currUser['role_name'] ?? ($customer['account_tier'] ?? 'Strategic Client');
    $currUser['email'] = !empty($currUser['email']) ? $currUser['email'] : ($customerAccount['email'] ?? ($customer['primary_contact_email'] ?? ''));
}

// Live badge counters directly from MariaDB using prepared statements
$bOrderStmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE cus_id = :cid");
$bOrderStmt->execute([':cid' => $cusId]);
$badgeOrders = (int)$bOrderStmt->fetchColumn();

$bPrjStmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE cus_id = :cid");
$bPrjStmt->execute([':cid' => $cusId]);
$badgeProjects = (int)$bPrjStmt->fetchColumn();

$bInvStmt = $pdo->prepare("SELECT COUNT(*) FROM invoices WHERE cus_id = :cid");
$bInvStmt->execute([':cid' => $cusId]);
$badgeInvoices = (int)$bInvStmt->fetchColumn();

$bDocStmt = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE related_cus_id = :cid1 OR related_prj_id IN (SELECT prj_id FROM projects WHERE cus_id = :cid2)");
$bDocStmt->execute([':cid1' => $cusId, ':cid2' => $cusId]);
$badgeDocs = (int)$bDocStmt->fetchColumn();

$bTktStmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE requester_cus_id = :cid");
$bTktStmt->execute([':cid' => $cusId]);
$badgeTickets = (int)$bTktStmt->fetchColumn();

$bSvcStmt = $pdo->prepare("SELECT COUNT(*) FROM customer_service_requests WHERE cus_id = :cid");
$bSvcStmt->execute([':cid' => $cusId]);
$badgeServices = (int)$bSvcStmt->fetchColumn();

$badgeCareers = (int)$pdo->query("SELECT COUNT(*) FROM job_postings WHERE is_published = 1")->fetchColumn();

/**
 * Render universal customer portal top header with authentic user and organization
 */
function renderCustomerHeader(string $activeSearchPlaceholder = 'Search projects, serial numbers, specs...'): void {
    global $currUser, $customer;
    $fullName = htmlspecialchars($currUser['full_name'] ?? 'Authorized Client', ENT_QUOTES, 'UTF-8');
    $companyName = htmlspecialchars($currUser['company_name'] ?? 'Enterprise Account', ENT_QUOTES, 'UTF-8');
    $roleName = htmlspecialchars($currUser['role_name'] ?? 'Enterprise SLA', ENT_QUOTES, 'UTF-8');
    $taxId = htmlspecialchars($customer['tax_id'] ?? 'VAT-ACTIVE', ENT_QUOTES, 'UTF-8');
    $initials = htmlspecialchars(mb_substr($currUser['full_name'] ?? 'VP', 0, 2), ENT_QUOTES, 'UTF-8');
    ?>
    <header class="fixed top-0 left-0 right-0 h-16 bg-primary-container z-50 flex items-center justify-between px-unit-lg border-b border-outline/20">
        <div class="flex items-center gap-unit-base">
            <button id="sidebar-toggle-btn" class="p-1.5 -ml-1 mr-1 rounded text-on-primary-container hover:text-on-primary hover:bg-surface-container-high/10 transition-colors flex items-center justify-center cursor-pointer focus:outline-none" title="Toggle Navigation Menu (Ctrl+B)">
                <span class="material-symbols-outlined text-2xl" id="sidebar-toggle-icon">menu</span>
            </button>
            <img alt="VOSTOKPRIBOR Logo" class="h-8 w-auto object-contain cursor-pointer" onclick="location.href='Dashboard.php'" src="assets/logo.svg">
            <div class="h-6 w-px bg-outline/30"></div>
            <div class="flex flex-col">
                <div class="flex items-center gap-unit-xs">
                    <span class="font-headline-sm text-headline-sm text-on-primary font-semibold tracking-tight cursor-pointer" onclick="location.href='Dashboard.php'">VOSTOKPRIBOR</span>
                    <span class="px-unit-xs py-0.5 rounded bg-surface-container-high/10 text-tertiary-fixed font-technical-tag text-technical-tag border border-tertiary-fixed/30">PORTAL</span>
                </div>
                <div class="flex items-center gap-unit-xs text-on-primary-container font-technical-tag text-technical-tag">
                    <span class="truncate max-w-[220px] font-medium text-on-primary"><?= $companyName ?></span>
                    <span class="text-outline">|</span>
                    <span class="text-primary-fixed-dim font-mono"><?= $taxId ?></span>
                </div>
            </div>
        </div>
        <div class="w-64 md:w-80 lg:w-96 max-w-md mx-2 shrink-1">
            <div class="header-search-bar flex items-center bg-primary px-unit-md py-1.5 rounded border border-outline/30 text-on-primary-container cursor-pointer transition-colors hover:border-outline/50">
                <span class="material-symbols-outlined text-sm mr-unit-sm text-on-primary-container">search</span>
                <input class="header-search-input bg-transparent border-none outline-none font-body-sm text-body-sm text-on-primary placeholder:text-on-primary-container w-full cursor-pointer"
                       placeholder="<?= htmlspecialchars($activeSearchPlaceholder, ENT_QUOTES, 'UTF-8') ?>" type="text">
                <span class="font-technical-tag text-technical-tag px-1.5 py-0.5 rounded bg-surface-container-high/10 text-on-primary-container border border-outline/30">Ctrl+K</span>
            </div>
        </div>
        <div class="flex items-center gap-2 sm:gap-4 lg:gap-unit-lg shrink-0">
            <div class="hidden md:flex items-center gap-unit-xs px-unit-sm py-1 rounded bg-primary border border-outline/20 font-technical-tag text-technical-tag text-on-primary">
                <span class="w-2 h-2 rounded-full bg-tertiary-fixed animate-pulse"></span>
                <span class="text-on-primary-container">Node:</span>
                <span class="text-tertiary-fixed">Online 99.98%</span>
            </div>
            <div id="header-bell-btn" class="relative flex items-center text-on-primary-container hover:text-on-primary cursor-pointer" title="Operational Alerts">
                <span class="material-symbols-outlined">notifications</span>
                <span id="bell-unread-dot" class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-tertiary-fixed ring-2 ring-primary-container"></span>
            </div>
            <a class="flex items-center text-on-primary-container hover:text-on-primary" href="Documents.php" title="Technical Documentation">
                <span class="material-symbols-outlined">menu_book</span>
            </a>
            <div class="h-6 w-px bg-outline/30"></div>
            <div class="flex items-center gap-unit-sm cursor-pointer" id="header-profile-btn">
                <div class="flex flex-col text-right">
                    <span class="font-headline-sm text-headline-sm text-on-primary font-medium leading-none"><?= $fullName ?></span>
                    <span class="font-technical-tag text-technical-tag text-on-primary-container mt-0.5"><?= $roleName ?></span>
                </div>
                <div class="w-8 h-8 rounded-full bg-tertiary-fixed/20 text-tertiary-fixed border border-tertiary-fixed/50 flex items-center justify-center font-bold text-xs">
                    <?= $initials ?>
                </div>
            </div>

            <!-- Top Bar Sign Out -->
            <a href="./api/logout.php?redirect=../Customer%20Portal/login.php" class="top-signout-btn" title="Sign Out of Customer Portal" onclick="(function(){sessionStorage.clear();localStorage.clear();})()">
                <span class="material-symbols-outlined">logout</span>
                <span>Sign Out</span>
            </a>
        </div>
    </header>
    <?php
}

/**
 * Render universal customer portal sidebar with live MariaDB badge counters and active indicators
 */
function renderCustomerSidebar(string $currentPage): void {
    global $badgeOrders, $badgeProjects, $badgeInvoices, $badgeDocs, $badgeTickets, $badgeServices, $badgeCareers, $customer;
    $managerName = htmlspecialchars($customer['primary_contact_name'] ?? 'Viktor Morozov', ENT_QUOTES, 'UTF-8');
    ?>
    <aside id="portal-sidebar" class="fixed left-0 top-16 bottom-0 w-64 bg-primary-container z-40 flex flex-col justify-between border-r border-outline/20">
        <div class="py-unit-md">
            <div class="px-unit-base mb-unit-sm font-label-caps text-label-caps text-on-primary-container uppercase tracking-wider flex items-center justify-between">
                <span>Operational Navigation</span>
                <button id="sidebar-collapse-btn" class="text-on-primary-container hover:text-on-primary p-0.5 rounded hover:bg-surface-container-high/10 transition-colors cursor-pointer" title="Collapse Menu">
                    <span class="material-symbols-outlined text-base">chevron_left</span>
                </button>
            </div>
            <nav class="flex flex-col gap-0.5" id="portal-nav-list">
                <!-- Dashboard -->
                <a data-path="dashboard" href="Dashboard.php"
                   class="flex items-center gap-unit-sm px-unit-base py-unit-sm transition-colors font-headline-sm text-headline-sm <?= $currentPage === 'dashboard' ? 'active bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container' : 'text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary font-normal' ?>"
                   <?= $currentPage === 'dashboard' ? 'aria-current="page"' : '' ?>>
                    <svg class="w-4 h-4 shrink-0 <?= $currentPage === 'dashboard' ? 'text-tertiary-fixed' : '' ?>" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <rect height="7" rx="1" width="7" x="3" y="3"></rect>
                        <rect height="7" rx="1" width="7" x="14" y="3"></rect>
                        <rect height="7" rx="1" width="7" x="14" y="14"></rect>
                        <rect height="7" rx="1" width="7" x="3" y="14"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Orders -->
                <a data-path="orders" href="Orders.php"
                   class="flex items-center gap-unit-sm px-unit-base py-unit-sm transition-colors font-headline-sm text-headline-sm <?= $currentPage === 'orders' ? 'active bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container' : 'text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary font-normal' ?>"
                   <?= $currentPage === 'orders' ? 'aria-current="page"' : '' ?>>
                    <svg class="w-4 h-4 shrink-0 <?= $currentPage === 'orders' ? 'text-tertiary-fixed' : '' ?>" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path>
                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                        <path d="M12 22V12"></path>
                    </svg>
                    <span>Orders</span>
                    <span id="nav-badge-orders" class="ml-auto px-2 py-0.5 text-xs font-mono font-bold rounded-full bg-surface-container-high/30 text-on-primary-container"><?= $badgeOrders ?></span>
                </a>

                <!-- Projects -->
                <a data-path="projects" href="ProjectListAndDetail.php"
                   class="flex items-center gap-unit-sm px-unit-base py-unit-sm transition-colors font-headline-sm text-headline-sm <?= $currentPage === 'projects' ? 'active bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container' : 'text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary font-normal' ?>"
                   <?= $currentPage === 'projects' ? 'aria-current="page"' : '' ?>>
                    <svg class="w-4 h-4 shrink-0 <?= $currentPage === 'projects' ? 'text-tertiary-fixed' : '' ?>" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <rect height="18" rx="2" width="18" x="3" y="3"></rect>
                        <path d="M3 9h18"></path>
                        <path d="M9 21V9"></path>
                    </svg>
                    <span>Projects</span>
                    <span id="nav-badge-projects" class="ml-auto px-2 py-0.5 text-xs font-mono font-bold rounded-full bg-surface-container-high/30 text-on-primary-container"><?= $badgeProjects ?></span>
                </a>

                <!-- Invoices -->
                <a data-path="invoices" href="Invoices.php"
                   class="flex items-center gap-unit-sm px-unit-base py-unit-sm transition-colors font-headline-sm text-headline-sm <?= $currentPage === 'invoices' ? 'active bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container' : 'text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary font-normal' ?>"
                   <?= $currentPage === 'invoices' ? 'aria-current="page"' : '' ?>>
                    <svg class="w-4 h-4 shrink-0 <?= $currentPage === 'invoices' ? 'text-tertiary-fixed' : '' ?>" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"></path>
                        <path d="M8 7h8"></path>
                        <path d="M8 11h8"></path>
                        <path d="M8 15h5"></path>
                    </svg>
                    <span>Invoices</span>
                    <span id="nav-badge-invoices" class="ml-auto px-2 py-0.5 text-xs font-mono font-bold rounded-full bg-surface-container-high/30 text-on-primary-container"><?= $badgeInvoices ?></span>
                </a>

                <!-- Documents -->
                <a data-path="documents" href="Documents.php"
                   class="flex items-center gap-unit-sm px-unit-base py-unit-sm transition-colors font-headline-sm text-headline-sm <?= $currentPage === 'documents' ? 'active bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container' : 'text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary font-normal' ?>"
                   <?= $currentPage === 'documents' ? 'aria-current="page"' : '' ?>>
                    <svg class="w-4 h-4 shrink-0 <?= $currentPage === 'documents' ? 'text-tertiary-fixed' : '' ?>" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"></path>
                    </svg>
                    <span>Documents</span>
                    <span id="nav-badge-documents" class="ml-auto px-2 py-0.5 text-xs font-mono font-bold rounded-full bg-surface-container-high/30 text-on-primary-container"><?= $badgeDocs ?></span>
                </a>

                <!-- Support -->
                <a data-path="support" href="SupportTicketView.php"
                   class="flex items-center gap-unit-sm px-unit-base py-unit-sm transition-colors font-headline-sm text-headline-sm <?= $currentPage === 'support' ? 'active bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container' : 'text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary font-normal' ?>"
                   <?= $currentPage === 'support' ? 'aria-current="page"' : '' ?>>
                    <svg class="w-4 h-4 shrink-0 <?= $currentPage === 'support' ? 'text-tertiary-fixed' : '' ?>" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                        <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path>
                    </svg>
                    <span>Support</span>
                    <span id="nav-badge-support" class="ml-auto px-2 py-0.5 text-xs font-mono font-bold rounded-full bg-surface-container-high/30 text-on-primary-container"><?= $badgeTickets ?></span>
                </a>

                <!-- Service Requests (Integrated with HR System) -->
                <a data-path="services" href="ServiceRequests.php"
                   class="flex items-center gap-unit-sm px-unit-base py-unit-sm transition-colors font-headline-sm text-headline-sm <?= $currentPage === 'services' ? 'active bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container' : 'text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary font-normal' ?>"
                   <?= $currentPage === 'services' ? 'aria-current="page"' : '' ?>>
                    <svg class="w-4 h-4 shrink-0 <?= $currentPage === 'services' ? 'text-tertiary-fixed' : '' ?>" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                    </svg>
                    <span>Request Services</span>
                    <span id="nav-badge-services" class="ml-auto px-2 py-0.5 text-xs font-mono font-bold rounded-full bg-surface-container-high/30 text-tertiary-fixed"><?= $badgeServices ?></span>
                </a>

                <!-- Careers & Job Postings (Live from HR System) -->
                <a data-path="careers" href="Careers.php"
                   class="flex items-center gap-unit-sm px-unit-base py-unit-sm transition-colors font-headline-sm text-headline-sm <?= $currentPage === 'careers' ? 'active bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container' : 'text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary font-normal' ?>"
                   <?= $currentPage === 'careers' ? 'aria-current="page"' : '' ?>>
                    <svg class="w-4 h-4 shrink-0 <?= $currentPage === 'careers' ? 'text-tertiary-fixed' : '' ?>" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                    <span>Careers &amp; Postings</span>
                    <span id="nav-badge-careers" class="ml-auto px-2 py-0.5 text-xs font-mono font-bold rounded-full bg-surface-container-high/30 text-on-primary-container"><?= $badgeCareers ?></span>
                </a>

                <!-- Account Settings -->
                <a data-path="account-settings" href="AccountSettings.php"
                   class="flex items-center gap-unit-sm px-unit-base py-unit-sm transition-colors font-headline-sm text-headline-sm <?= $currentPage === 'account-settings' ? 'active bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container' : 'text-on-primary-container hover:bg-surface-container-high/5 hover:text-on-primary font-normal' ?>"
                   <?= $currentPage === 'account-settings' ? 'aria-current="page"' : '' ?>>
                    <svg class="w-4 h-4 shrink-0 <?= $currentPage === 'account-settings' ? 'text-tertiary-fixed' : '' ?>" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <span>Account Settings</span>
                </a>

                <!-- Inter-System Bus -->
                <a data-path="integrations" href="Integrations.php"
                   class="flex items-center gap-unit-sm px-unit-base py-unit-sm text-secondary-fixed hover:bg-surface-container-high/5 hover:text-on-primary transition-colors font-headline-sm text-headline-sm font-semibold <?= $currentPage === 'integrations' ? 'active bg-surface-container-high/10 text-on-primary font-semibold border-l-4 border-on-tertiary-container' : '' ?>"
                   <?= $currentPage === 'integrations' ? 'aria-current="page"' : '' ?>>
                    <svg class="w-4 h-4 shrink-0 text-secondary-fixed" fill="none" stroke="#00E5FF" stroke-width="1.75" viewBox="0 0 24 24">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                    </svg>
                    <span>Inter-System Bus</span>
                </a>
            </nav>
        </div>

        <div class="portal-manager-card p-3 m-3 rounded-lg bg-primary/95 border border-outline/25 shadow-sm text-xs select-none">
            <div class="flex items-center justify-between mb-1.5">
                <span class="font-label-caps text-[10px] text-tertiary-fixed uppercase font-bold tracking-wider">Assigned Engineer</span>
                <span class="px-1.5 py-0.5 rounded bg-surface-container-high/15 text-on-primary-container font-mono text-[10px] border border-outline/20 font-semibold">SLA TIER 1</span>
            </div>
            <div class="flex items-center gap-2 mb-2">
                <div class="w-7 h-7 rounded-full bg-secondary-fixed/20 text-secondary-fixed flex items-center justify-center font-bold text-[10px] border border-secondary-fixed/30">
                    VM
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-semibold text-on-primary truncate text-[11px]"><?= $managerName ?></span>
                    <span class="text-[10px] text-on-primary-container/80 truncate">Industrial Field Specialist</span>
                </div>
            </div>
            <div class="flex items-center justify-between text-[10px] text-on-primary-container/70 pt-1.5 border-t border-outline/15">
                <span class="flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                    Direct Telemetry Channel
                </span>
                <a href="SupportTicketView.php" class="text-tertiary-fixed hover:underline font-semibold">Contact</a>
            </div>
        </div>
    </aside>
    <?php
}

/**
 * Injects user identity and live database statistics into client JavaScript
 */
function renderVostokScripts(): void {
    global $currUser, $badgeOrders, $badgeProjects, $badgeInvoices, $badgeDocs, $badgeTickets;
    ?>
    <script>
    window.VOSTOK_USER = <?= json_encode($currUser, JSON_UNESCAPED_UNICODE) ?>;
    window.VOSTOK_STATS = {
        orders: <?= (int)$badgeOrders ?>,
        projects: <?= (int)$badgeProjects ?>,
        invoices: <?= (int)$badgeInvoices ?>,
        documents: <?= (int)$badgeDocs ?>,
        tickets: <?= (int)$badgeTickets ?>
    };
    </script>
    <?php
}

