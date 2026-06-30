<?php 
session_start();
include_once 'config.php';
include_once 'auth.php';
include_once 'includes/all-requests-view-helpers.php';
include_once 'includes/all-requests-sql-filters.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "All-Admin-Requests";
$Page = "Ho-Approve-Pretty-Cash-Request";
$allRequestsFilterMethod = 'request';
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
<?php maha_ar_pretty_cash_request_table_styles(); ?>
</head>
<body>

 <div class="layout-wrapper layout-1 layout-without-sidenav">
<div class="layout-inner">

 <?php include_once 'top_header.php'; include_once 'sidebar.php'; ?>


<div class="layout-container">




<div class="layout-content">

<div class="container-fluid flex-grow-1 container-p-y">
<h4 class="font-weight-bold py-3 mb-0">Admin Approve Pretty Cash Request
  
</h4>

<div class="card" style="padding: 10px;">
<?php include_once 'includes/all-requests-date-filter.php'; ?>
<?php if ($arfShowTable) { ?>
<div class="card-datatable table-responsive maha-wide-dt-wrap">
<table id="example" class="table table-striped table-bordered maha-pc-request-table" style="width:100%">
        <?php maha_ar_pretty_cash_request_table_head(); ?>
        <tbody>
            <?php 
            $sql = "SELECT te.*,tu.Fname,tu.Lname,tu.Photo AS Uphoto,tu2.Fname AS MgrName,tu3.Fname AS AdminName,tu4.Fname AS AccName FROM tbl_prettycash_request te 
                INNER JOIN tbl_users tu ON tu.id=te.UserId 
                LEFT JOIN tbl_users tu2 ON tu2.id=te.MrgBy 
                LEFT JOIN tbl_users tu3 ON tu3.id=te.AdminBy 
                LEFT JOIN tbl_users tu4 ON tu4.id=te.AccBy 
                WHERE " . maha_ar_sql_where('petty_cash', 'approve', 'te', 'accountant') . "
                AND te.ExpenseDate>='" . PENDING_EXPENSE_FROM_DATE . "'";
            $sql .= maha_ar_sql_append_date('ExpenseDate');
            $sql .= " ORDER BY te.ExpenseDate DESC";
            $res = $conn->query($sql);
            while ($row = $res->fetch_assoc()) {
                maha_ar_pretty_cash_request_table_row($row);
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
<?php maha_ar_pretty_cash_request_datatable_init(); ?>
</body>
</html>
