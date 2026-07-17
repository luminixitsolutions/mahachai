<?php
/**
 * Centralized approval / rejection email notifications.
 * Call after a successful DB commit only. Failures never block approvals.
 *
 * Usage:
 *   require_once __DIR__ . '/includes/approval_mail_service.php';
 *   approval_mail_notify($conn, 'employee_expense', $id, 'manager', $status, $user_id, $remarks);
 */
date_default_timezone_set('Asia/Kolkata');

require_once __DIR__ . '/approval_mail_config.php';

if (!function_exists('approval_mail_ensure_log_table')) {
    function approval_mail_ensure_log_table($conn)
    {
        static $ready = null;
        if ($ready === true) {
            return true;
        }
        $check = @$conn->query("SHOW TABLES LIKE 'tbl_approval_mail_log'");
        if ($check && $check->num_rows > 0) {
            $ready = true;
            return true;
        }
        $sqlFile = dirname(__DIR__) . '/migrations/approval_mail_notifications.sql';
        if (is_file($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            if ($sql && @$conn->multi_query($sql)) {
                do {
                    if ($result = @$conn->store_result()) {
                        $result->free();
                    }
                } while (@$conn->more_results() && @$conn->next_result());
                if (!$conn->errno) {
                    $ready = true;
                    return true;
                }
            }
        }
        $ready = false;
        return false;
    }
}

if (!function_exists('approval_mail_h')) {
    function approval_mail_h($s)
    {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('approval_mail_esc')) {
    function approval_mail_esc($conn, $s)
    {
        return $conn->real_escape_string((string) $s);
    }
}

if (!function_exists('approval_desktop_ensure_table')) {
    function approval_desktop_ensure_table($conn)
    {
        static $ready = null;
        if ($ready !== null) {
            return $ready;
        }
        $sql = "CREATE TABLE IF NOT EXISTS `tbl_approval_desktop_notifications` (
          `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
          `user_id` int UNSIGNED NOT NULL,
          `module` varchar(64) NOT NULL,
          `request_id` int UNSIGNED NOT NULL,
          `stage` varchar(64) NOT NULL DEFAULT '',
          `decision` varchar(32) NOT NULL DEFAULT '',
          `title` varchar(255) NOT NULL,
          `message` text NOT NULL,
          `view_url` varchar(1000) NOT NULL DEFAULT '',
          `dedupe_key` varchar(64) NOT NULL,
          `delivered_at` datetime DEFAULT NULL,
          `read_at` datetime DEFAULT NULL,
          `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `uk_approval_desktop_dedupe` (`dedupe_key`),
          KEY `idx_adn_user_delivery` (`user_id`, `delivered_at`),
          KEY `idx_adn_module_request` (`module`, `request_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $ready = (bool) @$conn->query($sql);
        return $ready;
    }
}

if (!function_exists('approval_desktop_queue')) {
    function approval_desktop_queue($conn, array $ctx, $stage, $decision, $actorName, $remarks, $dedupeKey)
    {
        $userId = (int) ($ctx['requester_user_id'] ?? 0);
        if ($userId < 1 || !approval_desktop_ensure_table($conn)) {
            return false;
        }

        $decisionLabel = approval_mail_decision_label($decision);
        $role = approval_mail_role_label($stage);
        $title = $ctx['module_label'] . ' ' . $decisionLabel;
        $message = $ctx['request_no'] . ' was ' . strtolower($decisionLabel)
            . ' by ' . $role . ' (' . $actorName . ').';
        if ($remarks !== '') {
            $message .= ' Remarks: ' . $remarks . '.';
        }
        if (!empty($ctx['next_level']) && empty($ctx['is_final'])) {
            $message .= ' ' . $ctx['next_level'] . '.';
        } elseif (!empty($ctx['is_final'])) {
            $message .= approval_mail_is_reject($decision)
                ? ' The approval process is closed.'
                : ' This request is fully approved.';
        }

        $sql = "INSERT IGNORE INTO tbl_approval_desktop_notifications SET
            user_id='$userId',
            module='" . approval_mail_esc($conn, $ctx['module']) . "',
            request_id='" . (int) $ctx['request_id'] . "',
            stage='" . approval_mail_esc($conn, $stage) . "',
            decision='" . approval_mail_esc($conn, $decisionLabel) . "',
            title='" . approval_mail_esc($conn, $title) . "',
            message='" . approval_mail_esc($conn, $message) . "',
            view_url='" . approval_mail_esc($conn, $ctx['view_url'] ?? '') . "',
            dedupe_key='" . approval_mail_esc($conn, 'desktop-' . $dedupeKey) . "',
            created_at='" . approval_mail_esc($conn, date('Y-m-d H:i:s')) . "'";
        return (bool) @$conn->query($sql);
    }
}

if (!function_exists('approval_mail_user')) {
    function approval_mail_user($conn, $userId)
    {
        $userId = (int) $userId;
        if ($userId < 1) {
            return null;
        }
        return getRecord("SELECT id, Fname, Lname, EmailId, EmailId2, Phone, UnderByUser, Roll
            FROM tbl_users WHERE id='" . approval_mail_esc($conn, $userId) . "' LIMIT 1");
    }
}

if (!function_exists('approval_mail_user_name')) {
    function approval_mail_user_name($row)
    {
        if (!$row || !is_array($row)) {
            return '';
        }
        return trim(($row['Fname'] ?? '') . ' ' . ($row['Lname'] ?? ''));
    }
}

if (!function_exists('approval_mail_user_email')) {
    function approval_mail_user_email($row)
    {
        if (!$row || !is_array($row)) {
            return '';
        }
        $email = trim((string) ($row['EmailId'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = trim((string) ($row['EmailId2'] ?? ''));
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return '';
        }
        return $email;
    }
}

if (!function_exists('approval_mail_role_label')) {
    function approval_mail_role_label($stage)
    {
        $map = array(
            'manager' => 'Manager',
            'hr' => 'HR',
            'bh' => 'Business Head',
            'business_head' => 'Business Head',
            'admin' => 'Admin',
            'account' => 'Accounts',
            'accountant' => 'Accounts',
            'bdm' => 'BDM',
            'purchase' => 'Purchase',
            'nso' => 'NSO',
            'nso_first' => 'NSO (First)',
            'hierarchy' => 'Reporting Manager',
            'it' => 'IT',
            'department' => 'Department',
        );
        $k = strtolower(trim((string) $stage));
        return $map[$k] ?? ucwords(str_replace('_', ' ', $k));
    }
}

if (!function_exists('approval_mail_decision_label')) {
    function approval_mail_decision_label($decision)
    {
        $d = (string) $decision;
        if ($d === '1' || strcasecmp($d, 'approved') === 0 || strcasecmp($d, 'approve') === 0) {
            return 'Approved';
        }
        if ($d === '2' || strcasecmp($d, 'rejected') === 0 || strcasecmp($d, 'reject') === 0) {
            return 'Rejected';
        }
        if ($d === '3' || strcasecmp($d, 'partial') === 0) {
            return 'Partially Approved';
        }
        return 'Updated';
    }
}

if (!function_exists('approval_mail_is_reject')) {
    function approval_mail_is_reject($decision)
    {
        $d = (string) $decision;
        return $d === '2' || strcasecmp($d, 'rejected') === 0 || strcasecmp($d, 'reject') === 0;
    }
}

if (!function_exists('approval_mail_is_approve')) {
    function approval_mail_is_approve($decision)
    {
        $d = (string) $decision;
        return $d === '1' || $d === '3'
            || strcasecmp($d, 'approved') === 0
            || strcasecmp($d, 'approve') === 0
            || strcasecmp($d, 'partial') === 0;
    }
}

if (!function_exists('approval_mail_status_int')) {
    function approval_mail_status_int($v)
    {
        if ($v === null || $v === '') {
            return 0;
        }
        return (int) $v;
    }
}

if (!function_exists('approval_mail_base_url')) {
    function approval_mail_base_url()
    {
        if (function_exists('maha_site_base_url')) {
            return rtrim(maha_site_base_url(), '/') . '/';
        }
        global $SiteUrl;
        return rtrim((string) ($SiteUrl ?? 'https://kwickfoods.in/control_panel_maha/'), '/') . '/';
    }
}

if (!function_exists('approval_mail_format_amount')) {
    function approval_mail_format_amount($amount)
    {
        if ($amount === null || $amount === '') {
            return '—';
        }
        return '₹ ' . number_format((float) $amount, 2);
    }
}

if (!function_exists('approval_mail_format_date')) {
    function approval_mail_format_date($d)
    {
        if ($d === null || $d === '' || $d === '0000-00-00' || $d === '0000-00-00 00:00:00') {
            return '—';
        }
        $ts = strtotime((string) $d);
        if ($ts === false) {
            return (string) $d;
        }
        return date('d M Y', $ts);
    }
}

/**
 * Module metadata + request loader + next-level resolver.
 */
if (!function_exists('approval_mail_module_defs')) {
    function approval_mail_module_defs()
    {
        return array(
            'employee_expense' => array(
                'label' => 'Employee Expense',
                'table' => 'tbl_expense_request',
                'view' => 'all-pending-expenses.php',
                'id_param' => 'id',
            ),
            'petty_cash' => array(
                'label' => 'Petty Cash Expense',
                'table' => 'tbl_prettycash_request',
                'view' => 'all-pending-pretty-cash-request.php',
                'id_param' => 'id',
            ),
            'petty_limit' => array(
                'label' => 'Petty Cash Limit',
                'table' => 'tbl_petty_limit_request',
                'view' => 'all-pending-petty-limit-request.php',
                'id_param' => 'id',
            ),
            'vendor_expense' => array(
                'label' => 'Vendor Expense',
                'table' => 'tbl_vendor_expenses',
                'view' => 'all-pending-vendor-exepense-request.php',
                'id_param' => 'id',
            ),
            'nso_vendor_expense' => array(
                'label' => 'NSO Vendor Expense',
                'table' => 'tbl_nso_vendor_expenses',
                'view' => 'all-pending-nso-vendor-exepense-request.php',
                'id_param' => 'id',
            ),
            'resign' => array(
                'label' => 'Resignation',
                'table' => 'tbl_resign_request',
                'view' => 'manager-pending-resign-request.php',
                'id_param' => 'id',
            ),
            'advance_salary' => array(
                'label' => 'Salary Advance',
                'table' => 'tbl_advance_salary',
                'view' => 'manager-pending-advance-request.php',
                'id_param' => 'id',
            ),
            'advance_request' => array(
                'label' => 'Advance Payment',
                'table' => 'tbl_advance_request',
                'view' => 'account-pending-advance-request.php',
                'id_param' => 'id',
            ),
            'leave' => array(
                'label' => 'Leave Request',
                'table' => 'tbl_leave_request',
                'view' => 'manager-pending-leave-request.php',
                'id_param' => 'id',
            ),
            'attendance' => array(
                'label' => 'Attendance Request',
                'table' => 'tbl_attendance_request',
                'view' => 'manager-pending-attendance-request.php',
                'id_param' => 'id',
            ),
            'cash_book' => array(
                'label' => 'Cash Book',
                'table' => 'tbl_cash_book',
                'view' => 'pending-cash-book-request.php',
                'id_param' => 'id',
            ),
            'vendor_invoice' => array(
                'label' => 'Vendor Invoice',
                'table' => 'tbl_vendor_expense_invoices',
                'view' => 'admin-vendor-pending-invoice-request.php',
                'id_param' => 'id',
            ),
            'resignation_clearance' => array(
                'label' => 'Resignation Clearance',
                'table' => 'tbl_resignation_clearance',
                'view' => 'resignation-clearance.php',
                'id_param' => 'id',
            ),
        );
    }
}

if (!function_exists('approval_mail_load_context')) {
    /**
     * @return array|null Context with keys: module_label, request_no, requester_*, amount, details[], next_level, current_status, view_url, is_final
     */
    function approval_mail_load_context($conn, $module, $requestId, $stage, $decision)
    {
        $defs = approval_mail_module_defs();
        if (!isset($defs[$module])) {
            return null;
        }
        $def = $defs[$module];
        $requestId = (int) $requestId;
        $table = $def['table'];
        $row = getRecord("SELECT * FROM `$table` WHERE id='" . approval_mail_esc($conn, $requestId) . "' LIMIT 1");
        if (!$row) {
            return null;
        }

        $ctx = array(
            'module' => $module,
            'module_label' => $def['label'],
            'request_id' => $requestId,
            'request_no' => '#' . $requestId,
            'stage' => $stage,
            'decision' => $decision,
            'amount' => null,
            'details' => array(),
            'next_level' => '',
            'current_status' => '',
            'is_final' => false,
            'view_url' => approval_mail_base_url() . $def['view'] . '?' . $def['id_param'] . '=' . $requestId,
            'requester_user_id' => 0,
            'requester_name' => '',
            'requester_email' => '',
        );

        switch ($module) {
            case 'employee_expense':
                $ctx = approval_mail_ctx_employee_expense($conn, $ctx, $row, $stage, $decision);
                break;
            case 'petty_cash':
                $ctx = approval_mail_ctx_petty_cash($conn, $ctx, $row, $stage, $decision);
                break;
            case 'petty_limit':
                $ctx = approval_mail_ctx_petty_limit($conn, $ctx, $row, $stage, $decision);
                break;
            case 'vendor_expense':
                $ctx = approval_mail_ctx_vendor_expense($conn, $ctx, $row, $stage, $decision);
                break;
            case 'nso_vendor_expense':
                $ctx = approval_mail_ctx_nso_vendor($conn, $ctx, $row, $stage, $decision);
                break;
            case 'resign':
                $ctx = approval_mail_ctx_resign($conn, $ctx, $row, $stage, $decision);
                break;
            case 'advance_salary':
                $ctx = approval_mail_ctx_advance_salary($conn, $ctx, $row, $stage, $decision);
                break;
            case 'advance_request':
                $ctx = approval_mail_ctx_advance_request($conn, $ctx, $row, $stage, $decision);
                break;
            case 'leave':
                $ctx = approval_mail_ctx_leave($conn, $ctx, $row, $stage, $decision);
                break;
            case 'attendance':
                $ctx = approval_mail_ctx_attendance($conn, $ctx, $row, $stage, $decision);
                break;
            case 'cash_book':
                $ctx = approval_mail_ctx_generic_user($conn, $ctx, $row, $stage, $decision, array(
                    'user_field' => 'UserId',
                    'amount_fields' => array('Amount', 'TotalAmount'),
                    'date_fields' => array('ReqDate', 'CreatedDate', 'CashDate'),
                    'levels' => array(
                        array('ApproveStatus', 'Admin'),
                    ),
                ));
                break;
            case 'vendor_invoice':
                $ctx = approval_mail_ctx_generic_user($conn, $ctx, $row, $stage, $decision, array(
                    'user_field' => 'UserId',
                    'amount_fields' => array('Amount', 'InvoiceAmount', 'TotalAmount'),
                    'levels' => array(
                        'admin' => array('AdminStatus', 'Admin'),
                        'account' => array('AccStatus', 'Accounts'),
                    ),
                ));
                break;
            case 'resignation_clearance':
                $ctx = approval_mail_ctx_clearance($conn, $ctx, $row, $stage, $decision);
                break;
            default:
                return null;
        }

        return $ctx;
    }
}

if (!function_exists('approval_mail_fill_requester')) {
    function approval_mail_fill_requester($conn, array $ctx, $userId)
    {
        $user = approval_mail_user($conn, $userId);
        $ctx['requester_user_id'] = (int) $userId;
        $ctx['requester_name'] = approval_mail_user_name($user) ?: ('User #' . (int) $userId);
        $ctx['requester_email'] = approval_mail_user_email($user);
        return $ctx;
    }
}

if (!function_exists('approval_mail_next_from_levels')) {
    /**
     * @param array $levels ordered list of [statusKey => label] or [key, status, label]
     */
    function approval_mail_next_from_levels(array $row, array $levels, $decision)
    {
        if (approval_mail_is_reject($decision)) {
            return array('next' => '', 'is_final' => true, 'status' => 'Rejected');
        }
        $pending = '';
        $allDone = true;
        foreach ($levels as $item) {
            $statusKey = $item[0];
            $label = $item[1];
            $st = approval_mail_status_int($row[$statusKey] ?? 0);
            if ($st === 2) {
                return array('next' => '', 'is_final' => true, 'status' => 'Rejected');
            }
            if ($st !== 1 && $st !== 3) {
                $allDone = false;
                if ($pending === '') {
                    $pending = $label;
                }
            }
        }
        if ($allDone) {
            return array('next' => '', 'is_final' => true, 'status' => 'Fully Approved');
        }
        return array(
            'next' => $pending !== '' ? ('Pending approval by ' . $pending) : 'Pending further approval',
            'is_final' => false,
            'status' => $pending !== '' ? ('Pending — ' . $pending) : 'In Progress',
        );
    }
}

if (!function_exists('approval_mail_ctx_employee_expense')) {
    function approval_mail_ctx_employee_expense($conn, array $ctx, array $row, $stage, $decision)
    {
        $ctx = approval_mail_fill_requester($conn, $ctx, (int) ($row['UserId'] ?? 0));
        $amount = $row['MgrAmount'] ?? $row['Amount'] ?? null;
        if ($amount === null || $amount === '') {
            $amount = $row['Amount'] ?? null;
        }
        $ctx['amount'] = $amount;
        $ctx['request_no'] = !empty($row['TicketNo']) ? $row['TicketNo'] : ('EXP-' . $ctx['request_id']);
        $ctx['details'] = array(
            'Narration' => $row['Narration'] ?? '',
            'Request Date' => approval_mail_format_date($row['ReqDate'] ?? $row['CreatedDate'] ?? ''),
            'Expense Amount' => approval_mail_format_amount($row['Amount'] ?? ''),
            'Approved Amount' => approval_mail_format_amount($amount),
        );

        // Dynamic hierarchy
        $hasHierarchy = false;
        if (function_exists('expense_hierarchy_has_levels')) {
            $hasHierarchy = expense_hierarchy_has_levels($conn, $ctx['request_id']);
        } else {
            $chk = @$conn->query("SHOW TABLES LIKE 'tbl_expense_approval_levels'");
            if ($chk && $chk->num_rows > 0) {
                $cnt = getRecord("SELECT COUNT(*) AS c FROM tbl_expense_approval_levels WHERE ExpId='" . (int) $ctx['request_id'] . "'");
                $hasHierarchy = ((int) ($cnt['c'] ?? 0)) > 0;
            }
        }

        if ($hasHierarchy && !approval_mail_is_reject($decision)) {
            $pending = getRecord("SELECT eal.*, tu.Fname, tu.Lname FROM tbl_expense_approval_levels eal
                LEFT JOIN tbl_users tu ON tu.id = eal.ApproverUserId
                WHERE eal.ExpId='" . (int) $ctx['request_id'] . "' AND eal.Status='pending' ORDER BY eal.LevelNo ASC LIMIT 1");
            $waitingHr = approval_mail_status_int($row['HrStatus'] ?? null);
            $expCat = (int) ($row['ExpCatId'] ?? 0);
            if ($expCat === 3 && ($row['HrStatus'] === '0' || $row['HrStatus'] === 0)) {
                $ctx['next_level'] = 'Pending approval by HR';
                $ctx['is_final'] = false;
                $ctx['current_status'] = 'Pending — HR';
            } elseif ($pending) {
                $name = trim(($pending['Fname'] ?? '') . ' ' . ($pending['Lname'] ?? ''));
                $lvl = (int) ($pending['LevelNo'] ?? 0);
                $ctx['next_level'] = 'Pending approval by Level ' . $lvl . ($name !== '' ? (' (' . $name . ')') : '');
                $ctx['is_final'] = false;
                $ctx['current_status'] = $ctx['next_level'];
            } else {
                $admin = approval_mail_status_int($row['AdminStatus'] ?? 0);
                if ($admin !== 1) {
                    $ctx['next_level'] = 'Pending approval by Accounts / Admin';
                    $ctx['is_final'] = false;
                    $ctx['current_status'] = 'Pending — Accounts';
                } else {
                    $ctx['next_level'] = '';
                    $ctx['is_final'] = true;
                    $ctx['current_status'] = 'Fully Approved';
                }
            }
            if (approval_mail_is_reject($decision)) {
                $ctx['next_level'] = '';
                $ctx['is_final'] = true;
                $ctx['current_status'] = 'Rejected';
            }
            return $ctx;
        }

        $levels = array();
        $levels[] = array('ManagerStatus', 'Manager');
        if ((int) ($row['ExpCatId'] ?? 0) === 3 || isset($row['HrStatus'])) {
            if ($row['HrStatus'] !== null && $row['HrStatus'] !== '') {
                $levels[] = array('HrStatus', 'HR');
            }
        }
        if (isset($row['BhStatus']) && $row['BhStatus'] !== null && $row['BhStatus'] !== '') {
            $levels[] = array('BhStatus', 'Business Head');
        } elseif ((float) ($row['Amount'] ?? 0) > 2000 || (float) ($row['MgrAmount'] ?? 0) > 2000) {
            // Above threshold typically needs BH then Accounts
            if (approval_mail_status_int($row['ManagerStatus'] ?? 0) === 1
                && approval_mail_status_int($row['AdminStatus'] ?? 0) !== 1) {
                // keep dynamic below
            }
        }
        $levels[] = array('AdminStatus', 'Accounts / Admin');

        // Refresh row after update
        $fresh = getRecord("SELECT * FROM tbl_expense_request WHERE id='" . (int) $ctx['request_id'] . "' LIMIT 1");
        if ($fresh) {
            $row = $fresh;
        }

        // Rebuild levels from fresh data
        $levels = array(array('ManagerStatus', 'Manager'));
        if ($row['HrStatus'] !== null && $row['HrStatus'] !== '') {
            $levels[] = array('HrStatus', 'HR');
        }
        if ($row['BhStatus'] !== null && $row['BhStatus'] !== '') {
            $levels[] = array('BhStatus', 'Business Head');
        }
        $levels[] = array('AdminStatus', 'Accounts / Admin');

        $info = approval_mail_next_from_levels($row, $levels, $decision);
        $ctx['next_level'] = $info['next'];
        $ctx['is_final'] = $info['is_final'];
        $ctx['current_status'] = $info['status'];
        return $ctx;
    }
}

if (!function_exists('approval_mail_ctx_petty_cash')) {
    function approval_mail_ctx_petty_cash($conn, array $ctx, array $row, $stage, $decision)
    {
        $ctx = approval_mail_fill_requester($conn, $ctx, (int) ($row['UserId'] ?? 0));
        $ctx['amount'] = $row['AccAmount'] ?? $row['AdminAmount'] ?? $row['MannagerAmount'] ?? $row['Amount'] ?? null;
        $ctx['request_no'] = 'PC-' . $ctx['request_id'];
        $ctx['details'] = array(
            'Narration' => $row['Narration'] ?? '',
            'Request Date' => approval_mail_format_date($row['ReqDate'] ?? ''),
            'Requested Amount' => approval_mail_format_amount($row['Amount'] ?? ''),
        );
        $fresh = getRecord("SELECT * FROM tbl_prettycash_request WHERE id='" . (int) $ctx['request_id'] . "' LIMIT 1") ?: $row;
        $info = approval_mail_next_from_levels($fresh, array(
            array('ManagerStatus', 'Manager'),
            array('AdminStatus', 'Admin'),
            array('AccStatus', 'Accounts'),
        ), $decision);
        $ctx['next_level'] = $info['next'];
        $ctx['is_final'] = $info['is_final'];
        $ctx['current_status'] = $info['status'];
        return $ctx;
    }
}

if (!function_exists('approval_mail_ctx_petty_limit')) {
    function approval_mail_ctx_petty_limit($conn, array $ctx, array $row, $stage, $decision)
    {
        $ctx = approval_mail_fill_requester($conn, $ctx, (int) ($row['UserId'] ?? 0));
        $ctx['amount'] = $row['RequestedLimit'] ?? null;
        $ctx['request_no'] = 'PL-' . $ctx['request_id'];
        $ctx['details'] = array(
            'Current Limit' => approval_mail_format_amount($row['CurrentLimit'] ?? ''),
            'Requested Limit' => approval_mail_format_amount($row['RequestedLimit'] ?? ''),
            'Reason' => $row['Narration'] ?? '',
            'Request Date' => approval_mail_format_date($row['RequestDate'] ?? ''),
        );
        $fresh = getRecord("SELECT * FROM tbl_petty_limit_request WHERE id='" . (int) $ctx['request_id'] . "' LIMIT 1") ?: $row;
        $info = approval_mail_next_from_levels($fresh, array(
            array('ManagerStatus', 'Manager'),
            array('AdminStatus', 'Admin'),
            array('AccStatus', 'Accounts'),
        ), $decision);
        $ctx['next_level'] = $info['next'];
        $ctx['is_final'] = $info['is_final'];
        $ctx['current_status'] = $info['status'];
        return $ctx;
    }
}

if (!function_exists('approval_mail_ctx_vendor_expense')) {
    function approval_mail_ctx_vendor_expense($conn, array $ctx, array $row, $stage, $decision)
    {
        $userId = (int) ($row['UserId'] ?? 0);
        if ($userId < 1) {
            $userId = (int) ($row['VedId'] ?? 0);
        }
        $ctx = approval_mail_fill_requester($conn, $ctx, $userId);
        // Prefer vendor email if creator has none
        if ($ctx['requester_email'] === '' && !empty($row['VedId'])) {
            $ved = approval_mail_user($conn, (int) $row['VedId']);
            $ctx['requester_email'] = approval_mail_user_email($ved);
            if ($ctx['requester_name'] === '' || strpos($ctx['requester_name'], 'User #') === 0) {
                $ctx['requester_name'] = approval_mail_user_name($ved) ?: $ctx['requester_name'];
            }
        }
        $ctx['amount'] = $row['BdmAmount'] ?? $row['Amount'] ?? null;
        $ctx['request_no'] = 'VE-' . $ctx['request_id'];
        $ctx['details'] = array(
            'Narration' => $row['Narration'] ?? ($row['Remark'] ?? ''),
            'Request Date' => approval_mail_format_date($row['ReqDate'] ?? $row['CreatedDate'] ?? ''),
            'Amount' => approval_mail_format_amount($row['Amount'] ?? ''),
        );
        $fresh = getRecord("SELECT * FROM tbl_vendor_expenses WHERE id='" . (int) $ctx['request_id'] . "' LIMIT 1") ?: $row;
        $levels = array(array('BdmStatus', 'BDM'));
        // Purchase may be skipped for trusted vendors
        if (isset($fresh['PurchaseStatus']) && $fresh['PurchaseStatus'] !== null && $fresh['PurchaseStatus'] !== '') {
            $levels[] = array('PurchaseStatus', 'Purchase');
        }
        $levels[] = array('ManagerStatus', 'Manager');
        $levels[] = array('AdminStatus', 'Accounts');
        $info = approval_mail_next_from_levels($fresh, $levels, $decision);
        $ctx['next_level'] = $info['next'];
        $ctx['is_final'] = $info['is_final'];
        $ctx['current_status'] = $info['status'];
        return $ctx;
    }
}

if (!function_exists('approval_mail_ctx_nso_vendor')) {
    function approval_mail_ctx_nso_vendor($conn, array $ctx, array $row, $stage, $decision)
    {
        $userId = (int) ($row['UserId'] ?? 0);
        if ($userId < 1) {
            $userId = (int) ($row['VedId'] ?? 0);
        }
        $ctx = approval_mail_fill_requester($conn, $ctx, $userId);
        if ($ctx['requester_email'] === '' && !empty($row['VedId'])) {
            $ved = approval_mail_user($conn, (int) $row['VedId']);
            $ctx['requester_email'] = approval_mail_user_email($ved);
        }
        $ctx['amount'] = $row['Amount'] ?? null;
        $ctx['request_no'] = 'NSO-VE-' . $ctx['request_id'];
        $ctx['details'] = array(
            'Narration' => $row['Narration'] ?? '',
            'Request Date' => approval_mail_format_date($row['ReqDate'] ?? $row['CreatedDate'] ?? ''),
            'Amount' => approval_mail_format_amount($row['Amount'] ?? ''),
        );
        $fresh = getRecord("SELECT * FROM tbl_nso_vendor_expenses WHERE id='" . (int) $ctx['request_id'] . "' LIMIT 1") ?: $row;
        $levels = array();
        if (array_key_exists('NsoStatus', $fresh)) {
            $levels[] = array('NsoStatus', 'NSO (First)');
        }
        $levels[] = array('BdmStatus', 'NSO / BDM');
        if (isset($fresh['PurchaseStatus']) && $fresh['PurchaseStatus'] !== null && $fresh['PurchaseStatus'] !== '') {
            $levels[] = array('PurchaseStatus', 'Purchase');
        }
        $levels[] = array('ManagerStatus', 'Manager');
        $levels[] = array('AdminStatus', 'Accounts');
        $info = approval_mail_next_from_levels($fresh, $levels, $decision);
        $ctx['next_level'] = $info['next'];
        $ctx['is_final'] = $info['is_final'];
        $ctx['current_status'] = $info['status'];
        return $ctx;
    }
}

if (!function_exists('approval_mail_ctx_resign')) {
    function approval_mail_ctx_resign($conn, array $ctx, array $row, $stage, $decision)
    {
        $ctx = approval_mail_fill_requester($conn, $ctx, (int) ($row['UserId'] ?? 0));
        $ctx['request_no'] = 'RES-' . $ctx['request_id'];
        $ctx['details'] = array(
            'Resign Date' => approval_mail_format_date($row['ResignDate'] ?? $row['ReqDate'] ?? ''),
            'Reason' => $row['Narration'] ?? ($row['Reason'] ?? ''),
            'Request Date' => approval_mail_format_date($row['ReqDate'] ?? ''),
        );
        $fresh = getRecord("SELECT * FROM tbl_resign_request WHERE id='" . (int) $ctx['request_id'] . "' LIMIT 1") ?: $row;
        // Current product behaviour: manager approval is final (also sets HrStatus)
        if (approval_mail_is_reject($decision)) {
            $ctx['next_level'] = '';
            $ctx['is_final'] = true;
            $ctx['current_status'] = 'Rejected';
        } elseif (approval_mail_status_int($fresh['HrStatus'] ?? 0) === 1
            || (strtolower((string) $stage) === 'manager' && approval_mail_is_approve($decision))) {
            $ctx['next_level'] = '';
            $ctx['is_final'] = true;
            $ctx['current_status'] = 'Fully Approved';
        } else {
            $info = approval_mail_next_from_levels($fresh, array(
                array('ManagerStatus', 'Manager'),
                array('HrStatus', 'HR'),
            ), $decision);
            $ctx['next_level'] = $info['next'];
            $ctx['is_final'] = $info['is_final'];
            $ctx['current_status'] = $info['status'];
        }
        return $ctx;
    }
}

if (!function_exists('approval_mail_ctx_advance_salary')) {
    function approval_mail_ctx_advance_salary($conn, array $ctx, array $row, $stage, $decision)
    {
        $ctx = approval_mail_fill_requester($conn, $ctx, (int) ($row['UserId'] ?? 0));
        $ctx['amount'] = $row['Amount'] ?? null;
        $ctx['request_no'] = 'ADV-' . $ctx['request_id'];
        $ctx['details'] = array(
            'Amount' => approval_mail_format_amount($row['Amount'] ?? ''),
            'Narration' => $row['Narration'] ?? '',
            'Request Date' => approval_mail_format_date($row['ReqDate'] ?? ''),
        );
        $fresh = getRecord("SELECT * FROM tbl_advance_salary WHERE id='" . (int) $ctx['request_id'] . "' LIMIT 1") ?: $row;
        $info = approval_mail_next_from_levels($fresh, array(
            array('ManagerStatus', 'Manager'),
            array('HrStatus', 'HR'),
        ), $decision);
        $ctx['next_level'] = $info['next'];
        $ctx['is_final'] = $info['is_final'];
        $ctx['current_status'] = $info['status'];
        return $ctx;
    }
}

if (!function_exists('approval_mail_ctx_advance_request')) {
    function approval_mail_ctx_advance_request($conn, array $ctx, array $row, $stage, $decision)
    {
        $ctx = approval_mail_fill_requester($conn, $ctx, (int) ($row['UserId'] ?? 0));
        $ctx['amount'] = $row['Amount'] ?? null;
        $ctx['request_no'] = 'AP-' . $ctx['request_id'];
        $ctx['details'] = array(
            'Amount' => approval_mail_format_amount($row['Amount'] ?? ''),
            'Narration' => $row['Narration'] ?? '',
            'Request Date' => approval_mail_format_date($row['ReqDate'] ?? ''),
        );
        $fresh = getRecord("SELECT * FROM tbl_advance_request WHERE id='" . (int) $ctx['request_id'] . "' LIMIT 1") ?: $row;
        $info = approval_mail_next_from_levels($fresh, array(
            array('AdminStatus', 'Accounts'),
        ), $decision);
        $ctx['next_level'] = $info['next'];
        $ctx['is_final'] = $info['is_final'];
        $ctx['current_status'] = $info['status'];
        return $ctx;
    }
}

if (!function_exists('approval_mail_ctx_leave')) {
    function approval_mail_ctx_leave($conn, array $ctx, array $row, $stage, $decision)
    {
        $ctx = approval_mail_fill_requester($conn, $ctx, (int) ($row['UserId'] ?? 0));
        $ctx['request_no'] = 'LV-' . $ctx['request_id'];
        $ctx['details'] = array(
            'Leave Type' => $row['LeaveType'] ?? '',
            'From Date' => approval_mail_format_date($row['FromDate'] ?? ''),
            'To Date' => approval_mail_format_date($row['ToDate'] ?? ''),
            'Total Days' => $row['TotDays'] ?? '',
            'Reason' => $row['Narration'] ?? ($row['Reason'] ?? ''),
        );
        if (isset($row['ApprovedDays']) && $row['ApprovedDays'] !== '') {
            $ctx['details']['Approved Days'] = $row['ApprovedDays'];
        }
        $fresh = getRecord("SELECT * FROM tbl_leave_request WHERE id='" . (int) $ctx['request_id'] . "' LIMIT 1") ?: $row;
        // Manager may be final in practice; still show HR if HrStatus pending
        $info = approval_mail_next_from_levels($fresh, array(
            array('ManagerStatus', 'Manager'),
            array('HrStatus', 'HR'),
        ), $decision);
        // Partial approval still pending HR if configured
        if ((string) $decision === '3') {
            $info['status'] = 'Partially Approved' . ($info['next'] !== '' ? (' — ' . $info['next']) : '');
        }
        $ctx['next_level'] = $info['next'];
        $ctx['is_final'] = $info['is_final'];
        $ctx['current_status'] = $info['status'];
        return $ctx;
    }
}

if (!function_exists('approval_mail_ctx_attendance')) {
    function approval_mail_ctx_attendance($conn, array $ctx, array $row, $stage, $decision)
    {
        $ctx = approval_mail_fill_requester($conn, $ctx, (int) ($row['UserId'] ?? 0));
        $ctx['request_no'] = 'ATT-' . $ctx['request_id'];
        $ctx['details'] = array(
            'From Date' => approval_mail_format_date($row['FromDate'] ?? ''),
            'From Time' => $row['FromTime'] ?? '',
            'To Date' => approval_mail_format_date($row['ToDate'] ?? ''),
            'To Time' => $row['ToTime'] ?? '',
            'Reason' => $row['Narration'] ?? ($row['Reason'] ?? ''),
        );
        $fresh = getRecord("SELECT * FROM tbl_attendance_request WHERE id='" . (int) $ctx['request_id'] . "' LIMIT 1") ?: $row;
        $levels = array(array('ManagerStatus', 'Manager'));
        if (array_key_exists('BdmStatus', $fresh)) {
            $levels[] = array('BdmStatus', 'BDM');
        }
        if (array_key_exists('HrStatus', $fresh)) {
            $levels[] = array('HrStatus', 'HR');
        }
        $info = approval_mail_next_from_levels($fresh, $levels, $decision);
        // Attendance often applies attendance on first approval; still report remaining levels
        $ctx['next_level'] = $info['next'];
        $ctx['is_final'] = $info['is_final'];
        $ctx['current_status'] = $info['status'];
        return $ctx;
    }
}

if (!function_exists('approval_mail_ctx_generic_user')) {
    function approval_mail_ctx_generic_user($conn, array $ctx, array $row, $stage, $decision, array $opts)
    {
        $userField = $opts['user_field'] ?? 'UserId';
        $ctx = approval_mail_fill_requester($conn, $ctx, (int) ($row[$userField] ?? 0));
        foreach (($opts['amount_fields'] ?? array()) as $af) {
            if (isset($row[$af]) && $row[$af] !== '') {
                $ctx['amount'] = $row[$af];
                break;
            }
        }
        $details = array();
        foreach (($opts['date_fields'] ?? array()) as $df) {
            if (!empty($row[$df])) {
                $details[ucwords(str_replace('_', ' ', $df))] = approval_mail_format_date($row[$df]);
            }
        }
        if ($ctx['amount'] !== null) {
            $details['Amount'] = approval_mail_format_amount($ctx['amount']);
        }
        if (!empty($row['Narration'])) {
            $details['Narration'] = $row['Narration'];
        }
        $ctx['details'] = $details;
        $levels = array();
        foreach (($opts['levels'] ?? array()) as $stKey => $label) {
            if (is_array($label)) {
                $levels[] = array($label[0], $label[1]);
            } else {
                $levels[] = array($stKey, $label);
            }
        }
        if (empty($levels)) {
            $levels[] = array('AdminStatus', 'Admin');
        }
        $info = approval_mail_next_from_levels($row, $levels, $decision);
        $ctx['next_level'] = $info['next'];
        $ctx['is_final'] = $info['is_final'];
        $ctx['current_status'] = $info['status'];
        return $ctx;
    }
}

if (!function_exists('approval_mail_ctx_clearance')) {
    function approval_mail_ctx_clearance($conn, array $ctx, array $row, $stage, $decision)
    {
        $userId = (int) ($row['UserId'] ?? ($row['EmpId'] ?? 0));
        $ctx = approval_mail_fill_requester($conn, $ctx, $userId);
        $ctx['request_no'] = 'CLR-' . $ctx['request_id'];
        $ctx['details'] = array(
            'Overall Status' => $row['OverallStatus'] ?? '',
        );
        $levels = array(
            array('ManagerStatus', 'Manager'),
            array('ITClearanceStatus', 'IT'),
            array('DeptClearanceStatus', 'Department'),
            array('AccountsClearanceStatus', 'Accounts'),
            array('HRClearanceStatus', 'HR'),
        );
        $info = approval_mail_next_from_levels($row, $levels, $decision);
        $ctx['next_level'] = $info['next'];
        $ctx['is_final'] = $info['is_final'];
        $ctx['current_status'] = $info['status'];
        return $ctx;
    }
}

if (!function_exists('approval_mail_build_html')) {
    function approval_mail_build_html(array $ctx, $actorName, $actorRole, $remarks)
    {
        $decisionLabel = approval_mail_decision_label($ctx['decision']);
        $isReject = approval_mail_is_reject($ctx['decision']);
        $statusColor = $isReject ? '#c0392b' : '#1e7e34';
        $bannerBg = $isReject ? '#c0392b' : '#0F5A4A';
        $logo = approval_mail_logo_url();
        $when = date('d M Y, h:i A');

        $rowsHtml = '';
        $rows = array(
            'Request Type' => $ctx['module_label'],
            'Request No.' => $ctx['request_no'],
            'Employee / Requester' => $ctx['requester_name'],
            'Action' => $decisionLabel,
            'Action By' => trim($actorName . ($actorRole !== '' ? ' (' . $actorRole . ')' : '')),
            'Action Date' => $when,
            'Remarks' => $remarks !== '' ? $remarks : '—',
            'Current Status' => $ctx['current_status'],
        );
        if (!empty($ctx['amount']) || $ctx['amount'] === 0 || $ctx['amount'] === '0') {
            $rows['Amount'] = approval_mail_format_amount($ctx['amount']);
        }
        foreach (($ctx['details'] ?? array()) as $k => $v) {
            if ($v === null || $v === '') {
                continue;
            }
            if (!isset($rows[$k])) {
                $rows[$k] = $v;
            }
        }
        if (!$isReject) {
            if (!empty($ctx['is_final'])) {
                $rows['Final Approval'] = 'Yes — request is fully approved';
                $rows['Next Pending Level'] = 'None';
            } else {
                $rows['Final Approval'] = 'No — further approval required';
                $rows['Next Pending Level'] = $ctx['next_level'] !== '' ? $ctx['next_level'] : 'Further review pending';
            }
        } else {
            $rows['Final Approval'] = 'Rejected — process closed';
            $rows['Next Pending Level'] = 'None';
        }

        foreach ($rows as $label => $value) {
            $rowsHtml .= '<tr>
                <td style="padding:10px 12px;border-bottom:1px solid #eef2f0;color:#5a6b66;width:38%;font-size:13px;">'
                . approval_mail_h($label) . '</td>
                <td style="padding:10px 12px;border-bottom:1px solid #eef2f0;color:#1a2b27;font-size:13px;font-weight:600;">'
                . nl2br(approval_mail_h((string) $value)) . '</td>
            </tr>';
        }

        $nextBox = '';
        if (!$isReject && empty($ctx['is_final']) && $ctx['next_level'] !== '') {
            $nextBox = '<div style="margin:18px 0 0;padding:14px 16px;background:#f3faf7;border-left:4px solid #0F5A4A;border-radius:4px;">
                <div style="font-size:12px;color:#5a6b66;text-transform:uppercase;letter-spacing:.04em;">Next step</div>
                <div style="font-size:15px;color:#0F5A4A;font-weight:700;margin-top:4px;">' . approval_mail_h($ctx['next_level']) . '</div>
              </div>';
        } elseif (!$isReject && !empty($ctx['is_final'])) {
            $nextBox = '<div style="margin:18px 0 0;padding:14px 16px;background:#eef8f0;border-left:4px solid #1e7e34;border-radius:4px;">
                <div style="font-size:15px;color:#1e7e34;font-weight:700;">This request is fully approved.</div>
              </div>';
        } elseif ($isReject) {
            $nextBox = '<div style="margin:18px 0 0;padding:14px 16px;background:#fdf2f1;border-left:4px solid #c0392b;border-radius:4px;">
                <div style="font-size:15px;color:#c0392b;font-weight:700;">This request has been rejected. No further approvals are required.</div>
              </div>';
        }

        $viewBtn = '';
        if (!empty($ctx['view_url'])) {
            $viewBtn = '<p style="margin:24px 0 0;text-align:center;">
                <a href="' . approval_mail_h($ctx['view_url']) . '" style="display:inline-block;background:#0F5A4A;color:#fff;text-decoration:none;padding:12px 22px;border-radius:6px;font-weight:600;font-size:14px;">View Request</a>
              </p>';
        }

        $greeting = 'Dear ' . approval_mail_h($ctx['requester_name'] ?: 'Team') . ',';
        $intro = 'Your <strong>' . approval_mail_h($ctx['module_label']) . '</strong> request '
            . '<strong>' . approval_mail_h($ctx['request_no']) . '</strong> has been '
            . '<strong style="color:' . $statusColor . ';">' . approval_mail_h($decisionLabel) . '</strong> by '
            . approval_mail_h($actorRole) . '.';

        return '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#eef2f0;font-family:Segoe UI,Arial,Helvetica,sans-serif;">
  <div style="max-width:640px;margin:0 auto;padding:24px 12px;">
    <div style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 6px 24px rgba(15,90,74,0.08);">
      <div style="background:' . $bannerBg . ';padding:22px 20px;text-align:center;">
        <img src="' . approval_mail_h($logo) . '" alt="Maha Chai" style="max-height:56px;max-width:180px;">
        <div style="color:#ffffff;font-size:18px;font-weight:700;margin-top:10px;">Approval Notification</div>
        <div style="color:rgba(255,255,255,.85);font-size:13px;margin-top:4px;">' . approval_mail_h($ctx['module_label']) . ' · ' . approval_mail_h($decisionLabel) . '</div>
      </div>
      <div style="padding:28px 24px;color:#243530;">
        <p style="font-size:16px;margin:0 0 12px;">' . $greeting . '</p>
        <p style="font-size:14px;line-height:1.6;margin:0 0 18px;">' . $intro . '</p>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e4ece8;border-radius:8px;overflow:hidden;">
          ' . $rowsHtml . '
        </table>
        ' . $nextBox . '
        ' . $viewBtn . '
        <p style="margin:28px 0 0;font-size:14px;line-height:1.5;color:#4a5c57;">
          Regards,<br>
          <strong>Maha Chai</strong><br>
          Automated Approval System
        </p>
      </div>
      <div style="background:#f4f7f6;padding:14px 20px;text-align:center;font-size:11px;color:#7a8a85;">
        This is an automated notification. Please do not reply to this email.<br>
        © Maha Chai · Asia/Kolkata · ' . approval_mail_h($when) . '
      </div>
    </div>
  </div>
</body></html>';
    }
}

if (!function_exists('approval_mail_build_alt')) {
    function approval_mail_build_alt(array $ctx, $actorName, $actorRole, $remarks)
    {
        $decisionLabel = approval_mail_decision_label($ctx['decision']);
        $lines = array(
            'Dear ' . ($ctx['requester_name'] ?: 'Team') . ',',
            '',
            'Your ' . $ctx['module_label'] . ' request ' . $ctx['request_no'] . ' has been ' . $decisionLabel . ' by ' . $actorRole . '.',
            'Action By: ' . $actorName,
            'Remarks: ' . ($remarks !== '' ? $remarks : '—'),
            'Current Status: ' . $ctx['current_status'],
            'Next Pending Level: ' . ($ctx['next_level'] !== '' ? $ctx['next_level'] : (empty($ctx['is_final']) ? '—' : 'None (final)')),
            'View: ' . ($ctx['view_url'] ?? ''),
            '',
            'Regards,',
            'Maha Chai',
        );
        return implode("\n", $lines);
    }
}

if (!function_exists('approval_mail_phpmailer_send')) {
    function approval_mail_phpmailer_send($toEmail, $toName, $subject, $html, $alt, array $ccList)
    {
        $toEmail = trim((string) $toEmail);
        if ($toEmail === '' || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return array(false, 'Invalid recipient email');
        }

        $mailDirCandidates = array(
            dirname(__DIR__, 2) . '/mail',
            dirname(__DIR__) . '/../mail',
        );
        if (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $mailDirCandidates[] = rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/\\') . '/mail';
        }
        $mailDir = '';
        foreach ($mailDirCandidates as $candidate) {
            if (is_file(rtrim($candidate, '/\\') . '/PHPMailer/src/PHPMailer.php')) {
                $mailDir = rtrim($candidate, '/\\');
                break;
            }
        }
        if ($mailDir === '') {
            return array(false, 'PHPMailer not found');
        }

        require_once $mailDir . '/PHPMailer/src/Exception.php';
        require_once $mailDir . '/PHPMailer/src/PHPMailer.php';
        require_once $mailDir . '/PHPMailer/src/SMTP.php';

        $smtp = approval_mail_smtp();
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $smtp['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $smtp['username'];
            $mail->Password = $smtp['password'];
            $mail->SMTPSecure = $smtp['secure'];
            $mail->Port = (int) $smtp['port'];
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($smtp['from_email'], $smtp['from_name']);
            $mail->addReplyTo($smtp['from_email'], $smtp['from_name']);
            $mail->addAddress($toEmail, (string) $toName);

            foreach ($ccList as $ccEmail => $ccName) {
                $ccEmail = trim((string) $ccEmail);
                if ($ccEmail !== '' && strcasecmp($ccEmail, $toEmail) !== 0 && filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
                    $mail->addCC($ccEmail, (string) $ccName);
                }
            }

            $mail->isHTML(true);
            $mail->Subject = (string) $subject;
            $mail->Body = (string) $html;
            $mail->AltBody = (string) $alt;
            $mail->addCustomHeader('X-Auto-Response-Suppress', 'OOF, AutoReply');
            $mail->addCustomHeader('X-Mailer', 'Maha Chai Approval Notification');
            $mail->send();
            return array(true, '');
        } catch (\Throwable $e) {
            return array(false, $e->getMessage());
        }
    }
}

if (!function_exists('approval_mail_log_insert')) {
    function approval_mail_log_insert($conn, array $data)
    {
        if (!approval_mail_ensure_log_table($conn)) {
            return 0;
        }
        $dedupe = approval_mail_esc($conn, $data['dedupe_key']);
        $exists = getRecord("SELECT id, status FROM tbl_approval_mail_log WHERE dedupe_key='$dedupe' LIMIT 1");
        if ($exists) {
            // Already logged — prevent duplicate send
            return -1 * (int) $exists['id'];
        }
        $sql = "INSERT INTO tbl_approval_mail_log SET
            module='" . approval_mail_esc($conn, $data['module']) . "',
            request_id='" . (int) $data['request_id'] . "',
            stage='" . approval_mail_esc($conn, $data['stage']) . "',
            decision='" . approval_mail_esc($conn, $data['decision']) . "',
            actor_user_id='" . (int) $data['actor_user_id'] . "',
            dedupe_key='$dedupe',
            to_email='" . approval_mail_esc($conn, $data['to_email']) . "',
            cc_emails='" . approval_mail_esc($conn, $data['cc_emails']) . "',
            subject='" . approval_mail_esc($conn, $data['subject']) . "',
            body_preview='" . approval_mail_esc($conn, $data['body_preview']) . "',
            status='" . approval_mail_esc($conn, $data['status']) . "',
            error_message='" . approval_mail_esc($conn, $data['error_message']) . "',
            attempts='" . (int) $data['attempts'] . "',
            sent_at=" . ($data['sent_at'] ? ("'" . approval_mail_esc($conn, $data['sent_at']) . "'") : 'NULL') . ",
            created_at='" . approval_mail_esc($conn, date('Y-m-d H:i:s')) . "'";
        if (!$conn->query($sql)) {
            // Race on unique key = duplicate
            if ($conn->errno === 1062) {
                return -1;
            }
            @error_log('approval_mail_log_insert failed: ' . $conn->error);
            return 0;
        }
        return (int) $conn->insert_id;
    }
}

if (!function_exists('approval_mail_log_update')) {
    function approval_mail_log_update($conn, $logId, $status, $error = '', $sentAt = null)
    {
        $logId = (int) $logId;
        if ($logId < 1) {
            return;
        }
        $sentSql = $sentAt ? ("sent_at='" . approval_mail_esc($conn, $sentAt) . "',") : '';
        $conn->query("UPDATE tbl_approval_mail_log SET
            status='" . approval_mail_esc($conn, $status) . "',
            error_message='" . approval_mail_esc($conn, $error) . "',
            attempts=attempts+1,
            $sentSql
            updated_at='" . approval_mail_esc($conn, date('Y-m-d H:i:s')) . "'
            WHERE id='$logId'");
    }
}

/**
 * Main entry point — safe to call after successful approval/rejection DB write.
 *
 * @param mysqli $conn
 * @param string $module module key e.g. employee_expense
 * @param int $requestId
 * @param string $stage manager|hr|bh|account|bdm|purchase|nso|admin|...
 * @param string|int $decision 1|2|3 or approved|rejected
 * @param int $actorUserId
 * @param string $remarks
 * @param array $extra optional overrides: to_email, view_url, amount, skip_cc
 * @return bool true if sent (or intentionally skipped duplicate), false on hard skip/failure (never throws)
 */
if (!function_exists('approval_mail_notify')) {
    function approval_mail_notify($conn, $module, $requestId, $stage, $decision, $actorUserId, $remarks = '', array $extra = array())
    {
        try {
            date_default_timezone_set('Asia/Kolkata');
            $module = trim((string) $module);
            $requestId = (int) $requestId;
            $stage = strtolower(trim((string) $stage));
            $decision = (string) $decision;
            $actorUserId = (int) $actorUserId;
            $remarks = trim((string) $remarks);

            if ($module === '' || $requestId < 1) {
                return false;
            }
            if (!approval_mail_is_approve($decision) && !approval_mail_is_reject($decision)) {
                return false;
            }

            // Optional hierarchy helper for expense next-level detection
            $hierFile = __DIR__ . '/expense_hierarchy_approval.php';
            if ($module === 'employee_expense' && is_file($hierFile) && !function_exists('expense_hierarchy_has_levels')) {
                require_once $hierFile;
            }

            $ctx = approval_mail_load_context($conn, $module, $requestId, $stage, $decision);
            if (!$ctx) {
                @error_log("approval_mail_notify: context missing for $module#$requestId");
                return false;
            }

            if (!empty($extra['view_url'])) {
                $ctx['view_url'] = $extra['view_url'];
            }
            if (array_key_exists('amount', $extra) && $extra['amount'] !== null && $extra['amount'] !== '') {
                $ctx['amount'] = $extra['amount'];
            }
            if (!empty($extra['to_email'])) {
                $ctx['requester_email'] = trim((string) $extra['to_email']);
            }

            $actor = approval_mail_user($conn, $actorUserId);
            $actorName = approval_mail_user_name($actor) ?: ('User #' . $actorUserId);
            $actorRole = approval_mail_role_label($stage);
            if (!empty($actor['Roll'])) {
                $roleRow = getRecord("SELECT Name FROM tbl_user_type WHERE id='" . (int) $actor['Roll'] . "' LIMIT 1");
                if (!empty($roleRow['Name'])) {
                    $actorRole = $roleRow['Name'] . ' / ' . $actorRole;
                }
            }

            $decisionLabel = approval_mail_decision_label($decision);
            $subject = sprintf(
                '[Maha Chai] %s %s %s by %s',
                $ctx['module_label'],
                $ctx['request_no'],
                $decisionLabel,
                approval_mail_role_label($stage)
            );

            $toEmail = $ctx['requester_email'];
            $toName = $ctx['requester_name'];
            $cc = approval_mail_fixed_cc();
            if (!empty($extra['skip_cc'])) {
                $cc = array();
            }

            $dedupeKey = sha1(strtolower($module) . '|' . $requestId . '|' . $stage . '|' . $decision . '|' . $actorUserId);
            $ccCsv = implode(', ', array_keys($cc));

            // Desktop notification is independent of email availability or SMTP.
            // It is queued for the requester and delivered by the header poller.
            approval_desktop_queue($conn, $ctx, $stage, $decision, $actorName, $remarks, $dedupeKey);

            if ($toEmail === '' || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                approval_mail_log_insert($conn, array(
                    'module' => $module,
                    'request_id' => $requestId,
                    'stage' => $stage,
                    'decision' => $decisionLabel,
                    'actor_user_id' => $actorUserId,
                    'dedupe_key' => $dedupeKey . '|noemail',
                    'to_email' => '',
                    'cc_emails' => $ccCsv,
                    'subject' => $subject,
                    'body_preview' => 'Requester email missing',
                    'status' => 'skipped',
                    'error_message' => 'Requester email missing for user #' . (int) $ctx['requester_user_id'],
                    'attempts' => 0,
                    'sent_at' => null,
                ));
                @error_log("approval_mail_notify: no email for $module#$requestId user=" . $ctx['requester_user_id']);
                return false;
            }

            $html = approval_mail_build_html($ctx, $actorName, approval_mail_role_label($stage), $remarks);
            $alt = approval_mail_build_alt($ctx, $actorName, approval_mail_role_label($stage), $remarks);

            $logId = approval_mail_log_insert($conn, array(
                'module' => $module,
                'request_id' => $requestId,
                'stage' => $stage,
                'decision' => $decisionLabel,
                'actor_user_id' => $actorUserId,
                'dedupe_key' => $dedupeKey,
                'to_email' => $toEmail,
                'cc_emails' => $ccCsv,
                'subject' => $subject,
                'body_preview' => substr(strip_tags($alt), 0, 500),
                'status' => 'pending',
                'error_message' => '',
                'attempts' => 0,
                'sent_at' => null,
            ));

            if ($logId < 0) {
                // Duplicate — already sent/logged
                return true;
            }

            list($ok, $err) = approval_mail_phpmailer_send($toEmail, $toName, $subject, $html, $alt, $cc);
            if ($ok) {
                if ($logId > 0) {
                    approval_mail_log_update($conn, $logId, 'sent', '', date('Y-m-d H:i:s'));
                }
                return true;
            }

            if ($logId > 0) {
                approval_mail_log_update($conn, $logId, 'failed', $err, null);
            } else {
                @error_log('approval_mail_notify send failed: ' . $err);
            }
            return false;
        } catch (\Throwable $e) {
            @error_log('approval_mail_notify exception: ' . $e->getMessage());
            return false;
        }
    }
}
