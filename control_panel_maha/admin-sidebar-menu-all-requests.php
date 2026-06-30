<?php
/**
 * All Requests — consolidated pending / approve / reject queues.
 */

function sb_all_requests_active()
{
    global $MainPage, $Page;

    if (isset($MainPage) && $MainPage === 'All-Requests') {
        return true;
    }

    static $scripts = array(
        'pending-cash-book-request.php',
        'approve-cash-book-request.php',
        'reject-cash-book-request.php',
    );
    if (!empty($_SERVER['SCRIPT_NAME'])) {
        $script = basename($_SERVER['SCRIPT_NAME']);
        if (in_array($script, $scripts, true)) {
            return true;
        }
    }

    static $pages = array(
        'All-Pending-Pretty-Cash-Request',
        'All-Approve-Pretty-Cash-Request',
        'All-Reject-Pretty-Cash-Request',
        'All-Pending-Expenses',
        'All-Approve-Expenses',
        'All-Reject-Expenses',
        'All-Pending-Vendor-Expense-Request',
        'All-Approve-Vendor-Expense-Request',
        'All-Reject-Vendor-Expense-Request',
        'All-Pending-Nso-Vendor-Expense-Request',
        'All-Approve-Nso-Vendor-Expense-Request',
        'All-Reject-Nso-Vendor-Expense-Request',
        'All-Pending-Cash-Book-Request',
        'All-Approve-Cash-Book-Request',
        'All-Reject-Cash-Book-Request',
        'All-Pending-Attendance-Request',
        'All-Approve-Attendance-Request',
        'All-Reject-Attendance-Request',
        'All-Leave-Request',
        'All-Pending-Leave-Request',
        'All-Approve-Leave-Request',
        'All-Reject-Leave-Request',
        'All-Pending-Advance-Request',
        'All-Approve-Advance-Request',
        'All-Reject-Advance-Request',
        'All-Pending-Resign-Request',
        'All-Approve-Resign-Request',
        'All-Reject-Resign-Request',
    );

    return isset($Page) && in_array($Page, $pages, true);
}

function sb_all_request_status_submenu($title, $pendingHref, $approveHref, $rejectHref, $pendingPage, $approvePage, $rejectPage, $permIds, $allHref = null, $allPage = null)
{
    if (!sb_has_opt($permIds)) {
        return;
    }

    global $searchParams;

    sb_submenu($title, function () use ($pendingHref, $approveHref, $rejectHref, $pendingPage, $approvePage, $rejectPage, $permIds, $searchParams, $allHref, $allPage) {
        if ($allHref !== null) {
            sb_render_if_visible($permIds, function () use ($allHref, $allPage) {
                sb_link($allHref, 'All', $allPage);
            });
        }
        sb_render_if_visible($permIds, function () use ($pendingHref, $pendingPage) {
            sb_link($pendingHref, 'Pending', $pendingPage);
        });
        sb_render_if_visible($permIds, function () use ($approveHref, $approvePage, $searchParams) {
            sb_link($approveHref . $searchParams, 'Approve', $approvePage);
        });
        sb_render_if_visible($permIds, function () use ($rejectHref, $rejectPage, $searchParams) {
            sb_link($rejectHref . $searchParams, 'Reject', $rejectPage);
        });
    });
}

$allRequestsPermIds = array(4, 5, 6, 7, 8, 87, 89, 90, 91, 92, 93, 64, 65, 67, 68, 69, 71);

if (sb_has_opt($allRequestsPermIds)) {
    sb_group('All Requests', 'All-Requests', sb_capture(function () {
        sb_all_request_status_submenu(
            'Petty Cash Request',
            'all-pending-pretty-cash-request.php',
            'all-approve-pretty-cash-request.php',
            'all-reject-pretty-cash-request.php',
            'All-Pending-Pretty-Cash-Request',
            'All-Approve-Pretty-Cash-Request',
            'All-Reject-Pretty-Cash-Request',
            array(5, 90)
        );
        sb_all_request_status_submenu(
            'Employee Expenses',
            'all-pending-expenses.php',
            'all-approve-expenses.php',
            'all-reject-expenses.php',
            'All-Pending-Expenses',
            'All-Approve-Expenses',
            'All-Reject-Expenses',
            array(4, 89)
        );
        sb_all_request_status_submenu(
            'Vendor Expenses',
            'all-pending-vendor-exepense-request.php',
            'all-approve-vendor-exepense-request.php',
            'all-reject-vendor-exepense-request.php',
            'All-Pending-Vendor-Expense-Request',
            'All-Approve-Vendor-Expense-Request',
            'All-Reject-Vendor-Expense-Request',
            array(6, 91)
        );
        sb_all_request_status_submenu(
            'NSO Vendor Expenses',
            'all-pending-nso-vendor-exepense-request.php',
            'all-approve-nso-vendor-exepense-request.php',
            'all-reject-nso-vendor-exepense-request.php',
            'All-Pending-Nso-Vendor-Expense-Request',
            'All-Approve-Nso-Vendor-Expense-Request',
            'All-Reject-Nso-Vendor-Expense-Request',
            array(7, 92)
        );
        sb_all_request_status_submenu(
            'Cash Book Request',
            'pending-cash-book-request.php',
            'approve-cash-book-request.php',
            'reject-cash-book-request.php',
            'All-Pending-Cash-Book-Request',
            'All-Approve-Cash-Book-Request',
            'All-Reject-Cash-Book-Request',
            array(87)
        );
        sb_all_request_status_submenu(
            'Attendance Request',
            'all-pending-attendance-request.php',
            'all-approve-attendance-request.php',
            'all-reject-attendance-request.php',
            'All-Pending-Attendance-Request',
            'All-Approve-Attendance-Request',
            'All-Reject-Attendance-Request',
            array(8, 93)
        );
        sb_all_request_status_submenu(
            'Leave Request',
            'all-pending-leave-requests.php',
            'all-approve-leave-requests.php',
            'all-reject-leave-requests.php',
            'All-Pending-Leave-Request',
            'All-Approve-Leave-Request',
            'All-Reject-Leave-Request',
            array(67, 71, 93),
            'all-leave-requests.php',
            'All-Leave-Request'
        );
        sb_all_request_status_submenu(
            'Advance Request',
            'all-pending-advance-request.php',
            'all-approve-advance-request.php',
            'all-reject-advance-request.php',
            'All-Pending-Advance-Request',
            'All-Approve-Advance-Request',
            'All-Reject-Advance-Request',
            array(65, 69)
        );
        sb_all_request_status_submenu(
            'Resign Request',
            'all-pending-resign-request.php',
            'all-approve-resign-request.php',
            'all-reject-resign-request.php',
            'All-Pending-Resign-Request',
            'All-Approve-Resign-Request',
            'All-Reject-Resign-Request',
            array(64, 68)
        );
    }), true, 'sb_all_requests_active');
}
