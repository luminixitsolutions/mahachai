<?php
/**
 * Data + table rendering for all-expenses-view.php (view-only lists).
 */

require_once __DIR__ . '/ho-expense-acted-list-helpers.php';

if (!function_exists('maha_ae_render_list')) {
    function maha_ae_render_list($conn, $type, $mode)
    {
        switch ($type) {
            case 'employee_expense':
                maha_ae_render_employee_expense($conn, $mode);
                break;
            case 'petty_cash':
                maha_ae_render_petty_cash($conn, $mode);
                break;
            case 'vendor_expense':
                maha_ae_render_vendor_expense($conn, $mode, false);
                break;
            case 'nso_vendor_expense':
                maha_ae_render_vendor_expense($conn, $mode, true);
                break;
            case 'advance':
                maha_ae_render_advance($conn, $mode);
                break;
            case 'attendance':
                maha_ae_render_attendance($conn, $mode);
                break;
            case 'resign':
                maha_ae_render_resign($conn, $mode);
                break;
            case 'hiring':
                maha_ae_render_hiring($conn, $mode);
                break;
            case 'penalty':
                maha_ae_render_penalty($conn, $mode);
                break;
            default:
                echo '<tr><td colspan="12" class="text-center text-muted">Unsupported request type.</td></tr>';
        }
    }
}

if (!function_exists('maha_ae_sql_mode')) {
    function maha_ae_sql_mode($type, $mode)
    {
        $sqlType = $type;
        if ($type === 'advance' || $type === 'attendance' || $type === 'resign') {
            $sqlType = 'manager_hr';
        }
        return maha_ar_sql_where($sqlType, $mode);
    }
}

if (!function_exists('maha_ae_render_employee_expense')) {
    function maha_ae_render_employee_expense($conn, $mode)
    {
        ho_expense_all_pending_table_head();
        echo '<tbody>';
        $sql = "SELECT te.*, tu.Fname, tu.Lname, tu.UnderByUser, tu.Photo AS Uphoto,
            tu2.Fname AS MgrName, tu3.Fname AS AccName, tu4.Fname AS AccountName,
            tu5.Fname AS BhFname, tu5.Lname AS BhLname
            FROM tbl_expense_request te
            INNER JOIN tbl_users tu ON tu.id = te.UserId
            LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
            LEFT JOIN tbl_users tu3 ON tu3.id = te.AccBy
            LEFT JOIN tbl_users tu4 ON tu4.id = te.AccountBy
            LEFT JOIN tbl_users tu5 ON tu5.id = te.BhBy
            WHERE " . maha_ae_sql_mode('employee_expense', $mode) . "
            AND te.ExpenseDate >= '" . PENDING_EXPENSE_FROM_DATE . "'";
        $sql .= maha_ar_sql_append_date('ExpenseDate');
        $sql .= ' ORDER BY te.ExpenseDate DESC, te.id DESC';
        $res = $conn->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                ho_expense_all_pending_table_row($conn, $row);
            }
        }
        echo '</tbody>';
    }
}

if (!function_exists('maha_ae_render_petty_cash')) {
    function maha_ae_render_petty_cash($conn, $mode)
    {
        maha_ar_pretty_cash_request_table_head();
        echo '<tbody>';
        $sql = "SELECT te.*, tu.Fname, tu.Lname, tu.Photo AS Uphoto,
            tu2.Fname AS MgrName, tu3.Fname AS AdminName, tu4.Fname AS AccName
            FROM tbl_prettycash_request te
            INNER JOIN tbl_users tu ON tu.id = te.UserId
            LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
            LEFT JOIN tbl_users tu3 ON tu3.id = te.AdminBy
            LEFT JOIN tbl_users tu4 ON tu4.id = te.AccBy
            WHERE " . maha_ae_sql_mode('petty_cash', $mode) . "
            AND te.ExpenseDate >= '" . PENDING_EXPENSE_FROM_DATE . "'";
        $sql .= maha_ar_sql_append_date('ExpenseDate');
        $sql .= ' ORDER BY te.ExpenseDate DESC, te.id DESC';
        $res = $conn->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                maha_ar_pretty_cash_request_table_row($row);
            }
        }
        echo '</tbody>';
    }
}

