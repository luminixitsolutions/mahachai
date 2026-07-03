<?php
/**
 * Organized admin sidebar menu — 19 sections with flyout submenus.
 */

$today = date('Y-m-d');
$searchParams = '?FromDate=' . $today . '&ToDate=' . $today . '&Search=Search';

// ─── 0. Module hub (always visible) ───────────────────────────────────────────
$hubActive = isset($MainPage) && $MainPage === 'Module-Hub';
echo '<li class="sidenav-item' . ($hubActive ? ' active' : '') . '" data-menu-slug="module-hub">';
echo '<a href="menu-dashboard.php" class="sidenav-link"><div>Modules</div></a></li>';

// ─── 1. Dashboard ────────────────────────────────────────────────────────────
sb_group('Dashboard', 'Pretty-Cash-Ho', sb_capture(function () {
    sb_link('dashboard.php', 'Main Dashboard', 'Dashboard');
     sb_link('menu-dashboard.php', 'Menu Dashboard', 'Dashboard');
    sb_render_if_visible('108', function () {
        sb_link('sales-dashboard.php', 'Sales Dashboard', 'Weekly-Sale-Report-2');
    });
    sb_render_if_visible('113', function () {
        sb_link('stock-monitoring-dashboard.php', 'Stock Dashboard', 'Dashboard');
        sb_link('task-monitoring-dashboard.php', 'Task Dashboard', 'Dashboard');
        sb_link('outlet-audit-dashboard.php', 'Outlet Audit Dashboard', 'Dashboard');
    });
    sb_render_if_visible('122', function () {
        sb_link('feedback-dashboard.php', 'Feedback Dashboard', 'Dashboard');
        sb_link('invoice-payment-mode-dashboard.php', 'Payment mode change dashboard', 'Payment-Mode-Dashboard');
    });
    sb_render_if_visible('129', function () {
        sb_link('chai-pass-dashboard.php', 'Chai Pass Dashboard', '');
    });
    sb_render_if_visible('109', function () {
        sb_link('hr-dashboard.php', 'HR Dashboard', '');
    });
    sb_render_if_visible('110', function () {
        sb_link('account-dashboard.php', 'Account Dashboard', '');
    });
    sb_render_if_visible('111', function () {
        sb_link('franchise-dashboard.php', 'Franchise Dashboard', 'Employee-Dashboard');
    });
    sb_render_if_visible('112', function () {
        sb_link('project-dashboard.php', 'Project Dashboard', 'Employee-Dashboard');
    });
    sb_render_if_visible('95', function () {
        sb_link('employee-dashboard.php', 'Employee Dashboard', 'Employee-Dashboard');
    });
    sb_render_if_visible('1', function () {
        sb_link('expense-sale-dashboard.php', 'Sub Zone Expense vs Sale', '');
    });
    sb_render_if_visible('2', function () {
        sb_link('expense-sale-dashboard.php?value=zone', 'Zone Expense vs Sale', '');
    });
    sb_render_if_visible('3', function () {
        sb_link('expense-sale-report.php', 'Franchise Expense vs Sale Report', 'Weekly-Sale-Report-2');
    });
}), sb_section_visible('Dashboard'));

// ─── 2. Operations ───────────────────────────────────────────────────────────
$opsVisible = sb_section_visible('Operations');
sb_group('Operations', 'Operations-Management', sb_capture(function () {
    sb_render_if_visible('30', function () {
        sb_link('control-room-report.php', 'Control Room', 'Controll-Room');
    });
    sb_link('control-room-report.php', 'Control Room Report', 'Employee-Dashboard');
    sb_render_if_visible('31', function () {
        sb_link('view-store-manager-duties.php', 'Store Manager Duties', 'Store-Manager-Duties');
    });
    sb_render_if_visible('32', function () {
        sb_link('view-manager-checkpoint.php', 'Manager Checkpoints', 'Manager-Checkpoint');
    });
    sb_render_if_visible('164', function () {
        sb_link('youtube-videos.php', 'Recipe SOP', 'YouTube-Videos-List');
    });
    sb_link('upload-compliance.php', 'Upload Compliance', function () {
        global $MainPage;
        return isset($MainPage) && $MainPage == 'Upload-Compliance';
    });
    sb_render_if_visible('99', function () {
        sb_link('aliance-upload-docs.php', 'Alliances Upload Documents', 'Controll-Room');
    });
    sb_render_if_visible('33', function () {
        sb_submenu('Fuel Station Checklist', function () {
            sb_link('view-fuel-station-checklist.php', 'Pending', 'Pending-Fuel-Checklist');
            sb_link('approve-fuel-station-checklist.php', 'Approved', 'Approve-Fuel-Checklist');
            sb_link('reject-fuel-station-checklist.php', 'Rejected', 'Reject-Fuel-Checklist');
        });
    });
    sb_render_if_visible('165', function () {
        sb_submenu('Location Feasibility Checklist', function () {
            sb_link('location-feasibility-approval.php', 'Pending', 'Pending-Fuel-Checklist');
            sb_link('location-feasibility-approval.php?status=approved', 'Approved', 'Approve-Fuel-Checklist');
            sb_link('location-feasibility-approval.php?status=rejected', 'Rejected', 'Reject-Fuel-Checklist');
        });
    });
}), $opsVisible);

