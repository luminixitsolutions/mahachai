<?php
/**
 * Standalone expense approval menus (top-level horizontal nav items).
 * Visibility follows admin-sidebar-menu-permissions-employee-config.php (same as add-employee menu access).
 */

function sb_approval_group($title, $mainPage, $callback, $extraActiveCheck = null)
{
    if (!sb_section_visible($title)) {
        return;
    }
    sb_group($title, $mainPage, sb_capture($callback), true, $extraActiveCheck);
}

// ─── Admin Approval ───────────────────────────────────────────────────────────
sb_approval_group('Admin Approval', 'HO-Admin-Expenses', function () {
    if (sb_has_opt(array(4, 5, 6, 7, 8))) {
        sb_submenu('Pending Requests', function () {
            sb_render_if_visible('4', function () {
                sb_link('all-pending-expenses.php', 'Expenses', 'All-Pending-Expenses');
            });
            sb_render_if_visible('5', function () {
                sb_link('all-pending-pretty-cash-request.php', 'Petty Cash', 'All-Pending-Pretty-Cash-Request');
            });
            sb_render_if_visible('6', function () {
                sb_link('all-pending-vendor-exepense-request.php', 'Vendor Expenses', 'All-Pending-Pretty-Cash-Request');
            });
            sb_render_if_visible('7', function () {
                sb_link('all-pending-nso-vendor-exepense-request.php', 'NSO Vendor Expenses', 'All-Pending-Pretty-Cash-Request');
            });
            sb_render_if_visible('8', function () {
                sb_link('all-pending-attendance-request.php', 'Attendance Requests', 'All-Pending-Expenses');
            });
        });
    }
    sb_render_if_visible('38', function () {
        sb_submenu('Employee Expense', function () {
            sb_expense_status_links(
                'ho-admin-pending-expense-request.php',
                'ho-admin-approve-expense-request.php',
                'ho-admin-reject-expense-request.php',
                'HO-Admin-Pending-Expense-After-BH',
                'HO-Admin-Approve-Expense-Request',
                'HO-Admin-Reject-Expense-Request'
            );
        });
    });
    sb_render_if_visible('72', function () {
        sb_submenu('Vendor Expense', function () {
            sb_expense_status_links(
                'account-vendor-pending-expense-request.php',
                'account-vendor-approve-expense-request.php',
                'account-vendor-reject-expense-request.php',
                'Account-Vendor-Peding-Expense-Request',
                'Account-Vendor-Approve-Expense-Request',
                'Account-Vendor-Reject-Expense-Request'
            );
        });
    });
    sb_render_if_visible('119', function () {
        sb_submenu('Vendor Expense Below 2000', function () {
            sb_expense_status_links(
                'account-vendor-pending-expense-request-below.php',
                'account-vendor-approve-expense-request-below.php',
                'account-vendor-reject-expense-request-below.php',
                'Account-Vendor-Peding-Expense-Request',
                'Account-Vendor-Approve-Expense-Request',
                'Account-Vendor-Reject-Expense-Request'
            );
        });
    });
    sb_render_if_visible('73', function () {
        sb_submenu('NSO Vendor Expense', function () {
            sb_expense_status_links(
                'account-nso-vendor-pending-expense-request.php',
                'account-nso-vendor-approve-expense-request.php',
                'account-nso-vendor-reject-expense-request.php',
                'Account-Vendor-Peding-Expense-Request',
                'Account-Vendor-Approve-Expense-Request',
                'Account-Vendor-Reject-Expense-Request'
            );
        });
    });
    sb_render_if_visible('20', function () {
        sb_submenu('Admin Petty Cash Requests', function () {
            sb_link('ho-pending-pretty-cash-request.php', 'Pending', 'Ho-Pending-Pretty-Cash-Request');
            sb_link('ho-approve-pretty-cash-request.php', 'Approved', 'Ho-Approve-Pretty-Cash-Request');
            sb_link('ho-reject-pretty-cash-request.php', 'Rejected', 'Ho-Reject-Pretty-Cash-Request');
        });
    });
    sb_render_if_visible('168', function () {
        sb_submenu('Outlet Closure Approval', function () {
            sb_link('outlet-closure-approval-admin.php?filter=pending', 'Pending', function () {
                global $Page, $OutletClosureSidebarFilter;
                return isset($Page) && $Page == 'Outlet-Closure-Approval-Admin' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter === 'pending';
            });
            sb_link('outlet-closure-approval-admin.php?filter=approved', 'Approved', function () {
                global $Page, $OutletClosureSidebarFilter;
                return isset($Page) && $Page == 'Outlet-Closure-Approval-Admin' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter === 'approved';
            });
            sb_link('outlet-closure-approval-admin.php?filter=rejected', 'Rejected', function () {
                global $Page, $OutletClosureSidebarFilter;
                return isset($Page) && $Page == 'Outlet-Closure-Approval-Admin' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter === 'rejected';
            });
        });
    });
}, function () {
    global $Page, $MainPage;
    if (isset($MainPage) && $MainPage === 'Pretty-Cash-Ho') {
        return true;
    }
    return isset($Page) && in_array($Page, array(
        'Ho-Pending-Pretty-Cash-Request',
        'Ho-Approve-Pretty-Cash-Request',
        'Ho-Reject-Pretty-Cash-Request',
        'Outlet-Closure-Approval-Admin',
    ), true);
});