if (!function_exists('maha_ae_vendor_status_cell')) {
    function maha_ae_vendor_status_cell($status, $name, $date)
    {
        $name = htmlspecialchars(trim((string) $name));
        $dateStr = '';
        if ($date !== '' && $date !== '0000-00-00') {
            $ts = strtotime(str_replace('-', '/', (string) $date));
            $dateStr = $ts ? date('d/m/Y', $ts) : '';
        }
        if ((string) $status === '1') {
            echo "<span style='color:green;'>Approved<br>By {$name}" . ($dateStr ? " | {$dateStr}" : '') . '</span>';
        } elseif ((string) $status === '2') {
            echo "<span style='color:red;'>Rejected<br>By {$name}" . ($dateStr ? " | {$dateStr}" : '') . '</span>';
        } else {
            echo "<span style='color:orange;'>Pending" . ($name !== '' ? '<br>' . $name : '') . '</span>';
        }
    }
}

if (!function_exists('maha_ae_render_vendor_expense')) {
    function maha_ae_render_vendor_expense($conn, $mode, $nso = false)
    {
        $table = $nso ? 'tbl_nso_vendor_expenses' : 'tbl_vendor_expenses';
        ?>
        <thead>
        <tr>
            <th>Expense Id</th>
            <th>Expense Date</th>
            <th>Uploaded By</th>
            <th>Vendor</th>
            <th>Amount</th>
            <?php if ($nso) { ?><th>NSO Approve</th><?php } ?>
            <th>BDM Approve</th>
            <th>Purchase Approve</th>
            <th>Account Approve</th>
            <th>Admin Approve</th>
            <th>Narration</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $sqlType = $nso ? 'nso_vendor_expense' : 'vendor_expense';
        $sql = "SELECT te.*, tu.Fname, tu.Lname, tu2.Fname AS MgrName, tu3.Fname AS VedName,
            tu4.Fname AS BdmName, tu5.Fname AS PurchaseName, tu6.Fname AS AccName";
        if ($nso) {
            $sql .= ', tu8.Fname AS NsoByName';
        }
        $sql .= " FROM {$table} te
            INNER JOIN tbl_users tu ON tu.id = te.UserId
            LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
            LEFT JOIN tbl_users tu3 ON tu3.id = te.VedId
            LEFT JOIN tbl_users tu4 ON tu4.id = te.BdmBy
            LEFT JOIN tbl_users tu5 ON tu5.id = te.PurchaseBy
            LEFT JOIN tbl_users tu6 ON tu6.id = te.AccBy";
        if ($nso) {
            $sql .= ' LEFT JOIN tbl_users tu8 ON tu8.id = te.NsoBy';
        }
        $sql .= ' WHERE ' . maha_ae_sql_mode($sqlType, $mode) . "
            AND te.ExpenseDate >= '" . PENDING_EXPENSE_FROM_DATE . "'";
        $sql .= maha_ar_sql_append_date('ExpenseDate');
        $sql .= ' ORDER BY te.ExpenseDate DESC, te.id DESC';
        $res = $conn->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo (int) $row['id']; ?></td>
                    <td><?php echo maha_ar_fmt_date($row['ExpenseDate'] ?? ''); ?></td>
                    <td><?php echo maha_ar_esc(trim(($row['Fname'] ?? '') . ' ' . ($row['Lname'] ?? ''))); ?></td>
                    <td><?php echo maha_ar_esc($row['VedName'] ?? ''); ?></td>
                    <td class="text-money">&#8377;<?php echo number_format((float) ($row['Amount'] ?? 0), 2); ?></td>
                    <?php if ($nso) { ?>
                    <td><?php maha_ae_vendor_status_cell($row['NsoStatus'] ?? 0, $row['NsoByName'] ?? '', $row['NsoApproveDate'] ?? ''); ?></td>
                    <?php } ?>
                    <td><?php maha_ae_vendor_status_cell($row['BdmStatus'] ?? 0, $row['BdmName'] ?? '', $row['BdmApproveDate'] ?? ''); ?></td>
                    <td><?php maha_ae_vendor_status_cell($row['PurchaseStatus'] ?? 0, $row['PurchaseName'] ?? '', $row['PurchaseApproveDate'] ?? ''); ?></td>
                    <td><?php maha_ae_vendor_status_cell($row['ManagerStatus'] ?? 0, $row['MgrName'] ?? '', $row['ApproveDate'] ?? ''); ?></td>
                    <td><?php maha_ae_vendor_status_cell($row['AdminStatus'] ?? 0, $row['AccName'] ?? '', $row['AdminApproveDate'] ?? ''); ?></td>
                    <td><?php echo maha_ar_esc($row['Narration'] ?? ''); ?></td>
                </tr>
                <?php
            }
        }
        echo '</tbody>';
    }
}