// ─── 3. Attendance Management ────────────────────────────────────────────────
$attVisible = sb_section_visible('Attendance Management');
sb_group('Attendance Management', 'Documents', sb_capture(function () {
    if (sb_has_opt(35)) {
        sb_link('mark-attendance.php', 'Mark Attendance', 'Mark-Attendance');
    }
    if (sb_has_opt(115)) {
        sb_link('update-attendance.php', 'Update Single Attendance', '');
        sb_link('update-attendance-mult.php', 'Update Multiple Attendance', '');
    }
    sb_render_if_visible('35', function () {
        sb_link('attendance-task-report.php', 'Attendance Reports', 'Attendance-Task-Report');
    });
}), $attVisible);

// ─── 4. HR & Employee Management ─────────────────────────────────────────────
$hrVisible = sb_section_visible('HR & Employee Management');
sb_group('HR & Employee Management', 'Employee', sb_capture(function () {
    if (sb_has_opt(array(94, 56))) {
        sb_submenu('Employee Master', function () {
            sb_render_if_visible('94', function () {
                sb_link('employee-scheme.php', 'Employee Scheme', 'Add-Employee');
            });
            sb_render_if_visible('56', function () {
                sb_link('add-employee.php', 'Add Employee', 'Add-Employee');
            });
            sb_link('view-employee.php', 'View Employee', 'View-Employee');
            sb_link('trainee-employee.php', 'Trainee Employee', 'Trainee-Employee');
            sb_link('non-trainee-employee.php', 'Non Trainee Employee', 'Non-Trainee-Employee');
            sb_link('view-inactive-employee.php', 'Inactive Employees', 'View-Employee');
            sb_link('other-employee.php', 'Other Employee', 'View-Other-Employee');
            sb_link('internship-employee.php', 'Internship Employee', 'View-Internship-Employee');
            sb_link('view-cofo-employee.php', 'COFO Employees', 'View-Employee');
        });
    }
    sb_render_if_visible('133', function () {
        sb_link('manpower-report.php', 'Manpower Report', 'Employee-Dashboard');
    });
    sb_render_if_visible('77', function () {
        sb_link('outlet-employee-salary-report.php', 'Outlet Employee Salary Report', '');
    });
    if (sb_has_opt(100)) {
        sb_submenu('Company Policy', function () {
            sb_link('add-company-policy.php', 'Add Policy', 'All-Pending-Expenses');
            sb_link('view-company-policy.php', 'View Policy', 'Manager-Vendor-Peding-Expense-Request');
        });
    }
    if (sb_has_opt(121)) {
        sb_submenu('Laptop Handover', function () {
            sb_link('add-laptop-handover-details.php', 'Add Handover', 'All-Pending-Expenses');
            sb_link('view-laptop-handover.php', 'View Handover', 'Manager-Vendor-Peding-Expense-Request');
        });
    }
}), $hrVisible);

// ─── 4b. KRA / KPI Performance ───────────────────────────────────────────────
if (sb_section_visible('KRA / KPI Performance')) {
    sb_group('KRA / KPI Performance', 'KRA-Master', sb_capture(function () {
        sb_render_if_visible('155', function () {
            sb_link('kra-master.php', 'KRA Master', 'KRA-Master');
        });
        sb_render_if_visible('156', function () {
            sb_link('kpi-master.php', 'KPI Master', 'KPI-Master');
        });
        sb_render_if_visible('162', function () {
            sb_link('emp-hr-kra-requests.php', 'Employee KRA Requests', 'Emp-HR-KRA-Requests');
            sb_link('emp-hr-kra-request-detail.php', 'Employee KRA Request (detail)', 'Emp-HR-KRA-Request-Detail');
        });
    }), true);
}

