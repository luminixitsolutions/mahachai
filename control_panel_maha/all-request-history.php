<?php
session_start();
include_once 'config.php';
include_once 'auth.php';
include_once 'includes/all-requests-view-helpers.php';

$user_id = $_SESSION['Admin']['id'];
$MainPage = 'All-Requests';
$Page = 'All-Request-History';

$type = isset($_GET['type']) ? trim((string) $_GET['type']) : '';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$typeLabels = array(
    'employee_expense'   => 'Employee Expense',
    'petty_cash'         => 'Petty Cash Request',
    'vendor_expense'     => 'Vendor Expense',
    'nso_vendor_expense' => 'NSO Vendor Expense',
    'cash_book'          => 'Cash Book Request',
    'attendance'         => 'Attendance Request',
    'leave'              => 'Leave Request',
    'advance'            => 'Advance Request',
    'resign'             => 'Resign Request',
);

$summary = array();
$steps = array();
$detailHref = '';
$pageTitle = 'Request History';
$error = '';

function maha_ar_history_step($level, $status, $byName, $date, $comment = '', $amount = '')
{
    return array(
        'level'   => $level,
        'status'  => (string) $status,
        'by'      => trim((string) $byName),
        'date'    => $date,
        'comment' => trim((string) $comment),
        'amount'  => $amount,
    );
}