if (!function_exists('maha_ae_render_advance')) {
    function maha_ae_render_advance($conn, $mode)
    {
        ?>
        <thead><tr>
            <th>Request Id</th><th>Request Date</th><th>Manager Approve</th><th>HR Approve</th>
            <th>Photo</th><th>Employee Name</th><th>Amount</th><th>Narration</th>
        </tr></thead><tbody>
        <?php
        $sql = "SELECT te.*, tu.Fname, tu.Lname, tu.Photo AS Uphoto, tu2.Fname AS MgrName
            FROM tbl_advance_salary te
            INNER JOIN tbl_users tu ON tu.id = te.UserId
            LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
            WHERE " . maha_ae_sql_mode('advance', $mode) . "
            AND te.AdvanceDate >= '" . PENDING_EXPENSE_FROM_DATE . "'";
        $sql .= maha_ar_sql_append_date('AdvanceDate');
        $sql .= ' ORDER BY te.AdvanceDate DESC';
        $res = $conn->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $row['HrName'] = 'HR';
                echo '<tr>';
                echo '<td>' . maha_ar_id_link('advance', (int) $row['id']) . '</td>';
                echo '<td>' . maha_ar_fmt_date($row['AdvanceDate'] ?? '') . '</td>';
                maha_ar_mgr_hr_cells($row);
                echo '<td>';
                if (empty($row['Uphoto'])) {
                    echo '<img src="user_icon.jpg" style="width:40px;height:40px;" class="rounded-circle">';
                } else {
                    echo '<img src="../uploads/' . maha_ar_esc($row['Uphoto']) . '" style="width:40px;height:40px;" class="rounded-circle" onerror="this.src=\'user_icon.jpg\'">';
                }
                echo '</td>';
                echo '<td>' . maha_ar_esc(trim(($row['Fname'] ?? '') . ' ' . ($row['Lname'] ?? ''))) . '</td>';
                echo '<td>' . maha_ar_esc($row['AdvanceSalary'] ?? '') . '</td>';
                echo '<td>' . maha_ar_esc($row['Narration'] ?? '') . '</td>';
                echo '</tr>';
            }
        }
        echo '</tbody>';
    }
}