// ─── 4c. Training Management ─────────────────────────────────────────────────
if (sb_section_visible('Training Management')) {
    sb_group('Training Management', 'Training-Dashboard', sb_capture(function () {
        sb_render_if_visible('135', function () {
            sb_link('training-dashboard.php', 'Training Dashboard', 'Training-Dashboard');
        });
        sb_render_if_visible('136', function () {
            sb_link('add-training.php', 'Add Training', 'Add-Training');
        });
        sb_render_if_visible('137', function () {
            sb_link('view-training.php', 'View Training', 'View-Training');
        });
        sb_render_if_visible('138', function () {
            sb_link('training-types.php', 'Training Type Master', 'Training-Types');
        });
        sb_render_if_visible('139', function () {
            sb_link('training-reports.php', 'Training Reports', 'Training-Reports');
        });
    }), true);
}

// ─── 4d. Resignation & Clearance ─────────────────────────────────────────────
if (sb_section_visible('Resignation & Clearance')) {
    sb_group('Resignation & Clearance', 'Clearance-Dashboard', sb_capture(function () {
        sb_render_if_visible('140', function () {
            sb_link('resignation-clearance-dashboard.php', 'Dashboard', 'Clearance-Dashboard');
        });
        sb_render_if_visible('141', function () {
            sb_link('resignation-clearance-dashboard.php', 'Dashboard', 'Clearance-Dashboard');
        });
        sb_render_if_visible('142', function () {
            sb_link('view-all-resignations.php', 'View All Resignations', 'View-Resignations');
        });
        sb_render_if_visible('143', function () {
            sb_link('view-all-resignations.php?filter=it', 'IT Clearance', 'IT-Clearance');
        });
        sb_render_if_visible('144', function () {
            sb_link('view-all-resignations.php?filter=dept', 'Department Clearance', 'Dept-Clearance');
        });
        sb_render_if_visible('145', function () {
            sb_link('view-all-resignations.php?filter=accounts', 'Accounts Clearance', 'Accounts-Clearance');
        });
        sb_render_if_visible('146', function () {
            sb_link('view-all-resignations.php?filter=hr', 'HR Final Clearance', 'HR-Clearance');
        });
        sb_render_if_visible('147', function () {
            sb_link('view-all-resignations.php?filter=completed', 'Completed Clearances', 'Completed-Clearances');
        });
    }), true);
}

// ─── 5. Expense Management ───────────────────────────────────────────────────
$expVisible = sb_section_visible('Expense Management');
if ($expVisible) {
    sb_group('Expense Management', 'Petty-Expenses', sb_capture(function () {
        global $searchParams, $Roll;
        if ($Roll != 1) {
            sb_render_if_visible('9', function () {
                sb_link('all-employee-expenses.php', 'Employee Expenses', 'All-Pending-Expenses');
            });
            sb_render_if_visible('13', function () {
                sb_link('all-vendor-exepense-request.php', 'Vendor Expenses', 'All-Pending-Pretty-Cash-Request');
            });
            sb_render_if_visible('15', function () {
                sb_link('all-nso-vendor-exepense-request.php', 'NSO Vendor Expenses', 'All-Pending-Pretty-Cash-Request');
            });
            sb_render_if_visible('12', function () {
                sb_link('all-pretty-cash-expenses.php', 'Petty Cash Requests', 'All-Pending-Pretty-Cash-Request');
            });
            sb_render_if_visible('16', function () {
                sb_link('all-attendance-request.php', 'All Attendance Requests', 'All-Pending-Expenses');
            });
        }
        sb_render_if_visible('76', function () {
            sb_link('view-profit-loss.php', 'View Profit & Loss', 'View-Profit-Loss');
        });
        sb_submenu('Expense Reports', function () {
            sb_link('vendor-expense-report.php', 'Vendor Expense Report', 'Daily-Sale-Report');
            sb_link('vendor-expense-item-report.php', 'Vendor Expense Item Report', 'Vendor-Expense-Item-Report');
            sb_link('expenses-report.php', 'Expense Report', 'Expense-Report');
            sb_link('expense-summary-report.php', 'Expense Summary Report', 'Expense-Summary-Report');
        });
    }), true);
}

// ─── Expense approval menus (separate top-level items) ───────────────────────
include 'admin-sidebar-menu-expense-approvals.php';

// ─── All Expenses — view all request types (all / pending / approved / rejected) ─
include 'admin-sidebar-menu-all-expenses.php';