// ─── BH Approval ──────────────────────────────────────────────────────────────
sb_approval_group('BH Approval', 'BH-Employee-Expense', function () {
    sb_render_if_visible('116', function () {
        sb_submenu('Employee Expense', function () {
            sb_expense_status_links(
                'ho-business-head-pending-expense-request.php',
                'employee-approve-expense-request-below.php',
                'employee-reject-expense-request-below.php',
                'BH-Pending-Expense',
                'BH-Approved-Expense',
                'BH-Reject-Expense'
            );
        });
    });
    sb_render_if_visible('154', function () {
        sb_submenu('Penalty Approval', function () {
            sb_expense_status_links(
                'penalty_bh_approval.php',
                'penalty_bh_approved.php',
                'penalty_bh_rejected.php',
                'Penalty-BH-Pending',
                'Penalty-BH-Approved',
                'Penalty-BH-Rejected'
            );
        });
    });
    sb_render_if_visible('166', function () {
        sb_submenu('Outlet Closure Approval', function () {
            sb_link('outlet-closure-approval-bh.php?filter=pending', 'Pending', function () {
                global $Page, $OutletClosureSidebarFilter;
                return isset($Page) && $Page == 'Outlet-Closure-Approval-BH' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter === 'pending';
            });
            sb_link('outlet-closure-approval-bh.php?filter=approved', 'Approved', function () {
                global $Page, $OutletClosureSidebarFilter;
                return isset($Page) && $Page == 'Outlet-Closure-Approval-BH' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter === 'approved';
            });
            sb_link('outlet-closure-approval-bh.php?filter=rejected', 'Rejected', function () {
                global $Page, $OutletClosureSidebarFilter;
                return isset($Page) && $Page == 'Outlet-Closure-Approval-BH' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter === 'rejected';
            });
        });
    });
}, function () {
    global $Page;
    return isset($Page) && in_array($Page, array(
        'Penalty-BH-Pending',
        'Penalty-BH-Approved',
        'Penalty-BH-Rejected',
        'Outlet-Closure-Approval-BH',
    ), true);
});

