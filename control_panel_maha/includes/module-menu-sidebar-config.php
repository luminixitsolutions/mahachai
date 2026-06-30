<?php
/**
 * Module hub categories — aligned with admin-sidebar-menu-organized.php + expense-approvals.
 */

function maha_module_expense_status_items($L, $prefix, $pending, $approved, $rejected, $opts)
{
    return array(
        $L($prefix . ' — Pending', $pending, $opts),
        $L($prefix . ' — Approved', $approved, $opts),
        $L($prefix . ' — Rejected', $rejected, $opts),
    );
}

function maha_get_sidebar_module_menu_categories()
{
    $today = date('Y-m-d');
    $sp    = '?FromDate=' . $today . '&ToDate=' . $today . '&Search=Search';

    $L = function ($label, $href, $opts = array(), $extra = array()) {
        return array_merge(
            array('label' => $label, 'href' => $href, 'any_options' => $opts),
            $extra
        );
    };

    $E = function ($prefix, $pending, $approved, $rejected, $opts) use ($L) {
        return maha_module_expense_status_items($L, $prefix, $pending, $approved, $rejected, $opts);
    };

    return array(
        'dashboard' => array(
            'slug'   => 'dashboard',
            'title'  => 'Dashboard',
            'blurb'  => 'Main, sales, stock, HR, franchise, project, and expense vs sale dashboards.',
            'icon'   => 'ion ion-md-speedometer',
            'accent' => '#0F5A4A',
            'groups' => array(
                array(
                    'name'  => 'Dashboard',
                    'items' => array(
                        $L('Main Dashboard', 'dashboard.php', array()),
                        $L('Sales Dashboard', 'sales-dashboard.php', array('108')),
                        $L('Stock Dashboard', 'stock-monitoring-dashboard.php', array('113')),
                        $L('Task Dashboard', 'task-monitoring-dashboard.php', array('113')),
                        $L('Outlet Audit Dashboard', 'outlet-audit-dashboard.php', array('113')),
                        $L('Feedback Dashboard', 'feedback-dashboard.php', array('122')),
                        $L('Payment Mode Change Dashboard', 'invoice-payment-mode-dashboard.php', array('122')),
                        $L('Chai Pass Dashboard', 'chai-pass-dashboard.php', array('129')),
                        $L('HR Dashboard', 'hr-dashboard.php', array('109')),
                        $L('Account Dashboard', 'account-dashboard.php', array('110')),
                        $L('Franchise Dashboard', 'franchise-dashboard.php', array('111')),
                        $L('Project Dashboard', 'project-dashboard.php', array('112')),
                        $L('Employee Dashboard', 'employee-dashboard.php', array('95')),
                        $L('Sub Zone Expense vs Sale', 'expense-sale-dashboard.php', array('1')),
                        $L('Zone Expense vs Sale', 'expense-sale-dashboard.php?value=zone', array('2')),
                        $L('Franchise Expense vs Sale Report', 'expense-sale-report.php', array('3')),
                    ),
                ),
            ),
        ),

        'operations' => array(
            'slug'   => 'operations',
            'title'  => 'Operations',
            'blurb'  => 'Control room, store duties, compliance, fuel checklist, and outlet closure.',
            'icon'   => 'ion ion-md-construct',
            'accent' => '#17a2b8',
            'groups' => array(
                array(
                    'name'  => 'Operations',
                    'items' => array(
                        $L('Control Room', 'control-room-report.php', array('30')),
                        $L('Control Room Report', 'control-room-report.php', array()),
                        $L('Store Manager Duties', 'view-store-manager-duties.php', array('31')),
                        $L('Manager Checkpoints', 'view-manager-checkpoint.php', array('32')),
                        $L('Recipe SOP', 'youtube-videos.php', array('164')),
                        $L('Upload Compliance', 'upload-compliance.php', array()),
                        $L('Alliances Upload Documents', 'aliance-upload-docs.php', array('99')),
                    ),
                ),
                array(
                    'name'  => 'Fuel Station Checklist',
                    'items' => array(
                        $L('Pending', 'view-fuel-station-checklist.php', array('33')),
                        $L('Approved', 'approve-fuel-station-checklist.php', array('33')),
                        $L('Rejected', 'reject-fuel-station-checklist.php', array('33')),
                    ),
                ),
                array(
                    'name'  => 'Location Feasibility Checklist',
                    'items' => array(
                        $L('Location Feasibility Approval', 'location-feasibility-approval.php', array('165')),
                    ),
                ),
                array(
                    'name'  => 'Outlet Closure — BH Approval',
                    'items' => array(
                        $L('Pending', 'outlet-closure-approval-bh.php?filter=pending', array('166')),
                        $L('Approved', 'outlet-closure-approval-bh.php?filter=approved', array('166')),
                        $L('Rejected', 'outlet-closure-approval-bh.php?filter=rejected', array('166')),
                    ),
                ),
                array(
                    'name'  => 'Outlet Closure — Finance Approval',
                    'items' => array(
                        $L('Pending', 'outlet-closure-approval-finance.php?filter=pending', array('167')),
                        $L('Approved', 'outlet-closure-approval-finance.php?filter=approved', array('167')),
                        $L('Rejected', 'outlet-closure-approval-finance.php?filter=rejected', array('167')),
                    ),
                ),
                array(
                    'name'  => 'Outlet Closure — Admin Approval',
                    'items' => array(
                        $L('Pending', 'outlet-closure-approval-admin.php?filter=pending', array('168')),
                        $L('Approved', 'outlet-closure-approval-admin.php?filter=approved', array('168')),
                        $L('Rejected', 'outlet-closure-approval-admin.php?filter=rejected', array('168')),
                    ),
                ),
            ),
        ),

        'attendance-management' => array(
            'slug'   => 'attendance-management',
            'title'  => 'Attendance Management',
            'blurb'  => 'Start/end attendance, updates, HR/manager attendance requests, and reports.',
            'icon'   => 'ion ion-md-calendar',
            'accent' => '#007bff',
            'groups' => array(
                array(
                    'name'  => 'Attendance',
                    'items' => array(
                        $L('Start Attendance', 'add-attendance.php', array('35')),
                        $L('End Attendance', 'end-attendance.php', array('35')),
                        $L('Night Attendance', 'night-attendance.php', array('35')),
                        $L('Night End Attendance', 'night-end-attendance.php', array('35')),
                        $L('Update Single Attendance', 'update-attendance.php', array('115')),
                        $L('Update Multiple Attendance', 'update-attendance-mult.php', array('115')),
                        $L('Add Attendance Request (On Behalf)', 'add-attendance-request.php', array('173')),
                        $L('Attendance Reports', 'attendance-task-report.php', array()),
                    ),
                ),
                array(
                    'name'  => 'HR Attendance Request',
                    'items' => array_merge(
                        $E('HR Attendance', 'hr-pending-attendance-request.php', 'hr-approve-attendance-request.php', 'hr-reject-attendance-request.php', array('66'))
                    ),
                ),
                array(
                    'name'  => 'Manager Attendance Request',
                    'items' => array_merge(
                        $E('Manager Attendance', 'manager-pending-attendance-request.php', 'manager-approve-attendance-request.php', 'manager-reject-attendance-request.php', array('70'))
                    ),
                ),
            ),
        ),

        'hr-employee-management' => array(
            'slug'   => 'hr-employee-management',
            'title'  => 'HR & Employee Management',
            'blurb'  => 'Employees, training, KRA, policies, hiring, resign clearance, and HR requests.',
            'icon'   => 'ion ion-md-people',
            'accent' => '#6f42c1',
            'groups' => array(
                array(
                    'name'  => 'Employee Master',
                    'items' => array(
                        $L('Employee Scheme', 'employee-scheme.php', array('94')),
                        $L('Add Employee', 'add-employee.php', array('56')),
                        $L('View Employee', 'view-employee.php', array('56')),
                        $L('Trainee Employee', 'trainee-employee.php', array('56')),
                        $L('Non Trainee Employee', 'non-trainee-employee.php', array('56')),
                        $L('Inactive Employees', 'view-inactive-employee.php', array('56')),
                        $L('Other Employee', 'other-employee.php', array('56')),
                        $L('Internship Employee', 'internship-employee.php', array('56')),
                        $L('COFO Employees', 'view-cofo-employee.php', array('56')),
                    ),
                ),
                array(
                    'name'  => 'Reports',
                    'items' => array(
                        $L('Manpower Report', 'manpower-report.php', array('133')),
                        $L('Outlet Employee Salary Report', 'outlet-employee-salary-report.php', array('77')),
                    ),
                ),
                array(
                    'name'  => 'Company Policy',
                    'items' => array(
                        $L('Add Policy', 'add-company-policy.php', array('100')),
                        $L('View Policy', 'view-company-policy.php', array('100')),
                    ),
                ),
                array(
                    'name'  => 'Laptop Handover',
                    'items' => array(
                        $L('Add Handover', 'add-laptop-handover-details.php', array('121')),
                        $L('View Handover', 'view-laptop-handover.php', array('121')),
                    ),
                ),
                array(
                    'name'  => 'HR KRA',
                    'items' => array(
                        $L('KRA Master', 'kra-master.php', array('155')),
                        $L('KPI Master', 'kpi-master.php', array('156')),
                        $L('Employee KRA Requests', 'emp-hr-kra-requests.php', array('162')),
                        $L('Employee KRA Request (detail)', 'emp-hr-kra-request-detail.php', array('162')),
                    ),
                ),
                array(
                    'name'  => 'Training',
                    'items' => array(
                        $L('Training Dashboard', 'training-dashboard.php', array('135')),
                        $L('Add Training', 'add-training.php', array('136')),
                        $L('View Training', 'view-training.php', array('137')),
                        $L('Training Type Master', 'training-types.php', array('138')),
                        $L('Training Reports', 'training-reports.php', array('139')),
                    ),
                ),
                array(
                    'name'  => 'Resignation Clearance',
                    'items' => array(
                        $L('Clearance Dashboard', 'resignation-clearance-dashboard.php', array('141')),
                        $L('View All Resignations', 'view-all-resignations.php', array('142')),
                        $L('IT Clearance', 'view-all-resignations.php?filter=it', array('143')),
                        $L('Department Clearance', 'view-all-resignations.php?filter=dept', array('144')),
                        $L('Accounts Clearance', 'view-all-resignations.php?filter=accounts', array('145')),
                        $L('HR Final Clearance', 'view-all-resignations.php?filter=hr', array('146')),
                        $L('Completed Clearances', 'view-all-resignations.php?filter=completed', array('147')),
                    ),
                ),
                array(
                    'name'  => 'HR Resign Requests',
                    'items' => array_merge(
                        $E('HR Resign', 'hr-pending-resign-request.php', 'hr-approve-resign-request.php', 'hr-reject-resign-request.php', array('64'))
                    ),
                ),
                array(
                    'name'  => 'Manager Resign Requests',
                    'items' => array_merge(
                        $E('Manager Resign', 'manager-pending-resign-request.php', 'manager-approve-resign-request.php', 'manager-reject-resign-request.php', array('68'))
                    ),
                ),
                array(
                    'name'  => 'Manager Hiring Requests',
                    'items' => array(
                        $L('Pending', 'manager-hiring-request.php?tab=pending', array('149')),
                        $L('Approved', 'manager-hiring-request.php?tab=approved', array('149')),
                        $L('Rejected', 'manager-hiring-request.php?tab=rejected', array('149')),
                    ),
                ),
                array(
                    'name'  => 'HR Hiring Requests',
                    'items' => array(
                        $L('Pending', 'hr-hiring-request.php?tab=pending', array('150')),
                        $L('Approved', 'hr-hiring-request.php?tab=approved', array('150')),
                        $L('Rejected', 'hr-hiring-request.php?tab=rejected', array('150')),
                    ),
                ),
            ),
        ),

        'expense-management' => array(
            'slug'   => 'expense-management',
            'title'  => 'Expense Management',
            'blurb'  => 'Employee, vendor, petty cash lists, admin queues, and expense reports.',
            'icon'   => 'ion ion-md-card',
            'accent' => '#b8860b',
            'groups' => array(
                array(
                    'name'  => 'Expense Lists',
                    'items' => array(
                        $L('Employee Expenses', 'all-employee-expenses.php', array('9'), array('not_roll' => 1)),
                        $L('Vendor Expenses', 'all-vendor-exepense-request.php', array('13'), array('not_roll' => 1)),
                        $L('NSO Vendor Expenses', 'all-nso-vendor-exepense-request.php', array('15'), array('not_roll' => 1)),
                        $L('Petty Cash Requests', 'all-pretty-cash-expenses.php', array('12'), array('not_roll' => 1)),
                        $L('All Attendance Requests', 'all-attendance-request.php', array('16'), array('not_roll' => 1)),
                    ),
                ),
                array(
                    'name'  => 'Pending Requests (Admin)',
                    'items' => array(
                        $L('Expenses', 'all-pending-expenses.php', array('4'), array('not_roll' => 1)),
                        $L('Petty Cash', 'all-pending-pretty-cash-request.php', array('5'), array('not_roll' => 1)),
                        $L('Vendor Expenses', 'all-pending-vendor-exepense-request.php', array('6'), array('not_roll' => 1)),
                        $L('NSO Vendor Expenses', 'all-pending-nso-vendor-exepense-request.php', array('7'), array('not_roll' => 1)),
                        $L('Attendance Requests', 'all-pending-attendance-request.php', array('8'), array('not_roll' => 1)),
                    ),
                ),
                array(
                    'name'  => 'Approved Requests (Admin)',
                    'items' => array(
                        $L('Expenses', 'all-approve-expenses.php' . $sp, array('89'), array('not_roll' => 1)),
                        $L('Petty Cash', 'all-approve-pretty-cash-request.php' . $sp, array('90'), array('not_roll' => 1)),
                        $L('Vendor Expenses', 'all-approve-vendor-exepense-request.php' . $sp, array('91'), array('not_roll' => 1)),
                        $L('NSO Vendor Expenses', 'all-approve-nso-vendor-exepense-request.php' . $sp, array('92'), array('not_roll' => 1)),
                        $L('Attendance Requests', 'all-approve-attendance-request.php' . $sp, array('93'), array('not_roll' => 1)),
                        $L('Leave Requests', 'all-approve-leave-requests.php' . $sp, array('93'), array('not_roll' => 1)),
                    ),
                ),
                array(
                    'name'  => 'Account Vendor Expenses',
                    'items' => array(
                        $L('All Pending Account Vendor Expenses', 'all-pending-account-vendor-expenses.php', array('17')),
                    ),
                ),
                array(
                    'name'  => 'Vendor Expenses',
                    'items' => array(
                        $L('Add New Vendor Expenses', 'add-vendor-expenses.php', array('107')),
                        $L('View Vendor Expenses', 'view-vendor-expenses.php', array('107')),
                    ),
                ),
                array(
                    'name'  => 'Expense Reports',
                    'items' => array(
                        $L('Vendor Expense Report', 'vendor-expense-report.php', array()),
                        $L('Vendor Expense Item Report', 'vendor-expense-item-report.php', array()),
                        $L('Expense Report', 'expenses-report.php', array()),
                        $L('Expense Summary Report', 'expense-summary-report.php', array()),
                    ),
                ),
            ),
        ),

        'all-requests' => array(
            'slug'   => 'all-requests',
            'title'  => 'All Requests',
            'blurb'  => 'Consolidated pending, approved, and rejected queues for all request types.',
            'icon'   => 'ion ion-md-list-box',
            'accent' => '#6f42c1',
            'groups' => array(
                array(
                    'name'  => 'Petty Cash Request',
                    'items' => array_merge(
                        $E('Petty Cash', 'all-pending-pretty-cash-request.php', 'all-approve-pretty-cash-request.php' . $sp, 'all-reject-pretty-cash-request.php' . $sp, array('5', '90'))
                    ),
                ),
                array(
                    'name'  => 'Employee Expenses',
                    'items' => array_merge(
                        $E('Employee Expenses', 'all-pending-expenses.php', 'all-approve-expenses.php' . $sp, 'all-reject-expenses.php' . $sp, array('4', '89'))
                    ),
                ),
                array(
                    'name'  => 'Vendor Expenses',
                    'items' => array_merge(
                        $E('Vendor Expenses', 'all-pending-vendor-exepense-request.php', 'all-approve-vendor-exepense-request.php' . $sp, 'all-reject-vendor-exepense-request.php' . $sp, array('6', '91'))
                    ),
                ),
                array(
                    'name'  => 'NSO Vendor Expenses',
                    'items' => array_merge(
                        $E('NSO Vendor Expenses', 'all-pending-nso-vendor-exepense-request.php', 'all-approve-nso-vendor-exepense-request.php' . $sp, 'all-reject-nso-vendor-exepense-request.php' . $sp, array('7', '92'))
                    ),
                ),
                array(
                    'name'  => 'Cash Book Request',
                    'items' => array_merge(
                        $E('Cash Book', 'pending-cash-book-request.php', 'approve-cash-book-request.php' . $sp, 'reject-cash-book-request.php', array('87'))
                    ),
                ),
                array(
                    'name'  => 'Attendance Request',
                    'items' => array_merge(
                        $E('Attendance', 'all-pending-attendance-request.php', 'all-approve-attendance-request.php' . $sp, 'all-reject-attendance-request.php' . $sp, array('8', '93'))
                    ),
                ),
                array(
                    'name'  => 'Leave Request',
                    'items' => array_merge(
                        array($L('Leave — All', 'all-leave-requests.php', array('67', '71', '93'))),
                        $E('Leave', 'all-pending-leave-requests.php', 'all-approve-leave-requests.php' . $sp, 'all-reject-leave-requests.php' . $sp, array('67', '71', '93'))
                    ),
                ),
                array(
                    'name'  => 'Advance Request',
                    'items' => array_merge(
                        $E('Advance', 'all-pending-advance-request.php', 'all-approve-advance-request.php' . $sp, 'all-reject-advance-request.php', array('65', '69'))
                    ),
                ),
                array(
                    'name'  => 'Resign Request',
                    'items' => array_merge(
                        $E('Resign', 'all-pending-resign-request.php', 'all-approve-resign-request.php' . $sp, 'all-reject-resign-request.php', array('64', '68'))
                    ),
                ),
            ),
        ),

        'all-admin-requests' => array(
            'slug'   => 'all-admin-requests',
            'title'  => 'All Admin Request',
            'blurb'  => 'Admin approval queues — approve directly from pending when admin or HR approval is required.',
            'icon'   => 'ion ion-md-checkbox-outline',
            'accent' => '#e83e8c',
            'groups' => array(
                array(
                    'name'  => 'Petty Cash Request',
                    'items' => array_merge(
                        $E('Petty Cash', 'ho-pending-pretty-cash-request.php', 'ho-approve-pretty-cash-request.php' . $sp, 'ho-reject-pretty-cash-request.php' . $sp, array('20'))
                    ),
                ),
                array(
                    'name'  => 'Employee Expenses',
                    'items' => array_merge(
                        $E('Employee Expenses', 'ho-admin-pending-expense-request.php', 'ho-admin-approve-expense-request.php' . $sp, 'ho-admin-reject-expense-request.php' . $sp, array('38'))
                    ),
                ),
                array(
                    'name'  => 'Vendor Expenses',
                    'items' => array_merge(
                        $E('Vendor Expenses', 'account-vendor-pending-expense-request.php', 'account-vendor-approve-expense-request.php' . $sp, 'account-vendor-reject-expense-request.php' . $sp, array('72'))
                    ),
                ),
                array(
                    'name'  => 'NSO Vendor Expenses',
                    'items' => array_merge(
                        $E('NSO Vendor Expenses', 'account-nso-vendor-pending-expense-request.php', 'account-nso-vendor-approve-expense-request.php' . $sp, 'account-nso-vendor-reject-expense-request.php' . $sp, array('73'))
                    ),
                ),
                array(
                    'name'  => 'Cash Book Request',
                    'items' => array_merge(
                        $E('Cash Book', 'pending-cash-book-request.php', 'approve-cash-book-request.php' . $sp, 'reject-cash-book-request.php' . $sp, array('87'))
                    ),
                ),
                array(
                    'name'  => 'Attendance Request',
                    'items' => array_merge(
                        $E('Attendance', 'all-admin-pending-attendance-request.php', 'hr-approve-attendance-request.php' . $sp, 'hr-reject-attendance-request.php' . $sp, array('66'))
                    ),
                ),
                array(
                    'name'  => 'Leave Request',
                    'items' => array_merge(
                        $E('Leave', 'all-admin-pending-leave-request.php', 'hr-approve-leave-request.php' . $sp, 'hr-reject-leave-request.php' . $sp, array('67'))
                    ),
                ),
                array(
                    'name'  => 'Advance Request',
                    'items' => array_merge(
                        $E('Advance', 'all-admin-pending-advance-request.php', 'hr-approve-advance-request.php' . $sp, 'hr-reject-advance-request.php' . $sp, array('65'))
                    ),
                ),
                array(
                    'name'  => 'Resign Request',
                    'items' => array_merge(
                        $E('Resign', 'all-admin-pending-resign-request.php', 'hr-approve-resign-request.php' . $sp, 'hr-reject-resign-request.php' . $sp, array('64'))
                    ),
                ),
            ),
        ),

        'admin-approval' => array(
            'slug'   => 'admin-approval',
            'title'  => 'Admin Approval',
            'blurb'  => 'Admin approvals for employee, vendor, NSO vendor, below-2000 expenses, and petty cash.',
            'icon'   => 'ion ion-md-checkmark-circle',
            'accent' => '#dc3545',
            'groups' => array(
                array(
                    'name'  => 'Employee Expense',
                    'items' => array_merge(
                        $E('Employee Expense', 'ho-admin-pending-expense-request.php', 'ho-admin-approve-expense-request.php', 'ho-admin-reject-expense-request.php', array('38'))
                    ),
                ),
                array(
                    'name'  => 'Employee Expense Below 2000',
                    'items' => array_merge(
                        $E('Employee Expense Below 2000', 'ho-admin-pending-expense-request-below.php', 'ho-admin-approve-expense-request-below.php', 'ho-admin-reject-expense-request-below.php', array('38'))
                    ),
                ),
                array(
                    'name'  => 'Vendor Expense',
                    'items' => array_merge(
                        $E('Vendor Expense', 'account-vendor-pending-expense-request.php', 'account-vendor-approve-expense-request.php', 'account-vendor-reject-expense-request.php', array('72'))
                    ),
                ),
                array(
                    'name'  => 'Vendor Expense Below 2000',
                    'items' => array_merge(
                        $E('Vendor Expense Below 2000', 'account-vendor-pending-expense-request-below.php', 'account-vendor-approve-expense-request-below.php', 'account-vendor-reject-expense-request-below.php', array('119'))
                    ),
                ),
                array(
                    'name'  => 'NSO Vendor Expense',
                    'items' => array_merge(
                        $E('NSO Vendor Expense', 'account-nso-vendor-pending-expense-request.php', 'account-nso-vendor-approve-expense-request.php', 'account-nso-vendor-reject-expense-request.php', array('73'))
                    ),
                ),
                array(
                    'name'  => 'Admin Petty Cash Requests',
                    'items' => array_merge(
                        $E('Admin Petty Cash', 'ho-pending-pretty-cash-request.php', 'ho-approve-pretty-cash-request.php', 'ho-reject-pretty-cash-request.php', array('20'))
                    ),
                ),
            ),
        ),

        'bh-approval' => array(
            'slug'   => 'bh-approval',
            'title'  => 'BH Approval',
            'blurb'  => 'Business head employee expense and penalty approvals.',
            'icon'   => 'ion ion-md-briefcase',
            'accent' => '#e83e8c',
            'groups' => array(
                array(
                    'name'  => 'Employee Expense',
                    'items' => array_merge(
                        $E('Employee Expense', 'ho-business-head-pending-expense-request.php', 'employee-approve-expense-request-below.php', 'employee-reject-expense-request-below.php', array('116'))
                    ),
                ),
                array(
                    'name'  => 'Penalty Approval',
                    'items' => array_merge(
                        $E('Penalty', 'penalty_bh_approval.php', 'penalty_bh_approved.php', 'penalty_bh_rejected.php', array('154'))
                    ),
                ),
            ),
        ),

        'manager-approval' => array(
            'slug'   => 'manager-approval',
            'title'  => 'Manager Approval',
            'blurb'  => 'Manager employee and NSO vendor expense approvals.',
            'icon'   => 'ion ion-md-person',
            'accent' => '#fd7e14',
            'groups' => array(
                array(
                    'name'  => 'Employee Expense',
                    'items' => array_merge(
                        $E('Employee Expense', 'ho-manager-pending-expense-request.php', 'ho-manager-approve-expense-request.php', 'ho-manager-reject-expense-request.php', array('44'))
                    ),
                ),
                array(
                    'name'  => 'NSO Vendor Expense',
                    'items' => array_merge(
                        $E('NSO Vendor Expense', 'nso-vendor-pending-expense-request.php', 'nso-vendor-approve-expense-request.php', 'nso-vendor-reject-expense-request.php', array('43'))
                    ),
                ),
            ),
        ),

        'hr-approval' => array(
            'slug'   => 'hr-approval',
            'title'  => 'HR Approval',
            'blurb'  => 'HR salary expense approvals.',
            'icon'   => 'ion ion-md-contacts',
            'accent' => '#20c997',
            'groups' => array(
                array(
                    'name'  => 'Employee Expense',
                    'items' => array_merge(
                        $E('Employee Expense', 'hr-pending-expense-request.php', 'hr-approve-expense-request.php', 'hr-reject-expense-request.php', array('45'))
                    ),
                ),
            ),
        ),

        'account-approval' => array(
            'slug'   => 'account-approval',
            'title'  => 'Account Approval',
            'blurb'  => 'Accountant employee, vendor, and NSO vendor expense approvals.',
            'icon'   => 'ion ion-md-calculator',
            'accent' => '#28a745',
            'groups' => array(
                array(
                    'name'  => 'Employee Expense',
                    'items' => array_merge(
                        $E('Employee Expense', 'accountant-pending-emp-expense-request.php', 'accountant-approve-emp-expense-request.php', 'accountant-reject-emp-expense-request.php', array('88'))
                    ),
                ),
                array(
                    'name'  => 'Vendor Expense',
                    'items' => array_merge(
                        $E('Vendor Expense', 'manager-vendor-pending-expense-request.php', 'manager-vendor-approve-expense-request.php', 'manager-vendor-reject-expense-request.php', array('17'))
                    ),
                ),
                array(
                    'name'  => 'NSO Vendor Expense',
                    'items' => array_merge(
                        $E('NSO Vendor Expense', 'manager-nso-vendor-pending-expense-request.php', 'manager-nso-vendor-approve-expense-request.php', 'manager-nso-vendor-reject-expense-request.php', array('18'))
                    ),
                ),
            ),
        ),

        'purchase-approval' => array(
            'slug'   => 'purchase-approval',
            'title'  => 'Purchase Approval',
            'blurb'  => 'Purchase department vendor and NSO vendor expense approvals.',
            'icon'   => 'ion ion-md-basket',
            'accent' => '#795548',
            'groups' => array(
                array(
                    'name'  => 'Vendor Expense',
                    'items' => array_merge(
                        $E('Vendor Expense', 'purchase-vendor-pending-expense-request.php', 'purchase-vendor-approve-expense-request.php', 'purchase-vendor-reject-expense-request.php', array('41'))
                    ),
                ),
                array(
                    'name'  => 'NSO Vendor Expense',
                    'items' => array_merge(
                        $E('NSO Vendor Expense', 'nso-purchase-vendor-pending-expense-request.php', 'nso-purchase-vendor-approve-expense-request.php', 'nso-purchase-vendor-reject-expense-request.php', array('42'))
                    ),
                ),
            ),
        ),

        'bdm-approval' => array(
            'slug'   => 'bdm-approval',
            'title'  => 'BDM Approval',
            'blurb'  => 'BDM vendor expense, attendance request, and penalty approvals.',
            'icon'   => 'ion ion-md-pin',
            'accent' => '#6610f2',
            'groups' => array(
                array(
                    'name'  => 'Vendor Expense',
                    'items' => array_merge(
                        $E('Vendor Expense', 'bdm-vendor-pending-expense-request.php', 'bdm-vendor-approve-expense-request.php', 'bdm-vendor-reject-expense-request.php', array('40'))
                    ),
                ),
                array(
                    'name'  => 'BDM Attendance Request',
                    'items' => array_merge(
                        $E('BDM Attendance', 'bdm-pending-attendance-request.php', 'bdm-approve-attendance-request.php', 'bdm-reject-attendance-request.php', array('118'))
                    ),
                ),
                array(
                    'name'  => 'Penalty Approval',
                    'items' => array_merge(
                        $E('Penalty', 'penalty_bdm_approval.php', 'penalty_bdm_approved.php', 'penalty_bdm_rejected.php', array('153'))
                    ),
                ),
            ),
        ),

        'finance-accounts' => array(
            'slug'   => 'finance-accounts',
            'title'  => 'Finance & Accounts',
            'blurb'  => 'Cash book, advances, wallet, bank details, vendor payments, and P&L.',
            'icon'   => 'ion ion-md-cash',
            'accent' => '#28a745',
            'groups' => array(
                array(
                    'name'  => 'Cash Book',
                    'items' => array(
                        $L('Add Cash Book', 'add-cash-book.php', array('85')),
                        $L('View Cash Book', 'view-cash-book.php?FromDate=' . $today . '&ToDate=' . $today, array('19', '87', '85')),
                        $L('Pending Cash Report', 'pending-cash-report.php', array('19', '87', '85')),
                        $L('Cash & Online Collection', 'fr-bill-outstanding.php', array('19', '87', '85')),
                        $L('Pending Cash Book Requests', 'pending-cash-book-request.php', array('87')),
                        $L('Approved Cash Book Requests', 'approve-cash-book-request.php', array('87')),
                        $L('Rejected Cash Book Requests', 'reject-cash-book-request.php', array('87')),
                    ),
                ),
                array(
                    'name'  => 'Financial Management',
                    'items' => array(
                        $L('Advance Salary', 'view-advance-salary.php', array('36')),
                        $L('Advance Requests', 'advance-request.php', array('63')),
                        $L('Salary Slip', 'add-salary-slip.php', array('114')),
                        $L('Wallet', 'wallet.php', array('75')),
                        $L('Withdraw Amount Request', 'amount-request.php', array('74')),
                    ),
                ),
                array(
                    'name'  => 'Bank Details',
                    'items' => array(
                        $L('View Bank Details', 'view-bank-details.php', array('39')),
                        $L('Generate Bank Detail Excel', 'bank-detail-excel.php' . $sp, array('39')),
                    ),
                ),
                array(
                    'name'  => 'Pay Advance Amount',
                    'items' => array(
                        $L('Pending Advance Payment', 'account-pay-advance-payment.php', array('29')),
                        $L('Paid Advance Payment', 'account-paid-advance-payment.php', array('29')),
                    ),
                ),
                array(
                    'name'  => 'Pay Vendor Amount',
                    'items' => array(
                        $L('Pending Payment', 'view-payable-amount-vendors.php', array('47')),
                        $L('Pay Amount At a Time', 'pay-all-exp-amount-to-vendor.php', array('47')),
                        $L('Payment Done', 'view-payment-done-amount-vendors.php', array('47')),
                    ),
                ),
                array(
                    'name'  => 'Pay NSO Vendor Amount',
                    'items' => array(
                        $L('Pending Payment', 'view-payable-amount-nso-vendors.php', array('48')),
                        $L('Pay Amount At a Time', 'pay-all-exp-amount-to-nso-vendor.php', array('48')),
                        $L('Payment Done', 'view-payment-done-amount-nso-vendors.php', array('48')),
                    ),
                ),
                array(
                    'name'  => 'Profit & Loss',
                    'items' => array(
                        $L('View Profit & Loss', 'view-profit-loss.php', array('76')),
                    ),
                ),
            ),
        ),

        'petty-cash-management' => array(
            'slug'   => 'petty-cash-management',
            'title'  => 'Petty Cash Management',
            'blurb'  => 'Account and manager petty cash request queues.',
            'icon'   => 'ion ion-md-wallet',
            'accent' => '#ffc107',
            'groups' => array(
                array(
                    'name'  => 'Account Petty Cash Requests',
                    'items' => array_merge(
                        $E('Account Petty Cash', 'account-pending-pretty-cash-request.php', 'account-approve-pretty-cash-request.php', 'account-reject-pretty-cash-request.php', array('28'))
                    ),
                ),
                array(
                    'name'  => 'Manager Petty Cash Requests',
                    'items' => array_merge(
                        $E('Manager Petty Cash', 'manager-pending-pretty-cash-request.php', 'manager-approve-pretty-cash-request.php', 'manager-reject-pretty-cash-request.php', array('46'))
                    ),
                ),
            ),
        ),

        'cofo-fico-management' => array(
            'slug'   => 'cofo-fico-management',
            'title'  => 'COFO / FICO Management',
            'blurb'  => 'COFO details, approval requests, and payout reports.',
            'icon'   => 'ion ion-md-business',
            'accent' => '#9b59b6',
            'groups' => array(
                array(
                    'name'  => 'COFO Details',
                    'items' => array(
                        $L('Add COFO Details', 'add-cofo-details.php', array('117')),
                        $L('View COFO Details', 'view-cofo-details.php', array('117')),
                    ),
                ),
                array(
                    'name'  => 'COFO Detail Requests',
                    'items' => array_merge(
                        $E('COFO Details', 'pending-cofo-details.php', 'approved-cofo-details.php', 'reject-cofo-details.php', array('126'))
                    ),
                ),
                array(
                    'name'  => 'Payout Reports',
                    'items' => array(
                        $L('COFO Payout Report', 'cofo-payout-report.php', array('132')),
                        $L('FICO Payout Report', 'fico-payout-report.php', array('132')),
                    ),
                ),
            ),
        ),

        'franchise-business-partner' => array(
            'slug'   => 'franchise-business-partner',
            'title'  => 'Franchise & Business Partner',
            'blurb'  => 'Zones, franchise, queries, vendors, and business partners.',
            'icon'   => 'ion ion-md-storefront',
            'accent' => '#9b59b6',
            'groups' => array(
                array(
                    'name'  => 'Franchise',
                    'items' => array(
                        $L('Zone', 'main-zones.php', array('52')),
                        $L('Region', 'zones.php', array('52')),
                        $L('Sub Zone', 'sub-zones.php', array('53')),
                        $L('Assign Franchise To Zone', 'view-assign-franchise-to-zone.php', array('54')),
                        $L('Add Franchise', 'add-customer.php', array('55')),
                        $L('View Franchise', 'view-customers.php', array('55')),
                    ),
                ),
                array(
                    'name'  => 'Franchise Query',
                    'items' => array(
                        $L('Add Franchise Query', 'add-customer-query.php', array('61', '14')),
                        $L('View Franchise Query', 'view-customer-query.php', array('61')),
                    ),
                ),
                array(
                    'name'  => 'Business Partner',
                    'items' => array(
                        $L('Add Business Partner', 'add-freelancer-2.php', array('58', '14')),
                        $L('View My Partner', 'view-freelancer-2.php', array('58')),
                    ),
                ),
                array(
                    'name'  => 'Freelancer / Business Partner',
                    'items' => array(
                        $L('Add Business Partner', 'add-freelancer.php', array('59', '14')),
                        $L('View Business Partner', 'view-freelancer.php', array('59')),
                    ),
                ),
                array(
                    'name'  => 'Vendors',
                    'items' => array(
                        $L('Add Vendor', 'add-vendor.php', array('57', '14')),
                        $L('View Vendors', 'view-vendors.php', array('57')),
                    ),
                ),
            ),
        ),

        'customer-service' => array(
            'slug'   => 'customer-service',
            'title'  => 'Customer Service',
            'blurb'  => 'Customer feedback, complaints, and chai passes.',
            'icon'   => 'ion ion-md-headset',
            'accent' => '#007bff',
            'groups' => array(
                array(
                    'name'  => 'Customer Service',
                    'items' => array(
                        $L('Customer Feedback', 'customer-feedback-report.php', array('37')),
                        $L('Chai Passes', 'view_chai_pass.php', array('127')),
                    ),
                ),
                array(
                    'name'  => 'Complaints',
                    'items' => array(
                        $L('Add Complaints', 'add-complaints.php', array('50')),
                        $L('View Complaints', 'view-complaints.php', array('50')),
                        $L('Allocate Complaints', 'allocate-complaints.php', array('50')),
                        $L('Pending Complaints', 'view-complaints.php?Status=1', array('50')),
                        $L('In Process Complaints', 'view-complaints.php?Status=2', array('50')),
                        $L('Reject Complaints', 'view-complaints.php?Status=3', array('50')),
                        $L('Completed Complaints', 'view-complaints.php?Status=4', array('50')),
                    ),
                ),
            ),
        ),

        'task-ticket' => array(
            'slug'   => 'task-ticket',
            'title'  => 'Task & Ticket',
            'blurb'  => 'Tasks, ratings, and ticket management.',
            'icon'   => 'ion ion-md-clipboard',
            'accent' => '#fd7e14',
            'groups' => array(
                array(
                    'name'  => 'Task',
                    'items' => array(
                        $L('Add Task', 'add-task2.php', array()),
                        $L('View Task', 'view-task2.php', array()),
                        $L('Task Rating', 'task-rating-report.php', array()),
                    ),
                ),
                array(
                    'name'  => 'Ticket Management',
                    'items' => array(
                        $L('Ticket Dashboard', 'ticket_management/ticket-dashboard.php', array()),
                    ),
                ),
            ),
        ),

        'asset-management' => array(
            'slug'   => 'asset-management',
            'title'  => 'Asset Management',
            'blurb'  => 'Add and view company assets.',
            'icon'   => 'ion ion-md-cube',
            'accent' => '#6c757d',
            'groups' => array(
                array(
                    'name'  => 'Asset Management',
                    'items' => array(
                        $L('Add Asset', 'add-asset.php', array('50')),
                        $L('View Assets', 'view-assets.php', array('50')),
                    ),
                ),
            ),
        ),

        'approvals' => array(
            'slug'   => 'approvals',
            'title'  => 'Approvals',
            'blurb'  => 'HR and manager advance and leave request queues.',
            'icon'   => 'ion ion-md-checkbox',
            'accent' => '#6f42c1',
            'groups' => array(
                array(
                    'name'  => 'HR Advance Requests',
                    'items' => array_merge(
                        $E('HR Advance', 'hr-pending-advance-request.php', 'hr-approve-advance-request.php', 'hr-reject-advance-request.php', array('65'))
                    ),
                ),
                array(
                    'name'  => 'HR Leave Requests',
                    'items' => array_merge(
                        $E('HR Leave', 'hr-pending-leave-request.php', 'hr-approve-leave-request.php', 'hr-reject-leave-request.php', array('67'))
                    ),
                ),
                array(
                    'name'  => 'Manager Advance Requests',
                    'items' => array_merge(
                        $E('Manager Advance', 'manager-pending-advance-request.php', 'manager-approve-advance-request.php', 'manager-reject-advance-request.php', array('69'))
                    ),
                ),
                array(
                    'name'  => 'Manager Leave Requests',
                    'items' => array_merge(
                        $E('Manager Leave', 'manager-pending-leave-request.php', 'manager-approve-leave-request.php', 'manager-reject-leave-request.php', array('71'))
                    ),
                ),
                array(
                    'name'  => 'On Behalf Requests',
                    'items' => array(
                        $L('Add Leave Request (On Behalf)', 'add-leave-request.php', array('174')),
                    ),
                ),
            ),
        ),

        'idea-box' => array(
            'slug'   => 'idea-box',
            'title'  => 'Idea Box',
            'blurb'  => 'Manager and admin idea submissions and full report.',
            'icon'   => 'ion ion-md-bulb',
            'accent' => '#ffc107',
            'groups' => array(
                array(
                    'name'  => 'Idea Box',
                    'items' => array(
                        $L('Manager Ideas', 'idea-box-manager-requests.php', array('159')),
                        $L('Admin Ideas', 'idea-box-admin-requests.php', array('160')),
                        $L('Full Report', 'idea-box-full-report.php', array('161')),
                    ),
                ),
            ),
        ),

        'documents-media' => array(
            'slug'   => 'documents-media',
            'title'  => 'Documents & Media',
            'blurb'  => 'Documents, download center, galleries, and recipe SOP.',
            'icon'   => 'ion ion-md-folder',
            'accent' => '#17a2b8',
            'groups' => array(
                array(
                    'name'  => 'Documents & Media',
                    'items' => array(
                        $L('Attach Documents', 'attach-documents.php', array('34')),
                        $L('View Documents', 'view-attach-documents.php', array('34')),
                        $L('Download Center', 'view-upload-pdfs.php', array('34')),
                        $L('MahaTube', 'video-gallery.php', array('34')),
                        $L('Image Gallery', 'image-gallery.php', array('34')),
                        $L('Recipe SOP', 'youtube-videos.php', array('34')),
                    ),
                ),
            ),
        ),

        'e-commerce' => array(
            'slug'   => 'e-commerce',
            'title'  => 'E-Commerce',
            'blurb'  => 'Shop orders, products, categories, and storefront content.',
            'icon'   => 'ion ion-md-cart',
            'accent' => '#e83e8c',
            'groups' => array(
                array(
                    'name'  => 'E-Commerce',
                    'items' => array(
                        $L('Payment Method', 'payment-method.php', array('130')),
                        $L('Cancel Reason', 'cancel-reason.php', array('130')),
                        $L('Referral/Coupon/Offer Code', 'coupon-code.php', array('130')),
                        $L("Today's Orders", 'today-orders.php', array('130')),
                        $L('View Orders', 'view-orders.php', array('130')),
                        $L('Add Product', 'add-shop-product.php', array('130')),
                        $L('View Products', 'view-shop-products.php', array('130')),
                        $L('Category', 'shop-category.php', array('130')),
                        $L('Sub Category', 'shop-sub-category.php', array('130')),
                        $L('Product Attributes', 'attribute-value.php', array('130')),
                        $L('Shipping Price', 'shipping-price.php', array('130')),
                        $L('Home Sliders', 'home-sliders.php', array('130')),
                        $L('Home Banners', 'home-banners.php', array('130')),
                        $L("FAQ's", 'faqs.php', array('130')),
                    ),
                ),
            ),
        ),

        'reports' => array(
            'slug'   => 'reports',
            'title'  => 'Reports',
            'blurb'  => 'Sales, attendance, expense, wallet, HR, franchise, and audit reports.',
            'icon'   => 'ion ion-md-stats',
            'accent' => '#343a40',
            'groups' => array(
                array(
                    'name'  => 'Sales Reports',
                    'items' => array(
                        $L('Daily Sale Report', 'daily-sale-report.php', array('21')),
                        $L('Daily Sale Report 2', 'daily-sale-report-2.php', array('22')),
                        $L('Weekly Sale Report', 'weekly-sale-report.php', array('23')),
                        $L('Weekly Sale Report 2', 'weekly-sale-report-2.php', array('24')),
                        $L('Item Wise Sale Report', 'item-wise-sale-report.php', array('25')),
                        $L('Petty Cash Inventory Report', 'petty-cash-inventory-report.php', array('25')),
                        $L('Daily Wise Sale Report', 'daily-wise-sale-report.php?FromDate=' . $today . '&ToDate=' . $today, array('105')),
                    ),
                ),
                array(
                    'name'  => 'Expense Reports',
                    'items' => array(
                        $L('Expense Report', 'expenses-report.php', array('26')),
                        $L('Expense Summary Report', 'expense-summary-report.php', array('27')),
                        $L('Vendor Expense Report', 'vendor-expense-report.php', array()),
                        $L('Vendor Expense Item Report', 'vendor-expense-item-report.php', array('24')),
                    ),
                ),
                array(
                    'name'  => 'Attendance Reports',
                    'items' => array(
                        $L('Employee Attendance Report', 'attendance-report-new.php', array('81')),
                        $L('BDM Attendance Report', 'bdm-attendance-report.php', array('81')),
                        $L('Employee Attendance Report Month Wise', 'attendance-report-month-wise.php', array('81')),
                        $L('Employee Attendance Report Percentage Wise', 'attendance-report-percentage.php', array('98')),
                        $L('Employee Attendance Analysis Reports', 'attendance-analysis-reports.php', array('151')),
                        $L('Employee Attendance Timing Report', 'employee-attendance-timing-report.php', array('163')),
                        $L('Employee Absent Report', 'employee-absent-report.php', array('163')),
                        $L('Late Commerce Report', 'late-commerce-report.php', array('163')),
                        $L('Employee Location Tracking', 'employee-location-tracking-report.php', array()),
                        $L('Employee Location Report', 'emp-location-report.php', array()),
                    ),
                ),
                array(
                    'name'  => 'HR Reports',
                    'items' => array(
                        $L('Franchise Daily Checklist Report', 'franchise-daily-survey-report.php', array('77')),
                        $L('Employee Task Report', 'employee-daily-report.php', array('80')),
                        $L('Daily MIS of Joining', 'daily-mis-joining.php', array('96')),
                        $L('Daily MIS of Attrition (Exit)', 'daily-mis-attrition.php', array('97')),
                        $L('Generate Salary Sheet', 'generate-salary-sheet.php', array('120')),
                        $L('Leave Balance Report', 'pending-leave-report.php', array('120')),
                        $L('Exit Interview Report', 'exit-interview-report.php', array('152')),
                    ),
                ),
                array(
                    'name'  => 'Franchise Reports',
                    'items' => array(
                        $L('Franchise Query Report', 'franchise-query-report.php', array('79')),
                        $L('Franchise Time Gap Report', 'franchise-time-gap-report.php?FromDate=' . $today . '&ToDate=' . $today, array('131')),
                    ),
                ),
                array(
                    'name'  => 'Wallet Reports',
                    'items' => array(
                        $L('Employee Wallet Report', 'employee-wallet-report.php', array('82')),
                        $L('Employee Wallet Outstanding', 'employee-wallet-outstanding.php', array('83')),
                        $L('Employee Wallet Outstanding 2', 'employee-wallet-outstanding-2.php', array('84')),
                    ),
                ),
                array(
                    'name'  => 'Other Reports',
                    'items' => array(
                        $L('Stock Available Report', 'stock-available-report.php', array('104')),
                        $L('Cash Handover Report', 'cash-handover-report.php', array('102')),
                        $L('Shop Sessions Report', 'shop-open-close-report.php?FromDate=' . $today . '&ToDate=' . $today, array('106')),
                        $L('Outlet Audit Checklist Report', 'outlet-audit-checklist-report.php', array('148')),
                        $L('Cash & Online Collection Report', 'fr-bill-outstanding.php', array()),
                    ),
                ),
                array(
                    'name'  => 'Account Reports',
                    'items' => array(
                        $L('Wallet Balance Report', 'wallet-balance-report.php', array('78')),
                        $L('Transaction Report', 'transaction-report.php', array('78')),
                        $L('Expense Category Wise Report', 'expense-category-wise-report.php', array('78')),
                    ),
                ),
            ),
        ),

        'masters-settings' => array(
            'slug'   => 'masters-settings',
            'title'  => 'Masters / Settings',
            'blurb'  => 'Locations, designations, vendor types, and audit masters.',
            'icon'   => 'ion ion-md-list-box',
            'accent' => '#495057',
            'groups' => array(
                array(
                    'name'  => 'Masters / Settings',
                    'items' => array(
                        $L('Country', 'country.php', array()),
                        $L('State', 'state.php', array()),
                        $L('City', 'city.php', array()),
                        $L('Popup Image', 'popup-image.php', array()),
                        $L('Type Of Vendor', 'common-master.php?pageid=1', array()),
                        $L('Designation', 'user-type.php', array()),
                        $L('Departments', 'departments.php', array()),
                        $L('Franchise Locations', 'franchaise-location.php', array()),
                        $L('Model Type', 'common-master.php?pageid=2', array()),
                        $L('Cashback Amount', 'cashback-amount.php', array()),
                        $L('Shopping Cashback Price Range', 'sale-price-range.php', array()),
                        $L('Add Money Cashback Price Range', 'add-money-price-range.php', array()),
                        $L('Outlet Audit Category', 'outlet-audit-category.php', array()),
                        $L('Outlet Audit Questions', 'outlet-audit-questions.php', array()),
                    ),
                ),
            ),
        ),

        'user-accounts' => array(
            'slug'   => 'user-accounts',
            'title'  => 'User Accounts',
            'blurb'  => 'Control panel user account list, add user, and activity logs.',
            'icon'   => 'ion ion-md-key',
            'accent' => '#0F5A4A',
            'groups' => array(
                array(
                    'name'  => 'User Accounts',
                    'items' => array(
                        $L('View Users', 'view-users.php', array('123')),
                        $L('Add User', 'add-user.php', array('124')),
                        $L('User Activity Logs', 'report-user-activity-logs.php', array('125')),
                    ),
                ),
            ),
        ),

        'my-account' => array(
            'slug'   => 'my-account',
            'title'  => 'My Account',
            'blurb'  => 'Company profile, password, admin tools, and logout.',
            'icon'   => 'ion ion-md-person',
            'accent' => '#0F5A4A',
            'groups' => array(
                array(
                    'name'  => 'My Account',
                    'items' => array(
                        $L('Company Profile', 'company-information.php', array(), array('roll' => 1)),
                        $L('Change Password', 'change-password.php', array()),
                        $L('Delete Vendor Expenses', 'delete-vendor-exepense-request.php', array(), array('user_ids' => array(2650, 2651))),
                        $L('Delete NSO Vendor Expenses', 'delete-nso-vendor-exepense-request.php', array(), array('user_ids' => array(2650, 2651))),
                        $L('Upload Vendor Expense PDF', 'update-vendor-expenses.php', array(), array('user_ids' => array(2650, 2651))),
                        $L('Upload Employee Expense Documents', 'upload-emp-expense-files.php', array(), array('user_ids' => array(2650, 2651))),
                        $L('Log Out', 'logout.php', array()),
                    ),
                ),
            ),
        ),
    );
}