// ─── 6. Finance & Accounts ───────────────────────────────────────────────────
$finVisible = sb_section_visible('Finance & Accounts');
if ($finVisible) {
    sb_group('Finance & Accounts', 'Financial-Management', sb_capture(function () {
        global $searchParams, $today;
        if (sb_has_opt(array(19, 87, 85))) {
        sb_submenu('Cash Book', function () use ($today) {
            sb_render_if_visible('85', function () {
                sb_link('add-cash-book.php', 'Add Cash Book', 'Add-Cash-Book');
            });
            sb_link('view-cash-book.php?FromDate=' . $today . '&ToDate=' . $today, 'View Cash Book', 'View-Cash-Book');
            sb_link('pending-cash-report.php', 'Pending Cash Report', 'Franchise-Outstanding');
            sb_link('fr-bill-outstanding.php', 'Cash & Online Collection', 'Franchise-Outstanding');
            sb_render_if_visible('87', function () {
                sb_link('pending-cash-book-request.php', 'Pending Requests', 'Pending-Cash-Book-Request');
                sb_link('approve-cash-book-request.php', 'Approved Requests', 'Approve-Cash-Book-Request');
                sb_link('reject-cash-book-request.php', 'Rejected Requests', 'Reject-Cash-Book-Request');
            });
        });
    }
    sb_render_if_visible('36', function () {
        sb_link('view-advance-salary.php', 'Advance Salary', 'Advance-Salary');
    });
    sb_render_if_visible('63', function () {
        sb_link('advance-request.php', 'Advance Requests', 'Advance-Request');
    });
    sb_render_if_visible('114', function () {
        sb_link('add-salary-slip.php', 'Salary Slip', 'Amount-Request');
    });
    sb_render_if_visible('75', function () {
        sb_link('wallet.php', 'Wallet', 'Wallet');
    });
    sb_render_if_visible('74', function () {
        sb_link('amount-request.php', 'Withdraw Amount Request', 'Amount-Request');
    });
    if (sb_has_opt(39)) {
        sb_submenu('Bank Details', function () use ($searchParams) {
            sb_link('view-bank-details.php', 'View Bank Details', 'Bank-Details');
            sb_link('bank-detail-excel.php' . $searchParams, 'Generate Bank Detail Excel', 'Bank-Excel');
        });
    }
    if (sb_has_opt(29)) {
        sb_submenu('Pay Advance Amount', function () {
            sb_link('account-pay-advance-payment.php', 'Pending Advance Payment', 'Account-Pending-Advance-Payment');
            sb_link('account-paid-advance-payment.php', 'Paid Advance Payment', 'Account-Paid-Advance-Payment');
        });
    }
    if (sb_has_opt(47)) {
        sb_submenu('Pay Vendor Amount', function () {
            sb_link('view-payable-amount-vendors.php', 'Pending Payment', 'Customer-Notification');
            sb_link('pay-all-exp-amount-to-vendor.php', 'Pay Amount At a Time', 'Customer-Notification');
            sb_link('view-payment-done-amount-vendors.php', 'Payment Done', 'Employee-Notification');
        });
    }
    if (sb_has_opt(48)) {
        sb_submenu('Pay NSO Vendor Amount', function () {
            sb_link('view-payable-amount-nso-vendors.php', 'Pending Payment', 'Customer-Notification');
            sb_link('pay-all-exp-amount-to-nso-vendor.php', 'Pay Amount At a Time', 'Customer-Notification');
            sb_link('view-payment-done-amount-nso-vendors.php', 'Payment Done', 'Employee-Notification');
        });
    }
    }), true);
}

// ─── 8. COFO/FICO ────────────────────────────────────────────────────────────
if (sb_section_visible('COFO / FICO Management')) {
    sb_group('COFO / FICO Management', 'Manager-Vendor-Expenses', sb_capture(function () {
        if (sb_has_opt(117)) {
            sb_submenu('COFO Details', function () {
                sb_link('add-cofo-details.php', 'Add COFO Details', 'All-Pending-Expenses');
                sb_link('view-cofo-details.php', 'View COFO Details', 'Manager-Vendor-Peding-Expense-Request');
            });
        }
        if (sb_has_opt(126)) {
            sb_submenu('COFO Detail Requests', function () {
                sb_link('pending-cofo-details.php', 'Pending', '');
                sb_link('approved-cofo-details.php', 'Approved', '');
                sb_link('reject-cofo-details.php', 'Rejected', '');
            });
        }
        if (sb_has_opt(132)) {
            sb_submenu('Payout Reports', function () {
                sb_link('cofo-payout-report.php', 'COFO Payout Report', 'All-Pending-Expenses');
                sb_link('fico-payout-report.php', 'FICO Payout Report', 'Manager-Vendor-Peding-Expense-Request');
            });
        }
    }), true);
}

