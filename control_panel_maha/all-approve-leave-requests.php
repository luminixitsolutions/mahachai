<?php 
session_start();
include_once 'config.php';
include_once 'auth.php';
include_once 'includes/all-requests-view-helpers.php';
include_once 'includes/all-requests-sql-filters.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "All-Requests";
$Page = "All-Approve-Leave-Request";
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
</head>
<body>

 <div class="layout-wrapper layout-1 layout-without-sidenav">
<div class="layout-inner">

 <?php include_once 'top_header.php'; include_once 'sidebar.php'; ?>


<div class="layout-container">




<div class="layout-content">

<div class="container-fluid flex-grow-1 container-p-y">
<h4 class="font-weight-bold py-3 mb-0">All Approve Leave Request
  
</h4>

<div class="card" style="padding: 10px;">
<?php include_once 'includes/all-requests-date-filter.php'; ?>
<?php if ($arfShowTable) { ?>
<div class="card-datatable table-responsive maha-wide-dt-wrap">
<table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                
                 <th>Request Id</th>
                    <th>Request Date</th>
                     <th>Manager Approve</th>
                    <th>HR Approve</th>
                  
                 
               <th>Photo</th>
                <th>Employee Name</th>
               
               <th>Leave Reason</th>
             <th>From Date</th>
            
               <th>To Date</th>
              <th>Total Days</th>
                
                
               
                
               
            </tr>
        </thead>
        <tbody>
            <?php 
            $sql = "SELECT te.*,tu.Fname,tu.Lname,tu.Photo AS Uphoto,tu2.Fname AS MgrName,tu3.Fname AS HrName FROM tbl_leave_request te 
                INNER JOIN tbl_users tu ON tu.id=te.UserId 
                LEFT JOIN tbl_users tu2 ON tu2.id=te.MrgBy 
                LEFT JOIN tbl_users tu3 ON tu3.id=te.HrBy 
                WHERE " . maha_ar_sql_where('manager_hr', 'approve') . "
                AND te.ReqDate>='" . PENDING_EXPENSE_FROM_DATE . "'";
            $sql .= maha_ar_sql_append_date('ReqDate');
            $sql.=" ORDER BY te.ReqDate DESC";
            $res = $conn->query($sql);
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    maha_ar_leave_request_table_row($row);
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
<?php maha_ar_leave_request_datatable_init(); ?>
</body>
</html>