// ─── Manager Approval ───────────────────────────────────────────────────────
sb_approval_group('Manager Approval', 'HO-Manager-Expenses', function () {
    sb_render_if_visible('44', function () {
        sb_submenu('Employee Expense', function () {
            sb_expense_status_links(
                'ho-manager-pending-expense-request.php',
                'ho-manager-approve-expense-request.php',
                'ho-manager-reject-expense-request.php',
                'HO-Manager-Peding-Expense-Request',
                'HO-Manager-Approve-Expense-Request',
                'HO-Manager-Reject-Expense-Request'
            );
        });
    });
    sb_render_if_visible('46', function () {
        sb_submenu('Manager Petty Cash Requests', function () {
            sb_link('manager-pending-pretty-cash-request.php', 'Pending', 'Manager-Pending-Pretty-Cash-Request');
            sb_link('manager-approve-pretty-cash-request.php', 'Approved', 'Manager-Approve-Pretty-Cash-Request');
            sb_link('manager-reject-pretty-cash-request.php', 'Rejected', 'Manager-Reject-Pretty-Cash-Request');
        });
    });
    sb_render_if_visible('71', function () {
        sb_submenu('Manager Leave Requests', function () {
            sb_link('manager-pending-leave-request.php', 'Pending', 'Manager-Pending-Leave-Request');
            sb_link('manager-approve-leave-request.php', 'Approved', 'Manager-Approve-Leave-Request');
            sb_link('manager-reject-leave-request.php', 'Rejected', 'Manager-Reject-Leave-Request');
        });
    });
    sb_render_if_visible('69', function () {
        sb_submenu('Manager Advance Requests', function () {
            sb_link('manager-pending-advance-request.php', 'Pending', 'Manager-Pending-Advance-Request');
            sb_link('manager-approve-advance-request.php', 'Approved', 'Manager-Approve-Advance-Request');
            sb_link('manager-reject-advance-request.php', 'Rejected', 'Manager-Reject-Advance-Request');
        });
    });
    sb_render_if_visible('70', function () {
        sb_submenu('Manager Attendance Request', function () {
            sb_link('manager-pending-attendance-request.php', 'Pending', 'Manager-Pending-Attendance-Request');
            sb_link('manager-approve-attendance-request.php', 'Approved', 'Manager-Approve-Attendance-Request');
            sb_link('manager-reject-attendance-request.php', 'Rejected', 'Manager-Reject-Attendance-Request');
        });
    });
    sb_render_if_visible('68', function () {
        sb_submenu('Manager Resign Requests', function () {
            sb_link('manager-pending-resign-request.php', 'Pending', 'Manager-Pending-Resign-Request');
            sb_link('manager-approve-resign-request.php', 'Approved', 'Manager-Approve-Resign-Request');
            sb_link('manager-reject-resign-request.php', 'Rejected', 'Manager-Reject-Resign-Request');
        });
    });
    sb_render_if_visible('149', function () {
        sb_submenu('Manager Hiring Requests', function () {
            sb_link('manager-hiring-request.php?tab=pending', 'Pending', 'Manager-Hiring-Request');
            sb_link('manager-hiring-request.php?tab=approved', 'Approved', 'Manager-Hiring-Request');
            sb_link('manager-hiring-request.php?tab=rejected', 'Rejected', 'Manager-Hiring-Request');
        });
    });
    sb_render_if_visible('159', function () {
        sb_link('idea-box-manager-requests.php', 'Manager Ideas', 'Idea-Box-Manager');
    });
    sb_render_if_visible('43', function () {
        sb_submenu('NSO Vendor Expense', function () {
            sb_expense_status_links(
                'nso-vendor-pending-expense-request.php',
                'nso-vendor-approve-expense-request.php',
                'nso-vendor-reject-expense-request.php',
                'HO-Manager-Peding-Expense-Request',
                'HO-Manager-Approve-Expense-Request',
                'HO-Manager-Reject-Expense-Request'
            );
        });
    });
});