// ─── 9. Franchise & Business Partner ─────────────────────────────────────────
if (sb_section_visible('Franchise & Business Partner')) {
    sb_group('Franchise & Business Partner', 'Customers', sb_capture(function () {
        if (sb_has_opt(array(52, 53, 54, 55))) {
            sb_submenu('Franchise', function () {
                sb_render_if_visible('52', function () {
                    sb_link('main-zones.php', 'Zone', 'Zone');
                    sb_link('zones.php', 'Region', 'Zone');
                });
                sb_render_if_visible('53', function () {
                    sb_link('sub-zones.php', 'Sub Zone', 'Sub-Zone');
                });
                sb_render_if_visible('54', function () {
                    sb_link('view-assign-franchise-to-zone.php', 'Assign Franchise To Zone', 'Assign-Franchise-Zone');
                });
                sb_render_if_visible('55', function () {
                    sb_link('add-customer.php', 'Add Franchise', 'Add-Customers');
                    sb_link('view-customers.php', 'View Franchise', 'View-Customers');
                });
            });
        }
        sb_render_if_visible('61', function () {
            sb_submenu('Franchise Query', function () {
                sb_render_if_visible('14', function () {
                    sb_link('add-customer-query.php', 'Add Franchise Query', 'Add-Customer-Query');
                });
                sb_link('view-customer-query.php', 'View Franchise Query', 'View-Customer-Query');
            });
        });
        sb_render_if_visible('58', function () {
            sb_submenu('Business Partner', function () {
                sb_render_if_visible('14', function () {
                    sb_link('add-freelancer-2.php', 'Add Business Partner', 'Add-Freelancer');
                });
                sb_link('view-freelancer-2.php', 'View My Partner', 'View-Freelancer');
            });
        });
        sb_render_if_visible('59', function () {
            sb_submenu('Freelancer/Business Partner', function () {
                sb_render_if_visible('14', function () {
                    sb_link('add-freelancer.php', 'Add Business Partner', 'Add-Freelancer');
                });
                sb_link('view-freelancer.php', 'View Business Partner', 'View-Freelancer');
            });
        });
        sb_render_if_visible('57', function () {
            sb_submenu('Vendors', function () {
                sb_render_if_visible('14', function () {
                    sb_link('add-vendor.php', 'Add Vendor', 'Add-Vendors');
                });
                sb_link('view-vendors.php', 'View Vendors', 'View-Vendors');
            });
        });
    }), true);
}

// ─── 10. Customer Service ────────────────────────────────────────────────────
if (sb_section_visible('Customer Service')) {
    sb_group('Customer Service', 'Customer-Service', sb_capture(function () {
        sb_render_if_visible('37', function () {
            sb_link('customer-feedback-report.php', 'Customer Feedback', 'Customer-Feedback-Report');
        });
        sb_render_if_visible('127', function () {
            sb_link('view-chai-pass.php', 'Chai Pass', 'Chai-Pass');
        });
        sb_render_if_visible('128', function () {
            sb_link('view-chai-pass.php', 'Chai Pass', 'Chai-Pass');
        });
    }), true);
}

// ─── 11. Task & Ticket ───────────────────────────────────────────────────────
if (sb_section_visible('Task & Ticket')) {
    sb_group('Task & Ticket', 'Task', sb_capture(function () {
        sb_submenu('Task', function () {
            sb_link('add-task2.php', 'Add Task', 'Add-Task');
            sb_link('view-task2.php', 'View Task', 'View-Task');
            sb_link('task-rating-report.php', 'Task Rating', 'View-Task');
        });
        sb_link('ticket_management/ticket-dashboard.php', 'Ticket Management', 'Ticket-Management');
    }), true);
}

// ─── 12. Asset Management ────────────────────────────────────────────────────
if (sb_section_visible('Asset Management')) {
    sb_group('Asset Management', 'Asset', sb_capture(function () {
        sb_render_if_visible('50', function () {
            sb_link('add-asset.php', 'Add Asset', 'Add-Asset');
            sb_link('view-assets.php', 'View Assets', 'View-Asset');
        });
    }), true);
}

if (sb_section_visible('SIM Card Management')) {
    sb_group('SIM Card Management', 'SIM-Cards', sb_capture(function () {
        sb_render_if_visible('169', function () {
            sb_link('admin/sim-cards.php', 'All SIM Cards', 'SIM-Cards');
        });
        sb_render_if_visible('170', function () {
            sb_link('admin/sim-card-add.php', 'Add SIM Card', 'SIM-Card-Add');
        });
        sb_render_if_visible('171', function () {
            sb_link('admin/sim-card-import.php', 'Import Excel', 'SIM-Card-Import');
        });
        sb_render_if_visible('172', function () {
            sb_link('admin/sim-card-history.php', 'SIM History', 'SIM-Card-History');
        });
    }), true);
}