if (!function_exists('maha_ae_render_attendance')) {
    function maha_ae_render_attendance($conn, $mode)
    {
        ?>
        <thead><tr>
            <th>Request Id</th><th>Request Date</th><th>Manager Approve</th><th>HR Approve</th>
            <th>Photo</th><th>Employee Name</th><th>Attendance Type</th><th>In Date</th><th>In Time</th>
        </tr></thead><tbody>
        <?php
        $sql = "SELECT te.*, tu.Fname, tu.Lname, tu.Photo AS Uphoto, tu2.Fname AS MgrName
            FROM tbl_attendance_request te
            INNER JOIN tbl_users tu ON tu.id = te.UserId
            LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
            WHERE " . maha_ae_sql_mode('attendance', $mode) . "
            AND te.ReqDate >= '" . PENDING_EXPENSE_FROM_DATE . "'";
        $sql .= maha_ar_sql_append_date('ReqDate');
        $sql .= ' ORDER BY te.ReqDate DESC';
        $res = $conn->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $row['HrName'] = 'HR';
                echo '<tr>';
                echo '<td>' . maha_ar_id_link('attendance', (int) $row['id']) . '</td>';
                echo '<td>' . maha_ar_fmt_date($row['ReqDate'] ?? '') . '</td>';
                maha_ar_mgr_hr_cells($row);
                echo '<td>';
                if (empty($row['Uphoto'])) {
                    echo '<img src="user_icon.jpg" style="width:40px;height:40px;" class="rounded-circle">';
                } else {
                    echo '<img src="../uploads/' . maha_ar_esc($row['Uphoto']) . '" style="width:40px;height:40px;" class="rounded-circle" onerror="this.src=\'user_icon.jpg\'">';
                }
                echo '</td>';
                echo '<td>' . maha_ar_esc(trim(($row['Fname'] ?? '') . ' ' . ($row['Lname'] ?? ''))) . '</td>';
                echo '<td>' . maha_ar_esc($row['AttType'] ?? '') . '</td>';
                echo '<td>' . maha_ar_fmt_date($row['InDate'] ?? '') . '</td>';
                echo '<td>' . maha_ar_esc($row['InTime'] ?? '') . '</td>';
                echo '</tr>';
            }
        }
        echo '</tbody>';
    }
}

if (!function_exists('maha_ae_render_resign')) {
    function maha_ae_render_resign($conn, $mode)
    {
        ?>
        <thead><tr>
            <th>Request Id</th><th>Request Date</th><th>Manager Approve</th><th>HR Approve</th>
            <th>Photo</th><th>Employee Name</th><th>Reason</th><th>Last Working Day</th>
        </tr></thead><tbody>
        <?php
        $sql = "SELECT te.*, tu.Fname, tu.Lname, tu.Photo AS Uphoto, tu2.Fname AS MgrName
            FROM tbl_resign_request te
            INNER JOIN tbl_users tu ON tu.id = te.UserId
            LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
            WHERE " . maha_ae_sql_mode('resign', $mode) . "
            AND te.ReqDate >= '" . PENDING_EXPENSE_FROM_DATE . "'";
        $sql .= maha_ar_sql_append_date('ReqDate');
        $sql .= ' ORDER BY te.ReqDate DESC';
        $res = $conn->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $row['HrName'] = 'HR';
                echo '<tr>';
                echo '<td>' . (int) $row['id'] . '</td>';
                echo '<td>' . maha_ar_fmt_date($row['ReqDate'] ?? '') . '</td>';
                maha_ar_mgr_hr_cells($row);
                echo '<td>';
                if (empty($row['Uphoto'])) {
                    echo '<img src="user_icon.jpg" style="width:40px;height:40px;" class="rounded-circle">';
                } else {
                    echo '<img src="../uploads/' . maha_ar_esc($row['Uphoto']) . '" style="width:40px;height:40px;" class="rounded-circle" onerror="this.src=\'user_icon.jpg\'">';
                }
                echo '</td>';
                echo '<td>' . maha_ar_esc(trim(($row['Fname'] ?? '') . ' ' . ($row['Lname'] ?? ''))) . '</td>';
                echo '<td>' . maha_ar_esc($row['Reason'] ?? '') . '</td>';
                echo '<td>' . maha_ar_fmt_date($row['LastWorkingDay'] ?? '') . '</td>';
                echo '</tr>';
            }
        }
        echo '</tbody>';
    }
}

if (!function_exists('maha_ae_hiring_status_label')) {
    function maha_ae_hiring_status_label($status)
    {
        $st = (int) $status;
        if ($st === 0) {
            return '<span style="color:orange;">Pending</span>';
        }
        if ($st === 2) {
            return '<span style="color:red;">Rejected</span>';
        }
        if (in_array($st, array(1, 3, 4), true)) {
            return '<span style="color:green;">Approved</span>';
        }
        return '<span class="text-muted">—</span>';
    }
}