// ─── HR Approval ──────────────────────────────────────────────────────────────
sb_approval_group('HR Approval', 'HR-Expenses', function () {
    sb_render_if_visible('45', function () {
        sb_submenu('Employee Expense', function () {
            sb_expense_status_links(
                'hr-pending-expense-request.php',
                'hr-approve-expense-request.php',
                'hr-reject-expense-request.php',
                'HR-Peding-Expense-Request',
                'HR-Approve-Expense-Request',
                'HR-Reject-Expense-Request'
            );
        });
    });
    sb_render_if_visible('66', function () {
        sb_submenu('HR Attendance Request', function () {
            sb_link('hr-pending-attendance-request.php', 'Pending', 'Hr-Pending-Attendance-Request');
            sb_link('hr-approve-attendance-request.php', 'Approved', 'HR-Approve-Attendance-Request');
            sb_link('hr-reject-attendance-request.php', 'Rejected', 'HR-Reject-Attendance-Request');
        });
    });
    sb_render_if_visible('67', function () {
        sb_submenu('HR Leave Requests', function () {
            sb_link('hr-pending-leave-request.php', 'Pending', 'Hr-Pending-Leave-Request');
            sb_link('hr-approve-leave-request.php', 'Approved', 'HR-Approve-Leave-Request');
            sb_link('hr-reject-leave-request.php', 'Rejected', 'HR-Reject-Leave-Request');
        });
    });
    sb_render_if_visible('65', function () {
        sb_submenu('HR Advance Requests', function () {
            sb_link('hr-pending-advance-request.php', 'Pending', 'HR-Peding-Advance-Request');
            sb_link('hr-approve-advance-request.php', 'Approved', 'HR-Approve-Advance-Request');
            sb_link('hr-reject-advance-request.php', 'Rejected', 'HR-Reject-Advance-Request');
        });
    });
    sb_render_if_visible('64', function () {
        sb_submenu('HR Resign Requests', function () {
            sb_link('hr-pending-resign-request.php', 'Pending', 'HR-Peding-Resign-Request');
            sb_link('hr-approve-resign-request.php', 'Approved', 'HR-Approve-Resign-Request');
            sb_link('hr-reject-resign-request.php', 'Rejected', 'HR-Reject-Resign-Request');
        });
    });
    sb_render_if_visible('150', function () {
        sb_submenu('HR Hiring Requests', function () {
            sb_link('hr-hiring-request.php?tab=pending', 'Pending', 'HR-Hiring-Request');
            sb_link('hr-hiring-request.php?tab=approved', 'Approved', 'HR-Hiring-Request');
            sb_link('hr-hiring-request.php?tab=rejected', 'Rejected', 'HR-Hiring-Request');
        });
    });
});

// ─── Accounts Approval ────────────────────────────────────────────────────────
sb_approval_group('Accounts Approval', 'HO-Manager-Expenses', function () {
    sb_render_if_visible('88', function () {
        sb_submenu('Employee Expense', function () {
            sb_expense_status_links(
                'accountant-pending-emp-expense-request.php',
                'accountant-approve-emp-expense-request.php',
                'accountant-reject-emp-expense-request.php',
                'HO-Manager-Peding-Expense-Request',
                'HO-Manager-Approve-Expense-Request',
                'HO-Manager-Reject-Expense-Request'
            );
        });
    });
    sb_render_if_visible('17', function () {
        sb_submenu('Vendor Expense', function () {
            sb_expense_status_links(
                'manager-vendor-pending-expense-request.php',
                'manager-vendor-approve-expense-request.php',
                'manager-vendor-reject-expense-request.php',
                'Manager-Vendor-Peding-Expense-Request',
                'Manager-Vendor-Approve-Expense-Request',
                'Manager-Vendor-Reject-Expense-Request'
            );
        });
    });
    sb_render_if_visible('18', function () {
        sb_submenu('NSO Vendor Expense', function () {
            sb_expense_status_links(
                'manager-nso-vendor-pending-expense-request.php',
                'manager-nso-vendor-approve-expense-request.php',
                'manager-nso-vendor-reject-expense-request.php',
                'Manager-Vendor-Peding-Expense-Request',
                'Manager-Vendor-Approve-Expense-Request',
                'Manager-Vendor-Reject-Expense-Request'
            );
        });
    });
    sb_render_if_visible('28', function () {
        sb_submenu('Account Petty Cash Requests', function () {
            sb_link('account-pending-pretty-cash-request.php', 'Pending', 'Account-Pending-Pretty-Cash-Request');
            sb_link('account-approve-pretty-cash-request.php', 'Approved', 'Account-Approve-Pretty-Cash-Request');
            sb_link('account-reject-pretty-cash-request.php', 'Rejected', 'Account-Reject-Pretty-Cash-Request');
        });
    });
    sb_render_if_visible('167', function () {
        sb_submenu('Outlet Closure Approval', function () {
            sb_link('outlet-closure-approval-finance.php?filter=pending', 'Pending', function () {
                global $Page, $OutletClosureSidebarFilter;
                return isset($Page) && $Page == 'Outlet-Closure-Approval-Finance' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter === 'pending';
            });
            sb_link('outlet-closure-approval-finance.php?filter=approved', 'Approved', function () {
                global $Page, $OutletClosureSidebarFilter;
                return isset($Page) && $Page == 'Outlet-Closure-Approval-Finance' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter === 'approved';
            });
            sb_link('outlet-closure-approval-finance.php?filter=rejected', 'Rejected', function () {
                global $Page, $OutletClosureSidebarFilter;
                return isset($Page) && $Page == 'Outlet-Closure-Approval-Finance' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter === 'rejected';
            });
        });
    });
});