// ─── 14. Idea Box ────────────────────────────────────────────────────────────
if (sb_section_visible('Idea Box')) {
    sb_group('Idea Box', 'Idea-Box', sb_capture(function () {
        sb_render_if_visible('160', function () {
            sb_link('idea-box-admin-requests.php', 'Admin Ideas', 'Idea-Box-Admin');
        });
        sb_render_if_visible('161', function () {
            sb_link('idea-box-full-report.php', 'Full Report', 'Idea-Box-Full-Report');
        });
    }), true);
}

// ─── 15. Documents & Media ───────────────────────────────────────────────────
if (sb_section_visible('Documents & Media')) {
    sb_group('Documents & Media', 'Documents', sb_capture(function () {
        sb_link('attach-documents.php', 'Attach Documents', 'Add-Documents');
        sb_link('view-attach-documents.php', 'View Documents', 'View-Documents');
        sb_link('view-upload-pdfs.php', 'Download Center', 'View-Upload-Pdf');
        sb_link('video-gallery.php', 'MahaTube', 'Video-Gallery');
        sb_link('image-gallery.php', 'Image Gallery', 'Image-Gallery');
        sb_link('youtube-videos.php', 'Recipe SOP', 'YouTube-Videos-List');
    }), true);
}

// ─── 16. E-Commerce ──────────────────────────────────────────────────────────
if (sb_section_visible('E-Commerce')) {
    sb_group('E-Commerce', 'E-Commerce', sb_capture(function () {
        sb_link('payment-method.php', 'Payment Method', 'PaymentMethod');
        sb_link('cancel-reason.php', 'Cancel Reason', 'Cancel-Reason');
        sb_link('coupon-code.php', 'Referral/Coupon/Offer Code', 'Coupon-Code');
        sb_link('today-orders.php', 'Today\'s Orders', 'Today-Orders');
        sb_link('view-orders.php', 'View Orders', 'View-Orders');
        sb_link('add-shop-product.php', 'Add Product', 'Add-Product');
        sb_link('view-shop-products.php', 'View Products', 'View-Product');
        sb_link('shop-category.php', 'Category', 'Category');
        sb_link('shop-sub-category.php', 'Sub Category', 'Sub-Category');
        sb_link('attribute-value.php', 'Product Attributes', 'Attributes');
        sb_link('shipping-price.php', 'Shipping Price', 'Shipping-Price');
        sb_link('home-sliders.php', 'Home Sliders', 'Home Slider');
        sb_link('home-banners.php', 'Home Banners', 'Home Banner');
        sb_link('faqs.php', 'Faq\'s', 'Faq');
    }), true);
}