if ($id < 1 || !isset($typeLabels[$type])) {
    $error = 'Invalid request. Please open history from a valid request ID.';
} else {
    switch ($type) {
        case 'employee_expense':
            $sql = "SELECT te.*, tu.Fname, tu.Lname, tu.UnderByUser, tu2.Fname AS MgrName, tu3.Fname AS AccName,
                    tu4.Fname AS AccountName, tu5.Fname AS BhFname, tu5.Lname AS BhLname
                    FROM tbl_expense_request te
                    INNER JOIN tbl_users tu ON tu.id = te.UserId
                    LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
                    LEFT JOIN tbl_users tu3 ON tu3.id = te.AccBy
                    LEFT JOIN tbl_users tu4 ON tu4.id = te.AccountBy
                    LEFT JOIN tbl_users tu5 ON tu5.id = te.BhBy
                    WHERE te.id='$id' LIMIT 1";
            $row = getRecord($sql);
            if (empty($row['id'])) {
                $error = 'Employee expense request not found.';
                break;
            }
            $pageTitle = 'Employee Expense History #' . $id;
            $detailHref = 'employee-expense-details.php?id=' . $id;
            $summary = array(
                'Request ID'     => $row['id'],
                'Employee'       => trim($row['Fname'] . ' ' . $row['Lname']),
                'Expense Date'   => maha_ar_fmt_date($row['ExpenseDate']),
                'Amount'         => $row['Amount'],
                'Narration'      => $row['Narration'],
            );
            $bhName = trim(($row['BhFname'] ?? '') . ' ' . ($row['BhLname'] ?? ''));
            if (!in_array((int) ($row['UnderByUser'] ?? 0), array(5, 384, 415), true)) {
                $steps[] = maha_ar_history_step('Manager', $row['ManagerStatus'], $row['MgrName'], $row['ApproveDate'], $row['MannagerComment'] ?? '', $row['MgrAmount'] ?? '');
            }
            $steps[] = maha_ar_history_step('Business Head', $row['BhStatus'] ?? 0, $bhName, $row['BhApproveDate'] ?? '', $row['BhComment'] ?? '', $row['BhAmount'] ?? '');
            if (($row['Gst'] ?? '') === 'Yes') {
                $steps[] = maha_ar_history_step('Accountant', $row['AccountStatus'] ?? 0, $row['AccountName'] ?? '', $row['AccountApproveDate'] ?? '', $row['AccountComment'] ?? '', $row['AccountAmount'] ?? '');
            }
            $steps[] = maha_ar_history_step('Admin', $row['AdminStatus'], $row['AccName'], $row['AdminApproveDate'], $row['AdminComment'] ?? '', $row['AccAmount'] ?? '');
            break;

        case 'petty_cash':
            $sql = "SELECT te.*, tu.Fname, tu.Lname, tu2.Fname AS MgrName, tu3.Fname AS AdminName, tu4.Fname AS AccName
                    FROM tbl_prettycash_request te
                    INNER JOIN tbl_users tu ON tu.id = te.UserId
                    LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
                    LEFT JOIN tbl_users tu3 ON tu3.id = te.AdminBy
                    LEFT JOIN tbl_users tu4 ON tu4.id = te.AccBy
                    WHERE te.id='$id' LIMIT 1";
            $row = getRecord($sql);
            if (empty($row['id'])) {
                $error = 'Petty cash request not found.';
                break;
            }
            $pageTitle = 'Petty Cash History #' . $id;
            $summary = array(
                'Request ID'   => $row['id'],
                'Employee'     => trim($row['Fname'] . ' ' . $row['Lname']),
                'Request Date' => maha_ar_fmt_date($row['ExpenseDate']),
                'Amount'       => $row['Amount'],
                'Narration'    => $row['Narration'],
            );
            $steps[] = maha_ar_history_step('Manager', $row['ManagerStatus'], $row['MgrName'], $row['MannagerApproveDate'] ?? '', $row['MannagerComment'] ?? '', $row['MannagerAmount'] ?? '');
            $steps[] = maha_ar_history_step('Admin', $row['AdminStatus'], $row['AdminName'], $row['AdminApproveDate'] ?? '', $row['AdminComment'] ?? '', $row['AdminAmount'] ?? '');
            $steps[] = maha_ar_history_step('Accountant', $row['AccStatus'], $row['AccName'], $row['AccApproveDate'] ?? '', $row['AccComment'] ?? '', $row['AccAmount'] ?? '');
            break;

        case 'vendor_expense':
            $sql = "SELECT te.*, tu.Fname, tu.Lname, tu2.Fname AS MgrName, tu3.Fname AS VedName, tu4.Fname AS BdmName,
                    tu5.Fname AS PurchaseName, tu6.Fname AS AccName, tub.ShopName
                    FROM tbl_vendor_expenses te
                    LEFT JOIN tbl_users tu ON tu.id = te.UserId
                    LEFT JOIN tbl_users tu3 ON tu3.id = te.VedId
                    LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
                    LEFT JOIN tbl_users tu4 ON tu4.id = te.BdmBy
                    LEFT JOIN tbl_users tu5 ON tu5.id = te.PurchaseBy
                    LEFT JOIN tbl_users tu6 ON tu6.id = te.AccBy
                    LEFT JOIN tbl_users_bill tub ON tub.id = te.Locations
                    WHERE te.id='$id' LIMIT 1";
            $row = getRecord($sql);
            if (empty($row['id'])) {
                $error = 'Vendor expense request not found.';
                break;
            }
            $pageTitle = 'Vendor Expense History #' . $id;
            $summary = array(
                'Expense ID'   => $row['id'],
                'Uploaded By'  => trim(($row['Fname'] ?? '') . ' ' . ($row['Lname'] ?? '')),
                'Vendor'       => $row['VedName'] ?? '',
                'Location'     => $row['ShopName'] ?? '',
                'Expense Date' => maha_ar_fmt_date($row['ExpenseDate']),
                'Amount'       => $row['Amount'],
                'Narration'    => $row['Narration'],
            );
            $steps[] = maha_ar_history_step('BDM', $row['BdmStatus'], $row['BdmName'], $row['BdmApproveDate'] ?? '', '', $row['BdmAmount'] ?? '');
            if (($row['TrustedVendor'] ?? '') !== 'Yes') {
                $steps[] = maha_ar_history_step('Purchase Dept', $row['PurchaseStatus'], $row['PurchaseName'], $row['PurchaseApproveDate'] ?? '', '', $row['PurchaseAmount'] ?? '');
            }
            $steps[] = maha_ar_history_step('Accountant', $row['ManagerStatus'], $row['MgrName'], $row['ApproveDate'] ?? '', '', $row['MgrAmount'] ?? '');
            if (($row['TrustedVendor'] ?? '') !== 'Yes') {
                $steps[] = maha_ar_history_step('Admin', $row['AdminStatus'], $row['AccName'], $row['AdminApproveDate'] ?? '', $row['AdminComment'] ?? '', $row['AccAmount'] ?? '');
            }
            break;

        case 'nso_vendor_expense':
            $sql = "SELECT te.*, tu.Fname, tu.Lname, tu2.Fname AS MgrName, tu3.Fname AS VedName, tu4.Fname AS BdmName,
                    tu5.Fname AS PurchaseName, tu6.Fname AS AccName, tub.ShopName
                    FROM tbl_nso_vendor_expenses te
                    INNER JOIN tbl_users tu ON tu.id = te.UserId
                    LEFT JOIN tbl_users tu3 ON tu3.id = te.VedId
                    LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
                    LEFT JOIN tbl_users tu4 ON tu4.id = te.BdmBy
                    LEFT JOIN tbl_users tu5 ON tu5.id = te.PurchaseBy
                    LEFT JOIN tbl_users tu6 ON tu6.id = te.AccBy
                    LEFT JOIN tbl_users_bill tub ON tub.id = te.Locations
                    WHERE te.id='$id' LIMIT 1";
            $row = getRecord($sql);
            if (empty($row['id'])) {
                $error = 'NSO vendor expense request not found.';
                break;
            }
            $pageTitle = 'NSO Vendor Expense History #' . $id;
            $summary = array(
                'Expense ID'   => $row['id'],
                'Uploaded By'  => trim($row['Fname'] . ' ' . $row['Lname']),
                'Vendor'       => $row['VedName'] ?? '',
                'Location'     => $row['ShopName'] ?? '',
                'Expense Date' => maha_ar_fmt_date($row['ExpenseDate']),
                'Amount'       => $row['Amount'],
                'Narration'    => $row['Narration'],
            );
            $steps[] = maha_ar_history_step('BDM', $row['BdmStatus'], $row['BdmName'], $row['BdmApproveDate'] ?? '', '', $row['BdmAmount'] ?? '');
            $steps[] = maha_ar_history_step('Purchase Dept', $row['PurchaseStatus'], $row['PurchaseName'], $row['PurchaseApproveDate'] ?? '', '', $row['PurchaseAmount'] ?? '');
            $steps[] = maha_ar_history_step('Accountant', $row['ManagerStatus'], $row['MgrName'], $row['ApproveDate'] ?? '', '', $row['MgrAmount'] ?? '');
            $steps[] = maha_ar_history_step('Admin', $row['AdminStatus'], $row['AccName'], $row['AdminApproveDate'] ?? '', $row['AdminComment'] ?? '', $row['AccAmount'] ?? '');
            break;

        case 'cash_book':
            $sql = "SELECT tcb.*, tu.ShopName, tu.Phone, tu2.Fname AS ApproveByName
                    FROM tbl_cash_book tcb
                    INNER JOIN tbl_users tu ON tu.id = tcb.FrId
                    LEFT JOIN tbl_users tu2 ON tu2.id = tcb.ApproveBy
                    WHERE tcb.id='$id' LIMIT 1";
            $row = getRecord($sql);
            if (empty($row['id'])) {
                $error = 'Cash book request not found.';
                break;
            }
            $pageTitle = 'Cash Book History #' . $id;
            $summary = array(
                'Cash Book ID'  => $row['id'],
                'Franchise'     => $row['ShopName'],
                'Mobile'        => $row['Phone'],
                'Amount'        => $row['Amount'],
                'Transfer Date' => maha_ar_fmt_date($row['TransferDate']),
                'Narration'     => $row['Narration'],
            );
            $steps[] = maha_ar_history_step('Admin Approval', $row['ApproveStatus'], $row['ApproveByName'] ?? 'Admin', $row['ApproveDate'] ?? '', $row['ApproveComment'] ?? '', $row['Amount']);
            break;

        case 'attendance':
            $sql = "SELECT te.*, tu.Fname, tu.Lname, tu2.Fname AS MgrName, tu3.Fname AS HrName
                    FROM tbl_attendance_request te
                    INNER JOIN tbl_users tu ON tu.id = te.UserId
                    LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
                    LEFT JOIN tbl_users tu3 ON tu3.id = te.HrBy
                    WHERE te.id='$id' LIMIT 1";
            $row = getRecord($sql);
            if (empty($row['id'])) {
                $error = 'Attendance request not found.';
                break;
            }
            $pageTitle = 'Attendance History #' . $id;
            $summary = array(
                'Request ID'   => $row['id'],
                'Employee'     => trim($row['Fname'] . ' ' . $row['Lname']),
                'Request Date' => maha_ar_fmt_date($row['ReqDate']),
                'In Date/Time' => maha_ar_fmt_date($row['InDate'] ?? '') . ' ' . ($row['InTime'] ?? ''),
                'Out Date/Time'=> maha_ar_fmt_date($row['OutDate'] ?? '') . ' ' . ($row['OutTime'] ?? ''),
                'Type'         => $row['AttType'] ?? '',
            );
            $steps[] = maha_ar_history_step('Manager', $row['ManagerStatus'], $row['MgrName'], $row['MannagerApproveDate'] ?? '', $row['MannagerComment'] ?? '');
            $steps[] = maha_ar_history_step('HR', $row['HrStatus'], $row['HrName'] ?? 'HR', $row['HrApproveDate'] ?? '', $row['HrComment'] ?? '');
            break;

        case 'leave':
            $sql = "SELECT te.*, tu.Fname, tu.Lname, tu2.Fname AS MgrName, tu3.Fname AS HrName
                    FROM tbl_leave_request te
                    INNER JOIN tbl_users tu ON tu.id = te.UserId
                    LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
                    LEFT JOIN tbl_users tu3 ON tu3.id = te.HrBy
                    WHERE te.id='$id' LIMIT 1";
            $row = getRecord($sql);
            if (empty($row['id'])) {
                $error = 'Leave request not found.';
                break;
            }
            $pageTitle = 'Leave History #' . $id;
            $summary = array(
                'Request ID' => $row['id'],
                'Employee'   => trim($row['Fname'] . ' ' . $row['Lname']),
                'From Date'  => maha_ar_fmt_date($row['FromDate']),
                'To Date'    => maha_ar_fmt_date($row['ToDate']),
                'Total Days' => $row['TotDays'] ?? '',
                'Reason'     => $row['Narration'] ?? '',
            );
            $steps[] = maha_ar_history_step('Manager', $row['ManagerStatus'], $row['MgrName'], $row['MannagerApproveDate'] ?? '', $row['MannagerComment'] ?? '');
            $steps[] = maha_ar_history_step('HR', $row['HrStatus'], $row['HrName'] ?? 'HR', $row['HrApproveDate'] ?? '', $row['HrComment'] ?? '');
            break;

        case 'advance':
            $sql = "SELECT te.*, tu.Fname, tu.Lname, tu2.Fname AS MgrName, tu3.Fname AS HrName
                    FROM tbl_advance_salary te
                    INNER JOIN tbl_users tu ON tu.id = te.UserId
                    LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
                    LEFT JOIN tbl_users tu3 ON tu3.id = te.HrBy
                    WHERE te.id='$id' LIMIT 1";
            $row = getRecord($sql);
            if (empty($row['id'])) {
                $error = 'Advance request not found.';
                break;
            }
            $pageTitle = 'Advance History #' . $id;
            $summary = array(
                'Request ID'    => $row['id'],
                'Employee'      => trim($row['Fname'] . ' ' . $row['Lname']),
                'Advance Date'  => maha_ar_fmt_date($row['AdvanceDate']),
                'Advance Amount'=> $row['AdvanceSalary'],
                'Narration'     => $row['Narration'],
            );
            $steps[] = maha_ar_history_step('Manager', $row['ManagerStatus'], $row['MgrName'], $row['MannagerApproveDate'] ?? '', $row['MannagerComment'] ?? '');
            $steps[] = maha_ar_history_step('HR', $row['HrStatus'], $row['HrName'] ?? 'HR', $row['HrApproveDate'] ?? '', $row['HrComment'] ?? '');
            break;

        case 'resign':
            $sql = "SELECT te.*, tu.Fname, tu.Lname, tu.CustomerId, tu4.ShopName, tu2.Fname AS MgrName, tu3.Fname AS HrName
                    FROM tbl_resign_request te
                    INNER JOIN tbl_users tu ON tu.id = te.UserId
                    LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
                    LEFT JOIN tbl_users tu3 ON tu3.id = te.HrBy
                    LEFT JOIN tbl_users tu4 ON tu4.id = tu.UnderFrId
                    WHERE te.id='$id' LIMIT 1";
            $row = getRecord($sql);
            if (empty($row['id'])) {
                $error = 'Resign request not found.';
                break;
            }
            $pageTitle = 'Resign History #' . $id;
            $summary = array(
                'Request ID'      => $row['id'],
                'Employee'        => trim($row['Fname'] . ' ' . $row['Lname']),
                'Employee Code'   => $row['CustomerId'] ?? '',
                'Location'        => $row['ShopName'] ?? '',
                'Request Date'    => maha_ar_fmt_date($row['ReqDate']),
                'Last Working Day'=> maha_ar_fmt_date($row['LastWorkingDay'] ?? ''),
                'Reason'          => $row['Narration'] ?? '',
            );
            $steps[] = maha_ar_history_step('Manager', $row['ManagerStatus'], $row['MgrName'], $row['MannagerApproveDate'] ?? '', $row['MannagerComment'] ?? '');
            $steps[] = maha_ar_history_step('HR', $row['HrStatus'], $row['HrName'] ?? 'HR', $row['HrApproveDate'] ?? '', $row['HrComment'] ?? '');
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="default-style">
<head>
<title><?php echo maha_ar_esc($Proj_Title); ?> - <?php echo maha_ar_esc($pageTitle); ?></title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<?php include_once 'header_script.php'; ?>
</head>
<body>
<div class="layout-wrapper layout-1 layout-without-sidenav">
<div class="layout-inner">
<?php include_once 'top_header.php'; include_once 'sidebar.php'; ?>
<div class="layout-container">
<div class="layout-content">
<div class="container-fluid flex-grow-1 container-p-y">
<h4 class="font-weight-bold py-3 mb-0"><?php echo maha_ar_esc($pageTitle); ?></h4>
<p class="text-muted mb-3"><?php echo maha_ar_esc($typeLabels[$type] ?? ''); ?> — view-only approval history</p>

<?php if ($error !== '') { ?>
<div class="alert alert-danger"><?php echo maha_ar_esc($error); ?></div>
<?php } else { ?>

<div class="card mb-3">
    <div class="card-body">
        <h5 class="mb-3">Request Summary</h5>
        <div class="row">
            <?php foreach ($summary as $label => $value) { ?>
            <div class="col-md-4 col-sm-6 mb-2">
                <strong><?php echo maha_ar_esc($label); ?>:</strong>
                <?php echo maha_ar_esc($value); ?>
            </div>
            <?php } ?>
        </div>
        <?php if ($detailHref !== '') { ?>
        <div class="mt-2">
            <a href="<?php echo maha_ar_esc($detailHref); ?>" class="btn btn-sm btn-outline-primary" target="_blank">View Full Details</a>
        </div>
        <?php } ?>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Approval History (All Levels)</h5>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Level</th>
                        <th>Status</th>
                        <th>Action By</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Comment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($steps as $step) {
                        if ($step['level'] === '' || ($step['status'] === '' && $step['by'] === '' && $step['date'] === '')) {
                            continue;
                        }
                    ?>
                    <tr>
                        <td><?php echo maha_ar_esc($step['level']); ?></td>
                        <td><?php echo maha_ar_status_html($step['status'], 'Pending', $step['by'], $step['date'], $step['comment']); ?></td>
                        <td><?php echo maha_ar_esc($step['by'] !== '' ? $step['by'] : '—'); ?></td>
                        <td><?php echo maha_ar_esc(maha_ar_fmt_date($step['date']) ?: '—'); ?></td>
                        <td><?php echo maha_ar_esc($step['amount'] !== '' ? $step['amount'] : '—'); ?></td>
                        <td><?php echo maha_ar_esc($step['comment'] !== '' ? $step['comment'] : '—'); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php } ?>

<div class="mt-3">
    <a href="javascript:history.back();" class="btn btn-secondary">Back</a>
</div>

</div>
<?php include_once 'footer.php'; ?>
</div>
</div>
</div>
<div class="layout-overlay layout-sidenav-toggle"></div>
</div>
<?php include_once 'footer_script.php'; ?>
</body>
</html>
