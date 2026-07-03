<?php
/**
 * All Expenses menu — request types, permissions, and list page routes.
 */

if (!function_exists('maha_ae_menu_registry')) {
    function maha_ae_menu_registry()
    {
        global $searchParams;

        return array(
            'employee_expense' => array(
                'label' => 'Employee Expense',
                'perms' => array(9, 4, 89),
                'pages' => array(
                    'all'     => 'all-expenses-view.php?type=employee_expense&mode=all',
                    'pending' => 'all-pending-expenses.php',
                    'approve' => 'all-approve-expenses.php' . $searchParams,
                    'reject'  => 'all-reject-expenses.php' . $searchParams,
                ),
                'page_ids' => array(
                    'all' => 'All-Employee-Expenses-All',
                    'pending' => 'All-Pending-Expenses',
                    'approve' => 'All-Approve-Expenses',
                    'reject' => 'All-Reject-Expenses',
                ),
            ),
            'petty_cash' => array(
                'label' => 'Petty Cash Request',
                'perms' => array(12, 5, 90),
                'pages' => array(
                    'all'     => 'all-expenses-view.php?type=petty_cash&mode=all',
                    'pending' => 'all-pending-pretty-cash-request.php',
                    'approve' => 'all-approve-pretty-cash-request.php' . $searchParams,
                    'reject'  => 'all-reject-pretty-cash-request.php' . $searchParams,
                ),
                'page_ids' => array(
                    'all' => 'All-Petty-Cash-All',
                    'pending' => 'All-Pending-Pretty-Cash-Request',
                    'approve' => 'All-Approve-Pretty-Cash-Request',
                    'reject' => 'All-Reject-Pretty-Cash-Request',
                ),
            ),
            'vendor_expense' => array(
                'label' => 'Vendor Expense',
                'perms' => array(13, 6, 91),
                'pages' => array(
                    'all'     => 'all-expenses-view.php?type=vendor_expense&mode=all',
                    'pending' => 'all-pending-vendor-exepense-request.php',
                    'approve' => 'all-approve-vendor-exepense-request.php' . $searchParams,
                    'reject'  => 'all-reject-vendor-exepense-request.php' . $searchParams,
                ),
                'page_ids' => array(
                    'all' => 'All-Vendor-Expense-All',
                    'pending' => 'All-Pending-Vendor-Expense-Request',
                    'approve' => 'All-Approve-Vendor-Expense-Request',
                    'reject' => 'All-Reject-Vendor-Expense-Request',
                ),
            ),
            'nso_vendor_expense' => array(
                'label' => 'NSO Expense',
                'perms' => array(15, 7, 92),
                'pages' => array(
                    'all'     => 'all-expenses-view.php?type=nso_vendor_expense&mode=all',
                    'pending' => 'all-pending-nso-vendor-exepense-request.php',
                    'approve' => 'all-approve-nso-vendor-exepense-request.php' . $searchParams,
                    'reject'  => 'all-reject-nso-vendor-exepense-request.php' . $searchParams,
                ),
                'page_ids' => array(
                    'all' => 'All-Nso-Vendor-Expense-All',
                    'pending' => 'All-Pending-Nso-Vendor-Expense-Request',
                    'approve' => 'All-Approve-Nso-Vendor-Expense-Request',
                    'reject' => 'All-Reject-Nso-Vendor-Expense-Request',
                ),
            ),
            'leave' => array(
                'label' => 'Leave Request',
                'perms' => array(175, 67, 71),
                'pages' => array(
                    'all'     => 'all-leave-requests.php',
                    'pending' => 'all-pending-leave-requests.php',
                    'approve' => 'all-approve-leave-requests.php' . $searchParams,
                    'reject'  => 'all-reject-leave-requests.php' . $searchParams,
                ),
                'page_ids' => array(
                    'all' => 'All-Leave-Request',
                    'pending' => 'All-Pending-Leave-Request',
                    'approve' => 'All-Approve-Leave-Request',
                    'reject' => 'All-Reject-Leave-Request',
                ),
            ),
            'advance' => array(
                'label' => 'Advance Request',
                'perms' => array(176, 65, 69),
                'pages' => array(
                    'all'     => 'all-expenses-view.php?type=advance&mode=all',
                    'pending' => 'all-pending-advance-request.php',
                    'approve' => 'all-approve-advance-request.php' . $searchParams,
                    'reject'  => 'all-reject-advance-request.php' . $searchParams,
                ),
                'page_ids' => array(
                    'all' => 'All-Advance-Request-All',
                    'pending' => 'All-Pending-Advance-Request',
                    'approve' => 'All-Approve-Advance-Request',
                    'reject' => 'All-Reject-Advance-Request',
                ),
            ),
            'attendance' => array(
                'label' => 'Attendance Request',
                'perms' => array(16, 8, 93),
                'pages' => array(
                    'all'     => 'all-expenses-view.php?type=attendance&mode=all',
                    'pending' => 'all-pending-attendance-request.php',
                    'approve' => 'all-approve-attendance-request.php' . $searchParams,
                    'reject'  => 'all-reject-attendance-request.php' . $searchParams,
                ),
                'page_ids' => array(
                    'all' => 'All-Attendance-Request-All',
                    'pending' => 'All-Pending-Attendance-Request',
                    'approve' => 'All-Approve-Attendance-Request',
                    'reject' => 'All-Reject-Attendance-Request',
                ),
            ),
            'resign' => array(
                'label' => 'Resign Request',
                'perms' => array(177, 64, 68),
                'pages' => array(
                    'all'     => 'all-expenses-view.php?type=resign&mode=all',
                    'pending' => 'all-pending-resign-request.php',
                    'approve' => 'all-approve-resign-request.php' . $searchParams,
                    'reject'  => 'all-reject-resign-request.php' . $searchParams,
                ),
                'page_ids' => array(
                    'all' => 'All-Resign-Request-All',
                    'pending' => 'All-Pending-Resign-Request',
                    'approve' => 'All-Approve-Resign-Request',
                    'reject' => 'All-Reject-Resign-Request',
                ),
            ),
            'hiring' => array(
                'label' => 'Hiring Request',
                'perms' => array(178, 149, 150),
                'pages' => array(
                    'all'     => 'all-expenses-view.php?type=hiring&mode=all',
                    'pending' => 'all-expenses-view.php?type=hiring&mode=pending',
                    'approve' => 'all-expenses-view.php?type=hiring&mode=approve',
                    'reject'  => 'all-expenses-view.php?type=hiring&mode=reject',
                ),
                'page_ids' => array(
                    'all' => 'All-Hiring-Request-All',
                    'pending' => 'All-Hiring-Request-Pending',
                    'approve' => 'All-Hiring-Request-Approve',
                    'reject' => 'All-Hiring-Request-Reject',
                ),
            ),
            'outlet_closure' => array(
                'label' => 'Outlet Closure Request',
                'perms' => array(168),
                'pages' => array(
                    'all'     => 'outlet-closure-approval-admin.php?filter=all',
                    'pending' => 'outlet-closure-approval-admin.php?filter=pending',
                    'approve' => 'outlet-closure-approval-admin.php?filter=approved',
                    'reject'  => 'outlet-closure-approval-admin.php?filter=rejected',
                ),
                'page_ids' => array(
                    'all' => 'Outlet-Closure-Approval-Admin',
                    'pending' => 'Outlet-Closure-Approval-Admin',
                    'approve' => 'Outlet-Closure-Approval-Admin',
                    'reject' => 'Outlet-Closure-Approval-Admin',
                ),
            ),
            'penalty' => array(
                'label' => 'Penalty Request',
                'perms' => array(153, 154),
                'pages' => array(
                    'all'     => 'all-expenses-view.php?type=penalty&mode=all',
                    'pending' => 'all-expenses-view.php?type=penalty&mode=pending',
                    'approve' => 'all-expenses-view.php?type=penalty&mode=approve',
                    'reject'  => 'all-expenses-view.php?type=penalty&mode=reject',
                ),
                'page_ids' => array(
                    'all' => 'All-Penalty-Request-All',
                    'pending' => 'All-Penalty-Request-Pending',
                    'approve' => 'All-Penalty-Request-Approve',
                    'reject' => 'All-Penalty-Request-Reject',
                ),
            ),
        );
    }
}

if (!function_exists('maha_ae_mode_label')) {
    function maha_ae_mode_label($mode)
    {
        $map = array(
            'all' => 'All',
            'pending' => 'Pending',
            'approve' => 'Approved',
            'reject' => 'Rejected',
        );
        return $map[$mode] ?? ucfirst((string) $mode);
    }
}
