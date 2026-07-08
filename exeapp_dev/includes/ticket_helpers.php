<?php
/** Employee app ticket helpers — reuses control panel core. */
$tktHelperPaths = array(
    __DIR__ . '/../../control_panel_maha/includes/ticket_management_helpers.php',
);
$tktHelpersLoaded = false;
foreach ($tktHelperPaths as $tktHelperPath) {
    if (is_file($tktHelperPath)) {
        require_once $tktHelperPath;
        $tktHelpersLoaded = true;
        break;
    }
}
if (!$tktHelpersLoaded) {
    trigger_error('Ticket management helpers not found. Expected control_panel_maha/includes/ticket_management_helpers.php', E_USER_ERROR);
}

if (!function_exists('tkt_emp_allowed_departments')) {
    /** All active departments — employees can raise tickets to any department. */
    function tkt_emp_allowed_departments($conn, $userId)
    {
        return tkt_get_departments($conn);
    }
}

if (!function_exists('tkt_emp_default_department_id')) {
    function tkt_emp_default_department_id($conn, $userId)
    {
        return tkt_user_department_id($conn, $userId);
    }
}

if (!function_exists('tkt_emp_user_branch_id')) {
    function tkt_emp_user_branch_id($conn, $userId)
    {
        $row = getRecord("SELECT UnderFrId FROM tbl_users WHERE id='" . (int) $userId . "' LIMIT 1");
        return $row ? (int) ($row['UnderFrId'] ?? 0) : 0;
    }
}

if (!function_exists('tkt_emp_table_has_column')) {
    function tkt_emp_table_has_column($conn, $table, $column)
    {
        if (function_exists('tkt_table_has_column')) {
            return tkt_table_has_column($conn, $table, $column);
        }
        static $cache = array();
        $key = $table . '.' . $column;
        if (!isset($cache[$key])) {
            $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
            $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
            $r = @$conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
            $cache[$key] = ($r && $r->num_rows > 0);
        }
        return $cache[$key];
    }
}

if (!function_exists('tkt_emp_my_tickets_sql')) {
    /** Tickets raised by or reported by the logged-in employee. */
    function tkt_emp_my_tickets_sql($conn, $userId)
    {
        $uid = (int) $userId;
        $parts = array("t.created_by='$uid'");
        if (tkt_emp_table_has_column($conn, 'tbl_tickets', 'reported_by')) {
            $parts[] = "t.reported_by='$uid'";
        }
        return '(' . implode(' OR ', $parts) . ')';
    }
}

if (!function_exists('tkt_emp_assigned_tickets_sql')) {
    function tkt_emp_assigned_tickets_sql($conn, $userId)
    {
        $uid = (int) $userId;
        if (!tkt_emp_table_has_column($conn, 'tbl_tickets', 'assigned_to')) {
            return '0';
        }
        return "t.assigned_to='$uid'";
    }
}

if (!function_exists('tkt_safe_list')) {
    function tkt_safe_list($sql)
    {
        global $conn;
        $rows = array();
        $res = @$conn->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }
}

if (!function_exists('tkt_emp_fetch_tickets')) {
    function tkt_emp_fetch_tickets($conn, $userId, $statuses, $scope = 'my')
    {
        $userId = (int) $userId;
        if ($userId < 1 || empty($statuses)) {
            return array();
        }
        if (function_exists('tkt_repair_blank_ticket_statuses')) {
            tkt_repair_blank_ticket_statuses($conn);
        }
        $ownerSql = ($scope === 'assigned')
            ? tkt_emp_assigned_tickets_sql($conn, $userId)
            : tkt_emp_my_tickets_sql($conn, $userId);

        $pendingLike = array('open', 'assigned', 'pending', 'waiting_for_user', 'waiting_for_approval', 'waiting_for_vendor', 'on_hold', 'reopened');
        $includeBlank = (bool) array_intersect((array) $statuses, $pendingLike);
        $statusSql = function_exists('tkt_status_matches_filter')
            ? tkt_status_matches_filter($conn, $statuses, $includeBlank)
            : "LOWER(TRIM(t.status)) IN ('" . implode("','", array_map(function ($st) use ($conn) {
                return mysqli_real_escape_string($conn, strtolower((string) $st));
            }, (array) $statuses)) . "')";

        $sql = "SELECT t.*, td.Name AS DeptName
            FROM tbl_tickets t
            LEFT JOIN tbl_departments td ON td.id=t.department_id
            WHERE $ownerSql AND $statusSql
            ORDER BY t.created_at DESC, t.id DESC";
        return tkt_safe_list($sql);
    }
}
