<?php 
session_start();
include_once 'config.php';
include_once 'auth.php';
include_once 'includes/all-requests-view-helpers.php';
include_once 'includes/all-requests-sql-filters.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "All-Requests";
$Page = "All-Reject-Expenses";
$allRequestsRequireSearch = true;
?>
<!DOCTYPE html>
<html lang="en" class="default-style">
<head>
<title><?php echo $Proj_Title; ?> </title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="description" content="" />
<meta name="keywords" content="">
<meta name="author" content="" />
<?php include_once 'header_script.php'; ?>
<style>
.maha-wide-dt-wrap { overflow: visible !important; }
.maha-wide-dt-wrap .dataTables_scrollHead,
.maha-wide-dt-wrap .dataTables_scrollFoot { display: none !important; }
.maha-wide-dt-wrap .dataTables_scrollBody { overflow: visible !important; }
.maha-pc-request-table .maha-ar-approve-cell { min-width: 130px; white-space: normal; vertical-align: middle !important; }
.maha-pc-request-table .text-money { text-align: right !important; white-space: nowrap !important; }
.maha-pc-request-table .narration-cell { min-width: 180px; white-space: normal !important; word-break: break-word; }
</style>
</head>
<body>

 <div class="layout-wrapper layout-1 layout-without-sidenav">
<div class="layout-inner">

 <?php include_once 'top_header.php'; include_once 'sidebar.php'; ?>


<div class="layout-container">

<div class="layout-content">

<div class="container-fluid flex-grow-1 container-p-y">
<h4 class="font-weight-bold py-3 mb-0">All Reject Employee Expense Request
  
</h4>

<div class="card" style="padding: 10px;">
<?php include_once 'includes/all-requests-date-filter.php'; ?>
<?php if ($arfShowTable) { ?>
<div class="card-datatable table-responsive maha-wide-dt-wrap">
<table id="example" class="table table-striped table-bordered maha-pc-request-table" style="width:100%">
        <thead>
            <tr>
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
        <tbody>
            <?php 
            $sql = "SELECT te.*, tu.Fname, tu.Lname, tu.UnderByUser, tu.Photo AS Uphoto,
                tu2.Fname AS MgrName, tu3.Fname AS AccName, tu4.Fname AS AccountName,
                tu5.Fname AS BhFname, tu5.Lname AS BhLname
                FROM tbl_expense_request te
                INNER JOIN tbl_users tu ON tu.id = te.UserId
                LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
                LEFT JOIN tbl_users tu3 ON tu3.id = te.AccBy
                LEFT JOIN tbl_users tu4 ON tu4.id = te.AccountBy
                LEFT JOIN tbl_users tu5 ON tu5.id = te.BhBy
                WHERE " . maha_ar_sql_where('employee_expense', 'reject') . "
                AND te.ExpenseDate >= '" . PENDING_EXPENSE_FROM_DATE . "'";
            $sql .= maha_ar_sql_append_date('ExpenseDate');
            $sql .= " ORDER BY te.ExpenseDate DESC, te.id DESC";
            $res = $conn->query($sql);
            if (!$res) {
                $sql = "SELECT te.*, tu.Fname, tu.Lname, tu.UnderByUser, tu.Photo AS Uphoto,
                tu2.Fname AS MgrName, tu3.Fname AS AccName, tu4.Fname AS AccountName
                FROM tbl_expense_request te
                INNER JOIN tbl_users tu ON tu.id = te.UserId
                LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
                LEFT JOIN tbl_users tu3 ON tu3.id = te.AccBy
                LEFT JOIN tbl_users tu4 ON tu4.id = te.AccountBy
                WHERE te.UserId != 0 AND (
                    te.ManagerStatus IN ('2', 2) OR te.AdminStatus IN ('2', 2)
                    OR (te.Gst = 'Yes' AND te.AccountStatus IN ('2', 2))
                )
                AND te.ExpenseDate >= '" . PENDING_EXPENSE_FROM_DATE . "'";
                $sql .= maha_ar_sql_append_date('ExpenseDate');
                $sql .= " ORDER BY te.ExpenseDate DESC, te.id DESC";
                $res = $conn->query($sql);
            }
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $UnderByUser = isset($row['UnderByUser']) ? $row['UnderByUser'] : null;
                    $empId = (int) $row['UserId'];
                    $photoPath = '../uploads/' . (isset($row['Uphoto']) ? $row['Uphoto'] : '');
            ?>
            <tr>
                <td><?php echo maha_ar_id_link('employee_expense', (int) $row['id']); ?></td>
                <td><?php echo maha_ar_fmt_date(isset($row['ExpenseDate']) ? $row['ExpenseDate'] : ''); ?></td>
                <td><a href="employee-hierarchy.php?id=<?php echo $empId; ?>" target="_blank"><?php echo maha_ar_esc(trim($row['Fname'] . ' ' . $row['Lname'])); ?></a></td>
                <td><?php if (empty($row['Uphoto'])) { ?>
                    <img src="user_icon.jpg" class="d-block ui-w-40 rounded-circle" style="width:40px;height:40px;">
                    <?php } elseif (file_exists($photoPath)) { ?>
                    <img src="<?php echo maha_ar_esc($photoPath); ?>" class="d-block ui-w-40 rounded-circle" alt="" style="width:40px;height:40px;">
                    <?php } else { ?>
                    <img src="user_icon.jpg" class="d-block ui-w-40 rounded-circle" style="width:40px;height:40px;">
                    <?php } ?></td>
                <td class="text-money"><?php echo maha_ar_esc($row['Amount']); ?></td>
                <td class="text-money"><?php echo maha_ar_esc(isset($row['AccAmount']) ? $row['AccAmount'] : ''); ?></td>
                <?php maha_ar_emp_expense_manager_cell($row, $UnderByUser); ?>
                <?php maha_ar_emp_expense_bh_cell($row); ?>
                <?php maha_ar_emp_expense_account_cell($row); ?>
                <?php maha_ar_emp_expense_admin_cell($row); ?>
                <td class="narration-cell"><?php echo maha_ar_esc(isset($row['Narration']) ? $row['Narration'] : ''); ?></td>
            </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table>
</div>
<?php } ?>
</div>
</div>

<?php include_once 'footer.php'; ?>

</div>

</div>

</div>

<div class="layout-overlay layout-sidenav-toggle"></div>
</div>


<?php include_once 'footer_script.php'; ?>
<script type="text/javascript">
$(document).ready(function() {
    if ($('#example').length) {
        $('#example').DataTable({
            scrollX: false,
            order: [[0, 'desc']],
            dom: 'Bfrtip',
            buttons: ['excelHtml5']
        });
    }
    var loader = document.getElementById('page-loader');
    if (loader) {
        loader.classList.add('hidden');
        setTimeout(function () {
            if (loader.parentNode) {
                loader.parentNode.removeChild(loader);
            }
        }, 300);
    }
});
</script>
</body>
</html>
