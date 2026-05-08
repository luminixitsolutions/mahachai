<?php include 'loader.php';

$appName = 'control_panel_maha'; // or 'billsoftappadmin2025'
$sessionId = session_id();
$conn->query("DELETE FROM tbl_active_admin WHERE session_id='$user_id'");
$conn->query("INSERT INTO tbl_active_admin (app_name, session_id, last_login)
              VALUES ('$appName', '$user_id', NOW())");

$sql = "SELECT * FROM tbl_active_admin WHERE app_name='$appName' AND session_id='$user_id' LIMIT 1";
$rncnt = getRow($sql);
if($rncnt>0){}else{
   unset($_SESSION['UserId']);
unset($_SESSION['Admin']);
unset($_SESSION['Roll']);?>
<script language="javascript">
window.location.href="index.php";
</script>
<?php }
?>

<!-- YOUR PAGE HTML / PHP STARTS BELOW -->

<div class="sidenav bg-dark">
<div id="layout-sidenav" class=" <!--container--> layout-sidenav-horizontal sidenav-horizontal flex-grow-0 bg-dark" style="padding-left:15px;padding-right:15px;">
     <div class="app-brand demo">
                    <span class="app-brand-logo demo">
                        <a href="dashboard.php"><img src="logo5.png" alt="Brand Logo" class="img-fluid" style="width: 60px;"></a> 
                    </span>
                    <h3 style="font-size:18px; padding-left:10px;padding-top:10px;">Maha Chai</h3>
                   <!-- <a href="dashboard.php" class="app-brand-text demo sidenav-text font-weight-normal ml-2"><?php echo $Proj_Title; ?></a>-->
                    <a href="javascript:" class="layout-sidenav-toggle sidenav-link text-large ml-auto">
                        <i class="ion ion-md-menu align-middle"></i>
                    </a>
                </div>
                <div class="sidenav-divider mt-0"></div>
    <ul class="sidenav-inner">
       <?php if(in_array("108", $Options) || in_array("109", $Options) || in_array("110", $Options) || in_array("111", $Options) || in_array("112", $Options) || in_array("113", $Options)) {?>
        <li class="sidenav-item <?php if($MainPage=='Pretty-Cash-Ho') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Company Dashboard</div>
</a>
<ul class="sidenav-menu">
            <?php if(in_array("108", $Options)){?>
             <li class="sidenav-item">
<a href="sales-dashboard.php" class="sidenav-link">
<div>Sales Dashboard </div>
<?php if($Page=='Weekly-Sale-Report-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
         <?php } if(in_array("122", $Options)){?>
         <li class="sidenav-item">
            <a href="feedback-dashboard.php" class="sidenav-link">
                
                <div>Feedback Dashboard </div>
                <?php if($Page=='Dashboard') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        
        <li class="sidenav-item">
            <a href="invoice-payment-mode-dashboard.php" class="sidenav-link">
                <div>Payment mode change dashboard</div>
                <?php if(isset($Page) && $Page=='Payment-Mode-Dashboard') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
           
<?php }  if(in_array("129", $Options)){?>
 <li class="sidenav-item">
            <a href="chai-pass-dashboard.php" class="sidenav-link">
                
                <div>Chai Pass Dashboard </div>
                <?php if($Page=='') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
<?php }  if(in_array("109", $Options)){?>
 <li class="sidenav-item">
            <a href="hr-dashboard.php" class="sidenav-link">
                
                <div>HR Dashboard </div>
                <?php if($Page=='') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
      <?php }  if(in_array("110", $Options)){?>
         <li class="sidenav-item">
            <a href="account-dashboard.php" class="sidenav-link">
                
                <div>Account Dashboard  </div>
                <?php if($Page=='') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
       <?php }  if(in_array("111", $Options)){?>
<li class="sidenav-item">
            <a href="franchise-dashboard.php" class="sidenav-link">
                
                <div>Franchise Dashboard </div>
                <?php if($Page=='Employee-Dashboard') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        <?php }  if(in_array("112", $Options)){?>
        <li class="sidenav-item">
            <a href="project-dashboard.php" class="sidenav-link">
                
                <div>Project Dashboard </div>
                <?php if($Page=='Employee-Dashboard') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        <?php }  if(in_array("113", $Options)){?>
         <li class="sidenav-item">
            <a href="stock-dashboard.php?FromDate=<?php echo date('Y-m-d');?>&ToDate=<?php echo date('Y-m-d');?>" class="sidenav-link">
                
                <div>Stock Dashboard </div>
                <?php if($Page=='Dashboard') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
<?php } ?>
 
 </ul>
</li> 
       <?php } ?> 
        <li class="sidenav-item <?php if($MainPage=='Pretty-Cash-Ho') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Dashboard</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
            <a href="dashboard.php" class="sidenav-link">
                
                <div>Dashboard </div>
                <?php if($Page=='Dashboard') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
<?php if(in_array("1", $Options)) {?>
 <li class="sidenav-item">
            <a href="expense-sale-dashboard.php" class="sidenav-link">
                
                <div>Sub Zone Expense vs Sale</div>
                <?php if($Page=='') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        <?php } if(in_array("2", $Options)) {?>
         <li class="sidenav-item">
            <a href="expense-sale-dashboard.php?value=zone" class="sidenav-link">
                
                <div>Zone Expense vs Sale</div>
                <?php if($Page=='') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        <?php } if(in_array("3", $Options)) {?>
        <li class="sidenav-item">
<a href="expense-sale-report.php" class="sidenav-link">
<div>Franchise Expense vs Sale Report</div>
<?php if($Page=='Weekly-Sale-Report-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 <?php } if(in_array("95", $Options)) {?>
 <li class="sidenav-item">
            <a href="employee-dashboard.php" class="sidenav-link">
                
                <div>Employee Dashboard </div>
                <?php if($Page=='Employee-Dashboard') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
<?php } ?>
 
 </ul>
</li>  

<?php  if(in_array("115", $Options)) {?>

 <li class="sidenav-item <?php if($MainPage=='Manager-Vendor-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Update Attendance</div>
</a>
<ul class="sidenav-menu">
    
     <li class="sidenav-item">
            <a href="update-attendance.php" class="sidenav-link">
                
                <div>Update Attendance Single </div>
                <?php if($Page=='') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
 <li class="sidenav-item">
            <a href="update-attendance-mult.php" class="sidenav-link">
                
                <div>Update Attendance Multiple</div>
                <?php if($Page=='') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>

    
 </ul>
</li>   

<?php } if(in_array("126", $Options)) {?>
<li class="sidenav-item <?php if($MainPage=='Manager-Vendor-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Cofo Details Requests</div>
</a>
<ul class="sidenav-menu">
    
     <li class="sidenav-item">
            <a href="pending-cofo-details.php" class="sidenav-link">
                
                <div>Pending Cofo Details Requests </div>
                <?php if($Page=='') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
 <li class="sidenav-item">
            <a href="approved-cofo-details.php" class="sidenav-link">
                
                <div>Approve Cofo Details Requests</div>
                <?php if($Page=='') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
 <li class="sidenav-item">
            <a href="reject-cofo-details.php" class="sidenav-link">
                
                <div>Reject Cofo Details Requests</div>
                <?php if($Page=='') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
    
 </ul>
</li>   
        <?php } ?>
        <li class="sidenav-item">
            <a href="control-room-report.php" class="sidenav-link">
                
                <div>Control Room Report </div>
                <?php if($Page=='Employee-Dashboard') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        
        
         <?php  if(in_array("164", $Options)) {?>
        <li class="sidenav-item <?php if(isset($MainPage) && $MainPage=='YouTube-Videos') {?> active <?php } ?>">
<a href="youtube-videos.php" class="sidenav-link">
<div>Recipe SOP</div>
<?php if(isset($Page) && $Page=='YouTube-Videos-List') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
        <?php } if(in_array("133", $Options)) {?>
        <li class="sidenav-item">
            <a href="manpower-report.php" class="sidenav-link">
                
                <div>Manpower Report </div>
                <?php if($Page=='Employee-Dashboard') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        
<?php }  if(in_array("127", $Options)) {?>

<li class="sidenav-item">
            <a href="view_chai_pass.php" class="sidenav-link">
                
                <div>Chai Passes </div>
                <?php if($Page=='Employee-Dashboard') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        
<?php } if(in_array("101", $Options)) {?>
<!-- <li class="sidenav-item">
            <a href="admin-approve-emp-expenses.php" class="sidenav-link">
                
                <div>Admin Approve All Expenses </div>
                <?php if($Page=='Employee-Dashboard') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>-->
<?php } ?>

 <?php  if(in_array("99", $Options)) {?>
<li class="sidenav-item">
            <a href="aliance-upload-docs.php" class="sidenav-link">
                
                <div>Alliances Upload Documents</div>
                <?php if($Page=='Controll-Room') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
         <?php } if(in_array("100", $Options)) {?>
    <li class="sidenav-item <?php if($MainPage=='Manager-Vendor-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Company Policy</div>
</a>
<ul class="sidenav-menu">
    
     <li class="sidenav-item">
            <a href="add-company-policy.php" class="sidenav-link">
                <div>Add Company Policy</div>
                <?php if($Page=='All-Pending-Expenses') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        
 <li class="sidenav-item">
<a href="view-company-policy.php" class="sidenav-link">

<div>View Company Policy</div>
<?php if($Page=='Manager-Vendor-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

    
 </ul>
</li>   

    <li class="sidenav-item <?php if(isset($MainPage) && $MainPage=='LID') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">
<div>LID</div>
</a>
<ul class="sidenav-menu">
     <li class="sidenav-item">
            <a href="add-lid.php" class="sidenav-link">
                <div>Add LID</div>
                <?php if(isset($Page) && $Page=='Add-LID') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
 <li class="sidenav-item">
<a href="view-lid.php" class="sidenav-link">
<div>View LID</div>
<?php if(isset($Page) && $Page=='View-LID') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
 </ul>
</li>

 <?php } if(in_array("121", $Options)) {?>
    <li class="sidenav-item <?php if($MainPage=='Manager-Vendor-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Laptop Handover</div>
</a>
<ul class="sidenav-menu">
    
     <li class="sidenav-item">
            <a href="add-laptop-handover-details.php" class="sidenav-link">
                <div>Add Laptop Handover</div>
                <?php if($Page=='All-Pending-Expenses') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        
 <li class="sidenav-item">
<a href="view-laptop-handover.php" class="sidenav-link">

<div>View Laptop Handover</div>
<?php if($Page=='Manager-Vendor-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

    
 </ul>
</li>   
        
        <?php } if(in_array("117", $Options)) {?>
    <li class="sidenav-item <?php if($MainPage=='Manager-Vendor-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>COFO Details</div>
</a>
<ul class="sidenav-menu">
    
     <li class="sidenav-item">
            <a href="add-cofo-details.php" class="sidenav-link">
                <div>Add COFO Details</div>
                <?php if($Page=='All-Pending-Expenses') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        
 <li class="sidenav-item">
<a href="view-cofo-details.php" class="sidenav-link">

<div>View COFO Details</div>
<?php if($Page=='Manager-Vendor-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

    
 </ul>
</li>   

<?php } if(in_array("132", $Options)) {?>
    <li class="sidenav-item <?php if($MainPage=='Manager-Vendor-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>COFO/FICO Payout Report</div>
</a>
<ul class="sidenav-menu">
    
     <li class="sidenav-item">
            <a href="cofo-payout-report.php" class="sidenav-link">
                <div>COFO Payout Report</div>
                <?php if($Page=='All-Pending-Expenses') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        
 <li class="sidenav-item">
<a href="fico-payout-report.php" class="sidenav-link">

<div>FICO Payout Report</div>
<?php if($Page=='Manager-Vendor-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

    
 </ul>
</li>  
       
        <?php } if(in_array("4", $Options) || in_array("5", $Options) || in_array("6", $Options) || in_array("7", $Options) || in_array("8", $Options)) {?>
         <li class="sidenav-item <?php if($MainPage=='Petty-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Pending Requests</div>
</a>
<ul class="sidenav-menu">
    <?php if(in_array("4", $Options)) {?>
     <li class="sidenav-item">
            <a href="all-pending-expenses.php" class="sidenav-link">
                <div>All Pending Expenses</div>
                <?php if($Page=='All-Pending-Expenses') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        <?php } if(in_array("5", $Options)) {?>
        <li class="sidenav-item">
<a href="all-pending-pretty-cash-request.php" class="sidenav-link">
 
<div>All Pending Petty Cash Requests</div>
<?php if($Page=='All-Pending-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("6", $Options)) {?>
<li class="sidenav-item">
<a href="all-pending-vendor-exepense-request.php" class="sidenav-link">
<div>All Pending Vendor Expense Requests</div>
<?php if($Page=='All-Pending-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("7", $Options)) {?>
<li class="sidenav-item">
<a href="all-pending-nso-vendor-exepense-request.php" class="sidenav-link">
<div>All Pending NSO Vendor Expense Requests</div>
<?php if($Page=='All-Pending-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("8", $Options)) {?>
 <li class="sidenav-item">
            <a href="all-pending-attendance-request.php" class="sidenav-link">
                <div>All Pending Attendance Request</div>
                <?php if($Page=='All-Pending-Expenses') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        
        
        <?php } ?>
 </ul>
</li>  
<?php } if(in_array("89", $Options) || in_array("90", $Options) || in_array("91", $Options) || in_array("92", $Options) || in_array("93", $Options)) {?>

<li class="sidenav-item <?php if($MainPage=='Petty-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Approved Requests</div>
</a>
<ul class="sidenav-menu">
    <?php if(in_array("89", $Options)) {?>
     <li class="sidenav-item">
            <a href="all-approve-expenses.php?FromDate=<?php echo date('Y-m-d');?>&ToDate=<?php echo date('Y-m-d');?>&Search=Search" class="sidenav-link">
                <div>All Approve Expenses</div>
                <?php if($Page=='All-Pending-Expenses') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        <?php } if(in_array("90", $Options)) {?>
        <li class="sidenav-item">
<a href="all-approve-pretty-cash-request.php?FromDate=<?php echo date('Y-m-d');?>&ToDate=<?php echo date('Y-m-d');?>&Search=Search" class="sidenav-link">
 
<div>All Approved Petty Cash Requests</div>
<?php if($Page=='All-Pending-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("91", $Options)) {?>
<li class="sidenav-item">
<a href="all-approve-vendor-exepense-request.php?FromDate=<?php echo date('Y-m-d');?>&ToDate=<?php echo date('Y-m-d');?>&Search=Search" class="sidenav-link">
<div>All Approved Vendor Expense Requests</div>
<?php if($Page=='All-Pending-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("92", $Options)) {?>
<li class="sidenav-item">
<a href="all-approve-nso-vendor-exepense-request.php?FromDate=<?php echo date('Y-m-d');?>&ToDate=<?php echo date('Y-m-d');?>&Search=Search" class="sidenav-link">
<div>All Approved NSO Vendor Expense Requests</div>
<?php if($Page=='All-Pending-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("93", $Options)) {?>
 <li class="sidenav-item">
            <a href="all-approve-attendance-request.php?FromDate=<?php echo date('Y-m-d');?>&ToDate=<?php echo date('Y-m-d');?>&Search=Search" class="sidenav-link">
                <div>All Approved Attendance Request</div>
                <?php if($Page=='All-Pending-Expenses') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        
        
        <li class="sidenav-item">
            <a href="all-approve-leave-requests.php?FromDate=<?php echo date('Y-m-d');?>&ToDate=<?php echo date('Y-m-d');?>&Search=Search" class="sidenav-link">
                <div>All Approved Leave Request</div>
                <?php if($Page=='All-Pending-Expenses') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        <?php } ?>
 </ul>
</li>  
          <?php } ?> 

          <!-- Penalty Approval (BDM & Business Head) - Option IDs 153-158 -->
          <?php if(in_array("153", $Options)) {?>
          <li class="sidenav-item <?php if($MainPage=='Penalty-Approval') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>BDM Penalty Request</div>
</a>
<ul class="sidenav-menu">
<li class="sidenav-item">
<a href="penalty_bdm_approval.php" class="sidenav-link">
<div>BDM Penalty Request - Pending</div>
<?php if($Page=='Penalty-BDM-Pending') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="penalty_bdm_approved.php" class="sidenav-link">
<div>BDM Penalty Request - Approve</div>
<?php if($Page=='Penalty-BDM-Approved') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<li class="sidenav-item">
<a href="penalty_bdm_rejected.php" class="sidenav-link">
<div>BDM Penalty Request - Reject</div>
<?php if($Page=='Penalty-BDM-Rejected') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
 
 </ul>
</li>
<?php } ?>

          <?php if(in_array("154", $Options)) {?>
  <li class="sidenav-item <?php if($MainPage=='Penalty-Approval') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Business Head Penalty Request</div>
</a>
<ul class="sidenav-menu">
<li class="sidenav-item">
<a href="penalty_bh_approval.php" class="sidenav-link">
<div>Business Head Penalty Request - Pending</div>
<?php if($Page=='Penalty-BH-Pending') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="penalty_bh_approved.php" class="sidenav-link">
<div>Business Head Penalty Request - Approve</div>
<?php if($Page=='Penalty-BH-Approved') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="penalty_bh_rejected.php" class="sidenav-link">
<div>Business Head Penalty Request - Reject</div>
<?php if($Page=='Penalty-BH-Rejected') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
 </ul>
</li>
<?php } ?>

<?php if(in_array("159", $Options) || in_array("160", $Options)) { ?>
          <li class="sidenav-item <?php if($MainPage=='Idea-Box') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">
<div>Idea Box</div>
</a>
<ul class="sidenav-menu">
<?php if(in_array("159", $Options)) { ?>
<li class="sidenav-item">
<a href="idea-box-manager-requests.php" class="sidenav-link">
<div>Manager idea requests</div>
<?php if(isset($Page) && $Page=='Idea-Box-Manager') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>
<?php if(in_array("160", $Options)) { ?>
<li class="sidenav-item">
<a href="idea-box-admin-requests.php" class="sidenav-link">
<div>Admin idea requests</div>
<?php if(isset($Page) && $Page=='Idea-Box-Admin') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>
</ul>
</li>
<?php } ?>
            
            <?php if($Roll!=1){
            if(in_array("9", $Options) || in_array("12", $Options) || in_array("13", $Options) || in_array("15", $Options) || in_array("16", $Options)) { ?>
            <li class="sidenav-item <?php if($MainPage=='Petty-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Expense Management</div>
</a>
<ul class="sidenav-menu">
    <?php if(in_array("9", $Options)) {?>
     <li class="sidenav-item">
            <a href="all-employee-expenses.php" class="sidenav-link">
                <div>Employee Expenses</div>
                <?php if($Page=='All-Pending-Expenses') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        <?php } if(in_array("12", $Options)) {?>
        <li class="sidenav-item">
<a href="all-pretty-cash-expenses.php" class="sidenav-link">
 
<div>All Petty Cash Requests</div>
<?php if($Page=='All-Pending-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("13", $Options)) {?>
<li class="sidenav-item">
<a href="all-vendor-exepense-request.php" class="sidenav-link">
<div>All Vendor Expense Requests</div>
<?php if($Page=='All-Pending-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("15", $Options)) {?>
<li class="sidenav-item">
<a href="all-nso-vendor-exepense-request.php" class="sidenav-link">
<div>All NSO Vendor Expense Requests</div>
<?php if($Page=='All-Pending-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("16", $Options)) {?>
 <li class="sidenav-item">
            <a href="all-attendance-request.php" class="sidenav-link">
                <div>All Attendace Request</div>
                <?php if($Page=='All-Pending-Expenses') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        
        <li class="sidenav-item">
            <a href="all-pending-leave-requests.php" class="sidenav-link">
                <div>All Pending Leave Request</div>
                <?php if($Page=='All-Pending-Expenses') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        <?php } ?>
 </ul>
</li>  
        <?php } } if(in_array("17", $Options)) {?>
    <li class="sidenav-item <?php if($MainPage=='Manager-Vendor-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Account Vendor Expenses</div>
</a>
<ul class="sidenav-menu">
    
     <li class="sidenav-item">
            <a href="all-pending-account-vendor-expenses.php" class="sidenav-link">
                <div>All Pending Expenses</div>
                <?php if($Page=='All-Pending-Expenses') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        
 <li class="sidenav-item">
<a href="manager-vendor-pending-expense-request.php" class="sidenav-link">

<div>Pending Expense Request</div>
<?php if($Page=='Manager-Vendor-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="manager-vendor-approve-expense-request.php" class="sidenav-link">

<div>Approve Expense Requests</div>
<?php if($Page=='Manager-Vendor-Approve-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="manager-vendor-reject-expense-request.php" class="sidenav-link">

<div>Reject Expense Requests</div>
<?php if($Page=='Manager-Vendor-Reject-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

    
 </ul>
</li>   


 <?php } if(in_array("18", $Options)) {?>

 <li class="sidenav-item <?php if($MainPage=='Manager-Vendor-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Account NSO Vendor Expenses</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="manager-nso-vendor-pending-expense-request.php" class="sidenav-link">

<div>Pending Expense Request</div>
<?php if($Page=='Manager-Vendor-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="manager-nso-vendor-approve-expense-request.php" class="sidenav-link">

<div>Approve Expense Requests</div>
<?php if($Page=='Manager-Vendor-Approve-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="manager-nso-vendor-reject-expense-request.php" class="sidenav-link">

<div>Reject Expense Requests</div>
<?php if($Page=='Manager-Vendor-Reject-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>


    
 </ul>
</li>   
<?php } if(in_array("19", $Options) || in_array("87", $Options)) {?>
        <li class="sidenav-item <?php if($MainPage=='Cash-Book') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Cash Book</div>
</a>
<ul class="sidenav-menu">
    <?php if(in_array("85", $Options)) {?>
     <li class="sidenav-item">
<a href="add-cash-book.php" class="sidenav-link">

<div>Add Cash Book</div>
<?php if($Page=='Add-Cash-Book') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>

<li class="sidenav-item">
<a href="view-cash-book.php?FromDate=<?php echo date('Y-m-d');?>&ToDate=<?php echo date('Y-m-d');?>" class="sidenav-link"> 

<div>View Cash Book</div>
<?php if($Page=='View-Cash-Book') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>


 <li class="sidenav-item">
<a href="pending-cash-report.php" class="sidenav-link">

<div>Pending Cash Report</div>
<?php if($Page=='Franchise-Outstanding') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

    <li class="sidenav-item">
<a href="fr-bill-outstanding.php" class="sidenav-link">

<div>Cash And Online Collection Report</div>
<?php if($Page=='Franchise-Outstanding') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<?php if(in_array("87", $Options)) {?>
 <li class="sidenav-item">
<a href="pending-cash-book-request.php" class="sidenav-link">

<div>Pending Cash Book Request</div>
<?php if($Page=='Pending-Cash-Book-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="approve-cash-book-request.php" class="sidenav-link">

<div>Approved Cash Book Requests</div>
<?php if($Page=='Approve-Cash-Book-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>


<li class="sidenav-item">
<a href="reject-cash-book-request.php" class="sidenav-link">

<div>Rejected Cash Book Requests</div>
<?php if($Page=='Reject-Cash-Book-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    <?php } ?>
 </ul>
</li>   
        
<?php } if(in_array("20", $Options)) {?>

<li class="sidenav-item <?php if($MainPage=='Pretty-Cash-Ho') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Admin Petty Cash Request</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="ho-pending-pretty-cash-request.php" class="sidenav-link">

<div>Pending Petty Cash Request</div>
<?php if($Page=='Ho-Pending-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="ho-approve-pretty-cash-request.php" class="sidenav-link">

<div>Approved Petty Cash Requests</div>
<?php if($Page=='Ho-Approve-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="ho-reject-pretty-cash-request.php" class="sidenav-link">

<div>Rejected Petty Cash Requests</div>
<?php if($Page=='Ho-Reject-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>  

<?php } ?>

<li class="sidenav-item <?php if($MainPage=='Report-New') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">
<div>Expense Report</div>
</a>
<ul class="sidenav-menu"> 
<li class="sidenav-item">
<a href="vendor-expense-report.php" class="sidenav-link">
<div>Vendor Expense Report</div>
<?php if($Page=='Daily-Sale-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
</ul>
</li>

<?php  if(in_array("21", $Options) || in_array("22", $Options) || in_array("23", $Options) || in_array("24", $Options) || in_array("25", $Options) || in_array("26", $Options) || in_array("27", $Options)) {?>
     <li class="sidenav-item <?php if($MainPage=='Report-New') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Report New</div>
</a>
<ul class="sidenav-menu"> 
 <?php if(in_array("21", $Options)){?>
<li class="sidenav-item">
<a href="daily-sale-report.php" class="sidenav-link">
<div> Daily Sale Report</div>
<?php if($Page=='Daily-Sale-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
 <?php } if(in_array("23", $Options)){?>
 <li class="sidenav-item">
<a href="weekly-sale-report.php" class="sidenav-link">
<div> Weekly Sale report</div>
<?php if($Page=='Weekly-Sale-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
 <?php } if(in_array("25", $Options)){?>
 <li class="sidenav-item">
<a href="item-wise-sale-report.php" class="sidenav-link">
<div> Item Wise Sale Report</div>
<?php if($Page=='Item-Wise-Sale-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
 <li class="sidenav-item">
<a href="petty-cash-inventory-report.php" class="sidenav-link">
<div> Petty Cash Inventory Report</div>
<?php if($Page=='Petty-Cash-Inventory-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
 <?php } if(in_array("26", $Options)){?>
 <li class="sidenav-item">
<a href="expenses-report.php" class="sidenav-link">
<div> Expense Report</div>
<?php if($Page=='Expense-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
 <?php } if(in_array("27", $Options)){?>
 <li class="sidenav-item">
<a href="expense-summary-report.php" class="sidenav-link">
<div> Expense Summary Report</div>
<?php if($Page=='Expense-Summary-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 <?php } if(in_array("22", $Options)){?>
 <li class="sidenav-item">
<a href="daily-sale-report-2.php" class="sidenav-link">
<div> Daily Sale Report </div>
<?php if($Page=='Daily-Sale-Report-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
 <?php } if(in_array("24", $Options)){?>
 <li class="sidenav-item">
<a href="weekly-sale-report-2.php" class="sidenav-link">
<div> Weekly Sale Report </div>
<?php if($Page=='Weekly-Sale-Report-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 <li class="sidenav-item">
<a href="vendor-expense-item-report.php" class="sidenav-link">
<div> Vendor Expense Item Report </div>
<?php if($Page=='Vendor-Expense-Item-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>


</ul>
</li>


<?php } if(in_array("28", $Options)){?>
    <li class="sidenav-item <?php if($MainPage=='Pretty-Cash-Account') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Account Petty Cash Request</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="account-pending-pretty-cash-request.php" class="sidenav-link"> 

<div>Pending Petty Cash Request</div>
<?php if($Page=='Account-Pending-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="account-approve-pretty-cash-request.php" class="sidenav-link">

<div>Approved Petty Cash Requests</div>
<?php if($Page=='Account-Approve-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="account-reject-pretty-cash-request.php" class="sidenav-link">

<div>Rejected Petty Cash Requests</div>
<?php if($Page=='Account-Reject-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>  

<?php } if(in_array("29", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Accountant-Advance-Account') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Pay Advance Amount</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="account-pay-advance-payment.php" class="sidenav-link"> 

<div>Pending Advance Payment</div>
<?php if($Page=='Account-Pending-Advance-Payment') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="account-paid-advance-payment.php" class="sidenav-link">

<div>Paid Advance Payment</div>
<?php if($Page=='Account-Paid-Advance-Payment') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
  

 </ul>
</li> 
<?php } ?>

  <?php if(in_array("30", $Options) || in_array("31", $Options) || in_array("32", $Options)){?>
        <li class="sidenav-item <?php if($MainPage=='Operations-Management') {?> open active <?php } ?>">
            <a href="javascript:" class="sidenav-link sidenav-toggle">
                <div>Operations Management</div>
            </a>
            <ul class="sidenav-menu">
                <?php if(in_array("30", $Options)){?>
                <li class="sidenav-item">
                    <a href="control-room-report.php" class="sidenav-link">
                        <div>Control Room</div>
                        <?php if($Page=='Controll-Room') {?>
                        <div class="pl-1 ml-auto">
                            <span class="badge badge-dot badge-primary"></span>
                        </div>
                        <?php } ?>
                    </a>
                </li>
                <?php } if(in_array("31", $Options)){?>
                <li class="sidenav-item">
                    <a href="view-store-manager-duties.php" class="sidenav-link">
                        <div>Store Manager Duties</div>
                        <?php if($Page=='Store-Manager-Duties') {?>
                        <div class="pl-1 ml-auto">
                            <span class="badge badge-dot badge-primary"></span>
                        </div>
                        <?php } ?>
                    </a>
                </li>
                <?php } if(in_array("32", $Options)){?>
                <li class="sidenav-item">
                    <a href="view-manager-checkpoint.php" class="sidenav-link">
                        <div>Manager Checkpoints</div>
                        <?php if($Page=='Manager-Checkpoint') {?>
                        <div class="pl-1 ml-auto">
                            <span class="badge badge-dot badge-primary"></span>
                        </div>
                        <?php } ?>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </li>
        <?php } if(in_array("33", $Options)){?>
        
        <li class="sidenav-item <?php if($MainPage=='Fuel-Checklist') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Fuel Station Checklist</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="view-fuel-station-checklist.php" class="sidenav-link">
<div>Pending</div>
<?php if($Page=='Pending-Fuel-Checklist') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="approve-fuel-station-checklist.php" class="sidenav-link">

<div>Approves</div>
<?php if($Page=='Approve-Fuel-Checklist') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="reject-fuel-station-checklist.php" class="sidenav-link">

<div>Reject</div>
<?php if($Page=='Reject-Fuel-Checklist') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
  

 </ul>
</li>  

<?php } if(in_array("34", $Options)){?>
       
        <li class="sidenav-item <?php if($MainPage=='Documents') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Documents</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="attach-documents.php" class="sidenav-link">
<div>Attach Documents</div>
<?php if($Page=='Add-Documents') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="view-attach-documents.php" class="sidenav-link">

<div>View Documents</div>
<?php if($Page=='View-Documents') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>  
     
       
    <?php } if(in_array("35", $Options)){?>
       <li class="sidenav-item <?php if($MainPage=='Documents') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Attendance</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
            <a href="add-attendance.php" class="sidenav-link">
                
                <div>Day Attendance</div>
                <?php if($Page=='Day-Attendance') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        <li class="sidenav-item">
            <a href="night-attendance.php" class="sidenav-link">
                
                <div>Night Attendance</div>
                <?php if($Page=='Night-Attendance') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        
        <li class="sidenav-item">
            <a href="end-attendance.php" class="sidenav-link">
                
                <div>Day End Attendance</div>
                <?php if($Page=='End-Attendance') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>
        
        <li class="sidenav-item">
            <a href="night-end-attendance.php" class="sidenav-link">
                
                <div>Night End Attendance</div>
                <?php if($Page=='Night-End-Attendance') {?>
                <div class="pl-1 ml-auto">
                    <span class="badge badge-dot badge-primary"></span>
                </div>
                <?php } ?>
            </a>
        </li>

 </ul>
</li>  
<?php } ?>


      <?php if(in_array("36", $Options) || in_array("63", $Options) || in_array("74", $Options) || in_array("75", $Options) || in_array("114", $Options)){?>
        <li class="sidenav-item <?php if($MainPage=='Financial-Management') {?> open active <?php } ?>">
            <a href="javascript:" class="sidenav-link sidenav-toggle">
                <div>Financial Management</div>
            </a>
            <ul class="sidenav-menu">
                <?php if(in_array("36", $Options)){?>
                <li class="sidenav-item">
                    <a href="view-advance-salary.php" class="sidenav-link">
                        <div>Advance Salary</div>
                        <?php if($Page=='Advance-Salary') {?>
                        <div class="pl-1 ml-auto">
                            <span class="badge badge-dot badge-primary"></span>
                        </div>
                        <?php } ?>
                    </a>
                </li>
                <?php } if(in_array("63", $Options)){?>
                <li class="sidenav-item">
                    <a href="advance-request.php" class="sidenav-link">
                        <div>Account/Admin Advance Request</div>
                        <?php if($Page=='Advance-Request') {?>
                        <div class="pl-1 ml-auto">
                            <span class="badge badge-dot badge-primary"></span>
                        </div>
                        <?php } ?>
                    </a>
                </li>
                <?php } if(in_array("114", $Options)){?>
                <li class="sidenav-item">
                    <a href="add-salary-slip.php" class="sidenav-link">
                        <div>Salary Slip</div>
                        <?php if($Page=='Amount-Request') {?>
                        <div class="pl-1 ml-auto">
                            <span class="badge badge-dot badge-primary"></span>
                        </div>
                        <?php } ?>
                    </a>
                </li>
                <?php } if(in_array("74", $Options)){?>
                <li class="sidenav-item">
                    <a href="amount-request.php" class="sidenav-link">
                        <div>Withdraw Amount Request</div>
                        <?php if($Page=='Amount-Request') {?>
                        <div class="pl-1 ml-auto">
                            <span class="badge badge-dot badge-primary"></span>
                        </div>
                        <?php } ?>
                    </a>
                </li>
                <?php } if(in_array("75", $Options)){?>
                <li class="sidenav-item">
                    <a href="wallet.php" class="sidenav-link">
                        <div>Wallet</div>
                        <?php if($Page=='Wallet') {?>
                        <div class="pl-1 ml-auto">
                            <span class="badge badge-dot badge-primary"></span>
                        </div>
                        <?php } ?>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </li>
      
<?php } if(in_array("37", $Options) || in_array("62", $Options)){?>
        <li class="sidenav-item <?php if($MainPage=='Customer-Service') {?> open active <?php } ?>">
            <a href="javascript:" class="sidenav-link sidenav-toggle">
                <div>Customer Service</div>
            </a>
            <ul class="sidenav-menu">
                <?php if(in_array("37", $Options)){?>
                <li class="sidenav-item">
                    <a href="customer-feedback-report.php" class="sidenav-link">
                        <div>Customer Feedback</div>
                        <?php if($Page=='Customer-Feedback-Report') {?>
                        <div class="pl-1 ml-auto">
                            <span class="badge badge-dot badge-primary"></span>
                        </div>
                        <?php } ?>
                    </a>
                </li>
                <?php } ?>
               
               
            </ul>
        </li>
       <?php } if(in_array("116", $Options)){?>
        <li class="sidenav-item <?php if($MainPage=='BH-Employee-Expense') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>BH Employee Expense</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="ho-business-head-pending-expense-request.php" class="sidenav-link">
<div>Pending expense</div>
<?php if($Page=='BH-Pending-Expense') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="employee-approve-expense-request-below.php" class="sidenav-link">

<div>Approve expense</div>
<?php if($Page=='BH-Approved-Expense') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="employee-reject-expense-request-below.php" class="sidenav-link">

<div>Reject expense</div>
<?php if($Page=='BH-Reject-Expense') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li> 

<?php } if(in_array("38", $Options)){?>
         <li class="sidenav-item <?php if($MainPage=='HO-Admin-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Admin Employee Expense</div>
</a>
<ul class="sidenav-menu">

 <li class="sidenav-item">
<a href="ho-admin-pending-expense-request.php" class="sidenav-link">
<div>Pending expense</div>
<?php if($Page=='HO-Admin-Pending-Expense-After-BH') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="ho-admin-approve-expense-request.php" class="sidenav-link">

<div>Approve expense</div>
<?php if($Page=='HO-Admin-Approve-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="ho-admin-reject-expense-request.php" class="sidenav-link">

<div>Reject expense</div>
<?php if($Page=='HO-Admin-Reject-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>  

<?php } if(in_array("39", $Options)){?>
      <li class="sidenav-item <?php if($MainPage=='HO-Manager-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Bank Details</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="view-bank-details.php" class="sidenav-link">

<div>Bank Details</div>
<?php if($Page=='Bank-Details') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="bank-detail-excel.php?FromDate=<?php echo date('Y-m-d');?>&ToDate=<?php echo date('Y-m-d');?>&Search=Search" class="sidenav-link">

<div>Generate Bank Detail Excel</div>
<?php if($Page=='Bank-Excel') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
 </ul>
</li>  
<?php } if(in_array("40", $Options)){?>


        
      
        <li class="sidenav-item <?php if($MainPage=='HO-Manager-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>BDM Vendor Expenses</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="bdm-vendor-pending-expense-request.php" class="sidenav-link">

<div>Pending Expense Request</div>
<?php if($Page=='HO-Manager-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="bdm-vendor-approve-expense-request.php" class="sidenav-link">

<div>Approved Expense Requests</div>
<?php if($Page=='HO-Manager-Approve-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="bdm-vendor-reject-expense-request.php" class="sidenav-link">

<div>Rejected Expense Requests</div>
<?php if($Page=='HO-Manager-Reject-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>  

<?php } if(in_array("107", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Account-Vendor-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Vendor Expenses</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="add-vendor-expenses.php" class="sidenav-link">

<div>Add New Vendor Expenses</div>
<?php if($Page=='Account-Vendor-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="view-vendor-expenses.php" class="sidenav-link">

<div>View Vendor Expenses</div>
<?php if($Page=='Account-Vendor-Approve-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
 </ul>
</li>  

<?php } if(in_array("41", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Account-Vendor-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Purchase Dept Vendor Expenses</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="purchase-vendor-pending-expense-request.php" class="sidenav-link">

<div>Pending Expense Request</div>
<?php if($Page=='Account-Vendor-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="purchase-vendor-approve-expense-request.php" class="sidenav-link">

<div>Approve Expense Requests</div>
<?php if($Page=='Account-Vendor-Approve-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="purchase-vendor-reject-expense-request.php" class="sidenav-link">

<div>Reject Expense Requests</div>
<?php if($Page=='Account-Vendor-Reject-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
 </ul>
</li>   

<?php } if(in_array("42", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Account-Vendor-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Purchase Dept NSO Vendor Expenses</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="nso-purchase-vendor-pending-expense-request.php" class="sidenav-link">

<div>Pending Expense Request</div>
<?php if($Page=='Account-Vendor-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="nso-purchase-vendor-approve-expense-request.php" class="sidenav-link">

<div>Approve Expense Requests</div>
<?php if($Page=='Account-Vendor-Approve-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="nso-purchase-vendor-reject-expense-request.php" class="sidenav-link">

<div>Reject Expense Requests</div>
<?php if($Page=='Account-Vendor-Reject-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
 </ul>
</li>   
<?php } if(in_array("103", $Options)){?>
<!-- <li class="sidenav-item <?php if($MainPage=='HO-Manager-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>First NSO Vendor Expenses </div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="nso-vendor-pending-expense-request-first.php" class="sidenav-link">

<div>Pending Expense Request</div>
<?php if($Page=='HO-Manager-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="nso-vendor-approve-expense-request-first.php" class="sidenav-link">

<div>Approved Expense Requests</div>
<?php if($Page=='HO-Manager-Approve-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="nso-vendor-reject-expense-request-first.php" class="sidenav-link">

<div>Rejected Expense Requests</div>
<?php if($Page=='HO-Manager-Reject-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>  -->
<?php } if(in_array("43", $Options)){?>
 <li class="sidenav-item <?php if($MainPage=='HO-Manager-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>NSO Vendor Expenses</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="nso-vendor-pending-expense-request.php" class="sidenav-link">

<div>Pending Expense Request</div>
<?php if($Page=='HO-Manager-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="nso-vendor-approve-expense-request.php" class="sidenav-link">

<div>Approved Expense Requests</div>
<?php if($Page=='HO-Manager-Approve-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="nso-vendor-reject-expense-request.php" class="sidenav-link">

<div>Rejected Expense Requests</div>
<?php if($Page=='HO-Manager-Reject-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>  


<?php } if(in_array("88", $Options)){?>
       <!-- <li class="sidenav-item <?php if($MainPage=='HO-Manager-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Accoutant Employee Expenses</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="accountant-pending-emp-expense-request.php" class="sidenav-link">

<div>Pending Expense Request</div>
<?php if($Page=='HO-Manager-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="accountant-approve-emp-expense-request.php" class="sidenav-link">

<div>Approved Expense Requests</div>
<?php if($Page=='HO-Manager-Approve-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="accountant-reject-emp-expense-request.php" class="sidenav-link">

<div>Rejected Expense Requests</div>
<?php if($Page=='HO-Manager-Reject-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>  -->
       
<?php } if(in_array("45", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='HR-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>HR salary expense</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="hr-pending-expense-request.php" class="sidenav-link">

<div>Pending expense</div>
<?php if($Page=='HR-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="hr-approve-expense-request.php" class="sidenav-link">

<div>Approve expense</div>
<?php if($Page=='HR-Approve-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="hr-reject-expense-request.php" class="sidenav-link">

<div>Reject expense</div>
<?php if($Page=='HR-Reject-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li> 

      
        <?php } if(in_array("46", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Pretty-Cash-Manager') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Manager Petty Cash Request</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="manager-pending-pretty-cash-request.php" class="sidenav-link">

<div>Pending Petty Cash Request</div>
<?php if($Page=='Manager-Pending-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="manager-approve-pretty-cash-request.php" class="sidenav-link">

<div>Approved Petty Cash Requests</div>
<?php if($Page=='Manager-Approve-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="manager-reject-pretty-cash-request.php" class="sidenav-link">

<div>Rejected Petty Cash Requests</div>
<?php if($Page=='Manager-Reject-Pretty-Cash-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>  

<?php } if(in_array("47", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Notifications') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Pay Vendor Amount</div>
</a>
<ul class="sidenav-menu">
  
 <li class="sidenav-item">
<a href="view-payable-amount-vendors.php" class="sidenav-link">

<div>Pending Payment</div>
<?php if($Page=='Customer-Notification') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="pay-all-exp-amount-to-vendor.php" class="sidenav-link">
<div>Pay Amount At a Time</div>
<?php if($Page=='Customer-Notification') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 <li class="sidenav-item">
<a href="view-payment-done-amount-vendors.php" class="sidenav-link">

<div>Payment Done</div>
<?php if($Page=='Employee-Notification') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
</ul>
</li>

<?php } if(in_array("48", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Notifications') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Pay NSO Vendor Amount</div>
</a>
<ul class="sidenav-menu">
  
 <li class="sidenav-item">
<a href="view-payable-amount-nso-vendors.php" class="sidenav-link">

<div>Pending Payment</div>
<?php if($Page=='Customer-Notification') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="pay-all-exp-amount-to-nso-vendor.php" class="sidenav-link">
<div>Pay Amount At a Time</div>
<?php if($Page=='Customer-Notification') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 <li class="sidenav-item">
<a href="view-payment-done-amount-nso-vendors.php" class="sidenav-link">

<div>Payment Done</div>
<?php if($Page=='Employee-Notification') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
</ul>
</li>



<?php } if(in_array("49", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Complaints') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Complaints</div>
</a>
<ul class="sidenav-menu">
    <li class="sidenav-item">
<a href="add-complaints.php" class="sidenav-link">
<div>Add Complaints</div>
<?php if($Page=='Add-Complaints') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<li class="sidenav-item">
<a href="view-complaints.php" class="sidenav-link">
<div>View Complaints</div>
<?php if($Page=='View-Complaints') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="allocate-complaints.php" class="sidenav-link">
<div>Allocate Complaints</div>
<?php if($Page=='Allocate-Complaints') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="view-complaints.php?Status=1" class="sidenav-link">
<div>Pending Complaints</div>
<?php if($Page=='View-Complaints-1') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="view-complaints.php?Status=2" class="sidenav-link">
<div>In Process Complaints</div>
<?php if($Page=='View-Complaints-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>man
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="view-complaints.php?Status=3" class="sidenav-link">
<div>Reject Complaints</div>
<?php if($Page=='View-Complaints-3') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="view-complaints.php?Status=4" class="sidenav-link">
<div>Completed Complaints</div>
<?php if($Page=='View-Complaints-4') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

</ul>
</li>
<?php } if(in_array("50", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Asset') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Asset management</div>
</a>
<ul class="sidenav-menu">
    <li class="sidenav-item">
<a href="add-asset.php" class="sidenav-link">
<div>Add Asset</div>
<?php if($Page=='Add-Asset') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<li class="sidenav-item">
<a href="view-assets.php" class="sidenav-link">
<div>View Assets</div>
<?php if($Page=='View-Asset') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>


</ul>
</li>


<?php } if(in_array("51", $Options)){?>



<li class="sidenav-item <?php if($MainPage=='Masters') {?> open active <?php } ?>">
            <a href="javascript:" class="sidenav-link sidenav-toggle">
                
                <div>Masters</div>
            </a>
            <ul class="sidenav-menu">


	
	<li class="sidenav-item <?php if($Page=='Location') {?> open active <?php } ?>">
	<a href="javascript:" class="sidenav-link sidenav-toggle">
	<div>Locations</div>
	</a>
	<ul class="sidenav-menu">
	<li class="sidenav-item">
	<a href="country.php" class="sidenav-link" style="color: #000;">
	<div>Country</div>
	<?php if($Page2=='Country') {?>
	<div class="pl-1 ml-auto">
	<span class="badge badge-dot badge-primary"></span>
	</div>
	<?php } ?>	
	</a>
	</li>
	<li class="sidenav-item">
	<a href="state.php" class="sidenav-link" style="color: #000;">
	<div>State</div>
	<?php if($Page2=='State') {?>
	<div class="pl-1 ml-auto">
	<span class="badge badge-dot badge-primary"></span>
	</div>
	<?php } ?>	
	</a>
	</li>
	<li class="sidenav-item">
	<a href="city.php" class="sidenav-link" style="color: #000;">
	<div>City</div>
	<?php if($Page2=='City') {?>
	<div class="pl-1 ml-auto">
	<span class="badge badge-dot badge-primary"></span>
	</div>
	<?php } ?>	
	</a>
	</li>

	</ul>
	</li>
	
	<li class="sidenav-item">
	<a href="popup-image.php" class="sidenav-link">
	<div>Popup Image</div>
	<?php if($Page=='Outlet-Audit-Questions') {?>
	<div class="pl-1 ml-auto">
	<span class="badge badge-dot badge-primary"></span>
	</div>
	<?php } ?>	
	</a>
	</li>
	
<li class="sidenav-item">
<a href="common-master.php?pageid=1" class="sidenav-link">
<div>Type Of Vendor </div> 
<?php if($Page=='TypeOfVendor') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="user-type.php" class="sidenav-link">
<div>Designation </div> 
<?php if($Page=='UserType') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="departments.php" class="sidenav-link">
<div>Departments </div> 
<?php if($Page=='Departments') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="franchaise-location.php" class="sidenav-link">
<div>Franchise Locations </div> 
<?php if($Page=='Franchise-Location') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="location-feasibility-approval.php" class="sidenav-link">
<div>Location Feasibility Approval</div>
<?php if(isset($Page) && ($Page=='Location-Feasibility-Approval' || $Page=='Location-Feasibility-Approval-View')) {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item <?php if(isset($Page) && ($Page=='Outlet-Closure-Approval-BH' || $Page=='Outlet-Closure-Approval-BH-View')) {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">
<div>Outlet Closure — BH approval</div>
<?php if(isset($Page) && ($Page=='Outlet-Closure-Approval-BH' || $Page=='Outlet-Closure-Approval-BH-View')) {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
<ul class="sidenav-menu">
<li class="sidenav-item">
<a href="outlet-closure-approval-bh.php?filter=pending" class="sidenav-link">
<div>Pending</div>
<?php if(isset($Page) && $Page=='Outlet-Closure-Approval-BH' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter==='pending') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<li class="sidenav-item">
<a href="outlet-closure-approval-bh.php?filter=approved" class="sidenav-link">
<div>Approves</div>
<?php if(isset($Page) && $Page=='Outlet-Closure-Approval-BH' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter==='approved') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<li class="sidenav-item">
<a href="outlet-closure-approval-bh.php?filter=rejected" class="sidenav-link">
<div>Reject</div>
<?php if(isset($Page) && $Page=='Outlet-Closure-Approval-BH' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter==='rejected') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
</ul>
</li>

<li class="sidenav-item <?php if(isset($Page) && ($Page=='Outlet-Closure-Approval-Finance' || $Page=='Outlet-Closure-Approval-Finance-View')) {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">
<div>Outlet Closure — Finance approval</div>
<?php if(isset($Page) && ($Page=='Outlet-Closure-Approval-Finance' || $Page=='Outlet-Closure-Approval-Finance-View')) {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
<ul class="sidenav-menu">
<li class="sidenav-item">
<a href="outlet-closure-approval-finance.php?filter=pending" class="sidenav-link">
<div>Pending</div>
<?php if(isset($Page) && $Page=='Outlet-Closure-Approval-Finance' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter==='pending') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<li class="sidenav-item">
<a href="outlet-closure-approval-finance.php?filter=approved" class="sidenav-link">
<div>Approves</div>
<?php if(isset($Page) && $Page=='Outlet-Closure-Approval-Finance' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter==='approved') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<li class="sidenav-item">
<a href="outlet-closure-approval-finance.php?filter=rejected" class="sidenav-link">
<div>Reject</div>
<?php if(isset($Page) && $Page=='Outlet-Closure-Approval-Finance' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter==='rejected') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
</ul>
</li>

<li class="sidenav-item <?php if(isset($Page) && ($Page=='Outlet-Closure-Approval-Admin' || $Page=='Outlet-Closure-Approval-Admin-View')) {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">
<div>Outlet Closure — Admin approval</div>
<?php if(isset($Page) && ($Page=='Outlet-Closure-Approval-Admin' || $Page=='Outlet-Closure-Approval-Admin-View')) {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
<ul class="sidenav-menu">
<li class="sidenav-item">
<a href="outlet-closure-approval-admin.php?filter=pending" class="sidenav-link">
<div>Pending</div>
<?php if(isset($Page) && $Page=='Outlet-Closure-Approval-Admin' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter==='pending') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<li class="sidenav-item">
<a href="outlet-closure-approval-admin.php?filter=approved" class="sidenav-link">
<div>Approves</div>
<?php if(isset($Page) && $Page=='Outlet-Closure-Approval-Admin' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter==='approved') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<li class="sidenav-item">
<a href="outlet-closure-approval-admin.php?filter=rejected" class="sidenav-link">
<div>Reject</div>
<?php if(isset($Page) && $Page=='Outlet-Closure-Approval-Admin' && isset($OutletClosureSidebarFilter) && $OutletClosureSidebarFilter==='rejected') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
</ul>
</li>

<li class="sidenav-item">
<a href="common-master.php?pageid=2" class="sidenav-link">
<div>Model Type </div> 
<?php if($Page=='TypeOfVendor') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>


<li class="sidenav-item">
<a href="view-upload-pdfs.php" class="sidenav-link">
<div> Download Center</div>
<?php if($Page=='View-Upload-Pdf') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 <li class="sidenav-item">
<a href="video-gallery.php" class="sidenav-link">
<div> MahaTube</div>
<?php if($Page=='Video-Gallery') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li> 

<li class="sidenav-item">
<a href="image-gallery.php" class="sidenav-link">
<div> Image Gallery</div>
<?php if($Page=='Image-Gallery') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

	<li class="sidenav-item">
	<a href="cashback-amount.php" class="sidenav-link">
	<div>Cashback Amount</div>
	<?php if($Page=='Cashback-Amount') {?>
	<div class="pl-1 ml-auto">
	<span class="badge badge-dot badge-primary"></span>
	</div>
	<?php } ?>	
	</a>
	</li>

	<li class="sidenav-item">
	<a href="points-setting.php" class="sidenav-link">
	<div>Points Setting</div>
	<?php if($Page=='Points-Setting') {?>
	<div class="pl-1 ml-auto">
	<span class="badge badge-dot badge-primary"></span>
	</div>
	<?php } ?>	
	</a>
	</li>

<li class="sidenav-item">
	<a href="sale-price-range.php" class="sidenav-link">
	<div>Shopping Cashback Price Range</div>
	<?php if($Page=='Sell-Price-Range') {?>
	<div class="pl-1 ml-auto">
	<span class="badge badge-dot badge-primary"></span>
	</div>
	<?php } ?>	
	</a>
	</li>

	<li class="sidenav-item">
	<a href="add-money-price-range.php" class="sidenav-link">
	<div>Add Money Cashback Price Range</div>
	<?php if($Page=='Money-Price-Range') {?>
	<div class="pl-1 ml-auto">
	<span class="badge badge-dot badge-primary"></span>
	</div>
	<?php } ?>	
	</a>
	</li>


	<li class="sidenav-item">
	<a href="outlet-audit-category.php" class="sidenav-link">
	<div>Outlet Audit Category</div>
	<?php if($Page=='Outlet-Audit-Category') {?>
	<div class="pl-1 ml-auto">
	<span class="badge badge-dot badge-primary"></span>
	</div>
	<?php } ?>	
	</a>
	</li>
	
		<li class="sidenav-item">
	<a href="outlet-audit-questions.php" class="sidenav-link">
	<div>Outlet Audit Questions</div>
	<?php if($Page=='Outlet-Audit-Questions') {?>
	<div class="pl-1 ml-auto">
	<span class="badge badge-dot badge-primary"></span>
	</div>
	<?php } ?>	
	</a>
	</li>
	
	
</ul>
</li>



<?php } if(in_array("52", $Options) || in_array("53", $Options) || in_array("54", $Options) || in_array("55", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Customers') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Franchise</div>
</a>
<ul class="sidenav-menu">
    <?php if(in_array("52", $Options)){?>
    <li class="sidenav-item">
<a href="main-zones.php" class="sidenav-link">
<div> Zone</div>
<?php if($Page=='Zone') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
 <li class="sidenav-item">
<a href="zones.php" class="sidenav-link">
<div> Region</div>
<?php if($Page=='Zone') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("53", $Options)){?>
<li class="sidenav-item">
<a href="sub-zones.php" class="sidenav-link">
<div> Sub Zone</div>
<?php if($Page=='Sub-Zone') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("54", $Options)){?>
<li class="sidenav-item">
<a href="view-assign-franchise-to-zone.php" class="sidenav-link">
<div> Assign Franchise To Zone</div>
<?php if($Page=='Assign-Franchise-Zone') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
   <?php } if(in_array("55", $Options)) {?>   
<li class="sidenav-item">
<a href="add-customer.php" class="sidenav-link">
<div> Add Franchise</div>
<?php if($Page=='Add-Customers') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="view-customers.php" class="sidenav-link">
<div> View Franchise</div>
<?php if($Page=='View-Customers') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>
</ul>
</li>


<?php } if(in_array("56", $Options) || in_array("94", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Employee') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Employee</div>
</a>
<ul class="sidenav-menu">
   <?php if(in_array("94", $Options)) {?>  
   <li class="sidenav-item">
<a href="employee-scheme.php" class="sidenav-link">
<div>Employee Scheme</div>
<?php if($Page=='Add-Employee') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
 <?php } if(in_array("56", $Options)) {?>  
<li class="sidenav-item">
<a href="add-employee.php" class="sidenav-link">
<div> Add Employee</div>
<?php if($Page=='Add-Employee') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>
<li class="sidenav-item">
<a href="view-employee.php" class="sidenav-link">
<div> View Employee</div>
<?php if($Page=='View-Employee') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="trainee-employee.php" class="sidenav-link">
<div> Trainee Employee</div>
<?php if($Page=='Trainee-Employee') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="non-trainee-employee.php" class="sidenav-link">
<div> Non Trainee Employee</div>
<?php if($Page=='Non-Trainee-Employee') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="view-inactive-employee.php" class="sidenav-link">
<div> Inactive Employees</div>
<?php if($Page=='View-Employee') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="other-employee.php" class="sidenav-link">
<div> Other Employee</div>
<?php if($Page=='View-Other-Employee') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="internship-employee.php" class="sidenav-link">
<div> Internship Employee</div>
<?php if($Page=='View-Internship-Employee') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="view-cofo-employee.php" class="sidenav-link">
<div> COFO Employees</div>
<?php if($Page=='View-Employee') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

</ul>
</li>
<?php } if(in_array("57", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Vendors') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Vendors</div>
</a>
<ul class="sidenav-menu">
 <?php if(in_array("14", $Options)) {?>    
<li class="sidenav-item">
<a href="add-vendor.php" class="sidenav-link">
<div> Add Vendor</div>
<?php if($Page=='Add-Vendors') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li> 
<?php } ?>
<li class="sidenav-item">
<a href="view-vendors.php" class="sidenav-link">
<div> View Vendors</div>
<?php if($Page=='View-Vendors') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
</ul>
</li>

<?php } if(in_array("58", $Options)){?>


<li class="sidenav-item <?php if($MainPage=='Freelancer') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Business Partner</div>
</a>
<ul class="sidenav-menu">
   <?php if(in_array("14", $Options)) {?> 
<li class="sidenav-item">
<a href="add-freelancer-2.php" class="sidenav-link">
<div> Add Business Partner</div>
<?php if($Page=='Add-Freelancer') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>

<li class="sidenav-item">
<a href="view-freelancer-2.php" class="sidenav-link">
<div> View My Partner</div>
<?php if($Page=='View-Freelancer') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

</ul>
</li>
<?php } if(in_array("59", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Freelancer') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Freelancer/Business Partner</div>
</a>
<ul class="sidenav-menu">
   <?php if(in_array("14", $Options)) {?>  
<li class="sidenav-item">
<a href="add-freelancer.php" class="sidenav-link">
<div> Add Business Partner</div>
<?php if($Page=='Add-Freelancer') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>
<li class="sidenav-item">
<a href="view-freelancer.php" class="sidenav-link">
<div> View Business Partner</div>
<?php if($Page=='View-Freelancer') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

</ul>
</li>
<?php } ?>

<li class="sidenav-item <?php if($MainPage=='Task') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Task</div>
</a>
<ul class="sidenav-menu">

<li class="sidenav-item">
<a href="add-task2.php" class="sidenav-link">
<div> Add Task</div>
<?php if($Page=='Add-Task') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="view-task2.php" class="sidenav-link">
<div> View Task</div>
<?php if($Page=='View-Task') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<li class="sidenav-item">
<a href="task-rating-report.php" class="sidenav-link">
<div> Task Rating</div>
<?php if($Page=='View-Task') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

</ul>
</li>

 <li class="sidenav-item">
                    <a href="ticket_management/ticket-dashboard.php" class="sidenav-link">
                        <div>Ticket Management</div>
                        <?php if($Page=='Ticket-Management') {?>
                        <div class="pl-1 ml-auto">
                            <span class="badge badge-dot badge-primary"></span>
                        </div>
                        <?php } ?>
                    </a>
                </li>

<!-- TRAINING MODULE MENU -->
<!-- TRAINING MODULE MENU -->
<?php if(in_array("135", $Options) || in_array("136", $Options) || in_array("137", $Options) || in_array("138", $Options) || in_array("139", $Options)) { ?>
<li class="sidenav-item <?php if($MainPage=='Training') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">
<div>Training</div>
</a>
<ul class="sidenav-menu">

<?php if(in_array("135", $Options)) { ?>
<li class="sidenav-item">
<a href="training-dashboard.php" class="sidenav-link">
<div>Training Dashboard</div>
<?php if($Page=='Training-Dashboard') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>

<?php if(in_array("136", $Options)) { ?>
<li class="sidenav-item">
<a href="add-training.php" class="sidenav-link">
<div>Add Training</div>
<?php if($Page=='Add-Training') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>

<?php if(in_array("137", $Options)) { ?>
<li class="sidenav-item">
<a href="view-training.php" class="sidenav-link">
<div>View Training</div>
<?php if($Page=='View-Training') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>

<?php if(in_array("138", $Options)) { ?>
<li class="sidenav-item">
<a href="training-types.php" class="sidenav-link">
<div>Training Type Master</div>
<?php if($Page=='Training-Types') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>

<?php if(in_array("139", $Options)) { ?>
<li class="sidenav-item">
<a href="training-reports.php" class="sidenav-link">
<div>Training Reports</div>
<?php if($Page=='Training-Reports') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>

</ul>
</li>
<?php } ?>
<!-- END TRAINING MODULE MENU -->

<?php if(in_array("136", $Options) || in_array("137", $Options)) { ?>
<li class="sidenav-item <?php if(isset($MainPage) && $MainPage=='Training-Videos') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">
<div>Training Videos</div>
</a>
<ul class="sidenav-menu">
<?php if(in_array("136", $Options)) { ?>
<li class="sidenav-item">
<a href="add-training-video.php" class="sidenav-link">
<div>Add Video</div>
<?php if(isset($Page) && $Page=='Add-Training-Video') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>
<?php if(in_array("137", $Options)) { ?>
<li class="sidenav-item">
<a href="view-training-video.php" class="sidenav-link">
<div>View Video</div>
<?php if(isset($Page) && $Page=='View-Training-Video') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<li class="sidenav-item">
<a href="view-training-video-exam-results.php" class="sidenav-link">
<div>Exam results</div>
<?php if(isset($Page) && $Page=='View-Training-Exam-Results') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>
</ul>
</li>
<?php } ?>


<?php if(in_array("155", $Options) || in_array("156", $Options) || in_array("162", $Options)) { ?>
<li class="sidenav-item <?php if($MainPage=='HR-KRA') {?> open active <?php } ?>">
          <a href="javascript:" class="sidenav-link sidenav-toggle">
              <div>HR KRA</div>
          </a>
          <ul class="sidenav-menu">
              <?php if(in_array("155", $Options)) { ?>
              <li class="sidenav-item">
                  <a href="kra-master.php" class="sidenav-link <?php if($Page=='KRA-Master') echo 'active'; ?>">
                      <div>KRA Master</div>
                  </a>
              </li>
              <?php } ?>
              <?php if(in_array("156", $Options)) { ?>
              <li class="sidenav-item">
                  <a href="kpi-master.php" class="sidenav-link <?php if($Page=='KPI-Master') echo 'active'; ?>">
                      <div>KPI Master</div>
                  </a>
              </li>
              <?php } ?>
              
              <?php if(in_array("162", $Options)) { ?>
              <li class="sidenav-item">
                  <a href="emp-hr-kra-requests.php" class="sidenav-link <?php if(isset($Page) && ($Page=='Emp-HR-KRA-Requests' || $Page=='Emp-HR-KRA-Request-Detail')) echo 'active'; ?>">
                      <div>Employee KRA Requests</div>
                      <?php if(isset($Page) && ($Page=='Emp-HR-KRA-Requests' || $Page=='Emp-HR-KRA-Request-Detail')) {?>
                      <div class="pl-1 ml-auto">
                      <span class="badge badge-dot badge-primary"></span>
                      </div>
                      <?php } ?>
                  </a>
              </li>
              <li class="sidenav-item">
                  <a href="emp-hr-kra-request-detail.php" class="sidenav-link <?php if(isset($Page) && $Page=='Emp-HR-KRA-Request-Detail') echo 'active'; ?>">
                      <div>Employee KRA Request (detail)</div>
                      <?php if(isset($Page) && $Page=='Emp-HR-KRA-Request-Detail') {?>
                      <div class="pl-1 ml-auto">
                      <span class="badge badge-dot badge-primary"></span>
                      </div>
                      <?php } ?>
                  </a>
              </li>
              <?php } ?>
          </ul>
      </li>
<?php } ?>


<!-- RESIGNATION CLEARANCE MODULE MENU -->
<?php if(in_array("140", $Options) || in_array("141", $Options) || in_array("142", $Options) || in_array("143", $Options) || in_array("144", $Options) || in_array("145", $Options) || in_array("146", $Options) || in_array("147", $Options)) { ?>
<li class="sidenav-item <?php if($MainPage=='Resignation-Clearance') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">
<div>Resignation Clearance</div>
</a>
<ul class="sidenav-menu">

<?php if(in_array("141", $Options)) { ?>
<li class="sidenav-item">
<a href="resignation-clearance-dashboard.php" class="sidenav-link">
<div>Clearance Dashboard</div>
<?php if($Page=='Clearance-Dashboard') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>

<?php if(in_array("142", $Options)) { ?>
<li class="sidenav-item">
<a href="view-all-resignations.php" class="sidenav-link">
<div>View All Resignations</div>
<?php if($Page=='View-Resignations') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>

<?php if(in_array("143", $Options)) { ?>
<li class="sidenav-item">
<a href="view-all-resignations.php?filter=it" class="sidenav-link">
<div>IT Clearance</div>
<?php if($Page=='IT-Clearance') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>

<?php if(in_array("144", $Options)) { ?>
<li class="sidenav-item">
<a href="view-all-resignations.php?filter=dept" class="sidenav-link">
<div>Department Clearance</div>
<?php if($Page=='Dept-Clearance') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>

<?php if(in_array("145", $Options)) { ?>
<li class="sidenav-item">
<a href="view-all-resignations.php?filter=accounts" class="sidenav-link">
<div>Accounts Clearance</div>
<?php if($Page=='Accounts-Clearance') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>

<?php if(in_array("146", $Options)) { ?>
<li class="sidenav-item">
<a href="view-all-resignations.php?filter=hr" class="sidenav-link">
<div>HR Final Clearance</div>
<?php if($Page=='HR-Clearance') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>

<?php if(in_array("147", $Options)) { ?>
<li class="sidenav-item">
<a href="view-all-resignations.php?filter=completed" class="sidenav-link">
<div>Completed Clearances</div>
<?php if($Page=='Completed-Clearances') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>

</ul>
</li>
<?php } ?>
<!-- END RESIGNATION CLEARANCE MODULE MENU -->

<?php if(in_array("61", $Options)){?>

<li class="sidenav-item <?php if($MainPage=='Customer-Query') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Franchise Query</div>
</a>
<ul class="sidenav-menu">
   <?php if(in_array("14", $Options)) {?>  
<li class="sidenav-item">
<a href="add-customer-query.php" class="sidenav-link">
<div> Add Franchise Query</div>
<?php if($Page=='Add-Customer-Query') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>
<li class="sidenav-item">
<a href="view-customer-query.php" class="sidenav-link">
<div> View Franchise Query</div>
<?php if($Page=='View-Customer-Query') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

</ul>
</li>

 
<?php } if(in_array("64", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='HR-Resign') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>HR Resign Request</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="hr-pending-resign-request.php" class="sidenav-link">

<div>Pending HR Resign Request</div>
<?php if($Page=='HR-Peding-Resign-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="hr-approve-resign-request.php" class="sidenav-link">

<div>Approved HR Resign Requests</div>
<?php if($Page=='HR-Approve-Resign-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="hr-reject-resign-request.php" class="sidenav-link">

<div>Rejected HR Resign Requests</div>
<?php if($Page=='HR-Reject-Resign-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>   
<?php } if(in_array("65", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='HR-Advance') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>HR Advance Request</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="hr-pending-advance-request.php" class="sidenav-link">

<div>Pending HR Advance Request</div>
<?php if($Page=='HR-Peding-Advance-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="hr-approve-advance-request.php" class="sidenav-link">

<div>Approved HR Advance Requests</div>
<?php if($Page=='HR-Approve-Advance-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="hr-reject-advance-request.php" class="sidenav-link">

<div>Rejected HR Advance Requests</div>
<?php if($Page=='HR-Reject-Advance-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>   

<?php } if(in_array("118", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='HR-Attendance') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>BDM Attendance Request</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="bdm-pending-attendance-request.php" class="sidenav-link">

<div>Pending Request</div>
<?php if($Page=='Hr-Pending-Attendance-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="bdm-approve-attendance-request.php" class="sidenav-link">

<div>Approved Requests</div>
<?php if($Page=='HR-Approve-Attendance-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="bdm-reject-attendance-request.php" class="sidenav-link">

<div>Rejected Requests</div>
<?php if($Page=='HR-Reject-Attendance-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li> 

<?php } if(in_array("66", $Options)){?>

<li class="sidenav-item <?php if($MainPage=='HR-Attendance') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>HR Attendance Request</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="hr-pending-attendance-request.php" class="sidenav-link">

<div>Pending Request</div>
<?php if($Page=='Hr-Pending-Attendance-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="hr-approve-attendance-request.php" class="sidenav-link">

<div>Approved Requests</div>
<?php if($Page=='HR-Approve-Attendance-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="hr-reject-attendance-request.php" class="sidenav-link">

<div>Rejected Requests</div>
<?php if($Page=='HR-Reject-Attendance-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>  
<?php } if(in_array("67", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='HR-Leave') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>HR Leave Request</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="hr-pending-leave-request.php" class="sidenav-link">

<div>Pending Request</div>
<?php if($Page=='Hr-Pending-Leave-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="hr-approve-leave-request.php" class="sidenav-link">

<div>Approved Requests</div>
<?php if($Page=='HR-Approve-Leave-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="hr-reject-leave-request.php" class="sidenav-link">

<div>Rejected Requests</div>
<?php if($Page=='HR-Reject-Leave-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>  

<?php } if(in_array("68", $Options)){?>

<li class="sidenav-item <?php if($MainPage=='Resign-Manager-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Manager Resign Request</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="manager-pending-resign-request.php" class="sidenav-link">

<div>Pending Resign Request</div>
<?php if($Page=='Manager-Pending-Resign-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="manager-approve-resign-request.php" class="sidenav-link">

<div>Approved Resign Requests</div>
<?php if($Page=='Manager-Approve-Resign-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="manager-reject-resign-request.php" class="sidenav-link">

<div>Rejected Resign Requests</div>
<?php if($Page=='Manager-Reject-Resign-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>  
<?php } if(in_array("69", $Options)){?>

<li class="sidenav-item <?php if($MainPage=='Advance-Payment-Manager') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Manager Advance Request</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="manager-pending-advance-request.php" class="sidenav-link">

<div>Pending Advance Request</div>
<?php if($Page=='Manager-Pending-Advance-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="manager-approve-advance-request.php" class="sidenav-link">

<div>Approved Advance Requests</div>
<?php if($Page=='Manager-Approve-Advance-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="manager-reject-advance-request.php" class="sidenav-link">

<div>Rejected Advance Requests</div>
<?php if($Page=='Manager-Reject-Advance-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>  

<?php } if(in_array("70", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Attendance-Manager-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Manager Attendance Request</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="manager-pending-attendance-request.php" class="sidenav-link">

<div>Pending Attendance Request</div>
<?php if($Page=='Manager-Pending-Attendance-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="manager-approve-attendance-request.php" class="sidenav-link">

<div>Approved Attendance Requests</div>
<?php if($Page=='Manager-Approve-Attendance-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="manager-reject-attendance-request.php" class="sidenav-link">

<div>Rejected Attendance Requests</div>
<?php if($Page=='Manager-Reject-Attendance-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>  

<?php } if(in_array("71", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Leave-Manager-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Manager Leave Request</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="manager-pending-leave-request.php" class="sidenav-link">

<div>Pending Leave Request</div>
<?php if($Page=='Manager-Pending-Leave-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="manager-approve-leave-request.php" class="sidenav-link">

<div>Approved Leave Requests</div>
<?php if($Page=='Manager-Approve-Leave-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="manager-reject-leave-request.php" class="sidenav-link">

<div>Rejected Leave Requests</div>
<?php if($Page=='Manager-Reject-Leave-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>  
<?php } if(in_array("44", $Options)){?>
    <li class="sidenav-item <?php if($MainPage=='HO-Manager-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Manager Expenses</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="ho-manager-pending-expense-request.php" class="sidenav-link">
<div>Pending Expenses Requests</div>
<div class="small text-muted" style="font-size: 0.72rem; line-height: 1.2; opacity: 0.9;"></div>
<?php if($Page=='HO-Manager-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 

<li class="sidenav-item">
<a href="ho-manager-approve-expense-request.php" class="sidenav-link">

<div>Approved Expenses Requests</div>
<?php if($Page=='HO-Manager-Approve-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
    <li class="sidenav-item">
<a href="ho-manager-reject-expense-request.php" class="sidenav-link">

<div>Rejected Expenses Requests</div>
<?php if($Page=='HO-Manager-Reject-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

 </ul>
</li>  

<?php } if(in_array("149", $Options)){?>
        <li class="sidenav-item <?php if($MainPage=='Manager-Hiring-Request') {?> open active <?php } ?>">
            <a href="javascript:" class="sidenav-link sidenav-toggle">
                <div>Manager Hiring Request</div>
            </a>
            <ul class="sidenav-menu">
                <li class="sidenav-item">
                    <a href="manager-hiring-request.php?tab=pending" class="sidenav-link">
                        <div>Pending Request</div>
                    </a>
                </li>
                <li class="sidenav-item">
                    <a href="manager-hiring-request.php?tab=approved" class="sidenav-link">
                        <div>Approved Request</div>
                    </a>
                </li>
                <li class="sidenav-item">
                    <a href="manager-hiring-request.php?tab=rejected" class="sidenav-link">
                        <div>Rejected Request</div>
                    </a>
                </li>
            </ul>
        </li>
      <?php } ?>
      <?php if(in_array("150", $Options)){?>
        <li class="sidenav-item <?php if($MainPage=='HR-Hiring-Request') {?> open active <?php } ?>">
            <a href="javascript:" class="sidenav-link sidenav-toggle">
                <div>HR Hiring Request</div>
            </a>
            <ul class="sidenav-menu">
                <li class="sidenav-item">
                    <a href="hr-hiring-request.php?tab=pending" class="sidenav-link">
                        <div>Pending Request</div>
                    </a>
                </li>
                <li class="sidenav-item">
                    <a href="hr-hiring-request.php?tab=approved" class="sidenav-link">
                        <div>Approved Request</div>
                    </a>
                </li>
                <li class="sidenav-item">
                    <a href="hr-hiring-request.php?tab=rejected" class="sidenav-link">
                        <div>Rejected Request</div>
                    </a>
                </li>
            </ul>
        </li>
<?php } if(in_array("119", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Account-Vendor-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Admin Vendor Expenses Below 2000</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="account-vendor-pending-expense-request-below.php" class="sidenav-link">

<div>Pending Expense Request</div>
<?php if($Page=='Account-Vendor-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="account-vendor-approve-expense-request-below.php" class="sidenav-link">

<div>Approve Expense Requests</div>
<?php if($Page=='Account-Vendor-Approve-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="account-vendor-reject-expense-request-below.php" class="sidenav-link">

<div>Reject Expense Requests</div>
<?php if($Page=='Account-Vendor-Reject-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
 </ul>
</li>  

<?php } if(in_array("72", $Options)){?>

<li class="sidenav-item <?php if($MainPage=='Account-Vendor-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Admin Vendor Expenses</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="account-vendor-pending-expense-request.php" class="sidenav-link">

<div>Pending Expense Request</div>
<?php if($Page=='Account-Vendor-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="account-vendor-approve-expense-request.php" class="sidenav-link">

<div>Approve Expense Requests</div>
<?php if($Page=='Account-Vendor-Approve-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="account-vendor-reject-expense-request.php" class="sidenav-link">

<div>Reject Expense Requests</div>
<?php if($Page=='Account-Vendor-Reject-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
 </ul>
</li>   
<?php } if(in_array("73", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Account-Vendor-Expenses') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Admin NSO Vendor Expenses</div>
</a>
<ul class="sidenav-menu">
    
 <li class="sidenav-item">
<a href="account-nso-vendor-pending-expense-request.php" class="sidenav-link">

<div>Pending Expense Request</div>
<?php if($Page=='Account-Vendor-Peding-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="account-nso-vendor-approve-expense-request.php" class="sidenav-link">

<div>Approve Expense Requests</div>
<?php if($Page=='Account-Vendor-Approve-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="account-nso-vendor-reject-expense-request.php" class="sidenav-link">

<div>Reject Expense Requests</div>
<?php if($Page=='Account-Vendor-Reject-Expense-Request') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
    
 </ul>
</li>   
<?php } ?>



<?php if(in_array("76", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Profit-Loss') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Profit And Loss</div>
</a>
<ul class="sidenav-menu">
    
     <!-- <li class="sidenav-item">
<a href="add-profit-loss.php" class="sidenav-link">

<div>Add Profit And Loss</div>
<?php if($Page=='Add-Profit-Loss') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li> -->

<li class="sidenav-item">
<a href="view-profit-loss.php" class="sidenav-link">

<div>View Profit And Loss</div>
<?php if($Page=='View-Profit-Loss') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
</ul>
</li>
 <?php } if(in_array("77", $Options) || in_array("79", $Options) || in_array("80", $Options) || in_array("81", $Options) || in_array("82", $Options) || in_array("83", $Options) || in_array("84", $Options) || in_array("96", $Options) || in_array("97", $Options) || in_array("102", $Options) || in_array("56", $Options) || (isset($_SESSION['Admin']['Roll']) && (int)$_SESSION['Admin']['Roll'] === 1)){?>
<li class="sidenav-item <?php if($MainPage=='Report') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Report</div>
</a>
<ul class="sidenav-menu"> 
<?php if(in_array("102", $Options)){?>
 <li class="sidenav-item">
<a href="cash-handover-report.php" class="sidenav-link">
<div> Cash Handover Report</div>
<?php if($Page=='Employee-Wallet-Outstanding-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("163", $Options)){?>
 <li class="sidenav-item">
<a href="employee-attendance-timing-report.php" class="sidenav-link">
<div> Employee Attendance Timing Report</div>
<?php if($Page=='Employee-Wallet-Outstanding-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
 <li class="sidenav-item">
<a href="late-commerce-report.php" class="sidenav-link">
<div> Late Commerce Report</div>
<?php if($Page=='Late-Commerce-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("148", $Options)){?>
 <li class="sidenav-item">
<a href="outlet-audit-checklist-report.php" class="sidenav-link">
<div> Outlet Audit Checklist Report</div>
<?php if($Page=='Outlet-Audit-Checklist-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>
<?php if(in_array("56", $Options) || (isset($_SESSION['Admin']['Roll']) && (int)$_SESSION['Admin']['Roll'] === 1)){ ?>
 <li class="sidenav-item">
<a href="report-user-activity-logs.php" class="sidenav-link">
<div> User Activity Log</div>
<?php if(isset($Page) && $Page=='User-Activity-Log-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("120", $Options)){?>
 <li class="sidenav-item">
<a href="generate-salary-sheet.php" class="sidenav-link">
<div> Generate Salary Sheet</div>
<?php if($Page=='Employee-Wallet-Outstanding-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<li class="sidenav-item">
<a href="pending-leave-report.php" class="sidenav-link">

<div>Leave Balance Report</div>
<?php if($Page=='Pending-Leave-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("104", $Options)){?>
 <li class="sidenav-item">
<a href="stock-available-report.php" class="sidenav-link">
<div> Stock Available Report</div>
<?php if($Page=='Employee-Wallet-Outstanding-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("105", $Options)){?>
 <li class="sidenav-item">
<a href="daily-wise-sale-report.php?FromDate=<?php echo date('Y-m-d');?>&ToDate=<?php echo date('Y-m-d');?>" class="sidenav-link">
<div> Daily Wise Sale Report</div>
<?php if($Page=='Employee-Wallet-Outstanding-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<?php } if(in_array("131", $Options)){?>
 <li class="sidenav-item">
<a href="franchise-time-gap-report.php?FromDate=<?php echo date('Y-m-d');?>&ToDate=<?php echo date('Y-m-d');?>" class="sidenav-link">
<div> Franchsie Time Gap Report</div>
<?php if($Page=='Employee-Wallet-Outstanding-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("106", $Options)){?>
 <li class="sidenav-item">
<a href="shop-open-close-report.php?FromDate=<?php echo date('Y-m-d');?>&ToDate=<?php echo date('Y-m-d');?>" class="sidenav-link">
<div> Shop Sessions Report</div>
<?php if($Page=='Employee-Wallet-Outstanding-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
 <?php } if(in_array("77", $Options)){?>
 <li class="sidenav-item">
<a href="franchise-daily-survey-report.php" class="sidenav-link">
<div> Franchise Daily Checklist Report</div>
<?php if($Page=='Franchise-Daily-Survey-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("79", $Options)){?>
 <li class="sidenav-item">
<a href="franchise-query-report.php" class="sidenav-link">
<div> Franchise Query report</div>
<?php if($Page=='Franchise-Query-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<?php } if(in_array("80", $Options)){?>
 <li class="sidenav-item">
<a href="employee-daily-report.php" class="sidenav-link">
<div> Employee Task Report</div>
<?php if($Page=='Employee-Daily-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("81", $Options)){?>
<li class="sidenav-item">
<a href="attendance-report-new.php" class="sidenav-link">
<div> Employee Attendace Report</div>
<?php if($Page=='Attendance-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<li class="sidenav-item">
<a href="bdm-attendance-report.php" class="sidenav-link">
<div> BDM Attendace Report</div>
<?php if($Page=='Employee-Wallet-Outstanding-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<li class="sidenav-item">
<a href="attendance-report-month-wise.php" class="sidenav-link">
<div> Employee Attendace Report Month Wise</div>
<?php if($Page=='Attendance-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("98", $Options)){?>
<li class="sidenav-item">
<a href="attendance-report-percentage.php" class="sidenav-link">
<div> Employee Attendace Report Percentage Wise</div>
<?php if($Page=='Attendance-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("151", $Options)){?>
<li class="sidenav-item">
<a href="attendance-analysis-reports.php" class="sidenav-link">
<div> Employee Attendance Analysis Reports</div>
<?php if($Page=='Attendance-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("82", $Options)){?>
<li class="sidenav-item">
<a href="employee-wallet-report.php" class="sidenav-link">
<div> Employee Wallet Report</div>
<?php if($Page=='Employee-Wallet-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("83", $Options)){?>

 <li class="sidenav-item">
<a href="employee-wallet-outstanding.php" class="sidenav-link">
<div> Employee Wallet Outstanding</div>
<?php if($Page=='Employee-Wallet-Outstanding') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("84", $Options)){?>
 <li class="sidenav-item">
<a href="employee-wallet-outstanding-2.php" class="sidenav-link">
<div> Employee Wallet Outstanding 2</div>
<?php if($Page=='Employee-Wallet-Outstanding-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("96", $Options)){?>
 <li class="sidenav-item">
<a href="daily-mis-joining.php" class="sidenav-link">
<div> Daily MIS of Joining</div>
<?php if($Page=='Employee-Wallet-Outstanding-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("97", $Options)){?>
 <li class="sidenav-item">
<a href="daily-mis-attrition.php" class="sidenav-link">
<div> Daily MIS of Attrition (Exit)</div>
<?php if($Page=='Employee-Wallet-Outstanding-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } if(in_array("152", $Options)){?>
<li class="sidenav-item">
<a href="exit-interview-report.php" class="sidenav-link">
<div> Exit Interview Report</div>
<?php if($Page=='Exit-Interview-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<?php } if(in_array("161", $Options)) { ?>
<li class="sidenav-item">
<a href="idea-box-full-report.php" class="sidenav-link">
<div>Idea Box full report</div>
<?php if(isset($Page) && $Page=='Idea-Box-Full-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<?php } ?>



</ul>
</li> 




<?php } if(in_array("78", $Options)){?>
<li class="sidenav-item <?php if($MainPage=='Account-Report') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>Account Report</div>
</a>
<ul class="sidenav-menu">
  
<li class="sidenav-item">
<a href="wallet-balance-report.php" class="sidenav-link">
<div> Wallet Balance Report</div>
<?php if($Page=='Wallet-Balance-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="transaction-report.php" class="sidenav-link">
<div> Transaction Report</div>
<?php if($Page=='Transaction-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="expense-category-wise-report.php" class="sidenav-link">
<div> Expense Category Wise Report</div>
<?php if($Page=='Transaction-Report') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

</ul>
</li>
<?php } ?>



<?php if(in_array("130", $Options)) {?>
<li class="sidenav-item <?php if($MainPage=='E-Commerce') {?> open active <?php } ?>">
<a href="javascript:" class="sidenav-link sidenav-toggle">

<div>E-Commerce</div>
</a>
<ul class="sidenav-menu"> 

<li class="sidenav-item">
<a href="payment-method.php" class="sidenav-link">
<div>Payment Method</div>
<?php if($Page=='PaymentMethod') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="cancel-reason.php" class="sidenav-link">
<div>Cancel Reason</div>
<?php if($Page=='Cancel-Reason') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="coupon-code.php" class="sidenav-link">
<div>Referral/Coupon/Offer Code</div>
<?php if($Page=='Coupon-Code') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="today-orders.php" class="sidenav-link">
<div> Today's Orders</div>
<?php if($Page=='Today-Orders') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<li class="sidenav-item">
<a href="view-orders.php" class="sidenav-link">
<div> View Orders</div>
<?php if($Page=='View-Orders') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="add-shop-product.php" class="sidenav-link">
<div> Add Product</div>
<?php if($Page=='Add-Product') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
<li class="sidenav-item">
<a href="view-shop-products.php" class="sidenav-link">
<div> View Products</div>
<?php if($Page=='View-Product') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="shop-category.php" class="sidenav-link">
<div>Category </div> 
<?php if($Page=='Category') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
 <li class="sidenav-item">
<a href="shop-sub-category.php" class="sidenav-link">
<div>Sub Category</div>
<?php if($Page=='Sub-Category') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>	
</a>
</li> 

<!-- <li class="sidenav-item">
<a href="brands.php" class="sidenav-link">
<div>Brands</div>
<?php if($Page=='Brands') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li> -->

<li class="sidenav-item">
<a href="attribute-value.php" class="sidenav-link">
<div>Product Attributes</div>
<?php if($Page=='Attributes') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li> 

<li class="sidenav-item">
<a href="shipping-price.php" class="sidenav-link">
<div>Shipping Price</div>
<?php if($Page=='Shipping-Price') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>


<li class="sidenav-item">
<a href="home-sliders.php" class="sidenav-link">
<div>Home Sliders</div>
<?php if($Page=='Home Slider') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="home-banners.php" class="sidenav-link">
<div>Home Banners</div>
<?php if($Page=='Home Banner') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="faqs.php" class="sidenav-link">
<div>Faq's</div>
<?php if($Page=='Faq') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

</ul>
</li> 
<?php } ?>


        <li class="sidenav-item <?php if($MainPage=='Account') {?> open active <?php } ?>">
            <a href="javascript:" class="sidenav-link sidenav-toggle">
                
                <div><?php echo $row77['Fname']." ".$row77['Lname']; ?></div>
            </a>
            <ul class="sidenav-menu">
<?php 
 if($Roll == 1){?>
 <li class="sidenav-item ">
                    <a href="company-information.php" class="sidenav-link">
                        <div><i class="feather icon-unlock text-muted"></i> Company Profile</div>
                        <?php if($Page=='Company-Profile') {?>
                        <div class="pl-1 ml-auto">
                            <span class="badge badge-dot badge-primary"></span>
                        </div>
                        <?php } ?>
                    </a>
                </li>
            <?php } ?>
                <li class="sidenav-item ">
                    <a href="change-password.php" class="sidenav-link">
                        <div><i class="feather icon-unlock text-muted"></i> Change Password</div>
                        <?php if($Page=='Change-Password') {?>
                        <div class="pl-1 ml-auto">
                            <span class="badge badge-dot badge-primary"></span>
                        </div>
                        <?php } ?>
                    </a>
                </li>
                <?php if($user_id == 2651 || $user_id == 2650){?>
      <li class="sidenav-item">
<a href="delete-vendor-exepense-request.php" class="sidenav-link">
<div> Delete Vendor Expenses </div>
<?php if($Page=='Weekly-Sale-Report-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="delete-nso-vendor-exepense-request.php" class="sidenav-link">
<div> Delete NSO Vendor Expenses </div>
<?php if($Page=='Weekly-Sale-Report-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="update-vendor-expenses.php" class="sidenav-link">
<div> Upload Vendor Expense PDF </div>
<?php if($Page=='Weekly-Sale-Report-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>

<li class="sidenav-item">
<a href="upload-emp-expense-files.php" class="sidenav-link">
<div> Upload Employee Expense Documents </div>
<?php if($Page=='Weekly-Sale-Report-2') {?>
<div class="pl-1 ml-auto">
<span class="badge badge-dot badge-primary"></span>
</div>
<?php } ?>
</a>
</li>
       <?php } ?>
                <li class="sidenav-item">
                    <a href="logout.php" class="sidenav-link">
                        <div><i class="feather icon-power text-danger"></i> Log Out</div>
                    </a>
                </li>
            </ul>
        </li>

    </ul>
</div>
</div>
<script>
    setInterval(function() {
        console.log('ddd');
  fetch('ping.php'); // this can be a blank PHP file that only starts the session
}, 10000);
</script>

