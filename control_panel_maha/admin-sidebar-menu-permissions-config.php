<?php
/**
 * Control Panel menu access sections — aligned with admin sidebar (horizontal nav).
 * Each section title matches a sidebar top-level (or approval) menu name.
 */
return array(
    array(
        'title' => 'Dashboard',
        'ids'   => array(1, 2, 3, 95, 108, 109, 110, 111, 112, 113, 122, 129),
    ),
    array(
        'title' => 'Operations',
        'ids'   => array(30, 31, 32, 33, 99, 164, 165),
    ),
    array(
        'title' => 'Attendance Management',
        'groups' => array(
            array('title' => 'Attendance', 'ids' => array(35, 115)),
            array('title' => 'On Behalf Requests', 'ids' => array(173)),
        ),
    ),
    array(
        'title' => 'HR & Employee Management',
        'groups' => array(
            array('title' => 'Employee Master', 'ids' => array(94, 56)),
            array('title' => 'Reports', 'ids' => array(133, 77)),
            array('title' => 'Company Policy', 'ids' => array(100)),
            array('title' => 'Laptop Handover', 'ids' => array(121)),
            array('title' => 'HR KRA', 'ids' => array(155, 156, 157, 158, 162)),
            array('title' => 'Training', 'ids' => array(135, 136, 137, 138, 139)),
            array('title' => 'Resignation Clearance', 'ids' => array(140, 141, 142, 143, 144, 145, 146, 147)),
        ),
    ),
    array(
        'title' => 'Expense Management',
        'groups' => array(
            array('title' => 'All Expense Lists', 'ids' => array(9, 12, 13, 15, 16)),
            array('title' => 'Pending Requests (Admin)', 'ids' => array(4, 5, 6, 7, 8)),
            array('title' => 'Approved Requests (Admin)', 'ids' => array(89, 90, 91, 92, 93, 76)),
            array('title' => 'Vendor Expenses', 'ids' => array(107)),
        ),
    ),
    array(
        'title' => 'All Expenses',
        'groups' => array(
            array('title' => 'Employee Expense', 'ids' => array(4, 89)),
            array('title' => 'Petty Cash Request', 'ids' => array(5, 90)),
            array('title' => 'Vendor Expense', 'ids' => array(6, 91)),
            array('title' => 'NSO Expense', 'ids' => array(7, 92)),
            array('title' => 'Leave Request', 'ids' => array(67, 71, 93)),
            array('title' => 'Advance Request', 'ids' => array(65, 69)),
            array('title' => 'Attendance Request', 'ids' => array(8, 93)),
            array('title' => 'Resign Request', 'ids' => array(64, 68)),
            array('title' => 'Hiring Request', 'ids' => array(149, 150)),
            array('title' => 'Outlet Closure Request', 'ids' => array(168)),
            array('title' => 'Penalty Request', 'ids' => array(153, 154)),
        ),
    ),
    array(
        'title' => 'All Requests',
        'groups' => array(
            array('title' => 'Petty Cash Request', 'ids' => array(5, 90)),
            array('title' => 'Employee Expenses', 'ids' => array(4, 89)),
            array('title' => 'Vendor Expenses', 'ids' => array(6, 91)),
            array('title' => 'NSO Vendor Expenses', 'ids' => array(7, 92)),
            array('title' => 'Cash Book Request', 'ids' => array(87)),
            array('title' => 'Attendance Request', 'ids' => array(8, 93)),
            array('title' => 'Leave Request', 'ids' => array(67, 71, 93)),
            array('title' => 'Advance Request', 'ids' => array(65, 69)),
            array('title' => 'Resign Request', 'ids' => array(64, 68)),
        ),
    ),
    array(
        'title' => 'All Admin Request',
        'groups' => array(
            array('title' => 'Petty Cash Request', 'ids' => array(20)),
            array('title' => 'Employee Expenses', 'ids' => array(38)),
            array('title' => 'Vendor Expenses', 'ids' => array(72)),
            array('title' => 'NSO Vendor Expenses', 'ids' => array(73)),
            array('title' => 'Cash Book Request', 'ids' => array(87)),
            array('title' => 'Attendance Request', 'ids' => array(66)),
            array('title' => 'Leave Request', 'ids' => array(67)),
            array('title' => 'Advance Request', 'ids' => array(65)),
            array('title' => 'Resign Request', 'ids' => array(64)),
        ),
    ),
    array(
        'title' => 'Admin Approval',
        'groups' => array(
            array('title' => 'Employee Expense', 'ids' => array(38)),
            array('title' => 'Vendor Expense', 'ids' => array(72, 119)),
            array('title' => 'NSO Vendor Expense', 'ids' => array(73)),
            array('title' => 'Admin Petty Cash Requests', 'ids' => array(20)),
            array('title' => 'Outlet Closure', 'ids' => array(168)),
        ),
    ),
    array(
        'title' => 'BH Approval',
        'groups' => array(
            array('title' => 'Employee Expense', 'ids' => array(116)),
            array('title' => 'Penalty Approval', 'ids' => array(154)),
            array('title' => 'Outlet Closure', 'ids' => array(166)),
        ),
    ),
    array(
        'title' => 'Manager Approval',
        'ids'   => array(44, 46, 71, 69, 70, 68, 149, 159, 43),
    ),
    array(
        'title' => 'HR Approval',
        'ids'   => array(45, 66, 67, 65, 64, 150),
    ),
    array(
        'title' => 'Account Approval',
        'ids'   => array(88, 17, 18, 28, 167),
    ),
    array(
        'title' => 'Purchase Approval',
        'groups' => array(
            array('title' => 'Vendor Expense', 'ids' => array(41)),
            array('title' => 'NSO Vendor Expense', 'ids' => array(42)),
        ),
    ),
    array(
        'title' => 'BDM Approval',
        'groups' => array(
            array('title' => 'Vendor Expense', 'ids' => array(40)),
            array('title' => 'BDM Attendance Request', 'ids' => array(118)),
            array('title' => 'Penalty Approval', 'ids' => array(153)),
        ),
    ),
    array(
        'title' => 'Finance & Accounts',
        'ids'   => array(85, 19, 87, 36, 63, 114, 75, 74, 39, 29, 47, 48),
    ),
    array(
        'title' => 'COFO / FICO Management',
        'ids'   => array(117, 126, 132),
    ),
    array(
        'title' => 'Franchise & Business Partner',
        'ids'   => array(52, 53, 54, 55, 61, 58, 59, 57, 14, 10, 11),
    ),
    array(
        'title' => 'Customer Service',
        'ids'   => array(37, 127),
    ),
    array(
        'title' => 'Task & Ticket',
        'ids'   => array(101),
    ),
    array(
        'title' => 'Asset Management',
        'groups' => array(
            array('title' => 'Assets', 'ids' => array(50)),
            array('title' => 'SIM Card Management', 'ids' => array(169, 170, 171, 172)),
        ),
    ),
    array(
        'title' => 'Approvals (Advance & Leave)',
        'groups' => array(
            array('title' => 'On Behalf Requests', 'ids' => array(174)),
        ),
    ),
    array(
        'title' => 'Idea Box',
        'ids'   => array(159, 160, 161),
    ),
    array(
        'title' => 'Documents & Media',
        'ids'   => array(34),
    ),
    array(
        'title' => 'E-Commerce',
        'ids'   => array(130),
    ),
    array(
        'title' => 'Reports',
        'ids'   => array(21, 22, 23, 24, 25, 26, 27, 79, 80, 81, 82, 83, 84, 78, 96, 97, 98, 102, 104, 105, 106, 120, 131, 148, 151, 152, 163),
    ),
    array(
        'title' => 'Masters / Settings',
        'ids'   => array(49, 51, 60, 62),
    ),
    array(
        'title' => 'User Accounts',
        'ids'   => array(123, 124, 125),
    ),
);
