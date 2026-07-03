<?php
/**
 * All Expenses — view-only consolidated lists (all / pending / approved / rejected).
 */

require_once __DIR__ . '/admin-sidebar-menu-helpers.php';
require_once __DIR__ . '/includes/all-expenses-menu-registry.php';

function sb_all_expenses_active()
{
    global $MainPage, $Page;

    if (isset($MainPage) && $MainPage === 'All-Expenses') {
        return true;
    }
    if (isset($MainPage) && $MainPage === 'All-Requests') {
        return true;
    }

    $registry = maha_ae_menu_registry();
    foreach ($registry as $cfg) {
        if (!empty($cfg['page_ids']) && isset($Page) && in_array($Page, array_values($cfg['page_ids']), true)) {
            return true;
        }
    }

    return isset($Page) && $Page === 'Outlet-Closure-Approval-Admin';
}

function sb_all_expense_status_submenu($typeKey)
{
    $registry = maha_ae_menu_registry();
    if (!isset($registry[$typeKey])) {
        return;
    }
    $cfg = $registry[$typeKey];
    if (!sb_has_opt($cfg['perms'])) {
        return;
    }

    global $searchParams;

    sb_submenu($cfg['label'], function () use ($cfg) {
        sb_link($cfg['pages']['all'], 'All', $cfg['page_ids']['all']);
        sb_link($cfg['pages']['pending'], 'Pending', $cfg['page_ids']['pending']);
        sb_link($cfg['pages']['approve'], 'Approve', $cfg['page_ids']['approve']);
        sb_link($cfg['pages']['reject'], 'Reject', $cfg['page_ids']['reject']);
    });
}

$allExpensesPermIds = array();
foreach (maha_ae_menu_registry() as $cfg) {
    $allExpensesPermIds = array_merge($allExpensesPermIds, $cfg['perms']);
}
$allExpensesPermIds = array_values(array_unique($allExpensesPermIds));

if (sb_has_opt($allExpensesPermIds)) {
    sb_group('All Expenses', 'All-Expenses', sb_capture(function () {
        sb_all_expense_status_submenu('employee_expense');
        sb_all_expense_status_submenu('petty_cash');
        sb_all_expense_status_submenu('vendor_expense');
        sb_all_expense_status_submenu('nso_vendor_expense');
        sb_all_expense_status_submenu('leave');
        sb_all_expense_status_submenu('advance');
        sb_all_expense_status_submenu('attendance');
        sb_all_expense_status_submenu('resign');
        sb_all_expense_status_submenu('hiring');
        sb_all_expense_status_submenu('outlet_closure');
        sb_all_expense_status_submenu('penalty');
    }), true, 'sb_all_expenses_active');
}