// ─── 17. Reports ─────────────────────────────────────────────────────────────
if (sb_section_visible('Reports')) {
    sb_group('Reports', 'Report', sb_capture(function () use ($today, $searchParams) {
        if (sb_has_opt(array(21, 22, 23, 24, 25, 105))) {
            sb_submenu('Sales Reports', function () use ($today) {
                sb_render_if_visible('21', function () {
                    sb_link('daily-sale-report.php', 'Daily Sale Report', 'Daily-Sale-Report');
                });
                sb_render_if_visible('22', function () {
                    sb_link('daily-sale-report-2.php', 'Daily Sale Report', 'Daily-Sale-Report-2');
                });
                sb_render_if_visible('23', function () {
                    sb_link('weekly-sale-report.php', 'Weekly Sale report', 'Weekly-Sale-Report');
                });
                sb_render_if_visible('24', function () {
                    sb_link('weekly-sale-report-2.php', 'Weekly Sale Report', 'Weekly-Sale-Report-2');
                });
                sb_render_if_visible('25', function () {
                    sb_link('item-wise-sale-report.php', 'Item Wise Sale Report', 'Item-Wise-Sale-Report');
                    sb_link('petty-cash-inventory-report.php', 'Petty Cash Inventory Report', 'Petty-Cash-Inventory-Report');
                });
                sb_render_if_visible('105', function () {
                    sb_link('daily-wise-sale-report.php?FromDate=' . $today . '&ToDate=' . $today, 'Daily Wise Sale Report', 'Employee-Wallet-Outstanding-2');
                });
            });
        }
        if (sb_has_opt(array(26, 27))) {
            sb_submenu('Expense Reports', function () {
                sb_render_if_visible('26', function () {
                    sb_link('expenses-report.php', 'Expense Report', 'Expense-Report');
                });
                sb_render_if_visible('27', function () {
                    sb_link('expense-summary-report.php', 'Expense Summary Report', 'Expense-Summary-Report');
                });
                sb_link('vendor-expense-report.php', 'Vendor Expense Report', 'Daily-Sale-Report');
                sb_render_if_visible('24', function () {
                    sb_link('vendor-expense-item-report.php', 'Vendor Expense Item Report', 'Vendor-Expense-Item-Report');
                });
            });
        }
        if (sb_has_opt(array(81, 98, 151, 163))) {
            sb_submenu('Attendance Reports', function () {
                sb_render_if_visible('81', function () {
                    sb_link('attendance-report-new.php', 'Employee Attendace Report', 'Attendance-Report');
                    sb_link('attendance-report-month-wise.php', 'Employee Attendace Report Month Wise', 'Attendance-Report');
                });
                sb_render_if_visible('98', function () {
                    sb_link('attendance-report-percentage.php', 'Employee Attendace Report Percentage Wise', 'Attendance-Report');
                });
                sb_render_if_visible('151', function () {
                    sb_link('attendance-analysis-reports.php', 'Employee Attendance Analysis Reports', 'Attendance-Report');
                });
                sb_render_if_visible('163', function () {
                    sb_link('employee-attendance-timing-report.php', 'Employee Attendance Timing Report', 'Employee-Wallet-Outstanding-2');
                    sb_link('employee-absent-report.php', 'Employee Absent Report', 'Absent-Attendance-Report');
                    sb_link('late-commerce-report.php', 'Late Commerce Report', 'Late-Commerce-Report');
                });
                sb_link('employee-location-tracking-report.php', 'Employee Location Tracking', 'Employee-Location-Tracking-Report');
                sb_link('emp-location-report.php', 'Employee Location Report', 'Employee-Location-Report');
            });
        }
        if (sb_has_opt(array(77, 80, 96, 97, 120, 152))) {
            sb_submenu('HR Reports', function () {
                sb_render_if_visible('77', function () {
                    sb_link('franchise-daily-survey-report.php', 'Franchise Daily Checklist Report', 'Franchise-Daily-Survey-Report');
                });
                sb_render_if_visible('80', function () {
                    sb_link('employee-daily-report.php', 'Employee Task Report', 'Employee-Daily-Report');
                });
                sb_render_if_visible('96', function () {
                    sb_link('daily-mis-joining.php', 'Daily MIS of Joining', 'Employee-Wallet-Outstanding-2');
                });
                sb_render_if_visible('97', function () {
                    sb_link('daily-mis-attrition.php', 'Daily MIS of Attrition (Exit)', 'Employee-Wallet-Outstanding-2');
                });
                sb_render_if_visible('120', function () {
                    sb_link('generate-salary-sheet.php', 'Generate Salary Sheet', 'Employee-Wallet-Outstanding-2');
                    sb_link('pending-leave-report.php', 'Leave Balance Report', 'Pending-Leave-Report');
                });
                sb_render_if_visible('152', function () {
                    sb_link('exit-interview-report.php', 'Exit Interview Report', 'Exit-Interview-Report');
                });
            });
        }
        if (sb_has_opt(array(79, 131))) {
            sb_submenu('Franchise Reports', function () use ($today) {
                sb_render_if_visible('79', function () {
                    sb_link('franchise-query-report.php', 'Franchise Query report', 'Franchise-Query-Report');
                });
                sb_render_if_visible('131', function () {
                    sb_link('franchise-time-gap-report.php?FromDate=' . $today . '&ToDate=' . $today, 'Franchsie Time Gap Report', 'Employee-Wallet-Outstanding-2');
                });
            });
        }
        sb_render_if_visible('104', function () {
            sb_link('stock-available-report.php', 'Stock Available Report', 'Employee-Wallet-Outstanding-2');
        });
        sb_render_if_visible('102', function () {
            sb_link('cash-handover-report.php', 'Cash Handover Report', 'Employee-Wallet-Outstanding-2');
        });
        sb_render_if_visible('106', function () {
            sb_link('shop-open-close-report.php?FromDate=' . $today . '&ToDate=' . $today, 'Shop Sessions Report', 'Employee-Wallet-Outstanding-2');
        });
        sb_render_if_visible('80', function () {
            sb_link('employee-daily-report.php', 'Employee Task Report', 'Employee-Daily-Report');
        });
        if (sb_has_opt(array(81, 82, 83, 84, 96, 97))) {
            sb_submenu('Wallet Reports', function () {
                sb_render_if_visible('81', function () {
                    sb_link('attendance-report-new.php', 'Employee Attendace Report', 'Attendance-Report');
                });
                sb_render_if_visible('82', function () {
                    sb_link('employee-wallet-report.php', 'Employee Wallet Report', 'Employee-Wallet-Report');
                });
                sb_render_if_visible('83', function () {
                    sb_link('employee-wallet-outstanding.php', 'Employee Wallet Outstanding', 'Employee-Wallet-Outstanding');
                });
                sb_render_if_visible('84', function () {
                    sb_link('employee-wallet-outstanding-2.php', 'Employee Wallet Outstanding 2', 'Employee-Wallet-Outstanding-2');
                });
                sb_render_if_visible('96', function () {
                    sb_link('daily-mis-joining.php', 'Daily MIS of Joining', 'Employee-Wallet-Outstanding-2');
                });
                sb_render_if_visible('97', function () {
                    sb_link('daily-mis-attrition.php', 'Daily MIS of Attrition (Exit)', 'Employee-Wallet-Outstanding-2');
                });
            });
        }
        sb_render_if_visible('148', function () {
            sb_link('outlet-audit-checklist-report.php', 'Outlet Audit Checklist Report', 'Outlet-Audit-Checklist-Report');
        });
        sb_render_if_visible('78', function () {
            sb_submenu('Account Reports', function () {
                sb_link('wallet-balance-report.php', 'Wallet Balance Report', 'Wallet-Balance-Report');
                sb_link('transaction-report.php', 'Transaction Report', 'Transaction-Report');
                sb_link('expense-category-wise-report.php', 'Expense Category Wise Report', 'Transaction-Report');
            });
        });
        sb_link('fr-bill-outstanding.php', 'Cash & Online Collection Report', 'Franchise-Outstanding');
    }), true);
}