if (!function_exists('maha_ae_render_hiring')) {
    function maha_ae_render_hiring($conn, $mode)
    {
        ?>
        <thead><tr>
            <th>ID</th><th>Franchise / Outlet</th><th>Required</th><th>Remarks</th>
            <th>Requested By</th><th>Created Date</th><th>Manager Status</th><th>HR Status</th>
        </tr></thead><tbody>
        <?php
        $sql = "SELECT hr.*, u.ShopName, ru.Fname AS RequestedByName, ru.Lname AS RequestedByLname
            FROM tbl_hiring_request hr
            LEFT JOIN tbl_users u ON hr.FranchiseId = u.id
            LEFT JOIN tbl_users ru ON hr.RequestedBy = ru.id
            WHERE " . maha_ar_sql_where('hiring', $mode, 'hr') . '
            ORDER BY hr.id DESC';
        $res = $conn->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . (int) $row['id'] . '</td>';
                echo '<td>' . maha_ar_esc($row['ShopName'] ?? '') . '</td>';
                echo '<td>' . maha_ar_esc($row['Required'] ?? '') . '</td>';
                echo '<td>' . maha_ar_esc($row['Remarks'] ?? '') . '</td>';
                echo '<td>' . maha_ar_esc(trim(($row['RequestedByName'] ?? '') . ' ' . ($row['RequestedByLname'] ?? ''))) . '</td>';
                echo '<td>' . maha_ar_fmt_date($row['CreatedDate'] ?? '') . '</td>';
                echo '<td>' . maha_ae_hiring_status_label($row['Status'] ?? 0) . '</td>';
                $hrSt = (int) ($row['Status'] ?? 0);
                if ($hrSt === 1) {
                    echo '<td><span style="color:orange;">Pending HR</span></td>';
                } elseif ($hrSt === 3) {
                    echo '<td><span style="color:green;">HR Approved</span></td>';
                } elseif ($hrSt === 4) {
                    echo '<td><span style="color:red;">HR Rejected</span></td>';
                } else {
                    echo '<td><span class="text-muted">—</span></td>';
                }
                echo '</tr>';
            }
        }
        echo '</tbody>';
    }
}

if (!function_exists('maha_ae_penalty_status_badge')) {
    function maha_ae_penalty_status_badge($status)
    {
        $st = strtolower(trim((string) $status));
        if ($st === 'approved') {
            return '<span style="color:green;">Approved</span>';
        }
        if ($st === 'rejected') {
            return '<span style="color:red;">Rejected</span>';
        }
        if ($st === 'pending') {
            return '<span style="color:orange;">Pending</span>';
        }
        return '<span class="text-muted">—</span>';
    }
}

if (!function_exists('maha_ae_render_penalty')) {
    function maha_ae_render_penalty($conn, $mode)
    {
        ?>
        <thead><tr>
            <th>ID</th><th>Franchise</th><th>Amount</th><th>Date</th><th>Reason</th>
            <th>BDM Status</th><th>BH Status</th>
        </tr></thead><tbody>
        <?php
        $sql = "SELECT p.*, u.ShopName AS franchise_name
            FROM tbl_penalty p
            LEFT JOIN tbl_users u ON u.id = p.franchise_id
            WHERE " . maha_ar_sql_where('penalty', $mode, 'p') . '
            ORDER BY p.id DESC';
        $res = $conn->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . (int) $row['id'] . '</td>';
                echo '<td>' . maha_ar_esc($row['franchise_name'] ?? '') . '</td>';
                echo '<td>&#8377;' . number_format((float) ($row['penalty_amount'] ?? 0), 2) . '</td>';
                echo '<td>' . maha_ar_fmt_date($row['penalty_date'] ?? '') . '</td>';
                echo '<td>' . maha_ar_esc($row['reason'] ?? '') . '</td>';
                echo '<td>' . maha_ae_penalty_status_badge($row['bdm_status'] ?? '') . '</td>';
                echo '<td>' . maha_ae_penalty_status_badge($row['bh_status'] ?? '') . '</td>';
                echo '</tr>';
            }
        }
        echo '</tbody>';
    }
}