// ─── Purchase Approval ──────────────────────────────────────────────────────
sb_approval_group('Purchase Approval', 'HO-Manager-Expenses', function () {
    sb_render_if_visible('41', function () {
        sb_submenu('Vendor Expense', function () {
            sb_expense_status_links(
                'purchase-vendor-pending-expense-request.php',
                'purchase-vendor-approve-expense-request.php',
                'purchase-vendor-reject-expense-request.php',
                'Account-Vendor-Peding-Expense-Request',
                'Account-Vendor-Approve-Expense-Request',
                'Account-Vendor-Reject-Expense-Request'
            );
        });
    });
    sb_render_if_visible('42', function () {
        sb_submenu('NSO Vendor Expense', function () {
            sb_expense_status_links(
                'nso-purchase-vendor-pending-expense-request.php',
                'nso-purchase-vendor-approve-expense-request.php',
                'nso-purchase-vendor-reject-expense-request.php',
                'Account-Vendor-Peding-Expense-Request',
                'Account-Vendor-Approve-Expense-Request',
                'Account-Vendor-Reject-Expense-Request'
            );
        });
    });
});

// ─── BDM Approval ─────────────────────────────────────────────────────────────
sb_approval_group('BDM Approval', 'HO-Manager-Expenses', function () {
    sb_render_if_visible('40', function () {
        sb_submenu('Vendor Expense', function () {
            sb_expense_status_links(
                'bdm-vendor-pending-expense-request.php',
                'bdm-vendor-approve-expense-request.php',
                'bdm-vendor-reject-expense-request.php',
                'HO-Manager-Peding-Expense-Request',
                'HO-Manager-Approve-Expense-Request',
                'HO-Manager-Reject-Expense-Request'
            );
        });
    });
    sb_render_if_visible('118', function () {
        sb_submenu('BDM Attendance Request', function () {
            sb_link('bdm-pending-attendance-request.php', 'Pending', 'Hr-Pending-Attendance-Request');
            sb_link('bdm-approve-attendance-request.php', 'Approved', 'HR-Approve-Attendance-Request');
            sb_link('bdm-reject-attendance-request.php', 'Rejected', 'HR-Reject-Attendance-Request');
        });
    });
    sb_render_if_visible('153', function () {
        sb_submenu('Penalty Approval', function () {
            sb_expense_status_links(
                'penalty_bdm_approval.php',
                'penalty_bdm_approved.php',
                'penalty_bdm_rejected.php',
                'Penalty-BDM-Pending',
                'Penalty-BDM-Approved',
                'Penalty-BDM-Rejected'
            );
        });
    });
}, function () {
    global $Page;
    return isset($Page) && in_array($Page, array(
        'Penalty-BDM-Pending',
        'Penalty-BDM-Approved',
        'Penalty-BDM-Rejected',
        'Hr-Pending-Attendance-Request',
        'HR-Approve-Attendance-Request',
        'HR-Reject-Attendance-Request',
    ), true);
});