// ─── 18. Masters / Settings ──────────────────────────────────────────────────
if (sb_section_visible('Masters / Settings')) {
    sb_group('Masters / Settings', 'Masters', sb_capture(function () {
        sb_render_if_visible('49', function () {
            sb_submenu('Locations', function () {
                sb_link('country.php', 'Country', function () {
                    global $Page2;
                    return isset($Page2) && $Page2 == 'Country';
                });
                sb_link('state.php', 'State', function () {
                    global $Page2;
                    return isset($Page2) && $Page2 == 'State';
                });
                sb_link('city.php', 'City', function () {
                    global $Page2;
                    return isset($Page2) && $Page2 == 'City';
                });
            });
        });
        sb_render_if_visible('51', function () {
            sb_link('popup-image.php', 'Popup Image', 'Outlet-Audit-Questions');
            sb_link('common-master.php?pageid=1', 'Type Of Vendor', 'TypeOfVendor');
            sb_link('user-type.php', 'Designation', 'UserType');
            sb_link('departments.php', 'Departments', 'Departments');
            sb_link('franchaise-location.php', 'Franchise Locations', 'Franchise-Location');
            sb_link('common-master.php?pageid=2', 'Model Type', 'TypeOfVendor');
            sb_link('cashback-amount.php', 'Cashback Amount', 'Cashback-Amount');
            sb_link('sale-price-range.php', 'Shopping Cashback Price Range', 'Sell-Price-Range');
            sb_link('add-money-price-range.php', 'Add Money Cashback Price Range', 'Money-Price-Range');
        });
        sb_render_if_visible('60', function () {
            sb_link('outlet-audit-category.php', 'Outlet Audit Category', 'Outlet-Audit-Category');
        });
        sb_render_if_visible('62', function () {
            sb_link('outlet-audit-questions.php', 'Outlet Audit Questions', 'Outlet-Audit-Questions');
        });
    }), true);
}

// ─── 19. Account ─────────────────────────────────────────────────────────────
$accountTitle = (isset($row77['Fname']) ? $row77['Fname'] : '') . ' ' . (isset($row77['Lname']) ? $row77['Lname'] : '');
sb_group(trim($accountTitle), 'Account', sb_capture(function () {
    global $Roll, $user_id;
    if ($Roll == 1) {
        sb_link('company-information.php', 'Company Profile', 'Company-Profile');
    }
    sb_link('change-password.php', 'Change Password', 'Change-Password');
    if ($user_id == 2651 || $user_id == 2650) {
        sb_link('delete-vendor-exepense-request.php', 'Delete Vendor Expenses', 'Weekly-Sale-Report-2');
        sb_link('delete-nso-vendor-exepense-request.php', 'Delete NSO Vendor Expenses', 'Weekly-Sale-Report-2');
        sb_link('update-vendor-expenses.php', 'Upload Vendor Expense PDF', 'Weekly-Sale-Report-2');
        sb_link('upload-emp-expense-files.php', 'Upload Employee Expense Documents', 'Weekly-Sale-Report-2');
    }
    sb_link('logout.php', 'Log Out', null);
}), true, null, 'my-account');
