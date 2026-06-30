<?php
/**
 * Read-only status display + history links for All Requests listing pages.
 */

if (!function_exists('maha_ar_esc')) {
    function maha_ar_esc($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('maha_ar_fmt_date')) {
    function maha_ar_fmt_date($date)
    {
        if ($date === null || $date === '' || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return '';
        }
        $ts = strtotime(str_replace('-', '/', (string) $date));
        return $ts ? date('d/m/Y', $ts) : '';
    }
}

if (!function_exists('maha_ar_history_url')) {
    function maha_ar_history_url($type, $id)
    {
        return 'all-request-history.php?type=' . urlencode((string) $type) . '&id=' . (int) $id;
    }
}

if (!function_exists('maha_ar_id_link')) {
    function maha_ar_id_link($type, $id, $label = null, $target = '_blank')
    {
        $label = $label !== null ? $label : (int) $id;
        $targetAttr = $target ? ' target="' . maha_ar_esc($target) . '"' : '';

        return '<a href="' . maha_ar_esc(maha_ar_history_url($type, $id)) . '"' . $targetAttr . '>' . maha_ar_esc($label) . '</a>';
    }
}

if (!function_exists('maha_ar_status_html')) {
    /**
     * @param string|int $status 0=pending, 1=approved, 2=rejected
     */
    function maha_ar_status_html($status, $pendingLabel, $byName = '', $date = '', $comment = '')
    {
        $status = (string) $status;
        $byName = trim((string) $byName);
        $dateStr = maha_ar_fmt_date($date);
        $suffix = '';
        if ($byName !== '') {
            $suffix .= '<br>By ' . maha_ar_esc($byName);
        }
        if ($dateStr !== '') {
            $suffix .= ' | ' . maha_ar_esc($dateStr);
        }
        if (trim((string) $comment) !== '') {
            $suffix .= '<br><small>' . maha_ar_esc($comment) . '</small>';
        }

        if ($status === '1') {
            return "<span style=\"color:green;\">Approved{$suffix}</span>";
        }
        if ($status === '2') {
            return "<span style=\"color:red;\">Rejected{$suffix}</span>";
        }

        return '<span style="color:orange;">' . maha_ar_esc($pendingLabel) . '</span>';
    }
}

if (!function_exists('maha_ar_echo_status')) {
    function maha_ar_echo_status($status, $pendingLabel, $byName = '', $date = '', $comment = '')
    {
        echo maha_ar_status_html($status, $pendingLabel, $byName, $date, $comment);
    }
}

if (!function_exists('maha_ar_emp_expense_manager_cell')) {
    function maha_ar_emp_expense_manager_cell($row, $underByUser = null)
    {
        if ($underByUser === null && isset($row['UnderByUser'])) {
            $underByUser = $row['UnderByUser'];
        }
        if (in_array((int) $underByUser, array(5, 384, 415), true)) {
            echo '<td style="color:red;">No manager assigned</td>';
            return;
        }
        echo '<td>';
        maha_ar_echo_status(
            $row['ManagerStatus'] ?? 0,
            'Pending By Manager',
            $row['MgrName'] ?? '',
            $row['ApproveDate'] ?? '',
            $row['MannagerComment'] ?? ($row['ManagerComment'] ?? '')
        );
        echo '</td>';
    }
}

if (!function_exists('maha_ar_emp_expense_bh_cell')) {
    function maha_ar_emp_expense_bh_cell($row)
    {
        $bhSt = isset($row['BhStatus']) ? (string) $row['BhStatus'] : '';
        $bhName = trim(($row['BhFname'] ?? '') . ' ' . ($row['BhLname'] ?? ''));
        if ($bhName === '' && !empty($row['BhName'])) {
            $bhName = $row['BhName'];
        }
        echo '<td>';
        if ($bhSt === '1' || ($bhSt === '' && !empty($row['BhBy']))) {
            maha_ar_echo_status('1', '', $bhName !== '' ? $bhName : '—', $row['BhApproveDate'] ?? '', $row['BhComment'] ?? '');
        } elseif ($bhSt === '2') {
            maha_ar_echo_status('2', '', $bhName !== '' ? $bhName : '—', $row['BhApproveDate'] ?? '', $row['BhComment'] ?? '');
        } elseif ($bhSt === '0') {
            maha_ar_echo_status('0', 'Pending By Business Head', '', '', $row['BhComment'] ?? '');
        } else {
            echo '<span style="color:#6c757d;">Legacy / no BH record</span>';
        }
        echo '</td>';
    }
}

if (!function_exists('maha_ar_emp_expense_account_cell')) {
    function maha_ar_emp_expense_account_cell($row)
    {
        echo '<td>';
        if (isset($row['Gst']) && $row['Gst'] === 'Yes') {
            maha_ar_echo_status(
                $row['AccountStatus'] ?? 0,
                'Pending By Accountant',
                $row['AccountName'] ?? '',
                $row['AccountApproveDate'] ?? '',
                $row['AccountComment'] ?? ''
            );
        } else {
            echo '<span style="color:#6c757d;">N/A</span>';
        }
        echo '</td>';
    }
}

if (!function_exists('maha_ar_emp_expense_admin_cell')) {
    function maha_ar_emp_expense_admin_cell($row)
    {
        echo '<td>';
        maha_ar_echo_status(
            $row['AdminStatus'] ?? 0,
            'Pending By Admin',
            $row['AccName'] ?? ($row['AdminName'] ?? ''),
            $row['AdminApproveDate'] ?? '',
            $row['AdminComment'] ?? ''
        );
        echo '</td>';
    }
}

if (!function_exists('maha_ar_pretty_cash_manager_cell')) {
    function maha_ar_pretty_cash_manager_cell($row, $noManager = false)
    {
        echo '<td>';
        if ($noManager) {
            echo '<span style="color:red;">No manager assigned</span>';
        } else {
            maha_ar_echo_status(
                $row['ManagerStatus'] ?? 0,
                'Pending By Manager',
                $row['MgrName'] ?? '',
                $row['MannagerApproveDate'] ?? '',
                $row['MannagerComment'] ?? ''
            );
        }
        echo '</td>';
    }
}

if (!function_exists('maha_ar_pretty_cash_admin_cell')) {
    function maha_ar_pretty_cash_admin_cell($row)
    {
        echo '<td>';
        maha_ar_echo_status(
            $row['AdminStatus'] ?? 0,
            'Pending By Admin',
            $row['AdminName'] ?? '',
            $row['AdminApproveDate'] ?? '',
            $row['AdminComment'] ?? ''
        );
        echo '</td>';
    }
}

if (!function_exists('maha_ar_pretty_cash_account_cell')) {
    function maha_ar_pretty_cash_account_cell($row)
    {
        echo '<td>';
        maha_ar_echo_status(
            $row['AccStatus'] ?? 0,
            'Pending By Accountant',
            $row['AccName'] ?? '',
            $row['AccApproveDate'] ?? '',
            $row['AccComment'] ?? ''
        );
        echo '</td>';
    }
}

if (!function_exists('maha_ar_mgr_hr_cells')) {
    function maha_ar_mgr_hr_cells($row, $mgrDateField = 'MannagerApproveDate', $hrDateField = 'HrApproveDate')
    {
        $mgrDate = $row[$mgrDateField] ?? '';
        echo '<td>';
        maha_ar_echo_status(
            $row['ManagerStatus'] ?? 0,
            'Pending By Manager',
            $row['MgrName'] ?? '',
            $mgrDate,
            $row['MannagerComment'] ?? ''
        );
        echo '</td><td>';
        maha_ar_echo_status(
            $row['HrStatus'] ?? 0,
            'Pending By HR',
            $row['HrName'] ?? ($row['AccName'] ?? 'HR'),
            $row[$hrDateField] ?? '',
            $row['HrComment'] ?? ''
        );
        echo '</td>';
    }
}

if (!function_exists('maha_ar_leave_request_table_row')) {
    function maha_ar_leave_request_table_row($row)
    {
        $MgrName = trim((string) ($row['MgrName'] ?? ''));
        $HrName = trim((string) ($row['HrName'] ?? 'HR'));
        if ($HrName === '') {
            $HrName = 'HR';
        }
        $mgrDate = maha_ar_fmt_date($row['MannagerApproveDate'] ?? '');
        $hrDate = maha_ar_fmt_date($row['HrApproveDate'] ?? '');
        $reqDate = maha_ar_fmt_date($row['ReqDate'] ?? '');
        $fromDate = maha_ar_fmt_date($row['FromDate'] ?? '');
        $toDate = maha_ar_fmt_date($row['ToDate'] ?? '');
        $mgrComment = trim((string) ($row['MannagerComment'] ?? ''));
        $hrComment = trim((string) ($row['HrComment'] ?? ''));
        ?>
            <tr>
                <td><?php echo (int) $row['id']; ?></td>
                <td><?php echo $reqDate !== '' ? maha_ar_esc($reqDate) : '-'; ?></td>
                <td>
                    <?php if (($row['ManagerStatus'] ?? '') == '1') { ?>
                        <span style="color:green;">Approved<?php if ($MgrName !== '') { ?><br>By <?php echo maha_ar_esc($MgrName); ?><?php } ?><?php if ($mgrDate !== '') { ?> | <?php echo maha_ar_esc($mgrDate); ?><?php } ?><?php if ($mgrComment !== '') { ?><br><small><?php echo maha_ar_esc($mgrComment); ?></small><?php } ?></span>
                    <?php } elseif (($row['ManagerStatus'] ?? '') == '2') { ?>
                        <span style="color:red;">Rejected<?php if ($MgrName !== '') { ?><br>By <?php echo maha_ar_esc($MgrName); ?><?php } ?><?php if ($mgrDate !== '') { ?> | <?php echo maha_ar_esc($mgrDate); ?><?php } ?><?php if ($mgrComment !== '') { ?><br><small><?php echo maha_ar_esc($mgrComment); ?></small><?php } ?></span>
                    <?php } else { ?>
                        <span style="color:orange;">Pending By Manager</span>
                    <?php } ?>
                </td>
                <td>
                    <?php if (($row['HrStatus'] ?? '') == '1') { ?>
                        <span style="color:green;">Approved<?php if ($HrName !== '') { ?><br>By <?php echo maha_ar_esc($HrName); ?><?php } ?><?php if ($hrDate !== '') { ?> | <?php echo maha_ar_esc($hrDate); ?><?php } ?><?php if ($hrComment !== '') { ?><br><small><?php echo maha_ar_esc($hrComment); ?></small><?php } ?></span>
                    <?php } elseif (($row['HrStatus'] ?? '') == '2') { ?>
                        <span style="color:red;">Rejected<?php if ($HrName !== '') { ?><br>By <?php echo maha_ar_esc($HrName); ?><?php } ?><?php if ($hrDate !== '') { ?> | <?php echo maha_ar_esc($hrDate); ?><?php } ?><?php if ($hrComment !== '') { ?><br><small><?php echo maha_ar_esc($hrComment); ?></small><?php } ?></span>
                    <?php } else { ?>
                        <span style="color:orange;">Pending By HR</span>
                    <?php } ?>
                </td>
                <td>
                    <?php if (empty($row['Uphoto'])) { ?>
                        <img src="user_icon.jpg" class="d-block ui-w-40 rounded-circle" style="width: 40px;height: 40px;">
                    <?php } elseif (file_exists('../uploads/' . $row['Uphoto'])) { ?>
                        <img src="../uploads/<?php echo maha_ar_esc($row['Uphoto']); ?>" class="d-block ui-w-40 rounded-circle" alt="" style="width: 40px;height: 40px;">
                    <?php } else { ?>
                        <img src="user_icon.jpg" class="d-block ui-w-40 rounded-circle" style="width: 40px;height: 40px;">
                    <?php } ?>
                </td>
                <td><?php echo maha_ar_esc(trim(($row['Fname'] ?? '') . ' ' . ($row['Lname'] ?? ''))); ?></td>
                <td><?php echo maha_ar_esc($row['Narration'] ?? ''); ?></td>
                <td><?php echo $fromDate !== '' ? maha_ar_esc($fromDate) : '-'; ?></td>
                <td><?php echo $toDate !== '' ? maha_ar_esc($toDate) : '-'; ?></td>
                <td><?php echo maha_ar_esc($row['TotDays'] ?? ''); ?> Days</td>
            </tr>
        <?php
    }
}

if (!function_exists('maha_ar_leave_request_datatable_init')) {
    function maha_ar_leave_request_datatable_init()
    {
        ?>
<script type="text/javascript">
    $(document).ready(function() {
        if ($('#example').length) {
            $('#example').DataTable({
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: ['excelHtml5'],
                order: [[0, 'desc']]
            });
        }
        if (typeof hideLoader === 'function') {
            hideLoader();
        }
    });
</script>
        <?php
    }
}

if (!function_exists('maha_ar_vendor_admin_cell')) {
    function maha_ar_vendor_admin_cell($row)
    {
        $adminName = $row['AccName'] ?? ($row['AdminName'] ?? '');
        $admindate = $row['AdminApproveDate'] ?? '';
        $pendingLabel = 'Pending By Admin';
        if (isset($row['Amount']) && (float) $row['Amount'] <= 2000 && ($row['TrustedVendor'] ?? '') !== 'Yes') {
            $pendingLabel = 'Pending By Anup Asawani';
        }
        echo '<td>';
        maha_ar_echo_status($row['AdminStatus'] ?? 0, $pendingLabel, $adminName, $admindate, $row['AdminComment'] ?? '');
        echo '</td>';
    }
}

if (!function_exists('maha_ar_admin_action_cell')) {
    /**
     * Status cell — clickable approve link only while pending (status 0).
     */
    function maha_ar_admin_action_cell($status, $pendingLabel, $approveHref, $byName = '', $date = '', $comment = '')
    {
        echo '<td>';
        $st = (string) $status;
        if ($st === '0' || $st === '') {
            echo '<a href="' . maha_ar_esc($approveHref) . '">';
            maha_ar_echo_status('0', $pendingLabel, $byName, $date, $comment);
            echo '</a>';
        } else {
            maha_ar_echo_status($status, $pendingLabel, $byName, $date, $comment);
        }
        echo '</td>';
    }
}

if (!function_exists('maha_ar_emp_expense_admin_action_cell')) {
    function maha_ar_emp_expense_admin_action_cell($row, $page = 'ho')
    {
        $href = 'approve-expense-by-account.php?id=' . (int) $row['id'] . '&page=' . urlencode($page);
        maha_ar_admin_action_cell(
            $row['AdminStatus'] ?? 0,
            'Pending By Admin',
            $href,
            $row['AccName'] ?? '',
            $row['AdminApproveDate'] ?? '',
            $row['AdminComment'] ?? ''
        );
    }
}

if (!function_exists('maha_ar_pretty_cash_admin_action_cell')) {
    function maha_ar_pretty_cash_admin_action_cell($row, $page = 'ho')
    {
        $href = 'approve-pretty-cash-by-admin.php?id=' . (int) $row['id'] . '&page=' . urlencode($page);
        maha_ar_admin_action_cell(
            $row['AdminStatus'] ?? 0,
            'Pending By Admin',
            $href,
            $row['AdminName'] ?? '',
            $row['AdminApproveDate'] ?? '',
            $row['AdminComment'] ?? ''
        );
    }
}

if (!function_exists('maha_ar_vendor_admin_action_cell')) {
    function maha_ar_vendor_admin_action_cell($row, $nso = false)
    {
        $href = ($nso ? 'approve-nso-vendor-expense-by-account.php' : 'approve-vendor-expense-by-account.php')
            . '?id=' . (int) $row['id'];
        $pendingLabel = 'Pending By Admin';
        if (!$nso && isset($row['Amount']) && (float) $row['Amount'] <= 2000) {
            $pendingLabel = 'Pending By Anup Asawani';
        }
        maha_ar_admin_action_cell(
            $row['AdminStatus'] ?? 0,
            $pendingLabel,
            $href,
            $row['AccName'] ?? '',
            $row['AdminApproveDate'] ?? '',
            $row['AdminComment'] ?? ''
        );
    }
}

if (!function_exists('maha_ar_hr_action_cell')) {
    function maha_ar_hr_action_cell($row, $approveScript, $dateField = 'HrApproveDate')
    {
        $href = $approveScript . '?id=' . (int) $row['id'];
        maha_ar_admin_action_cell(
            $row['HrStatus'] ?? 0,
            'Pending By HR',
            $href,
            $row['HrName'] ?? 'HR',
            $row[$dateField] ?? '',
            $row['HrComment'] ?? ''
        );
    }
}

if (!function_exists('maha_ar_cash_book_admin_action_cell')) {
    function maha_ar_cash_book_admin_action_cell($row)
    {
        $href = 'approve-cash-book-by-admin.php?id=' . (int) $row['id'];
        maha_ar_admin_action_cell(
            $row['ApproveStatus'] ?? 0,
            'Pending By Admin',
            $href,
            'Admin',
            $row['ApproveDate'] ?? '',
            $row['ApproveComment'] ?? ''
        );
    }
}

if (!function_exists('maha_ar_pretty_cash_approve_cell_html')) {
    function maha_ar_pretty_cash_approve_cell_html($status, $byName = '', $date = '', $noManager = false)
    {
        if ($noManager) {
            return '<div class="approve-status text-muted">N/A</div><div class="approve-by">No manager</div>';
        }
        $st = (string) $status;
        $html = '';
        if ($st === '1') {
            $html .= '<div class="approve-status text-success">Approved</div>';
        } elseif ($st === '2') {
            $html .= '<div class="approve-status text-danger">Rejected</div>';
        } else {
            $html .= '<div class="approve-status text-warning">Pending</div>';
        }
        $byName = trim((string) $byName);
        if ($byName !== '') {
            $html .= '<div class="approve-by">' . maha_ar_esc($byName) . '</div>';
        }
        $dateStr = maha_ar_fmt_date($date);
        if ($dateStr !== '') {
            $html .= '<div class="approve-date">' . maha_ar_esc($dateStr) . '</div>';
        }
        return $html;
    }
}

if (!function_exists('maha_ar_emp_expense_bh_approve_cell_html')) {
    function maha_ar_emp_expense_bh_approve_cell_html(array $row)
    {
        $bhSt = isset($row['BhStatus']) ? (string) $row['BhStatus'] : '';
        $bhName = trim(($row['BhFname'] ?? '') . ' ' . ($row['BhLname'] ?? ''));
        if ($bhSt === '1' || ($bhSt === '' && !empty($row['BhBy']))) {
            return maha_ar_pretty_cash_approve_cell_html('1', $bhName !== '' ? $bhName : '—', $row['BhApproveDate'] ?? '');
        }
        if ($bhSt === '2') {
            return maha_ar_pretty_cash_approve_cell_html('2', $bhName !== '' ? $bhName : '—', $row['BhApproveDate'] ?? '');
        }
        if ($bhSt === '0') {
            return maha_ar_pretty_cash_approve_cell_html('0', '', '');
        }

        return '<div class="approve-status text-muted">N/A</div><div class="approve-by">Legacy</div>';
    }
}

if (!function_exists('maha_ar_emp_expense_account_approve_cell_html')) {
    function maha_ar_emp_expense_account_approve_cell_html(array $row)
    {
        if (isset($row['Gst']) && $row['Gst'] === 'Yes') {
            return maha_ar_pretty_cash_approve_cell_html(
                $row['AccountStatus'] ?? 0,
                $row['AccountName'] ?? '',
                $row['AccountApproveDate'] ?? ''
            );
        }

        return '<div class="approve-status text-muted">N/A</div>';
    }
}

if (!function_exists('maha_ar_emp_expense_request_table_head')) {
    function maha_ar_emp_expense_request_table_head($showStockCols = false)
    {
        ?>
            <thead>
            <tr>
                <?php if ($showStockCols) { ?>
                <th style="width:36px;">
                    <input type="checkbox" id="chkSelectAllExpenseStock" title="Select all with pending stock">
                </th>
                <th>Add Stock</th>
                <?php } ?>
                <th>Expense Id</th>
                <th>Expense Date</th>
                <th>Employee Name</th>
                <th>Photo</th>
                <th>Amount</th>
                <th>Admin Approve Amount</th>
                <th>Manager Approve</th>
                <th>Business Head Approve</th>
                <th>Account Approve</th>
                <th>Admin Approve</th>
                <th>Narration</th>
            </tr>
            </thead>
        <?php
    }
}

if (!function_exists('maha_ar_emp_expense_request_table_row')) {
    function maha_ar_emp_expense_request_table_row(array $row, array $opts = array())
    {
        $showStockCols = !empty($opts['show_stock_cols']);
        $canSelectForStock = !empty($opts['can_select_for_stock']);
        $prodLineCount = (int) ($opts['prod_line_count'] ?? 0);
        $pendingStockCount = (int) ($opts['pending_stock_count'] ?? 0);
        $expIdRow = (int) ($row['id'] ?? 0);
        $empId = (int) ($row['UserId'] ?? 0);
        $noManager = in_array((int) ($row['UnderByUser'] ?? 0), array(5, 384, 415), true);
        $mgrName = trim((string) ($row['MgrName'] ?? ''));
        $adminName = trim((string) ($row['AccName'] ?? ($row['AdminName'] ?? '')));
        $trAttrs = $showStockCols
            ? ' class="expense-stock-row" data-expid="' . $expIdRow . '" data-pending-stock="' . ($canSelectForStock ? '1' : '0') . '"'
            : '';
        ?>
            <tr<?php echo $trAttrs; ?>>
                <?php if ($showStockCols) { ?>
                <td class="text-center">
                    <?php if ($canSelectForStock) { ?>
                    <input type="checkbox" class="exp-stock-chk" value="<?php echo $expIdRow; ?>">
                    <?php } ?>
                </td>
                <td class="exp-stock-action-cell">
                    <?php if ($prodLineCount < 1) { ?>
                        <span class="text-muted">—</span>
                    <?php } elseif ($pendingStockCount < 1) { ?>
                        <span class="badge badge-success exp-stock-status-badge">In stock</span>
                    <?php } else { ?>
                        <button type="button" class="btn btn-sm btn-primary btn-add-expense-to-stock"
                            data-expid="<?php echo $expIdRow; ?>">Add to Stock</button>
                    <?php } ?>
                </td>
                <?php } ?>
                <td><?php echo maha_ar_id_link('employee_expense', $expIdRow); ?></td>
                <td><?php echo maha_ar_fmt_date($row['ExpenseDate'] ?? ''); ?></td>
                <td class="emp-name-cell"><a href="employee-hierarchy.php?id=<?php echo $empId; ?>" target="_blank"><?php echo maha_ar_esc(trim(($row['Fname'] ?? '') . ' ' . ($row['Lname'] ?? ''))); ?></a></td>
                <td class="photo-cell"><?php if (empty($row['Uphoto'])) { ?>
                  <img src="user_icon.jpg" class="d-block ui-w-40 rounded-circle mx-auto" style="width: 40px;height: 40px;">
                 <?php } elseif (file_exists('../uploads/' . $row['Uphoto'])) { ?>
                 <img src="../uploads/<?php echo maha_ar_esc($row['Uphoto']); ?>" class="d-block ui-w-40 rounded-circle mx-auto" alt="" style="width: 40px;height: 40px;">
                  <?php } else { ?>
                 <img src="user_icon.jpg" class="d-block ui-w-40 rounded-circle mx-auto" style="width: 40px;height: 40px;">
             <?php } ?></td>
                <td class="text-money">&#8377;<?php echo number_format((float) ($row['Amount'] ?? 0), 2); ?></td>
                <td class="text-money">&#8377;<?php echo number_format((float) ($row['AccAmount'] ?? 0), 2); ?></td>
                <td class="maha-ar-approve-cell"><?php echo maha_ar_pretty_cash_approve_cell_html($row['ManagerStatus'] ?? 0, $mgrName, $row['ApproveDate'] ?? '', $noManager); ?></td>
                <td class="maha-ar-approve-cell"><?php echo maha_ar_emp_expense_bh_approve_cell_html($row); ?></td>
                <td class="maha-ar-approve-cell"><?php echo maha_ar_emp_expense_account_approve_cell_html($row); ?></td>
                <?php if (!empty($opts['admin_action'])) {
                    maha_ar_emp_expense_admin_action_cell($row, isset($opts['admin_action_page']) ? $opts['admin_action_page'] : 'ho');
                } else { ?>
                <td class="maha-ar-approve-cell"><?php echo maha_ar_pretty_cash_approve_cell_html($row['AdminStatus'] ?? 0, $adminName, $row['AdminApproveDate'] ?? ''); ?></td>
                <?php } ?>
                <td class="narration-cell"><?php echo maha_ar_esc($row['Narration'] ?? ''); ?></td>
            </tr>
        <?php
    }
}

if (!function_exists('maha_ar_emp_expense_request_datatable_init')) {
    function maha_ar_emp_expense_request_datatable_init($orderCol = 0, $photoCol = 3, array $moneyCols = array(4, 5), $narrationCol = 10, $excelFilename = 'employee_expenses')
    {
        $moneyColsJson = json_encode(array_values($moneyCols));
        ?>
<script type="text/javascript">
$(document).ready(function() {
    if ($('#example').length) {
        $('#example').DataTable({
            autoWidth: false,
            scrollX: false,
            dom: 'Bfrtip',
            order: [[<?php echo (int) $orderCol; ?>, 'desc']],
            buttons: ['excelHtml5'],
            columnDefs: [
                { targets: [<?php echo (int) $photoCol; ?>], orderable: false, searchable: false },
                { targets: <?php echo $moneyColsJson; ?>, className: 'text-money' },
                { targets: [<?php echo (int) $narrationCol; ?>], className: 'narration-cell' }
            ]
        });
    }
});
</script>
        <?php
    }
}

if (!function_exists('maha_ar_pretty_cash_request_table_styles')) {
    function maha_ar_pretty_cash_request_table_styles()
    {
        echo '<style>
.maha-wide-dt-wrap {
    overflow: visible !important;
}
.maha-wide-dt-wrap .dataTables_scrollHead,
.maha-wide-dt-wrap .dataTables_scrollFoot {
    display: none !important;
}
.maha-wide-dt-wrap .dataTables_scrollBody {
    overflow: visible !important;
}
.maha-wide-dt-wrap table.dataTable thead th,
.maha-wide-dt-wrap table.dataTable tbody td {
    vertical-align: middle !important;
}
.maha-pc-request-table .maha-ar-approve-cell { min-width: 130px; white-space: normal; vertical-align: middle !important; }
.maha-pc-request-table .maha-ar-approve-cell .approve-status { font-weight: 600; line-height: 1.3; }
.maha-pc-request-table .maha-ar-approve-cell .approve-by { font-size: 12px; color: #495057; line-height: 1.3; }
.maha-pc-request-table .maha-ar-approve-cell .approve-date { font-size: 11px; color: #6c757d; line-height: 1.3; }
.maha-pc-request-table .text-money { text-align: right !important; white-space: nowrap !important; font-variant-numeric: tabular-nums; }
.maha-pc-request-table .narration-cell { min-width: 180px; max-width: 280px; white-space: normal !important; word-break: break-word; }
.maha-pc-request-table .photo-cell { width: 56px; text-align: center !important; white-space: nowrap !important; }
.maha-pc-request-table .emp-name-cell { min-width: 160px; white-space: nowrap !important; }
</style>';
    }
}

if (!function_exists('maha_ar_pretty_cash_request_table_row')) {
    function maha_ar_pretty_cash_request_table_row(array $row)
    {
        $MgrName = trim((string) ($row['MgrName'] ?? ''));
        $AdminName = trim((string) ($row['AdminName'] ?? ''));
        $AccName = trim((string) ($row['AccName'] ?? ''));
        $EmpId = (int) ($row['UserId'] ?? 0);
        $sql88 = "SELECT SUM(creditAmt) As Credit,SUM(debitAmt) As Debit FROM (SELECT (case when Status='Cr' then sum(Amount) else 0 end) as creditAmt,(case when Status='Dr' then sum(Amount) else 0 end) as debitAmt FROM wallet WHERE UserId='$EmpId' GROUP BY Status) as a";
        $row88 = getRecord($sql88);
        $WalletBal = (float)($row88['Credit'] ?? 0) - (float)($row88['Debit'] ?? 0);
        $sql66 = "SELECT ExpApproval FROM tbl_users WHERE id='$EmpId'";
        $row66 = getRecord($sql66);
        $noManager = ((int) ($row66['ExpApproval'] ?? 0) === 1);
        ?>
            <tr>
                <td><?php echo maha_ar_id_link('petty_cash', (int) $row['id']); ?></td>
                <td><?php echo maha_ar_fmt_date($row['ExpenseDate'] ?? ''); ?></td>
                <td class="emp-name-cell"><a href="employee-hierarchy.php?id=<?php echo $EmpId; ?>" target="_blank"><?php echo maha_ar_esc(trim(($row['Fname'] ?? '') . ' ' . ($row['Lname'] ?? ''))); ?></a></td>
                <td class="photo-cell"><?php if (empty($row['Uphoto'])) { ?>
                  <img src="user_icon.jpg" class="d-block ui-w-40 rounded-circle mx-auto" style="width: 40px;height: 40px;">
                 <?php } elseif (file_exists('../uploads/' . $row['Uphoto'])) { ?>
                 <img src="../uploads/<?php echo maha_ar_esc($row['Uphoto']); ?>" class="d-block ui-w-40 rounded-circle mx-auto" alt="" style="width: 40px;height: 40px;">
                  <?php } else { ?>
                 <img src="user_icon.jpg" class="d-block ui-w-40 rounded-circle mx-auto" style="width: 40px;height: 40px;">
             <?php } ?></td>
                <td class="text-money">&#8377;<?php echo number_format((float) ($row['Amount'] ?? 0), 2); ?></td>
                <td class="text-money">&#8377;<?php echo number_format($WalletBal, 2); ?></td>
                <td class="maha-ar-approve-cell"><?php echo maha_ar_pretty_cash_approve_cell_html($row['ManagerStatus'] ?? 0, $MgrName, $row['MannagerApproveDate'] ?? '', $noManager); ?></td>
                <td class="maha-ar-approve-cell"><?php echo maha_ar_pretty_cash_approve_cell_html($row['AdminStatus'] ?? 0, $AdminName, $row['AdminApproveDate'] ?? ''); ?></td>
                <td class="maha-ar-approve-cell"><?php echo maha_ar_pretty_cash_approve_cell_html($row['AccStatus'] ?? 0, $AccName, $row['AccApproveDate'] ?? ''); ?></td>
                <td class="narration-cell"><?php echo maha_ar_esc($row['Narration'] ?? ''); ?></td>
            </tr>
        <?php
    }
}

if (!function_exists('maha_ar_pretty_cash_request_table_head')) {
    function maha_ar_pretty_cash_request_table_head()
    {
        ?>
            <thead>
            <tr>
                <th>Request Id</th>
                <th>Request Date</th>
                <th>Employee Name</th>
                <th>Photo</th>
                <th>Amount</th>
                <th>Wallet Balance</th>
                <th>Manager Approve</th>
                <th>Admin Approve</th>
                <th>Accountant Approve</th>
                <th>Narration</th>
            </tr>
            </thead>
        <?php
    }
}

if (!function_exists('maha_ar_pretty_cash_request_datatable_init')) {
    function maha_ar_pretty_cash_request_datatable_init()
    {
        ?>
<script type="text/javascript">
$(document).ready(function() {
    if ($('#example').length) {
        $('#example').DataTable({
            autoWidth: false,
            scrollX: false,
            dom: 'Bfrtip',
            order: [[0, 'desc']],
            buttons: ['excelHtml5'],
            columnDefs: [
                { targets: [3], orderable: false, searchable: false },
                { targets: [4, 5], className: 'text-money' },
                { targets: [9], className: 'narration-cell' }
            ]
        });
    }
});
</script>
        <?php
    }
}
